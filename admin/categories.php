<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (!isLoggedIn()) redirect('/community-skill-exchange/login.php');
if ((currentUser()['role'] ?? 'member') !== 'admin') redirect('/community-skill-exchange/member/dashboard.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories</title>
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
                    <a class="nav-link active" href="/community-skill-exchange/admin/categories.php">Categories</a>
                    <a class="nav-link" href="/community-skill-exchange/admin/reports.php">Reports</a>
                    <button id="logoutButton" class="btn btn-outline-light mt-4">Logout</button>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 bg-light">
                <div class="page-header px-4"><h2 class="mb-0">Category Management</h2></div>
                <div id="messageBox" class="alert alert-box" role="alert"></div>
                <div class="card p-4 mb-4">
                    <form id="categoryForm">
                        <div class="row g-3">
                            <div class="col-md-4"><input class="form-control" name="category_name" placeholder="Category name" required></div>
                            <div class="col-md-6"><input class="form-control" name="description" placeholder="Description"></div>
                            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Add</button></div>
                        </div>
                    </form>
                </div>
                <div class="card p-4">
                    <table class="table"><thead><tr><th>Name</th><th>Description</th><th>Delete</th></tr></thead><tbody id="categoryTable"></tbody></table>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function loadCategories() {
            const categories = await apiRequest('/community-skill-exchange/gateway/index.php/api/admin/categories');
            const table = document.getElementById('categoryTable');
            table.innerHTML = (categories || []).map(item => `
                <tr>
                    <td>${item.category_name}</td>
                    <td>${item.description || '—'}</td>
                    <td><button class="btn btn-sm btn-danger" data-delete="${item.id}">Delete</button></td>
                </tr>
            `).join('');

            document.querySelectorAll('[data-delete]').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.getAttribute('data-delete');
                    try {
                        await apiRequest(`/community-skill-exchange/gateway/index.php/api/admin/categories/${id}`, {method: 'DELETE'});
                        loadCategories();
                    } catch (error) { showMessage('danger', error.message); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await loadCategories();
            const form = document.getElementById('categoryForm');
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                try {
                    const payload = Object.fromEntries(new FormData(form).entries());
                    await apiRequest('/community-skill-exchange/gateway/index.php/api/admin/categories', {method: 'POST', body: JSON.stringify(payload)});
                    form.reset();
                    await loadCategories();
                } catch (error) { showMessage('danger', error.message); }
            });
        });
    </script>
</body>
</html>
