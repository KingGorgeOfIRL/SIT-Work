<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('/login.php');
}

$user_id = $_SESSION['user_id'];
$user = get_user_by_id($user_id);
$pets = get_pets_by_user_id($user_id);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Update user info
    $user_data = [
        'id' => $user_id,
        'name' => $_POST['name'],
        'contact' => $_POST['contact'],
    ];

    if (!empty($_POST['password'])) {
        $user_data['password'] = $_POST['password'];
    }

    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_photo'];
        $file_name = uniqid() . '-' . basename($file['name']);
        $target_path = UPLOAD_DIR . $file_name;
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $user_data['profile_photo'] = '/data/uploads/' . $file_name;
            // Optionally, delete the old photo
        }
    }

    update_user($user_data);

    // --- Pet Updates ---
    $existing_pets = $_POST['pet'] ?? [];
    foreach($existing_pets as $pet_id => $pet_data) {
        $p_data = [
            'id' => $pet_id,
            'user_id' => $user_id,
            'name' => $pet_data['name'],
            'breed' => $pet_data['breed'],
            'age' => $pet_data['age'],
        ];

        if (isset($_FILES['pet_photo']['name'][$pet_id]) && $_FILES['pet_photo']['error'][$pet_id] === UPLOAD_ERR_OK) {
            $file_name = uniqid() . '-' . basename($_FILES['pet_photo']['name'][$pet_id]);
            $target_path = UPLOAD_DIR . $file_name;
            if (move_uploaded_file($_FILES['pet_photo']['tmp_name'][$pet_id], $target_path)) {
                $p_data['photo'] = '/data/uploads/' . $file_name;
            }
        }
        update_pet($p_data);
    }
    
    // --- Add new pets ---
    if (!empty($_POST['new_pet_name'][0])) {
        foreach($_POST['new_pet_name'] as $key => $name) {
            $new_pet_photo_path = '';
            if (isset($_FILES['new_pet_photo']['name'][$key]) && $_FILES['new_pet_photo']['error'][$key] === UPLOAD_ERR_OK) {
                $file_name = uniqid() . '-' . basename($_FILES['new_pet_photo']['name'][$key]);
                $target_path = UPLOAD_DIR . $file_name;
                if (move_uploaded_file($_FILES['new_pet_photo']['tmp_name'][$key], $target_path)) {
                    $new_pet_photo_path = '/data/uploads/' . $file_name;
                }
            }
            save_pet([
                'user_id' => $user_id,
                'name' => $name,
                'breed' => $_POST['new_pet_breed'][$key],
                'age' => $_POST['new_pet_age'][$key],
                'photo' => $new_pet_photo_path
            ]);
        }
    }
    
    // --- Delete pets ----
    if(!empty($_POST['delete_pet'])) {
        foreach($_POST['delete_pet'] as $pet_id_to_delete) {
            delete_pet($pet_id_to_delete);
        }
    }


    $success = 'Profile updated successfully!';
    // Refresh data
    $user = get_user_by_id($user_id);
    $pets = get_pets_by_user_id($user_id);
}

?>

<?php include '../includes/header.php'; ?>

<h2>Edit Profile</h2>
<hr>

<?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

<form action="edit.php" method="post" enctype="multipart/form-data">

    <!-- User Information -->
    <div class="card mb-4">
        <div class="card-header">Your Information</div>
        <div class="card-body">
            <div class="mb-3">
                <label for="username" class="form-label">Username (cannot be changed)</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password (leave blank to keep current password)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
                <label for="profile_photo" class="form-label">Update Profile Photo</label>
                <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                <img src="<?php echo $user['profile_photo']; ?>" alt="Current profile photo" class="img-thumbnail mt-2" width="150">
            </div>
        </div>
    </div>

    <!-- Pet Information -->
    <div class="card mb-4">
        <div class="card-header">Your Pets</div>
        <div class="card-body">
            <?php foreach($pets as $pet): ?>
            <div class="pet-form mb-3 border p-3">
                <input type="hidden" name="pet[<?php echo $pet['id']; ?>][id]" value="<?php echo $pet['id']; ?>">
                <div class="form-check form-switch float-end">
                  <input class="form-check-input" type="checkbox" role="switch" id="delete_pet_<?php echo $pet['id']; ?>" name="delete_pet[]" value="<?php echo $pet['id']; ?>">
                  <label class="form-check-label" for="delete_pet_<?php echo $pet['id']; ?>">Delete</label>
                </div>
                <h5>Edit Pet: <?php echo htmlspecialchars($pet['name']); ?></h5>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="pet[<?php echo $pet['id']; ?>][name]" value="<?php echo htmlspecialchars($pet['name']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Breed</label>
                    <input type="text" class="form-control" name="pet[<?php echo $pet['id']; ?>][breed]" value="<?php echo htmlspecialchars($pet['breed']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Age</label>
                    <input type="number" class="form-control" name="pet[<?php echo $pet['id']; ?>][age]" value="<?php echo htmlspecialchars($pet['age']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Update Pet Photo</label>
                    <input type="file" class="form-control" name="pet_photo[<?php echo $pet['id']; ?>]" accept="image/*">
                    <?php if($pet['photo']): ?>
                    <img src="<?php echo $pet['photo']; ?>" alt="Current pet photo" class="img-thumbnail mt-2" width="150">
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <hr>
            <h5>Add New Pet</h5>
             <div id="new-pets-container">
                <div class="pet-form mb-3 border p-3">
                    <div class="mb-3">
                        <label class="form-label">Pet's Name</label>
                        <input type="text" class="form-control" name="new_pet_name[]">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Breed</label>
                        <input type="text" class="form-control" name="new_pet_breed[]">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Age</label>
                        <input type="number" class="form-control" name="new_pet_age[]">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pet's Photo</label>
                        <input type="file" class="form-control" name="new_pet_photo[]" accept="image/*">
                    </div>
                </div>
            </div>
            <button type="button" id="add-new-pet-btn" class="btn btn-info mt-2">Add Another New Pet</button>
        </div>
    </div>


    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>


<script>
// A bit of JS to add new pet forms dynamically
document.getElementById('add-new-pet-btn').addEventListener('click', function() {
    let container = document.getElementById('new-pets-container');
    let newPetForm = document.createElement('div');
    newPetForm.classList.add('pet-form', 'mb-3', 'border', 'p-3');
    newPetForm.innerHTML = `
        <div class="mb-3">
            <label class="form-label">Pet's Name</label>
            <input type="text" class="form-control" name="new_pet_name[]">
        </div>
        <div class="mb-3">
            <label class="form-label">Breed</label>
            <input type="text" class="form-control" name="new_pet_breed[]">
        </div>
        <div class="mb-3">
            <label class="form-label">Age</label>
            <input type="number" class="form-control" name="new_pet_age[]">
        </div>
        <div class="mb-3">
            <label class="form-label">Pet's Photo</label>
            <input type="file" class="form-control" name="new_pet_photo[]" accept="image/*">
        </div>
        <button type="button" class="btn btn-danger btn-sm remove-pet-btn">Remove</button>
    `;
    container.appendChild(newPetForm);
});
const newPetsContainer = document.getElementById('new-pets-container');
newPetsContainer.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-pet-btn')) {
        e.target.parentElement.remove();
    }
});
</script>

<?php include '../includes/footer.php'; ?>
