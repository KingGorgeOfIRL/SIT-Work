<?php
require_once __DIR__ . '/process/session.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/process/user.php';

// Require login
require_login();

// Get current user and all users
$current_user = get_current_user();
$all_users = get_all_users();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pet Lovers Community</title>
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
        <div class="row mb-4">
            <div class="col">
                <h1 class="mb-0">Community Member Directory</h1>
                <p class="text-muted">Browse profiles of other pet lovers and their furry friends</p>
            </div>
        </div>

        <?php if (empty($all_users)): ?>
            <div class="alert alert-info">
                <h5>No members yet</h5>
                <p class="mb-0">Be the first to join the Pet Lovers Community!</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($all_users as $user): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm hover-shadow">
                            <!-- Profile Photo -->
                            <div style="height: 250px; background: #f0f0f0; overflow: hidden;">
                                <?php if (!empty($user['profile_photo'])): ?>
                                    <img src="<?php echo get_image_url($user['profile_photo'], 'profile'); ?>"
                                         alt="<?php echo htmlspecialchars($user['name']); ?>"
                                         class="w-100 h-100" style="object-fit: cover;">
                                <?php else: ?>
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                        <svg width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm.078-10a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 0 1h-.5v.5a.5.5 0 0 1-1 0v-.5h-.5a.5.5 0 0 1 0-1h.5v-.5a.5.5 0 0 1 .5-.5zM3 0a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 0 1h-.5v.5a.5.5 0 0 1-1 0v-.5h-.5a.5.5 0 0 1 0-1h.5v-.5A.5.5 0 0 1 3 0zM15 8a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 0 1h-.5v.5a.5.5 0 0 1-1 0v-.5h-.5a.5.5 0 0 1 0-1h.5v-.5a.5.5 0 0 1 .5-.5z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($user['name']); ?></h5>
                                <p class="card-text text-muted small">@<?php echo htmlspecialchars($user['username']); ?></p>

                                <?php
                                $user_pets = get_user_pets($user['user_id']);
                                $pet_count = count($user_pets);
                                ?>

                                <?php if ($pet_count > 0): ?>
                                    <p class="card-text">
                                        <small><strong>🐾 <?php echo $pet_count; ?> pet<?php echo $pet_count !== 1 ? 's' : ''; ?></strong></small>
                                    </p>
                                    <div class="mb-3">
                                        <?php foreach (array_slice($user_pets, 0, 3) as $pet): ?>
                                            <small class="badge bg-light text-dark me-1 mb-1">
                                                <?php echo htmlspecialchars($pet['pet_name']); ?>
                                            </small>
                                        <?php endforeach; ?>
                                        <?php if ($pet_count > 3): ?>
                                            <small class="badge bg-light text-dark">+<?php echo $pet_count - 3; ?> more</small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Contact Info -->
                                <div class="text-muted small mb-3">
                                    <?php if (!empty($user['email'])): ?>
                                        <p class="mb-1">📧 <?php echo htmlspecialchars($user['email']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($user['phone'])): ?>
                                        <p class="mb-0">📱 <?php echo htmlspecialchars($user['phone']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <a href="/profile.php?user=<?php echo urlencode($user['user_id']); ?>" class="btn btn-primary btn-sm w-100">View Profile</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-white border-top mt-5 py-4">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; 2024 Pet Lovers Community. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .hover-shadow {
            transition: box-shadow 0.3s;
        }
        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
