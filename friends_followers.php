<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Followers (people who follow me)
$followers_query = pg_query_params($conn, "
    SELECT u.username FROM users u
    JOIN follows f ON u.id = f.follower_id
    WHERE f.followed_id = $1
", [$user_id]);

// Friends (mutual follows)
$friends_query = pg_query_params($conn, "
    SELECT u.username FROM users u
    JOIN follows f1 ON u.id = f1.follower_id
    JOIN follows f2 ON u.id = f2.followed_id
    WHERE f1.followed_id = $1 AND f2.follower_id = $1
", [$user_id]);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Friends & Followers</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        body {
            background: black;
            font-family: 'Orbitron', sans-serif;
            color: #8de6d6;
            padding: 2rem;
        }
        h1, h2 {
            text-align: center;
            text-shadow: 0 0 10px #00f5ff;
        }
        ul {
            list-style: none;
            padding: 0;
            max-width: 600px;
            margin: auto;
        }
        li {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 5px #00f5ff;
        }
        a {
            color: #8de6d6;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>👥 Your Network</h1>

    <h2>🤝 Friends</h2>
    <ul>
        <?php while ($row = pg_fetch_assoc($friends_query)): ?>
            <li><a href="user.php?username=<?= htmlspecialchars($row['username']) ?>">@<?= htmlspecialchars($row['username']) ?></a></li>
        <?php endwhile; ?>
    </ul>

    <h2>👣 Followers</h2>
    <ul>
        <?php while ($row = pg_fetch_assoc($followers_query)): ?>
            <li><a href="user.php?username=<?= htmlspecialchars($row['username']) ?>">@<?= htmlspecialchars($row['username']) ?></a></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
