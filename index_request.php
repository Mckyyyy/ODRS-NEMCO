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

// Function to fetch the price for a specific document type
function getPrice($documentType, $conn)
{
    $sql = "SELECT price FROM document_prices WHERE document_type = '$documentType'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['price'];
    } else {
        return 0; // Default price if no price is found
    }
}
// Add this logic after inserting new requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentNumber = $_POST['studentNumber'];
    $documentType = $_POST['documentType'];

    $insert_sql = "INSERT INTO requests (studentNumber, document_type, is_read) VALUES ('$studentNumber', '$documentType', 0)";
    $conn->query($insert_sql);

    echo json_encode(['success' => true]);
    exit;
}
// Fetch prices for multiple document types
$priceForEnrollment = getPrice('forEnrollment', $conn);
$priceForReissuance = getPrice('forReissuance', $conn);
$priceForBoardExam = getPrice('forBoardExam', $conn);
$priceForGoodmoral = getPrice('forGoodmoral', $conn);
$priceForCertification = getPrice('forCertification', $conn);
$priceForAuthentication = getPrice('forAuthentication', $conn);
$priceForDismissal = getPrice('forDismissal', $conn);
$priceOthers = getPrice('others', $conn);


// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEMCO Request</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        /* Global Styles */
        html,
        body {
            width: 100%;
            height: 100%;
            min-height: 100%;
            margin: 0;
            background-image: url("bg1.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            justify-content: center;
            align-items: center;
            position: relative;
            background-position: center;
        }

        .container-fluid {
            padding: 1px 20px;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            /* Center all the cards within the container */
        }

        .card {
            flex: 1 1 calc(40% - 16px);
            box-sizing: border-box;
            margin-bottom: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            display: flex;
            /* Enables two-column layout */
            flex-wrap: wrap;
            gap: 15px;
            /* Space between columns */
            padding: 20px;
            justify-content: center;
            /* Ensure columns are centered within the card body */
        }

        .card-body .col {
            flex: 1 1 calc(50% - 10px);
            /* Two equal columns */
            box-sizing: border-box;
            background: rgba(250, 250, 250, 0.8);
            /* Light background for columns */
            padding: 10px;
            text-align: center;
            /* Center text inside each column */
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .card-body .col:hover {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-title,
        .card-body p {
            text-align: center;
            /* Center both the title and the description */
            width: 100%;
            /* Ensure full-width to prevent them from staying in-line */
            margin: 0;
            /* Remove any default margin that may cause extra space */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card {
                flex: 1 1 calc(60% - 16px);
            }

            .card-body .col {
                flex: 1 1 100%;
                /* Stacks columns vertically for smaller screens */
            }
        }

        @media (max-width: 480px) {
            .card {
                flex: 1 1 100%;
            }

            .card-body .col {
                flex: 1 1 100%;
                /* Ensures full-width columns for small devices */
            }
        }

        /* Form Container */
        .form-container {
            border: 1px solid black;
            border-radius: 10px;
            padding: 20px;
            margin-top: 45px;
            margin-bottom: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            position: relative;
            /* Added to position the button */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            /* Soft shadow */
            animation: float 3s ease-in-out infinite;
            /* Floating animation */
        }

        .top-right-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        /* Enhanced Form Row */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
            /* Adjusted spacing */
            gap: 15px;
            /* Space between columns */
            padding: 15px;
            /* Internal padding */
            border-radius: 10px;
            /* Rounded corners for a smoother look */
            background: linear-gradient(to right, rgba(250, 250, 250, 0.8), rgba(240, 240, 240, 0.8));
            /* Light gradient */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Subtle depth effect */
            transition: all 0.3s ease-in-out;
            /* Smooth hover animation */
        }

        .row:hover {
            background: rgba(255, 255, 255, 0.95);
            /* Brighter background on hover */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            /* Elevated shadow */
        }

        .col-md-3 {
            flex: 0 0 calc(25% - 10px);
            /* Responsive columns */
            box-sizing: border-box;
            margin: 5px 0;
            /* Vertical spacing */
        }

        @media (max-width: 768px) {
            .col-md-3 {
                flex: 0 0 calc(50% - 10px);
                /* Stack two columns for tablets */
            }
        }

        @media (max-width: 480px) {
            .col-md-3 {
                flex: 0 0 100%;
                /* Full width for small screens */
            }
        }

        /* Original Toggle Switch (Enhanced Styling Only) */
        .switch {
            position: relative;
            display: inline-block;
            width: 35px;
            /* Smaller width */
            height: 22px;
            /* Smaller height */
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* Toggle Switch Enhancements */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            /* Default off color */
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            /* Smooth transitions */
            border-radius: 10px;
            /* Soft rounded edges */
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.2);
            /* Inner shadow for depth */
        }

        .slider:before {
            position: absolute;
            content: "X";
            height: 19px;
            width: 19px;
            left: 1px;
            bottom: 1.6px;
            background-color: white;
            /* Default background */
            transition: transform 0.3s ease, color 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
            /* Smooth animations */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            /* Maintain current readability */
            color: red;
            /* Default color for off state */
            border-radius: 100%;
            /* Circular button */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            /* Button shadow for subtle depth */
        }

        /* On State Enhancements */
        input:checked+.slider {
            background-color: #8a03cd;
            /* Purple for on state */
            box-shadow: 0 0 10px rgba(138, 3, 205, 0.6);
            /* Outer glow effect for active */
        }

        input:checked+.slider:before {
            transform: translateX(15px);
            /* Keep original slide distance */
            content: "✔";
            /* Retain the check symbol */
            color: white;
            /* Clear contrast for active state */
            background-color: w;
            /* Match toggle background for seamless look */
            box-shadow: 0 2px 6px rgba(138, 3, 205, 0.4);
            /* Glow effect for the toggle button */
        }

        /* Hover and Focus Enhancements */
        .slider:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            /* Enhanced depth on hover */
        }

        input:focus+.slider {
            outline: 2px solid rgba(138, 3, 205, 0.5);
            /* Focus ring for accessibility */
            outline-offset: 3px;
        }

        /* Dropdown Styles */
        select.form-control {
            appearance: none;
            /* Remove default styling */
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 20 20"><polygon points="0,0 20,0 10,10" fill="gray"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            /* Position arrow */
            background-size: 10px;
            padding-right: 25px;
            /* Space for the arrow */
        }

        select.form-control:focus {
            box-shadow: none;
            /* Remove box-shadow on focus */
        }

        /* Footer Customization */
        footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #e0e0e0;
            font-size: 14px;
            color: #6c757d;
        }

        footer a {
            text-decoration: none;
            color: #007bff;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Add spacing between form groups */
        .form-group.col-md-2,
        .form-group.col-md-1 {
            margin-bottom: 20px;
            /* Space below each form group */
            margin-right: 10px;
            /* Space between form groups horizontally */
            padding: 12px;
            /* Internal spacing */
            border-radius: 10px;
            /* Rounded corners */
            background-color: rgba(255, 255, 255, 0.8);
            /* Very light off-white background */
            border: 1px solid rgba(220, 220, 220, 1);
            /* Subtle border for separation */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            /* Very light shadow for depth */
            transition: all 0.3s ease-in-out;
            /* Smooth hover effect */
        }

        /* Hover effects */
        .form-group.col-md-2:hover,
        .form-group.col-md-1:hover {
            background-color: rgba(255, 255, 255, 1);
            /* Pure white background on hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Slightly enhanced shadow on hover */
            transform: translateY(-2px);
            /* Lift effect */
        }

        /* Adjustments for responsiveness */
        @media (max-width: 768px) {

            .form-group.col-md-2,
            .form-group.col-md-1 {
                margin-right: 0;
                /* Remove horizontal spacing for full-width elements */
            }
        }
    </style>
    <!--

  /* Switch styling */
.switch {
    position: relative;
    display: inline-block;
    width: 33px; /* Smaller width */
    height: 28px; /* Smaller height */
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

/* Square Slider */
.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: background-color 0.4s ease, box-shadow 0.2s ease;
    border-radius: 5px; /* Slight rounding for a softer square */
}

.slider:before {
    position: absolute;
    content: "✘";
    height: 20px;
    width: 20px;
    left: 2px;
    bottom: 4px;
    background-color: white;
    transition: transform 0.4s ease, color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px; /* Adjusted font size */
    color: red;
    border-radius: 2px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Toggle on state */
input:checked + .slider {
    background-color: #8a03cd; /* Green background when checked */
    box-shadow: 0 0 8px rgba(40, 167, 69, 0.6);
}
input:checked + .slider:before {
    transform: translateX(10px); /* Adjusted for square size */
    content: "✔";
    border-radius: 3px; /* Slight rounding */
    transition: transform 0.4s ease, background-color 0.3s ease, color 0.3s ease;
}
/* Hover effect */
.slider:hover {
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
}

/* Focus accessibility */
input:focus + .slider {
    outline: 2px solid #8a03cd;
    outline-offset: 3px;
}
//-->

</head>

<body>
    <div class="container-fluid">
        <div class="form-container"> <!-- New container div for form -->
            <div class="text-left my-4">
                <img src="nemco.jpg" alt="NEMCO Logo" class="img-fluid"
                    style="max-width: 100px; float: left; margin-right: 15px; margin-top: -20px;">
                <h1>NEMCO Registrar Documents Request</h1>
            </div>
            <div class="d-flex justify-content-center justify-content-md-end mt-3 mt-md-0">
                <a href="index_request.php" class="btn btn-primary top-right-button btn-sm btn-md-lg">New Request</a>
            </div>

            <form method="post" action="process_form.php" enctype="multipart/form-data">
                <div class="form-row" style="margin-top: 50px;">
                    <div class="form-group col-md-2">
                        <label for="studentNumber">Student Number</label>
                        <input type="text" class="form-control" name="studentNumber" id="studentNumber"
                            placeholder="00XX-XXXX">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="firstName">First Name</label>
                        <input type="text" class="form-control" name="firstName" id="firstName" placeholder="FIRST NAME"
                            required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="lastName">Last Name</label>
                        <input type="text" class="form-control" name="lastName" id="lastName" placeholder="LAST NAME"
                            required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="middleName">Middle Name</label>
                        <input type="text" class="form-control" name="middleName" id="middleName"
                            placeholder="Middle Name" required>
                    </div>
                    <div class="form-group col-md-1">
                        <label for="suffix">Suffix</label>
                        <select class="form-control" name="suffix" id="suffix">
                            <option></option>
                            <option>Jr.</option>
                            <option>Sr.</option>
                            <option>I</option>
                            <option>II</option>
                            <option>III</option>
                            <option>IV</option>
                            <option>V</option>
                        </select>
                    </div>
                    <div class="w-100"></div>
                    <div class="form-group col-md-2">
                        <label for="programName">Program Name</label>
                        <select class="form-control" name="program" id="program">
                            <option></option>
                            <option>BS in Info Technology</option>
                            <option>BS in Criminology</option>
                            <option>BS in Business Administration</option>
                            <option>BS in Education</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="contactNumber">Contact Number</label>
                        <input type="text" class="form-control" name="contactNumber" id="contactNumber"
                            placeholder="09XXXXXXXXX" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="lastTermAttended">Last Term Attended</label>
                        <input type="text" class="form-control" name="lastTermAttended" id="lastTermAttended"
                            placeholder="YYYY" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="semester">Semester</label>
                        <select class="form-control" name="semester" id="semester" required>
                            <option></option>
                            <option>First Semester</option>
                            <option>Second Semester</option>
                            <option>Summer</option>
                        </select>
                    </div>
                </div>

                <h2>Student's Records</h2>
                <p>Click the switch to select the desired records.</p>

                <div class="card-container">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title" style="text-align: center;">
                                ₱<?php echo number_format($priceForEnrollment, 2); ?>/page</h5>
                            <p style="text-align: center;">Transcript of Records (ToR)</p>
                            <div class="form-group">
                                <!-- Row for the top 4 checkboxes -->
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forEnrollment" id="forEnrollment">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="forEnrollment">
                                                For Enrollment
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forBoardExam" id="forBoardExam">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="forBoardExam">
                                                For Board Exam
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forReference" id="forReference">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="forReference">
                                                For Reference
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forStudiesAbroad" id="forStudiesAbroad">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="forStudiesAbroad">
                                                For Studies Abroad
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forTransfer" id="forTransfer">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="forTransfer">
                                                For Transfer
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forEmployment" id="forEmployment">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="forEmployment">
                                                For Employment
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forRanking" id="forRanking">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="forRanking">
                                                For Ranking and Promotion
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card parent-card">
                        <div class="card-body">
                            <label class="switch switch-top-left">
                                <input type="checkbox" name="forReissuance" id="forReissuance">
                                <span class="slider"></span>
                            </label>
                            <h5 class="card-title" style="text-align: center;">
                                ₱<?php echo number_format($priceForReissuance, 2); ?>/page</h5>
                            <p style="text-align: center;">Re-issuance of Diploma</p>
                            <div class="card-container">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="letterRequest">Letter Request</label>
                                            <input type="file" class="form-control-file" name="letterRequest"
                                                id="letterRequest">
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="affidavitLoss">Affidavit of Loss</label>
                                            <input type="file" class="form-control-file" name="affidavitLoss"
                                                id="affidavitLoss">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                style="border: 1px solid #007bff; border-radius: 5px; padding: 15px; background-color: #f8f9fa;">
                                <h6 style="font-weight: bold; color: #007bff;">Note:</h6>
                                <ul style="margin-left: 20px; color: #343a40; font-size: 14px; line-height: 1.6;">
                                    <li>
                                        <h6 style="font-weight: normal;">
                                            <strong>Document Requirements:</strong> Bring the original copy of the
                                            letter request and affidavit of loss upon
                                            claiming a duplicate copy of the diploma.
                                        </h6>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>


                    <div class="card" style="height: 20%;">
                        <div class="card-body" style="margin-bottom: 50px;">
                            <h5 class="card-title" style="text-align: center;">
                                ₱<?php echo number_format($priceForGoodmoral, 2); ?>/page</h5>
                            <p style="text-align: center;">Certificate</p>
                            <div class="form-group">
                                <!-- Switches -->
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forGoodmoral" id="forGoodmoral">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="goodMoral">
                                                Certificate of Good Moral Character
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forGpa" id="forGpa">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="gpa">
                                                Certificate of Grade Point Average (GPA)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forEarnedUnits" id="forEarnedUnits">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="earnedUnits">
                                                Certificate of Earned Units
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forEnrollmentcert" id="forEnrollmentcert">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="enrollmentcert">
                                                Certificate of Enrollment
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forGraduating" id="forGraduating">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="graduating">
                                                Certificate of Graduating
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forGraduation" id="forGraduation">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="graduation">
                                                Certificate of Graduation
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forSpec" id="forSpec">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="spec">
                                                Certificate for Summer Job
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="formediumInstruction"
                                                    id="formediumInstruction">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="formediumInstruction">
                                                Medium of Instruction Certificate (MOI)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forRegistration" id="forRegistration">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="forRegistration">
                                                Certificate of Registration
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card parent-card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <div class="nested-cards">
                                <div class="card">
                                    <div class="card-body">
                                        <label class="switch switch-top-left">
                                            <input type="checkbox" name="forCertification" id="forCertification">
                                            <span class="slider"></span>
                                        </label>
                                        <h5 class="card-title" style="text-align: center;">
                                            ₱<?php echo number_format($priceForCertification, 2); ?>/page</h5>
                                        <p>Certification, Authentication and Verification (CAV)</p>
                                        <div class="form-group">
                                            <!-- Checkbox -->
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <label class="switch switch-top-left">
                                            <input type="checkbox" name="forAuthentication" id="forAuthentication">
                                            <span class="slider"></span>
                                        </label>
                                        <p>Authentication</p>
                                        <h5 class="card-title" style="text-align: center;">
                                            ₱<?php echo number_format($priceForAuthentication, 2); ?>/page</h5>
                                        <div class="form-group">
                                            <!-- Number Input -->
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <label class="switch switch-top-left">
                                            <input type="checkbox" name="forDismissal" id="forDismissal">
                                            <span class="slider"></span>
                                        </label>
                                        <h5 class="card-title" style="text-align: center;">
                                            ₱<?php echo number_format($priceForDismissal, 2); ?>/page</h5>
                                        <p>Honorable Dismissal</p>
                                        <div class="form-group">
                                            <!-- Checkbox -->
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <label class="switch switch-top-left">
                                            <input type="checkbox" name="forOthers" id="forOthers">
                                            <span class="slider"></span>
                                        </label>

                                        <h5 class="card-title" style="text-align: center;">
                                            ₱<?php echo number_format($priceOthers, 2); ?>/page</h5>
                                        <p>Others</p>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="others" id="others"
                                                placeholder="Others(Pls Specify)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--

                    <div class="card" style="max-width: 70%; margin: auto;">
                        <div class="card-body" style="margin-bottom: 50px;">
                            <h5 class="card-title" style="text-align: center;"></h5>
                            <p style="text-align: center;">Transcript of Records</p>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forGraduated2" id="forGraduated2">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="graduated2yearprogram">
                                            For Graduated (2 Year Program)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forGraduated4" id="forGraduated4">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="graduated4yearprogram">
                                            For Graduated (4 Year Program)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forUndergrad1" id="forUndergrad1">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="undergrad1">
                                            For Undergraduate (1st Year)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forUndergrad2" id="forUndergrad2">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="undergrad2">
                                            For Undergraduate (2nd Year)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3" style="margin-left: auto;">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forUndergrad3" id="forUndergrad3">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="undergrad3">
                                            For Undergraduate (3rd Year)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="margin-right: auto;">
                                        <div class="form-check">
                                            <label class="switch">
                                                <input type="checkbox" name="forUndergrad4" id="forUndergrad4">
                                                <span class="slider"></span>
                                            </label>
                                            <label class="form-check-label" for="graduation">
                                            For Undergraduate (4th Year)
                                            </label>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        //-->
                    <div class="form-group">
                        <div
                            style="border: 1px solid #007bff; border-radius: 5px; padding: 15px; background-color: #f8f9fa;">
                            <h6 style="font-weight: bold; color: #007bff;">Note:</h6>
                            <ul style="margin-left: 20px; color: #343a40; font-size: 14px; line-height: 1.6;">
                                <li>
                                    <h6 style="font-weight: normal;">
                                        <strong>Authorization Letter:</strong> Authorization letter with photocopies of
                                        IDs of the owner and the authorized
                                        person must be presented if the owner cannot personally claim the requested
                                        records.
                                    </h6>
                                </li>
                                <li>
                                    <h6 style="font-weight: normal;">
                                        <strong>Document Requirements:</strong> Bring the original copy of the letter
                                        request and affidavit of loss upon
                                        claiming a duplicate copy of the diploma.
                                    </h6>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <button type="submit" id="submitButton" class="btn btn-primary mr-3">Submit</button>
                    <a href="index.php" class="btn btn-primary">Back</a>
                </div>

            </form>
            <div><br>
                <footer class="footer_fixed">
                    <div class="text-center">
                        NEMCO - Registrar Office <a href="https://www.facebook.com/nemco1946/"> | NEMCO OFFICIAL FB PAGE
                            - All Rights Reserved - &copy; Copyright 2024 </a>
                    </div>
                    <div class="clearfix"></div>
                </footer>
            </div>
        </div>
    </div>
    <div id="alertModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3 id="modalTitle">Re-issuance of Diploma</h3>
            <p>Please upload both files before submitting.</p>
            <button id="modalOkButton">OK</button>
        </div>
    </div>
    <style>
        /* Modal Background */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            /* Darker background with slight transparency */
            backdrop-filter: blur(4px);
            /* Adds a blur effect to the background */
            animation: fadeIn 0.3s ease;
        }

        /* Modal Content */
        .modal-content {
            position: relative;
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            /* Subtle gradient background */
            margin: 10% auto;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            /* Larger shadow for better focus */
            width: 90%;
            max-width: 400px;
            text-align: center;
            animation: slideDown 0.4s ease-out;
            font-family: 'Arial', sans-serif;
        }

        /* Modal Title */
        .modal-content h3 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 15px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Modal Message */
        .modal-content p {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        /* Button Styling */
        .modal-content button {
            background: linear-gradient(90deg, #007BFF, #0056b3);
            /* Gradient button background */
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .modal-content button:hover {
            background: linear-gradient(90deg, #0056b3, #007BFF);
            /* Reverse gradient on hover */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            transform: translateY(-3px);
            /* Adds a lift effect */
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                padding: 15px 20px;
            }

            .modal-content h3 {
                font-size: 1.5rem;
            }

            .modal-content p {
                font-size: 0.9rem;
            }

            .modal-content button {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
    <script>
        document.getElementById('submitButton').addEventListener('click', function (event) {
            const toggle = document.getElementById('forReissuance');
            const letterRequest = document.getElementById('letterRequest').files.length;
            const affidavitLoss = document.getElementById('affidavitLoss').files.length;

            // Check if the toggle is on and both file inputs are empty
            if (toggle.checked && (!letterRequest || !affidavitLoss)) {
                event.preventDefault(); // Prevent form submission
                const modal = document.getElementById('alertModal');
                modal.style.display = 'block';

                // Close the modal and scroll to the "Re-issuance of Diploma" paragraph
                const okButton = document.getElementById('modalOkButton');
                okButton.onclick = () => {
                    modal.style.display = 'none';
                };

                // Close the modal when clicking outside of it
                window.onclick = (e) => {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                };
            }
        });
    </script>
    <!-- Include jQuery and SweetAlert libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>



    <script>
        $(document).ready(function () {
            $('form').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                // Function to format keys into a more readable format
                function formatKey(key) {
                    if (key === 'formediumInstruction') {
                        return 'Medium Instruction'; // Removed 'For'
                    }
                    if (key === 'forEnrollmentcert') {
                        return 'Certificate of Enrollment'; // Removed 'For'
                    }
                    if (key === 'semester') {
                        return 'Semester'; // Ensure 'Semester' appears correctly
                    }
                    return key
                        .replace(/^for/, '') // Remove 'For' prefix
                        .replace(/([A-Z])/g, ' $1') // Add a space before each uppercase letter
                        .replace(/^./, str => str.toUpperCase()) // Capitalize the first letter
                        .trim(); // Remove any extra spaces
                }

                // Prices for the documents
                const prices = {
                    forEnrollment: 200,
                    forBoardExam: 200,
                    forReference: 200,
                    forStudiesAbroad: 200,
                    forTransfer: 200,
                    forEmployment: 200,
                    forRanking: 200,
                    forReissuance: 200,
                    forGoodmoral: 35,
                    forGpa: 35,
                    forRegistration: 35,
                    forEarnedUnits: 35,
                    forGraduating: 35,
                    forGraduation: 35,
                    forSpec: 35,
                    formediumInstruction: 35,
                    forCertification: 30,
                    forAuthentication: 60,
                    forDismissal: 65,
                    forEnrollmentcert: 35,
                    forOthers: 25,
                    forGraduated2: 0,
                    forGraduated4: 0,
                    forUndergrad1: 0,
                    forUndergrad2: 0,
                    forUndergrad3: 0,
                    forUndergrad4: 0,
                };

                let formData = new FormData(this);
                let totalPrice = 0;

                // Variables to hold the names and student number
                let studentNumber = '';
                let firstName = '';
                let middleName = '';
                let lastName = '';
                let suffix = '';

                // Sections for categorization
                let certificateSection = '';
                let transcriptSection = '';
                let torSection = '';
                let reIssuanceSection = ''; // Section for Re-issuance
                let cavSection = ''; // Section for CAV
                let authenticationSection = ''; // Section for Authentication
                let honorableDismissalSection = ''; // Section for Honorable Dismissal
                let othersSection = ''; // Section for Others
                let dataString = ''; // Initialize dataString for display

                formData.forEach((value, key) => {
                    if (key === 'forOthers' && $(`[name="${key}"]`).is(':checked')) {
                        const otherInput = $(`[name="others"]`).val(); // Get 'others' input value

                        if (otherInput && otherInput.trim() !== '') {
                            let price = prices[key] || 0;
                            let checkSymbol = '<span style="color: green;">✔</span>'; // Add checkmark symbol

                            // Add to Others section
                            othersSection += `<strong>Others:</strong> ${checkSymbol} ${otherInput} (₱${price.toFixed(2)}) `;
                            othersSection += `<button type="button" class="btn btn-sm btn-primary" id="minus-${key}" style="height: 20px;">-</button>`;
                            othersSection += `<input type="number" class="form-control copy-input" id="copies-${key}" value="1" style="width: 60px; height: 25px; display: inline-block; margin: 0 5px; text-align: center;" min="1" />`;
                            othersSection += `<button type="button" class="btn btn-sm btn-primary" id="plus-${key}" style="height: 20px;">+</button><br>`;

                            formData.append('others', otherInput); // Append 'others' value to formData
                        }
                    } else if (key.startsWith('for') && $(`[name="${key}"]`).is(':checked')) {
                        let price = prices[key] || 0;
                        let checkSymbol = '<span style="color: green;">✔</span>'; // Add checkmark symbol

                        // Determine the section for each key based on conditions
                        if (key === 'forReissuance') {
                            // Add to Re-issuance section
                            reIssuanceSection += `<strong>${formatKey(key)}</strong> ${checkSymbol} (₱${price.toFixed(2)}) `;
                            reIssuanceSection += `<button type="button" class="btn btn-sm btn-primary" id="minus-${key}" style="height: 20px;">-</button>`;
                            reIssuanceSection += `<input type="number" class="form-control copy-input" id="copies-${key}" value="1" style="width: 60px; height: 25px; display: inline-block; margin: 0 5px; text-align: center;" min="1" />`;
                            reIssuanceSection += `<button type="button" class="btn btn-sm btn-primary" id="plus-${key}" style="height: 20px;">+</button><br>`;
                        } else if (key === 'forCertification') {
                            // Add to CAV section (for Certification)
                            cavSection += `<strong>${formatKey(key)}</strong> ${checkSymbol} (₱${price.toFixed(2)}) `;
                            cavSection += `<button type="button" class="btn btn-sm btn-primary" id="minus-${key}" style="height: 20px;">-</button>`;
                            cavSection += `<input type="number" class="form-control copy-input" id="copies-${key}" value="1" style="width: 60px; height: 25px; display: inline-block; margin: 0 5px; text-align: center;" min="1" />`;
                            cavSection += `<button type="button" class="btn btn-sm btn-primary" id="plus-${key}" style="height: 20px;">+</button><br>`;
                        } else if (key === 'forDismissal') {
                            // Add to Honorable Dismissal section
                            honorableDismissalSection += `<strong>${formatKey(key)}</strong> ${checkSymbol} (₱${price.toFixed(2)}) `;
                            honorableDismissalSection += `<button type="button" class="btn btn-sm btn-primary" id="minus-${key}" style="height: 20px;">-</button>`;
                            honorableDismissalSection += `<input type="number" class="form-control copy-input" id="copies-${key}" value="1" style="width: 60px; height: 25px; display: inline-block; margin: 0 5px; text-align: center;" min="1" />`;
                            honorableDismissalSection += `<button type="button" class="btn btn-sm btn-primary" id="plus-${key}" style="height: 20px;">+</button><br>`;
                        } else if (key === 'forAuthentication') {
                            // Add to Authentication section
                            authenticationSection += `<strong>${formatKey(key)}</strong> ${checkSymbol} (₱${price.toFixed(2)}) `;
                            authenticationSection += `<button type="button" class="btn btn-sm btn-primary" id="minus-${key}" style="height: 20px;">-</button>`;
                            authenticationSection += `<input type="number" class="form-control copy-input" id="copies-${key}" value="1" style="width: 60px; height: 25px; display: inline-block; margin: 0 5px; text-align: center;" min="1" />`;
                            authenticationSection += `<button type="button" class="btn btn-sm btn-primary" id="plus-${key}" style="height: 20px;">+</button><br>`;


                        } else if ([
                            'forGoodmoral',
                            'forRegistration',
                            'forGpa',
                            'forEarnedUnits',
                            'forGraduating',
                            'forGraduation',
                            'forSpec',
                            'formediumInstruction',
                            'forEnrollmentcert'
                        ].includes(key)) {
                            certificateSection += `<strong>${formatKey(key)}</strong> ${checkSymbol} (₱${price.toFixed(2)}) `;
                            certificateSection += `<button type="button" class="btn btn-sm btn-primary" id="minus-${key}" style="height: 20px;">-</button>`;
                            certificateSection += `<input type="number" class="form-control copy-input" id="copies-${key}" value="1" style="width: 60px; height: 25px; display: inline-block; margin: 0 5px; text-align: center;" min="1" />`;
                            certificateSection += `<button type="button" class="btn btn-sm btn-primary" id="plus-${key}" style="height: 20px;">+</button><br>`;
                        } else if ([
                            'forGraduated2',
                            'forGraduated4',
                            'forUndergrad1',
                            'forUndergrad2',
                            'forUndergrad3',
                            'forUndergrad4'
                        ].includes(key)) {
                            torSection += `<strong>${formatKey(key)}</strong> ${checkSymbol} (₱${price.toFixed(2)}) `;
                            torSection += `<button type="button" class="btn btn-sm btn-primary" id="minus-${key}" style="height: 20px;">-</button>`;
                            torSection += `<input type="number" class="form-control copy-input" id="copies-${key}" value="1" style="width: 60px; height: 25px; display: inline-block; margin: 0 5px; text-align: center;" min="1" />`;
                            torSection += `<button type="button" class="btn btn-sm btn-primary" id="plus-${key}" style="height: 20px;">+</button><br>`;
                        } else {
                            transcriptSection += `<strong>${formatKey(key)}</strong> ${checkSymbol} (₱${price.toFixed(2)}) `;
                            transcriptSection += `<button type="button" class="btn btn-sm btn-primary" id="minus-${key}" style="height: 20px;">-</button>`;
                            transcriptSection += `<input type="number" class="form-control copy-input" id="copies-${key}" value="1" style="width: 60px; height: 25px; display: inline-block; margin: 0 5px; text-align: center;" min="1" />`;
                            transcriptSection += `<button type="button" class="btn btn-sm btn-primary" id="plus-${key}" style="height: 20px;">+</button><br>`;
                        }

                        // Update total price based on copies input
                        const copiesKey = `copies-${key}`;
                        const copies = parseInt(formData.get(copiesKey), 10) || 1; // Default to 1 copy
                        totalPrice += price * copies;
                    } else {
                        // Collect user info and other inputs
                        if (key === 'studentNumber') {
                            studentNumber = value;
                        } else if (key === 'firstName') {
                            firstName = value;
                        } else if (key === 'middleName') {
                            middleName = value;
                        } else if (key === 'lastName') {
                            lastName = value;
                        } else if (key === 'suffix') {
                            suffix = value;
                        } else if (key === 'program' || key === 'contactNumber' || key === 'lastTermAttended' || key === 'semester') {
                            dataString += `<strong>${formatKey(key)}:</strong> ${value}<br>`;
                        }
                    }
                });

                // Add the full name and student number at the top
                let fullName = `${firstName} ${middleName} ${lastName} ${suffix}`.trim();
                if (fullName) {
                    dataString = `<strong>Full Name:</strong> ${fullName}<br>` + dataString;
                }
                if (studentNumber) {
                    dataString = `<strong>Student Number:</strong> ${studentNumber}<br>` + dataString;
                }

                // Combine sections for transcripts, ToR, certificates, and other categories
                if (reIssuanceSection) {
                    dataString += `<br><h5>Re-issuance of Diploma</h5>${reIssuanceSection}`;
                }
                if (cavSection) {
                    dataString += `<br><h5>Certification, Authentication and Verification (CAV)</h5>${cavSection}`;
                }
                if (authenticationSection) {
                    dataString += `<br><h5>Authentication</h5>${authenticationSection}`;
                }
                if (honorableDismissalSection) {
                    dataString += `<br><h5>Honorable Dismissal</h5>${honorableDismissalSection}`;
                }
                if (othersSection) {
                    dataString += `<br><h5>Others</h5>${othersSection}`;
                }
                if (transcriptSection) {
                    dataString += `<br><h5>Transcript of Records (ToR)</h5>${transcriptSection}`;
                }
                if (certificateSection) {
                    dataString += `<br><h5>Certificate</h5>${certificateSection}`;
                }
                if (torSection) {
                    dataString += `<br><h5>N/A</h5>${torSection}`;
                }

                // Add the total price to the display string with high contrast color
                dataString += `<br><strong style="color: #FF4500; font-size: 1.2em;" id="price-display">Tentative Price: ₱${totalPrice.toFixed(2)}</strong>`;

                // Add the dynamic "+" and "-" button functionality
                Swal.fire({
                    title: 'Confirm your request',
                    html: `<div style="text-align: left;">${dataString}</div>`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                    didOpen: () => {
                        $('.btn-sm').on('click', function () {
                            const key = $(this).attr('id').split('-')[1];
                            const input = $(`#copies-${key}`);
                            let currentValue = parseInt(input.val()) || 1;

                            if ($(this).attr('id').includes('plus')) {
                                currentValue += 1;
                            } else if ($(this).attr('id').includes('minus') && currentValue > 1) {
                                currentValue -= 1;
                            }

                            input.val(currentValue);

                            // Recalculate total price
                            updateTotalPrice();
                        });

                        $('.copy-input').on('change', function () {
                            updateTotalPrice();
                        });

                        function updateTotalPrice() {
                            totalPrice = 0;
                            $('.copy-input').each(function () {
                                const key = $(this).attr('id').split('-')[1];
                                const price = prices[key] || 0;
                                const currentCopies = parseInt($(this).val()) || 1;
                                totalPrice += price * currentCopies;
                            });

                            $('#price-display').text(`Tentative Price: ₱${totalPrice.toFixed(2)}`);
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.copy-input').each(function () {
                            const key = $(this).attr('id').split('-')[1];
                            formData.append(`copies-${key}`, $(this).val()); // Append all copies
                        });

                        formData.append('totalPrice', totalPrice);
                        // Send form data via AJAX
                        $.ajax({
                            url: 'process_form.php',
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                if (response.status === 'success') {
                                    // Display SweetAlert with success message and total price
                                    Swal.fire({
                                        title: '<h2 style="color: #4caf50; font-size: 24px; font-weight: bold;">Success!</h2>',
                                        html: `
                                    <p style="font-size: 16px; color: #333;">Take note of the transaction code:</p>
                                    <div style="
                                        border: 2px dashed #2196f3; 
                                        padding: 15px; 
                                        display: inline-block; 
                                        margin-bottom: 15px; 
                                        background-color: #f0f8ff; 
                                        border-radius: 8px;
                                        font-size: 18px;
                                        color: #000;
                                        font-weight: bold;
                                        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
                                    ">
                                        ${response.transaction_code}
                                    </div>
                                    <p style="font-size: 16px; color: #555;">Tentative Price: <span style="font-weight: bold; color: #d32f2f;">₱${totalPrice.toFixed(2)}</span></p>
                                    <p style="font-size: 14px; color: #666; line-height: 1.5;">
                                        Please take screenshots or pictures.<br>
                                        Use the transaction code to track the status of your request.<br>
                                        Visit NEMCO once the status is updated to 
                                        <strong style="color: #ff9800;">"Status: Ready for Final Printing"</strong>.
                                    </p>
                                `,
                                        icon: 'success',
                                        confirmButtonText: '<span style="font-size: 16px; font-weight: bold;">OK</span>',
                                        customClass: {
                                            popup: 'swal-custom-popup'
                                        }

                                    }).then(function () {
                                        // After the user clicks OK, show another pop-up with the download button
                                        Swal.fire({
                                            title: 'Download Information',
                                            html: `
                                        <p>Would you like to download your transaction details?</p>
                                        <button id="download-btn" class="swal2-confirm swal2-styled" style="display:inline-block;">Download PDF</button>
                                    `,
                                            showCancelButton: false,
                                            showConfirmButton: false,
                                            didOpen: () => {
                                                // Add click event to the download button
                                                document.getElementById('download-btn').addEventListener('click', function () {
                                                    downloadTransactionInfoAsPDF(response.transaction_code, totalPrice, dataString);
                                                });
                                            }
                                        });
                                    });
                                } else {
                                    // Display error message if something went wrong
                                    Swal.fire({
                                        title: 'Error!',
                                        text: response.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'An unexpected error occurred.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });

            // Function to handle the download of transaction information as PDF
            function downloadTransactionInfoAsPDF(transactionCode) {
                const { jsPDF } = window.jspdf;

                const doc = new jsPDF({
                    unit: 'mm',
                    format: 'a4' // Set format to A4
                });

                const pageWidth = doc.internal.pageSize.getWidth();
                const pageHeight = doc.internal.pageSize.getHeight();

                // Header Banner
                doc.setFillColor(200, 230, 255); // Light blue banner
                doc.rect(0, 0, pageWidth, 20, 'F'); // Full-width banner
                doc.setFont('Helvetica', 'bold');
                doc.setFontSize(16);
                doc.setTextColor(0, 0, 128);
                doc.text('NEMCO Transaction Details', pageWidth / 2, 12, null, null, 'center');

                // Title Section
                doc.setFontSize(20);
                doc.setTextColor(0, 0, 128);
                doc.text('Transaction Information', pageWidth / 2, 40, null, null, 'center');
                doc.setDrawColor(0, 0, 128);
                doc.setLineWidth(0.8);
                doc.line(30, 42, pageWidth - 30, 42); // Underline below the title

                // Transaction Code Section
                doc.setFontSize(14);
                doc.setFont('Helvetica', 'bold');
                doc.setTextColor(0, 0, 0);
                doc.text('Your Transaction Code:', pageWidth / 2, 60, null, null, 'center');

                // Rounded box for transaction code
                const boxWidth = 50;
                const boxHeight = 15;
                const boxX = (pageWidth - boxWidth) / 2;
                const boxY = 65;
                doc.setDrawColor(0, 0, 128); // Dark blue border
                doc.setFillColor(245, 245, 245); // Light grey background
                doc.roundedRect(boxX, boxY, boxWidth, boxHeight, 5, 5, 'FD');

                // Center the transaction code inside the box
                doc.setFontSize(16); // Slightly larger font size for code
                doc.setFont('Helvetica', 'bold');
                doc.setTextColor(255, 0, 0); // Red color for transaction code
                doc.text(transactionCode, pageWidth / 2, boxY + 10, null, null, 'center');


                // Instructions Section
                doc.setFontSize(12);
                doc.setFont('Helvetica', 'normal');
                doc.setTextColor(0, 0, 0);
                doc.text('Please follow the instructions below:', 20, 85);

                // Bullet points with improved layout
                const instructions = [
                    '\u2022 Take screenshots or pictures of this document as a backup.',
                    '\u2022 Safeguard your transaction code; it is required for tracking.',
                    '\u2022 Track the request status on the NEMCO website.',
                    '\u2022 Bring proper identification when visiting NEMCO offices.',
                    '\u2022 Ensure your payment is processed promptly to avoid delays.',
                ];

                let startY = 95;
                instructions.forEach((instruction) => {
                    const splitText = doc.splitTextToSize(instruction, pageWidth - 40);
                    doc.text(splitText, 30, startY);
                    startY += splitText.length * 6 + 3;
                });

                // Tracking Section
                startY += 10; // Add spacing before tracking section
                doc.setFontSize(14);
                doc.setFont('Helvetica', 'bold');
                doc.text('Tracking Your Request:', pageWidth / 2, startY, null, null, 'center');

                startY += 8; // Position below the heading
                doc.setFont('Helvetica', 'normal');
                const trackingText = 'Use the transaction code above to monitor the progress of your request. You can check the status via the NEMCO system using the following steps:';
                const trackingSteps = [
                    '1. Visit the NEMCO website NEMCO Registrar Documents Request.',
                    '2. Navigate to the "Track Your Request" section.',
                    '3. Enter your transaction code and submit.',
                    '4. Follow the instructions provided for further steps.',
                ];
                const wrappedTrackingText = doc.splitTextToSize(trackingText, pageWidth - 40);
                doc.text(wrappedTrackingText, 20, startY);

                startY += wrappedTrackingText.length * 6 + 5;
                trackingSteps.forEach((step) => {
                    const wrappedStep = doc.splitTextToSize(step, pageWidth - 40);
                    doc.text(wrappedStep, 30, startY);
                    startY += wrappedStep.length * 6 + 3;
                });

                // Add Note Section
                startY += 12; // Add space before the note
                doc.setFontSize(11);
                doc.setFont('Helvetica', 'italic');
                doc.setTextColor(50, 50, 50);
                const note = `Note: 
                1. If the status of your request is marked as "Ready for Printing," visit the nearest NEMCO branch. 
                2. Only proceed to the branch once the status is marked as "Ready for Printing".`;
                const wrappedNote = doc.splitTextToSize(note, pageWidth - 40);
                doc.text(wrappedNote, 20, startY);

                // Footer
                doc.setDrawColor(0, 0, 128);
                doc.setLineWidth(0.3);
                doc.line(20, pageHeight - 20, pageWidth - 20, pageHeight - 20);

                doc.setFontSize(10);
                doc.setTextColor(100);
                doc.text('Generated by NEMCO System | Please retain this PDF for your records.', pageWidth / 2, pageHeight - 10, null, null, 'center');

                // Save the PDF
                doc.save(`Transaction_${transactionCode}.pdf`);

                // Redirect after download
                window.location.href = 'index.php';
            }


        });
    </script>



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>