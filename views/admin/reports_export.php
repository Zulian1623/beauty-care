<?php
require_admin();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=monthly-reports.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Bulan', 'Total Order', 'Total Sales']);

$rows = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total_orders, COALESCE(SUM(total),0) AS total_sales
    FROM orders
    WHERE payment_status='paid'
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
")->fetchAll();


// ✅ TAMBAHKAN DI SINI
admin_log('Export laporan bulanan', [
    'total_bulan' => count($rows)
]);

foreach ($rows as $row) {
    fputcsv($output, [$row['month'], $row['total_orders'], $row['total_sales']]);
}

fclose($output);
exit;