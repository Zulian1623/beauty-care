    <?php
    use App\Models\Cart;
    use App\Models\CartItem;

    $title = 'Checkout';
    require_login();

    $cartModel = new Cart($pdo);
    $cartItemModel = new CartItem($pdo);
    $cart = $cartModel->findByUser(current_user_id());

    if (!$cart) {
        flash('error', 'Keranjang kamu masih kosong.');
        redirect('/cart');
    }

    $items = $cartItemModel->byCart((int) $cart['id']);

    if (!$items) {
        flash('error', 'Keranjang kamu masih kosong.');
        redirect('/cart');
    }

    $addressStmt = $pdo->prepare('SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC');
    $addressStmt->execute([current_user_id()]);
    $addresses = $addressStmt->fetchAll();

    if (!$addresses) {
        flash('error', 'Isi alamat terlebih dahulu sebelum checkout.');
        redirect('/user/addresses');
    }

    $shippingRates = $pdo->query('SELECT * FROM shipping_rates WHERE is_active = 1 ORDER BY city ASC')->fetchAll();
    $subtotal = cart_subtotal();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        $addressId = (int) ($_POST['address_id'] ?? 0);
        $shippingId = (int) ($_POST['shipping_id'] ?? 0);
        $paymentMethod = trim($_POST['payment_method'] ?? 'bank_transfer');
        $notes = trim($_POST['notes'] ?? '');

        if (!in_array($paymentMethod, ['bank_transfer', 'ewallet', 'qris', 'cod'], true)) {
            flash('error', 'Metode pembayaran tidak valid.');
            redirect('/checkout');
        }

        $addressStmt = $pdo->prepare('SELECT * FROM addresses WHERE id = ? AND user_id = ? LIMIT 1');
        $addressStmt->execute([$addressId, current_user_id()]);
        $address = $addressStmt->fetch();

        if (!$address) {
            die('Alamat tidak valid.');
        }

        $stmt = $pdo->prepare('SELECT * FROM shipping_rates WHERE id = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$shippingId]);
        $shipping = $stmt->fetch();

        if (!$shipping) {
            die('Ongkir tidak valid.');
        }

        foreach ($items as $cartItem) {
            $stockStmt = $pdo->prepare('SELECT stock, is_active FROM products WHERE id = ? LIMIT 1');
            $stockStmt->execute([$cartItem['product_id']]);
            $dbProduct = $stockStmt->fetch();

            if (
                !$dbProduct ||
                (int) $dbProduct['is_active'] !== 1 ||
                (int) $dbProduct['stock'] < (int) $cartItem['qty']
            ) {
                flash('error', 'Checkout dibatalkan karena stok salah satu produk tidak mencukupi.');
                redirect('/cart');
            }
        }

        $orderCode = 'INV-' . date('YmdHis') . '-' . random_int(100, 999);
        $shippingCost = (float) $shipping['cost'];
        $total = $subtotal + $shippingCost;

        $pdo->beginTransaction();

        try {
            $orderStmt = $pdo->prepare('
                INSERT INTO orders(
                    order_code,
                    user_id,
                    address_id,
                    recipient_name_snapshot,
                    phone_snapshot,
                    address_line_snapshot,
                    city_snapshot,
                    province_snapshot,
                    postal_code_snapshot,
                    subtotal,
                    shipping_cost,
                    total,
                    payment_method,
                    payment_status,
                    order_status,
                    notes
                ) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ');

            $orderStmt->execute([
                $orderCode,
                current_user_id(),
                $addressId,
                $address['recipient_name'],
                $address['phone'],
                $address['address_line'],
                $address['city'],
                $address['province'],
                $address['postal_code'],
                $subtotal,
                $shippingCost,
                $total,
                $paymentMethod,
                'pending',
                'new',
                $notes,
            ]);

            $orderId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare('
                INSERT INTO order_items(
                    order_id,
                    product_id,
                    product_name,
                    product_price,
                    qty,
                    line_total
                ) VALUES(?,?,?,?,?,?)
            ');

            $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
            $movementStmt = $pdo->prepare("
                INSERT INTO stock_movements(product_id, movement_type, qty, note, created_by)
                VALUES(?, 'out', ?, ?, ?)
            ");

            foreach ($items as $item) {
                $lineTotal = (float) $item['price_at_added'] * (int) $item['qty'];

                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['name'],
                    $item['price_at_added'],
                    $item['qty'],
                    $lineTotal
                ]);

                $stockStmt->execute([$item['qty'], $item['product_id'], $item['qty']]);

                if ($stockStmt->rowCount() === 0) {
                    throw new RuntimeException('Stok gagal diperbarui.');
                }

                $movementStmt->execute([
                    $item['product_id'],
                    $item['qty'],
                    'Pengurangan stok otomatis untuk order ' . $orderCode,
                    current_user_id(),
                ]);
            }

            $cartModel->clear((int) $cart['id']);
            $pdo->commit();

            flash('success', 'Checkout berhasil. Silakan lanjutkan pembayaran sesuai metode yang dipilih.');
            redirect('/user/order-detail?id=' . $orderId);
        } catch (Throwable $e) {
            $pdo->rollBack();
            die('Checkout gagal: ' . $e->getMessage());
        }
    }

    require BASE_PATH . '/views/layouts/header.php';
    ?>

    <h1>Checkout</h1>

    <form method="post" class="card form-grid">
        <?= csrf_input() ?>

        <label>Pilih Alamat
            <select name="address_id" required>
                <?php foreach ($addresses as $address): ?>
                    <option value="<?= $address['id'] ?>">
                        <?= e($address['recipient_name']) ?> - <?= e($address['city']) ?>, <?= e($address['province']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Pilih Ongkir
            <select name="shipping_id" required>
                <?php foreach ($shippingRates as $rate): ?>
                    <option value="<?= $rate['id'] ?>">
                        <?= e($rate['label']) ?> - <?= e($rate['city']) ?> (<?= rupiah($rate['cost']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Metode Pembayaran
            <select name="payment_method" required>
                <option value="bank_transfer">Transfer Bank</option>
                <option value="ewallet">E-Wallet</option>
                <option value="qris">QRIS</option>
                <option value="cod">COD</option>
            </select>
        </label>

        <label>Catatan
            <textarea name="notes"></textarea>
        </label>

        <div class="card" style="background:#fafafa;">
            <h3>Ringkasan Belanja</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= e($item['name']) ?></td>
                            <td><?= (int) $item['qty'] ?></td>
                            <td><?= rupiah($item['price_at_added']) ?></td>
                            <td><?= rupiah($item['line_total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p>Subtotal: <strong><?= rupiah($subtotal) ?></strong></p>
        <button class="btn">Buat Pesanan</button>
    </form> 

    <?php require BASE_PATH . '/views/layouts/footer.php'; ?>