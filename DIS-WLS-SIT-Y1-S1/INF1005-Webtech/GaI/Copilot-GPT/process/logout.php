<?php
require_once __DIR__ . '/../process/session.php';

// Logout the user
logout_user();

// Redirect to home
header('Location: /');
exit();

?>
