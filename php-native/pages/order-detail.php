<?php
/**
 * Stickusteak POS - Order Detail Page
 * View detailed order information
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$orderId = $_GET['id'] ?? 0;
$username = $_SESSION['username'] ?? 'User';

try {
    $pdo = getDbConnection();
    
    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*, t.name as table_name, pm.name as payment_method_name,
               u.username as created_by_name
        FROM orders o
        LEFT JOIN tables t ON o.table_id = t.id
        LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
        LEFT JOIN users u ON o.created_by = u.id
        WHERE o.id = :id
    ");
    $stmt->execute(['id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        die('Order not found');
    }
    
    // Get order items
    $stmt = $pdo->prepare("
        SELECT oi.*, m.name as item_name
        FROM order_items oi
        LEFT JOIN menu_items m ON oi.menu_item_id = m.id
        WHERE oi.order_id = :id
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
    <title>Order #<?php echo $orderId; ?> - Stickusteak POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .order-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        .order-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .status-draft { background: #6c757d; color: white; }
        .status-pending { background: #ffc107; color: #000; }
        .status-preparing { background: #17a2b8; color: white; }
        .status-ready { background: #007bff; color: white; }
        .status-paid { background: #28a745; color: white; }
        .status-completed { background: #218838; color: white; }
        .status-cancelled { background: #dc3545; color: white; }
        .item-row {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .item-row:last-child { border-bottom: none; }
        .voided-item {
            opacity: 0.5;
            text-decoration: line-through;
            background: #ffe6e6;
            padding: 10px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="/php-native/pages/orders.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Orders
            </a>
            <button class="btn btn-outline-primary ms-2" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Print
            </button>
        </div>
        
        <!-- Order Header -->
        <div class="order-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Order #<?php echo $orderId; ?></h2>
                    <p class="mb-0 opacity-75"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                </div>
                <div class="text-end">
                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                        <?php echo strtoupper($order['status']); ?>
                    </span>
                    <div class="mt-2"><?php echo htmlspecialchars($order['table_name'] ?? 'Takeaway'); ?></div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Order Items -->
            <div class="col-md-8">
                <div class="order-card">
                    <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i>Order Items</h5>
                    <?php if (empty($items)): ?>
                        <p class="text-muted text-center py-4">No items in this order</p>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <div class="item-row <?php echo $item['is_voided'] ? 'voided-item' : ''; ?>">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong><?php echo $item['quantity']; ?>x</strong>
                                        <?php echo htmlspecialchars($item['item_name']); ?>
                                        <?php if ($item['is_voided']): ?>
                                            <span class="badge bg-danger ms-2">VOID</span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <?php if ($item['is_voided']): ?>
                                            <span class="text-muted">VOID</span>
                                        <?php else: ?>
                                            Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($item['notes']): ?>
                                    <div class="text-muted small mt-1">
                                        <?php 
                                        $notes = @json_decode($item['notes'], true);
                                        if (is_array($notes)):
                                            echo 'Notes: ' . htmlspecialchars(implode(', ', $notes));
                                        endif;
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($item['modifiers']): ?>
                                    <div class="text-muted small">
                                        <?php 
                                        $modifiers = @json_decode($item['modifiers'], true);
                                        if (is_array($modifiers)):
                                            echo 'Modifiers: ' . htmlspecialchars(implode(', ', $modifiers));
                                        endif;
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($item['is_voided'] && !empty($item['void_reason_text'])): ?>
                                    <div class="text-danger small mt-1">
                                        <i class="bi bi-x-circle me-1"></i>Reason: <?php echo htmlspecialchars($item['void_reason_text']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="order-card">
                    <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>Order Summary</h5>
                    
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Subtotal:</span>
                        <span>Rp <?php echo number_format($order['sub_total'] ?: $order['total_amount'], 0, ',', '.'); ?></span>
                    </div>
                    
                    <?php if ($order['service_charge'] > 0): ?>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Service Charge:</span>
                        <span>Rp <?php echo number_format($order['service_charge'], 0, ',', '.'); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($order['tax_amount'] > 0): ?>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Tax:</span>
                        <span>Rp <?php echo number_format($order['tax_amount'], 0, ',', '.'); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($order['discount_amount'] > 0): ?>
                    <div class="d-flex justify-content-between py-2 border-bottom text-success">
                        <span>Discount:</span>
                        <span>-Rp <?php echo number_format($order['discount_amount'], 0, ',', '.'); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between py-3 fw-bold fs-5">
                        <span>TOTAL:</span>
                        <span>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between py-2 border-top">
                        <span>Paid:</span>
                        <span>Rp <?php echo number_format($order['paid_amount'], 0, ',', '.'); ?></span>
                    </div>
                    
                    <?php if ($order['change_amount'] > 0): ?>
                    <div class="d-flex justify-content-between py-2 text-success">
                        <span>Change:</span>
                        <span>Rp <?php echo number_format($order['change_amount'], 0, ',', '.'); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <hr class="my-3">
                    
                    <div class="small">
                        <div class="mb-2">
                            <strong>Payment Method:</strong><br>
                            <?php echo htmlspecialchars($order['payment_method_name'] ?? '-'); ?>
                        </div>
                        <div class="mb-2">
                            <strong>Service Type:</strong><br>
                            <?php echo ucfirst(str_replace('_', ' ', $order['service_type'])); ?>
                        </div>
                        <?php if ($order['created_by_name']): ?>
                        <div class="mb-2">
                            <strong>Created By:</strong><br>
                            <?php echo htmlspecialchars($order['created_by_name']); ?>
                        </div>
                        <?php endif; ?>
                        <div class="mb-2">
                            <strong>Created:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                        </div>
                        <div>
                            <strong>Updated:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($order['updated_at'])); ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($order['status'] === 'pending'): ?>
                <div class="order-card">
                    <h6 class="mb-3">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-info" onclick="updateStatus('preparing')">
                            <i class="bi bi-fire me-1"></i>Start Preparing
                        </button>
                        <button class="btn btn-success" onclick="updateStatus('ready')">
                            <i class="bi bi-check-circle me-1"></i>Mark Ready
                        </button>
                        <button class="btn btn-danger" onclick="updateStatus('cancelled')">
                            <i class="bi bi-x-circle me-1"></i>Cancel Order
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateStatus(newStatus) {
            if (!confirm('Are you sure you want to change order status to ' + newStatus + '?')) {
                return;
            }
            
            fetch('/php-native/api/kds/update-order-status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    order_id: <?php echo $orderId; ?>,
                    status: newStatus
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Order status updated!');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }
    </script>
</body>
</html>
