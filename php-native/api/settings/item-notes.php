<?php
/**
 * RestoQwen POS - Item Notes API
 * GET /api/settings/item-notes.php
 * POST /api/settings/item-notes.php
 */

require_once __DIR__ . '/../../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get all notes
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM item_notes WHERE is_active = 1 ORDER BY sort_order, name");
        $stmt->execute();
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        jsonResponse([
            'success' => true,
            'notes' => $notes,
            'count' => count($notes)
        ]);
    } catch (PDOException $e) {
        error_log("Notes fetch error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to fetch notes'], 500);
    }
} elseif ($method === 'POST') {
    // Create or update note
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $input['id'] ?? 0;
        $name = $input['name'] ?? '';
        $category = $input['category'] ?? 'general';
        $color = $input['color'] ?? 'primary';
        $is_active = $input['is_active'] ?? 1;
        
        if (empty($name)) {
            jsonResponse(['success' => false, 'message' => 'Note name is required'], 400);
        }
        
        if ($id) {
            // Update existing
            $stmt = $pdo->prepare("
                UPDATE item_notes 
                SET name = :name, category = :category, color = :color, is_active = :is_active, updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                'id' => $id,
                'name' => $name,
                'category' => $category,
                'color' => $color,
                'is_active' => $is_active
            ]);
            jsonResponse(['success' => true, 'message' => 'Note updated successfully']);
        } else {
            // Create new
            $stmt = $pdo->prepare("
                INSERT INTO item_notes (name, category, color, is_active, sort_order, created_at)
                VALUES (:name, :category, :color, :is_active, 0, NOW())
            ");
            $stmt->execute([
                'name' => $name,
                'category' => $category,
                'color' => $color,
                'is_active' => $is_active
            ]);
            jsonResponse(['success' => true, 'message' => 'Note created successfully', 'id' => $pdo->lastInsertId()]);
        }
    } catch (PDOException $e) {
        error_log("Notes save error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to save note'], 500);
    }
} elseif ($method === 'DELETE') {
    // Delete note
    try {
        $pdo = getDbConnection();
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            jsonResponse(['success' => false, 'message' => 'Note ID required'], 400);
        }
        
        $stmt = $pdo->prepare("DELETE FROM item_notes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        jsonResponse(['success' => true, 'message' => 'Note deleted successfully']);
    } catch (PDOException $e) {
        error_log("Notes delete error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to delete note'], 500);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
