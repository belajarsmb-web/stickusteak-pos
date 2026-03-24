<?php
/**
 * Test Tickets API
 */
require_once __DIR__ . '/../config/database.php';

echo "<h2>Testing Tickets API</h2>";

try {
    $pdo = getDbConnection();
    echo "<p>✅ Database connected successfully</p>";
    
    // Test 1: Check if tickets table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'tickets'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Tickets table exists</p>";
        
        // Count tickets
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM tickets");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>📊 Total tickets: <strong>{$count['count']}</strong></p>";
        
        // Get recent tickets
        $stmt = $pdo->query("SELECT id, ticket_number, table_id, status, opened_at FROM tickets ORDER BY opened_at DESC LIMIT 5");
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($tickets) > 0) {
            echo "<h3>Recent Tickets:</h3>";
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>ID</th><th>Ticket Number</th><th>Table ID</th><th>Status</th><th>Opened At</th></tr>";
            foreach ($tickets as $ticket) {
                echo "<tr>";
                echo "<td>{$ticket['id']}</td>";
                echo "<td>{$ticket['ticket_number']}</td>";
                echo "<td>{$ticket['table_id']}</td>";
                echo "<td>{$ticket['status']}</td>";
                echo "<td>{$ticket['opened_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>⚠️ No tickets found in database</p>";
        }
    } else {
        echo "<p>❌ Tickets table does not exist!</p>";
    }
    
    // Test 2: Check orders with ticket_id
    echo "<h3>Orders with Ticket ID:</h3>";
    $stmt = $pdo->query("
        SELECT o.id, o.ticket_id, t.ticket_number, o.total_amount, o.status 
        FROM orders o
        LEFT JOIN tickets t ON o.ticket_id = t.id
        WHERE o.ticket_id IS NOT NULL
        ORDER BY o.created_at DESC
        LIMIT 10
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($orders) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Order ID</th><th>Ticket ID</th><th>Ticket Number</th><th>Amount</th><th>Status</th></tr>";
        foreach ($orders as $order) {
            echo "<tr>";
            echo "<td>{$order['id']}</td>";
            echo "<td>{$order['ticket_id']}</td>";
            echo "<td>{$order['ticket_number'] ?? 'N/A'}</td>";
            echo "<td>Rp " . number_format($order['total_amount'], 0, ',', '.') . "</td>";
            echo "<td>{$order['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ No orders with ticket_id found</p>";
    }
    
    // Test 3: Direct API call
    echo "<h3>Direct API Test:</h3>";
    $apiUrl = "http://localhost/php-native/api/tickets/list-all.php?status=open";
    echo "<p>Testing: <a href='$apiUrl' target='_blank'>$apiUrl</a></p>";
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            echo "<p>✅ API returned success</p>";
            echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<p>❌ API returned error: " . ($data['message'] ?? 'Unknown') . "</p>";
        }
    } else {
        echo "<p>❌ API returned HTTP $httpCode</p>";
        echo "<pre>$response</pre>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { padding: 10px; text-align: left; }
    th { background: #f0f0f0; }
    p { margin: 10px 0; }
    .success { color: green; }
    .error { color: red; }
</style>
