<?php
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filename = $_FILES["profile_img"]["name"];
    $tempname = $_FILES["profile_img"]["tmp_name"];
    $folder = "./image/" . $filename;

    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $email_address = $_POST['email_address'];
    $psswrd = $_POST['psswrd'];

    // Check if email exists
    $select = mysqli_query($conn, "SELECT email_address FROM user_info WHERE email_address = '".$email_address."'") or exit(mysqli_error($conn));
    if (mysqli_num_rows($select)) {
        header('Location: register.php');
        exit(0);
    }
    // Hash the password
    $hashed_password = password_hash($psswrd, PASSWORD_DEFAULT);

    // Insert into the social_media_db
    $sql = "INSERT INTO user_info (profile_img, f_name, l_name, email_address, passwrd) 
            VALUES ('$filename','$f_name', '$l_name', '$email_address', '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        // Ensure the directory exists
        if (!is_dir('./image/')) {
            mkdir('./image/', 0777, true);
        }
        // Upload the image
        if (move_uploaded_file($tempname, $folder)) {
             header('Location: login.php');
        exit(0);
        } else {
            echo "<h3>&nbsp; Failed to upload image!</h3>";
        }
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Close the connection
$conn->close();
?>
