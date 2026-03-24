<?php
/**
 * RestoQwen POS - Menu Image Upload API
 * POST /api/menu/upload-image.php
 */

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    if (!isset($_FILES['image'])) {
        jsonResponse(['success' => false, 'message' => 'No image uploaded'], 400);
    }
    
    $image = $_FILES['image'];
    $itemId = $_POST['item_id'] ?? 0;
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($image['type'], $allowedTypes)) {
        jsonResponse(['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP'], 400);
    }
    
    if ($image['size'] > $maxSize) {
        jsonResponse(['success' => false, 'message' => 'File too large. Max 2MB'], 400);
    }
    
    // Create upload directory if not exists
    $uploadDir = __DIR__ . '/../../uploads/menu-items/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $filename = 'menu_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($image['tmp_name'], $filepath)) {
        // Update database if item_id provided
        if ($itemId) {
            $pdo = getDbConnection();
            $imageUrl = '/php-native/uploads/menu-items/' . $filename;
            
            $stmt = $pdo->prepare("UPDATE menu_items SET image_url = :image_url, updated_at = NOW() WHERE id = :id");
            $stmt->execute([
                'image_url' => $imageUrl,
                'id' => $itemId
            ]);
        }
        
        jsonResponse([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'url' => '/php-native/uploads/menu-items/' . $filename,
            'filename' => $filename
        ]);
    } else {
        jsonResponse(['success' => false, 'message' => 'Failed to save image'], 500);
    }
    
} catch (Exception $e) {
    error_log("Image upload error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
