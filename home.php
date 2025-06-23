<!-- social_site/home.php -->
<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
?>