<?php
session_start();
include('connection.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not admin
    exit();
}

// Check if user_id is provided in the URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Prepare the delete query
    $delete_query = "DELETE FROM users WHERE user_id = ?";

    if ($stmt = $database->prepare($delete_query)) {
        // Bind parameters
        $stmt->bind_param("i", $user_id);

        // Execute the query
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "User deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to delete user.";
        }
        $stmt->close();
    }
} else {
    $_SESSION['error_message'] = "No user ID provided.";
}

// Redirect back to manage_users.php
header("Location: manage_users.php");
exit();
?>