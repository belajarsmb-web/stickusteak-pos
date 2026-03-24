<?php
/**
 * RestoQwen POS - Menu Delete API
 * DELETE /api/menu/delete.php?id=
 */

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $id = $_GET['id'] ?? 0;

    if (!$id) {
        jsonResponse(['success' => false, 'message' => 'Item ID required'], 400);
    }

    // Check if item is used in any orders
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM order_items WHERE menu_item_id = :id");
    $stmt->execute(['id' => $id]);
    $orderCount = $stmt->fetch()['count'] ?? 0;

    if ($orderCount > 0) {
        jsonResponse([
            'success' => false,
            'message' => "Cannot delete item. This item is used in {$orderCount} order(s). Mark as unavailable instead."
        ], 400);
    }

    // Check if item is used in modifier groups
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM menu_item_modifiers WHERE menu_item_id = :id");
    $stmt->execute(['id' => $id]);
    $modifierCount = $stmt->fetch()['count'] ?? 0;

    if ($modifierCount > 0) {
        jsonResponse([
            'success' => false,
            'message' => "Cannot delete item. This item has {$modifierCount} modifier group(s) assigned. Remove modifiers first."
        ], 400);
    }

    // Safe to delete
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = :id");
    $stmt->execute(['id' => $id]);

    jsonResponse([
        'success' => true,
        'message' => 'Menu item deleted successfully'
    ]);

} catch (PDOException $e) {
    error_log("Menu delete error: " . $e->getMessage());
    
    // Check for foreign key constraint error
    if ($e->getCode() == 23000) {
        jsonResponse([
            'success' => false,
            'message' => 'Cannot delete item. This item is referenced by other records. Mark as unavailable instead.'
        ], 400);
    }
    
    jsonResponse([
        'success' => false,
        'message' => 'Failed to delete menu item: ' . $e->getMessage()
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
