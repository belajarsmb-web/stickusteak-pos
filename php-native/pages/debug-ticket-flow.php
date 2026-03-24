<?php
/**
 * Debug Ticket Flow
 * Verify ticket-based order system is working
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

echo "<h2>🎫 Debug Ticket Flow</h2>";

// Check if tickets table exists
echo "<h3>1. Tickets Table Structure</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'tickets'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tickets table EXISTS<br>";
        $stmt = $pdo->query("DESCRIBE tickets");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Tickets table NOT FOUND! Run create-tickets-table.sql<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Check if orders table has ticket_id column
echo "<h3>2. Orders Table - ticket_id Column</h3>";
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'ticket_id'");
    if ($stmt->rowCount() > 0) {
        echo "✅ ticket_id column EXISTS in orders table<br>";
    } else {
        echo "❌ ticket_id column NOT FOUND! Run create-tickets-table.sql<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Check current data
echo "<h3>3. Current Data Status</h3>";
$stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets");
$tickets = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
echo "Total Tickets: <strong>$tickets</strong><br>";

$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$orders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
echo "Total Orders: <strong>$orders</strong><br>";

$stmt = $pdo->query("SELECT COUNT(*) as total FROM order_items");
$items = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
echo "Total Order Items: <strong>$items</strong><br>";

// Test ticket creation flow
echo "<h3>4. Test Ticket Creation Flow</h3>";
echo "<form method='POST'>";
echo "<label>Table ID: <input type='number' name='table_id' value='14' min='1'></label> ";
echo "<label>Customer Name: <input type='text' name='customer_name' value='Test User'></label> ";
echo "<label>Customer Phone: <input type='text' name='customer_phone' value='08123456789'></label> ";
echo "<button type='submit' name='test_create'>Test Create Ticket</button>";
echo "</form>";

if (isset($_POST['test_create'])) {
    try {
        $tableId = (int)$_POST['table_id'];
        $customerName = $_POST['customer_name'];
        $customerPhone = $_POST['customer_phone'];
        
        // Check existing ticket
        $stmt = $pdo->prepare("SELECT id FROM tickets WHERE table_id = ? AND status = 'open' ORDER BY opened_at DESC LIMIT 1");
        $stmt->execute([$tableId]);
        $existingTicket = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingTicket) {
            echo "<div style='background:#90EE90; padding:10px; margin:10px 0;'>";
            echo "✅ EXISTING Ticket Found:<br>";
            echo "Ticket ID: <strong>{$existingTicket['id']}</strong><br>";
            echo "This table already has an active ticket. New orders will use this ticket.<br>";
            echo "</div>";
        } else {
            echo "<div style='background:#FFD700; padding:10px; margin:10px 0;'>";
            echo "⚠️ NO Existing Ticket<br>";
            echo "New ticket will be created for this table.<br>";
            echo "</div>";
        }
        
        // Create test ticket
        $ticketNumber = 'TKT-' . date('Ymd') . '-TEST';
        $stmt = $pdo->prepare("INSERT INTO tickets (table_id, ticket_number, status, customer_name, customer_phone) VALUES (?, ?, 'open', ?, ?)");
        $stmt->execute([$tableId, $ticketNumber, $customerName, $customerPhone]);
        $ticketId = $pdo->lastInsertId();
        
        echo "<div style='background:#90EE90; padding:10px; margin:10px 0;'>";
        echo "✅ Ticket Created Successfully!<br>";
        echo "Ticket ID: <strong>$ticketId</strong><br>";
        echo "Ticket Number: <strong>$ticketNumber</strong><br>";
        echo "Table ID: <strong>$tableId</strong><br>";
        echo "</div>";
        
        // Create test order
        $stmt = $pdo->prepare("INSERT INTO orders (table_id, ticket_id, total_amount, status) VALUES (?, ?, 0, 'draft')");
        $stmt->execute([$tableId, $ticketId]);
        $orderId = $pdo->lastInsertId();
        
        echo "<div style='background:#90EE90; padding:10px; margin:10px 0;'>";
        echo "✅ Test Order Created!<br>";
        echo "Order ID: <strong>$orderId</strong><br>";
        echo "Ticket ID: <strong>$ticketId</strong><br>";
        echo "This order is linked to the ticket!<br>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div style='background:#FF6B6B; padding:10px; margin:10px 0;'>";
        echo "❌ Error: " . $e->getMessage() . "<br>";
        echo "</div>";
    }
}

// Show recent tickets
echo "<h3>5. Recent Tickets</h3>";
$stmt = $pdo->query("SELECT t.*, (SELECT COUNT(*) FROM orders o WHERE o.ticket_id = t.id) as orders_count FROM tickets t ORDER BY t.opened_at DESC LIMIT 10");
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($tickets) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Ticket ID</th><th>Ticket Number</th><th>Table ID</th><th>Status</th><th>Customer</th><th>Orders</th><th>Opened At</th></tr>";
    foreach ($tickets as $ticket) {
        echo "<tr>";
        echo "<td>{$ticket['id']}</td>";
        echo "<td>{$ticket['ticket_number']}</td>";
        echo "<td>{$ticket['table_id']}</td>";
        echo "<td><strong>{$ticket['status']}</strong></td>";
        echo "<td>{$ticket['customer_name']}<br>{$ticket['customer_phone']}</td>";
        echo "<td>{$ticket['orders_count']}</td>";
        echo "<td>{$ticket['opened_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No tickets found. Click 'Test Create Ticket' to create one.</p>";
}

// Show recent orders
echo "<h3>6. Recent Orders</h3>";
$stmt = $pdo->query("SELECT o.*, t.ticket_number FROM orders o LEFT JOIN tickets t ON o.ticket_id = t.id ORDER BY o.created_at DESC LIMIT 10");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($orders) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Order ID</th><th>Ticket</th><th>Table</th><th>Status</th><th>Total</th><th>Created</th></tr>";
    foreach ($orders as $order) {
        echo "<tr>";
        echo "<td>{$order['id']}</td>";
        echo "<td>" . ($order['ticket_number'] ?? 'N/A') . "</td>";
        echo "<td>{$order['table_id']}</td>";
        echo "<td><strong>{$order['status']}</strong></td>";
        echo "<td>Rp " . number_format($order['total_amount'], 0, ',', '.') . "</td>";
        echo "<td>{$order['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No orders found.</p>";
}

echo "<hr>";
echo "<h3>✅ FLOW VERIFICATION</h3>";
echo "<ol>";
echo "<li>Click 'Test Create Ticket' → Creates NEW Ticket</li>";
echo "<li>Click again with SAME Table ID → Reuses EXISTING Ticket</li>";
echo "<li>Check 'Recent Tickets' → See ticket with orders linked</li>";
echo "<li>Check 'Recent Orders' → See orders with ticket_id linked</li>";
echo "</ol>";
?>
<style>
    body { font-family: monospace; padding: 20px; }
    td, th { padding: 8px; }
    input, button { padding: 8px; margin: 5px; }
</style>
