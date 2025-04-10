<?php
// process_form.php

header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "records_db1";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $response['status'] = 'error';
        $response['message'] = 'Connection failed: ' . $conn->connect_error;
    } else {
        // Collect form data
        $studentNumber = $_POST['studentNumber'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $middleName = $_POST['middleName'];
        $suffix = $_POST['suffix'];
        $program = $_POST['program'];
        $contactNumber = $_POST['contactNumber'];
        $lastTermAttended = $_POST['lastTermAttended'];
        $semester = $_POST['semester'];
        $forGoodmoral = isset($_POST['forGoodmoral']) ? 1 : 0;
        $forDismissal = isset($_POST['forDismissal']) ? 1 : 0;
        $forEnrollment = isset($_POST['forEnrollment']) ? 1 : 0;
        $forBoardExam = isset($_POST['forBoardExam']) ? 1 : 0;
        $forReference = isset($_POST['forReference']) ? 1 : 0;
        $forStudiesAbroad = isset($_POST['forStudiesAbroad']) ? 1 : 0;
        $forTransfer = isset($_POST['forTransfer']) ? 1 : 0;
        $forEmployment = isset($_POST['forEmployment']) ? 1 : 0;
        $forRanking = isset($_POST['forRanking']) ? 1 : 0;
        $forCertification = isset($_POST['forCertification']) ? 1 : 0;
        $forGpa = isset($_POST['forGpa']) ? 1 : 0;
        $forRegistration = isset($_POST['forRegistration']) ? 1 : 0;
        $forGraduation = isset($_POST['forGraduation']) ? 1 : 0;
        $forAuthentication = isset($_POST['forAuthentication']) ? 1 : 0;
        $formediumInstruction = isset($_POST['formediumInstruction']) ? 1 : 0;
        $forReissuance = isset($_POST['forReissuance']) ? 1 : 0;
        $forEnrollmentcert = isset($_POST['forEnrollmentcert']) ? 1 : 0;
        $forEarnedUnits = isset($_POST['forEarnedUnits']) ? 1 : 0;
        $forGraduating = isset($_POST['forGraduating']) ? 1 : 0;
        $forSpec = isset($_POST['forSpec']) ? 1 : 0;
        $forOthers = isset($_POST['forOthers']) ? 1 : 0;
        $forGraduated2 = isset($_POST['forGraduated2']) ? 1 : 0;
        $forGraduated4 = isset($_POST['forGraduated4']) ? 1 : 0;
        $forUndergrad1 = isset($_POST['forUndergrad1']) ? 1 : 0;
        $forUndergrad2 = isset($_POST['forUndergrad2']) ? 1 : 0;
        $forUndergrad3 = isset($_POST['forUndergrad3']) ? 1 : 0;
        $forUndergrad4 = isset($_POST['forUndergrad4']) ? 1 : 0;
        $others = isset($_POST['others']) ? $_POST['others'] : null; // Capture 'others' input



        $copies = [];
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'copies-') === 0) {
                    $documentKey = str_replace('copies-', '', $key);
                    $copies[$documentKey] = intval($value); // Save the number of copies for each document
                }
            }


        // Automatically generate a studentNumber if it is empty
        if (empty($studentNumber)) {
            $studentNumber = 'NEMCO' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Ensure the student number is unique in the database
            do {
                $query = "SELECT COUNT(*) AS count FROM requests WHERE studentNumber = '$studentNumber'";
                $result = $conn->query($query);
                $row = $result->fetch_assoc();
                if ($row['count'] > 0) {
                    $studentNumber = 'NEMCO' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                } else {
                    break;
                }
            } while (true);
        }

        // Prices for documents
        $prices = array(
    'forEnrollment' => 200,
    'forBoardExam' => 200,
    'forReference' => 200,
    'forStudiesAbroad' => 200,
    'forTransfer' => 200,
    'forEmployment' => 200,
    'forRanking' => 200,
    'forReissuance' => 200,
    'forGoodmoral' => 35,
    'forGpa' => 35,
    'forRegistration' => 35,
    'forEarnedUnits' => 35,
    'forGraduating' => 35,
    'forGraduation' => 35,
    'forSpec' => 35,
    'formediumInstruction' => 35,
    'forCertification' => 30,
    'forAuthentication' => 60,
    'forDismissal' => 65,
    'forEnrollmentcert' => 35,
    'forOthers' => 25,
    'forGraduated2' => 0,
    'forGraduated4' => 0,
    'forUndergrad1' => 0,
    'forUndergrad2' => 0,
    'forUndergrad3' => 0,
    'forUndergrad4' => 0,
);

        // Calculate total price with copies
        $totalPrice = 0;
        foreach ($prices as $key => $price) {
            if (isset($_POST[$key]) && $_POST[$key]) {
                $numberOfCopies = isset($copies[$key]) ? $copies[$key] : 1;
                $totalPrice += $price * $numberOfCopies;
            }
        }

        // Handle file uploads
        $uploadDirectory = 'uploads/';
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        // Get the file names
        $letterRequest = $_FILES['letterRequest']['name'];
        $affidavitLoss = $_FILES['affidavitLoss']['name'];

        // Define the paths where the files will be saved
        $letterRequestPath = $uploadDirectory . basename($letterRequest);
        $affidavitLossPath = $uploadDirectory . basename($affidavitLoss);

        // Handle file upload for Letter Request
        if (!empty($letterRequest)) {
            if (!move_uploaded_file($_FILES['letterRequest']['tmp_name'], $letterRequestPath)) {
                $response['status'] = 'error';
                $response['message'] = 'Failed to upload Letter Request.';
                echo json_encode($response);
                exit;
            }
        }
        
        // Handle file upload for Affidavit of Loss
        if (!empty($affidavitLoss)) {
            if (!move_uploaded_file($_FILES['affidavitLoss']['tmp_name'], $affidavitLossPath)) {
                $response['status'] = 'error';
                $response['message'] = 'Failed to upload Affidavit of Loss.';
                echo json_encode($response);
                exit;
            }
        }

        // Insert data into database with file paths
        $transaction_code = substr(md5(uniqid(rand(), true)), 0, 8); // Shorter transaction code

        $copiesJson = json_encode($copies); // Convert the copies array to JSON

