<?php
session_start();
require '../vendor/autoload.php';
include('connection.php');

use Google\Client;
$client = new Google\Client();
$client->setClientId("<secret>");
$client->setClientSecret("<secret>");
$client->setRedirectUri("http://localhost/fabrication-lab/php/redirect.php");

// Check if the 'code' parameter exists
if (!isset($_GET['code'])) {
    // Redirect back to the login page or show an error message
    header("Location: logreg.php?error=Google authentication canceled or failed.");
    exit();
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (isset($token['error'])) {
    // Handle token error, redirect or display an error message
    header("Location: logreg.php?error=Failed to fetch access token.");
    exit();
}

$client->setAccessToken($token);

use Google\Service\Oauth2;
$oauth2 = new Google\Service\Oauth2($client);
$userInfo = $oauth2->userinfo->get();

$email = $userInfo->email;
$username = $userInfo->name;
$profilePicture = $userInfo->picture;

$query = "SELECT * FROM users WHERE email = ?";
$stmt = $database->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['is_verified'] = $user['is_verified'];

    if (is_null($user['password']) || $user['is_verified'] == 0) {
        header("Location: set_password.php");
        exit();
    } else {
        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();
    }
} else {
    $insertQuery = "INSERT INTO users (username, email, role, profile_picture) VALUES (?, ?, 'customer', ?)";
    $stmt = $database->prepare($insertQuery);
    $stmt->bind_param("sss", $username, $email, $profilePicture);
    $stmt->execute();

    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'customer';

    header("Location: set_password.php");
    exit();
}