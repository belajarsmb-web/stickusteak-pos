-- ============================================
-- RestoQwen POS - Sample Steak Restaurant Data
-- Menu Items, Inventory, and Recipes
-- ============================================

USE posreato;

-- ============================================
-- 1. INVENTORY ITEMS (Bahan Baku)
-- ============================================

-- Daging & Protein
INSERT INTO inventory_items (name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Daging Sapi Sirloin Premium', 'MEAT-001', 'kg', 15.00, 5.00, 30.00, 8.00, 150000.00, 1),
('Daging Sapi Tenderloin', 'MEAT-002', 'kg', 8.00, 3.00, 20.00, 5.00, 200000.00, 1),
('Daging Sapi Ribeye', 'MEAT-003', 'kg', 10.00, 4.00, 25.00, 6.00, 180000.00, 1),
('Daging Sapi Striploin', 'MEAT-004', 'kg', 12.00, 4.00, 25.00, 7.00, 170000.00, 1),
('Ikan Salmon Fillet', 'FISH-001', 'kg', 5.00, 2.00, 15.00, 3.00, 120000.00, 1),
('Ayam Breast', 'POULTRY-001', 'kg', 10.00, 3.00, 20.00, 5.00, 45000.00, 1),
('Daging Kambi', 'MEAT-005', 'kg', 6.00, 2.00, 15.00, 4.00, 140000.00, 1),
('Sosis Sapi', 'MEAT-006', 'kg', 8.00, 3.00, 20.00, 5.00, 65000.00, 1),
('Bacon', 'MEAT-007', 'kg', 5.00, 2.00, 10.00, 3.00, 85000.00, 1),
('Hati Ayam', 'POULTRY-002', 'kg', 4.00, 1.00, 8.00, 2.00, 35000.00, 1);

-- Sayuran
INSERT INTO inventory_items (name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Kentang', 'VEG-001', 'kg', 25.00, 10.00, 50.00, 15.00, 15000.00, 1),
('Wortel', 'VEG-002', 'kg', 15.00, 5.00, 30.00, 8.00, 12000.00, 1),
('Brokoli', 'VEG-003', 'kg', 8.00, 3.00, 15.00, 5.00, 18000.00, 1),
('Buncis', 'VEG-004', 'kg', 6.00, 2.00, 12.00, 3.00, 16000.00, 1),
('Jamur Champignon', 'VEG-005', 'kg', 5.00, 2.00, 10.00, 3.00, 35000.00, 1),
('Bawang Bombay', 'VEG-006', 'kg', 10.00, 3.00, 20.00, 5.00, 18000.00, 1),
('Bawang Putih', 'VEG-007', 'kg', 8.00, 2.00, 15.00, 3.00, 25000.00, 1),
('Tomat', 'VEG-008', 'kg', 12.00, 5.00, 25.00, 8.00, 14000.00, 1),
('Selada', 'VEG-009', 'kg', 5.00, 2.00, 10.00, 3.00, 12000.00, 1),
('Paprika Merah', 'VEG-010', 'kg', 4.00, 1.00, 8.00, 2.00, 35000.00, 1),
('Paprika Kuning', 'VEG-011', 'kg', 4.00, 1.00, 8.00, 2.00, 35000.00, 1),
('Asparagus', 'VEG-012', 'kg', 3.00, 1.00, 6.00, 2.00, 55000.00, 1),
('Zucchini', 'VEG-013', 'kg', 6.00, 2.00, 12.00, 3.00, 22000.00, 1),
('Jagung Manis', 'VEG-014', 'kg', 8.00, 3.00, 15.00, 5.00, 18000.00, 1);

-- Bumbu & Rempah
INSERT INTO inventory_items (name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Garam', 'SPICE-001', 'kg', 5.00, 1.00, 10.00, 2.00, 8000.00, 1),
('Lada Hitam', 'SPICE-002', 'kg', 2.00, 0.50, 5.00, 1.00, 85000.00, 1),
('Rosemary Segar', 'HERB-001', 'gram', 500.00, 100.00, 1000.00, 200.00, 45000.00, 1),
('Thyme Segar', 'HERB-002', 'gram', 400.00, 100.00, 800.00, 200.00, 40000.00, 1),
('Oregano', 'HERB-003', 'gram', 300.00, 50.00, 600.00, 100.00, 35000.00, 1),
('Parsley', 'HERB-004', 'gram', 500.00, 100.00, 1000.00, 200.00, 30000.00, 1),
('Kayu Manis', 'SPICE-003', 'gram', 200.00, 50.00, 400.00, 100.00, 25000.00, 1),
('Pala', 'SPICE-004', 'gram', 100.00, 20.00, 200.00, 50.00, 30000.00, 1),
('Cengkeh', 'SPICE-005', 'gram', 100.00, 20.00, 200.00, 50.00, 35000.00, 1),
('Jintan', 'SPICE-006', 'gram', 150.00, 30.00, 300.00, 50.00, 28000.00, 1);

-- Produk Susu & Lemak
INSERT INTO inventory_items (name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Butter', 'DAIRY-001', 'kg', 8.00, 2.00, 15.00, 3.00, 95000.00, 1),
('Keju Cheddar', 'DAIRY-002', 'kg', 5.00, 1.00, 10.00, 2.00, 110000.00, 1),
('Keju Mozzarella', 'DAIRY-003', 'kg', 4.00, 1.00, 8.00, 2.00, 100000.00, 1),
('Keju Parmesan', 'DAIRY-004', 'kg', 3.00, 0.50, 6.00, 1.00, 150000.00, 1),
('Cream', 'DAIRY-005', 'liter', 10.00, 3.00, 20.00, 5.00, 45000.00, 1),
('Susu Full Cream', 'DAIRY-006', 'liter', 15.00, 5.00, 30.00, 8.00, 22000.00, 1),
('Minyak Zaitun', 'OIL-001', 'liter', 5.00, 1.00, 10.00, 2.00, 120000.00, 1),
('Minyak Goreng', 'OIL-002', 'liter', 10.00, 3.00, 20.00, 5.00, 25000.00, 1);

-- Bahan Saus
INSERT INTO inventory_items (name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Saus Tomat', 'SAUCE-001', 'liter', 8.00, 2.00, 15.00, 3.00, 35000.00, 1),
('Saus BBQ', 'SAUCE-002', 'liter', 6.00, 2.00, 12.00, 3.00, 45000.00, 1),
('Saus Teriyaki', 'SAUCE-003', 'liter', 5.00, 1.00, 10.00, 2.00, 40000.00, 1),
('Kecap Inggris (Worcestershire)', 'SAUCE-004', 'ml', 2000.00, 500.00, 4000.00, 1000.00, 55000.00, 1),
('Mustard', 'SAUCE-005', 'kg', 3.00, 1.00, 6.00, 2.00, 45000.00, 1),
('Mayones', 'SAUCE-006', 'kg', 5.00, 1.00, 10.00, 2.00, 35000.00, 1),
('Sambal', 'SAUCE-007', 'kg', 4.00, 1.00, 8.00, 2.00, 30000.00, 1),
('Anggur Merah (untuk wine sauce)', 'WINE-001', 'ml', 3000.00, 1000.00, 5000.00, 1500.00, 85000.00, 1),
('Kaldu Sapi', 'STOCK-001', 'liter', 10.00, 3.00, 20.00, 5.00, 25000.00, 1),
('Kaldu Ayam', 'STOCK-002', 'liter', 8.00, 2.00, 15.00, 3.00, 20000.00, 1);

-- Nasi & Pasta
INSERT INTO inventory_items (name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Beras Basmati', 'GRAIN-001', 'kg', 20.00, 5.00, 40.00, 10.00, 25000.00, 1),
('Beras Putih', 'GRAIN-002', 'kg', 25.00, 10.00, 50.00, 15.00, 18000.00, 1),
('Spaghetti', 'PASTA-001', 'kg', 10.00, 3.00, 20.00, 5.00, 22000.00, 1),
('Fettuccine', 'PASTA-002', 'kg', 8.00, 2.00, 15.00, 3.00, 25000.00, 1),
('Penne', 'PASTA-003', 'kg', 6.00, 2.00, 12.00, 3.00, 24000.00, 1);

-- Roti & Pendamping
INSERT INTO inventory_items (name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Roti Baguette', 'BREAD-001', 'pcs', 20.00, 5.00, 40.00, 10.00, 15000.00, 1),
('Roti Bawang', 'BREAD-002', 'pcs', 30.00, 10.00, 60.00, 20.00, 8000.00, 1),
('Tepung Terigu', 'FLOUR-001', 'kg', 15.00, 5.00, 30.00, 10.00, 12000.00, 1),
('Tepung Panir', 'FLOUR-002', 'kg', 8.00, 2.00, 15.00, 3.00, 18000.00, 1),
('Telur Ayam', 'EGG-001', 'pcs', 100.00, 30.00, 200.00, 50.00, 2500.00, 1);

-- Minuman
INSERT INTO inventory_items (name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Kopi Biji', 'BEV-001', 'kg', 5.00, 1.00, 10.00, 2.00, 150000.00, 1),
('Teh Hitam', 'BEV-002', 'kg', 3.00, 0.50, 6.00, 1.00, 80000.00, 1),
('Coklat Bubuk', 'BEV-003', 'kg', 4.00, 1.00, 8.00, 2.00, 95000.00, 1),
('Gula Pasir', 'BEV-004', 'kg', 20.00, 5.00, 40.00, 10.00, 14000.00, 1),
('Sirup Vanilla', 'SYRUP-001', 'ml', 2000.00, 500.00, 4000.00, 1000.00, 65000.00, 1),
('Sirup Caramel', 'SYRUP-002', 'ml', 2000.00, 500.00, 4000.00, 1000.00, 65000.00, 1),
('Jus Jeruk', 'JUICE-001', 'liter', 10.00, 3.00, 20.00, 5.00, 35000.00, 1),
('Soda Water', 'BEV-005', 'liter', 15.00, 5.00, 30.00, 8.00, 12000.00, 1);

-- ============================================
-- 2. MENU ITEMS
-- ============================================
-- Category mapping: 1=Premium Steaks, 2=Burgers & Sandwiches, 3=Side Dishes, 4=Desserts, 5=Beverages

-- STEAK (category_id = 1 - Premium Steaks)
INSERT INTO menu_items (name, description, price, cost_price, category_id, image_url, is_active, is_available, sort_order) VALUES
('Classic Sirloin Steak', '250g sapi sirloin premium dengan saus lada hitam, kentang tumbuk, dan sayuran musiman', 185000.00, 75000.00, 1, NULL, 1, 1, 1),
('Tenderloin Mignon', '200g tenderloin sapi pilihan dengan saus mushroom, asparagus, dan baked potato', 245000.00, 105000.00, 1, NULL, 1, 1, 2),
('Ribeye Deluxe', '300g ribeye marble dengan garlic butter, grilled vegetables, dan french fries', 225000.00, 95000.00, 1, NULL, 1, 1, 3),
('Striploin Supreme', '280g striploin dengan rosemary jus, mashed potato, dan buncis', 215000.00, 90000.00, 1, NULL, 1, 1, 4),
('T-Bone Special', '400g T-bone steak dengan saus BBQ, corn on the cob, dan salad', 265000.00, 115000.00, 1, NULL, 1, 1, 5),
('Wagyu A5 Experience', '150g Wagyu A5 Jepang dengan truffle sauce, asparagus, dan potato gratin', 550000.00, 280000.00, 1, NULL, 1, 1, 6),
('Lamb Chop', '6 pcs kambing chop dengan mint sauce, roasted vegetables, dan couscous', 195000.00, 85000.00, 1, NULL, 1, 1, 7),
('Grilled Salmon', '200g salmon fillet dengan lemon butter sauce, nasi, dan sayuran', 145000.00, 65000.00, 1, NULL, 1, 1, 8),
('Beef Bourguignon', 'Daging sapi slow-cooked dengan anggur merah, jamur, dan kentang', 165000.00, 70000.00, 1, NULL, 1, 1, 9),
('Chicken Cordon Bleu', 'Dada ayam dengan ham dan keju, saus mustard, kentang, dan salad', 95000.00, 42000.00, 1, NULL, 1, 1, 10);

-- PASTA & BURGERS (category_id = 2 - Burgers & Sandwiches)
INSERT INTO menu_items (name, description, price, cost_price, category_id, image_url, is_active, is_available, sort_order) VALUES
('Spaghetti Bolognese', 'Spaghetti dengan saus daging sapi cincang dan parmesan', 75000.00, 32000.00, 2, NULL, 1, 1, 11),
('Fettuccine Carbonara', 'Fettuccine dengan cream, bacon, keju, dan kuning telur', 85000.00, 38000.00, 2, NULL, 1, 1, 12),
('Penne Arrabbiata', 'Penne dengan saus tomat pedas, bawang putih, dan parsley', 70000.00, 28000.00, 2, NULL, 1, 1, 13),
('Spaghetti Aglio Olio', 'Spaghetti dengan minyak zaitun, bawang putih, dan cabai', 65000.00, 25000.00, 2, NULL, 1, 1, 14);

-- APPETIZER & SIDES (category_id = 3 - Side Dishes)
INSERT INTO menu_items (name, description, price, cost_price, category_id, image_url, is_active, is_available, sort_order) VALUES
('Caesar Salad', 'Selada romaine dengan Caesar dressing, crouton, dan parmesan', 55000.00, 22000.00, 3, NULL, 1, 1, 15),
('French Onion Soup', 'Sup bawang bombay dengan keju gratin dan roti baguette', 48000.00, 18000.00, 3, NULL, 1, 1, 16),
('Mushroom Soup', 'Sup krim jamur champignon dengan crouton', 45000.00, 17000.00, 3, NULL, 1, 1, 17),
('Beef Carpaccio', 'Irisan tipis daging sapi mentah dengan arugula dan parmesan', 85000.00, 40000.00, 3, NULL, 1, 1, 18),
('Chicken Liver Pate', 'Pate hati ayam dengan roti panggang dan acar', 65000.00, 28000.00, 3, NULL, 1, 1, 19),
('Calamari Fritti', 'Cumi goreng tepung dengan tartar sauce', 70000.00, 32000.00, 3, NULL, 1, 1, 20),
('Bruschetta', 'Roti panggang dengan tomat, basil, dan minyak zaitun', 42000.00, 16000.00, 3, NULL, 1, 1, 21),
('Garlic Bread', 'Roti baguette dengan garlic butter dan parsley', 35000.00, 12000.00, 3, NULL, 1, 1, 22),
('Mashed Potato', 'Kentang tumbuk dengan butter dan susu', 28000.00, 10000.00, 3, NULL, 1, 1, 23),
('Grilled Vegetables', 'Sayuran panggang musiman', 35000.00, 15000.00, 3, NULL, 1, 1, 24),
('French Fries', 'Kentang goreng renyah', 25000.00, 8000.00, 3, NULL, 1, 1, 25),
('Baked Potato', 'Kentang panggang dengan butter', 30000.00, 12000.00, 3, NULL, 1, 1, 26);

-- DESSERT (category_id = 4 - Desserts)
INSERT INTO menu_items (name, description, price, cost_price, category_id, image_url, is_active, is_available, sort_order) VALUES
('Tiramisu', 'Dessert klasik Italia dengan kopi dan mascarpone', 55000.00, 22000.00, 4, NULL, 1, 1, 27),
('Chocolate Lava Cake', 'Kue coklat dengan lelehan coklat hangat dan es krim vanilla', 65000.00, 28000.00, 4, NULL, 1, 1, 28),
('Creme Brulee', 'Krim vanilla dengan lapisan karamel renyah', 58000.00, 24000.00, 4, NULL, 1, 1, 29),
('Panna Cotta', 'Puding Italia dengan saus berry', 52000.00, 20000.00, 4, NULL, 1, 1, 30),
('Ice Cream Trio', '3 scoop es krim (vanilla, coklat, strawberry)', 45000.00, 18000.00, 4, NULL, 1, 1, 31),
('Apple Pie', 'Pai apel hangat dengan es krim vanilla', 55000.00, 22000.00, 4, NULL, 1, 1, 32),
('Cheesecake', 'Kue keju New York dengan saus strawberry', 60000.00, 25000.00, 4, NULL, 1, 1, 33);

-- BEVERAGE (category_id = 5 - Beverages)
INSERT INTO menu_items (name, description, price, cost_price, category_id, image_url, is_active, is_available, sort_order) VALUES
('Americano Coffee', 'Kopi hitam Amerika', 35000.00, 12000.00, 5, NULL, 1, 1, 34),
('Cappuccino', 'Kopi dengan susu dan foam', 42000.00, 15000.00, 5, NULL, 1, 1, 35),
('Cafe Latte', 'Kopi latte dengan susu steam', 45000.00, 16000.00, 5, NULL, 1, 1, 36),
('Hot Chocolate', 'Coklat panas dengan whipped cream', 40000.00, 15000.00, 5, NULL, 1, 1, 37),
('Orange Juice', 'Jus jeruk segar', 35000.00, 14000.00, 5, NULL, 1, 1, 38),
('Mineral Water', 'Air mineral', 15000.00, 5000.00, 5, NULL, 1, 1, 39),
('Sparkling Water', 'Air soda', 20000.00, 8000.00, 5, NULL, 1, 1, 40),
('Iced Tea', 'Teh dingin', 25000.00, 8000.00, 5, NULL, 1, 1, 41),
('Lemon Squash', 'Minuman lemon segar', 30000.00, 10000.00, 5, NULL, 1, 1, 42),
('Strawberry Smoothie', 'Smoothie stroberi dengan susu', 45000.00, 18000.00, 5, NULL, 1, 1, 43);

-- ============================================
-- 3. RECIPE INGREDIENTS (Resep per Menu)
-- ============================================

-- Classic Sirloin Steak (menu_item_id = 1)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(1, 1, 250, 'gram'),   -- Sirloin 250g
(1, 20, 200, 'gram'),  -- Kentang 200g
(1, 21, 50, 'gram'),   -- Wortel 50g
(1, 22, 50, 'gram'),   -- Brokoli 50g
(1, 23, 30, 'gram'),   -- Buncis 30g
(1, 29, 30, 'gram'),   -- Butter 30g
(1, 26, 10, 'gram'),   -- Lada hitam 10g
(1, 25, 5, 'gram');    -- Garam 5g

-- Tenderloin Mignon (menu_item_id = 2)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(2, 2, 200, 'gram'),   -- Tenderloin 200g
(2, 24, 100, 'gram'),  -- Jamur 100g
(2, 31, 50, 'gram'),   -- Asparagus 50g
(2, 20, 200, 'gram'),  -- Kentang 200g
(2, 29, 30, 'gram'),   -- Butter 30g
(2, 39, 100, 'ml'),    -- Kaldu sapi 100ml
(2, 25, 5, 'gram');    -- Garam 5g

-- Ribeye Deluxe (menu_item_id = 3)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(3, 3, 300, 'gram'),   -- Ribeye 300g
(3, 29, 40, 'gram'),   -- Butter 40g
(3, 27, 20, 'gram'),   -- Bawang putih 20g
(3, 29, 100, 'gram'),  -- Paprika 100g
(3, 20, 150, 'gram'),  -- Kentang 150g
(3, 25, 5, 'gram');    -- Garam 5g

-- Striploin Supreme (menu_item_id = 4)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(4, 4, 280, 'gram'),   -- Striploin 280g
(4, 20, 200, 'gram'),  -- Kentang 200g
(4, 23, 50, 'gram'),   -- Buncis 50g
(4, 33, 20, 'gram'),   -- Rosemary 20g
(4, 39, 100, 'ml'),    -- Kaldu sapi 100ml
(4, 29, 30, 'gram');   -- Butter 30g

-- T-Bone Special (menu_item_id = 5)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(5, 1, 400, 'gram'),   -- Sirloin dengan tulang 400g
(5, 33, 1, 'pcs'),     -- Jagung 1 buah
(5, 28, 100, 'gram'),  -- Selada 100g
(5, 27, 50, 'gram'),   -- Tomat 50g
(5, 36, 50, 'ml');     -- Saus BBQ 50ml

-- Wagyu A5 Experience (menu_item_id = 6)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(6, 1, 150, 'gram'),   -- Wagyu 150g
(6, 31, 50, 'gram'),   -- Asparagus 50g
(6, 20, 150, 'gram'),  -- Kentang 150g
(6, 35, 30, 'ml'),     -- Cream 30ml
(6, 34, 20, 'gram');   -- Parmesan 20g

-- Lamb Chop (menu_item_id = 7)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(7, 7, 300, 'gram'),   -- Daging kambing 300g
(7, 29, 100, 'gram'),  -- Paprika 100g
(7, 22, 50, 'gram'),   -- Brokoli 50g
(7, 23, 50, 'gram'),   -- Buncis 50g
(7, 33, 20, 'gram'),   -- Rosemary 20g
(7, 25, 5, 'gram');    -- Garam 5g

-- Grilled Salmon (menu_item_id = 8)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(8, 5, 200, 'gram'),   -- Salmon 200g
(8, 29, 30, 'gram'),   -- Butter 30g
(8, 39, 100, 'ml'),    -- Kaldu ayam 100ml
(8, 41, 150, 'gram'),  -- Beras 150g
(8, 22, 50, 'gram'),   -- Brokoli 50g
(8, 21, 50, 'gram');   -- Wortel 50g

-- Beef Bourguignon (menu_item_id = 9)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(10, 1, 250, 'gram'),  -- Daging sapi 250g
(10, 24, 100, 'gram'), -- Jamur 100g
(10, 48, 200, 'ml'),   -- Anggur merah 200ml
(10, 39, 200, 'ml'),   -- Kaldu sapi 200ml
(10, 20, 200, 'gram'), -- Kentang 200g
(10, 26, 50, 'gram'),  -- Bawang bombay 50g
(10, 27, 10, 'gram');  -- Bawang putih 10g

-- Chicken Cordon Bleu (menu_item_id = 10)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(8, 6, 200, 'gram'),   -- Ayam breast 200g
(8, 8, 50, 'gram'),    -- Sosis/ham 50g
(8, 32, 50, 'gram'),   -- Cheddar 50g
(8, 49, 50, 'gram'),   -- Tepung panir 50g
(8, 50, 2, 'pcs'),     -- Telur 2 butir
(8, 20, 150, 'gram'),  -- Kentang 150g
(8, 28, 50, 'gram');   -- Selada 50g

-- Spaghetti Bolognese (menu_item_id = 11)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(11, 45, 150, 'gram'), -- Spaghetti 150g
(11, 1, 100, 'gram'),  -- Daging cincang 100g
(11, 37, 100, 'ml'),   -- Saus tomat 100ml
(11, 26, 30, 'gram'),  -- Bawang bombay 30g
(11, 27, 10, 'gram'),  -- Bawang putih 10g
(11, 34, 20, 'gram');  -- Parmesan 20g

-- Fettuccine Carbonara (menu_item_id = 12)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(12, 46, 150, 'gram'), -- Fettuccine 150g
(12, 9, 80, 'gram'),   -- Bacon 80g
(12, 35, 50, 'ml'),    -- Cream 50ml
(12, 50, 2, 'pcs'),    -- Telur 2 butir
(12, 34, 30, 'gram');  -- Parmesan 30g

-- Caesar Salad (menu_item_id = 15)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(15, 28, 150, 'gram'), -- Selada romaine 150g
(15, 40, 50, 'ml'),    -- Mayones 50ml
(15, 34, 30, 'gram'),  -- Parmesan 30g
(15, 49, 50, 'gram');  -- Crouton 50g

-- French Onion Soup (menu_item_id = 16)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(16, 26, 200, 'gram'), -- Bawang bombay 200g
(16, 39, 300, 'ml'),   -- Kaldu sapi 300ml
(16, 33, 50, 'gram'),  -- Mozzarella 50g
(16, 47, 2, 'pcs');    -- Baguette 2 pcs

-- Mushroom Soup (menu_item_id = 17)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(17, 24, 200, 'gram'), -- Jamur 200g
(17, 35, 100, 'ml'),   -- Cream 100ml
(17, 29, 30, 'gram'),  -- Butter 30g
(17, 26, 30, 'gram'),  -- Bawang bombay 30g
(17, 40, 200, 'ml');   -- Kaldu ayam 200ml

-- Chocolate Lava Cake (menu_item_id = 28)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(28, 52, 100, 'gram'), -- Coklat bubuk 100g
(28, 48, 80, 'gram'),  -- Tepung terigu 80g
(28, 50, 3, 'pcs'),    -- Telur 3 butir
(28, 53, 80, 'gram'),  -- Gula 80g
(28, 29, 50, 'gram');  -- Butter 50g

-- Tiramisu (menu_item_id = 27)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(27, 51, 50, 'gram'),  -- Kopi 50g
(27, 35, 200, 'ml'),   -- Cream 200ml
(27, 50, 4, 'pcs'),    -- Telur 4 butir
(27, 53, 100, 'gram'), -- Gula 100g
(27, 47, 10, 'pcs');   -- Ladyfinger biscuit 10 pcs

-- Cappuccino (menu_item_id = 35)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(35, 51, 20, 'gram'),  -- Kopi 20g
(35, 36, 150, 'ml');   -- Susu 150ml

-- Cafe Latte (menu_item_id = 36)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(36, 51, 20, 'gram'),  -- Kopi 20g
(36, 36, 200, 'ml');   -- Susu 200ml

-- Hot Chocolate (menu_item_id = 37)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(37, 52, 30, 'gram'),  -- Coklat bubuk 30g
(37, 36, 200, 'ml'),   -- Susu 200ml
(37, 53, 20, 'gram');  -- Gula 20g

-- Orange Juice (menu_item_id = 38)
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(38, 56, 250, 'ml');   -- Jus jeruk 250ml

SELECT 'Sample data inserted successfully!' AS status;
