<?php
/**
 * Stickusteak POS - Inventory Management
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
    <title>Inventory Management - Stickusteak POS</title>
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
        }

        .sidebar-brand {
            padding: 25px 20px;
            font-size: 1.6rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
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
            padding-left: 25px;
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0.1));
            color: var(--gold-primary);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
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
            border-color: var(--gold-primary);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }

        .stat-card-value {
            font-size: 2.5rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Filters */
        .filters-bar {
            background: rgba(42,42,42,0.5);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 8px;
            padding: 10px 15px;
            color: #fff;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--gold-primary);
            background: rgba(255,255,255,0.08);
        }

        .filter-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 8px;
            padding: 10px 15px;
            color: #fff;
            min-width: 150px;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--gold-primary);
        }

        .btn-premium {
            background: var(--gold-gradient);
            border: none;
            color: var(--black-primary);
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-premium:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.4);
        }

        /* Inventory Table */
        .inventory-table-container {
            background: rgba(42,42,42,0.5);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 12px;
            overflow: hidden;
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
        }

        .inventory-table thead {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0.05));
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
        }

        .inventory-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--gold-light);
            font-size: 0.9rem;
        }

        .inventory-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }

        .inventory-table tbody tr {
            transition: all 0.3s;
        }

        .inventory-table tbody tr:hover {
            background: rgba(212, 175, 55, 0.05);
        }

        /* Stock Status Badges */
        .badge-stock {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-in-stock {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }

        .badge-low-stock {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }

        .badge-out-of-stock {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        /* Modal */
        .modal-content {
            background: linear-gradient(135deg, var(--black-tertiary), var(--black-primary));
            border: 2px solid var(--gold-dark);
            border-radius: 15px;
        }

        .modal-header {
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
            background: rgba(42,42,42,0.5);
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid rgba(212, 175, 55, 0.3);
        }

        .form-label {
            color: var(--gold-light);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            color: #fff;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.08);
            border-color: var(--gold-primary);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
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

        .form-select option:checked,
        .form-select option:checked:hover,
        .form-select option:focus,
        .form-select option:active {
            background: var(--gold-primary);
            color: #000;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.5);
        }

        /* Action Buttons */
        .btn-action {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 6px;
            margin: 0 3px;
        }

        .btn-action-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
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
                <i class="bi bi-grid-3x3-gap"></i>POS Tables
            </a>
            <a href="/php-native/pages/tickets.php" class="nav-link">
                <i class="bi bi-receipt"></i>Tickets
            </a>
            <a href="/php-native/pages/menu.php" class="nav-link">
                <i class="bi bi-menu-button-wide"></i>Menu
            </a>
            <a href="/php-native/pages/recipes.php" class="nav-link">
                <i class="bi bi-journal-text"></i>Recipes
            </a>
            <a href="/php-native/pages/modifiers.php" class="nav-link">
                <i class="bi bi-ui-checks-grid"></i>Modifiers
            </a>
            <a href="/php-native/pages/inventory.php" class="nav-link active">
                <i class="bi bi-box-seam"></i>Inventory
            </a>
            <a href="/php-native/pages/customers.php" class="nav-link">
                <i class="bi bi-people"></i>Customers
            </a>
            <a href="/php-native/pages/reports.php" class="nav-link">
                <i class="bi bi-graph-up"></i>Reports
            </a>
            <a href="/php-native/pages/users.php" class="nav-link">
                <i class="bi bi-people-fill"></i>Users
            </a>
            <a href="/php-native/pages/settings.php" class="nav-link">
                <i class="bi bi-gear"></i>Settings
            </a>
            <a href="/php-native/index.php" class="nav-link mt-5">
                <i class="bi bi-box-arrow-left"></i>Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">📦 Inventory Management</h1>
            <p style="color: rgba(255,255,255,0.6);">Manage your stock, ingredients, and supplies</p>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-card-value" id="totalItems">0</div>
                <div class="stat-card-label">Total Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value" id="lowStockItems" style="color: #ffc107;">0</div>
                <div class="stat-card-label">Low Stock</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value" id="outOfStockItems" style="color: #dc3545;">0</div>
                <div class="stat-card-label">Out of Stock</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value" id="totalValue">Rp 0</div>
                <div class="stat-card-label">Total Value</div>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="filters-bar">
            <input type="text" class="search-input" id="searchInput" placeholder="🔍 Search items..." onkeyup="filterInventory()">
            <select class="filter-select" id="categoryFilter" onchange="filterInventory()">
                <option value="">All Categories</option>
                <option value="MEAT">Meat & Protein</option>
                <option value="FISH">Fish & Seafood</option>
                <option value="POULTRY">Poultry</option>
                <option value="VEG">Vegetables</option>
                <option value="SPICE">Spices & Herbs</option>
                <option value="DAIRY">Dairy</option>
                <option value="GRAIN">Grains & Pasta</option>
                <option value="OIL">Oils & Sauces</option>
                <option value="OTHER">Other</option>
            </select>
            <select class="filter-select" id="statusFilter" onchange="filterInventory()">
                <option value="">All Status</option>
                <option value="in_stock">In Stock</option>
                <option value="low_stock">Low Stock</option>
                <option value="out_of_stock">Out of Stock</option>
            </select>
            <button class="btn-premium" onclick="openAddModal()">
                <i class="bi bi-plus-circle me-2"></i>Add Item
            </button>
        </div>

        <!-- Inventory Table -->
        <div class="inventory-table-container">
            <table class="inventory-table" id="inventoryTable">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Current Stock</th>
                        <th>Min Stock</th>
                        <th>Status</th>
                        <th>Cost Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody">
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: rgba(255,255,255,0.5);">
                            <i class="bi bi-hourglass-split" style="font-size: 2rem;"></i>
                            <p>Loading inventory...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="inventoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">📦 Add New Inventory Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="inventoryForm">
                        <input type="hidden" id="itemId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Item Name *</label>
                                <input type="text" class="form-control" id="itemName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SKU *</label>
                                <input type="text" class="form-control" id="itemSku" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit *</label>
                                <select class="form-select" id="itemUnit" required>
                                    <option value="">Select Unit</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="gr">Gram (gr)</option>
                                    <option value="l">Liter (l)</option>
                                    <option value="ml">Milliliter (ml)</option>
                                    <option value="pcs">Pieces (pcs)</option>
                                    <option value="box">Box</option>
                                    <option value="pack">Pack</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cost Price (per unit) *</label>
                                <input type="number" class="form-control" id="costPrice" step="0.01" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Current Stock *</label>
                                <input type="number" class="form-control" id="currentStock" step="0.01" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Min Stock Level *</label>
                                <input type="number" class="form-control" id="minStock" step="0.01" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Max Stock Level</label>
                                <input type="number" class="form-control" id="maxStock" step="0.01">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reorder Point</label>
                                <input type="number" class="form-control" id="reorderPoint" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <input type="checkbox" class="form-check-input" id="isActive" checked>
                                    Active
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-premium" onclick="saveInventory()">
                        <i class="bi bi-check-circle me-2"></i>Save Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Movement Modal -->
    <div class="modal fade" id="movementModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📊 Stock Movement History</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="movementHistory">
                    <p style="text-align: center; color: rgba(255,255,255,0.5);">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let inventoryData = [];
        let bootstrapModal;

        // Load inventory on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadInventory();
            bootstrapModal = new bootstrap.Modal(document.getElementById('inventoryModal'));
        });

        // Load inventory from API
        async function loadInventory() {
            try {
                const response = await fetch('/php-native/api/inventory/index.php');
                const data = await response.json();

                if (data.success) {
                    inventoryData = data.data || [];
                    renderInventoryTable();
                    updateStats();
                } else {
                    showError('Failed to load inventory: ' + (data.message || ''));
                }
            } catch (error) {
                console.error('Error loading inventory:', error);
                showError('Error loading inventory');
            }
        }

        // Render inventory table
        function renderInventoryTable() {
            const tbody = document.getElementById('inventoryTableBody');
            
            if (inventoryData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: rgba(255,255,255,0.5);">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p>No inventory items found</p>
                        </td>
                    </tr>
                `;
                return;
            }

            let html = '';
            inventoryData.forEach(item => {
                const status = getStockStatus(item.current_stock, item.min_stock);
                const statusBadge = getStatusBadge(status);

                html += `
                    <tr>
                        <td><strong style="color: var(--gold-light);">${item.name}</strong></td>
                        <td><code style="color: rgba(255,255,255,0.6);">${item.sku || '-'}</code></td>
                        <td>${item.unit || '-'}</td>
                        <td>${parseFloat(item.current_stock).toFixed(2)}</td>
                        <td>${parseFloat(item.min_stock).toFixed(2)}</td>
                        <td>${statusBadge}</td>
                        <td>Rp ${parseFloat(item.cost_price || 0).toLocaleString('id-ID')}</td>
                        <td>
                            <button class="btn btn-action btn-action-sm" style="background: rgba(13,110,253,0.2); color: #0d6efd; border: 1px solid #0d6efd;" onclick="editItem(${item.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-action btn-action-sm" style="background: rgba(23,162,184,0.2); color: #17a2b8; border: 1px solid #17a2b8;" onclick="viewMovements(${item.id})">
                                <i class="bi bi-clock-history"></i>
                            </button>
                            <button class="btn btn-action btn-action-sm" style="background: rgba(220,53,69,0.2); color: #dc3545; border: 1px solid #dc3545;" onclick="deleteItem(${item.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }

        // Get stock status
        function getStockStatus(current, min) {
            const curr = parseFloat(current);
            const minVal = parseFloat(min);
            
            if (curr <= 0) return 'out_of_stock';
            if (curr <= minVal) return 'low_stock';
            return 'in_stock';
        }

        // Get status badge HTML
        function getStatusBadge(status) {
            const badges = {
                'in_stock': '<span class="badge-stock badge-in-stock">✅ In Stock</span>',
                'low_stock': '<span class="badge-stock badge-low-stock">⚠️ Low Stock</span>',
                'out_of_stock': '<span class="badge-stock badge-out-of-stock">❌ Out of Stock</span>'
            };
            return badges[status] || badges['in_stock'];
        }

        // Update stats
        function updateStats() {
            const total = inventoryData.length;
            const lowStock = inventoryData.filter(item => 
                parseFloat(item.current_stock) <= parseFloat(item.min_stock) && parseFloat(item.current_stock) > 0
            ).length;
            const outOfStock = inventoryData.filter(item => parseFloat(item.current_stock) <= 0).length;
            const totalValue = inventoryData.reduce((sum, item) => 
                sum + (parseFloat(item.cost_price) * parseFloat(item.current_stock)), 0
            );

            document.getElementById('totalItems').textContent = total;
            document.getElementById('lowStockItems').textContent = lowStock;
            document.getElementById('outOfStockItems').textContent = outOfStock;
            document.getElementById('totalValue').textContent = 'Rp ' + totalValue.toLocaleString('id-ID', {maximumFractionDigits: 0});
        }

        // Filter inventory
        function filterInventory() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const category = document.getElementById('categoryFilter').value;
            const status = document.getElementById('statusFilter').value;

            let filtered = inventoryData;

            if (search) {
                filtered = filtered.filter(item => 
                    item.name.toLowerCase().includes(search) ||
                    (item.sku && item.sku.toLowerCase().includes(search))
                );
            }

            if (category) {
                filtered = filtered.filter(item => item.category === category);
            }

            if (status) {
                filtered = filtered.filter(item => {
                    const itemStatus = getStockStatus(item.current_stock, item.min_stock);
                    return itemStatus === status;
                });
            }

            inventoryData = filtered;
            renderInventoryTable();
        }

        // Open add modal
        function openAddModal() {
            document.getElementById('modalTitle').textContent = '📦 Add New Inventory Item';
            document.getElementById('inventoryForm').reset();
            document.getElementById('itemId').value = '';
            bootstrapModal.show();
        }

        // Edit item
        function editItem(id) {
            const item = inventoryData.find(i => i.id === id);
            if (!item) return;

            document.getElementById('modalTitle').textContent = '✏️ Edit Inventory Item';
            document.getElementById('itemId').value = item.id;
            document.getElementById('itemName').value = item.name;
            document.getElementById('itemSku').value = item.sku || '';
            document.getElementById('itemUnit').value = item.unit || '';
            document.getElementById('currentStock').value = item.current_stock;
            document.getElementById('minStock').value = item.min_stock;
            document.getElementById('maxStock').value = item.max_stock || '';
            document.getElementById('costPrice').value = item.cost_price;
            document.getElementById('reorderPoint').value = item.reorder_point || '';
            document.getElementById('isActive').checked = item.is_active;

            bootstrapModal.show();
        }

        // Save inventory
        async function saveInventory() {
            const itemId = document.getElementById('itemId').value;
            const data = {
                name: document.getElementById('itemName').value,
                sku: document.getElementById('itemSku').value,
                unit: document.getElementById('itemUnit').value,
                current_stock: parseFloat(document.getElementById('currentStock').value),
                min_stock: parseFloat(document.getElementById('minStock').value),
                max_stock: parseFloat(document.getElementById('maxStock').value) || null,
                cost_price: parseFloat(document.getElementById('costPrice').value),
                reorder_point: parseFloat(document.getElementById('reorderPoint').value) || null,
                is_active: document.getElementById('isActive').checked
            };

            // Validate
            if (!data.name || !data.sku || !data.unit) {
                alert('Please fill in all required fields');
                return;
            }

            try {
                const url = itemId ? '/php-native/api/inventory/update.php' : '/php-native/api/inventory/store.php';
                const method = itemId ? 'PUT' : 'POST';
                
                if (itemId) {
                    data.id = itemId;
                }

                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    bootstrapModal.hide();
                    showSuccess(itemId ? 'Item updated successfully' : 'Item added successfully');
                    loadInventory();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error saving inventory:', error);
                alert('Error saving inventory');
            }
        }

        // Delete item
        async function deleteItem(id) {
            if (!confirm('Are you sure you want to delete this item?')) return;

            try {
                const response = await fetch(`/php-native/api/inventory/delete.php?id=${id}`, {
                    method: 'DELETE'
                });

                const result = await response.json();

                if (result.success) {
                    showSuccess('Item deleted successfully');
                    loadInventory();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error deleting item:', error);
                alert('Error deleting item');
            }
        }

        // View movements
        async function viewMovements(itemId) {
            try {
                const response = await fetch(`/php-native/api/inventory/movements.php?item_id=${itemId}`);
                const data = await response.json();

                if (data.success) {
                    const movements = data.movements || [];
                    let html = '';

                    if (movements.length === 0) {
                        html = '<p style="text-align: center; color: rgba(255,255,255,0.5);">No movement history</p>';
                    } else {
                        html = '<div style="max-height: 400px; overflow-y: auto;">';
                        movements.forEach(mov => {
                            const color = mov.movement_type === 'in' ? '#28a745' : '#dc3545';
                            const sign = mov.movement_type === 'in' ? '+' : '-';
                            html += `
                                <div style="padding: 10px; border-bottom: 1px solid rgba(212,175,55,0.1);">
                                    <div style="display: flex; justify-content: space-between;">
                                        <strong style="color: ${color};">${mov.movement_type.toUpperCase()}</strong>
                                        <span style="color: ${color}; font-weight: bold;">${sign}${parseFloat(mov.quantity).toFixed(2)} ${mov.unit}</span>
                                    </div>
                                    <div style="font-size: 0.85rem; color: rgba(255,255,255,0.6); margin-top: 5px;">
                                        ${mov.reference_type ? mov.reference_type + ' - ' : ''}${mov.notes || 'No notes'}
                                    </div>
                                    <div style="font-size: 0.75rem; color: rgba(255,255,255,0.4); margin-top: 3px;">
                                        ${new Date(mov.created_at).toLocaleString('id-ID')}
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    }

                    document.getElementById('movementHistory').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('movementModal')).show();
                } else {
                    alert('Error loading movement history');
                }
            } catch (error) {
                console.error('Error loading movements:', error);
                alert('Error loading movement history');
            }
        }

        // Show success message
        function showSuccess(message) {
            // Simple alert for now - can be enhanced with toast
            alert('✅ ' + message);
        }

        // Show error message
        function showError(message) {
            alert('❌ ' + message);
        }
    </script>
</body>
</html>
