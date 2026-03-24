<?php
/**
 * Stickusteak POS - Printer Management
 * Manage multiple printers and routing
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
    <title>Printer Management - Stickusteak POS</title>
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
        
        .printer-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .printer-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .printer-card.active {
            border-color: #28a745;
            background: #f0fff4;
        }
        
        .printer-card.inactive {
            border-color: #dc3545;
            background: #fff5f5;
            opacity: 0.7;
        }
        
        .printer-type-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .type-kitchen { background: #dc3545; color: white; }
        .type-bar { background: #ffc107; color: #000; }
        .type-receipt { background: #007bff; color: white; }
        .type-label { background: #28a745; color: white; }
        
        .connection-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .conn-network { background: #17a2b8; color: white; }
        .conn-usb { background: #6c757d; color: white; }
        .conn-bluetooth { background: #007bff; color: white; }
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
                    <h2><i class="bi bi-printer me-2"></i>Printer Management</h2>
                    <button class="btn btn-primary" onclick="showAddPrinterModal()">
                        <i class="bi bi-plus-lg me-1"></i>Add Printer
                    </button>
                </div>

                <!-- Info Card -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Printer Routing:</strong> Configure multiple printers for different areas (Kitchen, Bar, Cashier).
                    Orders will be automatically routed to the appropriate printer based on item category.
                </div>

                <!-- Printers List -->
                <div class="content-card">
                    <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i>Configured Printers</h5>
                    <div id="printersList">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2">Loading printers...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Printer Modal -->
    <div class="modal fade" id="printerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-printer me-2"></i><span id="modalTitle">Add Printer</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="printerForm">
                        <input type="hidden" id="printerId">
                        <div class="mb-3">
                            <label class="form-label">Printer Name</label>
                            <input type="text" class="form-control" id="printerName" required placeholder="e.g., Main Kitchen Printer">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location/Type</label>
                            <select class="form-select" id="printerType" required>
                                <option value="kitchen">Kitchen</option>
                                <option value="bar">Bar</option>
                                <option value="receipt">Receipt/Cashier</option>
                                <option value="label">Label Printer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Connection Type</label>
                            <select class="form-select" id="connectionType" required onchange="toggleConnectionFields()">
                                <option value="network">Network (TCP/IP)</option>
                                <option value="usb">USB</option>
                                <option value="bluetooth">Bluetooth</option>
                            </select>
                        </div>
                        <div id="networkFields">
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label">IP Address</label>
                                    <input type="text" class="form-control" id="ipAddress" placeholder="192.168.1.100">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Port</label>
                                    <input type="number" class="form-control" id="printerPort" value="9100">
                                </div>
                            </div>
                        </div>
                        <div id="usbFields" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">USB Device Path</label>
                                <input type="text" class="form-control" id="devicePath" placeholder="/dev/usb/lp0">
                            </div>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="printerActive" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="savePrinter()">
                        <i class="bi bi-check-lg me-1"></i>Save Printer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load printers
        async function loadPrinters() {
            try {
                const response = await fetch('/php-native/api/settings/printers.php');
                const data = await response.json();
                
                const container = document.getElementById('printersList');
                
                if (data.success && data.printers.length > 0) {
                    let html = '';
                    data.printers.forEach(printer => {
                        const typeClass = `type-${printer.type}`;
                        const connClass = `conn-${printer.connection_type}`;
                        const activeClass = printer.is_active ? 'active' : 'inactive';
                        const statusText = printer.is_active ? 'Active' : 'Inactive';
                        
                        let connectionInfo = '';
                        if (printer.connection_type === 'network') {
                            connectionInfo = `${printer.ip_address}:${printer.port}`;
                        } else if (printer.connection_type === 'usb') {
                            connectionInfo = printer.device_path || 'USB';
                        } else {
                            connectionInfo = 'Bluetooth';
                        }
                        
                        html += `
                            <div class="printer-card ${activeClass}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="bi bi-printer me-2"></i>${printer.name}
                                            ${printer.is_default ? '<span class="badge bg-warning text-dark ms-2">Default</span>' : ''}
                                        </h6>
                                        <div class="mb-2">
                                            <span class="printer-type-badge ${typeClass}">${printer.type.toUpperCase()}</span>
                                            <span class="connection-badge ${connClass} ms-2">${printer.connection_type}</span>
                                            <span class="badge bg-${printer.is_active ? 'success' : 'danger'} ms-2">${statusText}</span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-hdd-network me-1"></i>${connectionInfo}
                                        </small>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editPrinter(${printer.id}, '${printer.name}', '${printer.type}', '${printer.connection_type}', '${printer.ip_address || ''}', ${printer.port || 9100}, '${printer.device_path || ''}', ${printer.is_active})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger ms-1" onclick="deletePrinter(${printer.id})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-printer" style="font-size: 3rem;"></i>
                            <p class="mt-3">No printers configured</p>
                            <button class="btn btn-primary mt-2" onclick="showAddPrinterModal()">
                                <i class="bi bi-plus-lg me-1"></i>Add First Printer
                            </button>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading printers:', error);
            }
        }
        
        // Toggle connection fields
        function toggleConnectionFields() {
            const connType = document.getElementById('connectionType').value;
            document.getElementById('networkFields').style.display = connType === 'network' ? 'block' : 'none';
            document.getElementById('usbFields').style.display = connType === 'usb' ? 'block' : 'none';
        }
        
        // Show add printer modal
        function showAddPrinterModal() {
            document.getElementById('printerId').value = '';
            document.getElementById('printerName').value = '';
            document.getElementById('printerType').value = 'kitchen';
            document.getElementById('connectionType').value = 'network';
            document.getElementById('ipAddress').value = '';
            document.getElementById('printerPort').value = 9100;
            document.getElementById('devicePath').value = '';
            document.getElementById('printerActive').checked = true;
            document.getElementById('modalTitle').textContent = 'Add Printer';
            toggleConnectionFields();
            
            const modal = new bootstrap.Modal(document.getElementById('printerModal'));
            modal.show();
        }
        
        // Edit printer
        function editPrinter(id, name, type, connType, ip, port, devicePath, isActive) {
            document.getElementById('printerId').value = id;
            document.getElementById('printerName').value = name;
            document.getElementById('printerType').value = type;
            document.getElementById('connectionType').value = connType;
            document.getElementById('ipAddress').value = ip || '';
            document.getElementById('printerPort').value = port || 9100;
            document.getElementById('devicePath').value = devicePath || '';
            document.getElementById('printerActive').checked = isActive;
            document.getElementById('modalTitle').textContent = 'Edit Printer';
            toggleConnectionFields();
            
            const modal = new bootstrap.Modal(document.getElementById('printerModal'));
            modal.show();
        }
        
        // Save printer
        async function savePrinter() {
            const id = document.getElementById('printerId').value;
            const name = document.getElementById('printerName').value;
            const type = document.getElementById('printerType').value;
            const connection_type = document.getElementById('connectionType').value;
            const ip_address = document.getElementById('ipAddress').value;
            const port = document.getElementById('printerPort').value;
            const device_path = document.getElementById('devicePath').value;
            const is_active = document.getElementById('printerActive').checked ? 1 : 0;
            
            if (!name) {
                alert('Printer name is required');
                return;
            }
            
            if (connection_type === 'network' && !ip_address) {
                alert('IP address is required for network printers');
                return;
            }
            
            try {
                const url = '/php-native/api/settings/printers.php';
                const method = id ? 'PUT' : 'POST';
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: id || null,
                        name,
                        type,
                        connection_type,
                        ip_address,
                        port,
                        device_path,
                        is_active
                    })
                });
                const data = await response.json();
                if (data.success) {
                    alert(id ? 'Printer updated successfully!' : 'Printer added successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('printerModal')).hide();
                    loadPrinters();
                } else {
                    alert(data.message || 'Failed to save printer');
                }
            } catch (error) {
                console.error('Error saving printer:', error);
                alert('Error saving printer');
            }
        }
        
        // Delete printer
        async function deletePrinter(id) {
            if (!confirm('Are you sure you want to delete this printer?')) return;
            
            try {
                const response = await fetch(`/php-native/api/settings/printers.php?id=${id}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                if (data.success) {
                    loadPrinters();
                } else {
                    alert(data.message || 'Failed to delete printer');
                }
            } catch (error) {
                console.error('Error deleting printer:', error);
                alert('Error deleting printer');
            }
        }
        
        document.addEventListener('DOMContentLoaded', loadPrinters);
    </script>
</body>
</html>
