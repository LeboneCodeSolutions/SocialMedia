<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = "";
$database = "social_media_db";

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if (isset($_POST['login-btn'])) {
    // Sanitize and collect form data
    $email_address = $conn->real_escape_string($_POST['email_address']);
    $psswrd = $conn->real_escape_string($_POST['psswrd']);

    // Prepare and execute query
    $stmt = $conn->prepare('SELECT * FROM user_info WHERE email_address = ?');
    $stmt->bind_param("s", $email_address);
    $stmt->execute();
    $stmt_result = $stmt->get_result();

    if ($stmt_result->num_rows > 0) {
        $data = $stmt_result->fetch_assoc();
        if (password_verify($psswrd, $data['passwrd'])) {
            // Successful login
            // Set session variables
            $_SESSION['id'] = $data['id'];
          
            $_SESSION['email_address'] = $data['email_address'];
            $_SESSION['message'] = 'You are logged in!';
            $_SESSION['type'] = 'alert-success';

            // Redirect to index.php (or any other page you want to redirect to)
            header('Location: index.php');
            exit();
        } else {
            // Password incorrect
            $_SESSION['message'] = 'Invalid Email or Password';
            $_SESSION['type'] = 'alert-danger';
            header('Location: login.php');
            exit();
        }
    } else {
        // No matching user found
        $_SESSION['message'] = 'Invalid Email or Password';
        $_SESSION['type'] = 'alert-danger';
        header('Location: login.php');
        exit();
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">

</head>

<body>
    <div class='login-container'>
    <form action="login.php" method="POST">

    
        <h1 class='login-heading'>Welcome Back</h1>

        <p class='login-msg'> Sign in below to access all features </p>

        <hr class='login-hr'>

        <input  class="enter-email"type="email" name="email_address" placeholder='Email Address' required> <br> 
<div class='forgot-pass-container'>
        <a class='forget-password' href="password_reset.php">Forgot?</a><br>
</div>  <input class='enter-password' type="password" name="psswrd" placeholder='Password' required>
        <br>
<input class='login-btn' type="submit" name="login-btn" value="Log In">
        <br> 
      
        
        <p class='sign-up-txt'>Don't have an account? <a href="register.html">Sign up </a> </p>

    </form>
</div>


</body>

</html>