<?php
$title = 'Pesanan Saya';
require_login();

$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([current_user_id()]);
$orders = $stmt->fetchAll();

require BASE_PATH . '/views/layouts/header.php';
?>

<h1>Pesanan Saya</h1>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Total</th>
                <th>Status</th>
                <th>Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($orders)): ?>
            <tr>
                <td colspan="5" style="text-align:center;">Belum ada pesanan</td>
            </tr>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= e($order['order_code']) ?></td>
                    <td><?= rupiah($order['total']) ?></td>
                    <td><?= e($order['order_status']) ?></td>
                    <td><?= e($order['payment_status']) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/user/order-detail?id=<?= $order['id'] ?>">Detail</a> |
                        <a href="<?= BASE_URL ?>/user/invoice?id=<?= $order['id'] ?>">Invoice</a>

                        <?php if ($order['order_status'] === 'shipped'): ?>
                            |
                            <form method="post" action="<?= BASE_URL ?>/order/confirm" style="display:inline;">
                                <?= csrf_input() ?>
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button class="btn btn-sm" onclick="return confirm('Yakin pesanan sudah diterima?')">
                                    Pesanan Diterima
                                </button>
                            </form>

                        <?php elseif ($order['order_status'] === 'completed'): ?>
                            | <span style="color: green; font-weight: bold;">Selesai</span>

                        <?php elseif ($order['order_status'] === 'cancelled'): ?>
                            | <span style="color: red;">Dibatalkan</span>

                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>