<?php
/**
 * Process Payment for Ticket
 * POST /api/payments/process-ticket-payment.php
 * 
 * Updates:
 * - Ticket status → 'paid'
 * - All orders in ticket → 'paid'
 * - Table status → 'available'
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    $ticketId = $input['ticket_id'] ?? 0;
    $paymentMethodId = (int)($input['payment_method_id'] ?? 1);
    $paidAmount = floatval($input['paid_amount'] ?? 0);
    $customerName = $input['customer_name'] ?? '';
    $customerPhone = $input['customer_phone'] ?? '';

    if (!$ticketId) {
        throw new Exception('Ticket ID required');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Get ticket and calculate total
    $ticketStmt = $pdo->prepare("
        SELECT t.*, 
               COALESCE(SUM(o.total_amount), 0) as total_amount
        FROM tickets t
        LEFT JOIN orders o ON t.id = o.ticket_id AND o.status NOT IN ('cancelled', 'voided')
        WHERE t.id = :ticket_id
        GROUP BY t.id
    ");
    $ticketStmt->execute(['ticket_id' => $ticketId]);
    $ticket = $ticketStmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        throw new Exception('Ticket not found');
    }

    $totalAmount = floatval($ticket['total_amount']);
    $change = $paidAmount - $totalAmount;

    if ($paidAmount < $totalAmount) {
        throw new Exception('Insufficient payment amount. Total: Rp ' . number_format($totalAmount, 0, ',', '.') . ', Paid: Rp ' . number_format($paidAmount, 0, ',', '.'));
    }

    // Get all order IDs in this ticket
    $ordersStmt = $pdo->prepare("SELECT id FROM orders WHERE ticket_id = :ticket_id");
    $ordersStmt->execute(['ticket_id' => $ticketId]);
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($orders) === 0) {
        throw new Exception('No orders found in ticket');
    }

    // Get the first order ID for receipt
    $firstOrderId = $orders[0]['id'];

    // Update all orders to 'paid' with customer info and paid_amount
    $updateOrdersStmt = $pdo->prepare("
        UPDATE orders 
        SET status = 'paid', 
            customer_name = :customer_name,
            customer_phone = :customer_phone,
            paid_amount = :paid_amount,
            updated_at = NOW() 
        WHERE ticket_id = :ticket_id
    ");
    $updateOrdersStmt->execute([
        'ticket_id' => $ticketId,
        'customer_name' => $customerName,
        'customer_phone' => $customerPhone,
        'paid_amount' => $paidAmount
    ]);

    // Record payment for the first order (main payment)
    $paymentStmt = $pdo->prepare("
        INSERT INTO payments (order_id, payment_method_id, amount, created_at)
        VALUES (:order_id, :payment_method_id, :amount, NOW())
    ");
    $paymentStmt->execute([
        'order_id' => $firstOrderId,
        'payment_method_id' => $paymentMethodId,
        'amount' => $paidAmount
    ]);

    // Update ticket status to 'paid' with customer info
    // But first verify ALL orders in this ticket are actually paid
    $checkStmt = $pdo->prepare("
        SELECT COUNT(*) as total, 
               SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count
        FROM orders 
        WHERE ticket_id = :ticket_id AND status NOT IN ('cancelled', 'voided')
    ");
    $checkStmt->execute(['ticket_id' => $ticketId]);
    $checkData = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($checkData['total'] > 0 && $checkData['total'] === $checkData['paid_count']) {
        // All orders are paid, update ticket to 'paid'
        $updateTicketStmt = $pdo->prepare("
            UPDATE tickets 
            SET status = 'paid', 
                customer_name = :customer_name,
                customer_phone = :customer_phone,
                closed_at = NOW() 
            WHERE id = :ticket_id
        ");
        $updateTicketStmt->execute([
            'ticket_id' => $ticketId,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone
        ]);
    } else {
        // Not all orders paid yet, keep as 'open' or update to 'partial'
        // For now, keep as 'open' but update customer info
        $updateTicketStmt = $pdo->prepare("
            UPDATE tickets 
            SET customer_name = :customer_name,
                customer_phone = :customer_phone
            WHERE id = :ticket_id
        ");
        $updateTicketStmt->execute([
            'ticket_id' => $ticketId,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone
        ]);
    }

    // Update table status to 'available'
    $updateTableStmt = $pdo->prepare("UPDATE tables SET status = 'available', updated_at = NOW() WHERE id = :table_id");
    $updateTableStmt->execute(['table_id' => $ticket['table_id']]);

    // Get payment method name for response
    $pmStmt = $pdo->prepare("SELECT name FROM payment_methods WHERE id = :id");
    $pmStmt->execute(['id' => $paymentMethodId]);
    $pmData = $pmStmt->fetch(PDO::FETCH_ASSOC);
    $paymentMethodName = $pmData['name'] ?? 'Cash';

    // Commit transaction
    $pdo->commit();

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Payment processed successfully',
        'ticket_id' => $ticketId,
        'order_id' => $firstOrderId,
        'total_amount' => $totalAmount,
        'paid_amount' => $paidAmount,
        'change' => $change,
        'table_id' => $ticket['table_id'],
        'payment_method_name' => $paymentMethodName,
        'customer_name' => $customerName,
        'customer_phone' => $customerPhone
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
