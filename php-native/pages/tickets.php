<?php
/**
 * Stickusteak POS - Tickets Management Page
 * View and manage all tickets with their orders
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'Staff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets - Stickusteak</title>
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
            --black-tertiary: #2a2a2a;
        }

        * { font-family: 'Poppins', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Playfair Display', serif; }

        body {
            background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 100%);
            color: #fff;
            min-height: 100vh;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--black-tertiary) 0%, var(--black-primary) 100%);
            border-right: 2px solid var(--gold-dark);
            position: fixed;
            width: 260px;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 25px 20px;
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary), var(--gold-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 14px 20px;
            margin: 6px 12px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background: rgba(212, 175, 55, 0.1);
            color: var(--gold-light);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0.1));
            color: var(--gold-primary);
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9), rgba(26,26,26,0.9));
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.2);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--gold-primary);
        }

        .stat-label {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .ticket-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.95), rgba(26,26,26,0.95));
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .ticket-card:hover {
            border-color: var(--gold-primary);
            transform: translateX(5px);
        }

        .ticket-card.open {
            border-left: 4px solid #ffc107;
        }

        .ticket-card.paid {
            border-left: 4px solid #28a745;
        }

        .ticket-card.closed {
            border-left: 4px solid #6c757d;
        }

        .ticket-number {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gold-primary);
        }

        .filter-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: rgba(255,255,255,0.8);
            padding: 8px 20px;
            border-radius: 25px;
            transition: all 0.3s;
        }

        .filter-btn:hover, .filter-btn.active {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            color: var(--black-primary);
            border-color: transparent;
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            color: var(--black-primary);
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }

        .modal-content {
            background: linear-gradient(135deg, var(--black-secondary), var(--black-tertiary));
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .modal-header {
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }

        .modal-footer {
            border-top: 1px solid rgba(212, 175, 55, 0.2);
        }

        .order-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
        }

        .order-item.voided {
            opacity: 0.5;
            background: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.3);
        }

        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-open {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .badge-paid {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .badge-pending {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255,255,255,0.5);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--gold-dark);
            margin-bottom: 20px;
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--black-primary); }
        ::-webkit-scrollbar-thumb { background: var(--gold-dark); border-radius: 4px; }

        @media print {
            .sidebar, .btn-gold, .filter-section { display: none !important; }
            .main-content { margin-left: 0 !important; }
            .modal-footer { display: none !important; }
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
                    <a href="/php-native/pages/kds-kitchen.php" class="nav-link" target="_blank">
                        <i class="bi bi-egg-fried"></i>Kitchen Display
                    </a>
                    <a href="/php-native/pages/kds-bar.php" class="nav-link" target="_blank">
                        <i class="bi bi-cup-straw"></i>Bar Display
                    </a>
                    <a href="/php-native/pages/tickets.php" class="nav-link active">
                        <i class="bi bi-receipt"></i>Tickets
                    </a>
                    <a href="/php-native/pages/menu.php" class="nav-link">
                        <i class="bi bi-menu-button-wide"></i>Menu
                    </a>
                    <a href="/php-native/pages/modifiers.php" class="nav-link">
                        <i class="bi bi-ui-checks-grid"></i>Modifiers
                    </a>
                    <a href="/php-native/pages/customers.php" class="nav-link">
                        <i class="bi bi-people"></i>Customers
                    </a>
                    <a href="/php-native/pages/reports.php" class="nav-link">
                        <i class="bi bi-graph-up"></i>Reports
                    </a>
                    <a href="/php-native/pages/shifts.php" class="nav-link">
                        <i class="bi bi-clock"></i>Shifts
                    </a>
                    <a href="/php-native/pages/settings.php" class="nav-link">
                        <i class="bi bi-gear"></i>Settings
                    </a>
                </nav>
                <div class="mt-auto">
                    <a href="/php-native/api/auth/logout.php" class="nav-link" style="color: rgba(220,53,69,0.8);">
                        <i class="bi bi-box-arrow-left"></i>Logout
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="page-title mb-1"><i class="bi bi-receipt me-2"></i>Ticket Management</h2>
                        <p class="mb-0" style="color: rgba(255,255,255,0.5);">View and manage all tickets for current period</p>
                    </div>
                    <div class="d-flex gap-2">
                        <input type="date" class="form-control" id="filterDate" style="width: auto; background: rgba(255,255,255,0.05); border-color: rgba(212,175,55,0.3); color: #fff;" value="<?php echo date('Y-m-d'); ?>">
                        <button class="btn btn-gold" onclick="loadTickets()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-value" id="totalTickets">0</div>
                            <div class="stat-label">Total Tickets</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-value" style="color: #ffc107;" id="openTickets">0</div>
                            <div class="stat-label">Open</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-value" style="color: #28a745;" id="paidTickets">0</div>
                            <div class="stat-label">Paid</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-value" id="totalRevenue">Rp 0</div>
                            <div class="stat-label">Total Revenue</div>
                        </div>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="filter-section d-flex gap-2 mb-4">
                    <button class="filter-btn active" data-filter="all" onclick="filterTickets('all')">
                        <i class="bi bi-list me-1"></i>All
                    </button>
                    <button class="filter-btn" data-filter="open" onclick="filterTickets('open')">
                        <i class="bi bi-clock me-1"></i>Open
                    </button>
                    <button class="filter-btn" data-filter="paid" onclick="filterTickets('paid')">
                        <i class="bi bi-check-circle me-1"></i>Paid
                    </button>
                </div>

                <!-- Tickets List -->
                <div id="ticketsList">
                    <div class="empty-state">
                        <i class="bi bi-hourglass-split"></i>
                        <h3>Loading tickets...</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Detail Modal -->
    <div class="modal fade" id="ticketDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Ticket: <span id="modalTicketNumber"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="ticketDetailContent">
                    <!-- Content loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-gold" onclick="printTicket()">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let allTickets = [];
        let currentFilter = 'all';
        let currentTicketId = null;

        // Load tickets
        async function loadTickets() {
            try {
                const date = document.getElementById('filterDate').value;
                const response = await fetch(`/php-native/api/tickets/index.php?date=${date}`);
                const data = await response.json();

                if (data.success) {
                    allTickets = data.tickets;
                    
                    // Update stats
                    document.getElementById('totalTickets').textContent = data.stats.total_tickets;
                    document.getElementById('openTickets').textContent = data.stats.open_tickets;
                    document.getElementById('paidTickets').textContent = data.stats.paid_tickets;
                    document.getElementById('totalRevenue').textContent = 'Rp ' + parseFloat(data.stats.total_revenue).toLocaleString('id-ID');

                    filterTickets(currentFilter);
                }
            } catch (error) {
                console.error('Error loading tickets:', error);
                document.getElementById('ticketsList').innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle"></i>
                        <h3>Error loading tickets</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }

        // Filter tickets
        function filterTickets(filter) {
            currentFilter = filter;
            
            // Update filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.filter === filter) btn.classList.add('active');
            });

            const filtered = filter === 'all' ? allTickets : allTickets.filter(t => t.status === filter);
            renderTickets(filtered);
        }

        // Render tickets
        function renderTickets(tickets) {
            const container = document.getElementById('ticketsList');

            if (tickets.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h3>No tickets found</h3>
                        <p>No tickets for selected date and status</p>
                    </div>
                `;
                return;
            }

            let html = '';
            tickets.forEach(ticket => {
                const statusClass = ticket.status === 'open' ? 'open' : (ticket.status === 'paid' ? 'paid' : 'closed');
                const badgeClass = ticket.status === 'open' ? 'badge-open' : (ticket.status === 'paid' ? 'badge-paid' : 'badge-secondary');
                const time = new Date(ticket.opened_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                const date = new Date(ticket.opened_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

                html += `
                    <div class="ticket-card ${statusClass}" onclick="showTicketDetail(${ticket.id})">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="ticket-number">${ticket.ticket_number}</div>
                                <div class="mt-2" style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">
                                    <i class="bi bi-calendar3 me-1"></i>${date}
                                    <i class="bi bi-clock ms-2 me-1"></i>${time}
                                    <i class="bi bi-people ms-2 me-1"></i>${ticket.order_count} order(s), ${ticket.items_count} item(s)
                                </div>
                                <div class="mt-2" style="color: rgba(255,255,255,0.5); font-size: 0.8rem;">
                                    Table: ${ticket.table_id || '-'}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge-status ${badgeClass}">${ticket.status.toUpperCase()}</span>
                                <div class="mt-2" style="font-size: 1.2rem; font-weight: 700; color: var(--gold-primary);">
                                    Rp ${parseFloat(ticket.total_amount || 0).toLocaleString('id-ID')}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // Show ticket detail
        async function showTicketDetail(ticketId) {
            currentTicketId = ticketId;
            const ticket = allTickets.find(t => t.id === ticketId);
            
            if (!ticket) return;

            document.getElementById('modalTicketNumber').textContent = ticket.ticket_number;

            let html = `
                <div class="d-flex justify-content-between mb-4 p-3" style="background: rgba(212,175,55,0.1); border-radius: 10px;">
                    <div>
                        <div style="font-size: 0.85rem; color: rgba(255,255,255,0.6);">Table</div>
                        <div style="font-weight: 600;">${ticket.table_id || '-'}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: rgba(255,255,255,0.6);">Opened</div>
                        <div style="font-weight: 600;">${new Date(ticket.opened_at).toLocaleString('id-ID')}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: rgba(255,255,255,0.6);">Status</div>
                        <span class="badge-status ${ticket.status === 'open' ? 'badge-open' : 'badge-paid'}">${ticket.status.toUpperCase()}</span>
                    </div>
                    <div class="text-end">
                        <div style="font-size: 0.85rem; color: rgba(255,255,255,0.6);">Total</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--gold-primary);">
                            Rp ${parseFloat(ticket.total_amount || 0).toLocaleString('id-ID')}
                        </div>
                    </div>
                </div>

                <h6 class="mb-3" style="color: var(--gold-primary);">
                    <i class="bi bi-cart3 me-2"></i>Orders (${ticket.orders.length})
                </h6>
            `;

            ticket.orders.forEach((order, orderIndex) => {
                const orderStatusClass = order.status === 'paid' ? 'badge-paid' : (order.status === 'pending' ? 'badge-pending' : 'badge-open');
                
                html += `
                    <div class="mb-4 p-3" style="background: rgba(255,255,255,0.03); border-radius: 10px; border: 1px solid rgba(212,175,55,0.1);">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span style="font-weight: 600;">Order #${order.id}</span>
                                <span style="color: rgba(255,255,255,0.5); font-size: 0.8rem; margin-left: 10px;">
                                    ${order.cashier_name || 'Staff'} | ${new Date(order.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                </span>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                ${order.payment_method ? `<span style="color: rgba(255,255,255,0.5); font-size: 0.8rem;">${order.payment_method}</span>` : ''}
                                <span class="badge-status ${orderStatusClass}">${order.status}</span>
                                <span style="font-weight: 600; color: var(--gold-primary);">
                                    Rp ${parseFloat(order.total_amount).toLocaleString('id-ID')}
                                </span>
                            </div>
                        </div>
                        <div style="padding-left: 15px; border-left: 2px solid rgba(212,175,55,0.3);">
                `;

                if (order.items && order.items.length > 0) {
                    order.items.forEach(item => {
                        const isVoided = item.is_voided == 1;
                        html += `
                            <div class="order-item ${isVoided ? 'voided' : ''}">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <span style="font-weight: 600;">${item.quantity}x</span>
                                        <span>${item.item_name || 'Unknown Item'}</span>
                                        ${isVoided ? '<span class="badge" style="background: #dc3545; margin-left: 5px;">VOID</span>' : ''}
                                    </div>
                                    <div class="text-end">
                                        <span style="color: var(--gold-primary);">
                                            Rp ${parseFloat(item.price * item.quantity).toLocaleString('id-ID')}
                                        </span>
                                    </div>
                                </div>
                                ${item.modifiers && item.modifiers !== '[]' ? `
                                    <div style="font-size: 0.8rem; color: rgba(255,255,255,0.5); margin-top: 5px; padding-left: 20px;">
                                        <i class="bi bi-ui-checks-grid"></i> ${item.modifiers.replace(/[\[\]"]/g, '')}
                                    </div>
                                ` : ''}
                                ${item.notes && item.notes !== '[]' && item.notes !== '[]' ? `
                                    <div style="font-size: 0.8rem; color: rgba(255,255,255,0.5); margin-top: 5px; padding-left: 20px;">
                                        <i class="bi bi-chat-left-text"></i> ${item.notes.replace(/[\[\]"]/g, '')}
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    });
                } else {
                    html += `<div style="color: rgba(255,255,255,0.4); font-style: italic;">No items</div>`;
                }

                html += `</div></div>`;
            });

            // Order Summary
            html += `
                <div class="mt-4 p-3" style="background: rgba(212,175,55,0.1); border-radius: 10px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>Rp ${parseFloat(ticket.total_amount || 0).toLocaleString('id-ID')}</span>
                    </div>
                </div>
            `;

            document.getElementById('ticketDetailContent').innerHTML = html;

            const modal = new bootstrap.Modal(document.getElementById('ticketDetailModal'));
            modal.show();
        }

        // Print ticket
        function printTicket() {
            if (currentTicketId) {
                const ticket = allTickets.find(t => t.id === currentTicketId);
                if (ticket && ticket.orders && ticket.orders.length > 0) {
                    // Use first order ID from the ticket
                    window.open(`/php-native/pages/receipt.php?order_id=${ticket.orders[0].id}&cashier=${ticket.orders[0].cashier_name || 'Staff'}`, '_blank');
                } else {
                    alert('No orders found for this ticket');
                }
            }
        }

        // Date filter change
        document.getElementById('filterDate').addEventListener('change', loadTickets);

        // Initial load
        document.addEventListener('DOMContentLoaded', loadTickets);
    </script>
</body>
</html>
