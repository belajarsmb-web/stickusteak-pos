<?php
/**
 * Mobile POS - Order Page with Steak Modifiers
 */

require_once __DIR__ . '/../config/database.php';

$tableId = $_GET['table_id'] ?? 0;
$pdo = getDbConnection();

// Get table info
$table = $pdo->prepare("SELECT * FROM tables WHERE id = ? AND is_active = 1");
$table->execute([$tableId]);
$tableInfo = $table->fetch();

if (!$tableInfo) {
    die('Invalid table');
}

// Get categories
$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();

// Get modifier groups - get all groups
$modifierGroups = $pdo->query("SELECT g.*, 
    (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', m.id, 'name', m.name, 'price', m.price)) 
     FROM modifiers m WHERE m.modifier_group_id = g.id AND m.is_active = 1) as modifiers
    FROM modifier_groups g WHERE g.is_active = 1 ORDER BY g.id")->fetchAll();

// Get menu items
$menuItems = $pdo->query("
    SELECT m.*, c.name as category_name
    FROM menu_items m
    LEFT JOIN categories c ON m.category_id = c.id
    WHERE m.is_active = 1 AND m.is_available = 1
    ORDER BY c.sort_order, m.sort_order
")->fetchAll();

// Find steak category
$steakCategoryId = null;
foreach ($categories as $cat) {
    if (stripos($cat['name'], 'steak') !== false) {
        $steakCategoryId = $cat['id'];
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Order - <?php echo htmlspecialchars($tableInfo['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="mobile-order-v2.css" rel="stylesheet">
    <style>
        .modifier-section {
            margin-bottom: 15px;
            padding: 12px;
            background: #16213e;
            border-radius: 8px;
            border: 1px solid #e9456033;
        }
        .modifier-section h6 {
            color: #e94560;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .modifier-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e9456022;
        }
        .modifier-option:last-child {
            border-bottom: none;
        }
        .modifier-option label {
            flex: 1;
            margin: 0;
            font-size: 12px;
            color: #fff;
            cursor: pointer;
        }
        .modifier-option input[type="radio"] {
            margin-right: 10px;
            accent-color: #e94560;
        }
        .modifier-price {
            color: #e94560;
            font-size: 11px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h5>🍽️ <?php echo htmlspecialchars($tableInfo['name']); ?></h5>
        <small>Table <?php echo htmlspecialchars($tableInfo['table_number']); ?></small>
    </div>

    <!-- Category Nav -->
    <div class="category-nav">
        <button class="active" onclick="filterCategory('all', this)">All</button>
        <?php foreach ($categories as $cat): ?>
            <button onclick="filterCategory('<?php echo $cat['id']; ?>', this)">
                <?php echo htmlspecialchars($cat['name']); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Menu Grid -->
    <div class="menu-grid">
        <?php foreach ($menuItems as $item): ?>
            <div class="menu-card" data-category="<?php echo $item['category_id']; ?>">
                <?php if ($item['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <?php else: ?>
                    <div style="width:100%;height:150px;background:#222;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-camera" style="font-size:2rem;color:#444;"></i>
                    </div>
                <?php endif; ?>
                <div class="menu-card-body">
                    <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                    <?php if ($item['description']): ?>
                        <p><?php echo htmlspecialchars(substr($item['description'], 0, 80)); ?>...</p>
                    <?php endif; ?>
                    <div class="price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></div>
                    <button class="btn-add" onclick="showModifiers(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>', <?php echo $item['price']; ?>, <?php echo $item['category_id']; ?>)">
                        <i class="bi bi-plus-circle"></i> Add
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Cart Bar -->
    <div class="cart-bar" id="cartBar">
        <div class="cart-info">
            <small><span id="cartCount">0</span> items</small>
            <div class="total">Rp <span id="cartTotal">0</span></div>
        </div>
        <button class="btn-view" onclick="viewCart()">
            <i class="bi bi-cart3"></i> View Cart
        </button>
    </div>

    <!-- Modifiers Modal -->
    <div class="modal fade" id="modifiersModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🥩 Customize Your Steak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modifiersBody">
                    <!-- Modifiers rendered here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="addToCartWithModifiers()">
                        <i class="bi bi-check-circle"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🛒 Your Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cartModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="submitOrder()">
                        <i class="bi bi-check-circle"></i> Submit Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cart = [];
        let currentCategory = 'all';
        let currentItem = null;
        let selectedModifiers = {};
        const steakCategoryId = <?php echo $steakCategoryId ?: 'null'; ?>;
        const modifierGroups = <?php echo json_encode($modifierGroups); ?>;

        // Filter category
        function filterCategory(catId, btn) {
            currentCategory = catId;
            document.querySelectorAll('.category-nav button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            document.querySelectorAll('.menu-card').forEach(card => {
                if (catId === 'all' || card.dataset.category == catId) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Show modifiers for steak
        function showModifiers(id, name, price, categoryId) {
            if (categoryId == steakCategoryId) {
                // Show modifiers modal for steak
                currentItem = { id, name, basePrice: price, categoryId, modifiers: {}, finalPrice: price };
                selectedModifiers = {};
                
                let html = '';
                modifierGroups.forEach(group => {
                    const modifiers = JSON.parse(group.modifiers || '[]');
                    if (modifiers.length > 0) {
                        html += `
                            <div class="modifier-section">
                                <h6>${group.name} ${group.selection_type === 'required' ? '*' : ''}</h6>
                        `;
                        
                        if (group.selection_type === 'single') {
                            modifiers.forEach(mod => {
                                html += `
                                    <div class="modifier-option">
                                        <label>
                                            <input type="radio" name="modifier_${group.id}" value="${mod.id}" onchange="selectModifier('${group.id}', '${mod.name}', ${mod.price})">
                                            ${mod.name}
                                        </label>
                                        ${mod.price > 0 ? `<span class="modifier-price">+Rp ${mod.price.toLocaleString('id-ID')}</span>` : ''}
                                    </div>
                                `;
                            });
                        } else {
                            modifiers.forEach(mod => {
                                html += `
                                    <div class="modifier-option">
                                        <label>
                                            <input type="checkbox" value="${mod.id}" onchange="selectModifier('${group.id}', '${mod.name}', ${mod.price}, this.checked)">
                                            ${mod.name}
                                        </label>
                                        ${mod.price > 0 ? `<span class="modifier-price">+Rp ${mod.price.toLocaleString('id-ID')}</span>` : ''}
                                    </div>
                                `;
                            });
                        }
                        
                        html += '</div>';
                    }
                });
                
                document.getElementById('modifiersBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modifiersModal')).show();
            } else {
                // Add to cart directly for non-steak items
                addToCart(id, name, price, [], price);
            }
        }

        // Select modifier
        function selectModifier(groupId, name, price, checked = true) {
            if (!selectedModifiers[groupId]) {
                selectedModifiers[groupId] = [];
            }
            
            // For radio buttons, replace; for checkboxes, add/remove
            const group = modifierGroups.find(g => g.id == groupId);
            if (group && group.selection_type === 'single') {
                selectedModifiers[groupId] = [{ name, price }];
            } else {
                if (checked) {
                    selectedModifiers[groupId].push({ name, price });
                } else {
                    selectedModifiers[groupId] = selectedModifiers[groupId].filter(m => m.name !== name);
                }
            }
            
            // Calculate final price
            let modifierTotal = 0;
            Object.values(selectedModifiers).flat().forEach(m => {
                modifierTotal += m.price;
            });
            currentItem.finalPrice = currentItem.basePrice + modifierTotal;
        }

        // Add to cart with modifiers
        function addToCartWithModifiers() {
            const allModifiers = Object.values(selectedModifiers).flat();
            addToCart(
                currentItem.id,
                currentItem.name,
                currentItem.finalPrice,
                allModifiers,
                currentItem.finalPrice
            );
            bootstrap.Modal.getInstance(document.getElementById('modifiersModal')).hide();
        }

        // Add to cart
        function addToCart(id, name, price, modifiers, finalPrice) {
            const existing = cart.find(i => {
                if (i.id !== id) return false;
                if (JSON.stringify(i.modifiers) !== JSON.stringify(modifiers)) return false;
                return true;
            });
            
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({
                    id,
                    name,
                    price: finalPrice,
                    basePrice: finalPrice,
                    quantity: 1,
                    modifiers
                });
            }
            updateCartBar();
        }

        // Update cart bar
        function updateCartBar() {
            const count = cart.reduce((sum, i) => sum + i.quantity, 0);
            const total = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
            
            document.getElementById('cartCount').textContent = count;
            document.getElementById('cartTotal').textContent = total.toLocaleString('id-ID');
            
            const cartBar = document.getElementById('cartBar');
            if (count > 0) {
                cartBar.classList.add('show');
            } else {
                cartBar.classList.remove('show');
            }
        }

        // View cart
        function viewCart() {
            const modalBody = document.getElementById('cartModalBody');
            
            if (cart.length === 0) {
                modalBody.innerHTML = '<p class="text-center">Cart is empty</p>';
            } else {
                let html = '';
                cart.forEach((item, index) => {
                    html += `
                        <div class="cart-item">
                            <div class="cart-item-name">${item.name}</div>
                            ${item.modifiers && item.modifiers.length > 0 ? `
                                <div style="font-size:10px;color:#888;margin:4px 0;">
                                    ${item.modifiers.map(m => m.name).join(', ')}
                                </div>
                            ` : ''}
                            <div class="cart-item-price">Rp ${item.price.toLocaleString('id-ID')}</div>
                            <div class="qty-control">
                                <button class="qty-btn" onclick="updateQty(${index}, -1)">-</button>
                                <span class="qty-display">${item.quantity}</span>
                                <button class="qty-btn" onclick="updateQty(${index}, 1)">+</button>
                            </div>
                        </div>
                    `;
                });
                modalBody.innerHTML = html;
            }
            
            new bootstrap.Modal(document.getElementById('cartModal')).show();
        }

        // Update quantity
        function updateQty(index, delta) {
            cart[index].quantity += delta;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            updateCartBar();
            viewCart();
        }

        // Submit order
        function submitOrder() {
            if (cart.length === 0) {
                alert('Cart is empty');
                return;
            }
            
            const total = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
            if (!confirm(`Submit order?\n\nTotal: Rp ${total.toLocaleString('id-ID')}`)) {
                return;
            }
            
            fetch('submit-order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    table_id: <?php echo $tableId; ?>,
                    items: cart
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Order submitted successfully!');
                    cart = [];
                    updateCartBar();
                    bootstrap.Modal.getInstance(document.getElementById('cartModal')).hide();
                } else {
                    alert('Error: ' + (data.error || data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                alert('Error submitting order');
                console.error(err);
            });
        }
    </script>
</body>
</html>
