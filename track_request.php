<?php
// track_request.php

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

$request = null;
if (isset($_POST['transaction_code'])) {
    $transaction_code = $_POST['transaction_code'];
    
    $sql = "SELECT * FROM requests WHERE transaction_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $transaction_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Request</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Track Your Request</h1>
        <form method="POST" action="track_request.php">
            <div class="form-group">
                <label for="transaction_code">Transaction Code</label>
                <input type="text" id="transaction_code" name="transaction_code" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Track</button>
        </form>
        
        <?php if ($request): ?>
        <div class="mt-4">
            <h2>Request Details</h2>
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td><?php echo htmlspecialchars($request['id']); ?></td>
                </tr>
                <tr>
                    <th>Transaction Code</th>
                    <td><?php echo htmlspecialchars($request['transaction_code']); ?></td>
                </tr>
                <tr>
                    <th>Student Number</th>
                    <td><?php echo htmlspecialchars($request['studentNumber']); ?></td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td><?php echo htmlspecialchars($request['firstName'] . ' ' . $request['middleName'] . ' ' . $request['lastName']); ?></td>
                </tr>
                <tr>
                    <th>Program</th>
                    <td><?php echo htmlspecialchars($request['program']); ?></td>
                </tr>
                <tr>
                    <th>Contact Number</th>
                    <td><?php echo htmlspecialchars($request['contactNumber']); ?></td>
                </tr>
                <tr>
                    <th>Last Term Attended</th>
                    <td><?php echo htmlspecialchars($request['lastTermAttended']); ?></td>
                </tr>
                <tr>
                    <th>Payment</th>
                    <td>$<?php echo htmlspecialchars($request['payment']); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?php echo htmlspecialchars($request['status']); ?></td>
                </tr>
            </table>
        </div>
        <?php elseif (isset($_POST['transaction_code'])): ?>
        <div class="alert alert-danger mt-4">No request found with that transaction code.</div>
        <?php endif; ?>
    </div>
</body>
</html>
