<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "records_db1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to check for requests with 'Pending' status
$query = "SELECT COUNT(*) AS new_requests FROM requests WHERE status = 'Pending'";
$result = $conn->query($query);

if ($result && $row = $result->fetch_assoc()) {
    echo json_encode(['new_requests' => $row['new_requests']]);
} else {
    echo json_encode(['new_requests' => 0]);
}

$conn->close();
?>
