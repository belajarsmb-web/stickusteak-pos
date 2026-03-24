<?php
/**
 * Inventory API - POST record stock movement (in/out)
 * Required: item_id, movement_type (in/out), quantity
 * Optional: reference_type, reference_id, notes
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require authentication
requireAuth();

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $errors = [];
    
    if (empty($input['item_id'])) {
        $errors[] = 'Item ID is required';
    }
    
    if (empty($input['movement_type'])) {
        $errors[] = 'Movement type is required (in/out)';
    } elseif (!in_array($input['movement_type'], ['in', 'out'])) {
        $errors[] = 'Movement type must be "in" or "out"';
    }
    
    if (!isset($input['quantity']) || !is_numeric($input['quantity']) || floatval($input['quantity']) <= 0) {
        $errors[] = 'Valid quantity is required';
    }
    
    if (!empty($errors)) {
        jsonResponse(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
    }
    
    $itemId = intval($input['item_id']);
    $movementType = $input['movement_type'];
    $quantity = floatval($input['quantity']);
    
    // Check if item exists
    $item = dbQuery("SELECT * FROM inventory WHERE id = ? AND is_active = 1", [$itemId]);
    $itemData = $item->fetch(PDO::FETCH_ASSOC);
    
    if (!$itemData) {
        jsonResponse(['success' => false, 'message' => 'Inventory item not found'], 404);
    }
    
    // Check if sufficient stock for "out" movement
    if ($movementType === 'out' && $itemData['current_stock'] < $quantity) {
        jsonResponse([
            'success' => false, 
            'message' => 'Insufficient stock',
            'current_stock' => $itemData['current_stock'],
            'requested' => $quantity
        ], 400);
    }
    
    // Start transaction
    $pdo = dbQuery("SELECT 1")->fetch(); // Get PDO connection indirectly
    
    try {
        // Update stock level
        if ($movementType === 'in') {
            dbExecute("UPDATE inventory SET current_stock = current_stock + ?, updated_at = NOW() WHERE id = ?", [$quantity, $itemId]);
        } else {
            dbExecute("UPDATE inventory SET current_stock = current_stock - ?, updated_at = NOW() WHERE id = ?", [$quantity, $itemId]);
        }
        
        // Record movement
        $referenceType = !empty($input['reference_type']) ? $input['reference_type'] : 'manual';
        $referenceId = !empty($input['reference_id']) ? intval($input['reference_id']) : null;
        $notes = !empty($input['notes']) ? trim($input['notes']) : 'Manual stock movement';
        
        dbExecute("INSERT INTO inventory_movements (item_id, movement_type, quantity, reference_type, reference_id, notes, created_at) 
                   VALUES (?, ?, ?, ?, ?, ?, NOW())", 
                   [$itemId, $movementType, $quantity, $referenceType, $referenceId, $notes]);
        
        // Get updated item
        $updatedItem = dbQuery("SELECT * FROM inventory WHERE id = ?", [$itemId])->fetch(PDO::FETCH_ASSOC);
        
        jsonResponse([
            'success' => true,
            'message' => 'Stock movement recorded successfully',
            'data' => [
                'item' => $updatedItem,
                'movement' => [
                    'type' => $movementType,
                    'quantity' => $quantity,
                    'previous_stock' => $itemData['current_stock'],
                    'new_stock' => $updatedItem['current_stock']
                ]
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to record stock movement', 'error' => $e->getMessage()], 500);
}
