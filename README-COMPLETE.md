# RestoQwen POS - Complete Restaurant Management System

## 📋 Project Overview

RestoQwen POS adalah sistem manajemen restoran lengkap berbasis web dengan fitur POS (Point of Sale), Kitchen Display System (KDS), dan manajemen order yang komprehensif.

## 🚀 Features

### Core Features
- ✅ **POS System** - Table-based ordering system
- ✅ **Kitchen Display System (KDS)** - Real-time order display for kitchen
- ✅ **Bar Display System (BDS)** - Real-time order display for bar
- ✅ **Menu Management** - CRUD menu items with categories
- ✅ **Modifier System** - Customizable item modifiers (e.g., steak temperature, sauces)
- ✅ **Item Notes** - Pre-defined notes for orders (e.g., "Tanpa Garam", "Pedas")
- ✅ **Order Management** - Complete order lifecycle management
- ✅ **Void System** - Void items with reasons tracking
- ✅ **Print Tracking** - Track print/reprint history
- ✅ **Payment System** - Multiple payment methods with tax & service charge
- ✅ **Receipt Printing** - Thermal printer support (58mm/80mm) + PDF
- ✅ **Customer Management** - Customer database
- ✅ **User Management** - Role-based access control
- ✅ **Reports** - Sales and business analytics

### Advanced Features
- ✅ **Auto-refresh KDS/BDS** - Every 10 seconds
- ✅ **Sound Notifications** - Alert for new orders
- ✅ **Auto-print Tickets** - Automatic kitchen/bar ticket printing
- ✅ **Print History** - Track all prints and reprints with reasons
- ✅ **Tax & Service Charge** - Configurable percentages
- ✅ **Category-based Modifiers** - Modifiers linked to specific categories

## 📁 Project Structure

```
restoopncode/
├── php-native/
│   ├── pages/              # Frontend pages
│   │   ├── login.php
│   │   ├── dashboard.php
│   │   ├── pos-tables.php
│   │   ├── pos-order.php
│   │   ├── orders.php
│   │   ├── menu.php
│   │   ├── modifiers.php
│   │   ├── customers.php
│   │   ├── reports.php
│   │   ├── users.php
│   │   ├── settings.php
│   │   ├── kds-kitchen.php
│   │   ├── kds-bar.php
│   │   ├── print-history.php
│   │   └── receipt.php
│   ├── api/                # API endpoints
│   │   ├── menu/
│   │   ├── modifiers/
│   │   ├── orders/
│   │   ├── pos/
│   │   ├── kds/
│   │   └── settings/
│   ├── config/
│   │   └── database.php
│   └── includes/
│       └── auth.php
├── database/               # SQL scripts
│   ├── sample-steak-menu.sql
│   ├── create-payment-methods.sql
│   ├── create-void-reasons.sql
│   └── create-modifiers.sql
└── frontend/               # React frontend (if used)
```

## 🗄️ Database Tables

### Core Tables
- `menu_items` - Menu items
- `categories` - Menu categories
- `modifier_groups` - Modifier groups (e.g., "Steak Temperature")
- `modifiers` - Individual modifiers (e.g., "Medium Rare")
- `orders` - Orders
- `order_items` - Order items (with notes, modifiers, print tracking)
- `order_print_log` - Print/reprint history log
- `tables` - Restaurant tables

### Management Tables
- `customers` - Customer database
- `users` - User accounts
- `roles` - User roles
- `payment_methods` - Payment methods
- `system_settings` - System configuration

### Reference Tables
- `void_reasons` - Void reasons (10 default)
- `print_reasons` - Reprint reasons (6 default)
- `item_notes` - Item notes library

## 🔧 Installation

### Prerequisites
- PHP 7.4+
- MySQL 5.7+ / MariaDB 10.4+
- Web server (Apache/Nginx)
- Laragon/XAMPP (recommended for Windows)

### Setup Steps

1. **Clone/Copy Project**
   ```bash
   Copy project to C:\Project\restoopncode
   ```

2. **Database Setup**
   ```sql
   CREATE DATABASE posreato;
   USE posreato;
   -- Import your database schema
   ```

