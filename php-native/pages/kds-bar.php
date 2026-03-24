<?php
/**
 * Stickusteak POS - Bar Display System (BDS)
 * Premium Black & Gold Theme with Auto-refresh
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">
    <title>Bar Display - BDS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #E91E63;
            --secondary-color: #00BCD4;
            --gold-primary: #D4AF37;
            --gold-light: #F4DF89;
            --gold-dark: #AA8C2C;
            --black-primary: #0a0a0a;
            --black-secondary: #1a1a1a;
            --black-tertiary: #2a2a2a;
        }

        * { font-family: 'Poppins', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', serif; }

        body {
            background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 100%);
            color: #fff;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, var(--black-tertiary) 0%, var(--black-primary) 100%);
            border-bottom: 2px solid var(--primary-color);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 20px rgba(233, 30, 99, 0.2);
        }

        .header h2 {
            margin: 0;
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-stats {
            display: flex;
            gap: 15px;
        }

        .stat-badge {
            background: rgba(233, 30, 99, 0.2);
            border: 1px solid var(--primary-color);
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            color: var(--primary-color);
        }

        /* BDS Grid */
        .bds-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 20px;
            padding: 30px;
        }

        /* Order Card */
        .order-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9), rgba(26,26,26,0.9));
            border: 1px solid rgba(233, 30, 99, 0.2);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .order-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
            box-shadow: 0 10px 30px rgba(233, 30, 99, 0.3);
        }

        .order-card.priority-high {
            border: 2px solid #dc3545;
            animation: pulse 2s infinite;
        }

        .order-card.priority-normal {
            border: 2px solid var(--primary-color);
        }

        .order-card.priority-low {
            border: 2px solid #28a745;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 10px rgba(233, 30, 99, 0.3); }
            50% { box-shadow: 0 0 20px rgba(233, 30, 99, 0.6); }
        }

        .order-header {
            padding: 15px;
            background: linear-gradient(135deg, rgba(233, 30, 99, 0.2), rgba(233, 30, 99, 0.05));
            border-bottom: 1px solid rgba(233, 30, 99, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-table {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .order-timer {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .order-body {
            padding: 15px;
        }

        .order-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(233, 30, 99, 0.1);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .item-name {
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 1rem;
        }

        .item-qty {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .item-notes {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.5);
            margin-top: 5px;
        }

        .item-notes span {
            display: inline-block;
            background: rgba(233, 30, 99, 0.2);
            border: 1px solid var(--primary-color);
            padding: 2px 8px;
            border-radius: 10px;
            margin-right: 5px;
            margin-top: 3px;
        }

        .order-footer {
            padding: 15px;
            border-top: 1px solid rgba(233, 30, 99, 0.2);
            display: flex;
            gap: 10px;
        }

        .btn-bds {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
        }

        .btn-start {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .btn-ready {
            background: linear-gradient(135deg, #28a745, #34ce57);
            color: white;
        }

        .btn-served {
            background: linear-gradient(135deg, #17a2b8, #5bc0de);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: rgba(255,255,255,0.5);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        /* Last Update */
        .last-update {
            text-align: center;
            padding: 10px;
            color: rgba(255,255,255,0.5);
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>🍹 Bar Display System</h2>
        <div class="header-stats">
            <span class="stat-badge">📋 <span id="totalOrders">0</span> Orders</span>
            <span class="stat-badge">⏱️ <span id="avgTime">0</span>m Avg</span>
            <span class="stat-badge">🔴 <span id="highPriority">0</span> Urgent</span>
        </div>
    </div>

    <!-- BDS Container -->
    <div class="bds-container" id="bdsContainer">
        <div class="empty-state">
            <i class="bi bi-hourglass-split"></i>
            <h3>Loading orders...</h3>
        </div>
    </div>

    <!-- Last Update -->
    <div class="last-update">
        Last updated: <span id="lastUpdate">-</span> | Auto-refresh in <span id="countdown">10</span>s
    </div>

    <script>
        let lastOrderCount = 0;
        let countdown = 10;

        // Load orders
        async function loadOrders() {
            try {
                const response = await fetch('/php-native/api/kds/bar-orders.php?t=' + Date.now());
                const data = await response.json();

                if (data.success) {
                    renderOrders(data.orders);
                    updateStats(data.orders);
                    
                    // Play sound if new order
                    if (data.orders.length > lastOrderCount && lastOrderCount > 0) {
                        playNotificationSound();
                    }
                    lastOrderCount = data.orders.length;
                    
                    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('id-ID');
                }
            } catch (error) {
                console.error('Error fetching orders:', error);
            }
        }

        // Render orders
        function renderOrders(orders) {
            const container = document.getElementById('bdsContainer');

            if (orders.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h3>No pending beverage orders</h3>
                        <p>Beverage orders will appear here when customers place them</p>
                    </div>
                `;
                return;
            }

            let html = '';
            orders.forEach(order => {
                // Skip completed/served orders
                if (order.status === 'served') return;
                
                const priorityClass = `priority-${order.priority}`;
                const timerColor = order.prep_time_minutes > 30 ? '#dc3545' :
                                   order.prep_time_minutes > 15 ? '#ffc107' : '#28a745';

                let itemsHtml = '';
                order.items.forEach(item => {
                    // Parse modifiers from JSON string
                    let modifiers = [];
                    if (item.modifiers) {
                        try {
                            modifiers = typeof item.modifiers === 'string' ? JSON.parse(item.modifiers) : item.modifiers;
                        } catch(e) { modifiers = []; }
                    }

                    // Parse notes (plain text or JSON)
                    let notes = [];
                    if (item.notes) {
                        if (typeof item.notes === 'string') {
                            try {
                                const decoded = JSON.parse(item.notes);
                                if (Array.isArray(decoded)) {
                                    notes = decoded;
                                } else {
                                    notes = [item.notes];
                                }
                            } catch(e) { notes = [item.notes]; }
                        } else {
                            notes = item.notes;
                        }
                    }

                    // Combine modifiers and notes for display
                    const allDetails = [...modifiers, ...notes];
                    
                    itemsHtml += `
                        <div class="order-item">
                            <div class="item-header">
                                <span class="item-name">🍹 ${item.item_name}</span>
                                <span class="item-qty">x${item.quantity}</span>
                            </div>
                            ${allDetails.length > 0 ? `
                                <div class="item-notes" style="margin-top: 5px;">
                                    ${allDetails.map(detail => `<span style="display:inline-block; margin:2px 5px; padding:3px 8px; background:rgba(212,175,55,0.2); border-radius:10px; font-size:11px;">• ${detail}</span>`).join('')}
                                </div>
                            ` : ''}
                            ${item.is_voided ? `<div style="color:#dc3545; font-size:11px; margin-top:5px;">⚠️ VOIDED</div>` : ''}
                        </div>
                    `;
                });

                html += `
                    <div class="order-card ${priorityClass}">
                        <div class="order-header">
                            <div>
                                <div class="order-table">🍽️ Table ${order.table_name}</div>
                                <small style="color: rgba(255,255,255,0.5);">Order #${order.id}</small>
                            </div>
                            <div class="order-timer" style="border-color: ${timerColor}; color: ${timerColor};">
                                ⏱️ ${order.prep_time_formatted}
                            </div>
                        </div>
                        <div class="order-body">
                            ${itemsHtml}
                        </div>
                        <div class="order-footer">
                            ${order.status === 'sent_to_kitchen' ? `
                                <button class="btn-bds btn-start" onclick="updateOrderStatus(${order.id}, 'in_progress')">
                                    🍹 Start Preparing
                                </button>
                            ` : ''}
                            ${order.status === 'in_progress' ? `
                                <button class="btn-bds btn-ready" onclick="updateOrderStatus(${order.id}, 'ready')">
                                    ✅ Ready to Serve
                                </button>
                            ` : ''}
                            ${order.status === 'ready' ? `
                                <button class="btn-bds btn-served" onclick="updateOrderStatus(${order.id}, 'served')">
                                    🎉 Mark Served
                                </button>
                            ` : ''}
                            ${order.status === 'served' ? `
                                <span style="color: #28a745; font-weight: 600; width: 100%; text-align: center;">
                                    ✓ Completed
                                </span>
                            ` : ''}
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // Update stats
        function updateStats(orders) {
            document.getElementById('totalOrders').textContent = orders.length;
            
            const avgTime = orders.length > 0 
                ? Math.round(orders.reduce((sum, o) => sum + o.prep_time_minutes, 0) / orders.length)
                : 0;
            document.getElementById('avgTime').textContent = avgTime;
            
            const highPriority = orders.filter(o => o.priority === 'high').length;
            document.getElementById('highPriority').textContent = highPriority;
        }

        // Update order status
        async function updateOrderStatus(orderId, status) {
            if (!confirm(`Mark order #${orderId} as ${status}?`)) return;

            try {
                const response = await fetch('/php-native/api/kds/update-order-status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId, status: status })
                });

                const result = await response.json();
                if (result.success) {
                    loadOrders();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('Error updating order status');
            }
        }

        // Play notification sound
        function playNotificationSound() {
            // Visual notification only for bar
            document.title = '🔔 NEW DRINK ORDER! - BDS';
            setTimeout(() => {
                document.title = '🍹 Bar Display - BDS';
            }, 5000);
        }

        // Countdown timer
        setInterval(() => {
            countdown--;
            if (countdown < 0) countdown = 10;
            document.getElementById('countdown').textContent = countdown;
        }, 1000);

        // Auto-refresh every 10 seconds
        setInterval(loadOrders, 10000);

        // Initial load
        loadOrders();
    </script>
</body>
</html>
