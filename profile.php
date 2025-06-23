<!-- social_site/profile.php -->
<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) exit();
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $bio = $_POST['bio'];
    $pic = '';
    if (isset($_FILES['profile_pic'])) {
        $target = 'uploads/' . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target);
        $pic = $target;
    }
    $sql = "UPDATE users SET name=$1, age=$2, bio=$3, profile_pic=$4 WHERE id=$5";
    $params = array($name, $age, $bio, $pic, $user_id);
    pg_query_params($conn, $sql, $params);
    echo "Profile updated.";
}
?>
<form method="POST" enctype="multipart/form-data">
    Name: <input type="text" name="name"><br>
    Age: <input type="number" name="age"><br>
    Bio: <textarea name="bio"></textarea><br>
    Profile Picture: <input type="file" name="profile_pic"><br>
    <input type="submit" value="Save">
</form>