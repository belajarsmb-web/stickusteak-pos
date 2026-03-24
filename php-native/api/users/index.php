<?php
/**
 * RestoQwen POS - Users API (Index/Get All)
 * GET /api/users/index.php
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    
    $sql = "SELECT u.id, u.username, u.email, u.full_name, u.role_id, u.is_active, u.last_login_at, u.created_at, u.updated_at,
            r.name as role
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            ORDER BY u.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse([
        'success' => true,
        'users' => $users,
        'count' => count($users)
    ]);

} catch (PDOException $e) {
    error_log("Error fetching users: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch users'
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
