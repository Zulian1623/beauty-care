<?php require_once BASE_PATH . '/app/helpers/functions.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container topbar clean-navbar">
        <a class="brand" href="<?= BASE_URL ?>/">Beauty Care</a>

        <nav class="nav-center">
            <a href="<?= BASE_URL ?>/">Home</a>
            <a href="<?= BASE_URL ?>/catalog">Products</a>
            <a href="<?= BASE_URL ?>/blog">Blog</a>
            <a href="#contact">Contact</a>
        </nav>

        <div class="nav-icons">
            <a href="<?= BASE_URL ?>/catalog" title="Wishlist">♡</a>

            <?php if (is_logged_in()): ?>
                <a href="<?= BASE_URL ?>/user/dashboard" title="Account">👤</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login" title="Account">👤</a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/cart" title="Keranjang">🛒</a>

            <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" target="_blank" title="WhatsApp">WhatsApp</a>
        </div>
    </div>
</header>

<main class="container page-space">
    <?php if ($msg = flash('success')): ?>
        <div class="alert success"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="alert error"><?= e($msg) ?></div>
    <?php endif; ?>
 