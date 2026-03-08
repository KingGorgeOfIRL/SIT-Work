<?php
require_once __DIR__ . '/../process/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../process/user.php';

// Redirect if not in wizard
if (!isset($_SESSION['wizard_data']) || empty($_SESSION['wizard_data']['username'])) {
    header('Location: /onboarding/step1.php');
    exit();
}

$errors = [];
$form_data = $_SESSION['wizard_data'];

// Handle form submission (save all data)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token validation failed.';
    } else {
        // Create user account
        $user_result = create_user(
            $form_data['username'],
            $form_data['password'],
            $form_data['name'],
            $form_data['email'],
            $form_data['phone'] ?? ''
        );

        if (!$user_result['success']) {
            $errors[] = $user_result['message'];
        } else {
            $user_id = $user_result['user_id'];

            // Update user with profile photo if available
            if (!empty($form_data['profile_photo'])) {
                update_user($user_id, ['profile_photo' => $form_data['profile_photo']]);
            }

            // Add pets if any
            if (!empty($form_data['pets'])) {
                foreach ($form_data['pets'] as $pet) {
                    add_pet($user_id, $pet['pet_name'], $pet['breed'], $pet['age'], $pet['pet_photo']);
                }
            }

            // Clear wizard session and log in the user
            unset($_SESSION['wizard_data']);
            login_user($form_data['username'], $form_data['password']);

            // Redirect to dashboard
            header('Location: /dashboard.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding - Step 5</title>
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
                    <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="card-title mb-1">Review Your Information</h2>
                        <p class="text-muted mb-4">Step 5 of 5: Confirmation & Save</p>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Account Summary -->
                        <h5 class="mb-3">Account Information</h5>
                        <div class="card mb-4 bg-light">
                            <div class="card-body">
                                <p class="mb-2"><strong>Username:</strong> <?php echo htmlspecialchars($form_data['username']); ?></p>
                                <p class="mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($form_data['name']); ?></p>
                                <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($form_data['email']); ?></p>
                                <?php if (!empty($form_data['phone'])): ?>
                                    <p class="mb-0"><strong>Phone:</strong> <?php echo htmlspecialchars($form_data['phone']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Profile Photo -->
                        <?php if (!empty($form_data['profile_photo'])): ?>
                            <h5 class="mb-3">Profile Photo</h5>
                            <div class="text-center mb-4">
                                <img src="<?php echo get_image_url($form_data['profile_photo'], 'profile'); ?>"
                                     alt="Profile" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                        <?php endif; ?>

                        <!-- Pets Summary -->
                        <?php if (!empty($form_data['pets'])): ?>
                            <h5 class="mb-3">Your Pets (<?php echo count($form_data['pets']); ?>)</h5>
                            <div class="mb-4">
                                <?php foreach ($form_data['pets'] as $pet): ?>
                                    <div class="card mb-2">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-0">🐾 <?php echo htmlspecialchars($pet['pet_name']); ?></h6>
                                                    <small class="text-muted">
                                                        <?php if (!empty($pet['breed'])): ?>
                                                            <?php echo htmlspecialchars($pet['breed']); ?>
                                                            <?php if (!empty($pet['age'])): ?>
                                                                • <?php echo htmlspecialchars($pet['age']); ?>
                                                            <?php endif; ?>
                                                        <?php elseif (!empty($pet['age'])): ?>
                                                            Age: <?php echo htmlspecialchars($pet['age']); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                                <?php if (!empty($pet['pet_photo'])): ?>
                                                    <div class="col-md-4 text-end">
                                                        <img src="<?php echo get_image_url($pet['pet_photo'], 'pet'); ?>"
                                                             alt="<?php echo htmlspecialchars($pet['pet_name']); ?>"
                                                             class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-4">No pets added yet. You can add them later!</p>
                        <?php endif; ?>

                        <!-- Save Form -->
                        <form method="POST">
                            <?php echo get_csrf_field(); ?>

                            <div class="alert alert-info mb-4">
                                <small>By clicking "Complete Onboarding", you agree to create your account with the above information.</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="/onboarding/step4.php" class="btn btn-outline-secondary">← Back</a>
                                <button type="submit" class="btn btn-success btn-lg">Complete Onboarding ✓</button>
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
