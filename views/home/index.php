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

require BASE_PATH . '/views/layouts/header.php';
?>

<section class="hero">
    <div>
        <span class="badge" style="background:#fce7f3;color:#e11d8a;">✨ New Collection</span>

        <h1>
            Radiant Skin <br>
            <span style="color:#e11d8a;">Starts Here</span>
        </h1>

        <p id="shortDesc">
            Temukan rangkaian skincare premium kami yang diformulasikan dengan bahan-bahan alami...
        </p>

        <p id="fullDesc" style="display:none;">
            Temukan rangkaian skincare premium kami yang diformulasikan dengan bahan-bahan alami pilihan untuk membantu mengembalikan dan menonjolkan kecantikan alami kulitmu. Setiap produk dirancang secara khusus untuk memberikan perawatan menyeluruh, mulai dari menutrisi, melembapkan, hingga melindungi kulit dari berbagai faktor eksternal seperti polusi dan paparan sinar matahari.

            Kami mengutamakan kualitas dan keamanan dengan menggunakan formulasi yang telah teruji, sehingga aman digunakan untuk pemakaian sehari-hari. Dengan kombinasi bahan aktif yang efektif dan lembut di kulit, produk kami membantu memperbaiki tekstur kulit, menjaga kelembapan, serta memberikan tampilan kulit yang lebih cerah, sehat, dan bercahaya.

            Glowé hadir sebagai solusi perawatan kulit modern yang tidak hanya fokus pada hasil instan, tetapi juga kesehatan kulit jangka panjang. Jadikan rutinitas skincare sebagai momen self-care terbaikmu, dan rasakan perubahan nyata pada kulitmu setiap hari.
        </p>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a class="btn" href="<?= BASE_URL ?>/catalog">Shop Now</a>
            <button type="button" class="btn btn-outline" onclick="toggleDesc()">Learn More</button>
        </div>

        <div style="margin-top:30px;display:flex;gap:30px;flex-wrap:wrap;">
            <div><strong>100%</strong><br><small>Natural</small></div>
            <div><strong>50k+</strong><br><small>Customers</small></div>
            <div><strong>4.9★</strong><br><small>Rating</small></div>
        </div>
    </div>

    <div class="card" style="padding:0;overflow:hidden;">
        <img src="<?= BASE_URL ?>/assets/img/1.jpg" alt="Hero Image" style="width:1000%;height:680px;object-fit:cover;border-radius:20px;">
    </div>
</section>

<section id="catalog">
    <h2>Our Products</h2>

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
</section>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>

<script>
function toggleDesc() {
    const shortDesc = document.getElementById('shortDesc');
    const fullDesc = document.getElementById('fullDesc');

    if (fullDesc.style.display === 'none') {
        fullDesc.style.display = 'block';
        shortDesc.style.display = 'none';
    } else {
        fullDesc.style.display = 'none';
        shortDesc.style.display = 'block';
    }
}
</script>