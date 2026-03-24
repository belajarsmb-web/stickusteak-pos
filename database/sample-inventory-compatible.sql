-- ============================================
-- Sample Inventory Data - Compatible Version
-- For existing database without selling_price column
-- ============================================

USE posreato;

-- Clear existing data
DELETE FROM recipe_ingredients;

-- Check if inventory_items table has the right structure
SELECT 'Checking inventory_items table structure...' AS status;

-- Insert inventory items (without selling_price)
INSERT INTO inventory_items (outlet_id, name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
(1, 'Daging Sapi Sirloin', 'MEAT-001', 'kg', 25.00, 5.00, 50.00, 8.00, 150000.00, 1),
(1, 'Daging Sapi Tenderloin', 'MEAT-002', 'kg', 15.00, 3.00, 30.00, 5.00, 200000.00, 1),
(1, 'Daging Sapi Ribeye', 'MEAT-003', 'kg', 18.00, 4.00, 35.00, 6.00, 180000.00, 1),
(1, 'Ayam Breast', 'POULTRY-001', 'kg', 15.00, 3.00, 25.00, 5.00, 45000.00, 1),
(1, 'Ikan Salmon Fillet', 'FISH-001', 'kg', 8.00, 2.00, 15.00, 3.00, 250000.00, 1),
(1, 'Kentang', 'VEG-001', 'kg', 50.00, 10.00, 80.00, 15.00, 15000.00, 1),
(1, 'Wortel', 'VEG-002', 'kg', 25.00, 5.00, 40.00, 8.00, 12000.00, 1),
(1, 'Brokoli', 'VEG-003', 'kg', 15.00, 3.00, 25.00, 5.00, 18000.00, 1),
(1, 'Bawang Bombay', 'VEG-004', 'kg', 20.00, 5.00, 30.00, 8.00, 18000.00, 1),
(1, 'Bawang Putih', 'VEG-005', 'kg', 15.00, 3.00, 25.00, 5.00, 28000.00, 1),
(1, 'Tomat', 'VEG-006', 'kg', 18.00, 5.00, 30.00, 8.00, 14000.00, 1),
(1, 'Selada', 'VEG-007', 'kg', 8.00, 2.00, 15.00, 3.00, 12000.00, 1),
(1, 'Garam', 'SPICE-001', 'kg', 10.00, 2.00, 20.00, 3.00, 8000.00, 1),
(1, 'Lada Hitam', 'SPICE-002', 'gr', 2000.00, 500.00, 5000.00, 1000.00, 120000.00, 1),
(1, 'Gula Pasir', 'SUGAR-001', 'kg', 50.00, 10.00, 80.00, 15.00, 14000.00, 1),
(1, 'Minyak Goreng', 'OIL-001', 'liter', 50.00, 10.00, 80.00, 15.00, 25000.00, 1),
(1, 'Mentega', 'DAIRY-001', 'kg', 15.00, 3.00, 25.00, 5.00, 95000.00, 1),
(1, 'Telur Ayam', 'DAIRY-002', 'pcs', 500.00, 100.00, 800.00, 150.00, 2500.00, 1),
(1, 'Susu UHT', 'DAIRY-003', 'liter', 30.00, 5.00, 50.00, 8.00, 18000.00, 1),
(1, 'Keju Cheddar', 'DAIRY-004', 'kg', 8.00, 2.00, 15.00, 3.00, 120000.00, 1),
(1, 'Saus Tomat', 'SAUCE-001', 'liter', 20.00, 5.00, 35.00, 8.00, 25000.00, 1),
(1, 'Kecap Manis', 'SAUCE-002', 'liter', 15.00, 3.00, 25.00, 5.00, 22000.00, 1),
(1, 'Beras', 'GRAIN-001', 'kg', 100.00, 20.00, 150.00, 30.00, 12000.00, 1),
(1, 'Tepung Terigu', 'GRAIN-002', 'kg', 50.00, 10.00, 80.00, 15.00, 15000.00, 1),
(1, 'Pasta Spaghetti', 'GRAIN-003', 'kg', 25.00, 5.00, 40.00, 8.00, 22000.00, 1),
(1, 'Roti Burger', 'GRAIN-004', 'pcs', 100.00, 20.00, 150.00, 30.00, 8000.00, 1);

-- Insert recipe ingredients
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
-- Steak Sirloin (menu_item_id: 1)
(1, 1, 0.250, 'kg'),
(1, 16, 0.010, 'liter'),
(1, 14, 0.002, 'kg'),
(1, 13, 0.001, 'kg'),
-- Steak Tenderloin (menu_item_id: 2)
(2, 2, 0.250, 'kg'),
(2, 16, 0.010, 'liter'),
(2, 14, 0.002, 'kg'),
(2, 13, 0.001, 'kg'),
-- Steak Ribeye (menu_item_id: 3)
(3, 3, 0.300, 'kg'),
(3, 16, 0.010, 'liter'),
(3, 14, 0.002, 'kg'),
(3, 13, 0.001, 'kg');

SELECT 'Sample data inserted successfully!' AS status;
SELECT CONCAT('Total inventory items: ', COUNT(*)) as info FROM inventory_items;
SELECT CONCAT('Total recipe ingredients: ', COUNT(*)) as info FROM recipe_ingredients;
