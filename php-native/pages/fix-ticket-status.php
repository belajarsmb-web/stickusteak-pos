<?php
/**
 * Fix Ticket Status & Payment Data
 * Updates tickets where all orders are paid but ticket is still open
 * Also fixes paid_amount for orders that were paid but amount not recorded
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDbConnection();
    
    echo "<h2>🔧 Fix Ticket Status & Payment Data</h2>";
    
    // PART 1: Fix tickets where all orders are paid but ticket is still open
    $sql = "
        SELECT
            t.id,
            t.ticket_number,
            t.status as ticket_status,
            COUNT(o.id) as total_orders,
            SUM(CASE WHEN o.status = 'paid' THEN 1 ELSE 0 END) as paid_orders
        FROM tickets t
        LEFT JOIN orders o ON t.id = o.ticket_id AND o.status NOT IN ('cancelled', 'voided')
        WHERE t.status = 'open'
        GROUP BY t.id
        HAVING total_orders > 0 AND total_orders = paid_orders
    ";
    
    $stmt = $pdo->query($sql);
    $inconsistentTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Part 1: Tickets with Status Inconsistency</h3>";
    
    if (count($inconsistentTickets) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Ticket ID</th><th>Ticket Number</th><th>Ticket Status</th><th>Total Orders</th><th>Paid Orders</th><th>Action</th></tr>";
        
        foreach ($inconsistentTickets as $ticket) {
            echo "<tr>";
            echo "<td>{$ticket['id']}</td>";
            echo "<td><strong>{$ticket['ticket_number']}</strong></td>";
            echo "<td><span style='color: orange;'>{$ticket['ticket_status']}</span></td>";
            echo "<td>{$ticket['total_orders']}</td>";
            echo "<td><span style='color: green;'>{$ticket['paid_orders']}</span></td>";
            echo "<td><button onclick='fixTicket({$ticket['id']})' style='padding: 5px 10px; background: green; color: white; border: none; cursor: pointer; border-radius: 3px;'>✅ Fix This</button></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p style='color: orange;'><strong>⚠️ Found " . count($inconsistentTickets) . " ticket(s) with wrong status!</strong></p>";
    } else {
        echo "<p style='color: green; font-size: 1.2rem;'>✅ Part 1: All tickets have correct status!</p>";
    }
    
    // PART 2: Fix orders with paid status but no paid_amount
    $sql2 = "
        SELECT
            o.id,
            o.ticket_id,
            t.ticket_number,
            o.total_amount,
            o.paid_amount,
            o.status
        FROM orders o
        LEFT JOIN tickets t ON o.ticket_id = t.id
        WHERE o.status = 'paid'
        AND (o.paid_amount IS NULL OR o.paid_amount = 0)
        ORDER BY o.id DESC
        LIMIT 50
    ";
    
    $stmt2 = $pdo->query($sql2);
    $ordersWithoutPayment = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Part 2: Paid Orders Without paid_amount</h3>";
    
    if (count($ordersWithoutPayment) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Order ID</th><th>Ticket Number</th><th>Total Amount</th><th>Paid Amount</th><th>Status</th><th>Action</th></tr>";
        
        foreach ($ordersWithoutPayment as $order) {
            echo "<tr>";
            echo "<td>{$order['id']}</td>";
            echo "<td>" . (isset($order['ticket_number']) ? $order['ticket_number'] : 'N/A') . "</td>";
            echo "<td>Rp " . number_format($order['total_amount'], 0, ',', '.') . "</td>";
            echo "<td style='color: red;'>" . (isset($order['paid_amount']) && $order['paid_amount'] ? number_format($order['paid_amount'], 0, ',', '.') : 'NULL/0') . "</td>";
            echo "<td style='color: green;'>{$order['status']}</td>";
            echo "<td><button onclick='fixOrder({$order['id']}, {$order['total_amount']})' style='padding: 5px 10px; background: blue; color: white; border: none; cursor: pointer; border-radius: 3px;'>✅ Fix</button></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p style='color: orange;'><strong>⚠️ Found " . count($ordersWithoutPayment) . " order(s) without paid_amount!</strong></p>";
    } else {
        echo "<p style='color: green; font-size: 1.2rem;'>✅ Part 2: All paid orders have paid_amount recorded!</p>";
    }
    
    // Fix All Button
    echo "<h3>Fix All Issues?</h3>";
    echo "<button onclick='fixAll()' style='padding: 15px 30px; background: green; color: white; border: none; cursor: pointer; border-radius: 5px; font-size: 1.1rem;'>✅ Fix All Issues Automatically</button>";
    
    echo "<div id='result' style='margin-top: 20px; padding: 20px; border: 2px solid #28a745; border-radius: 10px; display: none; background: #d4edda;'></div>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
}
?>

<script>
async function fixTicket(ticketId) {
    try {
        const response = await fetch('/php-native/api/shifts/close.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                ticket_id: ticketId,
                fix_only: true
            })
        });
        
        if (response.ok) {
            alert('✅ Ticket #' + ticketId + ' status fixed!');
            location.reload();
        } else {
            alert('❌ Failed to fix ticket');
        }
    } catch (error) {
        alert('❌ Error: ' + error.message);
    }
}

async function fixOrder(orderId, totalAmount) {
    try {
        const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                fix_order: orderId,
                paid_amount: totalAmount
            })
        });
        
        if (response.ok) {
            alert('✅ Order #' + orderId + ' paid_amount fixed!');
            location.reload();
        } else {
            alert('❌ Failed to fix order');
        }
    } catch (error) {
        alert('❌ Error: ' + error.message);
    }
}

async function fixAll() {
    if (!confirm('Fix all ticket statuses and order payment amounts?')) return;
    
    try {
        const response = await fetch('/php-native/api/fix-ticket-order-data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ fix_all: true })
        });
        
        const result = await response.json();
        
        const resultDiv = document.getElementById('result');
        resultDiv.style.display = 'block';
        
        if (result.success) {
            resultDiv.innerHTML = '<h3 style="color: green;">✅ Success!</h3>' +
                '<p>Fixed ' + result.tickets_fixed + ' ticket statuses</p>' +
                '<p>Fixed ' + result.orders_fixed + ' order payment amounts</p>' +
                '<button onclick="location.reload()" style="margin-top: 10px; padding: 10px 20px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 5px;">🔄 Refresh Page</button>';
        } else {
            resultDiv.innerHTML = '<h3 style="color: red;">❌ Error</h3><p>' + result.message + '</p>';
        }
    } catch (error) {
        alert('❌ Error: ' + error.message);
    }
}
</script>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { padding: 10px; text-align: left; }
    th { background: #f0f0f0; }
    h2 { color: #333; }
    h3 { color: #666; margin-top: 30px; }
    button { transition: all 0.3s; }
    button:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
</style>
