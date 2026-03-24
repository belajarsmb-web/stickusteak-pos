<?php
/**
 * RestoQwen POS - KDS Diagnostic Tool
 * Check if KDS is working correctly
 */

require_once __DIR__ . '/php-native/config/database.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>KDS Diagnostic - RestoQwen POS</title>
    <style>
        body { font-family: monospace; background: #1a1a1a; color: #0f0; padding: 20px; }
        h1 { color: #D4AF37; }
        .success { color: #0f0; }
        .error { color: #f00; }
        .warning { color: #ff0; }
        .info { color: #0ff; }
        pre { background: #000; padding: 10px; border-radius: 5px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #333; }
    </style>
</head>
<body>
    <h1>🔍 KDS Diagnostic Tool</h1>
    
    <?php
    try {
        $pdo = getDbConnection();
        echo "<p class='success'>✅ Database connection successful</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
        exit;
    }
    ?>
    
    <h2>1. Recent Orders</h2>
    <?php
    $stmt = $pdo->query("SELECT id, table_id, status, total_amount, created_at FROM orders ORDER BY created_at DESC LIMIT 10");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <table>
        <tr><th>ID</th><th>Table</th><th>Status</th><th>Total</th><th>Created</th></tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td><?php echo $order['table_id']; ?></td>
                <td>
                    <?php 
                    $statusClass = in_array($order['status'], ['pending', 'preparing', 'ready', 'in_progress']) ? 'success' : 'warning';
                    echo "<span class='$statusClass'>" . $order['status'] . "</span>";
                    ?>
                </td>
                <td>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                <td><?php echo $order['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>2. KDS-Visible Orders (pending/preparing/ready/in_progress)</h2>
    <?php
    $stmt = $pdo->query("
        SELECT id, table_id, status, total_amount, created_at 
        FROM orders 
        WHERE status IN ('pending', 'preparing', 'ready', 'in_progress')
        ORDER BY created_at DESC
    ");
    $kdsOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <p>Found <strong><?php echo count($kdsOrders); ?></strong> orders</p>
    <?php if (count($kdsOrders) > 0): ?>
        <table>
            <tr><th>ID</th><th>Table</th><th>Status</th><th>Total</th><th>Created</th></tr>
            <?php foreach ($kdsOrders as $order): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo $order['table_id']; ?></td>
                    <td><span class='success'><?php echo $order['status']; ?></span></td>
                    <td>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                    <td><?php echo $order['created_at']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class='warning'>⚠️ No orders visible in KDS! All orders are either paid or cancelled.</p>
        <p>Create a new order WITHOUT paying to test KDS.</p>
    <?php endif; ?>
    
    <h2>3. Order Items for KDS-Visible Orders</h2>
    <?php if (count($kdsOrders) > 0): ?>
        <?php
        $orderIds = implode(',', array_column($kdsOrders, 'id'));
        $stmt = $pdo->query("
            SELECT oi.id, oi.order_id, oi.quantity, oi.price, m.name as item_name, 
                   c.name as category, oi.is_printed, oi.print_count
            FROM order_items oi
            JOIN menu_items m ON oi.menu_item_id = m.id
            LEFT JOIN categories c ON m.category_id = c.id
            WHERE oi.order_id IN ($orderIds)
            ORDER BY oi.order_id, oi.id
        ");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <p>Found <strong><?php echo count($items); ?></strong> items</p>
        <table>
            <tr><th>Item ID</th><th>Order ID</th><th>Item Name</th><th>Category</th><th>Qty</th><th>Printed</th></tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>#<?php echo $item['id']; ?></td>
                    <td>#<?php echo $item['order_id']; ?></td>
                    <td><?php echo $item['item_name']; ?></td>
                    <td><?php echo $item['category'] ?? 'N/A'; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>
                        <?php if ($item['is_printed']): ?>
                            <span class='success'>✅ (<?php echo $item['print_count']; ?>x)</span>
                        <?php else: ?>
                            <span class='warning'>❌ Not printed</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    
    <h2>4. KDS API Test</h2>
    <p>Testing Kitchen Orders API...</p>
    <?php
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/php-native/api/kds/kitchen-orders.php';
    $apiResult = @file_get_contents($apiUrl);
    if ($apiResult) {
        $apiData = json_decode($apiResult, true);
        if ($apiData && $apiData['success']) {
            echo "<p class='success'>✅ KDS API working! Found " . count($apiData['orders']) . " orders</p>";
            echo "<pre>" . json_encode($apiData, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p class='error'>❌ KDS API returned error</p>";
            echo "<pre>" . htmlspecialchars($apiResult) . "</pre>";
        }
    } else {
        echo "<p class='error'>❌ Cannot access KDS API</p>";
        echo "<p>URL: <a href='<?php echo $apiUrl; ?>' target='_blank'><?php echo $apiUrl; ?></a></p>";
    }
    ?>
    
    <h2>5. Bar Orders API Test</h2>
    <p>Testing Bar Orders API...</p>
    <?php
    $barApiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/php-native/api/kds/bar-orders.php';
    $barApiResult = @file_get_contents($barApiUrl);
    if ($barApiResult) {
        $barApiData = json_decode($barApiResult, true);
        if ($barApiData && $barApiData['success']) {
            echo "<p class='success'>✅ Bar API working! Found " . count($barApiData['orders']) . " orders</p>";
            echo "<pre>" . json_encode($barApiData, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p class='error'>❌ Bar API returned error</p>";
            echo "<pre>" . htmlspecialchars($barApiResult) . "</pre>";
        }
    } else {
        echo "<p class='error'>❌ Cannot access Bar API</p>";
        echo "<p>URL: <a href='<?php echo $barApiUrl; ?>' target='_blank'><?php echo $barApiUrl; ?></a></p>";
    }
    ?>
    
    <h2>6. Recommendations</h2>
    <?php
    $issues = [];
    
    if (count($kdsOrders) === 0) {
        $issues[] = "No orders with status pending/preparing/ready/in_progress. Create a new order and DON'T pay yet!";
    }
    
    if (count($items) === 0 && count($kdsOrders) > 0) {
        $issues[] = "Orders exist but no items found. Check order_items table.";
    }
    
    if (!$apiResult || !$barApiResult) {
        $issues[] = "APIs not accessible. Check web server configuration.";
    }
    
    if (count($issues) > 0):
        ?>
        <div class='warning'>
            <p><strong>Issues Found:</strong></p>
            <ul>
                <?php foreach ($issues as $issue): ?>
                    <li><?php echo $issue; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <p class='success'>✅ No issues found! KDS should be working correctly.</p>
    <?php endif; ?>
    
    <hr>
    <p><a href='/php-native/pages/kds-kitchen.php' target='_blank'>Open KDS Kitchen</a> | 
       <a href='/php-native/pages/kds-bar.php' target='_blank'>Open KDS Bar</a> | 
       <a href='/php-native/pages/pos-tables.php'>Go to POS Tables</a></p>
</body>
</html>
