<?php
/**
 * Low Stock Alerts API
 * GET /api/inventory/low-stock-alerts.php
 * Returns items that are below min_stock level
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/auto-stock-deduction.php';

try {
    $pdo = getDbConnection();
    
    // Get low stock items
    $lowStockItems = getLowStockItems($pdo);
    
    // Get counts
    $outOfStock = array_filter($lowStockItems, function($item) {
        return $item['status'] === 'out_of_stock';
    });
    
    $lowStock = array_filter($lowStockItems, function($item) {
        return $item['status'] === 'low_stock';
    });
    
    jsonResponse([
        'success' => true,
        'alerts' => $lowStockItems,
        'summary' => [
            'total_alerts' => count($lowStockItems),
            'out_of_stock' => count($outOfStock),
            'low_stock' => count($lowStock)
        ]
    ]);

} catch (PDOException $e) {
    error_log("Low stock alerts error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch low stock alerts',
        'error' => $e->getMessage()
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
