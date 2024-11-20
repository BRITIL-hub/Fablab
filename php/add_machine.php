<?php
session_start();
include('connection.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not admin
    exit();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $machine_name = mysqli_real_escape_string($database, $_POST['machine_name']);
    $machine_image = $_FILES['machine_image'];

    // Check if an image file was uploaded
    if ($machine_image['error'] == UPLOAD_ERR_OK) {
        // Set the target directory
        $target_dir = "../images/";
        // Create a unique file name to avoid overwriting
        $target_file = $target_dir . basename($machine_image["name"]);
        
        // Move the uploaded file to the target directory
        if (move_uploaded_file($machine_image["tmp_name"], $target_file)) {
            // Insert into the machines table
            $insert_query = "INSERT INTO machines (machine_name, machine_photo) VALUES (?, ?)";
            $insert_stmt = $database->prepare($insert_query);
            $insert_stmt->bind_param('ss', $machine_name, $target_file);
            if ($insert_stmt->execute()) {
                $_SESSION['success_message'] = "Machine added successfully!";
            } else {
                $_SESSION['error_message'] = "Error: " . mysqli_error($database);
            }
        } else {
            $_SESSION['error_message'] = "Error uploading file.";
        }
    } else {
        $_SESSION['error_message'] = "Error: " . $machine_image['error'];
    }
    header("Location: manage_appointments.php"); // Redirect back to manage appointments page
}
?>
