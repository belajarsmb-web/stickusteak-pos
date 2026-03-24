<?php
/**
 * RestoQwen POS - User Profile API
 * GET /api/auth/profile.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $userId = getCurrentUserId();
    
    $stmt = getDbConnection()->prepare("
        SELECT id, username, email, full_name, role, phone, created_at
        FROM users
        WHERE id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        jsonResponse(['success' => false, 'message' => 'User not found'], 404);
    }
    
    jsonResponse([
        'success' => true,
        'user' => $user
    ]);
    
} catch (PDOException $e) {
    error_log("Profile error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to get profile'
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
