<!-- social_site/index.php -->

<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>LYV</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        .top-right {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .top-right a {
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="top-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            Logged in as <strong><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></strong>
        <?php else: ?>
            <a href="signup.php">Sign Up</a> | <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
    <h1>Live Your Vision</h1>
</body>
</html>




<h1>Welcome!</h1>
<a href="profile.html">Edit Profile</a> | <a href="post.php">New Post</a> | <a href="feed.html">Community Board</a> | <a href="logout.php">Logout</a> | <a href="spaceminigame.php">Mini Game</a>


