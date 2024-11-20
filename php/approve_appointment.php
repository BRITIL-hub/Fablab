<?php
session_start();
include('connection.php');
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];

    // Fetch appointment and user details
    $query = "SELECT a.appointment_id, a.appointment_date, u.email, u.username, m.machine_name 
              FROM appointments a 
              JOIN users u ON a.user_id = u.user_id 
              JOIN machines m ON a.machine_id = m.machine_id 
              WHERE a.appointment_id = ?";
    $stmt = $database->prepare($query);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $appointment = $result->fetch_assoc();
        $user_email = $appointment['email'];
        $username = $appointment['username'];
        $machine_name = $appointment['machine_name'];
        $appointment_date = date("F j, Y, g:i a", strtotime($appointment['appointment_date']));

        // Update the appointment status to 'approved'
        $update_query = "UPDATE appointments SET status = 'approved' WHERE appointment_id = ?";
        $update_stmt = $database->prepare($update_query);
        $update_stmt->bind_param("i", $appointment_id);
        if ($update_stmt->execute()) {
            // Send confirmation email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'abegail.basan@ctu.edu.ph';
                $mail->Password = 'vgpy fagc vbyp nkyc';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('fabricationlab.ctudanao@gmail.com', 'FabLab CTU Danao');
                $mail->addAddress($user_email);

                $mail->isHTML(true);
                $mail->Subject = 'Appointment Approved';
                $mail->Body = "Hello $username,<br><br>
                               Your appointment has been approved. Here are the details:<br>
                               <strong>Machine:</strong> $machine_name<br>
                               <strong>Date and Time:</strong> $appointment_date<br><br>
                               Thank you for booking with us!<br><br>
                               Regards,<br>FabLab CTU Danao Team";

                $mail->send();
                $_SESSION['success_message'] = "Appointment approved, and confirmation email sent to the user.";
            } catch (Exception $e) {
                $_SESSION['error_message'] = "Failed to send email: {$mail->ErrorInfo}";
            }
        } else {
            $_SESSION['error_message'] = "Failed to update appointment status.";
        }
    } else {
        $_SESSION['error_message'] = "Appointment not found.";
    }
} else {
    $_SESSION['error_message'] = "Invalid request.";
}

header("Location: manage_appointments.php");
exit();
?>