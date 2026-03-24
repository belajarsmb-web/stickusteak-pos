<?php
/**
 * Inventory API - POST create inventory item
 * Compatible with actual database schema
 */

error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($input['name'])) {
        throw new Exception('Item name is required');
    }
    if (empty($input['sku'])) {
        throw new Exception('SKU is required');
    }
    if (!isset($input['current_stock'])) {
        throw new Exception('Current stock is required');
    }
    if (empty($input['unit'])) {
        throw new Exception('Unit is required');
    }
    if (!isset($input['cost_price'])) {
        throw new Exception('Cost price is required');
    }

    // Check duplicate SKU
    $checkStmt = $pdo->prepare("SELECT id FROM inventory_items WHERE sku = ?");
    $checkStmt->execute([trim($input['sku'])]);
    if ($checkStmt->rowCount() > 0) {
        throw new Exception('SKU already exists');
    }

    // Insert new item
    $stmt = $pdo->prepare("
        INSERT INTO inventory_items (
            outlet_id, name, sku, unit, current_stock, min_stock, max_stock, 
            reorder_point, cost_price, is_active, created_at, updated_at
        ) VALUES (
            1, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW()
        )
    ");
    
    $stmt->execute([
        trim($input['name']),
        trim($input['sku']),
        trim($input['unit']),
        floatval($input['current_stock']),
        floatval($input['min_stock'] ?? 0),
        floatval($input['max_stock'] ?? 0),
        floatval($input['reorder_point'] ?? 0),
        floatval($input['cost_price'])
    ]);

    $itemId = $pdo->lastInsertId();

    // Get created item
    $getItem = $pdo->prepare("SELECT * FROM inventory_items WHERE id = ?");
    $getItem->execute([$itemId]);
    $item = $getItem->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Item created successfully',
        'data' => $item
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
