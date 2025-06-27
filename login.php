<!-- social_site/login.php -->
<?php
require 'db.php';

$error = "";


session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT id, password FROM users WHERE username = $1";
    $result = pg_query_params($conn, $sql, array($username));
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username;
            header("Location: home.php");
            exit();
        }
    }
    echo "Login failed.";
}
?>




<html>
<head>
    <title>Login</title>
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
    </style>
</head>
<body>
        <!-- Fallback space video background -->
    <video autoplay muted loop id="space-bg">
        <source src="assets/space_bg.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <div class="centered">
        <div class="landing-container">
            <h1>Log In</h1>
            <?php if (!empty($error)) echo "<p style='color: pink;'>$error</p>"; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required><br><br>
                <input type="password" name="password" placeholder="Password" required><br><br>
                <input type="submit" value="Log In" class="btn">
            </form>
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
</div>

</body>
</html>
