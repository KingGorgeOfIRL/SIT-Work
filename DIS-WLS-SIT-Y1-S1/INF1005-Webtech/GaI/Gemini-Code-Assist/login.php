<?php
require_once 'includes/session.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    redirect('/profile/view.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        $user = get_user_by_username($username);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            redirect('/profile/view.php');
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<h2>Login</h2>
<hr>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form action="login.php" method="post">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>

<p class="mt-3">
    Don't have an account? <a href="/onboarding/step1_user_pass.php">Register here</a>.
</p>

<?php include 'includes/footer.php'; ?>
