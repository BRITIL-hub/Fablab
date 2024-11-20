<?php
session_start();
include('connection.php');

// Check if the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: logreg.php');
    exit();
}

$error_message = "";
$success_message = "";

// Handle the POST request for setting the password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];

    // Password validation
    if (!preg_match("/^(?=.*[A-Z])(?=.*[!@#$%^&*()_\-+=<>?]).{8,}$/", $password)) {
        $error_message = "Password must be at least 8 characters long, include at least one capital letter, and one special character.";
    } else {
        // Hash the password and update in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "UPDATE users SET password = ?, is_verified = 1 WHERE user_id = ?";
        $stmt = $database->prepare($query);
        $stmt->bind_param("si", $hashed_password, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $success_message = "Password updated successfully. Redirecting to your dashboard...";
        } else {
            $error_message = "Error updating password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script 
        src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous">
    </script>
    <title>Set Your Password</title>
    <link rel="icon" type="image/png" href="../images/fablogo2.png">
    <link rel="stylesheet" href="setpass_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Modal styling (consistent with verify.php) */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            opacity: 0;
            animation: fadeIn 0.3s forwards;
        }

        .modal-content {
            background: linear-gradient(45deg, #1848c6, #4d84e2);
            padding: 25px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            position: relative;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            color: #fff;
            transform: translateY(-20px);
            animation: slideIn 0.3s forwards;
        }

        .modal-content h2 {
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .modal-content p {
            color: #e6f5ff;
            font-size: 1rem;
            line-height: 1.5;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            font-weight: bold;
            color: #fff;
            cursor: pointer;
            background-color: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .close:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            to {
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Success message modal -->
    <?php if ($success_message): ?>
    <div class="modal" id="successModal">
        <div class="modal-content">
            <span class="close" id="closeSuccessModal">&times;</span>
            <h2>Success</h2>
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Error message modal -->
    <?php if ($error_message): ?>
    <div class="modal" id="errorModal">
        <div class="modal-content">
            <span class="close" id="closeErrorModal">&times;</span>
            <h2>Error</h2>
            <p><?php echo htmlspecialchars($error_message); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <form id="setPasswordForm" method="POST" action="set_password.php">
        <img id="toplogo" src="../images/fablogo2.png">
        <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="New Password" required>
            <i class="fas fa-eye" id="toggleEye"></i> <!-- Eye icon to toggle visibility -->
        </div>
        <input type="submit" class="btn" value="Set Password">
    </form>

    <script>
        // Function to show modal
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        // Modal handling for success
        const successModal = document.getElementById('successModal');
        const closeSuccessModal = document.getElementById('closeSuccessModal');

        if (successModal) {
            showModal('successModal');
            closeSuccessModal.onclick = () => {
                successModal.style.display = 'none';
                window.location.href = 'user_dashboard.php';
            };
        }

        // Modal handling for error
        const errorModal = document.getElementById('errorModal');
        const closeErrorModal = document.getElementById('closeErrorModal');

        if (errorModal) {
            showModal('errorModal');
            closeErrorModal.onclick = () => {
                errorModal.style.display = 'none';
            };
        }

        // Close modals when clicking outside the modal content
        window.onclick = (event) => {
            if (event.target === successModal) {
                successModal.style.display = 'none';
                window.location.href = 'user_dashboard.php';
            } else if (event.target === errorModal) {
                errorModal.style.display = 'none';
            }
        };

        // Password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
    // Correct the selector for the password input field and toggle eye icon
    const passwordInput = document.querySelector('form#setPasswordForm input[name="password"]');
    const toggleEye = document.getElementById('toggleEye');

    // Event listener for mouse down (show password)
    toggleEye.addEventListener('mousedown', function() {
        passwordInput.setAttribute('type', 'text');
        this.classList.add('fa-eye-slash'); // Change icon to eye-slash
    });

    // Event listener for mouse up (hide password)
    toggleEye.addEventListener('mouseup', function() {
        passwordInput.setAttribute('type', 'password');
        this.classList.remove('fa-eye-slash'); // Remove eye-slash icon
    });

    // Event listener for mouse leave (hide password)
    toggleEye.addEventListener('mouseleave', function() {
        passwordInput.setAttribute('type', 'password');
        this.classList.remove('fa-eye-slash'); // Remove eye-slash icon
    });
});

    </script>
</body>
</html>