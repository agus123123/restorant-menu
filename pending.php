<?php
require_once 'includes/header.php';
$order_id = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : '';
?>

<div class="container" style="padding: 5rem 20px; text-align: center;">
    <div style="background: var(--white); padding: 3rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); max-width: 600px; margin: 0 auto;">
        <i class="fas fa-clock" style="font-size: 5rem; color: #f39c12; margin-bottom: 1.5rem;"></i>
        <h2 style="margin-bottom: 1rem;">Menunggu Pembayaran</h2>
        <p style="font-size: 1.1rem; margin-bottom: 2rem;">Silakan selesaikan pembayaran Anda untuk Order ID: <strong><?= $order_id ?></strong></p>
        
        <a href="index.php" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">Kembali ke Beranda</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
