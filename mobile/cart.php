<?php
/**
 * Stickusteak POS - Mobile Cart Page
 * Review cart and customize items
 */

$token = $_GET['token'] ?? '';
$tableId = $_GET['table'] ?? 0;
$page_title = "Cart";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cart - Stickusteak</title>
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
            padding: 20px;
            padding-bottom: 100px;
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }
        
        .cart-item {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.1);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .item-name {
            font-weight: 600;
            color: var(--gold-light);
            font-size: 1.1rem;
        }
        
        .item-price {
            font-weight: 700;
            color: var(--gold-primary);
        }
        
        .item-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .qty-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .qty-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(212,175,55,0.2);
            border: 1px solid rgba(212,175,55,0.3);
            color: var(--gold-primary);
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .qty-value {
            font-weight: 700;
            min-width: 30px;
            text-align: center;
        }
        
        .add-notes-btn {
            background: transparent;
            border: 1px solid rgba(212,175,55,0.3);
            color: rgba(255,255,255,0.6);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            cursor: pointer;
        }
        
        .summary-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        
        .summary-total {
            border-top: 1px solid rgba(212,175,55,0.2);
            padding-top: 15px;
            margin-top: 15px;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gold-primary);
        }
        
        .checkout-btn {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary), var(--gold-light));
            border: none;
            color: var(--black-primary);
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 25px;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212,175,55,0.4);
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255,255,255,0.4);
        }
        
        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Animated Notice Banner */
        .notice-banner {
            background: linear-gradient(135deg, rgba(212,175,55,0.15) 0%, rgba(170,140,44,0.1) 100%);
            border: 1px solid rgba(212,175,55,0.3);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: noticePulse 2s ease-in-out infinite;
        }

        @keyframes noticePulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(212,175,55,0.4);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(212,175,55,0);
                transform: scale(1.02);
            }
        }

        .notice-icon {
            font-size: 1.5rem;
            animation: iconBounce 1s ease-in-out infinite;
        }

        @keyframes iconBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .notice-text {
            flex: 1;
            color: var(--gold-light);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .notice-text strong {
            color: var(--gold-primary);
            font-weight: 700;
        }

        .notice-dismiss {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.5);
            cursor: pointer;
            padding: 5px;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .notice-dismiss:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <h1 class="page-title"><i class="bi bi-basket me-2"></i>Your Cart</h1>

    <!-- Animated Notice Banner -->
    <div class="notice-banner" id="noticeBanner" style="display: flex;">
        <div class="notice-icon">🥩</div>
        <div class="notice-text">
            <strong>Pastikan Sudah Tambahkan Catatan:</strong><br>
            Tingkat Kematangan, Saus dan Pilihan Kentang untuk menu Steak
        </div>
        <button class="notice-dismiss" onclick="dismissNotice()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div id="cartContent"></div>

    <div id="summarySection" style="display: none;">
        <div class="summary-card">
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotal">Rp 0</span>
            </div>
            <div class="summary-row">
                <span>Tax (10%)</span>
                <span id="tax">Rp 0</span>
            </div>
            <div class="summary-row">
                <span>Service Charge (5%)</span>
                <span id="service">Rp 0</span>
            </div>
            <div class="summary-row summary-total">
                <span>TOTAL</span>
                <span id="total">Rp 0</span>
            </div>
            <button class="checkout-btn" onclick="goToCheckout()">
                <i class="bi bi-receipt me-2"></i>Proceed to Checkout
            </button>
        </div>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('mobileCart') || '[]');
        let tableId = <?php echo (int)$tableId; ?>;
        let token = '<?php echo htmlspecialchars($token); ?>';

        document.addEventListener('DOMContentLoaded', function() {
            renderCart();
            // Show banner always for now
            const banner = document.getElementById('noticeBanner');
            if (banner) {
                banner.style.display = 'flex';
            }
        });

        // Dismiss notice banner
        function dismissNotice() {
            const banner = document.getElementById('noticeBanner');
            if (banner) {
                banner.style.display = 'none';
            }
        }

        function renderCart() {
            const container = document.getElementById('cartContent');
            
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart">
                        <i class="bi bi-basket"></i>
                        <h3>Your cart is empty</h3>
                        <p style="color: rgba(255,255,255,0.5);">Add items from the menu to start your order</p>
                        <button class="btn btn-outline-light mt-3" onclick="goToMenu()">
                            <i class="bi bi-arrow-left me-1"></i>Back to Menu
                        </button>
                    </div>
                `;
                document.getElementById('summarySection').style.display = 'none';
                return;
            }
            
            let html = '';
            cart.forEach((item, index) => {
                html += `
                    <div class="cart-item">
                        <div class="item-header">
                            <div>
                                <div class="item-name">${item.name}</div>
                                ${item.modifiers && item.modifiers.length > 0 ? `
                                    <div style="margin-top: 5px; font-size: 0.85rem; color: rgba(255,255,255,0.6);">
                                        ${item.modifiers.map(mod => `<div>+ ${mod.name}</div>`).join('')}
                                    </div>
                                ` : ''}
                            </div>
                            <div class="item-price">Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</div>
                        </div>
                        <div class="item-controls">
                            <div class="qty-control">
                                <button class="qty-btn" onclick="updateQty(${index}, -1)">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <span class="qty-value">${item.quantity}</span>
                                <button class="qty-btn" onclick="updateQty(${index}, 1)">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <button class="add-notes-btn" onclick="addNotes(${index})">
                                <i class="bi bi-chat-left-text me-1"></i>${item.notes && item.notes.length > 0 ? 'Edit Notes' : 'Add Notes'}
                            </button>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            updateSummary();
            document.getElementById('summarySection').style.display = 'block';
        }

        function updateQty(index, change) {
            cart[index].quantity += change;
            
            if (cart[index].quantity <= 0) {
                if (confirm('Remove this item from cart?')) {
                    cart.splice(index, 1);
                } else {
                    cart[index].quantity = 1;
                }
            }
            
            saveCart();
            renderCart();
        }

        function addNotes(index) {
            const item = cart[index];
            const currentNotes = item.notes ? item.notes.join(', ') : '';
            const newNotes = prompt('Add special instructions (e.g., No salt, Extra spicy):', currentNotes);
            
            if (newNotes !== null) {
                item.notes = newNotes.split(',').map(n => n.trim()).filter(n => n);
                saveCart();
                renderCart();
            }
        }

        function updateSummary() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const tax = subtotal * 0.10;
            const service = subtotal * 0.05;
            const total = subtotal + tax + service;
            
            document.getElementById('subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('tax').textContent = 'Rp ' + tax.toLocaleString('id-ID');
            document.getElementById('service').textContent = 'Rp ' + service.toLocaleString('id-ID');
            document.getElementById('total').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        function saveCart() {
            localStorage.setItem('mobileCart', JSON.stringify(cart));
        }

        function goToMenu() {
            window.location.href = '/php-native/mobile/menu.php?token=' + token + '&table=' + tableId;
        }

        function goToCheckout() {
            window.location.href = '/php-native/mobile/checkout.php?token=' + token + '&table=' + tableId;
        }
    </script>
</body>
</html>
