<?php
require_once __DIR__ . '/../process/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../process/user.php';

// Require login
require_login();

// Verify CSRF token
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validate_csrf_token($_POST['csrf_token'] ?? '')) {
    header('Location: /profile.php');
    exit();
}

// Get current user ID before deletion
$user_id = $_SESSION['user_id'];

// Delete the user and associated data
$result = delete_user($user_id);

if ($result['success']) {
    // Logout
    logout_user();

    // Redirect to home with success message
    session_start();
    $_SESSION['message'] = 'Your account has been successfully deleted.';
    header('Location: /');
    exit();
} else {
    // Redirect back with error
    header('Location: /profile.php?error=' . urlencode($result['message']));
    exit();
}

?>
