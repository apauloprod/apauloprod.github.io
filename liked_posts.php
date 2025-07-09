<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch liked posts
$sql = "
    SELECT posts.id, users.username, posts.content, posts.media
    FROM likes
    JOIN posts ON likes.post_id = posts.id
    JOIN users ON posts.user_id = users.id
    WHERE likes.user_id = $1
    ORDER BY posts.id DESC
";

$result = pg_query_params($conn, $sql, [$user_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Liked Posts</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Orbitron', sans-serif;
            background: black;
            color: #8de6d6;
            padding-top: 80px;
        }
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #111;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            z-index: 1000;
        }
        .header a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
        }
        .header .left, .header .right {
            display: flex;
            align-items: center;
        }
        h1 {
            text-align: center;
            text-shadow: 0 0 20px #00f5ff;
        }
        .post {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin: 30px auto;
            padding: 20px;
            box-shadow: 0 0 10px #00f5ff;
            max-width: 600px;
        }
        .post img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }
        .post .actions {
            margin-top: 10px;
        }
        .post .actions a {
            color: #8de6d6;
            margin-right: 10px;
            text-decoration: none;
        }
        .glow-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: 1px solid #8de6d6;
            color: #8de6d6;
            background-color: transparent;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            margin: 2rem auto;
            display: block;
            width: fit-content;
            text-shadow: 0 0 8px #8de6d6;
            transition: background-color 0.3s, color 0.3s;
        }
        .glow-button:hover {
            background-color: #8de6d6;
            color: #000;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="left">
        <a href="home.php">Home</a>
        <a href="post.php">New Post</a>
        <a href="feed.php">Community Board</a>
        <a href="spaceminigame.php">Mini Game</a>
    </div>
    <div class="right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Profile</a>
            <span style="margin-left: 10px; padding-right: 10px;">Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <?php else: ?>
            <a href="signup.php">Sign Up</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<h1>❤️ Liked Posts</h1>
<?php while ($row = pg_fetch_assoc($result)): ?>
    <div class="post">
        <h3>@<?= htmlspecialchars($row['username']) ?></h3>
        <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
        <?php if (!empty($row['media'])): ?>
            <img src="<?= htmlspecialchars($row['media']) ?>" alt="Post Image">
        <?php endif; ?>
        <div class="actions">
            <a href="like.php?post_id=<?= $row['id'] ?>">❤️ Unlike</a>
            <a href="comment.php?post_id=<?= $row['id'] ?>">💬 Comment</a>
            <a href="share.php?post_id=<?= $row['id'] ?>">🔗 Share</a>
        </div>
    </div>
<?php endwhile; ?>

<a href="profile.php" class="glow-button">⬅ Back to Profile</a>
</body>
</html>
