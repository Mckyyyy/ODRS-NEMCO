<?php
include 'db_connection.php';
header('Content-Type: application/json');

// Function to send JSON responses with HTTP status codes
function send_response($success, $message, $code = 200) {
    http_response_code($code);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit();
}

// Check database connection
if (!$conn) {
    send_response(false, 'Failed to connect to the database.', 500);
}

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, 'Expected POST request method.', 405);
}

// Validate input data
$id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_VALIDATE_INT) : null;
$expiration_date_input = $_POST['expiration_date'] ?? null;

if (!$id || !$expiration_date_input) {
    send_response(false, 'Invalid input data. ID and expiration date are required.', 400);
}

// Validate the expiration date format and check if it's not in the past
$expiration_date = DateTime::createFromFormat('Y-m-d', $expiration_date_input);
$current_date = new DateTime();
$current_date->setTime(0, 0, 0); // Reset time part for date comparison

if (!$expiration_date) {
    send_response(false, 'Invalid expiration date format. Expected format: YYYY-MM-DD.', 400);
}

// Remove time part for consistent comparison
$expiration_date->setTime(0, 0, 0);

if ($expiration_date < $current_date) {
    send_response(false, 'Expiration date cannot be in the past.', 400);
}

// Format the expiration date for the database
$formatted_expiration_date = $expiration_date->format('Y-m-d');

// First check if the request exists
$check_sql = "SELECT id FROM requests WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
if (!$check_stmt) {
    error_log("SQL prepare error: {$conn->error}");
    send_response(false, 'Failed to prepare check statement.', 500);
}

$check_stmt->bind_param('i', $id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    $check_stmt->close();
    send_response(false, 'Request not found.', 404);
}
$check_stmt->close();

// Prepare and execute the update query
$sql = "UPDATE requests SET expiration_date = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("SQL prepare error: {$conn->error}");
    send_response(false, 'Failed to prepare SQL statement.', 500);
}

$stmt->bind_param('si', $formatted_expiration_date, $id);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_response(true, 'Expiration date updated successfully.');
    } else {
        send_response(false, 'No changes were made to the expiration date.', 400);
    }
} else {
    error_log("SQL execution error: {$stmt->error}");
    send_response(false, 'Failed to update expiration date. Please try again later.', 500);
}

// Close the statement and database connection
$stmt->close();
$conn->close();
