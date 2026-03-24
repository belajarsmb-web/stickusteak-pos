-- Restaurant POS System Database Schema
-- MariaDB with InnoDB Engine
-- Database: posreato

-- Disable foreign key checks for import
SET FOREIGN_KEY_CHECKS = 0;

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS posreato CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE posreato;

-- ========================================
-- 1. USER MANAGEMENT & AUTHENTICATION
-- ========================================

-- Roles table
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    permissions JSON, -- Store permissions as JSON array
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role_name (name),
    INDEX idx_role_active (is_active)
);

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role_id INT NOT NULL,
    outlet_id INT, -- NULL for admin users
    is_active BOOLEAN DEFAULT true,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    INDEX idx_user_username (username),
    INDEX idx_user_email (email),
    INDEX idx_user_role (role_id),
    INDEX idx_user_active (is_active),
    INDEX idx_user_outlet (outlet_id)
);

-- User sessions table
CREATE TABLE user_sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT NOT NULL,
    refresh_token_hash VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_user (user_id),
    INDEX idx_session_expires (expires_at)
);

-- Activity logs table
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    resource_type VARCHAR(50),
    resource_id INT,
    details JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_log_user (user_id),
    INDEX idx_log_action (action),
    INDEX idx_log_resource (resource_type, resource_id),
    INDEX idx_log_created (created_at)
);

-- Shift management table
CREATE TABLE shifts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    outlet_id INT NOT NULL,
    shift_date DATE NOT NULL,
    clock_in TIMESTAMP NULL,
    clock_out TIMESTAMP NULL,
    total_hours DECIMAL(5,2) DEFAULT 0,
    status ENUM('active', 'completed', 'closed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    INDEX idx_shift_user (user_id),
    INDEX idx_shift_outlet (outlet_id),
    INDEX idx_shift_date (shift_date),
    INDEX idx_shift_status (status)
);

-- ========================================
-- 2. OUTLET & TABLE MANAGEMENT
-- ========================================

-- Outlets table
CREATE TABLE outlets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    tax_rate DECIMAL(5,2) DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'IDR',
    timezone VARCHAR(50) DEFAULT 'Asia/Jakarta',
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_outlet_active (is_active)
);

-- Tables table
CREATE TABLE tables (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT NOT NULL,
    table_number VARCHAR(10) NOT NULL,
    name VARCHAR(50),
    capacity INT DEFAULT 4,
    status ENUM('available', 'occupied', 'reserved', 'cleaning') DEFAULT 'available',
    position_x INT DEFAULT 0,
    position_y INT DEFAULT 0,
    width INT DEFAULT 100,
    height INT DEFAULT 100,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    UNIQUE KEY unique_table_outlet (outlet_id, table_number),
    INDEX idx_table_outlet (outlet_id),
    INDEX idx_table_status (status),
    INDEX idx_table_active (is_active)
);

-- Table reservations table
CREATE TABLE table_reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_id INT NOT NULL,
    customer_name VARCHAR(100),
    customer_phone VARCHAR(20),
    reservation_time DATETIME NOT NULL,
    party_size INT DEFAULT 1,
    notes TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE,
    INDEX idx_reservation_table (table_id),
    INDEX idx_reservation_time (reservation_time),
    INDEX idx_reservation_status (status)
);

-- ========================================
-- 3. MENU & CATEGORIES
-- ========================================

-- Categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    color VARCHAR(7) DEFAULT '#007bff', -- Hex color for UI
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    INDEX idx_category_outlet (outlet_id),
    INDEX idx_category_order (sort_order),
    INDEX idx_category_active (is_active)
);

-- Menu items table
CREATE TABLE menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT,
    category_id INT,
    code VARCHAR(20) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    cost_price DECIMAL(10,2) DEFAULT 0,
    image_url VARCHAR(500),
    is_active BOOLEAN DEFAULT true,
    is_available BOOLEAN DEFAULT true,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    UNIQUE KEY unique_code_outlet (outlet_id, code),
    INDEX idx_menu_outlet (outlet_id),
    INDEX idx_menu_category (category_id),
    INDEX idx_menu_active (is_active),
    INDEX idx_menu_available (is_available)
);

