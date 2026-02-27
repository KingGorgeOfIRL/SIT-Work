<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['onboarding_data']['profile_photo'])) {
    redirect('/onboarding/step3_profile_photo.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pets = [];
    $pet_names = $_POST['pet_name'];
    $pet_breeds = $_POST['pet_breed'];
    $pet_ages = $_POST['pet_age'];
    $pet_photos = $_FILES['pet_photo'];

    if (empty($pet_names[0])) {
        $error = "You must add at least one pet.";
    } else {
        foreach ($pet_names as $key => $name) {
            if (empty($name)) continue;

            $pet_photo_path = '';
            if (isset($pet_photos['name'][$key]) && $pet_photos['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $pet_photos['name'][$key],
                    'tmp_name' => $pet_photos['tmp_name'][$key],
                ];
                $file_name = uniqid() . '-' . basename($file['name']);
                $target_path = UPLOAD_DIR . $file_name;

                $check = getimagesize($file['tmp_name']);
                if ($check !== false) {
                    if (move_uploaded_file($file['tmp_name'], $target_path)) {
                        $pet_photo_path = '/data/uploads/' . $file_name;
                    }
                }
            }

            $pets[] = [
                'name' => $name,
                'breed' => $pet_breeds[$key],
                'age' => $pet_ages[$key],
                'photo' => $pet_photo_path
            ];
        }
        $_SESSION['onboarding_data']['pets'] = $pets;
        redirect('/onboarding/step5_confirmation.php');
    }
}

?>

<?php include '../includes/header.php'; ?>

<h2>Onboarding - Step 4: Pet Info</h2>
<hr>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form action="step4_pet_info.php" method="post" enctype="multipart/form-data">
    <div id="pets-container">
        <div class="pet-form mb-3 border p-3">
            <h4>Pet 1</h4>
            <div class="mb-3">
                <label for="pet_name[]" class="form-label">Pet's Name</label>
                <input type="text" class="form-control" name="pet_name[]" required>
            </div>
            <div class="mb-3">
                <label for="pet_breed[]" class="form-label">Breed</label>
                <input type="text" class="form-control" name="pet_breed[]" required>
            </div>
            <div class="mb-3">
                <label for="pet_age[]" class="form-label">Age</label>
                <input type="number" class="form-control" name="pet_age[]" required>
            </div>
            <div class="mb-3">
                <label for="pet_photo[]" class="form-label">Pet's Photo</label>
                <input type="file" class="form-control" name="pet_photo[]" accept="image/*">
            </div>
        </div>
    </div>

    <button type="button" id="add-pet-btn" class="btn btn-info mt-2">Add Another Pet</button>
    <hr>
    <a href="/onboarding/step3_profile_photo.php" class="btn btn-secondary">Previous</a>
    <button type="submit" class="btn btn-primary">Next</button>
</form>

<?php include '../includes/footer.php'; ?>
