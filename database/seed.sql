-- ============================================
-- Restopos Database Seeder for MariaDB
-- Database: posreato
-- ============================================

-- ============================================
-- 1. INSERT ROLES
-- ============================================
INSERT IGNORE INTO roles (id, name, description, permissions, isActive)
VALUES
(1, 'admin', 'Administrator', '["all"]', 1),
(2, 'manager', 'Manager', '["view_reports", "manage_menu", "manage_orders", "manage_inventory", "manage_users"]', 1),
(3, 'cashier', 'Cashier', '["create_orders", "process_payments", "view_orders"]', 1),
(4, 'kitchen', 'Kitchen Staff', '["view_kitchen", "update_order_status"]', 1);

-- ============================================
-- 2. INSERT USERS
-- ============================================
INSERT IGNORE INTO users (id, username, email, password_hash, full_name, phone, role_id, outlet_id, is_active)
VALUES
(1, 'admin', 'admin@restopos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '021-1234567', 1, 1, 1),
(2, 'manager', 'manager@restopos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manager Toko', '021-1234568', 2, 1, 1),
(3, 'cashier', 'cashier@restopos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kasir Utama', '021-1234569', 3, 1, 1);

-- ============================================
-- 3. INSERT OUTLETS
-- ============================================
INSERT IGNORE INTO outlets (id, name, address, phone, email, tax_rate, currency, timezone, is_active)
VALUES
(1, 'Restopos Main Branch', 'Jl. Sudirman No. 1, Jakarta Pusat', '021-12345678', 'main@restopos.com', 11.00, 'IDR', 'Asia/Jakarta', 1),
(2, 'Restopos Cabang Selatan', 'Jl. TB Simatupang No. 88, Jakarta Selatan', '021-87654321', 'south@restopos.com', 11.00, 'IDR', 'Asia/Jakarta', 1);

-- ============================================
-- 4. INSERT TABLES
-- ============================================
INSERT IGNORE INTO `tables` (id, outlet_id, table_number, name, capacity, status, position_x, position_y, width, height, is_active)
VALUES
(1, 1, 'T-01', 'Meja 1', 4, 'available', 50, 50, 120, 80, 1),
(2, 1, 'T-02', 'Meja 2', 4, 'available', 200, 50, 120, 80, 1),
(3, 1, 'T-03', 'Meja 3', 2, 'occupied', 50, 200, 100, 80, 1),
(4, 1, 'T-04', 'Meja 4', 6, 'available', 200, 200, 150, 80, 1),
(5, 1, 'T-05', 'Meja 5 VIP', 8, 'reserved', 400, 50, 200, 100, 1),
(6, 1, 'T-06', 'Meja 6', 4, 'available', 400, 200, 120, 80, 1),
(7, 1, 'T-07', 'Outdoor 1', 4, 'available', 600, 50, 120, 80, 1),
(8, 1, 'T-08', 'Outdoor 2', 4, 'cleaning', 600, 200, 120, 80, 1);

-- ============================================
-- 5. INSERT CATEGORIES
-- ============================================
INSERT IGNORE INTO categories (id, outlet_id, name, description, sort_order, color, is_active)
VALUES
(1, 1, 'Makanan Berat', 'Nasi, Mie, Bakmi dan sejenisnya', 1, '#FF6B35', 1),
(2, 1, 'Makanan Ringan', 'Snack, gorengan, cemilan', 2, '#F7C59F', 1),
(3, 1, 'Minuman Dingin', 'Es, juice, smoothie', 3, '#2196F3', 1),
(4, 1, 'Minuman Panas', 'Kopi, teh, jamu', 4, '#795548', 1),
(5, 1, 'Dessert', 'Kue, es krim, pudding', 5, '#E91E63', 1),
(6, 1, 'Special Menu', 'Menu spesial chef', 6, '#9C27B0', 1);

-- ============================================
-- 6. INSERT MENU ITEMS
-- ============================================
INSERT IGNORE INTO menu_items (id, outlet_id, category_id, code, name, description, price, cost_price, is_active, is_available, sort_order)
VALUES
(1, 1, 1, 'MK001', 'Nasi Goreng Spesial', 'Nasi goreng dengan telur, ayam, dan sayuran', 45000, 18000, 1, 1, 1),
(2, 1, 1, 'MK002', 'Ayam Bakar Madu', 'Ayam bakar dengan saus madu istimewa', 55000, 22000, 1, 1, 2),
(3, 1, 1, 'MK003', 'Mie Goreng Jumbo', 'Mie goreng porsi besar dengan seafood', 48000, 20000, 1, 1, 3),
(4, 1, 1, 'MK004', 'Nasi Uduk Komplit', 'Nasi uduk dengan lauk pauk lengkap', 42000, 17000, 1, 1, 4),
(5, 1, 1, 'MK005', 'Soto Betawi', 'Soto Betawi kuah santan', 40000, 16000, 1, 1, 5),
(6, 1, 1, 'MK006', 'Gado-Gado', 'Gado-gado dengan bumbu kacang', 35000, 14000, 1, 1, 6),
(7, 1, 2, 'MR001', 'Kentang Goreng', 'French fries crispy dengan saus pilihan', 25000, 8000, 1, 1, 1),
(8, 1, 2, 'MR002', 'Kulit Ayam Crispy', 'Kulit ayam goreng crispy dengan sambal', 22000, 7000, 1, 1, 2),
(9, 1, 2, 'MR003', 'Bakwan Sayur', 'Bakwan sayuran goreng crispy (5 pcs)', 18000, 6000, 1, 1, 3),
(10, 1, 3, 'MD001', 'Es Teh Manis', 'Teh manis segar dengan es', 10000, 3000, 1, 1, 1),
(11, 1, 3, 'MD002', 'Es Jeruk', 'Jeruk peras segar dengan es', 15000, 5000, 1, 1, 2),
(12, 1, 3, 'MD003', 'Jus Alpukat', 'Jus alpukat segar dengan susu coklat', 25000, 9000, 1, 1, 3),
(13, 1, 3, 'MD004', 'Es Campur', 'Es campur dengan berbagai topping', 22000, 8000, 1, 1, 4),
(14, 1, 3, 'MD005', 'Boba Taro', 'Minuman boba rasa taro dengan pearl', 28000, 10000, 1, 1, 5),
(15, 1, 4, 'MP001', 'Kopi Hitam', 'Kopi hitam tubruk pilihan', 12000, 4000, 1, 1, 1),
(16, 1, 4, 'MP002', 'Kopi Susu Gula Aren', 'Kopi susu dengan gula aren premium', 25000, 8000, 1, 1, 2),
(17, 1, 4, 'MP003', 'Teh Tarik', 'Teh tarik khas dengan susu kental', 18000, 6000, 1, 1, 3),
(18, 1, 4, 'MP004', 'Coklat Panas', 'Minuman coklat panas dengan whipped cream', 22000, 7000, 1, 1, 4),
(19, 1, 5, 'DS001', 'Es Krim 2 Scoop', 'Es krim dengan 2 scoop pilihan rasa', 28000, 10000, 1, 1, 1),
(20, 1, 5, 'DS002', 'Pudding Alpukat', 'Pudding alpukat segar dengan saus coklat', 22000, 8000, 1, 1, 2),
(21, 1, 5, 'DS003', 'Pisang Goreng Madu', 'Pisang goreng dengan topping madu dan keju', 20000, 7000, 1, 1, 3),
(22, 1, 6, 'SP001', 'Steak Wagyu 200gr', 'Daging wagyu premium 200gr dengan saus mushroom', 185000, 80000, 1, 1, 1),
(23, 1, 6, 'SP002', 'Paket Hemat Keluarga', 'Nasi + 4 Ayam + Minuman x4', 150000, 60000, 1, 1, 2);

-- ============================================
-- 7. INSERT INVENTORY ITEMS
-- ============================================
INSERT IGNORE INTO inventory_items (id, outlet_id, name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active)
VALUES
(1, 1, 'Beras Premium', 'BRS001', 'kg', 50.00, 20.00, 100.00, 25.00, 15000, 1),
(2, 1, 'Minyak Goreng', 'MYK001', 'liter', 30.00, 10.00, 50.00, 12.00, 18000, 1),
(3, 1, 'Ayam Segar', 'AYM001', 'kg', 15.00, 5.00, 30.00, 7.00, 35000, 1),
(4, 1, 'Telur Ayam', 'TLR001', 'butir', 200.00, 50.00, 500.00, 60.00, 2000, 1),
(5, 1, 'Bawang Merah', 'BWM001', 'kg', 5.00, 2.00, 15.00, 3.00, 25000, 1),
(6, 1, 'Gas LPG 12kg', 'GAS001', 'tabung', 2.00, 1.00, 5.00, 1.00, 180000, 1);

-- ============================================
-- 8. INSERT CUSTOMERS
-- ============================================
INSERT IGNORE INTO customers (id, outlet_id, name, phone, email, membership_tier, total_spent, total_visits, is_active)
VALUES
(1, 1, 'Budi Santoso', '081234500001', 'budi@email.com', 'gold', 2500000, 25, 1),
(2, 1, 'Siti Rahayu', '081234500002', 'siti@email.com', 'silver', 1200000, 12, 1),
(3, 1, 'Ahmad Fauzi', '081234500003', NULL, 'bronze', 350000, 5, 1),
(4, 1, 'Dewi Kusuma', '081234500004', 'dewi@email.com', 'platinum', 5800000, 58, 1);

SELECT 'Seed completed!' as status;
