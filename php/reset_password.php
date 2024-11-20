<?php
include('connection.php');

$message = '';  // Variable to store the message

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists in the database
    $sql = "SELECT * FROM users WHERE reset_token = ?";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['new_password'];
            $repeat_password = $_POST['repeat_password'];

            // Password validation: Alphanumeric with at least one capital letter and one number
            if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[A-Z]).{8,}$/", $new_password)) {
                $message = "Password must be at least 8 characters long and include at least one capital letter, one number, and one letter.";
            } elseif ($new_password !== $repeat_password) {
                $message = "Passwords do not match.";
            } else {
                // Ensure the new password is different from current/previous passwords
                $user = $result->fetch_assoc();
                if (password_verify($new_password, $user['password'])) {
                    $message = "You cannot use your current or previous password.";
                } else {
                    // Hash the new password
                    $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

                    // Update password and reset the token
                    $sql = "UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?";
                    $stmt = $database->prepare($sql);
                    $stmt->bind_param("ss", $new_password_hashed, $token);
                    $stmt->execute();

                    $message = "Your password has been updated successfully!";
                }
            }
        }
    } else {
        $message = "Invalid token.";
    }
} else {
    $message = "No token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-image: url(../images/cover2.png);
            color: black;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-size: cover;
            background-position: center;
        }

        /* Form Styling */
        form {
            background-color: white;
            padding: 40px;
            padding-right: 65px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin: 0 auto;
            box-sizing: border-box;
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            color: #333;
            background-color: #f9f9f9;
        }

        button[type="submit"] {
            width: 190px;
            background-color: #1848c6;
            border: none;
            outline: none;
            height: 49px;
            border-radius: 49px;
            color: #fff;
            text-transform: uppercase;
            font-weight: 600;
            margin: 10px 0;
            cursor: pointer;
            transition: 0.5s;
        }

        button[type="submit"]:hover {
            background-color: #1637a0;
        }

        button[type="submit"]:active {
            background-color: #122e7f;
        }

        a {
            color: #1848c6;
            text-decoration: none;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Mobile Responsiveness */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            form {
                width: 100%;
                max-width: 100%;
                padding: 20px;
            }

            label {
                font-size: 1rem;
            }

            input[type="password"], button {
                font-size: 1rem;
            }
        }

        /* Modal Styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: none;
            z-index: 999;
        }

        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
            z-index: 1000;
            text-align: center;
        }

        .modal.active, .modal-overlay.active {
            display: block;
        }

        .modal .btn {
            background-color: #1848c6;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .modal .btn:hover {
            background-color: #1637a0;
        }
    </style>
    <link rel="icon" type="image/png" href="../images/fablogo2.png">
</head>
<body>

<!-- HTML Form -->
<form method="POST" action="reset_password.php?token=<?php echo $_GET['token']; ?>">
    <label for="new_password">New Password:</label>
    <input type="password" name="new_password" required>
    
    <label for="repeat_password">Repeat Password:</label>
    <input type="password" name="repeat_password" required>

    <button type="submit">Reset Password</button>
</form>

<!-- Modal for Messages -->
<div id="modal-overlay" class="modal-overlay"></div>
<div id="modal" class="modal">
    <p id="modal-message"></p>
    <button class="btn" onclick="closeModal()">Close</button>
</div>

<script>
    // JavaScript to show the modal with a message
    const message = "<?php echo $message; ?>";
    const modal = document.getElementById('modal');
    const modalMessage = document.getElementById('modal-message');
    const modalOverlay = document.getElementById('modal-overlay');

    if (message) {
        modalMessage.textContent = message;
        modal.classList.add('active'); // Show the modal
        modalOverlay.classList.add('active'); // Darken the background
    }

    // Function to close the modal
    function closeModal() {
        modal.classList.remove('active');
        modalOverlay.classList.remove('active');
    }
</script>

</body>
</html>