<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../notification/NotificationService.php';

class ExchangeService
{
    public static function requestExchange(int $senderId, array $payload): array
    {
        $receiverId = (int) ($payload['receiver_id'] ?? 0);
        $offeredSkillId = (int) ($payload['offered_skill_id'] ?? 0);
        $requestedSkillId = (int) ($payload['requested_skill_id'] ?? 0);
        $message = trim((string) ($payload['message'] ?? ''));

        if ($receiverId <= 0 || $offeredSkillId <= 0 || $requestedSkillId <= 0) {
            throw new InvalidArgumentException('Receiver, offered skill and requested skill are required.');
        }

        if ($senderId === $receiverId) {
            throw new InvalidArgumentException('You cannot exchange with yourself.');
        }

        $pdo = getDbConnection();
        $checkOffered = $pdo->prepare('SELECT id FROM user_skills WHERE user_id = :user_id AND skill_id = :skill_id LIMIT 1');
        $checkOffered->execute(['user_id' => $senderId, 'skill_id' => $offeredSkillId]);
        if (!$checkOffered->fetch()) {
            throw new InvalidArgumentException('You do not own the offered skill selected.');
        }

        $checkWanted = $pdo->prepare('SELECT id FROM wanted_skills WHERE user_id = :user_id AND skill_id = :skill_id LIMIT 1');
        $checkWanted->execute(['user_id' => $receiverId, 'skill_id' => $requestedSkillId]);
        $checkOfferedTarget = $pdo->prepare('SELECT id FROM user_skills WHERE user_id = :user_id AND skill_id = :skill_id LIMIT 1');
        $checkOfferedTarget->execute(['user_id' => $receiverId, 'skill_id' => $requestedSkillId]);

        if (!$checkWanted->fetch() && !$checkOfferedTarget->fetch()) {
            throw new InvalidArgumentException('The target user does not currently need or offer this skill.');
        }

        $stmt = $pdo->prepare(
            'INSERT INTO exchange_requests (sender_id, receiver_id, offered_skill_id, requested_skill_id, message, status, created_at)
             VALUES (:sender_id, :receiver_id, :offered_skill_id, :requested_skill_id, :message, "Pending", NOW())'
        );
        $stmt->execute([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'offered_skill_id' => $offeredSkillId,
            'requested_skill_id' => $requestedSkillId,
            'message' => $message,
        ]);

        $requestId = (int) $pdo->lastInsertId();
        $receiver = $pdo->prepare('SELECT full_name FROM users WHERE id = :id LIMIT 1');
        $receiver->execute(['id' => $receiverId]);
        $receiverName = $receiver->fetchColumn() ?: 'Member';
        NotificationService::createNotification($receiverId, 'You received a new exchange request from another member.');

        return ['success' => true, 'request_id' => $requestId, 'message' => 'Exchange request sent to ' . $receiverName . '.'];
    }

    public static function listForUser(int $userId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare(
            'SELECT er.*, sender.full_name AS sender_name, receiver.full_name AS receiver_name,
                    offered.skill_name AS offered_skill_name, requested.skill_name AS requested_skill_name
             FROM exchange_requests er
             INNER JOIN users sender ON sender.id = er.sender_id
             INNER JOIN users receiver ON receiver.id = er.receiver_id
             INNER JOIN skills offered ON offered.id = er.offered_skill_id
             INNER JOIN skills requested ON requested.id = er.requested_skill_id
             WHERE (er.sender_id = :user_id OR er.receiver_id = :user_id)
             ORDER BY er.created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $requestId, int $userId, string $action): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM exchange_requests WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $requestId]);
        $request = $stmt->fetch();

        if (!$request) {
            throw new RuntimeException('Exchange request not found.');
        }

        $newStatus = '';
        if ($action === 'accept') {
            if ((int) $request['receiver_id'] !== $userId) {
                throw new InvalidArgumentException('Only the receiver can accept the request.');
            }
            $newStatus = 'Accepted';
            NotificationService::createNotification((int) $request['sender_id'], 'Your exchange request was accepted.');
        } elseif ($action === 'reject') {
            if ((int) $request['receiver_id'] !== $userId) {
                throw new InvalidArgumentException('Only the receiver can reject the request.');
            }
            $newStatus = 'Rejected';
            NotificationService::createNotification((int) $request['sender_id'], 'Your exchange request was rejected.');
        } elseif ($action === 'complete') {
            if ((int) $request['sender_id'] !== $userId && (int) $request['receiver_id'] !== $userId) {
                throw new InvalidArgumentException('You are not allowed to complete this exchange.');
            }
            $newStatus = 'Completed';
            NotificationService::createNotification((int) $request['sender_id'], 'The exchange was completed successfully.');
            NotificationService::createNotification((int) $request['receiver_id'], 'The exchange was completed successfully.');
        } else {
            throw new InvalidArgumentException('Unsupported exchange action.');
        }

        $update = $pdo->prepare('UPDATE exchange_requests SET status = :status WHERE id = :id');
        $update->execute(['status' => $newStatus, 'id' => $requestId]);

        return ['success' => true, 'status' => $newStatus, 'message' => 'Exchange request updated.'];
    }
}
