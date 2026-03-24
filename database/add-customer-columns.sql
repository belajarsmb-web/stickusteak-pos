-- Add customer info columns to orders table
USE posreato;

-- Add customer_name column
ALTER TABLE orders ADD COLUMN customer_name VARCHAR(255) DEFAULT '' AFTER order_source;

-- Add customer_phone column  
ALTER TABLE orders ADD COLUMN customer_phone VARCHAR(50) DEFAULT '' AFTER customer_name;

-- Verify columns added
SELECT 'Customer columns added!' AS status;
DESCRIBE orders;
