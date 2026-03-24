-- ============================================
-- RestoQwen POS - Sample Inventory & Recipe Data
-- Compatible with actual schema structure
-- ============================================

USE posreato;

-- Clear existing data
DELETE FROM recipe_ingredients;
DELETE FROM inventory_items;

-- ============================================
-- INVENTORY ITEMS
-- Schema: outlet_id, name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, selling_price, is_active
-- ============================================

-- Meat (15 items)
INSERT INTO inventory_items (outlet_id, name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, selling_price, is_active) VALUES
(1, 'Daging Sapi Sirloin Premium', 'MEAT-001', 'kg', 25.00, 5.00, 50.00, 8.00, 150000.00, 0, 1),
(1, 'Daging Sapi Tenderloin', 'MEAT-002', 'kg', 15.00, 3.00, 30.00, 5.00, 200000.00, 0, 1),
(1, 'Daging Sapi Ribeye', 'MEAT-003', 'kg', 18.00, 4.00, 35.00, 6.00, 180000.00, 0, 1),
(1, 'Ayam Breast', 'POULTRY-001', 'kg', 15.00, 3.00, 25.00, 5.00, 45000.00, 0, 1),
(1, 'Ikan Salmon Fillet', 'FISH-001', 'kg', 8.00, 2.00, 15.00, 3.00, 250000.00, 0, 1);

-- Vegetables (10 items)
INSERT INTO inventory_items (outlet_id, name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, selling_price, is_active) VALUES
(1, 'Kentang', 'VEG-001', 'kg', 50.00, 10.00, 80.00, 15.00, 15000.00, 0, 1),
(1, 'Wortel', 'VEG-002', 'kg', 25.00, 5.00, 40.00, 8.00, 12000.00, 0, 1),
(1, 'Brokoli', 'VEG-003', 'kg', 15.00, 3.00, 25.00, 5.00, 18000.00, 0, 1),
(1, 'Bawang Bombay', 'VEG-004', 'kg', 20.00, 5.00, 30.00, 8.00, 18000.00, 0, 1),
(1, 'Bawang Putih', 'VEG-005', 'kg', 15.00, 3.00, 25.00, 5.00, 28000.00, 0, 1),
(1, 'Tomat', 'VEG-006', 'kg', 18.00, 5.00, 30.00, 8.00, 14000.00, 0, 1),
(1, 'Selada', 'VEG-007', 'kg', 8.00, 2.00, 15.00, 3.00, 12000.00, 0, 1),
(1, 'Jamur Champignon', 'VEG-008', 'kg', 8.00, 2.00, 15.00, 3.00, 45000.00, 0, 1),
(1, 'Asparagus', 'VEG-009', 'kg', 5.00, 1.00, 10.00, 2.00, 85000.00, 0, 1),
(1, 'Jagung Manis', 'VEG-010', 'kg', 15.00, 3.00, 25.00, 5.00, 18000.00, 0, 1);

-- Spices (10 items)
INSERT INTO inventory_items (outlet_id, name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, selling_price, is_active) VALUES
(1, 'Garam', 'SPICE-001', 'kg', 10.00, 2.00, 20.00, 3.00, 8000.00, 0, 1),
(1, 'Lada Hitam', 'SPICE-002', 'gr', 2000.00, 500.00, 5000.00, 1000.00, 120000.00, 0, 1),
(1, 'Gula Pasir', 'SUGAR-001', 'kg', 50.00, 10.00, 80.00, 15.00, 14000.00, 0, 1),
(1, 'Minyak Goreng', 'OIL-001', 'liter', 50.00, 10.00, 80.00, 15.00, 25000.00, 0, 1),
(1, 'Mentega', 'DAIRY-001', 'kg', 15.00, 3.00, 25.00, 5.00, 95000.00, 0, 1),
(1, 'Telur Ayam', 'DAIRY-002', 'pcs', 500.00, 100.00, 800.00, 150.00, 2500.00, 0, 1),
(1, 'Susu UHT Full Cream', 'DAIRY-003', 'liter', 30.00, 5.00, 50.00, 8.00, 18000.00, 0, 1),
(1, 'Keju Cheddar', 'DAIRY-004', 'kg', 8.00, 2.00, 15.00, 3.00, 120000.00, 0, 1),
(1, 'Saus Tomat', 'SAUCE-001', 'liter', 20.00, 5.00, 35.00, 8.00, 25000.00, 0, 1),
(1, 'Kecap Manis', 'SAUCE-002', 'liter', 15.00, 3.00, 25.00, 5.00, 22000.00, 0, 1);

