<!-- social_site/feed.php -->
<?php
require 'db.php';
session_start();
$sql = "SELECT posts.id, users.username, content, media FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    echo "<div><h3>" . htmlspecialchars($row['username']) . "</h3>";
    echo "<p>" . htmlspecialchars($row['content']) . "</p>";
    if ($row['media']) echo "<img src='" . htmlspecialchars($row['media']) . "' width='200'><br>";
    echo "<a href='like.php?post_id={$row['id']}'>Like</a> | <a href='comment.php?post_id={$row['id']}'>Comment</a> | <a href='share.php?post_id={$row['id']}'>Share</a>";
    echo "</div><hr>";
}
?>


<!-- feed.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Feed</title>
    <link rel="stylesheet" href="futuristic_theme.css">
</head>
<body>
    
<!--<header>
    <h1>SocialSite</h1>
    <nav>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>
-->
<style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: 'Orbitron', sans-serif;
        background: black;
        overflow: auto; /* allow scrolling */
    }

    body::-webkit-scrollbar {
        width: 8px;
    }
    body::-webkit-scrollbar-thumb {
        background-color: #444;
        border-radius: 4px;
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

    .floating-text {
        position: absolute;
        top: 40%;
        width: 100%;
        text-align: center;
        font-size: 3rem;
        color: #fff;
        text-shadow: 0 0 20px #00f5ff, 0 0 40px #ff69f4;
        animation: float 6s ease-in-out infinite;
        z-index: 1;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(-10px);
        }
        50% {
            transform: translateY(10px);
        }
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
        z-index: 999;
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
        margin-top: 80px; /* header height + space */
        padding: 20px;
    }
    </style>
<script>
        let prevScrollPos = window.pageYOffset;
        window.onscroll = function () {
            const currentScrollPos = window.pageYOffset;
            const header = document.querySelector(".header");
            if (prevScrollPos > currentScrollPos) {
                header.style.top = "0";
            } else {
                header.style.top = "-70px";
            }
            prevScrollPos = currentScrollPos;
        };
    </script>

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
    <div class="feed-container">
        <div class="post">
            <div class="post-header">
                <img src="uploads/profile1.jpg" alt="User">
                <span>@johndoe</span>
            </div>
            <img class="post-img" src="uploads/post1.jpg" alt="Post">
            <div class="post-content">
                <div class="post-actions">
                    <a href="#">Like</a>
                    <a href="#">Comment</a>
                    <a href="#">Share</a>
                </div>
                <p>Had an amazing time at the beach today! ☀️🌊</p>
            </div>
        </div>

        <div class="post">
            <div class="post-header">
                <img src="uploads/profile2.jpg" alt="User">
                <span>@alice</span>
            </div>
            <img class="post-img" src="uploads/post2.jpg" alt="Post">
            <div class="post-content">
                <div class="post-actions">
                    <a href="#">Like</a>
                    <a href="#">Comment</a>
                    <a href="#">Share</a>
                </div>
                <p>New artwork just dropped! 🎨✨</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>