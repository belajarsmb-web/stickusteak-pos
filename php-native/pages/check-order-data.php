<?php
require_once __DIR__ . '/../config/database.php';

$orderId = $_GET['order_id'] ?? 12;

$pdo = getDbConnection();

echo "<h2>Checking Order #$orderId</h2>";

$stmt = $pdo->prepare("
    SELECT o.*, 
           t.table_number,
           tk.ticket_number,
           pm.name as payment_method_name
    FROM orders o
    LEFT JOIN tables t ON o.table_id = t.id
    LEFT JOIN tickets tk ON o.ticket_id = tk.id
    LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
    WHERE o.id = :id
");
$stmt->execute(['id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if ($order) {
    echo "<h3>Order Data:</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    foreach ($order as $key => $value) {
        echo "<tr><td><strong>$key</strong></td><td>" . ($value ?: 'NULL/EMPTY') . "</td></tr>";
    }
    echo "</table>";
    
    echo "<h3>Receipt Display Check:</h3>";
    echo "<ul>";
    echo "<li>Ticket #: " . ($order['ticket_number'] ?? 'MISSING') . "</li>";
    echo "<li>Table: " . ($order['table_number'] ?? 'MISSING') . "</li>";
    echo "<li>Customer: " . ($order['customer_name'] ?? 'MISSING') . "</li>";
    echo "<li>Payment: " . ($order['payment_method_name'] ?? 'MISSING') . "</li>";
    echo "<li>Paid Amount: " . ($order['paid_amount'] ?? 'MISSING') . "</li>";
    echo "</ul>";
    
    echo "<h3>Raw Order Data:</h3>";
    echo "<pre>";
    print_r($order);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>Order #$orderId not found!</p>";
}
?>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { padding: 10px; text-align: left; }
    th { background: #f0f0f0; }
    h2 { color: #333; }
    h3 { color: #666; margin-top: 20px; }
</style>
