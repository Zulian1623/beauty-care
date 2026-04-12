<?php
use App\Services\UploadService;
$title = 'Edit Produk';
require_admin();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) die('Produk tidak ditemukan.');
$brands = $pdo->query('SELECT * FROM brands ORDER BY name ASC')->fetchAll();
$categories = $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $brandId = (int)$_POST['brand_id'];
    $categoryId = (int)$_POST['category_id'];
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $sku = trim($_POST['sku'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $image = $product['image'];

    try {
        $newImage = UploadService::uploadImage($_FILES['image'], UPLOAD_PRODUCT_DIR);
        if ($newImage) $image = $newImage;
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
        redirect('/admin/products/edit?id=' . $id);
    }

    $update = $pdo->prepare('UPDATE products SET brand_id=?, category_id=?, name=?, description=?, price=?, stock=?, sku=?, image=?, is_active=? WHERE id=?');
    $update->execute([$brandId, $categoryId, $name, $description, $price, $stock, $sku, $image, $isActive, $id]);
    admin_log('Mengupdate brand', ['id' => $id, 'name' => $name, 'description' => $description]);
    flash('success', 'Produk berhasil diperbarui.');
    redirect('/admin/products');
}

require BASE_PATH . '/views/layouts/admin_header.php';
?>
<h1>Edit Produk</h1>
<form method="post" enctype="multipart/form-data" class="card form-grid">
    <?= csrf_input() ?>
    <label>Brand<select name="brand_id" required><?php foreach ($brands as $b): ?><option value="<?= $b['id'] ?>" <?= $product['brand_id'] == $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?></select></label>
    <label>Kategori<select name="category_id" required><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
    <label>Nama Produk<input type="text" name="name" value="<?= e($product['name']) ?>" required></label>
    <label>SKU<input type="text" name="sku" value="<?= e($product['sku']) ?>"></label>
    <label>Harga<input type="number" name="price" value="<?= e($product['price']) ?>" required></label>
    <label>Stok<input type="number" name="stock" value="<?= e($product['stock']) ?>" required></label>
    <label>Deskripsi<textarea name="description" required><?= e($product['description']) ?></textarea></label>
    <label>Gambar Baru<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"></label>
    <label><input type="checkbox" name="is_active" <?= $product['is_active'] ? 'checked' : '' ?>> Aktif</label>
    <button class="btn">Update</button>
</form>
<?php require BASE_PATH . '/views/layouts/admin_footer.php'; ?>
