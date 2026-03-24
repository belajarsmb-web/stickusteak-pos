# 📁 Project Structure - RestoOPNCode POS

**Complete directory and file structure reference**

---

## Root Directory

```
C:\Project\restoopncode\
│
├── 📁 php-native/                 # Main Application (PHP)
├── 📁 database/                   # Database Scripts
├── 📁 mobile/                     # Mobile App (if exists)
├── 📁 backups/                    # Backup Storage
│
├── backup.bat                     # Create Backup
├── restore.bat                    # Restore Backup
├── cleanup-backups.bat            # Cleanup Old Backups
├── create-backup.ps1              # PowerShell Backup Script
├── restore-backup.ps1             # PowerShell Restore Script
│
├── README.md                      # Main Documentation
├── QUICKSTART.md                  # Quick Start Guide
├── BACKUP-GUIDE.md                # Backup Documentation
├── PROJECT-STATUS-REPORT.md       # Project Status
├── STRUCTURE.md                   # This File
│
└── ...                            # Other scripts & docs
```

---

## 📂 php-native/ Directory (Main Application)

```
php-native/
│
├── index.php                      # Entry Point (Router)
│                                  # Redirects to login or dashboard
│
├── 📁 config/
│   └── database.php               # Database Connection
│                                  # - PDO connection setup
│                                  # - Helper functions (dbQuery, dbExecute)
│                                  # - Transaction support
│
├── 📁 includes/
│   └── auth.php                   # Authentication Functions
│                                  # - checkAuth()
│                                  # - checkRole()
│                                  # - logout()
│
├── 📁 pages/                      # Application Pages (33 files)
│   │
│   ├── Authentication
│   │   └── login.php              # Login Page
│   │
│   ├── Dashboard & Main
│   │   ├── dashboard.php          # Main Dashboard
│   │   └── index.php              # (redirects to dashboard)
│   │
│   ├── POS System
│   │   ├── pos-tables.php         # Table Selection
│   │   ├── pos-order.php          # Order Taking (POS)
│   │   └── order-detail.php       # Order Details View
│   │
│   ├── Orders Management
│   │   ├── orders.php             # Orders List
│   │   └── print-history.php      # Print History
│   │
│   ├── Menu Management
│   │   ├── menu.php               # Menu Items CRUD
│   │   └── modifiers.php          # Modifiers CRUD
│   │
│   ├── Kitchen Display
│   │   ├── kds-kitchen.php        # Kitchen Display
│   │   ├── kds-bar.php            # Bar Display
│   │   ├── kitchen-ticket.php     # Kitchen Ticket View
│   │   └── bar-ticket.php         # Bar Ticket View
│   │
│   ├── Mobile Order
│   │   └── (see mobile/ folder)
│   │
│   ├── Inventory & Recipes
│   │   ├── inventory.php          # Inventory Management
│   │   └── recipes.php            # Recipe Management
│   │
│   ├── Customers & CRM
│   │   └── customers.php          # Customer Management
│   │
│   ├── Users & Shifts
│   │   ├── users.php              # User Management
│   │   └── shifts.php             # Shift Management
│   │
│   ├── Reports
│   │   ├── reports.php            # Main Reports
│   │   └── reports-tickets.php    # Ticket Reports
│   │
│   ├── Settings
│   │   ├── settings.php           # General Settings
│   │   ├── settings-receipt-template.php  # Receipt Template
│   │   ├── settings-printer.php   # Printer Settings
│   │   ├── settings-tax-service.php  # Tax & Service
│   │   └── settings-notes.php     # Notes Settings
│   │
│   ├── Receipt & Printing
│   │   ├── receipt.php            # Receipt Print Page
│   │   └── view-tickets.php       # View Tickets by Table
│   │
│   └── Debug & Test
│       ├── debug-orders.php       # Order Debug
│       ├── debug-ticket-flow.php  # Ticket Flow Debug
│       └── test-receipt-data.php  # Receipt Test
│
├── 📁 api/                        # REST API Endpoints (50+ files)
│   │
│   ├── 📁 auth/
│   │   ├── login.php              # POST: User login
│   │   ├── logout.php             # GET: User logout
│   │   └── profile.php            # GET: User profile
│   │
│   ├── 📁 users/
│   │   ├── index.php              # GET: List users
│   │   ├── store.php              # POST: Create user
│   │   ├── update.php             # PUT: Update user
│   │   └── delete.php             # DELETE: Delete user
│   │
│   ├── 📁 dashboard/
│   │   ├── stats.php              # GET: Dashboard statistics
│   │   └── recent-orders.php      # GET: Recent orders
│   │
│   ├── 📁 menu/
│   │   ├── index.php              # GET: List menu items
│   │   ├── store.php              # POST: Create menu item
│   │   ├── update.php             # PUT: Update menu item
│   │   ├── delete.php             # DELETE: Delete menu item
│   │   └── categories.php         # GET: List categories
│   │
│   ├── 📁 modifiers/
│   │   ├── index.php              # GET: List modifiers
│   │   ├── store.php              # POST: Create modifier
│   │   ├── update.php             # PUT: Update modifier
│   │   └── delete.php             # DELETE: Delete modifier
│   │
│   ├── 📁 orders/
│   │   ├── list.php               # GET: List orders
│   │   ├── detail.php             # GET: Order details
│   │   ├── store.php              # POST: Create order
│   │   ├── update.php             # PUT: Update order
│   │   └── void-item.php          # DELETE: Void order item
│   │
│   ├── 📁 payments/
│   │   ├── store.php              # POST: Record payment
│   │   └── process.php            # POST: Process payment
│   │
│   ├── 📁 pos/
│   │   ├── store-order.php        # POST: Create POS order
│   │   ├── complete-order.php     # POST: Complete order & payment
│   │   └── table-orders.php       # GET: Get table orders
│   │
│   ├── 📁 mobile/
│   │   └── submit-order.php       # POST: Submit mobile order
│   │
│   ├── 📁 kds/
│   │   ├── kitchen.php            # GET: Kitchen orders
│   │   ├── bar.php                # GET: Bar orders
│   │   └── update-status.php      # PUT: Update item status
│   │
│   ├── 📁 tickets/
│   │   ├── create.php             # POST: Create ticket
│   │   ├── get-by-table.php       # GET: Get ticket by table
│   │   └── list.php               # GET: List all tickets
│   │
│   ├── 📁 inventory/
│   │   ├── index.php              # GET: List inventory
│   │   ├── movements.php          # POST: Record movement
│   │   ├── low-stock.php          # GET: Low stock alerts
│   │   └── auto-stock-deduction.php  # Auto stock functions
│   │
│   ├── 📁 recipes/
│   │   ├── index.php              # GET: List recipes
│   │   ├── store.php              # POST: Create recipe
│   │   ├── update.php             # PUT: Update recipe
│   │   └── delete.php             # DELETE: Delete recipe
│   │
│   ├── 📁 shifts/
│   │   ├── active.php             # GET: Get active shift
│   │   ├── open.php               # POST: Open shift
│   │   ├── close.php              # POST: Close shift
│   │   └── list.php               # GET: Shift history
│   │
│   ├── 📁 customers/
│   │   ├── index.php              # GET: List customers
│   │   ├── store.php              # POST: Create customer
│   │   ├── update.php             # PUT: Update customer
│   │   └── delete.php             # DELETE: Delete customer
│   │
│   ├── 📁 reports/
│   │   ├── sales.php              # GET: Sales report
│   │   ├── inventory.php          # GET: Inventory report
│   │   └── tickets.php            # GET: Ticket report
│   │
│   ├── 📁 settings/
│   │   ├── receipt-settings.php   # GET/PUT: Receipt settings
│   │   ├── receipt-templates.php  # GET/PUT: Receipt templates
│   │   ├── printer-settings.php   # GET/PUT: Printer settings
│   │   └── tax-service.php        # GET/PUT: Tax & service
│   │
│   ├── 📁 tables/
│   │   ├── index.php              # GET: List tables
│   │   └── update-status.php      # PUT: Update table status
│   │
│   ├── 📁 notes/
│   │   └── settings.php           # GET/PUT: Notes settings
│   │
│   └── 📁 recipes/
│       └── (see recipes/ above)
│
├── 📁 mobile/                     # Mobile Order Pages
│   ├── order.php                  # Mobile Order Page (responsive)
│   ├── order-simple.php           # Simple Order Page
│   ├── order-with-modifiers.php  # Order with Modifiers
│   ├── submit-order.php           # Submit Order API
│   ├── test-order.php             # Test Order Page
│   └── test-submit.php            # Test Submit API
│
├── 📁 assets/                     # Static Assets
│   ├── 📁 css/
│   │   ├── style.css              # Main Stylesheet
│   │   ├── pos.css                # POS Styles
│   │   ├── kds.css                # KDS Styles
│   │   ├── receipt.css            # Receipt Print Styles
│   │   └── mobile.css             # Mobile Styles
│   │
│   ├── 📁 js/
│   │   ├── main.js                # Main JavaScript
│   │   ├── pos.js                 # POS Functions
│   │   ├── kds.js                 # KDS Functions
│   │   ├── receipt.js             # Receipt Functions
│   │   └── mobile.js              # Mobile Functions
│   │
│   └── 📁 images/
│       ├── 📁 menu/               # Menu Item Images
│       ├── 📁 logo/               # Restaurant Logos
│       └── 📁 icons/              # Custom Icons
│
└── 📁 uploads/                    # User Uploads
    ├── 📁 menu/                   # Menu Images
    │   ├── item-1.jpg
    │   ├── item-2.jpg
    │   └── ...
    │
    └── 📁 logo/                   # Logo Images
        ├── logo-1.png
        └── ...
```

