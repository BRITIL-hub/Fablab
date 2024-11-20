<?php
session_start();
include('connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get parameters
$date = $_GET['date']; // Expected in 'Y-m-d' format
$time = $_GET['time']; // Expected in 'h:i A' format
$machine_id = intval($_GET['machine_id']);
$user_id = $_SESSION['user_id']; // Link the appointment to the logged-in user

// Convert time to 24-hour format
$time_24hr = date("H:i", strtotime($time)); // Convert 12-hour to 24-hour
$appointment_date = $date . ' ' . $time_24hr; // Combine date and time

// Check if the user has already booked this machine for the same date and time
$check_existing_booking_query = "
    SELECT * 
    FROM appointments 
    WHERE user_id = ? AND machine_id = ? AND DATE(appointment_date) = ? AND TIME(appointment_date) = ?
";
$stmt = $database->prepare($check_existing_booking_query);
$stmt->bind_param("iiss", $user_id, $machine_id, $date, $time_24hr); // Use 24-hour time format
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // The user has already booked this slot, deny the booking
    $message = "You have already booked this machine for the selected date and time.";
} else {
    // Check if there are available slots for this machine, date, and time
    $check_slots_query = "
        SELECT slots_available 
        FROM schedules 
        WHERE machine_id = ? AND DATE(date) = ? AND TIME(date) = ? AND slots_available > 0
    ";
    $stmt = $database->prepare($check_slots_query);
    $stmt->bind_param("iss", $machine_id, $date, $time_24hr); // Use 24-hour time format
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Check if slots are still available
        if ($row['slots_available'] > 0) {
            // Insert the appointment into the appointments table
            $insert_query = "INSERT INTO appointments (user_id, machine_id, appointment_date, status) VALUES (?, ?, ?, 'pending')";
            $stmt = $database->prepare($insert_query);
            $stmt->bind_param("iis", $user_id, $machine_id, $appointment_date);

            if ($stmt->execute()) {
                // Update the number of available slots after successful booking
                $update_slots_query = "
                    UPDATE schedules 
                    SET slots_available = slots_available - 1 
                    WHERE machine_id = ? AND DATE(date) = ? AND TIME(date) = ?
                ";
                $update_stmt = $database->prepare($update_slots_query);
                $update_stmt->bind_param("iss", $machine_id, $date, $time_24hr); // Use 24-hour time format
                $update_stmt->execute();

                $message = "Appointment booked successfully for " . htmlspecialchars($appointment_date);
            } else {
                $message = "Error booking appointment: " . htmlspecialchars($stmt->error);
            }
        } else {
            $message = "No slots available for the selected time.";
        }
    } else {
        $message = "No available slots for the selected date and time.";
    }
}

$stmt->close();
$database->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Booking</title>
    <style>
        /* Basic styling for the page */
        body {
            font-family: Arial, sans-serif;
            background-image: url(../images/cover2.png); /* Light blue background */
            color: #1848c6; /* Dark blue text */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden; /* Prevent scroll during preloader */
            flex-direction: column;
        }

        /* Preloader styles */
        .preloader {
            position: absolute;
            top: 50%; /* Move preloader to the center */
            left: 50%;
            transform: translate(-50%, -70%); /* Center the preloader */
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }

        .spinner {
            border: 8px solid #f3f3f3; /* Light grey */
            border-top: 8px solid #1848c6; /* Dark blue */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Container for content */
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            margin-top: 60px; /* Add margin top to make space for the preloader */
        }

        /* Heading styling */
        h2 {
            color: #1848c6;
            text-align: center;
        }

        /* Message styling */
        .message {
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
            text-align: center;
        }

        /* Success and error message styling */
        .success {
            background-color: #e6f5ff;
            color: #1848c6;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

    <div class="preloader">
        <div class="spinner"></div>
    </div>

    <div class="container">
        <h2>Appointment Booking</h2>

        <?php if (isset($message)) : ?>
            <div class="message <?php echo (strpos($message, 'Error') === false) ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <script>
            // Wait for 3 seconds before hiding preloader and redirecting
            setTimeout(function() {
                document.querySelector('.preloader').style.display = 'none';  // Hide preloader
                window.location.href = "user_dashboard.php";  // Redirect to the user dashboard
            }, 3000);  // 3 seconds
        </script>
    </div>

</body>
</html>
