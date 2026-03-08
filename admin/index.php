<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Fetch Orders
$stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Savoria</title>
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: var(--secondary-color);
            color: white;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            color: #ccc;
            padding: 1rem;
            display: block;
            border-radius: var(--radius-md);
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .main-content {
            flex-grow: 1;
            padding: 2rem;
            background-color: #f1f5f9;
        }
    </style>
</head>
<body>

<div class="admin-layout">
    <aside class="sidebar">
        <h2 style="color: var(--primary-color); font-family: var(--font-heading); margin-bottom: 2rem; font-size: 1.8rem;">Savoria.</h2>
        <a href="index.php" class="active"><i class="fas fa-shopping-bag" style="width: 25px;"></i> Pesanan Masuk</a>
        <a href="#" onclick="alert('Fitur Manajemen Menu sedang dalam pengembangan')"><i class="fas fa-utensils" style="width: 25px;"></i> Kelola Menu</a>
        
        <div style="margin-top: auto;">
            <a href="logout.php" style="color: var(--danger-color);"><i class="fas fa-sign-out-alt" style="width: 25px;"></i> Keluar</a>
        </div>
    </aside>

    <main class="main-content">
        <h2 style="margin-bottom: 2rem; color: var(--secondary-color);">Daftar Pesanan</h2>
        
        <div style="background: var(--white); padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow-x: auto;">
            <table class="cart-table" style="min-width: 900px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Waktu</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($order['order_id']) ?></strong></td>
                        <td><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?><br><small><?= htmlspecialchars($order['customer_phone']) ?></small></td>
                        <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
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
                            <span style="background-color: <?= $status_color ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">
                                <?= $status_text ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;" onclick="alert('Metode: <?= $order['payment_method'] ?? '-' ?>\nRef: <?= $order['payment_ref'] ?? '-' ?>\nAlamat: <?= addslashes($order['delivery_address']) ?>\nTanggal Kirim: <?= date('d M Y', strtotime($order['delivery_date'])) ?>')">Detail Lengkap</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($orders)): ?>
                        <tr><td colspan="6" class="text-center">Belum ada pesanan</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
