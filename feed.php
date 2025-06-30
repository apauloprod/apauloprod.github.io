<?php
require 'db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Feed</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family: 'Orbitron', sans-serif;
            background: transparent;
            overflow-x: hidden;
            overflow-y: auto;
        }

        #space-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            z-index: -1;
        }

        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #111;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            z-index: 1000;
            transition: top 0.3s;
        }

        .header a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
        }

        .header .left, .header .right {
            display: flex;
            align-items: center;
        }

        .content {
            padding-top: 100px;
            padding-bottom: 50px;
            max-width: 800px;
            margin: auto;
            z-index: 10;
            position: relative;
            color: #fff;
        }

        .post {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 30px;
            padding: 20px;
            box-shadow: 0 0 10px #00f5ff;
        }

        .post h3 {
            margin-top: 0;
            color: #8de6d6;
        }

        .post img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 10px;
            display: block;
        }

        .post p {
            color: #fff;
            text-shadow: 0 0 10px #fff;
        }

        .post a {
            color: #8de6d6;
            margin-right: 10px;
            text-decoration: none;
        }

        body::-webkit-scrollbar {
            width: 8px;
        }

        body::-webkit-scrollbar-thumb {
            background-color: #444;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<video autoplay muted loop id="space-bg">
    <source src="assets/space_bg.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
</video>

<div class="header">
    <div class="left">
        <a href="home.php">Home</a>
        <a href="post.php">New Post</a>
        <a href="feed.php">Community Board</a>
        <a href="spaceminigame.php">Mini Game</a>
    </div>
    <div class="right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Profile</a>
            <span style="margin-left: 10px;">Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="signup.php">Sign Up</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="content">
    <?php
    $sql = "SELECT posts.id, users.username, content, media, zoom FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC";
    $result = pg_query($conn, $sql);
    while ($row = pg_fetch_assoc($result)) {
        $zoom = isset($row['zoom']) ? floatval($row['zoom']) : 1.0;
        echo "<div class='post'>";
        //echo "<h3>@" . htmlspecialchars($row['username']) . "</h3>";
        echo "<h3><a href='user.php?username=" . urlencode($row['username']) . "'>@"
     . htmlspecialchars($row['username']) . "</a></h3>";
        echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
        if (!empty($row['media'])) {
            echo "<img src='" . htmlspecialchars($row['media']) . "' alt='Post Image' style='transform: scale({$zoom});'>";
        }
        echo "<div class='post-actions'>";
        echo "<a href='like.php?post_id={$row['id']}'>Like</a>";
        echo "<a href='comment.php?post_id={$row['id']}'>Comment</a>";
        echo "<a href='share.php?post_id={$row['id']}'>Share</a>";
        echo "</div></div>";
    }
    ?>
</div>
</body>
</html>
