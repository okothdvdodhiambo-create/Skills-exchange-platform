<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') === 'admin') redirect('/community-skill-exchange/admin/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suggested Matches</title>
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
                    <a class="nav-link active" href="/community-skill-exchange/member/matches.php">Suggested Matches</a>
                    <a class="nav-link" href="/community-skill-exchange/member/requests.php">Exchange Requests</a>
                    <a class="nav-link" href="/community-skill-exchange/member/exchanges.php">My Exchanges</a>
                    <a class="nav-link" href="/community-skill-exchange/member/notifications.php">Notifications</a>
                    <button id="logoutButton" class="btn btn-outline-light mt-4">Logout</button>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 bg-light">
                <div class="page-header px-4"><h2 class="mb-0">Suggested Matches</h2></div>
                <div id="matchResults" class="row g-4"></div>
            </main>
        </div>
    </div>

    <script>
        async function loadMatches() {
            const matches = await apiRequest('/community-skill-exchange/gateway/index.php/api/matches');
            const container = document.getElementById('matchResults');
            container.innerHTML = (matches || []).map(match => `
                <div class="col-md-6">
                    <div class="card p-4 match-card h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">${match.full_name}</h5>
                            <span class="badge bg-primary-subtle text-primary">${match.location || 'Unknown'}</span>
                        </div>
                        <p class="text-muted small mb-2">${match.match_reason}</p>
                        <p><strong>Offers:</strong> ${(match.offered_skills || []).join(', ') || '—'}</p>
                        <p><strong>Needs:</strong> ${(match.wanted_skills || []).join(', ') || '—'}</p>
                        <form class="exchange-form" data-user-id="${match.user_id}">
                            <input type="hidden" name="receiver_id" value="${match.user_id}">
                            <button type="submit" class="btn btn-primary">Send Exchange Request</button>
                        </form>
                    </div>
                </div>
            `).join('') || '<div class="col-12"><p class="text-muted">No matches available right now.</p></div>';

            document.querySelectorAll('.exchange-form').forEach(form => {
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    const receiverId = form.dataset.userId;
                    const payload = {
                        receiver_id: receiverId,
                        offered_skill_id: 1,
                        requested_skill_id: 2,
                        message: 'I would like to exchange skills with you.'
                    };
                    try {
                        await apiRequest('/community-skill-exchange/gateway/index.php/api/exchanges/request', {method: 'POST', body: JSON.stringify(payload)});
                        showMessage('success', 'Exchange request sent.');
                    } catch (error) { showMessage('danger', error.message); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', loadMatches);
    </script>
</body>
</html>