---

## 📂 database/ Directory

```
database/
│
├── Core Schema
│   ├── schema.sql                 # Complete Database Schema
│   ├── seed.sql                   # Sample Data (Users, Roles, etc.)
│   └── seed_sample_data.sql       # Additional Sample Data
│
├── Table Creation Scripts
│   ├── create-customers-table.sql
│   ├── create-modifiers.sql
│   ├── create-payment-methods.sql
│   ├── create-tickets-table.sql
│   ├── create-void-reasons.sql
│   └── create-item-notes.sql
│
├── Migration Scripts
│   ├── add-customer-columns.sql   # Add customer_name, customer_phone
│   ├── add-payment-columns.sql    # Add payment columns
│   ├── add-receipt-columns.sql    # Add receipt template columns
│   ├── fix-customer-columns.sql   # Fix customer data
│   ├── fix-payment-tables.sql    # Fix payment tables
│   ├── fix-menu-images.sql        # Fix menu images
│   ├── fix-item-notes-table.sql   # Fix notes table
│   ├── fix-notes.sql              # Fix notes data
│   └── cleanup-duplicate-modifiers.sql
│
├── Feature Migrations
│   ├── shift-balance-migration.sql    # Shift Management
│   ├── unit-conversion-migration.sql  # Unit Conversion (pending)
│   ├── mobile-order-migration.sql     # Mobile Order
│   └── update-receipt-header.sql      # Receipt Header
│
├── Sample Data
│   ├── sample-steak-menu.sql          # Steak Menu Sample
│   ├── sample-steak-menu-final.sql    # Final Steak Menu
│   ├── sample-inventory-simple.sql    # Simple Inventory
│   ├── sample-inventory-recipes.sql   # Inventory with Recipes
│   ├── quick-sample-menu.sql          # Quick Menu Sample
│   └── setup-steak-modifiers.sql      # Steak Modifiers
│
├── Maintenance Scripts
│   ├── delete-all-transactions.sql    # Clear All Transactions
│   ├── check-tables.sql               # Check Tables Structure
│   ├── check-orders-table.sql         # Check Orders Structure
│   ├── check-modifiers.sql            # Check Modifiers
│   ├── check-modifiers-data.sql       # Check Modifiers Data
│   ├── check-item-notes-structure.sql # Check Notes Structure
│   └── seed_transactions.sql          # Seed Test Transactions
│
└── Documentation
    └── README-STEAK-MENU.md       # Steak Menu Documentation
```

