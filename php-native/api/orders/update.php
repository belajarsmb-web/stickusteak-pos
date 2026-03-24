<?php
/**
 * RestoQwen POS - Orders Update API
 * POST /api/orders/update.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require authentication
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    $status = $input['status'] ?? '';
    
    if (!$id || !$status) {
        jsonResponse(['success' => false, 'message' => 'Order ID and status are required'], 400);
    }

    $validStatuses = ['pending', 'sent_to_kitchen', 'in_progress', 'served', 'ready', 'completed', 'cancelled', 'paid', 'voided'];
    if (!in_array($status, $validStatuses)) {
        jsonResponse(['success' => false, 'message' => 'Invalid status'], 400);
    }
    
    $stmt = getDbConnection()->prepare("UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $id]);
    
    jsonResponse([
        'success' => true,
        'message' => 'Order updated successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Order update error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to update order'
    ], 500);
}
