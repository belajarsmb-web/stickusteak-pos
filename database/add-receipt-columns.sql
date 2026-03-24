-- ============================================
-- Add Receipt Template Columns - Simple Version
-- ============================================

USE posreato;

-- Try to add columns (will fail if already exist, which is OK)
ALTER TABLE receipt_templates ADD COLUMN address VARCHAR(255) DEFAULT '' AFTER header_text;
ALTER TABLE receipt_templates ADD COLUMN phone VARCHAR(50) DEFAULT '' AFTER address;
ALTER TABLE receipt_templates ADD COLUMN website VARCHAR(255) DEFAULT '' AFTER footer_text;
ALTER TABLE receipt_templates ADD COLUMN social_media VARCHAR(255) DEFAULT '' AFTER website;

SELECT 'Receipt template columns added successfully!' AS status;
