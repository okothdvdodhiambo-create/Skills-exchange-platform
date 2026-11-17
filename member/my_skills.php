<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') === 'admin') redirect('/community-skill-exchange/admin/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Skills</title>
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
                    <a class="nav-link active" href="/community-skill-exchange/member/my_skills.php">Manage My Skills</a>
                    <a class="nav-link" href="/community-skill-exchange/member/find_skills.php">Find Skills</a>
                    <a class="nav-link" href="/community-skill-exchange/member/matches.php">Suggested Matches</a>
                    <a class="nav-link" href="/community-skill-exchange/member/requests.php">Exchange Requests</a>
                    <a class="nav-link" href="/community-skill-exchange/member/exchanges.php">My Exchanges</a>
                    <a class="nav-link" href="/community-skill-exchange/member/notifications.php">Notifications</a>
                    <button id="logoutButton" class="btn btn-outline-light mt-4">Logout</button>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 bg-light">
                <div class="page-header px-4"><h2 class="mb-0">Manage My Skills</h2></div>
                <div id="messageBox" class="alert alert-box" role="alert"></div>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card p-4">
                            <h4>Add Offered Skill</h4>
                            <form id="offerForm">
                                <div class="mb-3">
                                    <label class="form-label">Skill</label>
                                    <select class="form-select" name="skill_id" id="offerSkillSelect"></select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Experience Level</label>
                                    <input class="form-control" name="experience_level" placeholder="Beginner, Intermediate..." required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                                <button class="btn btn-primary" type="submit">Add Offered Skill</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card p-4">
                            <h4>Add Wanted Skill</h4>
                            <form id="wantedForm">
                                <div class="mb-3">
                                    <label class="form-label">Skill</label>
                                    <select class="form-select" name="skill_id" id="wantedSkillSelect"></select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                                <button class="btn btn-primary" type="submit">Add Wanted Skill</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function loadSkills() {
            const response = await apiRequest('/community-skill-exchange/gateway/index.php/api/skills');
            const skills = response.skills || [];
            const options = skills.map(skill => `<option value="${skill.id}">${skill.skill_name}</option>`).join('');
            document.getElementById('offerSkillSelect').innerHTML = options;
            document.getElementById('wantedSkillSelect').innerHTML = options;
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await loadSkills();
            const offerForm = document.getElementById('offerForm');
            const wantedForm = document.getElementById('wantedForm');

            offerForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                try {
                    const payload = Object.fromEntries(new FormData(offerForm).entries());
                    await apiRequest('/community-skill-exchange/gateway/index.php/api/skills/offer', {method: 'POST', body: JSON.stringify(payload)});
                    showMessage('success', 'Offered skill added successfully.');
                    offerForm.reset();
                } catch (error) { showMessage('danger', error.message); }
            });

            wantedForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                try {
                    const payload = Object.fromEntries(new FormData(wantedForm).entries());
                    await apiRequest('/community-skill-exchange/gateway/index.php/api/skills/wanted', {method: 'POST', body: JSON.stringify(payload)});
                    showMessage('success', 'Wanted skill added successfully.');
                    wantedForm.reset();
                } catch (error) { showMessage('danger', error.message); }
            });
        });
    </script>
</body>
</html>
