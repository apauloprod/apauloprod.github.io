<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) exit();
$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT username, name, age, bio, profile_pic FROM users WHERE id = $1";
$result = pg_query_params($conn, $sql, [$user_id]);
$user = pg_fetch_assoc($result);


$followers_result = pg_query_params($conn, "SELECT COUNT(*) FROM follows WHERE followed_id = $1", [$user_id]);
$followers_count = pg_fetch_result($followers_result, 0, 0);

$friends_result = pg_query($conn, "SELECT COUNT(*) FROM follows f1 JOIN follows f2 ON f1.follower_id = f2.followed_id AND f1.followed_id = f2.follower_id WHERE f1.followed_id = $user_id");
$friends_count = pg_fetch_result($friends_result, 0, 0);


// Fetch user's posts
$posts_sql = "SELECT id, content, media FROM posts WHERE user_id = $1 ORDER BY id DESC";
$posts_result = pg_query_params($conn, $posts_sql, [$user_id]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Profile</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: 'Orbitron', sans-serif;
        }
        #space-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            z-index: -1;
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
            z-index: 999;
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
        .dropdown {
            position: relative;
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
        .content {
            margin-top: 70px;
            padding: 20px;
            overflow-y: auto;
            height: calc(100vh - 70px);
            color: #8de6d6;
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
        .post textarea {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
            border-radius: 6px;
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            border: none;
        }
        .post form button {
            margin-top: 10px;
            padding: 0.5rem 1rem;
            background-color: #8de6d6;
            color: #000;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .post form button.delete {
            background-color: #ff4d4d;
            color: #fff;
        }
    </style>
</head>
<body>
<video autoplay muted loop id="space-bg">
    <source src="assets/space_bg.mp4" type="video/mp4">
</video>
<div class="header">
    <div class="left">
          <a href="home.php">Home</a>
          <a href="post.php">New Post</a>
          <a href="your_feed.php">Your Feed</a>
          <a href="feed.php">Community Board</a>
          <a href="spaceminigame.php">Mini Game</a>
          <a href="shop.php">Your Shop</a>
    </div>
    <div class="right">
        <div class="dropdown">
            <a href="#">👤 Hello, <?= htmlspecialchars($_SESSION['username']) ?></a>
            <div class="dropdown-content">
                <a href="profile.php">Profile</a>
                <a href="portfolio.php">Portfolio</a>
                <a href="liked_posts.php">Liked Posts</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <h1>Your Profile</h1>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p>
    <strong>
        <a href="friends_followers.php" style="color: #8de6d6; text-decoration: underline;">
            Followers:
        </a>
    </strong> <?= $followers_count ?>
    |
    <strong>
        <a href="friends_followers.php" style="color: #8de6d6; text-decoration: underline;">
            Friends:
        </a>
    </strong> <?= $friends_count ?>
    </p>
    <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?> <a href="edit_field.php?field=name">Edit</a></p>
    <p><strong>Age:</strong> <?= htmlspecialchars($user['age']) ?> <a href="edit_field.php?field=age">Edit</a></p>
    <p><strong>Bio:</strong><br><?= nl2br(htmlspecialchars($user['bio'])) ?> <a href="edit_field.php?field=bio">Edit</a></p>
    <p><strong>Profile Picture:</strong><br>
        <?php if ($user['profile_pic']): ?>
            <img src="<?= htmlspecialchars($user['profile_pic']) ?>" width="150" alt="Profile Picture"><br>
        <?php endif; ?>
        <a href="edit_field.php?field=profile_pic">Change Picture</a>
    </p>

    <h2>Your Posts</h2>
    <?php while ($post = pg_fetch_assoc($posts_result)): ?>
        <div class="post">
            <form method="POST" action="update_post.php">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <?php if ($post['media']): ?>
                    <img src="<?= htmlspecialchars($post['media']) ?>" alt="Post image">
                <?php endif; ?>
                <textarea name="content"><?= htmlspecialchars($post['content']) ?></textarea><br>
                <button type="submit">Update</button>
            </form>
            <form method="POST" action="delete_post.php" onsubmit="return confirm('Are you sure you want to delete this post?');">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <button type="submit" class="delete">Delete</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
