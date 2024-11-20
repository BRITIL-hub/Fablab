<?php
session_start();
include('connection.php');

// Set the time zone to Philippine Time (Asia/Manila)
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch the user record
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $database->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['user_id'];

        // Fetch the login attempts record
        $attempt_query = "SELECT * FROM login_attempts WHERE user_id = ?";
        $attempt_stmt = $database->prepare($attempt_query);
        $attempt_stmt->bind_param("i", $user_id);
        $attempt_stmt->execute();
        $attempt_result = $attempt_stmt->get_result();

        if ($attempt_result->num_rows == 1) {
            $attempts_data = $attempt_result->fetch_assoc();
            $failed_attempts = $attempts_data['failed_attempts'];
            $lock_until = new DateTime($attempts_data['lock_until']);
            $current_time = new DateTime();

            // Check if the account is locked
            if ($failed_attempts >= 5 && $current_time < $lock_until) {
                header("Location: logreg.php?error=Your account is locked. Please wait 24 hours or reset your password.");
                exit();
            }
        } else {
            // If no record exists, create one for the user
            $insert_attempt_query = "INSERT INTO login_attempts (user_id, failed_attempts, lock_until) VALUES (?, 0, NULL)";
            $insert_attempt_stmt = $database->prepare($insert_attempt_query);
            $insert_attempt_stmt->bind_param("i", $user_id);
            $insert_attempt_stmt->execute();
            $failed_attempts = 0;
            $lock_until = new DateTime();
        }

        // Check if the password is set
        if (is_null($user['password'])) {
            header("Location: set_password.php");
            exit();
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Successful login, reset failed attempts
            $reset_attempts_query = "UPDATE login_attempts SET failed_attempts = 0, lock_until = NULL WHERE user_id = ?";
            $reset_stmt = $database->prepare($reset_attempts_query);
            $reset_stmt->bind_param("i", $user_id);
            $reset_stmt->execute();

            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['is_verified'] = $user['is_verified'];

            // Redirect based on user role
            if ($user['role'] == 'admin') {
                header("Location: manage_users.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            // Invalid password, increment failed attempts
            $failed_attempts++;
            $lock_until = null;

            if ($failed_attempts >= 5) {
                // Lock the account for 24 hours
                $lock_until_time = new DateTime();
                $lock_until_time->modify('+24 hours');
                $lock_until = $lock_until_time->format('Y-m-d H:i:s');
            }

            $update_attempts_query = "UPDATE login_attempts SET failed_attempts = ?, lock_until = ? WHERE user_id = ?";
            $update_stmt = $database->prepare($update_attempts_query);
            $update_stmt->bind_param("isi", $failed_attempts, $lock_until, $user_id);
            $update_stmt->execute();

            header("Location: logreg.php?error=Invalid email or password!");
            exit();
        }
    } else {
        // User does not exist
        header("Location: logreg.php?error=Invalid email or password!");
        exit();
    }
}
?>