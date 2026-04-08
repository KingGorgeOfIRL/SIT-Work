<?php
require_once __DIR__ . '/process/session.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/process/user.php';

// Require login
require_login();

// Get current user
$current_user = get_current_user();
$is_own_profile = true;
$profile_user = $current_user;

// Check if viewing another user's profile
if (isset($_GET['user'])) {
    $user_id = sanitize($_GET['user']);
    $profile_user = get_user($user_id);

    if ($profile_user === null) {
        echo "User not found.";
        exit();
    }

    $is_own_profile = ($user_id === $current_user['user_id']);
}

// Handle edit form submission
$editing = false;
$edit_errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_own_profile) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $edit_errors[] = 'CSRF token validation failed.';
    } elseif (isset($_POST['action'])) {
        if ($_POST['action'] === 'edit') {
            $name = sanitize($_POST['name'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $phone = sanitize($_POST['phone'] ?? '');

            if (empty($name)) {
                $edit_errors[] = 'Name is required.';
            } elseif (!validate_email($email)) {
                $edit_errors[] = 'Please enter a valid email address.';
            } else {
                $update_data = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                ];

                // Handle profile photo update
                if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['size'] > 0) {
                    $result = validate_image_upload($_FILES['profile_photo'], true);
                    if ($result !== true) {
                        $edit_errors[] = $result;
                    } else {
                        // Delete old photo if exists
                        if (!empty($current_user['profile_photo'])) {
                            delete_image($current_user['profile_photo'], 'profile');
                        }

                        $upload_result = save_image($_FILES['profile_photo'], 'profile');
                        if ($upload_result['success']) {
                            $update_data['profile_photo'] = $upload_result['filename'];
                        } else {
                            $edit_errors[] = $upload_result['message'];
                        }
                    }
                }

                if (empty($edit_errors)) {
                    update_user($current_user['user_id'], $update_data);
                    $current_user = get_current_user();
                    $profile_user = $current_user;
                    $editing = false;
                }
            }
        } elseif ($_POST['action'] === 'add_pet') {
            $pet_name = sanitize($_POST['pet_name'] ?? '');
            $breed = sanitize($_POST['breed'] ?? '');
            $age = sanitize($_POST['age'] ?? '');

            if (empty($pet_name)) {
                $edit_errors[] = 'Pet name is required.';
            } else {
                $pet_photo = '';
                if (isset($_FILES['pet_photo']) && $_FILES['pet_photo']['size'] > 0) {
                    $result = validate_image_upload($_FILES['pet_photo'], false);
                    if ($result !== true) {
                        $edit_errors[] = $result;
                    } else {
                        $upload_result = save_image($_FILES['pet_photo'], 'pet');
                        if ($upload_result['success']) {
                            $pet_photo = $upload_result['filename'];
                        } else {
                            $edit_errors[] = $upload_result['message'];
                        }
                    }
                }

                if (empty($edit_errors)) {
                    add_pet($current_user['user_id'], $pet_name, $breed, $age, $pet_photo);
                    header('Location: /profile.php');
                    exit();
                }
            }
        } elseif ($_POST['action'] === 'delete_pet') {
            $pet_id = sanitize($_POST['pet_id'] ?? '');
            delete_pet($pet_id);
            header('Location: /profile.php');
            exit();
        }
    }
}

// Check if edit mode requested
if (isset($_GET['edit']) && $_GET['edit'] === '1' && $is_own_profile) {
    $editing = true;
}

