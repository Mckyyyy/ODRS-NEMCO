<?php
// update_payment.php

include 'db_connection.php'; // Your DB connection script

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $payment = $_POST['payment'];

    // Validate payment
    if (!is_numeric($payment) || $payment <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid payment amount']);
        exit();
    }

    // Fetch current payment from database to check if it has changed
    $stmt = $conn->prepare("SELECT payment FROM requests WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentPayment = $result->fetch_assoc()['payment'];

    // Check if the payment is different from the current value in the database
    $paymentUpdated = ($payment != $currentPayment);

    // Update payment in the database if it has changed
    if ($paymentUpdated) {
        $stmt = $conn->prepare("UPDATE requests SET payment = ? WHERE id = ?");
        $stmt->bind_param('di', $payment, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'paymentUpdated' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update payment']);
        }
    } else {
        echo json_encode(['success' => true, 'paymentUpdated' => false]);
    }

    $stmt->close();
}
?>
