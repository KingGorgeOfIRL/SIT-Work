<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('/login.php');
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    delete_user($user_id);
    
    // Logout user
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    redirect('/login.php?message=profile_deleted');
}

?>

<?php include '../includes/header.php'; ?>

<h2>Delete Profile</h2>
<hr>

<div class="alert alert-danger">
    <strong>Warning!</strong> This action is irreversible. Are you sure you want to delete your entire profile, including all your pet information?
</div>

<form action="delete.php" method="post">
    <a href="/profile/edit.php" class="btn btn-secondary">Cancel</a>
    <button type="submit" name="delete" class="btn btn-danger">Yes, Delete My Profile</button>
</form>

<?php include '../includes/footer.php'; ?>
