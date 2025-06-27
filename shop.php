<!--shop page -->


<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shop - LYV</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: 'Orbitron', sans-serif;
            background: black;
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

        .floating-text {
            position: absolute;
            top: 40%;
            width: 100%;
            text-align: center;
            font-size: 3rem;
            color: #fff;
            text-shadow: 0 0 20px #00f5ff, 0 0 40px #ff69f4;
            animation: float 6s ease-in-out infinite;
            z-index: 1;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(-10px);
            }
            50% {
                transform: translateY(10px);
            }
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
            z-index: 999;
            transition: top 0.3s;
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
    </style>
    <script>
        let prevScrollPos = window.pageYOffset;
        window.onscroll = function () {
            const currentScrollPos = window.pageYOffset;
            const header = document.querySelector(".header");
            if (prevScrollPos > currentScrollPos) {
                header.style.top = "0";
            } else {
                header.style.top = "-70px";
            }
            prevScrollPos = currentScrollPos;
        };
    </script>
</head>
<body>
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
                <a href="profile.php">Profile</a>
                <span style="margin-left: 10px;">Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="signup.php">Sign Up</a>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>

    </div>

    <div id="cart" style="position: absolute; top: 80px; right: 20px; background: #111; color: white; padding: 10px; border-radius: 8px; z-index: 1000;"></div>

    <!-- HTML Placeholder for Cart 
    <div id="cart"></div>-->

    <!-- Fallback space video background -->
    <video autoplay muted loop id="space-bg">
        <source src="assets/space_bg.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <div class="floating-text">Live Your Vision</div>
</body>
</html>



<script>

// Sample HTML Structure (assumed for logic to work)
// <div class="item" data-id="1" data-name="T-Shirt" data-price="20">
//   <button class="add-to-cart">Add to Cart</button>
// </div>
// Repeat for Sweatshirt, Sweatpants, etc.

const cart = [];

function addToCart(itemId, itemName, itemPrice) {
  const existingItem = cart.find(item => item.id === itemId);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.push({ id: itemId, name: itemName, price: itemPrice, quantity: 1 });
  }

  renderCart();
}

function renderCart() {
  const cartContainer = document.getElementById('cart');
  cartContainer.innerHTML = '';

  cart.forEach(item => {
    const itemElement = document.createElement('div');
    itemElement.classList.add('cart-item');
    itemElement.innerText = `${item.name} x${item.quantity} - $${item.price * item.quantity}`;
    cartContainer.appendChild(itemElement);
  });

  const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
  const totalElement = document.createElement('div');
  totalElement.innerText = `Total: $${total}`;
  cartContainer.appendChild(totalElement);
}

function toggleCartDisplay() {
  const cartEl = document.getElementById('cart');
  const isVisible = cartEl.classList.contains('visible');
  if (isVisible) {
    cartEl.classList.remove('visible');
    cartEl.classList.add('hidden');
  } else {
    cartEl.classList.remove('hidden');
    cartEl.classList.add('visible');
  }
}

// Hooking up event listeners for add-to-cart buttons
document.querySelectorAll('.add-to-cart').forEach(button => {
  button.addEventListener('click', (e) => {
    const itemElement = e.target.closest('.item');
    const itemId = parseInt(itemElement.dataset.id);
    const itemName = itemElement.dataset.name;
    const itemPrice = parseFloat(itemElement.dataset.price);

    addToCart(itemId, itemName, itemPrice);
  });
});

// Insert cart HTML into header
document.addEventListener('DOMContentLoaded', () => {
  const headerRight = document.querySelector('.header .right');

  const cartWrapper = document.createElement('div');
  cartWrapper.style.position = 'relative';

  const cartIcon = document.createElement('div');
  cartIcon.innerHTML = '🛒';
  cartIcon.style.fontSize = '2rem';
  cartIcon.style.cursor = 'pointer';
  cartIcon.title = 'View Cart';
  cartIcon.addEventListener('click', toggleCartDisplay);

  const cartContainer = document.createElement('div');
  cartContainer.id = 'cart';
  cartContainer.style.position = 'absolute';
  cartContainer.style.top = '40px';
  cartContainer.style.right = '0';
  cartContainer.style.width = '250px';
  cartContainer.style.maxHeight = '400px';
  cartContainer.style.overflowY = 'auto';
  cartContainer.style.backgroundColor = '#111';
  cartContainer.style.color = 'white';
  cartContainer.style.padding = '15px';
  cartContainer.style.borderRadius = '8px';
  cartContainer.style.boxShadow = '0 0 10px rgba(0,0,0,0.5)';
  cartContainer.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
  cartContainer.classList.add('hidden');

  // Inject basic slide-in/out styles
  const style = document.createElement('style');
  style.textContent = `
    #cart.hidden {
      transform: translateX(110%);
      pointer-events: none;
      opacity: 0;
    }
    #cart.visible {
      transform: translateX(0);
      pointer-events: auto;
      opacity: 1;
    }
  `;
  document.head.appendChild(style);

  cartWrapper.appendChild(cartIcon);
  cartWrapper.appendChild(cartContainer);
  headerRight.insertBefore(cartWrapper, headerRight.firstChild);
});

</script>

