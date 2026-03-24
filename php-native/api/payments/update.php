<?php
/**
 * Payments API - PUT update payment status
 * Required: id
 * Optional: status, transaction_id, notes
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require authentication
requireAuth();

// Only allow PUT/POST method
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $errors = [];
    
    if (empty($input['id'])) {
        $errors[] = 'Payment ID is required';
    }
    
    if (!empty($errors)) {
        jsonResponse(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
    }
    
    $paymentId = intval($input['id']);
    
    // Check if payment exists
    $existingPayment = dbQuery("SELECT * FROM payments WHERE id = ?", [$paymentId]);
    $payment = $existingPayment->fetch(PDO::FETCH_ASSOC);
    
    if (!$payment) {
        jsonResponse(['success' => false, 'message' => 'Payment not found'], 404);
    }
    
    // Validate status if provided
    $validStatuses = ['pending', 'completed', 'failed', 'refunded'];
    if (isset($input['status']) && !in_array($input['status'], $validStatuses)) {
        $errors[] = 'Invalid status. Valid statuses: ' . implode(', ', $validStatuses);
    }
    
    if (!empty($errors)) {
        jsonResponse(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
    }
    
    // Build update query dynamically
    $updateFields = [];
    $params = [];
    $oldStatus = $payment['status'];
    
    if (isset($input['status'])) {
        $updateFields[] = 'status = ?';
        $params[] = $input['status'];
    }
    if (isset($input['transaction_id'])) {
        $updateFields[] = 'transaction_id = ?';
        $params[] = trim($input['transaction_id']);
    }
    if (isset($input['notes'])) {
        $updateFields[] = 'notes = ?';
        $params[] = trim($input['notes']);
    }
    
    // Always update updated_at
    $updateFields[] = 'updated_at = NOW()';
    
    if (empty($updateFields) || count($updateFields) === 1) {
        jsonResponse(['success' => false, 'message' => 'No valid fields to update'], 400);
    }
    
    $params[] = $paymentId;
    $sql = "UPDATE payments SET " . implode(', ', $updateFields) . " WHERE id = ?";
    
    dbExecute($sql, $params);
    
    // If status changed to completed, update order status
    if (isset($input['status']) && $input['status'] === 'completed' && $oldStatus !== 'completed') {
        dbExecute("UPDATE orders SET payment_status = 'paid', status = 'completed', updated_at = NOW() WHERE id = ?", [$payment['order_id']]);
    }
    
    // If status changed to refunded, update order status
    if (isset($input['status']) && $input['status'] === 'refunded' && $oldStatus !== 'refunded') {
        dbExecute("UPDATE orders SET payment_status = 'refunded', updated_at = NOW() WHERE id = ?", [$payment['order_id']]);
    }
    
    // Fetch updated payment
    $updatedPayment = dbQuery("SELECT * FROM payments WHERE id = ?", [$paymentId])->fetch(PDO::FETCH_ASSOC);
    
    jsonResponse([
        'success' => true,
        'message' => 'Payment updated successfully',
        'data' => $updatedPayment
    ]);
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to update payment', 'error' => $e->getMessage()], 500);
}
