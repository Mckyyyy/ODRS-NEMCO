<?php
include 'db_connection.php';
header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Move to Recycle Bin by setting `deleted_at` timestamp
    $stmt = $conn->prepare("UPDATE requests SET deleted_at = NOW() WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Request moved to Recycle Bin successfully.';
    } else {
        $response['success'] = false;
        $response['error'] = 'Failed to move to Recycle Bin.';
    }

    $stmt->close();
} else {
    $response['success'] = false;
    $response['error'] = 'Invalid request method.';
}

echo json_encode($response);
?>
