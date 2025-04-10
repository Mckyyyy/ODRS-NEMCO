<?php
// Assuming this file is named restore_request.php

// Database connection details
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

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Restore the request by setting deleted_at to NULL and updating the status
    $update_sql = "UPDATE requests SET deleted_at = NULL, status = 'Pending' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the Recycle Bin or show a success message
    header("Location: admin_dashboard.php#recycleSection"); // Redirect to the Recycle Bin
    exit();
}

// Close the database connection
$conn->close();
