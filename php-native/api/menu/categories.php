<?php
/**
 * RestoQwen POS - Categories API
 * GET /api/menu/categories.php - Get all categories
 * POST /api/menu/categories.php - Create category
 * PUT /api/menu/categories.php - Update category
 * DELETE /api/menu/categories.php?id= - Delete category
 */

require_once __DIR__ . '/../../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get all categories
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY sort_order, name");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        jsonResponse([
            'success' => true,
            'categories' => $categories,
            'count' => count($categories)
        ]);
    } catch (PDOException $e) {
        error_log("Categories fetch error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to fetch categories'], 500);
    }
} elseif ($method === 'POST') {
    // Create category
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);
        
        $name = $input['name'] ?? '';
        $description = $input['description'] ?? '';
        $color = $input['color'] ?? '#007bff';
        $display_routing = $input['display_routing'] ?? 'kitchen';
        $is_active = $input['is_active'] ?? 1;
        
        if (empty($name)) {
            jsonResponse(['success' => false, 'message' => 'Category name is required'], 400);
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO categories (name, description, color, display_routing, is_active, sort_order, created_at)
            VALUES (:name, :description, :color, :display_routing, :is_active, 0, NOW())
        ");
        
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'color' => $color,
            'display_routing' => $display_routing,
            'is_active' => $is_active
        ]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Category created successfully',
            'id' => $pdo->lastInsertId()
        ]);
    } catch (PDOException $e) {
        error_log("Category create error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to create category'], 500);
    }
} elseif ($method === 'PUT') {
    // Update category
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $input['id'] ?? 0;
        $name = $input['name'] ?? '';
        $description = $input['description'] ?? '';
        $color = $input['color'] ?? '#007bff';
        $display_routing = $input['display_routing'] ?? 'kitchen';
        $is_active = $input['is_active'] ?? 1;
        
        if (!$id || empty($name)) {
            jsonResponse(['success' => false, 'message' => 'Invalid data'], 400);
        }
        
        $stmt = $pdo->prepare("
            UPDATE categories 
            SET name = :name, 
                description = :description, 
                color = :color, 
                display_routing = :display_routing, 
                is_active = :is_active,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'color' => $color,
            'display_routing' => $display_routing,
            'is_active' => $is_active
        ]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    } catch (PDOException $e) {
        error_log("Category update error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to update category'], 500);
    }
} elseif ($method === 'DELETE') {
    // Delete category
    try {
        $pdo = getDbConnection();
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            jsonResponse(['success' => false, 'message' => 'Category ID required'], 400);
        }
        
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    } catch (PDOException $e) {
        error_log("Category delete error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to delete category'], 500);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
