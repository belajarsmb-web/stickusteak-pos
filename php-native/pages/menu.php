<?php
/**
 * Stickusteak POS - Menu Management
 * Premium Black & Gold Theme
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
$page_title = "Menu";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Stickusteak POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="/php-native/assets/css/premium-theme.css" rel="stylesheet">
    <style>
        /* Fix dropdown options visibility */
        .form-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #fff;
        }
        
        .form-select:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
            color: #fff;
        }
        
        .form-select option {
            background: #1a1a1a;
            color: #fff;
            padding: 10px;
        }
        
        .form-select option:hover {
            background: var(--gold-primary);
            color: var(--black-primary);
        }
        
        .form-select option:checked,
        .form-select option:checked:hover,
        .form-select option:focus,
        .form-select option:active {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            color: var(--black-primary);
            font-weight: 600;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .menu-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: slideUp 0.6s ease-out;
            position: relative;
        }
        
        .menu-card:hover {
            transform: translateY(-10px) scale(1.02);
            border-color: var(--gold-primary);
            box-shadow: 0 20px 50px rgba(212,175,55,0.3);
        }
        
        .menu-card-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: linear-gradient(135deg, rgba(212,175,55,0.1) 0%, rgba(212,175,55,0.05) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .menu-card-image::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50%;
            background: linear-gradient(to top, rgba(26,26,26,0.9), transparent);
        }
        
        .menu-card-icon {
            font-size: 4rem;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            z-index: 1;
        }
        
        .menu-card-body {
            padding: 20px;
        }
        
        .menu-card-name {
            font-size: 1.2rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            color: var(--gold-light);
            margin-bottom: 8px;
        }
        
        .menu-card-description {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.6);
            margin-bottom: 15px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .menu-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid rgba(212,175,55,0.1);
        }
        
        .menu-card-price {
            font-size: 1.4rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .menu-card-actions {
            display: flex;
            gap: 8px;
        }
        
        .availability-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 2;
        }
        
        .available {
            background: rgba(40,167,69,0.9);
            color: #fff;
            border: 1px solid #28a745;
        }
        
        .unavailable {
            background: rgba(220,53,69,0.9);
            color: #fff;
            border: 1px solid #dc3545;
        }
        
        .category-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        
        .category-btn {
            padding: 10px 20px;
            border-radius: 20px;
            border: 1px solid rgba(212,175,55,0.3);
            background: transparent;
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .category-btn:hover, .category-btn.active {
            background: var(--gold-gradient);
            border-color: var(--gold-primary);
            color: var(--black-primary);
            transform: translateY(-2px);
        }
        
        .search-box {
            position: relative;
            margin-bottom: 25px;
        }
        
        .search-box input {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212,175,55,0.3);
            color: #fff;
            padding: 14px 20px 14px 50px;
            border-radius: 25px;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(212,175,55,0.2);
        }
        
        .search-box input::placeholder {
            color: rgba(255,255,255,0.4);
        }
        
        .search-box i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
            font-size: 1.2rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: rgba(255,255,255,0.4);
            grid-column: 1 / -1;
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
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-mini {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-mini:hover {
            transform: translateY(-5px);
            border-color: var(--gold-primary);
        }
        
        .stat-mini-value {
            font-size: 2rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-mini-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
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
                    <a href="/php-native/pages/menu.php" class="nav-link active">
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
                <!-- Page Header -->
                <div class="pos-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1"><i class="bi bi-menu-button-wide me-2"></i>Menu Management</h2>
                            <p class="mb-0" style="color: rgba(255,255,255,0.5); font-size: 0.9rem;">Manage your restaurant menu items</p>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary me-2" onclick="openCategoryModal()">
                                <i class="bi bi-folder me-1"></i>Categories
                            </button>
                            <button class="btn btn-primary" onclick="openAddItemModal()">
                                <i class="bi bi-plus-lg me-1"></i>Add Item
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="stats-row">
                    <div class="stat-mini">
                        <div class="stat-mini-value" id="totalItems">0</div>
                        <div class="stat-mini-label">Total Items</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-value" id="availableItems">0</div>
                        <div class="stat-mini-label">Available</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-value" id="unavailableItems">0</div>
                        <div class="stat-mini-label">Unavailable</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-value" id="totalCategories">0</div>
                        <div class="stat-mini-label">Categories</div>
                    </div>
                </div>

                <!-- Search & Filter -->
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="searchMenu" placeholder="Search menu items..." onkeyup="filterMenu()">
                </div>

                <div class="category-filter" id="categoryFilter">
                    <button class="category-btn active" onclick="filterCategory('all')">All Items</button>
                </div>

                <!-- Menu Grid -->
                <div class="menu-grid" id="menuGrid">
                    <div class="empty-state">
                        <i class="bi bi-hourglass-split"></i>
                        <h3>Loading menu...</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Item Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i><span id="modalTitle">Add Menu Item</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="itemForm">
                        <input type="hidden" id="itemId">
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="itemName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="itemDescription" rows="2"></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Price (Rp)</label>
                                <input type="number" class="form-control" id="itemPrice" step="1000" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select class="form-select" id="itemCategory">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Display Routing</label>
                            <select class="form-select" id="itemDisplayRouting">
                                <option value="kitchen">Kitchen Only</option>
                                <option value="bar">Bar Only</option>
                                <option value="both">Both Kitchen & Bar</option>
                            </select>
                            <small class="text-muted" style="color: rgba(255,255,255,0.4);">Determines which display this item appears on</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="itemImage" accept="image/*">
                            <div id="currentImagePreview" class="mt-2"></div>
                            <small class="text-muted" style="color: rgba(255,255,255,0.4);">Upload product photo (JPG, PNG, max 2MB)</small>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Available</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="itemAvailable" checked>
                                <label class="form-check-label" for="itemAvailable" style="color: rgba(255,255,255,0.8);">Show this item on menu</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitItem()">
                        <i class="bi bi-check-lg me-1"></i>Save Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-folder me-2"></i>Manage Categories</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <button class="btn btn-primary btn-sm" onclick="showAddCategory()">
                            <i class="bi bi-plus-lg me-1"></i>Add Category
                        </button>
                    </div>
                    <div id="categoriesList" class="list-group">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-folder-plus me-2"></i><span id="categoryModalTitle">Add Category</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <input type="hidden" id="categoryId">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="categoryDescription" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="categoryColor" value="#007bff" style="width: 100px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Display Routing</label>
                            <select class="form-select" id="categoryRouting">
                                <option value="kitchen">Kitchen</option>
                                <option value="bar">Bar</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="categoryActive" checked>
                            <label class="form-check-label" style="color: rgba(255,255,255,0.8);">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveCategory()">
                        <i class="bi bi-check-lg me-1"></i>Save Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let menuItems = [];
        let categories = [];
        let currentFilter = 'all';

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadMenu();
            loadCategories();
        });

        // Load menu
        async function loadMenu() {
            try {
                const response = await fetch('/php-native/api/menu/index.php');
                const data = await response.json();
                
                if (data.success && data.items) {
                    menuItems = data.items;
                    renderMenu(menuItems);
                    updateStats();
                }
            } catch (error) {
                console.error('Error loading menu:', error);
            }
        }

        // Load categories
        async function loadCategories() {
            try {
                const response = await fetch('/php-native/api/menu/categories.php');
                const data = await response.json();

                if (data.success) {
                    categories = data.categories;
                    renderCategoryFilter();
                    loadCategoryDropdown();
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        // Load categories to dropdown
        async function loadCategoryDropdown() {
            try {
                const response = await fetch('/php-native/api/menu/categories.php');
                const data = await response.json();

                if (data.success) {
                    const select = document.getElementById('itemCategory');
                    select.innerHTML = '<option value="">Select Category</option>';
                    
                    data.categories.forEach(cat => {
                        select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        // Render category filter
        function renderCategoryFilter() {
            const container = document.getElementById('categoryFilter');
            let html = '<button class="category-btn active" onclick="filterCategory(\'all\')">All Items</button>';
            
            categories.forEach(cat => {
                html += `<button class="category-btn" onclick="filterCategory('${cat.id}')">${cat.name}</button>`;
            });
            
            container.innerHTML = html;
            
            // Update stats
            document.getElementById('totalCategories').textContent = categories.length;
        }

        // Render menu grid
        function renderMenu(items) {
            const grid = document.getElementById('menuGrid');
            
            if (items.length === 0) {
                grid.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h3>No menu items found</h3>
                    </div>
                `;
                return;
            }
            
            let html = '';
            items.forEach(item => {
                const icon = getCategoryIcon(item.category_name);
                const imageHtml = item.image_url && item.image_url.trim() !== '' ? 
                    `<img src="${item.image_url}" alt="${item.name}" class="menu-card-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">` +
                    `<div class="menu-card-image" style="display:none;"><i class="bi ${icon} menu-card-icon"></i></div>` :
                    `<div class="menu-card-image"><i class="bi ${icon} menu-card-icon"></i></div>`;
                
                html += `
                    <div class="menu-card">
                        ${imageHtml}
                        <span class="availability-badge ${item.is_available == 1 ? 'available' : 'unavailable'}">
                            ${item.is_available == 1 ? 'Available' : 'Unavailable'}
                        </span>
                        <div class="menu-card-body">
                            <div class="menu-card-name">${item.name}</div>
                            <div class="menu-card-description">${item.description || 'No description'}</div>
                            <div class="menu-card-footer">
                                <div class="menu-card-price">Rp ${parseFloat(item.price).toLocaleString('id-ID')}</div>
                                <div class="menu-card-actions">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editItem(${item.id}, '${escapeHtml(item.name)}', '${escapeHtml(item.description || '')}', ${item.price}, ${item.category_id || 0}, ${item.is_available}, '${item.display_routing || 'kitchen'}', '${item.image_url || ''}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteItem(${item.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            grid.innerHTML = html;
        }

        // Update stats
        function updateStats() {
            document.getElementById('totalItems').textContent = menuItems.length;
            document.getElementById('availableItems').textContent = menuItems.filter(i => i.is_available == 1).length;
            document.getElementById('unavailableItems').textContent = menuItems.filter(i => i.is_available == 0).length;
        }

        // Filter by category
        function filterCategory(category) {
            currentFilter = category;
            
            document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            if (category === 'all') {
                renderMenu(menuItems);
            } else {
                const filtered = menuItems.filter(item => item.category_id == category);
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

        // Get icon based on category
        function getCategoryIcon(categoryName) {
            const icons = {
                'Steak Premium': 'bi-heart-fill',
                'Rice & Pasta': 'bi-bowl',
                'Appetizer': 'bi-flower1',
                'Beverages': 'bi-cup-straw',
                'Dessert': 'bi-cake2',
                'Coffee & Tea': 'bi-cup-hot'
            };
            return icons[categoryName] || 'bi-egg-fried';
        }

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML.replace(/'/g, "\\'");
        }

        // Open add item modal
        function openAddItemModal() {
            document.getElementById('itemId').value = '';
            document.getElementById('itemName').value = '';
            document.getElementById('itemDescription').value = '';
            document.getElementById('itemPrice').value = '';
            document.getElementById('itemDisplayRouting').value = 'kitchen';
            document.getElementById('itemAvailable').checked = true;
            document.getElementById('itemImage').value = '';
            document.getElementById('currentImagePreview').innerHTML = '';
            document.getElementById('modalTitle').textContent = 'Add Menu Item';

            // Load categories
            loadCategoryDropdown();
            
            // Clear category selection
            setTimeout(() => {
                document.getElementById('itemCategory').value = '';
            }, 100);

            const modal = new bootstrap.Modal(document.getElementById('itemModal'));
            modal.show();
        }

        // Edit item
        async function editItem(id, name, description, price, categoryId, isAvailable, displayRouting, imageUrl) {
            document.getElementById('itemId').value = id;
            document.getElementById('itemName').value = name;
            document.getElementById('itemDescription').value = description;
            document.getElementById('itemPrice').value = price;
            document.getElementById('itemDisplayRouting').value = displayRouting || 'kitchen';
            document.getElementById('itemAvailable').checked = isAvailable == 1;
            document.getElementById('modalTitle').textContent = 'Edit Menu Item';
            document.getElementById('itemImage').value = '';

            // Load categories first, then set the value
            await loadCategoryDropdown();
            
            // Set category after dropdown is loaded
            setTimeout(() => {
                document.getElementById('itemCategory').value = categoryId;
            }, 100);

            // Show current image preview
            const previewDiv = document.getElementById('currentImagePreview');
            if (imageUrl && imageUrl.trim() !== '') {
                previewDiv.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <img src="${imageUrl}" alt="${name}" style="max-width: 200px; border-radius: 8px; margin-top: 5px;">
                        <small class="text-muted" style="color: rgba(255,255,255,0.5);">Current image</small>
                    </div>
                `;
            } else {
                previewDiv.innerHTML = '<small class="text-muted" style="color: rgba(255,255,255,0.5);">No image set</small>';
            }

            const modal = new bootstrap.Modal(document.getElementById('itemModal'));
            modal.show();
        }

        // Submit item
        async function submitItem() {
            const id = document.getElementById('itemId').value;
            const name = document.getElementById('itemName').value;
            const description = document.getElementById('itemDescription').value;
            const price = document.getElementById('itemPrice').value;
            const category_id = document.getElementById('itemCategory').value;
            const display_routing = document.getElementById('itemDisplayRouting').value;
            const is_available = document.getElementById('itemAvailable').checked ? 1 : 0;

            if (!name || !price || !category_id) {
                alert('Please fill in all required fields');
                return;
            }

            try {
                let image_url = '';
                
                // Upload image if selected
                const imageInput = document.getElementById('itemImage');
                if (imageInput.files && imageInput.files[0]) {
                    const formData = new FormData();
                    formData.append('image', imageInput.files[0]);
                    if (id) formData.append('item_id', id);
                    
                    const uploadResponse = await fetch('/php-native/api/menu/upload-image.php', {
                        method: 'POST',
                        body: formData
                    });
                    const uploadData = await uploadResponse.json();
                    
                    if (uploadData.success) {
                        image_url = uploadData.url;
                    } else {
                        alert('Image upload failed: ' + uploadData.message);
                        return;
                    }
                }
                
                const url = id ? '/php-native/api/menu/update.php' : '/php-native/api/menu/store.php';
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        id: id || null, 
                        name, 
                        description, 
                        price, 
                        category_id,
                        display_routing,
                        is_available,
                        image_url 
                    })
                });
                const data = await response.json();
                if (data.success) {
                    alert(id ? 'Item updated successfully!' : 'Item added successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                    loadMenu();
                } else {
                    alert(data.message || 'Failed to save item');
                }
            } catch (error) {
                console.error('Error saving item:', error);
                alert('Error saving item');
            }
        }

        // Delete item
        async function deleteItem(id) {
            if (!confirm('Are you sure you want to delete this item?')) return;

            try {
                const response = await fetch(`/php-native/api/menu/delete.php?id=${id}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                if (data.success) {
                    loadMenu();
                } else {
                    alert(data.message || 'Failed to delete item');
                }
            } catch (error) {
                console.error('Error deleting item:', error);
            }
        }

        // Category functions
        function openCategoryModal() {
            loadCategoriesList();
            const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
            modal.show();
        }

        async function loadCategoriesList() {
            try {
                const response = await fetch('/php-native/api/menu/categories.php');
                const data = await response.json();
                const container = document.getElementById('categoriesList');
                
                if (data.success && data.categories.length > 0) {
                    let html = '';
                    data.categories.forEach(cat => {
                        html += `
                            <div class="list-group-item d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.05); border-color: rgba(212,175,55,0.2); color: #fff;">
                                <div>
                                    <strong style="color: ${cat.color}">${cat.name}</strong>
                                    <small class="text-muted" style="color: rgba(255,255,255,0.4);">${cat.description || ''}</small>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editCategory(${cat.id}, '${escapeHtml(cat.name)}', '${escapeHtml(cat.description || '')}', '${cat.color}', '${cat.display_routing}', ${cat.is_active})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${cat.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p class="text-muted" style="color: rgba(255,255,255,0.4);">No categories found</p>';
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        function showAddCategory() {
            document.getElementById('categoryId').value = '';
            document.getElementById('categoryName').value = '';
            document.getElementById('categoryDescription').value = '';
            document.getElementById('categoryColor').value = '#007bff';
            document.getElementById('categoryRouting').value = 'kitchen';
            document.getElementById('categoryActive').checked = true;
            document.getElementById('categoryModalTitle').textContent = 'Add Category';
            
            bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
            const modal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            modal.show();
        }

        function editCategory(id, name, description, color, routing, isActive) {
            document.getElementById('categoryId').value = id;
            document.getElementById('categoryName').value = name;
            document.getElementById('categoryDescription').value = description;
            document.getElementById('categoryColor').value = color;
            document.getElementById('categoryRouting').value = routing;
            document.getElementById('categoryActive').checked = isActive;
            document.getElementById('categoryModalTitle').textContent = 'Edit Category';
            
            bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
            const modal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            modal.show();
        }

        async function saveCategory() {
            const id = document.getElementById('categoryId').value;
            const name = document.getElementById('categoryName').value;
            const description = document.getElementById('categoryDescription').value;
            const color = document.getElementById('categoryColor').value;
            const display_routing = document.getElementById('categoryRouting').value;
            const is_active = document.getElementById('categoryActive').checked ? 1 : 0;

            if (!name) {
                alert('Category name is required');
                return;
            }

            try {
                const url = id ? '/php-native/api/menu/categories.php' : '/php-native/api/menu/categories.php';
                const method = id ? 'PUT' : 'POST';
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id || null, name, description, color, display_routing, is_active })
                });
                const data = await response.json();
                if (data.success) {
                    alert(id ? 'Category updated successfully!' : 'Category created successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
                    loadCategories();
                    loadCategoriesList();
                } else {
                    alert(data.message || 'Failed to save category');
                }
            } catch (error) {
                console.error('Error saving category:', error);
                alert('Error saving category');
            }
        }

        async function deleteCategory(id) {
            if (!confirm('Are you sure you want to delete this category?')) return;
            
            try {
                const response = await fetch(`/php-native/api/menu/categories.php?id=${id}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                if (data.success) {
                    loadCategories();
                    loadCategoriesList();
                } else {
                    alert(data.message || 'Failed to delete category');
                }
            } catch (error) {
                console.error('Error deleting category:', error);
                alert('Error deleting category');
            }
        }
    </script>
</body>
</html>
