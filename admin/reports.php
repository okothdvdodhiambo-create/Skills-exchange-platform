<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') !== 'admin') redirect('/community-skill-exchange/member/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports</title>
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
                    <a class="nav-link" href="/community-skill-exchange/admin/dashboard.php">Dashboard</a>
                    <a class="nav-link" href="/community-skill-exchange/admin/users.php">Members</a>
                    <a class="nav-link" href="/community-skill-exchange/admin/categories.php">Categories</a>
                    <a class="nav-link active" href="/community-skill-exchange/admin/reports.php">Reports</a>
                    <button id="logoutButton" class="btn btn-outline-light mt-4">Logout</button>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 bg-light">
                <div class="page-header px-4"><h2 class="mb-0">Reports</h2></div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card p-4">
                            <h4>Exchange Status</h4>
                            <div id="statusReport"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-4">
                            <h4>Popular Skills</h4>
                            <div id="skillReport"></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function loadReports() {
            const [status, skills] = await Promise.all([
                apiRequest('/community-skill-exchange/gateway/index.php/api/reports/exchanges'),
                apiRequest('/community-skill-exchange/gateway/index.php/api/reports/skills')
            ]);

            document.getElementById('statusReport').innerHTML = (status || []).map(item => `
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>${item.status}</span>
                    <strong>${item.total}</strong>
                </div>
            `).join('');

            document.getElementById('skillReport').innerHTML = (skills || []).map(item => `
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>${item.skill_name}</span>
                    <strong>${Number(item.offered_count || 0) + Number(item.wanted_count || 0)}</strong>
                </div>
            `).join('');
        }

        document.addEventListener('DOMContentLoaded', loadReports);
    </script>
</body>
</html>