-- Grains (5 items)
INSERT INTO inventory_items (outlet_id, name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, selling_price, is_active) VALUES
(1, 'Beras', 'GRAIN-001', 'kg', 100.00, 20.00, 150.00, 30.00, 12000.00, 0, 1),
(1, 'Tepung Terigu', 'GRAIN-002', 'kg', 50.00, 10.00, 80.00, 15.00, 15000.00, 0, 1),
(1, 'Pasta Spaghetti', 'GRAIN-003', 'kg', 25.00, 5.00, 40.00, 8.00, 22000.00, 0, 1),
(1, 'Roti Burger', 'GRAIN-004', 'pcs', 100.00, 20.00, 150.00, 30.00, 8000.00, 0, 1),
(1, 'Roti Tawar', 'GRAIN-005', 'pcs', 50.00, 10.00, 80.00, 15.00, 15000.00, 0, 1);

-- ============================================
-- RECIPE INGREDIENTS
-- Schema: menu_item_id, inventory_item_id, quantity, unit
-- ============================================

-- STEAK SIRLOIN (Menu Item ID: 1)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(1, 1, 0.250, 'kg'),  -- Sirloin 250gr
(1, 14, 0.010, 'liter'),  -- Minyak goreng 10ml
(1, 12, 0.002, 'kg'),  -- Lada hitam 2gr
(1, 11, 0.001, 'kg');  -- Garam 1gr

-- STEAK TENDERLOIN (Menu Item ID: 2)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(2, 2, 0.250, 'kg'),  -- Tenderloin 250gr
(2, 14, 0.010, 'liter'),
(2, 12, 0.002, 'kg'),
(2, 11, 0.001, 'kg');

-- STEAK RIBEYE (Menu Item ID: 3)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(3, 3, 0.300, 'kg'),  -- Ribeye 300gr
(3, 14, 0.010, 'liter'),
(3, 12, 0.002, 'kg'),
(3, 11, 0.001, 'kg');

-- NASI GORENG (Menu Item ID: 4)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(4, 21, 0.200, 'kg'),  -- Beras 200gr
(4, 4, 0.100, 'kg'),  -- Ayam breast 100gr
(4, 6, 0.050, 'kg'),  -- Bawang putih 50gr
(4, 5, 0.050, 'kg'),  -- Bawang bombay 50gr
(4, 20, 0.030, 'liter'),  -- Kecap manis 30ml
(4, 17, 2, 'pcs');  -- Telur 2 pcs

-- AYAM BAKAR (Menu Item ID: 5)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(5, 4, 0.250, 'kg'),  -- Ayam breast 250gr
(5, 20, 0.030, 'liter'),  -- Kecap manis 30ml
(5, 6, 0.030, 'kg');  -- Bawang putih 30gr

-- SALMON GRILLED (Menu Item ID: 6)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(6, 5, 0.200, 'kg'),  -- Salmon 200gr
(6, 14, 0.015, 'liter'),  -- Minyak goreng 15ml
(6, 11, 0.001, 'kg'),  -- Garam 1gr
(6, 12, 0.002, 'kg');  -- Lada hitam 2gr

-- SPAGHETTI BOLOGNESE (Menu Item ID: 7)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(7, 23, 0.150, 'kg'),  -- Spaghetti 150gr
(7, 1, 0.150, 'kg'),  -- Sirloin 150gr
(7, 19, 0.100, 'liter'),  -- Saus tomat 100ml
(7, 16, 0.050, 'kg'),  -- Keju cheddar 50gr
(7, 6, 0.030, 'kg');  -- Bawang putih 30gr

-- BURGER SPECIAL (Menu Item ID: 8)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(8, 24, 2, 'pcs'),  -- Roti burger 2 pcs
(8, 1, 0.150, 'kg'),  -- Sirloin 150gr
(8, 17, 1, 'pcs'),  -- Telur 1 pcs
(8, 16, 0.030, 'kg'),  -- Keju cheddar 30gr
(8, 8, 0.050, 'kg');  -- Selada 50gr

-- FRENCH FRIES (Menu Item ID: 9)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(9, 7, 0.200, 'kg'),  -- Kentang 200gr
(9, 14, 0.500, 'liter'),  -- Minyak goreng 500ml
(9, 11, 0.002, 'kg');  -- Garam 2gr

-- CAESAR SALAD (Menu Item ID: 10)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(10, 8, 0.100, 'kg'),  -- Selada 100gr
(10, 4, 0.100, 'kg'),  -- Ayam breast 100gr
(10, 16, 0.050, 'kg'),  -- Keju cheddar 50gr
(10, 15, 0.050, 'kg');  -- Mentega 50gr

SELECT 'Sample inventory and recipe data inserted successfully!' AS status;
SELECT CONCAT('Total inventory items: ', COUNT(*)) as info FROM inventory_items;
SELECT CONCAT('Total recipe ingredients: ', COUNT(*)) as info FROM recipe_ingredients;
