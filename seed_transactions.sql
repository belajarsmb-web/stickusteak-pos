-- Sample Transactions (Orders) to populate Reports
USE posreato;

SET @outlet_id = 1;
SET @admin_user_id = 1;

-- 1. Order for today
INSERT INTO orders (outlet_id, table_id, service_type, status, sub_total, tax_amount, service_charge, total_amount, created_by, created_at)
VALUES (@outlet_id, 1, 'dine_in', 'paid', 223000, 22300, 22300, 267600, @admin_user_id, NOW());
SET @order_id1 = LAST_INSERT_ID();

INSERT INTO order_items (order_id, menu_item_id, quantity, price, cost_price, status) 
VALUES 
(@order_id1, (SELECT id FROM menu_items WHERE code = 'MC-001' LIMIT 1), 1, 185000, 120000, 'served'),
(@order_id1, (SELECT id FROM menu_items WHERE code = 'DR-001' LIMIT 1), 1, 38000, 12000, 'served');

INSERT INTO payments (order_id, payment_method_id, amount, status, created_by, created_at)
VALUES (@order_id1, 4, 267600, 'completed', @admin_user_id, NOW());

-- 2. Order for yesterday
INSERT INTO orders (outlet_id, table_id, service_type, status, sub_total, tax_amount, service_charge, total_amount, created_by, created_at)
VALUES (@outlet_id, 2, 'dine_in', 'paid', 120000, 12000, 12000, 144000, @admin_user_id, DATE_SUB(NOW(), INTERVAL 1 DAY));
SET @order_id2 = LAST_INSERT_ID();

INSERT INTO order_items (order_id, menu_item_id, quantity, price, cost_price, status) 
VALUES 
(@order_id2, (SELECT id FROM menu_items WHERE code = 'MC-002' LIMIT 1), 1, 75000, 35000, 'served'),
(@order_id2, (SELECT id FROM menu_items WHERE code = 'DS-001' LIMIT 1), 1, 45000, 15000, 'served');

INSERT INTO payments (order_id, payment_method_id, amount, status, created_by, created_at)
VALUES (@order_id2, 1, 144000, 'completed', @admin_user_id, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- 3. Another order for today
INSERT INTO orders (outlet_id, table_id, service_type, status, sub_total, tax_amount, service_charge, total_amount, created_by, created_at)
VALUES (@outlet_id, 3, 'dine_in', 'paid', 45000, 4500, 4500, 54000, @admin_user_id, NOW());
SET @order_id3 = LAST_INSERT_ID();

INSERT INTO order_items (order_id, menu_item_id, quantity, price, cost_price, status) 
VALUES (@order_id3, (SELECT id FROM menu_items WHERE code = 'AP-001' LIMIT 1), 1, 45000, 15000, 'served');

INSERT INTO payments (order_id, payment_method_id, amount, status, created_by, created_at)
VALUES (@order_id3, 4, 54000, 'completed', @admin_user_id, NOW());
