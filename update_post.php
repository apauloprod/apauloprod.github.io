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
    $content = $_POST['content'] ?? '';

    if ($post_id && $content) {
        $sql = "UPDATE posts SET content = $1 WHERE id = $2 AND user_id = $3";
        $params = [$content, $post_id, $user_id];
        pg_query_params($conn, $sql, $params);
    }
}

header('Location: profile.php');
exit();
