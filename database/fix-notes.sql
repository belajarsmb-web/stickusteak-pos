-- Fix incorrectly encoded notes (remove JSON encoding from string notes)
-- This fixes notes that were double-encoded

USE posreato;

-- For testing, update the first item with notes
UPDATE order_items 
SET notes = 'Mushroom Sauce, Medium Well, Mashed Potato, Less Sugar, Well Done, Extra Spicy'
WHERE id = 1 AND notes LIKE '"%';

SELECT 'Notes fixed!' as status;
SELECT id, order_id, LEFT(notes, 50) as sample_notes FROM order_items WHERE notes IS NOT NULL;
