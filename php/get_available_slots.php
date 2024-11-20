<?php
session_start();
include('connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.0 403 Forbidden");
    exit();
}

// Check if machine_id is provided
if (isset($_GET['machine_id'])) {
    $machine_id = intval($_GET['machine_id']);

    // Query to get available slots for the specific machine
    $query = "
        SELECT DATE(date) AS available_date 
        FROM schedules 
        WHERE machine_id = ? AND slots_available > 0
        GROUP BY available_date
    ";

    $stmt = $database->prepare($query);
    $stmt->bind_param("i", $machine_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $available_dates = [];
    while ($row = $result->fetch_assoc()) {
        $available_dates[] = $row['available_date'];
    }

    // Return available dates as JSON
    header('Content-Type: application/json');
    echo json_encode($available_dates);
} else {
    header("HTTP/1.0 400 Bad Request");
    echo json_encode(['error' => 'Machine ID is required']);
}
?>