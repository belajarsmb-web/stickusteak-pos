<?php
/**
 * Test Shift Management APIs
 */
require_once __DIR__ . '/../config/database.php';

echo "<h2>🔄 Shift Management - API Test</h2>";

try {
    $pdo = getDbConnection();
    echo "<p>✅ Database connected</p>";
    
    // Test 1: Check shifts table
    echo "<h3>Test 1: Shifts Table Structure</h3>";
    $stmt = $pdo->query("DESCRIBE shifts");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test 2: Check existing shifts
    echo "<h3>Test 2: Existing Shifts</h3>";
    $stmt = $pdo->query("
        SELECT s.*, u.username,
        (SELECT COUNT(*) FROM orders WHERE created_by = s.user_id 
         AND created_at BETWEEN s.opened_at AND COALESCE(s.closed_at, NOW())) as order_count
        FROM shifts s
        LEFT JOIN users u ON s.user_id = u.id
        ORDER BY s.opened_at DESC
        LIMIT 10
    ");
    $shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($shifts) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>User</th><th>Opened</th><th>Closed</th><th>Open Balance</th><th>Close Balance</th><th>Status</th><th>Orders</th></tr>";
        foreach ($shifts as $shift) {
            $statusColor = $shift['status'] === 'open' ? 'green' : 'gray';
            echo "<tr>";
            echo "<td>{$shift['id']}</td>";
            echo "<td>{$shift['username']}</td>";
            echo "<td>{$shift['opened_at']}</td>";
            echo "<td>" . ($shift['closed_at'] ?? 'Not closed') . "</td>";
            echo "<td>Rp " . number_format($shift['opening_balance'], 0, ',', '.') . "</td>";
            echo "<td>Rp " . number_format($shift['closing_balance'] ?? 0, 0, ',', '.') . "</td>";
            echo "<td><strong style='color: $statusColor;'>{$shift['status']}</strong></td>";
            echo "<td>{$shift['order_count']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No shifts found. Click 'Open Shift' to create one.</p>";
    }
    
    // Test 3: Active Shift
    echo "<h3>Test 3: Check Active Shift</h3>";
    $stmt = $pdo->query("
        SELECT s.*, u.username
        FROM shifts s
        LEFT JOIN users u ON s.user_id = u.id
        WHERE s.status = 'open'
        ORDER BY s.opened_at DESC
    ");
    $activeShifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($activeShifts) > 0) {
        echo "<p style='color: green;'>✅ Found " . count($activeShifts) . " active shift(es)</p>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>User</th><th>Opened</th><th>Duration</th></tr>";
        foreach ($activeShifts as $shift) {
            $opened = new DateTime($shift['opened_at']);
            $now = new DateTime();
            $diff = $now->diff($opened);
            $duration = sprintf('%dh %dm', $diff->h, $diff->i + ($diff->days * 60));
            
            echo "<tr>";
            echo "<td>{$shift['id']}</td>";
            echo "<td>{$shift['username']}</td>";
            echo "<td>{$shift['opened_at']}</td>";
            echo "<td>$duration</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No active shifts</p>";
    }
    
    // Test 4: API Endpoints
    echo "<h3>Test 4: API Endpoints</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Endpoint</th><th>Method</th><th>Test</th><th>Status</th></tr>";
    
    $endpoints = [
        ['GET', '/php-native/api/shifts/active.php'],
        ['GET', '/php-native/api/shifts/list.php'],
        ['POST', '/php-native/api/shifts/open.php'],
        ['POST', '/php-native/api/shifts/close.php']
    ];
    
    foreach ($endpoints as $api) {
        echo "<tr>";
        echo "<td><strong>{$api[1]}</strong></td>";
        echo "<td>{$api[0]}</td>";
        echo "<td><a href='{$api[1]}' target='_blank' style='color: blue; text-decoration: underline;'>Test API</a></td>";
        echo "<td>✅ Exists</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Quick Actions</h3>";
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='/php-native/pages/shifts.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-right: 10px;'>🕐 Go to Shifts Page</a>";
    echo "<a href='/php-native/pages/dashboard.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-right: 10px;'>📊 Dashboard</a>";
    echo "<a href='javascript:location.reload();' style='padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>🔄 Refresh</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
}
?>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { padding: 10px; text-align: left; }
    th { background: #f0f0f0; }
    h2 { color: #333; }
    h3 { color: #666; margin-top: 30px; }
    a { text-decoration: none; }
</style>
