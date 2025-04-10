<?php
// fetch_request.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "records_db1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$transaction_code = $_GET['transaction_code'] ?? '';

if (!$transaction_code) {
    echo json_encode(['success' => false, 'message' => 'Transaction code is required']);
    exit;
}

// Fetch the requested slip details from the database
$sql = "SELECT copiesData FROM requests WHERE transaction_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $transaction_code);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();

if ($request) {
    // Decode the JSON stored in the `copiesData` column
    echo json_encode([
        'success' => true,
        'copiesData' => json_decode($request['copiesData'], true),
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No record found']);
}

$stmt->close();
$conn->close();
?>
