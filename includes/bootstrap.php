<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
            sendJson(['success' => false, 'message' => 'Authentication required.'], 401);
        }

        header('Location: /community-skill-exchange/login.php');
        exit;
    }
}

function requireRole(string $role): void
{
    requireLogin();

    if ((currentUser()['role'] ?? '') !== $role) {
        if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
            sendJson(['success' => false, 'message' => 'Access denied.'], 403);
        }

        header('Location: /community-skill-exchange/login.php');
        exit;
    }
}

function readRequestPayload(): array
{
    $rawBody = file_get_contents('php://input');
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if ($rawBody !== false && trim($rawBody) !== '') {
        $decoded = json_decode($rawBody, true);
        if (is_array($decoded) && (str_contains(strtolower($contentType), 'application/json') || json_last_error() === JSON_ERROR_NONE)) {
            return $decoded;
        }

        if (str_contains(strtolower($contentType), 'application/json')) {
            parse_str($rawBody, $decodedForm);
            if (!empty($decodedForm)) {
                return $decodedForm;
            }
            throw new InvalidArgumentException('Malformed JSON request body.');
        }

        parse_str($rawBody, $decodedForm);
        if (!empty($decodedForm)) {
            return $decodedForm;
        }
    }

    return is_array($_POST) ? $_POST : [];
}

function sendJson(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

function escapeHtml(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool
{
    return !empty($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}
