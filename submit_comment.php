<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? '';
$comment = trim($_POST['comment'] ?? '');

if ($post_id && $comment) {
    $query = "INSERT INTO comments (post_id, user_id, comment) VALUES ($1, $2, $3)";
    $result = pg_query_params($conn, $query, [$post_id, $user_id, $comment]);

    if ($result) {
        http_response_code(200);
    } else {
        http_response_code(500);
    }
} else {
    http_response_code(400);
}
?>
