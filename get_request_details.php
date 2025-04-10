<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "records_db1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed']);
    exit();
}

if (isset($_GET['transaction_code'])) {
    $transactionCode = $_GET['transaction_code'];

    // Prepare and execute the query to fetch the request details
    $stmt = $conn->prepare("SELECT * FROM requests WHERE transaction_code = ?");
    $stmt->bind_param("s", $transactionCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        // Respond with the data from the database, including the 'others' field
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'No data found for the given transaction code']);
    }
} else {
    echo json_encode(['error' => 'No transaction code provided']);
}

$conn->close();
?>
