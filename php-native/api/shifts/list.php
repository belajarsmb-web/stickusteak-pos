<?php
/**
 * Shift Management API - List Shifts
 * GET /api/shifts/list.php
 * Returns shift history
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();

    // Get shift history
    $stmt = $pdo->query("
        SELECT * FROM shifts
        ORDER BY id DESC
        LIMIT 50
    ");
    $shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse([
        'success' => true,
        'shifts' => $shifts,
        'count' => count($shifts)
    ]);

} catch (PDOException $e) {
    error_log("List shifts error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch shifts',
        'error' => $e->getMessage()
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
