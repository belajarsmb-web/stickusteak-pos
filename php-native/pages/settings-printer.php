<?php
/**
 * Stickusteak POS - Printer Settings
 * Configure thermal printer settings
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
    <title>Printer Settings - Stickusteak POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .sidebar .nav-link i { margin-right: 10px; }
        .sidebar-brand {
            padding: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .main-content { padding: 30px; }
        .content-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .logout-btn {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-radius: 8px;
            margin: 4px 10px;
            transition: all 0.3s;
        }
        .logout-btn:hover { background: rgba(220,53,69,0.3); color: white; }
        
        .printer-preview {
            border: 2px solid #333;
            padding: 15px;
            background: white;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            max-width: 300px;
            margin: 0 auto;
        }
        
        .printer-preview-58 {
            max-width: 220px;
        }
        
        .printer-preview-80 {
            max-width: 300px;
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
                    <a href="/php-native/pages/menu.php" class="nav-link">
                        <i class="bi bi-menu-button-wide"></i>Menu
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
                    <a href="/php-native/pages/settings.php" class="nav-link active">
                        <i class="bi bi-gear"></i>Settings
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
                <h2 class="mb-4"><i class="bi bi-printer me-2"></i>Printer Settings</h2>

                <div class="row">
                    <div class="col-md-6">
                        <div class="content-card mb-4">
                            <h5 class="mb-3"><i class="bi bi-sliders me-2"></i>Printer Configuration</h5>
                            <form id="printerSettingsForm">
                                <div class="mb-3">
                                    <label class="form-label">Paper Size</label>
                                    <select class="form-select" id="paperSize">
                                        <option value="58">58mm (Small Thermal)</option>
                                        <option value="80" selected>80mm (Standard Thermal)</option>
                                    </select>
                                    <small class="text-muted">Select your thermal printer paper width</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Restaurant Name</label>
                                    <input type="text" class="form-control" id="restaurantName" value="Stickusteak POS">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Restaurant Address</label>
                                    <textarea class="form-control" id="restaurantAddress" rows="2">Jl. Example No. 123, Jakarta</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="restaurantPhone" value="+62 21 1234567">
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="autoPrint" checked>
                                    <label class="form-check-label">Auto-print receipt after payment</label>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="showNotes" checked>
                                    <label class="form-check-label">Show item notes on receipt</label>
                                </div>
                                
                                <button type="button" class="btn btn-primary" onclick="savePrinterSettings()">
                                    <i class="bi bi-save me-1"></i>Save Settings
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="printTestReceipt()">
                                    <i class="bi bi-printer me-1"></i>Test Print
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="content-card">
                            <h5 class="mb-3"><i class="bi bi-eye me-2"></i>Receipt Preview</h5>
                            <div id="printerPreview" class="printer-preview printer-preview-80">
                                <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
                                    <strong style="font-size: 16px;">Stickusteak POS</strong><br>
                                    <span style="font-size: 11px;">Jl. Example No. 123, Jakarta</span><br>
                                    <span style="font-size: 11px;">Telp: +62 21 1234567</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 11px;">
                                    <div>
                                        <strong>Order #:</strong> 123<br>
                                        <strong>Table:</strong> Table 1<br>
                                        <strong>Date:</strong> 17/03/2026 20:00
                                    </div>
                                    <div style="text-align: right;">
                                        <strong>Cashier:</strong> Admin<br>
                                        <strong>Payment:</strong> Cash
                                    </div>
                                </div>
                                <div style="border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span>Black Angus Tenderloin</span>
                                        <span>x1</span>
                                        <span>Rp 285.000</span>
                                    </div>
                                    <div style="font-size: 10px; color: #666; background: #f9f9f9; padding: 3px; margin-top: 3px;">
                                        Note: Tanpa Garam, Pedas
                                    </div>
                                </div>
                                <div style="border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                                        <span>Subtotal:</span>
                                        <span>Rp 285.000</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: bold; border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;">
                                        <span>TOTAL:</span>
                                        <span>Rp 285.000</span>
                                    </div>
                                </div>
                                <div style="text-align: center; font-size: 11px; padding-top: 10px;">
                                    <p>Thank you for your visit!</p>
                                    <p>Powered by Stickusteak POS</p>
                                </div>
                            </div>
                            <p class="text-center text-muted mt-3 small">
                                <i class="bi bi-info-circle me-1"></i>
                                Preview shows approximate receipt appearance
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load current settings
        document.addEventListener('DOMContentLoaded', function() {
            // You can load settings from localStorage or API
            const settings = {
                paperSize: localStorage.getItem('printer_paperSize') || '80',
                restaurantName: localStorage.getItem('printer_restaurantName') || 'Stickusteak POS',
                restaurantAddress: localStorage.getItem('printer_restaurantAddress') || 'Jl. Example No. 123, Jakarta',
                restaurantPhone: localStorage.getItem('printer_restaurantPhone') || '+62 21 1234567',
                autoPrint: localStorage.getItem('printer_autoPrint') !== 'false',
                showNotes: localStorage.getItem('printer_showNotes') !== 'false'
            };
            
            document.getElementById('paperSize').value = settings.paperSize;
            document.getElementById('restaurantName').value = settings.restaurantName;
            document.getElementById('restaurantAddress').value = settings.restaurantAddress;
            document.getElementById('restaurantPhone').value = settings.restaurantPhone;
            document.getElementById('autoPrint').checked = settings.autoPrint;
            document.getElementById('showNotes').checked = settings.showNotes;
            
            updatePreview(settings);
        });
        
        // Update preview
        function updatePreview(settings) {
            const preview = document.getElementById('printerPreview');
            preview.className = 'printer-preview printer-preview-' + settings.paperSize;
            
            preview.querySelector('strong').textContent = settings.restaurantName;
            const lines = preview.querySelectorAll('span');
            lines[0].textContent = settings.restaurantAddress;
            lines[1].textContent = 'Telp: ' + settings.restaurantPhone;
        }
        
        // Save settings
        function savePrinterSettings() {
            const settings = {
                paperSize: document.getElementById('paperSize').value,
                restaurantName: document.getElementById('restaurantName').value,
                restaurantAddress: document.getElementById('restaurantAddress').value,
                restaurantPhone: document.getElementById('restaurantPhone').value,
                autoPrint: document.getElementById('autoPrint').checked,
                showNotes: document.getElementById('showNotes').checked
            };
            
            // Save to localStorage (or send to API)
            Object.keys(settings).forEach(key => {
                localStorage.setItem('printer_' + key, settings[key]);
            });
            
            updatePreview(settings);
            alert('Printer settings saved successfully!');
        }
        
        // Print test receipt
        function printTestReceipt() {
            const paperSize = document.getElementById('paperSize').value;
            window.open('/php-native/pages/receipt.php?order_id=1&size=' + paperSize + '&test=1', '_blank');
        }
    </script>
</body>
</html>
