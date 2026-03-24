-- ============================================
-- RestoQwen POS - Sample Steak Restaurant Data
-- Menu Items and Recipes (Supplement)
-- ============================================

USE posreato;

-- ============================================
-- 1. ADD MISSING INVENTORY ITEMS (if not exists)
-- ============================================

INSERT IGNORE INTO inventory_items (name, sku, unit, current_stock, min_stock, max_stock, reorder_point, cost_price, is_active) VALUES
('Daging Sapi Sirloin Premium', 'STEAK-001', 'kg', 15.00, 5.00, 30.00, 8.00, 150000.00, 1),
('Daging Sapi Tenderloin', 'STEAK-002', 'kg', 8.00, 3.00, 20.00, 5.00, 200000.00, 1),
('Daging Sapi Ribeye', 'STEAK-003', 'kg', 10.00, 4.00, 25.00, 6.00, 180000.00, 1),
('Daging Sapi Striploin', 'STEAK-004', 'kg', 12.00, 4.00, 25.00, 7.00, 170000.00, 1),
('Ikan Salmon Fillet', 'STEAK-005', 'kg', 5.00, 2.00, 15.00, 3.00, 120000.00, 1),
('Daging Kambing', 'STEAK-006', 'kg', 6.00, 2.00, 15.00, 4.00, 140000.00, 1),
('Bacon Strip', 'STEAK-007', 'kg', 5.00, 2.00, 10.00, 3.00, 85000.00, 1),
('Asparagus', 'VEG-015', 'kg', 3.00, 1.00, 6.00, 2.00, 55000.00, 1),
('Anggur Merah Dry', 'WINE-002', 'ml', 3000.00, 1000.00, 5000.00, 1500.00, 85000.00, 1);

-- ============================================
-- 2. ADD STEAK RESTAURANT MENU ITEMS
-- ============================================

-- Get max sort_order first
SET @max_sort = (SELECT COALESCE(MAX(sort_order), 0) FROM menu_items);

-- Premium Steaks (category_id = 1)
INSERT INTO menu_items (name, description, price, cost_price, category_id, code, image_url, is_active, is_available, sort_order) VALUES
('Classic Sirloin Steak', '250g sapi sirloin premium dengan saus lada hitam, kentang tumbuk, dan sayuran musiman', 185000.00, 75000.00, 1, 'STEAK-001', NULL, 1, 1, @max_sort + 1),
('Tenderloin Mignon', '200g tenderloin sapi pilihan dengan saus mushroom, asparagus, dan baked potato', 245000.00, 105000.00, 1, 'STEAK-002', NULL, 1, 1, @max_sort + 2),
('Ribeye Deluxe', '300g ribeye marble dengan garlic butter, grilled vegetables, dan french fries', 225000.00, 95000.00, 1, 'STEAK-003', NULL, 1, 1, @max_sort + 3),
('Striploin Supreme', '280g striploin dengan rosemary jus, mashed potato, dan buncis', 215000.00, 90000.00, 1, 'STEAK-004', NULL, 1, 1, @max_sort + 4),
('T-Bone Special', '400g T-bone steak dengan saus BBQ, corn on the cob, dan salad', 265000.00, 115000.00, 1, 'STEAK-005', NULL, 1, 1, @max_sort + 5),
('Wagyu A5 Experience', '150g Wagyu A5 Jepang dengan truffle sauce, asparagus, dan potato gratin', 550000.00, 280000.00, 1, 'STEAK-006', NULL, 1, 1, @max_sort + 6),
('Lamb Chop', '6 pcs kambing chop dengan mint sauce, roasted vegetables, dan couscous', 195000.00, 85000.00, 1, 'STEAK-007', NULL, 1, 1, @max_sort + 7),
('Grilled Salmon', '200g salmon fillet dengan lemon butter sauce, nasi, dan sayuran', 145000.00, 65000.00, 1, 'STEAK-008', NULL, 1, 1, @max_sort + 8),
('Beef Bourguignon', 'Daging sapi slow-cooked dengan anggur merah, jamur, dan kentang', 165000.00, 70000.00, 1, 'STEAK-009', NULL, 1, 1, @max_sort + 9),
('Chicken Cordon Bleu', 'Dada ayam dengan ham dan keju, saus mustard, kentang, dan salad', 95000.00, 42000.00, 1, 'STEAK-010', NULL, 1, 1, @max_sort + 10);

-- ============================================
-- 3. RECIPE INGREDIENTS
-- ============================================

