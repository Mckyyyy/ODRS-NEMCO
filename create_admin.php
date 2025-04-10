<?php
// create_admin.php

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

// Check if admin already exists
$sql = "SELECT * FROM users WHERE username = 'admin'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Admin user does not exist, create it
    $admin_username = 'admin';
    $admin_password = 'admin123';
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $admin_username, $hashed_password);

    if ($stmt->execute()) {
        echo "Admin user created successfully!";
    } else {
        echo "Error creating admin user: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Admin user already exists.";
}

$conn->close();
?>
