<?php
/**
 * Mobile Payment Processing API
 * POST /api/mobile/process-payment.php
 */

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    $order_id = $input['order_id'] ?? 0;
    $payment_method_id = $input['payment_method_id'] ?? 0;
    $amount_paid = $input['amount_paid'] ?? 0;
    $table_id = $input['table_id'] ?? 0;

    if (!$order_id) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Order ID required']);
        exit;
    }

    if (!$payment_method_id) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Payment method required']);
        exit;
    }

    $pdo->beginTransaction();

    // Get order total
    $stmt = $pdo->prepare("SELECT total_amount FROM orders WHERE id = :id");
    $stmt->execute(['id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $pdo->rollBack();
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    $total = floatval($order['total_amount']);
    $change = floatval($amount_paid) - $total;

    if ($change < 0) {
        $pdo->rollBack();
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Insufficient payment amount']);
        exit;
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

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Payment processed successfully',
        'change' => $change,
        'order_id' => $order_id
    ]);

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Payment process error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process payment: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Payment process error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process payment'
    ]);
}
