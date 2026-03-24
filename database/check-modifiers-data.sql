USE posreato;

-- Check all modifier groups
SELECT '=== ALL MODIFIER GROUPS ===' AS '';
SELECT id, name, min_selection, max_selection, is_required, is_active 
FROM modifier_groups 
ORDER BY id;

-- Check all modifiers
SELECT '=== ALL MODIFIERS ===' AS '';
SELECT mg.id as group_id, mg.name as group_name, m.id as mod_id, m.name as modifier_name, m.price 
FROM modifier_groups mg
LEFT JOIN modifiers m ON m.modifier_group_id = mg.id
ORDER BY mg.id, m.id;

-- Check specifically for potato
SELECT '=== POTATO MODIFIERS ===' AS '';
SELECT mg.name as group_name, m.name as modifier_name, m.price 
FROM modifier_groups mg
JOIN modifiers m ON m.modifier_group_id = mg.id
WHERE mg.name LIKE '%Potato%' OR mg.name LIKE '%Kentang%';
