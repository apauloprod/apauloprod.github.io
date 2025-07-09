<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['post_id'])) {
    header('Location: feed.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = $_GET['post_id'];

// Check if the user already liked the post
$check_like = pg_query_params($conn, "SELECT 1 FROM likes WHERE user_id = $1 AND post_id = $2", [$user_id, $post_id]);

if (pg_num_rows($check_like) > 0) {
    // Unlike the post
    pg_query_params($conn, "DELETE FROM likes WHERE user_id = $1 AND post_id = $2", [$user_id, $post_id]);
} else {
    // Like the post
    pg_query_params($conn, "INSERT INTO likes (user_id, post_id) VALUES ($1, $2)", [$user_id, $post_id]);
}

header("Location: feed.php");
exit();
