<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') === 'admin') redirect('/community-skill-exchange/admin/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Skills</title>
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
                    <a class="nav-link active" href="/community-skill-exchange/member/find_skills.php">Find Skills</a>
                    <a class="nav-link" href="/community-skill-exchange/member/matches.php">Suggested Matches</a>
                    <a class="nav-link" href="/community-skill-exchange/member/requests.php">Exchange Requests</a>
                    <a class="nav-link" href="/community-skill-exchange/member/exchanges.php">My Exchanges</a>
                    <a class="nav-link" href="/community-skill-exchange/member/notifications.php">Notifications</a>
                    <button id="logoutButton" class="btn btn-outline-light mt-4">Logout</button>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 bg-light">
                <div class="page-header px-4"><h2 class="mb-0">Find Skills</h2></div>
                <div class="card p-4">
                    <table class="table">
                        <thead>
                            <tr><th>Skill</th><th>Category</th><th>Description</th></tr>
                        </thead>
                        <tbody id="skillTableBody"></tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function loadSkills() {
            const response = await apiRequest('/community-skill-exchange/gateway/index.php/api/skills');
            const skills = response.skills || [];
            document.getElementById('skillTableBody').innerHTML = skills.map(skill => `
                <tr>
                    <td>${skill.skill_name}</td>
                    <td>${skill.category_name}</td>
                    <td>${skill.description || '—'}</td>
                </tr>
            `).join('');
        }

        document.addEventListener('DOMContentLoaded', loadSkills);
    </script>
</body>
</html>
