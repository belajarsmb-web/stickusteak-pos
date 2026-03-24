<?php
/**
 * RestoQwen POS - Users Store API
 * POST /api/users/store.php
 */

error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Only admins can create users
$userRole = $_SESSION['role'] ?? '';
if (!in_array($userRole, ['admin', 'manager'])) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    $full_name = $input['full_name'] ?? '';
    $username = $input['username'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $role_id = $input['role_id'] ?? 3;
    $is_active = $input['is_active'] ?? 1;

    if (empty($full_name) || empty($username) || empty($password)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Name, username, and password are required']);
        exit;
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch()) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit;
    }

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, username, email, password_hash, role_id, is_active, created_at)
        VALUES (:full_name, :username, :email, :password_hash, :role_id, :is_active, NOW())
    ");

    $stmt->execute([
        'full_name' => $full_name,
        'username' => $username,
        'email' => $email,
        'password_hash' => $passwordHash,
        'role_id' => $role_id,
        'is_active' => $is_active
    ]);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'User added successfully',
        'id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    error_log("User store error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add user: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("User store error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add user'
    ]);
}
