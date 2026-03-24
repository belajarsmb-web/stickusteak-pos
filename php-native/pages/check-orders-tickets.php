<?php
/**
 * Check Orders and Tickets
 */
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDbConnection();
    
    echo "<h2>Orders with Ticket Info</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Order ID</th><th>Ticket ID</th><th>Ticket Number</th><th>Table ID</th><th>Status</th><th>Created</th></tr>";
    
    $stmt = $pdo->query("
        SELECT o.id, o.ticket_id, tk.ticket_number, o.table_id, o.status, o.created_at
        FROM orders o
        LEFT JOIN tickets tk ON o.ticket_id = tk.id
        ORDER BY o.id DESC
        LIMIT 20
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ticketDisplay = $row['ticket_number'] ?? 'NULL';
        $ticketIdDisplay = $row['ticket_id'] ?? 'NULL';
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$ticketIdDisplay}</td>";
        echo "<td>{$ticketDisplay}</td>";
        echo "<td>{$row['table_id']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h2>Tickets</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Ticket ID</th><th>Ticket Number</th><th>Table ID</th><th>Status</th><th>Orders Count</th></tr>";
    
    $stmt = $pdo->query("
        SELECT t.id, t.ticket_number, t.table_id, t.status,
               (SELECT COUNT(*) FROM orders WHERE ticket_id = t.id) as order_count
        FROM tickets t
        ORDER BY t.id DESC
        LIMIT 10
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['ticket_number']}</td>";
        echo "<td>{$row['table_id']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "<td>{$row['order_count']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
