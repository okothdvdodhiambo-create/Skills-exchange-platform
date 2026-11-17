<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') !== 'admin') redirect('/community-skill-exchange/member/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Members</title>
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
                    <a class="nav-link active" href="/community-skill-exchange/admin/users.php">Members</a>
                    <a class="nav-link" href="/community-skill-exchange/admin/categories.php">Categories</a>
                    <a class="nav-link" href="/community-skill-exchange/admin/reports.php">Reports</a>
                    <button id="logoutButton" class="btn btn-outline-light mt-4">Logout</button>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 bg-light">
                <div class="page-header px-4"><h2 class="mb-0">Member Management</h2></div>
                <div class="card p-4">
                    <table class="table">
                        <thead><tr><th>Name</th><th>Email</th><th>Location</th><th>Role</th></tr></thead>
                        <tbody id="userTable"></tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function loadUsers() {
            const users = await apiRequest('/community-skill-exchange/gateway/index.php/api/admin/members');
            const table = document.getElementById('userTable');
            table.innerHTML = (users || []).map(member => `
                <tr>
                    <td>${member.full_name}</td>
                    <td>${member.email}</td>
                    <td>${member.location || '—'}</td>
                    <td><span class="badge bg-primary">${member.role}</span></td>
                </tr>
            `).join('');
        }

        document.addEventListener('DOMContentLoaded', loadUsers);
    </script>
</body>
</html>
