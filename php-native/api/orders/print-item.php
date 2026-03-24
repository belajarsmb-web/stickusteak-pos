<?php
/**
 * RestoQwen POS - Print Order Item API
 * POST /api/orders/print-item.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    
    $order_item_id = $input['order_item_id'] ?? 0;
    $print_reason_id = $input['print_reason_id'] ?? null;
    $print_reason_text = $input['print_reason_text'] ?? '';
    $user_id = getCurrentUserId();
    $is_reprint = $input['is_reprint'] ?? false;
    
    if (!$order_item_id) {
        jsonResponse(['success' => false, 'message' => 'Order item ID required'], 400);
    }
    
    // Get order item details
    $stmt = $pdo->prepare("
        SELECT oi.*, o.status as order_status, o.table_id
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE oi.id = :id
    ");
    $stmt->execute(['id' => $order_item_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$item) {
        jsonResponse(['success' => false, 'message' => 'Order item not found'], 404);
    }
    
    if ($item['is_voided']) {
        jsonResponse(['success' => false, 'message' => 'Cannot print voided item'], 400);
    }
    
    // For initial print (not reprint)
    if (!$is_reprint && $item['is_printed']) {
        jsonResponse(['success' => false, 'message' => 'Item already printed. Use reprint if needed.'], 400);
    }
    
    $pdo->beginTransaction();
    
    // Update print status
    $stmt = $pdo->prepare("
        UPDATE order_items 
        SET is_printed = 1,
            print_count = print_count + 1,
            printed_by = :user_id,
            printed_at = NOW(),
            updated_at = NOW()
        WHERE id = :id
    ");
    
    $stmt->execute([
        'id' => $order_item_id,
        'user_id' => $user_id
    ]);
    
    // Get updated print count
    $stmt = $pdo->prepare("SELECT print_count FROM order_items WHERE id = :id");
    $stmt->execute(['id' => $order_item_id]);
    $updatedItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Log reprint reason if this is a reprint
    if ($is_reprint && ($print_reason_id || $print_reason_text)) {
        $stmt = $pdo->prepare("
            INSERT INTO order_print_log (order_item_id, print_reason_id, print_reason_text, printed_by, printed_at)
            VALUES (:order_item_id, :print_reason_id, :print_reason_text, :printed_by, NOW())
        ");
        $stmt->execute([
            'order_item_id' => $order_item_id,
            'print_reason_id' => $print_reason_id,
            'print_reason_text' => $print_reason_text,
            'printed_by' => $user_id
        ]);
    }
    
    $pdo->commit();
    
    jsonResponse([
        'success' => true,
        'message' => $is_reprint ? 'Item reprinted successfully' : 'Item printed successfully',
        'print_count' => $updatedItem['print_count']
    ]);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Print item error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to print item'], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
