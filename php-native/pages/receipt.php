<?php
/**
 * Stickusteak POS - Receipt/Invoice Print Page
 * Supports thermal printer (58mm/80mm) and PDF
 */

require_once __DIR__ . '/../config/database.php';

$orderId = $_GET['order_id'] ?? 0;

if (!$orderId) {
    die('Order ID required');
}

try {
    $pdo = getDbConnection();
    
    // Get default receipt template
    $stmt = $pdo->prepare("SELECT * FROM receipt_templates WHERE is_default = 1 OR is_active = 1 LIMIT 1");
    $stmt->execute();
    $template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get order details - include customer info, ticket, and payment
    $stmt = $pdo->prepare("
        SELECT o.*, 
               t.table_number as table_name, 
               pm.name as payment_method_name,
               tk.ticket_number
        FROM orders o
        LEFT JOIN tables t ON o.table_id = t.id
        LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
        LEFT JOIN tickets tk ON o.ticket_id = tk.id
        WHERE o.id = :id
    ");
    $stmt->execute(['id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If payment_method_name is still empty, try to get from payments table
    if (empty($order['payment_method_name']) && !empty($order['id'])) {
        $payStmt = $pdo->prepare("
            SELECT pm.name as payment_method_name 
            FROM payments p 
            LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id 
            WHERE p.order_id = ? 
            ORDER BY p.id DESC LIMIT 1
        ");
        $payStmt->execute([$orderId]);
        $payRow = $payStmt->fetch(PDO::FETCH_ASSOC);
        if ($payRow && !empty($payRow['payment_method_name'])) {
            $order['payment_method_name'] = $payRow['payment_method_name'];
        }
    }
    
    // Final fallback: show 'Cash' if no payment method found (for backward compatibility)
    if (empty($order['payment_method_name'])) {
        $order['payment_method_name'] = 'Cash';
    }
    
    // Ensure customer info is available
    if (!$order) {
        die('Order not found');
    }
    
    // Get order items
    $stmt = $pdo->prepare("
        SELECT oi.*, m.name as item_name
        FROM order_items oi
        LEFT JOIN menu_items m ON oi.menu_item_id = m.id
        WHERE oi.order_id = :id
        ORDER BY oi.id
    ");
    $stmt->execute(['id' => $orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get system settings
    $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ('tax_percentage', 'service_charge_percentage', 'tax_enabled', 'service_enabled', 'restaurant_name', 'restaurant_address', 'restaurant_phone')");
    $stmt->execute();
    $settingsRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $settings = [];
    foreach ($settingsRows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    // Use template settings or fallback to system settings
    $restaurantName = $template && !empty($template['header_text']) ?
        explode("\n", $template['header_text'])[0] :
        ($settings['restaurant_name'] ?? 'Stickusteak');
    $restaurantAddress = $template && !empty($template['header_text']) ?
        implode("\n", array_slice(explode("\n", $template['header_text']), 1)) :
        ($settings['restaurant_address'] ?? '');
    $restaurantPhone = $settings['restaurant_phone'] ?? '';
    $showLogo = $template ? $template['show_logo'] : 1;
    $logoPath = $template && !empty($template['logo_path']) ? $template['logo_path'] : null;
    $showTax = $template ? $template['show_tax_breakdown'] : 1;
    $showService = $template ? $template['show_service_charge'] : 1;
    $showQR = $template ? $template['show_qr_code'] : 0;
    $qrText = $template ? $template['qr_code_text'] : '';
    $fontSize = $template ? $template['font_size'] : 'medium';
    $paperSize = $template ? $template['paper_size'] : '80mm';
    
    // Customer info settings from template
    $showCustomerInfo = isset($template['show_customer_info']) ? $template['show_customer_info'] : 1;
    $customerNameLabel = $template && !empty($template['customer_name_label']) ? $template['customer_name_label'] : 'Customer Name';
    $customerPhoneLabel = $template && !empty($template['customer_phone_label']) ? $template['customer_phone_label'] : 'Phone Number';
    
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo $orderId; ?> - <?php echo $restaurantName; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: <?php echo $paperSize == '58mm' ? '58mm' : '80mm'; ?> auto;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
                font-size: <?php echo $fontSize == 'small' ? '10px' : ($fontSize == 'large' ? '14px' : '12px'); ?>;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                width: 100%;
                padding: 10px;
            }
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
        }
        
        .receipt-container {
            max-width: <?php echo $paperSize == '58mm' ? '280px' : '380px'; ?>;
            margin: 20px auto;
            background: white;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-size: <?php echo $fontSize == 'small' ? '10px' : ($fontSize == 'large' ? '14px' : '12px'); ?>;
        }
        
        .receipt-logo {
            max-width: 150px;
            max-height: 80px;
            margin: 0 auto 10px;
            display: block;
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .receipt-header h3 {
            font-size: <?php echo $fontSize == 'small' ? '14px' : ($fontSize == 'large' ? '18px' : '16px'); ?>;
            margin: 0;
            font-weight: bold;
        }
        
        .receipt-header p {
            font-size: <?php echo $fontSize == 'small' ? '9px' : ($fontSize == 'large' ? '12px' : '11px'); ?>;
            margin: 2px 0;
            white-space: pre-line;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 11px;
        }
        
        .receipt-items {
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .receipt-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .receipt-item-name {
            flex: 1;
            font-size: 11px;
        }
        
        .receipt-notes {
            font-size: 9px;
            color: #666;
            display: inline;
            margin-left: 5px;
            padding: 2px 6px;
            background: #f0f0f0;
            border-radius: 3px;
        }
        
        .receipt-item-price {
            text-align: right;
        }
        
        .receipt-item-qty {
            margin: 0 10px;
        }
        
        .receipt-totals {
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .receipt-total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }
        
        .receipt-total-row.grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .barcode {
            text-align: center;
            margin: 10px 0;
            font-family: 'Libre Barcode 39', cursive;
            font-size: 24px;
        }
        
        .receipt-footer {
            text-align: center;
            font-size: 11px;
            padding-top: 10px;
        }
        
        .receipt-footer p {
            margin: 2px 0;
        }
        
        .action-buttons {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        .btn-print {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
</head>
<body>
    <div class="receipt-container">
        <!-- Logo: Show if logo_path exists OR always show default emoji -->
        <div style="text-align: center; margin-bottom: 10px; min-height: 50px; line-height: 50px;">
            <?php if (!empty($logoPath)): ?>
                <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="Logo" style="max-width: 150px; max-height: 80px;">
            <?php else: ?>
                <span style="font-size: 48px; display: inline-block;">🍽️</span>
            <?php endif; ?>
        </div>
        
        <!-- Header -->
        <div class="receipt-header">
            <h3><?php echo htmlspecialchars($restaurantName); ?></h3>
            <?php if ($restaurantAddress): ?>
                <p><?php echo nl2br(htmlspecialchars($restaurantAddress)); ?></p>
            <?php endif; ?>
            <?php if ($restaurantPhone): ?>
                <p>Telp: <?php echo htmlspecialchars($restaurantPhone); ?></p>
            <?php endif; ?>
        </div>

        <!-- Ticket Details - ALWAYS SHOW ON EVERY RECEIPT -->
        <div style="border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
            <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; vertical-align: top; padding-right: 5px;">
                        <div style="font-weight: bold; margin-bottom: 5px; font-size: 10px; text-transform: uppercase;">TICKET DETAILS</div>
                        <div style="margin-bottom: 3px;"><strong>Nomor Ticket:</strong> <?php echo htmlspecialchars($order['ticket_number'] ?? 'N/A'); ?></div>
                        <div style="margin-bottom: 3px;"><strong>Nomor Meja:</strong> <?php echo htmlspecialchars($order['table_name'] ?? '-'); ?></div>
                        <div style="margin-bottom: 3px;"><strong>Tanggal:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></div>
                        <div style="margin-bottom: 3px;"><strong>Kasir:</strong> <?php echo htmlspecialchars($_GET['cashier'] ?? 'Staff'); ?></div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding-left: 5px; border-left: 1px dashed #ccc; padding-left: 10px;">
                        <div style="font-weight: bold; margin-bottom: 5px; font-size: 10px; text-transform: uppercase;">CUSTOMER INFORMATION</div>
                        <?php if (!empty($order['customer_name']) || !empty($order['customer_phone'])): ?>
                            <div style="margin-bottom: 3px;"><strong>Nama:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></div>
                            <div><strong>Telepon:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></div>
                        <?php else: ?>
                            <div style="color: #999; font-style: italic; font-size: 10px;">Walk-in Customer</div>
                        <?php endif; ?>
                        <div style="margin-top: 8px; padding-top: 5px; border-top: 1px dotted #ccc;">
                            <div style="margin-bottom: 3px;"><strong>Pembayaran:</strong> <?php echo htmlspecialchars($order['payment_method_name'] ?? 'Cash'); ?></div>
                            <div><strong>Status:</strong> <?php echo strtoupper(str_replace('_', ' ', $order['status'])); ?></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Pesanan / Items -->
        <div class="receipt-items">
            <?php 
            $validSubtotal = 0;
            foreach ($items as $item): 
                $itemTotal = $item['price'] * $item['quantity'];
                // Only add to subtotal if NOT voided
                if (!$item['is_voided']) {
                    $validSubtotal += $itemTotal;
                }
            ?>
                <div class="receipt-item" style="<?php echo $item['is_voided'] ? 'opacity: 0.5; text-decoration: line-through;' : ''; ?>">
                    <div style="width: 100%;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                            <div class="receipt-item-name" style="flex: 1;">
                                <?php echo htmlspecialchars($item['item_name']); ?>
                                <?php if ($item['is_voided']): ?>
                                    <span class="receipt-notes" style="background: #ffe6e6; color: #dc3545; margin-left: 5px;">[VOIDED]</span>
                                <?php endif; ?>
                            </div>
                            <div class="receipt-item-qty" style="margin: 0 10px;">x<?php echo $item['quantity']; ?></div>
                            <div class="receipt-item-price" style="text-align: right;">
                                <?php if ($item['is_voided']): ?>
                                    <span style="color: #dc3545;">VOID</span>
                                <?php else: ?>
                                    Rp <?php echo number_format($itemTotal, 0, ',', '.'); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Notes and Modifiers - Displayed BELOW item name -->
                        <?php
                        // Collect all notes/modifiers text
                        $displayTexts = [];

                        // Parse notes field (can be plain text or JSON array)
                        if (!empty($item['notes'])) {
                            if (is_string($item['notes'])) {
                                // Check if it's JSON encoded
                                $decoded = @json_decode($item['notes'], true);
                                if (is_array($decoded)) {
                                    foreach ($decoded as $note) {
                                        if (is_string($note) && !empty($note)) {
                                            $displayTexts[] = $note;
                                        }
                                    }
                                } else {
                                    // Plain text notes
                                    $displayTexts[] = $item['notes'];
                                }
                            }
                        }

                        // Parse modifiers field
                        if (!empty($item['modifiers'])) {
                            $decoded = @json_decode($item['modifiers'], true);
                            if (is_array($decoded)) {
                                foreach ($decoded as $mod) {
                                    if (is_string($mod) && !empty($mod)) {
                                        $displayTexts[] = $mod;
                                    } elseif (is_array($mod) && !empty($mod['name'])) {
                                        $displayTexts[] = $mod['name'];
                                    }
                                }
                            }
                        }

                        // Display notes/modifiers below item name (only if not voided)
                        if (!empty($displayTexts) && !$item['is_voided']):
                        ?>
                            <div style="font-size: 9px; color: #666; margin-top: 3px; padding-left: 5px;">
                                <span style="font-style: italic;">[<?php echo htmlspecialchars(implode(', ', $displayTexts)); ?>]</span>
                            </div>
                        <?php endif; ?>

                        <?php if ($item['is_voided'] && !empty($item['void_reason_text'])): ?>
                            <div style="font-size: 8px; color: #dc3545; margin-top: 3px; padding-left: 5px;">
                                <span style="font-style: italic;">Reason: <?php echo htmlspecialchars($item['void_reason_text']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Totals - USING STORED VALUES FROM ORDER -->
        <div class="receipt-totals">
            <?php
            // Use stored values from order
            $subtotal = floatval($order['sub_total'] ?? $validSubtotal);
            $serviceCharge = floatval($order['service_charge'] ?? 0);
            $taxAmount = floatval($order['tax_amount'] ?? 0);
            $total = floatval($order['total_amount'] ?? ($subtotal + $serviceCharge + $taxAmount));
            $paidAmount = floatval($order['paid_amount'] ?? $total);
            $changeAmount = $paidAmount > $total ? $paidAmount - $total : 0;
            ?>

            <div class="receipt-total-row">
                <span>Subtotal:</span>
                <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
            </div>
            <?php if ($serviceCharge > 0): ?>
            <div class="receipt-total-row">
                <span>Service Charge:</span>
                <span>Rp <?php echo number_format($serviceCharge, 0, ',', '.'); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($taxAmount > 0): ?>
            <div class="receipt-total-row">
                <span>PPN:</span>
                <span>Rp <?php echo number_format($taxAmount, 0, ',', '.'); ?></span>
            </div>
            <?php endif; ?>
            <div class="receipt-total-row grand-total">
                <span>TOTAL:</span>
                <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
            </div>
            <?php if ($order['status'] === 'paid'): ?>
            <div class="receipt-total-row">
                <span>Bayar:</span>
                <span>Rp <?php echo number_format($paidAmount, 0, ',', '.'); ?></span>
            </div>
            <?php if ($changeAmount > 0): ?>
            <div class="receipt-total-row">
                <span>Kembalian:</span>
                <span>Rp <?php echo number_format($changeAmount, 0, ',', '.'); ?></span>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            
            <!-- QR Code (if enabled) -->
            <?php if ($showQR && !empty($qrText)): ?>
            <div style="text-align: center; margin: 15px 0;">
                <div style="background: #000; width: 100px; height: 100px; margin: 0 auto; display: flex; align-items: center; justify-content: center; color: #fff;">
                    [QR Code]
                </div>
                <small><?php echo htmlspecialchars($qrText); ?></small>
            </div>
            <?php endif; ?>

            <!-- Footer from Template -->
            <?php if ($template && !empty($template['footer_text'])): ?>
            <div style="margin-top: 15px; padding-top: 10px; border-top: 1px dashed #999; text-align: center; font-size: <?php echo $fontSize == 'small' ? '9px' : ($fontSize == 'large' ? '12px' : '11px'); ?>; white-space: pre-line;">
                <?php echo nl2br(htmlspecialchars($template['footer_text'])); ?>
            </div>
            <?php else: ?>
            <div class="receipt-footer">
                <p>Terima Kasih atas Kunjungan Anda!</p>
                <p>Powered by Stickusteak POS</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons no-print">
        <button class="btn btn-primary btn-print" onclick="window.print()" title="Print Receipt">
            <i class="bi bi-printer"></i>
        </button>
        <button class="btn btn-success btn-print" onclick="downloadPDF()" title="Download PDF">
            <i class="bi bi-file-earmark-pdf"></i>
        </button>
        <button class="btn btn-secondary btn-print" onclick="window.close()" title="Close">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        // Auto print on load (optional)
        <?php if (isset($_GET['autoprint']) && $_GET['autoprint'] == '1'): ?>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
        <?php endif; ?>

        // Download PDF
        function downloadPDF() {
            const element = document.querySelector('.receipt-container');
            const opt = {
                margin: 0,
                filename: 'receipt-<?php echo $orderId; ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: [80, 200], orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
