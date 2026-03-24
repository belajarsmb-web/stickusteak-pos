-- ============================================
-- Fix Menu Item Image URLs
-- Update image_url to use correct path
-- ============================================

USE posreato;

-- Check current image URLs
SELECT id, name, image_url FROM menu_items WHERE image_url IS NOT NULL AND image_url != '';

-- If image_url is NULL or empty but files exist, update them
-- Example: If you have files in uploads/menu-items/ folder

-- Update image_url format if needed (uncomment if needed)
-- UPDATE menu_items 
-- SET image_url = CONCAT('/php-native/uploads/menu-items/', image_url)
-- WHERE image_url NOT LIKE '/php-native/%';

SELECT 'Check menu_items table above to see current image URLs' AS info;
