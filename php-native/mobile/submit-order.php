<?php
ob_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');
ob_clean();

try {
    $pdo = getDbConnection();
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) throw new Exception('Invalid JSON');

    $tableId = (int)($data['table_id'] ?? 0);
    $items = $data['items'] ?? [];
    $customerName = $data['customer_name'] ?? '';
    $customerPhone = $data['customer_phone'] ?? '';

    if ($tableId < 1) throw new Exception('Invalid table ID');
    if (empty($items)) throw new Exception('No items');

    $total = 0;
    foreach ($items as $item) {
        $total += floatval($item['price'] ?? 0) * intval($item['quantity'] ?? 1);
    }

    $pdo->beginTransaction();

    // Check if there's an active ticket for this table
    $stmt = $pdo->prepare("SELECT id FROM tickets WHERE table_id = ? AND status = 'open' ORDER BY opened_at DESC LIMIT 1");
    $stmt->execute([$tableId]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ticket) {
        // Use existing ticket
        $ticketId = $ticket['id'];
    } else {
        // Create new ticket
        $ticketNumber = 'TKT-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO tickets (table_id, ticket_number, status, customer_name, customer_phone) VALUES (?, ?, 'open', ?, ?)");
        $stmt->execute([$tableId, $ticketNumber, $customerName, $customerPhone]);
        $ticketId = $pdo->lastInsertId();
    }

    // Insert order with ticket_id
    $stmt = $pdo->prepare("INSERT INTO orders (table_id, ticket_id, total_amount, status, customer_name, customer_phone) VALUES (?, ?, ?, 'sent_to_kitchen', ?, ?)");
    $stmt->execute([$tableId, $ticketId, $total, $customerName, $customerPhone]);
    $orderId = $pdo->lastInsertId();

    // Insert items with modifiers
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price, modifiers, notes) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        $modifiers = !empty($item['modifiers']) ? json_encode(array_column($item['modifiers'], 'name')) : null;
        // Notes is already a string, store as JSON array with single element or plain text
        $notes = !empty($item['notes']) ? $item['notes'] : null;
        $stmt->execute([$orderId, (int)$item['id'], (int)($item['quantity'] ?? 1), floatval($item['price'] ?? 0), $modifiers, $notes]);
    }

    // Update table
    $pdo->prepare("UPDATE tables SET status = 'occupied' WHERE id = ?")->execute([$tableId]);
    $pdo->commit();

    ob_end_clean();
    echo json_encode(['success' => true, 'order_id' => $orderId, 'ticket_id' => $ticketId]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
