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


<!-- profile.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="futuristic_theme.css">
</head>
<body>
<header>
    <h1>SocialSite</h1>
    <nav>
        <a href="feed.html">Home</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="profile-container">
    <div class="profile-pic">
        <img src="uploads/profile.jpg" alt="Profile Picture" width="150">
    </div>
    <div class="profile-details">
        <h2>@username</h2>
        <div class="profile-stats">
            <div><strong>34</strong> posts</div>
            <div><strong>128</strong> followers</div>
            <div><strong>87</strong> following</div>
        </div>
        <div class="profile-bio">
            <p>Name: Jane Doe</p>
            <p>Age: 26</p>
            <p>Bio: Explorer | Music Lover | Designer</p>
        </div>
    </div>
</div>
</body>
</html>