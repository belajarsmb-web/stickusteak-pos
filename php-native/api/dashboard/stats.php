<?php
/**
 * RestoQwen POS - Dashboard Stats API
 * GET /api/dashboard/stats.php
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    $today = date('Y-m-d');
    
    // Today's orders count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = :today");
    $stmt->execute(['today' => $today]);
    $todayOrders = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Today's revenue
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders WHERE DATE(created_at) = :today AND status != 'cancelled'");
    $stmt->execute(['today' => $today]);
    $todayRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'] ?? 0;
    
    // Pending orders
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
    $stmt->execute();
    $pendingOrders = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Total customers
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM customers");
    $stmt->execute();
    $totalCustomers = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    jsonResponse([
        'success' => true,
        'stats' => [
            'todayOrders' => (int)$todayOrders,
            'todayRevenue' => (float)$todayRevenue,
            'pendingOrders' => (int)$pendingOrders,
            'totalCustomers' => (int)$totalCustomers
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch dashboard stats'
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
