<?php
include('connection.php');

if (isset($_GET['machine_id'])) {
    $machine_id = intval($_GET['machine_id']); // Sanitize input

    // Fetch schedules for the given machine_id
    $query = "SELECT * FROM schedules WHERE machine_id = $machine_id ORDER BY date";
    $result = mysqli_query($database, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($schedule = mysqli_fetch_assoc($result)) {
            $date = date('Y-m-d', strtotime($schedule['date']));
            $time = date('H:i', strtotime($schedule['date']));
            $slots = htmlspecialchars($schedule['slots_available']);

            echo "<tr>
                    <td>$date</td>
                    <td>$time</td>
                    <td>$slots</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No schedules found for this machine.</td></tr>";
    }
} else {
    echo "<tr><td colspan='3'>Invalid machine ID.</td></tr>";
}
?>
