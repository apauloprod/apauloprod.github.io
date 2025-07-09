<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: feed.php");
    exit();
}

$follower_id = $_SESSION['user_id'];
$followed_id = $_POST['followed_id'] ?? null;
$action = $_POST['action'] ?? '';

if (!$followed_id || $followed_id == $follower_id) {
    header("Location: feed.php");
    exit();
}

if ($action === 'follow') {
    $sql = "INSERT INTO follows (follower_id, followed_id) VALUES ($1, $2) ON CONFLICT DO NOTHING";
    pg_query_params($conn, $sql, [$follower_id, $followed_id]);
} elseif ($action === 'unfollow') {
    $sql = "DELETE FROM follows WHERE follower_id = $1 AND followed_id = $2";
    pg_query_params($conn, $sql, [$follower_id, $followed_id]);
}

header("Location: user.php?username=" . urlencode($_GET['username'] ?? ''));
exit();
