<?php
/**
 * RestoQwen POS - Update Order Status API
 * POST /api/kds/update-order-status.php
 */

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    
    $order_id = $input['order_id'] ?? 0;
    $status = $input['status'] ?? '';
    
    if (!$order_id) {
        jsonResponse(['success' => false, 'message' => 'Order ID required'], 400);
    }

    $validStatuses = ['pending', 'sent_to_kitchen', 'in_progress', 'served', 'ready', 'completed', 'cancelled'];
    if (!in_array($status, $validStatuses)) {
        jsonResponse(['success' => false, 'message' => 'Invalid status'], 400);
    }
    
    $stmt = $pdo->prepare("UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $order_id]);
    
    jsonResponse([
        'success' => true,
        'message' => 'Order status updated successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Update order status error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to update order status'
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
