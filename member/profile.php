<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') === 'admin') redirect('/community-skill-exchange/admin/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
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
                    <a class="nav-link active" href="/community-skill-exchange/member/profile.php">My Profile</a>
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
                    <h2 class="mb-0">My Profile</h2>
                </div>
                <div class="card p-4">
                    <div id="messageBox" class="alert alert-box" role="alert"></div>
                    <form id="profileForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input class="form-control" name="full_name" id="full_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input class="form-control" id="email" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input class="form-control" name="phone" id="phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input class="form-control" name="location" id="location">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Save Profile</button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function loadProfile() {
            const data = await apiRequest('/community-skill-exchange/gateway/index.php/api/users/profile');
            document.getElementById('full_name').value = data.full_name || '';
            document.getElementById('email').value = data.email || '';
            document.getElementById('phone').value = data.phone || '';
            document.getElementById('location').value = data.location || '';
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await loadProfile();
            const form = document.getElementById('profileForm');
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                try {
                    const payload = Object.fromEntries(new FormData(form).entries());
                    const response = await apiRequest('/community-skill-exchange/gateway/index.php/api/users/profile', {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    });
                    showMessage('success', 'Profile updated.');
                } catch (error) {
                    showMessage('danger', error.message);
                }
            });
        });
    </script>
</body>
</html>
