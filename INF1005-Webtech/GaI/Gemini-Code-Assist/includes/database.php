<?php

define('USER_DATA_PATH', __DIR__ . '/../data/users.csv');
define('PET_DATA_PATH', __DIR__ . '/../data/pets.csv');
define('UPLOAD_DIR', __DIR__ . '/../data/uploads/');

function get_all_users() {
    if (!file_exists(USER_DATA_PATH)) {
        return [];
    }
    $users = [];
    if (($handle = fopen(USER_DATA_PATH, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $users[] = [
                'id' => $data[0],
                'username' => $data[1],
                'password' => $data[2],
                'name' => $data[3],
                'contact' => $data[4],
                'profile_photo' => $data[5]
            ];
        }
        fclose($handle);
    }
    return $users;
}

function get_user_by_username($username) {
    $users = get_all_users();
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

function get_user_by_id($id) {
    $users = get_all_users();
    foreach ($users as $user) {
        if ($user['id'] === $id) {
            return $user;
        }
    }
    return null;
}

function save_user($user_data) {
    $users = get_all_users();
    $new_id = empty($users) ? 1 : (int)end($users)['id'] + 1;
    $user_data['id'] = $new_id;

    // Hash the password
    $user_data['password'] = password_hash($user_data['password'], PASSWORD_DEFAULT);

    $file = fopen(USER_DATA_PATH, 'a');
    fputcsv($file, [
        $user_data['id'],
        $user_data['username'],
        $user_data['password'],
        $user_data['name'],
        $user_data['contact'],
        $user_data['profile_photo']
    ]);
    fclose($file);
    return $new_id;
}

function update_user($user_data) {
    $users = get_all_users();
    $updated_users = [];
    foreach ($users as $user) {
        if ($user['id'] === $user_data['id']) {
            $user['name'] = $user_data['name'];
            $user['contact'] = $user_data['contact'];
            if (!empty($user_data['password'])) {
                $user['password'] = password_hash($user_data['password'], PASSWORD_DEFAULT);
            }
            if (!empty($user_data['profile_photo'])) {
                $user['profile_photo'] = $user_data['profile_photo'];
            }
        }
        $updated_users[] = $user;
    }

    $file = fopen(USER_DATA_PATH, 'w');
    foreach ($updated_users as $user) {
        fputcsv($file, [
            $user['id'],
            $user['username'],
            $user['password'],
            $user['name'],
            $user['contact'],
            $user['profile_photo']
        ]);
    }
    fclose($file);
}

function delete_user($user_id) {
    $users = get_all_users();
    $updated_users = [];
    foreach ($users as $user) {
        if ($user['id'] !== $user_id) {
            $updated_users[] = $user;
        }
    }

    $file = fopen(USER_DATA_PATH, 'w');
    foreach ($updated_users as $user) {
        fputcsv($file, $user);
    }
    fclose($file);

    // Also delete user's pets
    $pets = get_all_pets();
    $updated_pets = [];
    foreach($pets as $pet) {
        if ($pet['user_id'] !== $user_id) {
            $updated_pets[] = $pet;
        }
    }
    $file = fopen(PET_DATA_PATH, 'w');
    foreach ($updated_pets as $pet) {
        fputcsv($file, $pet);
    }
    fclose($file);
}


function get_all_pets() {
    if (!file_exists(PET_DATA_PATH)) {
        return [];
    }
    $pets = [];
    if (($handle = fopen(PET_DATA_PATH, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $pets[] = [
                'id' => $data[0],
                'user_id' => $data[1],
                'name' => $data[2],
                'breed' => $data[3],
                'age' => $data[4],
                'photo' => $data[5]
            ];
        }
        fclose($handle);
    }
    return $pets;
}

function get_pets_by_user_id($user_id) {
    $pets = get_all_pets();
    $user_pets = [];
    foreach ($pets as $pet) {
        if ($pet['user_id'] === $user_id) {
            $user_pets[] = $pet;
        }
    }
    return $user_pets;
}

function save_pet($pet_data) {
    $pets = get_all_pets();
    $new_id = empty($pets) ? 1 : (int)end($pets)['id'] + 1;
    $pet_data['id'] = $new_id;

    $file = fopen(PET_DATA_PATH, 'a');
    fputcsv($file, [
        $pet_data['id'],
        $pet_data['user_id'],
        $pet_data['name'],
        $pet_data['breed'],
        $pet_data['age'],
        $pet_data['photo']
    ]);
    fclose($file);
    return $new_id;
}

function update_pet($pet_data) {
    $pets = get_all_pets();
    $updated_pets = [];
    foreach ($pets as &$pet) {
        if ($pet['id'] === $pet_data['id']) {
            $pet = array_merge($pet, $pet_data);
        }
        $updated_pets[] = $pet;
    }

    $file = fopen(PET_DATA_PATH, 'w');
    foreach ($updated_pets as $pet) {
        fputcsv($file, $pet);
    }
    fclose($file);
}

function delete_pet($pet_id) {
    $pets = get_all_pets();
    $updated_pets = [];
    foreach ($pets as $pet) {
        if ($pet['id'] !== $pet_id) {
            $updated_pets[] = $pet;
        }
    }

    $file = fopen(PET_DATA_PATH, 'w');
    foreach ($updated_pets as $pet) {
        fputcsv($file, $pet);
    }
    fclose($file);
}
?>