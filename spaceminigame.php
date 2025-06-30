<?php
session_start();
require 'db.php';

// Get current user total clicks if logged in
$totalClicks = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $res = pg_query_params($conn, "SELECT total_clicks FROM user_clicks WHERE user_id = $1", [$user_id]);
    if ($res && pg_num_rows($res) > 0) {
        $row = pg_fetch_assoc($res);
        $totalClicks = intval($row['total_clicks']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Space Clicker Mini Game</title>
  <link rel="stylesheet" href="futuristic_theme.css" />
  <style>
    html, body {
        margin: 0;
        padding: 0;
        min-height: 100%;
        font-family: 'Orbitron', sans-serif;
        background: transparent;
        color: #fff;
        overflow-x: hidden;
        overflow-y: auto;
        position: relative;
    }
    #space-bg {
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        object-fit: cover;
        z-index: -1;
        filter: brightness(0.7);
    }
    .header {
        position: fixed;
        top: 0; left: 0; right: 0;
        background-color: #111;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 25px;
        z-index: 1000;
        font-size: 1rem;
        box-shadow: 0 2px 8px #0009;
    }
    .header a {
        color: #8de6d6;
        text-decoration: none;
        margin-left: 20px;
        white-space: nowrap;
        transition: color 0.3s ease;
    }
    .header a:hover {
        color: #fff;
    }
    .header .left, .header .right {
        display: flex;
        align-items: center;
    }
    .dropdown {
        position: relative;
        display: inline-block;
    }
    .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: #222;
        min-width: 160px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.5);
        border-radius: 8px;
        overflow: hidden;
        z-index: 1001;
    }
    .dropdown-content a {
        color: #8de6d6;
        padding: 12px 16px;
        display: block;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }
    .dropdown-content a:hover {
        background-color: #333;
        color: #fff;
    }
    .dropdown:hover .dropdown-content {
        display: block;
    }
    .content {
        max-width: 600px;
        margin: 130px auto 4rem auto;
        text-align: center;
        padding: 0 20px;
        z-index: 10;
        position: relative;
    }
    h1 {
      font-size: 3rem;
      margin-bottom: 0.25rem;
      text-shadow: 0 0 8px #8de6d6aa;
    }
    p {
      font-size: 1.25rem;
      margin-top: 0;
      margin-bottom: 2rem;
      text-shadow: 0 0 6px #000;
    }
    #click-button {
      font-size: 2.5rem;
      padding: 1rem 3rem;
      background: linear-gradient(135deg, #00ffe7, #0088ff);
      border: none;
      border-radius: 15px;
      color: #000;
      cursor: pointer;
      box-shadow: 0 0 12px #00ffe7aa;
      transition: background 0.3s ease, box-shadow 0.3s ease;
      user-select: none;
    }
    #click-button:hover {
      background: linear-gradient(135deg, #00e6ce, #006ecc);
      box-shadow: 0 0 20px #00e6cecc;
    }
    #score {
      margin-top: 1.5rem;
      font-size: 2rem;
      color: #8de6d6;
      text-shadow: 0 0 10px #00ffe7;
      user-select: none;
    }
    #leaderboard {
      margin-top: 3.5rem;
      background: rgba(20, 30, 40, 0.7);
      padding: 1rem 1.5rem;
      border-radius: 12px;
      box-shadow: 0 0 15px #0088ff88;
    }
    #leaderboard h2 {
      margin-top: 0;
      margin-bottom: 1rem;
      font-size: 1.8rem;
      text-shadow: 0 0 10px #00bfffaa;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 8px;
      overflow: hidden;
      font-size: 1.1rem;
      color: #cceeff;
      user-select: none;
    }
    th, td {
      padding: 0.85rem 1rem;
      border-bottom: 1px solid #00557744;
      text-align: left;
    }
    th {
      background: #004466cc;
      text-shadow: 0 0 3px #00aaffaa;
    }
    tbody tr:hover {
      background: #00559933;
    }
    body::-webkit-scrollbar {
      width: 10px;
    }
    body::-webkit-scrollbar-thumb {
      background-color: #0088ffaa;
      border-radius: 5px;
    }
    .login-msg {
      margin-top: 2rem;
      font-size: 1.2rem;
      color: #f66;
      text-shadow: 0 0 4px #900;
    }
  </style>
</head>
<body>
  <video autoplay muted loop id="space-bg" playsinline>
      <source src="assets/space_bg.mp4" type="video/mp4" />
      Your browser does not support the video tag.
  </video>

  <div class="header">
      <div class="left">
          <a href="home.php">Home</a>
          <a href="post.php">New Post</a>
          <a href="feed.php">Community Board</a>
          <a href="spaceminigame.php">Mini Game</a>
          <a href="shop.php">Your Shop</a>
      </div>
      <div class="right">
          <?php if (isset($_SESSION['user_id'])): ?>
              <div class="dropdown">
                  <a href="#">👤 Hello, <?= htmlspecialchars($_SESSION['username']) ?></a>
                  <div class="dropdown-content">
                      <a href="profile.php">Profile</a>
                      <a href="portfolio.php">Portfolio</a>
                      <a href="logout.php">Logout</a>
                  </div>
              </div>
          <?php else: ?>
              <a href="signup.php">Sign Up</a>
              <a href="login.php">Login</a>
          <?php endif; ?>
      </div>
  </div>

  <div class="content">
    <h1>🚀 Space Clicker</h1>
    <p>Tap the spaceship as many times as you want! Your clicks are saved forever.</p>

    <button id="click-button" <?= isset($_SESSION['user_id']) ? '' : 'disabled title="Please log in to click"' ?>>🚀 Tap!</button>
    <p id="score">Total Clicks: <?= $totalClicks ?></p>

    <?php if (!isset($_SESSION['user_id'])): ?>
      <p class="login-msg">Please <a href="login.php" style="color:#8de6d6;">log in</a> to start clicking!</p>
    <?php endif; ?>

    <div id="leaderboard">
      <h2>🌟 Leaderboard - Total Clicks</h2>
      <table>
        <thead>
          <tr><th>User</th><th>Clicks</th></tr>
        </thead>
        <tbody>
          <?php
          // Leaderboard query
          $result = pg_query($conn, "
            SELECT u.username, COALESCE(uc.total_clicks, 0) AS total_clicks
            FROM users u
            LEFT JOIN user_clicks uc ON u.id = uc.user_id
            ORDER BY total_clicks DESC
            LIMIT 10
          ");

          while ($row = pg_fetch_assoc($result)) {
              echo "<tr><td>" . htmlspecialchars($row['username']) . "</td><td>" . intval($row['total_clicks']) . "</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    const clickButton = document.getElementById("click-button");
    const scoreEl = document.getElementById("score");

    <?php if (isset($_SESSION['user_id'])): ?>
    clickButton.addEventListener("click", () => {
      // Disable button briefly to prevent spam clicks too fast (optional)
      clickButton.disabled = true;

      fetch('update_clicks.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
      })
      .then(res => res.json())
      .then(data => {
        if (data.total_clicks !== undefined) {
          scoreEl.textContent = `Total Clicks: ${data.total_clicks}`;
        } else {
          console.error('Error: ', data.error || 'Unknown error');
        }
      })
      .catch(err => {
        console.error('Fetch error:', err);
      })
      .finally(() => {
        clickButton.disabled = false;
      });
    });
    <?php endif; ?>
  </script>
</body>
</html>
