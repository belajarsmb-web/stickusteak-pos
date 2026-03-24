<?php
/**
 * Tickets API - List All Active Tickets
 * GET /api/tickets/list-all.php
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    
    // Get filter parameters
    $status = $_GET['status'] ?? 'open';  // default to open tickets
    $ticketId = $_GET['ticket_id'] ?? '';  // Changed from table_id
    $search = $_GET['search'] ?? '';
    
    $params = [];
    $whereConditions = ["1=1"];
    
    // Filter by status
    if ($status && $status !== 'all') {
        $whereConditions[] = "t.status = :status";
        $params['status'] = $status;
    }
    
    // Filter by specific ticket ID (for loading details)
    if ($ticketId) {
        $whereConditions[] = "t.id = :ticket_id";
        $params['ticket_id'] = $ticketId;
    }
    
    // Search
    if ($search) {
        $whereConditions[] = "(t.ticket_number LIKE :search OR t.customer_name LIKE :search OR t.customer_phone LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    $whereClause = implode(" AND ", $whereConditions);
    
    // Build main query
    $sql = "SELECT 
        t.id,
        t.ticket_number,
        t.table_id,
        tbl.name as table_name,
        tbl.table_number as table_number,
        t.customer_name,
        t.customer_phone,
        t.status,
        t.opened_at,
        t.closed_at,
        COUNT(DISTINCT o.id) as orders_count,
        COALESCE(SUM(o.total_amount), 0) as total_amount
        FROM tickets t
        LEFT JOIN tables tbl ON t.table_id = tbl.id
        LEFT JOIN orders o ON t.id = o.ticket_id AND o.status NOT IN ('cancelled', 'voided')
        WHERE $whereClause
        GROUP BY t.id, t.ticket_number, t.table_id, tbl.name, tbl.table_number, 
                 t.customer_name, t.customer_phone, t.status, t.opened_at, t.closed_at
        ORDER BY t.opened_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get items count and orders for each ticket
    foreach ($tickets as &$ticket) {
        // Get items count
        $itemsStmt = $pdo->prepare("
            SELECT COUNT(*) as items_count
            FROM order_items oi
            JOIN orders ord ON oi.order_id = ord.id
            WHERE ord.ticket_id = :ticket_id AND oi.is_voided = 0
        ");
        $itemsStmt->execute(['ticket_id' => $ticket['id']]);
        $itemsData = $itemsStmt->fetch(PDO::FETCH_ASSOC);
        $ticket['items_count'] = $itemsData['items_count'] ?? 0;
        
        // Get orders for this ticket
        $ordersStmt = $pdo->prepare("
            SELECT o.id, o.total_amount, o.status, o.created_at
            FROM orders o
            WHERE o.ticket_id = :ticket_id
            ORDER BY o.created_at ASC
        ");
        $ordersStmt->execute(['ticket_id' => $ticket['id']]);
        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get items for each order
        foreach ($orders as &$order) {
            $itemsStmt = $pdo->prepare("
                SELECT oi.id, oi.menu_item_id, m.name as item_name, 
                       oi.quantity, oi.price, oi.notes, oi.modifiers, oi.is_voided
                FROM order_items oi
                LEFT JOIN menu_items m ON oi.menu_item_id = m.id
                WHERE oi.order_id = :order_id
            ");
            $itemsStmt->execute(['order_id' => $order['id']]);
            $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $ticket['orders'] = $orders;
        
        // Calculate duration
        if ($ticket['opened_at']) {
            $opened = new DateTime($ticket['opened_at']);
            $now = new DateTime();
            $diff = $now->diff($opened);
            $ticket['duration'] = sprintf('%dh %dm', $diff->h, $diff->i + ($diff->days * 60));
        }
    }
    
    jsonResponse([
        'success' => true,
        'tickets' => $tickets,
        'count' => count($tickets)
    ]);
    
} catch (PDOException $e) {
    error_log("Tickets list PDO error: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    jsonResponse([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ], 500);
} catch (Exception $e) {
    error_log("Tickets list error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($data);
    exit;
}
