<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') === 'admin') redirect('/community-skill-exchange/admin/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Dashboard</title>
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
                    <a class="nav-link active" href="/community-skill-exchange/member/dashboard.php">Dashboard</a>
                    <a class="nav-link" href="/community-skill-exchange/member/profile.php">My Profile</a>
                    <a class="nav-link" href="/community-skill-exchange/member/my_skills.php">Manage My Skills</a>
                    <a class="nav-link" href="/community-skill-exchange/member/find_skills.php">Find Skills</a>
                    <a class="nav-link" href="/community-skill-exchange/member/matches.php">Suggested Matches</a>
                    <a class="nav-link" href="/community-skill-exchange/member/requests.php">Exchange Requests</a>
                    <a class="nav-link" href="/community-skill-exchange/member/exchanges.php">My Exchanges</a>
                    <a class="nav-link" href="/community-skill-exchange/member/notifications.php">Notifications</a>
                    <button id="logoutButton" class="btn btn-outline-light mt-4">Logout</button>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 bg-light">
                <div class="page-header px-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <p class="mb-1 text-white-50">Welcome back</p>
                            <h2 class="mb-0">Hello, <?= escapeHtml(currentUser()['full_name'] ?? 'Member') ?>!</h2>
                        </div>
                        <a href="/community-skill-exchange/member/profile.php" class="btn btn-light">Edit profile</a>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card"><div class="text-muted">Offered Skills</div><h3 id="offeredCount">0</h3></div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card"><div class="text-muted">Needed Skills</div><h3 id="wantedCount">0</h3></div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card"><div class="text-muted">Pending Requests</div><h3 id="pendingCount">0</h3></div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card"><div class="text-muted">Completed Exchanges</div><h3 id="completedCount">0</h3></div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card p-4 h-100">
                            <h4 class="mb-3">Suggested Matches</h4>
                            <div id="matchList"></div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card p-4 h-100">
                            <h4 class="mb-3">Recent Notifications</h4>
                            <div id="notificationList"></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function loadDashboard() {
            try {
                const [profile, skills, matches, exchanges, notifications] = await Promise.all([
                    apiRequest('/community-skill-exchange/gateway/index.php/api/users/profile'),
                    apiRequest('/community-skill-exchange/gateway/index.php/api/skills'),
                    apiRequest('/community-skill-exchange/gateway/index.php/api/matches'),
                    apiRequest('/community-skill-exchange/gateway/index.php/api/exchanges'),
                    apiRequest('/community-skill-exchange/gateway/index.php/api/notifications')
                ]);

                document.getElementById('offeredCount').textContent = (profile && profile.id) ? 'Loaded' : '0';
                document.getElementById('pendingCount').textContent = String((exchanges || []).filter(item => item.status === 'Pending').length);
                document.getElementById('completedCount').textContent = String((exchanges || []).filter(item => item.status === 'Completed').length);

                const matchList = document.getElementById('matchList');
                const items = matches || [];
                matchList.innerHTML = items.length ? items.slice(0, 3).map(item => `
                    <div class="border rounded p-3 mb-3 match-card">
                        <div class="d-flex justify-content-between">
                            <strong>${item.full_name}</strong>
                            <span class="badge bg-primary-subtle text-primary">${item.location || 'Unknown'}</span>
                        </div>
                        <p class="text-muted small mb-2">${item.match_reason || 'Mutual exchange opportunity'}</p>
                        <p class="small mb-1"><strong>Offers:</strong> ${(item.offered_skills || []).join(', ')}</p>
                        <p class="small mb-0"><strong>Needs:</strong> ${(item.wanted_skills || []).join(', ')}</p>
                    </div>
                `).join('') : '<p class="text-muted">No matches yet.</p>';

                const notificationList = document.getElementById('notificationList');
                const notificationsList = notifications || [];
                notificationList.innerHTML = notificationsList.length ? notificationsList.slice(0, 4).map(item => `
                    <div class="border-bottom pb-2 mb-2">
                        <p class="mb-1">${item.message}</p>
                        <small class="text-muted">${new Date(item.created_at).toLocaleDateString()}</small>
                    </div>
                `).join('') : '<p class="text-muted">No notifications.</p>';
            } catch (error) {
                console.error(error);
            }
        }

        document.addEventListener('DOMContentLoaded', loadDashboard);
    </script>
</body>
</html>
