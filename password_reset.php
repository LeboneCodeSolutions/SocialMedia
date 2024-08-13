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

if (isset($_POST['reset-password'])) {
    // Forgot password functionality
    $email_address = $_POST['email_address'];
    $token = bin2hex(random_bytes(50)); // Generate a unique token
    $expiry_time = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token expires in 1 hour

    // Check if email exists
    $select = $conn->prepare("SELECT email_address FROM user_info WHERE email_address = ?");
    $select->bind_param("s", $email_address);
    $select->execute();
    $select->store_result();

    if ($select->num_rows > 0) {
        // Save token and expiry time to database
        $update = $conn->prepare("UPDATE user_info SET reset_token = ?, reset_expiry = ? WHERE email_address = ?");
        $update->bind_param("sss", $token, $expiry_time, $email_address);
        $update->execute();

        // Send reset email
        $reset_link = "http://yourdomain.com/password_reset.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n$reset_link";
        $headers = "From: no-reply@yourdomain.com";

        if (mail($email_address, $subject, $message, $headers)) {
            echo "Password reset link has been sent to your email address.";
        } else {
            echo "Failed to send email.";
        }
    } else {
        echo "No user found with that email address.";
    }

    $select->close();
    $update->close();

} elseif (isset($_POST['update-password'])) {
    // Reset password functionality
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Verify token and expiry time
    $select = $conn->prepare("SELECT email_address, reset_expiry FROM user_info WHERE reset_token = ?");
    $select->bind_param("s", $token);
    $select->execute();
    $result = $select->get_result();
    $user = $result->fetch_assoc();

    if ($user && strtotime($user['reset_expiry']) > time()) {
        // Update password
        $email_address = $user['email_address'];
        $update = $conn->prepare("UPDATE user_info SET passwrd = ?, reset_token = NULL, reset_expiry = NULL WHERE email_address = ?");
        $update->bind_param("ss", $hashed_password, $email_address);
        $update->execute();

        echo "Your password has been updated successfully.";
    } else {
        echo "Invalid or expired token.";
    }

    $select->close();
    $update->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body>
    <?php if (!isset($_GET['token'])): ?>
    <form action="password_reset.php" method="post">
        <h1>Forgot Password</h1>
        <label for="email_address">Email Address:</label>
        <input type="email" name="email_address" required> <br><br>
        <input type="submit" name="reset-password" value="Reset Password">
    </form>
    <?php else: ?>
    <form action="password_reset.php" method="post">
        <h1>Reset Password</h1>
        <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required> <br><br>
        <input type="submit" name="update-password" value="Update Password">
    </form>
    <?php endif; ?>
</body>
</html>
