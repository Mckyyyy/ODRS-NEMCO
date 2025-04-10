<?php
// admin.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "records_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, studentNumber, firstName, middleName, lastName, program, contactNumber, lastTermAttended, payment, transaction_code FROM requests ORDER BY createdAt DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Student Information View</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Number</th>
                    <th>Name</th>
                    <th>Program</th>
                    <th>Contact Number</th>
                    <th>Last Term Attended</th>
                    <th>Transaction Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['studentNumber']}</td>
                                <td>{$row['firstName']} {$row['middleName']} {$row['lastName']}</td>
                                <td>{$row['program']}</td>
                                <td>{$row['contactNumber']}</td>
                                <td>{$row['lastTermAttended']}</td>
                                <td>{$row['transaction_code']}</td>
                                <td><button class='btn btn-info btn-sm' onclick='viewRequestDetails(\"{$row['transaction_code']}\")'>View</button></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No requests found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function viewRequestDetails(transactionCode) {
            $.ajax({
                url: 'get_request_details.php',
                method: 'GET',
                data: { transaction_code: transactionCode },
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        Swal.fire({
                            title: 'Error',
                            text: data.error,
                            icon: 'error',
                            confirmButtonText: 'Close'
                        });
                    } else {
                        Swal.fire({
                            title: 'Request Details',
                            html: `
                                <p><strong>Transaction Code:</strong> ${data.transaction_code}</p>
                                <p><strong>Status:</strong> ${data.status}</p>
                                <p><strong>Details:</strong> ${data.details}</p>
                            `,
                            icon: 'info',
                            confirmButtonText: 'Close'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'There was an error retrieving the request details.',
                        icon: 'error',
                        confirmButtonText: 'Close'
                    });
                }
            });
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
