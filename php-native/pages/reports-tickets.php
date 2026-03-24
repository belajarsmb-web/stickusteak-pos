<?php
/**
 * Tickets Report - Recall & View All Tickets
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
$searchTicketId = $_GET['ticket_id'] ?? '';
$searchTable = $_GET['table_id'] ?? '';
$searchStatus = $_GET['status'] ?? '';
$searchDate = $_GET['date'] ?? date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets Report - Recall Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }
        .filter-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
            border: 1px solid #D4AF37;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .ticket-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
            border: 2px solid #D4AF37;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }
        .ticket-header {
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .ticket-number {
            color: #D4AF37;
            font-weight: 700;
            font-size: 1.3rem;
        }
        .order-item {
            background: rgba(212, 175, 55, 0.05);
            border-left: 3px solid #D4AF37;
            padding: 10px;
            margin: 10px 0;
        }
        .btn-gold {
            background: linear-gradient(135deg, #D4AF37, #AA8C2C);
            color: #000;
            border: none;
        }
        .status-open { color: #28a745; }
        .status-paid { color: #ffc107; }
        .status-closed { color: #dc3545; }
        .form-control, .form-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid #D4AF37;
            color: #fff;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.1);
            border-color: #D4AF37;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1><i class="bi bi-receipt me-2"></i>Tickets Report - Recall Tickets</h1>
                <p class="text-muted">Search and view all tickets by Ticket ID, Table, Date, or Status</p>
            </div>
            <div class="col-auto">
                <a href="/php-native/pages/dashboard.php" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <h4 class="mb-3"><i class="bi bi-funnel me-2"></i>Filter Tickets</h4>
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Ticket ID</label>
                    <input type="text" class="form-control" name="ticket_id" placeholder="e.g., TKT-20260320-0001" value="<?php echo htmlspecialchars($searchTicketId); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Table ID</label>
                    <input type="number" class="form-control" name="table_id" placeholder="e.g., 14" value="<?php echo htmlspecialchars($searchTable); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All</option>
                        <option value="open" <?php echo $searchStatus === 'open' ? 'selected' : ''; ?>>Open</option>
                        <option value="paid" <?php echo $searchStatus === 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="closed" <?php echo $searchStatus === 'closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($searchDate); ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-gold w-100">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Results -->
        <div id="ticketsContainer">
            <?php
            require_once __DIR__ . '/../config/database.php';
            
            try {
                $pdo = getDbConnection();
                
                // Build query
                $sql = "SELECT t.*, 
                        (SELECT COUNT(*) FROM orders o WHERE o.ticket_id = t.id) as orders_count,
                        (SELECT SUM(total_amount) FROM orders o WHERE o.ticket_id = t.id) as total_amount
                        FROM tickets t
                        WHERE 1=1";
                
                $params = [];
                
                if ($searchTicketId) {
                    $sql .= " AND t.ticket_number LIKE ?";
                    $params[] = "%$searchTicketId%";
                }
                
                if ($searchTable) {
                    $sql .= " AND t.table_id = ?";
                    $params[] = $searchTable;
                }
                
                if ($searchStatus) {
                    $sql .= " AND t.status = ?";
                    $params[] = $searchStatus;
                }
                
                if ($searchDate) {
                    $sql .= " AND DATE(t.opened_at) = ?";
                    $params[] = $searchDate;
                }
                
                $sql .= " ORDER BY t.opened_at DESC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($tickets) > 0) {
                    echo "<div class='alert alert-success'>Found <strong>" . count($tickets) . "</strong> ticket(s)</div>";
                    
                    foreach ($tickets as $ticket) {
                        $statusClass = $ticket['status'] === 'open' ? 'status-open' : 
                                     ($ticket['status'] === 'paid' ? 'status-paid' : 'status-closed');
                        
                        // Get orders in this ticket
                        $ordersStmt = $pdo->prepare("
                            SELECT o.* FROM orders o WHERE o.ticket_id = ? ORDER BY o.created_at ASC
                        ");
                        $ordersStmt->execute([$ticket['id']]);
                        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        echo "<div class='ticket-card'>";
                        echo "<div class='ticket-header'>";
                        echo "<div class='d-flex justify-content-between align-items-center'>";
                        echo "<div>";
                        echo "<div class='ticket-number'>🎫 {$ticket['ticket_number']}</div>";
                        echo "<div class='text-muted'>Opened: " . date('d/m/Y H:i', strtotime($ticket['opened_at'])) . "</div>";
                        echo "</div>";
                        echo "<div>";
                        echo "<span class='badge {$statusClass} fs-6'>" . strtoupper($ticket['status']) . "</span>";
                        echo "</div>";
                        echo "</div>";
                        
                        if ($ticket['customer_name']) {
                            echo "<div class='mt-2'>";
                            echo "<strong>👤 Customer:</strong> " . htmlspecialchars($ticket['customer_name']);
                            if ($ticket['customer_phone']) {
                                echo " | 📱 " . htmlspecialchars($ticket['customer_phone']);
                            }
                            echo "</div>";
                        }
                        
                        echo "<div class='mt-2'>";
                        echo "<strong>🍽️ Table:</strong> {$ticket['table_id']} | ";
                        echo "<strong>📋 Orders:</strong> {$ticket['orders_count']} | ";
                        echo "<strong>💰 Total:</strong> Rp " . number_format($ticket['total_amount'] ?? 0, 0, ',', '.');
                        echo "</div>";
                        echo "</div>";
                        
                        // Display orders
                        if (count($orders) > 0) {
                            echo "<div class='mt-3'>";
                            echo "<h5 class='mb-3'>📋 Orders in this Ticket</h5>";
                            
                            foreach ($orders as $order) {
                                // Get items for this order
                                $itemsStmt = $pdo->prepare("
                                    SELECT oi.*, m.name as item_name
                                    FROM order_items oi
                                    LEFT JOIN menu_items m ON oi.menu_item_id = m.id
                                    WHERE oi.order_id = ?
                                ");
                                $itemsStmt->execute([$order['id']]);
                                $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                echo "<div class='order-item'>";
                                echo "<div class='d-flex justify-content-between'>";
                                echo "<strong>Order #{$order['id']}</strong>";
                                echo "<span class='text-muted'>" . date('H:i', strtotime($order['created_at'])) . "</span>";
                                echo "</div>";
                                
                                foreach ($items as $item) {
                                    $modifiers = $item['modifiers'] ? json_decode($item['modifiers'], true) : [];
                                    $notes = $item['notes'] ? json_decode($item['notes'], true) : [];
                                    $allDetails = array_merge($modifiers, $notes);
                                    
                                    echo "<div class='mt-2'>";
                                    echo "<div>• {$item['quantity']}x {$item['item_name']}</div>";
                                    if (count($allDetails) > 0) {
                                        echo "<div class='text-muted small'>";
                                        foreach ($allDetails as $detail) {
                                            echo "<span class='badge bg-secondary me-1'>{$detail}</span>";
                                        }
                                        echo "</div>";
                                    }
                                    echo "</div>";
                                }
                                
                                echo "<div class='text-end mt-2'>";
                                echo "<strong>Rp " . number_format($order['total_amount'], 0, ',', '.') . "</strong>";
                                echo "</div>";
                                echo "</div>";
                            }
                            
                            echo "</div>";
                        }
                        
                        echo "</div>";
                    }
                } else {
                    echo "<div class='alert alert-warning'>No tickets found. Try different filters.</div>";
                }
                
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
            }
            ?>
        </div>
    </div>

    <script>
        // Auto-submit on date change
        document.querySelector('input[name="date"]').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>