3. **Configure Database Connection**
   Edit `php-native/config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'posreato');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Import Sample Data** (Optional)
   ```bash
   mysql -u root posreato < database/sample-steak-menu.sql
   mysql -u root posreato < database/create-payment-methods.sql
   mysql -u root posreato < database/create-void-reasons.sql
   ```

5. **Access Application**
   ```
   http://localhost/php-native/pages/login.php
   ```

## 🔐 Default Credentials

```
Username: admin
Password: admin123
```

## 📱 Usage Guide

### 1. Create Order
1. Go to **POS Tables** → Select table
2. Click menu items to add to cart
3. Select modifiers (if any)
4. Add notes (if any)
5. Click **Submit Order**

### 2. Submit to Kitchen
1. After submitting order, click **Submit to Kitchen** button
2. Item marked as **[SUBMITTED]**
3. Order appears on KDS/BDS

### 3. Process Payment
1. Click **Bayar / Pay** button
2. Select payment method
3. Enter amount (for cash)
4. Process payment
5. Receipt prints automatically

### 4. Void Item
1. Click **X** button on item
2. Select void reason
3. Confirm void

### 5. Reprint Item
1. Click **Reprint** button (for submitted items)
2. Select reprint reason
3. Confirm reprint

## 🔌 API Endpoints

### Menu API
- `GET /api/menu/index.php` - Get all menu items
- `POST /api/menu/store.php` - Create menu item
- `POST /api/menu/update.php` - Update menu item
- `DELETE /api/menu/delete.php?id=` - Delete menu item
- `GET/POST/PUT/DELETE /api/menu/categories.php` - Category CRUD

### Modifiers API
- `GET/POST/PUT /api/modifiers/groups.php` - Modifier groups CRUD
- `GET/POST/PUT/DELETE /api/modifiers/items.php` - Modifiers CRUD

### Orders API
- `GET /api/orders/index.php` - Get all orders
- `POST /api/orders/void-item.php` - Void order item
- `GET /api/orders/void-reasons.php` - Get void reasons
- `POST /api/orders/print-item.php` - Print/reprint item
- `GET /api/orders/print-reasons.php` - Get print reasons
- `GET /api/orders/print-history.php` - Get print history

### POS API
- `POST /api/pos/store-order.php` - Create order
- `GET /api/pos/table-orders.php?table_id=` - Get table orders
- `GET /api/pos/payment-methods.php` - Get payment methods
- `POST /api/pos/complete-order.php` - Complete order

### KDS API
- `GET /api/kds/kitchen-orders.php` - Get kitchen orders
- `GET /api/kds/bar-orders.php` - Get bar orders
- `POST /api/kds/update-order-status.php` - Update order status

### Settings API
- `GET/POST /api/settings/tax-service.php` - Tax/Service settings
- `GET/POST/PUT/DELETE /api/settings/item-notes.php` - Item notes CRUD

## 🎯 Order Flow

```
1. Select Table (POS Tables)
   ↓
2. Add Items (POS Order)
   ├─ Select Modifiers
   └─ Add Notes
   ↓
3. Submit Order
   ↓
4. Submit to Kitchen (Print)
   ├─ Kitchen receives ticket
   └─ KDS shows order
   ↓
5. Payment
   ├─ Select payment method
   └─ Process payment
   ↓
6. Receipt
   ├─ Print receipt
   └─ Show print history
```

## 🖨️ Receipt Features

- Thermal printer support (58mm/80mm)
- PDF download option
- Auto-print on payment
- Shows:
  - Item modifiers
  - Item notes
  - Tax breakdown
  - Service charge
  - Print history

## 📊 Reports

- Sales by category
- Top selling items
- Hourly analysis
- Payment method breakdown
- Void/Reprint reports

## 🔒 Security Features

- Session-based authentication
- Role-based access control
- SQL injection prevention (prepared statements)
- XSS protection (htmlspecialchars)

## 🎨 UI/UX Features

- Responsive design (Bootstrap 5)
- Modern gradient theme
- Real-time updates
- Sound notifications
- Auto-refresh KDS/BDS
- Thermal printer optimized layouts

## 📝 Changelog

### Version 1.0 (Current)
- ✅ Complete POS system
- ✅ KDS/BDS with auto-refresh
- ✅ Print tracking system
- ✅ Void system with reasons
- ✅ Reprint system with reasons
- ✅ Tax & service charge
- ✅ Multiple payment methods
- ✅ Receipt printing (thermal + PDF)
- ✅ Modifier system
- ✅ Item notes system

## 🤝 Support

For issues or questions, please check:
1. Database connection settings
2. PHP error logs
3. Browser console for JavaScript errors

## 📄 License

Proprietary - RestoQwen POS

---

**Developed with ❤️ for Restaurant Management**
