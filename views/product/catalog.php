<?php
$title = 'Catalog';
$q = trim($_GET['q'] ?? '');
$brand = (int)($_GET['brand'] ?? 0);
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 8;
$offset = ($page - 1) * $limit;

$brands = $pdo->query('SELECT * FROM brands ORDER BY name ASC')->fetchAll();

$where = " WHERE p.is_active = 1 AND (p.expired_date IS NULL OR p.expired_date >= CURDATE()) ";
$params = [];

if ($q !== '') {
    $where .= ' AND (p.name LIKE ? OR p.description LIKE ? OR b.name LIKE ?) ';
    $kw = "%$q%";
    $params[] = $kw;
    $params[] = $kw;
    $params[] = $kw;
}
if ($brand > 0) {
    $where .= ' AND p.brand_id = ? ';
    $params[] = $brand;
}

$countSql = "SELECT COUNT(*) total FROM products p JOIN brands b ON b.id = p.brand_id $where";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = (int)$countStmt->fetch()['total'];
$totalPages = max(1, (int)ceil($total / $limit));

$sql = "SELECT p.*, b.name AS brand_name FROM products p JOIN brands b ON b.id = p.brand_id $where ORDER BY p.id DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

require BASE_PATH . '/views/layouts/header.php';
?>
<h1>Catalog</h1>
<form class="card search-bar" method="get" action="<?= BASE_URL ?>/catalog">
    <input type="text" name="q" placeholder="Cari produk..." value="<?= e($q) ?>">
    <select name="brand">
        <option value="0">Semua Brand</option>
        <?php foreach ($brands as $b): ?>
            <option value="<?= $b['id'] ?>" <?= $brand === (int)$b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn">Search</button>
</form>

<div class="grid products-grid">
    <?php foreach ($products as $product): ?>
        <article class="card product-card">
            <?php if ($product['image']): ?>
                <img class="product-thumb" src="<?= BASE_URL ?>/uploads/products/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
            <?php endif; ?>
            <h3><?= e($product['name']) ?></h3>
            <p><?= e($product['brand_name']) ?></p>
            <p><?= e(mb_strimwidth(strip_tags($product['description']), 0, 80, '...')) ?></p>
            <strong><?= rupiah($product['price']) ?></strong>
            <a class="btn btn-outline" href="<?= BASE_URL ?>/product?slug=<?= e($product['slug']) ?>">Detail</a>
        </article>
    <?php endforeach; ?>
</div>

<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="page-link <?= $i === $page ? 'active' : '' ?>" href="<?= BASE_URL ?>/catalog?q=<?= urlencode($q) ?>&brand=<?= $brand ?>&page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
