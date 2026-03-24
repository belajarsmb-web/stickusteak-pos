<?php
/**
 * Stickusteak POS - Tickets API
 * GET /api/tickets/index.php - Get all tickets with order details
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    $status = $_GET['status'] ?? '';
    $date = $_GET['date'] ?? date('Y-m-d');
    
    // Build query - get all tickets for current period (today)
    $sql = "SELECT t.*, 
            (SELECT COUNT(DISTINCT o.id) FROM orders o WHERE o.ticket_id = t.id) as order_count,
            (SELECT COALESCE(SUM(oi.quantity), 0) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.ticket_id = t.id AND oi.is_voided = 0) as items_count,
            (SELECT COALESCE(SUM(o.total_amount), 0) FROM orders o WHERE o.ticket_id = t.id) as total_amount,
            (SELECT GROUP_CONCAT(DISTINCT o.status SEPARATOR ',') FROM orders o WHERE o.ticket_id = t.id) as order_statuses
            FROM tickets t
            WHERE DATE(t.opened_at) = :date";
    
    $params = ['date' => $date];
    
    if ($status && $status !== 'all') {
        $sql .= " AND t.status = :status";
        $params['status'] = $status;
    }
    
    $sql .= " ORDER BY t.opened_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get orders and items for each ticket
    foreach ($tickets as &$ticket) {
        $orderStmt = $pdo->prepare("
            SELECT o.*, 
                   u.username as cashier_name,
                   pm.name as payment_method,
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id AND oi.is_voided = 0) as items_count
            FROM orders o
            LEFT JOIN users u ON o.created_by = u.id
            LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
            WHERE o.ticket_id = :ticket_id
            ORDER BY o.created_at ASC
        ");
        $orderStmt->execute(['ticket_id' => $ticket['id']]);
        $orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get items for each order
        foreach ($orders as &$order) {
            $itemStmt = $pdo->prepare("
                SELECT oi.*, m.name as item_name, m.code as item_code
                FROM order_items oi
                LEFT JOIN menu_items m ON oi.menu_item_id = m.id
                WHERE oi.order_id = :order_id
                ORDER BY oi.id ASC
            ");
            $itemStmt->execute(['order_id' => $order['id']]);
            $order['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $ticket['orders'] = $orders;
    }
    
    // Calculate stats
    $stats = [
        'total_tickets' => count($tickets),
        'open_tickets' => count(array_filter($tickets, fn($t) => $t['status'] === 'open')),
        'paid_tickets' => count(array_filter($tickets, fn($t) => $t['status'] === 'paid')),
        'total_revenue' => array_sum(array_column($tickets, 'total_amount'))
    ];
    
    jsonResponse([
        'success' => true,
        'tickets' => $tickets,
        'stats' => $stats,
        'count' => count($tickets)
    ]);
    
} catch (PDOException $e) {
    error_log("Tickets fetch error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch tickets: ' . $e->getMessage()
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
