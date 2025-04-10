<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "records_db1";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if download parameter is set and file is specified
if (isset($_GET['download']) && $_GET['download'] == 'true' && isset($_GET['file'])) {
    $file = 'uploads/' . basename($_GET['file']);  // Ensure the file is in the uploads folder
    
    // Check if file exists
    if (file_exists($file)) {
        // Get the file's MIME type
        $mimeType = mime_content_type($file);

        // Set appropriate headers to force the file to be downloaded
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file)); // Set content length
        
        // Output the file to be downloaded
        readfile($file);
        exit;
    } else {
        // If the file doesn't exist, show an error message
        echo "File not found.";
    }
} else {
    // If no download parameter or file is set, proceed to generate documents
    if (isset($_GET['studentNumber']) && isset($_GET['document_type'])) {
        $student_number = $_GET['studentNumber'];
        $document_type = $_GET['document_type'];

        // Restricted documents
        $restricted_documents = ['Graduated2', 'Graduated4', 'Undergrad1', 'Undergrad2', 'Undergrad3', 'Undergrad4', 'authentication', 'others', 'dismissal', 'cav' ];

        if (in_array($document_type, $restricted_documents)) {
            // Pass the message to admin dashboard for display in modal
            header("Location: admin_dashboard.php?modalMessage=This cannot be generated.#requestListSection");
            exit;
        }

        // Handle document generation for other document types
        switch ($document_type) {
            case 'goodmoralcert':
                header("Location: certificate_of_goodmoral.php?studentNumber=$student_number");
                break;
            case 'enrollmentcert':
                header("Location: certificate_of_enrollment.php?studentNumber=$student_number");
                break;
            case 'certification':
                header("Location: certificate_of_certification.php?studentNumber=$student_number");
                break;
            case 'gpa':
                header("Location: certificate_of_gpa.php?studentNumber=$student_number");
                break;
            case 'registration':
                header("Location: registration.php?studentNumber=$student_number");
                break;
            case 'graduation':
                header("Location: certificate_of_graduation.php?studentNumber=$student_number");
                break;
            case 'graduating':
                header("Location: certificate_of_graduating.php?studentNumber=$student_number");
                break;
            case 'sju':
                header("Location: certificate_for_sju.php?studentNumber=$student_number");
                break;
            case 'mti':
                header("Location: certificate_of_mti.php?studentNumber=$student_number");
                break;
            case 'earnedUnits':
                header("Location: certificate_of_earnedUnits.php?studentNumber=$student_number");
                break;
            case 'boardExam':
                header("Location: board_exam.php?studentNumber=$student_number");
                break;
            case 'transfer':
                header("Location: transfer.php?studentNumber=$student_number");
                break;
            case 'employment':
                header("Location: employment.php?studentNumber=$student_number");
                break;
            case 'mediumInstruction':
                header("Location: mediumInstruction.php?studentNumber=$student_number");
                break;
            case 'Graduated2':
                header("Location: Graduated2.php?studentNumber=$student_number");
                break;
            case 'Graduated4':
                header("Location: Graduated4.php?studentNumber=$student_number");
                break;
            case 'Undergrad1':
                header("Location: Undergrad1.php?studentNumber=$student_number");
                break;
            case 'Undergrad2':
                header("Location: Undergrad2.php?studentNumber=$student_number");
                break;
            case 'Undergrad3':
                header("Location: Undergrad3.php?studentNumber=$student_number");
                break;
            case 'Undergrad4':
                header("Location: Undergrad4.php?studentNumber=$student_number");
                break;
            case 'reference':
                    header("Location: reference.php?studentNumber=$student_number");
                    break;
            case 'ranking':
                    header("Location: ranking.php?studentNumber=$student_number");
                    break;
            case 'sabroad':
                    header("Location: abroad.php?studentNumber=$student_number");
                    break;
            case 'dismissal':
                    header("Location: dismissal.php?studentNumber=$student_number");
                    break;
            case 'others':
                    header("Location: others.php?studentNumber=$student_number");
                    break;
            case 'authentication':
                    header("Location: authentication.php?studentNumber=$student_number");
                    break;
            case 'Enrollmentcert':
                    header("Location: Enrollmentcert.php?studentNumber=$student_number");
                    break;
                    case 'cav':
                        header("Location: cav.php?studentNumber=$student_number");
                        break;
            default:
                echo "Unknown document type.";
                exit;
        }
        exit;
    } else {
        echo "Invalid parameters.";
        exit;
    }
}

// Close the database connection
$conn->close();
?>
