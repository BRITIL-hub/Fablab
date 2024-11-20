<?php
session_start();
include('connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.0 403 Forbidden");
    exit();
}

// Check if machine_id and date/time are provided
if (isset($_GET['machine_id']) && isset($_GET['date']) && isset($_GET['time'])) {
    $machine_id = intval($_GET['machine_id']);
    $appointment_date = $_GET['date'] . ' ' . $_GET['time'];  // Combine date and time

    echo "<h1>Book an appointment for $appointment_date</h1>";
    echo "<form action='confirm_booking.php' method='POST' id='bookingForm'>
            <input type='hidden' name='appointment_date' value='$appointment_date'>
            <input type='hidden' name='machine_id' value='$machine_id'>
            <button type='submit'>Confirm Booking</button>
        </form>";
    
    // Prompt the user to confirm their booking
    echo "<script>
            if (confirm('You have selected the following date and time: $appointment_date. Do you want to proceed?')) {
                document.getElementById('bookingForm').submit();
            }
        </script>";
} else {
    echo "Machine ID, date, and time are required.";
}
?>