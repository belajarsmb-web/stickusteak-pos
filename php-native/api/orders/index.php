<?php
/**
 * RestoQwen POS - Orders API (Index/Get All)
 * GET /api/orders/index.php
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    $status = $_GET['status'] ?? '';
    $date = $_GET['date'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = $_GET['limit'] ?? 50;
    
    $sql = "SELECT o.*, 
            o.customer_name as customer_name, 
            o.customer_phone as customer_phone,
            tbl.name as table_name,
            tk.ticket_number as ticket_number
            FROM orders o
            LEFT JOIN tables tbl ON o.table_id = tbl.id
            LEFT JOIN tickets tk ON o.ticket_id = tk.id
            WHERE 1=1";
    $params = [];

    if ($status) {
        $sql .= " AND o.status = :status";
        $params['status'] = $status;
    }

    if ($date) {
        $sql .= " AND DATE(o.created_at) = :date";
        $params['date'] = $date;
    }

    if ($search) {
        $sql .= " AND (o.id LIKE :search OR o.customer_name LIKE :search)";
        $params['search'] = "%$search%";
    }

    $sql .= " ORDER BY o.created_at DESC";
    
    // Add limit safely
    $sql .= " LIMIT " . intval($limit);

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get item counts, void info, and items for each order
    foreach ($orders as &$order) {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(quantity), 0) as items,
                   COALESCE(SUM(CASE WHEN is_voided = 1 THEN quantity ELSE 0 END), 0) as voided_items
            FROM order_items
            WHERE order_id = :order_id
        ");
        $stmt->execute(['order_id' => $order['id']]);
        $itemData = $stmt->fetch(PDO::FETCH_ASSOC);
        $order['items_count'] = (int)($itemData['items'] ?? 0);
        $order['voided_count'] = (int)($itemData['voided_items'] ?? 0);
        
        // Get items for this order
        $itemsStmt = $pdo->prepare("
            SELECT oi.*, m.name as item_name
            FROM order_items oi
            LEFT JOIN menu_items m ON oi.menu_item_id = m.id
            WHERE oi.order_id = :order_id
            ORDER BY oi.id ASC
        ");
        $itemsStmt->execute(['order_id' => $order['id']]);
        $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    jsonResponse([
        'success' => true,
        'orders' => $orders,
        'count' => count($orders)
    ]);
    
} catch (PDOException $e) {
    error_log("Orders fetch error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch orders'
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
