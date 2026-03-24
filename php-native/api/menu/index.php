<?php
/**
 * RestoQwen POS - Menu API (Index/Get All)
 * GET /api/menu/index.php
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';
    
    $sql = "SELECT m.*, c.name as category_name FROM menu_items m
            LEFT JOIN categories c ON m.category_id = c.id
            WHERE 1=1";
    $params = [];
    
    if ($search) {
        $sql .= " AND (m.name LIKE :search OR m.description LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    if ($category) {
        $sql .= " AND (c.name LIKE :cat OR m.category_id = :cat)";
        $params['cat'] = $category;
    }
    
    $sql .= " ORDER BY m.sort_order, m.name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    jsonResponse([
        'success' => true,
        'items' => $items,
        'count' => count($items)
    ]);
    
} catch (PDOException $e) {
    error_log("Menu fetch error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch menu items: ' . $e->getMessage()
    ], 500);
}

/**
 * Send JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
