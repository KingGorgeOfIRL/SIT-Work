<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('/login.php');
}

$view_user_id = $_GET['id'] ?? null;

?>

<?php include '../includes/header.php'; ?>

<?php if ($view_user_id): ?>
    <?php
    $user = get_user_by_id($view_user_id);
    $pets = get_pets_by_user_id($view_user_id);
    ?>
    <?php if ($user): ?>
        <h2><?php echo htmlspecialchars($user['name']); ?>'s Profile</h2>
        <hr>
        <div class="card mb-3">
            <div class="card-header">
                Personal Information
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <img src="<?php echo $user['profile_photo']; ?>" class="img-fluid rounded-start" alt="Profile Photo">
                    </div>
                    <div class="col-md-8">
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($user['contact']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Pet Information
            </div>
            <div class="card-body">
                <?php if (empty($pets)): ?>
                    <p>This user has no pets.</p>
                <?php else: ?>
                    <?php foreach ($pets as $pet): ?>
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
                <?php endif; ?>
            </div>
        </div>
        <a href="/profile/view.php" class="btn btn-primary mt-3">Back to All Profiles</a>
    <?php else: ?>
        <div class="alert alert-danger">User not found.</div>
    <?php endif; ?>

<?php else: ?>
    <h2>All User Profiles</h2>
    <hr>
    <div class="row">
        <?php
        $users = get_all_users();
        foreach ($users as $user):
        ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="<?php echo $user['profile_photo']; ?>" class="card-img-top" alt="Profile Photo" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($user['name']); ?></h5>
                        <a href="/profile/view.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">View Profile</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
