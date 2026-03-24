USE posreato;

-- Delete duplicate/incomplete modifier groups
DELETE FROM modifiers WHERE modifier_group_id IN (1, 2);
DELETE FROM modifier_groups WHERE id IN (1, 2);

-- Verify only 3 groups remain
SELECT '=== REMAINING MODIFIER GROUPS ===' AS '';
SELECT id, name, min_selection, max_selection, is_required 
FROM modifier_groups 
WHERE is_active = 1
ORDER BY id;

SELECT '=== ALL MODIFIERS ===' AS '';
SELECT mg.id as group_id, mg.name as group_name, COUNT(m.id) as modifier_count
FROM modifier_groups mg
LEFT JOIN modifiers m ON m.modifier_group_id = mg.id
WHERE mg.is_active = 1
GROUP BY mg.id, mg.name
ORDER BY mg.id;

SELECT 'Cleanup Complete!' AS status;
