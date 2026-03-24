<?php
/**
 * RestoQwen POS - Modifier Groups API
 * GET /api/modifiers/groups.php - Get all modifier groups with modifiers
 * POST /api/modifiers/groups.php - Create modifier group
 * PUT /api/modifiers/groups.php - Update modifier group
 * DELETE /api/modifiers/groups.php?id= - Delete modifier group
 */

require_once __DIR__ . '/../../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get all modifier groups with their modifiers
    try {
        $pdo = getDbConnection();
        $categoryId = $_GET['category_id'] ?? null;
        
        $sql = "SELECT mg.*, 
            GROUP_CONCAT(m.id) as modifier_ids,
            GROUP_CONCAT(m.name) as modifier_names,
            GROUP_CONCAT(m.price) as modifier_prices,
            GROUP_CONCAT(m.is_active) as modifier_active
            FROM modifier_groups mg
            LEFT JOIN modifiers m ON m.modifier_group_id = mg.id
            WHERE 1=1";
        
        if ($categoryId) {
            $sql .= " AND mg.category_id = " . intval($categoryId);
        }
        
        $sql .= " GROUP BY mg.id ORDER BY mg.name";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format modifiers as array
        foreach ($groups as &$group) {
            $group['modifiers'] = [];
            if ($group['modifier_ids']) {
                $ids = explode(',', $group['modifier_ids']);
                $names = explode(',', $group['modifier_names']);
                $prices = explode(',', $group['modifier_prices']);
                $active = explode(',', $group['modifier_active']);
                
                foreach ($ids as $key => $id) {
                    $group['modifiers'][] = [
                        'id' => $id,
                        'name' => $names[$key],
                        'price' => $prices[$key],
                        'is_active' => $active[$key]
                    ];
                }
            }
            unset($group['modifier_ids']);
            unset($group['modifier_names']);
            unset($group['modifier_prices']);
            unset($group['modifier_active']);
            
            // Convert to selection_type for frontend
            if ($group['is_required']) {
                $group['selection_type'] = $group['max_selection'] > 1 ? 'required_multiple' : 'required_single';
            } else {
                $group['selection_type'] = $group['max_selection'] > 1 ? 'multiple' : 'single';
            }
        }
        
        jsonResponse([
            'success' => true,
            'groups' => $groups,
            'count' => count($groups)
        ]);
    } catch (PDOException $e) {
        error_log("Modifier groups fetch error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to fetch modifier groups: ' . $e->getMessage()], 500);
    }
} elseif ($method === 'POST') {
    // Create modifier group
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);
        
        $name = $input['name'] ?? '';
        $description = $input['description'] ?? '';
        $selection_type = $input['selection_type'] ?? 'single';
        $min_selections = $input['min_selections'] ?? 0;
        $max_selections = $input['max_selections'] ?? 1;
        $category_id = $input['category_id'] ?? null;
        $is_active = $input['is_active'] ?? 1;
        
        if (empty($name)) {
            jsonResponse(['success' => false, 'message' => 'Group name is required'], 400);
        }
        
        // Determine is_required from selection_type
        $is_required = ($selection_type === 'required_single' || $selection_type === 'required_multiple') ? 1 : 0;
        
        $pdo->beginTransaction();
        
        // Insert group
        $stmt = $pdo->prepare("
            INSERT INTO modifier_groups (name, min_selection, max_selection, is_required, is_active, category_id, created_at)
            VALUES (:name, :min_selection, :max_selection, :is_required, :is_active, :category_id, NOW())
        ");
        
        $stmt->execute([
            'name' => $name,
            'min_selection' => $min_selections,
            'max_selection' => $max_selections,
            'is_required' => $is_required,
            'is_active' => $is_active,
            'category_id' => $category_id ?: null
        ]);
        
        $groupId = $pdo->lastInsertId();
        
        $pdo->commit();
        
        jsonResponse([
            'success' => true,
            'message' => 'Modifier group created successfully',
            'id' => $groupId
        ]);
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Modifier group create error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to create modifier group: ' . $e->getMessage()], 500);
    }
} elseif ($method === 'PUT') {
    // Update modifier group
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $input['id'] ?? 0;
        $name = $input['name'] ?? '';
        $description = $input['description'] ?? '';
        $selection_type = $input['selection_type'] ?? 'single';
        $min_selections = $input['min_selections'] ?? 0;
        $max_selections = $input['max_selections'] ?? 1;
        $category_id = $input['category_id'] ?? null;
        $is_active = $input['is_active'] ?? 1;
        
        if (!$id || empty($name)) {
            jsonResponse(['success' => false, 'message' => 'Invalid data'], 400);
        }
        
        // Determine is_required from selection_type
        $is_required = ($selection_type === 'required_single' || $selection_type === 'required_multiple') ? 1 : 0;
        
        $stmt = $pdo->prepare("
            UPDATE modifier_groups 
            SET name = :name, 
                min_selection = :min_selection, 
                max_selection = :max_selection, 
                is_required = :is_required, 
                is_active = :is_active,
                category_id = :category_id,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'min_selection' => $min_selections,
            'max_selection' => $max_selections,
            'is_required' => $is_required,
            'is_active' => $is_active,
            'category_id' => $category_id ?: null
        ]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Modifier group updated successfully'
        ]);
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Modifier group update error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to update modifier group: ' . $e->getMessage()], 500);
    }
} elseif ($method === 'DELETE') {
    // Delete modifier group
    try {
        $pdo = getDbConnection();
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            jsonResponse(['success' => false, 'message' => 'Group ID required'], 400);
        }
        
        $stmt = $pdo->prepare("DELETE FROM modifier_groups WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Modifier group deleted successfully'
        ]);
    } catch (PDOException $e) {
        error_log("Modifier group delete error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to delete modifier group'], 500);
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
