<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Sample products
$products = [
    1 => ['name' => 'Galactic Hoodie', 'price' => 45.00, 'image' => 'uploads/product1.jpg', 'description' => 'Stay warm and cosmic in the Galactic Hoodie. Perfect for spacewalks and chill nights.'],
    2 => ['name' => 'Orbit T-Shirt', 'price' => 25.00, 'image' => 'uploads/tshirt.jpg', 'description' => 'Lightweight and breathable Orbit T-Shirt. Sport the stars with style.'],
    3 => ['name' => 'Cosmic Sweatpants', 'price' => 55.00, 'image' => 'uploads/sweatpants.jpg', 'description' => 'Comfort meets the cosmos in these Cosmic Sweatpants. Lounge or launch!'],
];

// Patches to drag — provide your transparent PNG paths here
$patches = [
    ['id' => 101, 'name' => 'Star Patch', 'image' => 'patches/star.png'],
    ['id' => 102, 'name' => 'Moon Patch', 'image' => 'patches/moon.png'],
    ['id' => 103, 'name' => 'Rocket Patch', 'image' => 'patches/rocket.png'],
];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!array_key_exists($id, $products)) {
    http_response_code(404);
    echo "Product not found";
    exit();
}

$product = $products[$id];
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($product['name']) ?> - LYV Shop</title>
    <link rel="stylesheet" href="futuristic_theme.css">
    <style>
        /* Keep your existing styles plus patches sidebar and canvas */

        html, body {
            margin: 0; padding: 0; height: 100%;
            font-family: 'Orbitron', sans-serif;
            background: black;
            color: white;
            overflow-x: hidden;
            
        }
        body{
            overflow-y: scroll; 
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
        main {
            max-width: 900px;
            margin: 120px auto 60px;
            padding: 20px;
            background: #111;
            border-radius: 15px;
            box-shadow: 0 0 30px #00f5ff44;
            display: flex;
            gap: 30px;
            color: white;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 0 0 15px #00f5ff;
        }
        .price {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 15px;
            text-shadow: 0 0 15px #ff69f4;
        }
        .description {
            font-size: 1.1rem;
            line-height: 1.5;
            margin-bottom: 25px;
            color: #ccc;
        }
        /* Patches sidebar */
        #patches-sidebar {
            width: 120px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            overflow-y: auto;
            max-height: 600px;
            border-right: 1px solid #00f5ff44;
            padding-right: 10px;
        }
        #patches-sidebar img {
            width: 100%;
            cursor: grab;
            border-radius: 8px;
            border: 2px solid transparent;
            user-select: none;
        }
        #patches-sidebar img:active {
            cursor: grabbing;
        }
        /* Container for product image + canvas */
        #product-canvas-container {
            position: relative;
            flex-grow: 1;
            max-width: 600px;
            max-height: 600px;
            user-select: none;
        }
        #product-image {
            max-width: 100%;
            border-radius: 12px;
            display: block;
            user-select: none;
            pointer-events: none;
        }
        /* Canvas overlays on product image */
        #patch-canvas {
            position: absolute;
            top: 0;
            left: 0;
            cursor: grab;
        }
        /* Add to cart button and back link */
        #actions-container {
            max-width: 900px;
            margin: 10px auto 60px;
            text-align: center;
        }
        button#add-to-cart-btn, a.back-link {
            background: #00f5ff;
            border: none;
            color: black;
            font-weight: bold;
            padding: 12px 25px;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
            margin: 0 10px;
        }
        button#add-to-cart-btn:hover, a.back-link:hover {
            background: #ff69f4;
            color: white;
        }
    </style>
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

<video autoplay muted loop id="space-bg">
    <source src="assets/space_bg.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
</video>

<div class="floating-text">Live Your Vision</div>

<main>
    <div id="patches-sidebar" aria-label="Available patches">
        <?php foreach ($patches as $patch): ?>
            <img src="<?= htmlspecialchars($patch['image']) ?>" 
                 draggable="true" 
                 data-patch-id="<?= $patch['id'] ?>" 
                 data-patch-src="<?= htmlspecialchars($patch['image']) ?>"
                 alt="<?= htmlspecialchars($patch['name']) ?>">
        <?php endforeach; ?>
    </div>

    <div id="product-canvas-container" aria-label="Product customization area">
        <h1><?= htmlspecialchars($product['name']) ?></h1>

        <img id="product-image" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <canvas id="patch-canvas"></canvas>

        <div class="price">$<?= number_format($product['price'], 2) ?></div>
        <div class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></div>

    <div id="actions-container">
        <button id="add-to-cart-btn">Add to Cart</button>
        <a href="shop.php" class="back-link">&larr; Back to Shop</a>
    </div>
        
    </div>
</main>