---

## 🔧 Utility Scripts

### Backup Scripts
```
backup.bat                     # Create Backup (double-click)
restore.bat                    # Restore Backup (double-click)
cleanup-backups.bat            # Cleanup Old Backups
create-backup.ps1              # PowerShell Backup Logic
restore-backup.ps1             # PowerShell Restore Logic
cleanup-old-backups.ps1        # PowerShell Cleanup Logic
```

### Setup Scripts
```
setup-laragon.bat              # Laragon Setup
start_with_apache.bat          # Apache Startup
setup-steak-modifiers.bat      # Setup Steak Modifiers
```

### Database Scripts
```
clear-all-data.bat             # Delete All Transactions
fix-notes.bat                  # Fix Notes Data
fix-payments.bat               # Fix Payment Data
add-receipt-columns.bat        # Add Receipt Columns
update-receipt-header.bat      # Update Receipt Header
run-sample-data.bat            # Import Sample Data
run-compatible-data.bat        # Import Compatible Data
run-simple-data.bat            # Import Simple Data
```

### Utility Scripts
```
extract_log.js                 # Log Extractor
fix-preview-receipt.js         # Receipt Preview Fix
```

---

## 📄 Documentation Files

```
README.md                      # Main Documentation
QUICKSTART.md                  # Quick Start Guide (5 min setup)
BACKUP-GUIDE.md                # Backup System Guide
PROJECT-STATUS-REPORT.md       # Current Project Status
STRUCTURE.md                   # This File (Structure)

COMPLETE-IMPLEMENTATION-GUIDE.md    # Implementation Guide
COMPLETE-TEST-REPORT.md             # Test Report
CRITICAL-FEATURES-PROGRESS.md       # Features Progress
CLEANUP-SUMMARY.md                  # Cleanup Summary

FILTER-NOTES-FIX.md                 # Notes Filter Fix
FIX-ITEM-NOTES-ERROR.md             # Item Notes Error Fix
FIX-REPORT.md                       # Fix Report
INVENTORY-MANAGEMENT-COMPLETE.md    # Inventory Complete
ISSUES-FIXES.md                     # Issues & Fixes

KDS-TESTING-GUIDE.md                # KDS Testing Guide
MOBILE-ORDER-*.md                   # Mobile Order Documentation (7 files)
MODIFIER-MANAGEMENT-FIX.md          # Modifier Fix
MODIFIERS-COMPACT-LAYOUT.md         # Modifiers Layout
NOTES-FIX-COMPLETE.md               # Notes Fix Complete
ORDER-PERSISTENCE-FIX.md            # Order Persistence Fix

PHP-NATIVE-REPORT.md                # PHP Native Report
PHP-NATIVE-SETUP-GUIDE.md           # PHP Native Setup
RECEIPT-HEADER-GUIDE.md             # Receipt Header Guide

STEAK-MODIFIERS-MOBILE.md           # Steak Modifiers Mobile
STEAK-MODIFIERS-SETUP-COMPLETE.md   # Steak Modifiers Setup

TEST-REPORT.md                      # Test Report
TICKET-SYSTEM-IMPLEMENTATION.md     # Ticket System
VOID-FUNCTION-FIX.md                # Void Function Fix

README-COMPLETE.md                  # Complete README
README.txt                          # Simple README
README-BACKUP.md                    # Backup README
BACKUP-README.txt                   # Backup Quick Reference
```

