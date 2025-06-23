<!-- social_site/feed.php -->
<?php
require 'db.php';
$sql = "SELECT posts.id, users.username, content, media FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC";
$result = pg_query($conn, $sql);
while ($row = pg_fetch_assoc($result)) {
    echo "<div><h3>" . htmlspecialchars($row['username']) . "</h3>";
    echo "<p>" . htmlspecialchars($row['content']) . "</p>";
    if ($row['media']) echo "<img src='" . htmlspecialchars($row['media']) . "' width='200'><br>";
    echo "<a href='like.php?post_id={$row['id']}'>Like</a> | <a href='comment.php?post_id={$row['id']}'>Comment</a> | <a href='share.php?post_id={$row['id']}'>Share</a>";
    echo "</div><hr>";
}
?>