<?php
/**
 * Recipe Management API - Get All Recipes
 * Compatible version
 */

error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    
    // Check if menu_items table exists and has data
    $menuCheck = $pdo->query("SELECT id, name FROM menu_items WHERE is_active = 1 ORDER BY name");
    $menuItems = $menuCheck->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($menuItems)) {
        echo json_encode([
            'success' => true,
            'recipes' => [],
            'count' => 0,
            'message' => 'No menu items available'
        ]);
        exit;
    }
    
    // Get all recipes grouped by menu item
    $stmt = $pdo->query("
        SELECT 
            ri.menu_item_id,
            m.name as menu_item_name,
            ri.inventory_item_id,
            i.name as ingredient_name,
            ri.quantity,
            ri.unit,
            i.cost_price,
            (ri.quantity * i.cost_price) as ingredient_cost
        FROM recipe_ingredients ri
        JOIN menu_items m ON ri.menu_item_id = m.id
        LEFT JOIN inventory_items i ON ri.inventory_item_id = i.id
        ORDER BY m.name, ri.id
    ");
    
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group by menu item
    $recipes = [];
    foreach ($ingredients as $ing) {
        $menuItemId = $ing['menu_item_id'];
        
        if (!isset($recipes[$menuItemId])) {
            $recipes[$menuItemId] = [
                'menu_item_id' => $menuItemId,
                'menu_item_name' => $ing['menu_item_name'],
                'ingredients' => [],
                'ingredients_count' => 0,
                'total_cost' => 0
            ];
        }
        
        $recipes[$menuItemId]['ingredients'][] = [
            'inventory_item_id' => $ing['inventory_item_id'],
            'ingredient_name' => $ing['ingredient_name'],
            'quantity' => floatval($ing['quantity']),
            'unit' => $ing['unit'],
            'cost_price' => floatval($ing['cost_price']),
            'ingredient_cost' => floatval($ing['ingredient_cost'])
        ];
        
        $recipes[$menuItemId]['total_cost'] += floatval($ing['ingredient_cost']);
    }
    
    // Convert to array and count ingredients
    $recipesArray = array_values($recipes);
    foreach ($recipesArray as &$recipe) {
        $recipe['ingredients_count'] = count($recipe['ingredients']);
    }
    
    echo json_encode([
        'success' => true,
        'recipes' => $recipesArray,
        'count' => count($recipesArray)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ]);
}
