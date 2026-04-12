<?php
$title = 'Order Detail';
require_admin();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT o.*, u.name AS user_name, u.email, a.recipient_name, a.phone, a.address_line, a.city, a.province, a.postal_code
    FROM orders o
    JOIN users u ON u.id = o.user_id
    JOIN addresses a ON a.id = o.address_id
    WHERE o.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    die('Order tidak ditemukan.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $orderStatus = trim($_POST['order_status'] ?? $order['order_status']);
    $pdo->prepare('UPDATE orders SET order_status = ? WHERE id = ?')->execute([$orderStatus, $id]);
    admin_log('Update status pesanan', ['order_id' => $id, 'status' => $orderStatus]);
    flash('success', 'Status order diperbarui.');
    redirect('/admin/order-detail?id=' . $id);
}

$itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

require BASE_PATH . '/views/layouts/admin_header.php';
?>
<h1>Order Detail</h1>

<div class="card">
    <p><strong>Invoice:</strong> <?= e($order['order_code']) ?></p>
    <p><strong>User:</strong> <?= e($order['user_name']) ?> (<?= e($order['email']) ?>)</p>
    <p><strong>Penerima:</strong> <?= e($order['recipient_name']) ?> - <?= e($order['phone']) ?></p>
    <p><strong>Alamat:</strong> <?= e($order['address_line']) ?>, <?= e($order['city']) ?>, <?= e($order['province']) ?> <?= e($order['postal_code']) ?></p>
    <p><strong>Total:</strong> <?= rupiah($order['total']) ?></p>
    <p><strong>Status Pembayaran:</strong> <?= e($order['payment_status']) ?></p>
</div>

<div class="card">
    <h3>Item Pesanan</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['product_name']) ?></td>
                    <td><?= $item['qty'] ?></td>
                    <td><?= rupiah($item['product_price']) ?></td>
                    <td><?= rupiah($item['line_total']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h3>Update Status Order</h3>
    <form method="post" class="inline-form">
        <?= csrf_input() ?>
        <select name="order_status">
            <?php foreach (['new','processed','shipped','completed','cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= $order['order_status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn">Update</button>
    </form>
</div>

<?php require BASE_PATH . '/views/layouts/admin_footer.php'; ?>