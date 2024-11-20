<?php
session_start();
include('connection.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all machines, including the machine_photo and availability
$machine_query = "SELECT * FROM machines";
$machine_result = mysqli_query($database, $machine_query);

// Fetch schedules and join with machines
$schedule_query = "SELECT schedules.machine_id, schedules.date, schedules.slots_available, machines.machine_name, machines.availability 
                   FROM schedules 
                   INNER JOIN machines ON schedules.machine_id = machines.machine_id 
                   ORDER BY schedules.date";
$schedule_result = mysqli_query($database, $schedule_query);

// Organize schedules by machine_id
$schedules_by_machine_id = [];
while ($schedule = mysqli_fetch_assoc($schedule_result)) {
    $machine_id = $schedule['machine_id'];
    $schedules_by_machine_id[$machine_id][] = $schedule;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/fablogo2.png">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>Admin Dashboard</h1>
        </div>
        <nav class="nav-links">
            <a class="nav-link" href="manage_users.php" data-page="manage_users">
                <i class="fas fa-users"></i>
                Manage Users
            </a>
            <a class="nav-link active" href="manage_appointments.php" data-page="manage_appointments">
                <i class="fas fa-calendar-alt"></i>
                Manage Appointments
            </a>
            <a class="nav-link" href="manage_machines.php" data-page="manage_machines">
                <i class="fas fa-cogs"></i>
                Add Machines and Manage Schedules
            </a>
            <a class="nav-link logout" href="javascript:void(0);" onclick="confirmLogout()">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </nav>
    </div>

    <main>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="color: green; margin-bottom: 20px;">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <h2>Appointments</h2>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Machine</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="appointmentData">
                <!-- Appointment rows will be dynamically inserted here -->
            </tbody>
        </table>
    </main>

    <script>
        // Fetch the updated appointments list every 5 seconds
        function fetchAppointments() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_appointments.php", true);
            xhr.onload = function() {
                if (xhr.status == 200) {
                    document.getElementById("appointmentData").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Fetch appointments initially and then every 5 seconds
        fetchAppointments();
        setInterval(fetchAppointments, 5000);

          // Function to confirm logout
       function confirmLogout() {
            const confirmAction = confirm("Are you sure you want to log out?");
            if (confirmAction) {
                // Redirect to the logout page
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>