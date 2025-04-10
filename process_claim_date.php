<?php
include 'db_connection.php';
// Add CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

$response = [];

// Function to send a JSON response
function send_response($success, $message, $code = 200, $data = null) {
    http_response_code($code);
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit();
}

// Check database connection
if (!$conn) {
    send_response(false, 'Failed to connect to the database.', 500);
}

// Ensure the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, 'Expected POST request method.', 405);
}

// Validate and sanitize inputs
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? filter_var($input['id'], FILTER_VALIDATE_INT) : 
    (isset($_POST['id']) ? filter_var($_POST['id'], FILTER_VALIDATE_INT) : null);
$claim_date_input = $input['claim_date'] ?? $_POST['claim_date'] ?? null;

if (!$id || !$claim_date_input) {
    send_response(false, 'Invalid input data. ID and claim date are required.', 400);
}

// Validate the claim date format
$claim_date = DateTime::createFromFormat('Y-m-d', $claim_date_input);
if (!$claim_date) {
    send_response(false, 'Invalid claim date format. Expected format: YYYY-MM-DD.', 400);
}

// Prepare and execute the SQL statement
$formatted_claim_date = $claim_date->format('Y-m-d');
$stmt = $conn->prepare("UPDATE requests SET claim_date = ? WHERE id = ?");
if (!$stmt) {
    error_log("SQL prepare error: {$conn->error}");
    send_response(false, 'Failed to prepare the SQL statement.', 500);
}

$stmt->bind_param('si', $formatted_claim_date, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(true, 'Claim date saved successfully.', 200, ['id' => $id, 'claim_date' => $formatted_claim_date]);
    } else {
        send_response(false, 'No rows updated. The ID might not exist.', 404);
    }
} else {
    error_log("SQL execution error: {$stmt->error}");
    send_response(false, 'Failed to save claim date. Please try again.', 500);
}

// Close statement and connection
$stmt->close();
$conn->close();
