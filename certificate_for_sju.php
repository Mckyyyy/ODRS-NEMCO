<?php
// Include the database connection file
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

// Check if student number is passed
if (!isset($_GET['studentNumber'])) {
    die("Student number not specified.");
}

$studentNumber = $_GET['studentNumber'];

// Fetch student details based on the student number
$sql = "SELECT * FROM requests WHERE studentNumber = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentNumber);
$stmt->execute();
$result = $stmt->get_result();

// Check if a student is found
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Use a ternary operator to ensure keys exist and concatenate
    $firstName = $row['firstName'] ?? '';
    $middleName = isset($row['middleName']) ? $row['middleName'] . ' ' : ''; // Add a space only if middle name exists
    $lastName = $row['lastName'] ?? '';
    $fullName = trim($firstName . ' ' . $middleName . $lastName); // Trim to remove extra spaces
    $program = $row['program'] ?? '';
    $schoolYear = $row['schoolYear'] ?? 'SY 2023-2024';
    $schoolName = "Northeastern Mindanao Colleges"; // Hardcoded as per the sample
    $location = "Surigao City"; // Hardcoded as per the sample
} else {
    echo "No student found.";
    exit; // Exit if no student found
}

$stmt->close();

// Function to format the day with suffix
function formatDayWithSuffix($day) {
    $suffix = 'th';
    if ($day % 10 == 1 && $day != 11) {
        $suffix = 'st';
    } elseif ($day % 10 == 2 && $day != 12) {
        $suffix = 'nd';
    } elseif ($day % 10 == 3 && $day != 13) {
        $suffix = 'rd';
    }
    return $day . $suffix;
}

// Get the current date details
$currentDay = date('j'); // Day of the month without leading zeros
$currentMonth = date('F'); // Full textual representation of a month
$currentYear = date('Y'); // Full numeric representation of a year

// Format the current day
$formattedDay = formatDayWithSuffix($currentDay);
?>

<script>
    // JavaScript function to redirect to the Request List section
    function goToRequestList() {
        window.location.href = "admin_dashboard.php#requestListSection";
    }
</script>

<!DOCTYPE html>
<html>
<head>
    <title>Certificate of Good Moral Character</title>
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
            position: relative; /* Enables absolute positioning for logos */
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
        .logo-left {
            position: absolute;
            top: 0;
            left: 10px;
            width: 100px;
        }
        .logo-right {
            position: absolute;
            top: 0;
            right: 10px;
            width: 100px;
        }
        .header {
            text-align: center;
            margin-bottom: 50px;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
        }
        /* Styling the back button */
        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 5px 10px;
            font-size: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        @media print {
            body {
                background: skyblue; /* Background for print */
            }
            .back-button,
            button:not(.print-button) {
                display: none;
            }
        }
        .print-button {
    position: absolute;
    left: 540px;
    padding: 10px 20px;
    font-size: 14px;
    background-color: darkblue;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
    </style>
    <script>
        // Function to navigate to the request section on the admin dashboard
        function goToRequestSection() {
            window.location.href = "admin_dashboard.php#requestListSection";
        }

        function printCertificate() {
            var printContents = document.getElementById('certificate').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(); // Optional: Reload to reset state
        }
    </script>
</head>
<body>
    <!-- Back Button with onclick functionality -->
    <button class="back-button" onclick="goToRequestSection()">Back</button>

    <div class="certificate" id="certificate">
        <!-- Left and right logos with absolute positioning -->
        <img src="CHEDLOGO.png" class="logo-left" alt="DepEd Logo" style="margin-left: 60px; margin-top: 10px;">
        <img src="nemco.jpg" class="logo-right" alt="School Logo" style="margin-right: 60px; margin-top: 10px;">

        <div class="header">
            <h3 style="">Republic of the Philippines</h3>
            <h3>Commission on Higher Education</h3>
            <h3>Region XIII</h3>
            <h3>Division of I DON'T KNOW</h3>
            <h2>NORTHEASTERN MINDANAO COLLEGES</h2>
            <h3>Surigao City</h3>
        </div>

        <h1>Certificate for Summer Job Units</h1><br>

        <p>TO WHOM IT MAY CONCERN:</p>

        <p>This is to certify that <strong><?php echo $fullName; ?></strong> is a bonafide <strong><?php echo $program; ?></strong> student of this school, <strong><?php echo $schoolYear; ?></strong>.</p>

        <p>This further certifies that he/she is of good moral character, having not been subjected to disciplinary action while a student in this school.</p>

        <p>Issued upon request of the interested party for whatever legal purpose it may serve him/her.</p>

        <p>Given this <strong><?php echo $formattedDay; ?></strong> day of <strong><?php echo $currentMonth; ?></strong> at <strong><?php echo $schoolName; ?></strong>, <strong><?php echo $location; ?></strong>.</p>

        <div class="footer">
            <h3>MC CHIELO B. REAMBONANZA</h3>
            <h3>Principal</h3>
        </div>
    </div>

    <!-- Print Certificate Button -->
    <button class="print-button" onclick="printCertificate()" style="display: block; margin: 20px auto; padding: 10px 20px;">Print</button>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
