<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<!-- background: black; -->
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

        .dropdown {
        position: relative;
        display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #222;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1001;
        }
        .dropdown-content a {
            color: #fff;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #333;
        }
        .dropdown:hover .dropdown-content {
            display: block;
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
        .header .right {
        padding-right: 50px;
        position: relative;
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
    <!-- Fallback space video background -->
    <video autoplay muted loop id="space-bg">
        <source src="assets/space_bg.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <div class="header">
        <div class="left">
            <a href="home.php">Home</a>
            <a href="post.php">New Post</a>
            <a href="your_feed.php">Your Feed</a>
            <a href="feed.php">Community Board</a>
            <a href="spaceminigame.php">Mini Game</a>
            <a href="shop.php">Your Shop</a>
        </div>
        <div class="right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="dropdown">
                    <a href="#">👤 Hello, <?= htmlspecialchars($_SESSION['username']) ?></a>
                    <div class="dropdown-content">
                        <a href="profile.php">Profile</a>
                        <a href="portfolio.php">Portfolio</a>
                        <a href="liked_posts.php">Liked Posts</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
              <a href="signup.php">Sign Up</a>
              <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>




    <div class="floating-text">Live Your Vision</div>
</body>
</html>
