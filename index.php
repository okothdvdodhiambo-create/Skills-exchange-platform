<?php
require_once __DIR__ . '/includes/bootstrap.php';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Skill Exchange</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="/community-skill-exchange/frontend/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand text-primary" href="/community-skill-exchange/index.php"><i class="fa-solid fa-handshake me-2"></i>Community Skill Exchange</a>
            <div class="ms-auto d-flex gap-2">
                <a href="/community-skill-exchange/about.php" class="btn btn-link text-dark text-decoration-none">About</a>
                <a href="/community-skill-exchange/how-it-works.php" class="btn btn-link text-dark text-decoration-none">How it Works</a>
                <a href="/community-skill-exchange/login.php" class="btn btn-outline-primary">Login</a>
                <a href="/community-skill-exchange/register.php" class="btn btn-primary">Register</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-primary-subtle text-primary mb-3">Community Service Exchange</span>
                    <h1 class="display-5 fw-bold">Trade skills, build trust, and strengthen your community.</h1>
                    <p class="lead text-muted mt-3">Connect with neighbors, share expertise, and discover skill-for-skill exchanges that create value without cash.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="/community-skill-exchange/register.php" class="btn btn-primary btn-lg">Join Now</a>
                        <a href="/community-skill-exchange/login.php" class="btn btn-outline-primary btn-lg">Member Login</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card p-4 border-0 shadow-lg">
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="stat-card h-100">
                                    <i class="fa-solid fa-users text-primary fs-2"></i>
                                    <h4 class="mt-3 mb-0">Members</h4>
                                    <p class="text-muted mb-0">Connected community</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card h-100">
                                    <i class="fa-solid fa-list-check text-primary fs-2"></i>
                                    <h4 class="mt-3 mb-0">Skills</h4>
                                    <p class="text-muted mb-0">Shared expertise</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card h-100">
                                    <i class="fa-solid fa-arrows-rotate text-primary fs-2"></i>
                                    <h4 class="mt-3 mb-0">Matches</h4>
                                    <p class="text-muted mb-0">Mutual exchanges</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card h-100">
                                    <i class="fa-solid fa-bell text-primary fs-2"></i>
                                    <h4 class="mt-3 mb-0">Alerts</h4>
                                    <p class="text-muted mb-0">Live updates</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card h-100 p-4">
                    <div class="mb-3 text-primary fs-2"><i class="fa-solid fa-user-plus"></i></div>
                    <h4>Create Your Profile</h4>
                    <p class="text-muted">List the skills you can offer and what you need from others in the community.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4">
                    <div class="mb-3 text-primary fs-2"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <h4>Find Compatible Matches</h4>
                    <p class="text-muted">Use intelligent matching to see opportunities where skill exchange is mutually beneficial.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4">
                    <div class="mb-3 text-primary fs-2"><i class="fa-solid fa-people-arrows"></i></div>
                    <h4>Exchange Services</h4>
                    <p class="text-muted">Send requests, accept proposals, complete exchanges, and leave reviews to build trust.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer text-center">
        <div class="container">
            <p class="mb-0">© 2026 Community Skill Exchange Platform</p>
        </div>
    </footer>
</body>
</html>
