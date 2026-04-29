<?php
$title = 'User Dashboard';
require_login();
require BASE_PATH . '/views/layouts/header.php';
?>
<h1>Halo, <?= e(current_user()['name']) ?></h1>
<div class="grid two-cols">
    <a class="card" href="<?= BASE_URL ?>/user/addresses">Kelola Alamat</a>
    <a class="card" href="<?= BASE_URL ?>/user/orders">Pesanan Saya</a>
    <a href="<?= BASE_URL ?>/logout">Logout</a>
</div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>