<?php
/**
 * RestoQwen POS - Get Table Orders API
 * GET /api/pos/table-orders.php?table_id=
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    $table_id = $_GET['table_id'] ?? 0;
    
    if (!$table_id) {
        jsonResponse(['success' => false, 'message' => 'Table ID required'], 400);
    }
    
    // Get ALL ACTIVE orders for this table (exclude paid/completed/cancelled)
    // Include ticket_number by joining with tickets table
    $sql = "SELECT o.*,
            tk.ticket_number,
            (SELECT JSON_ARRAYAGG(JSON_OBJECT(
                'id', oi.id,
                'menu_item_id', oi.menu_item_id,
                'quantity', oi.quantity,
                'price', oi.price,
                'name', m.name,
                'notes', oi.notes,
                'modifiers', oi.modifiers,
                'is_voided', oi.is_voided,
                'status', oi.status
            )) FROM order_items oi
            LEFT JOIN menu_items m ON oi.menu_item_id = m.id
            WHERE oi.order_id = o.id) as items
            FROM orders o
            LEFT JOIN tickets tk ON o.ticket_id = tk.id
            WHERE o.table_id = :table_id
            AND o.status NOT IN ('paid', 'completed', 'cancelled', 'voided')
            ORDER BY o.created_at ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['table_id' => $table_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parse items JSON and add notes_array/modifiers_array
    foreach ($orders as &$order) {
        if ($order['items']) {
            $items = json_decode($order['items'], true);
            // Parse notes and modifiers for each item
            foreach ($items as &$item) {
                $item['notes_array'] = !empty($item['notes']) ? @json_decode($item['notes'], true) : [];
                $item['modifiers_array'] = !empty($item['modifiers']) ? @json_decode($item['modifiers'], true) : [];
            }
            $order['items'] = $items;
        } else {
            $order['items'] = [];
        }
    }
    
    jsonResponse([
        'success' => true,
        'orders' => $orders,
        'count' => count($orders)
    ]);
    
} catch (PDOException $e) {
    error_log("Table orders fetch error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch table orders'
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
