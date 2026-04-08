<?php
require_once __DIR__ . '/../process/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if not in wizard
if (!isset($_SESSION['wizard_data']) || empty($_SESSION['wizard_data']['name'])) {
    header('Location: /onboarding/step1.php');
    exit();
}

$errors = [];
$form_data = $_SESSION['wizard_data'];

// Initialize pets array if not exists
if (!isset($_SESSION['wizard_data']['pets'])) {
    $_SESSION['wizard_data']['pets'] = [];
}

// Handle adding a pet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token validation failed.';
    } elseif ($_POST['action'] === 'add_pet') {
        $pet_name = sanitize($_POST['pet_name'] ?? '');
        $breed = sanitize($_POST['breed'] ?? '');
        $age = sanitize($_POST['age'] ?? '');

        if (empty($pet_name)) {
            $errors[] = 'Pet name is required.';
        } else {
            // Handle pet photo upload if provided
            $pet_photo = '';
            if (isset($_FILES['pet_photo']) && $_FILES['pet_photo']['size'] > 0) {
                $result = validate_image_upload($_FILES['pet_photo'], false);
                if ($result !== true) {
                    $errors[] = $result;
                } else {
                    $upload_result = save_image($_FILES['pet_photo'], 'pet');
                    if ($upload_result['success']) {
                        $pet_photo = $upload_result['filename'];
                    } else {
                        $errors[] = $upload_result['message'];
                    }
                }
            }

            // If no errors, add pet to array
            if (empty($errors)) {
                $_SESSION['wizard_data']['pets'][] = [
                    'pet_name' => $pet_name,
                    'breed' => $breed,
                    'age' => $age,
                    'pet_photo' => $pet_photo,
                ];

                // Reset form fields
                $_POST['pet_name'] = '';
                $_POST['breed'] = '';
                $_POST['age'] = '';
            }
        }
    } elseif ($_POST['action'] === 'remove_pet') {
        $index = intval($_POST['pet_index'] ?? -1);
        if ($index >= 0 && $index < count($_SESSION['wizard_data']['pets'])) {
            $pet = $_SESSION['wizard_data']['pets'][$index];
            if (!empty($pet['pet_photo'])) {
                delete_image($pet['pet_photo'], 'pet');
            }
            array_splice($_SESSION['wizard_data']['pets'], $index, 1);
        }
    } elseif ($_POST['action'] === 'continue') {
        if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
            $errors[] = 'CSRF token validation failed.';
        } else {
            // Proceed to next step (pets are optional)
            header('Location: /onboarding/step5.php');
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
    <title>Onboarding - Step 4</title>
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
                    <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="card-title mb-1">Your Pets</h2>
                        <p class="text-muted mb-4">Step 4 of 5: Pet Information</p>

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

                        <!-- List of added pets -->
                        <?php if (!empty($_SESSION['wizard_data']['pets'])): ?>
                            <div class="mb-4">
                                <h5 class="mb-3">Your Pets (<?php echo count($_SESSION['wizard_data']['pets']); ?>)</h5>
                                <?php foreach ($_SESSION['wizard_data']['pets'] as $index => $pet): ?>
                                    <div class="card mb-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($pet['pet_name']); ?></h6>
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
                                                <div class="col-md-4 text-end">
                                                    <form method="POST" style="display: inline;">
                                                        <?php echo get_csrf_field(); ?>
                                                        <input type="hidden" name="action" value="remove_pet">
                                                        <input type="hidden" name="pet_index" value="<?php echo $index; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <hr>
                        <?php endif; ?>

                        <!-- Add pet form -->
                        <h5 class="mb-3">Add a Pet</h5>
                        <form method="POST" enctype="multipart/form-data">
                            <?php echo get_csrf_field(); ?>
                            <input type="hidden" name="action" value="add_pet">

                            <div class="mb-3">
                                <label for="pet_name" class="form-label">Pet Name *</label>
                                <input type="text" class="form-control" id="pet_name" name="pet_name"
                                       placeholder="e.g., Fluffy, Max" value="<?php echo htmlspecialchars($_POST['pet_name'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="breed" class="form-label">Breed</label>
                                <input type="text" class="form-control" id="breed" name="breed"
                                       placeholder="e.g., Golden Retriever" value="<?php echo htmlspecialchars($_POST['breed'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="text" class="form-control" id="age" name="age"
                                       placeholder="e.g., 2 years, 8 months" value="<?php echo htmlspecialchars($_POST['age'] ?? ''); ?>">
                            </div>

                            <div class="mb-4">
                                <label for="pet_photo" class="form-label">Pet Photo (Optional)</label>
                                <input type="file" class="form-control" id="pet_photo" name="pet_photo" accept="image/*">
                                <small class="form-text text-muted">JPG, PNG or GIF (minimum 200x200 px, max 5MB)</small>
                            </div>

                            <button type="submit" class="btn btn-success w-100 mb-3">+ Add Pet</button>
                        </form>

                        <!-- Navigation buttons -->
                        <form method="POST">
                            <?php echo get_csrf_field(); ?>
                            <input type="hidden" name="action" value="continue">
                            <div class="d-flex justify-content-between">
                                <a href="/onboarding/step3.php" class="btn btn-outline-secondary">← Back</a>
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
