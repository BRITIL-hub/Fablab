<?php
session_start();
include('connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.0 403 Forbidden");
    exit();
}

// Check if machine_id and date are provided
if (isset($_GET['machine_id']) && isset($_GET['date'])) {
    $machine_id = intval($_GET['machine_id']);
    $date = $_GET['date']; // This should be in 'Y-m-d' format

    // Query to get available time slots for the specific machine on the selected date
    $query = "
        SELECT DATE_FORMAT(date, '%H:%i') AS time_slot, slots_available 
        FROM schedules 
        WHERE machine_id = ? AND DATE(date) = ? AND slots_available > 0
    ";

    $stmt = $database->prepare($query);
    $stmt->bind_param("is", $machine_id, $date);  // Pass the machine ID and the date part (Y-m-d)
    $stmt->execute();
    $result = $stmt->get_result();

    $available_slots = [];
    while ($row = $result->fetch_assoc()) {
        $time_24hr = $row['time_slot']; // Time in 24-hour format
        $time_12hr = date("h:i A", strtotime($time_24hr)); // Convert to 12-hour format
        $available_slots[] = $time_12hr; // Store the converted time
    }

    // Split the available time slots into AM and PM groups
    $am_slots = [];
    $pm_slots = [];

    foreach ($available_slots as $slot) {
        // Check if the time slot contains "AM" or "PM"
        if (strpos($slot, 'AM') !== false) {
            $am_slots[] = $slot;
        } else {
            $pm_slots[] = $slot;
        }
    }

    // Merge the AM and PM slots with AM first
    $sorted_slots = array_merge($am_slots, $pm_slots);

    // Return sorted available slots as JSON
    header('Content-Type: application/json');
    echo json_encode($sorted_slots);
} else {
    header("HTTP/1.0 400 Bad Request");
    echo json_encode(['error' => 'Machine ID and date are required']);
}
?>
