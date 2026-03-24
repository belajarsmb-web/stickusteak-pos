<?php
/**
 * RestoQwen POS - Tables Store API
 * POST /api/tables/store.php
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
    $table_number = $input['table_number'] ?? $name; // Use name as table_number if not provided
    $capacity = $input['capacity'] ?? 4;
    
    // Get default outlet_id (first outlet or 1)
    $outlet_id = $input['outlet_id'] ?? 1;

    if (empty($name)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Table name is required']);
        exit;
    }

    $pdo->beginTransaction();

    // Insert table
    $stmt = $pdo->prepare("
        INSERT INTO tables (outlet_id, table_number, name, capacity, status)
        VALUES (:outlet_id, :table_number, :name, :capacity, 'available')
    ");

    $stmt->execute([
        'outlet_id' => $outlet_id,
        'table_number' => $table_number,
        'name' => $name,
        'capacity' => $capacity
    ]);

    $tableId = $pdo->lastInsertId();

    // Generate QR code for the new table
    $qrToken = bin2hex(random_bytes(32)); // Generate unique 64-char token
    $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
    $qrUrl = $baseUrl . '/php-native/mobile/index.php?token=' . $qrToken;

    $stmt = $pdo->prepare("
        INSERT INTO qr_codes (table_id, qr_token, qr_url, is_active, created_at)
        VALUES (:table_id, :qr_token, :qr_url, 1, NOW())
    ");

    $stmt->execute([
        'table_id' => $tableId,
        'qr_token' => $qrToken,
        'qr_url' => $qrUrl
    ]);

    $pdo->commit();

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Table added successfully with QR code',
        'id' => $tableId,
        'qr_token' => $qrToken,
        'qr_url' => $qrUrl
    ]);

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Table store error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add table: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Table store error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add table'
    ]);
}
