<?php
/**
 * Fix Ticket & Order Data API
 * POST /api/fix-ticket-order-data.php
 */

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    $ticketsFixed = 0;
    $ordersFixed = 0;

    // Fix all tickets with inconsistent status
    if (isset($input['fix_all'])) {
        // Fix tickets - update status to 'paid' if all orders are paid
        $fixTicketsStmt = $pdo->prepare("
            UPDATE tickets t
            INNER JOIN (
                SELECT ticket_id, COUNT(*) as total,
                       SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid
                FROM orders
                WHERE status NOT IN ('cancelled', 'voided')
                GROUP BY ticket_id
                HAVING total = paid
            ) x ON t.id = x.ticket_id
            SET t.status = 'paid', t.closed_at = NOW()
            WHERE t.status = 'open'
        ");
        $fixTicketsStmt->execute();
        $ticketsFixed = $fixTicketsStmt->rowCount();

        // Fix orders without paid_amount
        $fixOrdersStmt = $pdo->prepare("
            UPDATE orders
            SET paid_amount = total_amount
            WHERE status = 'paid'
            AND (paid_amount IS NULL OR paid_amount = 0)
        ");
        $fixOrdersStmt->execute();
        $ordersFixed = $fixOrdersStmt->rowCount();

        jsonResponse([
            'success' => true,
            'tickets_fixed' => $ticketsFixed,
            'orders_fixed' => $ordersFixed
        ]);
    }

    jsonResponse(['success' => false, 'message' => 'Invalid request']);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
