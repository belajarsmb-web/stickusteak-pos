<?php
/**
 * RestoQwen POS - General Settings API
 * GET /api/settings/general.php - Get general settings
 * POST /api/settings/general.php - Save general settings
 */

require_once __DIR__ . '/../../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $pdo = getDbConnection();
        
        $settings = [];
        
        // Get all settings
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        // Set defaults if not exist
        $defaults = [
            'restaurant_name' => 'RestoQwen POS',
            'restaurant_address' => 'Jl. Example No. 123, Jakarta',
            'restaurant_phone' => '+62 21 1234567',
            'currency' => 'IDR',
            'currency_symbol' => 'Rp',
            'language' => 'id',
            'timezone' => 'Asia/Jakarta',
            'tax_percentage' => '10',
            'service_charge_percentage' => '5',
            'tax_enabled' => '1',
            'service_enabled' => '1'
        ];
        
        foreach ($defaults as $key => $default) {
            if (!isset($settings[$key])) {
                $settings[$key] = $default;
            }
        }
        
        jsonResponse([
            'success' => true,
            'settings' => $settings
        ]);
        
    } catch (PDOException $e) {
        error_log("General settings fetch error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to fetch settings'], 500);
    }
    
} elseif ($method === 'POST') {
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);
        
        $settings_to_save = [
            'restaurant_name' => $input['restaurant_name'] ?? 'RestoQwen POS',
            'restaurant_address' => $input['restaurant_address'] ?? '',
            'restaurant_phone' => $input['restaurant_phone'] ?? '',
            'currency' => $input['currency'] ?? 'IDR',
            'currency_symbol' => $input['currency_symbol'] ?? 'Rp',
            'language' => $input['language'] ?? 'id',
            'timezone' => $input['timezone'] ?? 'Asia/Jakarta',
            'tax_percentage' => $input['tax_percentage'] ?? '10',
            'service_charge_percentage' => $input['service_charge_percentage'] ?? '5',
            'tax_enabled' => isset($input['tax_enabled']) ? ($input['tax_enabled'] ? '1' : '0') : '1',
            'service_enabled' => isset($input['service_enabled']) ? ($input['service_enabled'] ? '1' : '0') : '1'
        ];
        
        $pdo->beginTransaction();
        
        foreach ($settings_to_save as $key => $value) {
            // Check if setting exists
            $stmt = $pdo->prepare("SELECT id FROM system_settings WHERE setting_key = :key");
            $stmt->execute(['key' => $key]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Update existing
                $stmt = $pdo->prepare("
                    UPDATE system_settings 
                    SET setting_value = :value, updated_at = NOW() 
                    WHERE setting_key = :key
                ");
                $stmt->execute([
                    'value' => $value,
                    'key' => $key
                ]);
            } else {
                // Insert new
                $stmt = $pdo->prepare("
                    INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_global, created_at)
                    VALUES (:key, :value, 'string', 'General setting', 1, NOW())
                ");
                $stmt->execute([
                    'key' => $key,
                    'value' => $value
                ]);
            }
        }
        
        $pdo->commit();
        
        jsonResponse([
            'success' => true,
            'message' => 'Settings saved successfully'
        ]);
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("General settings save error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to save settings'], 500);
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
