<?php
/**
 * Tables API - PUT update table
 * Required: id
 * Optional: table_number, name, capacity, position_x, position_y, width, height, status
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require authentication
requireAuth();

// Only allow PUT/POST method
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $errors = [];
    
    if (empty($input['id'])) {
        $errors[] = 'Table ID is required';
    }
    
    if (!empty($errors)) {
        jsonResponse(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
    }
    
    $tableId = intval($input['id']);
    
    // Check if table exists
    $existingTable = dbQuery("SELECT * FROM tables WHERE id = ?", [$tableId]);
    $table = $existingTable->fetch(PDO::FETCH_ASSOC);
    
    if (!$table) {
        jsonResponse(['success' => false, 'message' => 'Table not found'], 404);
    }
    
    // Validate capacity if provided
    if (isset($input['capacity']) && (!is_numeric($input['capacity']) || intval($input['capacity']) < 1)) {
        $errors[] = 'Capacity must be a positive number';
    }
    
    // Validate status if provided
    $validStatuses = ['available', 'occupied', 'reserved', 'maintenance'];
    if (isset($input['status']) && !in_array($input['status'], $validStatuses)) {
        $errors[] = 'Invalid status. Valid statuses: ' . implode(', ', $validStatuses);
    }
    
    if (!empty($errors)) {
        jsonResponse(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
    }
    
    // Check for duplicate table number (excluding current table)
    if (isset($input['table_number']) && $input['table_number'] !== $table['table_number']) {
        $checkTable = dbQuery("SELECT id FROM tables WHERE table_number = ? AND is_active = 1 AND id != ?", [$input['table_number'], $tableId]);
        if ($checkTable->fetchColumn()) {
            jsonResponse(['success' => false, 'message' => 'A table with this number already exists'], 409);
        }
    }
    
    // Build update query dynamically
    $updateFields = [];
    $params = [];
    
    if (isset($input['table_number'])) {
        $updateFields[] = 'table_number = ?';
        $params[] = trim($input['table_number']);
    }
    if (isset($input['name'])) {
        $updateFields[] = 'name = ?';
        $params[] = trim($input['name']);
    }
    if (isset($input['capacity'])) {
        $updateFields[] = 'capacity = ?';
        $params[] = intval($input['capacity']);
    }
    if (isset($input['position_x'])) {
        $updateFields[] = 'position_x = ?';
        $params[] = floatval($input['position_x']);
    }
    if (isset($input['position_y'])) {
        $updateFields[] = 'position_y = ?';
        $params[] = floatval($input['position_y']);
    }
    if (isset($input['width'])) {
        $updateFields[] = 'width = ?';
        $params[] = floatval($input['width']);
    }
    if (isset($input['height'])) {
        $updateFields[] = 'height = ?';
        $params[] = floatval($input['height']);
    }
    if (isset($input['status'])) {
        $updateFields[] = 'status = ?';
        $params[] = $input['status'];
    }
    if (isset($input['is_active'])) {
        $updateFields[] = 'is_active = ?';
        $params[] = intval($input['is_active']);
    }
    
    // Always update updated_at
    $updateFields[] = 'updated_at = NOW()';
    
    if (empty($updateFields) || count($updateFields) === 1) {
        jsonResponse(['success' => false, 'message' => 'No valid fields to update'], 400);
    }
    
    $params[] = $tableId;
    $sql = "UPDATE tables SET " . implode(', ', $updateFields) . " WHERE id = ?";
    
    dbExecute($sql, $params);
    
    // Fetch updated table
    $updatedTable = dbQuery("SELECT * FROM tables WHERE id = ?", [$tableId])->fetch(PDO::FETCH_ASSOC);
    
    jsonResponse([
        'success' => true,
        'message' => 'Table updated successfully',
        'data' => $updatedTable
    ]);
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to update table', 'error' => $e->getMessage()], 500);
}
