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
</head>
<body>
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
</body>
</html>
