<?php
// delete_request.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "records_db1";

$id = $_GET['id']; // Get the ID from the query string

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Soft delete the record by setting the deleted_at column to the current timestamp
$sql = "UPDATE requests SET deleted_at = NOW() WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php#recycleSection"); // Redirect to the dashboard after soft deletion
} else {
    echo "Error updating record: " . $conn->error;
}

$stmt->close();
$conn->close();

?>
<script>
    // JavaScript function to redirect to the Request List section
    function confirmDeletion() {
        window.location.href = "admin_dashboard.php#recycleSection";
    }
</script>
