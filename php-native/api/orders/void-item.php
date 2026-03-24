<?php
/**
 * RestoQwen POS - Void Order Item API
 * POST /api/orders/void-item.php
 * Includes auto stock return functionality
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../inventory/auto-stock-deduction.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    
    $order_item_id = $input['order_item_id'] ?? 0;
    $void_reason_id = $input['void_reason_id'] ?? null;
    $void_reason_text = $input['void_reason_text'] ?? '';
    
    // Get user_id from session if available, otherwise use default
    session_start();
    $user_id = $_SESSION['user_id'] ?? 1;

    if (!$order_item_id) {
        jsonResponse(['success' => false, 'message' => 'Order item ID required'], 400);
    }

    // Allow void without reason (reason can be optional)
    $void_reason_id = $void_reason_id ?: null;
    $void_reason_text = $void_reason_text ?: null;
    
    $pdo->beginTransaction();
    
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
        $pdo->rollBack();
        jsonResponse(['success' => false, 'message' => 'Order item not found'], 404);
    }
    
    if ($item['is_voided']) {
        $pdo->rollBack();
        jsonResponse(['success' => false, 'message' => 'Item already voided'], 400);
    }
    
    // Void the order item
    $stmt = $pdo->prepare("
        UPDATE order_items
        SET is_voided = 1,
            void_reason = :void_reason_id,
            void_reason_text = :void_reason_text,
            voided_by = :voided_by,
            voided_at = NOW(),
            updated_at = NOW()
        WHERE id = :id
    ");

    $stmt->execute([
        'id' => $order_item_id,
        'void_reason_id' => $void_reason_id,
        'void_reason_text' => $void_reason_text,
        'voided_by' => $user_id
    ]);

    // AUTO STOCK RETURN - Return ingredients back to inventory
    try {
        $returnResult = returnStockForVoidedItem($item, $pdo);
        $stockAlert = '';
        if (isset($returnResult['success']) && !$returnResult['success']) {
            error_log("Stock return failed: " . ($returnResult['message'] ?? 'Unknown error'));
        }
    } catch (Exception $e) {
        error_log("Stock return error: " . $e->getMessage());
        // Don't fail the void if stock return fails - just log it
    }
    
    // Check if all items are voided - if so, update order status
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total, SUM(is_voided) as voided
        FROM order_items
        WHERE order_id = :order_id
    ");
    $stmt->execute(['order_id' => $item['order_id']]);
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($counts['total'] == $counts['voided']) {
        // All items voided - cancel order
        $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $item['order_id']]);
        
        // Free up table if all orders cancelled
        if ($item['table_id']) {
            $stmt = $pdo->prepare("UPDATE tables SET status = 'available' WHERE id = :id");
            $stmt->execute(['id' => $item['table_id']]);
        }
    }
    
    $pdo->commit();
    
    jsonResponse([
        'success' => true,
        'message' => 'Item voided successfully',
        'item_id' => $order_item_id,
        'order_status' => $counts['total'] == $counts['voided'] ? 'cancelled' : $item['order_status']
    ]);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Void item error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to void item'], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
