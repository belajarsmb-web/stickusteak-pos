-- ============================================
-- Fix Missing Payment Columns
-- Compatible with MySQL 5.7+
-- ============================================

USE posreato;

-- ============================================
-- 1. Fix payments table
-- ============================================

-- Add change_amount column
SET @dbname = DATABASE();
SET @tablename = 'payments';
SET @columnname = 'change_amount';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  'ALTER TABLE payments ADD COLUMN change_amount DECIMAL(10,2) DEFAULT 0 AFTER amount'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add payment_date column
SET @dbname = DATABASE();
SET @tablename = 'payments';
SET @columnname = 'payment_date';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  'ALTER TABLE payments ADD COLUMN payment_date DATETIME AFTER created_at'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ============================================
-- 2. Fix orders table
-- ============================================

-- Add paid_amount column
SET @dbname = DATABASE();
SET @tablename = 'orders';
SET @columnname = 'paid_amount';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  'ALTER TABLE orders ADD COLUMN paid_amount DECIMAL(10,2) DEFAULT 0 AFTER total_amount'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add change_amount column
SET @dbname = DATABASE();
SET @tablename = 'orders';
SET @columnname = 'change_amount';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  'ALTER TABLE orders ADD COLUMN change_amount DECIMAL(10,2) DEFAULT 0 AFTER paid_amount'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add service_charge column
SET @dbname = DATABASE();
SET @tablename = 'orders';
SET @columnname = 'service_charge';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  'ALTER TABLE orders ADD COLUMN service_charge DECIMAL(10,2) DEFAULT 0 AFTER sub_total'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add tax_amount column
SET @dbname = DATABASE();
SET @tablename = 'orders';
SET @columnname = 'tax_amount';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  'ALTER TABLE orders ADD COLUMN tax_amount DECIMAL(10,2) DEFAULT 0 AFTER service_charge'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ============================================
-- 3. Verify
-- ============================================

SELECT 'Payments table:' AS info;
DESCRIBE payments;

SELECT 'Orders table:' AS info;
DESCRIBE orders;

SELECT 'All columns added successfully!' AS status;
