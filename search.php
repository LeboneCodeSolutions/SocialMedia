<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "social_media_db";
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">

</head>
<body>
    
<div class='table-1'>

    <?php
    if(isset($_GET['search'])){
        $filtervalues = $_GET['search'];
        $filtervalues = $conn->real_escape_string($filtervalues); // Sanitize input to prevent SQL injection
        $query = "SELECT * FROM user_info WHERE CONCAT(f_name, l_name, email_address) LIKE '%$filtervalues%'";
        $query_run = mysqli_query($conn, $query);

        if(mysqli_num_rows($query_run) > 0){
            while($items = mysqli_fetch_assoc($query_run)){  
                ?>
                  
            <div class='send-message-container'>
                    <h2> <?= htmlspecialchars($items['email_address']); ?></h2>
                    <a href="view-profile.php?email=<?= urlencode($items['email_address']); ?>">
                    <p class='send-message-email'>View Profile</p>
</a>

            <form>
                    <textarea class='send-message-textarea'> </textarea>
                    <br>
                    <button class='send-message-btn' type='submit'>Send Message</button>
            </form>
            </div>
                <?php
            }
        } else {
            ?>
                <p class='user-not-found-paragraph'>User Not Found!</p>
            <?php
        }
    }
    ?>
</div>

</body>
</html>
