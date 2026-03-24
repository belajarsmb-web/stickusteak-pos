<?php
/**
 * Inventory API - Simple test version
 * GET /api/inventory/test.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    
    // Simple query to test if table exists and has data
    $stmt = $pdo->query("SELECT id, name, sku, current_stock, min_stock FROM inventory_items LIMIT 10");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Inventory API working!',
        'items' => $items,
        'count' => count($items)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