-- ========================================
-- 4. MODIFIER SYSTEM
-- ========================================

-- Modifier groups table
CREATE TABLE modifier_groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    min_selection INT DEFAULT 0,
    max_selection INT DEFAULT 1,
    is_required BOOLEAN DEFAULT false,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    INDEX idx_modifier_group_outlet (outlet_id),
    INDEX idx_modifier_group_order (sort_order),
    INDEX idx_modifier_group_active (is_active)
);

-- Modifiers table
CREATE TABLE modifiers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    modifier_group_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price_adjustment DECIMAL(10,2) DEFAULT 0,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (modifier_group_id) REFERENCES modifier_groups(id) ON DELETE CASCADE,
    INDEX idx_modifier_group (modifier_group_id),
    INDEX idx_modifier_order (sort_order),
    INDEX idx_modifier_active (is_active)
);

-- Menu item modifiers relationship
CREATE TABLE menu_item_modifiers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_item_id INT NOT NULL,
    modifier_group_id INT NOT NULL,
    is_required BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    FOREIGN KEY (modifier_group_id) REFERENCES modifier_groups(id) ON DELETE CASCADE,
    UNIQUE KEY unique_menu_modifier (menu_item_id, modifier_group_id),
    INDEX idx_menu_item_modifier (menu_item_id),
    INDEX idx_modifier_menu_item (modifier_group_id)
);

-- ========================================
-- 5. ORDER MANAGEMENT
-- ========================================

-- Orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT NOT NULL,
    table_id INT,
    customer_name VARCHAR(100),
    customer_phone VARCHAR(20),
    customer_address TEXT,
    service_type ENUM('dine_in', 'takeaway', 'delivery') NOT NULL,
    status ENUM('draft', 'sent_to_kitchen', 'in_progress', 'served', 'paid', 'voided', 'refunded') DEFAULT 'draft',
    sub_total DECIMAL(10,2) DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    service_charge DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) DEFAULT 0,
    paid_amount DECIMAL(10,2) DEFAULT 0,
    change_amount DECIMAL(10,2) DEFAULT 0,
    notes TEXT,
    created_by INT NOT NULL,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order_outlet (outlet_id),
    INDEX idx_order_table (table_id),
    INDEX idx_order_status (status),
    INDEX idx_order_service_type (service_type),
    INDEX idx_order_created (created_at),
    INDEX idx_order_created_by (created_by)
);

-- Order items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    cost_price DECIMAL(10,2) DEFAULT 0,
    notes TEXT,
    status ENUM('pending', 'preparing', 'ready', 'served', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE RESTRICT,
    INDEX idx_order_item_order (order_id),
    INDEX idx_order_item_menu (menu_item_id),
    INDEX idx_order_item_status (status)
);

-- Order item modifiers table
CREATE TABLE order_item_modifiers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_item_id INT NOT NULL,
    modifier_id INT NOT NULL,
    modifier_group_id INT NOT NULL,
    price_adjustment DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE,
    FOREIGN KEY (modifier_id) REFERENCES modifiers(id) ON DELETE RESTRICT,
    FOREIGN KEY (modifier_group_id) REFERENCES modifier_groups(id) ON DELETE RESTRICT,
    INDEX idx_order_item_modifier (order_item_id),
    INDEX idx_modifier_order_item (modifier_id)
);

-- Order logs table (for audit trail)
CREATE TABLE order_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    user_id INT NOT NULL,
    details JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_order_log_order (order_id),
    INDEX idx_order_log_action (action),
    INDEX idx_order_log_user (user_id),
    INDEX idx_order_log_created (created_at)
);

-- ========================================
-- 6. PAYMENT MANAGEMENT
-- ========================================

-- Payment methods table
CREATE TABLE payment_methods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT,
    name VARCHAR(50) NOT NULL,
    type ENUM('cash', 'card', 'qris', 'ewallet', 'transfer', 'custom') NOT NULL,
    description TEXT,
    mdr_rate DECIMAL(5,4) DEFAULT 0, -- Merchant Discount Rate
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    INDEX idx_payment_method_outlet (outlet_id),
    INDEX idx_payment_method_type (type),
    INDEX idx_payment_method_active (is_active)
);

