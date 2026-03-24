<?php
/**
 * Stickusteak POS - Modifiers Management
 * Premium Black & Gold Theme
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
$page_title = "Modifiers";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifiers - Stickusteak POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="/php-native/assets/css/premium-theme.css" rel="stylesheet">
    <link href="/php-native/assets/css/modifiers-compact.css" rel="stylesheet">
    <style>
        .modifiers-container {
            margin-top: 30px;
        }
        
        .groups-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .modifier-group-card {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 12px; max-height: calc(100vh - 250px); display: flex; flex-direction: column;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: slideRight 0.6s ease-out;
        }
        
        .modifier-group-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold-primary);
            box-shadow: 0 15px 40px rgba(212,175,55,0.3);
        }
        
        .group-header {
            background: linear-gradient(135deg, rgba(212,175,55,0.2) 0%, rgba(212,175,55,0.05) 100%);
            padding: 12px;
            border-bottom: 1px solid rgba(212,175,55,0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .group-title {
            font-size: 1.3rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }
        
        .group-meta {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
            margin-top: 5px;
        }
        
        .group-actions {
            display: flex;
            gap: 8px;
        }
        
        .group-body {
            padding: 12px;
        }
        
        .modifiers-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .modifier-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(212,175,55,0.1);
            border-radius: 10px;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }
        
        .modifier-item:hover {
            background: rgba(212,175,55,0.05);
            border-color: rgba(212,175,55,0.3);
        }
        
        .modifier-info {
            flex: 1;
        }
        
        .modifier-name {
            font-weight: 600;
            color: var(--gold-light);
            margin-bottom: 5px;
        }
        
        .modifier-price {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.5);
        }
        
        .modifier-price.positive {
            color: #28a745;
        }
        
        .modifier-price.negative {
            color: #dc3545;
        }
        
        .modifier-actions {
            display: flex;
            gap: 8px;
        }
        
        .badge-required {
            background: rgba(220,53,69,0.2);
            color: #dc3545;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-optional {
            background: rgba(23,162,184,0.2);
            color: #17a2b8;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-single, .badge-multiple {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-single {
            background: rgba(13,110,253,0.2);
            color: #0d6efd;
        }
        
        .badge-multiple {
            background: rgba(111,66,193,0.2);
            color: #6f42c1;
        }
        
        .add-modifier-btn {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background: rgba(212,175,55,0.1);
            border: 1px dashed rgba(212,175,55,0.3);
            border-radius: 10px;
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .add-modifier-btn:hover {
            background: rgba(212,175,55,0.2);
            border-color: var(--gold-primary);
            color: #fff;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: rgba(255,255,255,0.4);
            grid-column: 1 / -1;
        }
        
        .empty-state i {
            font-size: 5rem;
            margin-bottom: 20px;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-mini {
            background: linear-gradient(135deg, rgba(42,42,42,0.9) 0%, rgba(26,26,26,0.9) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-mini:hover {
            transform: translateY(-5px);
            border-color: var(--gold-primary);
        }
        
        .stat-mini-value {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-mini-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        @keyframes slideRight {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
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
                    <a href="/php-native/pages/modifiers.php" class="nav-link active">
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
                <!-- Page Header -->
                <div class="pos-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1"><i class="bi bi-ui-checks-grid me-2"></i>Modifiers Management</h2>
                            <p class="mb-0" style="color: rgba(255,255,255,0.5); font-size: 0.9rem;">Manage modifier groups and options</p>
                        </div>
                        <div>
                            <button class="btn btn-primary" onclick="showAddGroupModal()">
                                <i class="bi bi-plus-lg me-1"></i>Add Modifier Group
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="stats-row">
                    <div class="stat-mini">
                        <div class="stat-mini-value" id="totalGroups">0</div>
                        <div class="stat-mini-label">Total Groups</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-value" id="totalModifiers">0</div>
                        <div class="stat-mini-label">Total Modifiers</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-value" id="requiredGroups">0</div>
                        <div class="stat-mini-label">Required Groups</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-value" id="optionalGroups">0</div>
                        <div class="stat-mini-label">Optional Groups</div>
                    </div>
                </div>

                <!-- Modifier Groups Grid -->
                <div class="modifiers-container">
                    <div class="groups-row" id="groupsContainer">
                        <div class="empty-state">
                            <i class="bi bi-hourglass-split"></i>
                            <h3>Loading modifiers...</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Group Modal -->
    <div class="modal fade" id="groupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-folder me-2"></i><span id="groupModalTitle">Add Modifier Group</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="groupForm">
                        <input type="hidden" id="groupId">
                        <div class="mb-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" class="form-control" id="groupName" required placeholder="e.g., Steak Temperature">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="groupDescription" rows="2" placeholder="e.g., Choose how you want your steak cooked"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Selection Type</label>
                            <select class="form-select" id="groupType">
                                <option value="single">Single Selection (Optional)</option>
                                <option value="required_single">Single Selection (Required)</option>
                                <option value="multiple">Multiple Selection (Optional)</option>
                                <option value="required_multiple">Multiple Selection (Required)</option>
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Min Selections</label>
                                <input type="number" class="form-control" id="groupMin" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max Selections</label>
                                <input type="number" class="form-control" id="groupMax" value="1">
                            </div>
                        </div>
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" id="groupActive" checked>
                            <label class="form-check-label" style="color: rgba(255,255,255,0.8);">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveGroup()">
                        <i class="bi bi-check-lg me-1"></i>Save Group
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modifier Modal -->
    <div class="modal fade" id="modifierModal" tabindex="-1" aria-labelledby="modifierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifierModalLabel"><i class="bi bi-tag me-2"></i><span id="modifierModalTitle">Add Modifier</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="modifierForm">
                        <input type="hidden" id="modifierId">
                        <input type="hidden" id="modifierGroupId">
                        <div class="mb-3">
                            <label class="form-label">Modifier Name</label>
                            <input type="text" class="form-control" id="modifierName" required placeholder="e.g., Medium Rare">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price Adjustment (Rp)</label>
                            <input type="number" class="form-control" id="modifierPrice" step="1000" value="0">
                            <small class="text-muted" style="color: rgba(255,255,255,0.4);">Use negative value for discount</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="modifierActive" checked>
                            <label class="form-check-label" style="color: rgba(255,255,255,0.8);">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveModifier()">
                        <i class="bi bi-check-lg me-1"></i>Save Modifier
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let modifierGroups = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', loadModifierGroups);

        // Load modifier groups
        async function loadModifierGroups() {
            try {
                const response = await fetch('/php-native/api/modifiers/groups.php');
                const data = await response.json();
                
                if (data.success) {
                    modifierGroups = data.groups;
                    renderModifierGroups();
                    updateStats();
                }
            } catch (error) {
                console.error('Error loading modifier groups:', error);
            }
        }

        // Render modifier groups
        function renderModifierGroups() {
            const container = document.getElementById('groupsContainer');
            
            if (modifierGroups.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-ui-checks-grid"></i>
                        <h3>No modifier groups yet</h3>
                        <p class="text-muted">Click "Add Modifier Group" to create one</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            modifierGroups.forEach((group, index) => {
                const isRequired = group.selection_type.includes('required');
                const isMultiple = group.selection_type.includes('multiple');
                const typeBadge = isRequired ? 
                    `<span class="badge-required">Required</span>` : 
                    `<span class="badge-optional">Optional</span>`;
                const selectionBadge = isMultiple ?
                    `<span class="badge-multiple">Multiple</span>` :
                    `<span class="badge-single">Single</span>`;
                
                html += `
                    <div class="modifier-group-card" style="animation-delay: ${index * 0.1}s">
                        <div class="group-header">
                            <div>
                                <h5 class="group-title">${group.name}</h5>
                                <div class="group-meta">
                                    ${typeBadge} ${selectionBadge}
                                    ${group.description ? `<br><small>${group.description}</small>` : ''}
                                </div>
                            </div>
                            <div class="group-actions">
                                <button class="btn btn-sm btn-outline-primary" onclick="editGroup(${group.id}, '${escapeHtml(group.name)}', '${escapeHtml(group.description || '')}', '${group.selection_type}', ${group.min_selection}, ${group.max_selection}, ${group.is_active})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteGroup(${group.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="group-body">
                            <div class="modifiers-list" id="modifiers-${group.id}">
                                ${group.modifiers && group.modifiers.length > 0 ? 
                                    group.modifiers.map(mod => `
                                        <div class="modifier-item">
                                            <div class="modifier-info">
                                                <div class="modifier-name">${mod.name}</div>
                                                <div class="modifier-price ${mod.price > 0 ? 'positive' : mod.price < 0 ? 'negative' : ''}">
                                                    ${mod.price > 0 ? '+' : ''}Rp ${parseInt(mod.price).toLocaleString('id-ID')}
                                                </div>
                                            </div>
                                            <div class="modifier-actions">
                                                <button class="btn btn-sm btn-outline-primary" onclick="editModifier(${mod.id}, '${escapeHtml(mod.name)}', ${mod.price}, ${mod.is_active})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteModifier(${mod.id})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `).join('') :
                                    '<p class="text-muted" style="color: rgba(255,255,255,0.4); text-align: center; padding: 12px;">No modifiers in this group</p>'
                                }
                            </div>
                            <button class="add-modifier-btn" onclick="showAddModifierModal(${group.id})">
                                <i class="bi bi-plus-lg me-1"></i>Add Modifier
                            </button>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        // Update stats
        function updateStats() {
            document.getElementById('totalGroups').textContent = modifierGroups.length;
            const totalModifiers = modifierGroups.reduce((sum, g) => sum + (g.modifiers ? g.modifiers.length : 0), 0);
            document.getElementById('totalModifiers').textContent = totalModifiers;
            document.getElementById('requiredGroups').textContent = modifierGroups.filter(g => g.selection_type.includes('required')).length;
            document.getElementById('optionalGroups').textContent = modifierGroups.filter(g => !g.selection_type.includes('required')).length;
        }

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML.replace(/'/g, "\\'");
        }

        // Show add group modal
        function showAddGroupModal() {
            document.getElementById('groupId').value = '';
            document.getElementById('groupName').value = '';
            document.getElementById('groupDescription').value = '';
            document.getElementById('groupType').value = 'single';
            document.getElementById('groupMin').value = 0;
            document.getElementById('groupMax').value = 1;
            document.getElementById('groupActive').checked = true;
            document.getElementById('groupModalTitle').textContent = 'Add Modifier Group';
            
            const modal = new bootstrap.Modal(document.getElementById('groupModal'));
            modal.show();
        }

        // Edit group
        function editGroup(id, name, description, type, min, max, isActive) {
            document.getElementById('groupId').value = id;
            document.getElementById('groupName').value = name;
            document.getElementById('groupDescription').value = description;
            document.getElementById('groupType').value = type;
            document.getElementById('groupMin').value = min;
            document.getElementById('groupMax').value = max;
            document.getElementById('groupActive').checked = isActive;
            document.getElementById('groupModalTitle').textContent = 'Edit Modifier Group';
            
            const modal = new bootstrap.Modal(document.getElementById('groupModal'));
            modal.show();
        }

        // Save group
        async function saveGroup() {
            const id = document.getElementById('groupId').value;
            const name = document.getElementById('groupName').value;
            const description = document.getElementById('groupDescription').value;
            const selection_type = document.getElementById('groupType').value;
            const min_selections = document.getElementById('groupMin').value;
            const max_selections = document.getElementById('groupMax').value;
            const is_active = document.getElementById('groupActive').checked ? 1 : 0;
            
            if (!name) {
                alert('Group name is required');
                return;
            }
            
            try {
                const url = '/php-native/api/modifiers/groups.php';
                const method = id ? 'PUT' : 'POST';
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: id || null,
                        name,
                        description,
                        selection_type,
                        min_selections,
                        max_selections,
                        is_active
                    })
                });
                const data = await response.json();
                if (data.success) {
                    alert(id ? 'Group updated successfully!' : 'Group created successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('groupModal')).hide();
                    loadModifierGroups();
                } else {
                    alert(data.message || 'Failed to save group');
                }
            } catch (error) {
                console.error('Error saving group:', error);
                alert('Error saving group');
            }
        }

        // Delete group
        async function deleteGroup(id) {
            if (!confirm('Are you sure you want to delete this group?')) return;
            
            try {
                const response = await fetch(`/php-native/api/modifiers/groups.php?id=${id}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                if (data.success) {
                    loadModifierGroups();
                } else {
                    alert(data.message || 'Failed to delete group');
                }
            } catch (error) {
                console.error('Error deleting group:', error);
                alert('Error deleting group');
            }
        }

        // Show add modifier modal
        function showAddModifierModal(groupId) {
            document.getElementById('modifierId').value = '';
            document.getElementById('modifierGroupId').value = groupId;
            document.getElementById('modifierName').value = '';
            document.getElementById('modifierPrice').value = 0;
            document.getElementById('modifierActive').checked = true;
            document.getElementById('modifierModalTitle').textContent = 'Add Modifier';
            
            const modal = new bootstrap.Modal(document.getElementById('modifierModal'));
            modal.show();
        }

        // Edit modifier
        function editModifier(id, name, price, isActive) {
            document.getElementById('modifierId').value = id;
            document.getElementById('modifierName').value = name;
            document.getElementById('modifierPrice').value = price;
            document.getElementById('modifierActive').checked = isActive;
            document.getElementById('modifierModalTitle').textContent = 'Edit Modifier';
            
            const modal = new bootstrap.Modal(document.getElementById('modifierModal'));
            modal.show();
        }

        // Save modifier
        async function saveModifier() {
            const id = document.getElementById('modifierId').value;
            const modifier_group_id = document.getElementById('modifierGroupId').value;
            const name = document.getElementById('modifierName').value;
            const price = document.getElementById('modifierPrice').value;
            const is_active = document.getElementById('modifierActive').checked ? 1 : 0;

            if (!name || !modifier_group_id) {
                alert('Modifier name and group are required');
                return;
            }

            try {
                const url = '/php-native/api/modifiers/items.php';
                const method = id ? 'PUT' : 'POST';
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: id || null,
                        modifier_group_id,
                        name,
                        price,
                        is_active
                    })
                });
                const data = await response.json();
                if (data.success) {
                    alert(id ? 'Modifier updated successfully!' : 'Modifier created successfully!');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modifierModal'));
                    if (modal) {
                        modal.hide();
                    }
                    loadModifierGroups();
                } else {
                    alert(data.message || 'Failed to save modifier');
                }
            } catch (error) {
                console.error('Error saving modifier:', error);
                alert('Error saving modifier: ' + error.message);
            }
        }

        // Delete modifier
        async function deleteModifier(id) {
            if (!confirm('Are you sure you want to delete this modifier?')) return;
            
            try {
                const response = await fetch(`/php-native/api/modifiers/items.php?id=${id}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                if (data.success) {
                    loadModifierGroups();
                } else {
                    alert(data.message || 'Failed to delete modifier');
                }
            } catch (error) {
                console.error('Error deleting modifier:', error);
                alert('Error deleting modifier');
            }
        }
    </script>
</body>
</html>
