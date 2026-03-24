<?php
/**
 * Auto Stock Deduction API
 * Called when order is submitted to deduct ingredients based on recipes
 * 
 * Usage: Include this in order submission flow
 * Input: order_id (from newly created order)
 */

require_once __DIR__ . '/../../config/database.php';

/**
 * Deduct stock for an order based on recipe ingredients
 * @param int $orderId Order ID
 * @param PDO $pdo Database connection
 * @return array Result with success status and details
 */
function deductStockForOrder($orderId, $pdo) {
    try {
        // Get order items with menu item IDs and quantities
        $stmt = $pdo->prepare("
            SELECT menu_item_id, quantity 
            FROM order_items 
            WHERE order_id = ? AND is_voided = 0
        ");
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($orderItems)) {
            return ['success' => true, 'message' => 'No items to deduct', 'deducted' => 0];
        }

        $deductedCount = 0;
        $lowStockAlerts = [];

        // Process each order item
        foreach ($orderItems as $orderItem) {
            $menuItemId = $orderItem['menu_item_id'];
            $orderQuantity = intval($orderItem['quantity']);

            // Get recipe ingredients for this menu item
            $recipeStmt = $pdo->prepare("
                SELECT ri.inventory_item_id, ri.quantity as recipe_qty, ri.unit,
                       ii.name as ingredient_name, ii.current_stock, ii.min_stock, ii.sku
                FROM recipe_ingredients ri
                JOIN inventory_items ii ON ri.inventory_item_id = ii.id
                WHERE ri.menu_item_id = ? AND ii.is_active = 1
            ");
            $recipeStmt->execute([$menuItemId]);
            $ingredients = $recipeStmt->fetchAll(PDO::FETCH_ASSOC);

            // If no recipe found, skip this item
            if (empty($ingredients)) {
                continue;
            }

            // Deduct each ingredient with unit conversion
            foreach ($ingredients as $ingredient) {
                $inventoryItemId = $ingredient['inventory_item_id'];
                $recipeQty = floatval($ingredient['recipe_qty']);
                $recipeUnit = $ingredient['unit'] ?? 'pcs';
                
                // Get inventory item details for conversion
                try {
                    $invItemStmt = $pdo->prepare("
                        SELECT unit as inventory_unit, conversion_rate, current_stock, min_stock
                        FROM inventory_items WHERE id = ?
                    ");
                    $invItemStmt->execute([$inventoryItemId]);
                    $invItem = $invItemStmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Convert recipe quantity to inventory unit
                    $totalNeeded = convertUnit(
                        $recipeQty * $orderQuantity,
                        $recipeUnit,
                        $invItem['inventory_unit'] ?? 'pcs',
                        floatval($invItem['conversion_rate'] ?? 1)
                    );
                } catch (Exception $e) {
                    // Fallback: no conversion, use recipe unit directly
                    error_log("Conversion error, using fallback: " . $e->getMessage());
                    $totalNeeded = $recipeQty * $orderQuantity;
                }
                
                // Check if sufficient stock
                if (floatval($ingredient['current_stock']) < $totalNeeded) {
                    // Insufficient stock - log warning but continue
                    $lowStockAlerts[] = [
                        'ingredient' => $ingredient['ingredient_name'],
                        'sku' => $ingredient['sku'],
                        'needed' => $totalNeeded,
                        'available' => floatval($ingredient['current_stock']),
                        'shortage' => $totalNeeded - floatval($ingredient['current_stock'])
                    ];
                }

                // Deduct stock
                $deductStmt = $pdo->prepare("
                    UPDATE inventory_items 
                    SET current_stock = current_stock - ?, 
                        updated_at = NOW() 
                    WHERE id = ?
                ");
                $deductStmt->execute([$totalNeeded, $inventoryItemId]);

                // Create movement record
                $movementStmt = $pdo->prepare("
                    INSERT INTO inventory_movements 
                    (item_id, movement_type, quantity, reference_type, reference_id, notes, created_at)
                    VALUES (?, 'out', ?, 'order', ?, ?, NOW())
                ");
                $movementStmt->execute([
                    $inventoryItemId,
                    $totalNeeded,
                    $orderId,
                    'Auto-deduct for Order #' . $orderId . ' - ' . $ingredient['ingredient_name']
                ]);

                $deductedCount++;

                // Check for low stock alert
                $newStock = floatval($ingredient['current_stock']) - $totalNeeded;
                if ($newStock <= floatval($ingredient['min_stock'])) {
                    $lowStockAlerts[] = [
                        'type' => 'low_stock',
                        'ingredient' => $ingredient['ingredient_name'],
                        'sku' => $ingredient['sku'],
                        'new_stock' => $newStock,
                        'min_stock' => floatval($ingredient['min_stock'])
                    ];
                }
            }
        }

        $result = [
            'success' => true,
            'message' => 'Stock deducted successfully',
            'deducted' => $deductedCount,
            'order_id' => $orderId
        ];

        if (!empty($lowStockAlerts)) {
            $result['alerts'] = $lowStockAlerts;
            $result['has_alerts'] = true;
        }

        return $result;

    } catch (PDOException $e) {
        error_log("Stock deduction error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to deduct stock: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Check and return low stock items
 * @param PDO $pdo Database connection
 * @return array Low stock items
 */
function getLowStockItems($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT id, name, sku, category, current_stock, min_stock,
                   CASE
                       WHEN current_stock <= 0 THEN 'out_of_stock'
                       WHEN current_stock <= min_stock THEN 'low_stock'
                       ELSE 'in_stock'
                   END as status
            FROM inventory_items
            WHERE is_active = 1 AND current_stock <= min_stock
            ORDER BY 
                CASE 
                    WHEN current_stock <= 0 THEN 1
                    WHEN current_stock <= min_stock THEN 2
                    ELSE 3
                END,
                (current_stock / min_stock) ASC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Low stock check error: " . $e->getMessage());
        return [];
    }
}

/**
 * Convert units if needed
 * @param float $quantity Quantity in recipe unit
 * @param string $recipeUnit Unit used in recipe
 * @param string $inventoryUnit Unit used in inventory
 * @param float $conversionRate Conversion rate from inventory table
 * @return float Quantity in inventory unit
 */
function convertUnit($quantity, $recipeUnit, $inventoryUnit, $conversionRate = 1) {
    // If same unit, no conversion needed
    if ($recipeUnit === $inventoryUnit) {
        return $quantity;
    }
    
    // Standard conversion factors
    $conversions = [
        'kg_to_gr' => 1000,
        'gr_to_kg' => 0.001,
        'l_to_ml' => 1000,
        'ml_to_l' => 0.001,
    ];
    
    $conversionKey = strtolower($recipeUnit) . '_to_' . strtolower($inventoryUnit);
    
    // Check if we have a direct conversion
    if (isset($conversions[$conversionKey])) {
        return $quantity * $conversions[$conversionKey];
    }
    
    // Use custom conversion rate from inventory table
    if ($inventoryUnit === $recipeUnit) {
        return $quantity;
    }
    
    // Try to convert via base unit
    return $quantity * $conversionRate;
}

/**
 * Return stock for voided item
 * @param array $item Order item data (with menu_item_id, quantity)
 * @param PDO $pdo Database connection
 * @return array Result with success status and details
 */
function returnStockForVoidedItem($item, $pdo) {
    try {
        $menuItemId = $item['menu_item_id'];
        $voidedQuantity = intval($item['quantity']);
        $orderId = $item['order_id'];
        
        // Get recipe ingredients for this menu item
        $recipeStmt = $pdo->prepare("
            SELECT ri.inventory_item_id, ri.quantity as recipe_qty, ri.unit,
                   ii.name as ingredient_name, ii.current_stock, ii.sku
            FROM recipe_ingredients ri
            JOIN inventory_items ii ON ri.inventory_item_id = ii.id
            WHERE ri.menu_item_id = ? AND ii.is_active = 1
        ");
        $recipeStmt->execute([$menuItemId]);
        $ingredients = $recipeStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If no recipe found, skip
        if (empty($ingredients)) {
            return ['success' => true, 'message' => 'No recipe found - no stock to return', 'returned' => 0];
        }
        
        $returnedCount = 0;

        // Return each ingredient with unit conversion
        foreach ($ingredients as $ingredient) {
            $inventoryItemId = $ingredient['inventory_item_id'];
            $recipeQty = floatval($ingredient['recipe_qty']);
            $recipeUnit = $ingredient['unit'] ?? 'pcs';
            
            // Get inventory item details for conversion
            try {
                $invItemStmt = $pdo->prepare("
                    SELECT unit as inventory_unit, conversion_rate
                    FROM inventory_items WHERE id = ?
                ");
                $invItemStmt->execute([$inventoryItemId]);
                $invItem = $invItemStmt->fetch(PDO::FETCH_ASSOC);
                
                // Convert recipe quantity to inventory unit
                $totalToReturn = convertUnit(
                    $recipeQty * $voidedQuantity,
                    $recipeUnit,
                    $invItem['inventory_unit'] ?? 'pcs',
                    floatval($invItem['conversion_rate'] ?? 1)
                );
            } catch (Exception $e) {
                // Fallback: no conversion, use recipe unit directly
                error_log("Conversion error in void return, using fallback: " . $e->getMessage());
                $totalToReturn = $recipeQty * $voidedQuantity;
            }
            
            // Add back stock
            $returnStmt = $pdo->prepare("
                UPDATE inventory_items 
                SET current_stock = current_stock + ?, 
                    updated_at = NOW() 
                WHERE id = ?
            ");
            $returnStmt->execute([$totalToReturn, $inventoryItemId]);
            
            // Create movement record (IN)
            $movementStmt = $pdo->prepare("
                INSERT INTO inventory_movements 
                (item_id, movement_type, quantity, reference_type, reference_id, notes, created_at)
                VALUES (?, 'in', ?, 'void', ?, ?, NOW())
            ");
            $movementStmt->execute([
                $inventoryItemId,
                $totalToReturn,
                $orderId,
                'Stock return for voided Order #' . $orderId . ' - ' . $ingredient['ingredient_name']
            ]);
            
            $returnedCount++;
        }
        
        return [
            'success' => true,
            'message' => 'Stock returned successfully',
            'returned' => $returnedCount,
            'order_id' => $orderId
        ];
        
    } catch (PDOException $e) {
        error_log("Stock return error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to return stock: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ];
    }
}

// If called directly (for testing)
if (basename($_SERVER['PHP_SELF']) == 'auto-stock-deduction.php') {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $orderId = $input['order_id'] ?? 0;
        
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Order ID required']);
            exit;
        }
        
        $pdo = getDbConnection();
        $result = deductStockForOrder($orderId, $pdo);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
}
