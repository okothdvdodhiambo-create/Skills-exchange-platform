<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') === 'admin') redirect('/community-skill-exchange/admin/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/community-skill-exchange/frontend/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="/community-skill-exchange/frontend/js/app.js" defer></script>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <aside class="col-lg-2 sidebar p-3">
                <h4 class="text-white mb-4"><i class="fa-solid fa-handshake me-2"></i>Skill Exchange</h4>
                <nav class="nav flex-column">
                    <a class="nav-link" href="/community-skill-exchange/member/dashboard.php">Dashboard</a>
                    <a class="nav-link" href="/community-skill-exchange/member/profile.php">My Profile</a>
                    <a class="nav-link" href="/community-skill-exchange/member/my_skills.php">Manage My Skills</a>
                    <a class="nav-link" href="/community-skill-exchange/member/find_skills.php">Find Skills</a>
                    <a class="nav-link" href="/community-skill-exchange/member/matches.php">Suggested Matches</a>
                    <a class="nav-link" href="/community-skill-exchange/member/requests.php">Exchange Requests</a>
                    <a class="nav-link" href="/community-skill-exchange/member/exchanges.php">My Exchanges</a>
                    <a class="nav-link active" href="/community-skill-exchange/member/notifications.php">Notifications</a>
                    <button id="logoutButton" class="btn btn-outline-light mt-4">Logout</button>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 bg-light">
                <div class="page-header px-4"><h2 class="mb-0">Notifications</h2></div>
                <div class="card p-4">
                    <div id="notificationList" class="list-group"></div>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function loadNotifications() {
            const notifications = await apiRequest('/community-skill-exchange/gateway/index.php/api/notifications');
            const list = document.getElementById('notificationList');
            list.innerHTML = (notifications || []).map(item => `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <span>${item.message}</span>
                        <small class="text-muted">${new Date(item.created_at).toLocaleDateString()}</small>
                    </div>
                    ${item.is_read == 0 ? `<button class="btn btn-sm btn-outline-primary mt-2" data-read="${item.id}">Mark as Read</button>` : '<span class="badge bg-success mt-2">Read</span>'}
                </div>
            `).join('');

            document.querySelectorAll('[data-read]').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.getAttribute('data-read');
                    await apiRequest(`/community-skill-exchange/gateway/index.php/api/notifications/${id}/read`, {method: 'PUT'});
                    loadNotifications();
                });
            });
        }

        document.addEventListener('DOMContentLoaded', loadNotifications);
    </script>
</body>
</html>
