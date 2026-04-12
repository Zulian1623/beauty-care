<?php
$title = 'Stock Movement';
require_admin();

$productId = (int) ($_GET['id'] ?? 0);

$productStmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
$productStmt->execute([$productId]);
$product = $productStmt->fetch();

if (!$product) {
    die('Produk tidak ditemukan.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $type = $_POST['movement_type'] ?? 'adjustment';
    $qty = max(1, (int) ($_POST['qty'] ?? 0));
    $note = trim($_POST['note'] ?? '');

    if ($type === 'in') {
        $pdo->prepare('UPDATE products SET stock = stock + ? WHERE id = ?')->execute([$qty, $productId]);
    } elseif ($type === 'out') {
        $pdo->prepare('UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id = ?')->execute([$qty, $productId]);
    } else {
        $pdo->prepare('UPDATE products SET stock = ? WHERE id = ?')->execute([$qty, $productId]);
    }

    $pdo->prepare('INSERT INTO stock_movements(product_id,movement_type,qty,note) VALUES(?,?,?,?)')
        ->execute([$productId, $type, $qty, $note]);

    admin_log('Pergerakan stok produk', [
        'product_id' => $productId,
        'product_name' => $product['name'],
        'movement_type' => $type,
        'qty' => $qty,
        'note' => $note
    ]);

    flash('success', 'Pergerakan stok disimpan.');
    redirect('/admin/products/stock?id=' . $productId);
}

$movementsStmt = $pdo->prepare('SELECT * FROM stock_movements WHERE product_id = ? ORDER BY id DESC');
$movementsStmt->execute([$productId]);
$movements = $movementsStmt->fetchAll();

require BASE_PATH . '/views/layouts/admin_header.php';
?>
<h1>Stock Movement - <?= e($product['name']) ?></h1>

<div class="grid two-cols">
    <form method="post" class="card form-grid">
        <?= csrf_input() ?>
        <label>Tipe
            <select name="movement_type">
                <option value="in">Stock In</option>
                <option value="out">Stock Out</option>
                <option value="adjustment">Adjustment</option>
            </select>
        </label>

        <label>Qty
            <input type="number" name="qty" min="1" required>
        </label>

        <label>Catatan
            <textarea name="note"></textarea>
        </label>

        <button class="btn">Simpan</button>
    </form>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Tipe</th>
                    <th>Qty</th>
                    <th>Catatan</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movements as $movement): ?>
                    <tr>
                        <td><?= e($movement['movement_type']) ?></td>
                        <td><?= $movement['qty'] ?></td>
                        <td><?= e($movement['note']) ?></td>
                        <td><?= e($movement['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/admin_footer.php'; ?>