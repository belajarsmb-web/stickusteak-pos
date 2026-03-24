<?php
/**
 * Mobile POS - Premium Order Page
 * Black & Gold Theme with Modern Animations
 */

require_once __DIR__ . '/../config/database.php';

$tableId = $_GET['table_id'] ?? 0;
$pdo = getDbConnection();

// Get table info
$table = $pdo->prepare("SELECT * FROM tables WHERE id = ? AND is_active = 1");
$table->execute([$tableId]);
$tableInfo = $table->fetch();

if (!$tableInfo) {
    die('<div class="error">Invalid table</div>');
}

// Get categories
$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();

// Get modifier groups
$modifierGroups = $pdo->query("SELECT g.id, g.name, g.min_selection, g.max_selection, g.is_required,
    (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', m.id, 'name', m.name, 'price', m.price)) 
     FROM modifiers m WHERE m.modifier_group_id = g.id AND m.is_active = 1) as modifiers
    FROM modifier_groups g 
    WHERE g.is_active = 1 
    ORDER BY g.id")->fetchAll();

// Get menu items
$menuItems = $pdo->query("
    SELECT m.*, c.name as category_name
    FROM menu_items m
    LEFT JOIN categories c ON m.category_id = c.id
    WHERE m.is_active = 1 AND m.is_available = 1
    ORDER BY c.sort_order, m.sort_order
")->fetchAll();

// Find steak category
$steakCategoryId = 'null';
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
    <title>Mobile Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 100%); color: #fff; padding-bottom: 90px; }
        .header { background: linear-gradient(135deg, var(--black-tertiary), var(--black-primary)); border-bottom: 2px solid var(--gold-dark); padding: 20px; position: sticky; top: 0; z-index: 1000; box-shadow: 0 5px 30px rgba(212, 175, 55, 0.3); }
        .header h5 { font-size: 1.3rem; font-weight: 700; background: var(--gold-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 5px; }
        .header small { color: rgba(212, 175, 55, 0.9); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; }
        .category-nav { display: flex; gap: 10px; overflow-x: auto; padding: 15px 20px; background: var(--black-tertiary); position: sticky; top: 85px; z-index: 999; border-bottom: 1px solid rgba(212, 175, 55, 0.3); }
        .category-nav button { flex-shrink: 0; padding: 10px 20px; border-radius: 25px; border: 1px solid var(--gold-dark); background: transparent; color: rgba(255,255,255,0.7); font-weight: 600; transition: all 0.3s; }
        .category-nav button.active { background: var(--gold-gradient); color: var(--black-primary); border-color: var(--gold-light); }
        .menu-grid { display: grid; grid-template-columns: 1fr; gap: 20px; padding: 20px; }
        @media (min-width: 768px) { .menu-grid { grid-template-columns: repeat(2, 1fr); max-width: 800px; margin: 0 auto; } }
        .menu-card { background: linear-gradient(135deg, var(--black-tertiary), var(--black-secondary)); border-radius: 15px; overflow: hidden; border: 1px solid rgba(212, 175, 55, 0.2); box-shadow: 0 5px 20px rgba(0,0,0,0.4); transition: all 0.4s; }
        .menu-card:hover { transform: translateY(-5px); border-color: var(--gold-primary); box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4); }
        .menu-card img { width: 100%; height: 180px; object-fit: cover; background: #222; transition: transform 0.4s; }
        .menu-card:hover img { transform: scale(1.05); }
        .menu-card-body { padding: 15px; }
        .menu-card h6 { font-size: 1.1rem; font-weight: 700; color: var(--gold-light); margin-bottom: 8px; }
        .menu-card p { font-size: 0.8rem; color: rgba(255,255,255,0.6); margin-bottom: 12px; }
        .menu-card .price { font-size: 1.3rem; font-weight: 700; background: var(--gold-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 12px; display: block; }
        .btn-add { width: 100%; padding: 12px; background: var(--gold-gradient); border: none; border-radius: 10px; color: var(--black-primary); font-weight: 700; transition: all 0.3s; }
        .btn-add:hover { transform: scale(1.05); }
        .cart-bar { position: fixed; bottom: 0; left: 0; right: 0; background: linear-gradient(135deg, var(--black-tertiary), var(--black-primary)); border-top: 2px solid var(--gold-dark); padding: 15px 20px; box-shadow: 0 -5px 30px rgba(212, 175, 55, 0.3); display: none; z-index: 1000; }
        .cart-bar.show { display: block; }
        .cart-info small { display: block; font-size: 0.75rem; color: rgba(212, 175, 55, 0.8); margin-bottom: 5px; text-transform: uppercase; }
        .cart-info .total { font-size: 1.5rem; font-weight: 700; background: var(--gold-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-view { width: 100%; padding: 14px; background: var(--gold-gradient); border: none; border-radius: 12px; color: var(--black-primary); font-weight: 700; margin-top: 10px; }
        .modal-content { background: linear-gradient(135deg, var(--black-tertiary), var(--black-primary)); border: 2px solid var(--gold-dark); color: #fff; }
        .modal-header { border-bottom: 1px solid rgba(212, 175, 55, 0.3); }
        .modal-title { background: var(--gold-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .modal-body { padding: 25px; }
        .modal-footer { border-top: 1px solid rgba(212, 175, 55, 0.3); }
        .modifier-section { margin-bottom: 20px; padding: 15px; background: rgba(212, 175, 55, 0.05); border-radius: 12px; border: 1px solid rgba(212, 175, 55, 0.2); }
        .modifier-section h6 { color: var(--gold-light); font-size: 0.95rem; font-weight: 700; margin-bottom: 12px; }
        .modifier-option { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(212, 175, 55, 0.1); }
        .modifier-option label { flex: 1; font-size: 0.85rem; color: #fff; cursor: pointer; }
        .modifier-price { color: var(--gold-light); font-size: 0.8rem; font-weight: 700; }
        .cart-item { background: rgba(212, 175, 55, 0.05); border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 12px; padding: 15px; margin-bottom: 12px; }
        .cart-item-name { font-size: 0.95rem; font-weight: 700; color: var(--gold-light); margin-bottom: 5px; }
        .cart-item-price { font-size: 0.85rem; color: rgba(255,255,255,0.6); }
        .cart-item-notes { font-size: 0.75rem; color: rgba(212, 175, 55, 0.8); font-style: italic; margin-top: 5px; padding: 5px; background: rgba(212, 175, 55, 0.1); border-radius: 5px; border-left: 2px solid var(--gold-primary); }
        .qty-control { display: flex; align-items: center; gap: 12px; margin-top: 10px; }
        .qty-btn { width: 35px; height: 35px; border-radius: 50%; border: 1px solid var(--gold-dark); background: transparent; color: var(--gold-light); font-size: 1.2rem; }
        .qty-display { font-weight: 700; min-width: 35px; text-align: center; color: var(--gold-light); }
        .btn-notes { width: 100%; padding: 8px; background: rgba(212, 175, 55, 0.2); border: 1px solid var(--gold-dark); border-radius: 8px; color: var(--gold-light); font-size: 0.8rem; font-weight: 600; margin-top: 8px; transition: all 0.3s; }
        .btn-notes:hover { background: rgba(212, 175, 55, 0.3); }
        .btn-notes.has-notes { background: rgba(212, 175, 55, 0.4); border-color: var(--gold-light); }
        .error { background: rgba(220, 53, 69, 0.2); border: 1px solid #dc3545; color: #ff6b6b; padding: 20px; border-radius: 10px; text-align: center; margin: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h5><?php echo htmlspecialchars($tableInfo['name']); ?></h5>
        <small>Table <?php echo htmlspecialchars($tableInfo['table_number']); ?></small>
    </div>

    <div class="category-nav">
        <button class="active" onclick="filterCategory('all', this)">All</button>
        <?php foreach ($categories as $cat): ?>
            <button onclick="filterCategory('<?php echo $cat['id']; ?>', this)"><?php echo htmlspecialchars($cat['name']); ?></button>
        <?php endforeach; ?>
    </div>

    <div class="menu-grid">
        <?php foreach ($menuItems as $item): ?>
            <div class="menu-card" data-category="<?php echo $item['category_id']; ?>">
                <?php if ($item['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <?php else: ?>
                    <div style="width:100%;height:180px;background:#222;display:flex;align-items:center;justify-content:center;"><i class="bi bi-camera" style="font-size:3rem;color:#444;"></i></div>
                <?php endif; ?>
                <div class="menu-card-body">
                    <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                    <?php if ($item['description']): ?>
                        <p><?php echo htmlspecialchars(substr($item['description'], 0, 80)); ?>...</p>
                    <?php endif; ?>
                    <span class="price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></span>
                    <button class="btn-add" onclick="showModifiers(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>', <?php echo $item['price']; ?>, <?php echo $item['category_id']; ?>)">Add to Cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="cart-bar" id="cartBar">
        <div class="cart-info">
            <small><span id="cartCount">0</span> items</small>
            <div class="total">Rp <span id="cartTotal">0</span></div>
        </div>
        <button class="btn-view" onclick="viewCart()">View Cart</button>
    </div>

    <div class="modal fade" id="modifiersModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customize Your Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modifiersBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addToCartWithModifiers()">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Your Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cartModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="showCustomerForm()">Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Info Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="customerName" placeholder="Your name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="customerPhone" placeholder="08123456789">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-primary" onclick="submitOrderWithCustomer()">Submit Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Modal -->
    <div class="modal fade" id="notesModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Item Notes / Special Requests</label>
                        <textarea class="form-control" id="itemNotes" rows="4" placeholder="e.g., No ice, Medium rare, Less sugar, etc."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quick Notes</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="addQuickNote('No Ice')">No Ice</button>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="addQuickNote('Less Sugar')">Less Sugar</button>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="addQuickNote('Medium Rare')">Medium Rare</button>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="addQuickNote('Well Done')">Well Done</button>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="addQuickNote('Extra Spicy')">Extra Spicy</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="clearNotes()">Clear Notes</button>
                    <button type="button" class="btn btn-primary" onclick="saveNotes()">Save Notes</button>
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
        let currentNoteIndex = null;
        const steakCategoryId = <?php echo $steakCategoryId; ?>;
        const modifierGroups = <?php echo json_encode($modifierGroups); ?>;

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

        function showModifiers(id, name, price, categoryId) {
            if (categoryId == steakCategoryId) {
                currentItem = { id, name, basePrice: price, categoryId, modifiers: {}, finalPrice: price };
                selectedModifiers = {};
                let html = '';
                if (modifierGroups && modifierGroups.length > 0) {
                    modifierGroups.forEach(function(group) {
                        const modifiers = group.modifiers ? JSON.parse(group.modifiers) : [];
                        if (modifiers.length > 0) {
                            html += '<div class="modifier-section"><h6>' + group.name + '</h6>';
                            if (group.max_selection == 1) {
                                modifiers.forEach(function(mod) {
                                    html += '<div class="modifier-option"><label><input type="radio" name="modifier_' + group.id + '" value="' + mod.id + '" onchange="selectModifier(\'' + group.id + '\', \'' + mod.name.replace(/'/g, "\\'") + '\', ' + mod.price + ')">' + mod.name + '</label>';
                                    if (mod.price > 0) html += '<span class="modifier-price">+Rp ' + mod.price.toLocaleString('id-ID') + '</span>';
                                    html += '</div>';
                                });
                            } else {
                                modifiers.forEach(function(mod) {
                                    html += '<div class="modifier-option"><label><input type="checkbox" value="' + mod.id + '" onchange="selectModifier(\'' + group.id + '\', \'' + mod.name.replace(/'/g, "\\'") + '\', ' + mod.price + ', this.checked)">' + mod.name + '</label>';
                                    if (mod.price > 0) html += '<span class="modifier-price">+Rp ' + mod.price.toLocaleString('id-ID') + '</span>';
                                    html += '</div>';
                                });
                            }
                            html += '</div>';
                        }
                    });
                }
                document.getElementById('modifiersBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modifiersModal')).show();
            } else {
                addToCart(id, name, price, [], price);
            }
        }

        function selectModifier(groupId, name, price, checked) {
            if (checked === undefined) checked = true;
            if (!selectedModifiers[groupId]) selectedModifiers[groupId] = [];
            const group = modifierGroups.find(function(g) { return g.id == groupId; });
            if (group && group.max_selection == 1) {
                selectedModifiers[groupId] = [{ name, price }];
            } else {
                if (checked) {
                    selectedModifiers[groupId].push({ name, price });
                } else {
                    selectedModifiers[groupId] = selectedModifiers[groupId].filter(function(m) { return m.name !== name; });
                }
            }
            let modifierTotal = 0;
            Object.values(selectedModifiers).flat().forEach(function(m) { modifierTotal += m.price; });
            currentItem.finalPrice = currentItem.basePrice + modifierTotal;
        }

        function addToCartWithModifiers() {
            const allModifiers = Object.values(selectedModifiers).flat();
            addToCart(currentItem.id, currentItem.name, currentItem.finalPrice, allModifiers, currentItem.finalPrice);
            bootstrap.Modal.getInstance(document.getElementById('modifiersModal')).hide();
        }

        function addToCart(id, name, price, modifiers, finalPrice) {
            const existing = cart.find(function(i) { return i.id === id && JSON.stringify(i.modifiers) === JSON.stringify(modifiers); });
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({ id, name, price: finalPrice, basePrice: finalPrice, quantity: 1, modifiers });
            }
            updateCartBar();
        }

        function updateCartBar() {
            const count = cart.reduce(function(sum, i) { return sum + i.quantity; }, 0);
            const total = cart.reduce(function(sum, i) { return sum + (i.price * i.quantity); }, 0);
            document.getElementById('cartCount').textContent = count;
            document.getElementById('cartTotal').textContent = total.toLocaleString('id-ID');
            const cartBar = document.getElementById('cartBar');
            if (count > 0) { cartBar.classList.add('show'); } else { cartBar.classList.remove('show'); }
        }

        function viewCart() {
            const modalBody = document.getElementById('cartModalBody');
            if (cart.length === 0) {
                modalBody.innerHTML = '<p>Cart is empty</p>';
            } else {
                let html = '';
                cart.forEach(function(item, index) {
                    html += '<div class="cart-item"><div class="cart-item-name">' + item.name + '</div>';
                    if (item.modifiers && item.modifiers.length > 0) {
                        html += '<div style="font-size:12px;color:#888;margin:5px 0;">' + item.modifiers.map(function(m) { return m.name; }).join(', ') + '</div>';
                    }
                    html += '<div class="cart-item-price">Rp ' + item.price.toLocaleString('id-ID') + '</div>';
                    if (item.notes && item.notes.trim() !== '') {
                        html += '<div class="cart-item-notes"><i class="bi bi-chat-left-text"></i> ' + escapeHtml(item.notes) + '</div>';
                    }
                    html += '<button class="btn-notes ' + (item.notes && item.notes.trim() !== '' ? 'has-notes' : '') + '" onclick="openNotesModal(' + index + ')"><i class="bi bi-pencil"></i> ' + (item.notes && item.notes.trim() !== '' ? 'Edit Notes' : 'Add Notes') + '</button>';
                    html += '<div class="qty-control"><button class="qty-btn" onclick="updateQty(' + index + ', -1)">-</button><span class="qty-display">' + item.quantity + '</span><button class="qty-btn" onclick="updateQty(' + index + ', 1)">+</button></div></div>';
                });
                modalBody.innerHTML = html;
            }
            new bootstrap.Modal(document.getElementById('cartModal')).show();
        }

        function showCustomerForm() {
            bootstrap.Modal.getInstance(document.getElementById('cartModal')).hide();
            document.getElementById('customerName').value = '';
            document.getElementById('customerPhone').value = '';
            new bootstrap.Modal(document.getElementById('customerModal')).show();
        }

        function submitOrderWithCustomer() {
            const name = document.getElementById('customerName').value.trim();
            const phone = document.getElementById('customerPhone').value.trim();
            
            if (!name || !phone) {
                alert('Please fill in your name and phone number');
                return;
            }
            
            if (cart.length === 0) {
                alert('Cart is empty');
                return;
            }
            
            const total = cart.reduce(function(sum, i) { return sum + (i.price * i.quantity); }, 0);
            if (!confirm('Submit order?\n\nName: ' + name + '\nPhone: ' + phone + '\n\nTotal: Rp ' + total.toLocaleString('id-ID'))) return;
            
            fetch('submit-order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    table_id: <?php echo $tableId; ?>,
                    items: cart,
                    customer_name: name,
                    customer_phone: phone
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    alert('Order submitted to kitchen!');
                    cart = [];
                    updateCartBar();
                    bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
                    // Stay on order page, don't redirect to receipt
                    // Receipt only after payment, not after order submission
                } else {
                    alert('Error: ' + (data.error || data.message || 'Unknown error'));
                }
            })
            .catch(function(err) {
                alert('Error submitting order');
                console.error(err);
            });
        }

        function updateQty(index, delta) {
            cart[index].quantity += delta;
            if (cart[index].quantity <= 0) cart.splice(index, 1);
            updateCartBar();
            viewCart();
        }

        function openNotesModal(index) {
            currentNoteIndex = index;
            document.getElementById('itemNotes').value = cart[index].notes || '';
            new bootstrap.Modal(document.getElementById('notesModal')).show();
        }

        function addQuickNote(note) {
            const textarea = document.getElementById('itemNotes');
            const current = textarea.value.trim();
            if (current === '') {
                textarea.value = note;
            } else if (current.indexOf(note) === -1) {
                textarea.value = current + ', ' + note;
            }
        }

        function saveNotes() {
            if (currentNoteIndex !== null) {
                const notes = document.getElementById('itemNotes').value.trim();
                cart[currentNoteIndex].notes = notes;
                viewCart();
                bootstrap.Modal.getInstance(document.getElementById('notesModal')).hide();
            }
        }

        function clearNotes() {
            if (currentNoteIndex !== null) {
                cart[currentNoteIndex].notes = '';
                document.getElementById('itemNotes').value = '';
                viewCart();
                bootstrap.Modal.getInstance(document.getElementById('notesModal')).hide();
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
