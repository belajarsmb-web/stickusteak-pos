<?php
/**
 * Stickusteak POS - Tax & Service Settings
 * Configure tax and service charge percentages
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax & Service Settings - Stickusteak POS</title>
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
        }
        .logout-btn:hover { background: rgba(220,53,69,0.3); color: white; }
        
        .preview-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .bill-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #dee2e6;
        }
        
        .bill-row.total {
            border-bottom: none;
            border-top: 2px solid #333;
            font-weight: bold;
            font-size: 1.2rem;
            padding-top: 15px;
            margin-top: 10px;
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
                <h2 class="mb-4"><i class="bi bi-percent me-2"></i>Tax & Service Charge Settings</h2>

                <div class="row">
                    <div class="col-md-6">
                        <div class="content-card">
                            <h5 class="mb-3"><i class="bi bi-sliders me-2"></i>Configuration</h5>
                            <form id="taxServiceForm">
                                <div class="mb-3">
                                    <label class="form-label">Tax Percentage (%)</label>
                                    <input type="number" class="form-control" id="taxPercentage" step="0.1" value="10">
                                    <small class="text-muted">Government tax (e.g., VAT, GST)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Service Charge Percentage (%)</label>
                                    <input type="number" class="form-control" id="serviceChargePercentage" step="0.1" value="5">
                                    <small class="text-muted">Service charge for staff</small>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="taxEnabled" checked>
                                    <label class="form-check-label">Enable Tax</label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="serviceEnabled" checked>
                                    <label class="form-check-label">Enable Service Charge</label>
                                </div>
                                
                                <button type="button" class="btn btn-primary" onclick="saveSettings()">
                                    <i class="bi bi-save me-1"></i>Save Settings
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="content-card">
                            <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>Bill Preview</h5>
                            <div class="preview-box">
                                <div class="bill-row">
                                    <span>Subtotal</span>
                                    <span id="previewSubtotal">Rp 100.000</span>
                                </div>
                                <div class="bill-row">
                                    <span>Service Charge (<span id="previewServiceRate">5</span>%)</span>
                                    <span id="previewService">Rp 5.000</span>
                                </div>
                                <div class="bill-row">
                                    <span>Tax (<span id="previewTaxRate">10</span>%)</span>
                                    <span id="previewTax">Rp 10.000</span>
                                </div>
                                <div class="bill-row total">
                                    <span>TOTAL</span>
                                    <span id="previewTotal">Rp 115.000</span>
                                </div>
                            </div>
                            <p class="text-muted small mt-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Service charge is calculated on subtotal. Tax is calculated on (subtotal + service charge).
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
        document.addEventListener('DOMContentLoaded', async function() {
            await loadSettings();
            updatePreview();
        });

        async function loadSettings() {
            try {
                const response = await fetch('/php-native/api/settings/tax-service.php');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('taxPercentage').value = data.settings.tax_percentage || 10;
                    document.getElementById('serviceChargePercentage').value = data.settings.service_charge_percentage || 5;
                    document.getElementById('taxEnabled').checked = data.settings.tax_enabled !== '0';
                    document.getElementById('serviceEnabled').checked = data.settings.service_enabled !== '0';
                }
            } catch (error) {
                console.error('Error loading settings:', error);
            }
        }

        async function saveSettings() {
            const tax_percentage = parseFloat(document.getElementById('taxPercentage').value) || 0;
            const service_charge_percentage = parseFloat(document.getElementById('serviceChargePercentage').value) || 0;
            const tax_enabled = document.getElementById('taxEnabled').checked ? 1 : 0;
            const service_enabled = document.getElementById('serviceEnabled').checked ? 1 : 0;

            try {
                const response = await fetch('/php-native/api/settings/tax-service.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        tax_percentage,
                        service_charge_percentage,
                        tax_enabled,
                        service_enabled
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    alert('Settings saved successfully!');
                    updatePreview();
                } else {
                    alert('Failed to save settings');
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                alert('Error saving settings');
            }
        }

        function updatePreview() {
            const subtotal = 100000;
            const serviceRate = parseFloat(document.getElementById('serviceChargePercentage').value) || 0;
            const taxRate = parseFloat(document.getElementById('taxPercentage').value) || 0;
            const serviceEnabled = document.getElementById('serviceEnabled').checked;
            const taxEnabled = document.getElementById('taxEnabled').checked;

            const serviceCharge = serviceEnabled ? (subtotal * serviceRate / 100) : 0;
            // Tax is calculated on subtotal only (not including service charge)
            const taxAmount = taxEnabled ? (subtotal * taxRate / 100) : 0;
            const total = subtotal + serviceCharge + taxAmount;

            document.getElementById('previewServiceRate').textContent = serviceRate;
            document.getElementById('previewTaxRate').textContent = taxRate;
            document.getElementById('previewService').textContent = 'Rp ' + Math.round(serviceCharge).toLocaleString('id-ID');
            document.getElementById('previewTax').textContent = 'Rp ' + Math.round(taxAmount).toLocaleString('id-ID');
            document.getElementById('previewTotal').textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
        }

        // Add event listeners for live preview
        ['taxPercentage', 'serviceChargePercentage', 'taxEnabled', 'serviceEnabled'].forEach(id => {
            document.getElementById(id).addEventListener('input', updatePreview);
            document.getElementById(id).addEventListener('change', updatePreview);
        });
    </script>
</body>
</html>
