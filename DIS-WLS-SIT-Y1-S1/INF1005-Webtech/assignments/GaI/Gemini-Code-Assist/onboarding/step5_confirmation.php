<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['onboarding_data']['pets'])) {
    redirect('/onboarding/step4_pet_info.php');
}

$data = $_SESSION['onboarding_data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $user_id = save_user([
        'username' => $data['username'],
        'password' => $data['password'],
        'name' => $data['name'],
        'contact' => $data['contact'],
        'profile_photo' => $data['profile_photo']
    ]);

    foreach ($data['pets'] as $pet) {
        save_pet([
            'user_id' => $user_id,
            'name' => $pet['name'],
            'breed' => $pet['breed'],
            'age' => $pet['age'],
            'photo' => $pet['photo']
        ]);
    }

    // Log the user in
    $_SESSION['user_id'] = $user_id;

    // Clear onboarding data
    unset($_SESSION['onboarding_data']);

    redirect('/profile/view.php');
}

?>

<?php include '../includes/header.php'; ?>

<h2>Onboarding - Step 5: Confirmation</h2>
<hr>

<h3>Review Your Information</h3>

<div class="card mb-3">
    <div class="card-header">
        Personal Information
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <img src="<?php echo $data['profile_photo']; ?>" class="img-fluid rounded-start" alt="Profile Photo">
            </div>
            <div class="col-md-8">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($data['username']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($data['name']); ?></p>
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($data['contact']); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Pet Information
    </div>
    <div class="card-body">
        <?php foreach ($data['pets'] as $pet): ?>
            <div class="row mb-3">
                <div class="col-md-4">
                    <?php if ($pet['photo']): ?>
                        <img src="<?php echo $pet['photo']; ?>" class="img-fluid rounded-start" alt="Pet Photo">
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <p><strong>Pet Name:</strong> <?php echo htmlspecialchars($pet['name']); ?></p>
                    <p><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?></p>
                </div>
            </div>
            <hr>
        <?php endforeach; ?>
    </div>
</div>

<form action="step5_confirmation.php" method="post" class="mt-3">
    <a href="/onboarding/step4_pet_info.php" class="btn btn-secondary">Previous</a>
    <button type="submit" name="save" class="btn btn-success">Save and Complete</button>
</form>


<?php include '../includes/footer.php'; ?>
