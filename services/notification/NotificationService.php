<?php

require_once __DIR__ . '/../../config/database.php';

class NotificationService
{
    public static function createNotification(int $userId, string $message): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (:user_id, :message, 0, NOW())'
        );
        $stmt->execute(['user_id' => $userId, 'message' => $message]);

        return ['success' => true, 'id' => (int) $pdo->lastInsertId()];
    }

    public static function listForUser(int $userId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare(
            'SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function markAsRead(int $notificationId, int $userId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $notificationId, 'user_id' => $userId]);

        return ['success' => true, 'message' => 'Notification marked as read.'];
    }
}
