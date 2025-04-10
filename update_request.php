<?php
// update_request.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "records_db1";

// Get data from form
$id = $_POST['id'];
$studentNumber = $_POST['studentNumber'];
$firstName = $_POST['firstName'];
$payment = $_POST['payment']; // Get payment from the form

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "UPDATE requests SET studentNumber=?, firstName=?, payment=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdi", $studentNumber, $firstName, $payment, $id);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php");
} else {
    echo "Error updating record: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
