<?php
// delete_user.php

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

// Check if user ID is set and is a valid integer
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $userId = $_GET['id'];

    // Prepare and execute the deletion query
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param('i', $userId);

    if ($stmt->execute()) {
        // Redirect to admin dashboard after successful deletion
        header("Location: admin_dashboard.php?message=User deleted successfully");
    } else {
        // Redirect to admin dashboard with error message
        header("Location: admin_dashboard.php?message=Error deleting user");
    }

    $stmt->close();
} else {
    // Redirect to admin dashboard with error message if ID is invalid
    header("Location: admin_dashboard.php?message=Invalid user ID");
}

$conn->close();
?>
