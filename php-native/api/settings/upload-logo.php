<?php
/**
 * RestoQwen POS - Logo Upload API
 * POST /api/settings/upload-logo.php
 */

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    if (!isset($_FILES['logo'])) {
        jsonResponse(['success' => false, 'message' => 'No logo uploaded'], 400);
    }
    
    $logo = $_FILES['logo'];
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxSize = 500 * 1024; // 500KB
    
    if (!in_array($logo['type'], $allowedTypes)) {
        jsonResponse(['success' => false, 'message' => 'Invalid file type. Allowed: PNG, JPG, GIF'], 400);
    }
    
    if ($logo['size'] > $maxSize) {
        jsonResponse(['success' => false, 'message' => 'File too large. Max 500KB'], 400);
    }
    
    // Create upload directory if not exists
    $uploadDir = __DIR__ . '/../../uploads/logos/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($logo['name'], PATHINFO_EXTENSION);
    $filename = 'logo_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($logo['tmp_name'], $filepath)) {
        $logoUrl = '/php-native/uploads/logos/' . $filename;
        
        // Update template if template_id provided
        if (isset($_POST['template_id']) && !empty($_POST['template_id'])) {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("UPDATE receipt_templates SET logo_path = :logo_path, updated_at = NOW() WHERE id = :id");
            $stmt->execute([
                'logo_path' => $logoUrl,
                'id' => $_POST['template_id']
            ]);
        }
        
        jsonResponse([
            'success' => true,
            'message' => 'Logo uploaded successfully',
            'url' => $logoUrl,
            'filename' => $filename
        ]);
    } else {
        jsonResponse(['success' => false, 'message' => 'Failed to save logo'], 500);
    }
    
} catch (Exception $e) {
    error_log("Logo upload error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
