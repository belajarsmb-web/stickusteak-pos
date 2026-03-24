<?php
/**
 * Payments API - POST create payment
 * Required: order_id, payment_method_id, amount
 * Optional: transaction_id, status, notes
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require authentication
requireAuth();

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $errors = [];
    
    if (empty($input['order_id'])) {
        $errors[] = 'Order ID is required';
    }
    
    if (empty($input['payment_method_id'])) {
        $errors[] = 'Payment method ID is required';
    }
    
    if (!isset($input['amount']) || !is_numeric($input['amount']) || floatval($input['amount']) <= 0) {
        $errors[] = 'Valid payment amount is required';
    }
    
    if (!empty($errors)) {
        jsonResponse(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
    }
    
    $orderId = intval($input['order_id']);
    $paymentMethodId = intval($input['payment_method_id']);
    $amount = floatval($input['amount']);
    
    // Check if order exists
    $order = dbQuery("SELECT * FROM orders WHERE id = ?", [$orderId]);
    $orderData = $order->fetch(PDO::FETCH_ASSOC);
    
    if (!$orderData) {
        jsonResponse(['success' => false, 'message' => 'Order not found'], 404);
    }
    
    // Check if payment method exists
    $paymentMethod = dbQuery("SELECT * FROM payment_methods WHERE id = ? AND is_active = 1", [$paymentMethodId]);
    if (!$paymentMethod->fetch(PDO::FETCH_ASSOC)) {
        jsonResponse(['success' => false, 'message' => 'Payment method not found or inactive'], 404);
    }
    
    // Check if order already has a completed payment
    $existingPayment = dbQuery("SELECT id FROM payments WHERE order_id = ? AND status = 'completed'", [$orderId]);
    if ($existingPayment->fetchColumn()) {
        jsonResponse(['success' => false, 'message' => 'Order already has a completed payment'], 409);
    }
    
    // Validate payment amount against order total
    if ($amount > floatval($orderData['total_amount'])) {
        jsonResponse(['success' => false, 'message' => 'Payment amount exceeds order total'], 400);
    }
    
    // Determine default status
    $status = isset($input['status']) ? $input['status'] : 'pending';
    $validStatuses = ['pending', 'completed', 'failed', 'refunded'];
    if (!in_array($status, $validStatuses)) {
        $status = 'pending';
    }
    
    // Insert new payment
    $sql = "INSERT INTO payments (order_id, payment_method_id, amount, transaction_id, status, notes, is_active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
    
    dbExecute($sql, [
        $orderId,
        $paymentMethodId,
        $amount,
        !empty($input['transaction_id']) ? trim($input['transaction_id']) : null,
        $status,
        !empty($input['notes']) ? trim($input['notes']) : null
    ]);
    
    $paymentId = dbQuery("SELECT LAST_INSERT_ID()")->fetchColumn();
    
    // If payment is completed, update order status
    if ($status === 'completed') {
        dbExecute("UPDATE orders SET payment_status = 'paid', status = 'completed', updated_at = NOW() WHERE id = ?", [$orderId]);
    }
    
    // Fetch the created payment
    $payment = dbQuery("SELECT * FROM payments WHERE id = ?", [$paymentId])->fetch(PDO::FETCH_ASSOC);
    
    jsonResponse([
        'success' => true,
        'message' => 'Payment created successfully',
        'data' => $payment
    ], 201);
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to create payment', 'error' => $e->getMessage()], 500);
}
