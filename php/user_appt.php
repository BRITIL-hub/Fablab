<?php
session_start();
include('connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch appointments for the logged-in user
$query = "SELECT appointments.appointment_date, appointments.status, machines.machine_name
          FROM appointments
          JOIN machines ON appointments.machine_id = machines.machine_id
          WHERE appointments.user_id = ? 
          ORDER BY appointments.appointment_date ASC";
$stmt = $database->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link rel="icon" type="image/png" href="../images/fablogo2.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="userboard.css">
    <link rel="stylesheet" href="userboard2.css">
    <style>
        body{
            background-image: url(../images/cover2.png);
        }
    </style>
</head>
<body>

<!--NAVIGATION BAR-->
<header>
    <div class="topnav" id="myTopnav">
        <img src="../images/logo.jpg" alt="Logo" class="logo-img"> 
            <a href="user_dashboard.php">Home</a>
            <a href="user_appt.php">My Appointments</a>
            <a href="user_profile.php">My Profile</a>

        <div class="right-links">
        <a href="logout.php">Logout</a>
        </div>
        <span class="icon" onclick="toggleMenu()">
            <i class="fa fa-bars"></i>
        </span>
    </div>
</header>

    <div class="dashboard-container">
        <h1>Booked Appointments</h1>
        <table>
            <thead>
                <tr>
                    <th>Machine</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($appointment = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['machine_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No appointments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <script>
            // Close modal on outside click
    window.onclick = function(event) {
        if (event.target == document.getElementById("myModal")) {
            closeModal();
        }
    }

    function toggleMenu() {
        const nav = document.getElementById("myTopnav");
        if (nav.className === "topnav") {
            nav.className += " responsive";
        } else {
            nav.className = "topnav";
        }
    }
    </script>
</body>
</html>