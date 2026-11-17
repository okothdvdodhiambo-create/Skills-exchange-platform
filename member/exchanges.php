<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') === 'admin') redirect('/community-skill-exchange/admin/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Exchanges</title>
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
                    <a class="nav-link active" href="/community-skill-exchange/member/exchanges.php">My Exchanges</a>
                    <a class="nav-link" href="/community-skill-exchange/member/notifications.php">Notifications</a>
                    <button id="logoutButton" class="btn btn-outline-light mt-4">Logout</button>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 bg-light">
                <div class="page-header px-4"><h2 class="mb-0">My Exchanges</h2></div>
                <div class="card p-4">
                    <table class="table">
                        <thead><tr><th>Partner</th><th>Offered</th><th>Requested</th><th>Status</th><th>Complete</th></tr></thead>
                        <tbody id="exchangeTable"></tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function loadExchanges() {
            const exchanges = await apiRequest('/community-skill-exchange/gateway/index.php/api/exchanges');
            const tbody = document.getElementById('exchangeTable');
            tbody.innerHTML = (exchanges || []).map(exchange => `
                <tr>
                    <td>${exchange.sender_name === '<?= escapeHtml(currentUser()['full_name'] ?? '') ?>' ? exchange.receiver_name : exchange.sender_name}</td>
                    <td>${exchange.offered_skill_name}</td>
                    <td>${exchange.requested_skill_name}</td>
                    <td><span class="badge bg-secondary">${exchange.status}</span></td>
                    <td>
                        ${exchange.status !== 'Completed' ? `<button class="btn btn-sm btn-success" data-complete="${exchange.id}">Mark Complete</button>` : '<span class="text-muted">Completed</span>'}
                    </td>
                </tr>
            `).join('');

            document.querySelectorAll('[data-complete]').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.getAttribute('data-complete');
                    try {
                        await apiRequest(`/community-skill-exchange/gateway/index.php/api/exchanges/${id}/complete`, {method: 'PUT'});
                        loadExchanges();
                    } catch (error) { console.error(error); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', loadExchanges);
    </script>
</body>
</html>
