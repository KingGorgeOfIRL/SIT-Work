<?php
require_once __DIR__ . '/process/session.php';
require_once __DIR__ . '/includes/functions.php';

// If logged in, redirect to dashboard
if (is_logged_in()) {
    header('Location: /dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Lovers Community - Onboarding</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">🐾 Pet Lovers Community</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div>
                    <h1 class="display-4 mb-4">Welcome to Pet Lovers Community! 🐾</h1>
                    <p class="lead mb-4">Join a vibrant community of pet enthusiasts. Share your love for pets, connect with fellow pet owners, and discover amazing stories about beloved companions.</p>

                    <div class="d-grid gap-3 d-sm-flex">
                        <a href="/onboarding/step1.php" class="btn btn-primary btn-lg px-4 gap-3">Get Started</a>
                        <a href="/login.php" class="btn btn-outline-secondary btn-lg px-4">Login</a>
                    </div>

                    <div class="mt-5">
                        <h5 class="mb-3">Why Join Us?</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">✓ Share photos and details about your beloved pets</li>
                            <li class="mb-2">✓ Connect with other pet lovers in your community</li>
                            <li class="mb-2">✓ Browse profiles of amazing pets and their owners</li>
                            <li class="mb-2">✓ Easy-to-use profile management</li>
                            <li class="mb-2">✓ Support multiple pets per profile</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="text-center">
                    <div style="font-size: 120px; margin-bottom: 20px;">🐶🐱🐰</div>
                    <div class="card shadow-lg">
                        <div class="card-body p-5">
                            <h4 class="card-title mb-4">Quick Start</h4>
                            <div class="steps">
                                <div class="step mb-3">
                                    <div class="step-number">1</div>
                                    <h6>Create Account</h6>
                                    <p class="text-muted small">Set your username and password</p>
                                </div>
                                <div class="step mb-3">
                                    <div class="step-number">2</div>
                                    <h6>Personal Info</h6>
                                    <p class="text-muted small">Add your name and contact details</p>
                                </div>
                                <div class="step mb-3">
                                    <div class="step-number">3</div>
                                    <h6>Profile Photo</h6>
                                    <p class="text-muted small">Upload your profile picture</p>
                                </div>
                                <div class="step mb-3">
                                    <div class="step-number">4</div>
                                    <h6>Pet Details</h6>
                                    <p class="text-muted small">Tell us about your pets</p>
                                </div>
                                <div class="step">
                                    <div class="step-number">5</div>
                                    <h6>Confirm & Save</h6>
                                    <p class="text-muted small">Review and complete setup</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white border-top mt-5 py-4">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; 2024 Pet Lovers Community. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
