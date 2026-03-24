USE posreato;

-- Check orders table structure
SELECT '=== ORDERS TABLE ===' AS '';
DESCRIBE orders;

-- Check if status column exists
SELECT '=== CHECK STATUS COLUMN ===' AS '';
SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'posreato' 
AND TABLE_NAME = 'orders' 
AND COLUMN_NAME = 'status';

-- Check order_items structure
SELECT '=== ORDER_ITEMS TABLE ===' AS '';
DESCRIBE order_items;
