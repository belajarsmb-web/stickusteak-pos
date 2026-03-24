<?php
/**
 * Mobile Order API - Get Menu
 * GET /api/mobile/menu.php
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    
    // Get categories
    $stmt = $pdo->query("
        SELECT id, name, description, color, display_routing 
        FROM categories 
        WHERE is_active = 1 
        ORDER BY sort_order, name
    ");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get menu items with category
    $stmt = $pdo->query("
        SELECT m.*, c.name as category_name, c.display_routing
        FROM menu_items m
        LEFT JOIN categories c ON m.category_id = c.id
        WHERE m.is_available = 1 AND (c.is_active = 1 OR c.is_active IS NULL)
        ORDER BY c.sort_order, m.sort_order, m.name
    ");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group items by category
    $menuByCategory = [];
    foreach ($categories as $category) {
        $menuByCategory[$category['id']] = [
            'category' => $category,
            'items' => []
        ];
    }
    
    foreach ($items as $item) {
        $categoryId = $item['category_id'] ?? 0;
        if (!isset($menuByCategory[$categoryId])) {
            $menuByCategory[$categoryId] = [
                'category' => [
                    'id' => $categoryId,
                    'name' => $item['category_name'] ?? 'Other',
                    'description' => '',
                    'color' => '#667eea'
                ],
                'items' => []
            ];
        }
        $menuByCategory[$categoryId]['items'][] = $item;
    }

    // Get modifier groups
    $stmt = $pdo->query("
        SELECT mg.*,
        (SELECT JSON_ARRAYAGG(JSON_OBJECT(
            'id', m.id,
            'name', m.name,
            'price', m.price,
            'is_active', m.is_active
        )) FROM modifiers m WHERE m.modifier_group_id = mg.id AND m.is_active = 1) as modifiers
        FROM modifier_groups mg
        WHERE mg.is_active = 1
        ORDER BY mg.name
    ");
    $modifierGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get categories that require notes (from settings or default)
    $stmt = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'categories_require_notes'");
    $notesSetting = $stmt->fetchColumn();
    $categoriesRequireNotes = $notesSetting ? json_decode($notesSetting, true) : ['Steak', 'Premium Steaks', 'Grill'];
    
    // If no setting exists, use default
    if (empty($categoriesRequireNotes)) {
        $categoriesRequireNotes = ['Steak', 'Premium Steaks', 'Grill'];
    }

    // Get menu item modifiers relationship
    $stmt = $pdo->query("
        SELECT mim.menu_item_id, 
        GROUP_CONCAT(mim.modifier_group_id) as modifier_group_ids
        FROM menu_item_modifiers mim
        GROUP BY mim.menu_item_id
    ");
    $menuItemModifiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Create lookup map
    $modifierGroupMap = [];
    foreach ($modifierGroups as $mg) {
        $modifierGroupMap[$mg['id']] = $mg;
    }
    
    $menuModifierMap = [];
    foreach ($menuItemModifiers as $mm) {
        $groupIds = explode(',', $mm['modifier_group_ids']);
        $menuModifierMap[$mm['menu_item_id']] = array_map(function($id) use ($modifierGroupMap) {
            return $modifierGroupMap[$id] ?? null;
        }, $groupIds);
        $menuModifierMap[$mm['menu_item_id']] = array_filter($menuModifierMap[$mm['menu_item_id']]);
    }

    // Add modifier groups to each item
    foreach ($menuByCategory as $categoryId => $categoryData) {
        foreach ($categoryData['items'] as &$item) {
            $itemId = $item['id'];
            $item['modifierGroups'] = $menuModifierMap[$itemId] ?? [];
        }
    }
    
    jsonResponse([
        'success' => true,
        'menu' => array_values($menuByCategory),
        'modifierGroups' => $modifierGroups,
        'categories' => $categories,
        'categoriesRequireNotes' => $categoriesRequireNotes
    ]);

} catch (PDOException $e) {
    error_log("Mobile menu error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to load menu'], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
