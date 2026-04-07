<?php
$title = 'Home';
require BASE_PATH . '/views/layouts/header.php';
?>

<section class="hero">
    <div>
        <span class="badge" style="background:#fce7f3;color:#e11d8a;">✨ New Collection</span>

        <h1>
            Radiant Skin <br>
            <span style="color:#e11d8a;">Starts Here</span>
        </h1>

        <p>
            Discover our luxurious skincare collection crafted with natural ingredients
            to reveal your natural glow and beauty.
        </p>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a class="btn" href="<?= BASE_URL ?>/catalog">Shop Now</a>
            <a class="btn btn-outline" href="<?= BASE_URL ?>/catalog">Learn More</a>
        </div>

        <div style="margin-top:30px;display:flex;gap:30px;flex-wrap:wrap;">
            <div><strong>100%</strong><br><small>Natural</small></div>
            <div><strong>50k+</strong><br><small>Customers</small></div>
            <div><strong>4.9★</strong><br><small>Rating</small></div>
        </div>
    </div>

    <div class="card" style="padding:0;overflow:hidden;">
        <img src="<?= BASE_URL ?>public/assets/img/1.png" alt="Hero Image" style="width:100%;height:680px;object-fit:cover;border-radius:20px;">
    </div>
</section>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>