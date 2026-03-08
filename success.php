<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$order_id = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : '';

// Update status to settlement immediately based on user redirection, 
// though the webhook is the primary source of truth.
if ($order_id) {
    $stmt = $conn->prepare("UPDATE orders SET payment_status = 'settlement' WHERE order_id = ? AND payment_status = 'pending'");
    $stmt->execute([$order_id]);
}
?>

<div class="container" style="padding: 5rem 20px; text-align: center;">
    <div style="background: var(--white); padding: 3rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); max-width: 600px; margin: 0 auto;">
        <i class="fas fa-check-circle" style="font-size: 5rem; color: var(--success-color); margin-bottom: 1.5rem;"></i>
        <h2 style="margin-bottom: 1rem;">Pembayaran Berhasil!</h2>
        <p style="font-size: 1.1rem; margin-bottom: 2rem;">Terima kasih atas pesanan Anda. Order ID Anda: <strong><?= $order_id ?></strong></p>
        <p style="margin-bottom: 2rem;">Pesanan Anda sedang kami proses dan tim Savoria akan segera menghubungi Anda melalui WhatsApp untuk konfirmasi pengiriman.</p>
        
        <a href="index.php" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">Kembali ke Beranda</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
