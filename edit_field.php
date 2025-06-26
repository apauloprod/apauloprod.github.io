<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) exit();

$user_id = $_SESSION['user_id'];
$field = $_GET['field'] ?? '';
$allowed_fields = ['name', 'age', 'bio', 'profile_pic'];

if (!in_array($field, $allowed_fields)) {
    echo "Invalid field.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($field === 'profile_pic') {
        $upload_dir = 'uploads/';
        $file_path = $upload_dir . basename($_FILES['value']['name']);
        if (move_uploaded_file($_FILES['value']['tmp_name'], $file_path)) {
            $sql = "UPDATE users SET profile_pic = $1 WHERE id = $2";
            pg_query_params($conn, $sql, [$file_path, $user_id]);
        }
    } else {
        $value = trim($_POST['value']);
        $sql = "UPDATE users SET {$field} = $1 WHERE id = $2";
        pg_query_params($conn, $sql, [$value, $user_id]);
    }
    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit <?= htmlspecialchars($field) ?></title>
</head>
<body>
    <h1>Edit <?= htmlspecialchars(ucfirst($field)) ?></h1>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($field === 'profile_pic'): ?>
            <input type="file" name="value" required><br>
        <?php elseif ($field === 'bio'): ?>
            <textarea name="value" required rows="5" cols="40"></textarea><br>
        <?php elseif ($field === 'age'): ?>
            <input type="number" name="value" required><br>
        <?php else: ?>
            <input type="text" name="value" required><br>
        <?php endif; ?>
        <input type="submit" value="Update">
    </form>
    <p><a href="profile.php">Cancel</a></p>
</body>
</html>
