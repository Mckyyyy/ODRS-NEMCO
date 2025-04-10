<?php
// Database connection
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

// Assuming that payment updates are stored in a 'payments' or 'requests' table with transaction code
// You will need to adjust the query based on how payments are tracked in your database.

if (isset($_GET['transaction_code'])) {
    $transactionCode = $_GET['transaction_code'];

    // Query to fetch the updated total price for the given transaction code
    $sql = "SELECT SUM(price) AS total_price FROM payments WHERE transaction_code = '$transactionCode'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newTotalPrice = $row['total_price'];

        // Return the new total price as JSON
        echo json_encode(['success' => true, 'newTotalPrice' => $newTotalPrice]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No payments found for this transaction code']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Transaction code not provided']);
}

$conn->close();
?>
