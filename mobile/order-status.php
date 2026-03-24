<?php
/**
 * Stickusteak POS - Mobile Order Status Page
 * Track order status in real-time
 */

$token = $_GET['token'] ?? '';
$page_title = "Order Status";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Order Status - Stickusteak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold-primary: #D4AF37;
            --gold-light: #F4DF89;
            --gold-dark: #AA8C2C;
            --black-primary: #0a0a0a;
            --black-secondary: #1a1a1a;
        }
        
        * { font-family: 'Poppins', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', serif; }
        
        body {
            background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 100%);
            color: #fff;
            padding: 20px;
        }
        
        .status-header {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, rgba(212,175,55,0.1) 0%, transparent 100%);
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .order-number {
            font-size: 3rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            color: var(--black-primary);
        }
        
        .progress-timeline {
            position: relative;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        
        .progress-line {
            position: absolute;
            top: 25px;
            left: 20px;
            right: 20px;
            height: 3px;
            background: rgba(212,175,55,0.2);
            z-index: 1;
        }
        
        .progress-fill {
            position: absolute;
            top: 25px;
            left: 20px;
            height: 3px;
            background: linear-gradient(90deg, var(--gold-dark), var(--gold-primary));
            z-index: 2;
            transition: width 0.5s ease;
        }
        
        .timeline-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 3;
        }
        
        .step {
            text-align: center;
            flex: 1;
        }
        
        .step-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 10px;
            background: rgba(212,175,55,0.2);
            border: 3px solid rgba(212,175,55,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: rgba(255,255,255,0.4);
            transition: all 0.3s;
        }
        
        .step.completed .step-icon {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            border-color: var(--gold-primary);
            color: var(--black-primary);
        }
        
        .step.active .step-icon {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            border-color: var(--gold-primary);
            color: var(--black-primary);
            box-shadow: 0 0 20px rgba(212,175,55,0.5);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .step-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
        }
        
        .step.completed .step-label,
        .step.active .step-label {
            color: var(--gold-light);
            font-weight: 600;
        }
        
        .order-details {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(212,175,55,0.1);
        }
        
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .detail-label {
            color: rgba(255,255,255,0.6);
        }
        
        .detail-value {
            font-weight: 600;
            color: var(--gold-light);
        }
        
        .items-list {
            margin-top: 15px;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed rgba(212,175,55,0.1);
        }
        
        .item-row:last-child {
            border-bottom: none;
        }
        
        .timer-display {
            text-align: center;
            padding: 20px;
            background: rgba(212,175,55,0.1);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .timer-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--gold-primary);
        }
        
        .timer-label {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Status Header -->
    <div class="status-header">
        <div class="order-number" id="orderNumber">#--</div>
        <div class="status-badge" id="statusBadge">Loading...</div>
    </div>

    <!-- Progress Timeline -->
    <div class="progress-timeline">
        <div class="progress-line"></div>
        <div class="progress-fill" id="progressFill" style="width: 0%"></div>
        
        <div class="timeline-steps">
            <div class="step" id="step1">
                <div class="step-icon"><i class="bi bi-receipt"></i></div>
                <div class="step-label">Order Received</div>
            </div>
            <div class="step" id="step2">
                <div class="step-icon"><i class="bi bi-fire"></i></div>
                <div class="step-label">Preparing</div>
            </div>
            <div class="step" id="step3">
                <div class="step-icon"><i class="bi bi-check-circle"></i></div>
                <div class="step-label">Ready</div>
            </div>
            <div class="step" id="step4">
                <div class="step-icon"><i class="bi bi-check-lg"></i></div>
                <div class="step-label">Completed</div>
            </div>
        </div>
    </div>

    <!-- Timer -->
    <div class="timer-display" id="timerDisplay">
        <div class="timer-value" id="timerValue">--:--</div>
        <div class="timer-label">Estimated waiting time</div>
    </div>

    <!-- Order Details -->
    <div class="order-details">
        <h4 style="color: var(--gold-light); margin-bottom: 15px;">
            <i class="bi bi-receipt me-2"></i>Order Details
        </h4>
        <div class="detail-row">
            <span class="detail-label">Table</span>
            <span class="detail-value" id="tableInfo">--</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Order Time</span>
            <span class="detail-value" id="orderTime">--</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Total Amount</span>
            <span class="detail-value" id="totalAmount">Rp --</span>
        </div>
        
        <div class="items-list" id="itemsList"></div>
    </div>

    <script>
        let orderToken = '<?php echo htmlspecialchars($token); ?>';
        let refreshInterval;

        // Load order status on page load
        document.addEventListener('DOMContentLoaded', function() {
            // If no token in URL, try to get from localStorage
            if (!orderToken || orderToken === '') {
                const savedToken = localStorage.getItem('mobileOrderToken');
                if (savedToken) {
                    orderToken = savedToken;
                    console.log('Using saved order token from localStorage');
                } else {
                    // Try to get from table ID
                    const urlParams = new URLSearchParams(window.location.search);
                    const tableId = urlParams.get('table');
                    if (tableId) {
                        orderToken = tableId;
                        console.log('Using table ID as token');
                    }
                }
            }
            
            if (orderToken) {
                loadOrderStatus();
                // Auto-refresh every 10 seconds
                refreshInterval = setInterval(loadOrderStatus, 10000);
            } else {
                document.getElementById('statusBadge').textContent = 'No Order Found';
                document.getElementById('orderNumber').textContent = '--';
            }
        });

        // Load order status
        async function loadOrderStatus() {
            try {
                const response = await fetch('/php-native/api/mobile/order-status.php?token=' + orderToken);
                const data = await response.json();
                
                if (data.success) {
                    updateOrderDisplay(data.order);
                    updateProgress(data.status_progress);
                } else {
                    document.getElementById('statusBadge').textContent = 'Order Not Found';
                    clearInterval(refreshInterval);
                }
            } catch (error) {
                console.error('Status load error:', error);
            }
        }

        // Update order display
        function updateOrderDisplay(order) {
            document.getElementById('orderNumber').textContent = '#' + order.order_number;
            document.getElementById('statusBadge').textContent = order.status_label;
            document.getElementById('tableInfo').textContent = order.table_name;
            document.getElementById('orderTime').textContent = order.created_at;
            document.getElementById('totalAmount').textContent = 'Rp ' + parseFloat(order.total_amount).toLocaleString('id-ID');
            
            // Update timer
            const remaining = order.estimated_remaining_minutes;
            document.getElementById('timerValue').textContent = remaining > 0 ? remaining + ' min' : 'Any time now!';
            
            // Render items
            const itemsList = document.getElementById('itemsList');
            if (order.items && order.items.length > 0) {
                let html = '<h6 style="color: var(--gold-light); margin: 15px 0 10px;">Items:</h6>';
                order.items.forEach(item => {
                    html += `
                        <div class="item-row" style="margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 600;">${item.quantity}x ${item.item_name}</span>
                                <span>Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</span>
                            </div>
                            ${item.modifiers_array && item.modifiers_array.length > 0 ? `
                                <div style="margin-top: 5px; padding-left: 15px; border-left: 2px solid rgba(212,175,55,0.3);">
                                    ${item.modifiers_array.map(mod => `
                                        <div style="font-size: 0.85rem; color: rgba(255,255,255,0.6);">
                                            + ${mod.name}
                                        </div>
                                    `).join('')}
                                </div>
                            ` : ''}
                            ${item.notes_array && item.notes_array.length > 0 ? `
                                <div style="margin-top: 5px; font-size: 0.85rem; color: rgba(255,255,255,0.5); font-style: italic;">
                                    <i class="bi bi-chat-left-text me-1"></i>${item.notes_array.join(', ')}
                                </div>
                            ` : ''}
                        </div>
                    `;
                });
                itemsList.innerHTML = html;
            }
        }

        // Update progress timeline
        function updateProgress(progress) {
            let completedCount = 0;
            
            progress.forEach((step, index) => {
                const stepEl = document.getElementById('step' + (index + 1));
                stepEl.classList.remove('completed', 'active');
                
                if (step.completed) {
                    stepEl.classList.add('completed');
                    completedCount++;
                }
            });
            
            // Set current step as active
            const currentStep = progress.find(s => !s.completed);
            if (currentStep) {
                const stepIndex = progress.indexOf(currentStep);
                document.getElementById('step' + (stepIndex + 1)).classList.add('active');
            }
            
            // Update progress bar
            const progressPercent = ((completedCount) / (progress.length - 1)) * 100;
            document.getElementById('progressFill').style.width = progressPercent + '%';
        }
    </script>
</body>
</html>
