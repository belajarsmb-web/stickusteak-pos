<?php
/**
 * Item Notes API
 * GET /api/notes/index.php - Get all item notes
 */

// Enable error logging but not display
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();

    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'item_notes'");
    if ($stmt->rowCount() === 0) {
        // Table doesn't exist, return empty array
        jsonResponse([
            'success' => true,
            'notes' => [],
            'count' => 0,
            'message' => 'Item notes table not found'
        ]);
    }

    // Check which column name exists (note_text or name)
    $stmt = $pdo->query("SHOW COLUMNS FROM item_notes LIKE 'note_text'");
    $hasNoteText = $stmt->rowCount() > 0;
    
    $textColumn = $hasNoteText ? 'note_text' : 'name';

    // Get all active item notes
    $stmt = $pdo->query("
        SELECT id, {$textColumn} as note_text, category, sort_order, is_active, created_at
        FROM item_notes
        WHERE is_active = 1
        ORDER BY category, sort_order, {$textColumn}
    ");
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse([
        'success' => true,
        'notes' => $notes,
        'count' => count($notes)
    ]);

} catch (PDOException $e) {
    error_log("Notes fetch error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to fetch notes: ' . $e->getMessage()], 500);
} catch (Exception $e) {
    error_log("Notes fetch error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Server error'], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
