<?php
require 'db.php';
session_start();

//This tells the browser we are returning JSON
header('Content-Type: application/json');

//Stop if user is not logged in or no post_id is provided
if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);  // sanitize input

//Check if the user already liked the post
$check_like = pg_query_params($conn, "SELECT 1 FROM likes WHERE user_id = $1 AND post_id = $2", [$user_id, $post_id]);

if (pg_num_rows($check_like) > 0) {
    // User already liked, so remove it
    pg_query_params($conn, "DELETE FROM likes WHERE user_id = $1 AND post_id = $2", [$user_id, $post_id]);
    $liked = false;
} else {
    // Add like
    pg_query_params($conn, "INSERT INTO likes (user_id, post_id) VALUES ($1, $2)", [$user_id, $post_id]);
    $liked = true;
}

//Get updated like count
$count_result = pg_query_params($conn, "SELECT COUNT(*) FROM likes WHERE post_id = $1", [$post_id]);
$count_row = pg_fetch_row($count_result);
$like_count = intval($count_row[0]);

//Return valid JSON only
echo json_encode([
    'success' => true,
    'liked' => $liked,
    'like_count' => $like_count
]);
exit;
