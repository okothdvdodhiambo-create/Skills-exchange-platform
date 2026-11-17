<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') === 'admin') redirect('/community-skill-exchange/admin/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exchange Requests</title>
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
                    <a class="nav-link active" href="/community-skill-exchange/member/requests.php">Exchange Requests</a>
                    <a class="nav-link" href="/community-skill-exchange/member/exchanges.php">My Exchanges</a>
                    <a class="nav-link" href="/community-skill-exchange/member/notifications.php">Notifications</a>
                    <button id="logoutButton" class="btn btn-outline-light mt-4">Logout</button>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 bg-light">
                <div class="page-header px-4"><h2 class="mb-0">Exchange Requests</h2></div>
                <div id="messageBox" class="alert alert-box" role="alert"></div>
                <div class="card p-4">
                    <table class="table">
                        <thead><tr><th>From</th><th>Skill Offer</th><th>Requested</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody id="requestTable"></tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function loadRequests() {
            const requests = await apiRequest('/community-skill-exchange/gateway/index.php/api/exchanges');
            const tbody = document.getElementById('requestTable');
            tbody.innerHTML = (requests || []).map(request => `
                <tr>
                    <td>${request.sender_name || request.sender_id}</td>
                    <td>${request.offered_skill_name}</td>
                    <td>${request.requested_skill_name}</td>
                    <td><span class="badge bg-secondary">${request.status}</span></td>
                    <td>
                        ${request.receiver_id == <?= (int) currentUser()['id'] ?> && request.status === 'Pending' ? `<button class="btn btn-sm btn-success me-2" data-action="accept" data-id="${request.id}">Accept</button><button class="btn btn-sm btn-danger" data-action="reject" data-id="${request.id}">Reject</button>` : '<span class="text-muted">No action</span>'}
                    </td>
                </tr>
            `).join('');

            document.querySelectorAll('[data-action]').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.getAttribute('data-id');
                    const action = btn.getAttribute('data-action');
                    try {
                        await apiRequest(`/community-skill-exchange/gateway/index.php/api/exchanges/${id}/${action}`, {method: 'PUT'});
                        showMessage('success', 'Exchange request updated.');
                        loadRequests();
                    } catch (error) { showMessage('danger', error.message); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', loadRequests);
    </script>
</body>
</html>
