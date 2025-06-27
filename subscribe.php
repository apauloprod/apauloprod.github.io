<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Invalid email address.";
        header("Location: index.php?msg=" . urlencode($msg));
        exit;
    }

    $check_sql = "SELECT id FROM email_subscribers WHERE email = $1";
    $check_result = pg_query_params($conn, $check_sql, [$email]);

    if (pg_num_rows($check_result) > 0) {
        $msg = "You’re already subscribed! Please log in or create an account.";
        header("Location: index.php?msg=" . urlencode($msg) . "&redirect=1");
        exit;
    }

    $insert_sql = "INSERT INTO email_subscribers (email) VALUES ($1)";
    $insert_result = pg_query_params($conn, $insert_sql, [$email]);

    if ($insert_result) {
        $msg = "Thanks for subscribing! Please log in or create an account.";
        header("Location: index.php?msg=" . urlencode($msg) . "&redirect=1");
    } else {
        $msg = "There was an error. Please try again.";
        header("Location: index.php?msg=" . urlencode($msg));
    }
    exit;
}
header("Location: index.php");
exit;
