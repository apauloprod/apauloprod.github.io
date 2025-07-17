<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) exit();
$user_id = $_SESSION['user_id'];

$error = "";

// Fetch current data
$sql = "SELECT username, name, age, bio, profile_pic FROM users WHERE id = $1";
$result = pg_query_params($conn, $sql, [$user_id]);
$user = pg_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $age = trim($_POST['age']);
    $bio = trim($_POST['bio']);

    $profile_pic_path = $user['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = "uploads/profile_" . $user_id . "." . $ext;
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $filename);
        $profile_pic_path = $filename;
    }

    $update_sql = "UPDATE users SET name = $1, age = $2, bio = $3, profile_pic = $4 WHERE id = $5";
    $update_result = pg_query_params($conn, $update_sql, [$name, $age, $bio, $profile_pic_path, $user_id]);

    if ($update_result) {
        header("Location: profile.php");
        exit();
    } else {
        $error = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        body {
            background-color: black;
            font-family: 'Orbitron', sans-serif;
            color: #8de6d6;
            padding: 2rem;
            text-align: center;
        }
        form {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 10px #00f5ff;
            margin-top: 2rem;
        }
        input, textarea {
            display: block;
            margin: 1rem auto;
            padding: 0.5rem;
            width: 80%;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: none;
            border-radius: 8px;
        }
        input[type="submit"] {
            background-color: #8de6d6;
            color: black;
            cursor: pointer;
            transition: 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #00f5ff;
        }
        .error {
            color: pink;
        }
    </style>
</head>
<body>
    <h1>Edit Your Profile</h1>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($user['name']) ?>" required>
        <input type="number" name="age" placeholder="Age" value="<?= htmlspecialchars($user['age']) ?>">
        <textarea name="bio" placeholder="Bio" rows="5" required><?= htmlspecialchars($user['bio']) ?></textarea>
        <label style="color:#fff">Profile Picture:</label>
        <input type="file" name="profile_pic" accept="image/*">
        <input type="submit" value="Save Changes">
    </form>
</body>
</html>
