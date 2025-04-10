<?php
// view_request.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "records_db1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the request ID from the URL (sanitize it)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Invalid request ID.");
}

// Prepare SQL query to fetch request details
$sql = "SELECT * FROM requests WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();

// Check if request exists
if (!$request) {
    die("Request not found.");
}

// Close the statement after use
$stmt->close();

// Decode copiesData JSON string to an associative array
$copiesData = json_decode($request['copiesData'], true) ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   
   <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 30px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #343a40;
            margin-bottom: 20px;
        }
        .table {
            background: linear-gradient(to bottom, #dcd0ff, #ffffff);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #dee2e6;
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 15px;
            border: 1px solid #dee2e6;
        }
        .btn-custom {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        // JavaScript function to redirect to the Request List section
        function goToRequestList() {
            window.location.href = "admin_dashboard.php#requestListSection";
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title text-center">View Request Details</h1>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <tbody>
                        <!-- Display request details -->
                        <tr><th>Transaction Code</th><td><?php echo htmlspecialchars($request['transaction_code']); ?></td></tr>
                        <tr><th>Student Number</th><td><?php echo htmlspecialchars($request['studentNumber']); ?></td></tr>
                        <tr><th>Name</th><td><?php echo htmlspecialchars($request['firstName'] . ' ' . $request['middleName'] . ' ' . $request['lastName']); ?></td></tr>
                        <tr><th>Program</th><td><?php echo htmlspecialchars($request['program']); ?></td></tr>
                        <tr><th>Contact Number</th><td><?php echo htmlspecialchars($request['contactNumber']); ?></td></tr>
                        <tr><th>Last Term Attended</th><td><?php echo htmlspecialchars($request['lastTermAttended']); ?></td></tr>
                        <tr><th>Semester</th><td><?php echo htmlspecialchars($request['semester']); ?></td></tr>
                        <tr><th>Payment</th><td>₱<?php echo number_format($request['payment'], 2); ?></td></tr>
                        <tr><th>Status</th><td><?php echo htmlspecialchars($request['status']); ?></td></tr>

                        <!-- Document requests with copies -->
                        <?php 
                        $documentTypes = [
                            'forEnrollment' => 'For Enrollment',
                            'forBoardExam' => 'For Board Exam',
                            'forRegistration' => 'Certificate of Registration',
                            'forReference' => 'For Reference',
                            'forStudiesAbroad' => 'For Studies Abroad',
                            'forTransfer' => 'For Transfer to Other School',
                            'forEmployment' => 'For Employment',
                            'forRanking' => 'For Ranking and Promotion',
                            'forReissuance' => 'Re-issuance of Diploma',
                            'forGoodmoral' => 'Certificate of Good Moral Character',
                            'forGpa' => 'Certificate of GPA',
                            'forEarnedUnits' => 'Certificate of Earned Units',
                            'forEnrollmentcert' => 'Certificate of Enrollment',
                            'forGraduating' => 'Certificate of Graduating',
                            'forGraduation' => 'Certificate of Graduation',
                            'forSpec' => 'Certificate for Summer Job Units (SPEC)',
                            'formediumInstruction' => 'Certificate of Medium Term of Instruction',
                            'forCertification' => 'Certification, Authentication, and Verification (CAV)',
                            'forAuthentication' => 'Authentication',
                            'forDismissal' => 'Honorable Dismissal',
                            'forOthers' => 'Others',
                            'forGraduated2' => 'For Graduated (2 Years Program)',
                            'forGraduated4' => 'For Graduated (4 Years Program)',
                            'forUndergrad1' => 'For Undergraduate (1st Year)',
                            'forUndergrad2' => 'For Undergraduate (2nd Year)',
                            'forUndergrad3' => 'For Undergraduate (3rd Year)',
                            'forUndergrad4' => 'For Undergraduate (4th Year)',
                        ];

                        foreach ($documentTypes as $key => $label) {
                            if (!empty($request[$key])) {
                                echo "<tr><th>{$label}</th><td>Requested";
                                if (isset($copiesData[$key])) {
                                    echo " ({$copiesData[$key]} copies)";
                                }
                                
                                // Display 'others' next to copies if forOthers is requested
                                if ($key === 'forOthers' && !empty($request['others'])) {
                                    echo " - " . htmlspecialchars($request['others']);
                                }
                                
                                echo "</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
