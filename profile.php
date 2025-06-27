<!-- social_site/profile.php -->
<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) exit();
$user_id = $_SESSION['user_id'];

// Fetch current user data using PostgreSQL
$sql = "SELECT username, name, age, bio, profile_pic FROM users WHERE id = $1";
$result = pg_query_params($conn, $sql, [$user_id]);
$user = pg_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home - LYV</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: 'Orbitron', sans-serif;
            background: black;
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
            margin-top: 70px; /* adjust if your header is taller or shorter */
            padding: 20px;
            overflow-y: auto;
            height: calc(100vh - 70px); /* fill remaining space below header */
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
</head>
<body>
    <div class="header">
        <div class="left">
            <a href="home.php">Home</a>
            <a href="post.php">New Post</a>
            <a href="feed.php">Community Board</a>
            <a href="spaceminigame.php">Mini Game</a>
            <a href="shop.php">Your Shop</a>
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
        <h1>Your Profile</h1>
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>

        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?> 
            <a href="edit_field.php?field=name">Edit</a>
        </p>

        <p><strong>Age:</strong> <?= htmlspecialchars($user['age']) ?> 
            <a href="edit_field.php?field=age">Edit</a>
        </p>

        <p><strong>Bio:</strong><br><?= nl2br(htmlspecialchars($user['bio'])) ?> 
            <a href="edit_field.php?field=bio">Edit</a>
        </p>

        <p><strong>Profile Picture:</strong><br>
            <?php if ($user['profile_pic']): ?>
                <img src="<?= htmlspecialchars($user['profile_pic']) ?>" width="150" alt="Profile Picture"><br>
            <?php endif; ?>
            <a href="edit_field.php?field=profile_pic">Change Picture</a>
        </p>
    </div>
</body>
</html>
