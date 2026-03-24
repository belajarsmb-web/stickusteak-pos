-- ============================================
-- RestoQwen POS - Mobile Order System
-- Database Migrations (Safe Version)
-- ============================================

USE posreato;

-- 1. Add mobile order columns to orders table (if not exists)
SET @dbname = DATABASE();
SET @tablename = 'orders';
SET @columnname = 'order_source';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ENUM(\'pos\', \'mobile\', \'kiosk\') DEFAULT \'pos\' COMMENT \'Order source channel\' AFTER status')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @columnname = 'mobile_token';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(64) UNIQUE COMMENT \'Mobile order tracking token\' AFTER order_source')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @columnname = 'customer_name';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(100) COMMENT \'Customer name for mobile orders\' AFTER mobile_token')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Create QR codes table for table QR codes
CREATE TABLE IF NOT EXISTS qr_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_id INT NOT NULL,
    qr_token VARCHAR(64) UNIQUE NOT NULL COMMENT 'Unique QR token',
    qr_url VARCHAR(500) COMMENT 'Full QR URL',
    is_active TINYINT DEFAULT 1 COMMENT '1=active, 0=inactive',
    scan_count INT DEFAULT 0 COMMENT 'Number of times scanned',
    last_scanned_at TIMESTAMP NULL COMMENT 'Last scan time',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE,
    INDEX idx_qr_token (qr_token),
    INDEX idx_table_id (table_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Table QR codes for mobile ordering';

-- 3. Populate QR codes for existing tables (skip if exists)
INSERT IGNORE INTO qr_codes (table_id, qr_token, qr_url, created_at)
SELECT 
    id, 
    MD5(CONCAT('table_', id, '_', UNIX_TIMESTAMP(), '_mobile_order')),
    CONCAT('http://localhost/php-native/mobile/index.php?token=', MD5(CONCAT('table_', id, '_', UNIX_TIMESTAMP(), '_mobile_order'))),
    NOW()
FROM tables
WHERE id NOT IN (SELECT table_id FROM qr_codes);

-- 4. Create order status tracking table
CREATE TABLE IF NOT EXISTS order_status_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    changed_by INT COMMENT 'User ID who changed status',
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Order status change history';

-- 5. Add index for mobile token lookup
ALTER TABLE orders ADD INDEX idx_mobile_token (mobile_token);
ALTER TABLE orders ADD INDEX idx_order_source (order_source);

-- Show results
SELECT '✅ Mobile Order Database Migration Completed!' AS status;
SELECT COUNT(*) as total_tables FROM tables;
SELECT COUNT(*) as total_qr_codes FROM qr_codes;
SELECT 'Sample QR Codes:' as info;
SELECT t.name as table_name, q.qr_token, q.qr_url 
FROM qr_codes q 
JOIN tables t ON q.table_id = t.id 
LIMIT 5;