-- Payments table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    payment_method_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    fee_amount DECIMAL(10,2) DEFAULT 0,
    transaction_id VARCHAR(100),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_payment_order (order_id),
    INDEX idx_payment_method (payment_method_id),
    INDEX idx_payment_status (status),
    INDEX idx_payment_created (created_at)
);

-- ========================================
-- 7. INVENTORY MANAGEMENT
-- ========================================

-- Inventory items table
CREATE TABLE inventory_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    sku VARCHAR(50) UNIQUE,
    unit VARCHAR(20) NOT NULL, -- kg, liter, pcs, etc.
    current_stock DECIMAL(10,2) DEFAULT 0,
    min_stock DECIMAL(10,2) DEFAULT 0,
    max_stock DECIMAL(10,2) DEFAULT 0,
    reorder_point DECIMAL(10,2) DEFAULT 0,
    cost_price DECIMAL(10,2) DEFAULT 0,
    selling_price DECIMAL(10,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    INDEX idx_inventory_outlet (outlet_id),
    INDEX idx_inventory_sku (sku),
    INDEX idx_inventory_active (is_active)
);

-- Recipe ingredients table (for menu items)
CREATE TABLE recipe_ingredients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_item_id INT NOT NULL,
    inventory_item_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    FOREIGN KEY (inventory_item_id) REFERENCES inventory_items(id) ON DELETE CASCADE,
    UNIQUE KEY unique_recipe_ingredient (menu_item_id, inventory_item_id),
    INDEX idx_recipe_menu (menu_item_id),
    INDEX idx_recipe_inventory (inventory_item_id)
);

-- Suppliers table
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    INDEX idx_supplier_outlet (outlet_id),
    INDEX idx_supplier_active (is_active)
);

-- Purchase orders table
CREATE TABLE purchase_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT NOT NULL,
    supplier_id INT NOT NULL,
    po_number VARCHAR(50) NOT NULL UNIQUE,
    order_date DATE NOT NULL,
    expected_date DATE,
    status ENUM('draft', 'sent', 'received', 'cancelled') DEFAULT 'draft',
    total_amount DECIMAL(10,2) DEFAULT 0,
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_po_outlet (outlet_id),
    INDEX idx_po_supplier (supplier_id),
    INDEX idx_po_status (status),
    INDEX idx_po_created (created_at)
);

-- Purchase order items table
CREATE TABLE purchase_order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    purchase_order_id INT NOT NULL,
    inventory_item_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    received_quantity DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (inventory_item_id) REFERENCES inventory_items(id) ON DELETE RESTRICT,
    INDEX idx_po_item_po (purchase_order_id),
    INDEX idx_po_item_inventory (inventory_item_id)
);

-- Inventory movements table
CREATE TABLE inventory_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT NOT NULL,
    inventory_item_id INT NOT NULL,
    movement_type ENUM('in', 'out', 'adjustment', 'waste') NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(10,2) DEFAULT 0,
    reference_type ENUM('purchase_order', 'order', 'adjustment', 'waste') NOT NULL,
    reference_id INT NOT NULL,
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    FOREIGN KEY (inventory_item_id) REFERENCES inventory_items(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_movement_outlet (outlet_id),
    INDEX idx_movement_item (inventory_item_id),
    INDEX idx_movement_type (movement_type),
    INDEX idx_movement_reference (reference_type, reference_id),
    INDEX idx_movement_created (created_at)
);

-- ========================================
-- 8. CUSTOMER & CRM
-- ========================================

-- Customers table
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    membership_tier ENUM('bronze', 'silver', 'gold', 'platinum') DEFAULT 'bronze',
    total_spent DECIMAL(12,2) DEFAULT 0,
    total_visits INT DEFAULT 0,
    last_visit DATE,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    INDEX idx_customer_outlet (outlet_id),
    INDEX idx_customer_phone (phone),
    INDEX idx_customer_email (email),
    INDEX idx_customer_tier (membership_tier),
    INDEX idx_customer_active (is_active)
);

