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
            display: flex;
            align-items: center;
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
        .profile-thumb {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
        }
        .comment-section {
            display: none;
            margin-top: 10px;
        }
        .comment-form input[type="text"] {
            width: 75%;
            padding: 8px;
            background: rgba(0,255,255,0.1);
            color: #8de6d6;
            border: 1px solid #00f5ff;
            border-radius: 8px;
            outline: none;
        }
        .comment-form button {
            padding: 8px 16px;
            margin-left: 10px;
            background-color: transparent;
            border: 1px solid #00f5ff;
            color: #8de6d6;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        .comment-form button:hover {
            background-color: #00f5ff;
            color: #000;
        }
        .comment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }
        .comment-text {
            flex-grow: 1;
        }
        .comment-delete {
            margin-left: 10px;
            color: red;
            cursor: pointer;
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
        <form action="search.php" method="GET" class="search-form" style="margin-left: 20px;">
            <input type="text" name="q" placeholder="Search users, posts, #tags" style="padding: 5px 10px; border-radius: 6px; border: none;">
            <button type="submit" style="padding: 5px 10px; background-color: #8de6d6; color: black; border-radius: 6px; border: none;">🔍</button>
        </form>
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
<?php
$sql = "SELECT posts.id, users.username, users.profile_pic, content, media, zoom,
       (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
       (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id AND likes.user_id = $1) > 0 AS user_liked,
       (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count
       FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC";
$result = pg_query_params($conn, $sql, [$user_id]);

while ($row = pg_fetch_assoc($result)) {
    $zoom = isset($row['zoom']) ? floatval($row['zoom']) : 1.0;
    $likeCount = $row['like_count'] ?? 0;
    $commentCount = $row['comment_count'] ?? 0;
    $liked = $row['user_liked'] === 't';
    $label = $liked ? "Unlike" : "Like";
    echo "<div class='post'>";
    echo "<h3>";
    if (!empty($row['profile_pic'])) {
        echo "<img src='" . htmlspecialchars($row['profile_pic']) . "' class='profile-thumb' alt='Profile'>";
    }
    echo "<a href='user.php?username=" . urlencode($row['username']) . "'>@" . htmlspecialchars($row['username']) . "</a></h3>";
    echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
    if (!empty($row['media'])) {
        echo "<img src='" . htmlspecialchars($row['media']) . "' alt='Post Image' style='transform: scale({$zoom});'>";
    }
    echo "<div class='post-actions'>";
    echo "<a href='#' class='like-button' data-post-id='{$row['id']}' data-liked='" . ($liked ? "true" : "false") . "' data-like-count='{$likeCount}'>{$label} ({$likeCount})</a>";
    echo "<a href='#' class='comment-toggle' data-id='{$row['id']}'>Comment ({$commentCount})</a>";
    echo "<a href='share.php?post_id={$row['id']}'>Share</a>";
    echo "</div>";
    echo "<div class='comment-section' id='comment-section-{$row['id']}'>";
    echo "<form class='comment-form' data-id='{$row['id']}'>";
    echo "<input type='text' name='comment' placeholder='Write a comment...'>";
    echo "<button type='submit'>Post</button>";
    echo "</form><div class='comment-list' id='comments-{$row['id']}'></div></div></div>";
}
?>
</div>
<script>

document.addEventListener('DOMContentLoaded', () => {
    // Like a comment
    document.addEventListener('click', async e => {
        if (e.target.classList.contains('comment-like')) {
            const commentId = e.target.dataset.commentId;
            const res = await fetch('like_comment.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `comment_id=${commentId}`
            });
            const data = await res.json();
            if (data.success) {
                e.target.textContent = `❤️ (${data.like_count})`;
            }
        }
    });

    // Delete a comment
    document.addEventListener('click', async e => {
        if (e.target.classList.contains('comment-delete')) {
            const commentId = e.target.dataset.commentId;
            if (confirm('Are you sure you want to delete this comment?')) {
                const res = await fetch('delete_comment.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `comment_id=${commentId}`
                });
                const data = await res.json();
                if (data.success) {
                    e.target.closest('.comment-item').remove();
                }
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const postId = this.getAttribute('data-post-id');
            const likeButton = this;
            fetch('like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'post_id=' + encodeURIComponent(postId)
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    likeButton.textContent = data.liked ? `Unlike (${data.like_count})` : `Like (${data.like_count})`;
                } else {
                    alert('Error: ' + data.message);
                }
            }).catch(err => {
                console.error('Fetch error:', err);
                alert('Network error');
            });
        });
    });

    document.querySelectorAll('.comment-toggle').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const postId = this.getAttribute('data-id');
            const section = document.getElementById(`comment-section-${postId}`);
            const isVisible = section.style.display === 'block';
            section.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) {
                const res = await fetch(`load_comments.php?post_id=${postId}`);
                const html = await res.text();
                document.getElementById(`comments-${postId}`).innerHTML = html;
            }
        });
    });

    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const postId = this.getAttribute('data-id');
            const commentInput = this.querySelector('input[name="comment"]');
            const comment = commentInput.value.trim();
            if (!comment) return;

            const res = await fetch('submit_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `post_id=${postId}&comment=${encodeURIComponent(comment)}`
            });

            if (res.ok) {
                const html = await fetch(`load_comments.php?post_id=${postId}`).then(r => r.text());
                document.getElementById(`comments-${postId}`).innerHTML = html;
                commentInput.value = '';
                const toggle = document.querySelector(`.comment-toggle[data-id='${postId}']`);
                const newCount = parseInt(toggle.textContent.match(/\d+/)?.[0] || 0) + 1;
                toggle.textContent = `Comment (${newCount})`;
            } else {
                alert("Failed to post comment.");
            }
        });
    });
});
</script>
</body>
</html>
