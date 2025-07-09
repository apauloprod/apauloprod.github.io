<?php
require 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$is_owner = false;

$view_id = $_GET['user_id'] ?? $user_id;
if (!$view_id) {
    echo "No user specified.";
    exit();
}

// Fetch username of the profile being viewed
$username_result = pg_query_params($conn, "SELECT username FROM users WHERE id = $1", [$view_id]);
$view_username = pg_num_rows($username_result) ? pg_fetch_result($username_result, 0, 0) : '';

$is_owner = ($user_id == $view_id);

$portfolio_result = pg_query_params($conn, "SELECT content FROM portfolios WHERE user_id = $1", [$view_id]);
$portfolio_content = pg_num_rows($portfolio_result) ? pg_fetch_result($portfolio_result, 0, 0) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_owner) {
    $content = $_POST['content'] ?? '';
    if (pg_num_rows($portfolio_result)) {
        pg_query_params($conn, "UPDATE portfolios SET content = $1, updated_at = CURRENT_TIMESTAMP WHERE user_id = $2", [$content, $user_id]);
    } else {
        pg_query_params($conn, "INSERT INTO portfolios (user_id, content) VALUES ($1, $2)", [$user_id, $content]);
    }
    header("Location: portfolio.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $is_owner ? 'Edit' : htmlspecialchars($view_username) . "'s" ?> Portfolio</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Orbitron', sans-serif;
            background: black;
            color: #8de6d6;
            padding-top: 80px;
            overflow-x: hidden;
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
            max-width: 1000px;
            margin: auto;
            padding: 2rem;
            position: relative;
        }
        .glow-button {
            padding: 0.75rem 1.5rem;
            border: 1px solid #8de6d6;
            color: #8de6d6;
            background-color: transparent;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            margin-top: 1rem;
            text-shadow: 0 0 8px #8de6d6;
            transition: background-color 0.3s, color 0.3s;
            cursor: pointer;
        }
        .glow-button:hover {
            background-color: #8de6d6;
            color: #000;
        }
        #canvas {
            width: 100%;
            height: 600px;
            border: 2px dashed #8de6d6;
            position: relative;
            overflow: hidden;
        }
        .item {
            position: absolute;
            cursor: move;
            resize: both;
            overflow: hidden;
            transform-origin: center;
        }
        .item video, .item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .controls {
            position: absolute;
            top: 0;
            right: 0;
            display: flex;
            gap: 5px;
            background: rgba(0, 0, 0, 0.5);
            padding: 2px;
        }
        .controls button {
            background: #8de6d6;
            border: none;
            color: black;
            font-weight: bold;
            cursor: pointer;
        }
        #file-upload {
            margin-bottom: 1rem;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="left">
        <a href="home.php">Home</a>
        <a href="your_feed.php">Your Feed</a>
        <a href="post.php">New Post</a>
        <a href="feed.php">Community Board</a>
        <a href="spaceminigame.php">Mini Game</a>
    </div>
    <div class="right">
        <?php if ($user_id): ?>
            <a href="profile.php">Profile</a>
            <span style="margin-left: 10px; padding-right: 10px;">Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <?php else: ?>
            <a href="signup.php">Sign Up</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="content">
    <h1><?= $is_owner ? 'Edit Your' : htmlspecialchars($view_username) . "'s" ?> Portfolio</h1>

    <?php if ($is_owner): ?>
        <form method="POST">
            <input type="file" id="file-upload" accept="image/*,video/*">
            <div id="canvas"><?= $portfolio_content ?></div>
            <textarea name="content" id="hidden-content" style="display:none;"></textarea>
            <button type="submit" class="glow-button" onclick="saveCanvas()">Publish</button>
        </form>
        <script>
            const canvas = document.getElementById('canvas');
            const upload = document.getElementById('file-upload');

            upload.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const url = e.target.result;
                    const div = document.createElement('div');
                    div.className = 'item';
                    div.style.left = '50px';
                    div.style.top = '50px';
                    div.style.width = '300px';
                    div.style.height = '200px';
                    div.style.transform = 'rotate(0deg)';
                    const controls = document.createElement('div');
                    controls.className = 'controls';
                    controls.innerHTML = `
                        <button onclick="rotate(this.parentElement.parentElement, 15)">⤴</button>
                        <button onclick="removeItem(this.parentElement.parentElement)">✖</button>
                    `;
                    div.appendChild(controls);
                    div.innerHTML += file.type.startsWith('video') ? `<video src="${url}" controls></video>` : `<img src="${url}">`;
                    makeDraggableResizable(div);
                    canvas.appendChild(div);
                };
                reader.readAsDataURL(file);
            });

            function makeDraggableResizable(el) {
                el.style.position = 'absolute';
                el.style.zIndex = 10;
                el.addEventListener('mousedown', dragMouseDown);

                function dragMouseDown(e) {
                    if (e.target.tagName === 'BUTTON') return;
                    e.preventDefault();
                    let pos3 = e.clientX;
                    let pos4 = e.clientY;
                    document.onmouseup = closeDragElement;
                    document.onmousemove = elementDrag;

                    function elementDrag(e) {
                        e.preventDefault();
                        const pos1 = pos3 - e.clientX;
                        const pos2 = pos4 - e.clientY;
                        pos3 = e.clientX;
                        pos4 = e.clientY;
                        el.style.top = (el.offsetTop - pos2) + "px";
                        el.style.left = (el.offsetLeft - pos1) + "px";
                    }

                    function closeDragElement() {
                        document.onmouseup = null;
                        document.onmousemove = null;
                    }
                }
            }

            function rotate(el, degrees) {
                const current = el.style.transform.match(/rotate\(([-\d.]+)deg\)/);
                let angle = current ? parseFloat(current[1]) : 0;
                angle += degrees;
                el.style.transform = `rotate(${angle}deg)`;
            }

            function removeItem(el) {
                canvas.removeChild(el);
            }

            function saveCanvas() {
                document.getElementById('hidden-content').value = canvas.innerHTML;
            }
        </script>
    <?php else: ?>
        <div><?= $portfolio_content ?></div>
    <?php endif; ?>
</div>
</body>
</html>
