<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$order_id = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : '';
$order = null;
$error = '';

if ($order_id) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $error = 'Pesanan tidak ditemukan. Periksa kembali Order ID Anda.';
    }
}
?>

<div class="container" style="padding: 5rem 20px; min-height: 70vh;">
    <div style="max-width: 600px; margin: 0 auto; background: var(--white); padding: 3rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <h2 style="margin-bottom: 2rem; text-align: center; color: var(--secondary-color); font-family: var(--font-heading);">Lacak Pesanan Anda</h2>
        
        <form action="track_order.php" method="GET" style="margin-bottom: 2rem;">
            <div class="form-group d-flex" style="gap: 10px;">
                <input type="text" name="order_id" class="form-control" placeholder="Masukkan Order ID (Contoh: SAV-16...)" value="<?= $order_id ?>" required style="flex-grow: 1;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cek</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div style="background-color: #fce4e4; color: var(--danger-color); padding: 1rem; border-radius: var(--radius-md); text-align: center;">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($order): ?>
            <div style="border-top: 2px solid var(--border-color); padding-top: 2rem; margin-top: 1rem;">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.3rem;">Detail Pesanan</h3>
                
                <table style="width: 100%; border-collapse: separate; border-spacing: 0 10px;">
                    <tr>
                        <td style="color: var(--text-light); width: 40%;">Order ID</td>
                        <td style="font-weight: 600;"><?= htmlspecialchars($order['order_id']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-light);">Nama Pemesan</td>
                        <td style="font-weight: 600;"><?= htmlspecialchars($order['customer_name']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-light);">Tanggal Pengiriman</td>
                        <td style="font-weight: 600;"><?= date('d F Y', strtotime($order['delivery_date'])) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-light);">Total Tagihan</td>
                        <td style="font-weight: 600; color: var(--primary-color);">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?> <small>(+ Fee: Rp <?= number_format($order['fee'], 0, ',', '.') ?>)</small></td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-light);">Metode Pembayaran</td>
                        <td style="font-weight: 600;"><?= htmlspecialchars($order['payment_method']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-light);">Nomor Referensi</td>
                        <td style="font-weight: 600;"><?= htmlspecialchars($order['payment_ref']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-light);">Status Pembayaran</td>
                        <td>
                            <?php
                            $status_color = '#666';
                            $status_text = strtoupper($order['payment_status']);
                            if ($order['payment_status'] == 'settlement') {
                                $status_color = 'var(--success-color)';
                            } else if ($order['payment_status'] == 'pending') {
                                $status_color = '#f39c12';
                            } else if ($order['payment_status'] == 'cancel' || $order['payment_status'] == 'expire') {
                                $status_color = 'var(--danger-color)';
                            }
                            ?>
                            <span style="background-color: <?= $status_color ?>; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; display: inline-block;">
                                <?= $status_text ?>
                            </span>
                        </td>
                    </tr>
                </table>
                
                <?php if ($order['payment_status'] == 'pending'): ?>
                    <div class="mt-4 text-center">
                        <p style="margin-bottom: 1rem; font-size: 0.9rem; color: var(--text-light);">Segera selesaikan pembayaran agar pesanan dapat diproses.</p>
                        <?php if (!empty($order['payment_url'])): ?>
                            <a href="<?= htmlspecialchars($order['payment_url']) ?>" class="btn btn-outline btn-block" target="_blank">Lanjutkan / Instruksi Pembayaran</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
