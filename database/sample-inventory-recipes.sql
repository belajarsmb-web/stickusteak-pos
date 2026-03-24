-- ============================================
-- RestoQwen POS - Sample Inventory & Recipe Data
-- Complete inventory items and recipes for steak restaurant
-- ============================================

USE posreato;

-- ============================================
-- CLEAR EXISTING DATA (for clean import)
-- ============================================
DELETE FROM recipe_ingredients;
DELETE FROM inventory_items;

-- ============================================
-- 1. INVENTORY ITEMS (Bahan Baku)
-- Note: Table structure has: id, outlet_id, name, sku, unit, 
--       current_stock, min_stock, max_stock, reorder_point, 
--       cost_price, selling_price, is_active
-- ============================================

-- DAGING & PROTEIN (Meat & Protein)
INSERT INTO inventory_items (outlet_id, name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, selling_price, is_active) VALUES
(1, 'Daging Sapi Sirloin Premium', 'MEAT-001', 'kg', 25.00, 5.00, 50.00, 8.00, 150000.00, 0, 1),
(1, 'Daging Sapi Tenderloin', 'MEAT-002', 'kg', 15.00, 3.00, 30.00, 5.00, 200000.00, 0, 1),
(1, 'Daging Sapi Ribeye', 'MEAT-003', 'kg', 18.00, 4.00, 35.00, 6.00, 180000.00, 0, 1),
(1, 'Daging Sapi Striploin', 'MEAT-004', 'kg', 20.00, 4.00, 40.00, 7.00, 170000.00, 0, 1),
(1, 'Daging Sapi T-Bone', 'MEAT-005', 'kg', 12.00, 3.00, 25.00, 5.00, 190000.00, 0, 1),
(1, 'Ikan Salmon Fillet', 'FISH-001', 'kg', 8.00, 2.00, 15.00, 3.00, 250000.00, 0, 1),
(1, 'Ikan Dory Fillet', 'FISH-002', 'kg', 10.00, 3.00, 20.00, 5.00, 120000.00, 0, 1),
(1, 'Udang Windu Besar', 'SEAFOOD-001', 'kg', 5.00, 1.00, 10.00, 2.00, 180000.00, 0, 1),
(1, 'Ayam Breast', 'POULTRY-001', 'kg', 15.00, 3.00, 25.00, 5.00, 45000.00, 0, 1),
(1, 'Ayam Whole Chicken', 'POULTRY-002', 'kg', 20.00, 5.00, 30.00, 8.00, 35000.00, 0, 1),
(1, 'Daging Kambing', 'MEAT-006', 'kg', 8.00, 2.00, 15.00, 3.00, 140000.00, 0, 1),
(1, 'Sosis Sapi Premium', 'MEAT-007', 'kg', 10.00, 3.00, 20.00, 5.00, 65000.00, 0, 1),
(1, 'Bacon', 'MEAT-008', 'kg', 6.00, 2.00, 12.00, 3.00, 95000.00, 0, 1),
(1, 'Hati Ayam', 'POULTRY-003', 'kg', 5.00, 1.00, 10.00, 2.00, 35000.00, 0, 1),
(1, 'Cumi-Cumi Segar', 'SEAFOOD-002', 'kg', 4.00, 1.00, 8.00, 2.00, 85000.00, 0, 1);

