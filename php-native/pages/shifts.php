<?php
/**
 * Stickusteak POS - Shift Management
 * Premium Black & Gold Theme
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
    <title>Shift Management - Stickusteak POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold-primary: #D4AF37;
            --gold-light: #F4DF89;
            --gold-dark: #AA8C2C;
            --gold-gradient: linear-gradient(135deg, #D4AF37 0%, #F4DF89 50%, #AA8C2C 100%);
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
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 14px 20px;
            margin: 6px 12px;
            border-radius: 8px;
            transition: all 0.4s;
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
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .shift-status-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9), rgba(26,26,26,0.9));
            border: 2px solid var(--gold-dark);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
        }

        .shift-status-active {
            border-color: #28a745;
            box-shadow: 0 0 30px rgba(40, 167, 69, 0.3);
        }

        .shift-status-closed {
            border-color: #6c757d;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 15px 0;
        }

        .status-active {
            background: linear-gradient(135deg, #28a745, #34ce57);
            color: white;
        }

        .status-closed {
            background: linear-gradient(135deg, #6c757d, #adb5bd);
            color: white;
        }

        .stat-box {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
            margin-top: 8px;
            text-transform: uppercase;
        }

        .btn-premium {
            background: var(--gold-gradient);
            border: none;
            color: var(--black-primary);
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-premium:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.4);
        }

        .btn-danger-premium {
            background: linear-gradient(135deg, #dc3545, #ff6b6b);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }

        .modal-content {
            background: linear-gradient(135deg, var(--black-tertiary), var(--black-primary));
            border: 2px solid var(--gold-dark);
            border-radius: 15px;
        }

        .modal-header {
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
        }

        .modal-title {
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-control {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            color: #fff;
        }

        .form-control:focus {
            background: rgba(255,255,255,0.08);
            border-color: var(--gold-primary);
            color: #fff;
        }

        .form-label {
            color: var(--gold-light);
            font-weight: 600;
        }

        .table-custom {
            background: rgba(42,42,42,0.5);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 12px;
            overflow: hidden;
        }

        .table-custom thead {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0.05));
        }

        .table-custom th, .table-custom td {
            padding: 15px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            🍽️ Stickusteak
        </div>
        <nav class="mt-3">
            <a href="/php-native/pages/dashboard.php" class="nav-link">
                <i class="bi bi-speedometer2"></i>Dashboard
            </a>
            <a href="/php-native/pages/pos-tables.php" class="nav-link">
                <i class="bi bi-grid"></i>POS Tables
            </a>
            <a href="/php-native/pages/tickets.php" class="nav-link">
                <i class="bi bi-ticket"></i>Tickets
            </a>
            <a href="/php-native/pages/shifts.php" class="nav-link active">
                <i class="bi bi-clock-history"></i>Shifts
            </a>
            <a href="/php-native/pages/orders.php" class="nav-link">
                <i class="bi bi-receipt"></i>Orders (Report)
            </a>
            <a href="/php-native/pages/kds-kitchen.php" class="nav-link" target="_blank">
                <i class="bi bi-fire"></i>Kitchen Display
            </a>
            <a href="/php-native/pages/kds-bar.php" class="nav-link" target="_blank">
                <i class="bi bi-cup-straw"></i>Bar Display
            </a>
            <a href="/php-native/pages/menu.php" class="nav-link">
                <i class="bi bi-egg-fried"></i>Menu
            </a>
            <a href="/php-native/pages/inventory.php" class="nav-link">
                <i class="bi bi-box-seam"></i>Inventory
            </a>
            <a href="/php-native/pages/reports.php" class="nav-link">
                <i class="bi bi-graph-up"></i>Reports
            </a>
            <a href="/php-native/pages/settings.php" class="nav-link">
                <i class="bi bi-gear"></i>Settings
            </a>
            <a href="/php-native/pages/login.php?logout=1" class="nav-link mt-5">
                <i class="bi bi-box-arrow-right"></i>Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title mb-4">🕐 Shift Management</h1>
        
        <?php if (isset($_SESSION['shift_required_message'])): ?>
            <div class="alert alert-warning" style="background: rgba(255, 193, 7, 0.2); border: 2px solid var(--gold-primary); color: var(--gold-light);">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php 
                    echo htmlspecialchars($_SESSION['shift_required_message']);
                    unset($_SESSION['shift_required_message']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Current Shift Status -->
        <div class="shift-status-card" id="shiftStatusCard">
            <div id="shiftStatusContent">
                <i class="bi bi-hourglass-split" style="font-size: 3rem; color: var(--gold-primary);"></i>
                <p class="mt-3">Loading shift status...</p>
            </div>
        </div>

        <!-- Shift History -->
        <div class="mt-5">
            <h3 class="mb-3" style="color: var(--gold-light);">📋 Shift History</h3>
            <div class="table-custom">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Shift #</th>
                            <th>Cashier</th>
                            <th>Date</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Opening</th>
                            <th>Closing</th>
                            <th>Variance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="shiftHistoryBody">
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px;">
                                <i class="bi bi-hourglass-split" style="font-size: 2rem;"></i>
                                <p>Loading history...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Open Shift Modal -->
    <div class="modal fade" id="openShiftModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🕐 Open New Shift</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="openShiftForm">
                        <div class="mb-3">
                            <label class="form-label" style="color: var(--gold-light);">👤 Opened By</label>
                            <div class="form-control-plaintext" style="padding: 10px; background: rgba(212,175,55,0.1); border-radius: 5px; border-left: 3px solid var(--gold-primary);">
                                <strong><?php echo htmlspecialchars($username); ?></strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Opening Balance (Cash in Drawer) *</label>
                            <input type="number" class="form-control" id="openingBalance" step="0.01" value="0" required>
                            <small class="text-muted">Amount of cash at start of shift</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="shiftNotes" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-premium" onclick="openShift()">
                        <i class="bi bi-play-circle me-2"></i>Open Shift
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Close Shift Modal -->
    <div class="modal fade" id="closeShiftModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🔒 Close Shift</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="shiftSummary">
                        <div class="mb-3" style="background: rgba(212,175,55,0.1); padding: 15px; border-radius: 5px; border-left: 3px solid var(--gold-primary);">
                            <h6 class="mb-2" style="color: var(--gold-light);">👤 Shift Information</h6>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div>
                                    <div style="font-size: 12px; color: rgba(255,255,255,0.7);">Opened By</div>
                                    <div style="font-weight: bold; color: var(--gold-light);" id="shiftOpenedBy">-</div>
                                </div>
                                <div>
                                    <div style="font-size: 12px; color: rgba(255,255,255,0.7);">Opened At</div>
                                    <div style="font-weight: bold; color: var(--gold-light);" id="shiftOpenedAt">-</div>
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3" style="color: var(--gold-light);">📊 Shift Summary</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="stat-box">
                                    <div class="stat-value" id="summaryOrders">0</div>
                                    <div class="stat-label">Total Orders</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
                                    <div class="stat-value" id="summarySales">Rp 0</div>
                                    <div class="stat-label">Total Sales</div>
                                </div>
                            </div>
                        </div>
                        <hr style="border-color: rgba(212, 175, 55, 0.3);">
                        <div class="mb-3">
                            <label class="form-label">Closing Balance (Count cash in drawer) *</label>
                            <input type="number" class="form-control" id="closingBalance" step="0.01" required>
                            <small class="text-muted" id="expectedBalanceDisplay">Expected: Rp 0</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="closeShiftNotes" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger-premium" onclick="closeShift()">
                        <i class="bi bi-lock me-2"></i>Close Shift
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentShift = null;
        let openShiftModal, closeShiftModal;

        document.addEventListener('DOMContentLoaded', function() {
            openShiftModal = new bootstrap.Modal(document.getElementById('openShiftModal'));
            closeShiftModal = new bootstrap.Modal(document.getElementById('closeShiftModal'));
            loadShiftStatus();
            loadShiftHistory();
        });

        // Load current shift status
        async function loadShiftStatus() {
            try {
                const response = await fetch('/php-native/api/shifts/active.php');
                const data = await response.json();

                const container = document.getElementById('shiftStatusContent');
                const card = document.getElementById('shiftStatusCard');

                if (data.has_active_shift && data.shift) {
                    currentShift = data.shift;
                    card.className = 'shift-status-card shift-status-active';
                    container.innerHTML = `
                        <div class="status-badge status-active">
                            <i class="bi bi-check-circle me-2"></i>Shift Active
                        </div>
                        <h3 class="mt-3" style="color: var(--gold-light);">Shift #${currentShift.id}</h3>
                        <p class="mb-4">
                            <i class="bi bi-person me-1"></i>Opened by: <strong>${currentShift.opened_by_name || 'Unknown'}</strong><br>
                            <i class="bi bi-clock me-1"></i>Started: ${new Date(currentShift.created_at).toLocaleString('id-ID')}
                        </p>

                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <div class="stat-value">${currentShift.stats?.total_orders || 0}</div>
                                    <div class="stat-label">Orders</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <div class="stat-value">${formatIDR(currentShift.stats?.total_sales || 0)}</div>
                                    <div class="stat-label">Sales</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <div class="stat-value">${formatIDR(currentShift.opening_balance || 0)}</div>
                                    <div class="stat-label">Opening Balance</div>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-danger-premium mt-4" onclick="openCloseShiftModal()">
                            <i class="bi bi-lock me-2"></i>Close Shift
                        </button>
                    `;
                } else {
                    currentShift = null;
                    card.className = 'shift-status-card shift-status-closed';
                    container.innerHTML = `
                        <div class="status-badge status-closed">
                            <i class="bi bi-x-circle me-2"></i>No Active Shift
                        </div>
                        <h3 class="mt-3" style="color: var(--gold-light);">Start Your Shift</h3>
                        <p class="mb-4">Open a new shift to start processing orders</p>
                        <button class="btn btn-premium mt-3" onclick="openOpenShiftModal()">
                            <i class="bi bi-play-circle me-2"></i>Open Shift
                        </button>
                    `;
                }
            } catch (error) {
                console.error('Error loading shift status:', error);
                document.getElementById('shiftStatusContent').innerHTML = '<p class="text-danger">Error loading shift status</p>';
            }
        }

        // Open shift modal
        function openOpenShiftModal() {
            document.getElementById('openShiftForm').reset();
            openShiftModal.show();
        }

        // Close shift modal
        function openCloseShiftModal() {
            if (!currentShift) return;

            document.getElementById('closingBalance').value = '';
            document.getElementById('closeShiftNotes').value = '';

            // Update shift information
            document.getElementById('shiftOpenedBy').textContent = currentShift.opened_by_name || 'Unknown';
            document.getElementById('shiftOpenedAt').textContent = new Date(currentShift.created_at).toLocaleString('id-ID');

            // Update summary with stats from API
            document.getElementById('summaryOrders').textContent = currentShift.stats?.total_orders || 0;
            document.getElementById('summarySales').textContent = formatIDR(currentShift.stats?.total_sales || 0);

            const expected = parseFloat(currentShift.opening_balance || 0) + parseFloat(currentShift.stats?.total_paid || 0);
            document.getElementById('expectedBalanceDisplay').textContent = 'Expected: ' + formatIDR(expected);

            closeShiftModal.show();
        }

        // Open shift
        async function openShift() {
            const openingBalance = document.getElementById('openingBalance').value;
            const notes = document.getElementById('shiftNotes').value;
            
            try {
                const response = await fetch('/php-native/api/shifts/open.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        opening_balance: openingBalance,
                        notes: notes
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    openShiftModal.hide();
                    alert('✅ Shift opened successfully!');
                    loadShiftStatus();
                    loadShiftHistory();
                } else {
                    alert('❌ Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error opening shift:', error);
                alert('❌ Error opening shift');
            }
        }

        // Close shift
        async function closeShift() {
            const closingBalance = document.getElementById('closingBalance').value;
            const notes = document.getElementById('closeShiftNotes').value;
            
            if (!currentShift) return;
            
            try {
                const response = await fetch('/php-native/api/shifts/close.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        shift_id: currentShift.id,
                        closing_balance: closingBalance,
                        notes: notes
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    closeShiftModal.hide();

                    // Show variance alert
                    const variance = parseFloat(data.shift.variance);
                    if (variance !== 0) {
                        const varianceMsg = variance > 0
                            ? `✅ Over by ${formatIDR(Math.abs(variance))}`
                            : `⚠️ Short by ${formatIDR(Math.abs(variance))}`;
                        alert('Shift closed!\n' + varianceMsg + '\n\nOpening shift report...');
                    } else {
                        alert('✅ Shift closed successfully! Balance matches perfectly.\n\nOpening shift report...');
                    }

                    // Open shift report in new window
                    const reportUrl = `/php-native/pages/shift-report.php?shift_id=${data.shift.id}`;
                    window.open(reportUrl, '_blank');

                    loadShiftStatus();
                    loadShiftHistory();
                } else {
                    // Show error with proper formatting
                    let errorMsg = '❌ Error: ' + data.message;
                    
                    // If it's about open tickets, make it more readable
                    if (data.message.includes('ticket(s) still open')) {
                        errorMsg = '⚠️ Cannot Close Shift!\n\n' + data.message + '\n\nPlease process payment for all open tickets first.';
                    }
                    
                    alert(errorMsg);
                }
            } catch (error) {
                console.error('Error closing shift:', error);
                alert('❌ Error closing shift');
            }
        }

        // Load shift history
        async function loadShiftHistory() {
            try {
                const response = await fetch('/php-native/api/shifts/list.php');
                const data = await response.json();
                
                const tbody = document.getElementById('shiftHistoryBody');
                
                if (data.success && data.shifts && data.shifts.length > 0) {
                    let html = '';
                    data.shifts.forEach(shift => {
                        const varianceClass = shift.variance > 0 ? 'text-success' : 
                                            shift.variance < 0 ? 'text-danger' : '';
                        html += `
                            <tr>
                                <td>#${shift.id}</td>
                                <td>${shift.opened_by_name || 'Unknown'}</td>
                                <td>${formatDate(shift.shift_date)}</td>
                                <td>${formatTime(shift.clock_in)}</td>
                                <td>${shift.clock_out ? formatTime(shift.clock_out) : '-'}</td>
                                <td>${formatIDR(shift.opening_balance)}</td>
                                <td>${shift.closing_balance ? formatIDR(shift.closing_balance) : '-'}</td>
                                <td class="${varianceClass}">${shift.variance ? formatIDR(shift.variance) : '-'}</td>
                                <td>
                                    <span class="badge ${shift.status === 'active' ? 'bg-success' : 'bg-secondary'}">
                                        ${shift.status}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 40px;">No shift history</td></tr>';
                }
            } catch (error) {
                console.error('Error loading shift history:', error);
                document.getElementById('shiftHistoryBody').innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 40px; color: red;">Error loading history</td></tr>';
            }
        }

        // Helper functions
        function formatIDR(amount) {
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID', {maximumFractionDigits: 0});
        }

        function formatTime(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'});
        }
    </script>
</body>
</html>
