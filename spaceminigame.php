<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Space Clicker Mini Game</title>
  <link rel="stylesheet" href="futuristic_theme.css" />
  <style>
    html, body {
        margin: 0;
        padding: 0;
        min-height: 100%;
        font-family: 'Orbitron', sans-serif;
        overflow-x: hidden;
        background: transparent;
        position: relative;
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
        transition: top 0.3s;
    }
    .header a {
        color: #fff;
        text-decoration: none;
        margin-left: 20px;
        white-space: nowrap;
    }
    .header .left, .header .right {
        display: flex;
        align-items: center;
    }
    .header .right {
        padding-right: 20px;
        position: relative;
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
        box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
        z-index: 1001;
    }
    .dropdown-content a {
        color: #fff;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }
    .dropdown-content a:hover {
        background-color: #333;
    }
    .dropdown:hover .dropdown-content {
        display: block;
    }
    .content {
        padding-top: 100px;
        padding-bottom: 5rem;
        max-width: 800px;
        margin: auto;
        color: #fff;
        z-index: 10;
        position: relative;
    }
    .top-right {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 1rem;
        color: #fff;
    }
    .top-right a {
        color: #8de6d6;
        margin: 0 0.5rem;
        text-decoration: none;
    }
    #game-area {
      margin-top: 2rem;
    }
    #click-button {
      font-size: 2rem;
      padding: 1rem 2rem;
      background: #8de6d6;
      border: none;
      border-radius: 12px;
      cursor: pointer;
    }
    #leaderboard {
      margin-top: 3rem;
      max-width: 400px;
      margin-left: auto;
      margin-right: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
      overflow: hidden;
    }
    th, td {
      padding: 0.75rem;
      border-bottom: 1px solid #ccc;
      color: #fff;
    }
    body::-webkit-scrollbar {
      width: 8px;
    }
    body::-webkit-scrollbar-thumb {
      background-color: #444;
      border-radius: 4px;
    }
  </style>
</head>
<body>
  <!-- Fallback space video background -->
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
    <p>Tap the spaceship as many times as you can in 10 seconds!</p>

    <div id="game-area">
      <button id="click-button">🚀 Tap!</button>
      <p id="score">Score: 0</p>
      <p id="timer">Time Left: 10s</p>
    </div>

    <?php if (isset($_SESSION['username'])): ?>
    <form id="submit-form" method="POST" action="save_score.php">
      <input type="hidden" name="score" id="final-score" value="0">
      <input type="hidden" name="username" value="<?= htmlspecialchars($_SESSION['username']) ?>">
      <p>Submitting as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
      <button type="submit">Submit Score</button>
    </form>
    <?php else: ?>
      <p>You must <a href="login.php">log in</a> to submit your score.</p>
    <?php endif; ?>

    <div id="leaderboard">
      <h2>🌟 Leaderboard</h2>
      <table>
        <thead>
          <tr><th>User</th><th>Score</th></tr>
        </thead>
        <tbody>
          <?php
          require 'db.php';
          $result = pg_query($conn, "SELECT username, MAX(score) as score FROM scores GROUP BY username ORDER BY score DESC LIMIT 10");
          while ($row = pg_fetch_assoc($result)) {
            echo "<tr><td>" . htmlspecialchars($row['username']) . "</td><td>" . $row['score'] . "</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    let score = 0;
    let timeLeft = 10;
    let started = false;

    const scoreEl = document.getElementById("score");
    const timerEl = document.getElementById("timer");
    const clickButton = document.getElementById("click-button");
    const finalScoreInput = document.getElementById("final-score");

    clickButton.addEventListener("click", () => {
      if (!started) startGame();
      if (timeLeft > 0) {
        score++;
        scoreEl.textContent = `Score: ${score}`;
      }
    });

    function startGame() {
      started = true;
      const countdown = setInterval(() => {
        timeLeft--;
        timerEl.textContent = `Time Left: ${timeLeft}s`;
        if (timeLeft <= 0) {
          clearInterval(countdown);
          clickButton.disabled = true;
          finalScoreInput.value = score;
        }
      }, 1000);
    }
  </script>
</body>
</html>
