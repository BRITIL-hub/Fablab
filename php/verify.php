<?php
include('connection.php');

// Initialize an error message variable
$error_message = "";
$success_message = "";
$resend_message = "";

// Set the time zone to Philippine Time (Asia/Manila)
date_default_timezone_set('Asia/Manila');

// Get the email from the URL if available
$email = isset($_GET['email']) ? $_GET['email'] : '';

// Handle the POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $verification_code = $_POST['verification_code'];

    // Check if the code matches and is not expired
    $sql = "SELECT * FROM users WHERE email = ? AND verification_code = ? AND verification_code_expires_at >= NOW()";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("ss", $email, $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If code is correct and not expired, set the user as verified
        $sql = "UPDATE users SET is_verified = 1 WHERE email = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            // Set the success message and redirect to login page
            $_SESSION['success_message'] = "Your email has been successfully verified! You can now login.";
            header("Location: logreg.php");
            exit();
        } else {
            $error_message = "An error occurred while verifying your email. Please try again.";
            header("Location: verify.php?error_message=" . urlencode($error_message) . "&email=" . urlencode($email));
            exit();
        }
    } else {
        // Check if the verification code exists but is expired
        $sql = "SELECT * FROM users WHERE email = ? AND verification_code = ? AND verification_code_expires_at < NOW()";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("ss", $email, $verification_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Your verification code has expired. Please request a new one.";
        } else {
            $error_message = "Invalid verification code. Please check and try again.";
        }
        
        header("Location: verify.php?error_message=" . urlencode($error_message) . "&email=" . urlencode($email));
        exit();
    }
}

// Get the success message from the URL if available
$success_message = isset($_GET['success_message']) ? $_GET['success_message'] : '';

// Get the error message from the URL if available
$error_message = isset($_GET['error_message']) ? $_GET['error_message'] : '';

// Get the resend message from the URL if available
$resend_message = isset($_GET['resend_message']) ? $_GET['resend_message'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <title>Email Verification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="verify.css">
    <style>
        /* Modal styling */
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

    <!-- Resend message modal (for new verification code sent) -->
    <?php if ($resend_message): ?>
    <div class="modal" id="resendModal">
        <div class="modal-content">
            <span class="close" id="closeResendModal">&times;</span>
            <h2>Verification Code Sent</h2>
            <p><?php echo htmlspecialchars($resend_message); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <form id="verifyForm" method="post" action="verify.php">
        <div class="input-field">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required><br>
        </div>
        <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="text" name="verification_code" placeholder="Verification Code" required><br>
        </div>

        <button type="submit">Verify</button>
        <p>If your verification code has expired, <a href="resend_verification_code.php?email=<?php echo urlencode($email); ?>">click here to resend the verification code</a>.</p>
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
        }

        if (closeSuccessModal) {
            closeSuccessModal.onclick = () => {
                successModal.style.display = 'none';
                // Redirect to login page after closing success modal
                window.location.href = 'logreg.php';
            };
        }

        // Modal handling for error
        const errorModal = document.getElementById('errorModal');
        const closeErrorModal = document.getElementById('closeErrorModal');

        if (errorModal) {
            showModal('errorModal');
        }

        if (closeErrorModal) {
            closeErrorModal.onclick = () => {
                errorModal.style.display = 'none';
            };
        }

        // Resend modal handling (no redirect)
        const resendModal = document.getElementById('resendModal');
        const closeResendModal = document.getElementById('closeResendModal');

        if (resendModal) {
            showModal('resendModal');
            if (closeResendModal) {
                closeResendModal.onclick = () => {
                    resendModal.style.display = 'none';
                };
            }
        }

        // Close modals when clicking outside the modal content
        window.onclick = (event) => {
            if (event.target === successModal) {
                successModal.style.display = 'none';
                if (successModal) {
                    window.location.href = 'logreg.php';
                }
            } else if (event.target === errorModal) {
                errorModal.style.display = 'none';
            }
        };
    </script>
</body>
</html>