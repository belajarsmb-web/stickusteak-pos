<?php
/**
 * Payments API - GET all payments with order join
 * Supports filtering and pagination
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require authentication
requireAuth();

// Get query parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 20;
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$startDate = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$endDate = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

// Calculate offset
$offset = ($page - 1) * $limit;

try {
    // Build base query
    $where = ['p.is_active = ?'];
    $params = [1];
    
    // Add status filter
    if ($status !== '') {
        $where[] = 'p.status = ?';
        $params[] = $status;
    }
    
    // Add order_id filter
    if ($orderId > 0) {
        $where[] = 'p.order_id = ?';
        $params[] = $orderId;
    }
    
    // Add date range filter
    if ($startDate !== '') {
        $where[] = 'p.created_at >= ?';
        $params[] = $startDate . ' 00:00:00';
    }
    if ($endDate !== '') {
        $where[] = 'p.created_at <= ?';
        $params[] = $endDate . ' 23:59:59';
    }
    
    $whereClause = implode(' AND ', $where);
    
    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total FROM payments p WHERE {$whereClause}";
    $countStmt = dbQuery($countSql, $params);
    $total = $countStmt->fetchColumn();
    
    // Get payments with order join
    $sql = "SELECT 
                p.id,
                p.order_id,
                p.payment_method_id,
                p.amount,
                p.transaction_id,
                p.status,
                p.notes,
                p.is_active,
                p.created_at,
                p.updated_at,
                pm.name as payment_method_name,
                o.order_number,
                o.total_amount as order_total,
                o.status as order_status,
                c.name as customer_name,
                c.phone as customer_phone,
                t.table_number,
                t.name as table_name
            FROM payments p
            INNER JOIN orders o ON p.order_id = o.id
            LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
            LEFT JOIN customers c ON o.customer_id = c.id
            LEFT JOIN tables t ON o.table_id = t.id
            WHERE {$whereClause}
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $payments = dbQuery($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate pagination info
    $totalPages = ceil($total / $limit);
    
    jsonResponse([
        'success' => true,
        'data' => $payments,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ]
    ]);
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to fetch payments', 'error' => $e->getMessage()], 500);
}
