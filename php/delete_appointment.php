<?php
session_start();
include('connection.php');

// Ensure admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if the appointment ID is provided via GET request
if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];

    // Delete the appointment from the database
    $delete_query = "DELETE FROM appointments WHERE appointment_id = ?";
    $stmt = $database->prepare($delete_query);
    $stmt->bind_param("i", $appointment_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Appointment deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete appointment.";
    }
} else {
    $_SESSION['error_message'] = "Invalid request.";
}

// Redirect back to the manage appointments page
header("Location: manage_appointments.php");
exit();