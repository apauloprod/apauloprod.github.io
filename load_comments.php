<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['post_id'])) {
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_GET['post_id']);

// Fetch comments
$sql = "
    SELECT c.id, c.comment, c.user_id, u.username, u.profile_pic,
        (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id) AS like_count,
        EXISTS (
            SELECT 1 FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.user_id = $1
        ) AS user_liked
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.post_id = $2
    ORDER BY c.id ASC
";
$result = pg_query_params($conn, $sql, [$user_id, $post_id]);

while ($row = pg_fetch_assoc($result)) {
    $commentId = $row['id'];
    $username = htmlspecialchars($row['username']);
    $commentText = htmlspecialchars($row['comment']);
    $profilePic = htmlspecialchars($row['profile_pic'] ?? 'assets/default-avatar.png');
    $likeCount = (int) $row['like_count'];
    $userLiked = $row['user_liked'] === 't';

    echo "<div class='comment-item' data-id='{$commentId}'>";
    echo "  <img src='{$profilePic}' class='profile-thumb' alt='User'>";
    echo "  <div class='comment-text'><strong>@{$username}</strong>: {$commentText}</div>";
    echo "  <span class='comment-like' data-id='{$commentId}'>" .
            ($userLiked ? "♥" : "♡") . " ({$likeCount})</span>";
    if ($row['user_id'] == $user_id) {
        echo "  <span class='comment-delete' data-id='{$commentId}'>🗑️</span>";
    }
    echo "</div>";
}
?>
