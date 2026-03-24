<?php
/**
 * Receipt Settings API
 * GET /api/settings/receipt-settings.php - Get receipt settings
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDbConnection();

    // Get default receipt template
    $stmt = $pdo->query("SELECT * FROM receipt_templates WHERE is_default = 1 LIMIT 1");
    $template = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no default, get first template
    if (!$template) {
        $stmt = $pdo->query("SELECT * FROM receipt_templates LIMIT 1");
        $template = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get tax and service settings
    $stmt = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'tax_percentage'");
    $taxPercent = floatval($stmt->fetchColumn() ?: 10);

    $stmt = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'service_charge_percentage'");
    $servicePercent = floatval($stmt->fetchColumn() ?: 5);

    // Get outlet info
    $stmt = $pdo->query("SELECT * FROM outlets LIMIT 1");
    $outlet = $stmt->fetch(PDO::FETCH_ASSOC);

    // Format template data to match expected fields
    $templateData = null;
    if ($template) {
        $templateData = [
            'header_text' => $template['header_text'] ?? $template['restaurant_name'] ?? $outlet['name'] ?? 'RESTOQWEN',
            'address' => $template['address'] ?? $outlet['address'] ?? '',
            'phone' => $template['phone'] ?? $outlet['phone'] ?? '',
            'footer_text' => $template['footer_text'] ?? 'Thank you for your visit!',
            'website' => $template['website'] ?? '',
            'social_media' => $template['social_media'] ?? '',
            'logo_path' => $template['logo_path'] ?? ''
        ];
    }

    jsonResponse([
        'success' => true,
        'template' => $templateData,
        'tax_percent' => $taxPercent,
        'service_percent' => $servicePercent,
        'outlet' => $outlet
    ]);

} catch (PDOException $e) {
    error_log("Receipt settings fetch error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to fetch receipt settings'], 500);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
