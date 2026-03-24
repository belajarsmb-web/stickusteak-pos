<?php
/**
 * RestoQwen POS - Users Delete API
 * DELETE /api/users/delete.php?id=
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require authentication
requireAuth();

// Only admins can delete users
requireRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $id = $_GET['id'] ?? 0;
    
    if (!$id) {
        jsonResponse(['success' => false, 'message' => 'User ID required'], 400);
    }
    
    // Prevent deleting yourself
    if ($id == getCurrentUserId()) {
        jsonResponse(['success' => false, 'message' => 'Cannot delete your own account'], 400);
    }
    
    $stmt = getDbConnection()->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    jsonResponse([
        'success' => true,
        'message' => 'User deleted successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("User delete error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to delete user'
    ], 500);
}