<script>
(() => {
    const patchesSidebar = document.getElementById('patches-sidebar');
    const productImage = document.getElementById('product-image');
    const canvasContainer = document.getElementById('product-canvas-container');
    const canvas = document.getElementById('patch-canvas');
    const ctx = canvas.getContext('2d');

    const placedPatches = [];

    function resizeCanvas() {
        canvas.width = productImage.clientWidth;
        canvas.height = productImage.clientHeight;
        canvas.style.top = productImage.offsetTop + 'px';
        canvas.style.left = productImage.offsetLeft + 'px';
        redraw();
    }

    window.addEventListener('resize', resizeCanvas);
    productImage.addEventListener('load', resizeCanvas);
    if (productImage.complete) resizeCanvas();

    function redraw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (const patch of placedPatches) {
            ctx.drawImage(patch.img, patch.x, patch.y, patch.width, patch.height);
        }
    }

    let draggedPatch = null;
    let isDraggingCanvasPatch = false;

    patchesSidebar.querySelectorAll('img').forEach(patchImg => {
        patchImg.addEventListener('dragstart', e => {
            draggedPatch = {
                id: patchImg.dataset.patchId,
                src: patchImg.dataset.patchSrc,
                img: new Image(),
            };
            draggedPatch.img.src = draggedPatch.src;
            e.dataTransfer.setDragImage(patchImg, patchImg.width / 2, patchImg.height / 2);
        });
    });

    canvasContainer.addEventListener('dragover', e => {
        e.preventDefault();
    });

    canvasContainer.addEventListener('drop', e => {
        e.preventDefault();
        if (!draggedPatch) return;

        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const defaultWidth = canvas.width * 0.2;
        const aspectRatio = draggedPatch.img.width / draggedPatch.img.height;
        const width = defaultWidth;
        const height = defaultWidth / aspectRatio;

        placedPatches.push({
            id: draggedPatch.id,
            img: draggedPatch.img,
            x: x - width / 2,
            y: y - height / 2,
            width,
            height,
            isDragging: false,
            offsetX: 0,
            offsetY: 0
        });

        draggedPatch = null;
        redraw()

        patch.img.onload = () => {
            const w = canvas.width * 0.2;
            const h = w * (patch.height / patch.width);
            placedPatches.push({ img: patch, x: e.offsetX - w/2, y: e.offsetY - h/2, w, h, rotation: 0 });
            drawPatches();
        };

        // Modify drawPatches to apply rotation
        function drawPatches() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (const p of placedPatches) {
                ctx.save();
                ctx.translate(p.x + p.w / 2, p.y + p.h / 2);
                ctx.rotate(p.rotation || 0);
                ctx.drawImage(p.img, -p.w / 2, -p.h / 2, p.w, p.h);
                ctx.restore();
            }
        }

        let selectedPatch = null;
        canvas.addEventListener('mousedown', e => {
            const x = e.offsetX, y = e.offsetY;
            for (let i = placedPatches.length - 1; i >= 0; i--) {
                const p = placedPatches[i];
                const dx = x - (p.x + p.w / 2);
                const dy = y - (p.y + p.h / 2);
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < Math.max(p.w, p.h)) {
                    selectedPatch = p;
                    draggingPatch = { patch: p, offsetX: dx, offsetY: dy };
                    break;
                }
            }
        });

        canvas.addEventListener('mouseup', () => draggingPatch = null);
        document.addEventListener('keydown', e => {
            if (!selectedPatch) return;
            if (e.key === '+') {
                selectedPatch.w *= 5;
                selectedPatch.h *= 5;
            } else if (e.key === '-') {
                selectedPatch.w *= .1;
                selectedPatch.h *= 0.1;
            } else if (e.key.toLowerCase() === 'r') {
                selectedPatch.rotation += 5;
            }
            drawPatches();
        });

    });

    function getPatchAt(x, y) {
        for (let i = placedPatches.length - 1; i >= 0; i--) {
            const p = placedPatches[i];
            if (x >= p.x && x <= p.x + p.width &&
                y >= p.y && y <= p.y + p.height) {
                return p;
            }
        }
        return null;
    }

    canvas.addEventListener('mousedown', e => {
        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const patch = getPatchAt(x, y);
        if (patch) {
            patch.isDragging = true;
            patch.offsetX = x - patch.x;
            patch.offsetY = y - patch.y;
            isDraggingCanvasPatch = true;
            const index = placedPatches.indexOf(patch);
            placedPatches.splice(index, 1);
            placedPatches.push(patch);
            redraw();
        }
    });

    window.addEventListener('mousemove', e => {
        if (!isDraggingCanvasPatch) return;

        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        for (const patch of placedPatches) {
            if (patch.isDragging) {
                patch.x = x - patch.offsetX;
                patch.y = y - patch.offsetY;

                patch.x = Math.min(Math.max(0, patch.x), canvas.width - patch.width);
                patch.y = Math.min(Math.max(0, patch.y), canvas.height - patch.height);

                redraw();
                break;
            }
        }
    });

    window.addEventListener('mouseup', e => {
        for (const patch of placedPatches) {
            patch.isDragging = false;
        }
        isDraggingCanvasPatch = false;
    });

    document.getElementById('add-to-cart-btn').addEventListener('click', async () => {
        const productNaturalWidth = productImage.naturalWidth;
        const productNaturalHeight = productImage.naturalHeight;

        const mergeCanvas = document.createElement('canvas');
        mergeCanvas.width = productNaturalWidth;
        mergeCanvas.height = productNaturalHeight;
        const mergeCtx = mergeCanvas.getContext('2d');

        mergeCtx.drawImage(productImage, 0, 0, productNaturalWidth, productNaturalHeight);

        const scaleX = productNaturalWidth / canvas.width;
        const scaleY = productNaturalHeight / canvas.height;

        for (const patch of placedPatches) {
            mergeCtx.drawImage(
                patch.img,
                patch.x * scaleX,
                patch.y * scaleY,
                patch.width * scaleX,
                patch.height * scaleY
            );
        }

        const combinedImageDataURL = mergeCanvas.toDataURL('image/png');

        try {
            const response = await fetch('create_custom_product.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    productId: <?= json_encode($id) ?>,
                    customImage: combinedImageDataURL
                })
            });
            const result = await response.json();
            if (result.success) {
                alert('Custom product added to cart!');
            } else {
                alert('Error adding custom product: ' + result.message);
            }
        } catch (error) {
            alert('Network error: ' + error.message);
        }
    });
})();

</script>

</body>
</html>
