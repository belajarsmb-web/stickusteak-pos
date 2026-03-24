<?php
/**
 * Stickusteak POS - Orders Management
 * Premium Black & Gold Theme
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'Staff';
$page_title = "Orders";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Stickusteak POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="/php-native/assets/css/premium-theme.css" rel="stylesheet">
    <style>
        .order-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: slideUp 0.6s ease-out;
        }
        
        .order-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold-primary);
            box-shadow: 0 15px 40px rgba(212,175,55,0.2);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(212,175,55,0.2);
            margin-bottom: 15px;
        }
        
        .order-id {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .order-meta {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.6);
        }
        
        .order-items {
            margin: 15px 0;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed rgba(212,175,55,0.1);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-quantity {
            background: rgba(212,175,55,0.2);
            color: var(--gold-light);
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .order-total {
            font-size: 1.3rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            color: var(--gold-primary);
        }
        
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background: rgba(255,193,7,0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }
        
        .status-preparing {
            background: rgba(23,162,184,0.2);
            color: #17a2b8;
            border: 1px solid #17a2b8;
        }
        
        .status-ready {
            background: rgba(13,110,253,0.2);
            color: #0d6efd;
            border: 1px solid #0d6efd;
        }
        
        .status-completed, .status-paid {
            background: rgba(40,167,69,0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }
        
        .status-cancelled {
            background: rgba(220,53,69,0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        
        .filter-bar {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .filter-btn {
            padding: 10px 20px;
            border-radius: 20px;
            border: 1px solid rgba(212,175,55,0.3);
            background: transparent;
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            transition: all 0.3s;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: var(--gold-gradient);
            border-color: var(--gold-primary);
            color: var(--black-primary);
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: rgba(255,255,255,0.4);
        }
        
        .empty-state i {
            font-size: 5rem;
            margin-bottom: 20px;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="sidebar-brand">
                    🍽️ Stickusteak
                </div>
                <nav class="mt-3">
                    <a href="/php-native/pages/dashboard.php" class="nav-link">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                    <a href="/php-native/pages/pos-tables.php" class="nav-link">
                        <i class="bi bi-grid-3x3-gap"></i>POS Tables
                    </a>
                    <a href="/php-native/pages/orders.php" class="nav-link active">
                        <i class="bi bi-cart3"></i>Orders
                    </a>
                    <a href="/php-native/pages/menu.php" class="nav-link">
                        <i class="bi bi-menu-button-wide"></i>Menu
                    </a>
                    <a href="/php-native/pages/customers.php" class="nav-link">
                        <i class="bi bi-people"></i>Customers
                    </a>
                    <a href="/php-native/pages/reports.php" class="nav-link">
                        <i class="bi bi-graph-up"></i>Reports
                    </a>
                    <a href="/php-native/pages/users.php" class="nav-link">
                        <i class="bi bi-person-badge"></i>Users
                    </a>
                    <a href="/php-native/pages/settings.php" class="nav-link">
                        <i class="bi bi-gear"></i>Settings
                    </a>
                    <a href="/php-native/mobile/generate-qr.php" class="nav-link" target="_blank">
                        <i class="bi bi-qr-code"></i>QR Codes
                    </a>
                </nav>
                <div class="mt-auto">
                    <a href="/php-native/api/auth/logout.php" class="logout-btn">
                        <i class="bi bi-box-arrow-left"></i>Logout
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Page Header -->
                <div class="pos-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1"><i class="bi bi-cart3 me-2"></i>Orders Management</h2>
                            <p class="mb-0" style="color: rgba(255,255,255,0.5); font-size: 0.9rem;">View and manage all orders</p>
                        </div>
                        <div>
                            <button class="btn btn-refresh" onclick="loadOrders()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Bar -->
                <div class="stats-bar">
                    <div class="stat-box">
                        <div class="stat-value" id="totalOrders">0</div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value text-warning" id="pendingOrders" style="background: linear-gradient(135deg, #ffc107, #ffd93d); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">0</div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value text-info" id="preparingOrders" style="background: linear-gradient(135deg, #17a2b8, #5bc0de); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">0</div>
                        <div class="stat-label">Preparing</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value text-success" id="completedOrders" style="background: linear-gradient(135deg, #28a745, #34ce57); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">0</div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="filter-bar">
                    <div class="d-flex flex-wrap align-items-center">
                        <button class="filter-btn active" data-filter="all" onclick="filterOrders('all', event)">All Orders</button>
                        <button class="filter-btn" data-filter="pending" onclick="filterOrders('pending', event)">Pending</button>
                        <button class="filter-btn" data-filter="preparing" onclick="filterOrders('preparing', event)">Preparing</button>
                        <button class="filter-btn" data-filter="ready" onclick="filterOrders('ready', event)">Ready</button>
                        <button class="filter-btn" data-filter="completed" onclick="filterOrders('completed', event)">Completed</button>
                        <button class="filter-btn" data-filter="cancelled" onclick="filterOrders('cancelled', event)">Cancelled</button>
                        <div class="ms-auto">
                            <input type="date" class="form-control d-inline-block" id="filterDate" style="width: auto; background: rgba(255,255,255,0.05); border-color: rgba(212,175,55,0.3); color: #fff;">
                        </div>
                    </div>
                </div>

                <!-- Orders List -->
                <div id="ordersList">
                    <div class="empty-state">
                        <i class="bi bi-hourglass-split"></i>
                        <h3>Loading orders...</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Detail Modal -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Order #<span id="modalOrderId"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let allOrders = [];
        let currentFilter = 'all';

        // Load orders
        async function loadOrders() {
            try {
                const response = await fetch('/php-native/api/orders/index.php?limit=100');
                const data = await response.json();
                
                const container = document.getElementById('ordersList');
                
                if (data.success && data.orders && data.orders.length > 0) {
                    allOrders = data.orders;
                    filterOrders(currentFilter);
                    
                    // Update stats
                    document.getElementById('totalOrders').textContent = data.orders.length;
                    document.getElementById('pendingOrders').textContent = data.orders.filter(o => o.status === 'pending').length;
                    document.getElementById('preparingOrders').textContent = data.orders.filter(o => o.status === 'preparing').length;
                    document.getElementById('completedOrders').textContent = data.orders.filter(o => ['completed', 'paid'].includes(o.status)).length;
                } else {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h3>No orders found</h3>
                            <p class="text-muted">Orders will appear here once customers place them</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading orders:', error);
            }
        }

        // Filter orders
        function filterOrders(filter, event = null) {
            currentFilter = filter;

            // Update active button
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            
            // Only set active class if event is provided (user clicked)
            if (event && event.target) {
                event.target.classList.add('active');
            } else {
                // If called programmatically, find and activate the correct button
                const activeBtn = document.querySelector('.filter-btn[data-filter="' + filter + '"]');
                if (activeBtn) {
                    activeBtn.classList.add('active');
                }
            }
            
            const container = document.getElementById('ordersList');
            let filtered = allOrders;
            
            if (filter !== 'all') {
                if (filter === 'completed') {
                    filtered = allOrders.filter(o => o.status === 'completed' || o.status === 'paid');
                } else {
                    filtered = allOrders.filter(o => o.status === filter);
                }
            }
            
            const dateFilter = document.getElementById('filterDate').value;
            if (dateFilter) {
                filtered = filtered.filter(o => o.created_at.startsWith(dateFilter));
            }
            
            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-search"></i>
                        <h3>No orders found</h3>
                    </div>
                `;
                return;
            }
            
            let html = '';
            filtered.forEach(order => {
                const items = order.items || [];
                const itemsCount = order.items_count || items.length;
                const statusClass = `status-${order.status.toLowerCase()}`;
                const ticketNumber = order.ticket_number || null;
                const tableId = order.table_id || '-';
                const customerName = order.customer_name || null;
                const customerPhone = order.customer_phone || null;

                html += `
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-id">
                                    ${ticketNumber ? `🎫 Ticket: <span style="cursor:pointer; color: var(--gold-primary); text-decoration: underline;" onclick="showTicketDetails('${ticketNumber}', ${order.id})">${ticketNumber}</span>` : '🎫 Ticket: <span style="color: #999;">No Ticket</span>'}
                                </div>
                                <div class="order-meta">
                                    Order #${order.id} | Table ${tableId} | ${new Date(order.created_at).toLocaleString('en-GB')}
                                </div>
                                ${customerName ? `<div class="order-meta"><i class="bi bi-person me-1"></i>${customerName} ${customerPhone ? '| 📱 ' + customerPhone : ''}</div>` : ''}
                            </div>
                            <div class="text-end">
                                <span class="status-badge ${statusClass}">${order.status}</span>
                                <div class="order-total mt-2">Rp ${parseFloat(order.total_amount).toLocaleString('id-ID')}</div>
                            </div>
                        </div>
                        <div class="order-items">
                            ${items.slice(0, 3).map(item => `
                                <div class="order-item">
                                    <div>
                                        <span class="item-quantity">${item.quantity}x</span>
                                        <span class="ms-2">${item.item_name || 'Item'}</span>
                                        ${item.notes ? `<div class="text-muted small mt-1"><i class="bi bi-chat-left-text me-1"></i>${typeof item.notes === 'string' ? item.notes : ''}</div>` : ''}
                                    </div>
                                    <div>Rp ${parseFloat(item.price * item.quantity).toLocaleString('id-ID')}</div>
                                </div>
                            `).join('')}
                            ${items.length > 3 ? `<div class="text-muted small mt-2"><i class="bi bi-plus-circle me-1"></i>${items.length - 3} more items</div>` : ''}
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i class="bi bi-receipt me-1"></i>${itemsCount} items
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-light me-1" onclick="printReceipt(${order.id})">
                                    <i class="bi bi-printer me-1"></i>Print
                                </button>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="viewOrderDetail(${order.id})">
                                    <i class="bi bi-eye me-1"></i>Details
                                </button>
                                ${order.status === 'pending' ? `
                                    <button class="btn btn-sm btn-info" onclick="updateOrderStatus(${order.id}, 'preparing')">
                                        <i class="bi bi-fire me-1"></i>Start
                                    </button>
                                ` : ''}
                                ${order.status === 'preparing' ? `
                                    <button class="btn btn-sm btn-success" onclick="updateOrderStatus(${order.id}, 'ready')">
                                        <i class="bi bi-check-circle me-1"></i>Ready
                                    </button>
                                ` : ''}
                                ${order.status === 'ready' ? `
                                    <button class="btn btn-sm btn-success" onclick="updateOrderStatus(${order.id}, 'completed')">
                                        <i class="bi bi-check-lg me-1"></i>Complete
                                    </button>
                                ` : ''}
                                ${order.status === 'paid' || order.status === 'completed' ? `
                                    <button class="btn btn-sm btn-success" onclick="updateOrderStatus(${order.id}, 'paid')">
                                        <i class="bi bi-check-circle me-1"></i>Paid
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        // View order detail
        async function viewOrderDetail(orderId) {
            try {
                const response = await fetch(`/php-native/api/orders/index.php?id=${orderId}`);
                const data = await response.json();
                
                if (data.success && data.orders && data.orders.length > 0) {
                    const order = data.orders[0];
                    document.getElementById('modalOrderId').textContent = order.id;
                    
                    const items = order.items || [];
                    let itemsHtml = '';
                    let subtotal = 0;
                    
                    items.forEach(item => {
                        const itemTotal = item.price * item.quantity;
                        subtotal += itemTotal;
                        itemsHtml += `
                            <div class="order-item">
                                <div>
                                    <span class="item-quantity">${item.quantity}x</span>
                                    <span class="ms-2">${item.item_name || 'Item'}</span>
                                    ${item.notes ? `<div class="text-muted small mt-1"><i class="bi bi-chat-left-text me-1"></i>${typeof item.notes === 'string' ? item.notes : ''}</div>` : ''}
                                </div>
                                <div>Rp ${itemTotal.toLocaleString('id-ID')}</div>
                            </div>
                        `;
                    });
                    
                    document.getElementById('orderDetailContent').innerHTML = `
                        <div class="mb-3">
                            <strong>Order Information:</strong>
                            <div class="text-muted small mt-1">
                                <div><i class="bi bi-clock me-1"></i>${new Date(order.created_at).toLocaleString('en-GB')}</div>
                                ${order.table_name ? `<div><i class="bi bi-grid me-1"></i>Table: ${order.table_name}</div>` : ''}
                                <div><i class="bi bi-person me-1"></i>Status: <span class="status-badge status-${order.status.toLowerCase()}">${order.status}</span></div>
                            </div>
                        </div>
                        <div class="receipt-line" style="border-top: 1px dashed rgba(212,175,55,0.3); margin: 15px 0;"></div>
                        <div class="mb-3">
                            <strong>Order Items:</strong>
                            <div class="mt-2">
                                ${itemsHtml}
                            </div>
                        </div>
                        <div class="receipt-line" style="border-top: 1px dashed rgba(212,175,55,0.3); margin: 15px 0;"></div>
                        <div class="d-flex justify-content-between" style="font-size: 1.2rem; font-weight: 700; font-family: 'Playfair Display', serif; color: var(--gold-primary);">
                            <span>Total:</span>
                            <span>Rp ${parseFloat(order.total_amount).toLocaleString('id-ID')}</span>
                        </div>
                    `;
                    
                    const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
                    modal.show();
                }
            } catch (error) {
                console.error('Error loading order detail:', error);
                alert('Failed to load order details');
            }
        }

        // Update order status
        async function updateOrderStatus(orderId, newStatus) {
            if (!confirm(`Update order status to ${newStatus}?`)) return;
            
            try {
                const response = await fetch('/php-native/api/kds/update-order-status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        order_id: orderId,
                        status: newStatus
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Order status updated!');
                    loadOrders();
                } else {
                    alert(data.message || 'Failed to update status');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('Error updating order status');
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', loadOrders);

        // Auto-refresh every 30 seconds
        setInterval(loadOrders, 30000);

        // Show ticket details
        function showTicketDetails(ticketNumber, orderId) {
            // Fetch ticket details from API
            fetch(`/php-native/api/tickets/create.php?ticket_number=${encodeURIComponent(ticketNumber)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.ticket) {
                        const ticket = data.ticket;
                        let details = `🎫 Ticket: ${ticket.ticket_number}\n`;
                        details += `Status: ${ticket.status.toUpperCase()}\n`;
                        details += `Opened: ${new Date(ticket.opened_at).toLocaleString('id-ID')}\n`;
                        if (ticket.customer_name) {
                            details += `\n👤 Customer: ${ticket.customer_name}`;
                            if (ticket.customer_phone) {
                                details += `\n📱 Phone: ${ticket.customer_phone}`;
                            }
                        }
                        details += `\n\n📋 Orders: ${ticket.orders_count || 0}`;
                        if (ticket.total_amount) {
                            details += `\n💰 Total: Rp ${parseFloat(ticket.total_amount).toLocaleString('id-ID')}`;
                        }
                        
                        // Show order items if available
                        if (ticket.orders && ticket.orders.length > 0) {
                            details += `\n\n📝 Order Details:`;
                            ticket.orders.forEach((order, idx) => {
                                details += `\n\n  Order #${order.id} (${order.status.toUpperCase()})`;
                                // Parse items from JSON string if needed
                                let items = order.items;
                                if (typeof items === 'string') {
                                    try {
                                        items = JSON.parse(items);
                                    } catch(e) {
                                        items = [];
                                    }
                                }
                                if (items && items.length > 0) {
                                    items.forEach(item => {
                                        details += `\n    • ${item.quantity}x ${item.item_name || item.name}`;
                                        if (item.modifiers) {
                                            try {
                                                const mods = typeof item.modifiers === 'string' ? JSON.parse(item.modifiers) : item.modifiers;
                                                if (mods && mods.length > 0) {
                                                    details += ` [${mods.join(', ')}]`;
                                                }
                                            } catch(e) {}
                                        }
                                        if (item.notes) {
                                            // Notes is plain text now
                                            if (typeof item.notes === 'string' && item.notes.trim() !== '') {
                                                details += ` {${item.notes}}`;
                                            }
                                        }
                                        if (item.is_voided) {
                                            details += ` [VOID]`;
                                        }
                                    });
                                }
                            });
                        }
                        
                        details += `\n\n━━━━━━━━━━━━━━━━━━━━━━━`;
                        details += `\n[1] Print Receipt`;
                        details += `\n[2] Close`;
                        details += `\n━━━━━━━━━━━━━━━━━━━━━━━`;
                        
                        // Use confirm for action selection
                        const action = confirm(details + '\n\nClick OK to Print Receipt, Cancel to Close');
                        if (action) {
                            // Print first order in ticket
                            const firstOrderId = ticket.orders && ticket.orders.length > 0 ? ticket.orders[0].id : orderId;
                            if (firstOrderId) {
                                printReceipt(firstOrderId);
                            }
                        }
                    } else {
                        alert('Ticket details not found');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading ticket details: ' + error.message);
                });
        }

        // Print receipt
        function printReceipt(orderId) {
            window.open(`/php-native/pages/receipt.php?order_id=${orderId}`, '_blank', 'width=400,height=600');
        }
    </script>
</body>
</html>
