<?php
/**
 * RestoQwen POS - Get Void Reasons API
 * GET /api/orders/void-reasons.php
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM void_reasons WHERE is_active = 1 ORDER BY id");
    $stmt->execute();
    $reasons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    jsonResponse([
        'success' => true,
        'reasons' => $reasons,
        'count' => count($reasons)
    ]);
    
} catch (PDOException $e) {
    error_log("Void reasons fetch error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to fetch void reasons'], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
