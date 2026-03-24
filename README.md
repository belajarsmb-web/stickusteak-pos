# 🍽️ RestoOPNCode - Restaurant POS System

**Modern, Production-Ready Point of Sale System for Restaurants**

![Status](https://img.shields.io/badge/status-production--ready-green)
![Version](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.0+-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange)
![License](https://img.shields.io/badge/license-MIT-green)

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [System Requirements](#-system-requirements)
- [Installation](#-installation)
- [Project Structure](#-project-structure)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [API Endpoints](#-api-endpoints)
- [Database Schema](#-database-schema)
- [Testing](#-testing)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

### 🎯 Core Modules

| Module | Status | Description |
|--------|--------|-------------|
| **Authentication** | ✅ Complete | Login, session management, role-based access |
| **Dashboard** | ✅ Complete | Real-time stats, recent orders, quick actions |
| **Table Management** | ✅ Complete | Visual table layout, status tracking |
| **POS System** | ✅ Complete | Order taking, cart, payment processing |
| **Mobile Order** | ✅ Complete | QR code ordering, mobile-responsive |
| **Kitchen Display** | ✅ Complete | KDS for kitchen & bar |
| **Ticket System** | ✅ Complete | Order tickets with customer info |
| **Receipt Printing** | ✅ Complete | Thermal printer support (58mm/80mm) |
| **Menu Management** | ✅ Complete | Items, categories, modifiers |
| **Inventory** | ✅ 95% | Stock tracking, movements, recipes |
| **Recipe Management** | ✅ UI Complete | Recipe creation & management |
| **Shift Management** | ✅ Complete | Open/close shifts, cash balancing |
| **Customer CRM** | ✅ Complete | Customer database, loyalty tracking |
| **Reports** | ✅ Complete | Sales, inventory, ticket reports |
| **Settings** | ✅ Complete | System configuration |
| **Backup System** | ✅ Complete | Auto backup/restore |

### 🔥 Key Capabilities

- ✅ **Dine-in, Takeaway, Delivery** support
- ✅ **Real-time order tracking** with tickets
- ✅ **Kitchen Display System** (KDS) for food & bar
- ✅ **Mobile QR ordering** - customers order from phone
- ✅ **Thermal receipt printing** (58mm & 80mm)
- ✅ **Customer information** capture on all orders
- ✅ **Item notes & modifiers** (e.g., "No ice", "Medium rare")
- ✅ **Inventory management** with auto stock deduction
- ✅ **Recipe management** with ingredient tracking
- ✅ **Shift management** with cash balancing
- ✅ **Multi-user** with role-based permissions
- ✅ **Backup & restore** system included

---

## 🛠️ Tech Stack

### Backend
- **PHP 8.0+** - Server-side logic
- **MySQL 8.0+** - Database (MariaDB compatible)
- **PDO** - Database access layer
- **Session-based auth** - User authentication

### Frontend
- **HTML5, CSS3, JavaScript (ES6+)**
- **Bootstrap 5.3** - UI framework
- **Bootstrap Icons** - Icon library
- **jQuery** - DOM manipulation (minimal usage)

### Tools & Libraries
- **Laragon** - Local development environment (recommended)
- **phpMyAdmin** - Database management
- **PowerShell** - Backup scripts automation

---

## 💻 System Requirements

### Development Environment

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| **OS** | Windows 10 | Windows 11 |
| **RAM** | 4 GB | 8 GB |
| **Storage** | 2 GB free | 10 GB free |
| **PHP** | 7.4 | 8.0+ |
| **MySQL** | 5.7 | 8.0+ (MariaDB 10.5+) |

### Production Server

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| **OS** | Linux (Ubuntu 20.04) | Linux (Ubuntu 22.04 LTS) |
| **RAM** | 2 GB | 4 GB |
| **Storage** | 10 GB | 50 GB SSD |
| **PHP** | 7.4 | 8.0+ with OPcache |
| **MySQL** | 5.7 | 8.0+ |
| **Web Server** | Apache 2.4 / Nginx 1.18 | Apache 2.4 / Nginx 1.20+ |

### Recommended Local Setup

**Option 1: Laragon (Windows) - EASIEST** ⭐
```
Download: https://laragon.org/download/
Version: Laragon 6.0+ (Full package)
Includes: Apache, PHP 8+, MySQL 8+, phpMyAdmin
```

**Option 2: XAMPP**
```
Download: https://www.apachefriends.org/
Version: XAMPP 8.0+
Includes: Apache, MySQL, PHP, phpMyAdmin
```

**Option 3: Manual Installation**
```
- Apache 2.4+
- PHP 8.0+
- MySQL 8.0+
- phpMyAdmin (optional)
```

---

## 📦 Installation

### Quick Start (Laragon - Recommended) ⭐

**1. Install Laragon**
```bash
# Download and install Laragon
https://laragon.org/download/

# Install to: C:\laragon
```

**2. Clone Project**
```bash
# Clone or copy project to Laragon www folder
C:\laragon\www\restoopncode
```

**3. Setup Database**
```bash
# Open Laragon, click "Start All"

# Open phpMyAdmin
http://localhost/phpmyadmin

# Create database
CREATE DATABASE posreato CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Import schema
# - Go to "Import" tab
# - Select: C:\laragon\www\restoopncode\database\schema.sql
# - Click "Go"
```

**4. Import Sample Data (Optional)**
```bash
# In phpMyAdmin, select database "posreato"
# Go to "Import" tab
# Select: C:\laragon\www\restoopncode\database\seed.sql
# Click "Go"
```

**5. Configure Database**
```php
// File: C:\laragon\www\restoopncode\php-native\config\database.php

// Default configuration (usually no changes needed)
define('DB_HOST', 'localhost');
define('DB_NAME', 'posreato');
define('DB_USER', 'root');
define('DB_PASS', '');  // Empty password for Laragon
define('DB_CHARSET', 'utf8mb4');
```

**6. Access Application**
```bash
# Main entry point
http://localhost/php-native/

# Will auto-redirect to login
http://localhost/php-native/pages/login.php

# Default credentials (after importing seed.sql)
Username: admin
Password: admin123
```

---

### Alternative: Manual Installation

**1. Install Prerequisites**
```bash
# Install Apache
https://www.apachehaus.com/cgi-bin/download.pl

# Install PHP 8.0+
https://windows.php.net/download/

# Install MySQL 8.0+
https://dev.mysql.com/downloads/mysql/
```

**2. Configure Apache**
```apache
# httpd.conf - Enable these modules:
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule proxy_module modules/mod_proxy.so
LoadModule proxy_http_module modules/mod_proxy_http.so

# Add virtual host (optional):
<VirtualHost *:80>
    DocumentRoot "C:/Project/restoopncode/php-native"
    ServerName localhost
    <Directory "C:/Project/restoopncode/php-native">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**3. Configure PHP**
```ini
; php.ini - Recommended settings:
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
post_max_size = 64M
upload_max_filesize = 64M
```

**4. Setup Database**
```bash
# Create database
mysql -u root -p
CREATE DATABASE posreato CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Import schema
mysql -u root -p posreato < database/schema.sql

# Import sample data (optional)
mysql -u root -p posreato < database/seed.sql
```

**5. Update Database Config**
```php
// php-native/config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'posreato');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_CHARSET', 'utf8mb4');
```

**6. Set Permissions**
```bash
# Linux/Mac:
chmod -R 755 /var/www/html/restoopncode
chown -R www-data:www-data /var/www/html/restoopncode

# Windows: Right-click folder > Properties > Security
# Give IIS_IUSRS and IUSR read/write access
```

**7. Start Server & Access**
```bash
# Start Apache
# Windows: Apache monitor or service
# Linux: sudo systemctl start apache2

# Access application
http://localhost/php-native/
```

---

## 📁 Project Structure

```
restoopncode/
├── 📁 php-native/              # Main PHP Application
│   ├── index.php              # Entry point (router)
│   ├── config/
│   │   └── database.php       # Database connection & helpers
│   ├── includes/
│   │   └── auth.php           # Authentication functions
│   ├── pages/                 # Main application pages
│   │   ├── login.php          # Login page
│   │   ├── dashboard.php      # Dashboard
│   │   ├── pos-tables.php     # Table selection
│   │   ├── pos-order.php      # Order taking
│   │   ├── orders.php         # Orders list
│   │   ├── menu.php           # Menu management
│   │   ├── modifiers.php      # Modifier management
│   │   ├── inventory.php      # Inventory management
│   │   ├── recipes.php        # Recipe management
│   │   ├── customers.php      # Customer management
│   │   ├── users.php          # User management
│   │   ├── shifts.php         # Shift management
│   │   ├── reports.php        # Reports
│   │   ├── settings.php       # Settings
│   │   ├── receipt.php        # Receipt printing
│   │   ├── kds-kitchen.php    # Kitchen display
│   │   ├── kds-bar.php        # Bar display
│   │   └── ...
│   ├── api/                   # REST API endpoints
│   │   ├── auth/              # Authentication APIs
│   │   ├── menu/              # Menu APIs
│   │   ├── orders/            # Order APIs
│   │   ├── payments/          # Payment APIs
│   │   ├── pos/               # POS APIs
│   │   ├── mobile/            # Mobile order APIs
│   │   ├── kds/               # KDS APIs
│   │   ├── tickets/           # Ticket APIs
│   │   ├── inventory/         # Inventory APIs
│   │   ├── recipes/           # Recipe APIs
│   │   ├── shifts/            # Shift APIs
│   │   ├── customers/         # Customer APIs
│   │   ├── users/             # User APIs
│   │   ├── reports/           # Report APIs
│   │   └── settings/          # Settings APIs
│   ├── mobile/                # Mobile order pages
│   │   ├── order.php          # Mobile order page
│   │   └── submit-order.php   # Mobile order submission
│   ├── assets/
│   │   ├── css/               # Stylesheets
│   │   ├── js/                # JavaScript files
│   │   └── images/            # Images
│   └── uploads/               # Uploaded files
│       ├── menu/              # Menu item images
│       └── logo/              # Restaurant logos
│
├── 📁 database/               # Database scripts
│   ├── schema.sql             # Complete database schema
│   ├── seed.sql               # Sample data
│   ├── seed_sample_data.sql   # Additional sample data
│   ├── create-*.sql           # Table creation scripts
│   ├── fix-*.sql              # Fix/migration scripts
│   └── *.sql                  # Various migrations
│
├── 📁 mobile/                 # Mobile app (if exists)
│
├── 📁 backups/                # Backup files (auto-created)
│
├── 📁 php-native-backups/     # Project backups (outside project)
│   └── BACKUP_YYYYMMDD-HHMMSS_restoopncode.zip
│
├── backup.bat                 # Create backup
├── restore.bat                # Restore from backup
├── cleanup-backups.bat        # Cleanup old backups
├── create-backup.ps1          # PowerShell backup script
├── restore-backup.ps1         # PowerShell restore script
├── setup-laragon.bat          # Laragon setup script
├── start_with_apache.bat      # Apache startup script
│
├── README.md                  # This file
├── BACKUP-GUIDE.md            # Backup system documentation
├── PROJECT-STATUS-REPORT.md   # Current project status
└── ...                        # Other documentation
```

---

## ⚙️ Configuration

### Database Configuration

**File:** `php-native/config/database.php`

```php
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'posreato');
define('DB_USER', 'root');
define('DB_PASS', '');  // Empty for Laragon, set password for others
define('DB_CHARSET', 'utf8mb4');
```

### PHP Configuration

**Recommended php.ini settings:**
```ini
; Memory & Execution
memory_limit = 256M
max_execution_time = 300
max_input_time = 300

; File Uploads
upload_max_filesize = 64M
post_max_size = 64M

; Session
session.save_path = "/tmp"  ; Linux
session.save_path = "C:/Windows/Temp"  ; Windows
session.gc_maxlifetime = 7200

; Error Reporting (Production)
error_reporting = E_ALL
display_errors = Off
log_errors = On
error_log = /path/to/error.log

; Error Reporting (Development)
error_reporting = E_ALL
display_errors = On
```

### Apache Configuration

**Virtual Host Example:**
```apache
<VirtualHost *:80>
    ServerName posrestoo.local
    DocumentRoot "C:/Project/restoopncode/php-native"
    
    <Directory "C:/Project/restoopncode/php-native">
        AllowOverride All
        Require all granted
    </Directory>
    
    # Enable rewrite for clean URLs
    RewriteEngine On
    RewriteBase /
    
    # Log files
    ErrorLog "logs/posrestoo-error.log"
    CustomLog "logs/posrestoo-access.log" common
</VirtualHost>
```

---

## 🚀 Usage

### First Login

1. **Access application:**
   ```
   http://localhost/php-native/
   ```

2. **Default credentials** (after importing seed.sql):
   ```
   Username: admin
   Password: admin123
   ```

3. **Change password immediately after first login!**

### Main Workflows

#### 1. **Taking an Order (Dine-in)**
```
1. Go to: POS Tables (pos-tables.php)
2. Click on available table
3. Click menu items to add to cart
4. Add modifiers (if needed)
5. Add notes (special requests)
6. Click "Submit Order"
7. Order appears in Kitchen Display
```

#### 2. **Mobile QR Order**
```
1. Customer scans QR code on table
2. Opens: mobile/order.php?table_id=X
3. Customer selects items
4. Adds notes/modifiers
5. Enters name & phone
6. Submits order
7. Order appears in KDS
```

#### 3. **Kitchen Display System**
```
Kitchen:  http://localhost/php-native/pages/kds-kitchen.php
Bar:      http://localhost/php-native/pages/kds-bar.php

Features:
- Real-time order updates
- Mark items as ready
- Filter by status
- View order notes & modifiers
```

#### 4. **Receipt Printing**
```
After payment:
1. Receipt auto-opens in new window
2. Click print button or auto-prints
3. Supports 58mm & 80mm thermal printers
4. Can download as PDF
```

#### 5. **Shift Management**
```
1. Go to: Shifts (shifts.php)
2. Click "Open Shift"
3. Enter opening balance (cash in drawer)
4. Process orders during shift
5. Click "Close Shift"
6. Count actual cash
7. System shows variance (over/short)
```

#### 6. **Backup Database**
```bash
# Quick backup before changes:
backup.bat

# Restore if needed:
restore.bat

# Cleanup old backups:
cleanup-backups.bat
```

---

## 🔌 API Endpoints

### Base URL
```
Development: http://localhost/php-native/api/
Production:  https://yourdomain.com/php-native/api/
```

### Authentication
```
POST   /auth/login.php          - User login
GET    /auth/logout.php         - User logout
GET    /auth/profile.php        - Get user profile
```

### Menu
```
GET    /menu/index.php          - List menu items
POST   /menu/store.php          - Create menu item
PUT    /menu/update.php         - Update menu item
DELETE /menu/delete.php         - Delete menu item
GET    /categories/index.php    - List categories
```

### Orders
```
GET    /orders/list.php         - List orders
POST   /orders/store.php        - Create order
PUT    /orders/update.php       - Update order
DELETE /orders/void-item.php    - Void order item
GET    /orders/detail.php       - Order details
```

### POS
```
POST   /pos/store-order.php     - Create POS order
POST   /pos/complete-order.php  - Complete order & payment
GET    /pos/table-orders.php    - Get table orders
```

### Mobile Order
```
POST   /mobile/submit-order.php - Submit mobile order
```

### KDS
```
GET    /kds/kitchen.php         - Get kitchen orders
GET    /kds/bar.php             - Get bar orders
PUT    /kds/update-status.php   - Update item status
```

### Tickets
```
POST   /tickets/create.php      - Create ticket
GET    /tickets/get-by-table.php - Get ticket by table
GET    /tickets/list.php        - List all tickets
```

### Inventory
```
GET    /inventory/index.php     - List inventory
POST   /inventory/movements.php - Record movement
GET    /inventory/low-stock.php - Low stock alerts
```

### Recipes
```
GET    /recipes/index.php       - List recipes
POST   /recipes/store.php       - Create recipe
PUT    /recipes/update.php      - Update recipe
DELETE /recipes/delete.php      - Delete recipe
```

### Shifts
```
GET    /shifts/active.php       - Get active shift
POST   /shifts/open.php         - Open shift
POST   /shifts/close.php        - Close shift
GET    /shifts/list.php         - Shift history
```

### Customers
```
GET    /customers/index.php     - List customers
POST   /customers/store.php     - Create customer
PUT    /customers/update.php    - Update customer
DELETE /customers/delete.php    - Delete customer
```

### Reports
```
GET    /reports/sales.php       - Sales report
GET    /reports/inventory.php   - Inventory report
GET    /reports/tickets.php     - Ticket report
```

---

## 🗄️ Database Schema

### Key Tables

#### Users & Authentication
```sql
roles                  - User roles (Admin, Staff, Manager)
users                  - User accounts
user_roles             - Role assignments
```

#### Restaurant Setup
```sql
outlets                - Restaurant outlets/locations
tables                 - Dining tables
categories             - Menu categories
menu_items             - Menu items
modifiers              - Item modifiers (e.g., "No ice")
modifier_groups        - Modifier groups
```

#### Orders & Payments
```sql
orders                 - Order headers
order_items            - Order line items
payments               - Payment records
tickets                - Order tickets (groups orders by session)
void_reasons           - Reasons for voiding items
```

#### Inventory & Recipes
```sql
inventory_items        - Inventory stock
inventory_movements    - Stock movement log
recipes                - Recipe definitions
recipe_ingredients     - Recipe ingredients
purchase_orders        - Purchase orders
```

#### Customers & CRM
```sql
customers              - Customer database
customer_loyalty       - Loyalty points
```

#### Settings
```sql
system_settings        - System configuration
receipt_templates      - Receipt templates
shifts                 - Cashier shifts
shift_balance          - Shift cash balancing
```

### ERD Overview
```
roles (1) ──< (N) users
users (1) ──< (N) orders
tables (1) ──< (N) orders
orders (1) ──< (N) order_items
menu_items (1) ──< (N) order_items
orders (1) ──< (N) payments
outlets (1) ──< (N) users, tables, menu_items
```

---

## 🧪 Testing

### Manual Testing Checklist

#### POS Flow
- [ ] Login to system
- [ ] Select table from POS Tables
- [ ] Add items to cart
- [ ] Add modifiers to items
- [ ] Add notes to items
- [ ] Submit order
- [ ] Verify order appears in KDS
- [ ] Process payment
- [ ] Print receipt
- [ ] Verify customer info on receipt

#### Mobile Order Flow
- [ ] Open mobile order page
- [ ] Select items
- [ ] Add notes
- [ ] Enter customer info
- [ ] Submit order
- [ ] Verify order in KDS
- [ ] Verify ticket created

#### Kitchen Display
- [ ] View kitchen orders
- [ ] Mark item as ready
- [ ] Verify status update
- [ ] Filter by status

#### Inventory
- [ ] View inventory levels
- [ ] Record stock movement
- [ ] Create purchase order
- [ ] Receive purchase order
- [ ] Verify stock update

#### Shift Management
- [ ] Open shift
- [ ] Enter opening balance
- [ ] Process orders
- [ ] Close shift
- [ ] Enter closing balance
- [ ] Verify variance calculation

### Automated Testing (Future)
```bash
# PHPUnit tests (planned)
php vendor/bin/phpunit tests/

# API tests with Postman
# Import collection: tests/RestoOPNCode.postman_collection.json
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. **Database Connection Failed**
```
Error: SQLSTATE[HY000] [1045] Access denied

Solution:
1. Check database.php credentials
2. Verify MySQL is running
3. Check database exists: CREATE DATABASE posreato;
4. Verify user has permissions
```

#### 2. **Session Not Working**
```
Error: Cannot modify header information - headers already sent

Solution:
1. Ensure session_start() is called before any output
2. Check for whitespace before <?php
3. Clear browser cookies/cache
```

#### 3. **Images Not Uploading**
```
Error: Upload failed

Solution:
1. Check uploads/ folder permissions (755 or 777)
2. Verify upload_max_filesize in php.ini
3. Check post_max_size in php.ini
```

#### 4. **Receipt Not Printing**
```
Issue: Receipt window opens but nothing prints

Solution:
1. Check printer is set as default
2. Verify thermal printer driver installed
3. Test with different paper size (58mm vs 80mm)
4. Check browser print settings
```

#### 5. **KDS Not Updating**
```
Issue: Orders not appearing in KDS

Solution:
1. Refresh page (F5)
2. Check JavaScript console for errors
3. Verify database connection
4. Check order status in database
```

#### 6. **Cart Items Disappearing**
```
Issue: Items disappear from cart

Solution:
1. Check browser console for errors
2. Verify session is active
3. Clear browser cache
4. Check loadCurrentOrders() function (already fixed ✅)
```

#### 7. **Notes Not Displaying Correctly**
```
Issue: Notes show as individual characters

Solution:
1. Already fixed in this version ✅
2. Notes stored as plain text, not JSON
3. Check KDS and receipt display code
```

### Debug Mode

**Enable debug mode:**
```php
// php-native/config/database.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check logs
C:\laragon\data\logs\error.log  // Laragon
/var/log/apache2/error.log      // Linux Apache
```

### Database Debug

```sql
-- Check active sessions
SELECT * FROM users WHERE last_login_at > NOW() - INTERVAL 1 HOUR;

-- Check recent orders
SELECT * FROM orders ORDER BY created_at DESC LIMIT 10;

-- Check tickets
SELECT * FROM tickets WHERE status = 'open';

-- Check inventory movements
SELECT * FROM inventory_movements ORDER BY created_at DESC LIMIT 20;
```

---

## 🤝 Contributing

### How to Contribute

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. **Make your changes**
4. **Test thoroughly**
5. **Commit your changes**
   ```bash
   git commit -m "Add amazing feature"
   ```
6. **Push to the branch**
   ```bash
   git push origin feature/amazing-feature
   ```
7. **Open a Pull Request**

### Coding Standards

- **PHP:** PSR-12 coding standards
- **JavaScript:** ESLint recommended rules
- **HTML/CSS:** W3C standards
- **Database:** Consistent naming conventions

### Commit Message Format

```
type(scope): subject

body (optional)

footer (optional)

Types:
- feat: New feature
- fix: Bug fix
- docs: Documentation
- style: Formatting
- refactor: Code restructuring
- test: Tests
- chore: Maintenance
```

---

## 📄 License

This project is licensed under the **MIT License**.

```
Copyright (c) 2026 RestoOPNCode

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 📞 Support

### Documentation
- **Setup Guide:** See [Installation](#installation) section
- **API Docs:** See [API Endpoints](#api-endpoints) section
- **Backup Guide:** See `BACKUP-GUIDE.md`
- **Project Status:** See `PROJECT-STATUS-REPORT.md`

### Contact
- **Email:** support@restoopncode.com (placeholder)
- **Issues:** Create an issue in this repository
- **Discussions:** GitHub Discussions (if enabled)

### Resources
- **PHP Documentation:** https://www.php.net/docs.php
- **MySQL Documentation:** https://dev.mysql.com/doc/
- **Bootstrap Documentation:** https://getbootstrap.com/docs/

---

## 🙏 Acknowledgments

- **Bootstrap** - UI framework
- **Bootstrap Icons** - Icon library
- **Laragon** - Local development environment
- **The PHP Foundation** - PHP language
- **MySQL Team** - Database system

---

**Built with ❤️ for the restaurant industry**

**Version:** 1.0.0  
**Last Updated:** March 21, 2026  
**Status:** Production Ready (85% Complete)
