<?php
session_start();
include('connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $database->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Check if the profile picture URL is external (starts with 'http' or 'https')
$profilePicture = $user['profile_picture'];
$isExternal = preg_match('/^(http|https):\/\//', $profilePicture);
$profilePictureSrc = $isExternal ? $profilePicture : "../uploads/" . htmlspecialchars($profilePicture);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/fablogo2.png">
    <title>User Profile</title>
    <link rel="stylesheet" href="userboard.css">
    <link rel="stylesheet" href="userboard2.css">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-image: url(../images/cover2.png);
        }
    </style>
</head>
<body>

    <!-- NAVIGATION BAR -->
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
        <h1>User Profile</h1>
        <form id="editProfileForm" enctype="multipart/form-data" style="margin-top: 20px;">
            <!-- Profile Picture Section -->
            <div class="profile-picture-container" style="position: relative;">
                <img src="<?php echo $profilePictureSrc; ?>" alt="Profile Picture" 
                     style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; object-position: center; cursor: pointer;" 
                     id="profile-picture" onclick="document.getElementById('profile_photo').click();">
                <!-- Tooltip for Change Profile -->
                <span class="tooltip" id="tooltip">Change Profile</span>

                <!-- Hidden file input to change profile picture -->
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display: none;" onchange="previewProfilePicture(event)">
            </div>
            <br>
            <div class="input-field">
                <i class="fas fa-user"></i>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <br>
            <div class="input-field">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <br>
            <button type="button" onclick="submitEditProfile()">Save Changes</button>
        </form>
    </div>

    <script>
        // Submit Edit Profile Form
        function submitEditProfile() {
            const form = document.getElementById('editProfileForm');
            const formData = new FormData(form);

            fetch('update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile updated successfully!');
                    location.reload(); // Reload the page to show updated details
                } else {
                    alert('Error updating profile: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>

    <script>
        // Preview the selected profile picture
        function previewProfilePicture(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-picture').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        // Toggle the navigation menu
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