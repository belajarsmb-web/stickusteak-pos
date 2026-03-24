<?php
/**
 * RestoQwen POS - Customers Delete API
 * POST /api/customers/delete.php (soft delete)
 */

require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    jsonResponse(['success' => false, 'message' => 'Authentication required'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    $customerId = isset($input['id']) ? intval($input['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
    
    if ($customerId <= 0) {
        jsonResponse(['success' => false, 'message' => 'Customer ID required'], 400);
    }
    
    // Check if customer exists
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ? AND is_active = 1");
    $stmt->execute([$customerId]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        jsonResponse(['success' => false, 'message' => 'Customer not found or already deleted'], 404);
    }
    
    // Soft delete - set is_active = 0
    $stmt = $pdo->prepare("UPDATE customers SET is_active = 0, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$customerId]);
    
    jsonResponse([
        'success' => true,
        'message' => 'Customer deleted successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Customer delete error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to delete customer: ' . $e->getMessage()], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
