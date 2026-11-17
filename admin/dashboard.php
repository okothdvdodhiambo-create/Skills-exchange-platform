<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') !== 'admin') redirect('/community-skill-exchange/member/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/community-skill-exchange/frontend/css/style.css">
    <script src="/community-skill-exchange/frontend/js/app.js" defer></script>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <aside class="col-lg-2 sidebar p-3">
                <h4 class="text-white mb-4"><i class="fa-solid fa-shield-halved me-2"></i>Admin</h4>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="/community-skill-exchange/admin/dashboard.php">Dashboard</a>
                    <a class="nav-link" href="/community-skill-exchange/admin/users.php">Members</a>
                    <a class="nav-link" href="/community-skill-exchange/admin/categories.php">Categories</a>
                    <a class="nav-link" href="/community-skill-exchange/admin/reports.php">Reports</a>
                    <button id="logoutButton" class="btn btn-outline-light mt-4">Logout</button>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 bg-light">
                <div class="page-header px-4"><h2 class="mb-0">Admin Dashboard</h2></div>
                <div class="row g-4 mb-4">
                    <div class="col-md-3"><div class="stat-card"><div class="text-muted">Total Members</div><h3 id="memberCount">0</h3></div></div>
                    <div class="col-md-3"><div class="stat-card"><div class="text-muted">Skills</div><h3 id="skillCount">0</h3></div></div>
                    <div class="col-md-3"><div class="stat-card"><div class="text-muted">Pending</div><h3 id="pendingCount">0</h3></div></div>
                    <div class="col-md-3"><div class="stat-card"><div class="text-muted">Completed</div><h3 id="completedCount">0</h3></div></div>
                </div>
                <div class="card p-4">
                    <h4>Recent Activity</h4>
                    <table class="table"><thead><tr><th>ID</th><th>Sender</th><th>Receiver</th><th>Status</th></tr></thead><tbody id="activityTable"></tbody></table>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function loadStats() {
            const stats = await apiRequest('/community-skill-exchange/gateway/index.php/api/reports/dashboard');
            document.getElementById('memberCount').textContent = stats.total_members || 0;
            document.getElementById('skillCount').textContent = stats.total_skills || 0;
            document.getElementById('pendingCount').textContent = stats.pending_exchanges || 0;
            document.getElementById('completedCount').textContent = stats.completed_exchanges || 0;
            const table = document.getElementById('activityTable');
            const rows = (stats.recent_activity || []).map(item => `
                <tr>
                    <td>${item.id}</td>
                    <td>${item.sender_name}</td>
                    <td>${item.receiver_name}</td>
                    <td><span class="badge bg-secondary">${item.status}</span></td>
                </tr>
            `).join('');
            table.innerHTML = rows || '<tr><td colspan="4">No recent activity.</td></tr>';
        }

        document.addEventListener('DOMContentLoaded', loadStats);
    </script>
</body>
</html>
