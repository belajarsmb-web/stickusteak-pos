<?php
/**
 * RestoQwen POS - Menu Update API
 * POST /api/menu/update.php
 */

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = $input['id'] ?? 0;
    $name = $input['name'] ?? '';
    $description = $input['description'] ?? '';
    $price = $input['price'] ?? 0;
    $category_id = $input['category_id'] ?? 0;
    $display_routing = $input['display_routing'] ?? 'kitchen';
    $is_available = $input['is_available'] ?? 1;
    $image_url = $input['image_url'] ?? '';
    
    if (!$id || empty($name) || !$price || !$category_id) {
        jsonResponse(['success' => false, 'message' => 'ID, name, price, and category are required'], 400);
    }
    
    $stmt = $pdo->prepare("
        UPDATE menu_items 
        SET name = :name, 
            description = :description, 
            price = :price, 
            category_id = :category_id,
            display_routing = :display_routing,
            is_available = :is_available,
            image_url = :image_url,
            updated_at = NOW()
        WHERE id = :id
    ");
    
    $stmt->execute([
        'id' => $id,
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
        'message' => 'Menu item updated successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Menu update error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to update menu item'
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
