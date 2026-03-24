-- ============================================
-- FIX: Item Notes Table
-- Run this if item_notes table is missing
-- ============================================

USE posreato;

-- Create item notes table if not exists
CREATE TABLE IF NOT EXISTS item_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_text TEXT NOT NULL,
    category ENUM('kitchen', 'bar', 'general') DEFAULT 'general',
    color VARCHAR(20) DEFAULT 'primary',
    is_active TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Check if we need to add note_text column (if table exists with different structure)
SET @dbname = DATABASE();
SET @tablename = 'item_notes';
SET @columnname = 'note_text';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN note_text TEXT NOT NULL AFTER id')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Insert default notes (ignore duplicates)
INSERT IGNORE INTO item_notes (note_text, category, color, sort_order) VALUES
-- Kitchen Notes
('Tanpa Garam', 'kitchen', 'danger', 1),
('Kurang Garam', 'kitchen', 'warning', 2),
('Lebih Garam', 'kitchen', 'warning', 3),
('Tanpa Micin', 'kitchen', 'danger', 4),
('Pedas', 'kitchen', 'danger', 5),
('Lebih Pedas', 'kitchen', 'danger', 6),
('Tidak Pedas', 'kitchen', 'success', 7),
('Matang Sempurna', 'kitchen', 'primary', 8),
('Setengah Matang', 'kitchen', 'primary', 9),
('Mentah', 'kitchen', 'primary', 10),
('Tanpa Bawang', 'kitchen', 'danger', 11),
('Extra Keju', 'kitchen', 'info', 12),
('Tanpa Sayur', 'kitchen', 'danger', 13),
('Porsi Kecil', 'kitchen', 'warning', 14),
('Porsi Besar', 'kitchen', 'warning', 15),
-- Bar Notes
('Tanpa Es', 'bar', 'danger', 16),
('Extra Es', 'bar', 'info', 17),
('Kurang Manis', 'bar', 'warning', 18),
('Lebih Manis', 'bar', 'warning', 19),
('Tanpa Gula', 'bar', 'danger', 20),
('Hangat', 'bar', 'warning', 21),
('Dingin', 'bar', 'info', 22),
-- General Notes
('Segera', 'general', 'danger', 23),
('Jangan Terlalu Lama', 'general', 'warning', 24),
('Untuk Dibawa', 'general', 'info', 25),
('Pakai Sendok', 'general', 'primary', 26),
('Pakai Saus', 'general', 'primary', 27);

-- Update note_text from name if column exists and note_text is empty
SET @has_name = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE table_schema = 'posreato' AND table_name = 'item_notes' AND column_name = 'name');
SET @sql = IF(@has_name > 0,
    "UPDATE item_notes SET note_text = name WHERE note_text IS NULL OR note_text = ''",
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verify
SELECT COUNT(*) as total_notes FROM item_notes;
SELECT 'Item notes table created/verified successfully!' AS status;
