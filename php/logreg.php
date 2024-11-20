<?php
require '../vendor/autoload.php';
include('connection.php');

// Google Client Setup
use Google\Client;
$client = new Google\Client();
$client->setClientId("<secret>");
$client->setClientSecret("<secret>");
$client->setRedirectUri("http://localhost/fabrication-lab/php/redirect.php");

$client->addScope("email");
$client->addScope("profile");

// Force account selection prompt
$client->setPrompt("select_account");

$url = $client->createAuthUrl();
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" type="image/png" href="../images/fablogo2.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Sign in & Sign up Form</title>
</head>
<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <!-- LOGIN -->
                <form method="post" action="login.php" class="sign-in-form">
                    <h2 class="title">SIGN IN</h2>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required/>
                        <i class="fas fa-eye" id="toggleLoginPassword"></i> <!-- Eye icon to toggle visibility -->
                    </div>
                    <input type="submit" value="Login" class="btn solid" />
                    <div class="social-media">
                        <a href="#" class="forgot-password" onclick="openForgotPasswordModal()">Forgot password?</a>
                    </div>
                    <a href="<?= $url ?>" class="social-icon">
                        <i class="fab fa-google"></i>
                    </a>
                </form>

                <!-- REGISTER -->
                <form method="post" action="register.php" enctype="multipart/form-data" class="sign-up-form">
                    <h2 class="title">SIGN UP</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                        <i class="fas fa-eye" id="toggleRegisterPassword"></i> <!-- Eye icon to toggle visibility -->
                    </div>
                    <div class="input-field file-field">
                        <i class="fas fa-user"></i>
                        <label class="file-label">
                            <span class="file-name file-placeholder">Choose profile picture</span>
                        </label>
                        <input type="file" name="profile_picture" required accept="image/*">
                    </div>

                    <!-- Add this JavaScript after your existing scripts -->
                    <script>
                    document.querySelector('input[type="file"]').addEventListener('change', function(e) {
                        const fileName = e.target.files[0]?.name || 'Choose profile picture';
                        const fileNameElement = e.target.closest('.file-field').querySelector('.file-name');
                        fileNameElement.textContent = fileName;
                        fileNameElement.classList.toggle('file-placeholder', !e.target.files[0]);
                    });
                    </script>
                    <input type="submit" class="btn" value="Sign up" />
                </form>
            </div>

            <div class="panels-container">
                <div class="panel left-panel">
                    <div class="content">
                        <h3>New here ?</h3>
                        <p>
                            Unlock Your Creative Potential!
                            Sign up now to access our FabLab and bring your ideas to life with tools like 3D printing, laser cutting, and more.
                            Join today and start creating!
                        </p>
                        <button class="btn transparent" id="sign-up-btn">Sign up</button>
                        <a href="../index.php" class="home"><i class="fa fa-home"></i> Back to Home</a>
                    </div>
                    <img src="../images/log2.svg" class="image" alt="" />
                </div>
                <div class="panel right-panel">
                    <div class="content">
                        <h3>One of us ?</h3>
                        <p>
                            Ready to book your next project? Log in now to reserve your spot and access all the FabLab tools you need.
                            Not a member yet? Sign up now and start booking your creative space today!
                        </p>
                        <button class="btn transparent" id="sign-in-btn">Sign in</button>
                        <a href="../index.php" class="home"><i class="fa fa-home"></i> Back to Home</a>
                    </div>
                    <img src="../images/log1.svg" class="image" alt="" />
                </div>
            </div>
        </div>


      <!--preloader-->
        <div id="preloader">
      <div class="spinner"></div>
    </div>
<script>
    document.querySelector('.sign-in-form').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Show the preloader
        document.getElementById('preloader').style.display = 'flex';

        // Simulate a 3-second delay before submitting the form
        setTimeout(() => {
            this.submit(); // Submit the form after the delay
        }, 1500); // 3000 milliseconds = 3 seconds
    });
