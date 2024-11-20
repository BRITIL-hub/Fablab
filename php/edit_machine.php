<?php
session_start();
include('connection.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize variables
$machine_id = isset($_POST['machine_id']) ? $_POST['machine_id'] : null;
$machine_name = isset($_POST['machine_name']) ? $_POST['machine_name'] : '';
$availability = isset($_POST['availability']) ? $_POST['availability'] : null;
$machine_image = isset($_FILES['machine_image']) ? $_FILES['machine_image'] : null;

// Check if the request is an AJAX request or form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($machine_id) && !empty($machine_name)) {
        // Update machine name and availability
        $update_query = "UPDATE machines SET machine_name = ?, availability = ? WHERE machine_id = ?";
        if ($stmt = $database->prepare($update_query)) {
            $stmt->bind_param("sii", $machine_name, $availability, $machine_id);
            $stmt->execute();
            $stmt->close();
        }

        // Handle image upload if there's an image
        if (!empty($machine_image['name'])) {
            $target_dir = "../images/";
            $target_file = $target_dir . basename($machine_image['name']);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if the file is a valid image
            $check = getimagesize($machine_image['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($machine_image['tmp_name'], $target_file)) {
                    // Update the machine's image path in the database
                    $update_image_query = "UPDATE machines SET machine_photo = ? WHERE machine_id = ?";
                    if ($stmt = $database->prepare($update_image_query)) {
                        $stmt->bind_param("si", $target_file, $machine_id);
                        $stmt->execute();
                        $stmt->close();
                    }
                } else {
                    $_SESSION['error_message'] = "Sorry, there was an error uploading your image.";
                }
            } else {
                $_SESSION['error_message'] = "File is not an image.";
            }
        }

        // Set success message in the session and redirect or send response for AJAX
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // Respond to AJAX request
            echo json_encode(['status' => 'success', 'message' => 'Machine updated successfully']);
        } else {
            // Standard redirection after form submission
            $_SESSION['success_message'] = "Machine updated successfully!";
            header("Location: manage_appointments.php");
            exit();
        }
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['status' => 'error', 'message' => 'Error: Machine name is required.']);
        } else {
            $_SESSION['error_message'] = "Error: Machine name is required.";
            header("Location: manage_appointments.php"); // Redirect back on error
        }
    }
}
?>