-- Get the newly inserted menu item IDs
SET @sirloin_id = (SELECT id FROM menu_items WHERE name = 'Classic Sirloin Steak' ORDER BY id DESC LIMIT 1);
SET @tenderloin_id = (SELECT id FROM menu_items WHERE name = 'Tenderloin Mignon' ORDER BY id DESC LIMIT 1);
SET @ribeye_id = (SELECT id FROM menu_items WHERE name = 'Ribeye Deluxe' ORDER BY id DESC LIMIT 1);
SET @striploin_id = (SELECT id FROM menu_items WHERE name = 'Striploin Supreme' ORDER BY id DESC LIMIT 1);
SET @tbone_id = (SELECT id FROM menu_items WHERE name = 'T-Bone Special' ORDER BY id DESC LIMIT 1);
SET @wagyu_id = (SELECT id FROM menu_items WHERE name = 'Wagyu A5 Experience' ORDER BY id DESC LIMIT 1);
SET @lamb_id = (SELECT id FROM menu_items WHERE name = 'Lamb Chop' ORDER BY id DESC LIMIT 1);
SET @salmon_id = (SELECT id FROM menu_items WHERE name = 'Grilled Salmon' ORDER BY id DESC LIMIT 1);
SET @bourguignon_id = (SELECT id FROM menu_items WHERE name = 'Beef Bourguignon' ORDER BY id DESC LIMIT 1);
SET @cordonbleu_id = (SELECT id FROM menu_items WHERE name = 'Chicken Cordon Bleu' ORDER BY id DESC LIMIT 1);

-- Get inventory item IDs
SET @sirloin_inv = (SELECT id FROM inventory_items WHERE sku = 'STEAK-001');
SET @tenderloin_inv = (SELECT id FROM inventory_items WHERE sku = 'STEAK-002');
SET @ribeye_inv = (SELECT id FROM inventory_items WHERE sku = 'STEAK-003');
SET @striploin_inv = (SELECT id FROM inventory_items WHERE sku = 'STEAK-004');
SET @salmon_inv = (SELECT id FROM inventory_items WHERE sku = 'STEAK-005');
SET @lamb_inv = (SELECT id FROM inventory_items WHERE sku = 'STEAK-006');
SET @bacon_inv = (SELECT id FROM inventory_items WHERE sku = 'STEAK-007');
SET @asparagus_inv = (SELECT id FROM inventory_items WHERE sku = 'VEG-015');
SET @wine_inv = (SELECT id FROM inventory_items WHERE sku = 'WINE-002');

-- Common ingredients
SET @potato_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Kentang%' LIMIT 1);
SET @carrot_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Wortel%' LIMIT 1);
SET @broccoli_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Brokoli%' LIMIT 1);
SET @beans_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Buncis%' LIMIT 1);
SET @butter_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Butter%' LIMIT 1);
SET @pepper_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Lada%' LIMIT 1);
SET @salt_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Garam%' LIMIT 1);
SET @mushroom_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Jamur%' LIMIT 1);
SET @garlic_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Bawang Putih%' LIMIT 1);
SET @rosemary_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Rosemary%' LIMIT 1);
SET @beef_stock_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Kaldu Sapi%' LIMIT 1);
SET @bbq_sauce_inv = (SELECT id FROM inventory_items WHERE name LIKE '%BBQ%' LIMIT 1);
SET @cream_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Cream%' LIMIT 1);
SET @parmesan_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Parmesan%' LIMIT 1);
SET @chicken_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Ayam Breast%' LIMIT 1);
SET @cheddar_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Cheddar%' LIMIT 1);
SET @flour_breadcrumb_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Tepung Panir%' LIMIT 1);
SET @egg_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Telur%' LIMIT 1);
SET @lettuce_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Selada%' LIMIT 1);
SET @onion_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Bawang Bombay%' LIMIT 1);
SET @spaghetti_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Spaghetti%' LIMIT 1);
SET @tomato_sauce_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Saus Tomat%' LIMIT 1);
SET @fettuccine_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Fettuccine%' LIMIT 1);
SET @mayo_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Mayones%' LIMIT 1);
SET @baguette_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Baguette%' LIMIT 1);
SET @mozzarella_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Mozzarella%' LIMIT 1);
SET @cocoa_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Coklat Bubuk%' LIMIT 1);
SET @flour_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Terigu%' LIMIT 1);
SET @sugar_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Gula Pasir%' LIMIT 1);
SET @coffee_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Kopi%' LIMIT 1);
SET @milk_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Susu%' LIMIT 1);
SET @oj_inv = (SELECT id FROM inventory_items WHERE name LIKE '%Jeruk%' LIMIT 1);

-- Classic Sirloin Steak Recipe
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(@sirloin_id, @sirloin_inv, 250, 'gram'),
(@sirloin_id, @potato_inv, 200, 'gram'),
(@sirloin_id, @carrot_inv, 50, 'gram'),
(@sirloin_id, @broccoli_inv, 50, 'gram'),
(@sirloin_id, @beans_inv, 30, 'gram'),
(@sirloin_id, @butter_inv, 30, 'gram'),
(@sirloin_id, @pepper_inv, 10, 'gram'),
(@sirloin_id, @salt_inv, 5, 'gram');

