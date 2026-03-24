<?php
/**
 * Mobile Order API - Order Status
 * GET /api/mobile/order-status.php?token=TOKEN
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    $token = $_GET['token'] ?? '';

    if (empty($token)) {
        jsonResponse(['success' => false, 'message' => 'Order token required'], 400);
    }

    // Get order details - check both mobile_token and regular token
    $stmt = $pdo->prepare("
        SELECT o.*, t.name as table_name
        FROM orders o
        LEFT JOIN tables t ON o.table_id = t.id
        WHERE o.mobile_token = :token OR o.id = :token
        ORDER BY o.created_at DESC
        LIMIT 1
    ");
    $stmt->execute(['token' => $token]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        // Try to find by table_id if token is actually a table ID
        $tableId = (int)$token;
        if ($tableId > 0) {
            $stmt = $pdo->prepare("
                SELECT o.*, t.name as table_name
                FROM orders o
                LEFT JOIN tables t ON o.table_id = t.id
                WHERE o.table_id = :table_id AND o.status NOT IN ('completed', 'cancelled', 'refunded')
                ORDER BY o.created_at DESC
                LIMIT 1
            ");
            $stmt->execute(['table_id' => $tableId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    if (!$order) {
        jsonResponse(['success' => false, 'message' => 'Order not found'], 404);
    }

    // Get order items
    $stmt = $pdo->prepare("
        SELECT oi.*, m.name as item_name
        FROM order_items oi
        LEFT JOIN menu_items m ON oi.menu_item_id = m.id
        WHERE oi.order_id = :order_id
        ORDER BY oi.id
    ");
    $stmt->execute(['order_id' => $order['id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format items with parsed notes/modifiers
    foreach ($items as &$item) {
        $item['notes_array'] = !empty($item['notes']) ? @json_decode($item['notes'], true) : [];
        $item['modifiers_array'] = !empty($item['modifiers']) ? @json_decode($item['modifiers'], true) : [];
    }

    // Get status timeline
    $statusLabels = [
        'pending' => 'Order Received',
        'preparing' => 'Preparing',
        'ready' => 'Ready to Serve',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ];

    $statusProgress = [
        ['status' => 'pending', 'label' => 'Order Received', 'completed' => true],
        ['status' => 'preparing', 'label' => 'Preparing', 'completed' => in_array($order['status'], ['preparing', 'ready', 'completed'])],
        ['status' => 'ready', 'label' => 'Ready to Serve', 'completed' => in_array($order['status'], ['ready', 'completed'])],
        ['status' => 'completed', 'label' => 'Completed', 'completed' => $order['status'] === 'completed']
    ];

    // Calculate estimated time
    $createdTime = strtotime($order['created_at']);
    $currentTime = time();
    $elapsedMinutes = floor(($currentTime - $createdTime) / 60);
    $estimatedMinutes = 15; // Default estimate
    $remainingMinutes = max(0, $estimatedMinutes - $elapsedMinutes);

    jsonResponse([
        'success' => true,
        'order' => [
            'id' => $order['id'],
            'order_number' => $order['id'],
            'table_name' => $order['table_name'] ?? 'Takeaway',
            'customer_name' => $order['customer_name'],
            'status' => $order['status'],
            'status_label' => $statusLabels[$order['status']] ?? $order['status'],
            'created_at' => date('d/m/Y H:i', strtotime($order['created_at'])),
            'items' => $items,
            'total_amount' => floatval($order['total_amount']),
            'elapsed_minutes' => $elapsedMinutes,
            'estimated_remaining_minutes' => $remainingMinutes
        ],
        'status_progress' => $statusProgress
    ]);

} catch (PDOException $e) {
    error_log("Order status error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to get order status'], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
