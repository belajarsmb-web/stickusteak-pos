<?php
/**
 * Test Shift Statistics
 */
require_once __DIR__ . '/../config/database.php';

echo "<h2>📊 Shift Statistics Test</h2>";

try {
    $pdo = getDbConnection();
    
    // Get active shift
    $stmt = $pdo->query("SELECT * FROM shifts WHERE status = 'open' ORDER BY id DESC LIMIT 1");
    $shift = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($shift) {
        echo "<h3>Active Shift #{$shift['id']}</h3>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        echo "<tr><td>Shift ID</td><td>{$shift['id']}</td></tr>";
        echo "<tr><td>Status</td><td>{$shift['status']}</td></tr>";
        echo "<tr><td>Opening Balance</td><td>Rp " . number_format($shift['opening_balance'] ?? 0, 0, ',', '.') . "</td></tr>";
        echo "<tr><td>Created At</td><td>{$shift['created_at']}</td></tr>";
        echo "</table>";
        
        // Calculate stats
        $shiftStart = $shift['created_at'] ?? $shift['opened_at'] ?? date('Y-m-d H:i:s');
        
        echo "<h3>Shift Statistics (from $shiftStart to now)</h3>";
        
        $statsStmt = $pdo->prepare("
            SELECT 
                COUNT(DISTINCT o.id) as total_orders,
                COALESCE(SUM(o.total_amount), 0) as total_sales,
                COALESCE(SUM(CASE WHEN o.status IN ('paid', 'completed') THEN o.total_amount ELSE 0 END), 0) as total_paid
            FROM orders o
            WHERE o.created_at >= :shift_start
            AND o.status NOT IN ('cancelled', 'voided')
        ");
        $statsStmt->execute(['shift_start' => $shiftStart]);
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Metric</th><th>Value</th></tr>";
        echo "<tr><td>Total Orders</td><td><strong>{$stats['total_orders']}</strong></td></tr>";
        echo "<tr><td>Total Sales</td><td><strong>Rp " . number_format($stats['total_sales'], 0, ',', '.') . "</strong></td></tr>";
        echo "<tr><td>Total Paid</td><td><strong>Rp " . number_format($stats['total_paid'], 0, ',', '.') . "</strong></td></tr>";
        echo "</table>";
        
        // Show orders in this shift
        echo "<h3>Orders in This Shift</h3>";
        $ordersStmt = $pdo->prepare("
            SELECT o.id, o.total_amount, o.status, o.created_at
            FROM orders o
            WHERE o.created_at >= :shift_start
            AND o.status NOT IN ('cancelled', 'voided')
            ORDER BY o.created_at DESC
        ");
        $ordersStmt->execute(['shift_start' => $shiftStart]);
        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($orders) > 0) {
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>Order ID</th><th>Total</th><th>Status</th><th>Time</th></tr>";
            foreach ($orders as $order) {
                echo "<tr>";
                echo "<td>#{$order['id']}</td>";
                echo "<td>Rp " . number_format($order['total_amount'], 0, ',', '.') . "</td>";
                echo "<td>{$order['status']}</td>";
                echo "<td>{$order['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No orders found in this shift period.</p>";
        }
        
    } else {
        echo "<p style='color: orange;'>⚠️ No active shift found. Please open a shift first.</p>";
        echo "<a href='/php-native/pages/shifts.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Open Shift</a>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { padding: 10px; text-align: left; }
    th { background: #f0f0f0; }
    h2 { color: #333; }
    h3 { color: #666; margin-top: 30px; }
</style>
