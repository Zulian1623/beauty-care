<?php
$title = 'Categories';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'create';
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    if ($action === 'create') {
        $pdo->prepare('INSERT INTO categories(name) VALUES(?)')->execute([$name]);
        admin_log('Menambahkan kategori', ['name' => $name]);
        flash('success', 'Kategori ditambahkan.');
    }

    if ($action === 'update' && $id > 0) {
        $pdo->prepare('UPDATE categories SET name=? WHERE id=?')->execute([$name, $id]);
        admin_log('Mengupdate kategori', ['id' => $id, 'name' => $name]);
        flash('success', 'Kategori diperbarui.');
    }

    if ($action === 'delete' && $id > 0) {
        try {
            // 1. Set produk jadi tanpa kategori
            $stmt = $pdo->prepare('UPDATE products SET category_id = NULL WHERE category_id = ?');
            $stmt->execute([$id]);

            // 2. Hapus kategori
            $stmt2 = $pdo->prepare('DELETE FROM categories WHERE id = ?');
            $stmt2->execute([$id]);

            admin_log('Menghapus kategori', ['id' => $id]);
            flash('success', 'Kategori dihapus, produk terkait sekarang tidak berkategori.');
        } catch (PDOException $e) {
            // Ini bakal munculin pesan kalau ternyata DB-nya masih nolak NULL
            flash('error', 'Gagal: Pastikan database mengizinkan category_id kosong! (Error: ' . $e->getMessage() . ')');
        }
    }

    redirect('/admin/categories');
}

$editId = (int) ($_GET['edit'] ?? 0);
$editCategory = null;

if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
    $stmt->execute([$editId]);
    $editCategory = $stmt->fetch();
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll();

require BASE_PATH . '/views/layouts/admin_header.php';
?>
<h1>Categories</h1>
<div class="grid two-cols">
    <form method="post" class="card form-grid">
        <?= csrf_input() ?>
        <input type="hidden" name="action" value="<?= $editCategory ? 'update' : 'create' ?>">
        <input type="hidden" name="id" value="<?= $editCategory['id'] ?? 0 ?>">

        <label>Nama Kategori
            <input type="text" name="name" value="<?= e($editCategory['name'] ?? '') ?>" required>
        </label>

        <button class="btn"><?= $editCategory ? 'Update' : 'Simpan' ?></button>
    </form>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= e($category['name']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/categories?edit=<?= $category['id'] ?>">Edit</a>
                            <form method="post" class="inline-form" style="display:inline-flex">
                                <?= csrf_input() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                <button class="btn btn-danger btn-sm" data-confirm="Hapus kategori ini?">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require BASE_PATH . '/views/layouts/admin_footer.php'; ?>