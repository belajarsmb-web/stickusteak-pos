<?php
/**
 * RestoQwen POS - Tables Delete API
 * DELETE /api/tables/delete.php?id=
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require authentication
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $id = $_GET['id'] ?? 0;
    
    if (!$id) {
        jsonResponse(['success' => false, 'message' => 'Table ID required'], 400);
    }
    
    $stmt = getDbConnection()->prepare("DELETE FROM tables WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    jsonResponse([
        'success' => true,
        'message' => 'Table deleted successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Table delete error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to delete table'
    ], 500);
}
