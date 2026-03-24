<?php
// Test Mobile Order Submit
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/database.php';

echo "<h2>Test Mobile Order Submit</h2>";

$pdo = getDbConnection();

// Check if table exists
echo "<h3>1. Check Tables</h3>";
$stmt = $pdo->query("SELECT id, table_number, status FROM tables WHERE is_active = 1 ORDER BY id LIMIT 5");
$tables = $stmt->fetchAll();
foreach ($tables as $table) {
    echo "Table ID: {$table['id']}, Number: {$table['table_number']}, Status: {$table['status']}<br>";
}

// Check recent orders
echo "<h3>2. Recent Orders</h3>";
$stmt = $pdo->query("SELECT id, table_id, total_amount, status, customer_name, customer_phone, created_at FROM orders ORDER BY id DESC LIMIT 5");
$orders = $stmt->fetchAll();
foreach ($orders as $order) {
    echo "Order #{$order['id']}, Table: {$order['table_id']}, Status: {$order['status']}, Customer: {$order['customer_name']}, Total: {$order['total_amount']}<br>";
}

// Check order_items for last order
if (!empty($orders)) {
    $lastOrderId = $orders[0]['id'];
    echo "<h3>3. Order Items for Order #$lastOrderId</h3>";
    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$lastOrderId]);
    $items = $stmt->fetchAll();
    foreach ($items as $item) {
        echo "Item ID: {$item['menu_item_id']}, Qty: {$item['quantity']}, Price: {$item['price']}<br>";
        if ($item['modifiers']) {
            $mods = json_decode($item['modifiers'], true);
            echo "Modifiers: " . implode(', ', $mods) . "<br>";
        }
    }
}

echo "<h3>Test Complete</h3>";
