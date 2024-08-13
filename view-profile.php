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
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    
<div class='profile-container'>

    <?php
    if(isset($_GET['email'])){
        $email = $conn->real_escape_string($_GET['email']); // Sanitize input to prevent SQL injection
        $query = "SELECT * FROM user_info WHERE email_address = '$email'";
        $query_run = mysqli_query($conn, $query);

        if(mysqli_num_rows($query_run) > 0){
            $user = mysqli_fetch_assoc($query_run);
            ?>
            <div class='container-user-profile'>
                <h1 class='f_name_l_name'><?= htmlspecialchars($user['f_name']) . " " . htmlspecialchars($user['l_name']); ?></h1>
                <p class='user_profile_email'>Email: <?= htmlspecialchars($user['email_address']); ?></p>
           
           
           
           
           
          

            <h2>User Timeline</h2>
            <div class="user-timeline">
            <?php
                // Query to get the user's posts
                $posts_query = "SELECT post_description, post_date FROM posts WHERE email_address = '$email' ORDER BY post_date DESC";
                $posts_run = mysqli_query($conn, $posts_query);

                if(mysqli_num_rows($posts_run) > 0){
                    while($post = mysqli_fetch_assoc($posts_run)){
                        ?>
                        <div class="timeline-post">    <p>Posted On: <?= htmlspecialchars($post['post_date']); ?></p>
                            <p><?= htmlspecialchars($post['post_description']); ?></p>
                        
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No posts available.</p>";
                }
            ?>
            </div>
            <?php
        } else {
            echo "<p>User not found.</p>";
        }
    } else {
        echo "<p>No email provided.</p>";
    }
    ?>
</div>
</div>
</body>
</html>
