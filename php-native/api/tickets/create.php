<?php
/**
 * Tickets API - Create or Get Active Ticket for Table
 * POST /api/tickets/create.php - Create new ticket
 * GET /api/tickets/get-by-table.php?table_id=X - Get active ticket
 */

require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getDbConnection();

    if ($method === 'POST') {
        // Create new ticket
        $input = json_decode(file_get_contents('php://input'), true);
        $tableId = (int)($input['table_id'] ?? 0);
        $customerName = $input['customer_name'] ?? '';
        $customerPhone = $input['customer_phone'] ?? '';

        if (!$tableId) {
            throw new Exception('Table ID required');
        }

        // Generate ticket number
        $ticketNumber = 'TKT-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Create ticket
        $stmt = $pdo->prepare("
            INSERT INTO tickets (table_id, ticket_number, status, customer_name, customer_phone)
            VALUES (?, ?, 'open', ?, ?)
        ");
        $stmt->execute([$tableId, $ticketNumber, $customerName, $customerPhone]);
        $ticketId = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'ticket_id' => $ticketId,
            'ticket_number' => $ticketNumber,
            'message' => 'Ticket created successfully'
        ]);

    } elseif ($method === 'GET') {
        // Get ticket by ticket_number (any status) OR by table_id (open only)
        $tableId = (int)($_GET['table_id'] ?? 0);
        $ticketNumber = $_GET['ticket_number'] ?? '';

        if ($ticketNumber) {
            // Search by ticket number - any status
            $stmt = $pdo->prepare("
                SELECT * FROM tickets
                WHERE ticket_number LIKE ?
                ORDER BY opened_at DESC
                LIMIT 1
            ");
            $stmt->execute(["%$ticketNumber%"]);
        } elseif ($tableId) {
            // Get active ticket for table
            $stmt = $pdo->prepare("
                SELECT * FROM tickets
                WHERE table_id = ?
                ORDER BY opened_at DESC
                LIMIT 1
            ");
            $stmt->execute([$tableId]);
        } else {
            throw new Exception('Table ID or Ticket Number required');
        }
        
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ticket) {
            // Get all orders in this ticket
            $ordersStmt = $pdo->prepare("
                SELECT o.*, 
                (SELECT JSON_ARRAYAGG(JSON_OBJECT(
                    'id', oi.id,
                    'menu_item_id', oi.menu_item_id,
                    'quantity', oi.quantity,
                    'price', oi.price,
                    'name', m.name,
                    'notes', oi.notes,
                    'modifiers', oi.modifiers
                )) FROM order_items oi
                LEFT JOIN menu_items m ON oi.menu_item_id = m.id
                WHERE oi.order_id = o.id) as items
                FROM orders o
                WHERE o.ticket_id = ?
                ORDER BY o.created_at ASC
            ");
            $ordersStmt->execute([$ticket['id']]);
            $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total amount from all orders
            $totalStmt = $pdo->prepare("SELECT SUM(total_amount) as total_amount FROM orders WHERE ticket_id = ?");
            $totalStmt->execute([$ticket['id']]);
            $totalData = $totalStmt->fetch(PDO::FETCH_ASSOC);

            $ticket['orders'] = $orders;
            $ticket['orders_count'] = count($orders);
            $ticket['total_amount'] = $totalData['total_amount'] ?? 0;

            echo json_encode([
                'success' => true,
                'ticket' => $ticket,
                'message' => 'Ticket found'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No active ticket for this table'
            ]);
        }

    } else {
        throw new Exception('Method not allowed');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
