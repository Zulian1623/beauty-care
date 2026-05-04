<?php
use App\Services\UploadService;

$title = 'Tambah Produk';
require_admin();

$brands = $pdo->query('SELECT * FROM brands ORDER BY name ASC')->fetchAll();
$categories = $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $brandId = (int) $_POST['brand_id'];
    $categoryId = (int) $_POST['category_id'];
    $name = trim($_POST['name'] ?? '');
    $slug = slugify($name) . '-' . time();
    $description = trim($_POST['description'] ?? '');
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $expireDate = !empty($_POST['expired_date']) ? $_POST['expired_date'] : null;
    $sku = trim($_POST['sku'] ?? '');

    try {
        $image = UploadService::uploadImage($_FILES['image'], UPLOAD_PRODUCT_DIR);

        $stmt = $pdo->prepare('
            INSERT INTO products
            (brand_id, category_id, name, slug, description, price, stock, expired_date, sku, image, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ');

        $stmt->execute([
            $brandId,
            $categoryId,
            $name,
            $slug,
            $description,
            $price,
            $stock,
            $expireDate,
            $sku,
            $image
        ]);

        $productId = (int) $pdo->lastInsertId();

        $extraImages = UploadService::uploadMultipleImages($_FILES['gallery'], UPLOAD_PRODUCT_DIR);
        foreach ($extraImages as $index => $galleryImage) {
            $pdo->prepare('INSERT INTO product_images(product_id,image,is_primary) VALUES(?,?,?)')
                ->execute([$productId, $galleryImage, $index === 0 ? 1 : 0]);
        }

        admin_log('Menambahkan brand', ['name' => $name, 'description' => $description]);

        flash('success', 'Produk berhasil ditambahkan.');
        redirect('/admin/products');
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
        redirect('/admin/products/create');
    }
}

require BASE_PATH . '/views/layouts/admin_header.php';
?>
<h1>Tambah Produk</h1>
<form method="post" enctype="multipart/form-data" class="card form-grid">
    <?= csrf_input() ?>
    <label>Brand<select name="brand_id" required><?php foreach ($brands as $b): ?><option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option><?php endforeach; ?></select></label>
    <label>Kategori<select name="category_id" required><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
    <label>Nama Produk<input type="text" name="name" required></label>
    <label>SKU<input type="text" name="sku"></label>
    <label>Harga<input type="number" name="price" required></label>
    <label>Stok<input type="number" name="stock" required></label>
    <label>Tanggal Kadaluarsa<input type="date" name="expired_date"></label>
    <label>Deskripsi<textarea name="description" required></textarea></label>
    <label>Gambar Utama<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"></label>
    <label>Gallery Produk<input type="file" name="gallery[]" multiple accept=".jpg,.jpeg,.png,.webp"></label>
    <button class="btn">Simpan</button>
</form>
<?php require BASE_PATH . '/views/layouts/admin_footer.php'; ?>