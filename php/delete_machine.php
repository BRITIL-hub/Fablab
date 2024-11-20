<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $machine_id = $_POST['machine_id'];

    // Delete the machine from the database
    $delete_query = "DELETE FROM machines WHERE machine_id = ?";
    $stmt = $database->prepare($delete_query);
    $stmt->bind_param("i", $machine_id);

    if ($stmt->execute()) {
        echo "Machine deleted successfully.";
    } else {
        echo "Error deleting machine: " . $database->error;
    }

    $stmt->close();
    $database->close();
}
?>