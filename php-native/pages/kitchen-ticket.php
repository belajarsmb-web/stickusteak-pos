<?php
/**
 * Stickusteak POS - Kitchen Ticket Print
 * Thermal printer ticket for kitchen
 */

require_once __DIR__ . '/../config/database.php';

$orderId = $_GET['order_id'] ?? 0;

if (!$orderId) {
    die('Order ID required');
}

try {
    $pdo = getDbConnection();
    
    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*, t.name as table_name
        FROM orders o
        LEFT JOIN tables t ON o.table_id = t.id
        WHERE o.id = :id
    ");
    $stmt->execute(['id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        die('Order not found');
    }
    
    // Get kitchen items only (not beverages)
    $stmt = $pdo->prepare("
        SELECT oi.*, m.name as item_name, c.name as category
        FROM order_items oi
        LEFT JOIN menu_items m ON oi.menu_item_id = m.id
        LEFT JOIN categories c ON m.category_id = c.id
        WHERE oi.order_id = :id AND c.name NOT IN ('Beverages')
    ");
    $stmt->execute(['id' => $orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Ticket #<?php echo $orderId; ?></title>
    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            padding: 10px;
        }
        
        .ticket-container {
            max-width: 280px;
            margin: 0 auto;
        }
        
        .ticket-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        
        .ticket-header h3 {
            font-size: 14px;
            margin: 0;
            font-weight: bold;
        }
        
        .ticket-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 10px;
        }
        
        .ticket-table {
            width: 100%;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        
        .ticket-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .ticket-item-qty {
            font-weight: bold;
            margin-right: 8px;
        }
        
        .ticket-notes {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
            padding-left: 20px;
        }
        
        .ticket-footer {
            text-align: center;
            font-size: 10px;
            padding-top: 8px;
        }
        
        .urgent-badge {
            background: #000;
            color: #fff;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            display: inline-block;
            margin-top: 3px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="ticket-container">
        <div class="ticket-header">
            <h3>KITCHEN ORDER</h3>
            <p>Order #<?php echo $orderId; ?></p>
        </div>
        
        <div class="ticket-info">
            <div>
                <strong>Table:</strong> <?php echo htmlspecialchars($order['table_name'] ?? '-'); ?><br>
                <strong>Time:</strong> <?php echo date('H:i', strtotime($order['created_at'])); ?>
            </div>
            <div style="text-align: right;">
                <strong>Status:</strong> <?php echo strtoupper($order['status']); ?><br>
                <strong>Items:</strong> <?php echo count($items); ?>
            </div>
        </div>
        
        <?php
        $elapsed = floor((time() - strtotime($order['created_at'])) / 60);
        if ($elapsed > 15):
        ?>
            <div style="text-align: center; margin-bottom: 8px;">
                <span class="urgent-badge">⏰ <?php echo $elapsed; ?> MIN WAIT</span>
            </div>
        <?php endif; ?>
        
        <div class="ticket-table">
            <?php foreach ($items as $item): ?>
                <div class="ticket-item">
                    <div>
                        <span class="ticket-item-qty"><?php echo $item['quantity']; ?>x</span>
                        <?php echo htmlspecialchars($item['item_name']); ?>
                    </div>
                </div>
                <?php if ($item['notes']): 
                    $notes = json_decode($item['notes'], true);
                    if (is_array($notes) && count($notes) > 0):
                ?>
                    <div class="ticket-notes">
                        ⚠️ <?php echo implode(', ', $notes); ?>
                    </div>
                <?php endif; endif; ?>
            <?php endforeach; ?>
        </div>
        
        <div class="ticket-footer">
            <p>--- KITCHEN COPY ---</p>
            <p><?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>
    
    <script>
        // Auto-close after print
        setTimeout(() => {
            if (window.opener && !window.opener.closed) {
                window.close();
            }
        }, 2000);
    </script>
</body>
</html>
