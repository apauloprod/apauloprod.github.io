<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please log in to see the feed.</p>";
    exit();
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Feed</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family: 'Orbitron', sans-serif;
            background: transparent;
            overflow-x: hidden;
            overflow-y: auto;
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
            transition: top 0.3s;
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

        .content {
            padding-top: 100px;
            padding-bottom: 50px;
            max-width: 800px;
            margin: auto;
            z-index: 10;
            position: relative;
            color: #fff;
        }

        .post {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 30px;
            padding: 20px;
            box-shadow: 0 0 10px #00f5ff;
        }

        .post h3 {
            margin-top: 0;
            color: #8de6d6;
        }

        .post img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 10px;
            display: block;
        }

        .post p {
            color: #fff;
            text-shadow: 0 0 10px #fff;
        }

        .post a {
            color: #8de6d6;
            margin-right: 10px;
            text-decoration: none;
        }

        body::-webkit-scrollbar {
            width: 8px;
        }

        body::-webkit-scrollbar-thumb {
            background-color: #444;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<video autoplay muted loop id="space-bg">
    <source src="assets/space_bg.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
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

<div class="content">
    <?php
    $sql = "SELECT posts.id, users.username, content, media, zoom, (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count, (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id AND likes.user_id = $1) > 0 AS user_liked FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC";
    $result = pg_query_params($conn, $sql, array($user_id));

    if (!$result) {
        echo "<p>Error loading posts: " . pg_last_error($conn) . "</p>";
        exit();
    }


    while ($row = pg_fetch_assoc($result)) {
        $zoom = isset($row['zoom']) ? floatval($row['zoom']) : 1.0;
        $likeCount = $row['like_count'] ?? 0;
        $liked = $row['user_liked'] === 't';
        $label = $liked ? "Unlike" : "Like";

        echo "<div class='post'>";
        //echo "<h3>@" . htmlspecialchars($row['username']) . "</h3>";
        echo "<h3><a href='user.php?username=" . urlencode($row['username']) . "'>@"
     . htmlspecialchars($row['username']) . "</a></h3>";
        echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
        if (!empty($row['media'])) {
            echo "<img src='" . htmlspecialchars($row['media']) . "' alt='Post Image' style='transform: scale({$zoom});'>";
        }
        echo "<div class='post-actions'>";

        //echo "<a href='like.php?post_id={$row['id']}'>Like</a>";
        //echo "({$likeCount}) ";  // show the number of likes here

        //echo "<a href='#' class='like-button' data-post-id='{$row['id']}'>Like</a>";
        //echo " <span class='like-count' id='like-count-{$row['id']}'>({$likeCount})</span>";
        //echo "<a href='#' class='like-button' data-post-id='{$row['id']}'>Like ({$likeCount})</a>";
        echo "<a href='#' class='like-button' data-post-id='{$row['id']}' data-liked='" . ($liked ? "true" : "false") . "' data-like-count='{$likeCount}'>{$label} ({$likeCount})</a>";



        
        echo "<a href='comment.php?post_id={$row['id']}'>Comment</a>";
        echo "<a href='share.php?post_id={$row['id']}'>Share</a>";
        echo "</div></div>";
    }
    ?>
</div>

<script>//this.textContent = data.liked ? 'Unlike' : 'Like';
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const postId = this.getAttribute('data-post-id');
            const likeButton = this;

            fetch('like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + encodeURIComponent(postId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    //const likeCountSpan = document.getElementById('like-count-' + postId);
                    //likeCountSpan.textContent = data.like_count;
                    //likeButton.textContent = data.liked ? 'Unlike' : 'Like';

                    likeButton.textContent = data.liked ? `Unlike (${data.like_count})` : `Like (${data.like_count})`;
                    
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('Network error');
            });
        });
    });
});
</script>


</body>
</html>
