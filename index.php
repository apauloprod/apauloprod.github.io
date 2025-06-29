<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>



    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LYV - Join the Vision</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Orbitron', sans-serif;
            background: transparent;
            overflow: hidden;
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
        .centered {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #fff;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 2rem;
            border-radius: 12px;
        }
        .centered h1 {
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }
        .centered input[type="email"] {
            padding: 0.75rem;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            margin-bottom: 1rem;
            width: 250px;
        }
        .centered button {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            background-color: #8de6d6;
            color: #111;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .top-right {
            position: absolute;
            top: 10px;
            right: 20px;
            color: #fff;
        }
        .top-right a {
            color: #8de6d6;
            text-decoration: none;
            margin-left: 10px;
        }
    </style>

    <?php if (isset($_GET['msg'])): ?>
        <script>
            alert("<?= htmlspecialchars($_GET['msg']) ?>");
        <?php if (isset($_GET['redirect'])): ?>
            window.location.href = "login.php";
        <?php endif; ?>
        </script>
    <?php endif; ?>

</head>
<body>
    <video autoplay muted loop id="space-bg">
        <source src="assets/space_bg.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <div class="top-right">
        <a href="signup.php">Sign Up</a>
        <a href="login.php">Login</a>
    </div>

    <div class="centered">
        <h1>Do You Have A Vision?</h1>
        <p>Subscribe to get updates about our growing community.</p>
        <form method="POST" action="subscribe.php">
            <input type="email" name="email" placeholder="Your Email Address" required><br>
            <button type="submit">Enter Site</button>
        </form>
    </div>
</body>
</html>
