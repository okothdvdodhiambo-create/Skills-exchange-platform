<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/user/UserService.php';
require_once __DIR__ . '/../services/skill/SkillService.php';
require_once __DIR__ . '/../services/matching/MatchingService.php';
require_once __DIR__ . '/../services/exchange/ExchangeService.php';
require_once __DIR__ . '/../services/notification/NotificationService.php';
require_once __DIR__ . '/../services/reporting/ReportingService.php';

function gatewayRoute(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?? '/';

    $prefixes = ['/community-skill-exchange/gateway/index.php', '/gateway/index.php', '/community-skill-exchange/gateway', '/gateway'];
    foreach ($prefixes as $prefix) {
        if (str_starts_with($path, $prefix)) {
            $path = substr($path, strlen($prefix));
            break;
        }
    }

    $path = str_replace('//', '/', $path);
    return $path === '' ? '/' : $path;
}

$route = gatewayRoute();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($route === '/api/users/register' && $method === 'POST') {
        $payload = readRequestPayload();
        $response = UserService::register($payload);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/users/login' && $method === 'POST') {
        $payload = readRequestPayload();
        $response = UserService::login($payload);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/users/logout' && $method === 'POST') {
        $response = UserService::logout();
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/users/profile' && $method === 'GET') {
        requireLogin();
        $response = UserService::getProfile((int) currentUser()['id']);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/users/profile' && $method === 'PUT') {
        requireLogin();
        $payload = readRequestPayload();
        $response = UserService::updateProfile((int) currentUser()['id'], $payload);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/skills' && $method === 'GET') {
        requireLogin();
        $skills = SkillService::getSkills();
        $categories = SkillService::getCategories();
        sendJson(['success' => true, 'data' => ['categories' => $categories, 'skills' => $skills]], 200);
    }

    if ($route === '/api/skills/offer' && $method === 'POST') {
        requireLogin();
        $payload = readRequestPayload();
        $response = SkillService::addOfferedSkill((int) currentUser()['id'], $payload);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/skills/wanted' && $method === 'POST') {
        requireLogin();
        $payload = readRequestPayload();
        $response = SkillService::addWantedSkill((int) currentUser()['id'], $payload);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if (preg_match('#^/api/skills/([0-9]+)$#', $route, $matches) && $method === 'PUT') {
        requireLogin();
        $type = $_GET['type'] ?? 'offer';
        $payload = readRequestPayload();
        $response = SkillService::updateSkill((int) currentUser()['id'], $type, (int) $matches[1], $payload);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if (preg_match('#^/api/skills/([0-9]+)$#', $route, $matches) && $method === 'DELETE') {
        requireLogin();
        $type = $_GET['type'] ?? 'offer';
        $response = SkillService::deleteSkill((int) currentUser()['id'], $type, (int) $matches[1]);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/matches' && $method === 'GET') {
        requireLogin();
        $matches = MatchingService::getMatchesForUser((int) currentUser()['id']);
        sendJson(['success' => true, 'data' => $matches], 200);
    }

    if ($route === '/api/exchanges/request' && $method === 'POST') {
        requireLogin();
        $payload = readRequestPayload();
        $response = ExchangeService::requestExchange((int) currentUser()['id'], $payload);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/exchanges' && $method === 'GET') {
        requireLogin();
        $response = ExchangeService::listForUser((int) currentUser()['id']);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if (preg_match('#^/api/exchanges/([0-9]+)/accept$#', $route, $matches) && $method === 'PUT') {
        requireLogin();
        $response = ExchangeService::updateStatus((int) $matches[1], (int) currentUser()['id'], 'accept');
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if (preg_match('#^/api/exchanges/([0-9]+)/reject$#', $route, $matches) && $method === 'PUT') {
        requireLogin();
        $response = ExchangeService::updateStatus((int) $matches[1], (int) currentUser()['id'], 'reject');
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if (preg_match('#^/api/exchanges/([0-9]+)/complete$#', $route, $matches) && $method === 'PUT') {
        requireLogin();
        $response = ExchangeService::updateStatus((int) $matches[1], (int) currentUser()['id'], 'complete');
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/notifications' && $method === 'GET') {
        requireLogin();
        $response = NotificationService::listForUser((int) currentUser()['id']);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if (preg_match('#^/api/notifications/([0-9]+)/read$#', $route, $matches) && $method === 'PUT') {
        requireLogin();
        $response = NotificationService::markAsRead((int) $matches[1], (int) currentUser()['id']);
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/reports/dashboard' && $method === 'GET') {
        requireRole('admin');
        $response = ReportingService::dashboardStats();
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/reports/exchanges' && $method === 'GET') {
        requireRole('admin');
        $response = ReportingService::exchangesByStatus();
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/reports/skills' && $method === 'GET') {
        requireRole('admin');
        $response = ReportingService::skillPopularity();
        sendJson(['success' => true, 'data' => $response], 200);
    }

    if ($route === '/api/admin/members' && $method === 'GET') {
        requireRole('admin');
        sendJson(['success' => true, 'data' => UserService::listMembers()], 200);
    }

    if ($route === '/api/admin/categories' && $method === 'GET') {
        requireRole('admin');
        sendJson(['success' => true, 'data' => SkillService::getCategories()], 200);
    }

    if ($route === '/api/admin/categories' && $method === 'POST') {
        requireRole('admin');
        $payload = readRequestPayload();
        sendJson(['success' => true, 'data' => SkillService::createCategory($payload)], 200);
    }

    if (preg_match('#^/api/admin/categories/([0-9]+)$#', $route, $matches) && $method === 'PUT') {
        requireRole('admin');
        $payload = readRequestPayload();
        sendJson(['success' => true, 'data' => SkillService::updateCategory((int) $matches[1], $payload)], 200);
    }

    if (preg_match('#^/api/admin/categories/([0-9]+)$#', $route, $matches) && $method === 'DELETE') {
        requireRole('admin');
        SkillService::deleteCategory((int) $matches[1]);
        sendJson(['success' => true, 'data' => ['message' => 'Category deleted.']], 200);
    }

    sendJson(['success' => false, 'message' => 'Route not found.'], 404);
} catch (Throwable $exception) {
    sendJson(['success' => false, 'message' => $exception->getMessage()], 400);
}
