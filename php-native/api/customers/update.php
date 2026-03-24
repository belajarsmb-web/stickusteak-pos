<?php
/**
 * RestoQwen POS - Customers Update API
 * POST /api/customers/update.php
 */

require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    jsonResponse(['success' => false, 'message' => 'Authentication required'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($input['id'] ?? 0);
    
    if (!$id) {
        jsonResponse(['success' => false, 'message' => 'Customer ID required'], 400);
    }
    
    // Check if customer exists
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        jsonResponse(['success' => false, 'message' => 'Customer not found'], 404);
    }
    
    // Build update query dynamically
    $updateFields = [];
    $params = [];
    
    if (isset($input['name']) && !empty($input['name'])) {
        $updateFields[] = 'name = ?';
        $params[] = trim($input['name']);
    }
    if (isset($input['phone']) && $input['phone'] !== null) {
        $updateFields[] = 'phone = ?';
        $params[] = trim($input['phone']);
    }
    if (isset($input['email']) && $input['email'] !== null) {
        $updateFields[] = 'email = ?';
        $params[] = trim($input['email']);
    }
    if (isset($input['address']) && $input['address'] !== null) {
        $updateFields[] = 'address = ?';
        $params[] = trim($input['address']);
    }
    if (isset($input['date_of_birth']) && $input['date_of_birth'] !== null) {
        $updateFields[] = 'date_of_birth = ?';
        $params[] = $input['date_of_birth'];
    }
    if (isset($input['gender']) && $input['gender'] !== null) {
        $updateFields[] = 'gender = ?';
        $params[] = $input['gender'];
    }
    if (isset($input['membership_tier']) && $input['membership_tier'] !== null) {
        $updateFields[] = 'membership_tier = ?';
        $params[] = $input['membership_tier'];
    }
    if (isset($input['is_active'])) {
        $updateFields[] = 'is_active = ?';
        $params[] = intval($input['is_active']);
    }
    
    // Always update updated_at
    $updateFields[] = 'updated_at = NOW()';
    
    if (count($updateFields) <= 1) {
        jsonResponse(['success' => false, 'message' => 'No valid fields to update'], 400);
    }
    
    $params[] = $id;
    $sql = "UPDATE customers SET " . implode(', ', $updateFields) . " WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Fetch updated customer
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    $updatedCustomer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    jsonResponse([
        'success' => true,
        'message' => 'Customer updated successfully',
        'customer' => $updatedCustomer
    ]);
    
} catch (PDOException $e) {
    error_log("Customer update error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to update customer: ' . $e->getMessage()], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
