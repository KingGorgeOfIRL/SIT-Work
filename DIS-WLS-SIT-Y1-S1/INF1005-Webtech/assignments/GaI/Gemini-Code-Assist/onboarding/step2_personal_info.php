<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['onboarding_data']['username'])) {
    redirect('/onboarding/step1_user_pass.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];

    if (empty($name) || empty($contact)) {
        $error = 'Name and contact information are required.';
    } else {
        $_SESSION['onboarding_data']['name'] = $name;
        $_SESSION['onboarding_data']['contact'] = $contact;
        redirect('/onboarding/step3_profile_photo.php');
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Onboarding - Step 2: Personal Info</h2>
<hr>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form action="step2_personal_info.php" method="post">
    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" class="form-control" id="name" name="name" required value="<?php echo $_SESSION['onboarding_data']['name'] ?? ''; ?>">
    </div>
    <div class="mb-3">
        <label for="contact" class="form-label">Contact Info (Email or Phone)</label>
        <input type="text" class="form-control" id="contact" name="contact" required value="<?php echo $_SESSION['onboarding_data']['contact'] ?? ''; ?>">
    </div>
    <a href="/onboarding/step1_user_pass.php" class="btn btn-secondary">Previous</a>
    <button type="submit" class="btn btn-primary">Next</button>
</form>

<?php include '../includes/footer.php'; ?>
