<?php
require 'db.php';
session_start();

$username = $_GET['username'] ?? '';
if (!$username) {
    echo "User not specified.";
    exit();
}

$user_result = pg_query_params($conn, "SELECT id, name, bio, profile_pic FROM users WHERE username = $1", [$username]);
if (pg_num_rows($user_result) !== 1) {
    echo "User not found.";
    exit();
}
$user = pg_fetch_assoc($user_result);
$user_id = $user['id'];

$posts_result = pg_query_params($conn, "SELECT content, media FROM posts WHERE user_id = $1 ORDER BY id DESC", [$user_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($username) ?>'s Profile</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Orbitron', sans-serif;
            background: black;
            color: #8de6d6;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 2rem;
            text-align: center;
        }
        h1 {
            font-size: 2.5rem;
            text-shadow: 0 0 20px #00f5ff;
        }
        p {
            font-size: 1rem;
            text-shadow: 0 0 10px #fff;
        }
        .post {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 30px;
            padding: 20px;
            box-shadow: 0 0 10px #00f5ff;
        }
        .post img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }
        .glow-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: 1px solid #8de6d6;
            color: #8de6d6;
            background-color: transparent;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 1rem;
            text-shadow: 0 0 8px #8de6d6;
            transition: background-color 0.3s, color 0.3s;
        }
        .glow-button:hover {
            background-color: #8de6d6;
            color: #000;
        }
        .profile-pic {
            width: 150px;
            border-radius: 50%;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>@<?= htmlspecialchars($username) ?></h1>
        <?php if ($user['profile_pic']): ?>
            <img src="<?= htmlspecialchars($user['profile_pic']) ?>" class="profile-pic" alt="Profile Picture">
        <?php endif; ?>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><?= nl2br(htmlspecialchars($user['bio'])) ?></p>

        <h2>Posts</h2>
        <?php while ($post = pg_fetch_assoc($posts_result)): ?>
            <div class="post">
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <?php if (!empty($post['media'])): ?>
                    <img src="<?= htmlspecialchars($post['media']) ?>" alt="Post Media">
                <?php endif; ?>
            </div>
        <?php endwhile; ?>

        <a href="feed.php" class="glow-button">⬅ Back to Feed</a>
    </div>
</body>
</html>
