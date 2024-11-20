<?php
include('connection.php');
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(50)); // Generate a unique token

        // Insert the token into the database for later validation
        $sql = "UPDATE users SET reset_token = ? WHERE email = ?";
        $stmt = $database->prepare($sql);
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Send reset link via email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'abegail.basan@ctu.edu.ph'; 
            $mail->Password = 'vgpy fagc vbyp nkyc'; // Your Gmail app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('fabricationlab.ctudanao@gmail.com', 'FabLab CTU Danao');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click the following link to reset your password: <a href='http://localhost/fabrication-lab/php/reset_password.php?token=$token'>Reset Password</a>";

            $mail->send();

            $response['success'] = true;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['error'] = 'Failed to send email. Please try again.';
        }
    } else {
        $response['success'] = false;
        $response['error'] = 'Email not found in our records.';
    }
} else {
    $response['success'] = false;
    $response['error'] = 'Invalid request.';
}

echo json_encode($response);