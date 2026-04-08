<?php
require_once __DIR__ . '/process/session.php';
require_once __DIR__ . '/includes/functions.php';

// Handle login form submission
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        $login_error = 'CSRF token validation failed.';
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $login_error = 'Please enter both username and password.';
        } else {
            $result = login_user($username, $password);

            if ($result['success']) {
                header('Location: /dashboard.php');
                exit();
            } else {
                $login_error = $result['message'];
            }
        }
    }
}

// Redirect to dashboard if already logged in
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
    <title>Login - Pet Lovers Community</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">🐾 Pet Lovers Community</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4">Login</h2>

                        <?php if (!empty($login_error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($login_error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <?php echo get_csrf_field(); ?>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>

                        <hr>

                        <p class="text-center text-muted mb-0">
                            Don't have an account? <a href="/onboarding/step1.php">Get started here</a>
                        </p>
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
