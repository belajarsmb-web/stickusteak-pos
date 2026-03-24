<?php
/**
 * Reports API - GET inventory report
 * Returns current stock levels, low stock items, stock movements summary
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require authentication
requireAuth();

try {
    // Get query parameters
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $lowStockOnly = isset($_GET['low_stock']) ? filter_var($_GET['low_stock'], FILTER_VALIDATE_BOOLEAN) : false;
    $startDate = isset($_GET['start_date']) ? trim($_GET['start_date']) : date('Y-m-01');
    $endDate = isset($_GET['end_date']) ? trim($_GET['end_date']) : date('Y-m-d');
    
    // Build where clause for inventory items
    $where = ['i.is_active = 1'];
    $params = [];
    
    if ($category !== '') {
        $where[] = 'i.category = ?';
        $params[] = $category;
    }
    
    if ($lowStockOnly) {
        $where[] = 'i.current_stock <= i.min_stock_level';
    }
    
    $whereClause = implode(' AND ', $where);
    
    // Get inventory summary
    $summarySql = "SELECT 
                        COUNT(*) as total_items,
                        COALESCE(SUM(i.current_stock * i.unit_price), 0) as total_inventory_value,
                        SUM(CASE WHEN i.current_stock <= i.min_stock_level THEN 1 ELSE 0 END) as low_stock_count,
                        SUM(CASE WHEN i.current_stock <= 0 THEN 1 ELSE 0 END) as out_of_stock_count,
                        SUM(CASE WHEN i.current_stock >= i.max_stock_level THEN 1 ELSE 0 END) as overstocked_count,
                        COALESCE(AVG(i.current_stock), 0) as avg_stock_level
                    FROM inventory i
                    WHERE {$whereClause}";
    
    $summary = dbQuery($summarySql, $params)->fetch(PDO::FETCH_ASSOC);
    
    // Get low stock items (need reorder)
    $lowStockSql = "SELECT 
                        i.id,
                        i.name,
                        i.sku,
                        i.category,
                        i.current_stock,
                        i.min_stock_level,
                        i.max_stock_level,
                        i.unit,
                        i.unit_price,
                        i.reorder_point,
                        i.supplier_id,
                        s.name as supplier_name,
                        s.phone as supplier_phone,
                        (i.min_stock_level - i.current_stock) as shortage_quantity,
                        i.current_stock * i.unit_price as current_value
                    FROM inventory i
                    LEFT JOIN suppliers s ON i.supplier_id = s.id
                    WHERE i.is_active = 1 AND i.current_stock <= i.min_stock_level
                    ORDER BY i.current_stock ASC, i.name ASC";
    
    $lowStockItems = dbQuery($lowStockSql)->fetchAll(PDO::FETCH_ASSOC);
    
    // Get current stock by category
    $categoryStockSql = "SELECT 
                            i.category,
                            COUNT(*) as item_count,
                            COALESCE(SUM(i.current_stock), 0) as total_stock,
                            COALESCE(SUM(i.current_stock * i.unit_price), 0) as total_value,
                            SUM(CASE WHEN i.current_stock <= i.min_stock_level THEN 1 ELSE 0 END) as low_stock_count
                        FROM inventory i
                        WHERE i.is_active = 1
                        GROUP BY i.category
                        ORDER BY total_value DESC";
    
    $stockByCategory = dbQuery($categoryStockSql)->fetchAll(PDO::FETCH_ASSOC);
    
    // Get stock movements summary for date range
    $movementsSql = "SELECT 
                        im.movement_type,
                        COUNT(*) as movement_count,
                        COALESCE(SUM(im.quantity), 0) as total_quantity,
                        im.reference_type
                    FROM inventory_movements im
                    INNER JOIN inventory i ON im.item_id = i.id
                    WHERE DATE(im.created_at) BETWEEN ? AND ?
                    GROUP BY im.movement_type, im.reference_type
                    ORDER BY im.movement_type, total_quantity DESC";
    
    $movementsSummary = dbQuery($movementsSql, [$startDate, $endDate])->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent stock movements
    $recentMovementsSql = "SELECT 
                            im.id,
                            im.item_id,
                            im.movement_type,
                            im.quantity,
                            im.reference_type,
                            im.reference_id,
                            im.notes,
                            im.created_at,
                            i.name as item_name,
                            i.sku as item_sku,
                            i.current_stock as current_stock_after
                        FROM inventory_movements im
                        INNER JOIN inventory i ON im.item_id = i.id
                        WHERE i.is_active = 1
                        ORDER BY im.created_at DESC
                        LIMIT 50";
    
    $recentMovements = dbQuery($recentMovementsSql)->fetchAll(PDO::FETCH_ASSOC);
    
    // Get items needing reorder (below reorder point)
    $reorderSql = "SELECT 
                        i.id,
                        i.name,
                        i.sku,
                        i.current_stock,
                        i.reorder_point,
                        i.min_stock_level,
                        i.unit,
                        i.supplier_id,
                        s.name as supplier_name,
                        s.phone as supplier_phone,
                        (i.reorder_point - i.current_stock) as reorder_quantity
                    FROM inventory i
                    LEFT JOIN suppliers s ON i.supplier_id = s.id
                    WHERE i.is_active = 1 AND i.current_stock <= i.reorder_point
                    ORDER BY i.current_stock ASC";
    
    $itemsToReorder = dbQuery($reorderSql)->fetchAll(PDO::FETCH_ASSOC);
    
    // Get inventory value trend (by day for the date range)
    $valueTrendSql = "SELECT 
                        DATE(im.created_at) as date,
                        im.movement_type,
                        COALESCE(SUM(im.quantity * i.unit_price), 0) as value_change
                    FROM inventory_movements im
                    INNER JOIN inventory i ON im.item_id = i.id
                    WHERE DATE(im.created_at) BETWEEN ? AND ?
                    GROUP BY DATE(im.created_at), im.movement_type
                    ORDER BY date ASC, movement_type ASC";
    
    $valueTrend = dbQuery($valueTrendSql, [$startDate, $endDate])->fetchAll(PDO::FETCH_ASSOC);
    
    jsonResponse([
        'success' => true,
        'data' => [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'summary' => $summary,
            'low_stock_items' => $lowStockItems,
            'items_to_reorder' => $itemsToReorder,
            'stock_by_category' => $stockByCategory,
            'movements_summary' => $movementsSummary,
            'recent_movements' => $recentMovements,
            'value_trend' => $valueTrend
        ]
    ]);
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to generate inventory report', 'error' => $e->getMessage()], 500);
}
