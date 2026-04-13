<?php
$title = 'Home';

$stmt = $pdo->query("
    SELECT p.*, b.name AS brand_name
    FROM products p
    JOIN brands b ON b.id = p.brand_id
    WHERE p.is_active = 1
    ORDER BY p.id DESC
    LIMIT 8
");
$products = $stmt->fetchAll();

$totalProducts = (int) $pdo->query("
    SELECT COUNT(*) FROM products WHERE is_active = 1
")->fetchColumn();

require BASE_PATH . '/views/layouts/header.php';
?>

<section class="hero-section">
    <div class="hero-copy">
        <span class="hero-badge">✦ New Collection Available</span>

        <h1 class="hero-title">
            Radiant Skin <span>Starts Here</span>
        </h1>

        <p class="hero-desc">
            Discover our luxurious skincare collection crafted with natural ingredients
            to reveal your natural glow and beauty.
        </p>

        <div class="hero-actions">
            <a class="btn hero-btn" href="<?= BASE_URL ?>/products">Shop Now</a>
            <a class="btn btn-outline hero-btn-outline" href="#popular-products">Learn More</a>
        </div>

        <div class="hero-stats">
            <div>
                <strong>100%</strong>
                <span>Natural</span>
            </div>
            <div>
                <strong>50k+</strong>
                <span>Happy Customers</span>
            </div>
            <div>
                <strong>4.9★</strong>
                <span>Rating</span>
            </div>
        </div>
    </div>

    <div class="hero-media">
        <div class="hero-image-wrap">
            <img src="<?= BASE_URL ?>/assets/img/1.jpg" alt="Beauty Care">
        </div>
    </div>
</section>

<section class="benefits-section">
    <div class="benefit-card card">
        <div class="benefit-icon">🍃</div>
        <h3>100% Natural</h3>
        <p>All our products are made from natural and organic ingredients</p>
    </div>

    <div class="benefit-card card">
        <div class="benefit-icon">🛡</div>
        <h3>Dermatologist Tested</h3>
        <p>Clinically tested and approved by skin care professionals</p>
    </div>

    <div class="benefit-card card">
        <div class="benefit-icon">♡</div>
        <h3>Cruelty Free</h3>
        <p>We never test on animals and support ethical practices</p>
    </div>

    <div class="benefit-card card">
        <div class="benefit-icon">✦</div>
        <h3>Visible Results</h3>
        <p>See noticeable improvements in just 2-4 weeks</p>
    </div>
</section>

<section id="popular-products" class="products-home">
    <div class="section-intro">
        <span class="hero-badge">Best Sellers</span>
        <h2>Our Popular Products</h2>
        <p>Showing <?= count($products) ?> of <?= $totalProducts ?> products</p>
    </div>

    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <article class="card product-card">
                <?php if ($product['image']): ?>
                    <img
                        class="product-thumb"
                        src="<?= BASE_URL ?>/uploads/products/<?= e($product['image']) ?>"
                        alt="<?= e($product['name']) ?>"
                    >
                <?php else: ?>
                    <img
                        class="product-thumb"
                        src="<?= BASE_URL ?>/assets/img/1.jpg"
                        alt="<?= e($product['name']) ?>"
                    >
                <?php endif; ?>

                <h3><?= e($product['name']) ?></h3>
                <p class="product-brand"><?= e($product['brand_name']) ?></p>
                <p><?= e(mb_strimwidth(strip_tags($product['description']), 0, 90, '...')) ?></p>
                <strong><?= rupiah($product['price']) ?></strong>

                <div style="margin-top:14px;">
                    <a class="btn btn-outline product-link" href="<?= BASE_URL ?>/product?slug=<?= e($product['slug']) ?>">
                        Detail
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="show-all-wrap">
        <a class="btn" href="<?= BASE_URL ?>/products">Show All Products</a>
    </div>
</section>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>