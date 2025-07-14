<?php
require 'db.php';
session_start();

$pending_user = $_SESSION['pending_user'] ?? null;
$error = '';
$success = '';

if (!$pending_user) {
    header("Location: signup.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_code = trim($_POST['code']);

    $sql = "SELECT verification_code FROM users WHERE username = $1";
    $result = pg_query_params($conn, $sql, [$pending_user]);

    if ($row = pg_fetch_assoc($result)) {
        $stored_code = $row['verification_code'];

        if ($entered_code === $stored_code) {
            $update_sql = "UPDATE users SET email_verified = true, verification_code = NULL WHERE username = $1";
            pg_query_params($conn, $update_sql, [$pending_user]);

            unset($_SESSION['pending_user']);
            $_SESSION['verified_message'] = "Email verified successfully!";
            header("Location: login.php");
            exit();
        } else {
            $error = "Incorrect verification code.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Email</title>
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
        input {
            padding: 0.75rem;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            margin: 1rem 0;
            width: 200px;
        }
        input[type="submit"] {
            background-color: #8de6d6;
            color: #111;
            cursor: pointer;
        }
        .message {
            color: lightgreen;
        }
        .error {
            color: pink;
        }
    </style>
</head>
<body>
<video autoplay muted loop id="space-bg">
    <source src="assets/space_bg.mp4" type="video/mp4">
</video>

<div class="centered">
    <h1>Verify Your Email</h1>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
        <input type="text" name="code" placeholder="Enter 6-digit code" required><br>
        <input type="submit" value="Verify">
    </form>
</div>
</body>
</html>
