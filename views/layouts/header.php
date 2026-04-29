<?php require_once BASE_PATH . '/app/helpers/functions.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container topbar clean-navbar">
        <a class="brand" href="<?= BASE_URL ?>/">Beauty Care</a>

        <nav class="nav-center">
            <a href="<?= BASE_URL ?>/">Home</a>
            <a href="<?= BASE_URL ?>/#catalog" class="nav-link">Catalog</a>
            <a href="<?= BASE_URL ?>/cart">Cart (<?= cart_count() ?>)</a>

        <div class="nav-icons">
            <?php if (is_logged_in()): ?>
                <a href="<?= BASE_URL ?>/logout"><i class="fa-solid fa-right-from-bracket"></i></a>
                <a href="<?= BASE_URL ?>/user/dashboard" title="Account"><i class="fa-regular fa-user"></i></a>
                <?php if (is_admin()): ?>
                    <a href="<?= BASE_URL ?>/admin/dashboard">Admin</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/register"><i class="fa-solid fa-user-plus"></i></a>
                <a href="<?= BASE_URL ?>/login" title="Account" ><i class="fa-regular fa-user"></i></a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/cart" title="Keranjang"><i class="fa-solid fa-bag-shopping"></i></a>
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
 