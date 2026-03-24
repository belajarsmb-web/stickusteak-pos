<?php
/**
 * Stickusteak POS - Table Layout (POS) - Premium Black & Gold Theme
 * Visual table layout for taking orders
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

// Check if shift is open
function checkShiftStatus() {
    try {
        require_once __DIR__ . '/../config/database.php';
        $pdo = getDbConnection();
        
        $stmt = $pdo->query("SELECT * FROM shifts WHERE status = 'open' ORDER BY id DESC LIMIT 1");
        $shift = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $shift !== false;
    } catch (Exception $e) {
        return false;
    }
}

if (!checkShiftStatus()) {
    // No active shift, redirect to shifts page
    $_SESSION['shift_required_message'] = 'Please open a shift first before accessing POS Tables';
    header('Location: /php-native/pages/shifts.php');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - Table Layout - Stickusteak POS</title>
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
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--black-tertiary) 0%, var(--black-primary) 100%);
            border-right: 2px solid var(--gold-dark);
            box-shadow: 5px 0 20px rgba(212, 175, 55, 0.1);
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
            border-left: 3px solid var(--gold-primary);
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        .logout-btn {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 14px 20px;
            display: block;
            border-radius: 8px;
            margin: 6px 12px;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }
        
        .main-content { 
            padding: 30px;
        }
        
        .pos-header {
            background: linear-gradient(135deg, var(--black-tertiary) 0%, var(--black-secondary) 100%);
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid rgba(212, 175, 55, 0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        
        .pos-header h2 {
            background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: linear-gradient(135deg, var(--black-tertiary) 0%, var(--black-secondary) 100%);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(212, 175, 55, 0.2);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .stat-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.1), transparent);
            transition: left 0.5s;
        }
        
        .stat-box:hover {
            transform: translateY(-8px);
            border-color: var(--gold-primary);
            box-shadow: 0 15px 40px rgba(212, 175, 55, 0.2);
        }
        
        .stat-box:hover::before {
            left: 100%;
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .table-layout-container {
            background: linear-gradient(135deg, var(--black-tertiary) 0%, var(--black-secondary) 100%);
            border-radius: 15px;
            padding: 40px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }
        
        .floor-plan {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 35px;
            justify-items: center;
            align-items: center;
        }
        
        .table-item {
            width: 160px;
            height: 100px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: 2px solid;
            background: linear-gradient(135deg, rgba(30,30,30,0.9) 0%, rgba(20,20,20,0.9) 100%);
            animation: fadeInUp 0.6s ease-out;
        }
        
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
        
        .table-item:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 50px rgba(212, 175, 55, 0.3);
        }
        
        .table-item.available {
            border-color: #28a745;
            box-shadow: 0 5px 20px rgba(40, 167, 69, 0.3);
        }
        
        .table-item.available:hover {
            box-shadow: 0 20px 50px rgba(40, 167, 69, 0.5);
        }
        
        .table-item.occupied {
            border-color: #dc3545;
            box-shadow: 0 5px 20px rgba(220, 53, 69, 0.3);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 5px 20px rgba(220, 53, 69, 0.3);
            }
            50% {
                box-shadow: 0 5px 30px rgba(220, 53, 69, 0.6);
            }
        }
        
        .table-item.reserved {
            border-color: #ffc107;
            box-shadow: 0 5px 20px rgba(255, 193, 7, 0.3);
        }
        
        .table-item.maintenance {
            border-color: #6c757d;
            opacity: 0.6;
        }
        
        .table-number {
            font-size: 1.6rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            color: var(--gold-primary);
        }
        
        .table-info {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.6);
            margin-top: 5px;
        }
        
        .table-status {
            position: absolute;
            top: -12px;
            right: -12px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.85rem;
            border: 2px solid var(--gold-primary);
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
        }
        
        .legend {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid rgba(212, 175, 55, 0.3);
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }
        
        .legend-color {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            border: 2px solid var(--gold-dark);
        }
        
        .btn-refresh {
            background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold-primary) 100%);
            border: none;
            color: var(--black-primary);
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-refresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.4);
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold-primary) 100%);
            border: none;
            color: var(--black-primary);
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-gold:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: rgba(255,255,255,0.4);
        }
        
        .empty-state i {
            font-size: 5rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--black-primary);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gold-dark);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gold-primary);
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
                    <a href="/php-native/pages/pos-tables.php" class="nav-link active">
                        <i class="bi bi-grid-3x3-gap"></i>POS Tables
                    </a>
                    <a href="/php-native/pages/kds-kitchen.php" class="nav-link" target="_blank">
                        <i class="bi bi-egg-fried"></i>Kitchen Display
                    </a>
                    <a href="/php-native/pages/kds-bar.php" class="nav-link" target="_blank">
                        <i class="bi bi-cup-straw"></i>Bar Display
                    </a>
                    <a href="/php-native/pages/tickets.php" class="nav-link">
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
                <!-- Header -->
                <div class="pos-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1"><i class="bi bi-grid-3x3-gap me-2"></i>Table Layout</h2>
                            <p class="mb-0" style="color: rgba(255,255,255,0.5); font-size: 0.9rem;">Premium Dining Experience</p>
                        </div>
                        <div>
                            <button class="btn btn-refresh" onclick="loadTables()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Bar -->
                <div class="stats-bar">
                    <div class="stat-box">
                        <div class="stat-value" id="totalTables">0</div>
                        <div class="stat-label">Total Tables</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value text-success" id="availableTables" style="background: linear-gradient(135deg, #28a745, #34ce57); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">0</div>
                        <div class="stat-label">Available</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value" id="occupiedTables" style="background: linear-gradient(135deg, #dc3545, #ff6b6b); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">0</div>
                        <div class="stat-label">Occupied</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value" id="reservedTables" style="background: linear-gradient(135deg, #ffc107, #ffd93d); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">0</div>
                        <div class="stat-label">Reserved</div>
                    </div>
                </div>

                <!-- Table Layout -->
                <div class="table-layout-container">
                    <div class="floor-plan" id="floorPlan">
                        <div class="empty-state">
                            <i class="bi bi-hourglass-split"></i>
                            <h3>Loading tables...</h3>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="legend">
                        <div class="legend-item">
                            <div class="legend-color" style="background: linear-gradient(135deg, #28a745, #34ce57);"></div>
                            <span>Available</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: linear-gradient(135deg, #dc3545, #ff6b6b);"></div>
                            <span>Occupied</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: linear-gradient(135deg, #ffc107, #ffd93d);"></div>
                            <span>Reserved</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: linear-gradient(135deg, #6c757d, #868e96);"></div>
                            <span>Maintenance</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load tables from API
        async function loadTables() {
            try {
                const response = await fetch('/php-native/api/tables/index.php');
                const data = await response.json();
                
                const floorPlan = document.getElementById('floorPlan');
                
                if (data.success && data.tables && data.tables.length > 0) {
                    let html = '';
                    let available = 0, occupied = 0, reserved = 0, maintenance = 0;
                    
                    data.tables.forEach((table, index) => {
                        const status = table.status || 'available';
                        const statusClass = status === 'occupied' ? 'occupied' :
                                          status === 'reserved' ? 'reserved' :
                                          status === 'maintenance' ? 'maintenance' : 'available';
                        
                        // Count stats
                        if (status === 'available') available++;
                        else if (status === 'occupied') occupied++;
                        else if (status === 'reserved') reserved++;
                        else maintenance++;
                        
                        // Get current order info if occupied
                        const orderInfo = table.current_order ? 
                            `<div class="table-info">Order #${table.current_order}</div>` : 
                            `<div class="table-info">${table.capacity || '-'} seats</div>`;
                        
                        const statusIcon = status === 'occupied' ? '<i class="bi bi-person-fill"></i>' :
                                          status === 'reserved' ? '<i class="bi bi-clock"></i>' :
                                          status === 'maintenance' ? '<i class="bi bi-tools"></i>' : '';
                        
                        // Stagger animation
                        const delay = index * 0.1;
                        
                        html += `
                            <div class="table-item ${statusClass}"
                                 onclick="openTableOrder(${table.id}, '${table.name || 'Table ' + table.id}', '${status}')"
                                 style="animation-delay: ${delay}s">
                                <div class="table-number">${table.name || table.id}</div>
                                ${orderInfo}
                                ${statusIcon ? `<div class="table-status">${statusIcon}</div>` : ''}
                                <button class="btn btn-sm btn-gold mt-2" 
                                        onclick="event.stopPropagation(); openViewTickets(${table.id})"
                                        style="width: 100%; font-size: 0.75rem;">
                                    🎫 View Tickets
                                </button>
                            </div>
                        `;
                    });
                    
                    floorPlan.innerHTML = html;
                    
                    // Update stats
                    document.getElementById('totalTables').textContent = data.tables.length;
                    document.getElementById('availableTables').textContent = available;
                    document.getElementById('occupiedTables').textContent = occupied;
                    document.getElementById('reservedTables').textContent = reserved;
                } else {
                    floorPlan.innerHTML = `
                        <div class="empty-state" style="grid-column: 1/-1;">
                            <i class="bi bi-inbox"></i>
                            <h3>No tables configured</h3>
                            <p>Add tables from Settings page</p>
                            <a href="/php-native/pages/settings.php#tables-tab" class="btn btn-refresh mt-3">
                                <i class="bi bi-plus-lg me-1"></i>Add Table
                            </a>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading tables:', error);
                document.getElementById('floorPlan').innerHTML = `
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="bi bi-exclamation-triangle"></i>
                        <h3>Error loading tables</h3>
                    </div>
                `;
            }
        }

        // Open table order page
        function openTableOrder(tableId, tableName, status) {
            if (status === 'maintenance') {
                alert('This table is under maintenance and cannot be used.');
                return;
            }
            window.location.href = `/php-native/pages/pos-order.php?table_id=${tableId}&table_name=${encodeURIComponent(tableName)}&status=${status}`;
        }

        // Open view tickets page
        function openViewTickets(tableId) {
            window.location.href = `/php-native/pages/view-tickets.php?table_id=${tableId}`;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', loadTables);
        
        // Auto-refresh tables every 5 seconds to update status from mobile orders
        setInterval(loadTables, 5000);
    </script>
</body>
</html>
