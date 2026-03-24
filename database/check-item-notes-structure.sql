-- ============================================
-- FIX: Item Notes Table - Updated
-- Checks existing table structure
-- ============================================

USE posreato;

-- Check if table exists
SELECT 'Checking item_notes table...' AS status;

-- Show table structure
DESCRIBE item_notes;

-- Check what columns exist
SELECT COLUMN_NAME, DATA_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'posreato' 
  AND TABLE_NAME = 'item_notes';
