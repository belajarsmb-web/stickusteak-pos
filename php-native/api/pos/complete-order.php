<?php
/**
 * RestoQwen POS - Complete Order API
 * POST /api/pos/complete-order.php
 * Processes payment and completes order
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    $order_id = $input['order_id'] ?? 0;
    $table_id = $input['table_id'] ?? 0;
    $payment_method_id = $input['payment_method_id'] ?? 1;
    $paid_amount = floatval($input['paid_amount'] ?? 0);
    $customer_name = $input['customer_name'] ?? '';
    $customer_phone = $input['customer_phone'] ?? '';

    error_log("Complete order request: order_id=" . $order_id . ", paid_amount=" . $paid_amount);

    if (!$order_id) {
        error_log("Order ID missing");
        jsonResponse(['success' => false, 'message' => 'Order ID required'], 400);
    }

    // Get order total
    $orderStmt = $pdo->prepare("SELECT total_amount FROM orders WHERE id = ?");
    $orderStmt->execute([$order_id]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        error_log("Order #" . $order_id . " not found");
        jsonResponse(['success' => false, 'message' => 'Order not found'], 404);
    }

    $total_amount = floatval($order['total_amount']);
    $change_amount = $paid_amount - $total_amount;

    error_log("Order total: " . $total_amount . ", Paid: " . $paid_amount . ", Change: " . $change_amount);

    if ($paid_amount < $total_amount) {
        error_log("Insufficient payment: paid " . $paid_amount . " < total " . $total_amount);
        jsonResponse(['success' => false, 'message' => 'Insufficient payment amount'], 400);
    }

    $pdo->beginTransaction();

    // Check if payment_method_id column exists in orders table
    $colCheck = $pdo->query("SHOW COLUMNS FROM orders LIKE 'payment_method_id'");
    $hasPaymentMethodColumn = $colCheck->rowCount() > 0;
    
    // Build update query dynamically based on column existence
    if ($hasPaymentMethodColumn) {
        $stmt = $pdo->prepare("UPDATE orders SET status = 'paid', customer_name = ?, customer_phone = ?, payment_method_id = ?, paid_amount = ?, change_amount = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$customer_name, $customer_phone, $payment_method_id, $paid_amount, $change_amount > 0 ? $change_amount : 0, $order_id]);
    } else {
        // Fallback: update without payment_method_id (legacy compatibility)
        $stmt = $pdo->prepare("UPDATE orders SET status = 'paid', customer_name = ?, customer_phone = ?, paid_amount = ?, change_amount = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$customer_name, $customer_phone, $paid_amount, $change_amount > 0 ? $change_amount : 0, $order_id]);
        error_log("Warning: orders.payment_method_id column missing - payment method not stored in orders table");
    }

    // Record payment
    $paymentStmt = $pdo->prepare("
        INSERT INTO payments (order_id, payment_method_id, amount, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    $paymentStmt->execute([
        $order_id,
        $payment_method_id,
        $paid_amount
    ]);

    // Free up table
    if ($table_id) {
        $stmt = $pdo->prepare("UPDATE tables SET status = 'available' WHERE id = ?");
        $stmt->execute([$table_id]);
    }
    
    // Get ticket_id from order and check if all orders for this ticket are paid
    $orderStmt = $pdo->prepare("SELECT ticket_id FROM orders WHERE id = ?");
    $orderStmt->execute([$order_id]);
    $orderData = $orderStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($orderData && $orderData['ticket_id']) {
        $ticket_id = $orderData['ticket_id'];
        
        // Check if all orders for this ticket are paid/completed
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) as total_orders,
                   SUM(CASE WHEN status IN ('paid', 'completed', 'cancelled', 'voided') THEN 1 ELSE 0 END) as paid_orders
            FROM orders WHERE ticket_id = ?
        ");
        $checkStmt->execute([$ticket_id]);
        $check = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        // If all orders are paid, close the ticket
        if ($check && $check['total_orders'] == $check['paid_orders'] && $check['total_orders'] > 0) {
            // Get total amount from all orders
            $totalStmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE ticket_id = ?");
            $totalStmt->execute([$ticket_id]);
            $ticketTotal = $totalStmt->fetchColumn();
            
            $closeStmt = $pdo->prepare("
                UPDATE tickets 
                SET status = 'paid', 
                    total_amount = ?,
                    closed_at = NOW(), 
                    paid_at = NOW() 
                WHERE id = ?
            ");
            $closeStmt->execute([$ticketTotal, $ticket_id]);
        }
    }

    $pdo->commit();

    jsonResponse([
        'success' => true,
        'message' => 'Payment processed successfully',
        'order_id' => $order_id,
        'change_amount' => $change_amount
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Complete order error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to complete order: ' . $e->getMessage()
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
