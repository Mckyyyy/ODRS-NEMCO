<?php
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

// Handle form submission for updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedCopies = $_POST['copies'] ?? [];
    $copiesDataJson = json_encode($updatedCopies);

    // Update copiesData in the database
    $updateSql = "UPDATE requests SET copiesData = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('si', $copiesDataJson, $id);

    if ($updateStmt->execute()) {
        echo "<script>
            Swal.fire({
                title: 'Updated!',
                text: 'Copies updated successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to requestListSection after SweetAlert confirmation
                    window.location.href = '#requestListSection';
                }
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Failed to update copies.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }
    $updateStmt->close();
}

// Fetch request details
$sql = "SELECT * FROM requests WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();

if (!$request) {
    die("Request not found.");
}

$stmt->close();

$copiesData = json_decode($request['copiesData'], true) ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #495057;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        .card-header {
            background-color: #007bff;
            color: #ffffff;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .card-header h1 {
            font-size: 1.75rem;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        td {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        input[type="number"] {
            width: 80px;
            padding: 5px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .text-center {
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            th, td {
                padding: 10px;
            }

            input[type="number"] {
                width: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h1>Edit Request Details</h1>
            </div>
            <div class="card-body">
                <form id="updateForm" method="POST">
                    <table class="table table-striped">
                        <tbody>
                            <?php 
                            $documentTypes = [
                                'forEnrollment' => 'For Enrollment',
                                'forBoardExam' => 'For Board Exam',
                                'forReference' => 'For Reference',
                                'forRegistration' => 'Certificate of Registration',
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

                                    $value = $copiesData[$key] ?? 0;
                                    echo " <input type='number' name='copies[{$key}]' value='{$value}' class='form-control w-25 d-inline-block' style='max-width: 100px;' min='0' />";

                                    echo "</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Update Copies</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Handle form submission via AJAX
        $('#updateForm').submit(function(e) {
            e.preventDefault(); // Prevent the default form submission

            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: '', // Current page (or specify the PHP file if it's different)
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Handle the response after the AJAX request
                    if (response.includes('Updated!')) {
                        Swal.fire({
                            title: 'Updated!',
                            text: 'Copies updated successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to requestListSection after SweetAlert confirmation
                                window.location.href = 'http://localhost/php-php-php7/admin_dashboard.php#';
                            }
                        });
                    } else if (response.includes('Error!')) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to update copies.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong with the request.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
