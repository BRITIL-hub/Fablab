<?php
include('connection.php');

// Fetch all appointments with machine names and corresponding usernames
$appointment_query = "
    SELECT a.appointment_id, u.username, m.machine_name, a.appointment_date AS date, a.status 
    FROM appointments a 
    JOIN machines m ON a.machine_id = m.machine_id
    JOIN users u ON a.user_id = u.user_id
    ORDER BY a.appointment_date ASC"; // Order by appointment_date in ascending order

$appointment_result = mysqli_query($database, $appointment_query);

// Generate table rows with appointment data
$appointments_html = '';
while ($appointment = mysqli_fetch_assoc($appointment_result)) {
    $appointments_html .= '<tr>
        <td>' . htmlspecialchars($appointment['username']) . '</td>
        <td>' . htmlspecialchars($appointment['machine_name']) . '</td>
        <td>' . htmlspecialchars($appointment['date']) . '</td>
        <td>' . htmlspecialchars($appointment['status']) . '</td>
        <td>';

    // Check the status of the appointment and display the appropriate button
    if ($appointment['status'] == 'pending') {
        $appointments_html .= '<a href="approve_appointment.php?id=' . $appointment['appointment_id'] . '">Approve</a>';
    } elseif ($appointment['status'] == 'approved') {
        $appointments_html .= '<a href="complete_appointment.php?id=' . $appointment['appointment_id'] . '">Completed</a>';
    }

    $appointments_html .= '<a href="delete_appointment.php?id=' . $appointment['appointment_id'] . '" onclick="return confirmDelete();">Delete</a>
        </td>
    </tr>';
}

echo $appointments_html;
?>

<script>
// JavaScript confirmation prompt
function confirmDelete() {
    return confirm("Are you sure you want to delete this appointment?");
}
</script>