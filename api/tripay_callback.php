<?php
// api/tripay_callback.php

require_once '../config/database.php';
require_once '../config/tripay.php';

$privateKey = TRIPAY_PRIVATE_KEY;
$json_result = file_get_contents('php://input');

$signature = hash_hmac('sha256', $json_result, $privateKey);

// Validate signature from Tripay
if ($signature !== ($_SERVER['HTTP_X_CALLBACK_SIGNATURE'] ?? '')) {
    http_response_code(403);
    echo "Invalid Signature";
    exit;
}

$callback = json_decode($json_result, true);

if (!is_array($callback)) {
    http_response_code(400);
    echo "Invalid JSON callback";
    exit;
}

$merchantRef = $callback['merchant_ref'];
$tripayRef   = $callback['reference'];
$status      = $callback['status']; // PAID, UNPAID, EXPIRED, FAILED, REFUND

// Normalize status to our DB format
$new_status = 'pending';
if ($status === 'PAID') {
    $new_status = 'settlement';
} elseif ($status === 'EXPIRED' || $status === 'FAILED') {
    $new_status = 'expire';
} elseif ($status === 'REFUND') {
    $new_status = 'cancel'; // Or add 'refund' to your database enum
}

try {
    // Update order status based on callback
    $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE order_id = ? AND payment_ref = ?");
    $stmt->execute([$new_status, $merchantRef, $tripayRef]);

    $response = ['success' => true];
    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
