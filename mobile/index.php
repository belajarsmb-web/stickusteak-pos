<?php
/**
 * Stickusteak POS - Mobile Order Landing Page
 * Customer scans QR code and lands here
 */

$token = $_GET['token'] ?? '';
$page_title = "Mobile Order";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Stickusteak - Mobile Order</title>
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
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 100%);
            color: #fff;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
        }
        
        .hero-section {
            padding: 60px 20px 40px;
            text-align: center;
            background: linear-gradient(135deg, rgba(212,175,55,0.1) 0%, transparent 100%);
        }
        
        .logo-container {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary), var(--gold-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--black-primary);
            box-shadow: 0 10px 30px rgba(212,175,55,0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .hero-title {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }
        
        .hero-subtitle {
            font-size: 1rem;
            color: rgba(255,255,255,0.6);
            margin-bottom: 30px;
        }
        
        .table-info-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 15px;
            padding: 25px;
            margin: 0 20px 30px;
            text-align: center;
        }
        
        .table-number {
            font-size: 2.5rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            color: var(--gold-primary);
            margin-bottom: 10px;
        }
        
        .btn-primary-gold {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary), var(--gold-light));
            border: none;
            color: var(--black-primary);
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 25px;
            transition: all 0.3s;
            width: 100%;
            max-width: 300px;
        }
        
        .btn-primary-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212,175,55,0.4);
        }
        
        .features-section {
            padding: 40px 20px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255,255,255,0.03);
            border-radius: 10px;
            border: 1px solid rgba(212,175,55,0.1);
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            background: rgba(212,175,55,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--gold-primary);
            margin-right: 15px;
        }
        
        .feature-text {
            flex: 1;
        }
        
        .feature-title {
            font-weight: 600;
            color: var(--gold-light);
            margin-bottom: 3px;
        }
        
        .feature-desc {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
        }
        
        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 3px solid rgba(212,175,55,0.2);
            border-top-color: var(--gold-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .error-message {
            background: rgba(220,53,69,0.2);
            border: 1px solid #dc3545;
            color: #ff6b6b;
            padding: 15px;
            border-radius: 10px;
            margin: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="logo-container">
            <i class="bi bi-reception-4"></i>
        </div>
        <h1 class="hero-title">Stickusteak POS</h1>
        <p class="hero-subtitle">Premium Mobile Ordering</p>
    </div>

    <!-- Content -->
    <div id="loadingSection" style="text-align: center; padding: 40px 20px;">
        <div class="loading-spinner"></div>
        <p style="margin-top: 20px; color: rgba(255,255,255,0.6);">Validating QR code...</p>
    </div>

    <div id="errorSection" class="error-message" style="display: none;">
        <i class="bi bi-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
        <h4 style="margin-bottom: 10px;">Invalid QR Code</h4>
        <p style="color: rgba(255,255,255,0.7);">This QR code is invalid or has expired. Please scan the QR code on your table.</p>
    </div>

    <div id="contentSection" style="display: none;">
        <!-- Table Info -->
        <div class="table-info-card">
            <div style="color: rgba(255,255,255,0.6); font-size: 0.9rem; margin-bottom: 5px;">Your Table</div>
            <div class="table-number" id="tableNumber">-</div>
            <div style="color: rgba(255,255,255,0.5);">Scan QR to order</div>
        </div>

        <!-- Start Order Button -->
        <div style="text-align: center; padding: 0 20px 40px;">
            <button class="btn btn-primary-gold" onclick="startOrder()">
                <i class="bi bi-basket me-2"></i>Start Order
            </button>
        </div>

        <!-- Features -->
        <div class="features-section">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-menu-button-wide"></i>
                </div>
                <div class="feature-text">
                    <div class="feature-title">Browse Menu</div>
                    <div class="feature-desc">Explore our premium selection</div>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-ui-checks-grid"></i>
                </div>
                <div class="feature-text">
                    <div class="feature-title">Customize Order</div>
                    <div class="feature-desc">Add modifiers and special requests</div>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="feature-text">
                    <div class="feature-title">Track Status</div>
                    <div class="feature-desc">Real-time order updates</div>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="feature-text">
                    <div class="feature-title">Easy Payment</div>
                    <div class="feature-desc">Pay at table or counter</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let tableData = null;
        let orderToken = '';

        // Validate QR on page load
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            orderToken = urlParams.get('token');
            
            if (!orderToken) {
                showError('Please scan the QR code on your table');
                return;
            }
            
            validateQR(orderToken);
        });

        // Validate QR token
        async function validateQR(token) {
            try {
                const response = await fetch('/php-native/api/mobile/validate-qr.php?token=' + token);
                const data = await response.json();
                
                document.getElementById('loadingSection').style.display = 'none';
                
                if (data.success) {
                    tableData = data.table;
                    document.getElementById('tableNumber').textContent = data.table.name || 'Table ' + data.table.id;
                    document.getElementById('contentSection').style.display = 'block';
                } else {
                    showError(data.message || 'Invalid QR code');
                }
            } catch (error) {
                console.error('Validation error:', error);
                document.getElementById('loadingSection').style.display = 'none';
                showError('Connection error. Please check your internet connection.');
            }
        }

        // Show error
        function showError(message) {
            document.getElementById('errorSection').style.display = 'block';
            document.getElementById('errorSection').querySelector('p').textContent = message;
        }

        // Start order
        function startOrder() {
            if (!tableData) return;
            
            // Navigate to menu page with table info
            window.location.href = '/php-native/mobile/menu.php?token=' + orderToken + '&table=' + tableData.id;
        }
    </script>
</body>
</html>
