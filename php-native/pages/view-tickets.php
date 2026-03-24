<?php
/**
 * View Tickets by Table
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
$tableId = $_GET['table_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets - Table <?php echo $tableId; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }
        .ticket-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
            border: 1px solid #D4AF37;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .ticket-header {
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .ticket-number {
            color: #D4AF37;
            font-weight: 700;
            font-size: 1.2rem;
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
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="bi bi-ticket-perforated me-2"></i>Tickets - Table <?php echo $tableId; ?></h1>
                <a href="/php-native/pages/pos-tables.php" class="btn btn-outline-light mt-2">
                    <i class="bi bi-arrow-left me-1"></i>Back to Tables
                </a>
            </div>
        </div>

        <div id="ticketsContainer">
            <div class="text-center">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading tickets...</p>
            </div>
        </div>
    </div>

    <script>
        const tableId = <?php echo $tableId; ?>;

        async function loadTickets() {
            try {
                const response = await fetch(`/php-native/api/tickets/get-by-table.php?table_id=${tableId}`);
                const data = await response.json();

                const container = document.getElementById('ticketsContainer');

                if (data.success && data.ticket) {
                    const ticket = data.ticket;
                    const statusClass = ticket.status === 'open' ? 'text-success' : 
                                       ticket.status === 'paid' ? 'text-warning' : 'text-danger';

                    let html = `
                        <div class="ticket-card">
                            <div class="ticket-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="ticket-number">🎫 ${ticket.ticket_number}</div>
                                        <div class="text-muted">Opened: ${new Date(ticket.opened_at).toLocaleString('id-ID')}</div>
                                    </div>
                                    <div>
                                        <span class="badge ${statusClass} fs-6">${ticket.status.toUpperCase()}</span>
                                    </div>
                                </div>
                                ${ticket.customer_name ? `
                                    <div class="mt-2">
                                        <strong>👤 Customer:</strong> ${ticket.customer_name}
                                        ${ticket.customer_phone ? ` | 📱 ${ticket.customer_phone}` : ''}
                                    </div>
                                ` : ''}
                            </div>

                            <h5 class="mb-3">📋 Orders (${ticket.total_items || 0} items)</h5>
                    `;

                    if (ticket.orders && ticket.orders.length > 0) {
                        ticket.orders.forEach((order, idx) => {
                            const items = JSON.parse(order.items || '[]');
                            html += `
                                <div class="order-item">
                                    <div class="d-flex justify-content-between">
                                        <strong>Order #${order.id}</strong>
                                        <span class="text-muted">${new Date(order.created_at).toLocaleTimeString('id-ID')}</span>
                                    </div>
                            `;

                            items.forEach(item => {
                                const modifiers = item.modifiers ? JSON.parse(item.modifiers) : [];
                                const notes = item.notes ? item.notes : '';
                                const allDetails = [...modifiers];
                                if (notes && notes.trim() !== '') {
                                    allDetails.push(notes);
                                }

                                html += `
                                    <div class="mt-2" style="border-left: 2px solid #D4AF37; padding-left: 10px;">
                                        <div style="font-weight: 600;">• ${item.quantity}x ${item.item_name || 'Item'}</div>
                                        ${allDetails.length > 0 ? `
                                            <div class="text-muted" style="font-size: 0.75rem; margin-top: 5px;">
                                                <i class="bi bi-chat-left-text"></i> ${allDetails.join(', ')}
                                            </div>
                                        ` : ''}
                                    </div>
                                `;
                            });

                            html += `
                                <div class="text-end mt-2">
                                    <strong>Rp ${parseFloat(order.total_amount).toLocaleString('id-ID')}</strong>
                                </div>
                            </div>
                            `;
                        });
                    } else {
                        html += '<p class="text-muted">No orders in this ticket</p>';
                    }

                    html += `</div>`;
                    container.innerHTML = html;

                } else if (data.success && !data.ticket) {
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-ticket" style="font-size: 4rem; color: #666;"></i>
                            <h3 class="mt-3">No Active Tickets</h3>
                            <p class="text-muted">This table doesn't have any active tickets</p>
                            <a href="/php-native/pages/pos-tables.php" class="btn btn-gold mt-3">
                                <i class="bi bi-plus-circle me-1"></i>Create New Order
                            </a>
                        </div>
                    `;
                } else {
                    container.innerHTML = `<div class="alert alert-danger">Error loading tickets: ${data.message}</div>`;
                }
            } catch (error) {
                console.error('Error loading tickets:', error);
                document.getElementById('ticketsContainer').innerHTML = 
                    '<div class="alert alert-danger">Error loading tickets</div>';
            }
        }

        document.addEventListener('DOMContentLoaded', loadTickets);
    </script>
</body>
</html>
