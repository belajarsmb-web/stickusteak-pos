<?php
// Test Receipt Data
require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

echo "<h2>Test Receipt Data</h2>";

// Get latest order
echo "<h3>Latest Order</h3>";
$stmt = $pdo->query("SELECT id, table_id, customer_name, customer_phone, total_amount, status, created_at FROM orders ORDER BY id DESC LIMIT 1");
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if ($order) {
    echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";
    echo "<strong>Order #{$order['id']}</strong><br>";
    echo "Table ID: {$order['table_id']}<br>";
    echo "Customer Name: " . ($order['customer_name'] ?? 'NULL') . "<br>";
    echo "Customer Phone: " . ($order['customer_phone'] ?? 'NULL') . "<br>";
    echo "Total: {$order['total_amount']}<br>";
    echo "Status: {$order['status']}<br>";
    echo "Date: {$order['created_at']}<br>";
    echo "</div>";
    
    // Get table name
    echo "<h3>Table Info</h3>";
    $stmt = $pdo->prepare("SELECT id, table_number FROM tables WHERE id = ?");
    $stmt->execute([$order['table_id']]);
    $table = $stmt->fetch();
    if ($table) {
        echo "Table Number: {$table['table_number']}<br>";
    } else {
        echo "Table not found!<br>";
    }
    
    // Test receipt query
    echo "<h3>Receipt Query Test</h3>";
    $stmt = $pdo->prepare("
        SELECT o.*, t.table_number as table_name, pm.name as payment_method_name
        FROM orders o
        LEFT JOIN tables t ON o.table_id = t.id
        LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order['id']]);
    $receiptOrder = $stmt->fetch();
    
    if ($receiptOrder) {
        echo "<div style='border:1px solid #ccc; padding:10px;'>";
        echo "<strong>Receipt Data:</strong><br>";
        echo "Table Name: " . ($receiptOrder['table_name'] ?? 'NULL') . "<br>";
        echo "Customer Name: " . ($receiptOrder['customer_name'] ?? 'NULL') . "<br>";
        echo "Customer Phone: " . ($receiptOrder['customer_phone'] ?? 'NULL') . "<br>";
        echo "Payment Method: " . ($receiptOrder['payment_method_name'] ?? 'NULL') . "<br>";
        echo "</div>";
    }
    
    echo "<h3>Test Receipt Display</h3>";
    echo "<a href='receipt.php?order_id={$order['id']}' target='_blank' style='display:inline-block;padding:10px 20px;background:#D4AF37;color:#000;text-decoration:none;border-radius:5px;font-weight:bold;'>View Receipt</a>";
    
} else {
    echo "No orders found!<br>";
}

echo "<h3>Test Complete</h3>";
