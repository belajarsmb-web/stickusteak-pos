<?php
/**
 * RestoQwen POS - Payment API
 * GET /api/pos/payment-methods.php - Get payment methods
 * POST /api/pos/process-payment.php - Process payment
 */

require_once __DIR__ . '/../../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'GET') {
    // Get payment methods
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY id");
        $stmt->execute();
        $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        jsonResponse([
            'success' => true,
            'methods' => $methods,
            'count' => count($methods)
        ]);
    } catch (PDOException $e) {
        error_log("Payment methods fetch error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to fetch payment methods'], 500);
    }
} elseif ($method === 'POST') {
    // Process payment
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);
        
        $order_id = $input['order_id'] ?? 0;
        $payment_method_id = $input['payment_method_id'] ?? 0;
        $amount_paid = $input['amount_paid'] ?? 0;
        $table_id = $input['table_id'] ?? 0;
        
        if (!$order_id) {
            jsonResponse(['success' => false, 'message' => 'Order ID required'], 400);
        }
        
        if (!$payment_method_id) {
            jsonResponse(['success' => false, 'message' => 'Payment method required'], 400);
        }
        
        $pdo->beginTransaction();
        
        // Get order total
        $stmt = $pdo->prepare("SELECT total_amount FROM orders WHERE id = :id");
        $stmt->execute(['id' => $order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            $pdo->rollBack();
            jsonResponse(['success' => false, 'message' => 'Order not found'], 404);
        }
        
        $total = floatval($order['total_amount']);
        $change = floatval($amount_paid) - $total;
        
        if ($change < 0) {
            $pdo->rollBack();
            jsonResponse(['success' => false, 'message' => 'Insufficient payment amount'], 400);
        }
        
        // Update order status to paid
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET status = 'paid', 
                payment_method_id = :payment_method_id,
                paid_amount = :paid_amount,
                updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            'id' => $order_id,
            'payment_method_id' => $payment_method_id,
            'paid_amount' => $amount_paid
        ]);
        
        // Free up the table
        if ($table_id) {
            $stmt = $pdo->prepare("UPDATE tables SET status = 'available' WHERE id = :id");
            $stmt->execute(['id' => $table_id]);
        }
        
        $pdo->commit();
        
        jsonResponse([
            'success' => true,
            'message' => 'Payment processed successfully',
            'change' => $change,
            'order_id' => $order_id
        ]);
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Payment process error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to process payment'], 500);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
