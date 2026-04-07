<?php
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/security.php';
require_once BASE_PATH . '/app/helpers/formatter.php';
require_once BASE_PATH . '/app/helpers/middleware.php';

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function is_admin(): bool
{
    return is_logged_in() && (($_SESSION['user']['role'] ?? '') === 'admin');
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('/login');
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('/login');
    }
}

function current_user_id(): ?int
{
    return $_SESSION['user']['id'] ?? null;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function flash(string $key, ?string $value = null)
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }

    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $converted = @iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    if ($converted !== false) {
        $text = $converted;
    }
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text ?: 'item');
}

function cart_count(): int
{
    global $pdo;
    return \App\Services\CartService::count($pdo ?? null, current_user_id());
}

function cart_subtotal(): float
{
    global $pdo;
    return \App\Services\CartService::subtotal($pdo ?? null, current_user_id());
}

function whatsapp_order_url(string $orderCode, float $total): string
{
    $message = rawurlencode("Halo admin Glowé, saya ingin konfirmasi order $orderCode dengan total " . rupiah($total));
    return 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . $message;
}

function delete_file_if_exists(string $path): void
{
    if ($path && file_exists($path) && is_file($path)) {
        @unlink($path);
    }
}

function order_status_badge(string $status): string
{
    $map = [
        'new' => 'badge badge-gray',
        'processed' => 'badge badge-blue',
        'shipped' => 'badge badge-purple',
        'completed' => 'badge badge-green',
        'cancelled' => 'badge badge-red',
        'pending' => 'badge badge-yellow',
        'waiting_verification' => 'badge badge-yellow',
        'paid' => 'badge badge-green',
        'rejected' => 'badge badge-red',
        'verified' => 'badge badge-green',
        'inactive' => 'badge badge-red',
        'active' => 'badge badge-green',
    ];

    $class = $map[$status] ?? 'badge badge-gray';
    return '<span class="' . $class . '">' . e(ucwords(str_replace('_', ' ', $status))) . '</span>';
}

function admin_log(string $activity, array $context = []): void
{
    global $pdo;

    if (!is_admin() || !current_user_id()) {
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO admin_logs(admin_id, activity, context) VALUES(?,?,?)');
    $stmt->execute([ 
        current_user_id(),
        $activity,
        !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE) : null
    ]);
}