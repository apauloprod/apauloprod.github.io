<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT posts.id, posts.content, posts.media, users.username
    FROM posts
    JOIN users ON posts.user_id = users.id
    WHERE posts.user_id IN (
        SELECT followed_id FROM follows WHERE follower_id = $1
    )
    ORDER BY posts.id DESC
";
$result = pg_query_params($conn, $sql, [$user_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Feed</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Orbitron', sans-serif;
            background: black;
            color: #8de6d6;
            padding-top: 80px;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #222;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1001;
        }
        .dropdown-content a {
            color: #fff;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #333;
        }
        .dropdown:hover .dropdown-content {
            display: block;
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
        .header .right {
            padding-right: 20px;
            position: relative;
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
        .actions {
            margin-top: 10px;
        }
        .actions a {
            color: #8de6d6;
            margin-right: 10px;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="left">
        <a href="home.php">Home</a>
        <a href="post.php">New Post</a>
        <a href="your_feed.php">Your Feed</a>
        <a href="feed.php">Community Board</a>
        <a href="spaceminigame.php">Mini Game</a>
        <a href="shop.php">Your Shop</a>
                <form action="search.php" method="GET" class="search-form" style="margin-left: 20px;">
    <input type="text" name="q" placeholder="Search users, posts, #tags" 
           style="padding: 5px 10px; border-radius: 6px; border: none;">
    <button type="submit" style="padding: 5px 10px; background-color: #8de6d6; color: black; border-radius: 6px; border: none;">
        🔍
    </button>
</form>
    </div>
    <div class="right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="dropdown">
                    <a href="#">👤 Hello, <?= htmlspecialchars($_SESSION['username']) ?></a>
                    <div class="dropdown-content">
                        <a href="profile.php">Profile</a>
                        <a href="portfolio.php">Portfolio</a>
                        <a href="liked_posts.php">Liked Posts</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
              <a href="signup.php">Sign Up</a>
              <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
</div>

<h1>🚀 Your Feed</h1>
<?php while ($row = pg_fetch_assoc($result)): ?>
    <div class="post">
        <h3>@<?= htmlspecialchars($row['username']) ?></h3>
        <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
        <?php if (!empty($row['media'])): ?>
            <img src="<?= htmlspecialchars($row['media']) ?>" alt="Post Image">
        <?php endif; ?>
        <div class="actions">
            <a href="like.php?post_id=<?= $row['id'] ?>">❤️ Like</a>
            <a href="comment.php?post_id=<?= $row['id'] ?>">💬 Comment</a>
            <a href="share.php?post_id=<?= $row['id'] ?>">🔗 Share</a>
        </div>
    </div>
<?php endwhile; ?>
</body>
</html>
