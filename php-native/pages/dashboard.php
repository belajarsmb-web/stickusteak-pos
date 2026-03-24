<?php
/**
 * Stickusteak POS - Dashboard
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
    <title>Dashboard - Stickusteak POS</title>
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
            --charcoal: #1f1f1f;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
        }

        body {
            background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 100%);
            color: #fff;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--black-tertiary) 0%, var(--black-primary) 100%);
            border-right: 2px solid var(--gold-dark);
            box-shadow: 5px 0 20px rgba(212, 175, 55, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 25px 20px;
            font-size: 1.6rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
            background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold-primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 14px 20px;
            margin: 6px 12px;
            border-radius: 8px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 400;
            position: relative;
            overflow: hidden;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(180deg, var(--gold-primary), var(--gold-light));
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background: rgba(212, 175, 55, 0.1);
            color: var(--gold-light);
            padding-left: 25px;
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(212, 175, 55, 0.1) 100%);
            color: var(--gold-primary);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .sidebar .nav-link.active::before {
            transform: scaleY(1);
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            display: inline-block;
        }

        .logout-btn {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 14px 20px;
            margin: 6px 12px;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .logout-btn:hover {
            background: rgba(220, 53, 69, 0.2);
            color: #ff6b6b;
            border-color: #ff6b6b;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(170, 140, 44, 0.1) 100%);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 16px;
            padding: 35px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(212, 175, 55, 0.15);
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .welcome-title {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .welcome-info {
            color: rgba(255,255,255,0.7);
            font-size: 1rem;
            position: relative;
            z-index: 1;
        }

        .welcome-info strong {
            color: var(--gold-primary);
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 25px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(212, 175, 55, 0.25);
            border-color: var(--gold-primary);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-content {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            flex-shrink: 0;
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            color: var(--black-primary);
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.3);
        }

        .stat-icon.success {
            background: linear-gradient(135deg, #28a745, #34ce57);
            color: white;
            box-shadow: 0 5px 20px rgba(40, 167, 69, 0.3);
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, #ffc107, #ffd93d);
            color: var(--black-primary);
            box-shadow: 0 5px 20px rgba(255, 193, 7, 0.3);
        }

        .stat-icon.danger {
            background: linear-gradient(135deg, #dc3545, #ff6b6b);
            color: white;
            box-shadow: 0 5px 20px rgba(220, 53, 69, 0.3);
        }

        .stat-info {
            flex: 1;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .stat-label {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        /* Content Card */
        .content-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }

        .card-title {
            color: var(--gold-light);
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }

        .btn-premium {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            border: none;
            color: var(--black-primary);
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.4);
            color: var(--black-primary);
        }

        /* Table */
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }

        .table {
            margin: 0;
            color: #fff;
        }

        .table thead th {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(212, 175, 55, 0.1) 100%);
            border: none;
            padding: 16px 12px;
            font-weight: 600;
            color: var(--gold-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
        }

        .table tbody td {
            background: rgba(255,255,255,0.02);
            border-color: rgba(212, 175, 55, 0.1);
            padding: 14px 12px;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: all 0.3s;
        }

        .table tbody tr:hover {
            background: rgba(212, 175, 55, 0.1);
        }

        .table tbody tr[onclick] {
            cursor: pointer;
        }

        .table tbody tr[onclick]:hover {
            background: rgba(212, 175, 55, 0.15);
            transform: translateX(5px);
            transition: all 0.3s ease;
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.completed {
            background: linear-gradient(135deg, #28a745, #34ce57);
            color: white;
        }

        .status-badge.pending {
            background: linear-gradient(135deg, #ffc107, #ffd93d);
            color: var(--black-primary);
        }

        .status-badge.cancelled {
            background: linear-gradient(135deg, #dc3545, #ff6b6b);
            color: white;
        }

        .status-badge.preparing {
            background: linear-gradient(135deg, #17a2b8, #5bc0de);
            color: white;
        }

        /* Loading State */
        .loading-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255,255,255,0.4);
        }

        .loading-state .spinner-border {
            color: var(--gold-primary);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-card, .stat-card, .content-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.2s; }

        .content-card { animation-delay: 0.3s; }

        /* Skeleton Loading */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }

        .skeleton {
            background: linear-gradient(90deg, rgba(212,175,55,0.1) 0%, rgba(212,175,55,0.2) 50%, rgba(212,175,55,0.1) 100%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
            border-radius: 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .main-content {
                margin-left: 0;
            }
            .stats-row {
                grid-template-columns: 1fr;
            }
            .welcome-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="sidebar" style="min-height: 100vh; background: linear-gradient(180deg, var(--black-tertiary) 0%, var(--black-primary) 100%); border-right: 2px solid var(--gold-dark); box-shadow: 5px 0 20px rgba(212, 175, 55, 0.1); width: 260px; position: fixed; left: 0; top: 0; z-index: 1000; display: flex; flex-direction: column;">
                <div class="sidebar-brand" style="padding: 25px 20px; font-size: 1.6rem; font-weight: 700; font-family: 'Playfair Display', serif; border-bottom: 1px solid rgba(212, 175, 55, 0.3); background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold-primary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    🍽️ Stickusteak
                </div>
                <div class="nav flex-column" style="padding: 10px 0;">
                    <a class="nav-link active" href="/php-native/pages/dashboard.php" style="background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(212, 175, 55, 0.1) 100%); color: var(--gold-primary); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="/php-native/pages/pos-tables.php" style="color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;">
                        <i class="bi bi-grid me-2"></i>POS Tables
                    </a>
                    <a class="nav-link" href="/php-native/pages/tickets.php" style="color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;">
                        <i class="bi bi-ticket me-2"></i>Tickets
                    </a>
                    <a class="nav-link" href="/php-native/pages/shifts.php" style="color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;">
                        <i class="bi bi-clock-history me-2"></i>Shifts
                    </a>
                    <a class="nav-link" href="/php-native/pages/tickets.php" style="color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;">
                        <i class="bi bi-receipt me-2"></i>Tickets
                    </a>
                    <a class="nav-link" href="/php-native/pages/kds-kitchen.php" style="color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;" target="_blank">
                        <i class="bi bi-fire me-2"></i>Kitchen Display
                    </a>
                    <a class="nav-link" href="/php-native/pages/kds-bar.php" style="color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;" target="_blank">
                        <i class="bi bi-cup-straw me-2"></i>Bar Display
                    </a>
                    <a class="nav-link" href="/php-native/pages/menu.php" style="color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;">
                        <i class="bi bi-egg-fried me-2"></i>Menu
                    </a>
                    <a class="nav-link" href="/php-native/pages/inventory.php" style="color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;">
                        <i class="bi bi-box-seam me-2"></i>Inventory
                        <span id="lowStockBadge" class="badge bg-danger" style="display: none; position: absolute; right: 20px; top: 14px; font-size: 0.75rem;">0</span>
                    </a>
                    <a class="nav-link" href="/php-native/pages/reports.php" style="color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;">
                        <i class="bi bi-graph-up me-2"></i>Reports
                    </a>
                    <a class="nav-link" href="/php-native/pages/settings.php" style="color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;">
                        <i class="bi bi-gear me-2"></i>Settings
                    </a>
                    <a class="nav-link" href="/php-native/pages/login.php?logout=1" style="color: rgba(255,255,255,0.7); padding: 14px 20px; margin: 6px 12px; border-radius: 8px; transition: all 0.4s; text-decoration: none; display: block;">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content" style="padding-left: 280px; transition: all 0.3s;">
                <!-- Welcome Card -->
                <div class="welcome-card">
                    <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($username); ?>! 👋</h1>
                    <p class="welcome-info">
                        Role: <strong><?php echo htmlspecialchars($userRole); ?></strong> |
                        <?php echo date('l, F j, Y'); ?>
                    </p>
                </div>

                <!-- Quick Stats -->
                <div class="stats-row">
                    <!-- Shift Status Widget -->
                    <div class="stat-card" id="shiftStatusCard" style="background: linear-gradient(135deg, rgba(212,175,55,0.1), rgba(212,175,55,0.05)); border: 2px solid var(--gold-primary);">
                        <div class="stat-content">
                            <div class="stat-icon" style="background: rgba(212,175,55,0.2);">
                                <i class="bi bi-clock-history" style="color: var(--gold-primary);"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value" id="shiftStatus" style="color: var(--gold-primary); font-size: 1.2rem;">Checking...</div>
                                <div class="stat-label">Current Shift</div>
                                <div id="shiftDetails" style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-top: 5px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon primary">
                                <i class="bi bi-cart"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value" id="todayOrders">0</div>
                                <div class="stat-label">Today's Orders</div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon success">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value" id="todayRevenue">Rp 0</div>
                                <div class="stat-label">Today's Revenue</div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon warning">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value" id="pendingOrders">0</div>
                                <div class="stat-label">Pending Orders</div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon danger">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value" id="totalCustomers">0</div>
                                <div class="stat-label">Total Customers</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h5 class="card-title"><i class="bi bi-ticket me-2"></i>Recent Tickets</h5>
                        <a href="/php-native/pages/tickets.php" class="btn-premium">
                            <i class="bi bi-arrow-right me-1"></i>View All Tickets
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="recentTicketsTable">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Table</th>
                                    <th>Customer</th>
                                    <th>Orders</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Opened</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="loading-state">
                                            <div class="spinner-border" role="status"></div>
                                            <p style="margin-top: 15px;">Loading tickets...</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Format currency to IDR
        function formatIDR(amount) {
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
        }

        // Load dashboard stats
        async function loadDashboardStats() {
            try {
                const response = await fetch('/php-native/api/dashboard/stats.php');
                const data = await response.json();
                if (data.success) {
                    document.getElementById('todayOrders').textContent = data.stats.todayOrders || 0;
                    document.getElementById('todayRevenue').textContent = formatIDR(data.stats.todayRevenue || 0);
                    document.getElementById('pendingOrders').textContent = data.stats.pendingOrders || 0;
                    document.getElementById('totalCustomers').textContent = data.stats.totalCustomers || 0;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Load shift status
        async function loadShiftStatus() {
            try {
                const response = await fetch('/php-native/api/shifts/active.php');
                const data = await response.json();
                
                const shiftStatusEl = document.getElementById('shiftStatus');
                const shiftDetailsEl = document.getElementById('shiftDetails');
                const shiftCard = document.getElementById('shiftStatusCard');

                if (data.has_active_shift && data.shift) {
                    shiftStatusEl.textContent = '🟢 OPEN';
                    shiftStatusEl.style.color = '#28a745';
                    shiftDetailsEl.textContent = `Shift #${data.shift.id} | Started: ${new Date(data.shift.created_at || data.shift.opened_at).toLocaleTimeString('id-ID')}`;
                    shiftCard.style.borderColor = '#28a745';
                } else {
                    shiftStatusEl.textContent = '🔴 CLOSED';
                    shiftStatusEl.style.color = '#dc3545';
                    shiftDetailsEl.textContent = 'No active shift - Open a shift to start selling';
                    shiftCard.style.borderColor = '#dc3545';
                }
            } catch (error) {
                console.error('Error loading shift status:', error);
                document.getElementById('shiftStatus').textContent = 'Error';
            }
        }

        // Load recent tickets
        async function loadRecentTickets() {
            try {
                const response = await fetch('/php-native/api/tickets/list-all.php?status=all&limit=5');
                const data = await response.json();
                const tbody = document.querySelector('#recentTicketsTable tbody');

                if (data.success && data.tickets && data.tickets.length > 0) {
                    let html = '';
                    data.tickets.forEach(ticket => {
                        const statusClass = ticket.status === 'paid' || ticket.status === 'closed' ? 'status-completed' : 'status-pending';
                        const isClickable = ticket.status === 'open' || ticket.status === 'paid';
                        const clickAction = isClickable ? `onclick="goToTable(${ticket.table_id})" style="cursor: pointer;"` : '';
                        const titleAttr = isClickable ? `title="Click to open table ${ticket.table_name || ticket.table_id}"` : '';
                        
                        html += `
                            <tr ${clickAction} ${titleAttr}>
                                <td><strong style="color: var(--gold-light); text-decoration: ${isClickable ? 'underline' : 'none'};">${ticket.ticket_number}</strong></td>
                                <td>${ticket.table_name || 'Table ' + ticket.table_id}</td>
                                <td>${ticket.customer_name || '<span style="color: rgba(255,255,255,0.5);">Walk-in</span>'}</td>
                                <td>${ticket.orders_count || 0}</td>
                                <td><strong style="color: var(--gold-primary);">${formatIDR(ticket.total_amount)}</strong></td>
                                <td><span class="status-badge ${statusClass}">${ticket.status.toUpperCase()}</span></td>
                                <td>${new Date(ticket.opened_at || ticket.created_at).toLocaleTimeString('id-ID')}</td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="empty-state">
                                    <i class="bi bi-ticket" style="font-size: 2rem; color: rgba(255,255,255,0.3);"></i>
                                    <p style="margin-top: 10px; color: rgba(255,255,255,0.5);">No tickets yet</p>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error loading recent tickets:', error);
                document.querySelector('#recentTicketsTable tbody').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading tickets</td></tr>';
            }
        }

        // Go to table POS page
        function goToTable(tableId) {
            window.location.href = `/php-native/pages/pos-order.php?table_id=${tableId}`;
        }

        // Load recent orders
        async function loadRecentOrders() {
            try {
                const response = await fetch('/php-native/api/orders/index.php?limit=5');
                const data = await response.json();
                const tbody = document.querySelector('#recentOrdersTable tbody');

                if (data.success && data.orders && data.orders.length > 0) {
                    let html = '';
                    data.orders.forEach(order => {
                        const statusClass = order.status === 'completed' ? 'completed' :
                                           order.status === 'pending' ? 'pending' :
                                           order.status === 'cancelled' ? 'cancelled' : 'preparing';
                        html += `
                            <tr>
                                <td style="color: var(--gold-primary); font-weight: 600;">#${order.id}</td>
                                <td style="color: var(--gold-light);">${order.customer_name || 'Walk-in'}</td>
                                <td><span style="background: rgba(212,175,55,0.2); color: var(--gold-primary); padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;">${order.items_count || 0} items</span></td>
                                <td style="color: var(--gold-primary); font-weight: 600;">${formatIDR(order.total_amount || 0)}</td>
                                <td><span class="status-badge ${statusClass}">${order.status}</span></td>
                                <td style="color: rgba(255,255,255,0.6);">${formatTime(order.created_at)}</td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 60px 20px; color: rgba(255,255,255,0.4);">
                                <i class="bi bi-inbox" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                                No recent orders
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 60px 20px; color: rgba(255,255,255,0.4);">
                            <i class="bi bi-exclamation-triangle" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                            Error loading orders
                        </td>
                    </tr>
                `;
            }
        }

        // Format time
        function formatTime(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }

        // Initialize
        loadDashboardStats();
        loadShiftStatus();
        loadRecentTickets();
        loadLowStockAlerts();

        // Auto-refresh every 30 seconds
        setInterval(() => {
            loadDashboardStats();
            loadShiftStatus();
            loadRecentTickets();
            loadLowStockAlerts();
        }, 30000);

        // Load low stock alerts
        async function loadLowStockAlerts() {
            try {
                const response = await fetch('/php-native/api/inventory/low-stock-alerts.php');
                const data = await response.json();
                
                if (data.success) {
                    const badge = document.getElementById('lowStockBadge');
                    const totalAlerts = data.summary.total_alerts;
                    
                    if (totalAlerts > 0) {
                        badge.textContent = totalAlerts;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            } catch (error) {
                console.error('Error loading low stock alerts:', error);
            }
        }
    </script>
</body>
</html>
