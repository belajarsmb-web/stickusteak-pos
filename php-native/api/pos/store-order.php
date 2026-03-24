<?php
/**
 * RestoQwen POS - Store Order API
 * POST /api/pos/store-order.php
 */

// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../../config/database.php';

// Only include auto-stock-deduction if file exists
$autoStockFile = __DIR__ . '/../inventory/auto-stock-deduction.php';
if (file_exists($autoStockFile)) {
    require_once $autoStockFile;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        jsonResponse(['success' => false, 'message' => 'Invalid JSON input'], 400);
    }

    $table_id = $input['table_id'] ?? 0;
    $table_name = $input['table_name'] ?? '';
    $items = $input['items'] ?? [];
    $customer_name = $input['customer_name'] ?? '';
    $customer_phone = $input['customer_phone'] ?? '';

    if (empty($items)) {
        jsonResponse(['success' => false, 'message' => 'No items in order'], 400);
    }

    if (!$table_id) {
        jsonResponse(['success' => false, 'message' => 'Table ID required'], 400);
    }

    // Calculate totals
    $subTotal = 0;
    foreach ($items as $item) {
        $subTotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
    }
    
    // Get tax and service settings
    $taxPercent = 11; // Default 11%
    $servicePercent = 5; // Default 5%
    $taxEnabled = true;
    $serviceEnabled = true;
    
    try {
        $settingsStmt = $pdo->prepare("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ('tax_percentage', 'service_charge_percentage', 'tax_enabled', 'service_enabled')");
        $settingsStmt->execute();
        while ($row = $settingsStmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['setting_key'] === 'tax_percentage') $taxPercent = floatval($row['setting_value']);
            if ($row['setting_key'] === 'service_charge_percentage') $servicePercent = floatval($row['setting_value']);
            if ($row['setting_key'] === 'tax_enabled') $taxEnabled = $row['setting_value'] !== '0';
            if ($row['setting_key'] === 'service_enabled') $serviceEnabled = $row['setting_value'] !== '0';
        }
    } catch (Exception $e) {
        // Use defaults if settings fail
    }
    
    // Calculate service and tax
    $serviceCharge = $serviceEnabled ? ($subTotal * $servicePercent / 100) : 0;
    $taxAmount = $taxEnabled ? ($subTotal * $taxPercent / 100) : 0;
    $total = $subTotal + $serviceCharge + $taxAmount;

    // Start transaction
    $pdo->beginTransaction();

    // Check if there's an active ticket for this table
    $stmt = $pdo->prepare("SELECT id FROM tickets WHERE table_id = ? AND status = 'open' ORDER BY opened_at DESC LIMIT 1");
    $stmt->execute([$table_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ticket) {
        // Use existing ticket
        $ticketId = $ticket['id'];
        
        // Check if there's an active (unpaid) order for this ticket
        // If yes, we'll ADD ITEMS to existing order instead of creating new one
        $stmt = $pdo->prepare("
            SELECT id FROM orders 
            WHERE ticket_id = ? AND status IN ('sent_to_kitchen', 'preparing', 'pending')
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$ticketId]);
        $existingOrder = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingOrder) {
            // ADD ITEMS to existing order
            $orderId = $existingOrder['id'];
            
            // Update order total
            $stmt = $pdo->prepare("UPDATE orders SET total_amount = total_amount + ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$total, $orderId]);
            
            // Insert new items only (existing items stay)
            // Frontend should only send NEW items
        } else {
            // Create new order for this ticket
            $stmt = $pdo->prepare("
                INSERT INTO orders (table_id, ticket_id, total_amount, status, customer_name, customer_phone, created_at, updated_at)
                VALUES (:table_id, :ticket_id, :total_amount, 'sent_to_kitchen', :customer_name, :customer_phone, NOW(), NOW())
            ");
            $stmt->execute([
                'table_id' => $table_id,
                'ticket_id' => $ticketId,
                'total_amount' => $total,
                'customer_name' => $customer_name,
                'customer_phone' => $customer_phone
            ]);
            $orderId = $pdo->lastInsertId();
        }
    } else {
        // Create new ticket
        $ticketNumber = 'TKT-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO tickets (table_id, ticket_number, status, customer_name, customer_phone) VALUES (?, ?, 'open', ?, ?)");
        $stmt->execute([$table_id, $ticketNumber, $customer_name, $customer_phone]);
        $ticketId = $pdo->lastInsertId();
        
        // Create first order for this ticket
        $stmt = $pdo->prepare("
            INSERT INTO orders (table_id, ticket_id, sub_total, service_charge, tax_amount, total_amount, status, customer_name, customer_phone, created_at, updated_at)
            VALUES (:table_id, :ticket_id, :sub_total, :service_charge, :tax_amount, :total_amount, 'sent_to_kitchen', :customer_name, :customer_phone, NOW(), NOW())
        ");
        $stmt->execute([
            'table_id' => $table_id,
            'ticket_id' => $ticketId,
            'sub_total' => $subTotal,
            'service_charge' => $serviceCharge,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'customer_name' => $customer_name,
            'customer_phone' => $customer_phone
        ]);
        $orderId = $pdo->lastInsertId();
    }

    // Create order items (only for NEW items from frontend)
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, menu_item_id, quantity, price, notes, modifiers, created_at)
        VALUES (:order_id, :menu_item_id, :quantity, :price, :notes, :modifiers, NOW())
    ");

    foreach ($items as $item) {
        // Convert notes array to JSON string
        $notes = isset($item['notes']) && is_array($item['notes']) ? json_encode($item['notes']) : null;
        // Convert modifiers array to simple array of names only
        $modifierNames = [];
        if (isset($item['modifiers']) && is_array($item['modifiers'])) {
            foreach ($item['modifiers'] as $mod) {
                // Extract just the name from modifier object
                if (is_array($mod) && isset($mod['name'])) {
                    $modifierNames[] = $mod['name'];
                } elseif (is_string($mod)) {
                    $modifierNames[] = $mod;
                }
            }
        }
        $modifiers = !empty($modifierNames) ? json_encode($modifierNames) : null;

        // Fix: Use menu_id from frontend (it's actually the menu_item_id)
        $menuItemId = $item['menu_id'] ?? $item['menu_item_id'] ?? 0;

        // Validate menu item exists BEFORE inserting
        if (!$menuItemId || $menuItemId == 0) {
            error_log("Invalid menu item ID: " . json_encode($item));
            continue; // Skip this item, don't fail entire order
        }

        // Check if menu item exists in database
        $checkMenu = $pdo->prepare("SELECT id FROM menu_items WHERE id = ?");
        $checkMenu->execute([$menuItemId]);
        if ($checkMenu->rowCount() === 0) {
            error_log("Menu item ID " . $menuItemId . " not found in database, skipping");
            continue; // Skip this item, don't fail entire order
        }

        $stmt->execute([
            'order_id' => $orderId,
            'menu_item_id' => $menuItemId,
            'quantity' => $item['quantity'] ?? 1,
            'price' => $item['price'] ?? 0,
            'notes' => $notes,
            'modifiers' => $modifiers
        ]);
    }

    // Update table status to occupied
    $stmt = $pdo->prepare("UPDATE tables SET status = 'occupied' WHERE id = :id");
    $stmt->execute(['id' => $table_id]);

    $pdo->commit();

    // AUTO STOCK DEDUCTION - Enable stock deduction based on recipes
    try {
        // Check if auto-stock-deduction function exists
        if (file_exists($autoStockFile) && function_exists('deductStockForOrder')) {
            $stockResult = deductStockForOrder($orderId, $pdo);
            
            if (isset($stockResult['success']) && $stockResult['success']) {
                error_log("Stock deducted successfully for order #" . $orderId);
            } else {
                error_log("Stock deduction warning: " . ($stockResult['message'] ?? 'Unknown error'));
            }
        } else {
            error_log("Auto stock deduction not available - function not found");
        }
    } catch (Exception $e) {
        // Don't fail the order if stock deduction fails - just log it
        error_log("Stock deduction failed (non-critical): " . $e->getMessage());
    }

    jsonResponse([
        'success' => true,
        'message' => 'Order created successfully',
        'order_id' => $orderId,
        'total' => $total
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("POS Order store error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to create order: ' . $e->getMessage()
    ], 500);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("POS Order store error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to create order'
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
