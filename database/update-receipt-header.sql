-- ============================================
-- Update Receipt Header Information
-- Change restaurant name, address, phone
-- ============================================

USE posreato;

-- Update receipt template header
UPDATE receipt_templates SET
    header_text = 'Stickusteak',  -- Change this to your restaurant name
    address = 'SouthCity, Jakarta',  -- Change this to your address
    phone = '08123456789'  -- Change this to your phone number
WHERE is_default = 1;

-- Also update outlet info (as backup)
UPDATE outlets SET
    name = 'Stickusteak',
    address = 'SouthCity, Jakarta',
    phone = '08123456789'
WHERE id = 1;

SELECT 'Receipt header updated successfully!' AS status;
SELECT header_text, address, phone FROM receipt_templates WHERE is_default = 1;
