<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../routes/web.php';

$url = $_GET['url'] ?? '/';
$route = $routes[$url] ?? null;

if (!$route) {
    http_response_code(404);
    $title = '404 Not Found';
    require BASE_PATH . '/views/errors/404.php';
    exit;
}

if (($route['action'] ?? '') === 'logout') {
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . '/login');
    exit;
}

if (($route['action'] ?? '') === 'order_confirm') {
    $controller = new \App\Controllers\OrderController($pdo);
    $controller->confirm();
    exit;
}

$view = BASE_PATH . '/views/' . $route['view'] . '.php';
if (!file_exists($view)) {
    http_response_code(500);
    echo 'View tidak ditemukan.';
    exit;
}

require $view;