<?php
$title = 'Manage Products';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        $stmt = $pdo->prepare('SELECT image FROM products WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product) {
            if (!empty($product['image'])) {
                delete_file_if_exists(UPLOAD_PRODUCT_DIR . $product['image']);
            }

            $galleryStmt = $pdo->prepare('SELECT image FROM product_images WHERE product_id = ?');
            $galleryStmt->execute([$id]);
            $galleryImages = $galleryStmt->fetchAll();

            foreach ($galleryImages as $img) {
                delete_file_if_exists(UPLOAD_PRODUCT_DIR . $img['image']);
            }

            $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
            admin_log('Delete product', ['product_id' => $id]);
            admin_log('Menghapus brand', ['id' => $id]);
            flash('success', 'Produk berhasil dihapus.');
        }

        redirect('/admin/products');
    }
}

$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$countStmt = $pdo->prepare('SELECT COUNT(*) total FROM products WHERE name LIKE ?');
$countStmt->execute(["%$q%"]);
$total = (int)$countStmt->fetch()['total'];
$totalPages = max(1, (int)ceil($total / $limit));

$stmt = $pdo->prepare("
    SELECT p.*, b.name AS brand_name, c.name AS category_name
    FROM products p
    JOIN brands b ON b.id=p.brand_id
    JOIN categories c ON c.id=p.category_id
    WHERE p.name LIKE ?
    ORDER BY p.id DESC
    LIMIT $limit OFFSET $offset
");
$stmt->execute(["%$q%"]);
$products = $stmt->fetchAll();

require BASE_PATH . '/views/layouts/admin_header.php';
?>
<h1>Products</h1>
<div class="toolbar">
    <form method="get">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari produk...">
        <button class="btn">Cari</button>
    </form>
    <a class="btn" href="<?= BASE_URL ?>/admin/products/create">Tambah Produk</a>
</div>

<table class="table admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Brand</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td><?= e($product['name']) ?></td>
                <td><?= e($product['brand_name']) ?></td>
                <td><?= e($product['category_name']) ?></td>
                <td><?= rupiah($product['price']) ?></td>
                <td><?= $product['stock'] ?></td>
                <td><?= $product['is_active'] ? '<span class="badge badge-green">Aktif</span>' : '<span class="badge badge-red">Nonaktif</span>' ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/products/edit?id=<?= $product['id'] ?>">Edit</a> |
                    <a href="<?= BASE_URL ?>/admin/products/stock?id=<?= $product['id'] ?>">Stok</a> |
                    <a href="<?= BASE_URL ?>/admin/products/gallery?id=<?= $product['id'] ?>">Gallery</a>
                    <form method="post" class="inline-form" style="display:inline-flex">
                        <?= csrf_input() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                        <button class="btn btn-danger btn-sm" data-confirm="Hapus produk ini?">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="page-link <?= $i === $page ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/products?q=<?= urlencode($q) ?>&page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>

<?php require BASE_PATH . '/views/layouts/admin_footer.php'; ?>