<?php
/**
 * RestoQwen POS - Orders Store API
 * POST /api/orders/store.php
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
    $customer_id = $input['customer_id'] ?? null;
    $table_id = $input['table_id'] ?? null;
    $items = $input['items'] ?? [];
    
    if (empty($items)) {
        jsonResponse(['success' => false, 'message' => 'At least one item is required'], 400);
    }
    
    // Calculate total
    $total = 0;
    foreach ($items as $item) {
        $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
    }
    
    // Start transaction
    dbBeginTransaction();
    
    // Create order
    $stmt = $pdo->prepare("
        INSERT INTO orders (customer_id, table_id, total_amount, status, created_at)
        VALUES (:customer_id, :table_id, :total_amount, 'pending', NOW())
    ");
    $stmt->execute([
        'customer_id' => $customer_id,
        'table_id' => $table_id,
        'total_amount' => $total
    ]);
    
    $orderId = $pdo->lastInsertId();
    
    // Create order items
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, menu_id, quantity, price, subtotal)
        VALUES (:order_id, :menu_id, :quantity, :price, :subtotal)
    ");
    
    foreach ($items as $item) {
        $subtotal = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        $stmt->execute([
            'order_id' => $orderId,
            'menu_id' => $item['menu_id'] ?? $item['menu_item_id'] ?? null,
            'quantity' => $item['quantity'] ?? 1,
            'price' => $item['price'] ?? 0,
            'subtotal' => $subtotal
        ]);
    }
    
    dbCommit();
    
    jsonResponse([
        'success' => true,
        'message' => 'Order created successfully',
        'order_id' => $orderId,
        'total' => $total
    ]);
    
} catch (PDOException $e) {
    dbRollback();
    error_log("Order store error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to create order: ' . $e->getMessage()
    ], 500);
} catch (Exception $e) {
    dbRollback();
    error_log("Order store error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to create order'
    ], 500);
}
