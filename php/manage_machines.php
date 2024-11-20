<?php
session_start();
include('connection.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all machines, including the machine_photo and availability
$machine_query = "SELECT * FROM machines";
$machine_result = mysqli_query($database, $machine_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Machines</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/fablogo2.png">
</head>
<body>
<div class="sidebar">
        <div class="sidebar-header">
            <h1>Admin Dashboard</h1>
        </div>
        <nav class="nav-links">
            <a class="nav-link" href="manage_users.php" data-page="manage_users">
                <i class="fas fa-users"></i>
                Manage Users
            </a>
            <a class="nav-link" href="manage_appointments.php" data-page="manage_appointments">
                <i class="fas fa-calendar-alt"></i>
                Manage Appointments
            </a>
            <a class="nav-link active" href="manage_machines.php" data-page="manage_machines">
                <i class="fas fa-cogs"></i>
                Add Machines and Manage Schedules
            </a>
            <a class="nav-link logout" href="javascript:void(0);" onclick="confirmLogout()">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </nav>
    </div>

    <main>
        <h2>Add Machines and Manage Schedules</h2>
        <div class="machine-container">
            <?php if (mysqli_num_rows($machine_result) > 0) {
                while ($machine = mysqli_fetch_assoc($machine_result)) {
                    $machine_id = $machine['machine_id'];
                    $imagePath = "../images/" . $machine['machine_photo'];
                    $machine_name = htmlspecialchars($machine['machine_name']);
                    $machine_photo = $machine['machine_photo'];
                    echo '<div class="machine">
                            <img src="' . $imagePath . '" alt="' . $machine_name . '">
                            <p>' . $machine_name . '</p>
                            <button onclick="openEditMachineModal(' . $machine_id . ', \'' . $machine_name . '\', \'' . htmlspecialchars($machine_photo) . '\', ' . $machine['availability'] . ')">Edit</button>
                            <button onclick="openAddScheduleModal(' . $machine_id . ', \'' . $machine_name . '\')">Add Schedule</button>
                            <button onclick="openDisplaySchedulesModal(' . $machine_id . ', \'' . $machine_name . '\')">Display Schedules</button>
                          </div>';                    
                }
            } ?>
        </div>
        <button class="add-machine-btn" onclick="openAddMachineModal()">Add Machine</button>  

        <!-- Modal for Adding Schedule -->
        <div id="addScheduleModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddScheduleModal()">&times;</span>
                <h2>Add Schedule for <span id="selectedMachineName"></span></h2>
                <form id="addScheduleForm" method="post" action="add_schedule.php">
                    <input type="hidden" name="machine" id="selectedMachineId" value="">
                    <label for="date">Schedule Date:</label>
                    <input type="date" name="date" required><br>
                    <label for="time">Schedule Time:</label>
                    <input type="time" name="time" required><br>
                    <label for="slots">Available Slots:</label>
                    <input type="number" name="slots" min="1" required><br>
                    <button type="submit">Add Schedule</button>
                </form>
            </div>
        </div>

        <!-- Modal for Editing Machines -->
        <div id="editMachineModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditMachineModal()">&times;</span>
                <h2>Edit Machine</h2>
                <form id="editMachineForm" method="post" action="edit_machine.php" enctype="multipart/form-data">
                    <input type="hidden" name="machine_id" id="editMachineId" value="">
                    <label for="machine_name">Machine Name:</label>
                    <input type="text" name="machine_name" id="editMachineName" required><br>
                    <label for="machine_image">Upload New Image (optional):</label>
                    <input type="file" name="machine_image" id="editMachineImage"><br>
                    <img id="currentMachineImage" src="" alt="Current Image" style="width: 150px; height: auto;"><br>
                    <label for="machine_availability">Machine Availability:</label>
                    <select id="machine_availability" name="availability">
                        <option value="1">Available</option>
                        <option value="0">Unavailable</option>
                    </select><br><br>
                <div class="button-container">
                    <button type="submit" id="updateMachineBtn">Update Machine</button>
                    <button id="deleteMachineBtn" onclick="deleteMachine()">Delete Machine</button>
                </div>
                </form>
               
            </div>
        </div>

        <!-- Modal for Displaying Schedules -->
        <div id="displaySchedulesModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeDisplaySchedulesModal()">&times;</span>
                <h2>Schedules for <span id="displayMachineName"></span></h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Available Slots</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleData"></tbody>
                </table>
            </div>
        </div>

        <!-- Modal for Adding Machines -->
        <div id="addMachineModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddMachineModal()">&times;</span>
                <h2>Add Machine</h2>
                <form id="addMachineForm" method="post" action="add_machine.php" enctype="multipart/form-data">
                    <label for="machine_name">Machine Name:</label>
                    <input type="text" name="machine_name" required><br>
                    <label for="machine_image">Upload Image:</label>
                    <input type="file" name="machine_image" required><br>
                    <button type="submit">Add Machine</button>
                </form>
            </div>
        </div>
    </main>
    <script>
        // Open and close modals for Add Schedule, Edit Machine, and Display Schedules
        function openAddScheduleModal(machineId, machineName) {
            document.getElementById("addScheduleModal").style.display = "block";
            document.getElementById("selectedMachineId").value = machineId;
            document.getElementById("selectedMachineName").innerText = machineName;
        }

        function closeAddScheduleModal() {
            document.getElementById("addScheduleModal").style.display = "none";
        }

        function openEditMachineModal(machineId, machineName, machinePhoto, availability) {
            document.getElementById("editMachineModal").style.display = "block";
            document.getElementById("editMachineId").value = machineId;
            document.getElementById("editMachineName").value = machineName;
            document.getElementById("currentMachineImage").src = "../images/" + machinePhoto;

            // Set the availability dropdown based on the fetched value
            document.getElementById('machine_availability').value = availability;
        }

        // Edit machine
        document.getElementById('updateMachineBtn').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default form submission

            const machineId = document.getElementById('editMachineId').value;
            const machineName = document.getElementById('editMachineName').value;
            const availability = document.getElementById('machine_availability').value;
            const image = document.getElementById('editMachineImage').files[0]; // Get selected file if available

            // Prepare form data to send to the server
            const formData = new FormData();
            formData.append('machine_id', machineId);
            formData.append('machine_name', machineName);
            formData.append('availability', availability);
            if (image) {
                formData.append('machine_image', image); // Append image if a new image is selected
            }

            // Send the updated data to the server via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'edit_machine.php', true);

            xhr.onload = function() {
                if (xhr.status == 200) {
                    alert('Machine updated successfully!');
                    // Refresh the page or update the display as needed
                    location.reload();
                } else {
                    alert('Failed to update machine.');
                }
            };

            xhr.send(formData);
        });

        function deleteMachine() {
            const machineId = document.getElementById("editMachineId").value;

            if (confirm("Are you sure you want to delete this machine?")) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_machine.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert("Machine deleted successfully!");
                        // Refresh the page or update the display as needed
                        location.reload();
                    } else {
                        alert("Failed to delete the machine. Please try again.");
                    }
                };
                xhr.send("machine_id=" + machineId);
            }
        }

        function openDisplaySchedulesModal(machineId, machineName) {
            document.getElementById("displaySchedulesModal").style.display = "block";
            document.getElementById("displayMachineName").innerText = machineName;
            // Fetch schedules for the machine using AJAX
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_schedules.php?machine_id=" + machineId, true);
            xhr.onload = function() {
                if (xhr.status == 200) {
                    document.getElementById("scheduleData").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function closeDisplaySchedulesModal() {
            document.getElementById("displaySchedulesModal").style.display = "none";
        }

        function openAddMachineModal() {
            document.getElementById("addMachineModal").style.display = "block";
        }

        function closeAddMachineModal() {
            document.getElementById("addMachineModal").style.display = "none";
        }

        // Close modals when clicking outside of them
        window.onclick = function(event) {
            if (event.target.classList.contains("modal")) {
                event.target.style.display = "none";
            }
        }

          // Function to confirm logout
       function confirmLogout() {
            const confirmAction = confirm("Are you sure you want to log out?");
            if (confirmAction) {
                // Redirect to the logout page
                window.location.href = "logout.php";
            }
        }
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const navLinks = document.querySelectorAll(".nav-link");

        // Set active class from localStorage
        const activePage = localStorage.getItem("activePage");
        if (activePage) {
            navLinks.forEach(link => {
                link.classList.remove("active");
                if (link.getAttribute("data-page") === activePage) {
                    link.classList.add("active");
                }
            });
        }

        // Save active page to localStorage when clicked
        navLinks.forEach(link => {
            link.addEventListener("click", function () {
                localStorage.setItem("activePage", this.getAttribute("data-page"));
            });
        });
    });
</script>

</body>
</html>