<?php
/**
 * Shift Management API - Get Active Shift
 * GET /api/shifts/active.php
 * Returns currently active shift
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();

    // Get active shift (most recent open shift)
    $stmt = $pdo->query("
        SELECT * FROM shifts
        WHERE status = 'open'
        ORDER BY id DESC
        LIMIT 1
    ");
    $shift = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($shift) {
        // Get column names to support both snake_case and camelCase
        $columns = array_keys($shift);
        $hasSnakeCase = in_array('user_id', $columns);
        
        // Map field names for consistency
        $clockInField = $hasSnakeCase ? 'clock_in' : 'clockIn';
        $userIdField = $hasSnakeCase ? 'user_id' : 'userId';
        
        // Calculate shift statistics from orders
        $shiftStart = $shift[$clockInField] ?? date('Y-m-d H:i:s');
        
        // Add created_at alias for frontend compatibility
        $shift['created_at'] = $shiftStart;
        
        $statsStmt = $pdo->prepare("
            SELECT 
                COUNT(DISTINCT o.id) as total_orders,
                COALESCE(SUM(o.total_amount), 0) as total_sales,
                COALESCE(SUM(CASE WHEN o.status IN ('paid', 'completed') THEN o.total_amount ELSE 0 END), 0) as total_paid
            FROM orders o
            WHERE o.created_at >= ?
            AND o.status NOT IN ('cancelled', 'voided')
        ");
        $statsStmt->execute([$shiftStart]);
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        $shift['stats'] = $stats;
        
        // Get opened by name
        if (!empty($shift[$userIdField])) {
            $userStmt = $pdo->prepare("SELECT username, full_name FROM users WHERE id = ?");
            $userStmt->execute([$shift[$userIdField]]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
            $shift['opened_by_name'] = $user['full_name'] ?? $user['username'] ?? 'Unknown';
        }

        jsonResponse([
            'success' => true,
            'has_active_shift' => true,
            'shift' => $shift
        ]);
    } else {
        jsonResponse([
            'success' => true,
            'has_active_shift' => false,
            'message' => 'No active shift'
        ]);
    }

} catch (PDOException $e) {
    error_log("Get active shift error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to get active shift',
        'error' => $e->getMessage()
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
