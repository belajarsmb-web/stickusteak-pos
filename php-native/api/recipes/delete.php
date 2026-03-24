<?php
/**
 * Recipe API - Delete Recipe
 */

error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = getDbConnection();
    $menuItemId = $_GET['id'] ?? 0;
    
    if (!$menuItemId) {
        throw new Exception('Recipe ID is required');
    }

    // Check recipe exists
    $checkRecipe = $pdo->prepare("SELECT id FROM recipe_ingredients WHERE menu_item_id = ?");
    $checkRecipe->execute([$menuItemId]);
    if ($checkRecipe->rowCount() === 0) {
        throw new Exception('Recipe not found');
    }

    // Delete recipe
    $stmt = $pdo->prepare("DELETE FROM recipe_ingredients WHERE menu_item_id = ?");
    $stmt->execute([$menuItemId]);

    echo json_encode([
        'success' => true,
        'message' => 'Recipe deleted successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
