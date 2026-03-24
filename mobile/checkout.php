<?php
/**
 * Stickusteak POS - Mobile Checkout Page
 * Enter customer info and submit order
 */

$token = $_GET['token'] ?? '';
$tableId = $_GET['table'] ?? 0;
$page_title = "Checkout";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkout - Stickusteak</title>
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
        
        .form-section {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-label {
            color: rgba(255,255,255,0.8);
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-control {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212,175,55,0.3);
            color: #fff;
            padding: 12px 15px;
            border-radius: 10px;
        }
        
        .form-control:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(212,175,55,0.2);
            color: #fff;
        }
        
        .order-summary {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(212,175,55,0.1);
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary), var(--gold-light));
            border: none;
            color: var(--black-primary);
            padding: 18px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 25px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212,175,55,0.4);
        }
        
        .submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h1 class="page-title"><i class="bi bi-receipt me-2"></i>Checkout</h1>

    <form id="checkoutForm">
        <!-- Customer Info -->
        <div class="form-section">
            <h4 style="color: var(--gold-light); margin-bottom: 15px;">
                <i class="bi bi-person me-2"></i>Your Information
            </h4>
            <div class="mb-3">
                <label class="form-label">Name *</label>
                <input type="text" class="form-control" id="customerName" required placeholder="Your name">
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number *</label>
                <input type="tel" class="form-control" id="customerPhone" required placeholder="08xx-xxxx-xxxx">
            </div>
        </div>

        <!-- Order Summary -->
        <div class="form-section">
            <h4 style="color: var(--gold-light); margin-bottom: 15px;">
                <i class="bi bi-basket me-2"></i>Order Summary
            </h4>
            <div id="orderSummary"></div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="submit-btn" id="submitBtn">
            <i class="bi bi-check-lg me-2"></i>Place Order
        </button>
    </form>

    <script>
        let cart = JSON.parse(localStorage.getItem('mobileCart') || '[]');
        let tableId = <?php echo (int)$tableId; ?>;
        let token = '<?php echo htmlspecialchars($token); ?>';

        document.addEventListener('DOMContentLoaded', function() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                window.location.href = '/php-native/mobile/menu.php?token=' + token + '&table=' + tableId;
                return;
            }
            renderSummary();
        });

        function renderSummary() {
            const container = document.getElementById('orderSummary');
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const tax = subtotal * 0.10;
            const service = subtotal * 0.05;
            const total = subtotal + tax + service;

            let html = '';
            cart.forEach((item, index) => {
                html += `
                    <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid rgba(212,175,55,0.1);">
                        <div class="summary-item" style="font-weight: 600; color: var(--gold-light);">
                            <span>${item.quantity}x ${item.name}</span>
                            <span>Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</span>
                        </div>
                        ${item.modifiers && item.modifiers.length > 0 ? `
                            <div style="margin-top: 8px; padding-left: 15px; border-left: 2px solid rgba(212,175,55,0.3);">
                                ${item.modifiers.map(mod => `
                                    <div style="font-size: 0.85rem; color: rgba(255,255,255,0.6); margin-bottom: 4px;">
                                        + ${mod.name}
                                        ${mod.price > 0 ? '<span style="color: var(--gold-primary);">(+Rp ' + parseFloat(mod.price).toLocaleString('id-ID') + ')</span>' : ''}
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                        ${item.notes && item.notes.length > 0 ? `
                            <div style="margin-top: 8px; font-size: 0.85rem; color: rgba(255,255,255,0.5); font-style: italic;">
                                <i class="bi bi-chat-left-text me-1"></i>${item.notes.join(', ')}
                            </div>
                        ` : ''}
                    </div>
                `;
            });

            html += `
                <div style="border-top: 1px solid rgba(212,175,55,0.2); margin: 15px 0; padding-top: 15px;">
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span>Rp ${subtotal.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="summary-item">
                        <span>Tax (10%)</span>
                        <span>Rp ${tax.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="summary-item">
                        <span>Service Charge (5%)</span>
                        <span>Rp ${service.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="summary-item" style="font-size: 1.2rem; font-weight: 700; color: var(--gold-primary); margin-top: 10px;">
                        <span>TOTAL</span>
                        <span>Rp ${total.toLocaleString('id-ID')}</span>
                    </div>
                </div>
            `;

            container.innerHTML = html;
        }

        // Submit order
        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const customerName = document.getElementById('customerName').value.trim();
            const customerPhone = document.getElementById('customerPhone').value.trim();

            if (!customerName || !customerPhone) {
                alert('Please fill in your name and phone number');
                return;
            }

            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';

            try {
                const response = await fetch('/php-native/api/mobile/place-order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        table_id: tableId,
                        customer_name: customerName,
                        customer_phone: customerPhone,
                        items: cart,
                        token: token
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Save order token to localStorage
                    localStorage.setItem('mobileOrderToken', data.mobile_token);
                    localStorage.setItem('mobileOrderId', data.order_id);
                    
                    // Clear cart
                    localStorage.removeItem('mobileCart');

                    // Redirect to order status
                    window.location.href = '/php-native/mobile/order-status.php?token=' + data.mobile_token;
                } else {
                    alert('Failed to place order: ' + (data.message || 'Please try again'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Place Order';
                }
            } catch (error) {
                console.error('Order error:', error);
                alert('Connection error. Please try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Place Order';
            }
        });
    </script>
</body>
</html>