-- SAYURAN (Vegetables)
INSERT INTO inventory_items (name, sku, category, unit, base_unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Kentang', 'VEG-001', 'VEG', 'kg', 'kg', 50.000, 10.000, 80.000, 15.000, 15000.00, 1),
('Wortel', 'VEG-002', 'VEG', 'kg', 'kg', 25.000, 5.000, 40.000, 8.000, 12000.00, 1),
('Brokoli', 'VEG-003', 'VEG', 'kg', 'kg', 15.000, 3.000, 25.000, 5.000, 18000.00, 1),
('Buncis', 'VEG-004', 'VEG', 'kg', 'kg', 10.000, 2.000, 18.000, 3.000, 16000.00, 1),
('Jamur Champignon', 'VEG-005', 'VEG', 'kg', 'kg', 8.000, 2.000, 15.000, 3.000, 45000.00, 1),
('Jamur Enoki', 'VEG-006', 'VEG', 'kg', 'kg', 3.000, 1.000, 6.000, 1.500, 65000.00, 1),
('Bawang Bombay', 'VEG-007', 'VEG', 'kg', 'kg', 20.000, 5.000, 30.000, 8.000, 18000.00, 1),
('Bawang Putih', 'VEG-008', 'VEG', 'kg', 'kg', 15.000, 3.000, 25.000, 5.000, 28000.00, 1),
('Bawang Merah', 'VEG-009', 'VEG', 'kg', 'kg', 12.000, 3.000, 20.000, 5.000, 32000.00, 1),
('Tomat', 'VEG-010', 'VEG', 'kg', 'kg', 18.000, 5.000, 30.000, 8.000, 14000.00, 1),
('Selada', 'VEG-011', 'VEG', 'kg', 'kg', 8.000, 2.000, 15.000, 3.000, 12000.00, 1),
('Paprika Merah', 'VEG-012', 'VEG', 'kg', 'kg', 6.000, 1.500, 12.000, 2.000, 42000.00, 1),
('Paprika Kuning', 'VEG-013', 'VEG', 'kg', 'kg', 6.000, 1.500, 12.000, 2.000, 42000.00, 1),
('Paprika Hijau', 'VEG-014', 'VEG', 'kg', 'kg', 6.000, 1.500, 12.000, 2.000, 38000.00, 1),
('Asparagus', 'VEG-015', 'VEG', 'kg', 'kg', 5.000, 1.000, 10.000, 2.000, 85000.00, 1),
('Zucchini', 'VEG-016', 'VEG', 'kg', 'kg', 8.000, 2.000, 15.000, 3.000, 28000.00, 1),
('Jagung Manis', 'VEG-017', 'VEG', 'kg', 'kg', 15.000, 3.000, 25.000, 5.000, 18000.00, 1),
('Bayam', 'VEG-018', 'VEG', 'kg', 'kg', 6.000, 1.500, 12.000, 2.000, 10000.00, 1),
('Kol', 'VEG-019', 'VEG', 'kg', 'kg', 20.000, 5.000, 35.000, 8.000, 8000.00, 1),
('Timun', 'VEG-020', 'VEG', 'kg', 'kg', 15.000, 3.000, 25.000, 5.000, 10000.00, 1);

-- BUMBU & REMPAH (Spices & Herbs)
INSERT INTO inventory_items (name, sku, category, unit, base_unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Garam', 'SPICE-001', 'SPICE', 'kg', 'kg', 10.000, 2.000, 20.000, 3.000, 8000.00, 1),
('Lada Hitam', 'SPICE-002', 'SPICE', 'gr', 'gr', 2000.000, 500.000, 5000.000, 1000.000, 120000.00, 1),
('Lada Putih', 'SPICE-003', 'SPICE', 'gr', 'gr', 1500.000, 400.000, 4000.000, 800.000, 100000.00, 1),
('Rosemary Segar', 'HERB-001', 'HERB', 'gr', 'gr', 500.000, 100.000, 1000.000, 200.000, 65000.00, 1),
('Thyme Segar', 'HERB-002', 'HERB', 'gr', 'gr', 400.000, 100.000, 800.000, 200.000, 55000.00, 1),
('Oregano', 'HERB-003', 'HERB', 'gr', 'gr', 300.000, 50.000, 600.000, 100.000, 45000.00, 1),
('Parsley', 'HERB-004', 'HERB', 'gr', 'gr', 500.000, 100.000, 1000.000, 200.000, 40000.00, 1),
('Basil', 'HERB-005', 'HERB', 'gr', 'gr', 300.000, 50.000, 600.000, 100.000, 50000.00, 1),
('Kayu Manis', 'SPICE-004', 'SPICE', 'gr', 'gr', 200.000, 50.000, 400.000, 100.000, 35000.00, 1),
('Pala', 'SPICE-005', 'SPICE', 'gr', 'gr', 150.000, 30.000, 300.000, 50.000, 45000.00, 1),
('Cengkeh', 'SPICE-006', 'SPICE', 'gr', 'gr', 100.000, 20.000, 200.000, 40.000, 55000.00, 1),
('Jinten', 'SPICE-007', 'SPICE', 'gr', 'gr', 150.000, 30.000, 300.000, 50.000, 40000.00, 1),
('Ketumbar', 'SPICE-008', 'SPICE', 'gr', 'gr', 200.000, 50.000, 400.000, 100.000, 35000.00, 1),
('Jahe', 'SPICE-009', 'SPICE', 'kg', 'kg', 5.000, 1.000, 10.000, 2.000, 25000.00, 1),
('Kunyit', 'SPICE-010', 'SPICE', 'kg', 'kg', 4.000, 1.000, 8.000, 2.000, 22000.00, 1),
('Lengkuas', 'SPICE-011', 'SPICE', 'kg', 'kg', 3.000, 1.000, 6.000, 1.500, 20000.00, 1),
('Serai', 'SPICE-012', 'SPICE', 'kg', 'kg', 2.000, 0.500, 4.000, 1.000, 18000.00, 1),
('Daun Jeruk', 'SPICE-013', 'SPICE', 'kg', 'kg', 1.500, 0.300, 3.000, 0.500, 35000.00, 1),
('Daun Salam', 'SPICE-014', 'SPICE', 'kg', 'kg', 1.000, 0.200, 2.000, 0.400, 30000.00, 1);

-- MINYAK & SAUS (Oils & Sauces)
INSERT INTO inventory_items (name, sku, category, unit, base_unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Minyak Goreng', 'OIL-001', 'OIL', 'liter', 'liter', 50.000, 10.000, 80.000, 15.000, 25000.00, 1),
('Minyak Zaitun', 'OIL-002', 'OIL', 'liter', 'liter', 10.000, 2.000, 20.000, 3.000, 120000.00, 1),
('Minyak Wijen', 'OIL-003', 'OIL', 'liter', 'liter', 5.000, 1.000, 10.000, 2.000, 85000.00, 1),
('Mentega', 'DAIRY-001', 'DAIRY', 'kg', 'kg', 15.000, 3.000, 25.000, 5.000, 95000.00, 1),
('Margarin', 'DAIRY-002', 'DAIRY', 'kg', 'kg', 10.000, 2.000, 18.000, 3.000, 45000.00, 1),
('Susu UHT Full Cream', 'DAIRY-003', 'DAIRY', 'liter', 'liter', 30.000, 5.000, 50.000, 8.000, 18000.00, 1),
('Keju Cheddar', 'DAIRY-004', 'DAIRY', 'kg', 'kg', 8.000, 2.000, 15.000, 3.000, 120000.00, 1),
('Keju Mozzarella', 'DAIRY-005', 'DAIRY', 'kg', 'kg', 6.000, 1.500, 12.000, 2.500, 140000.00, 1),
('Keju Parmesan', 'DAIRY-006', 'DAIRY', 'kg', 'kg', 3.000, 1.000, 6.000, 1.500, 180000.00, 1),
('Telur Ayam', 'DAIRY-007', 'DAIRY', 'pcs', 'pcs', 500.000, 100.000, 800.000, 150.000, 2500.00, 1),
('Saus Tomat', 'SAUCE-001', 'SAUCE', 'liter', 'liter', 20.000, 5.000, 35.000, 8.000, 25000.00, 1),
('Saus Sambal', 'SAUCE-002', 'SAUCE', 'liter', 'liter', 15.000, 3.000, 25.000, 5.000, 28000.00, 1),
('Saus Tiram', 'SAUCE-003', 'SAUCE', 'liter', 'liter', 10.000, 2.000, 18.000, 3.000, 35000.00, 1),
('Saus BBQ', 'SAUCE-004', 'SAUCE', 'liter', 'liter', 8.000, 2.000, 15.000, 3.000, 42000.00, 1),
('Saus Steak Hitam', 'SAUCE-005', 'SAUCE', 'liter', 'liter', 6.000, 1.500, 12.000, 2.000, 55000.00, 1),
('Saus Mushroom', 'SAUCE-006', 'SAUCE', 'liter', 'liter', 5.000, 1.000, 10.000, 2.000, 48000.00, 1),
('Saus Lada Hitam', 'SAUCE-007', 'SAUCE', 'liter', 'liter', 5.000, 1.000, 10.000, 2.000, 52000.00, 1),
('Kecap Manis', 'SAUCE-008', 'SAUCE', 'liter', 'liter', 15.000, 3.000, 25.000, 5.000, 22000.00, 1),
('Kecap Asin', 'SAUCE-009', 'SAUCE', 'liter', 'liter', 8.000, 2.000, 15.000, 3.000, 28000.00, 1),
('Cuka Makan', 'SAUCE-010', 'SAUCE', 'liter', 'liter', 10.000, 2.000, 18.000, 3.000, 18000.00, 1),
('Mayones', 'SAUCE-011', 'SAUCE', 'liter', 'liter', 12.000, 3.000, 20.000, 5.000, 32000.00, 1),
('Saus Caesar', 'SAUCE-012', 'SAUCE', 'liter', 'liter', 5.000, 1.000, 10.000, 2.000, 65000.00, 1),
('Saus Ranch', 'SAUCE-013', 'SAUCE', 'liter', 'liter', 5.000, 1.000, 10.000, 2.000, 62000.00, 1),
('Saus Tartar', 'SAUCE-014', 'SAUCE', 'liter', 'liter', 4.000, 1.000, 8.000, 1.500, 58000.00, 1),
('Saus Hollandaise', 'SAUCE-015', 'SAUCE', 'liter', 'liter', 3.000, 0.500, 6.000, 1.000, 75000.00, 1);

-- BIJIAN & TEPUNG (Grains & Flour)
INSERT INTO inventory_items (name, sku, category, unit, base_unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Beras', 'GRAIN-001', 'GRAIN', 'kg', 'kg', 100.000, 20.000, 150.000, 30.000, 12000.00, 1),
('Tepung Terigu', 'GRAIN-002', 'GRAIN', 'kg', 'kg', 50.000, 10.000, 80.000, 15.000, 15000.00, 1),
('Tepung Roti', 'GRAIN-003', 'GRAIN', 'kg', 'kg', 20.000, 5.000, 35.000, 8.000, 18000.00, 1),
('Tepung Maizena', 'GRAIN-004', 'GRAIN', 'kg', 'kg', 15.000, 3.000, 25.000, 5.000, 16000.00, 1),
('Pasta Spaghetti', 'GRAIN-005', 'GRAIN', 'kg', 'kg', 25.000, 5.000, 40.000, 8.000, 22000.00, 1),
('Pasta Fettuccine', 'GRAIN-006', 'GRAIN', 'kg', 'kg', 15.000, 3.000, 25.000, 5.000, 24000.00, 1),
('Pasta Penne', 'GRAIN-007', 'GRAIN', 'kg', 'kg', 10.000, 2.000, 18.000, 3.000, 26000.00, 1),
('Roti Tawar', 'GRAIN-008', 'GRAIN', 'pcs', 'pcs', 50.000, 10.000, 80.000, 15.000, 15000.00, 1),
('Roti Burger', 'GRAIN-009', 'GRAIN', 'pcs', 'pcs', 100.000, 20.000, 150.000, 30.000, 8000.00, 1),
('Oatmeal', 'GRAIN-010', 'GRAIN', 'kg', 'kg', 10.000, 2.000, 18.000, 3.000, 35000.00, 1);

-- GULA & PEMANIS (Sugar & Sweeteners)
INSERT INTO inventory_items (name, sku, category, unit, base_unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Gula Pasir', 'SUGAR-001', 'SUGAR', 'kg', 'kg', 50.000, 10.000, 80.000, 15.000, 14000.00, 1),
('Gula Halus', 'SUGAR-002', 'SUGAR', 'kg', 'kg', 20.000, 5.000, 35.000, 8.000, 16000.00, 1),
('Gula Aren', 'SUGAR-003', 'SUGAR', 'kg', 'kg', 10.000, 2.000, 18.000, 3.000, 25000.00, 1),
('Madu', 'SUGAR-004', 'SUGAR', 'liter', 'liter', 8.000, 2.000, 15.000, 3.000, 85000.00, 1),
('Sirup Vanilla', 'SUGAR-005', 'SUGAR', 'liter', 'liter', 5.000, 1.000, 10.000, 2.000, 95000.00, 1),
('Sirup Coklat', 'SUGAR-006', 'SUGAR', 'liter', 'liter', 6.000, 1.500, 12.000, 2.500, 65000.00, 1),
('Sirup Strawberry', 'SUGAR-007', 'SUGAR', 'liter', 'liter', 5.000, 1.000, 10.000, 2.000, 68000.00, 1),
('Sirup Caramel', 'SUGAR-008', 'SUGAR', 'liter', 'liter', 5.000, 1.000, 10.000, 2.000, 72000.00, 1);

-- KOPI & TEH (Coffee & Tea)
INSERT INTO inventory_items (name, sku, category, unit, base_unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Kopi Arabica', 'COFFEE-001', 'COFFEE', 'kg', 'kg', 10.000, 2.000, 18.000, 3.000, 250000.00, 1),
('Kopi Robusta', 'COFFEE-002', 'COFFEE', 'kg', 'kg', 8.000, 2.000, 15.000, 3.000, 180000.00, 1),
('Teh Hitam', 'TEA-001', 'TEA', 'kg', 'kg', 5.000, 1.000, 10.000, 2.000, 120000.00, 1),
('Teh Hijau', 'TEA-002', 'TEA', 'kg', 'kg', 3.000, 0.500, 6.000, 1.000, 150000.00, 1),
('Teh Chamomile', 'TEA-003', 'TEA', 'kg', 'kg', 2.000, 0.300, 4.000, 0.500, 180000.00, 1),
('Coklat Bubuk', 'COFFEE-003', 'COFFEE', 'kg', 'kg', 8.000, 2.000, 15.000, 3.000, 95000.00, 1),
('Creamer', 'COFFEE-004', 'COFFEE', 'kg', 'kg', 10.000, 2.000, 18.000, 3.000, 65000.00, 1);

-- BAHAN KUE & DESSERT (Baking & Dessert)
INSERT INTO inventory_items (name, sku, category, unit, base_unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Coklat Batang Dark', 'BAKE-001', 'BAKE', 'kg', 'kg', 8.000, 2.000, 15.000, 3.000, 120000.00, 1),
('Coklat Batang Milk', 'BAKE-002', 'BAKE', 'kg', 'kg', 6.000, 1.500, 12.000, 2.500, 110000.00, 1),
('Coklat Batang White', 'BAKE-003', 'BAKE', 'kg', 'kg', 5.000, 1.000, 10.000, 2.000, 115000.00, 1),
('Kacang Almond', 'BAKE-004', 'BAKE', 'kg', 'kg', 5.000, 1.000, 10.000, 2.000, 180000.00, 1),
('Kacang Mete', 'BAKE-005', 'BAKE', 'kg', 'kg', 4.000, 1.000, 8.000, 1.500, 160000.00, 1),
('Kacang Kenari', 'BAKE-006', 'BAKE', 'kg', 'kg', 3.000, 0.500, 6.000, 1.000, 220000.00, 1),
('Kismis', 'BAKE-007', 'BAKE', 'kg', 'kg', 4.000, 1.000, 8.000, 1.500, 85000.00, 1),
('Kurma', 'BAKE-008', 'BAKE', 'kg', 'kg', 5.000, 1.000, 10.000, 2.000, 95000.00, 1),
('Vanilla Extract', 'BAKE-009', 'BAKE', 'ml', 'ml', 500.000, 100.000, 1000.000, 200.000, 150000.00, 1),
('Baking Powder', 'BAKE-010', 'BAKE', 'kg', 'kg', 5.000, 1.000, 10.000, 2.000, 45000.00, 1),
('Soda Kue', 'BAKE-011', 'BAKE', 'kg', 'kg', 5.000, 1.000, 10.000, 2.000, 42000.00, 1),
('Gelatin', 'BAKE-012', 'BAKE', 'kg', 'kg', 3.000, 0.500, 6.000, 1.000, 120000.00, 1),
('Whipping Cream', 'DAIRY-008', 'DAIRY', 'liter', 'liter', 15.000, 3.000, 25.000, 5.000, 45000.00, 1),
('Yoghurt Plain', 'DAIRY-009', 'DAIRY', 'kg', 'kg', 10.000, 2.000, 18.000, 3.000, 35000.00, 1),
('Es Krim Vanilla', 'DAIRY-010', 'DAIRY', 'liter', 'liter', 20.000, 5.000, 35.000, 8.000, 55000.00, 1),
('Es Krim Coklat', 'DAIRY-011', 'DAIRY', 'liter', 'liter', 15.000, 3.000, 25.000, 5.000, 58000.00, 1),
('Es Krim Strawberry', 'DAIRY-012', 'DAIRY', 'liter', 'liter', 12.000, 3.000, 20.000, 5.000, 60000.00, 1);

-- BUAH-BUAHAN (Fruits)
INSERT INTO inventory_items (name, sku, category, unit, base_unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Apel Fuji', 'FRUIT-001', 'FRUIT', 'kg', 'kg', 30.000, 5.000, 50.000, 8.000, 35000.00, 1),
('Apel Malang', 'FRUIT-002', 'FRUIT', 'kg', 'kg', 25.000, 5.000, 40.000, 8.000, 28000.00, 1),
('Pisang', 'FRUIT-003', 'FRUIT', 'kg', 'kg', 40.000, 10.000, 60.000, 15.000, 18000.00, 1),
('Jeruk', 'FRUIT-004', 'FRUIT', 'kg', 'kg', 35.000, 8.000, 55.000, 12.000, 22000.00, 1),
('Anggur', 'FRUIT-005', 'FRUIT', 'kg', 'kg', 15.000, 3.000, 25.000, 5.000, 45000.00, 1),
('Melon', 'FRUIT-006', 'FRUIT', 'kg', 'kg', 20.000, 5.000, 35.000, 8.000, 15000.00, 1),
('Semangka', 'FRUIT-007', 'FRUIT', 'kg', 'kg', 25.000, 5.000, 40.000, 8.000, 12000.00, 1),
('Nanas', 'FRUIT-008', 'FRUIT', 'kg', 'kg', 15.000, 3.000, 25.000, 5.000, 18000.00, 1),
('Mangga', 'FRUIT-009', 'FRUIT', 'kg', 'kg', 20.000, 5.000, 35.000, 8.000, 25000.00, 1),
('Pepaya', 'FRUIT-010', 'FRUIT', 'kg', 'kg', 15.000, 3.000, 25.000, 5.000, 12000.00, 1),
('Stroberi', 'FRUIT-011', 'FRUIT', 'kg', 'kg', 8.000, 2.000, 15.000, 3.000, 65000.00, 1),
('Blueberry', 'FRUIT-012', 'FRUIT', 'kg', 'kg', 3.000, 0.500, 6.000, 1.000, 180000.00, 1),
('Kiwi', 'FRUIT-013', 'FRUIT', 'kg', 'kg', 5.000, 1.000, 10.000, 2.000, 55000.00, 1),
('Alpukat', 'FRUIT-014', 'FRUIT', 'kg', 'kg', 15.000, 3.000, 25.000, 5.000, 35000.00, 1),
('Lemon', 'FRUIT-015', 'FRUIT', 'kg', 'kg', 10.000, 2.000, 18.000, 3.000, 45000.00, 1),
('Limau Nipis', 'FRUIT-016', 'FRUIT', 'kg', 'kg', 8.000, 2.000, 15.000, 3.000, 38000.00, 1);

-- ============================================
-- 2. RECIPE INGREDIENTS (Resep Menu Items)
-- ============================================

-- STEAK SIRLOIN (Menu Item ID: 1)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(1, 1, 0.250, 'kg', 150000.00, NOW()),  -- Sirloin 250gr
(1, 46, 0.010, 'liter', 120000.00, NOW()),  -- Minyak zaitun 10ml
(1, 2, 0.002, 'kg', 120000.00, NOW()),  -- Lada hitam 2gr
(1, 1, 0.001, 'kg', 8000.00, NOW());  -- Garam 1gr

-- STEAK TENDERLOIN (Menu Item ID: 2)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(2, 2, 0.250, 'kg', 200000.00, NOW()),  -- Tenderloin 250gr
(2, 46, 0.010, 'liter', 120000.00, NOW()),  -- Minyak zaitun 10ml
(2, 2, 0.002, 'kg', 120000.00, NOW()),  -- Lada hitam 2gr
(2, 1, 0.001, 'kg', 8000.00, NOW());  -- Garam 1gr

-- STEAK RIBEYE (Menu Item ID: 3)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(3, 3, 0.300, 'kg', 180000.00, NOW()),  -- Ribeye 300gr
(3, 46, 0.010, 'liter', 120000.00, NOW()),  -- Minyak zaitun 10ml
(3, 2, 0.002, 'kg', 120000.00, NOW()),  -- Lada hitam 2gr
(3, 1, 0.001, 'kg', 8000.00, NOW());  -- Garam 1gr

-- NASI GORENG (Menu Item ID: 4)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(4, 50, 0.200, 'kg', 12000.00, NOW()),  -- Beras 200gr
(4, 9, 0.100, 'kg', 45000.00, NOW()),  -- Ayam breast 100gr
(4, 8, 0.050, 'kg', 28000.00, NOW()),  -- Bawang putih 50gr
(4, 7, 0.050, 'kg', 18000.00, NOW()),  -- Bawang bombay 50gr
(4, 57, 0.030, 'liter', 22000.00, NOW()),  -- Kecap manis 30ml
(4, 60, 0.020, 'liter', 32000.00, NOW()),  -- Mayones 20ml
(4, 14, 0.050, 'kg', 14000.00, NOW()),  -- Tomat 50gr
(4, 127, 2, 'pcs', 2500.00, NOW());  -- Telur 2 pcs

-- AYAM BAKAR MADU (Menu Item ID: 5)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(5, 9, 0.250, 'kg', 45000.00, NOW()),  -- Ayam breast 250gr
(5, 134, 0.050, 'liter', 85000.00, NOW()),  -- Madu 50ml
(5, 57, 0.030, 'liter', 22000.00, NOW()),  -- Kecap manis 30ml
(5, 8, 0.030, 'kg', 28000.00, NOW()),  -- Bawang putih 30gr
(5, 64, 0.001, 'kg', 25000.00, NOW()),  -- Jahe 1gr
(5, 65, 0.001, 'kg', 22000.00, NOW());  -- Kunyit 1gr

-- SALMON GRILLED (Menu Item ID: 6)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(6, 6, 0.200, 'kg', 250000.00, NOW()),  -- Salmon 200gr
(6, 46, 0.015, 'liter', 120000.00, NOW()),  -- Minyak zaitun 15ml
(6, 141, 0.030, 'kg', 45000.00, NOW()),  -- Lemon 30gr
(6, 2, 0.002, 'kg', 120000.00, NOW()),  -- Lada hitam 2gr
(6, 1, 0.001, 'kg', 8000.00, NOW()),  -- Garam 1gr
(6, 24, 0.050, 'kg', 85000.00, NOW());  -- Asparagus 50gr

-- SPAGHETTI BOLOGNESE (Menu Item ID: 7)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(7, 55, 0.150, 'kg', 22000.00, NOW()),  -- Spaghetti 150gr
(7, 1, 0.150, 'kg', 150000.00, NOW()),  -- Sirloin 150gr
(7, 61, 0.100, 'liter', 25000.00, NOW()),  -- Saus tomat 100ml
(7, 8, 0.030, 'kg', 28000.00, NOW()),  -- Bawang putih 30gr
(7, 7, 0.050, 'kg', 18000.00, NOW()),  -- Bawang bombay 50gr
(7, 47, 0.050, 'kg', 95000.00, NOW()),  -- Mentega 50gr
(7, 2, 0.001, 'kg', 120000.00, NOW()),  -- Lada hitam 1gr
(7, 1, 0.001, 'kg', 8000.00, NOW());  -- Garam 1gr

-- BURGER SPECIAL (Menu Item ID: 8)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(8, 59, 2, 'pcs', 8000.00, NOW()),  -- Roti burger 2 pcs
(8, 1, 0.150, 'kg', 150000.00, NOW()),  -- Sirloin 150gr
(8, 127, 1, 'pcs', 2500.00, NOW()),  -- Telur 1 pcs
(8, 36, 0.030, 'kg', 120000.00, NOW()),  -- Cheddar 30gr
(8, 11, 0.050, 'kg', 12000.00, NOW()),  -- Selada 50gr
(8, 14, 0.050, 'kg', 14000.00, NOW()),  -- Tomat 50gr
(8, 60, 0.030, 'liter', 32000.00, NOW());  -- Mayones 30ml

-- FRENCH FRIES (Menu Item ID: 9)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(9, 21, 0.200, 'kg', 15000.00, NOW()),  -- Kentang 200gr
(9, 51, 0.500, 'liter', 25000.00, NOW()),  -- Minyak goreng 500ml
(9, 1, 0.002, 'kg', 8000.00, NOW());  -- Garam 2gr

-- CAESAR SALAD (Menu Item ID: 10)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(10, 11, 0.100, 'kg', 12000.00, NOW()),  -- Selada 100gr
(10, 9, 0.100, 'kg', 45000.00, NOW()),  -- Ayam breast 100gr
(10, 38, 0.050, 'kg', 180000.00, NOW()),  -- Parmesan 50gr
(10, 72, 0.050, 'liter', 65000.00, NOW()),  -- Saus caesar 50ml
(10, 47, 0.020, 'kg', 95000.00, NOW());  -- Mentega 20gr

-- ICE CREAM SUNDAE (Menu Item ID: 11)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(11, 144, 0.150, 'liter', 55000.00, NOW()),  -- Es krim vanilla 150ml
(11, 136, 0.030, 'liter', 65000.00, NOW()),  -- Sirup coklat 30ml
(11, 139, 0.020, 'kg', 180000.00, NOW()),  -- Almond 20gr
(11, 141, 0.030, 'kg', 35000.00, NOW());  -- Stroberi 30gr

-- KOPI LATTE (Menu Item ID: 12)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(12, 119, 0.020, 'kg', 250000.00, NOW()),  -- Kopi arabica 20gr
(12, 52, 0.200, 'liter', 18000.00, NOW()),  -- Susu UHT 200ml
(12, 133, 0.010, 'kg', 65000.00, NOW());  -- Creamer 10gr

-- JUICE ORANGE (Menu Item ID: 13)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit, cost_at_creation, created_at) VALUES
(13, 122, 0.200, 'kg', 22000.00, NOW()),  -- Jeruk 200gr
(13, 131, 0.010, 'kg', 14000.00, NOW());  -- Gula pasir 10gr

SELECT 'Sample inventory and recipe data inserted successfully!' AS status;
SELECT CONCAT('Total inventory items: ', COUNT(*)) as info FROM inventory_items;
SELECT CONCAT('Total recipe ingredients: ', COUNT(*)) as info FROM recipe_ingredients;
