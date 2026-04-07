<?php
$title = 'Admin Logs';
require_admin();

$logs = $pdo->query("
    SELECT l.*, u.name AS admin_name
    FROM admin_logs l
    JOIN users u ON u.id = l.admin_id
    ORDER BY l.id DESC
    LIMIT 100
")->fetchAll();

require BASE_PATH . '/views/layouts/admin_header.php';
?>
<h1>Admin Logs</h1>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Admin</th>
                <th>Aktivitas</th>
                <th>Context</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($logs): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= e($log['admin_name']) ?></td>
                        <td><?= e($log['activity']) ?></td>
                        <td><small><?= e($log['context']) ?></small></td>
                        <td><?= e($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Belum ada aktivitas admin.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require BASE_PATH . '/views/layouts/admin_footer.php'; ?>