<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'] ?? null;

    if ($post_id) {
        // Optionally delete associated media file
        $mediaQuery = pg_query_params($conn, "SELECT media FROM posts WHERE id = $1 AND user_id = $2", [$post_id, $user_id]);
        if ($mediaRow = pg_fetch_assoc($mediaQuery)) {
            $mediaPath = $mediaRow['media'];
            if ($mediaPath && file_exists($mediaPath)) {
                unlink($mediaPath);
            }
        }

        $sql = "DELETE FROM posts WHERE id = $1 AND user_id = $2";
        pg_query_params($conn, $sql, [$post_id, $user_id]);
    }
}

header('Location: profile.php');
exit();
