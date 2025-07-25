<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$comment_id = $_POST['comment_id'] ?? null;

if (!$comment_id || !is_numeric($comment_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid comment ID']);
    exit();
}

// Make sure the comment belongs to the user before deleting
$sql = "DELETE FROM comments WHERE id = $1 AND user_id = $2";
$result = pg_query_params($conn, $sql, [$comment_id, $user_id]);

if ($result && pg_affected_rows($result) > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete comment or no permission']);
}
?>
