<?php
// checkout.php
session_start();
require 'db.php'; // your database connection if needed

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. <a href='shop.php'>Go back to shop</a>.</p>";
    exit();
}

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout - LYV</title>
    <link rel="stylesheet" href="futuristic_theme.css">
</head>
<body>
    <h2>Checkout</h2>
    <div>
        <ul>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <li>
                    <?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?> - $
                    <?= number_format($item['price'] * $item['quantity'], 2) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <h3>Total: $<?= number_format($total, 2) ?></h3>

        <!-- Stripe Payment Form -->
        <form action="stripe_charge.php" method="POST">
            <input type="hidden" name="amount" value="<?= intval($total * 100) ?>"> <!-- cents -->
            <script
                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key="YOUR_STRIPE_PUBLIC_KEY"
                data-amount="<?= intval($total * 100) ?>"
                data-name="LYV Shop"
                data-description="Order Checkout"
                data-image="https://yourdomain.com/logo.png"
                data-locale="auto"
                data-currency="usd">
            </script>
        </form>
    </div>
</body>
</html>
