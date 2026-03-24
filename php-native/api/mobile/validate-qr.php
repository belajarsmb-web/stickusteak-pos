<?php
/**
 * Mobile Order API - Validate QR Token
 * GET /api/mobile/validate-qr.php?token=TOKEN
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    $token = $_GET['token'] ?? '';
    
    if (empty($token)) {
        jsonResponse(['success' => false, 'message' => 'QR token required'], 400);
    }
    
    // Validate QR token
    $stmt = $pdo->prepare("
        SELECT q.*, t.name as table_name, t.status as table_status
        FROM qr_codes q
        JOIN tables t ON q.table_id = t.id
        WHERE q.qr_token = :token AND q.is_active = 1
    ");
    $stmt->execute(['token' => $token]);
    $qrData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$qrData) {
        jsonResponse(['success' => false, 'message' => 'Invalid or expired QR code'], 404);
    }
    
    // Update scan count
    $stmt = $pdo->prepare("
        UPDATE qr_codes 
        SET scan_count = scan_count + 1, last_scanned_at = NOW() 
        WHERE id = :id
    ");
    $stmt->execute(['id' => $qrData['id']]);
    
    jsonResponse([
        'success' => true,
        'table' => [
            'id' => $qrData['table_id'],
            'name' => $qrData['table_name'],
            'status' => $qrData['table_status']
        ],
        'token' => $token
    ]);
    
} catch (PDOException $e) {
    error_log("QR validate error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Validation failed'], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
