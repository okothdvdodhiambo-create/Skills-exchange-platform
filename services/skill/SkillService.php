<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/bootstrap.php';

class SkillService
{
    public static function getCategories(): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->query('SELECT * FROM categories ORDER BY category_name ASC');
        return $stmt->fetchAll();
    }

    public static function getSkills(): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->query(
            'SELECT s.id, s.skill_name, s.description, c.category_name, c.id AS category_id
             FROM skills s
             INNER JOIN categories c ON c.id = s.category_id
             ORDER BY c.category_name ASC, s.skill_name ASC'
        );
        return $stmt->fetchAll();
    }

    public static function createCategory(array $payload): array
    {
        $name = trim((string) ($payload['category_name'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));

        if ($name === '') {
            throw new InvalidArgumentException('Category name is required.');
        }

        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO categories (category_name, description) VALUES (:category_name, :description)');
        $stmt->execute(['category_name' => $name, 'description' => $description]);

        return ['id' => (int) $pdo->lastInsertId(), 'category_name' => $name, 'description' => $description];
    }

    public static function updateCategory(int $id, array $payload): array
    {
        $name = trim((string) ($payload['category_name'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));

        if ($name === '') {
            throw new InvalidArgumentException('Category name is required.');
        }

        $pdo = getDbConnection();
        $stmt = $pdo->prepare('UPDATE categories SET category_name = :category_name, description = :description WHERE id = :id');
        $stmt->execute(['category_name' => $name, 'description' => $description, 'id' => $id]);

        return ['id' => $id, 'category_name' => $name, 'description' => $description];
    }

    public static function deleteCategory(int $id): void
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function getUserOfferedSkills(int $userId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare(
            'SELECT us.id AS user_skill_id, s.id AS skill_id, s.skill_name, c.category_name, us.experience_level, us.description
             FROM user_skills us
             INNER JOIN skills s ON s.id = us.skill_id
             INNER JOIN categories c ON c.id = s.category_id
             WHERE us.user_id = :user_id
             ORDER BY s.skill_name ASC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function getUserWantedSkills(int $userId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare(
            'SELECT ws.id AS wanted_skill_id, s.id AS skill_id, s.skill_name, c.category_name, ws.description
             FROM wanted_skills ws
             INNER JOIN skills s ON s.id = ws.skill_id
             INNER JOIN categories c ON c.id = s.category_id
             WHERE ws.user_id = :user_id
             ORDER BY s.skill_name ASC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function addOfferedSkill(int $userId, array $payload): array
    {
        $skillId = (int) ($payload['skill_id'] ?? 0);
        $experience = trim((string) ($payload['experience_level'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));

        if ($skillId <= 0 || $experience === '') {
            throw new InvalidArgumentException('Please select a valid skill and experience level.');
        }

        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT id FROM skills WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $skillId]);

        if (!$stmt->fetch()) {
            throw new InvalidArgumentException('The selected skill does not exist.');
        }

        $insert = $pdo->prepare(
            'INSERT INTO user_skills (user_id, skill_id, experience_level, description)
             VALUES (:user_id, :skill_id, :experience_level, :description)'
        );
        $insert->execute(['user_id' => $userId, 'skill_id' => $skillId, 'experience_level' => $experience, 'description' => $description]);

        return ['success' => true, 'message' => 'Offered skill added successfully.'];
    }

    public static function addWantedSkill(int $userId, array $payload): array
    {
        $skillId = (int) ($payload['skill_id'] ?? 0);
        $description = trim((string) ($payload['description'] ?? ''));

        if ($skillId <= 0) {
            throw new InvalidArgumentException('Please select a valid skill.');
        }

        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT id FROM skills WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $skillId]);

        if (!$stmt->fetch()) {
            throw new InvalidArgumentException('The selected skill does not exist.');
        }

        $insert = $pdo->prepare(
            'INSERT INTO wanted_skills (user_id, skill_id, description)
             VALUES (:user_id, :skill_id, :description)'
        );
        $insert->execute(['user_id' => $userId, 'skill_id' => $skillId, 'description' => $description]);

        return ['success' => true, 'message' => 'Wanted skill added successfully.'];
    }

    public static function updateSkill(int $userId, string $type, int $skillRecordId, array $payload): array
    {
        if ($type === 'offer') {
            $table = 'user_skills';
            $idColumn = 'id';
            $fields = ['experience_level', 'description'];
        } elseif ($type === 'want') {
            $table = 'wanted_skills';
            $idColumn = 'id';
            $fields = ['description'];
        } else {
            throw new InvalidArgumentException('Unsupported skill type.');
        }

        $pdo = getDbConnection();
        $check = $pdo->prepare("SELECT id FROM {$table} WHERE user_id = :user_id AND id = :id LIMIT 1");
        $check->execute(['user_id' => $userId, 'id' => $skillRecordId]);

        if (!$check->fetch()) {
            throw new InvalidArgumentException('Skill not found for this user.');
        }

        $updates = [];
        $params = ['user_id' => $userId, 'id' => $skillRecordId];

        foreach ($fields as $field) {
            if (array_key_exists($field, $payload)) {
                $value = trim((string) $payload[$field]);
                $updates[] = "$field = :$field";
                $params[$field] = $value;
            }
        }

        if (empty($updates)) {
            throw new InvalidArgumentException('No valid skill fields were provided.');
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $updates) . " WHERE user_id = :user_id AND id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return ['success' => true, 'message' => 'Skill updated successfully.'];
    }

    public static function deleteSkill(int $userId, string $type, int $skillRecordId): array
    {
        if ($type === 'offer') {
            $table = 'user_skills';
        } elseif ($type === 'want') {
            $table = 'wanted_skills';
        } else {
            throw new InvalidArgumentException('Unsupported skill type.');
        }

        $pdo = getDbConnection();
        $stmt = $pdo->prepare("DELETE FROM {$table} WHERE user_id = :user_id AND id = :id");
        $stmt->execute(['user_id' => $userId, 'id' => $skillRecordId]);

        return ['success' => true, 'message' => 'Skill removed successfully.'];
    }
}
