<?php
/**
 * Pet Community - User Management
 * Handles user CRUD operations
 */

require_once __DIR__ . '/../process/session.php';

/**
 * Get current logged-in user
 */
if (!function_exists('get_current_user')) {
    function get_current_user() {
        if (!is_logged_in()) {
            return null;
        }

        return find_in_csv('users.csv', 'user_id', $_SESSION['user_id']);
    }
}

/**
 * Create new user
 */
function create_user($username, $password, $name = '', $email = '', $phone = '') {
    // Validate inputs
    if (!validate_username($username)) {
        return ['success' => false, 'message' => 'Invalid username format. Use 3-20 alphanumeric characters or underscore.'];
    }

    if (!validate_password($password)) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
    }

    if (!empty($email) && !validate_email($email)) {
        return ['success' => false, 'message' => 'Invalid email format.'];
    }

    if (username_exists($username)) {
        return ['success' => false, 'message' => 'Username already taken.'];
    }

    // Create new user record
    $user_id = generate_id();
    $password_hash = hash_password($password);
    $now = get_timestamp();

    // Read existing users
    $users = read_csv('users.csv');

    // Create new user array with all fields
    $new_user = [
        'user_id' => $user_id,
        'username' => $username,
        'password_hash' => $password_hash,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'profile_photo' => '',
        'created_at' => $now,
        'updated_at' => $now,
    ];

    $users[] = $new_user;

    // Write back to CSV
    $headers = ['user_id', 'username', 'password_hash', 'name', 'email', 'phone', 'profile_photo', 'created_at', 'updated_at'];
    if (!write_csv('users.csv', $users, $headers)) {
        return ['success' => false, 'message' => 'Failed to save user data.'];
    }

    return ['success' => true, 'user_id' => $user_id, 'user' => $new_user];
}

/**
 * Update user
 */
function update_user($user_id, $data) {
    // Read all users
    $users = read_csv('users.csv');
    $updated = false;

    foreach ($users as &$user) {
        if ($user['user_id'] === $user_id) {
            // Only allow updating specific fields
            if (isset($data['name'])) {
                $user['name'] = sanitize($data['name']);
            }
            if (isset($data['email']) && validate_email($data['email'])) {
                $user['email'] = sanitize($data['email']);
            }
            if (isset($data['phone'])) {
                $user['phone'] = sanitize($data['phone']);
            }
            if (isset($data['profile_photo'])) {
                $user['profile_photo'] = $data['profile_photo'];
            }

            $user['updated_at'] = get_timestamp();
            $updated = true;
            break;
        }
    }

    if (!$updated) {
        return ['success' => false, 'message' => 'User not found.'];
    }

    // Write back to CSV
    $headers = ['user_id', 'username', 'password_hash', 'name', 'email', 'phone', 'profile_photo', 'created_at', 'updated_at'];
    if (!write_csv('users.csv', $users, $headers)) {
        return ['success' => false, 'message' => 'Failed to update user.'];
    }

    return ['success' => true, 'user' => $users[array_search($user_id, array_column($users, 'user_id'))]];
}

/**
 * Delete user and associated data
 */
function delete_user($user_id) {
    // Read all users
    $users = read_csv('users.csv');
    $found = false;
    $user_to_delete = null;

    // Find and remove user
    $new_users = [];
    foreach ($users as $user) {
        if ($user['user_id'] === $user_id) {
            $found = true;
            $user_to_delete = $user;
        } else {
            $new_users[] = $user;
        }
    }

    if (!$found) {
        return ['success' => false, 'message' => 'User not found.'];
    }

    // Delete user's profile photo
    if (!empty($user_to_delete['profile_photo'])) {
        delete_image($user_to_delete['profile_photo'], 'profile');
    }

    // Read all pets and delete user's pets
    $pets = read_csv('pets.csv');
    $new_pets = [];

    foreach ($pets as $pet) {
        if ($pet['user_id'] !== $user_id) {
            $new_pets[] = $pet;
        } else {
            // Delete pet photos
            if (!empty($pet['pet_photo'])) {
                delete_image($pet['pet_photo'], 'pet');
            }
        }
    }

    // Write users back
    $headers = ['user_id', 'username', 'password_hash', 'name', 'email', 'phone', 'profile_photo', 'created_at', 'updated_at'];
    $users_written = write_csv('users.csv', $new_users, $headers);

    // Write pets back
    $pet_headers = ['pet_id', 'user_id', 'pet_name', 'breed', 'age', 'pet_photo'];
    $pets_written = write_csv('pets.csv', $new_pets, $pet_headers);

    if (!$users_written || !$pets_written) {
        return ['success' => false, 'message' => 'Failed to delete user.'];
    }

    return ['success' => true, 'message' => 'User and all associated data deleted.'];
}

