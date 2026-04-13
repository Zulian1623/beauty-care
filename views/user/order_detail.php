<?php
$title = 'Detail Pesanan';
require_login();

use App\Services\UploadService;

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$id, current_user_id()]);
$order = $stmt->fetch();

if (!$order) {
    die('Order tidak ditemukan.');
}

$itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

$method = $order['payment_method'] ?? '';

$methodText = match ($method) {
    'bank_transfer' => 'Transfer Bank',
    'ewallet' => 'E-Wallet',
    'qris' => 'QRIS',
    'cod' => 'COD',
    default => 'Metode Pembayaran'
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $senderName = trim($_POST['sender_name'] ?? '');
    $bankName = trim($_POST['bank_name'] ?? '');
    $amount = (float) ($_POST['transfer_amount'] ?? 0);

    try {
        $proof = UploadService::uploadImage($_FILES['proof_image'], UPLOAD_PAYMENT_DIR);

        $ins = $pdo->prepare('
            INSERT INTO payment_confirmations(
                order_id,
                sender_name,
                bank_name,
                transfer_amount,
                proof_image,
                verification_status
            ) VALUES(?,?,?,?,?,?)
        ');
        $ins->execute([$id, $senderName, $bankName, $amount, $proof, 'pending']);

        $pdo->prepare("UPDATE orders SET payment_status = 'waiting_verification' WHERE id = ?")
            ->execute([$id]);

        flash('success', 'Bukti pembayaran berhasil diupload.');
        redirect('/user/order-detail?id=' . $id);
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
        redirect('/user/order-detail?id=' . $id);
    }
}

$bankSettings = $pdo->query("
    SELECT setting_key, setting_value
    FROM settings
    WHERE setting_key IN ('bank_name','bank_account_name','bank_account_number')
")->fetchAll();

$bank = [];
foreach ($bankSettings as $row) {
    $bank[$row['setting_key']] = $row['setting_value'];
}

require BASE_PATH . '/views/layouts/header.php';
?>

<h1>Detail Pesanan</h1>

<div class="card">
    <p>Invoice: <strong><?= e($order['order_code']) ?></strong></p>
    <p>Total: <strong><?= rupiah($order['total']) ?></strong></p>
    <p>Status Order: <?= e($order['order_status']) ?></p>
    <p>Status Pembayaran: <?= e($order['payment_status']) ?></p>
    <p>Metode Pembayaran: <strong><?= e($methodText) ?></strong></p>

    <h3><?= e($methodText) ?></h3>

    <?php if ($method === 'bank_transfer'): ?>
        <p><?= e($bank['bank_name'] ?? '') ?> - <?= e($bank['bank_account_number'] ?? '') ?> a.n <?= e($bank['bank_account_name'] ?? '') ?></p>
    <?php elseif ($method === 'ewallet'): ?>
        <p>DANA / OVO / GoPay / ShopeePay: 081234567890 a.n Glowé Skincare</p>
    <?php elseif ($method === 'qris'): ?>
        <p>Scan QRIS untuk melakukan pembayaran.</p>
        <p><img src="<?= BASE_URL ?>/assets/img/qr_link_gambar.png" alt="QRIS" style="max-width:220px;border-radius:12px;border:1px solid #eee;padding:10px;background:#fff;"></p>
    <?php elseif ($method === 'cod'): ?>
        <p>Pembayaran dilakukan saat pesanan diterima.</p>
    <?php endif; ?>

    <p>
        <a class="btn btn-outline" target="_blank" href="<?= whatsapp_order_url($order['order_code'], (float) $order['total']) ?>">
            Hubungi Admin via WhatsApp
        </a>
    </p>

    <hr>

    <h3>Item</h3>
    <ul>
        <?php foreach ($items as $item): ?>
            <li><?= e($item['product_name']) ?> x <?= $item['qty'] ?> = <?= rupiah($item['line_total']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<?php if ($method !== 'cod'): ?>
    <div class="card">
        <h3>
            <?php
            echo match ($method) {
                'bank_transfer' => 'Upload Bukti Transfer',
                'ewallet' => 'Upload Bukti Pembayaran E-Wallet',
                'qris' => 'Upload Bukti Pembayaran QRIS',
                default => 'Upload Bukti Pembayaran'
            };
            ?>
        </h3>

        <form method="post" enctype="multipart/form-data" class="form-grid">
            <?= csrf_input() ?>

            <label>Nama Pengirim
                <input type="text" name="sender_name" required>
            </label>

            <label>
                <?php if ($method === 'ewallet'): ?>
                    E-Wallet
                    <input type="text" name="bank_name" value="E-Wallet" required>
                <?php elseif ($method === 'qris'): ?>
                    Metode
                    <input type="text" name="bank_name" value="QRIS" required>
                <?php else: ?>
                    Bank Pengirim
                    <input type="text" name="bank_name" required>
                <?php endif; ?>
            </label>

            <label>Jumlah Transfer
                <input type="number" name="transfer_amount" required>
            </label>

            <label>Bukti Pembayaran
                <input type="file" name="proof_image" accept=".jpg,.jpeg,.png,.webp" required>
            </label>

            <button class="btn">Kirim Verifikasi</button>
        </form>
    </div>
<?php endif; ?>

<?php require BASE_PATH . '/views/layouts/footer.php'; ?>