-- ============================================
-- Shift Management - Balance Tracking
-- Add columns for cash management
-- ============================================

USE posreato;

-- Add balance columns to shifts table
ALTER TABLE shifts 
ADD COLUMN opening_balance DECIMAL(10,2) DEFAULT 0 AFTER status,
ADD COLUMN closing_balance DECIMAL(10,2) DEFAULT 0 AFTER opening_balance,
ADD COLUMN expected_balance DECIMAL(10,2) DEFAULT 0 AFTER closing_balance,
ADD COLUMN variance DECIMAL(10,2) DEFAULT 0 AFTER expected_balance,
ADD COLUMN notes TEXT AFTER variance;

-- Update existing shifts (set opening balance to 0)
UPDATE shifts SET opening_balance = 0 WHERE opening_balance IS NULL;
UPDATE shifts SET closing_balance = 0 WHERE closing_balance IS NULL;
UPDATE shifts SET expected_balance = 0 WHERE expected_balance IS NULL;
UPDATE shifts SET variance = 0 WHERE variance IS NULL;

SELECT 'Shift table updated with balance columns!' AS status;
