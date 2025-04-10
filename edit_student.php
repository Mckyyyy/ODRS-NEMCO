<?php
// edit_student.php

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

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch student data
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if (!$student) {
        echo "Student not found.";
        exit();
    }
} else {
    echo "No student ID provided.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studentNumber = $_POST['studentNumber'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $program = $_POST['program'];
    $contactNumber = $_POST['contactNumber'];
    $lastTermAttended = $_POST['lastTermAttended'];

    $update_sql = "UPDATE students SET studentNumber = ?, firstName = ?, lastName = ?, program = ?, contactNumber = ?, lastTermAttended = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('ssssssi', $studentNumber, $firstName, $lastName, $program, $contactNumber, $lastTermAttended, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Student</h2>
        <form method="POST" action="edit_student.php?id=<?php echo $id; ?>">
            <div class="form-group">
                <label for="studentNumber">Student Number</label>
                <input type="text" class="form-control" id="studentNumber" name="studentNumber" value="<?php echo htmlspecialchars($student['studentNumber']); ?>" required>
            </div>
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($student['firstName']); ?>" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($student['lastName']); ?>" required>
            </div>
            <div class="form-group">
                <label for="program">Program</label>
                <input type="text" class="form-control" id="program" name="program" value="<?php echo htmlspecialchars($student['program']); ?>" required>
            </div>
            <div class="form-group">
                <label for="contactNumber">Contact Number</label>
                <input type="text" class="form-control" id="contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($student['contactNumber']); ?>">
            </div>
            <div class="form-group">
                <label for="lastTermAttended">Last Term Attended</label>
                <input type="text" class="form-control" id="lastTermAttended" name="lastTermAttended" value="<?php echo htmlspecialchars($student['lastTermAttended']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update Student</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
