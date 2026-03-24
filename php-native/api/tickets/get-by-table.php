<?php
/**
 * Tickets API - Get Active Ticket by Table ID
 * GET /api/tickets/get-by-table.php?table_id=X
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';

try {
    $tableId = $_GET['table_id'] ?? 0;
    
    if (!$tableId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Table ID required']);
        exit;
    }
    
    $pdo = getDbConnection();
    
    // Get active ticket for table
    $stmt = $pdo->prepare("
        SELECT t.*, 
               (SELECT COUNT(*) FROM orders WHERE ticket_id = t.id) as order_count,
               (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE ticket_id = t.id) as total_amount,
               (SELECT COUNT(*) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.ticket_id = t.id) as total_items
        FROM tickets t
        WHERE t.table_id = ? AND t.status = 'open'
        ORDER BY t.opened_at DESC
        LIMIT 1
    ");
    $stmt->execute([$tableId]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($ticket) {
        // Get orders for this ticket
        $stmt = $pdo->prepare("
            SELECT o.id, o.ticket_id, o.total_amount, o.status, o.created_at,
                   (SELECT JSON_ARRAYAGG(JSON_OBJECT(
                       'id', oi.id,
                       'menu_item_id', oi.menu_item_id,
                       'item_name', m.name,
                       'quantity', oi.quantity,
                       'price', oi.price,
                       'notes', oi.notes,
                       'modifiers', oi.modifiers,
                       'status', oi.status,
                       'is_voided', oi.is_voided
                   ))
                    FROM order_items oi 
                    LEFT JOIN menu_items m ON oi.menu_item_id = m.id
                    WHERE oi.order_id = o.id) as items
            FROM orders o
            WHERE o.ticket_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$ticket['id']]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $ticket['orders'] = $orders;
        echo json_encode(['success' => true, 'ticket' => $ticket]);
    } else {
        echo json_encode(['success' => false, 'ticket' => null, 'message' => 'No active ticket found']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
