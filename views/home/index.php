<?php
$title = 'Home';

$stmt = $pdo->query("
    SELECT p.*, b.name AS brand_name
    FROM products p
    JOIN brands b ON b.id = p.brand_id
    WHERE p.is_active = 1 
    AND (p.expired_date IS NULL OR p.expired_date >= CURDATE())
    ORDER BY p.id DESC
    LIMIT 8
");
$products = $stmt->fetchAll();

$totalProducts = (int) $pdo->query("
    SELECT COUNT(*) FROM products 
    WHERE is_active = 1 
    AND (expired_date IS NULL OR expired_date >= CURDATE())
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
            <a class="btn hero-btn" href="#catalog">Shop Now</a>
            <a class="btn btn-outline hero-btn-outline" href="#about">Learn More</a>
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

<section id="about" class="about-section">
    <div class="about-container">
        <div class="about-copy">
            <span class="hero-badge">About Beauty Care</span>

            <h2>Natural Skincare for Your Daily Glow</h2>

            <p>
                Beauty Care menghadirkan koleksi skincare pilihan yang dirancang untuk
                membantu merawat, menutrisi, dan menjaga kesehatan kulit setiap hari.
                Dengan bahan-bahan alami yang dipilih secara hati-hati, setiap produk
                dibuat agar nyaman digunakan dan cocok untuk melengkapi rutinitas
                perawatan kulitmu.
            </p>

            <p>
                Kami percaya bahwa kulit sehat dimulai dari perawatan yang lembut,
                aman, dan konsisten. Karena itu, produk Beauty Care diformulasikan
                untuk membantu menampilkan kilau alami kulit, menjaga kelembapan,
                serta membuat kulit tampak lebih segar, bersih, dan bercahaya.
            </p>

            <div class="about-actions" style="margin-bottom: 20px;">
                <a class="btn hero-btn" href="#catalog">Explore Products</a>
            </div>
        </div>

        <div class="about-card card">
            <h3>Why Choose Us?</h3>

            <ul>
                <li>Made with selected natural ingredients</li>
                <li>Gentle formula for daily skincare routine</li>
                <li>Designed to nourish and protect your skin</li>
                <li>Suitable for customers who love soft and glowing skin</li>
            </ul>
        </div>
    </div>
</section>

<section id="catalog" class="products-home">
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