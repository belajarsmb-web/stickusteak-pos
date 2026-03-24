<?php
/**
 * RestoQwen POS - Tax & Service Settings API
 * GET /api/settings/tax-service.php - Get settings
 * POST /api/settings/tax-service.php - Save settings
 */

require_once __DIR__ . '/../../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $pdo = getDbConnection();
        
        $settings = [];
        
        // Get tax percentage
        $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'tax_percentage'");
        $stmt->execute();
        $settings['tax_percentage'] = $stmt->fetchColumn() ?: 10;
        
        // Get service charge percentage
        $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'service_charge_percentage'");
        $stmt->execute();
        $settings['service_charge_percentage'] = $stmt->fetchColumn() ?: 5;
        
        // Get tax enabled
        $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'tax_enabled'");
        $stmt->execute();
        $settings['tax_enabled'] = $stmt->fetchColumn() ?? 1;
        
        // Get service enabled
        $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'service_enabled'");
        $stmt->execute();
        $settings['service_enabled'] = $stmt->fetchColumn() ?? 1;
        
        jsonResponse([
            'success' => true,
            'settings' => $settings
        ]);
        
    } catch (PDOException $e) {
        error_log("Tax service settings fetch error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to fetch settings'], 500);
    }
    
} elseif ($method === 'POST') {
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);
        
        $tax_percentage = $input['tax_percentage'] ?? 10;
        $service_charge_percentage = $input['service_charge_percentage'] ?? 5;
        $tax_enabled = $input['tax_enabled'] ?? 1;
        $service_enabled = $input['service_enabled'] ?? 1;
        
        // Update each setting using REPLACE INTO
        $settingsToSave = [
            ['tax_percentage', $tax_percentage, 'number', 'Tax percentage (%)'],
            ['service_charge_percentage', $service_charge_percentage, 'number', 'Service charge percentage (%)'],
            ['tax_enabled', $tax_enabled, 'boolean', 'Enable tax'],
            ['service_enabled', $service_enabled, 'boolean', 'Enable service charge']
        ];
        
        foreach ($settingsToSave as $setting) {
            $checkStmt = $pdo->prepare("SELECT id FROM system_settings WHERE setting_key = ?");
            $checkStmt->execute([$setting[0]]);
            
            if ($checkStmt->fetch()) {
                // Update existing
                $updateStmt = $pdo->prepare("UPDATE system_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
                $updateStmt->execute([$setting[1], $setting[0]]);
            } else {
                // Insert new
                $insertStmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_active) VALUES (?, ?, ?, ?, 1)");
                $insertStmt->execute([$setting[0], $setting[1], $setting[2], $setting[3]]);
            }
        }
        
        jsonResponse([
            'success' => true,
            'message' => 'Settings saved successfully'
        ]);
        
    } catch (PDOException $e) {
        error_log("Tax service settings save error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to save settings: ' . $e->getMessage()], 500);
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
