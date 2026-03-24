<?php
/**
 * Stickusteak POS - Recipe Management
 * Premium Black & Gold Theme
 * Manage recipes linking menu items to ingredients
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Management - Stickusteak POS</title>
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

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
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

        .recipe-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9), rgba(26,26,26,0.9));
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .recipe-card:hover {
            border-color: var(--gold-primary);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.2);
        }

        .recipe-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }

        .recipe-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gold-light);
        }

        .recipe-cost {
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .ingredient-item {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-premium {
            background: var(--gold-gradient);
            border: none;
            color: var(--black-primary);
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-premium:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.4);
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

        .form-control, .form-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            color: #fff;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.08);
            border-color: var(--gold-primary);
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
            color: var(--gold-light);
            font-weight: 600;
        }

        .ingredient-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
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
            <a href="/php-native/pages/menu.php" class="nav-link">
                <i class="bi bi-menu-button-wide"></i>Menu
            </a>
            <a href="/php-native/pages/recipes.php" class="nav-link active">
                <i class="bi bi-journal-text"></i>Recipes
            </a>
            <a href="/php-native/pages/modifiers.php" class="nav-link">
                <i class="bi bi-ui-checks-grid"></i>Modifiers
            </a>
            <a href="/php-native/pages/shifts.php" class="nav-link">
                <i class="bi bi-clock-history"></i>Shifts
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
        <h1 class="page-title mb-4">📖 Recipe Management</h1>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p style="color: rgba(255,255,255,0.6);">Link menu items to ingredients for auto stock deduction</p>
            <button class="btn btn-premium" onclick="openAddRecipeModal()">
                <i class="bi bi-plus-circle me-2"></i>Add Recipe
            </button>
        </div>

        <!-- Recipes List -->
        <div id="recipesList">
            <div style="text-align: center; padding: 60px 20px; color: rgba(255,255,255,0.5);">
                <i class="bi bi-hourglass-split" style="font-size: 3rem;"></i>
                <p>Loading recipes...</p>
            </div>
        </div>
    </div>

    <!-- Add/Edit Recipe Modal -->
    <div class="modal fade" id="recipeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">📖 Add Recipe</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="recipeForm">
                        <input type="hidden" id="recipeId">
                        <div class="mb-4">
                            <label class="form-label">Menu Item *</label>
                            <select class="form-select" id="menuItemSelect" required>
                                <option value="">Select Menu Item</option>
                            </select>
                        </div>
                        
                        <h6 class="mb-3" style="color: var(--gold-light);">Ingredients</h6>
                        <div id="ingredientsList">
                            <!-- Dynamic ingredient rows will be added here -->
                        </div>
                        
                        <button type="button" class="btn btn-premium mt-3" onclick="addIngredientRow()">
                            <i class="bi bi-plus-circle me-2"></i>Add Ingredient
                        </button>
                        
                        <div class="mt-4 p-3" style="background: rgba(212, 175, 55, 0.1); border-radius: 8px;">
                            <div class="d-flex justify-content-between">
                                <strong style="color: var(--gold-light);">Total Recipe Cost:</strong>
                                <strong class="recipe-cost" id="totalRecipeCost">Rp 0</strong>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-premium" onclick="saveRecipe()">
                        <i class="bi bi-check-circle me-2"></i>Save Recipe
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let recipes = [];
        let menuItems = [];
        let inventoryItems = [];
        let recipeModal;

        document.addEventListener('DOMContentLoaded', function() {
            recipeModal = new bootstrap.Modal(document.getElementById('recipeModal'));
            loadRecipes();
            loadMenuItems();
            loadInventoryItems();
        });

        // Load all recipes
        async function loadRecipes() {
            try {
                const response = await fetch('/php-native/api/recipes/index.php');
                const data = await response.json();
                
                if (data.success) {
                    recipes = data.recipes || [];
                    renderRecipes();
                } else {
                    document.getElementById('recipesList').innerHTML = '<p class="text-center text-danger">Error loading recipes</p>';
                }
            } catch (error) {
                console.error('Error loading recipes:', error);
                document.getElementById('recipesList').innerHTML = '<p class="text-center text-danger">Error loading recipes</p>';
            }
        }

        // Render recipes list
        function renderRecipes() {
            const container = document.getElementById('recipesList');
            
            if (recipes.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 60px 20px; color: rgba(255,255,255,0.5);">
                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                        <p>No recipes found. Click "Add Recipe" to create one.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            recipes.forEach(recipe => {
                html += `
                    <div class="recipe-card">
                        <div class="recipe-header">
                            <div>
                                <div class="recipe-name">${recipe.menu_item_name || 'Unknown Item'}</div>
                                <small style="color: rgba(255,255,255,0.5);">${recipe.ingredients_count || 0} ingredients</small>
                            </div>
                            <div style="text-align: right;">
                                <div class="recipe-cost">${formatIDR(recipe.total_cost || 0)}</div>
                                <small style="color: rgba(255,255,255,0.5);">per serving</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-premium" onclick="editRecipe(${recipe.id})">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRecipe(${recipe.id})">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Load menu items
        async function loadMenuItems() {
            try {
                const response = await fetch('/php-native/api/menu/index.php');
                const data = await response.json();
                if (data.success) {
                    menuItems = data.items || [];
                    updateMenuItemSelect();
                }
            } catch (error) {
                console.error('Error loading menu items:', error);
            }
        }

        // Load inventory items
        async function loadInventoryItems() {
            try {
                const response = await fetch('/php-native/api/inventory/index.php');
                const data = await response.json();
                if (data.success) {
                    inventoryItems = data.data || [];
                    console.log('Loaded inventory items:', inventoryItems.length);
                    
                    // Show message if no inventory items
                    if (inventoryItems.length === 0) {
                        alert('⚠️ No inventory items found!\n\nPlease add inventory items first before creating recipes.\n\nGo to: Inventory Management page');
                    }
                }
            } catch (error) {
                console.error('Error loading inventory items:', error);
            }
        }

        // Update menu item select dropdown
        function updateMenuItemSelect() {
            const select = document.getElementById('menuItemSelect');
            const usedIds = recipes.map(r => r.menu_item_id);
            
            let options = '<option value="">Select Menu Item</option>';
            menuItems.forEach(item => {
                if (!usedIds.includes(item.id)) {
                    options += `<option value="${item.id}">${item.name}</option>`;
                }
            });
            select.innerHTML = options;
        }

        // Open add recipe modal
        function openAddRecipeModal() {
            document.getElementById('recipeForm').reset();
            document.getElementById('recipeId').value = '';
            document.getElementById('modalTitle').textContent = '📖 Add Recipe';
            document.getElementById('ingredientsList').innerHTML = '';
            updateMenuItemSelect();
            
            // Reload inventory items to ensure dropdown is populated
            loadInventoryItems().then(() => {
                addIngredientRow(); // Add first row after inventory loaded
            });
            
            recipeModal.show();
        }

        // Edit recipe
        async function editRecipe(id) {
            const recipe = recipes.find(r => r.id === id);
            if (!recipe) return;

            document.getElementById('recipeId').value = id;
            document.getElementById('modalTitle').textContent = '✏️ Edit Recipe';
            document.getElementById('menuItemSelect').value = recipe.menu_item_id;
            
            // Load recipe ingredients
            try {
                const response = await fetch(`/php-native/api/recipes/index.php?id=${id}`);
                const data = await response.json();
                if (data.success && data.recipe && data.recipe.ingredients) {
                    document.getElementById('ingredientsList').innerHTML = '';
                    data.recipe.ingredients.forEach(ing => {
                        addIngredientRow(ing.inventory_item_id, ing.quantity, ing.unit);
                    });
                    recipeModal.show();
                }
            } catch (error) {
                console.error('Error loading recipe:', error);
                alert('Error loading recipe details');
            }
        }

        // Add ingredient row
        function addIngredientRow(selectedId = '', qty = '', unit = '') {
            const container = document.getElementById('ingredientsList');
            const rowId = 'ing-' + Date.now();
            
            let options = '<option value="">Select Ingredient</option>';
            inventoryItems.forEach(item => {
                options += `<option value="${item.id}" data-unit="${item.unit}" data-cost="${item.cost_price || 0}" ${item.id == selectedId ? 'selected' : ''}>${item.name} (${item.unit})</option>`;
            });

            const row = document.createElement('div');
            row.className = 'ingredient-row';
            row.id = rowId;
            row.innerHTML = `
                <select class="form-select ingredient-select" onchange="updateRowCost('${rowId}')">
                    ${options}
                </select>
                <input type="number" class="form-control ingredient-qty" placeholder="Qty" value="${qty}" step="0.01" onchange="updateRowCost('${rowId}')">
                <input type="text" class="form-control ingredient-unit" placeholder="Unit" value="${unit}" readonly>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeIngredientRow('${rowId}')">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            container.appendChild(row);
            
            if (selectedId) {
                updateRowCost(rowId);
            }
        }

        // Remove ingredient row
        function removeIngredientRow(rowId) {
            const row = document.getElementById(rowId);
            if (row) row.remove();
            calculateTotalCost();
        }

        // Update row cost
        function updateRowCost(rowId) {
            const row = document.getElementById(rowId);
            const select = row.querySelector('.ingredient-select');
            const qtyInput = row.querySelector('.ingredient-qty');
            const unitInput = row.querySelector('.ingredient-unit');
            
            const option = select.options[select.selectedIndex];
            if (option.value) {
                unitInput.value = option.dataset.unit;
            }
            
            calculateTotalCost();
        }

        // Calculate total cost
        function calculateTotalCost() {
            let total = 0;
            document.querySelectorAll('.ingredient-row').forEach(row => {
                const select = row.querySelector('.ingredient-select');
                const qtyInput = row.querySelector('.ingredient-qty');
                
                const option = select.options[select.selectedIndex];
                if (option.value && qtyInput.value) {
                    const cost = parseFloat(option.dataset.cost || 0);
                    const qty = parseFloat(qtyInput.value || 0);
                    total += cost * qty;
                }
            });
            
            document.getElementById('totalRecipeCost').textContent = formatIDR(total);
        }

        // Save recipe
        async function saveRecipe() {
            const recipeId = document.getElementById('recipeId').value;
            const menuItemId = document.getElementById('menuItemSelect').value;
            
            if (!menuItemId) {
                alert('Please select a menu item');
                return;
            }

            // Collect ingredients
            const ingredients = [];
            document.querySelectorAll('.ingredient-row').forEach(row => {
                const select = row.querySelector('.ingredient-select');
                const qtyInput = row.querySelector('.ingredient-qty');
                const unitInput = row.querySelector('.ingredient-unit');
                
                if (select.value && qtyInput.value) {
                    ingredients.push({
                        inventory_item_id: select.value,
                        quantity: parseFloat(qtyInput.value),
                        unit: unitInput.value
                    });
                }
            });

            if (ingredients.length === 0) {
                alert('Please add at least one ingredient');
                return;
            }

            try {
                const url = recipeId ? '/php-native/api/recipes/update.php' : '/php-native/api/recipes/store.php';
                const method = recipeId ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: recipeId,
                        menu_item_id: menuItemId,
                        ingredients: ingredients
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    recipeModal.hide();
                    alert(recipeId ? '✅ Recipe updated!' : '✅ Recipe created!');
                    loadRecipes();
                } else {
                    alert('❌ Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error saving recipe:', error);
                alert('❌ Error saving recipe');
            }
        }

        // Delete recipe
        async function deleteRecipe(id) {
            if (!confirm('Are you sure you want to delete this recipe?')) return;

            try {
                const response = await fetch(`/php-native/api/recipes/delete.php?id=${id}`, {
                    method: 'DELETE'
                });

                const data = await response.json();
                
                if (data.success) {
                    alert('✅ Recipe deleted!');
                    loadRecipes();
                } else {
                    alert('❌ Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error deleting recipe:', error);
                alert('❌ Error deleting recipe');
            }
        }

        // Format currency
        function formatIDR(amount) {
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID', {maximumFractionDigits: 0});
        }
    </script>
</body>
</html>
