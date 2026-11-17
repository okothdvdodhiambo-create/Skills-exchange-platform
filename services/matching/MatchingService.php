<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/bootstrap.php';

class MatchingService
{
    public static function getMatchesForUser(int $userId): array
    {
        $pdo = getDbConnection();

        $offered = self::getSkillMap($userId, 'offer');
        $wanted = self::getSkillMap($userId, 'want');

        $stmt = $pdo->prepare('SELECT id, full_name, profile_image, location FROM users WHERE id != :user_id ORDER BY full_name ASC');
        $stmt->execute(['user_id' => $userId]);
        $users = $stmt->fetchAll();

        $matches = [];
        foreach ($users as $otherUser) {
            $otherOffered = self::getSkillMap((int) $otherUser['id'], 'offer');
            $otherWanted = self::getSkillMap((int) $otherUser['id'], 'want');

            $reasons = [];

            foreach ($offered as $skillId => $skillName) {
                if (isset($otherWanted[$skillId])) {
                    $reasons[] = sprintf('%s can be exchanged for %s', $skillName, $otherWanted[$skillId]);
                }
            }

            foreach ($otherOffered as $skillId => $skillName) {
                if (isset($wanted[$skillId])) {
                    $reasons[] = sprintf('%s can be exchanged for %s', $skillName, $wanted[$skillId]);
                }
            }

            if (empty($reasons)) {
                continue;
            }

            $mutualSkills = [];
            foreach ($offered as $skillId => $skillName) {
                if (isset($otherWanted[$skillId])) {
                    $mutualSkills[] = $skillName;
                }
            }
            foreach ($otherOffered as $skillId => $skillName) {
                if (isset($wanted[$skillId])) {
                    $mutualSkills[] = $skillName;
                }
            }

            $matches[] = [
                'user_id' => (int) $otherUser['id'],
                'full_name' => $otherUser['full_name'],
                'profile_image' => $otherUser['profile_image'] ?? 'default-user.png',
                'location' => $otherUser['location'] ?? 'Unknown',
                'offered_skills' => self::listSkillsForUser((int) $otherUser['id'], 'offer'),
                'wanted_skills' => self::listSkillsForUser((int) $otherUser['id'], 'want'),
                'match_reason' => implode('; ', array_unique($reasons)),
                'mutual_skills' => array_values(array_unique($mutualSkills)),
            ];
        }

        return $matches;
    }

    private static function getSkillMap(int $userId, string $type): array
    {
        $pdo = getDbConnection();

        if ($type === 'offer') {
            $query = 'SELECT s.id, s.skill_name
                      FROM user_skills us
                      INNER JOIN skills s ON s.id = us.skill_id
                      WHERE us.user_id = :user_id';
        } elseif ($type === 'want') {
            $query = 'SELECT s.id, s.skill_name
                      FROM wanted_skills ws
                      INNER JOIN skills s ON s.id = ws.skill_id
                      WHERE ws.user_id = :user_id';
        } else {
            return [];
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute(['user_id' => $userId]);

        $map = [];
        foreach ($stmt->fetchAll() as $row) {
            $map[(int) $row['id']] = $row['skill_name'];
        }

        return $map;
    }

    private static function listSkillsForUser(int $userId, string $type): array
    {
        $pdo = getDbConnection();

        if ($type === 'offer') {
            $query = 'SELECT s.skill_name FROM user_skills us INNER JOIN skills s ON s.id = us.skill_id WHERE us.user_id = :user_id';
        } else {
            $query = 'SELECT s.skill_name FROM wanted_skills ws INNER JOIN skills s ON s.id = ws.skill_id WHERE ws.user_id = :user_id';
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute(['user_id' => $userId]);
        return array_column($stmt->fetchAll(), 'skill_name');
    }
}
