<?php
/**
 * Stickusteak POS - Receipt Template Management
 * Manage receipt/struck templates with logo upload
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
    <title>Receipt Template - Stickusteak POS</title>
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
            margin-bottom: 20px;
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
        
        .receipt-preview {
            background: white;
            border: 1px solid #ddd;
            padding: 20px;
            max-width: 380px;
            margin: 0 auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        
        .receipt-preview.small { font-size: 10px; }
        .receipt-preview.large { font-size: 14px; }
        
        .receipt-logo {
            max-width: 150px;
            max-height: 80px;
            margin: 0 auto 10px;
            display: block;
        }
        
        .receipt-header, .receipt-footer {
            text-align: center;
            white-space: pre-line;
            margin: 10px 0;
        }
        
        .receipt-line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        .template-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .template-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .template-card.active {
            border-color: #28a745;
            background: #f0fff4;
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-receipt me-2"></i>Receipt Template</h2>
                    <button class="btn btn-primary" onclick="showTemplateModal()">
                        <i class="bi bi-plus-lg me-1"></i>New Template
                    </button>
                </div>

                <!-- Info Card -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Receipt Template:</strong> Customize your receipt/struk appearance with logo, header, footer, and layout options.
                </div>

                <div class="row">
                    <!-- Templates List -->
                    <div class="col-md-4">
                        <div class="content-card">
                            <h5 class="mb-3"><i class="bi bi-collection me-2"></i>Templates</h5>
                            <div id="templatesList">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <p class="mt-2">Loading templates...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Editor -->
                    <div class="col-md-8">
                        <div class="content-card">
                            <h5 class="mb-3"><i class="bi bi-pencil me-2"></i>Edit Template</h5>
                            <form id="templateForm">
                                <input type="hidden" id="templateId">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Template Name</label>
                                        <input type="text" class="form-control" id="templateName" required placeholder="e.g., Default Receipt">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Font Size</label>
                                        <select class="form-select" id="fontSize">
                                            <option value="small">Small</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="large">Large</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Paper Size</label>
                                        <select class="form-select" id="paperSize">
                                            <option value="58mm">58mm</option>
                                            <option value="80mm" selected>80mm</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Logo</label>
                                        <input type="file" class="form-control" id="logoUpload" accept="image/*">
                                        <div id="logoPreview" class="mt-2"></div>
                                        <small class="text-muted">PNG, JPG, max 500KB</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Display Options</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="showLogo" checked>
                                            <label class="form-check-label">Show Logo</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="showTax" checked>
                                            <label class="form-check-label">Show Tax Breakdown</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="showService" checked>
                                            <label class="form-check-label">Show Service Charge</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="showQR">
                                            <label class="form-check-label">Show QR Code</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Receipt Header Information</label>
                                        <div class="mb-2">
                                            <label class="form-label">Restaurant Name *</label>
                                            <input type="text" class="form-control" id="restaurantName" placeholder="e.g., Stickusteak">
                                            <small class="text-muted">This appears at the top of receipt</small>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" id="restaurantAddress" placeholder="e.g., SouthCity, Jakarta">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="restaurantPhone" placeholder="e.g., 08123456789">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold" style="color: var(--gold-primary);">👤 Customer Information (Always Display)</label>
                                        <div class="alert" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); color: rgba(255,255,255,0.9); font-size: 0.85rem;">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Customer name and phone from orders will be displayed prominently on every receipt
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Customer Name Label</label>
                                                <input type="text" class="form-control" id="customerNameLabel" value="Customer Name" placeholder="e.g., Customer Name">
                                                <small class="text-muted">Label shown on receipt</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone Number Label</label>
                                                <input type="text" class="form-control" id="customerPhoneLabel" value="Phone Number" placeholder="e.g., Phone Number">
                                                <small class="text-muted">Label shown on receipt</small>
                                            </div>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="showCustomerInfo" checked>
                                            <label class="form-check-label" style="color: rgba(255,255,255,0.8);">
                                                Show Customer Information on Receipt (Table, Customer Name, Phone)
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold">Receipt Footer</label>
                                        <div class="mb-2">
                                            <label class="form-label">Footer Text</label>
                                            <textarea class="form-control" id="footerText" rows="2" placeholder="Thank you for your visit!"></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Website</label>
                                            <input type="text" class="form-control" id="website" placeholder="www.yourwebsite.com">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Social Media</label>
                                            <input type="text" class="form-control" id="socialMedia" placeholder="@yourrestaurant">
                                        </div>
                                    </div>
                                    
                                    <div class="col-12" id="qrTextField" style="display: none;">
                                        <label class="form-label">QR Code Text/URL</label>
                                        <input type="text" class="form-control" id="qrText" placeholder="https://your-website.com">
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="templateActive" checked>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="button" class="btn btn-primary" onclick="saveTemplate()">
                                        <i class="bi bi-save me-1"></i>Save Template
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="previewReceipt()">
                                        <i class="bi bi-eye me-1"></i>Preview
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Receipt Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="receiptPreviewContainer" class="receipt-preview">
                        <!-- Preview will be rendered here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentLogoPath = '';
        
        // Load templates
        async function loadTemplates() {
            try {
                const response = await fetch('/php-native/api/settings/receipt-templates.php');
                const data = await response.json();
                
                const container = document.getElementById('templatesList');
                
                if (data.success && data.templates.length > 0) {
                    let html = '';
                    data.templates.forEach(t => {
                        const activeClass = t.is_default ? 'active' : '';
                        const defaultBadge = t.is_default ? '<span class="badge bg-warning text-dark">Default</span>' : '';
                        
                        html += `
                            <div class="template-card ${activeClass}" onclick="loadTemplate(${t.id})">
                                <h6 class="mb-1">
                                    ${t.template_name}
                                    ${defaultBadge}
                                </h6>
                                <small class="text-muted">
                                    ${t.paper_size} | ${t.font_size} | ${t.is_active ? 'Active' : 'Inactive'}
                                </small>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                    
                    // Load first template by default
                    if (data.templates.length > 0) {
                        loadTemplate(data.templates[0].id);
                    }
                } else {
                    container.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-receipt" style="font-size: 2rem;"></i>
                            <p class="mt-2">No templates yet</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading templates:', error);
            }
        }
        
        // Load template data
        async function loadTemplate(id) {
            try {
                const response = await fetch(`/php-native/api/settings/receipt-templates.php?id=${id}`);
                const data = await response.json();

                if (data.success && data.template) {
                    const t = data.template;
                    document.getElementById('templateId').value = t.id;
                    document.getElementById('templateName').value = t.template_name;
                    document.getElementById('fontSize').value = t.font_size;
                    document.getElementById('paperSize').value = t.paper_size;
                    
                    // Load receipt header fields
                    document.getElementById('restaurantName').value = t.header_text || '';
                    document.getElementById('restaurantAddress').value = t.address || '';
                    document.getElementById('restaurantPhone').value = t.phone || '';
                    
                    // Load footer fields
                    document.getElementById('footerText').value = t.footer_text || '';
                    document.getElementById('website').value = t.website || '';
                    document.getElementById('socialMedia').value = t.social_media || '';
                    
                    document.getElementById('showLogo').checked = t.show_logo == 1;
                    document.getElementById('showTax').checked = t.show_tax_breakdown == 1;
                    document.getElementById('showService').checked = t.show_service_charge == 1;
                    document.getElementById('showQR').checked = t.show_qr_code == 1;
                    document.getElementById('qrText').value = t.qr_code_text || '';
                    document.getElementById('templateActive').checked = t.is_active == 1;

                    currentLogoPath = t.logo_path || '';
                    if (t.logo_path) {
                        document.getElementById('logoPreview').innerHTML = `<img src="${t.logo_path}" style="max-width: 150px; max-height: 80px;">`;
                    } else {
                        document.getElementById('logoPreview').innerHTML = '';
                    }

                    toggleQRField();
                }
            } catch (error) {
                console.error('Error loading template:', error);
            }
        }
        
        // Toggle QR field
        document.getElementById('showQR')?.addEventListener('change', toggleQRField);
        function toggleQRField() {
            const showQR = document.getElementById('showQR').checked;
            document.getElementById('qrTextField').style.display = showQR ? 'block' : 'none';
        }
        
        // Handle logo upload
        document.getElementById('logoUpload')?.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Invalid file type. Please upload PNG, JPG, or GIF');
                this.value = '';
                return;
            }
            
            if (file.size > 500 * 1024) {
                alert('File too large. Maximum size is 500KB');
                this.value = '';
                return;
            }
            
            const formData = new FormData();
            formData.append('logo', file);
            const templateId = document.getElementById('templateId').value;
            if (templateId) {
                formData.append('template_id', templateId);
            }
            
            try {
                const response = await fetch('/php-native/api/settings/upload-logo.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    currentLogoPath = data.url;
                    document.getElementById('logoPreview').innerHTML = `<img src="${data.url}" style="max-width: 150px; max-height: 80px;">`;
                    alert('Logo uploaded successfully!');
                } else {
                    alert('Upload failed: ' + data.message);
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('Error uploading logo');
            }
        });
        
        // Save template
        async function saveTemplate() {
            const id = document.getElementById('templateId').value;
            const template_name = document.getElementById('templateName').value;
            const font_size = document.getElementById('fontSize').value;
            const paper_size = document.getElementById('paperSize').value;

            // Get receipt header fields
            const header_text = document.getElementById('restaurantName').value;
            const address = document.getElementById('restaurantAddress').value;
            const phone = document.getElementById('restaurantPhone').value;

            // Get footer fields
            const footer_text = document.getElementById('footerText').value;
            const website = document.getElementById('website').value;
            const social_media = document.getElementById('socialMedia').value;
            
            // Get customer info fields
            const show_customer_info = document.getElementById('showCustomerInfo').checked ? 1 : 0;
            const customer_name_label = document.getElementById('customerNameLabel').value;
            const customer_phone_label = document.getElementById('customerPhoneLabel').value;

            const show_logo = document.getElementById('showLogo').checked ? 1 : 0;
            const show_tax_breakdown = document.getElementById('showTax').checked ? 1 : 0;
            const show_service_charge = document.getElementById('showService').checked ? 1 : 0;
            const show_qr_code = document.getElementById('showQR').checked ? 1 : 0;
            const qr_code_text = document.getElementById('qrText').value;
            const is_active = document.getElementById('templateActive').checked ? 1 : 0;

            if (!template_name) {
                alert('Template name is required');
                return;
            }
            if (!header_text) {
                alert('Restaurant name is required');
                return;
            }

            try {
                const url = '/php-native/api/settings/receipt-templates.php';
                const method = id ? 'PUT' : 'POST';

                // Build data object with only filled fields
                const dataToSend = {
                    id: id || null,
                    template_name,
                    font_size,
                    paper_size,
                    header_text,
                    footer_text,
                    show_logo,
                    show_tax_breakdown,
                    show_service_charge,
                    show_qr_code,
                    qr_code_text,
                    logo_path: currentLogoPath,
                    is_active,
                    show_customer_info,
                    customer_name_label,
                    customer_phone_label
                };

                // Only add address/phone/website/social if they have values
                if (address) dataToSend.address = address;
                if (phone) dataToSend.phone = phone;
                if (website) dataToSend.website = website;
                if (social_media) dataToSend.social_media = social_media;

                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dataToSend)
                });
                const data = await response.json();

                if (data.success) {
                    alert(id ? 'Template updated successfully!' : 'Template created successfully!');
                    loadTemplates();
                } else {
                    alert(data.message || 'Failed to save template');
                }
            } catch (error) {
                console.error('Error saving template:', error);
                alert('Error saving template. Please run database migration first.');
            }
        }
        
        // Preview receipt
        function previewReceipt() {
            // Get values from new fields
            const restaurantName = document.getElementById('restaurantName').value || 'Your Restaurant';
            const restaurantAddress = document.getElementById('restaurantAddress').value || '';
            const restaurantPhone = document.getElementById('restaurantPhone').value || '';
            const footerText = document.getElementById('footerText').value || 'Thank you!';
            const website = document.getElementById('website').value || '';
            const socialMedia = document.getElementById('socialMedia').value || '';
            const fontSize = document.getElementById('fontSize').value;
            const showLogo = document.getElementById('showLogo').checked;
            const logoPath = currentLogoPath;

            const previewContainer = document.getElementById('receiptPreviewContainer');
            previewContainer.className = `receipt-preview ${fontSize}`;

            let html = '';

            // Logo
            if (showLogo && logoPath) {
                html += `<img src="${logoPath}" class="receipt-logo" style="max-width: 80px; max-height: 80px; margin: 0 auto 10px; display: block;">`;
            }

            // Header - Restaurant info
            html += `<div class="text-center" style="margin-bottom: 10px;">`;
            html += `<strong style="font-size: 13px;">${restaurantName}</strong><br>`;
            if (restaurantAddress) { html += `<span style="font-size: 10px;">${restaurantAddress}</span><br>`; }
            if (restaurantPhone) { html += `<span style="font-size: 10px;">Telp: ${restaurantPhone}</span><br>`; }
            html += `</div>`;
            html += `<hr style="border-top: 1px dashed #000; margin: 8px 0;">`;

            // Sample items
            html += `<table style="width: 100%; font-size: 11px;">`;
            html += `<tr><td style="text-align: left;">1x Item Name</td><td style="text-align: right;">Rp 50,000</td></tr>`;
            html += `<tr><td style="text-align: left;">2x Another Item</td><td style="text-align: right;">Rp 100,000</td></tr>`;
            html += `</table>`;

            html += `<hr style="border-top: 1px dashed #000; margin: 8px 0;">`;

            // Totals
            html += `<table style="width: 100%; font-size: 11px;">`;
            html += `<tr><td style="text-align: left;">Subtotal:</td><td style="text-align: right;">Rp 150,000</td></tr>`;

            if (document.getElementById('showService').checked) {
                html += `<tr><td style="text-align: left;">Service (5%):</td><td style="text-align: right;">Rp 7,500</td></tr>`;
            }

            if (document.getElementById('showTax').checked) {
                html += `<tr><td style="text-align: left;">Tax (10%):</td><td style="text-align: right;">Rp 15,000</td></tr>`;
            }

            html += `<tr style="font-weight: bold; font-size: 13px;"><td style="text-align: left;">TOTAL:</td><td style="text-align: right;">Rp 172,500</td></tr>`;
            html += `</table>`;

            html += `<hr style="border-top: 1px dashed #000; margin: 8px 0;">`;

            // Footer
            html += `<div class="text-center" style="margin-top: 10px; font-size: 10px;">`;
            if (footerText) { html += `<div>${footerText}</div>`; }
            if (website) { html += `<div>${website}</div>`; }
            if (socialMedia) { html += `<div>${socialMedia}</div>`; }
            html += `</div>`;

            previewContainer.innerHTML = html;

            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        }
        
        function showTemplateModal() {
            document.getElementById('templateForm').reset();
            document.getElementById('templateId').value = '';
            currentLogoPath = '';
            document.getElementById('logoPreview').innerHTML = '';
            toggleQRField();
        }
        
        document.addEventListener('DOMContentLoaded', loadTemplates);
    </script>
</body>
</html>
