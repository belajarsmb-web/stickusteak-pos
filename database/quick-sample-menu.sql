-- ============================================
-- Quick Sample Menu Items
-- For testing POS order flow
-- ============================================

USE posreato;

-- Clear existing
DELETE FROM menu_items;

-- Insert sample menu items
INSERT INTO menu_items (outlet_id, category_id, code, name, description, price, is_active, is_available) VALUES
(1, 1, 'STEAK-001', 'Steak Sirloin', 'Premium beef sirloin steak', 150000, 1, 1),
(1, 1, 'STEAK-002', 'Steak Tenderloin', 'Tender beef tenderloin steak', 180000, 1, 1),
(1, 1, 'STEAK-003', 'Steak Ribeye', 'Juicy beef ribeye steak', 170000, 1, 1),
(1, 2, 'RICE-001', 'Nasi Goreng', 'Indonesian fried rice', 45000, 1, 1),
(1, 2, 'RICE-002', 'Nasi Putih', 'Steamed white rice', 10000, 1, 1),
(1, 3, 'CHK-001', 'Ayam Bakar', 'Grilled chicken with honey sauce', 55000, 1, 1),
(1, 3, 'CHK-002', 'Ayam Goreng', 'Fried chicken', 40000, 1, 1),
(1, 4, 'FISH-001', 'Grilled Salmon', 'Grilled salmon fillet', 120000, 1, 1),
(1, 4, 'FISH-002', 'Fish & Chips', 'Battered fish with fries', 85000, 1, 1),
(1, 5, 'VEG-001', 'Capcay', 'Stir-fried vegetables', 35000, 1, 1),
(1, 5, 'VEG-002', 'Kangkung Belacan', 'Stir-fried water spinach', 30000, 1, 1),
(1, 6, 'SOUP-001', 'Tom Yum Soup', 'Spicy Thai soup', 40000, 1, 1),
(1, 6, 'SOUP-002', 'Cream Soup', 'Cream of mushroom soup', 35000, 1, 1),
(1, 7, 'ICE-001', 'Ice Tea', 'Cold iced tea', 10000, 1, 1),
(1, 7, 'ICE-002', 'Ice Lemon Tea', 'Cold lemon iced tea', 12000, 1, 1),
(1, 7, 'ICE-003', 'Orange Juice', 'Fresh orange juice', 15000, 1, 1),
(1, 7, 'ICE-004', 'Coffee', 'Hot coffee', 12000, 1, 1),
(1, 8, 'DST-001', 'Ice Cream', 'Vanilla ice cream', 20000, 1, 1),
(1, 8, 'DST-002', 'Fruit Salad', 'Mixed fruit salad', 25000, 1, 1),
(1, 8, 'DST-003', 'Pancake', 'Sweet pancake with syrup', 30000, 1, 1);

-- Update categories if they don't exist
INSERT INTO categories (outlet_id, name, sort_order, is_active) VALUES
(1, 'Steak', 1, 1),
(1, 'Rice', 2, 1),
(1, 'Chicken', 3, 1),
(1, 'Fish & Seafood', 4, 1),
(1, 'Vegetables', 5, 1),
(1, 'Soup', 6, 1),
(1, 'Beverages', 7, 1),
(1, 'Dessert', 8, 1)
ON DUPLICATE KEY UPDATE name=name;

SELECT 'Sample menu items added successfully!' AS status;
SELECT CONCAT('Total menu items: ', COUNT(*)) as info FROM menu_items;
