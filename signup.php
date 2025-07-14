<!-- social_site/signup.php -->
<?php
require 'db.php';
session_start();

function generateVerificationCode() {
    return rand(100000, 999999);
}

// Consider using PHPMailer or SendGrid/Mailgun for production reliability
function sendVerificationEmail($to, $code) {
    $subject = "Verify your LYV account";
    $message = "Your verification code is: $code";
    $headers = "From: no-reply@liveyourvision.com";

    // For production use, configure a mail server or external SMTP
    mail($to, $subject, $message, $headers);
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    if (empty($username) || empty($password) || empty($email)) {
        $error = "All fields are required.";
    } else {
        $check_sql = "SELECT id FROM users WHERE username = $1 OR email = $2";
        $check_result = pg_query_params($conn, $check_sql, array($username, $email));

        if (pg_num_rows($check_result) > 0) {
            $error = "Username or email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_code = generateVerificationCode();

            $insert_sql = "INSERT INTO users (username, password, email, email_verified, verification_code) VALUES ($1, $2, $3, false, $4)";
            $result = pg_query_params($conn, $insert_sql, array($username, $hashed_password, $email, $verification_code));

            if ($result) {
                sendVerificationEmail($email, $verification_code);
                $_SESSION['pending_user'] = $username;
                $_SESSION['verification_message'] = "Verification code sent! Please check your email.";
                header("Location: verify_email.php");
                exit();
            } else {
                $error = "Error creating user.";
            }
        }
    }
}
?>

<!DOCTYPE html> 
<html>
<head>
    <title>Sign Up</title>
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
        <h1>Create Account</h1>
        <?php if (!empty($error)) echo "<p style='color: pink;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <input type="submit" value="Sign Up" class="btn">
        </form>
        <p>Already have an account? <a href="login.php">Log In</a></p>
    </div>
</div>
</body>
</html>