-- ============================================
-- DELETE ALL TRANSACTIONS
-- Clean database for fresh testing
-- ============================================

USE posreato;

-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Delete all order items
DELETE FROM order_items;

-- Delete all orders
DELETE FROM orders;

-- Delete all tickets
DELETE FROM tickets;

-- Reset auto-increment
ALTER TABLE order_items AUTO_INCREMENT = 1;
ALTER TABLE orders AUTO_INCREMENT = 1;
ALTER TABLE tickets AUTO_INCREMENT = 1;

-- Reset table status to available
UPDATE tables SET status = 'available';

-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

SELECT 'All transactions deleted!' AS status;
SELECT COUNT(*) as total_orders FROM orders;
SELECT COUNT(*) as total_tickets FROM tickets;
SELECT COUNT(*) as total_order_items FROM order_items;
