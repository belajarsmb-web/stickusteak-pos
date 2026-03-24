<?php
/**
 * Test Submit Order API
 * Access: http://localhost/php-native/mobile/test-submit.php
 */

echo "<h2>Test Submit Order API</h2>";

require_once __DIR__ . '/../config/database.php';

echo "<h3>1. Database Connection</h3>";
try {
    $pdo = getDbConnection();
    echo "✅ Database connected successfully<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h3>2. Check Tables</h3>";
$tables = ['orders', 'order_items', 'tables'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists<br>";
        } else {
            echo "❌ Table '$table' NOT FOUND<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error checking table '$table': " . $e->getMessage() . "<br>";
    }
}

echo "<h3>3. Check Orders Table Structure</h3>";
try {
    $stmt = $pdo->query("DESCRIBE orders");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Orders columns: " . implode(', ', $columns) . "<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h3>4. Check Order_items Table Structure</h3>";
try {
    $stmt = $pdo->query("DESCRIBE order_items");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Order_items columns: " . implode(', ', $columns) . "<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h3>5. Test Insert Order</h3>";
try {
    $pdo->beginTransaction();
    
    // Test insert order
    $stmt = $pdo->prepare("INSERT INTO orders (table_id, total_amount, status) VALUES (?, ?, 'sent_to_kitchen')");
    $stmt->execute([1, 1000]);
    $orderId = $pdo->lastInsertId();
    echo "✅ Order inserted with ID: $orderId<br>";
    
    // Test insert order item
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$orderId, 1, 1, 1000]);
    echo "✅ Order item inserted<br>";
    
    // Update table
    $stmt = $pdo->prepare("UPDATE tables SET status = 'occupied' WHERE id = ?");
    $stmt->execute([1]);
    echo "✅ Table status updated<br>";
    
    $pdo->commit();
    echo "✅ Transaction committed<br>";
    
    // Cleanup
    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$orderId]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderId]);
    echo "✅ Test data cleaned up<br>";
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "❌ Test failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

echo "<h3>Test Complete</h3>";
