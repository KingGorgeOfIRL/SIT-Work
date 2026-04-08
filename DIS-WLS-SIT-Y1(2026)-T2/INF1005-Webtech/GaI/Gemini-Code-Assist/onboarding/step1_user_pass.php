<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } elseif (get_user_by_username($username)) {
        $error = 'Username already taken. Please choose another one.';
    } else {
        $_SESSION['onboarding_data'] = [
            'username' => $username,
            'password' => $password
        ];
        redirect('/onboarding/step2_personal_info.php');
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Onboarding - Step 1: Username and Password</h2>
<hr>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form action="step1_user_pass.php" method="post">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Next</button>
</form>

<?php include '../includes/footer.php'; ?>
