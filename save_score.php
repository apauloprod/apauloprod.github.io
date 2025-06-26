<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $score = (int) $_POST['score'];

    if (!empty($username) && $score >= 0) {
        $insert_sql = "INSERT INTO scores (username, score) VALUES ($1, $2)";
        $result = pg_query_params($conn, $insert_sql, [$username, $score]);

        if ($result) {
            header("Location: spaceminigame.php");
            exit();
        } else {
            echo "Error saving score.";
        }
    } else {
        echo "Invalid username or score.";
    }
} else {
    echo "Invalid request method.";
}
?>