</script>

        <!-- The error modal -->
        <div id="errorModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Error</h2>
                <p id="errorMessage"></p>
            </div>
        </div>

        <!-- Forgot Password Modal -->
        <div id="forgotPasswordModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeForgotPasswordModal()">&times;</span>
                <h2>Reset Password</h2>
                <form id="forgotPasswordForm" method="post" action="forgot_password.php">
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <input type="submit" value="Send Reset Link" class="btn solid" />
                </form>
            </div>
        </div>
    </div>

    <script>
        // Show error modal if there's an error
        <?php if ($error): ?>
            document.getElementById('errorMessage').textContent = '<?php echo $error; ?>';
            document.getElementById('errorModal').style.display = 'flex';
        <?php endif; ?>

        // Function to close the error modal
        function closeModal() {
            document.getElementById('errorModal').style.display = 'none';
        }

        // Functions to control the Forgot Password modal
        function openForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').style.display = 'flex';
        }

        function closeForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').style.display = 'none';
        }

        // Password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            const loginPassword = document.querySelector('form.sign-in-form input[name="password"]');
            const toggleLoginPassword = document.getElementById('toggleLoginPassword');

            toggleLoginPassword.addEventListener('mousedown', function() {
                loginPassword.setAttribute('type', 'text');
                this.classList.add('fa-eye-slash');
            });

            toggleLoginPassword.addEventListener('mouseup', function() {
                loginPassword.setAttribute('type', 'password');
                this.classList.remove('fa-eye-slash');
            });

            toggleLoginPassword.addEventListener('mouseleave', function() {
                loginPassword.setAttribute('type', 'password');
                this.classList.remove('fa-eye-slash');
            });

            const registerPassword = document.querySelector('form.sign-up-form input[name="password"]');
            const toggleRegisterPassword = document.getElementById('toggleRegisterPassword');

            toggleRegisterPassword.addEventListener('mousedown', function() {
                registerPassword.setAttribute('type', 'text');
                this.classList.add('fa-eye-slash');
            });

            toggleRegisterPassword.addEventListener('mouseup', function() {
                registerPassword.setAttribute('type', 'password');
                this.classList.remove('fa-eye-slash');
            });

            toggleRegisterPassword.addEventListener('mouseleave', function() {
                registerPassword.setAttribute('type', 'password');
                this.classList.remove('fa-eye-slash');
            });
        });
    </script>

    <script>
    // Handle the form submission with AJAX
    document.getElementById('forgotPasswordForm').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        const form = e.target;
        const formData = new FormData(form);

        fetch('forgot_password.php', {
            method: 'POST',
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // Show success modal
                    showModal('Success', 'Reset link has been sent to your email.', true);
                } else {
                    // Show error modal
                    showModal('Error', data.error, false);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                showModal('Error', 'An error occurred. Please try again.', false);
            });
    });

    // Function to show the modal and handle redirection
    function showModal(title, message, isSuccess) {
        const modal = document.createElement('div');
        modal.classList.add('modal');

        modal.innerHTML = `
            <div class="modal-content">
                <span class="close" onclick="closeModal(this, ${isSuccess})">&times;</span>
                <h2>${title}</h2>
                <p>${message}</p>
            </div>
        `;

        document.body.appendChild(modal);
        modal.style.display = 'flex';

        // Function to close the modal and redirect if successful
        window.closeModal = function (closeButton, isSuccess) {
            const modalElement = closeButton.closest('.modal');
            modalElement.remove();

            // Redirect to logreg.php if it was a success
            if (isSuccess) {
                window.location.href = 'logreg.php';
            }
        };
    }
    </script>

    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            flex-direction: column;
            align-items: center;
        }

        .modal-content {
            position: relative;
            text-align: center;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
        }
    </style>

    <script>
        document.querySelector('.sign-up-form').addEventListener('submit', function(e) {
        e.preventDefault();
              
        // Clear previous errors
        clearErrors();
              
        const formData = new FormData(this);
              
        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Show the error in a modal
                showModal(data.error);
            } else if (data.success) {
                // Redirect to the success page
                window.location.href = data.success;
            }
        })
        .catch(error => {
            showModal('An error occurred. Please try again.');
            });
       });

      function showModal(message) {
          document.getElementById('errorMessage').textContent = message;
          document.getElementById('errorModal').style.display = 'flex';
      }

      function clearErrors() {
          document.getElementById('errorMessage').textContent = '';
          document.getElementById('errorModal').style.display = 'none';
      }
      </script>
      <script src="app.js"></script>
</body>