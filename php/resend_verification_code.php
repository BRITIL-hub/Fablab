<?php
include('connection.php');
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// Initialize error and success message variables
$error_message = "";
$success_message = "";
$resend_message = "";

// Set the time zone to Philippine Time (Asia/Manila)
date_default_timezone_set('Asia/Manila');

// Check if the email is provided via GET parameter
if (isset($_GET['email'])) {
    $email = $_GET['email']; // Get email from the URL parameter

    // Check if the email exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a new verification code and expiration time
        $new_verification_code = rand(100000, 999999);
        $new_expiration_time = date("Y-m-d H:i:s", strtotime("+2 minutes"));

        // Update the verification code and expiration time in the database
        $sql = "UPDATE users SET verification_code = ?, verification_code_expires_at = ? WHERE email = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("sss", $new_verification_code, $new_expiration_time, $email);
        $stmt->execute();

        // Send the new verification code via email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'abegail.basan@ctu.edu.ph'; // Your Gmail address
            $mail->Password = 'vgpy fagc vbyp nkyc'; // Your Gmail password or app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('fabricationlab.ctudanao@gmail.com', 'FabLab CTU Danao');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'New Email Verification Code';
            $mail->Body = "Your new verification code is: <b>$new_verification_code</b>";

            $mail->send();

            $resend_message = "A new verification code has been sent to your email.";

            // Redirect back to verify.php with the resend message
            header("Location: verify.php?email=" . urlencode($email) . "&resend_message=" . urlencode($resend_message));
            exit();
        } catch (Exception $e) {
            // Set error message if email sending fails
            $error_message = "Failed to send the verification email. Error: {$mail->ErrorInfo}";
        }
    } else {
        $error_message = "Email not found.";
    }
} else {
    $error_message = "Email parameter is missing.";
}

// Return response as JSON (used by AJAX)
$response = [
    'error_message' => $error_message,
    'success_message' => $success_message
];

echo json_encode($response);
?>