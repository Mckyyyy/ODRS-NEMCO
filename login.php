<?php
// login.php
// Start session for login management
session_start();

// Redirect to admin_dashboard.php if already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    header("Location: admin_dashboard.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "records_db1";

// Create connection using MySQLi Object-Oriented
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_POST['login'])) {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    // Prepare statement to prevent SQL injection
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($pass, $row['password'])) {
            // Successful login
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $row['username'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // Invalid password
            $error = "Invalid username or password.";
        }
    } else {
        // Username not found
        $error = "Invalid username or password.";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    /* General body styles */
    body {
        background: linear-gradient(135deg, #e0f7fa, #80deea); /* Subtle blue gradient */
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Poppins', sans-serif; /* Clean modern font */
        margin: 0;
        overflow: hidden;
    }

    /* The container holding the login form */
    .login-container {
        background: linear-gradient(145deg, #ffffff, #e6e6e6); /* 3D effect with subtle gradient */
        padding: 40px;
        border-radius: 30px;
        box-shadow: 8px 8px 25px rgba(0, 0, 0, 0.2), -8px -8px 25px rgba(255, 255, 255, 0.8); /* 3D shadow effect */
        max-width: 450px;
        width: 100%;
        position: relative;
        overflow: hidden;
        z-index: 2;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-container:hover {
        transform: translateY(-5px); /* Subtle hover effect */
        box-shadow: 10px 10px 30px rgba(0, 0, 0, 0.3), -10px -10px 30px rgba(255, 255, 255, 0.9);
    }

    /* Decorative background shapes for 3D look */
    .login-container::before,
    .login-container::after {
        content: '';
        position: absolute;
        z-index: 1;
        filter: blur(50px);
    }

    .login-container::before {
        top: -60px;
        left: -60px;
        width: 220px;
        height: 220px;
        background: rgba(0, 123, 255, 0.15);
        border-radius: 50%;
    }

    .login-container::after {
        bottom: -60px;
        right: -60px;
        width: 180px;
        height: 180px;
        background: rgba(0, 123, 255, 0.15);
        border-radius: 50%;
    }

    /* Title styling */
    .login-title {
        text-align: center;
        margin-bottom: 25px;
        font-size: 32px;
        font-weight: bold;
        color: #343a40;
        position: relative;
        z-index: 2;
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Center and scale logo */
    img.logo {
        display: block;
        margin: 0 auto 25px;
        max-width: 100%;
        height: auto;
        max-height: 120px; /* Adjust for proportional fit */
        object-fit: contain;
        position: relative;
        z-index: 2;
        filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.2)); /* Add 3D depth to the image */
    }

    /* Labels */
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }

    /* Input fields */
    .form-control {
        border-radius: 50px;
        padding: 10px 20px;
        color: #495057;
        border: none;
        box-shadow: inset 2px 2px 8px rgba(0, 0, 0, 0.1), inset -2px -2px 8px rgba(255, 255, 255, 0.7); /* Inner 3D effect */
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        box-shadow: 0 0 12px rgba(0, 123, 255, 0.4);
    }

    /* Input icon styling */
    .input-group-text {
        background-color: #007bff;
        border: none;
        color: #fff;
        border-radius: 50px 0 0 50px;
        box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);
    }

    .input-group-text i {
        color: #fff;
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(145deg, #007bff, #0056b3); /* 3D gradient button */
        border: none;
        border-radius: 50px;
        padding: 12px 25px;
        font-weight: bold;
        color: #fff;
        box-shadow: 5px 5px 20px rgba(0, 91, 187, 0.3), -5px -5px 20px rgba(255, 255, 255, 0.8); /* Button shadow */
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-3px); /* Lift effect */
        box-shadow: 5px 5px 25px rgba(0, 91, 187, 0.4), -5px -5px 25px rgba(255, 255, 255, 1);
    }

    /* Error message styling */
    .alert {
        margin-top: 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 3px 3px 12px rgba(0, 0, 0, 0.1);
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
</head>

<body>
    <div class="login-container">
        <img src="nemco.jpg" alt="Login Logo" class="logo">
        <h2 class="login-title">Welcome to Admin Login</h2>
        <?php if (isset($error)) { echo "<div class='alert alert-danger text-center'>{$error}</div>"; } ?>
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required autofocus placeholder="Enter Username">
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter Password">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block w-100" name="login">Login</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
