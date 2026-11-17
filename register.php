<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (isLoggedIn()) {
    redirect('/community-skill-exchange/member/dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Community Skill Exchange</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/community-skill-exchange/frontend/css/style.css">
    <script src="/community-skill-exchange/frontend/js/app.js" defer></script>
</head>
<body class="bg-light">
    <div class="container">
        <div class="card form-card shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <h2 class="text-center mb-4">Create Account</h2>
                <div id="messageBox" class="alert alert-box" role="alert"></div>
                <form id="registerForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
                <p class="mt-3 text-center mb-0">Already a member? <a href="/community-skill-exchange/login.php">Login here</a></p>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('registerForm');
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const button = form.querySelector('button[type="submit"]');
                button.disabled = true;
                button.textContent = 'Creating account...';
                try {
                    const payload = Object.fromEntries(new FormData(form).entries());
                    const data = await apiRequest('/community-skill-exchange/gateway/index.php/api/users/register', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });
                    showMessage('success', 'Registration successful. Please login to continue.');
                    setTimeout(() => window.location.href = '/community-skill-exchange/login.php', 800);
                } catch (error) {
                    showMessage('danger', error.message);
                    button.disabled = false;
                    button.textContent = 'Register';
                }
            });
        });
    </script>
</body>
</html>
