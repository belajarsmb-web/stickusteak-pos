<?php
// Test Table Orders API
require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();
$table_id = 14;

echo "<h2>Test Table Orders API - Table $table_id</h2>";

// Get active orders
$sql = "SELECT o.*,
        (SELECT JSON_ARRAYAGG(JSON_OBJECT(
            'id', oi.id,
            'menu_item_id', oi.menu_item_id,
            'quantity', oi.quantity,
            'price', oi.price,
            'name', m.name,
            'notes', oi.notes,
            'modifiers', oi.modifiers,
            'is_voided', oi.is_voided
        )) FROM order_items oi
        LEFT JOIN menu_items m ON oi.menu_item_id = m.id
        WHERE oi.order_id = o.id) as items
        FROM orders o
        WHERE o.table_id = ?
        AND o.status IN ('sent_to_kitchen', 'pending', 'preparing', 'ready', 'in_progress', 'served')
        ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$table_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Found " . count($orders) . " orders</h3>";

foreach ($orders as $order) {
    echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";
    echo "<strong>Order #{$order['id']}</strong> - Status: {$order['status']} - Total: {$order['total_amount']}<br>";
    
    if ($order['items']) {
        $items = json_decode($order['items'], true);
        echo "<strong>Items:</strong><br>";
        foreach ($items as $item) {
            echo "• {$item['quantity']}x {$item['name']} (ID: {$item['menu_item_id']})<br>";
            if ($item['modifiers']) {
                $mods = json_decode($item['modifiers'], true);
                if ($mods) {
                    echo "  Modifiers: " . implode(', ', $mods) . "<br>";
                }
            }
        }
    }
    echo "</div>";
}

echo "<h3>Test Complete</h3>";
