<?php
// process_released_date.php
include 'db_connection.php'; // Ensure you have your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $release_date = $_POST['release_date'];

    // Update the release_date in the database
    $stmt = $conn->prepare("UPDATE requests SET release_date = ? WHERE id = ?");
    $stmt->bind_param('si', $release_date, $id);

    if ($stmt->execute()) {
        // Redirect back to the request list with a success message
        header('Location: admin_dashboard.php#requestListSection');
        exit();
    } else {
        // Handle error
        echo "Error updating release date: " . $stmt->error;
    }
}
?>
