<?php
// Start the session and include the connection file
session_start();
include('connection.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not admin
    exit();
}

// Check if form data has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $machine_id = $_POST['machine']; // Machine ID from the hidden input
    $date = $_POST['date']; // Selected date
    $time = $_POST['time']; // Selected time
    $slots_available = $_POST['slots']; // Available slots

    // Validate that all fields have been filled in
    if (empty($machine_id) || empty($date) || empty($time) || empty($slots_available)) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: manage_appointments.php"); // Redirect back with an error
        exit();
    }

    // Combine date and time into a single datetime string
    $dateTime = $date . ' ' . $time;

    // Check if the machine exists based on the machine_id
    $machine_query = "SELECT machine_id FROM machines WHERE machine_id = ?";
    $stmt = $database->prepare($machine_query);
    $stmt->bind_param('i', $machine_id); // Using integer type for machine_id
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Insert the new schedule into the schedules table
        $insert_query = "INSERT INTO schedules (date, slots_available, created_at, machine_id) VALUES (?, ?, NOW(), ?)";
        $insert_stmt = $database->prepare($insert_query);
        $insert_stmt->bind_param('ssi', $dateTime, $slots_available, $machine_id);
        
        if ($insert_stmt->execute()) {
            $_SESSION['success_message'] = "Schedule added successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to add the schedule.";
        }
    } else {
        $_SESSION['error_message'] = "Machine not found.";
    }
    header("Location: manage_appointments.php"); // Redirect back to manage appointments page
}
?>
