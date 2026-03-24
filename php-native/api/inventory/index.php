<?php
/**
 * Inventory API - Simple Version
 * GET /api/inventory/index.php
 */

// Disable HTML errors
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    
    // Simple query - adjust to your actual table structure
    $stmt = $pdo->query("
        SELECT 
            id,
            name,
            sku,
            unit,
            current_stock,
            min_stock,
            max_stock,
            reorder_point,
            cost_price,
            is_active,
            created_at
        FROM inventory_items
        WHERE is_active = 1
        ORDER BY name ASC
        LIMIT 100
    ");
    
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $items,
        'count' => count($items)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ]);
}
