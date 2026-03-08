<?php
session_start();
require_once 'config/database.php';

// Check if request is AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_id = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($menu_id > 0 && $quantity > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$menu_id])) {
            $_SESSION['cart'][$menu_id] += $quantity;
        } else {
            $_SESSION['cart'][$menu_id] = $quantity;
        }

        $cartCount = array_sum($_SESSION['cart']);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Item berhasil ditambahkan ke keranjang!',
                'cartCount' => $cartCount
            ]);
            exit;
        } else {
            $_SESSION['message'] = "Item berhasil ditambahkan ke keranjang!";
        }
    }
}

if (!$isAjax) {
    header("Location: index.php#menu");
    exit;
}
?>
