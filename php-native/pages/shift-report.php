<?php
/**
 * Shift Report - Detailed report after closing shift
 * Shows: Shift info, Sales summary, Item sales detail, Cash reconciliation
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

$shiftId = $_GET['shift_id'] ?? 0;

if (!$shiftId) {
    die('Shift ID required');
}

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDbConnection();
    
    // Get shift details
    $stmt = $pdo->prepare("SELECT * FROM shifts WHERE id = ?");
    $stmt->execute([$shiftId]);
    $shift = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$shift) {
        die('Shift not found');
    }
    
    // Get shift statistics
    $shiftStart = $shift['created_at'] ?? $shift['opened_at'] ?? date('Y-m-d H:i:s');
    $shiftEnd = $shift['closed_at'] ?? date('Y-m-d H:i:s');
    
    // Handle null values
    if (empty($shiftStart)) {
        $shiftStart = date('Y-m-d H:i:s');
    }
    if (empty($shiftEnd)) {
        $shiftEnd = date('Y-m-d H:i:s');
    }
    
    $statsStmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT o.id) as total_orders,
            COALESCE(SUM(o.total_amount), 0) as total_sales,
            COALESCE(SUM(CASE WHEN o.status IN ('paid', 'completed') THEN o.total_amount ELSE 0 END), 0) as total_paid,
            COALESCE(SUM(CASE WHEN o.status = 'pending' THEN o.total_amount ELSE 0 END), 0) as pending_amount
        FROM orders o
        WHERE o.created_at BETWEEN :start AND :end
        AND o.status NOT IN ('cancelled', 'voided')
    ");
    $statsStmt->execute(['start' => $shiftStart, 'end' => $shiftEnd]);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    // Get payment method breakdown
    $paymentStmt = $pdo->prepare("
        SELECT 
            pm.name as payment_method,
            COUNT(p.id) as count,
            COALESCE(SUM(p.amount), 0) as amount
        FROM payments p
        LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
        LEFT JOIN orders o ON p.order_id = o.id
        WHERE o.created_at BETWEEN :start AND :end
        GROUP BY pm.name
        ORDER BY amount DESC
    ");
    $paymentStmt->execute(['start' => $shiftStart, 'end' => $shiftEnd]);
    $payments = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get item sales detail
    $itemsStmt = $pdo->prepare("
        SELECT 
            m.name as item_name,
            SUM(oi.quantity) as qty_sold,
            COALESCE(SUM(oi.quantity * oi.price), 0) as total_amount,
            COUNT(DISTINCT oi.order_id) as order_count
        FROM order_items oi
        LEFT JOIN menu_items m ON oi.menu_item_id = m.id
        LEFT JOIN orders o ON oi.order_id = o.id
        WHERE o.created_at BETWEEN :start AND :end
        AND oi.is_voided = 0
        GROUP BY m.name
        ORDER BY total_amount DESC
    ");
    $itemsStmt->execute(['start' => $shiftStart, 'end' => $shiftEnd]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate cash variance
    $openingBalance = floatval($shift['opening_balance'] ?? 0);
    $closingBalance = floatval($shift['closing_balance'] ?? 0);
    $expectedCash = $openingBalance + $stats['total_paid'];
    $variance = $closingBalance - $expectedCash;
    
    // Get cashier name
    $cashierName = 'Unknown';
    if (!empty($shift['user_id'])) {
        $userStmt = $pdo->prepare("SELECT username, full_name FROM users WHERE id = ?");
        $userStmt->execute([$shift['user_id']]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        $cashierName = $user['full_name'] ?? $user['username'] ?? 'Unknown';
    } elseif (!empty($shift['opened_by_name'])) {
        $cashierName = $shift['opened_by_name'];
    }
    
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Report #<?php echo $shiftId; ?> - Stickusteak POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .report-container { box-shadow: none !important; border: none !important; }
        }
        
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .report-container {
            max-width: 210mm;
            margin: 30px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .report-header {
            text-align: center;
            border-bottom: 3px solid #D4AF37;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .report-title {
            color: #D4AF37;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-subtitle {
            color: #666;
            font-size: 16px;
        }
        
        .section-title {
            background: linear-gradient(135deg, #D4AF37, #AA8C2C);
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            margin: 30px 0 15px 0;
            border-radius: 5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #D4AF37;
        }
        
        .info-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            background: linear-gradient(135deg, #D4AF37, #AA8C2C);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            opacity: 0.9;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background: #D4AF37;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .variance-positive {
            color: #28a745;
            font-weight: bold;
        }
        
        .variance-negative {
            color: #dc3545;
            font-weight: bold;
        }
        
        .action-buttons {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        .btn-action {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="report-title">🍽️ STICKUSTEAK POS</div>
            <div class="report-subtitle">Shift Closing Report</div>
        </div>
        
        <!-- Shift Information -->
        <div class="section-title">📋 SHIFT INFORMATION</div>
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Shift Number</div>
                <div class="info-value">#<?php echo $shiftId; ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Cashier</div>
                <div class="info-value"><?php echo htmlspecialchars($cashierName); ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Opened At</div>
                <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($shiftStart)); ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Closed At</div>
                <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($shiftEnd)); ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Duration</div>
                <div class="info-value">
                    <?php
                    try {
                        $start = new DateTime($shiftStart);
                        $end = new DateTime($shiftEnd);
                        $diff = $start->diff($end);
                        echo sprintf('%dh %dm', $diff->h, $diff->i + ($diff->days * 60));
                    } catch (Exception $e) {
                        echo 'N/A';
                    }
                    ?>
                </div>
            </div>
            <div class="info-box">
                <div class="info-label">Status</div>
                <div class="info-value" style="color: #28a745;">CLOSED</div>
            </div>
        </div>
        
        <!-- Sales Summary -->
        <div class="section-title">💰 SALES SUMMARY</div>
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">Rp <?php echo number_format($stats['total_sales'], 0, ',', '.'); ?></div>
                <div class="stat-label">Total Sales</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">Rp <?php echo number_format($stats['total_paid'], 0, ',', '.'); ?></div>
                <div class="stat-label">Total Paid</div>
            </div>
            <div class="stat-box" style="background: linear-gradient(135deg, #6c757d, #495057);">
                <div class="stat-value">Rp <?php echo number_format($stats['pending_amount'], 0, ',', '.'); ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        
        <!-- Payment Method Breakdown -->
        <div class="section-title">💳 PAYMENT METHOD BREAKDOWN</div>
        <table>
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th class="text-center">Count</th>
                    <th class="text-right">Amount</th>
                    <th class="text-right">%</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalPayment = array_sum(array_column($payments, 'amount'));
                foreach ($payments as $payment):
                    $percentage = $totalPayment > 0 ? ($payment['amount'] / $totalPayment * 100) : 0;
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($payment['payment_method']); ?></strong></td>
                    <td class="text-center"><?php echo $payment['count']; ?></td>
                    <td class="text-right"><strong>Rp <?php echo number_format($payment['amount'], 0, ',', '.'); ?></strong></td>
                    <td class="text-right"><?php echo number_format($percentage, 1); ?>%</td>
                </tr>
                <?php endforeach; ?>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td>TOTAL</td>
                    <td class="text-center"><?php echo array_sum(array_column($payments, 'count')); ?></td>
                    <td class="text-right">Rp <?php echo number_format($totalPayment, 0, ',', '.'); ?></td>
                    <td class="text-right">100%</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Item Sales Detail -->
        <div class="section-title">🍽️ ITEM SALES DETAIL</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item Name</th>
                    <th class="text-center">Qty Sold</th>
                    <th class="text-center">Order Count</th>
                    <th class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($items as $item):
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><strong><?php echo htmlspecialchars($item['item_name']); ?></strong></td>
                    <td class="text-center"><?php echo $item['qty_sold']; ?></td>
                    <td class="text-center"><?php echo $item['order_count']; ?></td>
                    <td class="text-right"><strong>Rp <?php echo number_format($item['total_amount'], 0, ',', '.'); ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td colspan="4">TOTAL</td>
                    <td class="text-right">Rp <?php echo number_format(array_sum(array_column($items, 'total_amount')), 0, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Cash Reconciliation -->
        <div class="section-title">💵 CASH RECONCILIATION</div>
        <div class="info-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="info-box">
                <div class="info-label">Opening Balance</div>
                <div class="info-value">Rp <?php echo number_format($openingBalance, 0, ',', '.'); ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Expected Cash</div>
                <div class="info-value">Rp <?php echo number_format($expectedCash, 0, ',', '.'); ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Actual Cash</div>
                <div class="info-value">Rp <?php echo number_format($closingBalance, 0, ',', '.'); ?></div>
            </div>
        </div>
        
        <div class="info-box" style="margin-top: 15px; <?php echo $variance >= 0 ? 'border-left-color: #28a745;' : 'border-left-color: #dc3545;'; ?>">
            <div class="info-label">Variance (Over/Short)</div>
            <div class="info-value <?php echo $variance >= 0 ? 'variance-positive' : 'variance-negative'; ?>">
                <?php echo $variance >= 0 ? '+' : ''; ?>Rp <?php echo number_format(abs($variance), 0, ',', '.'); ?>
                <?php echo $variance >= 0 ? '(OVER)' : '(SHORT)'; ?>
            </div>
            <?php if (!empty($shift['notes'])): ?>
            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ccc;">
                <div class="info-label">Notes</div>
                <div style="font-size: 14px; color: #666;"><?php echo nl2br(htmlspecialchars($shift['notes'])); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #D4AF37; text-align: center; color: #666; font-size: 12px;">
            <p>Generated on <?php echo date('d/m/Y H:i:s'); ?></p>
            <p>Stickusteak POS System</p>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="action-buttons no-print">
        <button class="btn btn-secondary btn-action" onclick="window.close()">
            <i class="bi bi-x-circle me-2"></i>Close
        </button>
        <button class="btn btn-primary btn-action" onclick="window.print()">
            <i class="bi bi-printer me-2"></i>Print Report
        </button>
        <button class="btn btn-success btn-action" onclick="downloadPDF()">
            <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
        </button>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.querySelector('.report-container');
            const opt = {
                margin: 10,
                filename: 'Shift_Report_<?php echo $shiftId; ?>_<?php echo date("Ymd_His"); ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
        
        // Auto-print on load (optional)
        // window.onload = function() { setTimeout(function() { window.print(); }, 500); };
    </script>
</body>
</html>
