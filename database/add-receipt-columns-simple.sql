-- ============================================
-- Add Receipt Template Columns
-- Run this in phpMyAdmin
-- ============================================

USE posreato;

-- Add columns one by one
ALTER TABLE receipt_templates ADD COLUMN address VARCHAR(255) DEFAULT '';
ALTER TABLE receipt_templates ADD COLUMN phone VARCHAR(50) DEFAULT '';
ALTER TABLE receipt_templates ADD COLUMN website VARCHAR(255) DEFAULT '';
ALTER TABLE receipt_templates ADD COLUMN social_media VARCHAR(255) DEFAULT '';

SELECT 'Receipt template columns added successfully!' AS status;
SELECT 'Now you can edit receipt header in Settings page!' AS info;
