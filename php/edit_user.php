<?php
session_start();
include('connection.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not admin
    exit();
}

// Check if the user ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_users.php"); // Redirect if no ID is provided
    exit();
}

$user_id = $_GET['id'];

// Fetch user data based on user ID
$user_query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $database->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows === 0) {
    header("Location: manage_users.php"); // Redirect if user not found
    exit();
}

$user = $user_result->fetch_assoc();

// Update user details on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Update user details
    $update_query = "UPDATE users SET username = ?, email = ?, role = ? WHERE user_id = ?";
    $stmt = $database->prepare($update_query);
    $stmt->bind_param("sssi", $username, $email, $role, $user_id);
    
    if ($stmt->execute()) {
        header("Location: manage_users.php?update=success"); // Redirect to manage users with success message
        exit();
    } else {
        $error_message = "Error updating user details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="userboard2.css">
</head>
<body>
    <header>
        <h1>Edit User</h1>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_appointments.php">Manage Appointments</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <form action="" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="customer" <?php echo ($user['role'] === 'customer') ? 'selected' : ''; ?>>Customer</option>
            </select>

            <button type="submit">Update User</button>
            <?php if (isset($error_message)) { echo "<p>$error_message</p>"; } ?>
        </form>
    </main>

    <footer>   
        <p>&copy; 2024 Fabrication Laboratory</p>
    </footer>
</body>
</html>