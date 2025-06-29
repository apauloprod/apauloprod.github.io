<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['productId'], $data['customImage'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$productId = (int)$data['productId'];
$customImageData = $data['customImage'];

// Validate productId here if needed (e.g., from DB)

// Extract base64 data from "data:image/png;base64,..." format
if (preg_match('/^data:image\/png;base64,/', $customImageData)) {
    $customImageData = substr($customImageData, strpos($customImageData, ',') + 1);
    $customImageData = base64_decode($customImageData);

    if ($customImageData === false) {
        echo json_encode(['success' => false, 'message' => 'Base64 decode failed']);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid image data']);
    exit();
}

// Save image to uploads/custom/
$uploadDir = __DIR__ . '/uploads/custom/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = 'custom_' . $_SESSION['user_id'] . '_' . time() . '.png';
$filepath = $uploadDir . $filename;

if (file_put_contents($filepath, $customImageData) === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to save image']);
    exit();
}

// Add to cart session - you can adapt this to your cart system
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add custom product with the new image path
$_SESSION['cart'][] = [
    'id' => 'custom_' . time(),
    'product_id' => $productId,
    'name' => 'Custom ' . $productId,
    'price' => 0, // Or add your custom pricing logic
    'image' => 'uploads/custom/' . $filename,
    'quantity' => 1,
    'custom' => true,
];

echo json_encode(['success' => true]);
