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

// Get user data from session
$user_id = $_SESSION['id'];

// Fetch user details from the database
$stmt = $conn->prepare("SELECT email_address, profile_img FROM user_info WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_email = $user['email_address'];
    $profile_img = $user['profile_img'];
} else {
    $user_email = "Unknown";
    $profile_img = "default.png";
}

// Handle post creation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $create_post_txt = $_POST["create-post-text"];
    
    if (empty($create_post_txt)) {
        $post_error = 'Error: Post text cannot be empty.';
    } else {
        // Prepare and bind statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO posts (post_description, email_address) VALUES (?, ?)");
        $stmt->bind_param("ss", $create_post_txt, $user_email);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit();
        } else {
            $post_error = "OOPS! Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
}

// Fetch posts from the database
$sql = "SELECT post_description, email_address, post_date FROM posts ORDER BY post_date DESC";
$result = $conn->query($sql);

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zain:wght@200;300;400;700;800;900&display=swap" rel="stylesheet">
</head>

<body>
    <!--Nav Bar Section-->
    <header>
        <nav>
            <div class="search-container">
                <form action="search.php" method='GET'>
                    <input class="search" value='<?php if(isset($_GET['search'])) {echo $_GET['search']; }?>' type="text" placeholder="@username" name="search">
                </form>
            </div>

            <ul class="list-item">
                <div class="main-items">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Feed</a></li>
                    <li><a href="#">Friends</a></li>
                    <li><a href="#">Account <i class="fa fa-angle-down" aria-hidden="true"></i></a></li>
                </div>

                <div class="test-2">
                    <li><a href="#"> <i class="fa fa-cog fa-icon" aria-hidden="true"></i></a> </li>
                </div>

                <div class="test-3">
                    <li><a href="#"><i class="fa fa-user fa-icon" aria-hidden="true"></i></a></li>
                </div> 

                <div class="test-1">
                    <li><a href="logout.php"> <i class="fa-solid fa-right-from-bracket"></i></a></li>
                </div>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="user_information">
            <img class="user-img" src="<?php echo htmlspecialchars($profile_img); ?>" alt="Profile Image">
            <p class="user_handle">
                <?php echo htmlspecialchars($user_email); ?>
            </p>

            <div class="section-2">
                <ul class="list-item-1">
                    <li> <a href="#"><i class="fa-solid fa-house"></i> Feed</a></li>
                    <li> <a href="#"><i class="fa-solid fa-user-group"></i> Friends</a> </li>
                </ul>

                <hr class="hr-line">

                <!-- Display Post Error Message -->
                <?php if (isset($post_error)): ?>
                    <p class="error-message"><?php echo htmlspecialchars($post_error); ?></p>
                <?php endif; ?>

                <form class="create-post" action="index.php" method="POST">
                    <textarea name="create-post-text" class="text-area">Say Something...</textarea>
                    <br>
                    <button class="post-btn" type="submit"><strong>Post</strong></button>
                </form>
            </div>
        </div>

        <div class="feed-timeline">
            <h3 class="timeline-heading">Your Timeline:</h3>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="post">
                        <div class='post-description-container'> 
                             <p class="user-mail"><?php echo htmlspecialchars($row['email_address']); ?></p>
                            
                        <p class='post-description'><?php echo htmlspecialchars($row['post_description']); ?></p>
                      
                        <p class='post-date'><?php echo htmlspecialchars($row['post_date']); ?></p>
                        </div>
                 
                          <hr class='timeline-hr'> 
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No posts available.</p>
            <?php endif; ?>
        </div>

        <div class="who-to-follow">
            <h3 class="suggest-followers-heading">Suggested Followers</h3>
            <hr class="hr-line-1">
            <ul class="suggested-followers">
                <li><i class="fa fa-user" aria-hidden="true"></i> @scarletflloyd <button class="follow-btn">Follow</button></li>
                <li><i class="fa fa-user" aria-hidden="true"></i> @bibishelton <button class="follow-btn">Follow</button></li>
                <li><i class="fa fa-user" aria-hidden="true"></i>@coxbea <button class="follow-btn">Follow</button></li>
                <li> <i class="fa fa-user" aria-hidden="true"></i>@hanroken <button class="follow-btn">Follow</button></li>
            </ul>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>
