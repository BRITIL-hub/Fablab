<?php
session_start();
include('connection.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not admin
    exit();
}

// Fetch all users
$admin_query = "SELECT * FROM users WHERE role = 'admin'";
$user_query = "SELECT * FROM users WHERE role = 'customer'";

$admin_result = mysqli_query($database, $admin_query);
$user_result = mysqli_query($database, $user_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
            <a class="nav-link active" href="manage_users.php" data-page="manage_users">
                <i class="fas fa-users"></i>
                Manage Users
            </a>
            <a class="nav-link" href="manage_appointments.php" data-page="manage_appointments">
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
        <h2>Admin Users</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($admin = mysqli_fetch_assoc($admin_result)) { ?>
                    <tr>
                        <td><?php echo $admin['user_id']; ?></td>
                        <td><?php echo $admin['username']; ?></td>
                        <td><?php echo $admin['email']; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $admin['user_id']; ?>">Edit</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h2>Users (Customers)</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($user_result)) { ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['user_id']; ?>">Edit</a>
                            <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" onclick="return confirmDelete()">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </main>

</body>
</html>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this user?");
    }
       // Function to confirm logout
       function confirmLogout() {
            const confirmAction = confirm("Are you sure you want to log out?");
            if (confirmAction) {
                // Redirect to the logout page
                window.location.href = "logout.php";
            }
        }
</script>