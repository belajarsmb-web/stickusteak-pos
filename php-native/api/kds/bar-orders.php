<?php
/**
 * RestoQwen POS - Bar Orders API
 * GET /api/kds/bar-orders.php - Get beverage orders for bar
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();

    // Get orders with bar/beverage items - exclude served
    $sql = "SELECT DISTINCT o.id, o.table_id, o.status, o.created_at,
            t.table_number as table_name,
            oi.id as item_id, oi.menu_item_id, oi.quantity, oi.price, oi.notes, oi.modifiers,
            oi.is_printed, oi.print_count, oi.is_voided,
            m.name as item_name, m.display_routing
            FROM orders o
            LEFT JOIN tables t ON o.table_id = t.id
            JOIN order_items oi ON oi.order_id = o.id
            JOIN menu_items m ON oi.menu_item_id = m.id
            WHERE o.status IN ('sent_to_kitchen', 'in_progress', 'preparing', 'ready')
            AND (m.display_routing IN ('bar', 'both') OR m.display_routing IS NULL)
            AND oi.is_voided = 0
            ORDER BY o.created_at ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by order
    $orders = [];
    $orderMap = [];

    foreach ($rows as $row) {
        $orderId = $row['id'];

        if (!isset($orderMap[$orderId])) {
            $orderMap[$orderId] = count($orders);
            $orders[] = [
                'id' => $orderId,
                'table_id' => $row['table_id'],
                'table_name' => $row['table_name'] ?? 'Takeaway',
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'items' => []
            ];
        }

        $orders[$orderMap[$orderId]]['items'][] = [
            'id' => $row['item_id'],
            'menu_item_id' => $row['menu_item_id'],
            'quantity' => (int)$row['quantity'],
            'item_name' => $row['item_name'],
            'notes' => $row['notes'] ? json_decode($row['notes'], true) : [],
            'modifiers' => $row['modifiers'] ? json_decode($row['modifiers'], true) : [],
            'is_printed' => (bool)$row['is_printed'],
            'print_count' => (int)$row['print_count']
        ];
    }

    // Calculate preparation time and priority
    foreach ($orders as &$order) {
        $created = new DateTime($order['created_at']);
        $now = new DateTime();
        $interval = $created->diff($now);
        
        $order['prep_time_minutes'] = $interval->h * 60 + $interval->i;
        $order['prep_time_formatted'] = sprintf('%dh %dm', $interval->h, $interval->i);
        
        // Priority based on wait time
        if ($order['prep_time_minutes'] > 30) {
            $order['priority'] = 'high';
        } elseif ($order['prep_time_minutes'] > 15) {
            $order['priority'] = 'normal';
        } else {
            $order['priority'] = 'low';
        }
    }

    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'count' => count($orders),
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
