<?php

require_once __DIR__ . '/../../config/database.php';

class ReportingService
{
    public static function dashboardStats(): array
    {
        $pdo = getDbConnection();

        $memberCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $skillCount = $pdo->query('SELECT COUNT(*) FROM skills')->fetchColumn();
        $pending = $pdo->query('SELECT COUNT(*) FROM exchange_requests WHERE status = "Pending"')->fetchColumn();
        $completed = $pdo->query('SELECT COUNT(*) FROM exchange_requests WHERE status = "Completed"')->fetchColumn();
        $categoryCount = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();

        $recent = $pdo->query(
            'SELECT er.id, sender.full_name AS sender_name, receiver.full_name AS receiver_name, er.status, er.created_at
             FROM exchange_requests er
             INNER JOIN users sender ON sender.id = er.sender_id
             INNER JOIN users receiver ON receiver.id = er.receiver_id
             ORDER BY er.created_at DESC
             LIMIT 5'
        )->fetchAll();

        return [
            'total_members' => (int) $memberCount,
            'total_skills' => (int) $skillCount,
            'total_categories' => (int) $categoryCount,
            'pending_exchanges' => (int) $pending,
            'completed_exchanges' => (int) $completed,
            'recent_activity' => $recent,
        ];
    }

    public static function exchangesByStatus(): array
    {
        $pdo = getDbConnection();
        $query = $pdo->query('SELECT status, COUNT(*) AS total FROM exchange_requests GROUP BY status ORDER BY total DESC');
        return $query->fetchAll();
    }

    public static function skillPopularity(): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->query(
            'SELECT s.skill_name, COUNT(us.id) AS offered_count, COUNT(ws.id) AS wanted_count
             FROM skills s
             LEFT JOIN user_skills us ON us.skill_id = s.id
             LEFT JOIN wanted_skills ws ON ws.skill_id = s.id
             GROUP BY s.id
             ORDER BY (COUNT(us.id) + COUNT(ws.id)) DESC'
        );
        return $stmt->fetchAll();
    }
}
