<?php
/**
 * Pet Community - Core Functions
 * Utility functions for CSV operations, security, and image handling
 */

// Security configuration
define('CSV_DIR', __DIR__ . '/../data/');
define('UPLOAD_DIR', __DIR__ . '/../data/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('MIN_IMAGE_WIDTH', 300);
define('MIN_IMAGE_HEIGHT', 300);
define('MIN_PET_IMAGE_WIDTH', 200);
define('MIN_PET_IMAGE_HEIGHT', 200);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

/**
 * Read CSV file and return as array of associative arrays
 */
function read_csv($filename) {
    $filepath = CSV_DIR . $filename;

    if (!file_exists($filepath)) {
        return [];
    }

    $data = [];
    if (($handle = fopen($filepath, 'r')) !== false) {
        $headers = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            if (!empty(array_filter($row))) {
                $data[] = array_combine($headers, $row);
            }
        }
        fclose($handle);
    }

    return $data;
}

/**
 * Write data array to CSV file
 */
function write_csv($filename, $data, $headers) {
    $filepath = CSV_DIR . $filename;

    // Create backup before writing
    if (file_exists($filepath)) {
        copy($filepath, $filepath . '.bak');
    }

    $handle = fopen($filepath, 'w');
    if ($handle === false) {
        return false;
    }

    // Use flock for concurrent access safety
    flock($handle, LOCK_EX);

    // Write headers
    fputcsv($handle, $headers);

    // Write data rows
    foreach ($data as $row) {
        fputcsv($handle, $row);
    }

    flock($handle, LOCK_UN);
    fclose($handle);

    return true;
}

/**
 * Find record by column value in CSV
 */
function find_in_csv($filename, $search_column, $search_value) {
    $data = read_csv($filename);

    foreach ($data as $row) {
        if (isset($row[$search_column]) && $row[$search_column] == $search_value) {
            return $row;
        }
    }

    return null;
}

/**
 * Generate unique ID
 */
function generate_id() {
    return uniqid('user_', true);
}

/**
 * Generate unique pet ID
 */
function generate_pet_id() {
    return uniqid('pet_', true);
}

/**
 * Hash password securely
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sanitize user input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate username (alphanumeric + underscore, 3-20 characters)
 */
function validate_username($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username) === 1;
}

/**
 * Validate password (minimum 6 characters)
 */
function validate_password($password) {
    return strlen($password) >= 6;
}

/**
 * Check if username already exists
 */
function username_exists($username) {
    return find_in_csv('users.csv', 'username', $username) !== null;
}

/**
 * Validate uploaded image file
 * Returns error message or true if valid
 */
function validate_image_upload($file, $is_profile = true) {
    // Check file exists
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return 'No file uploaded';
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'File upload error. Please try again.';
    }

    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return 'File size exceeds 5MB limit';
    }

    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, ALLOWED_IMAGE_TYPES)) {
        return 'Only JPG, PNG, and GIF images are allowed';
    }

    // Validate image dimensions
    list($width, $height) = getimagesize($file['tmp_name']);

    if ($is_profile) {
        if ($width < MIN_IMAGE_WIDTH || $height < MIN_IMAGE_HEIGHT) {
            return "Profile photo must be at least 300x300 pixels";
        }
        if ($width !== $height) {
            return "Profile photo must be square (equal width and height)";
        }
    } else {
        if ($width < MIN_PET_IMAGE_WIDTH || $height < MIN_PET_IMAGE_HEIGHT) {
            return "Pet photo must be at least 200x200 pixels";
        }
    }

    return true;
}

/**
 * Save uploaded image and return filename
 */
function save_image($file, $type = 'profile') {
    $validation = validate_image_upload($file, $type === 'profile');
    if ($validation !== true) {
        return ['success' => false, 'message' => $validation];
    }

    // Determine upload directory
    if ($type === 'profile') {
        $upload_path = UPLOAD_DIR . 'profiles/';
    } else {
        $upload_path = UPLOAD_DIR . 'pets/';
    }

    // Create directories if they don't exist
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'img_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
    $destination = $upload_path . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'message' => 'Failed to save image'];
    }

    return ['success' => true, 'filename' => $filename, 'path' => $destination];
}

/**
 * Delete image file
 */
function delete_image($filename, $type = 'profile') {
    if (empty($filename)) {
        return true;
    }

    if ($type === 'profile') {
        $path = UPLOAD_DIR . 'profiles/' . $filename;
    } else {
        $path = UPLOAD_DIR . 'pets/' . $filename;
    }

    if (file_exists($path)) {
        unlink($path);
    }

    return true;
}

/**
 * Get image URL for display
 */
function get_image_url($filename, $type = 'profile') {
    if (empty($filename)) {
        return '/data/uploads/' . ($type === 'profile' ? 'profiles' : 'pets') . '/placeholder.png';
    }

    if ($type === 'profile') {
        return '/data/uploads/profiles/' . $filename;
    } else {
        return '/data/uploads/pets/' . $filename;
    }
}

/**
 * Format date/time
 */
function format_date($date_string) {
    $timestamp = strtotime($date_string);
    return date('M d, Y', $timestamp);
}

/**
 * Get current timestamp in ISO format
 */
function get_timestamp() {
    return date('Y-m-d H:i:s');
}

/**
 * Escape CSV field value
 */
function escape_csv_field($field) {
    return '"' . str_replace('"', '""', $field) . '"';
}

?>
