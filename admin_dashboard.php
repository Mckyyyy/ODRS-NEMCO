<?php
// admin_dashboard.php

session_start();
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "records_db1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch requests related to the current student or admin
$studentNumber = '1'; // Example student ID, adjust as needed
$sql = "SELECT * FROM requests WHERE studentNumber = '$studentNumber'";
$result = $conn->query($sql);

// Handle status update
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Update the status
    $update_sql = "UPDATE requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    $stmt->close();

    // Check if status is "Released" and move to recycle bin if true
    if ($status === 'Released') {
        // Set the deleted_at timestamp for the recycle bin
        $delete_sql = "UPDATE requests SET deleted_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        // Set a session variable to trigger SweetAlert on page reload
        $_SESSION['show_sweetalert'] = true;
    }

    // Redirect to avoid form resubmission
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch total request count
$request_sql = "SELECT COUNT(*) AS total_requests FROM requests";
$request_result = $conn->query($request_sql);
$total_requests = $request_result->fetch_assoc()['total_requests'];

// Fetch counts for completed and pending requests
$completed_sql = "SELECT COUNT(*) AS completed_requests FROM requests WHERE status = 'Completed'";
$completed_result = $conn->query($completed_sql);
$completed_requests = $completed_result->fetch_assoc()['completed_requests'];

$confirmed_sql = "SELECT COUNT(*) AS confirmed_requests FROM requests WHERE status = 'Confirmed'";
$confirmed_result = $conn->query($confirmed_sql);
$confirmed_requests = $confirmed_result->fetch_assoc()['confirmed_requests'];

$pending_sql = "SELECT COUNT(*) AS pending_requests FROM requests WHERE status = 'Pending'";
$pending_result = $conn->query($pending_sql);
$pending_requests = $pending_result->fetch_assoc()['pending_requests'];

$released_sql = "SELECT COUNT(*) AS released_requests FROM requests WHERE status = 'Released'";
$released_result = $conn->query($released_sql);
$released_requests = $released_result->fetch_assoc()['released_requests'];

// Fetch total payment (assuming you have a column for payment or similar)
$total_payment_sql = "SELECT SUM(payment) AS total_payment FROM requests";
$total_payment_result = $conn->query($total_payment_sql);
$total_payment = $total_payment_result->fetch_assoc()['total_payment'];

// Fetch non-deleted requests
$sql = "SELECT * FROM requests WHERE deleted_at IS NULL ORDER BY createdAt ASC";
$result = $conn->query($sql);

// Fetch deleted requests (for Recycle Bin)
$recycle_sql = "SELECT * FROM requests WHERE deleted_at IS NOT NULL ORDER BY deleted_at ASC";
$recycle_result = $conn->query($recycle_sql);

// Fetch users
$user_sql = "SELECT id, username, email, role FROM users ORDER BY id ASC";
$user_result = $conn->query($user_sql);

// Fetch students
$student_sql = "SELECT * FROM students ORDER BY studentNumber ASC";
$student_result = $conn->query($student_sql);

?>

