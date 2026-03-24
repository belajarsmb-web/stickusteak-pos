<?php
/**
 * RestoQwen POS - Customers API (Index/Get All)
 * GET /api/customers/index.php
 */

error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();
    $search = $_GET['search'] ?? '';

    // Check if customers table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'customers'");
    if ($stmt->rowCount() === 0) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'customers' => [],
            'count' => 0
        ]);
        exit;
    }

    // Get customers with order stats (using actual table columns)
    $sql = "SELECT c.id, c.name, c.phone, c.email, c.address, c.gender,
            c.membership_tier, c.total_spent, c.total_visits, c.last_visit, c.is_active,
            c.created_at, c.outlet_id,
            COALESCE((SELECT COUNT(*) FROM orders o WHERE o.customer_id = c.id), 0) as total_orders,
            COALESCE((SELECT SUM(total_amount) FROM orders o WHERE o.customer_id = c.id), 0) as calculated_total_spent
            FROM customers c 
            WHERE 1=1";
    $params = [];

    if ($search) {
        $sql .= " AND (c.name LIKE :search OR c.phone LIKE :search OR c.email LIKE :search)";
        $params['search'] = "%$search%";
    }

    $sql .= " ORDER BY c.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format response to match frontend expectations
    foreach ($customers as &$customer) {
        $customer['created_at'] = $customer['created_at'];
        $customer['total_spent'] = $customer['calculated_total_spent'] ?? $customer['total_spent'] ?? 0;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'customers' => $customers,
        'count' => count($customers)
    ]);

} catch (PDOException $e) {
    error_log("Customers fetch error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch customers: ' . $e->getMessage()
    ]);
}
