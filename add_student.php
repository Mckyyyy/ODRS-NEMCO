<?php
// add_student.php

session_start();
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit();
}

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

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studentNumber = $_POST['studentNumber'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $program = $_POST['program'];
    $contactNumber = $_POST['contactNumber'];
    $lastTermAttended = $_POST['lastTermAttended'];

    // Validate input
    if (!empty($studentNumber) && !empty($firstName) && !empty($lastName) && !empty($program)) {
        // Insert student data
        $insert_sql = "INSERT INTO students (studentNumber, firstName, lastName, program, contactNumber, lastTermAttended) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param('ssssss', $studentNumber, $firstName, $lastName, $program, $contactNumber, $lastTermAttended);

        if ($stmt->execute()) {
            $message = 'Student added successfully.';
            header("Location: admin_dashboard.php?message=" . urlencode($message));
            exit();
        } else {
            $message = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = 'Please fill in all required fields.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Student</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST" action="add_student.php">
            <div class="form-group">
                <label for="studentNumber">Student Number</label>
                <input type="text" class="form-control" id="studentNumber" name="studentNumber" required>
            </div>
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" required>
            </div>
            <div class="form-group">
                <label for="program">Program</label>
                <select class="form-control" id="program" name="program" required>
                    <option value="" disabled selected>Select a program</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Information Technology">Information Technology</option>
                    <option value="Software Engineering">Software Engineering</option>
                    <option value="Data Science">Data Science</option>
                    <!-- Add more programs as needed -->
                </select>
            </div>
            <div class="form-group">
                <label for="contactNumber">Contact Number</label>
                <input type="text" class="form-control" id="contactNumber" name="contactNumber">
            </div>
            <div class="form-group">
                <label for="lastTermAttended">Last Term Attended</label>
                <input type="text" class="form-control" id="lastTermAttended" name="lastTermAttended">
            </div>
            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
