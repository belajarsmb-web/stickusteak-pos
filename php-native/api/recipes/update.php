<?php
/**
 * Recipe API - Update Recipe
 */

error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    $menuItemId = $input['menu_item_id'] ?? 0;
    $ingredients = $input['ingredients'] ?? [];

    if (!$menuItemId) {
        throw new Exception('Menu item ID is required');
    }

    if (empty($ingredients)) {
        throw new Exception('At least one ingredient is required');
    }

    // Check if recipe exists
    $checkRecipe = $pdo->prepare("SELECT id FROM recipe_ingredients WHERE menu_item_id = ?");
    $checkRecipe->execute([$menuItemId]);
    if ($checkRecipe->rowCount() === 0) {
        throw new Exception('Recipe not found. Create it first.');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Delete existing ingredients
    $deleteStmt = $pdo->prepare("DELETE FROM recipe_ingredients WHERE menu_item_id = ?");
    $deleteStmt->execute([$menuItemId]);

    // Insert new ingredients
    $stmt = $pdo->prepare("
        INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");

    foreach ($ingredients as $ing) {
        if (empty($ing['inventory_item_id']) || !isset($ing['quantity'])) {
            continue;
        }
        
        $stmt->execute([
            $menuItemId,
            intval($ing['inventory_item_id']),
            floatval($ing['quantity']),
            $ing['unit'] ?? 'pcs'
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Recipe updated successfully'
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