$user_pets = get_user_pets($profile_user['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile_user['name']); ?> - Pet Lovers Community</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/dashboard.php">🐾 Pet Lovers Community</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard.php">Browse Members</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/profile.php">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/process/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if (!empty($edit_errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($edit_errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <?php if ($editing): ?>
                        <!-- Edit Form -->
                        <form method="POST" enctype="multipart/form-data">
                            <?php echo get_csrf_field(); ?>
                            <input type="hidden" name="action" value="edit">

                            <!-- Profile Photo Upload -->
                            <div style="height: 300px; background: #f0f0f0; overflow: hidden; position: relative;">
                                <?php if (!empty($profile_user['profile_photo'])): ?>
                                    <img id="photo-preview" src="<?php echo get_image_url($profile_user['profile_photo'], 'profile'); ?>"
                                         alt="Profile" class="w-100 h-100" style="object-fit: cover;">
                                <?php else: ?>
                                    <div id="photo-preview" class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                        <svg width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm.078-10a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 0 1h-.5v.5a.5.5 0 0 1-1 0v-.5h-.5a.5.5 0 0 1 0-1h.5v-.5a.5.5 0 0 1 .5-.5z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display:none;">
                                <label for="profile_photo" style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 8px 12px; border-radius: 4px; cursor: pointer;">
                                    Change Photo
                                </label>
                            </div>

                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="<?php echo htmlspecialchars($profile_user['name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?php echo htmlspecialchars($profile_user['email']); ?>" required>
                                </div>

                                <div class="mb-4">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           value="<?php echo htmlspecialchars($profile_user['phone'] ?? ''); ?>">
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                    <a href="/profile.php" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <!-- View Mode -->
                        <div style="height: 300px; background: #f0f0f0; overflow: hidden;">
                            <?php if (!empty($profile_user['profile_photo'])): ?>
                                <img src="<?php echo get_image_url($profile_user['profile_photo'], 'profile'); ?>"
                                     alt="<?php echo htmlspecialchars($profile_user['name']); ?>"
                                     class="w-100 h-100" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                    <svg width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm.078-10a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 0 1h-.5v.5a.5.5 0 0 1-1 0v-.5h-.5a.5.5 0 0 1 0-1h.5v-.5a.5.5 0 0 1 .5-.5z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <h4 class="card-title"><?php echo htmlspecialchars($profile_user['name']); ?></h4>
                            <p class="text-muted">@<?php echo htmlspecialchars($profile_user['username']); ?></p>

                            <div class="mb-3">
                                <p class="card-text text-muted">
                                    <?php if (!empty($profile_user['email'])): ?>
                                        <small>📧 <?php echo htmlspecialchars($profile_user['email']); ?></small><br>
                                    <?php endif; ?>
                                    <?php if (!empty($profile_user['phone'])): ?>
                                        <small>📱 <?php echo htmlspecialchars($profile_user['phone']); ?></small>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <hr>

                            <p class="text-muted small">Member since <?php echo format_date($profile_user['created_at']); ?></p>

                            <?php if ($is_own_profile): ?>
                                <div class="d-grid gap-2">
                                    <a href="/profile.php?edit=1" class="btn btn-primary">Edit Profile</a>
                                    <a href="/profile.php?delete=1" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.');">Delete Account</a>
                                </div>
                            <?php else: ?>
                                <a href="/dashboard.php" class="btn btn-secondary w-100">Back to Directory</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pets Section -->
            <div class="col-lg-8">
                <div class="mb-4">
                    <h3 class="mb-4">🐾 Pets (<?php echo count($user_pets); ?>)</h3>

                    <?php if (empty($user_pets)): ?>
                        <div class="alert alert-info">
                            <p class="mb-0"><?php echo $is_own_profile ? 'You have not added any pets yet. ' : 'This member has not added any pets yet.'; ?></p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($user_pets as $pet): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <?php if (!empty($pet['pet_photo'])): ?>
                                            <img src="<?php echo get_image_url($pet['pet_photo'], 'pet'); ?>"
                                                 alt="<?php echo htmlspecialchars($pet['pet_name']); ?>"
                                                 class="card-img-top" style="height: 200px; object-fit: cover;">
                                        <?php else: ?>
                                            <div style="height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                <svg width="50" height="50" fill="#ccc" viewBox="0 0 16 16">
                                                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zm.5-5v1.5h1.5a.5.5 0 0 1 0 1H13v1.5a.5.5 0 0 1-1 0V13h-1.5a.5.5 0 0 1 0-1H12V11a.5.5 0 0 1 1 0zm-8-8a3 3 0 1 1 6 0 3 3 0 0 1-6 0zM3.5 11a2.5 2.5 0 1 0 5 0 2.5 2.5 0 0 0-5 0z"/>
                                                </svg>
                                            </div>
                                        <?php endif; ?>

                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($pet['pet_name']); ?></h5>
                                            <p class="card-text text-muted">
                                                <?php if (!empty($pet['breed'])): ?>
                                                    <strong><?php echo htmlspecialchars($pet['breed']); ?></strong><br>
                                                <?php endif; ?>
                                                <?php if (!empty($pet['age'])): ?>
                                                    Age: <?php echo htmlspecialchars($pet['age']); ?>
                                                <?php endif; ?>
                                            </p>

                                            <?php if ($is_own_profile): ?>
                                                <form method="POST" style="display: inline;">
                                                    <?php echo get_csrf_field(); ?>
                                                    <input type="hidden" name="action" value="delete_pet">
                                                    <input type="hidden" name="pet_id" value="<?php echo htmlspecialchars($pet['pet_id']); ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($is_own_profile && !$editing): ?>
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPetModal">
                                + Add New Pet
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Form (hidden) -->
    <?php if (isset($_GET['delete']) && $is_own_profile): ?>
        <form action="/process/delete.php" method="POST" style="display:none;" id="delete-form">
            <?php echo get_csrf_field(); ?>
        </form>
        <script>
            if (confirm('Are you absolutely sure you want to permanently delete your account? This action cannot be undone.\n\nAll your profile data and pets will be removed from the system.')) {
                document.getElementById('delete-form').submit();
            } else {
                window.location.href = '/profile.php';
            }
        </script>
    <?php endif; ?>

    <!-- Add Pet Modal -->
    <?php if ($is_own_profile): ?>
        <div class="modal fade" id="addPetModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Pet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <?php echo get_csrf_field(); ?>
                        <input type="hidden" name="action" value="add_pet">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="new_pet_name" class="form-label">Pet Name *</label>
                                <input type="text" class="form-control" id="new_pet_name" name="pet_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_pet_breed" class="form-label">Breed</label>
                                <input type="text" class="form-control" id="new_pet_breed" name="breed">
                            </div>
                            <div class="mb-3">
                                <label for="new_pet_age" class="form-label">Age</label>
                                <input type="text" class="form-control" id="new_pet_age" name="age">
                            </div>
                            <div class="mb-3">
                                <label for="new_pet_photo" class="form-label">Pet Photo</label>
                                <input type="file" class="form-control" id="new_pet_photo" name="pet_photo" accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Pet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <footer class="bg-white border-top mt-5 py-4">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; 2024 Pet Lovers Community. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview image before upload
        document.getElementById('profile_photo')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('photo-preview');
                    preview.style.backgroundImage = 'url(' + event.target.result + ')';
                    preview.style.backgroundSize = 'cover';
                    preview.style.backgroundPosition = 'center';
                    preview.innerHTML = '';
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle add pet action
        document.querySelector('#addPetModal form')?.addEventListener('submit', function(e) {
            // Create input for action if not exists
            if (!this.querySelector('input[name="action"]')) {
                let actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'add_pet';
                this.appendChild(actionInput);
            }
        });
    </script>
</body>
</html>
