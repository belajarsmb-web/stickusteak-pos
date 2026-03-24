<?php
/**
 * Stickusteak POS - Customers Management
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
    <title>Customers - Stickusteak POS</title>
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }

        .btn-premium {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary), var(--gold-light));
            border: none;
            color: var(--black-primary);
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 25px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
            color: var(--black-primary);
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
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
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(212, 175, 55, 0.2);
            border-color: var(--gold-primary);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            position: relative;
            z-index: 1;
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            color: var(--black-primary);
        }

        .stat-icon.success {
            background: linear-gradient(135deg, #28a745, #34ce57);
            color: white;
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, #ffc107, #ffd93d);
            color: var(--black-primary);
        }

        .stat-content {
            position: relative;
            z-index: 1;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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

        /* Search Bar */
        .search-container {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .search-input {
            flex: 1;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .search-input:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
            color: #fff;
        }

        .search-input::placeholder {
            color: rgba(255,255,255,0.4);
        }

        .btn-search {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            border: none;
            color: var(--black-primary);
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.4);
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
            font-size: 0.85rem;
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

        .table-action-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            margin: 0 3px;
        }

        .table-action-btn.btn-outline-primary {
            border-color: var(--gold-primary);
            color: var(--gold-primary);
            background: transparent;
        }

        .table-action-btn.btn-outline-primary:hover {
            background: var(--gold-primary);
            color: var(--black-primary);
            transform: translateY(-2px);
        }

        .table-action-btn.btn-outline-secondary {
            border-color: rgba(212, 175, 55, 0.5);
            color: rgba(255,255,255,0.8);
            background: transparent;
        }

        .table-action-btn.btn-outline-secondary:hover {
            background: rgba(212, 175, 55, 0.3);
            color: #fff;
            transform: translateY(-2px);
        }

        /* Modal */
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

        .form-label {
            color: rgba(255,255,255,0.8);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #fff;
            padding: 12px 15px;
            border-radius: 10px;
        }

        .form-control:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
            color: #fff;
        }

        .btn-close-white {
            filter: invert(1);
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

        .stat-card, .content-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.2s; }

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
                    <a href="/php-native/pages/tickets.php" class="nav-link">
                        <i class="bi bi-receipt"></i>Tickets
                    </a>
                    <a href="/php-native/pages/menu.php" class="nav-link">
                        <i class="bi bi-menu-button-wide"></i>Menu
                    </a>
                    <a href="/php-native/pages/customers.php" class="nav-link active">
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

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Page Header -->
                <div class="page-header">
                    <h2 class="page-title"><i class="bi bi-people me-2"></i>Customers Management</h2>
                    <button class="btn-premium" data-bs-toggle="modal" data-bs-target="#newCustomerModal">
                        <i class="bi bi-person-plus me-2"></i>Add Customer
                    </button>
                </div>

                <!-- Stats Cards -->
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value" id="totalCustomers">0</div>
                            <div class="stat-label">Total Customers</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value" id="activeCustomers">0</div>
                            <div class="stat-label">Active This Month</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value" id="totalRevenue">Rp 0</div>
                            <div class="stat-label">Total Revenue</div>
                        </div>
                    </div>
                </div>

                <!-- Customers Table -->
                <div class="content-card">
                    <div class="search-container">
                        <input type="text" class="search-input" id="searchCustomer" placeholder="Search by name or phone...">
                        <button class="btn-search" onclick="loadCustomers()">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="customersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Total Orders</th>
                                    <th>Total Spent</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="loading-state">
                                            <div class="spinner-border" role="status"></div>
                                            <p style="margin-top: 15px;">Loading customers...</p>
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

    <!-- New Customer Modal -->
    <div class="modal fade" id="newCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add New Customer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="newCustomerForm">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person me-2"></i>Full Name</label>
                            <input type="text" class="form-control" id="customerName" required placeholder="Enter customer name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-telephone me-2"></i>Phone Number</label>
                            <input type="tel" class="form-control" id="customerPhone" placeholder="08xx-xxxx-xxxx">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-envelope me-2"></i>Email Address</label>
                            <input type="email" class="form-control" id="customerEmail" placeholder="customer@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-geo-alt me-2"></i>Address</label>
                            <textarea class="form-control" id="customerAddress" rows="3" placeholder="Enter customer address"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(212,175,55,0.3); color: #fff;">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-premium" onclick="submitCustomer()">
                        <i class="bi bi-check-lg me-1"></i>Save Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function loadCustomers() {
            try {
                const search = document.getElementById('searchCustomer').value;
                let url = '/php-native/api/customers/index.php?';
                if (search) url += `search=${encodeURIComponent(search)}`;

                const response = await fetch(url);
                const data = await response.json();
                const tbody = document.querySelector('#customersTable tbody');

                if (data.success && data.customers && data.customers.length > 0) {
                    document.getElementById('totalCustomers').textContent = data.customers.length;
                    
                    // Calculate active customers and revenue
                    const activeCount = data.customers.filter(c => c.total_orders > 0).length;
                    const totalRev = data.customers.reduce((sum, c) => sum + parseFloat(c.total_spent || 0), 0);
                    
                    document.getElementById('activeCustomers').textContent = activeCount;
                    document.getElementById('totalRevenue').textContent = 'Rp ' + totalRev.toLocaleString('id-ID');
                    
                    let html = '';
                    data.customers.forEach(customer => {
                        html += `
                            <tr>
                                <td style="color: var(--gold-primary); font-weight: 600;">#${customer.id}</td>
                                <td style="font-weight: 500; color: var(--gold-light);">${customer.name}</td>
                                <td>${customer.phone || '<span style="color: rgba(255,255,255,0.3);">-</span>'}</td>
                                <td>${customer.email || '<span style="color: rgba(255,255,255,0.3);">-</span>'}</td>
                                <td><span style="background: rgba(212,175,55,0.2); color: var(--gold-primary); padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">${customer.total_orders || 0} orders</span></td>
                                <td style="color: var(--gold-primary); font-weight: 600;">Rp ${(parseFloat(customer.total_spent || 0)).toLocaleString('id-ID')}</td>
                                <td style="color: rgba(255,255,255,0.6);">${formatDate(customer.created_at)}</td>
                                <td>
                                    <button class="btn table-action-btn btn-outline-primary" onclick="viewCustomer(${customer.id})" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn table-action-btn btn-outline-secondary" onclick="editCustomer(${customer.id})" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 60px 20px; color: rgba(255,255,255,0.4);">
                                <i class="bi bi-inbox" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                                No customers found
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error loading customers:', error);
                document.querySelector('#customersTable tbody').innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 60px 20px; color: rgba(255,255,255,0.4);">
                            <i class="bi bi-exclamation-triangle" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                            Error loading customers
                        </td>
                    </tr>
                `;
            }
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
        }

        async function submitCustomer() {
            const name = document.getElementById('customerName').value.trim();
            const phone = document.getElementById('customerPhone').value.trim();
            const email = document.getElementById('customerEmail').value.trim();
            const address = document.getElementById('customerAddress').value.trim();

            if (!name) {
                alert('Name is required');
                return;
            }

            try {
                const response = await fetch('/php-native/api/customers/store.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, phone, email, address })
                });
                const data = await response.json();
                if (data.success) {
                    alert('Customer added successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('newCustomerModal')).hide();
                    document.getElementById('newCustomerForm').reset();
                    loadCustomers();
                } else {
                    alert(data.message || 'Failed to add customer');
                }
            } catch (error) {
                console.error('Error adding customer:', error);
                alert('Error adding customer. Please try again.');
            }
        }

        function viewCustomer(id) {
            window.location.href = `/php-native/pages/customer-detail.php?id=${id}`;
        }

        function editCustomer(id) {
            alert('Edit functionality - Customer ID: ' + id);
        }

        // Search on Enter key
        document.getElementById('searchCustomer').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                loadCustomers();
            }
        });

        document.addEventListener('DOMContentLoaded', loadCustomers);
    </script>
</body>
</html>
