-- ============================================
-- Add payment_method_id to Orders Table
-- ============================================

-- Add payment_method_id column to orders if it doesn't exist
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS payment_method_id INT AFTER change_amount,
ADD COLUMN IF NOT EXISTS ticket_id INT AFTER table_id;

-- Add foreign key constraint
ALTER TABLE orders
ADD CONSTRAINT fk_order_payment_method
FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE SET NULL;

-- Add index for faster queries
ALTER TABLE orders
ADD INDEX idx_order_payment_method (payment_method_id);

SELECT 'payment_method_id and ticket_id columns added to orders!' AS status;
