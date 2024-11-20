<?php
session_start();
include('connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Fetch user ID from session
$user_id = $_SESSION['user_id'];

// Initialize variables
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$profile_picture = $_FILES['profile_photo'] ?? null;
$profile_picture_name = '';

// Validate input
if (empty($username) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Username and email are required']);
    exit();
}

// Prepare SQL statement to update user details
$query = "UPDATE users SET username = ?, email = ?";
$params = [$username, $email];

if ($profile_picture && $profile_picture['error'] == UPLOAD_ERR_OK) {
    // Validate file upload
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($profile_picture['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type']);
        exit();
    }

    // Generate a unique name for the uploaded file
    $profile_picture_name = uniqid() . '_' . basename($profile_picture['name']);
    $upload_path = '../uploads/' . $profile_picture_name;

    // Move the uploaded file to the uploads directory
    if (!move_uploaded_file($profile_picture['tmp_name'], $upload_path)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload profile picture']);
        exit();
    }

    // Include profile picture in the update query
    $query .= ", profile_picture = ?";
    $params[] = $profile_picture_name;
}

// Complete the SQL statement
$query .= " WHERE user_id = ?";
$params[] = $user_id;

// Prepare and execute the statement
$stmt = $database->prepare($query);
$stmt->bind_param(str_repeat('s', count($params) - 1) . 'i', ...$params);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
?>

