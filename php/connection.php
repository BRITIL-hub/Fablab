<?php
$database = new mysqli("fablab", "root", "Css@12345", "fabrication_lab");

// Check connection
if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
}

// Set the time zone to Philippine Time (Asia/Manila)
$database->query("SET time_zone = '+08:00'");

?>