-- Customer visits table
CREATE TABLE customer_visits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    outlet_id INT NOT NULL,
    order_id INT,
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    table_id INT,
    party_size INT DEFAULT 1,
    total_amount DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE SET NULL,
    INDEX idx_visit_customer (customer_id),
    INDEX idx_visit_outlet (outlet_id),
    INDEX idx_visit_date (visit_date),
    INDEX idx_visit_order (order_id)
);

-- Loyalty points table
CREATE TABLE loyalty_points (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    outlet_id INT NOT NULL,
    points_earned DECIMAL(10,2) DEFAULT 0,
    points_used DECIMAL(10,2) DEFAULT 0,
    points_balance DECIMAL(10,2) DEFAULT 0,
    transaction_type ENUM('purchase', 'redemption', 'bonus', 'adjustment') NOT NULL,
    reference_type ENUM('order', 'promotion', 'manual') NOT NULL,
    reference_id INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    INDEX idx_points_customer (customer_id),
    INDEX idx_points_outlet (outlet_id),
    INDEX idx_points_type (transaction_type),
    INDEX idx_points_reference (reference_type, reference_id),
    INDEX idx_points_created (created_at)
);

-- ========================================
-- 9. REPORTING & CONFIGURATION
-- ========================================

-- Printers table
CREATE TABLE printers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    type ENUM('kitchen', 'bar', 'receipt', 'label') NOT NULL,
    connection_type ENUM('network', 'usb', 'bluetooth') NOT NULL,
    ip_address VARCHAR(45),
    port INT,
    device_path VARCHAR(255),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    INDEX idx_printer_outlet (outlet_id),
    INDEX idx_printer_type (type),
    INDEX idx_printer_active (is_active)
);

-- Printer routing table
CREATE TABLE printer_routing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT NOT NULL,
    category_id INT,
    printer_id INT NOT NULL,
    is_default BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (printer_id) REFERENCES printers(id) ON DELETE CASCADE,
    INDEX idx_routing_outlet (outlet_id),
    INDEX idx_routing_category (category_id),
    INDEX idx_routing_printer (printer_id)
);

-- System settings table
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    outlet_id INT,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    is_global BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON DELETE CASCADE,
    UNIQUE KEY unique_setting (outlet_id, setting_key),
    INDEX idx_setting_outlet (outlet_id),
    INDEX idx_setting_key (setting_key),
    INDEX idx_setting_global (is_global)
);

-- ========================================
-- 10. VIEWS FOR REPORTING
-- ========================================

-- Daily sales summary view
CREATE VIEW daily_sales_summary AS
SELECT 
    DATE(created_at) as sale_date,
    outlet_id,
    COUNT(*) as total_orders,
    SUM(total_amount) as total_sales,
    SUM(paid_amount) as total_paid,
    AVG(total_amount) as avg_order_value
FROM orders 
WHERE status = 'paid'
GROUP BY DATE(created_at), outlet_id;

-- Top selling items view
CREATE VIEW top_selling_items AS
SELECT 
    mi.name as item_name,
    mi.category_id,
    c.name as category_name,
    SUM(oi.quantity) as total_quantity_sold,
    SUM(oi.quantity * oi.price) as total_revenue
FROM order_items oi
JOIN menu_items mi ON oi.menu_item_id = mi.id
JOIN categories c ON mi.category_id = c.id
JOIN orders o ON oi.order_id = o.id
WHERE o.status = 'paid'
GROUP BY mi.id, mi.name, mi.category_id, c.name
ORDER BY total_quantity_sold DESC;

-- Inventory low stock view
CREATE VIEW low_stock_alerts AS
SELECT 
    ii.name as item_name,
    ii.sku,
    ii.current_stock,
    ii.min_stock,
    ii.reorder_point,
    ii.outlet_id,
    o.name as outlet_name
FROM inventory_items ii
JOIN outlets o ON ii.outlet_id = o.id
WHERE ii.current_stock <= ii.reorder_point AND ii.is_active = true;

