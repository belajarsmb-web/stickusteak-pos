<?php
/**
 * Stickusteak POS - Users Management
 * View and manage system users
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
    <title>Users - Stickusteak POS</title>
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
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
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
                    <a href="/php-native/pages/users.php" class="nav-link active">
                        <i class="bi bi-person-badge"></i>Users
                    </a>
                    <a href="/php-native/pages/settings.php" class="nav-link">
                        <i class="bi bi-gear"></i>Settings
                    </a>
                    <a href="/php-native/mobile/generate-qr.php" class="nav-link" target="_blank">
                        <i class="bi bi-qr-code"></i>QR Codes
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
                    <h2><i class="bi bi-person-badge me-2"></i>Users Management</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newUserModal">
                        <i class="bi bi-plus-lg me-1"></i>Add User
                    </button>
                </div>

                <!-- Stats -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="content-card d-flex align-items-center">
                            <div class="stat-icon primary me-3" style="width:60px;height:60px;border-radius:12px;background:rgba(102,126,234,0.1);color:#667eea;display:flex;align-items:center;justify-content:center;font-size:1.8rem;">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <div class="stat-value" id="totalUsers" style="font-size:1.8rem;font-weight:bold;">0</div>
                                <div class="stat-label" style="color:#6c757d;">Total Users</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="content-card d-flex align-items-center">
                            <div class="stat-icon success me-3" style="width:60px;height:60px;border-radius:12px;background:rgba(40,167,69,0.1);color:#28a745;display:flex;align-items:center;justify-content:center;font-size:1.8rem;">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div>
                                <div class="stat-value" id="activeUsers" style="font-size:1.8rem;font-weight:bold;">0</div>
                                <div class="stat-label" style="color:#6c757d;">Active Users</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="content-card d-flex align-items-center">
                            <div class="stat-icon warning me-3" style="width:60px;height:60px;border-radius:12px;background:rgba(255,193,7,0.1);color:#ffc107;display:flex;align-items:center;justify-content:center;font-size:1.8rem;">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <div>
                                <div class="stat-value" id="adminUsers" style="font-size:1.8rem;font-weight:bold;">0</div>
                                <div class="stat-label" style="color:#6c757d;">Administrators</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="content-card">
                    <div class="table-responsive">
                        <table class="table table-hover" id="usersTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Loading users...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New User Modal -->
    <div class="modal fade" id="newUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="newUserForm">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="userFullName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="userUsername" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="userEmail" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="userPassword" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <select class="form-select" id="userRole">
                                    <option value="1">Admin</option>
                                    <option value="2">Manager</option>
                                    <option value="3">Cashier</option>
                                    <option value="4">Staff</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="userStatus">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitUser()">
                        <i class="bi bi-check-lg me-1"></i>Save User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function loadUsers() {
            try {
                const response = await fetch('/php-native/api/users/index.php');
                const data = await response.json();
                const tbody = document.querySelector('#usersTable tbody');
                
                if (data.success && data.users && data.users.length > 0) {
                    document.getElementById('totalUsers').textContent = data.users.length;
                    
                    let activeCount = 0;
                    let adminCount = 0;
                    let html = '';
                    
                    data.users.forEach(user => {
                        if (user.status === 1 || user.is_active) activeCount++;
                        if (user.role === 'admin' || user.role_id == 1) adminCount++;
                        
                        const initials = (user.full_name || user.username || 'U').split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
                        const statusClass = (user.status === 1 || user.is_active) ? 'success' : 'secondary';
                        const statusText = (user.status === 1 || user.is_active) ? 'Active' : 'Inactive';
                        
                        html += `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">${initials}</div>
                                        <span>${user.full_name || 'N/A'}</span>
                                    </div>
                                </td>
                                <td>${user.username}</td>
                                <td>${user.email || '-'}</td>
                                <td><span class="badge bg-primary">${user.role || 'Staff'}</span></td>
                                <td><span class="badge bg-${statusClass}">${statusText}</span></td>
                                <td>${user.last_login_at ? formatDate(user.last_login_at) : 'Never'}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editUser(${user.id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    tbody.innerHTML = html;
                    document.getElementById('activeUsers').textContent = activeCount;
                    document.getElementById('adminUsers').textContent = adminCount;
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No users found</td></tr>';
                }
            } catch (error) {
                console.error('Error loading users:', error);
            }
        }

        function formatDate(dateStr) {
            return new Date(dateStr).toLocaleString();
        }

        async function submitUser() {
            const full_name = document.getElementById('userFullName').value;
            const username = document.getElementById('userUsername').value;
            const email = document.getElementById('userEmail').value;
            const password = document.getElementById('userPassword').value;
            const role_id = document.getElementById('userRole').value;
            const is_active = document.getElementById('userStatus').value;

            if (!full_name || !username || !password) {
                alert('Please fill in all required fields');
                return;
            }

            try {
                const response = await fetch('/php-native/api/users/store.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ full_name, username, email, password, role_id, is_active })
                });
                const data = await response.json();
                if (data.success) {
                    alert('User added successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('newUserModal')).hide();
                    document.getElementById('newUserForm').reset();
                    loadUsers();
                } else {
                    alert(data.message || 'Failed to add user');
                }
            } catch (error) {
                console.error('Error adding user:', error);
                alert('Error adding user');
            }
        }

        function editUser(id) {
            alert('Edit functionality - User ID: ' + id);
        }

        async function deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user?')) return;
            
            try {
                const response = await fetch(`/php-native/api/users/delete.php?id=${id}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                if (data.success) {
                    loadUsers();
                } else {
                    alert(data.message || 'Failed to delete user');
                }
            } catch (error) {
                console.error('Error deleting user:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', loadUsers);
    </script>
</body>
</html>
