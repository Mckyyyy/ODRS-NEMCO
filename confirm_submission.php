<?php
// confirm_submission.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Store form data in session to use later
    session_start();
    $_SESSION['form_data'] = $_POST;
    $_SESSION['form_files'] = $_FILES;
    // Redirect to confirmation page with SweetAlert
    header("Location: confirmation_page.php");
    exit();
}
?>
