<?php
/**
 * Modifiers API - Update/Create Modifier
 * PUT /api/modifiers/items.php
 */

error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }

    $id = isset($input['id']) ? intval($input['id']) : 0;
    $modifier_group_id = isset($input['modifier_group_id']) ? intval($input['modifier_group_id']) : 0;
    $name = $input['name'] ?? '';
    $price = isset($input['price']) ? floatval($input['price']) : 0;
    $is_active = isset($input['is_active']) ? intval($input['is_active']) : 1;

    if (!$modifier_group_id || empty($name)) {
        throw new Exception('Modifier group ID and name are required');
    }

    if ($id > 0) {
        // Update existing modifier
        $stmt = $pdo->prepare("
            UPDATE modifiers 
            SET modifier_group_id = :modifier_group_id,
                name = :name,
                price = :price,
                is_active = :is_active,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([
            'id' => $id,
            'modifier_group_id' => $modifier_group_id,
            'name' => $name,
            'price' => $price,
            'is_active' => $is_active
        ]);
        
        $message = 'Modifier updated successfully';
    } else {
        // Create new modifier
        $stmt = $pdo->prepare("
            INSERT INTO modifiers (modifier_group_id, name, price, is_active, created_at, updated_at)
            VALUES (:modifier_group_id, :name, :price, :is_active, NOW(), NOW())
        ");
        
        $stmt->execute([
            'modifier_group_id' => $modifier_group_id,
            'name' => $name,
            'price' => $price,
            'is_active' => $is_active
        ]);
        
        $id = $pdo->lastInsertId();
        $message = 'Modifier created successfully';
    }

    jsonResponse([
        'success' => true,
        'message' => $message,
        'id' => $id
    ]);

} catch (Exception $e) {
    error_log("Modifier save error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to save modifier: ' . $e->getMessage()
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
