<?php
/**
 * Inventory API - PUT update inventory item
 * Compatible with actual database schema
 */

error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    $itemId = $input['id'] ?? 0;
    if (!$itemId) {
        throw new Exception('Item ID is required');
    }

    // Check item exists
    $checkStmt = $pdo->prepare("SELECT id FROM inventory_items WHERE id = ?");
    $checkStmt->execute([$itemId]);
    if ($checkStmt->rowCount() === 0) {
        throw new Exception('Item not found');
    }

    // Validate
    if (!empty($input['sku'])) {
        $checkSku = $pdo->prepare("SELECT id FROM inventory_items WHERE sku = ? AND id != ?");
        $checkSku->execute([trim($input['sku']), $itemId]);
        if ($checkSku->rowCount() > 0) {
            throw new Exception('SKU already exists');
        }
    }

    // Update item
    $stmt = $pdo->prepare("
        UPDATE inventory_items SET
            name = ?,
            sku = ?,
            unit = ?,
            current_stock = ?,
            min_stock = ?,
            max_stock = ?,
            reorder_point = ?,
            cost_price = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->execute([
        trim($input['name']),
        trim($input['sku']),
        trim($input['unit']),
        floatval($input['current_stock']),
        floatval($input['min_stock'] ?? 0),
        floatval($input['max_stock'] ?? 0),
        floatval($input['reorder_point'] ?? 0),
        floatval($input['cost_price']),
        $itemId
    ]);

    // Get updated item
    $getItem = $pdo->prepare("SELECT * FROM inventory_items WHERE id = ?");
    $getItem->execute([$itemId]);
    $item = $getItem->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Item updated successfully',
        'data' => $item
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
