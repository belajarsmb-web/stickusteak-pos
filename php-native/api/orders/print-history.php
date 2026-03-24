<?php
/**
 * RestoQwen POS - Get Print History API
 * GET /api/orders/print-history.php
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    
    // Get print history from order_items and order_print_log
    $stmt = $pdo->prepare("
        SELECT 
            oi.id as log_id,
            oi.order_id,
            oi.item_name,
            oi.print_count,
            oi.printed_at,
            oi.printed_by,
            u.username,
            'initial' as log_type,
            NULL as print_reason_text
        FROM order_items oi
        LEFT JOIN users u ON oi.printed_by = u.id
        WHERE oi.print_count = 1
        
        UNION ALL
        
        SELECT 
            opl.id as log_id,
            oi.order_id,
            oi.item_name,
            oi.print_count,
            opl.printed_at,
            opl.printed_by,
            u.username,
            'reprint' as log_type,
            opl.print_reason_text
        FROM order_print_log opl
        JOIN order_items oi ON opl.order_item_id = oi.id
        LEFT JOIN users u ON opl.printed_by = u.id
        
        ORDER BY printed_at DESC
        LIMIT 100
    ");
    $stmt->execute();
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    jsonResponse([
        'success' => true,
        'history' => $history,
        'count' => count($history)
    ]);
    
} catch (PDOException $e) {
    error_log("Print history fetch error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to fetch print history'], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
