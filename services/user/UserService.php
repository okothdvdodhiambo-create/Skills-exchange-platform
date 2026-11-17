<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/bootstrap.php';

class UserService
{
    public static function register(array $payload): array
    {
        $fullName = trim((string) ($payload['full_name'] ?? ''));
        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $password = (string) ($payload['password'] ?? '');
        $phone = trim((string) ($payload['phone'] ?? ''));
        $location = trim((string) ($payload['location'] ?? ''));

        if ($fullName === '' || $email === '' || $password === '') {
            throw new InvalidArgumentException('Full name, email and password are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }

        if (strlen($password) < 6) {
            throw new InvalidArgumentException('Password must be at least 6 characters long.');
        }

        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {
            throw new InvalidArgumentException('An account with that email already exists.');
        }

        $profileImage = $payload['profile_image'] ?? 'default-user.png';
        $role = strtolower((string) ($payload['role'] ?? 'member'));

        $stmt = $pdo->prepare(
            'INSERT INTO users (full_name, email, password, phone, location, profile_image, role, created_at)
             VALUES (:full_name, :email, :password, :phone, :location, :profile_image, :role, NOW())'
        );

        $stmt->execute([
            'full_name' => $fullName,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => $phone,
            'location' => $location,
            'profile_image' => $profileImage,
            'role' => in_array($role, ['member', 'admin'], true) ? $role : 'member',
        ]);

        $userId = (int) $pdo->lastInsertId();
        return self::getProfile($userId);
    }

    public static function login(array $payload): array
    {
        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $password = (string) ($payload['password'] ?? '');

        if ($email === '' || $password === '') {
            throw new InvalidArgumentException('Email and password are required.');
        }

        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            throw new InvalidArgumentException('Invalid email or password.');
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'location' => $user['location'],
            'profile_image' => $user['profile_image'],
        ];

        return [
            'success' => true,
            'message' => 'Login successful.',
            'user' => $_SESSION['user'],
        ];
    }

    public static function logout(): array
    {
        session_unset();
        session_destroy();

        return ['success' => true, 'message' => 'Logged out successfully.'];
    }

    public static function getProfile(int $userId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT id, full_name, email, phone, location, profile_image, role, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new RuntimeException('User profile not found.');
        }

        return $user;
    }

    public static function updateProfile(int $userId, array $payload): array
    {
        $allowedFields = ['full_name', 'phone', 'location'];
        $updates = [];
        $params = ['id' => $userId];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $payload)) {
                $value = trim((string) $payload[$field]);
                if ($field === 'full_name' && $value === '') {
                    throw new InvalidArgumentException('Full name cannot be empty.');
                }
                $updates[] = "$field = :$field";
                $params[$field] = $value;
            }
        }

        if (empty($updates)) {
            throw new InvalidArgumentException('No valid profile fields were supplied.');
        }

        $pdo = getDbConnection();
        $sql = 'UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return self::getProfile($userId);
    }

    public static function listMembers(): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->query('SELECT id, full_name, email, phone, location, role, created_at FROM users ORDER BY full_name ASC');
        return $stmt->fetchAll();
    }
}
