<?php
session_start();
ini_set('display_errors', 0);  // Turn off PHP error display in output
error_reporting(0);
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = intval($_SESSION['user_id']);

// Check if record exists
$res = pg_query_params($conn, "SELECT total_clicks FROM user_clicks WHERE user_id = $1", [$user_id]);

if ($res && pg_num_rows($res) > 0) {
    // Update total_clicks +1
    $update = pg_query_params($conn, "UPDATE user_clicks SET total_clicks = total_clicks + 1, last_updated = NOW() WHERE user_id = $1", [$user_id]);
} else {
    // Insert with 1 click
    $insert = pg_query_params($conn, "INSERT INTO user_clicks (user_id, total_clicks) VALUES ($1, 1)", [$user_id]);
}

// Fetch new total clicks
$res2 = pg_query_params($conn, "SELECT total_clicks FROM user_clicks WHERE user_id = $1", [$user_id]);
if ($res2 && pg_num_rows($res2) > 0) {
    $row = pg_fetch_assoc($res2);
    echo json_encode(['total_clicks' => intval($row['total_clicks'])]);
} else {
    echo json_encode(['error' => 'Failed to fetch total clicks']);
}
