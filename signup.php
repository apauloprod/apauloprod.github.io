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
        // Check if username already exists
        $check_sql = "SELECT id FROM users WHERE username = $1";
        $check_result = pg_query_params($conn, $check_sql, array($username));

        if (pg_num_rows($check_result) > 0) {
            $error = "Username already exists.";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, password) VALUES ($1, $2)";
            $result = pg_query_params($conn, $insert_sql, array($username, $hashed_password));

            if ($result) {
                echo "User created successfully. You can now <a href='login.php'>log in</a>.";
                exit();
            } else {
                $error = "Error creating user.";
            }
        }
    }
}
?>

<form method="POST" action="signup.php">
    <label>Username:</label>
    <input type="text" name="username" required><br>
    <label>Password:</label>
    <input type="password" name="password" required><br>
    <input type="submit" value="Sign Up">
</form>

<?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>