-- ========================================
-- 11. SAMPLE DATA
-- ========================================

-- Insert default roles
INSERT INTO roles (name, description, permissions) VALUES
('admin', 'System Administrator', '["*"]'),
('manager', 'Outlet Manager', '["orders:*", "inventory:*", "reports:*", "users:read", "tables:*"]'),
('supervisor', 'Shift Supervisor', '["orders:*", "tables:*", "payments:*"]'),
('cashier', 'Cashier', '["orders:create", "orders:read", "payments:create", "tables:read"]'),
('waiter', 'Waiter/Waitress', '["orders:create", "orders:read", "tables:*"]');

-- Insert default payment methods
INSERT INTO payment_methods (name, type, description, mdr_rate) VALUES
('Cash', 'cash', 'Cash payment', 0),
('Debit Card', 'card', 'Debit card payment', 0.025),
('Credit Card', 'card', 'Credit card payment', 0.035),
('QRIS', 'qris', 'QRIS payment', 0.007),
('GoPay', 'ewallet', 'Gojek payment', 0.02),
('OVO', 'ewallet', 'OVO payment', 0.02),
('Bank Transfer', 'transfer', 'Bank transfer payment', 0);

-- ========================================
-- 12. TRIGGERS FOR AUTOMATION
-- ========================================

-- Trigger to update table status when order is created/updated
DELIMITER $$
CREATE TRIGGER update_table_status_on_order
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.table_id IS NOT NULL THEN
        IF NEW.status IN ('draft', 'sent_to_kitchen', 'in_progress') THEN
            UPDATE tables SET status = 'occupied' WHERE id = NEW.table_id;
        ELSEIF NEW.status = 'paid' THEN
            UPDATE tables SET status = 'cleaning' WHERE id = NEW.table_id;
        END IF;
    END IF;
END$$
DELIMITER ;

-- Trigger to log order status changes
DELIMITER $$
CREATE TRIGGER log_order_status_change
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO order_logs (order_id, action, old_status, new_status, user_id, details)
        VALUES (NEW.id, 'status_changed', OLD.status, NEW.status, NEW.updated_by, 
                JSON_OBJECT('reason', 'Automatic status update'));
    END IF;
END$$
DELIMITER ;

-- Trigger to update inventory when order is paid
DELIMITER $$
CREATE TRIGGER update_inventory_on_order_paid
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_menu_item_id INT;
    DECLARE v_quantity DECIMAL(10,2);
    DECLARE v_outlet_id INT;
    
    DECLARE order_items_cursor CURSOR FOR
        SELECT oi.menu_item_id, oi.quantity, o.outlet_id
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE oi.order_id = NEW.id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    IF NEW.status = 'paid' AND OLD.status != 'paid' THEN
        OPEN order_items_cursor;
        
        read_loop: LOOP
            FETCH order_items_cursor INTO v_menu_item_id, v_quantity, v_outlet_id;
            IF done THEN
                LEAVE read_loop;
            END IF;
            
            -- Deduct inventory for each recipe ingredient
            INSERT INTO inventory_movements (
                outlet_id, inventory_item_id, movement_type, quantity, 
                reference_type, reference_id, notes, created_by
            )
            SELECT 
                v_outlet_id, ri.inventory_item_id, 'out', (ri.quantity * v_quantity),
                'order', NEW.id, CONCAT('Order #', NEW.id), NEW.updated_by
            FROM recipe_ingredients ri
            WHERE ri.menu_item_id = v_menu_item_id;
        END LOOP;
        
        CLOSE order_items_cursor;
    END IF;
END$$
DELIMITER ;

-- ========================================
-- 13. STORED PROCEDURES
-- ========================================

