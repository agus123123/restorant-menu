<?php
session_start();
require_once 'config/database.php';
require_once 'config/tripay.php';

// Fetch available payment channels from Tripay API
$payment_channels = [];
// In a real app, you might want to cache this response or store it in DB to avoid hitting Tripay API on every cart load
$tripay_response = getTripayChannels();
if (isset($tripay_response['success']) && $tripay_response['success'] === true) {
    $payment_channels = $tripay_response['data'];
} else {
    // Fallback or error handling if Tripay API fails to load channels
    error_log("Failed to load Tripay channels: " . print_r($tripay_response, true));
}

// Remove item
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header("Location: cart.php");
    exit;
}

// Fetch Cart Data
$cart_items = [];
$total_amount = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $conn->query("SELECT * FROM menus WHERE id IN ($ids)");
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($menus as $menu) {
        $qty = $_SESSION['cart'][$menu['id']];
        $subtotal = $qty * $menu['price'];
        $total_amount += $subtotal;
        
        $cart_items[] = [
            'id' => $menu['id'],
            'name' => htmlspecialchars($menu['name']),
            'price' => $menu['price'],
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    }
}

require_once 'includes/header.php';
?>

<div class="container" style="padding: 4rem 20px;">
    <h2 class="section-title">Keranjang Belanja</h2>

    <?php if (empty($cart_items)): ?>
        <div class="text-center">
            <p>Keranjang Anda masih kosong.</p>
            <a href="index.php#menu" class="btn btn-primary mt-4">Pesan Sekarang</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <!-- Cart Items List -->
            <div style="background: var(--white); padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow-x: auto;">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?= $item['name'] ?></td>
                                <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                <td><?= $item['qty'] ?></td>
                                <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                <td><a href="cart.php?remove=<?= $item['id'] ?>" style="color: var(--danger-color);"><i class="fas fa-trash"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="cart-total">
                    Total: Rp <?= number_format($total_amount, 0, ',', '.') ?>
                </div>
            </div>

            <!-- Checkout Form -->
            <div style="background: var(--white); padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem;">Informasi Pemesanan & Pembayaran</h3>
                <form action="checkout.php" method="POST">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="customer_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="tel" name="customer_phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Pengiriman</label>
                        <input type="date" name="delivery_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap Pengiriman</label>
                        <textarea name="delivery_address" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem; margin-top: 1.5rem;">
                        <label class="form-label">Metode Pembayaran (Tripay)</label>
                        <select name="method" class="form-control" required style="cursor: pointer;">
                            <option value="">-- Pilih Metode Pembayaran --</option>
                            <?php if(!empty($payment_channels)): ?>
                                <?php foreach($payment_channels as $channel): ?>
                                    <?php if($channel['active']): ?>
                                        <option value="<?= htmlspecialchars($channel['code']) ?>">
                                            <?= htmlspecialchars($channel['name']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="BRIVA">BRI Virtual Account</option>
                                <option value="BCAVA">BCA Virtual Account</option>
                                <option value="QRIS">QRIS</option>
                                <option value="ALFAMART">Alfamart</option>
                            <?php endif; ?>
                        </select>
                        <small style="color: var(--text-light); display: block; margin-top: 0.5rem;">Daftar metode diambil dari Tripay API.</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="font-size: 1.1rem; padding: 1rem;">Buat Pesanan & Bayar</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
