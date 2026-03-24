<?php
// Debug submit-order.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Submit Order</h2>";

require_once __DIR__ . '/../config/database.php';

echo "<h3>1. Database Connection</h3>";
try {
    $pdo = getDbConnection();
    echo "✅ Connected<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h3>2. Check Orders Table Columns</h3>";
$stmt = $pdo->query("DESCRIBE orders");
$cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Columns: " . implode(', ', $cols) . "<br>";

echo "<h3>3. Check if customer_name and customer_phone exist</h3>";
if (in_array('customer_name', $cols) && in_array('customer_phone', $cols)) {
    echo "✅ Customer columns exist<br>";
} else {
    echo "❌ Customer columns NOT found<br>";
    echo "<strong>Run this SQL:</strong><br>";
    echo "<code>ALTER TABLE orders ADD COLUMN customer_name VARCHAR(255), ADD COLUMN customer_phone VARCHAR(50);</code><br>";
}

echo "<h3>4. Test Insert with Customer Info</h3>";
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO orders (table_id, total_amount, status, customer_name, customer_phone) VALUES (?, ?, 'sent_to_kitchen', ?, ?)");
    $stmt->execute([1, 1000, 'Test Customer', '08123456789']);
    $orderId = $pdo->lastInsertId();
    echo "✅ Order inserted with ID: $orderId<br>";
    
    // Cleanup
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderId]);
    echo "✅ Test data cleaned<br>";
    
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h3>Debug Complete</h3>";
