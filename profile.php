<!-- social_site/profile.php -->
<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) exit();
$user_id = $_SESSION['user_id'];

// Fetch current user data using PostgreSQL
$sql = "SELECT username, name, age, bio, profile_pic FROM users WHERE id = $1";
$result = pg_query_params($conn, $sql, [$user_id]);
$user = pg_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="futuristic_theme.css">
</head>
<body>
    <h1>Your Profile</h1>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>

    <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?> 
        <a href="edit_field.php?field=name">Edit</a>
    </p>

    <p><strong>Age:</strong> <?= htmlspecialchars($user['age']) ?> 
        <a href="edit_field.php?field=age">Edit</a>
    </p>

    <p><strong>Bio:</strong><br><?= nl2br(htmlspecialchars($user['bio'])) ?> 
        <a href="edit_field.php?field=bio">Edit</a>
    </p>

    <p><strong>Profile Picture:</strong><br>
        <?php if ($user['profile_pic']): ?>
            <img src="<?= htmlspecialchars($user['profile_pic']) ?>" width="150" alt="Profile Picture"><br>
        <?php endif; ?>
        <a href="edit_field.php?field=profile_pic">Change Picture</a>
    </p>
</body>
</html>
