<?php
namespace App\Models;

class Order extends BaseModel
{
    public function byUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findForUser(int $id, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$id, $userId]);
        return $stmt->fetch() ?: null;
    }

    public function detail(int $id): ?array
    {
        $stmt = $this->db->prepare("\n            SELECT o.*,\n                   u.name AS user_name,\n                   u.email,\n                   COALESCE(o.recipient_name_snapshot, a.recipient_name) AS recipient_name,\n                   COALESCE(o.phone_snapshot, a.phone) AS phone,\n                   COALESCE(o.address_line_snapshot, a.address_line) AS address_line,\n                   COALESCE(o.city_snapshot, a.city) AS city,\n                   COALESCE(o.province_snapshot, a.province) AS province,\n                   COALESCE(o.postal_code_snapshot, a.postal_code) AS postal_code\n            FROM orders o\n            JOIN users u ON u.id = o.user_id\n            LEFT JOIN addresses a ON a.id = o.address_id\n            WHERE o.id = ?\n            LIMIT 1\n        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function paginate(string $status = '', int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        $where = $status ? ' WHERE o.order_status = ? ' : '';
        $params = $status ? [$status] : [];

        $countStmt = $this->db->prepare("SELECT COUNT(*) total FROM orders o $where");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];

        $stmt = $this->db->prepare("\n            SELECT o.*, u.name AS user_name\n            FROM orders o\n            JOIN users u ON u.id = o.user_id\n            $where\n            ORDER BY o.id DESC\n            LIMIT $limit OFFSET $offset\n        ");
        $stmt->execute($params);

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => max(1, (int) ceil($total / $limit)),
        ];
    }
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}
