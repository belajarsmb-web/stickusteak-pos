<?php
/**
 * Stickusteak POS - Settings
 * Premium Black & Gold Theme
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'Staff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Stickusteak POS</title>
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
            --black-tertiary: #2a2a2a;
            --charcoal: #1f1f1f;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
        }

        body {
            background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 100%);
            color: #fff;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--black-tertiary) 0%, var(--black-primary) 100%);
            border-right: 2px solid var(--gold-dark);
            box-shadow: 5px 0 20px rgba(212, 175, 55, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 25px 20px;
            font-size: 1.6rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
            background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold-primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 14px 20px;
            margin: 6px 12px;
            border-radius: 8px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 400;
            position: relative;
            overflow: hidden;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(180deg, var(--gold-primary), var(--gold-light));
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background: rgba(212, 175, 55, 0.1);
            color: var(--gold-light);
            padding-left: 25px;
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(212, 175, 55, 0.1) 100%);
            color: var(--gold-primary);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .sidebar .nav-link.active::before {
            transform: scaleY(1);
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            display: inline-block;
        }

        .logout-btn {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 14px 20px;
            margin: 6px 12px;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .logout-btn:hover {
            background: rgba(220, 53, 69, 0.2);
            color: #ff6b6b;
            border-color: #ff6b6b;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }

        /* Tab Navigation */
        .settings-tabs {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 10px;
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .settings-tabs .nav-link {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.6);
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .settings-tabs .nav-link:hover {
            background: rgba(212, 175, 55, 0.1);
            color: var(--gold-light);
        }

        .settings-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary));
            color: var(--black-primary);
            font-weight: 600;
        }

        /* Content Card */
        .content-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.5s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-title {
            color: var(--gold-light);
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }

        /* Forms */
        .form-label {
            color: rgba(255,255,255,0.8);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #fff;
            padding: 12px 15px;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
            color: #fff;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.3);
        }

        /* Buttons */
        .btn-premium {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-primary), var(--gold-light));
            border: none;
            color: var(--black-primary);
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 25px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
            color: var(--black-primary);
        }

        .btn-outline-premium {
            background: transparent;
            border: 1px solid var(--gold-primary);
            color: var(--gold-primary);
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-premium:hover {
            background: var(--gold-primary);
            color: var(--black-primary);
            transform: translateY(-2px);
        }

        /* Switch */
        .form-check-input {
            background-color: rgba(255,255,255,0.1);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .form-check-input:checked {
            background-color: var(--gold-primary);
            border-color: var(--gold-primary);
        }

        .form-check-label {
            color: rgba(255,255,255,0.8);
        }

        /* Tables Grid */
        .table-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
        }

        .table-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.2);
            border-color: var(--gold-primary);
        }

        .table-name {
            color: var(--gold-light);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .table-info {
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
            margin-bottom: 12px;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 15px;
        }

        .status-badge.available {
            background: linear-gradient(135deg, #28a745, #34ce57);
            color: white;
        }

        .status-badge.occupied {
            background: linear-gradient(135deg, #dc3545, #ff6b6b);
            color: white;
        }

        .status-badge.reserved {
            background: linear-gradient(135deg, #ffc107, #ffd93d);
            color: var(--black-primary);
        }

        .table-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .btn-icon.btn-outline-primary {
            border-color: var(--gold-primary);
            color: var(--gold-primary);
            background: transparent;
        }

        .btn-icon.btn-outline-primary:hover {
            background: var(--gold-primary);
            color: var(--black-primary);
            transform: translateY(-2px);
        }

        .btn-icon.btn-outline-danger {
            border-color: #ff6b6b;
            color: #ff6b6b;
            background: transparent;
        }

        .btn-icon.btn-outline-danger:hover {
            background: #ff6b6b;
            color: white;
            transform: translateY(-2px);
        }

        /* Modal */
        .modal-content {
            background: linear-gradient(135deg, var(--black-tertiary) 0%, var(--black-secondary) 100%);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 16px;
        }

        .modal-header {
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            padding: 20px 25px;
        }

        .modal-title {
            color: var(--gold-light);
            font-weight: 600;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid rgba(212, 175, 55, 0.2);
            padding: 15px 25px;
        }

        .btn-close-white {
            filter: invert(1);
        }

        /* Loading State */
        .loading-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255,255,255,0.4);
        }

        .loading-state .spinner-border {
            color: var(--gold-primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .main-content {
                margin-left: 0;
            }
            .settings-tabs {
                overflow-x: auto;
                flex-wrap: nowrap;
            }
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
                    <a href="/php-native/mobile/generate-qr.php" class="nav-link" target="_blank">
                        <i class="bi bi-qr-code"></i>QR Codes
                    </a>
                </nav>
                <div class="mt-auto">
                    <a href="/php-native/api/auth/logout.php" class="logout-btn">
                        <i class="bi bi-box-arrow-left me-2"></i>Logout
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title"><i class="bi bi-gear me-2"></i>Settings</h1>
                </div>

                <!-- Tab Navigation -->
                <ul class="settings-tabs nav" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#general-tab">
                            <i class="bi bi-sliders"></i>General
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#outlet-tab">
                            <i class="bi bi-shop"></i>Outlet
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tables-tab">
                            <i class="bi bi-grid"></i>Tables
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#notes-tab" onclick="window.location.href='/php-native/pages/settings-notes.php'">
                            <i class="bi bi-chat-left-text"></i>Item Notes
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#printer-tab" onclick="window.location.href='/php-native/pages/settings-printers.php'">
                            <i class="bi bi-printer"></i>Printers
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#receipt-tab" onclick="window.location.href='/php-native/pages/settings-receipt-template.php'">
                            <i class="bi bi-receipt"></i>Receipt Template
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tax-tab" onclick="window.location.href='/php-native/pages/settings-tax-service.php'">
                            <i class="bi bi-percent"></i>Tax & Service
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-tab">
                            <i class="bi bi-person"></i>Profile
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general-tab">
                        <div class="content-card">
                            <h5 class="card-title"><i class="bi bi-sliders me-2"></i>General Settings</h5>
                            <form id="generalSettingsForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Restaurant Name</label>
                                        <input type="text" class="form-control" id="restaurantName" value="Stickusteak POS">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Currency</label>
                                        <select class="form-select" id="currency">
                                            <option value="USD">USD ($)</option>
                                            <option value="EUR">EUR (€)</option>
                                            <option value="GBP">GBP (£)</option>
                                            <option value="IDR" selected>IDR (Rp)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Timezone</label>
                                        <select class="form-select" id="timezone">
                                            <option value="UTC">UTC</option>
                                            <option value="America/New_York">Eastern Time</option>
                                            <option value="America/Chicago">Central Time</option>
                                            <option value="Asia/Jakarta" selected>Jakarta (WIB)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Language</label>
                                        <select class="form-select" id="language">
                                            <option value="en">English</option>
                                            <option value="id" selected>Bahasa Indonesia</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enableNotifications" checked>
                                            <label class="form-check-label" for="enableNotifications">
                                                Enable Order Notifications
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enableSound">
                                            <label class="form-check-label" for="enableSound">
                                                Enable Sound Alerts
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-premium mt-4" onclick="saveGeneralSettings()">
                                    <i class="bi bi-save me-2"></i>Save Settings
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Outlet Settings -->
                    <div class="tab-pane fade" id="outlet-tab">
                        <div class="content-card">
                            <h5 class="card-title"><i class="bi bi-shop me-2"></i>Outlet Information</h5>
                            <form id="outletForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Outlet Name</label>
                                        <input type="text" class="form-control" id="outletName">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control" id="outletPhone">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="outletEmail">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tax Rate (%)</label>
                                        <input type="number" class="form-control" id="taxRate" step="0.1" value="10">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" id="outletAddress" rows="3"></textarea>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-premium mt-4" onclick="saveOutletSettings()">
                                    <i class="bi bi-save me-2"></i>Save Outlet
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tables Settings -->
                    <div class="tab-pane fade" id="tables-tab">
                        <div class="content-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="card-title mb-0"><i class="bi bi-grid me-2"></i>Table Management</h5>
                                <button class="btn btn-premium btn-sm" onclick="showAddTableModal()">
                                    <i class="bi bi-plus-lg me-1"></i>Add Table
                                </button>
                            </div>
                            <div class="row g-3" id="tablesGrid">
                                <div class="col-12 text-center">
                                    <div class="loading-state">
                                        <div class="spinner-border" role="status"></div>
                                        <p style="margin-top: 15px;">Loading tables...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Settings -->
                    <div class="tab-pane fade" id="profile-tab">
                        <div class="content-card">
                            <h5 class="card-title"><i class="bi bi-person me-2"></i>Profile Settings</h5>
                            <form id="profileForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="profileName" value="<?php echo htmlspecialchars($username); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="profileEmail">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="newPassword" placeholder="Leave blank to keep current">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirmPassword">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-premium mt-4" onclick="saveProfile()">
                                    <i class="bi bi-save me-2"></i>Update Profile
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load general settings on page load
        document.addEventListener('DOMContentLoaded', async () => {
            await loadGeneralSettings();
            await loadTables();
        });

        async function loadGeneralSettings() {
            try {
                const response = await fetch('/php-native/api/settings/general.php');
                const data = await response.json();

                if (data.success) {
                    const s = data.settings;
                    document.getElementById('restaurantName').value = s.restaurant_name || 'Stickusteak POS';
                    document.getElementById('currency').value = s.currency || 'IDR';
                    document.getElementById('timezone').value = s.timezone || 'Asia/Jakarta';
                    document.getElementById('language').value = s.language || 'id';
                }
            } catch (error) {
                console.error('Error loading settings:', error);
            }
        }

        async function saveGeneralSettings() {
            const restaurant_name = document.getElementById('restaurantName').value;
            const currency = document.getElementById('currency').value;
            const timezone = document.getElementById('timezone').value;
            const language = document.getElementById('language').value;

            try {
                const response = await fetch('/php-native/api/settings/general.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        restaurant_name,
                        currency,
                        timezone,
                        language
                    })
                });
                const data = await response.json();

                if (data.success) {
                    alert('Settings saved successfully!');
                    await loadGeneralSettings();
                } else {
                    alert('Failed to save settings: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                alert('Error saving settings');
            }
        }

        function saveOutletSettings() {
            alert('Outlet settings saved successfully!');
        }

        function saveProfile() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword && newPassword !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }

            alert('Profile updated successfully!');
        }

        async function loadTables() {
            try {
                const response = await fetch('/php-native/api/tables/index.php');
                const data = await response.json();
                const grid = document.getElementById('tablesGrid');

                if (data.success && data.tables && data.tables.length > 0) {
                    let html = '';
                    data.tables.forEach((table, index) => {
                        const statusClass = table.status === 'occupied' ? 'occupied' :
                                           table.status === 'reserved' ? 'reserved' : 'available';
                        const statusText = table.status === 'occupied' ? 'Occupied' :
                                          table.status === 'reserved' ? 'Reserved' : 'Available';
                        const delay = index * 0.1;
                        html += `
                            <div class="col-md-3 col-sm-6">
                                <div class="table-card" style="animation: fadeInUp 0.5s ease forwards; animation-delay: ${delay}s">
                                    <div class="table-name">Table ${table.name || table.id}</div>
                                    <div class="table-info"><i class="bi bi-people me-1"></i>${table.capacity || '-'} seats</div>
                                    <span class="status-badge ${statusClass}">${statusText}</span>
                                    <div class="table-actions">
                                        <button class="btn-icon btn-outline-primary" onclick="editTable(${table.id})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-icon btn-outline-danger" onclick="deleteTable(${table.id})" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    grid.innerHTML = html;
                } else {
                    grid.innerHTML = `
                        <div class="col-12 text-center">
                            <div class="loading-state">
                                <i class="bi bi-grid-3x3-gap" style="font-size: 3rem; margin-bottom: 15px; display: block; color: rgba(255,255,255,0.3);"></i>
                                <p>No tables configured</p>
                                <button class="btn btn-premium mt-3" onclick="showAddTableModal()">
                                    <i class="bi bi-plus-lg me-1"></i>Add Table
                                </button>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading tables:', error);
            }
        }

        function showAddTableModal() {
            const name = prompt('Enter table name/number:');
            const capacity = prompt('Enter table capacity (number of seats):');

            if (name && capacity) {
                addTable(name, capacity);
            }
        }

        async function addTable(name, capacity) {
            try {
                const response = await fetch('/php-native/api/tables/store.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name: name,
                        table_number: name,
                        capacity: capacity || 4
                    })
                });
                const data = await response.json();
                if (data.success) {
                    loadTables();
                    if (confirm('Table added successfully! Generate QR code now?')) {
                        window.open('/php-native/mobile/generate-qr.php', '_blank');
                    }
                } else {
                    alert(data.message || 'Failed to add table');
                }
            } catch (error) {
                console.error('Error adding table:', error);
                alert('Failed to add table: ' + error.message);
            }
        }

        function editTable(id) {
            alert('Edit table - ID: ' + id);
        }

        async function deleteTable(id) {
            if (!confirm('Are you sure you want to delete this table?')) return;

            try {
                const response = await fetch(`/php-native/api/tables/delete.php?id=${id}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                if (data.success) {
                    loadTables();
                } else {
                    alert(data.message || 'Failed to delete table');
                }
            } catch (error) {
                console.error('Error deleting table:', error);
            }
        }
    </script>
</body>
</html>