-- Procedure to create a new order
DELIMITER $$
CREATE PROCEDURE CreateOrder(
    IN p_outlet_id INT,
    IN p_table_id INT,
    IN p_service_type VARCHAR(20),
    IN p_customer_name VARCHAR(100),
    IN p_customer_phone VARCHAR(20),
    IN p_customer_address TEXT,
    IN p_created_by INT,
    OUT p_order_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    INSERT INTO orders (
        outlet_id, table_id, service_type, customer_name, 
        customer_phone, customer_address, created_by
    ) VALUES (
        p_outlet_id, p_table_id, p_service_type, p_customer_name,
        p_customer_phone, p_customer_address, p_created_by
    );
    
    SET p_order_id = LAST_INSERT_ID();
    
    COMMIT;
END$$
DELIMITER ;

-- Procedure to add item to order
DELIMITER $$
CREATE PROCEDURE AddOrderItem(
    IN p_order_id INT,
    IN p_menu_item_id INT,
    IN p_quantity INT,
    IN p_notes TEXT,
    IN p_created_by INT
)
BEGIN
    DECLARE v_price DECIMAL(10,2);
    DECLARE v_cost_price DECIMAL(10,2);
    
    SELECT price, cost_price INTO v_price, v_cost_price
    FROM menu_items WHERE id = p_menu_item_id;
    
    INSERT INTO order_items (
        order_id, menu_item_id, quantity, price, cost_price, notes
    ) VALUES (
        p_order_id, p_menu_item_id, p_quantity, v_price, v_cost_price, p_notes
    );
    
    -- Update order totals
    CALL UpdateOrderTotals(p_order_id);
END$$
DELIMITER ;

-- Procedure to update order totals
DELIMITER $$
CREATE PROCEDURE UpdateOrderTotals(IN p_order_id INT)
BEGIN
    DECLARE v_sub_total DECIMAL(10,2) DEFAULT 0;
    DECLARE v_tax_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_service_charge DECIMAL(10,2) DEFAULT 0;
    DECLARE v_discount_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_total_amount DECIMAL(10,2) DEFAULT 0;
    DECLARE v_outlet_tax_rate DECIMAL(5,2) DEFAULT 0;
    
    -- Calculate sub total
    SELECT COALESCE(SUM(oi.quantity * oi.price), 0) INTO v_sub_total
    FROM order_items oi WHERE oi.order_id = p_order_id;
    
    -- Get outlet tax rate
    SELECT o.tax_rate INTO v_outlet_tax_rate
    FROM orders ord JOIN outlets o ON ord.outlet_id = o.id
    WHERE ord.id = p_order_id;
    
    -- Calculate tax and service charge
    SET v_tax_amount = v_sub_total * (v_outlet_tax_rate / 100);
    SET v_service_charge = v_sub_total * 0.1; -- 10% service charge
    
    SET v_total_amount = v_sub_total + v_tax_amount + v_service_charge - v_discount_amount;
    
    UPDATE orders SET
        sub_total = v_sub_total,
        tax_amount = v_tax_amount,
        service_charge = v_service_charge,
        discount_amount = v_discount_amount,
        total_amount = v_total_amount
    WHERE id = p_order_id;
END$$
DELIMITER ;

-- ========================================
-- 14. INDEXES FOR PERFORMANCE
-- ========================================

-- Additional indexes for better query performance
CREATE INDEX idx_orders_date_status ON orders(created_at, status);
CREATE INDEX idx_order_items_order_status ON order_items(order_id, status);
CREATE INDEX idx_inventory_movements_date ON inventory_movements(created_at);
CREATE INDEX idx_activity_logs_date ON activity_logs(created_at);
CREATE INDEX idx_customer_visits_date ON customer_visits(visit_date);

-- ========================================
-- 15. COMMENTS AND DOCUMENTATION
-- ========================================

-- Add table comments for documentation
ALTER TABLE roles COMMENT = 'User roles and permissions';
ALTER TABLE users COMMENT = 'System users and authentication';
ALTER TABLE orders COMMENT = 'Restaurant orders and their lifecycle';
ALTER TABLE order_items COMMENT = 'Individual items within orders';
ALTER TABLE menu_items COMMENT = 'Restaurant menu items';
ALTER TABLE inventory_items COMMENT = 'Inventory and stock management';
ALTER TABLE payments COMMENT = 'Payment transactions';
ALTER TABLE customers COMMENT = 'Customer database and CRM';

-- ========================================
-- END OF SCHEMA
-- ========================================

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;