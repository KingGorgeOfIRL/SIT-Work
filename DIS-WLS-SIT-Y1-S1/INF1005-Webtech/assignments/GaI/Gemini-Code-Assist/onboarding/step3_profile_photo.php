<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['onboarding_data']['name'])) {
    redirect('/onboarding/step2_personal_info.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_photo'];
        $file_name = uniqid() . '-' . basename($file['name']);
        $target_path = UPLOAD_DIR . $file_name;

        // Check if it's a real image
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            $error = 'File is not an image.';
        } else {
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $_SESSION['onboarding_data']['profile_photo'] = '/data/uploads/' . $file_name;
                redirect('/onboarding/step4_pet_info.php');
            } else {
                $error = 'Failed to upload profile photo.';
            }
        }
    } else {
        $error = 'Please upload a profile photo.';
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Onboarding - Step 3: Profile Photo</h2>
<hr>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form action="step3_profile_photo.php" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="profile_photo" class="form-label">Upload Profile Photo</label>
        <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*" required>
    </div>
    <a href="/onboarding/step2_personal_info.php" class="btn btn-secondary">Previous</a>
    <button type="submit" class="btn btn-primary">Next</button>
</form>

<?php include '../includes/footer.php'; ?>