---

## 🎯 Key Files Explained

### Entry Points

| File | Purpose | Access URL |
|------|---------|------------|
| `php-native/index.php` | Main router | http://localhost/php-native/ |
| `php-native/pages/login.php` | Login page | http://localhost/php-native/pages/login.php |
| `php-native/pages/dashboard.php` | Dashboard | http://localhost/php-native/pages/dashboard.php |
| `php-native/pages/pos-tables.php` | Table selection | http://localhost/php-native/pages/pos-tables.php |
| `php-native/mobile/order.php` | Mobile order | http://localhost/php-native/mobile/order.php |

### Configuration Files

| File | Purpose |
|------|---------|
| `php-native/config/database.php` | Database connection & helpers |
| `php-native/includes/auth.php` | Authentication functions |

### Core APIs

| Endpoint | Purpose |
|----------|---------|
| `POST /api/auth/login.php` | User login |
| `POST /api/pos/store-order.php` | Create POS order |
| `POST /api/pos/complete-order.php` | Complete order & payment |
| `POST /api/mobile/submit-order.php` | Submit mobile order |
| `GET /api/kds/kitchen.php` | Get kitchen orders |
| `GET /api/tickets/get-by-table.php` | Get ticket by table |

### Database Scripts

| File | Purpose |
|------|---------|
| `database/schema.sql` | Complete database schema |
| `database/seed.sql` | Sample data (users, roles) |
| `database/seed_sample_data.sql` | Additional sample data |

---

## 📊 File Count Summary

| Category | Count |
|----------|-------|
| **PHP Pages** | 33 files |
| **API Endpoints** | 50+ files |
| **JavaScript Files** | 20+ files |
| **CSS Files** | 10+ files |
| **SQL Scripts** | 36 files |
| **Batch Scripts** | 19 files |
| **PowerShell Scripts** | 3 files |
| **Documentation** | 38 Markdown files |
| **Total Files** | **200+ files** |

---

## 🔍 Finding Files

### Need to modify...

**Login page?**
→ `php-native/pages/login.php`

**Dashboard?**
→ `php-native/pages/dashboard.php`

**POS order taking?**
→ `php-native/pages/pos-order.php`

**Receipt printing?**
→ `php-native/pages/receipt.php`

**Kitchen display?**
→ `php-native/pages/kds-kitchen.php`

**Mobile order?**
→ `php-native/mobile/order.php`

**Create order API?**
→ `php-native/api/pos/store-order.php`

**Payment API?**
→ `php-native/api/payments/process.php`

**Database schema?**
→ `database/schema.sql`

**Sample data?**
→ `database/seed.sql`

---

**Last Updated:** March 21, 2026  
**Version:** 1.0.0  
**Total Size:** ~375 MB (with backups)
