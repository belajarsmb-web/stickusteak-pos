-- Check and add customer columns to orders table
USE posreato;

-- Check if columns exist
SELECT 'Checking orders table columns...' AS '';
DESCRIBE orders;

-- Add customer_name if not exists
ALTER TABLE orders ADD COLUMN customer_name VARCHAR(255) DEFAULT '';

-- Add customer_phone if not exists  
ALTER TABLE orders ADD COLUMN customer_phone VARCHAR(50) DEFAULT '';

-- Verify
SELECT 'Columns added! Verifying...' AS '';
DESCRIBE orders;

SELECT 'Done! Customer columns ready.' AS 'Status';
