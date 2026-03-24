<?php
/**
 * RestoQwen POS - Sales Reports API
 * GET /api/reports/sales.php
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    $startDate = $_GET['start'] ?? date('Y-m-01');
    $endDate = $_GET['end'] ?? date('Y-m-d');
    $reportType = $_GET['type'] ?? 'sales';

    // Summary stats
    $stmt = $pdo->prepare("
        SELECT
            COALESCE(SUM(total_amount), 0) as total_revenue,
            COUNT(*) as total_orders,
            COALESCE(AVG(total_amount), 0) as avg_order_value
        FROM orders
        WHERE DATE(created_at) BETWEEN :start AND :end AND status != 'cancelled'
    ");
    $stmt->execute(['start' => $startDate, 'end' => $endDate]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Total items sold
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(oi.quantity), 0) as total_items
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE DATE(o.created_at) BETWEEN :start AND :end AND o.status != 'cancelled'
    ");
    $stmt->execute(['start' => $startDate, 'end' => $endDate]);
    $stats['total_items'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_items'] ?? 0;

    // Chart data - daily sales
    $stmt = $pdo->prepare("
        SELECT DATE(created_at) as date,
               COUNT(*) as orders,
               COALESCE(SUM(total_amount), 0) as revenue
        FROM orders
        WHERE DATE(created_at) BETWEEN :start AND :end AND status != 'cancelled'
        GROUP BY DATE(created_at)
        ORDER BY date
    ");
    $stmt->execute(['start' => $startDate, 'end' => $endDate]);
    $dailyData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $chartData = [
        'labels' => array_column($dailyData, 'date'),
        'data' => array_map(function($row) { return (float)$row['revenue']; }, $dailyData)
    ];

    // Category breakdown - use category_id and join with categories table
    $stmt = $pdo->prepare("
        SELECT c.name as category,
               COUNT(*) as count,
               COALESCE(SUM(oi.price * oi.quantity), 0) as revenue
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN menu_items m ON oi.menu_item_id = m.id
        LEFT JOIN categories c ON m.category_id = c.id
        WHERE DATE(o.created_at) BETWEEN :start AND :end AND o.status != 'cancelled'
        GROUP BY c.name
    ");
    $stmt->execute(['start' => $startDate, 'end' => $endDate]);
    $categoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $categoryChartData = [
        'labels' => array_column($categoryData, 'category'),
        'data' => array_map(function($row) { return (float)$row['revenue']; }, $categoryData)
    ];

    // Top selling items - use menu_item_id instead of menu_id
    $stmt = $pdo->prepare("
        SELECT m.name, c.name as category,
               SUM(oi.quantity) as quantity_sold,
               COALESCE(SUM(oi.price * oi.quantity), 0) as revenue
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN menu_items m ON oi.menu_item_id = m.id
        LEFT JOIN categories c ON m.category_id = c.id
        WHERE DATE(o.created_at) BETWEEN :start AND :end AND o.status != 'cancelled'
        GROUP BY m.id, m.name, c.name
        ORDER BY quantity_sold DESC
        LIMIT 10
    ");
    $stmt->execute(['start' => $startDate, 'end' => $endDate]);
    $topItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse([
        'success' => true,
        'stats' => [
            'total_revenue' => (float)($stats['total_revenue'] ?? 0),
            'total_orders' => (int)($stats['total_orders'] ?? 0),
            'avg_order_value' => (float)($stats['avg_order_value'] ?? 0),
            'total_items' => (int)($stats['total_items'] ?? 0)
        ],
        'chartData' => $chartData,
        'categoryData' => $categoryChartData,
        'topItems' => $topItems
    ]);

} catch (PDOException $e) {
    error_log("Reports error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch reports: ' . $e->getMessage()
    ], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
