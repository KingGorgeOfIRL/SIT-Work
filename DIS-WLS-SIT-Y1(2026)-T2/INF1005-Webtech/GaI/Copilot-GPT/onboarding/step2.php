<?php
require_once __DIR__ . '/../process/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if not in wizard
if (!isset($_SESSION['wizard_data']) || empty($_SESSION['wizard_data']['username'])) {
    header('Location: /onboarding/step1.php');
    exit();
}

$errors = [];
$form_data = $_SESSION['wizard_data'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token validation failed.';
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');

        // Validate name
        if (empty($name)) {
            $errors[] = 'Name is required.';
        }

        // Validate email
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!validate_email($email)) {
            $errors[] = 'Please enter a valid email address.';
        }

        // If no errors, save to session and go to next step
        if (empty($errors)) {
            $_SESSION['wizard_data']['name'] = $name;
            $_SESSION['wizard_data']['email'] = $email;
            $_SESSION['wizard_data']['phone'] = $phone;
            header('Location: /onboarding/step3.php');
            exit();
        }
    }
}

// Restore form data
$form_data = array_merge($form_data, [
    'name' => $_SESSION['wizard_data']['name'] ?? '',
    'email' => $_SESSION['wizard_data']['email'] ?? '',
    'phone' => $_SESSION['wizard_data']['phone'] ?? '',
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding - Step 2</title>
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
                    <div class="progress-bar" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="card-title mb-1">Tell Us About Yourself</h2>
                        <p class="text-muted mb-4">Step 2 of 5: Personal Information</p>

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
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?php echo htmlspecialchars($form_data['name']); ?>"
                                       placeholder="Your full name" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?php echo htmlspecialchars($form_data['email']); ?>"
                                       placeholder="your.email@example.com" required>
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="form-label">Phone Number (Optional)</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?php echo htmlspecialchars($form_data['phone']); ?>"
                                       placeholder="Your phone number">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="/onboarding/step1.php" class="btn btn-outline-secondary">← Back</a>
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
