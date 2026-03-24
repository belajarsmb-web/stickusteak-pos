<?php
/**
 * Debug Orders Status
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

echo "<h2>Debug Orders Status</h2>";

// Get all orders for table 14
echo "<h3>All Orders for Table 14</h3>";
$stmt = $pdo->query("SELECT id, table_id, status, total_amount, created_at FROM orders WHERE table_id = 14 ORDER BY id DESC LIMIT 10");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Order ID</th><th>Table ID</th><th>Status</th><th>Total</th><th>Created</th></tr>";
foreach ($orders as $order) {
    $statusClass = $order['status'] === 'paid' ? 'background:#90EE90' : '';
    echo "<tr style='$statusClass'>";
    echo "<td>#{$order['id']}</td>";
    echo "<td>{$order['table_id']}</td>";
    echo "<td><strong>{$order['status']}</strong></td>";
    echo "<td>Rp " . number_format($order['total_amount'], 0, ',', '.') . "</td>";
    echo "<td>{$order['created_at']}</td>";
    echo "</tr>";
}
echo "</table>";

// Check distinct status values
echo "<h3>Distinct Status Values in Database</h3>";
$stmt = $pdo->query("SELECT DISTINCT status FROM orders ORDER BY status");
$statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "<pre>";
print_r($statuses);
echo "</pre>";

// Test API response
echo "<h3>Test API Response for Table 14</h3>";
echo "<a href='/php-native/api/pos/table-orders.php?table_id=14' target='_blank'>View API Response</a>";

?>
<style>
    body { font-family: monospace; }
    td, th { padding: 8px; }
</style>
