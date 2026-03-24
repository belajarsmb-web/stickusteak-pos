-- Sample Data Seed for Restaurant POS
USE posreato;

-- 1. Ensure Outlet 1 exists
INSERT IGNORE INTO outlets (id, name, address, phone, is_active) 
VALUES (1, 'Antigravity Bistro', 'Jl. Sudirman No. 123, Jakarta', '021-1234567', true);

-- 2. Professional Categories (Reset for clean sample if desired, or just add)
INSERT IGNORE INTO categories (id, outlet_id, name, description, color, sort_order) VALUES
(20, 1, 'Main Course', 'Hidangan Utama', '#e74c3c', 1),
(21, 1, 'Appetizers', 'Makanan Pembuka', '#f1c40f', 2),
(22, 1, 'Coffee & Tea', 'Minuman Kafein', '#34495e', 3),
(23, 1, 'Mocktails', 'Minuman Segar', '#2ecc71', 4),
(24, 1, 'Desserts', 'Makanan Penutup', '#9b59b6', 5);

-- 3. Inventory Items
INSERT IGNORE INTO inventory_items (outlet_id, name, sku, unit, current_stock, min_stock, reorder_point, cost_price) VALUES
(1, 'Daging Sirloin Australia', 'INV-SIR-001', 'kg', 15.5, 5.0, 7.0, 120000),
(1, 'Beras Premium', 'INV-BER-001', 'kg', 50.0, 10.0, 15.0, 14000),
(1, 'Biji Kopi Arabica', 'INV-KOP-001', 'kg', 8.0, 2.0, 3.0, 180000),
(1, 'Kentang Frozen', 'INV-KEN-001', 'kg', 20.0, 5.0, 8.0, 25000),
(1, 'Susu Meiji', 'INV-SUS-001', 'Litre', 12.0, 4.0, 6.0, 28000),
(1, 'Sirup Lychee', 'INV-SYR-001', 'Bottle', 10.0, 2.0, 3.0, 65000);

-- 4. Menu Items
-- Link them to categories 20-24
INSERT IGNORE INTO menu_items (outlet_id, category_id, code, name, price, cost_price, is_active) VALUES
(1, 20, 'MC-001', 'Sirloin Steak 200g', 185000, 120000, true),
(1, 20, 'MC-002', 'Nasi Goreng Wagyu', 75000, 35000, true),
(1, 20, 'MC-003', 'Fettuccine Carbonara', 65000, 30000, true),
(1, 21, 'AP-001', 'Truffle Parmesan Fries', 45000, 15000, true),
(1, 21, 'AP-002', 'Fried Calamari', 55000, 25000, true),
(1, 22, 'DR-001', 'Iced Caffe Latte', 38000, 12000, true),
(1, 22, 'DR-002', 'Hot Cappuccino', 35000, 10000, true),
(1, 23, 'DR-003', 'Lychee Mojito', 42000, 15000, true),
(1, 24, 'DS-001', 'Chocolate Lava Cake', 45000, 18000, true);

-- 5. Tables (Indoor & Terrace)
-- Reset current tables to have a nice layout
DELETE FROM tables WHERE outlet_id = 1;
INSERT INTO tables (outlet_id, table_number, name, capacity, status, position_x, position_y, width, height) VALUES
(1, 'T1', 'Table 1', 2, 'available', 50, 50, 80, 80),
(1, 'T2', 'Table 2', 2, 'available', 150, 50, 80, 80),
(1, 'T3', 'Table 3', 4, 'occupied', 50, 150, 120, 80),
(1, 'T4', 'Table 4', 4, 'available', 180, 150, 120, 80),
(1, 'T5', 'Table 5', 6, 'available', 50, 250, 180, 80),
(1, 'VIP1', 'VIP Room 1', 10, 'available', 300, 50, 200, 150),
(1, 'OUT1', 'Terrace 1', 2, 'available', 300, 220, 80, 80),
(1, 'OUT2', 'Terrace 2', 2, 'available', 400, 220, 80, 80);

-- 6. Payment Methods (Ensuring standard methods)
INSERT IGNORE INTO payment_methods (id, outlet_id, name, type, is_active) VALUES
(1, NULL, 'Cash', 'cash', true),
(2, NULL, 'Debit Card', 'card', true),
(3, NULL, 'Credit Card', 'card', true),
(4, NULL, 'QRIS (GOPAY/OVO)', 'qris', true),
(5, NULL, 'Transfer Bank', 'transfer', true);
