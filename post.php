<!-- social_site/post.php -->
<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) exit();
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $media = '';
    if (isset($_FILES['media'])) {
        $target = 'uploads/' . basename($_FILES['media']['name']);
        move_uploaded_file($_FILES['media']['tmp_name'], $target);
        $media = $target;
    }
    $sql = "INSERT INTO posts (user_id, content, media) VALUES ($1, $2, $3)";
    $params = array($user_id, $content, $media);
    pg_query_params($conn, $sql, $params);
    echo "Post uploaded.";
}
?>
<form method="POST" enctype="multipart/form-data">
    Content: <textarea name="content"></textarea><br>
    Media (Image/Video): <input type="file" name="media"><br>
    <input type="submit" value="Post">
</form>