$sql = "INSERT INTO requests (
            studentNumber, firstName, middleName, lastName, suffix, program, 
            contactNumber, lastTermAttended, semester, forDismissal, forEnrollment, 
            forBoardExam, forReference, forStudiesAbroad, forTransfer, forEmployment, 
            forRanking, forGoodmoral, forCertification, forGpa, forRegistration, 
            forAuthentication, transaction_code, forReissuance, formediumInstruction, 
            forGraduation, forEnrollmentcert, forEarnedUnits, forGraduating, forSpec, 
            forOthers, payment, forGraduated2, forGraduated4, forUndergrad1, forUndergrad2, 
            forUndergrad3, forUndergrad4, letterRequest, affidavitLoss, copiesData, others)
        VALUES (
            '$studentNumber', '$firstName', '$middleName', '$lastName', '$suffix', 
            '$program', '$contactNumber', '$lastTermAttended', '$semester', 
            '$forDismissal', '$forEnrollment', '$forBoardExam', '$forReference', 
            '$forStudiesAbroad', '$forTransfer', '$forEmployment', '$forRanking', 
            '$forGoodmoral', '$forCertification', '$forGpa', '$forRegistration', 
            '$forAuthentication', '$transaction_code', '$forReissuance', '$formediumInstruction', 
            '$forGraduation', '$forEnrollmentcert', '$forEarnedUnits', '$forGraduating', 
            '$forSpec', '$forOthers', '$totalPrice', '$forGraduated2', '$forGraduated4', 
            '$forUndergrad1', '$forUndergrad2', '$forUndergrad3', '$forUndergrad4', 
            '$letterRequestPath', '$affidavitLossPath', '$copiesJson', '$others')";


        if ($conn->query($sql) === TRUE) {
            $response['status'] = 'success';
            $response['message'] = 'New record created successfully';
            $response['transaction_code'] = $transaction_code;
            $response['studentNumber'] = $studentNumber;
            $response['totalPrice'] = $totalPrice;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error: ' . $conn->error;
        }

        $conn->close();
    }

    echo json_encode($response);
}
?>
