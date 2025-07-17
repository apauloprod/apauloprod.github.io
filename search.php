<?php
require 'db.php';
session_start();

$q = trim($_GET['q'] ?? '');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Results for "<?= htmlspecialchars($q) ?>"</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        body {
            font-family: 'Orbitron', sans-serif;
            background: black;
            color: #8de6d6;
            padding: 2rem;
        }
        .result {
            border: 1px solid #8de6d6;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 10px #00f5ff;
        }
        a {
            color: #8de6d6;
            text-decoration: none;
        }
    </style>
</head>
<body>
<h1>🔍 Search Results for: "<?= htmlspecialchars($q) ?>"</h1>

<?php if ($q): ?>

    <!-- Search Usernames -->
    <h2>👤 Users</h2>
    <?php
    $user_query = pg_query_params($conn, "SELECT username FROM users WHERE username ILIKE $1", ["%$q%"]);
    if (pg_num_rows($user_query) > 0) {
        while ($user = pg_fetch_assoc($user_query)) {
            echo "<div class='result'><a href='user.php?username=" . urlencode($user['username']) . "'>@{$user['username']}</a></div>";
        }
    } else {
        echo "<p>No users found.</p>";
    }
    ?>

    <!-- Search Posts -->
    <h2>📝 Posts</h2>
    <?php
    $post_query = pg_query_params($conn,
        "SELECT posts.id, content, media, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE content ILIKE $1 ORDER BY posts.id DESC",
        ["%$q%"]
    );
    if (pg_num_rows($post_query) > 0) {
        while ($post = pg_fetch_assoc($post_query)) {
            echo "<div class='result'>";
            echo "<strong>@<a href='user.php?username=" . urlencode($post['username']) . "'>{$post['username']}</a></strong><br>";
            echo "<p>" . htmlspecialchars($post['content']) . "</p>";
            if ($post['media']) {
                echo "<img src='" . htmlspecialchars($post['media']) . "' style='max-width:100%; border-radius:8px;'>";
            }
            echo "<br><a href='feed.php#post{$post['id']}'>View Post</a>";
            echo "</div>";
        }
    } else {
        echo "<p>No matching posts found.</p>";
    }
    ?>

<?php else: ?>
    <p>Please enter a search term.</p>
<?php endif; ?>

<a href="feed.php" style="color: #8de6d6; text-decoration: underline;">⬅ Back to Feed</a>
</body>
</html>
