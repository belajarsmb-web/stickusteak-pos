<?php
/**
 * RestoQwen POS - Printers Management API
 * GET /api/settings/printers.php - Get all printers
 * POST /api/settings/printers.php - Create printer
 * PUT /api/settings/printers.php - Update printer
 * DELETE /api/settings/printers.php?id= - Delete printer
 */

require_once __DIR__ . '/../../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $pdo = getDbConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM printers ORDER BY type, name");
        $stmt->execute();
        $printers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        jsonResponse([
            'success' => true,
            'printers' => $printers,
            'count' => count($printers)
        ]);
        
    } catch (PDOException $e) {
        error_log("Printers fetch error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to fetch printers'], 500);
    }
    
} elseif ($method === 'POST') {
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);
        
        $name = $input['name'] ?? '';
        $type = $input['type'] ?? 'kitchen';
        $connection_type = $input['connection_type'] ?? 'network';
        $ip_address = $input['ip_address'] ?? null;
        $port = $input['port'] ?? 9100;
        $device_path = $input['device_path'] ?? null;
        $is_active = $input['is_active'] ?? 1;
        
        if (empty($name)) {
            jsonResponse(['success' => false, 'message' => 'Printer name is required'], 400);
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO printers (outlet_id, name, type, connection_type, ip_address, port, device_path, is_active, created_at)
            VALUES (1, :name, :type, :connection_type, :ip_address, :port, :device_path, :is_active, NOW())
        ");
        
        $stmt->execute([
            'name' => $name,
            'type' => $type,
            'connection_type' => $connection_type,
            'ip_address' => $ip_address,
            'port' => $port,
            'device_path' => $device_path,
            'is_active' => $is_active
        ]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Printer added successfully',
            'id' => $pdo->lastInsertId()
        ]);
        
    } catch (PDOException $e) {
        error_log("Printer create error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to add printer'], 500);
    }
    
} elseif ($method === 'PUT') {
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $input['id'] ?? 0;
        $name = $input['name'] ?? '';
        $type = $input['type'] ?? 'kitchen';
        $connection_type = $input['connection_type'] ?? 'network';
        $ip_address = $input['ip_address'] ?? null;
        $port = $input['port'] ?? 9100;
        $device_path = $input['device_path'] ?? null;
        $is_active = $input['is_active'] ?? 1;
        
        if (!$id || empty($name)) {
            jsonResponse(['success' => false, 'message' => 'Invalid data'], 400);
        }
        
        $stmt = $pdo->prepare("
            UPDATE printers 
            SET name = :name, 
                type = :type, 
                connection_type = :connection_type, 
                ip_address = :ip_address, 
                port = :port, 
                device_path = :device_path, 
                is_active = :is_active,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'type' => $type,
            'connection_type' => $connection_type,
            'ip_address' => $ip_address,
            'port' => $port,
            'device_path' => $device_path,
            'is_active' => $is_active
        ]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Printer updated successfully'
        ]);
        
    } catch (PDOException $e) {
        error_log("Printer update error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to update printer'], 500);
    }
    
} elseif ($method === 'DELETE') {
    try {
        $pdo = getDbConnection();
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            jsonResponse(['success' => false, 'message' => 'Printer ID required'], 400);
        }
        
        $stmt = $pdo->prepare("DELETE FROM printers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Printer deleted successfully'
        ]);
        
    } catch (PDOException $e) {
        error_log("Printer delete error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to delete printer'], 500);
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
