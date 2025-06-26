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
    body {
      text-align: center;
      padding: 2rem;
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
  </style>
</head>
<body>
  <div class="top-right">
    <?php if (isset($_SESSION['username'])): ?>
      Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
    <?php else: ?>
      <a href="login.php">Log In</a> | <a href="signup.php">Sign Up</a>
    <?php endif; ?>
  </div>

  <h1>🚀 Space Clicker</h1>
  <p>Tap the spaceship as many times as you can in 10 seconds!</p>

  <div id="game-area">
    <button id="click-button">🚀 Tap!</button>
    <p id="score">Score: 0</p>
    <p id="timer">Time Left: 10s</p>
  </div>

  <form id="submit-form" method="POST" action="save_score.php">
    <input type="hidden" name="score" id="final-score" value="0">
    <input type="text" name="username" placeholder="Enter username" required />
    <button type="submit">Submit Score</button>
  </form>

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
