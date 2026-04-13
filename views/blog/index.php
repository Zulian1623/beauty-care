<?php
$title = 'Blog';
require BASE_PATH . '/views/layouts/header.php';

$posts = [
    [
        'category' => 'Skincare Tips',
        'title' => '5 Langkah Skincare Routine untuk Kulit Sehat',
        'excerpt' => 'Rutinitas sederhana yang bisa bantu kulit tetap lembap, bersih, dan glowing setiap hari.',
        'image' => BASE_URL . '/assets/img/1.jpg',
        'date' => '13 April 2026',
        'author' => 'Beauty Care Team',
    ],
    [
        'category' => 'Ingredients',
        'title' => 'Kenapa Niacinamide Bagus untuk Wajah?',
        'excerpt' => 'Niacinamide membantu menjaga skin barrier, mengontrol minyak, dan membuat warna kulit tampak lebih merata.',
        'image' => BASE_URL . '/assets/img/1.jpg',
        'date' => '10 April 2026',
        'author' => 'Beauty Care Team',
    ],
    [
        'category' => 'Beauty Guide',
        'title' => 'Cara Memilih Serum Sesuai Jenis Kulit',
        'excerpt' => 'Kenali kebutuhan kulitmu dulu sebelum memilih serum agar hasil perawatan lebih maksimal.',
        'image' => BASE_URL . '/assets/img/1.jpg',
        'date' => '8 April 2026',
        'author' => 'Beauty Care Team',
    ],
];
?>

<section class="blog-page">
    <div class="section-intro">
        <span class="hero-badge">Beauty Blog</span>
        <h1>Tips, Insight & Skincare Articles</h1>
        <p>
            Temukan berbagai artikel seputar perawatan kulit, tips kecantikan,
            dan rekomendasi produk untuk rutinitas harianmu.
        </p>
    </div>

    <div class="blog-grid">
        <?php foreach ($posts as $post): ?>
            <article class="card blog-card">
                <img class="blog-thumb" src="<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>">

                <div class="blog-content">
                    <span class="blog-category"><?= e($post['category']) ?></span>
                    <h3><?= e($post['title']) ?></h3>
                    <p class="blog-meta"><?= e($post['date']) ?> • <?= e($post['author']) ?></p>
                    <p><?= e($post['excerpt']) ?></p>

                    <a class="btn btn-outline blog-btn" href="#">
                        Read More
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>