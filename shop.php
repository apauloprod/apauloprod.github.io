<!DOCTYPE html>
<html>
<head>
  <title>Shop - LYV</title>
  <link rel="stylesheet" href="futuristic_theme.css">
  <style>
    html, body {
      margin: 0; padding: 0; height: 100%; overflow: hidden;
      font-family: 'Orbitron', sans-serif;
      color: white;
    }
    #space-bg {
      position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
      object-fit: cover; z-index: -1;
    }
    .header {
      position: fixed; top: 0; width: 100%; background-color: #111;
      color: #fff; display: flex; justify-content: space-between; align-items: center;
      padding: 10px 20px; z-index: 999; transition: top 0.3s;
    }
    .header a {
      color: #fff; text-decoration: none; margin-left: 20px;
    }
    .header .left, .header .right {
      display: flex; align-items: center;
    }
    .products-container {
      position: relative;
      margin: 110px auto 40px;
      max-width: 1200px;
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
      overflow-y: auto;
      height: calc(100vh - 150px);
      padding: 0 20px;
    }
    .item {
      background: transparent;
      border: 1px solid #222;
      border-radius: 12px;
      width: 250px;
      padding: 15px;
      box-shadow: 0 0 15px #00f5ff33;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .item img {
      width: 100%; border-radius: 10px; margin-bottom: 15px;
    }
    .name { font-size: 1.2rem; margin-bottom: 6px; text-align: center; }
    .price { font-weight: bold; margin-bottom: 12px; }
    .actions {
      display: flex; justify-content: center; gap: 12px; width: 100%;
    }
    .item button, .item a {
      background: transparent;
      border: 1.5px solid #00f5ff;
      color: #00f5ff;
      font-weight: bold;
      padding: 10px 16px;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      flex: 1;
      text-shadow:
        0 0 5px #00f5ff,
        0 0 10px #00f5ff,
        0 0 20px #00f5ff,
        0 0 30px #ff69f4,
        0 0 40px #ff69f4;
    }
    .item button:hover, .item a:hover {
      background: #ff69f4;
      color: white;
    }
    #cart {
      position: fixed;
      top: 80px;
      right: 20px;
      background-color: #111;
      color: white;
      padding: 15px;
      border-radius: 10px;
      max-width: 300px;
      max-height: 400px;
      overflow-y: auto;
      z-index: 1000;
    }
  </style>
</head>
<body>
  <video autoplay muted loop id="space-bg">
    <source src="assets/space_bg.mp4" type="video/mp4">
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
      <a href="profile.php">Profile</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <div id="cart">Loading cart...</div>

  <main class="products-container">
    <div class="item" data-id="1" data-name="Galactic Hoodie" data-price="45.00">
      <a href="product.php?id=1">
        <img src="uploads/product1.jpg" alt="Galactic Hoodie">
      </a>
      <div class="name">Galactic Hoodie</div>
      <div class="price">$45.00</div>
      <div class="actions">
        <button class="add-to-cart">Add to Cart</button>
        <a href="product.php?id=1">View</a>
      </div>
    </div>
    <!-- Repeat for other products -->
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const cartEl = document.getElementById('cart');
      cartEl.textContent = 'Loading cart...';
      fetch('cart.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const cart = data.cart;
            if (Object.keys(cart).length === 0) {
              cartEl.textContent = 'Cart is empty';
            } else {
              cartEl.innerHTML = '';
              Object.values(cart).forEach(item => {
                const div = document.createElement('div');
                div.textContent = `${item.name} x${item.quantity} - $${(item.price * item.quantity).toFixed(2)}`;
                cartEl.appendChild(div);
              });
            }
          } else {
            cartEl.textContent = 'Failed to load cart.';
          }
        })
        .catch(() => {
          cartEl.textContent = 'Error loading cart.';
        });
    });
  </script>

  <script src="cart-ajax.js"></script>
</body>
</html>
