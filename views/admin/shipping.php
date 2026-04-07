<?php
$title = 'Shipping Rates';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'create';
    $id = (int) ($_POST['id'] ?? 0);
    $label = trim($_POST['label'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $cost = (float) ($_POST['cost'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($action === 'create') {
        $pdo->prepare('INSERT INTO shipping_rates(label,city,cost,is_active) VALUES(?,?,?,1)')
            ->execute([$label, $city, $cost]);

        admin_log('Menambahkan ongkir', ['label' => $label, 'city' => $city, 'cost' => $cost]);
        flash('success', 'Ongkir berhasil ditambahkan.');
    }

    if ($action === 'update' && $id > 0) {
        $pdo->prepare('UPDATE shipping_rates SET label=?, city=?, cost=?, is_active=? WHERE id=?')
            ->execute([$label, $city, $cost, $isActive, $id]);
        admin_log('Mengupdate ongkir', ['id' => $id, 'label' => $label, 'city' => $city, 'cost' => $cost]);
        flash('success', 'Ongkir berhasil diperbarui.');
    }

    if ($action === 'delete' && $id > 0) {
        $pdo->prepare('DELETE FROM shipping_rates WHERE id=?')->execute([$id]);
        admin_log('Menghapus ongkir', ['id' => $id]);
        flash('success', 'Ongkir berhasil dihapus.');
    }

    redirect('/admin/shipping');
}

$editId = (int) ($_GET['edit'] ?? 0);
$editRate = null;

if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM shipping_rates WHERE id = ? LIMIT 1');
    $stmt->execute([$editId]);
    $editRate = $stmt->fetch();
}

$rates = $pdo->query('SELECT * FROM shipping_rates ORDER BY id DESC')->fetchAll();

require BASE_PATH . '/views/layouts/admin_header.php';
?>
<h1>Shipping Rates</h1>
<div class="grid two-cols">
    <form method="post" class="card form-grid">
        <?= csrf_input() ?>
        <input type="hidden" name="action" value="<?= $editRate ? 'update' : 'create' ?>">
        <input type="hidden" name="id" value="<?= $editRate['id'] ?? 0 ?>">

        <label>Label
            <input type="text" name="label" value="<?= e($editRate['label'] ?? '') ?>" required>
        </label>

        <label>Kota
            <input type="text" name="city" value="<?= e($editRate['city'] ?? '') ?>" required>
        </label>

        <label>Biaya
            <input type="number" name="cost" value="<?= e((string) ($editRate['cost'] ?? '')) ?>" required>
        </label>

        <label>
            <input type="checkbox" name="is_active" <?= !isset($editRate['is_active']) || $editRate['is_active'] ? 'checked' : '' ?>>
            Aktif
        </label>

        <button class="btn"><?= $editRate ? 'Update' : 'Simpan' ?></button>
    </form>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Kota</th>
                    <th>Biaya</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rates as $rate): ?>
                    <tr>
                        <td><?= e($rate['label']) ?></td>
                        <td><?= e($rate['city']) ?></td>
                        <td><?= rupiah($rate['cost']) ?></td>
                        <td><?= $rate['is_active'] ? 'Aktif' : 'Nonaktif' ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/shipping?edit=<?= $rate['id'] ?>">Edit</a>
                            <form method="post" class="inline-form" style="display:inline-flex">
                                <?= csrf_input() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $rate['id'] ?>">
                                <button class="btn btn-danger btn-sm" data-confirm="Hapus ongkir ini?">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require BASE_PATH . '/views/layouts/admin_footer.php'; ?>