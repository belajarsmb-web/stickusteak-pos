<?php
/**
 * Inventory API - DELETE inventory item
 */

error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = getDbConnection();
    $itemId = $_GET['id'] ?? 0;
    
    if (!$itemId) {
        throw new Exception('Item ID is required');
    }

    // Check item exists
    $checkStmt = $pdo->prepare("SELECT id FROM inventory_items WHERE id = ?");
    $checkStmt->execute([$itemId]);
    if ($checkStmt->rowCount() === 0) {
        throw new Exception('Item not found');
    }

    // Soft delete - set is_active = 0
    $stmt = $pdo->prepare("UPDATE inventory_items SET is_active = 0, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$itemId]);

    echo json_encode([
        'success' => true,
        'message' => 'Item deleted successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
