<!-- social_site/db.php -->
<?php
$host = 'localhost';
$db = 'socialsite';
$user = 'postgres';
$pass = 'Visionary88!';
$conn = pg_connect("host=$host dbname=$db user=$user password=$pass");
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}
?>