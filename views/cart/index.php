<?php
$title = 'Cart';
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'update' && isset($_SESSION['cart'][$id])) {
        $qty = max(1, (int) ($_POST['qty'] ?? 1));

        $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            unset($_SESSION['cart'][$id]);
            flash('error', 'Produk tidak ditemukan dan dihapus dari keranjang.');
            redirect('/cart');
        }

        if ($qty > (int) $product['stock']) {
            $qty = (int) $product['stock'];
            flash('error', 'Jumlah melebihi stok tersedia. Qty disesuaikan.');
        }

        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['qty'] = $qty;
            $_SESSION['cart'][$id]['stock'] = (int) $product['stock'];
        }
    }

    if ($action === 'remove') {
        unset($_SESSION['cart'][$id]);
    }

    redirect('/cart');
}

$items = $_SESSION['cart'];
require BASE_PATH . '/views/layouts/header.php';
?>
<h1 style="margin-bottom:18px;">Keranjang Belanja</h1>

<?php if (!$items): ?>
    <div class="card">
        <p style="margin:0;font-size:1.05rem;">Keranjang kamu masih kosong.</p>
    </div>
<?php else: ?>
    <div class="card">
        <table class="table">
            <thead>
                <tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Total</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['name']) ?></td>
                    <td><?= rupiah($item['price']) ?></td>
                    <td>
                        <form method="post" class="inline-form">
                            <?= csrf_input() ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <input type="number" name="qty" min="1" max="<?= (int) ($item['stock'] ?? 9999) ?>" value="<?= $item['qty'] ?>">
                            <button class="btn btn-sm">Update</button>
                        </form>
                    </td>
                    <td><?= rupiah($item['price'] * $item['qty']) ?></td>
                    <td>
                        <form method="post">
                            <?= csrf_input() ?>
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <h3>Subtotal: <?= rupiah(cart_subtotal()) ?></h3>
        <a class="btn" href="<?= BASE_URL ?>/checkout">Checkout</a>
    </div>
<?php endif; ?>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>