<?php
/**
 * Mobile POS - Clean Order Page
 * Optimized for mobile with no overlapping
 */

require_once __DIR__ . '/../config/database.php';

$tableId = $_GET['table_id'] ?? 0;
$pdo = getDbConnection();

// Get table info
$table = $pdo->prepare("SELECT * FROM tables WHERE id = ? AND is_active = 1");
$table->execute([$tableId]);
$tableInfo = $table->fetch();

if (!$tableInfo) {
    die('<div class="alert alert-danger">Invalid table</div>');
}

// Get menu categories
$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();

// Get modifier groups
$modifierGroups = $pdo->query("SELECT * FROM modifier_groups WHERE is_active = 1 ORDER BY sort_order")->fetchAll();

// Get menu items
$menuItems = $pdo->query("
    SELECT m.*, c.name as category_name
    FROM menu_items m
    LEFT JOIN categories c ON m.category_id = c.id
    WHERE m.is_active = 1 AND m.is_available = 1
    ORDER BY c.sort_order, m.sort_order
")->fetchAll();

// Find steak category ID
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mobile Order - <?php echo htmlspecialchars($tableInfo['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="mobile-order-v2.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h5>🍽️ <?php echo htmlspecialchars($tableInfo['name']); ?></h5>
        <small>Table <?php echo htmlspecialchars($tableInfo['table_number']); ?></small>
    </div>

    <!-- Category Navigation -->
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
                    <button class="btn-add" onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>', <?php echo $item['price']; ?>)">
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

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🛒 Your Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cartModalBody">
                    <!-- Cart items rendered here -->
                </div>
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

        // Filter category
        function filterCategory(catId, btn) {
            currentCategory = catId;
            document.querySelectorAll('.category-nav button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            document.querySelectorAll('.menu-card').forEach(card => {
                if (catId === 'all' || card.dataset.category === catId) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Add to cart
        function addToCart(id, name, price) {
            const existing = cart.find(i => i.id === id);
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({ id, name, price, quantity: 1 });
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
            
            // Submit to server
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
