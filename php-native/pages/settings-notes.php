<?php
/**
 * Stickusteak POS - Item Notes Settings
 * Manage kitchen/bar notes for order items
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
    <title>Item Notes Settings - Stickusteak POS</title>
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
        
        .note-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            margin: 5px;
            font-weight: 500;
        }
        
        .category-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .category-btn {
            padding: 8px 20px;
            border-radius: 20px;
            border: 2px solid #dee2e6;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .category-btn:hover, .category-btn.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-chat-left-text me-2"></i>Item Notes / Remarks</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newNoteModal">
                        <i class="bi bi-plus-lg me-1"></i>Add Note
                    </button>
                </div>

                <!-- Info Card -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Item Notes</strong> are used to add special instructions to order items (e.g., "Tanpa Garam", "Pedas", "Tanpa Es").
                    These notes can be selected when adding items to orders in the POS system.
                </div>

                <!-- Category Filter -->
                <div class="category-filter">
                    <button class="category-btn active" onclick="filterNotes('all')">All</button>
                    <button class="category-btn" onclick="filterNotes('kitchen')">🍳 Kitchen</button>
                    <button class="category-btn" onclick="filterNotes('bar')">🍹 Bar</button>
                    <button class="category-btn" onclick="filterNotes('general')">📝 General</button>
                </div>

                <!-- Notes Grid -->
                <div class="content-card">
                    <div id="notesGrid">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Note Modal -->
    <div class="modal fade" id="newNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add/Edit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="noteForm">
                        <input type="hidden" id="noteId">
                        <div class="mb-3">
                            <label class="form-label">Note Name</label>
                            <input type="text" class="form-control" id="noteName" required placeholder="e.g., Tanpa Garam">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="noteCategory">
                                <option value="kitchen">🍳 Kitchen</option>
                                <option value="bar">🍹 Bar</option>
                                <option value="general">📝 General</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Color</label>
                            <select class="form-select" id="noteColor">
                                <option value="primary">Blue</option>
                                <option value="success">Green</option>
                                <option value="danger">Red</option>
                                <option value="warning">Yellow</option>
                                <option value="info">Cyan</option>
                                <option value="secondary">Gray</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="noteActive" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveNote()">
                        <i class="bi bi-check-lg me-1"></i>Save Note
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let allNotes = [];

        // Load notes
        async function loadNotes() {
            try {
                const response = await fetch('/php-native/api/settings/item-notes.php');
                const data = await response.json();
                
                if (data.success) {
                    allNotes = data.notes;
                    renderNotes(allNotes);
                }
            } catch (error) {
                console.error('Error loading notes:', error);
            }
        }

        // Render notes grid
        function renderNotes(notes) {
            const grid = document.getElementById('notesGrid');
            
            if (notes.length === 0) {
                grid.innerHTML = '<div class="text-center text-muted py-5"><p>No notes found</p></div>';
                return;
            }
            
            let html = '<div class="row">';
            notes.forEach(note => {
                const categoryIcon = note.category === 'kitchen' ? '🍳' : note.category === 'bar' ? '🍹' : '📝';
                html += `
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                            <div>
                                <span class="note-badge bg-${note.color} text-white">${note.name}</span>
                                <div class="small text-muted mt-1">
                                    ${categoryIcon} ${note.category.charAt(0).toUpperCase() + note.category.slice(1)}
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary" onclick="editNote(${note.id}, '${note.name}', '${note.category}', '${note.color}', ${note.is_active})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteNote(${note.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            grid.innerHTML = html;
        }

        // Filter notes by category
        function filterNotes(category) {
            document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            if (category === 'all') {
                renderNotes(allNotes);
            } else {
                const filtered = allNotes.filter(note => note.category === category);
                renderNotes(filtered);
            }
        }

        // Edit note
        function editNote(id, name, category, color, isActive) {
            document.getElementById('noteId').value = id;
            document.getElementById('noteName').value = name;
            document.getElementById('noteCategory').value = category;
            document.getElementById('noteColor').value = color;
            document.getElementById('noteActive').checked = isActive == 1;
            
            const modal = new bootstrap.Modal(document.getElementById('newNoteModal'));
            modal.show();
        }

        // Save note
        async function saveNote() {
            const id = document.getElementById('noteId').value;
            const name = document.getElementById('noteName').value;
            const category = document.getElementById('noteCategory').value;
            const color = document.getElementById('noteColor').value;
            const is_active = document.getElementById('noteActive').checked ? 1 : 0;
            
            if (!name) {
                alert('Please enter a note name');
                return;
            }
            
            try {
                const response = await fetch('/php-native/api/settings/item-notes.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, name, category, color, is_active })
                });
                const data = await response.json();
                
                if (data.success) {
                    alert(id ? 'Note updated successfully!' : 'Note created successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('newNoteModal')).hide();
                    document.getElementById('noteForm').reset();
                    document.getElementById('noteId').value = 0;
                    loadNotes();
                } else {
                    alert(data.message || 'Failed to save note');
                }
            } catch (error) {
                console.error('Error saving note:', error);
                alert('Error saving note');
            }
        }

        // Delete note
        async function deleteNote(id) {
            if (!confirm('Are you sure you want to delete this note?')) return;
            
            try {
                const response = await fetch(`/php-native/api/settings/item-notes.php?id=${id}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                
                if (data.success) {
                    loadNotes();
                } else {
                    alert(data.message || 'Failed to delete note');
                }
            } catch (error) {
                console.error('Error deleting note:', error);
                alert('Error deleting note');
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', loadNotes);
    </script>
</body>
</html>
