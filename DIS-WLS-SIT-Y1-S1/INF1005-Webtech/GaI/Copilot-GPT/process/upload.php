<?php
/**
 * Pet Community - File Upload Handler
 * Handles image upload processing
 */

require_once __DIR__ . '/../process/session.php';

$response = ['success' => false, 'message' => ''];

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit();
}

// Check CSRF token
if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
    $response['message'] = 'CSRF token validation failed';
    echo json_encode($response);
    exit();
}

// Determine upload type
$upload_type = isset($_POST['type']) ? sanitize($_POST['type']) : 'profile';

if (!isset($_FILES['image'])) {
    $response['message'] = 'No file uploaded';
    echo json_encode($response);
    exit();
}

$is_profile = ($upload_type === 'profile');
$result = save_image($_FILES['image'], $upload_type);

if (!$result['success']) {
    $response['message'] = $result['message'];
    echo json_encode($response);
    exit();
}

// Return successful response with filename
$response['success'] = true;
$response['filename'] = $result['filename'];
$response['url'] = get_image_url($result['filename'], $upload_type);

echo json_encode($response);
exit();

?>
