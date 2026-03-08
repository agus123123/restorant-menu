<?php
session_start();
require_once 'config/database.php';
require_once 'config/tripay.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// Get form details
$customer_name = htmlspecialchars($_POST['customer_name']);
$customer_email = filter_var($_POST['customer_email'], FILTER_SANITIZE_EMAIL);
$customer_phone = htmlspecialchars($_POST['customer_phone']);
$delivery_date = $_POST['delivery_date'];
$delivery_address = htmlspecialchars($_POST['delivery_address']);
$payment_method = htmlspecialchars($_POST['method']);

if (empty($payment_method)) {
    die("Pilih metode pembayaran terlebih dahulu.");
}

// Calculate totals and prepare item details
$order_items = [];
$total_amount = 0;
$ids = implode(',', array_keys($_SESSION['cart']));
$stmt = $conn->query("SELECT * FROM menus WHERE id IN ($ids)");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

$menu_data = []; 
foreach ($menus as $menu) {
    $qty = $_SESSION['cart'][$menu['id']];
    $menu_data[$menu['id']] = $menu;
    
    $subtotal = $qty * $menu['price'];
    $total_amount += $subtotal;
    
    $order_items[] = [
        'sku'      => 'MENU-' . $menu['id'],
        'name'     => substr($menu['name'], 0, 50),
        'price'    => (int)$menu['price'],
        'quantity' => $qty
    ];
}

$order_id = 'SAV-' . time() . '-' . rand(100, 999);
$return_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/catering-selfservice/track_order.php?order_id=" . $order_id;

// DB Transaction to save order
try {
    $conn->beginTransaction();
    
    // Insert into orders table (initial status pending)
    $stmt = $conn->prepare("INSERT INTO orders (order_id, customer_name, customer_email, customer_phone, delivery_address, delivery_date, total_amount, payment_status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
    $stmt->execute([$order_id, $customer_name, $customer_email, $customer_phone, $delivery_address, $delivery_date, $total_amount, $payment_method]);
    
    // Insert into order_items
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt_item->execute([$order_id, $id, $qty, $menu_data[$id]['price']]);
    }
    
    // Request Closed Payment Transaction to Tripay
    $tripay_response = createTripayTransaction(
        $order_id, 
        $total_amount, 
        $payment_method, 
        $customer_name, 
        $customer_email, 
        $customer_phone, 
        $order_items, 
        $return_url
    );
    
    if (isset($tripay_response['success']) && $tripay_response['success'] === true) {
        $data = $tripay_response['data'];
        
        // Update order with Tripay details
        $stmt_update = $conn->prepare("UPDATE orders SET payment_ref = ?, payment_url = ?, fee = ? WHERE order_id = ?");
        $stmt_update->execute([$data['reference'], $data['checkout_url'], $data['total_fee'], $order_id]);
        
        $conn->commit();
        $_SESSION['cart'] = []; // Clear cart
        
        // Redirect user to Tripay Checkout URL
        header("Location: " . $data['checkout_url']);
        exit;
        
    } else {
        $error_msg = isset($tripay_response['message']) ? $tripay_response['message'] : "Unknown error connecting to Tripay.";
        throw new Exception("Failed to create Tripay transaction: " . $error_msg);
    }
    
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    die("Error processing order: " . htmlspecialchars($e->getMessage()));
}
?>
