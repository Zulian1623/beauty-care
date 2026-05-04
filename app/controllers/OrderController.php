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

    public function confirm()
    {
        require_login();
        verify_csrf();

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $userId = current_user_id();

        if ($orderId <= 0) {
            flash('error', 'Pesanan tidak valid.');
            redirect('/user/orders');
        }

        $order = $this->orders->find($orderId);

        if (!$order || (int) $order['user_id'] !== (int) $userId) {
            flash('error', 'Pesanan tidak ditemukan atau bukan milik kamu.');
            redirect('/user/orders');
        }

        if ($order['order_status'] !== 'shipped') {
            flash('error', 'Pesanan belum bisa dikonfirmasi karena statusnya belum dikirim.');
            redirect('/user/orders');
        }

        $this->orders->updateStatus($orderId, 'completed');

        flash('success', 'Pesanan berhasil dikonfirmasi. Status pesanan sudah berubah menjadi selesai.');
        redirect('/user/orders');
    }
}