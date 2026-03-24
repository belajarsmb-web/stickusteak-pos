<?php
/**
 * Shift Management API - Open Shift
 * POST /api/shifts/open.php
 * Opens a new cashier shift
 */

require_once __DIR__ . '/../../config/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

if (!isset($_SESSION['user_id'])) {
    jsonResponse(['success' => false, 'message' => 'Authentication required'], 401);
}

try {
    $pdo = getDbConnection();
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['user_id'];
    $outletId = $input['outlet_id'] ?? 1;
    $notes = $input['notes'] ?? '';

    // Get columns from shifts table to support both naming conventions
    $columns = $pdo->query("DESCRIBE shifts")->fetchAll(PDO::FETCH_COLUMN);
    $hasSnakeCase = in_array('user_id', $columns);
    
    // Build insert based on actual columns
    $insertData = [];
    $params = [];
    
    if ($hasSnakeCase) {
        $insertData[] = "user_id = ?";
        $params[] = $userId;
        $insertData[] = "outlet_id = ?";
        $params[] = $outletId;
        $insertData[] = "shift_date = CURDATE()";
        $insertData[] = "status = 'open'";
        $insertData[] = "clock_in = NOW()";
        if (in_array('notes', $columns)) {
            $insertData[] = "notes = ?";
            $params[] = $notes;
        }
    } else {
        // camelCase
        $insertData[] = "userId = ?";
        $params[] = $userId;
        $insertData[] = "outletId = ?";
        $params[] = $outletId;
        $insertData[] = "shiftDate = CURDATE()";
        $insertData[] = "status = 'open'";
        $insertData[] = "clockIn = NOW()";
    }
    
    $sql = "INSERT INTO shifts SET " . implode(', ', $insertData);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $shiftId = $pdo->lastInsertId();

    // Get shift details
    $shiftStmt = $pdo->prepare("SELECT * FROM shifts WHERE id = ?");
    $shiftStmt->execute([$shiftId]);
    $shift = $shiftStmt->fetch(PDO::FETCH_ASSOC);

    jsonResponse([
        'success' => true,
        'message' => 'Shift opened successfully',
        'shift' => $shift,
        'shift_id' => $shiftId
    ]);

} catch (PDOException $e) {
    error_log("Open shift error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to open shift: ' . $e->getMessage()
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
