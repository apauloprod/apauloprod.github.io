<!-- social_site/signup.php -->
<?php
require 'db.php'; // PostgreSQL db connection

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        $check_sql = "SELECT id FROM users WHERE username = $1";
        $check_result = pg_query_params($conn, $check_sql, array($username));

        if (pg_num_rows($check_result) > 0) {
            $error = "Username already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, password) VALUES ($1, $2)";
            $result = pg_query_params($conn, $insert_sql, array($username, $hashed_password));

            if ($result) {
                echo "<p style='color: lightgreen;'>User created successfully. You can now <a href='login.php'>log in</a>.</p>";
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
</head>
<body>
<div class="landing-container">
    <h1>Create Account</h1>
    <?php if (!empty($error)) echo "<p style='color: pink;'>thereisanerror</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <input type="submit" value="Sign Up" class="btn">
    </form>
    <p>Already have an account? <a href="login.php">Log In</a></p>
</div>
</body>
</html>