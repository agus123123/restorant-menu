<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Savoria Catering - Premium Catering Service</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container d-flex justify-between align-center">
        <a href="index.php" class="logo">Savoria.</a>
        
        <button class="mobile-menu-btn" id="mobile-btn">
            <i class="fas fa-bars"></i>
        </button>

        <div class="nav-links" id="nav-links">
            <a href="index.php">Beranda</a>
            <a href="index.php#menu">Katalog Menu</a>
            <a href="track_order.php">Cek Pesanan</a>
            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cart-counter-badge" style="<?= $cartCount > 0 ? '' : 'display: none;' ?>"><?= $cartCount ?></span>
            </a>
        </div>
    </div>
</nav>

<!-- Toast Notification Container -->
<div class="toast-container" id="toast-container"></div>

<main>
