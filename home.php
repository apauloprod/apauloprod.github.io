<!-- social_site/home.php -->
<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
?>



<!DOCTYPE html>
<html>
<head>
    <title>Home - LYV</title>
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
            Logged in as <strong><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></strong> | <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="signup.php">Sign Up</a> | <a href="login.php">Login</a>
        <?php endif; ?>
    </div>

    <h1>Live Your Vision</h1>
    <a href="profile.php">Edit Profile</a> | <a href="post.php">New Post</a> | <a href="feed.php">Community Board</a> | <a href="spaceminigame.php">Mini Game</a>
</body>
</html>



