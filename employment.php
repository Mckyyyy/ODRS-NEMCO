<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "records_db1";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify if student number is passed
if (!isset($_GET['studentNumber'])) {
    die("Student number not specified.");
}

$studentNumber = $_GET['studentNumber'];

// Prepare and execute the query to fetch student details
$sql = "SELECT * FROM requests WHERE studentNumber = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentNumber);
$stmt->execute();
$result = $stmt->get_result();

// Check if student data is available
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Retrieve and concatenate student details
    $firstName = $row['firstName'] ?? '';
    $middleName = isset($row['middleName']) ? $row['middleName'] . ' ' : '';
    $lastName = $row['lastName'] ?? '';
    $fullName = trim($firstName . ' ' . $middleName . $lastName);
    $program = $row['program'] ?? '';
    $schoolYear = $row['schoolYear'] ?? 'SY 2023-2024';
    $schoolName = "Northeastern Mindanao Colleges";
    $location = "Surigao City";
} else {
    echo "No student found.";
    exit;
}

$stmt->close();

// Helper function to format the day with the correct suffix
function formatDayWithSuffix($day) {
    if (!in_array(($day % 100), [11, 12, 13])) {
        switch ($day % 10) {
            case 1: return $day . 'st';
            case 2: return $day . 'nd';
            case 3: return $day . 'rd';
        }
    }
    return $day . 'th';
}

// Get the current date details
$currentDay = date('j');
$currentMonth = date('F');
$currentYear = date('Y');
$formattedDay = formatDayWithSuffix($currentDay);
?>

<script>
    // Redirect function to go back to the Request List section
    function goToRequestSection() {
        window.location.href = "admin_dashboard.php#requestListSection";
    }

    // Print the certificate content
    function printCertificate() {
        const printContents = document.getElementById('certificate').innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate for Employment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        .certificate {
            border: 2px solid #000;
            padding: 20px;
            width: 800px;
            margin: 0 auto;
            position: relative;
        }
        h1, h2, h3 {
            text-align: center;
            text-transform: uppercase;
            margin: 5px 0;
        }
        p {
            font-size: 18px;
            text-align: justify;
        }
        /* Logo styling */
        .logo-left, .logo-right {
            position: absolute;
            top: 0;
            width: 100px;
        }
        .logo-left {
            left: 10px;
        }
        .logo-right {
            right: 10px;
        }
        .header, .footer {
            text-align: center;
        }
        .footer {
            margin-top: 50px;
        }
        .back-button, .print-button {
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: #fff;
        }
        .back-button {
            background-color: #4CAF50;
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .print-button {
            background-color: darkblue;
            display: block;
            margin: 20px auto;
        }
        @media print {
            .back-button, .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="back-button" onclick="goToRequestSection()">Back</button>

    <div class="certificate" id="certificate">
        <img src="CHEDLOGO.png" class="logo-left" alt="CHED Logo">
        <img src="nemco.jpg" class="logo-right" alt="School Logo">

        <div class="header">
            <h3>Republic of the Philippines</h3>
            <h3>Commission on Higher Education</h3>
            <h3>Region XIII</h3>
            <h2>NORTHEASTERN MINDANAO COLLEGES</h2>
            <h3>Surigao City</h3>
        </div>

        <h1>Certificate for Employment</h1><br>

        <p>TO WHOM IT MAY CONCERN:</p>

        <p>This is to certify that <strong><?php echo $fullName; ?></strong>, a <strong><?php echo $program; ?></strong> student of Northeastern Mindanao Colleges for the academic year <strong><?php echo $schoolYear; ?></strong>, is a bona fide student of this institution.</p>

        <p>This certification is issued upon the request of <strong><?php echo $fullName; ?></strong> for employment purposes. During his/her time at Northeastern Mindanao Colleges, he/she has exhibited good moral character and has not been subjected to any disciplinary action.</p>

        <p>Issued this <strong><?php echo $formattedDay; ?></strong> day of <strong><?php echo $currentMonth; ?></strong>, <strong><?php echo $currentYear; ?></strong> at <strong><?php echo $schoolName; ?></strong>, <strong><?php echo $location; ?></strong>.</p>

        <div class="footer">
            <h3>MC CHIELO B. REAMBONANZA</h3>
            <h3>Principal</h3>
        </div>
    </div>

    <button class="print-button" onclick="printCertificate()">Print</button>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