/**
 * Get user by ID
 */
function get_user($user_id) {
    return find_in_csv('users.csv', 'user_id', $user_id);
}

/**
 * Get all users
 */
function get_all_users() {
    return read_csv('users.csv');
}

/**
 * Get user's pets
 */
function get_user_pets($user_id) {
    $all_pets = read_csv('pets.csv');
    $user_pets = [];

    foreach ($all_pets as $pet) {
        if ($pet['user_id'] === $user_id) {
            $user_pets[] = $pet;
        }
    }

    return $user_pets;
}

/**
 * Add pet for user
 */
function add_pet($user_id, $pet_name, $breed, $age, $pet_photo = '') {
    // Validate input
    if (empty($pet_name)) {
        return ['success' => false, 'message' => 'Pet name is required.'];
    }

    // Read existing pets
    $pets = read_csv('pets.csv');

    // Create new pet
    $pet_id = generate_pet_id();
    $new_pet = [
        'pet_id' => $pet_id,
        'user_id' => $user_id,
        'pet_name' => sanitize($pet_name),
        'breed' => sanitize($breed),
        'age' => sanitize($age),
        'pet_photo' => $pet_photo,
    ];

    $pets[] = $new_pet;

    // Write back to CSV
    $headers = ['pet_id', 'user_id', 'pet_name', 'breed', 'age', 'pet_photo'];
    if (!write_csv('pets.csv', $pets, $headers)) {
        return ['success' => false, 'message' => 'Failed to save pet data.'];
    }

    return ['success' => true, 'pet_id' => $pet_id, 'pet' => $new_pet];
}

/**
 * Update pet
 */
function update_pet($pet_id, $data) {
    $pets = read_csv('pets.csv');
    $updated = false;

    foreach ($pets as &$pet) {
        if ($pet['pet_id'] === $pet_id) {
            if (isset($data['pet_name'])) {
                $pet['pet_name'] = sanitize($data['pet_name']);
            }
            if (isset($data['breed'])) {
                $pet['breed'] = sanitize($data['breed']);
            }
            if (isset($data['age'])) {
                $pet['age'] = sanitize($data['age']);
            }
            if (isset($data['pet_photo'])) {
                $pet['pet_photo'] = $data['pet_photo'];
            }

            $updated = true;
            break;
        }
    }

    if (!$updated) {
        return ['success' => false, 'message' => 'Pet not found.'];
    }

    $headers = ['pet_id', 'user_id', 'pet_name', 'breed', 'age', 'pet_photo'];
    if (!write_csv('pets.csv', $pets, $headers)) {
        return ['success' => false, 'message' => 'Failed to update pet.'];
    }

    return ['success' => true];
}

/**
 * Delete pet
 */
function delete_pet($pet_id) {
    $pets = read_csv('pets.csv');
    $found = false;
    $pet_to_delete = null;

    $new_pets = [];
    foreach ($pets as $pet) {
        if ($pet['pet_id'] === $pet_id) {
            $found = true;
            $pet_to_delete = $pet;
        } else {
            $new_pets[] = $pet;
        }
    }

    if (!$found) {
        return ['success' => false, 'message' => 'Pet not found.'];
    }

    // Delete pet photo
    if (!empty($pet_to_delete['pet_photo'])) {
        delete_image($pet_to_delete['pet_photo'], 'pet');
    }

    $headers = ['pet_id', 'user_id', 'pet_name', 'breed', 'age', 'pet_photo'];
    if (!write_csv('pets.csv', $new_pets, $headers)) {
        return ['success' => false, 'message' => 'Failed to delete pet.'];
    }

    return ['success' => true];
}

?>
