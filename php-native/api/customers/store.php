<?php
/**
 * RestoQwen POS - Customers Store API
 * POST /api/customers/store.php
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    $name = $input['name'] ?? '';
    $phone = $input['phone'] ?? '';
    $email = $input['email'] ?? '';
    $address = $input['address'] ?? '';

    if (empty($name)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Name is required']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO customers (name, phone, email, address, membership_tier, total_spent, total_visits, is_active, created_at, updated_at)
        VALUES (:name, :phone, :email, :address, 'bronze', 0, 0, 1, NOW(), NOW())
    ");

    $stmt->execute([
        'name' => $name,
        'phone' => $phone,
        'email' => $email,
        'address' => $address
    ]);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Customer added successfully',
        'id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    error_log("Customer store error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add customer: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Customer store error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add customer'
    ]);
}
