<?php
/**
 * Stickusteak POS - Order Page for Specific Table
 * Premium Black & Gold Theme
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'Staff';
$tableId = $_GET['table_id'] ?? 0;
$tableName = $_GET['table_name'] ?? 'Table';
$tableStatus = $_GET['status'] ?? 'available';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>POS Order - <?php echo htmlspecialchars($tableName); ?></title>
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

        * { font-family: 'Poppins', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', serif; }

        body {
            background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 100%);
            color: #fff;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--black-tertiary) 0%, var(--black-primary) 100%);
            border-right: 2px solid var(--gold-dark);
            box-shadow: 5px 0 20px rgba(212, 175, 55, 0.1);
            width: 260px;
            position: fixed;
            left: 0;
            top: 0;
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

        /* Main Layout */
        .main-wrapper {
            margin-left: 260px;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Menu Section */
        .menu-section {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }

        .btn-premium-outline {
            background: transparent;
            border: 1px solid var(--gold-primary);
            color: var(--gold-primary);
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-premium-outline:hover {
            background: var(--gold-primary);
            color: var(--black-primary);
            transform: translateY(-2px);
        }

        /* Search Box */
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box input {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #fff;
            padding: 12px 20px 12px 45px;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
            color: #fff;
        }

        .search-box input::placeholder {
            color: rgba(255,255,255,0.4);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
        }

        /* Category Tabs */
        .category-tabs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }

        .category-tab {
            padding: 10px 20px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 20px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.3s;
            color: rgba(255,255,255,0.7);
            font-weight: 500;
        }

        .category-tab:hover, .category-tab.active {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            border-color: var(--gold-primary);
            color: var(--black-primary);
        }

        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .menu-item-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .menu-item-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .menu-item-card:hover {
            border-color: var(--gold-primary);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.2);
        }

        .menu-item-card:hover::before {
            opacity: 1;
        }

        .menu-item-image {
            width: 100%;
            height: 140px;
            object-fit: cover;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .menu-item-icon {
            width: 100%;
            height: 140px;
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--black-primary);
            font-size: 3rem;
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .menu-item-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--gold-light);
            margin-bottom: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .menu-item-price {
            color: var(--gold-primary);
            font-weight: 700;
            font-size: 1rem;
        }

        /* Cart Section */
        .cart-section {
            width: 420px;
            background: linear-gradient(135deg, rgba(42,42,42,0.95) 0%, rgba(26,26,26,0.95) 100%);
            border-left: 2px solid rgba(212, 175, 55, 0.3);
            display: flex;
            flex-direction: column;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.5);
        }

        .cart-header {
            padding: 25px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            background: rgba(0,0,0,0.2);
        }

        .table-badge {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            color: var(--black-primary);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-top: 8px;
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .cart-item {
            display: flex;
            gap: 12px;
            padding: 15px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 12px;
            margin-bottom: 12px;
            transition: all 0.3s;
        }

        .cart-item:hover {
            border-color: var(--gold-primary);
            background: rgba(212, 175, 55, 0.05);
        }

        .cart-item-qty {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            border: 1px solid rgba(212, 175, 55, 0.3);
            background: rgba(255,255,255,0.05);
            color: var(--gold-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .qty-btn:hover {
            background: var(--gold-primary);
            color: var(--black-primary);
            transform: scale(1.1);
        }

        .cart-footer {
            padding: 25px;
            border-top: 1px solid rgba(212, 175, 55, 0.2);
            background: rgba(0,0,0,0.2);
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--gold-primary);
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .btn-premium {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary), var(--gold-light));
            border: none;
            color: var(--black-primary);
            padding: 14px 20px;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
            color: var(--black-primary);
        }

        .btn-premium-sm {
            padding: 8px 16px;
            font-size: 0.85rem;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255,255,255,0.4);
        }

        .empty-cart i {
            font-size: 5rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Modal Styles */
        .modal-content {
            background: linear-gradient(135deg, var(--black-tertiary) 0%, var(--black-secondary) 100%);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 16px;
        }

        .modal-header {
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            padding: 20px 25px;
        }

        .modal-title {
            color: var(--gold-light);
            font-weight: 600;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid rgba(212, 175, 55, 0.2);
            padding: 15px 25px;
        }

        .btn-close-white {
            filter: invert(1);
        }

        .form-control, .form-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #fff;
            padding: 12px 15px;
            border-radius: 10px;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
            color: #fff;
        }

        /* Fix dropdown options visibility */
        .form-select option {
            background: #1a1a1a;
            color: #fff;
        }

        .form-select option:hover {
            background: var(--gold-dark);
            color: #000;
        }

        .form-select option:checked {
            background: var(--gold-primary);
            color: #000;
        }

        .form-label {
            color: rgba(255,255,255,0.8);
            font-weight: 500;
            margin-bottom: 8px;
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

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.2);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gold-dark);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gold-primary);
        }

        /* Modifier Modal Horizontal Scroll */
        #modifierGroupsContainer::-webkit-scrollbar {
            height: 10px;
        }

        #modifierGroupsContainer::-webkit-scrollbar-track {
            background: rgba(212, 175, 55, 0.1);
            border-radius: 5px;
        }

        #modifierGroupsContainer::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, var(--gold-dark), var(--gold-primary));
            border-radius: 5px;
        }

        #modifierGroupsContainer::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, var(--gold-primary), var(--gold-light));
        }

        .modifier-group-card::-webkit-scrollbar {
            width: 6px;
        }

        .modifier-group-card::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
        }

        .modifier-group-card::-webkit-scrollbar-thumb {
            background: var(--gold-dark);
            border-radius: 3px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-wrapper {
                flex-direction: column;
            }
            .cart-section {
                width: 100%;
                height: 40%;
                border-left: none;
                border-top: 2px solid rgba(212, 175, 55, 0.3);
            }
            .menu-section {
                height: 60%;
            }
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
            <a href="/php-native/pages/tickets.php" class="nav-link">
                <i class="bi bi-receipt"></i>Tickets
            </a>
            <a href="/php-native/pages/pos-tables.php" class="nav-link active">
                <i class="bi bi-grid-3x3-gap"></i>POS Tables
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
                <i class="bi bi-box-arrow-left me-2"></i>Logout
            </a>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Menu Section -->
        <div class="menu-section">
            <div class="page-header">
                <div>
                    <h4 class="page-title"><i class="bi bi-egg-fried me-2"></i>Menu</h4>
                    <p style="color: rgba(255,255,255,0.5); margin: 5px 0 0 38px; font-size: 0.9rem;">Select items to add to order for <?php echo htmlspecialchars($tableName); ?></p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-warning btn-premium-sm" onclick="showHeldOrders()">
                        <i class="bi bi-pause-circle me-1"></i>Resume Hold
                    </button>
                    <a href="/php-native/pages/pos-tables.php" class="btn-premium-outline">
                        <i class="bi bi-arrow-left me-1"></i>Back to Tables
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="searchMenu" placeholder="Search menu items..." onkeyup="filterMenu()">
            </div>

            <!-- Category Tabs -->
            <div class="category-tabs" id="categoryTabs">
                <button class="category-tab active" onclick="filterCategory('all')">All Items</button>
            </div>

            <!-- Menu Grid -->
            <div class="menu-grid" id="menuGrid">
                <div class="loading-state">
                    <div class="spinner-border" role="status"></div>
                    <p style="margin-top: 15px;">Loading menu...</p>
                </div>
            </div>
        </div>

        <!-- Cart Section -->
        <div class="cart-section">
            <div class="cart-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" style="color: var(--gold-light);"><i class="bi bi-cart me-2"></i>Current Order</h5>
                        <span class="table-badge"><?php echo htmlspecialchars($tableName); ?></span>
                    </div>
                    <span class="badge" style="background: <?php echo $tableStatus === 'occupied' ? 'linear-gradient(135deg, #dc3545, #ff6b6b)' : 'linear-gradient(135deg, #28a745, #34ce57)'; ?>; color: white; padding: 8px 16px; border-radius: 20px; font-weight: 600;">
                        <?php echo ucfirst($tableStatus); ?>
                    </span>
                </div>
            </div>

            <div class="cart-items" id="cartItems">
                <div class="empty-cart">
                    <i class="bi bi-cart-x"></i>
                    <p>No items added yet</p>
                    <p class="small" style="color: rgba(255,255,255,0.4);">Click on menu items to add them to the order</p>
                </div>
            </div>

            <div class="cart-footer">
                <div class="cart-total">
                    <span>Total</span>
                    <span id="cartTotal">Rp 0</span>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-premium-outline btn-premium-sm" onclick="clearCart()">
                        <i class="bi bi-trash me-1"></i>Clear
                    </button>
                    <button class="btn btn-premium-outline btn-premium-sm" onclick="holdOrder()">
                        <i class="bi bi-pause me-1"></i>Hold
                    </button>
                    <button class="btn btn-warning btn-premium-sm" onclick="completeOrder()" id="btnCompleteOrder" style="display: none;">
                        <i class="bi bi-check-circle me-1"></i>Complete
                    </button>
                    <button class="btn btn-premium" onclick="submitOrder()" style="grid-column: 1 / -1;">
                        <i class="bi bi-cart-plus me-2"></i>Submit Order
                    </button>
                    <button class="btn btn-success btn-premium" onclick="openPaymentModal()" id="btnPayOrder" style="display: none; grid-column: 1 / -1;">
                        <i class="bi bi-cash me-2"></i>Bayar / Pay
                    </button>
                    <button class="btn btn-premium-outline" onclick="closeTableOrder()" style="grid-column: 1 / -1;">
                        <i class="bi bi-x-lg me-2"></i>Tutup / Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Modal -->
    <div class="modal fade" id="notesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-chat-left-text me-2"></i>Add Notes to Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="currentItemId">
                    <div class="mb-3">
                        <label class="form-label">Select Notes:</label>
                        <div class="category-filter" id="notesFilter">
                            <button class="category-btn active" data-category="all" onclick="filterNotesModal('all')">All</button>
                            <button class="category-btn" data-category="kitchen" onclick="filterNotesModal('kitchen')">🍳 Kitchen</button>
                            <button class="category-btn" data-category="bar" onclick="filterNotesModal('bar')">🍹 Bar</button>
                            <button class="category-btn" data-category="general" onclick="filterNotesModal('general')">📝 General</button>
                        </div>
                    </div>
                    <div id="notesSelection" class="p-3 border rounded" style="max-height: 300px; overflow-y: auto; border-color: rgba(212,175,55,0.2) !important; background: rgba(255,255,255,0.03);">
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Selected Notes:</label>
                        <div id="selectedNotesDisplay" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-premium-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-premium" onclick="saveItemNotes()">
                        <i class="bi bi-check-lg me-1"></i>Save Notes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #34ce57);">
                    <h5 class="modal-title"><i class="bi bi-cash me-2"></i>Payment / Pembayaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="paymentOrderId">

                    <div class="bill-breakdown mb-3" style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 12px; border: 1px solid rgba(212,175,55,0.2);">
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid rgba(212,175,55,0.1);">
                            <span>Subtotal</span>
                            <span id="billSubtotal" style="color: var(--gold-primary);">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid rgba(212,175,55,0.1);">
                            <span>Service Charge (<span id="serviceRate">5</span>%)</span>
                            <span id="billService" style="color: var(--gold-primary);">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid rgba(212,175,55,0.1);">
                            <span>Tax (<span id="taxRate">10</span>%)</span>
                            <span id="billTax" style="color: var(--gold-primary);">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-3 fw-bold fs-5" style="color: var(--gold-light);">
                            <span>TOTAL</span>
                            <span id="billTotal" style="color: var(--gold-primary);">Rp 0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method / Metode Pembayaran</label>
                        <div class="row g-2" id="paymentMethodsContainer">
                            <div class="text-center py-4">
                                <div class="spinner-border" style="color: var(--gold-primary);" role="status"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="cashPaymentSection" style="display: none;">
                        <label class="form-label">Amount Paid (Rp)</label>
                        <input type="number" class="form-control form-control-lg" id="amountPaid" placeholder="0" oninput="calculateChange()">
                        <div class="mt-2">
                            <button type="button" class="btn btn-premium-outline btn-premium-sm me-1" onclick="setExactAmount()">Exact</button>
                            <button type="button" class="btn btn-premium-outline btn-premium-sm me-1" onclick="setAmount(50000)">50.000</button>
                            <button type="button" class="btn btn-premium-outline btn-premium-sm me-1" onclick="setAmount(100000)">100.000</button>
                        </div>
                    </div>

                    <div class="alert" style="background: rgba(40,167,69,0.2); border: 1px solid #28a745; color: #34ce57;" id="changeDisplay" style="display: none;">
                        <div class="d-flex justify-content-between">
                            <span><i class="bi bi-arrow-return-left me-2"></i>Change / Kembalian:</span>
                            <strong class="fs-4" id="changeAmount" style="color: var(--gold-primary);">Rp 0</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-premium-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-premium btn-lg" onclick="processPayment()">
                        <i class="bi bi-check-circle me-2"></i>Process Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modifier Modal -->
    <div class="modal fade" id="modifierModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));">
                    <h5 class="modal-title" style="color: var(--black-primary);"><i class="bi bi-ui-checks-grid me-2"></i>Customize Your Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modifierItemId">
                    <input type="hidden" id="modifierItemName">
                    <input type="hidden" id="modifierItemPrice">

                    <div class="d-flex justify-content-between align-items-center mb-3" style="background: rgba(212,175,55,0.1); padding: 15px; border-radius: 12px; border: 1px solid rgba(212,175,55,0.2);">
                        <h6 class="mb-0" id="modifierItemDisplay" style="color: var(--gold-light); font-size: 1.2rem;">Item Name</h6>
                        <span style="color: var(--gold-primary); font-weight: 700; font-size: 1.1rem;">Rp <span id="modifierBasePrice">0</span></span>
                    </div>

                    <div id="modifierGroupsContainer" style="display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px; max-height: 400px;">
                        <!-- Modifier groups will be loaded here horizontally -->
                    </div>

                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center" style="border-color: rgba(212,175,55,0.2) !important; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px;">
                        <div>
                            <strong style="color: rgba(255,255,255,0.8);">Price Adjustment:</strong>
                            <strong style="color: var(--gold-primary); font-size: 1.3rem; margin-left: 10px;" id="modifierPriceAdjustment">+Rp 0</strong>
                        </div>
                        <div>
                            <strong style="color: var(--gold-light); font-size: 1.2rem;">Total: </strong>
                            <strong style="color: var(--gold-primary); font-size: 1.5rem;" id="modifierTotalPrice">Rp 0</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: rgba(0,0,0,0.2);">
                    <button type="button" class="btn btn-premium-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-premium btn-lg" onclick="confirmAddToCart()">
                        <i class="bi bi-cart-plus me-2"></i>Add to Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Void Modal -->
    <div class="modal fade" id="voidModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #dc3545, #ff6b6b);">
                    <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Void Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="voidItemId">
                    <input type="hidden" id="voidOrderId">

                    <div class="alert" style="background: rgba(255,193,7,0.2); border: 1px solid #ffc107; color: #ffd93d;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        This action will void the selected item from the order.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Reason:</label>
                        <select class="form-select" id="voidReasonSelect" onchange="toggleVoidReasonOther()">
                            <option value="">-- Select Reason --</option>
                        </select>
                    </div>

                    <div class="mb-3" id="voidReasonOtherDiv" style="display: none;">
                        <label class="form-label">Other Reason (please specify):</label>
                        <textarea class="form-control" id="voidReasonOther" rows="2" placeholder="Enter reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-premium-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-premium" style="background: linear-gradient(135deg, #dc3545, #ff6b6b);" onclick="confirmVoidItem()">
                        <i class="bi bi-x-circle me-2"></i>Void Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Held Orders Modal -->
    <div class="modal fade" id="heldOrdersModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));">
                    <h5 class="modal-title"><i class="bi bi-pause-circle me-2"></i>Order Ditahan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <p style="color: rgba(255,255,255,0.5); font-size: 0.85rem; margin-bottom: 15px;">
                        Klik order untuk melanjutkan pesanan
                    </p>
                    <div id="heldOrdersContent">
                        <p style="text-align: center; color: rgba(255,255,255,0.5);">Tidak ada order yang ditahan</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="clearHeldOrders()">
                        <i class="bi bi-trash me-1"></i>Hapus Semua
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reprint Modal -->
    <div class="modal fade" id="reprintModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #ffc107, #ffd93d);">
                    <h5 class="modal-title" style="color: var(--black-primary);"><i class="bi bi-printer me-2"></i>Reprint Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reprintItemId">
                    <input type="hidden" id="reprintOrderId">

                    <div class="alert" style="background: rgba(255,193,7,0.2); border: 1px solid #ffc107; color: #ffd93d;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        This item has already been submitted to kitchen. Please provide a reason for reprint.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Reprint Reason:</label>
                        <select class="form-select" id="reprintReasonSelect" onchange="toggleReprintReasonOther()">
                            <option value="">-- Select Reason --</option>
                        </select>
                    </div>

                    <div class="mb-3" id="reprintReasonOtherDiv" style="display: none;">
                        <label class="form-label">Other Reason (please specify):</label>
                        <textarea class="form-control" id="reprintReasonOther" rows="2" placeholder="Enter reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-premium-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-premium" style="background: linear-gradient(135deg, #ffc107, #ffd93d); color: var(--black-primary);" onclick="confirmReprintItem()">
                        <i class="bi bi-printer me-2"></i>Reprint
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const tableId = <?php echo (int)$tableId; ?>;
        const tableName = <?php echo json_encode($tableName); ?>;
        const tableStatus = <?php echo json_encode($tableStatus); ?>;

        let menuItems = [];
        let cart = [];
        let categories = [];
        let currentOrders = [];
        let itemNotes = [];
        let tempSelectedNotes = [];
        let paymentMethods = [];
        let selectedPaymentMethod = null;
        let currentTotal = 0;
        let modifierGroups = [];
        let selectedModifiers = {};
        let pendingCartItem = null;
        let taxSettings = { tax: 10, service: 5, taxEnabled: true, serviceEnabled: true };
        let printReasons = [];
        let voidReasons = [];

        // Load menu items
        async function loadMenu() {
            try {
                const response = await fetch('/php-native/api/menu/index.php');
                const data = await response.json();

                if (data.success && data.items) {
                    menuItems = data.items;
                    categories = [...new Set(data.items.map(item => item.category_name).filter(Boolean))];
                    renderCategoryTabs();
                    renderMenu(menuItems);
                }
            } catch (error) {
                console.error('Error loading menu:', error);
            }
        }

        // Render category tabs
        function renderCategoryTabs() {
            const tabsContainer = document.getElementById('categoryTabs');
            let html = '<button class="category-tab active" onclick="filterCategory(\'all\')">All Items</button>';
            categories.forEach(cat => {
                html += `<button class="category-tab" onclick="filterCategory('${cat}')">${cat}</button>`;
            });
            tabsContainer.innerHTML = html;
        }

        // Filter by category
        function filterCategory(category) {
            document.querySelectorAll('.category-tab').forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
            if (category === 'all') {
                renderMenu(menuItems);
            } else {
                const filtered = menuItems.filter(item => item.category_name === category);
                renderMenu(filtered);
            }
        }

        // Filter by search
        function filterMenu() {
            const search = document.getElementById('searchMenu').value.toLowerCase();
            const filtered = menuItems.filter(item =>
                item.name.toLowerCase().includes(search) ||
                (item.description && item.description.toLowerCase().includes(search))
            );
            renderMenu(filtered);
        }

        // Render menu grid
        function renderMenu(items) {
            const grid = document.getElementById('menuGrid');
            if (items.length === 0) {
                grid.innerHTML = '<div class="loading-state"><p>No menu items found</p></div>';
                return;
            }
            let html = '';
            items.forEach(item => {
                const icon = getMenuIcon(item.category_name);
                const iconClass = getCategoryIconClass(item.category_name);
                const imageHtml = item.image_url && item.image_url.trim() !== '' ?
                    `<img src="${item.image_url}" alt="${item.name}" class="menu-item-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">` +
                    `<div class="menu-item-icon" style="display:none;"><i class="bi ${iconClass}"></i></div>` :
                    `<div class="menu-item-icon"><i class="bi ${iconClass}"></i></div>`;
                html += `
                    <div class="menu-item-card" onclick="addToCart(${item.id})">
                        ${imageHtml}
                        <div class="menu-item-name">${item.name}</div>
                        <div class="menu-item-price">Rp ${parseFloat(item.price).toLocaleString('id-ID')}</div>
                    </div>
                `;
            });
            grid.innerHTML = html;
        }

        // Get menu icon based on category
        function getMenuIcon(category) {
            const icons = {
                'Steak Premium': 'bi-heart-fill',
                'Rice & Pasta': 'bi-bowl',
                'Appetizer': 'bi-flower1',
                'Beverages': 'bi-cup-straw',
                'Dessert': 'bi-cake2',
                'Coffee & Tea': 'bi-cup-hot',
                'Premium Steaks': 'bi-egg-fried',
                'Burgers & Sandwiches': 'bi-hamburger',
                'Side Dishes': 'bi-cup-straw',
                'Beverages': 'bi-cup-hot'
            };
            return icons[category] || 'bi-egg-fried';
        }
        
        function getCategoryIconClass(category) {
            const icons = {
                'Steak Premium': 'bi-heart-fill',
                'Rice & Pasta': 'bi-bowl',
                'Appetizer': 'bi-flower1',
                'Beverages': 'bi-cup-straw',
                'Dessert': 'bi-cake2',
                'Coffee & Tea': 'bi-cup-hot'
            };
            return icons[category] || 'bi-egg-fried';
        }

        // Get menu icon based on category
        function getCategoryIconClass(category) {
            const icons = {
                'Steak Premium': 'bi-heart-fill',
                'Rice & Pasta': 'bi-bowl',
                'Appetizer': 'bi-flower1',
                'Beverages': 'bi-cup-straw',
                'Dessert': 'bi-cake2',
                'Coffee & Tea': 'bi-cup-hot'
            };
            return icons[category] || 'bi-egg-fried';
        }

        // Add to cart - Each item is individual, don't auto-merge
        async function addToCart(itemId) {
            const item = menuItems.find(i => i.id === itemId);
            if (!item) return;
            await loadModifierGroupsForCategory(item.category_id);
            const hasRequiredModifiers = modifierGroups.some(g => g.selection_type.includes('required'));
            if (modifierGroups.length > 0) {
                pendingCartItem = { ...item };
                showModifierModal(item);
            } else {
                addItemToCart(item);
            }
        }

        // Add item to cart - Each click creates NEW individual item
        function addItemToCart(item, modifiers = [], priceAdjustment = 0) {
            // Don't merge items - each click creates new individual item
            // This allows different notes/modifiers for each item
            const newItem = {
                id: item.id,
                name: item.name,
                price: parseFloat(item.price) + priceAdjustment,
                basePrice: parseFloat(item.price),
                quantity: 1, // Always start with 1
                modifiers: modifiers || [],
                notes: [],
                priceAdjustment: priceAdjustment,
                orderItemId: null // New item, not in database yet
            };
            cart.push(newItem);
            console.log('Item added to cart:', newItem);
            console.log('Cart now has', cart.length, 'items');
            renderCart();
        }

        // Remove from cart - Support both individual items and quantity
        function removeFromCart(itemId) {
            const index = cart.findIndex(i => i.id === itemId);
            if (index > -1) {
                // For items from database (with orderItemId), don't reduce quantity, just remove
                if (cart[index].orderItemId) {
                    cart.splice(index, 1);
                } else {
                    // For new items, can reduce quantity
                    if (cart[index].quantity > 1) {
                        cart[index].quantity--;
                    } else {
                        cart.splice(index, 1);
                    }
                }
            }
            renderCart();
        }

        // Increase item quantity
        function increaseQuantity(itemId) {
            const item = cart.find(i => i.id === itemId);
            if (item) {
                item.quantity++;
                renderCart();
            }
        }

        // Render cart
        function renderCart() {
            const cartContainer = document.getElementById('cartItems');
            const btnComplete = document.getElementById('btnCompleteOrder');
            const btnPay = document.getElementById('btnPayOrder');
            if (btnComplete) btnComplete.style.display = currentOrders.length > 0 ? 'inline-block' : 'none';
            if (btnPay) btnPay.style.display = (cart.length > 0 || currentOrders.length > 0) ? 'inline-block' : 'none';

            if (cart.length === 0) {
                cartContainer.innerHTML = `
                    <div class="empty-cart">
                        <i class="bi bi-cart-x"></i>
                        <p>No items added yet</p>
                        <p class="small" style="color: rgba(255,255,255,0.4);">Click on menu items to add them to the order</p>
                    </div>
                `;
                document.getElementById('cartTotal').textContent = 'Rp 0';
                return;
            }

            let html = '';
            let total = 0;
            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                let notesHtml = '';
                if (item.notes && item.notes.length > 0) {
                    notesHtml = '<div class="mt-1">';
                    item.notes.forEach(noteName => {
                        notesHtml += `<span class="badge" style="background: rgba(212,175,55,0.3); color: var(--gold-primary); border: none; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem;">${noteName}</span>`;
                    });
                    notesHtml += '</div>';
                }
                html += `
                    <div class="cart-item" style="${item.is_voided ? 'opacity: 0.5; background: rgba(220,53,69,0.1);' : ''}">
                        <div style="flex: 1;">
                            <div class="fw-semibold" style="color: var(--gold-light);">
                                ${item.name}
                                ${item.is_voided ? '<span class="badge" style="background: linear-gradient(135deg, #dc3545, #ff6b6b);">VOID</span>' : ''}
                                ${item.is_printed ? '<span class="badge" style="background: linear-gradient(135deg, #28a745, #34ce57);">SUBMITTED</span>' : ''}
                            </div>
                            <div style="color: rgba(255,255,255,0.5); font-size: 0.85rem; margin: 5px 0;">Rp ${item.basePrice ? item.basePrice.toLocaleString('id-ID') : item.price.toLocaleString('id-ID')} x ${item.quantity}</div>
                            ${item.modifiers && item.modifiers.length > 0 ? `
                                <div style="color: rgba(255,255,255,0.5); font-size: 0.8rem; margin: 5px 0;">
                                    <i class="bi bi-ui-checks-grid me-1"></i>${item.modifiers.join(', ')}
                                </div>
                            ` : ''}
                            ${notesHtml}
                            ${!item.is_voided ? `
                                <button class="btn btn-sm btn-premium-outline btn-premium-sm mt-2" onclick="openNotesModal(${item.id})">
                                    <i class="bi bi-chat-left-text me-1"></i>${item.notes && item.notes.length > 0 ? 'Edit Notes' : 'Add Notes'}
                                </button>
                                <button class="btn btn-sm btn-premium-outline btn-premium-sm mt-2 ms-1" style="border-color: #dc3545; color: #dc3545;" onclick="openVoidModal(${item.orderItemId || item.id}, ${currentOrders[0]?.id || 0})">
                                    <i class="bi bi-x-circle"></i> Void
                                </button>
                            ` : `<div style="color: rgba(255,255,255,0.5); font-size: 0.8rem; margin-top: 10px;"><i class="bi bi-info-circle"></i> ${item.voidReason || 'No reason provided'}</div>`}
                        </div>
                        <div class="cart-item-qty">
                            ${!item.is_voided ? `
                                <button class="qty-btn" onclick="removeFromCart(${item.id})"><i class="bi bi-dash"></i></button>
                                <span class="fw-bold">${item.quantity}</span>
                                <button class="qty-btn" onclick="increaseQuantity(${item.id})"><i class="bi bi-plus"></i></button>
                            ` : '<span style="color: rgba(255,255,255,0.4);">Voided</span>'}
                        </div>
                    </div>
                `;
            });
            cartContainer.innerHTML = html;
            document.getElementById('cartTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Clear cart
        function clearCart() {
            if (confirm('Clear all items from cart?')) {
                cart = [];
                renderCart();
            }
        }

        // Hold order - save to localStorage
        function holdOrder() {
            if (cart.length === 0) { 
                alert('Tidak ada item di cart'); 
                return; 
            }
            
            // Save current cart with table info to localStorage
            const heldOrders = JSON.parse(localStorage.getItem('heldOrders') || '[]');
            const holdData = {
                tableId: tableId,
                tableName: tableName,
                items: cart,
                total: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
                heldAt: new Date().toISOString()
            };
            
            // Replace if same table, otherwise add new
            const existingIndex = heldOrders.findIndex(h => h.tableId === tableId);
            if (existingIndex >= 0) {
                heldOrders[existingIndex] = holdData;
            } else {
                heldOrders.push(holdData);
            }
            
            localStorage.setItem('heldOrders', JSON.stringify(heldOrders));
            
            alert('Order ditahan! Klik "Resume" untuk melanjutkan pesanan ini.');
            cart = [];
            renderCart();
        }
        
        // Resume held order
        function resumeHeldOrder(index) {
            const heldOrders = JSON.parse(localStorage.getItem('heldOrders') || '[]');
            if (heldOrders[index]) {
                const held = heldOrders[index];
                cart = held.items;
                renderCart();
                
                // Remove from held orders
                heldOrders.splice(index, 1);
                localStorage.setItem('heldOrders', JSON.stringify(heldOrders));
                
                alert(`Order untuk meja ${held.tableName || held.tableId} dikembalikan!`);
            }
        }
        
        // Show held orders modal
        function showHeldOrders() {
            const heldOrders = JSON.parse(localStorage.getItem('heldOrders') || '[]');
            
            if (heldOrders.length === 0) {
                alert('Tidak ada order yang ditahan');
                return;
            }
            
            let html = '<div class="held-orders-list">';
            heldOrders.forEach((held, index) => {
                const time = new Date(held.heldAt).toLocaleString('id-ID');
                html += `
                    <div class="held-order-item" onclick="resumeHeldOrder(${index})" style="cursor:pointer; padding:15px; border:1px solid rgba(212,175,55,0.3); border-radius:8px; margin-bottom:10px; background:rgba(255,255,255,0.05);">
                        <div style="display:flex; justify-content:space-between;">
                            <strong>Meja ${held.tableName || held.tableId}</strong>
                            <span style="color:var(--gold-primary);">Rp ${held.total.toLocaleString('id-ID')}</span>
                        </div>
                        <div style="font-size:0.85rem; color:rgba(255,255,255,0.5); margin-top:5px;">
                            ${held.items.length} item(s) | Ditahan: ${time}
                        </div>
                        <div style="font-size:0.75rem; color:rgba(255,255,255,0.3); margin-top:5px;">
                            ${held.items.map(i => `${i.quantity}x ${i.name}`).join(', ')}
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            document.getElementById('heldOrdersContent').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('heldOrdersModal'));
            modal.show();
        }
        
        // Clear held orders
        function clearHeldOrders() {
            if (confirm('Hapus semua order yang ditahan?')) {
                localStorage.removeItem('heldOrders');
                document.getElementById('heldOrdersContent').innerHTML = '<p style="text-align:center; color:rgba(255,255,255,0.5);">Tidak ada order yang ditahan</p>';
            }
        }

        // Close table order
        function closeTableOrder() {
            if (cart.length > 0) {
                if (!confirm('You have items in cart. Are you sure you want to close?')) return;
            }
            window.location.href = '/php-native/pages/pos-tables.php';
        }

        // Complete order
        async function completeOrder() {
            if (currentOrders.length === 0) { alert('No active orders to complete'); return; }
            if (!confirm('Mark all orders for this table as completed and free the table?')) return;
            try {
                const response = await fetch('/php-native/api/pos/complete-order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: currentOrders[0].id, table_id: tableId })
                });
                const data = await response.json();
                if (data.success) {
                    alert('Order completed! Table is now available.');
                    cart = []; currentOrders = []; renderCart();
                    setTimeout(() => { window.location.href = '/php-native/pages/pos-tables.php'; }, 1000);
                } else {
                    alert('Error: ' + (data.message || 'Failed to complete order'));
                }
            } catch (error) {
                console.error('Error completing order:', error);
                alert('Error completing order. Please try again.');
            }
        }

        // Submit order
        async function submitOrder() {
            if (cart.length === 0) { alert('Please add items to the cart first'); return; }
            const newItems = cart.filter(item => !item.order_id);
            if (newItems.length === 0 && currentOrders.length > 0) {
                alert('All items are from existing orders. Use Complete to finish the order.');
                return;
            }
            if (newItems.length === 0) { alert('Please add new items to the order'); return; }
            const total = newItems.reduce((sum, item) => sum + ((item.basePrice || item.price) * item.quantity), 0);
            if (!confirm(`Submit order for ${tableName}?\n\nTotal: Rp ${total.toLocaleString('id-ID')}`)) return;
            try {
                const response = await fetch('/php-native/api/pos/store-order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        table_id: tableId,
                        items: newItems.map(item => ({
                            menu_id: item.id,
                            quantity: item.quantity,
                            price: item.basePrice || item.price,
                            notes: item.notes || [],
                            modifiers: item.modifiers || []
                        })),
                        customer_name: '',
                        customer_phone: ''
                    })
                });
                const data = await response.json();
                if (data.success) {
                    alert('Order submitted successfully!');
                    cart = [];
                    renderCart();
                    loadCurrentOrders();
                } else {
                    alert('Error: ' + (data.message || 'Failed to submit order'));
                }
            } catch (error) {
                console.error('Error submitting order:', error);
                alert('Error submitting order. Please try again.');
            }
        }

        // Load modifier groups for category
        async function loadModifierGroupsForCategory(categoryId) {
            try {
                const response = await fetch(`/php-native/api/modifiers/groups.php?category_id=${categoryId}`);
                const data = await response.json();
                if (data.success) {
                    modifierGroups = data.groups || [];
                } else {
                    modifierGroups = [];
                }
            } catch (error) {
                console.error('Error loading modifiers:', error);
                modifierGroups = [];
            }
        }

        // Show modifier modal
        function showModifierModal(item) {
            document.getElementById('modifierItemId').value = item.id;
            document.getElementById('modifierItemName').value = item.name;
            document.getElementById('modifierItemPrice').value = item.price;
            document.getElementById('modifierItemDisplay').textContent = item.name;
            selectedModifiers = {};
            renderModifierGroups();
            const modal = new bootstrap.Modal(document.getElementById('modifierModal'));
            modal.show();
        }

        // Render modifier groups
        function renderModifierGroups() {
            const container = document.getElementById('modifierGroupsContainer');
            const basePrice = parseFloat(document.getElementById('modifierItemPrice').value) || 0;
            document.getElementById('modifierBasePrice').textContent = basePrice.toLocaleString('id-ID');
            updateModifierTotal();
            
            if (modifierGroups.length === 0) {
                container.innerHTML = '<p style="color: rgba(255,255,255,0.5); padding: 40px 20px; text-align: center;">No modifier options available</p>';
                return;
            }
            
            let html = '';
            modifierGroups.forEach((group, groupIndex) => {
                const isRequired = group.selection_type === 'required' || group.is_required == 1;
                const minSelection = group.min_selection || 1;
                const maxSelection = group.max_selection || 1;
                const isMultiple = maxSelection > 1;
                
                html += `
                    <div class="modifier-group-card" style="
                        flex: 0 0 280px;
                        border: 1px solid rgba(212,175,55,0.2);
                        border-radius: 12px;
                        padding: 15px;
                        background: rgba(255,255,255,0.03);
                        display: flex;
                        flex-direction: column;
                    ">
                        <h6 class="mb-2" style="color: var(--gold-light); font-size: 1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${group.name}
                            ${isRequired ? '<span style="color: #ff6b6b; font-size: 0.75rem;"> (Required)</span>' : ''}
                        </h6>
                        <p style="color: rgba(255,255,255,0.5); font-size: 0.8rem; margin-bottom: 10px; white-space: nowrap;">
                            Select ${minSelection}${isMultiple ? '-' + maxSelection : ''}
                        </p>
                        <div style="overflow-y: auto; flex: 1; max-height: 280px; padding-right: 5px;">
                            ${group.modifiers && group.modifiers.length > 0 ? group.modifiers.map((mod, modIndex) => {
                                const inputType = isMultiple ? 'checkbox' : 'radio';
                                const groupName = `modifier-group-${groupIndex}`;
                                return `
                                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.1); border-radius: 8px; margin-bottom: 8px; cursor: pointer; transition: all 0.3s;"
                                        onmouseover="this.style.background='rgba(212,175,55,0.1)'"
                                        onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                                        <input type="${inputType}" 
                                            name="${groupName}" 
                                            value="${mod.id}" 
                                            data-price="${mod.price}"
                                            data-name="${mod.name}"
                                            onchange="onModifierSelect(${groupIndex}, ${mod.id}, '${mod.name}', ${mod.price})"
                                            style="accent-color: var(--gold-primary); width: 16px; height: 16px; flex-shrink: 0;">
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="color: #fff; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${mod.name}</div>
                                            <div style="color: var(--gold-primary); font-weight: 600; font-size: 0.8rem;">
                                                ${mod.price > 0 ? '+Rp ' + parseFloat(mod.price).toLocaleString('id-ID') : mod.price < 0 ? '-Rp ' + Math.abs(parseFloat(mod.price)).toLocaleString('id-ID') : 'Included'}
                                            </div>
                                        </div>
                                    </label>
                                `;
                            }).join('') : '<p style="color: rgba(255,255,255,0.5); font-size: 0.85rem; text-align: center; padding: 15px 0;">No options</p>'}
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
            updateModifierPrice();
        }

        // Handle modifier selection
        function onModifierSelect(groupIndex, modifierId, modifierName, modifierPrice) {
            const group = modifierGroups[groupIndex];
            const isMultiple = group.max_selection > 1;
            if (!selectedModifiers[groupIndex]) {
                selectedModifiers[groupIndex] = [];
            }
            if (isMultiple) {
                const existingIndex = selectedModifiers[groupIndex].findIndex(m => m.id === modifierId);
                if (existingIndex >= 0) {
                    selectedModifiers[groupIndex].splice(existingIndex, 1);
                } else {
                    selectedModifiers[groupIndex].push({
                        id: modifierId,
                        name: modifierName,
                        price: parseFloat(modifierPrice)
                    });
                }
            } else {
                selectedModifiers[groupIndex] = [{
                    id: modifierId,
                    name: modifierName,
                    price: parseFloat(modifierPrice)
                }];
            }
            updateModifierPrice();
        }

        // Update modifier price
        function updateModifierPrice() {
            let totalAdjustment = 0;
            Object.values(selectedModifiers).forEach(groupModifiers => {
                if (groupModifiers) {
                    groupModifiers.forEach(mod => {
                        totalAdjustment += mod.price;
                    });
                }
            });
            document.getElementById('modifierPriceAdjustment').textContent = 
                (totalAdjustment >= 0 ? '+' : '') + 'Rp ' + totalAdjustment.toLocaleString('id-ID');
            updateModifierTotal();
        }

        // Update modifier total
        function updateModifierTotal() {
            const basePrice = parseFloat(document.getElementById('modifierItemPrice').value) || 0;
            let totalAdjustment = 0;
            Object.values(selectedModifiers).forEach(groupModifiers => {
                if (groupModifiers) {
                    groupModifiers.forEach(mod => {
                        totalAdjustment += mod.price;
                    });
                }
            });
            const total = basePrice + totalAdjustment;
            document.getElementById('modifierTotalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Confirm add to cart with modifiers
        function confirmAddToCart() {
            // Validate required modifiers
            let isValid = true;
            modifierGroups.forEach((group, index) => {
                const isRequired = group.selection_type === 'required' || group.is_required == 1;
                const minSelection = group.min_selection || 1;
                if (isRequired) {
                    const selected = selectedModifiers[index] || [];
                    if (selected.length < minSelection) {
                        isValid = false;
                        alert(`Please select at least ${minSelection} option(s) for ${group.name}`);
                    }
                }
            });
            if (!isValid) return;

            // Collect all modifiers
            const allModifiers = [];
            let totalAdjustment = 0;
            Object.values(selectedModifiers).forEach(groupModifiers => {
                if (groupModifiers) {
                    groupModifiers.forEach(mod => {
                        allModifiers.push(mod.name);
                        totalAdjustment += mod.price;
                    });
                }
            });

            // Add item to cart
            pendingCartItem.priceAdjustment = totalAdjustment;
            addItemToCart(pendingCartItem, allModifiers, totalAdjustment);
            pendingCartItem = null;
            bootstrap.Modal.getInstance(document.getElementById('modifierModal')).hide();
        }

        // Open notes modal
        function openNotesModal(itemId) {
            const item = cart.find(i => i.id === itemId);
            if (!item) return;
            document.getElementById('currentItemId').value = itemId;
            tempSelectedNotes = item.notes || [];
            loadNotes();
            const modal = new bootstrap.Modal(document.getElementById('notesModal'));
            modal.show();
        }

        // Load notes
        async function loadNotes() {
            try {
                const response = await fetch('/php-native/api/notes/index.php');
                const text = await response.text();
                
                // Check if response is valid JSON
                if (!text.trim().startsWith('{')) {
                    console.error('Invalid response from notes API:', text.substring(0, 200));
                    itemNotes = [];
                    renderNotesModal([]);
                    return;
                }
                
                const data = JSON.parse(text);
                if (data.success) {
                    itemNotes = data.notes || [];
                    renderNotesModal(itemNotes);
                } else {
                    console.warn('Notes API returned error:', data.message);
                    itemNotes = [];
                    renderNotesModal([]);
                }
            } catch (error) {
                console.error('Error loading notes:', error);
                itemNotes = [];
                renderNotesModal([]);
            }
        }

        // Render notes in modal
        function renderNotesModal(notes) {
            filterNotesModal('all');
        }

        // Filter notes modal
        function filterNotesModal(category) {
            document.querySelectorAll('#notesFilter .category-btn').forEach(btn => btn.classList.remove('active'));
            
            // Set active button
            if (event && event.target) {
                event.target.classList.add('active');
            } else {
                // If called programmatically, activate 'all' button
                const allBtn = document.querySelector('#notesFilter .category-btn[data-category="all"]');
                if (allBtn) allBtn.classList.add('active');
            }
            
            const container = document.getElementById('notesSelection');
            const filtered = category === 'all' ? itemNotes : itemNotes.filter(n => n.category === category);
            if (filtered.length === 0) {
                container.innerHTML = '<p style="color: rgba(255,255,255,0.5); text-align: center; padding: 20px;">No notes available</p>';
                return;
            }
            let html = '';
            filtered.forEach(note => {
                const isSelected = tempSelectedNotes.includes(note.note_text);
                html += `
                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px; background: ${isSelected ? 'rgba(212,175,55,0.2)' : 'rgba(255,255,255,0.03)'}; border: 1px solid ${isSelected ? 'var(--gold-primary)' : 'rgba(212,175,55,0.1)'}; border-radius: 8px; margin-bottom: 8px; cursor: pointer;">
                        <input type="checkbox" value="${note.note_text}" ${isSelected ? 'checked' : ''}
                            onchange="toggleNoteSelection(this)"
                            style="accent-color: var(--gold-primary); width: 18px; height: 18px;">
                        <span style="color: #fff;">${note.note_text}</span>
                    </label>
                `;
            });
            container.innerHTML = html;
            updateSelectedNotesDisplay();
        }

        // Toggle note selection
        function toggleNoteSelection(checkbox) {
            const noteText = checkbox.value;
            const index = tempSelectedNotes.indexOf(noteText);
            if (checkbox.checked && index === -1) {
                tempSelectedNotes.push(noteText);
            } else if (!checkbox.checked && index >= 0) {
                tempSelectedNotes.splice(index, 1);
            }
            updateSelectedNotesDisplay();
        }

        // Update selected notes display
        function updateSelectedNotesDisplay() {
            const display = document.getElementById('selectedNotesDisplay');
            if (tempSelectedNotes.length === 0) {
                display.innerHTML = '<p style="color: rgba(255,255,255,0.5);">No notes selected</p>';
            } else {
                display.innerHTML = tempSelectedNotes.map(note => 
                    `<span class="badge" style="background: rgba(212,175,55,0.3); color: var(--gold-primary); border: none; padding: 6px 12px; border-radius: 12px; margin: 4px; display: inline-block;">${note}</span>`
                ).join('');
            }
        }

        // Save item notes
        function saveItemNotes() {
            const itemId = parseInt(document.getElementById('currentItemId').value);
            const item = cart.find(i => i.id === itemId);
            if (item) {
                item.notes = tempSelectedNotes;
                renderCart();
            }
            bootstrap.Modal.getInstance(document.getElementById('notesModal')).hide();
        }

        // ==================== VOID FUNCTIONS ====================
        
        // Load void reasons
        async function loadVoidReasons() {
            try {
                const response = await fetch('/php-native/api/orders/void-reasons.php');
                const data = await response.json();
                if (data.success) {
                    voidReasons = data.reasons || [];
                } else {
                    voidReasons = [];
                }
            } catch (error) {
                console.error('Error loading void reasons:', error);
                voidReasons = [];
            }
        }

        // Open void modal
        function openVoidModal(itemId, orderId) {
            document.getElementById('voidItemId').value = itemId;
            document.getElementById('voidOrderId').value = orderId;
            document.getElementById('voidReasonSelect').value = '';
            document.getElementById('voidReasonOther').value = '';
            document.getElementById('voidReasonOtherDiv').style.display = 'none';
            
            // Populate reasons dropdown
            const select = document.getElementById('voidReasonSelect');
            select.innerHTML = '<option value="">-- Select Void Reason --</option>';
            voidReasons.forEach(reason => {
                select.innerHTML += `<option value="${reason.id}">${reason.name}</option>`;
            });
            
            const modal = new bootstrap.Modal(document.getElementById('voidModal'));
            modal.show();
        }

        // Toggle other reason field
        function toggleVoidReasonOther() {
            const select = document.getElementById('voidReasonSelect');
            const otherDiv = document.getElementById('voidReasonOtherDiv');
            otherDiv.style.display = select.value === 'other' ? 'block' : 'none';
        }

        // Confirm void item
        async function confirmVoidItem() {
            const itemId = document.getElementById('voidItemId').value;
            const orderId = document.getElementById('voidOrderId').value;
            const reasonSelect = document.getElementById('voidReasonSelect').value;
            const reasonOther = document.getElementById('voidReasonOther').value;

            if (!itemId) {
                alert('Invalid item ID');
                return;
            }

            let voidReason = '';
            if (reasonSelect === 'other') {
                if (!reasonOther.trim()) {
                    alert('Please enter a void reason');
                    return;
                }
                voidReason = reasonOther;
            } else if (reasonSelect) {
                const reason = voidReasons.find(r => r.id == reasonSelect);
                voidReason = reason ? reason.reason : 'No reason provided';
            } else {
                alert('Please select a void reason');
                return;
            }

            try {
                const response = await fetch('/php-native/api/orders/void-item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        order_item_id: parseInt(itemId),
                        void_reason_text: voidReason
                    })
                });

                const data = await response.json();
                if (data.success) {
                    // Update cart
                    const itemIndex = cart.findIndex(i => i.id == itemId);
                    if (itemIndex > -1) {
                        cart[itemIndex].is_voided = true;
                        cart[itemIndex].voidReason = voidReason;
                    }
                    
                    renderCart();
                    
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('voidModal')).hide();
                    
                    // Show success message
                    alert('✅ Item voided successfully');
                    
                    // Reload current orders to sync with database
                    if (orderId) {
                        await loadCurrentOrders();
                    }
                } else {
                    alert('❌ Error: ' + data.message);
                }
            } catch (error) {
                console.error('Void error:', error);
                alert('❌ Error voiding item. Please try again.');
            }
        }

        // ==================== PAYMENT FUNCTIONS ====================
        async function openPaymentModal() {
            if (cart.length === 0 && currentOrders.length === 0) {
                alert('No items to pay for');
                return;
            }
            await loadPaymentMethods();
            calculateBillTotal();
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        }

        // Load payment methods
        async function loadPaymentMethods() {
            try {
                const response = await fetch('/php-native/api/pos/payment-methods.php');
                const data = await response.json();
                if (data.success) {
                    paymentMethods = data.methods || [];
                    renderPaymentMethods();
                } else {
                    paymentMethods = [];
                }
            } catch (error) {
                console.error('Error loading payment methods:', error);
                paymentMethods = [];
            }
        }

        // Render payment methods
        function renderPaymentMethods() {
            const container = document.getElementById('paymentMethodsContainer');
            if (paymentMethods.length === 0) {
                container.innerHTML = '<div class="text-center py-4" style="color: rgba(255,255,255,0.5);">No payment methods available</div>';
                return;
            }
            let html = '';
            paymentMethods.forEach((method, index) => {
                html += `
                    <div class="col-md-6">
                        <label style="display: flex; align-items: center; gap: 12px; padding: 15px; background: rgba(255,255,255,0.03); border: 1px solid ${index === 0 ? 'var(--gold-primary)' : 'rgba(212,175,55,0.2)'}; border-radius: 12px; cursor: pointer; transition: all 0.3s;"
                            onclick="selectPaymentMethod(${index})">
                            <input type="radio" name="paymentMethod" value="${method.id}" ${index === 0 ? 'checked' : ''} style="accent-color: var(--gold-primary); width: 20px; height: 20px;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--gold-light);">${method.name}</div>
                                <div style="font-size: 0.85rem; color: rgba(255,255,255,0.5);">${method.description || ''}</div>
                            </div>
                        </label>
                    </div>
                `;
            });
            container.innerHTML = html;
            selectedPaymentMethod = paymentMethods[0];
        }

        // Select payment method
        function selectPaymentMethod(index) {
            selectedPaymentMethod = paymentMethods[index];
            document.querySelectorAll('#paymentMethodsContainer input[type="radio"]').forEach((radio, i) => {
                radio.checked = i === index;
                radio.closest('label').style.borderColor = i === index ? 'var(--gold-primary)' : 'rgba(212,175,55,0.2)';
            });
            if (selectedPaymentMethod && selectedPaymentMethod.name.toLowerCase().includes('cash')) {
                document.getElementById('cashPaymentSection').style.display = 'block';
            } else {
                document.getElementById('cashPaymentSection').style.display = 'none';
            }
        }

        // Calculate bill total
        function calculateBillTotal() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const serviceRate = taxSettings.serviceEnabled ? taxSettings.service : 0;
            const taxRate = taxSettings.taxEnabled ? taxSettings.tax : 0;
            const serviceCharge = subtotal * (serviceRate / 100);
            const tax = subtotal * (taxRate / 100);
            const total = subtotal + serviceCharge + tax;
            document.getElementById('billSubtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('billService').textContent = 'Rp ' + serviceCharge.toLocaleString('id-ID');
            document.getElementById('billTax').textContent = 'Rp ' + tax.toLocaleString('id-ID');
            document.getElementById('billTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
            currentTotal = total;
        }

        // Set amount
        function setAmount(amount) {
            document.getElementById('amountPaid').value = amount;
            calculateChange();
        }

        // Set exact amount
        function setExactAmount() {
            document.getElementById('amountPaid').value = currentTotal;
            calculateChange();
        }

        // Calculate change
        function calculateChange() {
            const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
            const change = paid - currentTotal;
            const changeDisplay = document.getElementById('changeDisplay');
            if (change >= 0) {
                changeDisplay.style.display = 'block';
                document.getElementById('changeAmount').textContent = 'Rp ' + change.toLocaleString('id-ID');
            } else {
                changeDisplay.style.display = 'none';
            }
        }

        // Process payment
        async function processPayment() {
            if (!selectedPaymentMethod) {
                alert('Please select a payment method');
                return;
            }
            if (selectedPaymentMethod.name.toLowerCase().includes('cash')) {
                const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
                if (paid < currentTotal) {
                    alert('Insufficient payment amount');
                    return;
                }
            }
            try {
                // Use complete-order.php API for payment processing
                const response = await fetch('/php-native/api/pos/complete-order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        order_id: currentOrders[0]?.id,
                        table_id: tableId,
                        payment_method_id: selectedPaymentMethod.id,
                        paid_amount: selectedPaymentMethod.name.toLowerCase().includes('cash') ?
                            parseFloat(document.getElementById('amountPaid').value) : currentTotal,
                        customer_name: '',
                        customer_phone: ''
                    })
                });
                const data = await response.json();
                if (data.success) {
                    console.log('Payment success');
                    console.log('Order to print:', currentOrders[0]);
                    console.log('Change:', data.change_amount);

                    // Print receipt BEFORE clearing cart
                    await printReceipt(currentOrders[0], data.change_amount || 0);

                    // Clear cart and orders AFTER printing
                    cart = [];
                    currentOrders = [];
                    renderCart();
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();

                    // Redirect after 2 seconds
                    setTimeout(() => {
                        window.location.href = '/php-native/pages/pos-tables.php';
                    }, 2000);
                } else {
                    alert('Error: ' + (data.message || 'Failed to process payment'));
                }
            } catch (error) {
                console.error('Error processing payment:', error);
                alert('Error processing payment. Please try again.');
            }
        }

        // Print receipt - Fixed version without nested template literals
        async function printReceipt(order, change) {
            let receiptSettings = {
                tax_percent: 10,
                service_percent: 5,
                outlet: { name: 'RESTAURANT', address: '', phone: '' },
                template: { footer_text: 'Thank you for your visit!' }
            };
            try {
                const response = await fetch('/php-native/api/settings/receipt-settings.php');
                const data = await response.json();
                if (data.success && data.template) {
                    receiptSettings = {
                        template: data.template,
                        tax_percent: data.tax_percent || 10,
                        service_percent: data.service_percent || 5,
                        outlet: data.outlet || {}
                    };
                }
            } catch (error) {
                console.error('Error loading receipt settings:', error);
            }

            let itemsHtml = '';
            let subtotal = 0;
            cart.forEach(function(item) {
                // Skip void items from subtotal calculation
                if (item.is_voided) {
                    // Still show void items on receipt for reference, but don't calculate
                    itemsHtml += '<tr><td colspan="2" style="text-align: left; padding: 5px 0; font-weight: bold; text-decoration: line-through; color: #999;">' + (item.name || 'Item') + ' <span style="color: red;">[VOIDED]</span></td></tr>';
                    itemsHtml += '<tr><td style="text-align: left; padding: 3px 0; padding-left: 15px; font-size: 11px; color: #999;">' + (item.quantity || 1) + 'x @ Rp ' + (item.price || 0).toLocaleString('id-ID') + '</td><td style="text-align: right; padding: 3px 0; color: #999;">Rp 0</td></tr>';
                    return; // Skip to next item, don't add to subtotal
                }
                
                const itemTotal = (item.price || 0) * (item.quantity || 1);
                subtotal += itemTotal;
                let allNotes = [];
                if (item.notes && Array.isArray(item.notes)) { allNotes = allNotes.concat(item.notes); }
                if (item.modifiers && Array.isArray(item.modifiers)) {
                    item.modifiers.forEach(function(m) {
                        if (typeof m === 'string') allNotes.push(m);
                        else if (m && m.name) allNotes.push(m.name);
                    });
                }
                const notesText = allNotes.length > 0 ? '[' + allNotes.join(', ') + ']' : '';
                itemsHtml += '<tr><td colspan="2" style="text-align: left; padding: 5px 0; font-weight: bold;">' + (item.name || 'Item') + '</td></tr>';
                itemsHtml += '<tr><td style="text-align: left; padding: 3px 0; padding-left: 15px; font-size: 11px;">' + (item.quantity || 1) + 'x @ Rp ' + (item.price || 0).toLocaleString('id-ID') + '</td><td style="text-align: right; padding: 3px 0;">Rp ' + itemTotal.toLocaleString('id-ID') + '</td></tr>';
                if (notesText) { itemsHtml += '<tr><td colspan="2" style="text-align: left; padding: 3px 0; padding-left: 15px; font-size: 9px; color: #666; font-style: italic;">' + notesText + '</td></tr>'; }
            });

            const tax = subtotal * (receiptSettings.tax_percent / 100);
            const service = subtotal * (receiptSettings.service_percent / 100);
            const total = subtotal + tax + service;
            const printWindow = window.open('', '_blank');
            let html = '<!DOCTYPE html><html><head><title>Receipt #' + (order.id || '') + '</title>';
            html += '<style>@media print { body { margin: 0; padding: 0; } .no-print { display: none; } } body { font-family: "Courier New", monospace; font-size: 11px; } .receipt { width: 58mm; padding: 10px; margin: 0 auto; } .text-center { text-align: center; } .text-right { text-align: right; } table { width: 100%; border-collapse: collapse; } td { padding: 2px 0; } .total-row { font-weight: bold; border-top: 1px dashed #000; padding-top: 5px; } .logo { max-width: 80px; max-height: 80px; margin: 0 auto 10px; display: block; }</style>';
            html += '</head><body><div class="receipt">';
            
            // Logo (if exists)
            if (receiptSettings.template.logo_path && receiptSettings.template.logo_path.trim() !== '') {
                html += '<img src="' + receiptSettings.template.logo_path + '" alt="Logo" class="logo" style="max-width: 80px; max-height: 80px; margin: 0 auto 10px; display: block;">';
            }

            // Header - Show ONLY ONCE - Use ONLY template data, NOT outlet data
            html += '<div class="text-center" style="margin-bottom: 10px;">';
            // ONLY use template.header_text, don't fallback to outlet.name
            const headerName = (receiptSettings.template.header_text && receiptSettings.template.header_text.trim() !== '') ? receiptSettings.template.header_text.trim() : 'RESTAURANT';
            html += '<h3 style="margin: 0; padding: 0; font-size: 13px; font-weight: bold;">' + headerName + '</h3>';

            // ONLY show template address and phone, NOT outlet address/phone
            const address = (receiptSettings.template.address && receiptSettings.template.address.trim() !== '') ? receiptSettings.template.address.trim() : '';
            const phone = (receiptSettings.template.phone && receiptSettings.template.phone.trim() !== '') ? receiptSettings.template.phone.trim() : '';

            if (address) {
                html += '<p style="margin: 3px 0; padding: 0; font-size: 10px;">' + address + '</p>';
            }
            if (phone) {
                html += '<p style="margin: 3px 0; padding: 0; font-size: 10px;">Telp: ' + phone + '</p>';
            }
            html += '</div><hr style="border-top: 1px dashed #000; margin: 8px 0;">';
            
            // Order info with Customer Information
            html += '<table style="margin-bottom: 8px;">';
            html += '<tr><td style="width: 50%; vertical-align: top;"><strong>ORDER DETAILS</strong></td><td style="width: 50%; vertical-align: top; border-left: 1px dashed #ccc; padding-left: 8px;"><strong>👤 CUSTOMER INFO</strong></td></tr>';
            html += '<tr><td>Order #: ' + (order.id || '-') + '</td><td style="border-left: 1px dashed #ccc; padding-left: 8px;">' + ((order.customer_name || order.customer_phone) ? '' : '<span style="color: #999; font-style: italic; font-size: 10px;">Walk-in Customer</span>') + '</td></tr>';
            html += '<tr><td>Table: ' + (order.table_name || '-') + '</td><td style="border-left: 1px dashed #ccc; padding-left: 8px;">' + (order.customer_name ? '<strong>Name:</strong> ' + order.customer_name : '') + '</td></tr>';
            html += '<tr><td>Date: ' + new Date().toLocaleString('id-ID') + '</td><td style="border-left: 1px dashed #ccc; padding-left: 8px;">' + (order.customer_phone ? '<strong>Phone:</strong> ' + order.customer_phone : '') + '</td></tr>';
            html += '<tr><td>Cashier: Staff</td><td style="border-left: 1px dashed #ccc; padding-left: 8px;"></td></tr>';
            html += '</table><hr style="border-top: 1px dashed #000;">';
            
            // Items
            html += '<table>' + itemsHtml + '</table><hr style="border-top: 1px dashed #000;">';
            
            // Totals
            html += '<table><tr><td>Subtotal:</td><td class="text-right">Rp ' + subtotal.toLocaleString('id-ID') + '</td></tr>';
            if (receiptSettings.service_percent > 0) { html += '<tr><td>Service (' + receiptSettings.service_percent + '%):</td><td class="text-right">Rp ' + service.toLocaleString('id-ID') + '</td></tr>'; }
            if (receiptSettings.tax_percent > 0) { html += '<tr><td>Tax (' + receiptSettings.tax_percent + '%):</td><td class="text-right">Rp ' + tax.toLocaleString('id-ID') + '</td></tr>'; }
            html += '<tr class="total-row grand-total"><td>TOTAL:</td><td class="text-right">Rp ' + total.toLocaleString('id-ID') + '</td></tr>';
            html += '<tr><td>Paid:</td><td class="text-right">Rp ' + (order.paid_amount || total).toLocaleString('id-ID') + '</td></tr>';
            html += '<tr><td>Change:</td><td class="text-right">Rp ' + (change || 0).toLocaleString('id-ID') + '</td></tr></table><hr style="border-top: 1px dashed #000;">';
            
            // Footer with website and social media
            html += '<p class="text-center" style="margin: 10px 0; font-size: 10px;">' + (receiptSettings.template.footer_text || 'Thank you for your visit!') + '</p>';
            if (receiptSettings.template.website) { html += '<p class="text-center" style="margin: 3px 0; font-size: 9px;">' + receiptSettings.template.website + '</p>'; }
            if (receiptSettings.template.social_media) { html += '<p class="text-center" style="margin: 3px 0; font-size: 9px;">' + receiptSettings.template.social_media + '</p>'; }
            
            html += '</div><script>window.onload = function() { window.print(); setTimeout(function() { window.close(); }, 500); };<\/script></body></html>';
            printWindow.document.write(html);
            printWindow.document.close();
        }

        // Load current orders
        async function loadCurrentOrders() {
            try {
                console.log('=== loadCurrentOrders START ===');
                console.log('Cart before load:', cart.length, 'items');
                console.log('Cart items:', cart.map(i => ({ id: i.id, name: i.name, orderItemId: i.orderItemId })));
                
                const response = await fetch(`/php-native/api/pos/table-orders.php?table_id=${tableId}&t=${Date.now()}`);
                const data = await response.json();

                console.log('API Response:', data);

                if (data.success && data.orders && data.orders.length > 0) {
                    // Filter out paid/completed orders explicitly
                    const activeOrders = data.orders.filter(order => {
                        const isActive = order.status !== 'paid' &&
                                        order.status !== 'completed' &&
                                        order.status !== 'cancelled' &&
                                        order.status !== 'voided';
                        if (!isActive) {
                            console.log('Excluding paid/completed order:', order.id, order.status);
                        }
                        return isActive;
                    });

                    currentOrders = activeOrders;

                    // Load ONLY database items (with orderItemId), preserve new cart items
                    let dbItems = [];
                    activeOrders.forEach(order => {
                        if (order.items) {
                            const items = order.items.map(item => ({
                                id: item.id,
                                orderItemId: item.id,
                                menuItemId: item.menu_item_id,
                                name: item.name || item.item_name,
                                price: parseFloat(item.price),
                                basePrice: parseFloat(item.price),
                                quantity: parseInt(item.quantity),
                                notes: item.notes_array || [],
                                modifiers: item.modifiers_array || [],
                                order_id: order.id,
                                is_voided: !!item.is_voided,
                                orderStatus: order.status
                            }));
                            dbItems = dbItems.concat(items);
                        }
                    });

                    console.log('DB items loaded:', dbItems.length);
                    
                    // Merge: Keep new cart items (without orderItemId), update/replace DB items
                    const newCartItems = cart.filter(item => !item.orderItemId);
                    console.log('New cart items to preserve:', newCartItems.length);
                    console.log('New items:', newCartItems.map(i => ({ id: i.id, name: i.name, orderItemId: i.orderItemId })));
                    
                    cart = [...dbItems, ...newCartItems];
                    renderCart();
                    console.log('=== loadCurrentOrders END ===');
                    console.log('Cart after load:', cart.length, 'items (' + dbItems.length + ' from DB, ' + newCartItems.length + ' new)');
                } else {
                    // Only clear cart items from DB, keep new items
                    const newCartItems = cart.filter(item => !item.orderItemId);
                    console.log('No active orders, keeping', newCartItems.length, 'new items');
                    cart = newCartItems;
                    renderCart();
                    console.log('=== loadCurrentOrders END (no orders) ===');
                }
            } catch (error) {
                console.error('Error loading orders:', error);
            }
        }

        // Print item
        function printItem(itemId, orderId) {
            const item = cart.find(i => i.id === itemId);
            if (!item) return;
            item.is_printed = true;
            item.print_count = (item.print_count || 0) + 1;
            renderCart();
            alert('Item submitted to kitchen!');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadMenu();
            loadCurrentOrders();
            loadVoidReasons();
            // Auto-refresh orders every 30 seconds to get new mobile orders (reduced frequency)
            setInterval(loadCurrentOrders, 30000);
        });
    </script>
</body>
</html>