-- Tenderloin Mignon Recipe
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(@tenderloin_id, @tenderloin_inv, 200, 'gram'),
(@tenderloin_id, @mushroom_inv, 100, 'gram'),
(@tenderloin_id, @asparagus_inv, 50, 'gram'),
(@tenderloin_id, @potato_inv, 200, 'gram'),
(@tenderloin_id, @butter_inv, 30, 'gram'),
(@tenderloin_id, @beef_stock_inv, 100, 'ml'),
(@tenderloin_id, @salt_inv, 5, 'gram');

-- Ribeye Deluxe Recipe
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(@ribeye_id, @ribeye_inv, 300, 'gram'),
(@ribeye_id, @butter_inv, 40, 'gram'),
(@ribeye_id, @garlic_inv, 20, 'gram'),
(@ribeye_id, @broccoli_inv, 100, 'gram'),
(@ribeye_id, @potato_inv, 150, 'gram'),
(@ribeye_id, @salt_inv, 5, 'gram');

-- Striploin Supreme Recipe
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(@striploin_id, @striploin_inv, 280, 'gram'),
(@striploin_id, @potato_inv, 200, 'gram'),
(@striploin_id, @beans_inv, 50, 'gram'),
(@striploin_id, @rosemary_inv, 20, 'gram'),
(@striploin_id, @beef_stock_inv, 100, 'ml'),
(@striploin_id, @butter_inv, 30, 'gram');

-- T-Bone Special Recipe
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(@tbone_id, @sirloin_inv, 400, 'gram'),
(@tbone_id, @potato_inv, 200, 'gram'),
(@tbone_id, @lettuce_inv, 100, 'gram'),
(@tbone_id, @tomato_sauce_inv, 50, 'ml'),
(@tbone_id, @bbq_sauce_inv, 50, 'ml');

-- Wagyu A5 Experience Recipe
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(@wagyu_id, @tenderloin_inv, 150, 'gram'),
(@wagyu_id, @asparagus_inv, 50, 'gram'),
(@wagyu_id, @potato_inv, 150, 'gram'),
(@wagyu_id, @cream_inv, 30, 'ml'),
(@wagyu_id, @parmesan_inv, 20, 'gram');

-- Lamb Chop Recipe
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(@lamb_id, @lamb_inv, 300, 'gram'),
(@lamb_id, @carrot_inv, 100, 'gram'),
(@lamb_id, @broccoli_inv, 50, 'gram'),
(@lamb_id, @beans_inv, 50, 'gram'),
(@lamb_id, @rosemary_inv, 20, 'gram'),
(@lamb_id, @salt_inv, 5, 'gram');

-- Grilled Salmon Recipe
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(@salmon_id, @salmon_inv, 200, 'gram'),
(@salmon_id, @butter_inv, 30, 'gram'),
(@salmon_id, @beef_stock_inv, 50, 'ml'),
(@salmon_id, @potato_inv, 150, 'gram'),
(@salmon_id, @broccoli_inv, 50, 'gram'),
(@salmon_id, @carrot_inv, 50, 'gram');

-- Beef Bourguignon Recipe
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(@bourguignon_id, @sirloin_inv, 250, 'gram'),
(@bourguignon_id, @mushroom_inv, 100, 'gram'),
(@bourguignon_id, @wine_inv, 200, 'ml'),
(@bourguignon_id, @beef_stock_inv, 200, 'ml'),
(@bourguignon_id, @potato_inv, 200, 'gram'),
(@bourguignon_id, @onion_inv, 50, 'gram'),
(@bourguignon_id, @garlic_inv, 10, 'gram');

-- Chicken Cordon Bleu Recipe
INSERT INTO recipe_ingredients (menu_item_id, inventory_item_id, quantity, unit) VALUES
(@cordonbleu_id, @chicken_inv, 200, 'gram'),
(@cordonbleu_id, @bacon_inv, 50, 'gram'),
(@cordonbleu_id, @cheddar_inv, 50, 'gram'),
(@cordonbleu_id, @flour_breadcrumb_inv, 50, 'gram'),
(@cordonbleu_id, @egg_inv, 2, 'pcs'),
(@cordonbleu_id, @potato_inv, 150, 'gram'),
(@cordonbleu_id, @lettuce_inv, 50, 'gram');

-- Summary
SELECT 
    'Sample steak restaurant menu data inserted successfully!' AS status,
    (SELECT COUNT(*) FROM menu_items WHERE category_id = 1) AS steak_items,
    (SELECT COUNT(*) FROM recipe_ingredients) AS total_recipes;