<?php
// Check if we need to show the SweetAlert popup for "Released" status
if (isset($_SESSION['show_sweetalert']) && $_SESSION['show_sweetalert'] === true) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Request Released',
                text: 'The request has been moved to the Recycle Bin.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    showSection('requestListSection'); // Navigate to the Request List section
                }
            });
        });
    </script>";
    unset($_SESSION['show_sweetalert']); // Clear the session variable
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Adding Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .sidebar {
            height: 100vh;
            position: fixed;
            width: 70px;
            background: linear-gradient(90deg, rgba(12, 67, 111, 1) 1%, rgba(84, 84, 208, 1) 100%);
            padding-top: 20px;
            color: #fff;
            transition: width 0.3s;
            overflow: hidden;
            z-index: 1000;
        }

        .sidebar:hover {
            width: 180px;
        }

        .sidebar a {
            display: block;
            color: #fff;
            padding: 15px;
            text-decoration: none;
            white-space: nowrap;
            transition: background-color 0.3s, padding-left 0.3s;
        }

        .sidebar a:hover {
            background-color: #495057;
            padding-left: 30px;
        }

        .sidebar h4 {
            font-size: 18px;
            transition: font-size 0.3s;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar:hover h4 {
            font-size: 24px;
        }

        .sidebar i {
            margin-right: 10px;
            transition: margin 0.3s;
        }

        .content {
            margin-left: 70px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .sidebar:hover~.content {
            margin-left: 180px;
        }

        .top-nav {
            background: linear-gradient(90deg, rgba(34, 109, 170, 1) 0%, rgba(61, 61, 152, 1) 100%);
            padding: 15px;
            margin-top: -20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-left: -20px;
            padding-left: 20px;
            width: calc(100% + 20px);
        }

        .top-nav h2 {
            color: #fff;
            margin: 0;
        }

        .card {
            margin-bottom: 20px;
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            padding: 25px;
            text-align: center;
        }

        .card i {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 48px;
            color: rgba(255, 255, 255, 0.3);
        }

        .actions-column {
            width: 220px;
        }

        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
        }

        .section {
            display: none;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .logout-btn {
            padding: 12px 20px;
            /* Adjusted padding for better proportion */
            background-color: #54B4D3;
            /* Base background color */
            border: none;
            color: #fff;
            /* White text */
            font-size: 16px;
            /* Standard font size */
            font-weight: 500;
            /* Slightly bolder text for emphasis */
            display: flex;
            align-items: center;
            justify-content: center;
            /* Center content horizontally and vertically */
            border-radius: 30px;
            /* Fully rounded button for a modern look */
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease, filter 0.3s ease;
            cursor: pointer;
            position: relative;
            /* To position the pseudo-element for an extra effect */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            /* Subtle shadow for depth */
        }

        /* Hover Effect: Smooth background change and subtle scaling */
        .logout-btn:hover {
            background-color: #468fa5;
            /* Darker shade on hover */
            transform: scale(1.05);
            /* Slightly increase size */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            /* Darker shadow on hover for depth */
            filter: brightness(1.1);
            /* Lighten the button to give it a more vibrant look */
        }

        /* Active (click) effect: Shrink back slightly for a press effect */
        .logout-btn:active {
            transform: scale(1);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            /* Lighter shadow on click */
        }

        /* Icon inside the button */
        .logout-btn i {
            margin-right: 10px;
            /* More spacing between the icon and the label */
            font-size: 18px;
            /* Icon size */
            transition: transform 0.3s ease;
            /* Smooth transition for the icon */
        }

        /* Hover effect on the icon: rotate slightly when hovering */
        .logout-btn:hover i {
            transform: rotate(15deg);
            /* Subtle rotation effect on the icon */
        }

        /* Focus State: Highlight the button when focused for accessibility */
        .logout-btn:focus {
            outline: none;
            /* Remove default outline */
            box-shadow: 0 0 0 3px rgba(84, 180, 211, 0.7);
            /* Add focus shadow for clarity */
        }

        /* Pseudo-element for a "glow" effect */
        .logout-btn::before {
            content: '';
            /* Empty content */
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(45deg, rgba(84, 180, 211, 0.2), rgba(84, 180, 211, 0));
            /* Glowing gradient */
            border-radius: 30px;
            /* Match border radius */
            z-index: -1;
            /* Ensure it stays behind the button */
            transition: all 0.4s ease;
        }

        /* Hover effect for pseudo-element: Increase glow intensity */
        .logout-btn:hover::before {
            background: linear-gradient(45deg, rgba(84, 180, 211, 0.5), rgba(84, 180, 211, 0));
            /* More intense glow */
        }

        /* Additional responsiveness for smaller screens */
        @media (max-width: 768px) {
            .logout-btn {
                padding: 10px 16px;
                /* Slightly smaller padding on mobile */
                font-size: 14px;
                /* Smaller text for mobile */
            }

            .logout-btn i {
                font-size: 16px;
                /* Smaller icon size on mobile */
            }
        }


        .modal .modal-header {
            background-color: #007bff;
            color: white;
            border-bottom: 2px solid #0056b3;
        }

        .modal .btn-close {
            color: white;
            background: none;
            border: none;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Table Enhancements */
        .table {
            border-collapse: collapse;
            width: 100%;
            background: white;
        }

        .table th {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 12px;
            border: 1px solid #ddd;
        }

        .table td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        /* Flexbox for equal-sized cards */
        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .card {
            flex: 1 1 0;
            min-width: 250px;
            max-width: 100%;
        }

        /* Animations */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        /* New Styles for Maintenance Section */
        #customSettingsSection {
            background: rgba(255, 255, 255, 0.1);
            /* Transparent white background */
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            /* Shadow for depth */
            text-align: center;
            margin-top: 20px;
            /* Adjust for spacing */
        }

        .customBtn {
            font-size: 1.2rem;
            /* Slightly larger text */
            padding: 15px 30px;
            /* Larger button size */
            border-radius: 50px;
            /* Fully rounded buttons */
            transition: all 0.3s ease;
            /* Smooth hover transition */
            display: inline-block;
        }

        .customBtn.customActivate {
            background-color: #ffcc00;
            color: #1d2671;
        }

        .customBtn.customDeactivate {
            background-color: #555;
            color: white;
        }

        .customBtn:hover {
            opacity: 0.8;
            /* Light hover effect */
        }

        /* Gradient Background for the new section */
        .customBackground {
            background: linear-gradient(135deg, #1d2671, #c33764);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }


        .notifications-container {
            position: relative;
            margin-right: 10px;
            /* Adjusted space to prevent overlap */
        }

        /* Notifications Button */
        .notifications-btn {
            padding: 10px 15px;
            /* Adjusted padding for a more balanced look */
            background-color: #54B4D3;
            /* Keep the background color */
            border: none;
            color: #fff;
            font-size: 16px;
            /* Keep consistent font size */
            display: flex;
            align-items: center;
            justify-content: center;
            /* Center horizontally */
            border-radius: 50%;
            /* Make the button circular */
            transition: background-color 0.3s ease, transform 0.2s ease;
            /* Smooth hover and click transitions */
            position: relative;
            /* Keep position for badge */
            cursor: pointer;
            /* Pointer cursor on hover */
        }

        .notifications-btn:hover {
            background-color: #468fa5;
            /* Darker shade on hover */
            transform: scale(1.1);
            /* Slightly increase button size for interactivity */
        }

        .notifications-btn:active {
            transform: scale(1);
            /* Button shrinks back on click */
        }

        /* Icon inside button */
        .notifications-btn i {
            font-size: 20px;
            /* Increased icon size for visibility */
            margin-right: 8px;
            /* Spacing between icon and label */
        }

        /* Badge Style */
        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            /* Red badge color */
            color: white;
            font-size: 12px;
            padding: 5px 8px;
            /* More padding for better legibility */
            border-radius: 50%;
            /* Make badge round */
            display: none;
            /* Hidden by default */
            font-weight: bold;
            /* Bold text for emphasis */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            /* Add a subtle shadow to make badge pop */
            animation: pulse 1.5s infinite;
            /* Pulse animation for attention */
        }

        /* Badge visible with notifications */
        .notifications-btn .badge {
            display: inline-block;
            animation: pulse 1.5s infinite;
            /* Pulse animation */
        }

        /* Pulse effect for the badge */
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .welcome-text-animation {
            font-size: 28px;
            /* Font size for the text */
            font-weight: bold;
            /* Emphasize the text */
            color: #fff;
            /* White text for contrast */
            font-family: 'Courier New', monospace;
            /* Monospace font for typing effect */
            white-space: nowrap;
            /* Prevent text from wrapping */
            overflow: hidden;
            /* Hide overflowing text for the animation */
            width: 0;
            /* Start with no visible text */
            animation: typing 4s steps(14, end);
            /* Only typing animation */
            animation-fill-mode: forwards;
            /* Ensure the text remains visible after animation */
        }

        /* Typing effect */
        @keyframes typing {
            from {
                width: 0;
                /* Start with no visible text */
            }

            to {
                width: 14ch;
                /* Adjust width to fit "Welcome, Admin" */
            }
        }
    </style>

    <script>
        // JavaScript to handle sidebar clicks and toggle sections
        function showSection(sectionId) {
            document.getElementById('loadingOverlay').style.display = 'flex';

            setTimeout(function () {
                document.querySelectorAll('.section').forEach(section => section.style.display = 'none');
                const section = document.getElementById(sectionId);
                if (section) {
                    section.style.display = 'block';
                } else {
                    console.error(`Section with ID ${sectionId} not found.`);
                }

                document.getElementById('loadingOverlay').style.display = 'none';
            }, 500);
        }
        let currentNotifications = 0;

        function updateNotificationsCount() {
            fetch('check_new_requests.php')
                .then(response => response.json())
                .then(data => {
                    const notificationsCount = document.getElementById('notifications-count');

                    // Retrieve the last known notification count from localStorage
                    let lastKnownNotifications = parseInt(localStorage.getItem('lastKnownNotifications')) || 0;

                    console.log('Current Notifications:', currentNotifications);
                    console.log('Last Known Notifications:', lastKnownNotifications);
                    console.log('Data from server:', data.new_requests);

                    if (data.new_requests > currentNotifications) {
                        // Update current notification count
                        currentNotifications = data.new_requests;

                        // Update the badge number
                        notificationsCount.textContent = currentNotifications;
                        notificationsCount.style.display = 'inline'; // Ensure the badge is visible

                        // Show the pop-up only if the new count exceeds the last known count
                        if (currentNotifications > lastKnownNotifications) {
                            Swal.fire({
                                title: 'New Request Alert!',
                                text: `You have ${currentNotifications} new request(s) with status 'Pending'.`,
                                icon: 'info',
                                showCloseButton: true,
                                allowOutsideClick: false,
                                confirmButtonText: 'OK'
                            });

                            // Update the last known notification count in localStorage
                            localStorage.setItem('lastKnownNotifications', currentNotifications);
                        }
                    } else {
                        // Always show the badge, even when there are no new notifications
                        notificationsCount.textContent = data.new_requests === 0 ? '0' : data.new_requests;
                        notificationsCount.style.display = 'inline'; // Ensure the badge is always visible

                        // Reset the last known notifications count in localStorage if there are no new requests
                        if (data.new_requests === 0) {
                            localStorage.setItem('lastKnownNotifications', 0);
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Function to display notifications on click
        function showNotifications() {
            Swal.fire({
                title: 'Notifications',
                text: `You have ${currentNotifications} new request(s) with status 'Pending'.`,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        setInterval(updateNotificationsCount);

    </script>
</head>

<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay"
        style="display:none; position: fixed; width: 100%; height: 100%; top: 0; left: 0; background: rgba(255, 255, 255, 0.7); z-index: 9999; justify-content: center; align-items: center;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <div class="sidebar">
        <h4 class="text-center d-flex align-items-center"
            style="border-bottom: 1px solid whitesmoke; padding-bottom: 12.5px; margin-top: -15px; font-size: 25px; font-weight: bold;">
            <img src="nemco.jpg" alt="Logo"
                style="width: 60px; height: auto; margin-right: 10px; margin-left: 10px;">NEMCO
        </h4>

        <a href="#" onclick="showSection('dashboardSection')"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="#" onclick="showSection('requestListSection')"><i class="fas fa-list"></i> Request Lists</a>
        <a href="#" onclick="showSection('usersSection')"><i class="fas fa-users"></i> Users</a>
        <a href="#" onclick="showSection('expiredSection')"><i class="fas fa-calendar-times"></i> Expired</a>
        <a href="#" onclick="showSection('settingsSection')"><i class="fas fa-cogs"></i> Settings</a>
        <a href="#" onclick="showSection('recycleSection')"><i class="fas fa-recycle"></i>Released</a>
    </div>

    <div class="content fade-in">
        <div class="top-nav" style="display: flex; align-items: center; justify-content: flex-end;">
            <h2 class="welcome-text-animation" style="margin-right: auto;">Welcome, Admin</h2>
            <div class="notifications-container" style="margin-right: 10px;">
                <button onclick="showNotifications()" class="btn btn-info notifications-btn">
                    <i class="fas fa-bell"></i>
                    <span id="notifications-count" class="badge">0</span>
                </button>
            </div>
            <button onclick="confirmLogout()" class="btn btn-info logout-btn">
                <i class="fas fa-user"></i> Logout
            </button>
        </div>
        <!-- Dashboard Section -->
        <div id="dashboardSection" class="section" style="display: block; margin-top: 10px;">
            <div class="container-fluid">
                <!-- Top Row -->
                <div class="row justify-content-center text-center">
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                        <div class="card text-white bg-primary shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Total Requests</h5>
                                <p class="card-text display-4"><?php echo $total_requests; ?></p>
                                <i class="fas fa-tasks fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                        <div class="card text-white bg-dark shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Released</h5>
                                <p class="card-text display-4"><?php echo $released_requests; ?></p>
                                <i class="fas fa-file-export fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                        <div class="card text-white bg-success shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">For Final Printing</h5>
                                <p class="card-text display-4"><?php echo $completed_requests; ?></p>
                                <i class="fas fa-print fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row -->
                <div class="row justify-content-center text-center">
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                        <div class="card text-white bg-secondary shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Confirmed Requests</h5>
                                <p class="card-text display-4"><?php echo $confirmed_requests; ?></p>
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                        <div class="card text-white bg-warning shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Pending Requests</h5>
                                <p class="card-text display-4"><?php echo $pending_requests; ?></p>
                                <i class="fas fa-hourglass-half fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clock Section -->
                <div class="row">
                    <div class="col-12 text-center">
                        <div id="clock" style="font-size: 3rem; font-weight: bold; color: #333;"></div>
                    </div>
                </div>
                <!-- Chart Section -->
                <div class="row">
                    <!-- Bar Chart -->
                    <div class="col-lg-8 col-md-12">
                        <canvas id="dashboardChart" style="max-height: 400px;"></canvas>
                    </div>
                    <!-- Pie Chart -->
                    <div class="col-lg-4 col-md-12 d-flex justify-content-center align-items-center">
                        <canvas id="dashboardPieChart" style="max-height: 250px; max-width: 250px;"></canvas>
                    </div>
                </div>

                <!-- Second Chart (Line Chart) -->
                <div class="row mt-5">
                    <div class="col-12">
                        <canvas id="dashboardLineChart" style="max-height: 400px;"></canvas>
                    </div>
                </div>

            </div>
        </div>

        <script>
            // Function to update clock with analytics tracking
            function updateClock() {
                const clockElement = document.getElementById('clock');

                // Get current UTC time and adjust for Philippine timezone (UTC+8)
                const now = new Date();
                const utcHours = now.getUTCHours();
                const utcMinutes = now.getUTCMinutes();
                const utcSeconds = now.getUTCSeconds();

                // Adjust to Philippine Time (UTC + 8)
                const philippineHours = (utcHours + 8) % 24;

                // Convert to 12-hour format and determine AM/PM
                const period = philippineHours >= 12 ? 'P.M' : 'A.M';
                const hours12 = philippineHours % 12 || 12;

                // Format minutes and seconds
                const minutes = String(utcMinutes).padStart(2, '0');
                const seconds = String(utcSeconds).padStart(2, '0');

                // Display the time with A.M./P.M.
                clockElement.textContent = `${hours12}:${minutes}:${seconds} ${period}`;

                // Analytics tracking for clock updates
                if (typeof gtag === 'function') {
                    gtag('event', 'clock_update', {
                        'event_category': 'Clock',
                        'event_label': 'Clock Displayed',
                        'value': `${hours12}:${minutes}:${seconds} ${period}`
                    });
                }
            }

            setInterval(updateClock, 1000);  // Update every second
            updateClock();  // Initial call to display the clock immediately
        </script>

        <style>
            /* Ensure the canvas elements are responsive */
            #dashboardChart,
            #dashboardLineChart {
                width: 100% !important;
                height: 400px !important;
                /* Set height based on preference */
            }
        </style>

        <script>
            // Bar Chart with Enhanced Design and Animation
            const ctx = document.getElementById('dashboardChart').getContext('2d');
            const dashboardChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Total Requests', 'Released Request', 'Final Printing', 'Confirmed Requests', 'Pending Requests'],
                    datasets: [{
                        label: '# of Requests',
                        data: [<?php echo $total_requests; ?>, <?php echo $released_requests; ?>, <?php echo $completed_requests; ?>, <?php echo $confirmed_requests; ?>, <?php echo $pending_requests; ?>],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 206, 86, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(200, 200, 200, 0.4)',
                                lineWidth: 1,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                callback: function (value) {
                                    return Number.isInteger(value) ? value : null; // Only show integers
                                },
                                color: '#333'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#333',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Requests Summary',
                            color: '#333',
                            font: {
                                size: 24,
                                weight: 'bold',
                                family: 'Arial, sans-serif'
                            },
                            padding: {
                                top: 20,
                                bottom: 10
                            }
                        }
                    },
                    animation: {
                        duration: 5000,
                        easing: 'easeOutBounce',
                        onComplete: function () {
                            console.log('Bar Chart Animation Completed');
                        }
                    },
                    onHover: function (e) {
                        e.native.target.style.cursor = 'pointer';
                    }
                }
            });

            // Line Chart with Enhanced Design, Visible Gradient Fill, and Matching Colors per Label
            const ctx2 = document.getElementById('dashboardLineChart').getContext('2d');

            // Create a stronger gradient for the line fill
            const gradientFill = ctx2.createLinearGradient(0, 0, 0, 400);
            gradientFill.addColorStop(0, 'rgba(75, 192, 192, 0.8)');
            gradientFill.addColorStop(0.5, 'rgba(75, 192, 192, 0.5)');
            gradientFill.addColorStop(1, 'rgba(75, 192, 192, 0.2)');

            const dashboardLineChart = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: ['Total Requests', 'Released Request', 'Final Printing', 'Confirmed Requests', 'Pending Requests'],
                    datasets: [{
                        label: 'Requests Trend',
                        data: [<?php echo $total_requests; ?>, <?php echo $released_requests; ?>, <?php echo $completed_requests; ?>, <?php echo $confirmed_requests; ?>, <?php echo $pending_requests; ?>],
                        backgroundColor: gradientFill,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 206, 86, 0.8)'
                        ],
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 9,
                        pointHoverBackgroundColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        pointHoverBorderColor: 'rgba(255, 255, 255, 1)',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#555',
                                font: {
                                    family: 'Arial, sans-serif',
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(200, 200, 200, 0.4)',
                                lineWidth: 1,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                color: '#555',
                                font: {
                                    family: 'Arial, sans-serif',
                                    size: 14,
                                    weight: 'bold'
                                },
                                callback: function (value) {
                                    return Number.isInteger(value) ? value : null;
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#555',
                                font: {
                                    family: 'Arial, sans-serif',
                                    size: 16,
                                    weight: 'bold'
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Requests Over Time',
                            color: '#333',
                            font: {
                                size: 24,
                                weight: 'bold',
                                family: 'Arial, sans-serif'
                            },
                            padding: {
                                top: 20,
                                bottom: 10
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleFont: {
                                family: 'Arial, sans-serif',
                                size: 16,
                                weight: 'bold'
                            },
                            bodyFont: {
                                family: 'Arial, sans-serif',
                                size: 14
                            },
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12
                        }
                    },
                    animation: {
                        duration: 5000,
                        easing: 'easeOutBounce'
                    }
                }
            });

            // Pie Chart with Enhanced Design and Animation
            const ctx3 = document.getElementById('dashboardPieChart').getContext('2d');
            const dashboardPieChart = new Chart(ctx3, {
                type: 'pie',
                data: {
                    labels: ['Total Requests', 'Released Request', 'Final Printing', 'Confirmed Requests', 'Pending Requests'],
                    datasets: [{
                        data: [<?php echo $total_requests; ?>, <?php echo $released_requests; ?>, <?php echo $completed_requests; ?>, <?php echo $confirmed_requests; ?>, <?php echo $pending_requests; ?>],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 206, 86, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 5000,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                generateLabels: function (chart) {
                                    const data = chart.data.datasets[0].data;
                                    const labels = chart.data.labels;
                                    return labels.map((label, index) => ({
                                        text: `${label}: ${data[index]}`,
                                        fillStyle: chart.data.datasets[0].backgroundColor[index],
                                        hidden: false,
                                        index: index
                                    }));
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value}`;
                                }
                            }
                        }
                    }
                }
            });
        </script>

        </script>
        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php elseif (isset($_GET['error'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <!-- Users Section -->
        <div id="usersSection" class="section">
            <div class="card">
                <div class="card-header">
                    <h3>User List</h3>
                </div>
                <div class="card-body">
                    <a href="add_user.php" class="btn btn-primary float-left mb-2">Add User</a>
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($user_result->num_rows > 0) {
                                while ($user = $user_result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$user['id']}</td>
                                            <td>{$user['username']}</td>
                                            <td>{$user['email']}</td>
                                            <td>{$user['role']}</td>
                                            <td>
                                                <a href='edit_user.php?id={$user['id']}' class='btn btn-warning btn-sm'></i> Edit</a>
                                                <a href='delete_user.php?id={$user['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this user?\")'></i> Delete</a>
                                            </td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No users found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
        include 'db_connection.php';  // Database connection
        
        // Fetch and display requests from the database
        $result = $conn->query("SELECT * FROM requests WHERE deleted_at IS NULL");
        // Fetch expired requests where expiration_date is less than current date
        $expired_sql = "SELECT * FROM requests WHERE expiration_date < NOW() AND status != 'Expired' AND deleted_at IS NULL ORDER BY expiration_date ASC";
        $expired_result = $conn->query($expired_sql);

        // Update requests that have passed expiration date to 'Expired' status
        $update_expired_sql = "UPDATE requests SET status = 'Expired' WHERE expiration_date < NOW() AND status != 'Expired'";
        $conn->query($update_expired_sql);


        ?>
        <!-- Request List Section -->
        <div id="requestListSection" class="section">
            <div class="card">
                <div class="card-header">
                    <h3>Request List</h3>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="mb-3">
                        <form method="GET" id="searchForm">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by Transaction Code, Student Number, or Name"
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                                id="searchInput">
                        </form>
                    </div>

                    <table class="table table-bordered mt-2" id="requestTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Transaction Code</th>
                                <th>Student Number</th>
                                <th>Name</th>
                                <th>Program</th>
                                <th>Contact Number</th>
                                <th>Term Attended</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="actions-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Number of requests per page
                            $perPage = 7;

                            // Get the current page or default to page 1
                            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

                            // Get the search query if it exists
                            $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

                            // Calculate the offset for the SQL query
                            $offset = ($page - 1) * $perPage;

                            // SQL to count total records (for pagination)
                            $totalSql = "SELECT COUNT(*) AS total FROM requests WHERE 
                                 transaction_code LIKE '%$searchQuery%' 
                                 OR studentNumber LIKE '%$searchQuery%' 
                                 OR CONCAT(firstName, ' ', middleName, ' ', lastName, ' ', suffix) LIKE '%$searchQuery%'";
                            $resultTotal = $conn->query($totalSql);
                            $totalRows = $resultTotal->fetch_assoc()['total'];
                            $totalPages = ceil($totalRows / $perPage);

                            // Fetch the filtered requests for the current page, excluding 'Released' status
                            // Fetch requests where status is not 'Expired' and expiration date is still valid
                            $sql = "SELECT * FROM requests WHERE 
                            (transaction_code LIKE '%$searchQuery%' 
                            OR studentNumber LIKE '%$searchQuery%' 
                            OR CONCAT(firstName, ' ', middleName, ' ', lastName, ' ', suffix) LIKE '%$searchQuery%')
                            AND status NOT IN ('Expired', 'Released')  -- Exclude expired and released requests
                            AND (expiration_date > NOW() OR expiration_date IS NULL)  -- Only include requests not expired
                            LIMIT $perPage OFFSET $offset";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Highlight searched term in relevant fields
                                    $highlightedTransactionCode = preg_replace("/($searchQuery)/i", "<span class='highlight'>$1</span>", $row['transaction_code']);
                                    $highlightedStudentNumber = preg_replace("/($searchQuery)/i", "<span class='highlight'>$1</span>", $row['studentNumber']);
                                    $highlightedName = preg_replace("/($searchQuery)/i", "<span class='highlight'>$1</span>", $row['firstName'] . ' ' . $row['middleName'] . ' ' . $row['lastName'] . ' ' . $row['suffix']);

                                    echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$highlightedTransactionCode}</td>
                                    <td>{$highlightedStudentNumber}</td>
                                    <td>{$highlightedName}</td>
                                    <td>{$row['program']}</td>
                                    <td>{$row['contactNumber']}</td>
                                    <td>{$row['lastTermAttended']}</td>";
                                    echo "<td style='vertical-align: middle; text-align: center;'>
                                    <form method='POST' action='update_payment.php' 
                                          class='d-flex flex-column align-items-center' id='paymentForm_{$row['id']}' onsubmit='return updatePayment(event, {$row['id']});'>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                        <input type='number' step='0.01' name='payment' value='{$row['payment']}' 
                                               class='form-control form-control-sm' 
                                               style='max-width: 80px; width: 100%; margin-bottom: 5px; padding: 4px; font-size: 12px;' required>
                                        <button type='submit' class='btn btn-primary btn-sm' 
                                                style='max-width: 80px; width: 100%; padding: 4px 8px; font-size: 12px;'>Update</button>
                                    </form>
                                </td>";
                                    echo "<td>{$row['status']}</td>";

                                    if ($row['status'] == 'Completed') {
                                        echo "<td>
                                        <form method='POST' action='process_claim_date.php' onsubmit='return saveClaimDate(event, {$row['id']});'>
                                            <input type='hidden' name='id' value='{$row['id']}'>
                                            <input type='date' name='claim_date' value='{$row['claim_date']}' required>
                                            <button type='submit' class='btn btn-success btn-sm mt-2'>Save</button>
                                        </form>
                                    </td>";
                                    } elseif ($row['status'] == 'Confirmed') {
                                        echo "<td>
                                    <form method='POST' action='process_expiration_date.php' onsubmit='return saveExpirationDate(event, {$row['id']});'>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <input type='date' name='expiration_date' value='{$row['expiration_date']}' required>
                                    <button type='submit' class='btn btn-warning btn-sm mt-2'>Set Expiration</button>
                                </form>

                                    </td>";
                                    } else {
                                        echo "<td>N/A</td>";
                                    }
                                    echo "<td class='actions-column'>
                                    <a href='#' class='btn btn-info btn-sm mt-2' onclick='loadRequestDetails({$row['id']})'>View</a>
                                    <a href='edit_request.php?id={$row['id']}' class='btn btn-warning btn-sm mt-2'>Slip</a>
                                    <form method='POST' action='admin_dashboard.php#requestListSection' style='display: inline;'>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                        <select name='status' class='form-control-sm mb-1 mt-1' onchange='this.form.submit()'>
                                            <option value='Pending'" . ($row['status'] == 'Pending' ? ' selected' : '') . ">Pending</option>
                                            <option value='Confirmed'" . ($row['status'] == 'Confirmed' ? ' selected' : '') . ">Confirmed</option>
                                            <option value='Completed'" . ($row['status'] == 'Completed' ? ' selected' : '') . ">Final Printing</option>
                                            <option value='Released'" . ($row['status'] == 'Released' ? ' selected' : '') . ">Released</option>
                                        </select>
                                        <input type='hidden' name='update_status' value='1'>
                                    </form>";

                                    // Checking if the document is for reissuance
                                    if ($row['forReissuance']) {
                                        // If it's for Re-issuance, show the name and download buttons
                                        echo "<div class='reissuance-box p-2 mb-2' style='background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 5px; font-size: 0.85rem;'><strong>Re-issuance of Diploma:</strong></div>";

                                        if ($row['letterRequest'] && $row['affidavitLoss']) {
                                            echo "<a href='generate_document.php?download=true&file={$row['letterRequest']}' class='btn btn-secondary btn-sm mt-2'>Download Letter Request</a>";
                                            echo "<a href='generate_document.php?download=true&file={$row['affidavitLoss']}' class='btn btn-secondary btn-sm mt-2'>Download Affidavit of Loss</a>";
                                            echo "<div style='margin-top: 10px;'></div>";
                                        } else {
                                            echo "<p class='text-warning mt-2'>Required files are not uploaded yet.</p>";
                                        }
                                    }

                                    // For other cases (or even when forReissuance is true), show the "Generate Document" button
                                    echo "<form action='generate_document.php' method='GET' class='d-inline'>
                                    <input type='hidden' name='studentNumber' value='{$row['studentNumber']}'>
                                    <select name='document_type' class='form-control-sm mb-1'>";

                                    $documents = [
                                        'goodmoralcert' => ['name' => 'Good Moral Cert.', 'field' => 'forGoodmoral'],
                                        'enrollmentcert' => ['name' => 'For Enrollment (ToR)', 'field' => 'forEnrollment'],
                                        'gpa' => ['name' => 'GPA Report', 'field' => 'forGpa'],
                                        'registration' => ['name' => 'Registration Cert', 'field' => 'forRegistration'],
                                        'ranking' => ['name' => 'For Ranking (ToR)', 'field' => 'forRanking'],
                                        'reference' => ['name' => 'For Reference (ToR)', 'field' => 'forReference'],
                                        'graduation' => ['name' => 'Graduation Cert.', 'field' => 'forGraduation'],
                                        'earnedUnits' => ['name' => 'Earned Units Cert.', 'field' => 'forEarnedUnits'],
                                        'graduating' => ['name' => 'Graduating Cert.', 'field' => 'forGraduating'],
                                        'boardExam' => ['name' => 'Board Exam (ToR)', 'field' => 'forBoardExam'],
                                        'transfer' => ['name' => 'For Transfer (ToR)', 'field' => 'forTransfer'],
                                        'employment' => ['name' => 'For Employment (ToR)', 'field' => 'forEmployment'],
                                        'sju' => ['name' => 'SJU Cert.', 'field' => 'forSpec'],
                                        'mediumInstruction' => ['name' => 'Medium of Instruction Cert.', 'field' => 'formediumInstruction'],
                                        'Graduated2' => ['name' => 'TOR 2ND GRADUATE', 'field' => 'forGraduated2'],
                                        'Graduated4' => ['name' => 'TOR 4TH GRADUATE', 'field' => 'forGraduated4'],
                                        'Undergrad1' => ['name' => 'TOR UnderGrad 1st Yr', 'field' => 'forUndergrad1'],
                                        'Undergrad2' => ['name' => 'TOR UnderGrad 2nd Yr', 'field' => 'forUndergrad2'],
                                        'Undergrad3' => ['name' => 'TOR UnderGrad 3rd Yr', 'field' => 'forUndergrad3'],
                                        'Undergrad4' => ['name' => 'TOR UnderGrad 4th Yr', 'field' => 'forUndergrad4'],
                                        'sabroad' => ['name' => 'Studies Abroad (ToR)', 'field' => 'forStudiesAbroad'],
                                        'dismissal' => ['name' => 'Honorable Dismissal', 'field' => 'forDismissal'],
                                        'Enrollmentcert' => ['name' => 'Enrollment Cert.', 'field' => 'forEnrollmentcert'],
                                        'others' => ['name' => 'Others', 'field' => 'forOthers'],
                                        'authentication' => ['name' => 'Authentication', 'field' => 'forAuthentication'],
                                        'cav' => ['name' => 'CAV', 'field' => 'forCertification']
                                    ];

                                    foreach ($documents as $key => $doc) {
                                        if ($row[$doc['field']]) {
                                            echo "<option value='$key'>{$doc['name']}</option>";
                                        }
                                    }

                                    echo "</select>
                                  <button type='submit' class='btn btn-primary btn-sm mt-2'>Generate Document</button>
                                </form>";

                                    echo "</td>
                                </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='11'>No requests found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0);" aria-label="Previous" title="Previous"
                                        onclick="loadPage(<?php echo $page - 1; ?>)">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <span class="page-link"><?php echo $i; ?></span>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0);" aria-label="Next" title="Next"
                                        onclick="loadPage(<?php echo $page + 1; ?>)">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">&raquo;</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>


                </div>
            </div>
        </div>

        <!-- Highlight Style -->
        <style>
            .highlight {
                background-color: yellow;
                font-weight: bold;
            }
        </style>

        <script>
            function attachSearchListeners() {
                const searchInput = document.getElementById('searchInput');
                if (!searchInput) return;

                searchInput.addEventListener('input', function () {
                    const searchQuery = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#requestTable tbody tr');

                    rows.forEach(row => {
                        const transactionCode = row.cells[1]?.textContent.toLowerCase() || '';
                        const studentNumber = row.cells[2]?.textContent.toLowerCase() || '';
                        const name = row.cells[3]?.textContent.toLowerCase() || '';

                        if (
                            transactionCode.includes(searchQuery) ||
                            studentNumber.includes(searchQuery) ||
                            name.includes(searchQuery)
                        ) {
                            row.style.display = '';
                            row.innerHTML = row.innerHTML.replace(/(<span class='highlight'>)(.*?)(<\/span>)/g, '$2'); // Remove old highlights
                            row.cells[1].innerHTML = row.cells[1].textContent.replace(new RegExp(searchQuery, 'gi'), "<span class='highlight'>$&</span>");
                            row.cells[2].innerHTML = row.cells[2].textContent.replace(new RegExp(searchQuery, 'gi'), "<span class='highlight'>$&</span>");
                            row.cells[3].innerHTML = row.cells[3].textContent.replace(new RegExp(searchQuery, 'gi'), "<span class='highlight'>$&</span>");
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });

                document.getElementById('searchForm')?.addEventListener('submit', function (event) {
                    event.preventDefault(); // Prevent form submission
                });

                searchInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault(); // Disable Enter key
                    }
                });
            }

            function loadPage(page) {
                const url = `?page=${page}#requestListSection`;

                fetch(url, { method: 'GET' })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newSection = doc.querySelector('#requestListSection');

                        if (newSection) {
                            document.querySelector('#requestListSection').innerHTML = newSection.innerHTML;

                            // Reattach search listeners after content update
                            attachSearchListeners();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Initial attachment of search listeners
            attachSearchListeners();

            function saveExpirationDate(event, id) {
                const expirationDate = document.querySelector(`#expiration_date_${id}`).value;
                const currentDate = new Date().toISOString().split('T')[0];

                if (expirationDate <= currentDate) {
                    alert('The expiration date must be in the future.');
                    return false;
                }
                return true;
            }

        </script>

        <!-- Loading Modal -->
        <div id="loadingModal" class="modal-overlay">
            <div class="modal-content">
                <div class="loading-spinner" id="modalSpinner"></div>
                <div class="check-icon" id="modalCheck">&#10003;</div>
                <p id="modalMessage">Processing...</p>
            </div>
        </div>

        <!-- CSS for Modal -->
        <style>
            .modal-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                align-items: center;
                justify-content: center;
            }

            .modal-content {
                background: white;
                padding: 20px;
                border-radius: 10px;
                text-align: center;
                width: 200px;
            }

            .loading-spinner {
                width: 40px;
                height: 40px;
                border: 5px solid #f3f3f3;
                border-top: 5px solid #3498db;
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
                margin: 0 auto;
            }

            .check-icon {
                font-size: 40px;
                color: green;
                display: none;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
        <div class="modal fade modern-alert-modal" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="alertModalLabel">
                            <i class="bi bi-exclamation-circle-fill me-2"></i> ALERT
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Modal Body -->
                    <div class="modal-body text-center">
                        <p class="mt-3" id="modal-message">
                            This cannot be generated.
                        </p>
                    </div>
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary w-100 fw-bold" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Check if modalMessage exists in the URL
                const urlParams = new URLSearchParams(window.location.search);
                const modalMessage = urlParams.get('modalMessage');

                if (modalMessage) {
                    // Scroll smoothly to the requestListSection
                    document.getElementById('requestListSection').scrollIntoView({ behavior: 'smooth', block: 'start' });

                    // Show the modal
                    const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
                    alertModal.show();

                    // Remove modalMessage from the URL without reloading the page
                    const url = new URL(window.location.href);
                    url.searchParams.delete('modalMessage');
                    window.history.replaceState({}, document.title, url.toString());
                }
            });

        </script>
        <style>
            /* Smaller Modal Design */
            .modern-alert-modal .modal-dialog.modal-sm {
                max-width: 300px;
                margin: auto;
            }

            .modern-alert-modal .modal-content {
                border-radius: 12px;
                border: none;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }

            .modern-alert-modal .modal-header {
                background-color: #ff6f61;
                color: white;
                font-size: 1rem;
                font-weight: bold;
                justify-content: center;
                border-bottom: none;
            }

            .modern-alert-modal .modal-header .btn-close {
                position: absolute;
                right: 15px;
                top: 10px;
                background-color: rgba(255, 255, 255, 0.8);
                border-radius: 50%;
            }

            .modern-alert-modal .modal-body {
                padding: 1rem;
                font-size: 0.875rem;
                color: #555;
                text-align: center;
            }

            .modern-alert-modal .modal-footer {
                border-top: none;
                padding: 0.75rem;
                background-color: #f9f9f9;
                justify-content: center;
            }

            .modern-alert-modal .modal-footer .btn {
                background-color: #007bff;
                color: #fff;
                font-size: 0.875rem;
                padding: 0.5rem 1.5rem;
                border-radius: 20px;
                border: none;
            }

            .modern-alert-modal .modal-footer .btn:hover {
                background-color: #0056b3;
            }
        </style>
        <!-- JavaScript -->
        <script>
            function loadRequestListSection() {
                fetch(window.location.href, { method: 'GET' })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newRequestList = doc.querySelector('#requestListSection tbody').innerHTML;
                        document.querySelector('#requestListSection tbody').innerHTML = newRequestList;
                    })
                    .catch(error => console.error('Error loading request list section:', error));
            }

            function showLoadingModal() {
                document.getElementById('loadingModal').style.display = 'flex';
                document.getElementById('modalSpinner').style.display = 'block';
                document.getElementById('modalCheck').style.display = 'none';
                document.getElementById('modalMessage').innerText = 'Processing...';
            }

            function showSuccessModal() {
                document.getElementById('modalSpinner').style.display = 'none';
                document.getElementById('modalCheck').style.display = 'block';
                document.getElementById('modalMessage').innerText = 'Success!';
            }

            function hideLoadingModal() {
                setTimeout(() => { document.getElementById('loadingModal').style.display = 'none'; }, 1000);
            }

            function saveClaimDate(event, id) {
                event.preventDefault();
                showLoadingModal(); // Show loading modal while waiting for response

                const formData = new FormData(event.target);
                console.log('Sending form data:', formData); // Debugging: log the data being sent

                fetch('process_claim_date.php', {
                    method: 'POST',
                    body: formData // Send the form data to the server
                })
                    .then(response => {
                        console.log('Response received:', response); // Log the response object
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json(); // Parse the JSON response from the server
                    })
                    .then(data => {
                        console.log('Response data:', data); // Log the JSON data returned from the server
                        if (data.success) {
                            showSuccessModal(); // Show success modal if operation is successful
                            loadRequestListSection(); // Reload the request list
                        } else {
                            alert(data.error || 'Failed to save claim date');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error); // Log any error
                        alert('Error: ' + error.message); // Show the error message
                    })
                    .finally(() => {
                        hideLoadingModal(); // Hide the loading modal after the request completes
                    });
            }


            // Function to handle payment update with AJAX
            function updatePayment(event, id) {
                event.preventDefault();
                showLoadingModal(); // Show the loading modal

                const form = document.getElementById('paymentForm_' + id); // Get the form dynamically by ID
                const formData = new FormData(form); // Get form data

                fetch('update_payment.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json()) // Expecting JSON response from PHP
                    .then(data => {
                        if (data.success) {
                            showSuccessModal(); // Show success modal
                            loadRequestListSection(); // Reload the request list section after update
                        } else {
                            showErrorModal(data.error || 'Failed to update payment');
                        }
                    })
                    .catch(error => {
                        showErrorModal('An error occurred: ' + error);
                    })
                    .finally(() => {
                        hideLoadingModal();
                    });
            }

            function saveExpirationDate(event, id) {
                event.preventDefault();
                showLoadingModal(); // Show loading modal while waiting for response

                const formData = new FormData(event.target);
                console.log('Sending form data:', formData); // Debugging: log the data being sent

                fetch('process_expiration_date.php', {
                    method: 'POST',
                    body: formData // Send the form data to the server
                })
                    .then(response => {
                        console.log('Response received:', response); // Log the response object
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json(); // Parse the JSON response from the server
                    })
                    .then(data => {
                        console.log('Response data:', data); // Log the JSON data returned from the server
                        if (data.success) {
                            showSuccessModal(); // Show success modal if operation is successful
                            loadRequestListSection(); // Reload the request list
                        } else {
                            alert(data.error || 'Failed to save expiration date');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error); // Log any error
                        alert('Error: ' + error.message); // Show the error message
                    })
                    .finally(() => {
                        hideLoadingModal(); // Hide the loading modal after the request completes
                    });
            }

        </script>
        <!-- Modal for viewing request details -->
        <div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="custom-modal-content">
                    <div class="modal-body">
                        <div id="modalContent" class="p-3">
                            <p>Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function loadRequestDetails(requestId) {
                $('#viewRequestModal').modal('show');

                // Use AJAX to load the request details dynamically
                $.ajax({
                    url: 'view_request.php',
                    method: 'GET',
                    data: { id: requestId },
                    success: function (response) {
                        $('#modalContent').html(response);
                    },
                    error: function () {
                        $('#modalContent').html('<p class="text-danger">Error loading request details. Please try again.</p>');
                    }
                });
            }

            function loadRecycleDetails(requestId) {
                $('#viewRequestModal').modal('show');

                // Use AJAX to load the recycle request details dynamically
                $.ajax({
                    url: 'view_recycle_request.php',
                    method: 'GET',
                    data: { id: requestId },
                    success: function (response) {
                        $('#modalContent').html(response);
                    },
                    error: function () {
                        $('#modalContent').html('<p class="text-danger">Error loading recycle request details. Please try again.</p>');
                    }
                });
            }

            // Close the modal and set plain text in modal content after closing
            $('#viewRequestModal').on('hidden.bs.modal', function () {
                $('#modalContent').html('<p>Modal has been closed. No content to display.</p>');
            });
        </script>

        <!-- Styles -->
        <style>
            /* Modal and content styles */
            .custom-modal-content {
                background-color: transparent;
                /* Transparent background for modal */
                box-shadow: none;
                /* Remove shadow */
                border: none;
                /* Remove border */
            }

            .modal-body {
                text-align: center;
            }

            /* Preserve table header styling for specific tables */
            #requestTable th {
                background-color: #007bff;
                /* Blue background */
                color: #ffffff;
                /* White text */
                text-align: center;
            }

            #recycleSection table th {
                background-color: #007bff;
                /* Blue background */
                color: #ffffff;
                /* White text */
                text-align: center;
            }

            /* Remove extra blue bar in the modal header */
            .modal-header {
                background-color: transparent;
                /* Set transparent background */
                border-bottom: none;
                /* Remove bottom border */
            }
        </style>
        <!-- Recycle Bin Section -->
        <div id="recycleSection" class="section">
            <div class="card">
                <div class="card-header">
                    <h3>Released Documents</h3>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="mb-3">
                        <form method="GET" id="recycleSearchForm">
                            <input type="text" name="recycleSearch" class="form-control"
                                placeholder="Search by Transaction Code, Student Number, or Name"
                                value="<?php echo isset($_GET['recycleSearch']) ? htmlspecialchars($_GET['recycleSearch']) : ''; ?>"
                                id="recycleSearchInput">
                        </form>
                    </div>

                    <table class="table table-bordered mt-3" id="recycleTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Transaction Code</th>
                                <th>Student Number</th>
                                <th>Name</th>
                                <th>Program</th>
                                <th>Deleted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Get the search query if it exists
                            $recycleSearchQuery = isset($_GET['recycleSearch']) ? $_GET['recycleSearch'] : '';

                            // SQL query to fetch filtered results
                            $recycle_sql = "SELECT * FROM requests WHERE 
                                    (transaction_code LIKE '%$recycleSearchQuery%' 
                                    OR studentNumber LIKE '%$recycleSearchQuery%' 
                                    OR CONCAT(firstName, ' ', middleName, ' ', lastName) LIKE '%$recycleSearchQuery%') 
                                    AND deleted_at IS NOT NULL";
                            $recycle_result = $conn->query($recycle_sql);

                            if ($recycle_result->num_rows > 0) {
                                while ($row = $recycle_result->fetch_assoc()) {
                                    $highlightedTransactionCode = preg_replace("/($recycleSearchQuery)/i", "<span class='highlight'>$1</span>", $row['transaction_code']);
                                    $highlightedStudentNumber = preg_replace("/($recycleSearchQuery)/i", "<span class='highlight'>$1</span>", $row['studentNumber']);
                                    $highlightedName = preg_replace("/($recycleSearchQuery)/i", "<span class='highlight'>$1</span>", $row['firstName'] . ' ' . $row['middleName'] . ' ' . $row['lastName']);

                                    echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$highlightedTransactionCode}</td>
                                    <td>{$highlightedStudentNumber}</td>
                                    <td>{$highlightedName}</td>
                                    <td>{$row['program']}</td>
                                    <td>{$row['deleted_at']}</td>
                                    <td>
                                        <button class='btn btn-primary btn-sm' onclick=\"loadRecycleDetails('{$row['id']}')\">View Request</button>
                                        <a href='#' class='btn btn-success btn-sm' onclick='confirmRestore({$row['id']})'>Restore</a>
                                    </td>
                                </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No items in the Recycle Bin</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <script>
            function attachRecycleSearchListeners() {
                const searchInput = document.getElementById('recycleSearchInput');
                if (!searchInput) return;

                searchInput.addEventListener('input', function () {
                    const searchQuery = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#recycleTable tbody tr');

                    rows.forEach(row => {
                        const transactionCode = row.cells[1]?.textContent.toLowerCase() || '';
                        const studentNumber = row.cells[2]?.textContent.toLowerCase() || '';
                        const name = row.cells[3]?.textContent.toLowerCase() || '';

                        if (
                            transactionCode.includes(searchQuery) ||
                            studentNumber.includes(searchQuery) ||
                            name.includes(searchQuery)
                        ) {
                            row.style.display = '';
                            row.innerHTML = row.innerHTML.replace(/(<span class='highlight'>)(.*?)(<\/span>)/g, '$2'); // Remove old highlights
                            row.cells[1].innerHTML = row.cells[1].textContent.replace(new RegExp(searchQuery, 'gi'), "<span class='highlight'>$&</span>");
                            row.cells[2].innerHTML = row.cells[2].textContent.replace(new RegExp(searchQuery, 'gi'), "<span class='highlight'>$&</span>");
                            row.cells[3].innerHTML = row.cells[3].textContent.replace(new RegExp(searchQuery, 'gi'), "<span class='highlight'>$&</span>");
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });

                document.getElementById('recycleSearchForm')?.addEventListener('submit', function (event) {
                    event.preventDefault(); // Prevent form submission
                });

                searchInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault(); // Disable Enter key
                    }
                });
            }

            // Attach listeners on page load
            attachRecycleSearchListeners();
        </script>
        <style>
            .highlight {
                background-color: yellow;
                font-weight: bold;
            }
        </style>
        <Style>
            /* Custom Settings Section Styles */
            #settingsSection {
                background: linear-gradient(135deg, #1d2671, #c33764);
                /* Gradient background */
                border-radius: 15px;
                padding: 40px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
                /* Shadow for depth */
                color: #fff;
                /* Text color */
                margin-top: 20px;
                text-align: center;
            }

            #settingsSection h1 {
                font-size: 2rem;
                margin-bottom: 20px;
                color: white;
            }

            .btn-group .btn {
                font-size: 1.2rem;
                /* Larger text */
                padding: 15px 30px;
                /* Larger button size */
                border-radius: 50px;
                /* Fully rounded buttons */
                transition: all 0.3s ease;
                /* Smooth hover transition */
                margin-top: 20px;
            }

            .btn-warning {
                background-color: #ffcc00;
                border: none;
                color: #1d2671;
            }

            .btn-secondary {
                background-color: #555;
                border: none;
            }

            .btn:hover {
                opacity: 0.9;
            }
        </Style>


        <!-- Maintenance Mode Settings -->
        <div id="settingsSection" class="section p-3 text-center">
            <h1>Maintenance Mode Settings</h1>
            <div class="btn-group" role="group" aria-label="Maintenance Mode">
                <button id="maintenanceToggleOn" class="btn btn-warning btn-lg rounded-pill m-2">
                    <i class="fas fa-wrench"></i> Activate Maintenance Mode
                </button>
                <button id="maintenanceToggleOff" class="btn btn-secondary btn-lg rounded-pill m-2">
                    <i class="fas fa-power-off"></i> Deactivate Maintenance Mode
                </button>
            </div>
        </div>
        <!-- Modal Structure -->
        <div id="priceUpdateModal" class="unique-modal">
            <div class="unique-modal-content">
                <span class="unique-modal-close">&times;</span>
                <div class="unique-modal-header">
                    Update Successful!
                </div>
                <div class="unique-modal-body">
                    <p>The document prices have been successfully updated.</p>
                </div>
                <div class="unique-modal-footer">
                    <button class="btn" id="closeModalButton">Close</button>
                </div>
            </div>
        </div>
        <!-- Edit Document Prices Section -->
        <div id="editPricesSection" class="section price-editor-section" style="display: none;">
            <div class="price-editor-card">
                <div class="price-editor-header">
                    <h3>Edit Document Prices</h3>
                </div>
                <div class="price-editor-body">
                    <form action="updatePrices.php" method="POST" id="priceUpdateForm">
                        <div class="category-header">
                            <p class="categorize-label">Transcript of Records (ToR)</p>
                        </div>
                        <div class="price-editor-grid">
                            <!-- For Enrollment -->
                            <div class="price-editor-group">
                                <label for="forEnrollment">For Enrollment:</label>
                                <input type="number" id="forEnrollment" name="prices[forEnrollment]"
                                    placeholder="Enter new price">
                            </div>

                            <!-- For Board Exam -->
                            <div class="price-editor-group">
                                <label for="forBoardExam">For Board Exam:</label>
                                <input type="number" id="forBoardExam" name="prices[forBoardExam]"
                                    placeholder="Enter new price">
                            </div>

                            <!-- For Reference -->
                            <div class="price-editor-group">
                                <label for="forReference">For Reference:</label>
                                <input type="number" id="forReference" name="prices[forReference]"
                                    placeholder="Enter new price">
                            </div>

                            <!-- For Studies Abroad -->
                            <div class="price-editor-group">
                                <label for="forStudiesAbroad">For Studies Abroad:</label>
                                <input type="number" id="forStudiesAbroad" name="prices[forStudiesAbroad]"
                                    placeholder="Enter new price">
                            </div>

                            <!-- For Transfer -->
                            <div class="price-editor-group">
                                <label for="forTransfer">For Transfer:</label>
                                <input type="number" id="forTransfer" name="prices[forTransfer]"
                                    placeholder="Enter new price">
                            </div>

                            <!-- For Employment -->
                            <div class="price-editor-group">
                                <label for="forEmployment">For Employment:</label>
                                <input type="number" id="forEmployment" name="prices[forEmployment]"
                                    placeholder="Enter new price">
                            </div>

                            <!-- For Ranking -->
                            <div class="price-editor-group">
                                <label for="forRanking">For Ranking:</label>
                                <input type="number" id="forRanking" name="prices[forRanking]"
                                    placeholder="Enter new price">
                            </div>
                        </div>

                        <div class="category-header">
                            <p class="categorize-label">Re-issuance of Diploma</p>
                        </div>
                        <!-- For Reissuance -->
                        <div class="price-editor-group">
                            <label for="forReissuance">For Reissuance:</label>
                            <input type="number" id="forReissuance" name="prices[forReissuance]"
                                placeholder="Enter new price" class="short-input">
                        </div>

                        <div class="category-header">
                            <p class="categorize-label">Certificate</p>
                        </div>
                        <div class="price-editor-grid">
                            <!-- For Good Moral -->
                            <div class="price-editor-group">
                                <label for="forGoodmoral">For Good Moral:</label>
                                <input type="number" id="forGoodmoral" name="prices[forGoodmoral]"
                                    placeholder="Enter new price">
                            </div>

                            <!-- For GPA -->
                            <div class="price-editor-group">
                                <label for="forGpa">For GPA:</label>
                                <input type="number" id="forGpa" name="prices[forGpa]" placeholder="Enter new price">
                            </div>

                            <!-- For Earned Units -->
                            <div class="price-editor-group">
                                <label for="forEarnedUnits">For Earned Units:</label>
                                <input type="number" id="forEarnedUnits" name="prices[forEarnedUnits]"
                                    placeholder="Enter new price">
                            </div>

                            <!-- For Graduating -->
                            <div class="price-editor-group">
                                <label for="forGraduating">For Graduating:</label>
                                <input type="number" id="forGraduating" name="prices[forGraduating]"
                                    placeholder="Enter new price">
                            </div>

                            <!-- For Graduation -->
                            <div class="price-editor-group">
                                <label for="forGraduation">For Graduation:</label>
                                <input type="number" id="forGraduation" name="prices[forGraduation]"
                                    placeholder="Enter new price">
                            </div>

                            <!-- For Special Certification -->
                            <div class="price-editor-group">
                                <label for="forSpec">For Special Certification:</label>
                                <input type="number" id="forSpec" name="prices[forSpec]" placeholder="Enter new price">
                            </div>

                            <!-- For Medium of Instruction -->
                            <div class="price-editor-group">
                                <label for="formediumInstruction">For Medium of Instruction:</label>
                                <input type="number" id="formediumInstruction" name="prices[formediumInstruction]"
                                    placeholder="Enter new price">
                            </div>

                            <!-- For Enrollment Certification -->
                            <div class="price-editor-group">
                                <label for="forEnrollmentcert">For Enrollment Certification:</label>
                                <input type="number" id="forEnrollmentcert" name="prices[forEnrollmentcert]"
                                    placeholder="Enter new price">
                            </div>
                        </div>

                        <div class="category-header">
                            <p class="categorize-label">CAV</p>
                        </div>
                        <!-- For Certification -->
                        <div class="price-editor-group">
                            <label for="forCertification">Certification, Authentication and Verification (CAV):</label>
                            <input type="number" id="forCertification" name="prices[forCertification]"
                                placeholder="Enter new price" class="short-input">
                        </div>

                        <div class="category-header">
                            <p class="categorize-label"></p>
                        </div>
                        <div class="price-editor-group">
                            <label for="forAuthentication">Authentication</label>
                            <input type="number" id="forAuthentication" name="prices[forAuthentication]"
                                placeholder="Enter new price" class="short-input">
                        </div>
                        <div class="category-header">
                            <p class="categorize-label"></p>
                        </div>
                        <!-- For Dismissal -->
                        <div class="price-editor-group">
                            <label for="forDismissal">Honorable Dismissal:</label>
                            <input type="number" id="forDismissal" name="prices[forDismissal]"
                                placeholder="Enter new price" class="short-input">
                        </div>

                        <div class="category-header">
                            <p class="categorize-label"></p>
                        </div>
                        <!-- Others -->
                        <div class="price-editor-group">
                            <label for="others">Others:</label>
                            <input type="number" id="others" name="prices[others]" placeholder="Enter new price"
                                class="short-input">
                        </div>

                        <div class="price-editor-actions">
                            <button type="submit" class="price-editor-btn">Update Prices</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <style>
            .category-header p {
                font-size: 18px;
                font-weight: 700;
                color: #4CAF50;
                border-bottom: 2px solid #4CAF50;
                margin: 20px 0 10px;
                padding-bottom: 5px;
            }

            /* Style to reduce the width of specific input fields */
            .short-input {
                width: 200px;
                /* Set the width to 200px */
            }

            .price-editor-group input.short-input:focus {
                box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
                /* Keeps consistent hover focus style */
                outline: none;
                background-color: #ffffff;
            }

            /* Styling for Categorize Labels */
            .categorize-label {
                font-weight: bold;
                font-size: 16px;
                color: #333;
                background-color: #f4f4f4;
                /* Light gray background for visibility */
                border: 2px solid #4caf50;
                /* Green border */
                border-radius: 8px;
                /* Rounded corners */
                padding: 10px 15px;
                /* Spacing inside the border */
                margin: 15px 0;
                /* Spacing above and below */
                text-align: left;
                /* Center the text */
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
                /* Slight shadow for a clean look */
                width: 30%;
            }
        </style>

        <style>
            /* Enhanced Modal Styles */
            .unique-modal {
                display: none;
                /* Hidden by default */
                position: fixed;
                z-index: 1000;
                /* Ensure it appears above other elements */
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                /* Enable scroll if needed */
                background-color: rgba(0, 0, 0, 0.7);
                /* Black background with opacity */
                animation: fadeIn 0.5s;
                /* Smooth fade-in animation */
            }

            /* Keyframe for fade-in effect */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .unique-modal-content {
                background-color: #ffffff;
                /* White background */
                margin: 10% auto;
                /* Center the modal vertically */
                padding: 30px;
                border-radius: 10px;
                /* Rounded corners */
                width: 50%;
                /* Set a width */
                max-width: 600px;
                /* Responsive maximum width */
                text-align: center;
                /* Center-align content */
                box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
                /* Drop shadow */
                transform: translateY(-20px);
                /* Initial position for animation */
                animation: slideDown 0.5s ease-in-out;
                /* Smooth slide-down animation */
            }

            /* Keyframe for slide-down effect */
            @keyframes slideDown {
                from {
                    transform: translateY(-50px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .unique-modal-close {
                color: #aaa;
                font-size: 24px;
                font-weight: bold;
                cursor: pointer;
                position: absolute;
                /* Position close button at the top-right */
                top: 15px;
                right: 20px;
                transition: color 0.3s ease;
                /* Smooth hover transition */
            }

            .unique-modal-close:hover,
            .unique-modal-close:focus {
                color: #ff3b3b;
                /* Highlighted red color on hover */
                text-decoration: none;
                cursor: pointer;
            }

            .unique-modal-header {
                font-size: 24px;
                font-weight: bold;
                color: #333;
                /* Dark gray text */
                margin-bottom: 20px;
            }

            .unique-modal-body {
                font-size: 16px;
                color: #555;
                /* Lighter gray for body text */
                margin-bottom: 20px;
            }

            .unique-modal-footer {
                margin-top: 20px;
            }

            .unique-modal-footer .btn {
                display: inline-block;
                background-color: #4CAF50;
                /* Green button */
                color: white;
                padding: 10px 20px;
                text-align: center;
                font-size: 14px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s ease;
                /* Smooth hover transition */
            }

            .unique-modal-footer .btn:hover {
                background-color: #45a049;
                /* Darker green on hover */
            }

            .price-editor-section {
                font-family: 'Roboto', Arial, sans-serif;
                color: #333;
                background-color: #f8f9fa;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                max-width: 1200px;
                margin: 0 auto;
            }

            .price-editor-card {
                background: #ffffff;
                border: 1px solid #e0e0e0;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s, box-shadow 0.3s;
            }

            .price-editor-card:hover {
                transform: translateY(-5px);
                box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.15);
            }

            .price-editor-header {
                background: linear-gradient(45deg, #4caf50, #388e3c);
                color: #fff;
                padding: 15px;
                text-align: center;
                font-size: 20px;
                font-weight: 700;
                border-bottom: 1px solid #ddd;
            }

            .price-editor-body {
                padding: 20px;
            }

            .price-editor-grid {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                gap: 15px;
                margin-top: 10px;
            }

            .price-editor-group {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .price-editor-group label {
                font-weight: 600;
                font-size: 14px;
                color: #555;
            }

            .price-editor-group input {
                padding: 8px 10px;
                font-size: 14px;
                border: 1px solid #ccc;
                border-radius: 6px;
                height: 34px;
                background-color: #f9f9f9;
                transition: border-color 0.3s, box-shadow 0.3s;
            }

            .price-editor-group input:focus {
                border-color: #4caf50;
                box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
                outline: none;
                background-color: #ffffff;
            }

            .price-editor-actions {
                margin-top: 20px;
                text-align: center;
            }

            .price-editor-btn {
                background: linear-gradient(45deg, #4caf50, #66bb6a);
                color: white;
                border: none;
                padding: 10px 25px;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
                transition: background 0.3s, box-shadow 0.3s;
            }

            .price-editor-btn:hover {
                background: linear-gradient(45deg, #388e3c, #4caf50);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .price-editor-btn:active {
                transform: scale(0.98);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            }
        </style>
        <script>
            // Modal elements
            const modal = document.getElementById('priceUpdateModal');
            const closeModal = document.querySelector('.unique-modal-close');
            const closeFooterButton = document.getElementById('closeModalButton');
            const form = document.getElementById('priceUpdateForm');

            // Handle form submission with AJAX
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent normal form submission

                // Create a FormData object to capture form data
                const formData = new FormData(form);

                // Submit the form via AJAX
                fetch('updatePrices.php', {
                    method: 'POST',
                    body: formData,
                })
                    .then(response => response.text()) // Parse response as text
                    .then(data => {
                        console.log(data); // For debugging (can be removed)
                        if (data.includes('Prices updated successfully')) {
                            // Show the modal if the response indicates success
                            modal.style.display = 'block';
                        } else {
                            alert('Error updating prices: ' + data);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating prices.');
                    });
            });

            // Close the modal when the close button or footer button is clicked
            [closeModal, closeFooterButton].forEach(button => {
                button.addEventListener('click', function () {
                    modal.style.display = 'none';
                });
            });

            // Close the modal when clicking outside of it
            window.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        </script>
        <script>
            // Get references to the buttons and the form section
            const maintenanceToggleOn = document.getElementById('maintenanceToggleOn');
            const maintenanceToggleOff = document.getElementById('maintenanceToggleOff');
            const editPricesSection = document.getElementById('editPricesSection');

            // Function to update the display based on localStorage
            function updateDisplayBasedOnState() {
                const isMaintenanceModeOn = localStorage.getItem('maintenanceMode') === 'true';
                if (isMaintenanceModeOn) {
                    editPricesSection.style.display = 'block'; // Show the form
                } else {
                    editPricesSection.style.display = 'none'; // Hide the form
                }
            }

            // Show form when Activate Maintenance Mode is clicked
            maintenanceToggleOn.addEventListener('click', () => {
                localStorage.setItem('maintenanceMode', 'true'); // Save state
                updateDisplayBasedOnState(); // Update the display
            });

            // Hide form when Deactivate Maintenance Mode is clicked
            maintenanceToggleOff.addEventListener('click', () => {
                localStorage.setItem('maintenanceMode', 'false'); // Save state
                updateDisplayBasedOnState(); // Update the display
            });

            // Initialize the display when the page loads
            document.addEventListener('DOMContentLoaded', updateDisplayBasedOnState);
        </script>
        <script>
            document.getElementById('maintenanceToggleOn').addEventListener('click', function () {
                // Set maintenance mode flag in localStorage
                localStorage.setItem('maintenanceMode', 'on');

                // Show a SweetAlert popup for activation
                Swal.fire({
                    title: 'Maintenance Mode Activated',
                    text: 'The system is now in maintenance mode.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            });

            document.getElementById('maintenanceToggleOff').addEventListener('click', function () {
                // Remove maintenance mode flag from localStorage
                localStorage.removeItem('maintenanceMode');

                // Show a SweetAlert popup for deactivation
                Swal.fire({
                    title: 'Maintenance Mode Deactivated',
                    text: 'The system is now out of maintenance mode.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            });
        </script>
        <script>
            function confirmLogout() {
                Swal.fire({
                    title: 'Are you sure you want to log out?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'logout.php';
                    }
                });
            }

            function confirmRestore(id) {
                Swal.fire({
                    title: 'Are you sure you want to restore this request?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, restore it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'restore_request.php?id=' + id + '#recycleSection';
                    }
                });
            }

            window.onload = function () {
                // Ensure the page stays on Recycle Bin section if the hash is #recycleSection
                if (window.location.hash === '#recycleSection') {
                    showSection('recycleSection');
                }
            }
        </script>
        <!-- Expired Section -->
        <div id="expiredSection" class="section mt-4">
            <div class="card">
                <div class="card-header">
                    <h2>Expired Requests</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Transaction Code</th>
                                <th>Student Number</th>
                                <th>Name</th>
                                <th>Program</th>
                                <th>Expiration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch expired requests
                            $expired_sql = "SELECT * FROM requests WHERE expiration_date < NOW() AND status != 'Expired' AND deleted_at IS NULL ORDER BY expiration_date ASC";
                            $expired_result = $conn->query($expired_sql);

                            if ($expired_result && $expired_result->num_rows > 0) {
                                while ($row = $expired_result->fetch_assoc()) {
                                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['transaction_code']}</td>
                            <td>{$row['studentNumber']}</td>
                            <td>{$row['firstName']} {$row['middleName']} {$row['lastName']}</td>
                            <td>{$row['program']}</td>
                            <td>{$row['expiration_date']}</td>
                            <td>
                                <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>
                                    Delete
                                </button>
                            </td>
                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No expired requests found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Include SweetAlert2 CDN -->
    

        <script>
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault(); // Prevent the default link behavior

                    const id = this.getAttribute('data-id'); // Get the ID from data attribute

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to delete this expired request?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show success message after confirmation
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'The expired request has been deleted.',
                                showConfirmButton: true // Show the "OK" button to dismiss
                            }).then(() => {
                                // After the success alert is dismissed, redirect to delete request PHP script
                                window.location.href = 'delete_expired_request.php?id=' + id;
                            });
                        }
                    });
                });
            });

            // Example of a function to refresh the request list after expiration
            function checkAndRefreshExpiredRequests() {
                fetch('check_expired_requests.php')  // Server-side script to check expired requests
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadRequestListSection();  // Reload the request list after expiration
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Call this function every 1 second
            setInterval(checkAndRefreshExpiredRequests, 1000);  // 1 second in milliseconds

        </script>



    </div>
</body>

</html>

<?php
$conn->close();
?>