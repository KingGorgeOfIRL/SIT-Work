<?php
require_once __DIR__ . '/../process/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../process/user.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: /dashboard.php');
    exit();
}

$errors = [];
$form_data = [];

// Initialize session data for wizard
if (!isset($_SESSION['wizard_data'])) {
    $_SESSION['wizard_data'] = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token validation failed.';
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validate username
        if (empty($username)) {
            $errors[] = 'Username is required.';
        } elseif (!validate_username($username)) {
            $errors[] = 'Username must be 3-20 characters (alphanumeric and underscore only).';
        } elseif (username_exists($username)) {
            $errors[] = 'Username already taken.';
        }

        // Validate password
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (!validate_password($password)) {
            $errors[] = 'Password must be at least 6 characters.';
        } elseif ($password !== $password_confirm) {
            $errors[] = 'Passwords do not match.';
        }

        // If no errors, save to session and go to next step
        if (empty($errors)) {
            $_SESSION['wizard_data']['username'] = $username;
            $_SESSION['wizard_data']['password'] = $password;
            header('Location: /onboarding/step2.php');
            exit();
        }
    }
}

// Restore form data if exists
$form_data['username'] = $_SESSION['wizard_data']['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding - Step 1</title>
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
            <div class="col-md-8 col-lg-6">
                <!-- Progress bar -->
                <div class="progress mb-5" style="height: 5px;">
                    <div class="progress-bar" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="card-title mb-1">Create Your Account</h2>
                        <p class="text-muted mb-4">Step 1 of 5: Username & Password</p>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Please fix the following:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <?php echo get_csrf_field(); ?>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="<?php echo htmlspecialchars($form_data['username']); ?>"
                                       placeholder="3-20 characters (alphanumeric, underscore)" required>
                                <small class="form-text text-muted">Username must be 3-20 characters, containing only letters, numbers, and underscore.</small>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Minimum 6 characters" required>
                                <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirm" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                                       placeholder="Re-enter your password" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="/" class="btn btn-outline-secondary">Back</a>
                                <button type="submit" class="btn btn-primary">Next →</button>
                            </div>
                        </form>
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
