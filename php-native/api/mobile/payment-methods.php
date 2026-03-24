<?php
/**
 * Mobile Payment Methods API
 * GET /api/mobile/payment-methods.php - Get payment methods
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    
    // Get active payment methods
    $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY sort_order, name");
    $stmt->execute();
    $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse([
        'success' => true,
        'methods' => $methods,
        'count' => count($methods)
    ]);

} catch (PDOException $e) {
    error_log("Payment methods fetch error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to fetch payment methods'], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
