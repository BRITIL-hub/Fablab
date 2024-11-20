<?php
session_start();
include('connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: logreg.php"); // Redirect to login if not logged in
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $database->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch all machines
$machines_query = "SELECT machine_id, machine_name, machine_photo, availability FROM machines";
$machines_result = mysqli_query($database, $machines_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="userboard.css">
    <link rel="icon" type="image/png" href="../images/fablogo2.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body>

<!--NAVIGATION BAR-->
<header>
    <div class="topnav" id="myTopnav">
        <img src="../images/logo.jpg" alt="Logo" class="logo-img"> 
            <a href="user_dashboard.php">Home</a>
            <a href="user_appt.php">My Appointments</a>
            <a href="user_profile.php">My Profile</a>

            <h1 class="name">Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
        <div class="right-links">
        <a href="logout.php">Logout</a>
        </div>
        <span class="icon" onclick="toggleMenu()">
            <i class="fa fa-bars"></i>
        </span>
    </div>
</header>

<div class="dashboard-container">
    
    <!-- Available Machines Section -->
    <h2>List of Machines</h2>
    <div class="machine-container">
        <?php while ($machine = mysqli_fetch_assoc($machines_result)) { 
            // Check if machine is available or not
            $availabilityClass = ($machine['availability'] == 0) ? 'unavailable' : ''; // Add 'unavailable' class if not available
            $disabled = ($machine['availability'] == 0) ? 'disabled' : ''; // Add disabled attribute if not available
        ?>
            <div class="machine <?= $availabilityClass ?> <?= $disabled ?>" 
                onclick="openModal(<?php echo $machine['machine_id']; ?>, '<?php echo addslashes($machine['machine_name']); ?>')" 
                <?= ($machine['availability'] == 0) ? 'style="cursor: not-allowed; pointer-events: none;"' : '' ?>>
                
                <!-- Machine image container with overlay text -->
                <div class="machine-image-container">
                    <img src="../images/<?php echo htmlspecialchars($machine['machine_photo']); ?>" alt="<?php echo htmlspecialchars($machine['machine_name']); ?>">
                    
                    <!-- Overlay text for unavailable machines -->
                    <?php if ($machine['availability'] == 0): ?>
                        <div class="overlay-text">MACHINE UNAVAILABLE</div>
                    <?php endif; ?>
                </div>

                <!-- Grayed out machine name if unavailable -->
                <p class="<?= ($machine['availability'] == 0) ? 'unavailable-name' : '' ?>">
                    <?php echo htmlspecialchars($machine['machine_name']); ?>
                </p>
            </div>
        <?php } ?>
    </div>

    <!-- The Modal for Calendar -->
    <div id="myModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle"></h2>
            <input type="text" id="flatpickr" placeholder="Select a date">
            
            <!-- Time slot selection -->
            <div id="time-slot-container">
                <h3>Select a time slot:</h3>
                <select id="time-slot-select">
                    <!-- Time slots will be dynamically inserted here -->
                </select>
                <button id="confirmBookingBtn" onclick="confirmBooking()">Confirm Booking</button>
            </div>
        </div>
    </div>
</div>

<script>
    let machine_id, selectedDate;

    // Open the modal and set machine ID
    function openModal(id, name) {
        machine_id = id;
        document.getElementById("myModal").style.display = "block";
        document.getElementById("modalTitle").innerText = "Available Dates for Machine " + name;
        
        // Ensure time slot container is hidden when modal is first opened
        document.getElementById("time-slot-container").style.display = "none";
        // Initialize Flatpickr
        initFlatpickr();
    }

    // Close the modal
    function closeModal() {
        document.getElementById("myModal").style.display = "none";
        document.getElementById("time-slot-container").style.display = "none";
    }

    // Initialize Flatpickr
    function initFlatpickr() {
        const flatpickrElement = document.getElementById('flatpickr');
        flatpickr(flatpickrElement, {
            enable: [], // This will be populated with available dates
            dateFormat: "Y-m-d", // Display format
            onChange: function(selectedDates) {
                if (selectedDates.length > 0) {
                    selectedDate = selectedDates[0];
                    
                    // Correctly format the date to YYYY-MM-DD
                    const formattedDate = selectedDate.getFullYear() + '-' + 
                        String(selectedDate.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(selectedDate.getDate()).padStart(2, '0');
                    
                    // Fetch available times for the selected date
                    fetchAvailableTimes(machine_id, formattedDate);
                }
            }
        });

        // Fetch available dates
        fetchAvailableDates(machine_id, flatpickrElement);
    }

    // Fetch available dates for the selected machine
    function fetchAvailableDates(machine_id, flatpickrElement) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "get_available_slots.php?machine_id=" + machine_id, true);
        xhr.onload = function () {
            if (this.status == 200) {
                try {
                    const dates = JSON.parse(this.responseText);
                    const today = new Date(); // Get today's date
                    const availableDates = dates.filter(date => new Date(date) >= today); // Filter out past dates
                    flatpickrElement._flatpickr.set('enable', availableDates);
                } catch (e) {
                    console.error('Error parsing available dates:', e);
                }
            } else {
                console.error('Failed to fetch available dates');
            }
        };
        xhr.send();
    }

    // Fetch available time slots for the selected date
    function fetchAvailableTimes(machine_id, selectedDate) {
        console.log(`Fetching available times for machine_id: ${machine_id}, selectedDate: ${selectedDate}`); // Corrected backtick
        const xhr = new XMLHttpRequest();
        xhr.open("GET", `get_available_time.php?machine_id=${machine_id}&date=${encodeURIComponent(selectedDate)}`, true);
        xhr.onload = function () {
            if (this.status == 200) {
                try {
                    const timeSlots = JSON.parse(this.responseText);
                    const timeSlotSelect = document.getElementById("time-slot-select");

                    // Clear existing time slots
                    timeSlotSelect.innerHTML = "";

                    // Populate time slots
                    if (timeSlots.length > 0) {
                        timeSlots.forEach(slot => {
                            const option = document.createElement("option");
                            option.value = slot;
                            option.text = slot;
                            timeSlotSelect.add(option);
                        });

                        // Show the time slot container
                        document.getElementById("time-slot-container").style.display = "block";
                    } else {
                        alert("No available time slots for this date.");
                        document.getElementById("time-slot-container").style.display = "none";
                    }
                } catch (e) {
                    console.error('Error parsing available times:', e);
                }
            } else {
                console.error('Failed to fetch available times, Status:', this.status);
            }
        };
        xhr.send();
    }

    // Confirm booking
    function confirmBooking() {
        const selectedTime = document.getElementById("time-slot-select").value;
        if (selectedTime) {
            const formattedDate = selectedDate.getFullYear() + '-' + 
                String(selectedDate.getMonth() + 1).padStart(2, '0') + '-' + 
                String(selectedDate.getDate()).padStart(2, '0');

            const bookingUrl = `book_appointment.php?date=${formattedDate}&time=${selectedTime}&machine_id=${machine_id}`;
            console.log('Booking URL:', bookingUrl);

            if (confirm('Do you want to book a slot on ' + formattedDate + ' at ' + selectedTime + '?')) {
                window.location.href = bookingUrl;
            }
        } else {
            alert("Please select a time slot.");
        }
    }

    // Close modal on outside click
    window.onclick = function(event) {
        if (event.target == document.getElementById("myModal")) {
            closeModal();
        }
    }

    function toggleMenu() {
        const nav = document.getElementById("myTopnav");
        if (nav.className === "topnav") {
            nav.className += " responsive";
        } else {
            nav.className = "topnav";
        }
    }


</script>

</body>
</html>