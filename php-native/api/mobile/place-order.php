<?php
/**
 * Mobile Order API - Place Order
 * POST /api/mobile/place-order.php
 */

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    
    $table_id = $input['table_id'] ?? 0;
    $customer_name = $input['customer_name'] ?? '';
    $customer_phone = $input['customer_phone'] ?? '';
    $items = $input['items'] ?? [];
    $notes = $input['notes'] ?? '';
    $token = $input['token'] ?? '';
    
    // Validation
    if (!$table_id) {
        jsonResponse(['success' => false, 'message' => 'Table ID required'], 400);
    }
    
    if (empty($customer_name)) {
        jsonResponse(['success' => false, 'message' => 'Customer name required'], 400);
    }
    
    if (empty($items)) {
        jsonResponse(['success' => false, 'message' => 'At least one item required'], 400);
    }
    
    // Generate unique mobile token
    $mobile_token = bin2hex(random_bytes(32));
    
    // Calculate total
    $total = 0;
    foreach ($items as $item) {
        $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
    }
    
    // Get tax and service settings
    $stmt = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'tax_percentage'");
    $taxPercent = floatval($stmt->fetchColumn() ?: 10);
    
    $stmt = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'service_charge_percentage'");
    $servicePercent = floatval($stmt->fetchColumn() ?: 5);
    
    $taxAmount = ($total * $taxPercent) / 100;
    $serviceCharge = ($total * $servicePercent) / 100;
    $finalTotal = $total + $taxAmount + $serviceCharge;
    
    $pdo->beginTransaction();
    
    // Create order
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            table_id, total_amount, status, order_source, mobile_token, 
            customer_name, customer_phone, notes, created_at, updated_at
        ) VALUES (
            :table_id, :total_amount, 'pending', 'mobile', :mobile_token,
            :customer_name, :customer_phone, :notes, NOW(), NOW()
        )
    ");
    
    $stmt->execute([
        'table_id' => $table_id,
        'total_amount' => $finalTotal,
        'mobile_token' => $mobile_token,
        'customer_name' => $customer_name,
        'customer_phone' => $customer_phone,
        'notes' => $notes
    ]);
    
    $orderId = $pdo->lastInsertId();
    
    // Create order items
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, menu_item_id, quantity, price, notes, modifiers, created_at)
        VALUES (:order_id, :menu_item_id, :quantity, :price, :notes, :modifiers, NOW())
    ");
    
    foreach ($items as $item) {
        $stmt->execute([
            'order_id' => $orderId,
            'menu_item_id' => $item['menu_id'] ?? $item['id'] ?? 0,
            'quantity' => $item['quantity'] ?? 1,
            'price' => $item['price'] ?? 0,
            'notes' => isset($item['notes']) ? json_encode($item['notes']) : null,
            'modifiers' => isset($item['modifiers']) ? json_encode($item['modifiers']) : null
        ]);
    }
    
    // Update table status to occupied
    $stmt = $pdo->prepare("UPDATE tables SET status = 'occupied' WHERE id = :id");
    $stmt->execute(['id' => $table_id]);
    
    $pdo->commit();

    // Generate tracking URL
    $trackingUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/php-native/mobile/order-status.php?token=' . $mobile_token;

    jsonResponse([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $orderId,
        'mobile_token' => $mobile_token,
        'tracking_url' => $trackingUrl,
        'total' => $finalTotal,
        'breakdown' => [
            'subtotal' => $total,
            'tax' => $taxAmount,
            'service_charge' => $serviceCharge
        ]
    ]);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Mobile place order error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to place order'], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
