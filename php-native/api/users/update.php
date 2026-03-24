<?php
/**
 * RestoQwen POS - Update User
 * PUT /api/users/update.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require authentication
requireAuth();

// Only admins and managers can update users
requireRole(['admin', 'manager']);

// Only accept PUT/POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $input = getJsonInput();
    
    // Validate required fields
    if (empty($input['id'])) {
        jsonResponse(['success' => false, 'message' => 'User ID is required'], 400);
    }
    
    $userId = (int)$input['id'];
    
    // Check if user exists
    $user = dbQuery("SELECT id FROM users WHERE id = ?", [$userId]);
    if (empty($user)) {
        jsonResponse(['success' => false, 'message' => 'User not found'], 404);
    }
    
    // Build update fields
    $updateFields = [];
    $params = [];
    
    if (isset($input['email'])) {
        $email = sanitizeInput($input['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => 'Invalid email format'], 400);
        }
        // Check if email is used by another user
        $existing = dbQuery("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $userId]);
        if (!empty($existing)) {
            jsonResponse(['success' => false, 'message' => 'Email already exists'], 400);
        }
        $updateFields[] = "email = ?";
        $params[] = $email;
    }
    
    if (isset($input['full_name'])) {
        $updateFields[] = "full_name = ?";
        $params[] = sanitizeInput($input['full_name']);
    }
    
    if (isset($input['role'])) {
        $role = sanitizeInput($input['role']);
        $validRoles = ['admin', 'manager', 'staff', 'cashier'];
        if (!in_array($role, $validRoles)) {
            jsonResponse(['success' => false, 'message' => 'Invalid role'], 400);
        }
        $updateFields[] = "role = ?";
        $params[] = $role;
    }
    
    if (isset($input['status'])) {
        $status = sanitizeInput($input['status']);
        $validStatuses = ['active', 'inactive'];
        if (!in_array($status, $validStatuses)) {
            jsonResponse(['success' => false, 'message' => 'Invalid status'], 400);
        }
        $updateFields[] = "status = ?";
        $params[] = $status;
    }
    
    if (isset($input['password']) && !empty($input['password'])) {
        $updateFields[] = "password = ?";
        $params[] = hashPassword($input['password']);
    }
    
    // Add updated_at
    $updateFields[] = "updated_at = NOW()";
    
    if (empty($updateFields)) {
        jsonResponse(['success' => false, 'message' => 'No fields to update'], 400);
    }
    
    // Add user ID to params
    $params[] = $userId;
    
    // Execute update
    $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
    dbExecute($sql, $params);
    
    // Fetch updated user
    $updatedUser = dbQuery("SELECT id, username, email, full_name, role, status, updated_at FROM users WHERE id = ?", [$userId]);
    
    jsonResponse([
        'success' => true,
        'message' => 'User updated successfully',
        'user' => $updatedUser[0]
    ]);
    
} catch (Exception $e) {
    error_log("Error updating user: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to update user'
    ], 500);
}
