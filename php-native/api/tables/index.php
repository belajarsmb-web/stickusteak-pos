<?php
/**
 * RestoQwen POS - Tables API (Index/Get All)
 * GET /api/tables/index.php
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    
    // Get all tables with current ACTIVE order info (exclude paid/completed)
    $sql = "SELECT t.*,
            (SELECT o.id FROM orders o
             WHERE o.table_id = t.id AND o.status NOT IN ('paid', 'completed', 'cancelled', 'voided')
             ORDER BY o.created_at DESC LIMIT 1) as current_order_id,
            (SELECT COUNT(*) FROM order_items oi
             JOIN orders o ON oi.order_id = o.id
             WHERE o.table_id = t.id AND o.status NOT IN ('paid', 'completed', 'cancelled', 'voided')) as items_count
            FROM tables t
            ORDER BY t.id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format response
    foreach ($tables as &$table) {
        $table['current_order'] = $table['current_order_id'];
        unset($table['current_order_id']);
    }
    
    jsonResponse([
        'success' => true,
        'tables' => $tables,
        'count' => count($tables)
    ]);
    
} catch (PDOException $e) {
    error_log("Tables fetch error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch tables'
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
