<?php
session_start();
require '../vendor/autoload.php';

use Google\Client;
$client = new Google\Client();
$client->setClientId("<secretlang>");
$client->setClientSecret("<secret>");
$client->setRedirectUri("http://localhost/fabrication-lab/php/redirect.php");

if (isset($_SESSION['access_token'])) {
    $client->setAccessToken($_SESSION['access_token']);
    $client->revokeToken();
}

session_destroy();
header("Location: logreg.php");
exit();
