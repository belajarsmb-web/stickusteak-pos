<?php
/**
 * Shift Management API - Close Shift
 * POST /api/shifts/close.php
 * Closes active shift with closing balance
 */

error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    // Get active shift
    $shiftStmt = $pdo->query("SELECT * FROM shifts WHERE status = 'open' ORDER BY id DESC LIMIT 1");
    $shift = $shiftStmt->fetch(PDO::FETCH_ASSOC);

    if (!$shift) {
        jsonResponse(['success' => false, 'message' => 'No active shift found'], 404);
    }

    $shiftId = $shift['id'];
    $closingBalance = floatval($input['closing_balance'] ?? 0);
    $notes = $input['notes'] ?? '';

    // VALIDATION: Check if there are any open or unpaid tickets in this shift
    $checkTicketsStmt = $pdo->prepare("
        SELECT COUNT(*) as count, GROUP_CONCAT(ticket_number SEPARATOR ', ') as ticket_numbers
        FROM tickets
        WHERE status IN ('open', 'pending')
        AND opened_at >= :shift_start
    ");
    $checkTicketsStmt->execute(['shift_start' => $shift['opened_at'] ?? date('Y-m-d H:i:s')]);
    $ticketCheck = $checkTicketsStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($ticketCheck['count'] > 0) {
        jsonResponse([
            'success' => false,
            'message' => 'Cannot close shift! There are ' . $ticketCheck['count'] . ' ticket(s) still open/unpaid: ' . $ticketCheck['ticket_numbers'] . '. Please close all tickets before closing the shift.'
        ], 400);
        exit;
    }

    // Calculate shift statistics from orders
    $statsStmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT o.id) as total_orders,
            COALESCE(SUM(o.total_amount), 0) as total_sales,
            COALESCE(SUM(CASE WHEN o.status IN ('paid', 'completed') THEN o.total_amount ELSE 0 END), 0) as total_paid
        FROM orders o
        WHERE o.created_at >= :shift_start
        AND o.status NOT IN ('cancelled', 'voided')
    ");
    $statsStmt->execute(['shift_start' => $shift['opened_at'] ?? date('Y-m-d H:i:s')]);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    $shift['stats'] = $stats;

    // Close shift - only update status and notes (basic columns)
    $updateData = [];
    $updateData[] = "status = 'closed'";
    if ($notes) {
        $updateData[] = "notes = '" . $pdo->quote($notes . ' | Closing Balance: ' . $closingBalance) . "'";
    }
    
    $stmt = $pdo->prepare("UPDATE shifts SET " . implode(', ', $updateData) . " WHERE id = ?");
    $stmt->execute([$shiftId]);

    // Return shift data with stats
    jsonResponse([
        'success' => true,
        'message' => 'Shift closed successfully',
        'shift' => [
            'id' => $shiftId,
            'opening_balance' => $shift['opening_balance'],
            'closing_balance' => $closingBalance,
            'notes' => $notes,
            'stats' => $stats
        ]
    ]);

} catch (PDOException $e) {
    error_log("Close shift error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to close shift: ' . $e->getMessage()
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
