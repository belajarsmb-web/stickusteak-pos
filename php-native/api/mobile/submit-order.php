<?php
/**
 * Mobile POS - Submit Order API
 * POST /api/mobile/submit-order.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../inventory/auto-stock-deduction.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    $table_id = $input['table_id'] ?? 0;
    $items = $input['items'] ?? [];
    $service_type = $input['service_type'] ?? 'dine_in';

    if (!$table_id) {
        jsonResponse(['success' => false, 'message' => 'Table ID required'], 400);
    }

    if (empty($items)) {
        jsonResponse(['success' => false, 'message' => 'No items in order'], 400);
    }

    // Calculate total
    $subTotal = 0;
    foreach ($items as $item) {
        $subTotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
    }

    // Get tax rate from settings
    $taxRate = 11; // Default
    try {
        $outlet = $pdo->query("SELECT tax_rate FROM outlets WHERE id = 1 LIMIT 1")->fetch();
        if ($outlet) {
            $taxRate = (float)$outlet['tax_rate'];
        }
    } catch (Exception $e) {
        // Use default
    }

    $taxAmount = ($subTotal * $taxRate) / 100;
    $totalAmount = $subTotal + $taxAmount;

    // Start transaction
    $pdo->beginTransaction();

    // Create order with status 'sent_to_kitchen'
    // Get user_id from session if available, otherwise use default (mobile orders don't always have session)
    session_start();
    $created_by = $_SESSION['user_id'] ?? 1;
    
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            table_id, service_type, status, sub_total, tax_amount, total_amount,
            created_by, created_at, updated_at
        ) VALUES (
            :table_id, :service_type, 'sent_to_kitchen', :sub_total, :tax_amount, :total_amount,
            :created_by, NOW(), NOW()
        )
    ");
    $stmt->execute([
        'table_id' => $table_id,
        'service_type' => $service_type,
        'sub_total' => $subTotal,
        'tax_amount' => $taxAmount,
        'total_amount' => $totalAmount,
        'created_by' => $created_by
    ]);

    $orderId = $pdo->lastInsertId();

    // Create order items
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, menu_item_id, quantity, price, notes, modifiers, created_at)
        VALUES (:order_id, :menu_item_id, :quantity, :price, :notes, :modifiers, NOW())
    ");

    foreach ($items as $item) {
        $notes = isset($item['notes']) ? json_encode($item['notes']) : null;
        $modifiers = isset($item['modifiers']) ? json_encode($item['modifiers']) : null;

        // Fix: Use menu_id from frontend
        $menuItemId = $item['id'] ?? $item['menu_id'] ?? $item['menu_item_id'] ?? 0;
        
        if (!$menuItemId) {
            $pdo->rollBack();
            jsonResponse(['success' => false, 'message' => 'Invalid menu item ID'], 400);
        }

        $stmt->execute([
            'order_id' => $orderId,
            'menu_item_id' => $menuItemId,
            'quantity' => $item['quantity'] ?? 1,
            'price' => $item['price'] ?? 0,
            'notes' => $notes,
            'modifiers' => $modifiers
        ]);
    }

    // Update table status to occupied
    $stmt = $pdo->prepare("UPDATE tables SET status = 'occupied' WHERE id = :id");
    $stmt->execute(['id' => $table_id]);

    $pdo->commit();

    // AUTO STOCK DEDUCTION (after order is committed)
    try {
        $stockResult = deductStockForOrder($orderId, $pdo);
        $stockAlert = '';
        if (isset($stockResult['has_alerts']) && $stockResult['has_alerts']) {
            $stockAlert = ' (Warning: Some ingredients low on stock)';
        }
    } catch (Exception $e) {
        error_log("Stock deduction failed: " . $e->getMessage());
        $stockAlert = ' (Stock deduction failed - manual adjustment needed)';
    }

    jsonResponse([
        'success' => true,
        'message' => 'Order submitted successfully' . ($stockAlert ?? ''),
        'order_id' => $orderId,
        'total' => $totalAmount,
        'stock_deducted' => isset($stockResult) ? $stockResult['deducted'] : 0
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Mobile order submit error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to create order: ' . $e->getMessage()
    ], 500);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Mobile order submit error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to create order'
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
