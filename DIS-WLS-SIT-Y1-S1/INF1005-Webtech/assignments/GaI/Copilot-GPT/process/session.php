<?php
/**
 * Pet Community - Session Management
 * Handles user authentication and session configuration
 */

// Session configuration and security
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
    'sid_bits_per_character' => 6,
]);

// Set session timeout (30 minutes)
$timeout = 30 * 60;
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_unset();
        session_destroy();
        $_SESSION = [];
    }
}
$_SESSION['last_activity'] = time();

// Include functions for use in session handlers
require_once __DIR__ . '/../includes/functions.php';

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Login user by username and password
 */
function login_user($username, $password) {
    $user = find_in_csv('users.csv', 'username', $username);

    if ($user === null) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }

    if (!verify_password($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }

    // Set session variables
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['name'];

    return ['success' => true, 'user' => $user];
}

/**
 * Logout user
 */
function logout_user() {
    session_unset();
    session_destroy();
    $_SESSION = [];
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
}

/**
 * Require login (redirect if not logged in)
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit();
    }
}

/**
 * Require not logged in (redirect if logged in)
 */
function require_not_logged_in() {
    if (is_logged_in()) {
        header('Location: /dashboard.php');
        exit();
    }
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token for form
 */
function get_csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . generate_csrf_token() . '">';
}

?>
