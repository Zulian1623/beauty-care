<?php
$title = 'Brands';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'create';
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($action === 'create') {
        $pdo->prepare('INSERT INTO brands(name,description) VALUES(?,?)')->execute([$name, $description]);

        admin_log('Menambahkan brand', [
            'name' => $name,
            'description' => $description
        ]);

        flash('success', 'Brand ditambahkan.');
    }

    if ($action === 'update' && $id > 0) {
        $pdo->prepare('UPDATE brands SET name=?, description=? WHERE id=?')->execute([$name, $description, $id]);

        admin_log('Mengubah brand', [
            'brand_id' => $id,
            'name' => $name,
            'description' => $description
        ]);

        flash('success', 'Brand diperbarui.');
    }

    if ($action === 'delete' && $id > 0) {
        $pdo->prepare('DELETE FROM brands WHERE id=?')->execute([$id]);

        admin_log('Menghapus brand', [
            'brand_id' => $id
        ]);

        flash('success', 'Brand dihapus.');
    }

    redirect('/admin/brands');
}

$editId = (int) ($_GET['edit'] ?? 0);
$editBrand = null;

if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM brands WHERE id = ? LIMIT 1');
    $stmt->execute([$editId]);
    $editBrand = $stmt->fetch();
}

$brands = $pdo->query('SELECT * FROM brands ORDER BY id DESC')->fetchAll();

require BASE_PATH . '/views/layouts/admin_header.php';
?>
<h1>Brands</h1>

<div class="grid two-cols">
    <form method="post" class="card form-grid">
        <?= csrf_input() ?>
        <input type="hidden" name="action" value="<?= $editBrand ? 'update' : 'create' ?>">
        <input type="hidden" name="id" value="<?= $editBrand['id'] ?? 0 ?>">

        <label>Nama Brand
            <input type="text" name="name" value="<?= e($editBrand['name'] ?? '') ?>" required>
        </label>

        <label>Deskripsi
            <textarea name="description"><?= e($editBrand['description'] ?? '') ?></textarea>
        </label>

        <button class="btn"><?= $editBrand ? 'Update' : 'Simpan' ?></button>
    </form>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($brands as $brand): ?>
                    <tr>
                        <td><?= e($brand['name']) ?></td>
                        <td><?= e($brand['description']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/brands?edit=<?= $brand['id'] ?>">Edit</a>
                            <form method="post" class="inline-form" style="display:inline-flex">
                                <?= csrf_input() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $brand['id'] ?>">
                                <button class="btn btn-danger btn-sm" data-confirm="Hapus brand ini?">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require BASE_PATH . '/views/layouts/admin_footer.php'; ?>