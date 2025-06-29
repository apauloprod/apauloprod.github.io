<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = &$_SESSION['cart'];

function json_response($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'cart' => $data
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    json_response(true, '', $cart);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    parse_str(file_get_contents('php://input'), $post);

    if (isset($post['checkout'])) {
        // Here you would implement payment logic / order saving
        $_SESSION['cart'] = [];
        json_response(true, 'Checkout successful. Your order has been placed.', []);
    }

    if ($post['action'] === 'add' && isset($post['id'], $post['name'], $post['price'])) {
        $id = $post['id'];
        $name = $post['name'];
        $price = floatval($post['price']);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += 1;
        } else {
            $cart[$id] = [
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'quantity' => 1
            ];
        }

        json_response(true, 'Item added to cart.', $cart);
    }

    json_response(false, 'Invalid request.');
}

json_response(false, 'Unsupported request method.');
