<?php
$host = 'localhost';
$port = "5432";
$db = 'socialsite';
$user = 'postgres';
$pass = 'Visionary88!';
$conn = pg_connect("host=$host port=$port dbname=$db user=$user password=$pass");
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}
