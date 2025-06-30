<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) exit();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $scale = isset($_POST['zoom_scale']) ? floatval($_POST['zoom_scale']) : 1.0;
    $media = '';

    if (isset($_FILES['media']) && $_FILES['media']['size'] > 0) {
        $target = 'uploads/' . basename($_FILES['media']['name']);
        move_uploaded_file($_FILES['media']['tmp_name'], $target);

        $imageType = exif_imagetype($target);
        if (in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
            $srcImage = ($imageType == IMAGETYPE_JPEG) ? imagecreatefromjpeg($target) : imagecreatefrompng($target);
            $resized = imagecreatetruecolor(1080, 1080);

            $srcW = imagesx($srcImage);
            $srcH = imagesy($srcImage);
            $zoomedSize = intval(1080 / $scale);
            $srcX = max(0, ($srcW - $zoomedSize) / 2);
            $srcY = max(0, ($srcH - $zoomedSize) / 2);
            $cropSize = min($zoomedSize, $srcW, $srcH);

            imagecopyresampled($resized, $srcImage, 0, 0, $srcX, $srcY, 1080, 1080, $cropSize, $cropSize);

            ($imageType == IMAGETYPE_JPEG) ? imagejpeg($resized, $target) : imagepng($resized, $target);
            imagedestroy($srcImage);
            imagedestroy($resized);
        }

        $media = $target;
    }

    $sql = "INSERT INTO posts (user_id, content, media, zoom) VALUES ($1, $2, $3, $4)";
    $params = array($user_id, $content, $media, $scale);
    pg_query_params($conn, $sql, $params);
    header("Location: feed.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Orbitron', sans-serif;
            background: transparent;
            overflow-x: hidden;
        }
        #space-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            z-index: -1;
        }
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #111;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            z-index: 1000;
        }
        .header a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
        }
        .header .left, .header .right {
            display: flex;
            align-items: center;
        }
        .content {
            margin-top: 100px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            color: #fff;
            text-shadow: 0 0 10px #00f5ff;
            padding: 2rem;
            text-align: center;
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 1rem;
            padding: 1rem;
            font-size: 1rem;
            border-radius: 8px;
            border: none;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .glow-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            margin: 0.5rem;
            border: none;
            border-radius: 8px;
            background-color: transparent;
            color: #8de6d6;
            font-size: 1rem;
            cursor: pointer;
            text-align: center;
            border: 1px solid #8de6d6;
            text-shadow: 0 0 8px #8de6d6;
            transition: background-color 0.3s, color 0.3s;
        }
        .glow-button:hover {
            background-color: #8de6d6;
            color: #000;
        }
        .preview-container {
            position: relative;
            width: 300px;
            height: 300px;
            margin: 1rem auto;
            overflow: hidden;
        }
        .preview {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s;
        }
        .upload-label {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: 1px solid #8de6d6;
            cursor: pointer;
            margin-bottom: 1rem;
            color: #8de6d6;
            background-color: transparent;
            text-shadow: 0 0 8px #8de6d6;
        }
        .upload-label:hover {
            background-color: #8de6d6;
            color: #000;
        }
        #media-input {
            display: none;
        }
    </style>
</head>
<body>
<video autoplay muted loop id="space-bg">
    <source src="assets/space_bg.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
</video>
<div class="header">
    <div class="left">
        <a href="home.php">Home</a>
        <a href="post.php">New Post</a>
        <a href="feed.php">Community Board</a>
        <a href="spaceminigame.php">Mini Game</a>
    </div>
    <div class="right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Profile</a>
            <span style="margin-left: 10px;">Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <?php else: ?>
            <a href="signup.php">Sign Up</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>
<div class="content">
    <h2>Create a New Post</h2>
    <form method="POST" enctype="multipart/form-data">
        <textarea name="content" placeholder="What's on your mind?" required></textarea>
        <label for="media-input" class="upload-label" id="upload-label">Upload Photo</label>
        <input type="file" id="media-input" name="media" accept="image/*">
        <input type="hidden" name="zoom_scale" id="zoom-scale" value="1.0">
        <div class="preview-container">
            <img id="media-preview" class="preview" src="#" alt="Preview" style="display: none;">
        </div>
        <button type="button" class="glow-button" id="zoom-out" style="display: none;">Zoom Out</button>
        <button type="button" class="glow-button" id="zoom-in" style="display: none;">Zoom In</button>
        <input type="submit" value="Post" class="glow-button">
    </form>
</div>
<script>
    const mediaInput = document.getElementById('media-input');
    const preview = document.getElementById('media-preview');
    const uploadLabel = document.getElementById('upload-label');
    const zoomOutBtn = document.getElementById('zoom-out');
    const zoomInBtn = document.getElementById('zoom-in');
    const zoomInput = document.getElementById('zoom-scale');
    let scale = 1.0;
    mediaInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.setAttribute('src', e.target.result);
                preview.style.display = 'block';
                zoomOutBtn.style.display = 'inline-block';
                zoomInBtn.style.display = 'inline-block';
                uploadLabel.textContent = 'Change Photo';
                scale = 1.0;
                preview.style.transform = `scale(${scale})`;
                zoomInput.value = scale;
            }
            reader.readAsDataURL(file);
        }
    });
    zoomOutBtn.addEventListener('click', function () {
        scale = Math.max(0.5, scale - 0.1);
        preview.style.transform = `scale(${scale})`;
        zoomInput.value = scale;
    });
    zoomInBtn.addEventListener('click', function () {
        scale = Math.min(2.0, scale + 0.1);
        preview.style.transform = `scale(${scale})`;
        zoomInput.value = scale;
    });
</script>
</body>
</html>
