<?php
$host = "localhost";
$port = "5432";
$dbname = "socialsite";
$user = "postgres";
$password = "Visionary88!";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    echo "<p style='color: red;'>❌ Connection failed: " . pg_last_error() . "</p>";
} else {
    echo "<p style='color: lightgreen;'>✅ Database connection successful!</p>";
}
?>