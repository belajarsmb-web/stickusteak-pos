<?php
/**
 * RestoQwen POS - Menu Store API
 * POST /api/menu/store.php
 */

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    $name = $input['name'] ?? '';
    $description = $input['description'] ?? '';
    $price = $input['price'] ?? 0;
    $category_id = $input['category_id'] ?? 0;
    $display_routing = $input['display_routing'] ?? 'kitchen';
    $is_available = $input['is_available'] ?? 1;

    if (empty($name) || !$price || !$category_id) {
        jsonResponse(['success' => false, 'message' => 'Name, price, and category are required'], 400);
    }

    $image_url = $input['image_url'] ?? '';
    
    $stmt = $pdo->prepare("
        INSERT INTO menu_items (name, description, price, category_id, display_routing, is_available, image_url, code, created_at)
        VALUES (:name, :description, :price, :category_id, :display_routing, :is_available, :image_url, CONCAT('ITEM-', LPAD((SELECT COUNT(*) + 1 FROM menu_items), 3, '0')), NOW())
    ");

    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'category_id' => $category_id,
        'display_routing' => $display_routing,
        'is_available' => $is_available,
        'image_url' => $image_url
    ]);

    jsonResponse([
        'success' => true,
        'message' => 'Menu item added successfully',
        'id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    error_log("Menu store error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to add menu item'
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
