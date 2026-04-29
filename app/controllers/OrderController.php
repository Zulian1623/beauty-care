<?php
namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;

class OrderController
{
    private Order $orders;
    private OrderItem $items;

    public function __construct($pdo)
    {
        $this->orders = new Order($pdo);
        $this->items = new OrderItem($pdo);
    }

    public function detail(int $id): ?array
    {
        return $this->orders->detail($id);
    }

    public function items(int $id): array
    {
        return $this->items->byOrder($id);
    }

    // 🔥 TAMBAHAN: konfirmasi pesanan oleh user
    public function confirm()
    {
        require_login();
        verify_csrf();

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $userId = current_user_id();

        // ambil order
        $order = $this->orders->find($orderId);

        // validasi kepemilikan
        if (!$order || $order['user_id'] != $userId) {
            flash('error', 'Order ga valid.');
            redirect('/orders');
        }

        // validasi status harus shipped
        if ($order['order_status'] !== 'shipped') {
            flash('error', 'Belum bisa dikonfirmasi.');
            redirect('/orders');
        }

        // update ke completed
        $this->orders->updateStatus($orderId, 'completed');

        flash('success', 'Pesanan berhasil dikonfirmasi.');
        redirect('/orders');
    }
}