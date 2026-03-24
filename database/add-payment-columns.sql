-- ============================================
-- Add Payment Columns to Orders Table
-- ============================================

USE posreato;

-- Add paid_amount and change_amount columns if they don't exist
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS paid_amount DECIMAL(10,2) DEFAULT 0 AFTER total_amount,
ADD COLUMN IF NOT EXISTS change_amount DECIMAL(10,2) DEFAULT 0 AFTER paid_amount;

-- Add index for faster queries
ALTER TABLE orders 
ADD INDEX IF NOT EXISTS idx_order_status (status);

SELECT 'Payment columns added successfully!' AS status;
