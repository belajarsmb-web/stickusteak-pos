# PHP Native POS System - Setup & Installation Guide

**Restaurant POS System - PHP Native + MySQL**  
**Version:** 1.0  
**Last Updated:** March 18, 2026

---

## 📋 System Requirements

### Minimum Requirements:
- **PHP:** 7.4 or higher
- **MySQL:** 5.7 or higher / MariaDB 10.4+
- **Web Server:** Apache 2.4+ or Nginx
- **Extensions:** PDO, PDO_MYSQL, JSON
- **RAM:** 2GB
- **Storage:** 500MB

### Recommended:
- **PHP:** 8.0+
- **MySQL:** 8.0+ / MariaDB 10.5+
- **RAM:** 4GB
- **Web Server:** Apache with mod_rewrite enabled

---

## 🚀 Quick Start (Laragon - Windows)

### Step 1: Install Laragon
1. Download Laragon from [laragon.org](https://laragon.org)
2. Install to `C:\laragon`
3. Start Laragon → Click "Start All"

### Step 2: Setup Project
```bash
# Copy project to Laragon
Copy folder to: C:\laragon\www\pos

# Or use symlink (admin command prompt)
mklink /D C:\laragon\www\pos C:\Project\restoopncode\php-native
```

### Step 3: Create Database
**Option A: Using Laragon GUI**
1. Open Laragon → Click "Database"
2. Right-click → "Create Database"
3. Name: `posreato`
4. Collation: `utf8mb4_unicode_ci`

**Option B: Using SQL**
```bash
# Open Laragon Terminal (or MySQL command line)
mysql -u root -e "CREATE DATABASE posreato CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Step 4: Import Database Schema
```bash
# Import main schema
mysql -u root posreato < C:\laragon\www\pos\database\schema.sql

# Import sample data (optional)
mysql -u root posreato < C:\laragon\www\pos\database\seed.sql

# Import payment methods
mysql -u root posreato < C:\laragon\www\pos\database\create-payment-methods.sql

# Import void reasons
mysql -u root posreato < C:\laragon\www\pos\database\create-void-reasons.sql

# Import modifiers (optional)
mysql -u root posreato < C:\laragon\www\pos\database\create-modifiers.sql
```

### Step 5: Configure Database Connection
Edit file: `C:\laragon\www\pos\config\database.php`

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'posreato');
define('DB_USER', 'root');
define('DB_PASS', ''); // Leave empty for Laragon default
define('DB_CHARSET', 'utf8mb4');
```

### Step 6: Access Application
```
Login Page:     http://localhost/pos/pages/login.php
Dashboard:      http://localhost/pos/pages/dashboard.php
POS Tables:     http://localhost/pos/pages/pos-tables.php
KDS Kitchen:    http://localhost/pos/pages/kds-kitchen.php
KDS Bar:        http://localhost/pos/pages/kds-bar.php
```

### Step 7: Default Login
```
Username: admin
Password: admin123
```

---

## 🐧 Setup on Linux (LAMP Stack)

### Step 1: Install LAMP
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install apache2 mysql-server php php-mysql php-gd php-xml php-mbstring

# CentOS/RHEL
sudo yum install httpd mariadb-server php php-mysqlnd php-gd php-xml php-mbstring
```

### Step 2: Start Services
```bash
sudo systemctl start apache2
sudo systemctl start mysql
sudo systemctl enable apache2
sudo systemctl enable mysql
```

### Step 3: Create Database
```bash
mysql -u root -e "CREATE DATABASE posreato CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE USER 'posuser'@'localhost' IDENTIFIED BY 'pospassword';"
mysql -u root -e "GRANT ALL PRIVILEGES ON posreato.* TO 'posuser'@'localhost';"
mysql -u root -e "FLUSH PRIVILEGES;"
```

### Step 4: Copy Project Files
```bash
# Copy to web root
sudo cp -r /path/to/restoopncode/php-native /var/www/html/pos
sudo chown -R www-data:www-data /var/www/html/pos
sudo chmod -R 755 /var/www/html/pos
```

### Step 5: Configure Database
Edit: `/var/www/html/pos/config/database.php`

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'posreato');
define('DB_USER', 'posuser');
define('DB_PASS', 'pospassword');
define('DB_CHARSET', 'utf8mb4');
```

### Step 6: Import Schema
```bash
sudo mysql posreato < /var/www/html/pos/database/schema.sql
sudo mysql posreato < /var/www/html/pos/database/seed.sql
```

### Step 7: Access Application
```
http://your-server-ip/pos/pages/login.php
```

---

## 🍎 Setup on XAMPP (Windows/Mac/Linux)

### Step 1: Install XAMPP
1. Download from [apachefriends.org](https://www.apachefriends.org)
2. Install to default location:
   - Windows: `C:\xampp`
   - Mac: `/Applications/XAMPP`
   - Linux: `/opt/lampp`

### Step 2: Start Services
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL**

### Step 3: Copy Project
```bash
# Windows: Copy to C:\xampp\htdocs\pos
# Mac: Copy to /Applications/XAMPP/htdocs/pos
# Linux: Copy to /opt/lampp/htdocs/pos
```

### Step 4: Create Database
1. Open: `http://localhost/phpmyadmin`
2. Click "New" → Database name: `posreato`
3. Collation: `utf8mb4_unicode_ci`
4. Click "Create"

### Step 5: Import Schema
1. Click on `posreato` database
2. Click "Import" tab
3. Choose file: `database/schema.sql`
4. Click "Go"

### Step 6: Configure Database
Edit: `htdocs/pos/config/database.php`

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'posreato');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default is empty
define('DB_CHARSET', 'utf8mb4');
```

### Step 7: Access Application
```
http://localhost/pos/pages/login.php
```

---

## 🔧 Configuration Options

### 1. Database Configuration
**File:** `config/database.php`

```php
define('DB_HOST', 'localhost');     // Database host
define('DB_NAME', 'posreato');      // Database name
define('DB_USER', 'root');          // Database username
define('DB_PASS', '');              // Database password
define('DB_CHARSET', 'utf8mb4');    // Character set
```

### 2. Session Configuration
**File:** `pages/login.php` (default 30 minutes)

To change session timeout, add at top of each page:
```php
ini_set('session.gc_maxlifetime', 3600); // 1 hour
session_set_cookie_params(3600);
```

### 3. Upload Directory
**File:** `config/database.php` (or create new config)

```php
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
```

---

## 📊 Database Tables Overview

### Core Tables:
- `users` - User accounts
- `roles` - User roles (Admin, Cashier, Kitchen, etc.)
- `outlets` - Restaurant outlets (multi-location support)
- `tables` - Dining tables
- `categories` - Menu categories
- `menu_items` - Menu items
- `modifiers` - Item modifiers (e.g., steak temperature)
- `modifier_groups` - Modifier groups
- `orders` - Orders
- `order_items` - Order line items
- `customers` - Customer database
- `payment_methods` - Payment methods

### Reference Tables:
- `void_reasons` - Reasons for voiding items
- `print_reasons` - Reasons for reprinting
- `item_notes` - Predefined item notes

---

## 🧪 Testing Installation

### 1. Verify Database Connection
Create test file: `test-db.php`
```php
<?php
require_once 'config/database.php';
try {
    $pdo = getDbConnection();
    echo "✅ Database connection successful!";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
```
Access: `http://localhost/pos/test-db.php`

### 2. Verify Login
1. Go to: `http://localhost/pos/pages/login.php`
2. Login with: `admin / admin123`
3. Should redirect to dashboard

### 3. Verify POS Flow
1. **Create Order:**
   - Go to POS Tables
   - Click on available table
   - Add items to cart
   - Submit order
   
2. **Check KDS:**
   - Go to KDS Kitchen
   - Order should appear automatically
   
3. **Complete Order:**
   - Go back to POS Order
   - Click "Bayar / Pay"
   - Process payment
   - Table should become available

---

## 🔐 Security Recommendations

### 1. Change Default Password
```sql
UPDATE users 
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';
-- This sets password to 'password' - change immediately!
```

### 2. Enable HTTPS (Production)
```apache
# In .htaccess
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 3. Set Proper File Permissions
```bash
# Linux
chmod 755 /var/www/html/pos
chmod 644 /var/www/html/pos/config/database.php
chown -R www-data:www-data /var/www/html/pos/uploads
```

### 4. Disable Error Display (Production)
```php
// In php.ini or .htaccess
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
```

---

## 🐛 Troubleshooting

### Issue: "Database connection failed"
**Solution:**
1. Check MySQL/MariaDB is running
2. Verify database credentials in `config/database.php`
3. Test connection: `mysql -u root -p posreato`

### Issue: "Table doesn't exist"
**Solution:**
```bash
# Re-import schema
mysql -u root posreato < database/schema.sql
```

### Issue: "Session not working"
**Solution:**
1. Check `session_start()` is at top of each PHP file
2. Verify session directory is writable
3. Check browser cookies are enabled

### Issue: "403 Forbidden"
**Solution:**
```bash
# Check file permissions
chmod -R 755 /var/www/html/pos

# Check Apache config
# Ensure AllowOverride All is set
```

### Issue: "Blank white page"
**Solution:**
1. Enable error display temporarily:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
2. Check PHP error log
3. Check browser console for JavaScript errors

---

## 📝 Default Data

### Default User (after seed.sql)
```
Username: admin
Password: admin123
Role: Admin
```

### Default Tax Rate
```
Tax: 11%
Service Charge: 0%
```

### Default Tables (after seed.sql)
```
Table 1-10: Various capacities
Status: available
```

### Sample Menu Items (after sample-steak-menu.sql)
```
Categories: Appetizers, Main Course, Desserts, Beverages
Items: Various steak dishes, sides, drinks
```

---

## 🔄 Update Instructions

### Updating from Previous Version
```bash
# 1. Backup database
mysqldump -u root posreato > backup_$(date +%Y%m%d).sql

# 2. Backup files
cp -r /var/www/html/pos /var/www/html/pos_backup_$(date +%Y%m%d)

# 3. Replace files
rm -rf /var/www/html/pos/*
cp -r /path/to/new/version/* /var/www/html/pos/

# 4. Run migrations (if any)
mysql -u root posreato < database/migrations/upgrade_to_v1.x.sql

# 5. Clear cache
rm -rf /var/www/html/pos/uploads/cache/*

# 6. Test application
```

---

## 📞 Support & Maintenance

### Daily Maintenance
- [ ] Check error logs
- [ ] Verify backups are running
- [ ] Check disk space
- [ ] Monitor slow queries

### Weekly Maintenance
- [ ] Review void reports
- [ ] Check inventory levels
- [ ] Update menu items if needed
- [ ] Review user activity logs

### Monthly Maintenance
- [ ] Full database backup
- [ ] Optimize database tables
- [ ] Review and archive old orders
- [ ] Update system packages

---

## 📄 License & Credits

**System:** RestoQwen POS  
**Version:** 1.0 (PHP Native)  
**License:** Proprietary  

**Developed for:** Restaurant Management  
**Technologies:** PHP, MySQL, Bootstrap 5, JavaScript

---

**Installation Complete! 🎉**

For technical support, check:
1. PHP error logs
2. MySQL error logs  
3. Browser console (F12)
4. This documentation
