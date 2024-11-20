<?php
include('connection.php');
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email is already registered
    $email_check_query = "SELECT * FROM users WHERE email = ?";
    $stmt = $database->prepare($email_check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["error" => "Email is already registered. Please use a different email."]);
        exit();
    }

    // Validate password strength
    if (!preg_match("/^(?=.*[A-Z])(?=.*[!@#$%^&*()_\-+=<>?]).{8,}$/", $password)) {
        echo json_encode(["error" => "Password must be at least 8 characters long, include at least one capital letter, and one special character."]);
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Generate a random 6-digit verification code
    $verification_code = rand(100000, 999999);

    // Set the time zone to Philippine Time (Asia/Manila)
    date_default_timezone_set('Asia/Manila');

    // Calculate the expiration time (current time + 2 minutes)
    $expiration_time = date("Y-m-d H:i:s", strtotime("+2 minutes"));

    // Handle file upload
    $target_dir = "../uploads/";
    $profile_picture = basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . time() . '_' . $profile_picture; // Use time() to prevent filename conflicts

    // Validate file type and size
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    $file_size = $_FILES["profile_picture"]["size"];

    if ($check !== false && in_array($imageFileType, ["jpg", "jpeg", "png", "gif"]) && $file_size <= 4000000) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Insert user details into the database
            $sql = "INSERT INTO users (username, email, password, profile_picture, verification_code, verification_code_expires_at, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)";
            $stmt = $database->prepare($sql);
            $stmt->bind_param("ssssss", $username, $email, $hashed_password, $target_file, $verification_code, $expiration_time);

            if ($stmt->execute()) {
                // Send verification code via email using PHPMailer
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'your_email@example.com'; // Your Gmail address
                    $mail->Password = 'your_password_or_app_password'; // Your Gmail password or app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('your_email@example.com', 'Your Name');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification Code';
                    $mail->Body = "Your verification code is: <b>$verification_code</b>";

                    $mail->send();

                    echo json_encode(["success" => "verify.php?email=$email"]);
                    exit();
                } catch (Exception $e) {
                    echo json_encode(["error" => "Failed to send verification email. Error: {$mail->ErrorInfo}"]);
                    exit();
                }
            } else {
                echo json_encode(["error" => "Failed to register user. Please try again."]);
                exit();
            }
        } else {
            echo json_encode(["error" => "Sorry, there was an error uploading your file."]);
            exit();
        }
    } else {
        if ($file_size > 4000000) {
            echo json_encode(["error" => "File is too large. Maximum file size is 4MB."]);
        } else {
            echo json_encode(["error" => "File is not a valid image or wrong file format."]);
        }
        exit();
    }
}
?>