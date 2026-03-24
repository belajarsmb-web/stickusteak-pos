-- ============================================
-- Setup Steak Modifiers (FINAL - Tested)
-- ============================================

USE posreato;

-- ============================================
-- 1. CREATE MODIFIER GROUPS
-- ============================================

-- Sauce Selection Group
INSERT INTO modifier_groups (name, min_selection, max_selection, is_required, is_active) 
VALUES ('Sauce Selection', 1, 1, 1, 1);
SET @sauce_id = LAST_INSERT_ID();

INSERT INTO modifiers (modifier_group_id, name, price, is_active) VALUES
(@sauce_id, 'Black Pepper Sauce', 0, 1),
(@sauce_id, 'Mushroom Sauce', 0, 1),
(@sauce_id, 'Bechamel Sauce', 5000, 1),
(@sauce_id, 'Red Wine Sauce', 5000, 1);

-- Doneness Level Group
INSERT INTO modifier_groups (name, min_selection, max_selection, is_required, is_active) 
VALUES ('Doneness Level', 1, 1, 1, 1);
SET @doneness_id = LAST_INSERT_ID();

INSERT INTO modifiers (modifier_group_id, name, price, is_active) VALUES
(@doneness_id, 'Rare', 0, 1),
(@doneness_id, 'Medium Rare', 0, 1),
(@doneness_id, 'Medium', 0, 1),
(@doneness_id, 'Medium Well', 0, 1),
(@doneness_id, 'Well Done', 0, 1);

-- Potato Side Group
INSERT INTO modifier_groups (name, min_selection, max_selection, is_required, is_active) 
VALUES ('Potato Side', 1, 1, 1, 1);
SET @potato_id = LAST_INSERT_ID();

INSERT INTO modifiers (modifier_group_id, name, price, is_active) VALUES
(@potato_id, 'Mashed Potato', 0, 1),
(@potato_id, 'Baked Potato', 5000, 1),
(@potato_id, 'French Fries', 0, 1),
(@potato_id, 'Potato Gratin', 10000, 1);

-- ============================================
-- 2. VERIFY
-- ============================================

SELECT '=== MODIFIER GROUPS ===' AS '';
SELECT id, name, min_selection, max_selection FROM modifier_groups WHERE is_active = 1;

SELECT '=== MODIFIERS ===' AS '';
SELECT mg.name as 'Group', m.name as 'Modifier', m.price as 'Price' 
FROM modifier_groups mg
JOIN modifiers m ON m.modifier_group_id = mg.id
WHERE mg.is_active = 1
ORDER BY mg.id, m.id;

SELECT 'SETUP COMPLETE!' AS STATUS;
