<?php
require_once __DIR__ . '/../process/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if not in wizard
if (!isset($_SESSION['wizard_data']) || empty($_SESSION['wizard_data']['email'])) {
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
        // Profile photo is optional, so we allow skipping it
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['size'] > 0) {
            $result = validate_image_upload($_FILES['profile_photo'], true);
            if ($result !== true) {
                $errors[] = $result;
            } else {
                $upload_result = save_image($_FILES['profile_photo'], 'profile');
                if ($upload_result['success']) {
                    $_SESSION['wizard_data']['profile_photo'] = $upload_result['filename'];
                } else {
                    $errors[] = $upload_result['message'];
                }
            }
        }

        // If no errors, proceed to next step
        if (empty($errors)) {
            header('Location: /onboarding/step4.php');
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
    <title>Onboarding - Step 3</title>
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
                    <div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="card-title mb-1">Share Your Photo</h2>
                        <p class="text-muted mb-4">Step 3 of 5: Profile Photo</p>

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

                        <?php if (!empty($_SESSION['wizard_data']['profile_photo'])): ?>
                            <div class="alert alert-success">
                                ✓ Profile photo uploaded successfully!
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <?php echo get_csrf_field(); ?>

                            <div class="mb-4">
                                <label for="profile_photo" class="form-label">Upload Profile Photo</label>
                                <div class="custom-file-upload border-2 border-dashed rounded p-4 text-center" id="drop-zone">
                                    <input type="file" class="form-control d-none" id="profile_photo" name="profile_photo" accept="image/*">
                                    <svg class="mb-2" width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm3.5 7a2.5 2.5 0 1 0 5 0 2.5 2.5 0 0 0-5 0z"/>
                                    </svg>
                                    <p class="mb-1"><strong>Click to upload</strong> or drag and drop</p>
                                    <p class="text-muted small">JPG, PNG or GIF (Square, minimum 300x300 px, max 5MB)</p>
                                </div>
                                <small class="form-text text-muted d-block mt-2">
                                    Your profile photo must be square with a minimum width and height of 300px.
                                </small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="/onboarding/step2.php" class="btn btn-outline-secondary">← Back</a>
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
    <script>
        // Drag and drop file handling
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('profile_photo');

        dropZone.addEventListener('click', () => fileInput.click());

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('border-primary'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('border-primary'), false);
        });

        dropZone.addEventListener('drop', (e) => {
            fileInput.files = e.dataTransfer.files;
        });
    </script>
</body>
</html>
