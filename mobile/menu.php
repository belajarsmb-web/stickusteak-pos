<?php
/**
 * Stickusteak POS - Mobile Menu Page
 * Browse menu and add items to cart
 */

$token = $_GET['token'] ?? '';
$tableId = $_GET['table'] ?? 0;
$page_title = "Menu";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Menu - Stickusteak</title>
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
        }
        
        * { font-family: 'Poppins', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', serif; }
        
        body {
            background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 100%);
            color: #fff;
            padding-bottom: 80px;
        }
        
        .header-bar {
            background: linear-gradient(135deg, rgba(212,175,55,0.2) 0%, transparent 100%);
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(212,175,55,0.1);
        }
        
        .header-title {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }
        
        .category-scroll {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 15px 20px;
            scrollbar-width: none;
        }
        
        .category-scroll::-webkit-scrollbar { display: none; }
        
        .category-pill {
            padding: 10px 20px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 25px;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .category-pill.active, .category-pill:hover {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            border-color: var(--gold-primary);
            color: var(--black-primary);
        }
        
        .menu-section {
            padding: 20px;
        }
        
        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gold-light);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(212,175,55,0.2);
        }
        
        .menu-item-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.1);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            gap: 15px;
        }
        
        .item-details { flex: 1; }
        
        .item-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--gold-light);
            margin-bottom: 5px;
        }
        
        .item-description {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .item-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .item-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--gold-primary);
        }
        
        .add-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            border: none;
            color: var(--black-primary);
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .add-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(212,175,55,0.3);
        }
        
        .cart-float {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            color: var(--black-primary);
            padding: 15px 30px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 10px 30px rgba(212,175,55,0.4);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .cart-float:hover {
            transform: translateX(-50%) translateY(-3px);
        }
        
        .cart-count {
            background: rgba(0,0,0,0.2);
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 700;
        }
        
        .cart-total {
            font-weight: 700;
        }
        
        .loading-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255,255,255,0.4);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-bar">
        <h1 class="header-title">Our Menu</h1>
    </div>

    <!-- Category Scroll -->
    <div class="category-scroll" id="categoryScroll">
        <div class="category-pill active" onclick="filterCategory('all')">All Items</div>
    </div>

    <!-- Menu Content -->
    <div class="menu-section" id="menuContent">
        <div class="loading-state">
            <div class="spinner-border" style="color: var(--gold-primary);"></div>
            <p style="margin-top: 15px;">Loading menu...</p>
        </div>
    </div>

    <!-- Floating Cart Button -->
    <div class="cart-float" id="cartFloat" onclick="goToCart()" style="display: none;">
        <i class="bi bi-basket"></i>
        <span class="cart-count" id="cartCount">0</span>
        <span class="cart-total" id="cartTotal">Rp 0</span>
        <i class="bi bi-chevron-right"></i>
    </div>

    <!-- Modifier Modal -->
    <div class="modal fade" id="modifierModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content" style="background: linear-gradient(135deg, rgba(42,42,42,0.95) 0%, rgba(26,26,26,0.95) 100%); border: 1px solid rgba(212,175,55,0.2);">
                <div class="modal-header" style="border-bottom: 1px solid rgba(212,175,55,0.1);">
                    <h5 class="modal-title" id="modifierModalTitle" style="color: var(--gold-light);">
                        <i class="bi bi-ui-checks-grid me-2"></i>Customize Your Order
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modifierModalBody">
                    <!-- Modifier groups will be rendered here -->
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(212,175,55,0.1);">
                    <div style="flex: 1;">
                        <div style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Total</div>
                        <div id="modalTotalPrice" style="color: var(--gold-primary); font-size: 1.3rem; font-weight: 700;">Rp 0</div>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(212,175,55,0.3); color: #fff;">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addToCartBtn" style="background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary)); border: none; color: var(--black-primary); font-weight: 600;">
                        <i class="bi bi-basket me-2"></i>Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Modal for Required Categories -->
    <div class="modal fade" id="notesModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content" style="background: linear-gradient(135deg, rgba(42,42,42,0.95) 0%, rgba(26,26,26,0.95) 100%); border: 1px solid rgba(212,175,55,0.2);">
                <div class="modal-header" style="border-bottom: 1px solid rgba(212,175,55,0.1);">
                    <h5 class="modal-title" id="notesModalTitle" style="color: var(--gold-light);">
                        <i class="bi bi-chat-left-text me-2"></i>Customize Your Order
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="notesModalBody">
                    <div class="alert alert-info" style="background: rgba(212,175,55,0.1); border: 1px solid rgba(212,175,55,0.3); color: var(--gold-light);">
                        <i class="bi bi-info-circle me-2"></i>
                        Please select your preferences for this item.
                    </div>
                    <div id="notesOptionsContainer">
                        <!-- Notes options will be rendered here -->
                    </div>
                    <div class="mt-3">
                        <label class="form-label" style="color: rgba(255,255,255,0.8);">Custom Notes (Optional):</label>
                        <textarea class="form-control" id="customNotesInput" rows="2" placeholder="e.g., Extra spicy, Less salt..." style="background: rgba(255,255,255,0.05); border: 1px solid rgba(212,175,55,0.3); color: #fff; border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(212,175,55,0.1);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(212,175,55,0.3); color: #fff;">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmNotesBtn" style="background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary)); border: none; color: var(--black-primary); font-weight: 600;">
                        <i class="bi bi-check-lg me-2"></i>Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let menuData = [];
        let cart = JSON.parse(localStorage.getItem('mobileCart') || '[]');
        let tableId = <?php echo (int)$tableId; ?>;
        let token = '<?php echo htmlspecialchars($token); ?>';
        
        // Current item being added (with modifiers)
        let currentItem = null;
        let selectedModifiers = {};
        let selectedNotes = [];
        let modifierModal = null;
        let notesModal = null;
        let notesOptions = [
            { id: 1, name: 'Level Kematangan', options: ['Rare', 'Medium Rare', 'Medium', 'Medium Well', 'Well Done'] },
            { id: 2, name: 'Pilihan Saus', options: ['Black Pepper', 'Mushroom', 'BBQ', 'Garlic Butter', 'No Sauce'] },
            { id: 3, name: 'Pilihan Kentang', options: ['Mashed Potato', 'Baked Potato', 'French Fries', 'No Potato'] }
        ];

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadMenu();
            modifierModal = new bootstrap.Modal(document.getElementById('modifierModal'));
            notesModal = new bootstrap.Modal(document.getElementById('notesModal'));
            
            // Add to cart button handler
            document.getElementById('addToCartBtn').addEventListener('click', confirmAddToCart);
            
            // Confirm notes button handler
            document.getElementById('confirmNotesBtn').addEventListener('click', confirmNotesSelection);
        });

        // Load menu
        async function loadMenu() {
            try {
                const response = await fetch('/php-native/api/mobile/menu.php');
                const data = await response.json();

                if (data.success) {
                    menuData = data.menu;
                    renderCategories(data.categories);
                    renderMenu(data.menu, 'all', data.categoriesRequireNotes || []);
                    updateCartDisplay();
                }
            } catch (error) {
                console.error('Menu load error:', error);
            }
        }

        // Render categories
        function renderCategories(categories) {
            const container = document.getElementById('categoryScroll');
            let html = '<div class="category-pill active" onclick="filterCategory(\'all\')">All Items</div>';
            
            categories.forEach(cat => {
                html += `<div class="category-pill" onclick="filterCategory('${cat.id}')">${cat.name}</div>`;
            });
            
            container.innerHTML = html;
        }

        // Render menu
        function renderMenu(menu, filterCategory = 'all', categoriesRequireNotes = []) {
            const container = document.getElementById('menuContent');

            console.log('Rendering menu with categoriesRequireNotes:', categoriesRequireNotes);

            if (menu.length === 0) {
                container.innerHTML = '<div class="loading-state"><p>No items available</p></div>';
                return;
            }

            let html = '';
            menu.forEach(category => {
                if (filterCategory !== 'all' && category.category.id != filterCategory) return;

                if (category.items && category.items.length > 0) {
                    // Check if this category requires notes
                    const requireNotes = categoriesRequireNotes.some(cat => 
                        category.category.name.toLowerCase().includes(cat.toLowerCase())
                    );
                    
                    console.log(`Category: ${category.category.name}, requireNotes: ${requireNotes}`);
                    
                    html += `
                        <div class="category-section" data-category="${category.category.id}">
                            <h3 class="section-title">${category.category.name}</h3>
                            ${category.items.map(item => {
                                const hasModifiers = item.modifierGroups && item.modifierGroups.length > 0;
                                return `
                                <div class="menu-item-card">
                                    <div class="item-details">
                                        <div class="item-name">${item.name}</div>
                                        <div class="item-description">${item.description || 'No description'}</div>
                                        <div class="item-footer">
                                            <div class="item-price">Rp ${parseFloat(item.price).toLocaleString('id-ID')}</div>
                                            <button class="add-btn" onclick="handleAddToCart(${item.id}, '${item.name.replace(/'/g, "\\'")}', ${item.price}, ${hasModifiers}, ${requireNotes})">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `}).join('')}
                        </div>
                    `;
                }
            });

            container.innerHTML = html || '<div class="loading-state"><p>No items in this category</p></div>';
        }

        // Filter category
        function filterCategory(catId) {
            document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
            event.target.classList.add('active');
            renderMenu(menuData, catId);
        }

        // Handle add to cart (with or without modifiers/notes)
        function handleAddToCart(itemId, itemName, itemPrice, hasModifiers, requireNotes) {
            // Find the item in menuData
            let item = null;
            for (const category of menuData) {
                const found = category.items.find(i => i.id === itemId);
                if (found) {
                    item = found;
                    break;
                }
            }
            
            if (!item) return;
            
            // Check if notes are required for this category
            if (requireNotes) {
                // Show notes modal first
                showNotesModal(item, hasModifiers);
                return;
            }
            
            if (hasModifiers) {
                showModifierModal(item);
            } else {
                addToCart(itemId, itemName, itemPrice, [], []);
            }
        }

        // Show notes modal for required categories
        function showNotesModal(item, hasModifiers) {
            currentItem = { ...item, hasModifiers: hasModifiers, tempNotes: [] };
            selectedNotes = [];
            
            document.getElementById('notesModalTitle').innerHTML = 
                `<i class="bi bi-chat-left-text me-2"></i>Customize ${item.name}`;
            
            renderNotesModal();
            notesModal.show();
        }

        // Render notes modal content
        function renderNotesModal() {
            const container = document.getElementById('notesOptionsContainer');
            
            console.log('Rendering notes modal with options:', notesOptions);
            
            let html = '';
            notesOptions.forEach((noteGroup, groupIndex) => {
                html += `
                    <div class="notes-group" style="margin-bottom: 20px; padding: 15px; background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.2); border-radius: 12px;">
                        <h6 style="color: var(--gold-light); margin-bottom: 12px; font-size: 1rem;">
                            <i class="bi bi-check2-square me-2"></i>${noteGroup.name}
                            <span style="color: #ff6b6b; font-size: 0.75rem;">*</span>
                        </h6>
                        <div class="notes-options" style="display: flex; flex-direction: column; gap: 8px;">
                            ${noteGroup.options.map((option, optIndex) => `
                                <label style="display: flex; align-items: center; gap: 10px; padding: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.1); border-radius: 10px; cursor: pointer; transition: all 0.3s;"
                                    onmouseover="this.style.background='rgba(212,175,55,0.1)'"
                                    onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                                    <input type="radio" 
                                        name="notes-group-${groupIndex}" 
                                        value="${option}" 
                                        data-group="${noteGroup.name}"
                                        onchange="onNoteSelect(${groupIndex}, '${option}')"
                                        style="accent-color: var(--gold-primary); width: 18px; height: 18px; flex-shrink: 0;">
                                    <span style="color: #fff; flex: 1;">${option}</span>
                                </label>
                            `).join('')}
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            console.log('Notes modal rendered');
        }

        // Handle note selection
        function onNoteSelect(groupIndex, option) {
            if (!selectedNotes[groupIndex]) {
                selectedNotes[groupIndex] = option;
            } else {
                selectedNotes[groupIndex] = option;
            }
        }

        // Confirm notes selection
        function confirmNotesSelection() {
            // Validate all required notes are selected
            const totalGroups = notesOptions.length;
            const selectedCount = selectedNotes.filter(n => n !== undefined && n !== '').length;
            
            if (selectedCount < totalGroups) {
                alert(`Please select all options (${selectedCount}/${totalGroups} selected)`);
                return;
            }
            
            // Add custom notes if any
            const customNotes = document.getElementById('customNotesInput').value.trim();
            const allNotes = [...selectedNotes.filter(n => n), customNotes].filter(n => n);
            
            console.log('Notes confirmed:', allNotes);
            console.log('Current item:', currentItem);
            
            if (currentItem && currentItem.hasModifiers) {
                // Show modifier modal after notes
                currentItem.tempNotes = allNotes;
                notesModal.hide();
                // Wait for notes modal to close before opening modifier modal
                setTimeout(() => {
                    showModifierModal(currentItem);
                }, 300);
            } else {
                // Add to cart directly
                notesModal.hide();
                addToCart(currentItem.id, currentItem.name, currentItem.price, allNotes, []);
            }
        }

        // Show modifier modal
        function showModifierModal(item) {
            currentItem = { ...item, modifiers: [] };
            selectedModifiers = {};

            document.getElementById('modifierModalTitle').innerHTML = 
                `<i class="bi bi-ui-checks-grid me-2"></i>${item.name}`;

            renderModifierModal();
            modifierModal.show();
        }

        // Render modifier modal content
        function renderModifierModal() {
            const container = document.getElementById('modifierModalBody');
            const item = currentItem;

            console.log('Rendering modifier modal for item:', item);
            console.log('Item modifier groups:', item.modifierGroups);

            if (!item.modifierGroups || item.modifierGroups.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: rgba(255,255,255,0.5);">No customization options available for this item</p>';
                updateModalTotal();
                return;
            }

            let html = '';
            item.modifierGroups.forEach((group, groupIndex) => {
                const isRequired = group.selection_type === 'required' || group.is_required == 1;
                const minSelection = group.min_selection || 1;
                const maxSelection = group.max_selection || 1;
                const isMultiple = maxSelection > 1;

                html += `
                    <div class="modifier-group" style="margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid rgba(212,175,55,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h6 style="color: var(--gold-light); margin: 0;">
                                ${group.name}
                                ${isRequired ? '<span style="color: #ff6b6b; font-size: 0.8rem;"> (Required)</span>' : ''}
                            </h6>
                            <span style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">
                                ${isMultiple ? `Select ${minSelection}-${maxSelection}` : `Select ${minSelection}`}
                            </span>
                        </div>
                        <div class="modifier-options">
                            ${group.modifiers && group.modifiers.length > 0 ? group.modifiers.map((mod, modIndex) => {
                                const inputType = isMultiple ? 'checkbox' : 'radio';
                                const groupName = `modifier-group-${groupIndex}`;
                                return `
                                    <label class="modifier-option-item" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(212,175,55,0.1); border-radius: 10px; margin-bottom: 10px; cursor: pointer;">
                                        <input type="${inputType}"
                                            name="${groupName}"
                                            value="${mod.id}"
                                            data-price="${mod.price}"
                                            data-name="${mod.name.replace(/'/g, "\\'")}"
                                            data-group="${groupIndex}"
                                            onchange="onModifierSelect(${groupIndex}, ${mod.id}, '${mod.name.replace(/'/g, "\\'")}', ${mod.price})"
                                            style="accent-color: var(--gold-primary); width: 18px; height: 18px;">
                                        <span style="flex: 1; color: #fff;">${mod.name}</span>
                                        <span style="color: var(--gold-primary); font-weight: 600;">
                                            ${mod.price > 0 ? '+Rp ' + parseFloat(mod.price).toLocaleString('id-ID') : mod.price < 0 ? '-Rp ' + Math.abs(parseFloat(mod.price)).toLocaleString('id-ID') : 'Included'}
                                        </span>
                                    </label>
                                `;
                            }).join('') : '<p style="color: rgba(255,255,255,0.5); font-size: 0.9rem;">No options available</p>'}
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            updateModalTotal();
        }

        // Handle modifier selection
        function onModifierSelect(groupIndex, modifierId, modifierName, modifierPrice) {
            const group = currentItem.modifierGroups[groupIndex];
            const isMultiple = group.max_selection > 1;
            
            if (!selectedModifiers[groupIndex]) {
                selectedModifiers[groupIndex] = [];
            }
            
            if (isMultiple) {
                // For checkboxes, toggle selection
                const existingIndex = selectedModifiers[groupIndex].findIndex(m => m.id === modifierId);
                if (existingIndex >= 0) {
                    selectedModifiers[groupIndex].splice(existingIndex, 1);
                } else {
                    selectedModifiers[groupIndex].push({
                        id: modifierId,
                        name: modifierName,
                        price: parseFloat(modifierPrice),
                        groupId: group.id,
                        groupName: group.name
                    });
                }
            } else {
                // For radio buttons, replace selection
                selectedModifiers[groupIndex] = [{
                    id: modifierId,
                    name: modifierName,
                    price: parseFloat(modifierPrice),
                    groupId: group.id,
                    groupName: group.name
                }];
            }
            
            updateModalTotal();
        }

        // Update modal total price
        function updateModalTotal() {
            const basePrice = parseFloat(currentItem.price);
            let modifierTotal = 0;
            
            Object.values(selectedModifiers).forEach(groupModifiers => {
                if (groupModifiers) {
                    groupModifiers.forEach(mod => {
                        modifierTotal += mod.price;
                    });
                }
            });
            
            const total = basePrice + modifierTotal;
            document.getElementById('modalTotalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Confirm add to cart
        function confirmAddToCart() {
            // Validate required modifiers
            let isValid = true;
            let requiredMessage = '';
            
            if (currentItem.modifierGroups) {
                currentItem.modifierGroups.forEach((group, index) => {
                    const isRequired = group.selection_type === 'required' || group.is_required == 1;
                    const minSelection = group.min_selection || 1;
                    
                    if (isRequired) {
                        const selected = selectedModifiers[index] || [];
                        if (selected.length < minSelection) {
                            isValid = false;
                            requiredMessage += `Please select at least ${minSelection} option(s) for ${group.name}\n`;
                        }
                    }
                });
            }
            
            if (!isValid) {
                alert(requiredMessage);
                return;
            }
            
            // Collect all selected modifiers
            const allModifiers = [];
            Object.values(selectedModifiers).forEach(groupModifiers => {
                if (groupModifiers) {
                    allModifiers.push(...groupModifiers);
                }
            });
            
            // Calculate final price
            let modifierTotal = 0;
            allModifiers.forEach(mod => {
                modifierTotal += mod.price;
            });
            
            const finalPrice = parseFloat(currentItem.price) + modifierTotal;
            
            // Combine notes from notes modal and modifiers
            const allNotes = currentItem.tempNotes || [];
            
            console.log('Adding to cart:', {
                id: currentItem.id,
                name: currentItem.name,
                price: finalPrice,
                notes: allNotes,
                modifiers: allModifiers
            });
            
            // Add to cart
            addToCart(currentItem.id, currentItem.name, finalPrice, allNotes, allModifiers);
            
            modifierModal.hide();
        }

        // Add to cart
        function addToCart(itemId, itemName, itemPrice, notes = [], modifiers = []) {
            const existingItemIndex = cart.findIndex(i => i.id === itemId && JSON.stringify(i.notes) === JSON.stringify(notes) && JSON.stringify(i.modifiers) === JSON.stringify(modifiers));

            if (existingItemIndex >= 0) {
                cart[existingItemIndex].quantity += 1;
            } else {
                cart.push({
                    id: itemId,
                    name: itemName,
                    price: parseFloat(itemPrice),
                    quantity: 1,
                    notes: notes || [],
                    modifiers: modifiers || []
                });
            }

            saveCart();
            updateCartDisplay();

            // Show feedback
            const btn = event.target.closest('.add-btn') || document.getElementById('addToCartBtn');
            if (btn) {
                btn.style.transform = 'scale(1.1)';
                setTimeout(() => btn.style.transform = '', 200);
            }
        }

        // Save cart
        function saveCart() {
            localStorage.setItem('mobileCart', JSON.stringify(cart));
        }

        // Update cart display
        function updateCartDisplay() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            document.getElementById('cartCount').textContent = totalItems;
            document.getElementById('cartTotal').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
            
            document.getElementById('cartFloat').style.display = totalItems > 0 ? 'flex' : 'none';
        }

        // Go to cart
        function goToCart() {
            window.location.href = '/php-native/mobile/cart.php?token=' + token + '&table=' + tableId;
        }
    </script>
</body>
</html>
