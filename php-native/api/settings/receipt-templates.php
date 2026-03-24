<?php
/**
 * RestoQwen POS - Receipt Templates API
 * GET /api/settings/receipt-templates.php - Get all templates or single template
 * POST /api/settings/receipt-templates.php - Create template
 * PUT /api/settings/receipt-templates.php - Update template
 * DELETE /api/settings/receipt-templates.php?id= - Delete template
 */

require_once __DIR__ . '/../../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $pdo = getDbConnection();

        // Get single template if ID provided
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM receipt_templates WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $template = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($template) {
                jsonResponse([
                    'success' => true,
                    'template' => $template
                ]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Template not found'], 404);
            }
        } else {
            // Get all templates
            $stmt = $pdo->prepare("SELECT * FROM receipt_templates ORDER BY is_default DESC, name");
            $stmt->execute();
            $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            jsonResponse([
                'success' => true,
                'templates' => $templates,
                'count' => count($templates)
            ]);
        }

    } catch (PDOException $e) {
        error_log("Receipt templates fetch error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to fetch templates'], 500);
    }
    
} elseif ($method === 'POST') {
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);

        $template_name = $input['template_name'] ?? $input['name'] ?? '';
        $font_size = $input['font_size'] ?? 'medium';
        $paper_size = $input['paper_size'] ?? '80mm';
        $header_text = $input['header_text'] ?? '';
        $footer_text = $input['footer_text'] ?? '';
        $show_logo = $input['show_logo'] ?? 1;
        $show_tax_breakdown = $input['show_tax_breakdown'] ?? 1;
        $show_service_charge = $input['show_service_charge'] ?? 1;
        $show_qr_code = $input['show_qr_code'] ?? 0;
        $qr_code_text = $input['qr_code_text'] ?? '';
        $logo_path = $input['logo_path'] ?? '';
        $is_active = $input['is_active'] ?? 1;
        
        // Optional fields
        $address = isset($input['address']) && $input['address'] !== '' ? $input['address'] : null;
        $phone = isset($input['phone']) && $input['phone'] !== '' ? $input['phone'] : null;
        $website = isset($input['website']) && $input['website'] !== '' ? $input['website'] : null;
        $social_media = isset($input['social_media']) && $input['social_media'] !== '' ? $input['social_media'] : null;

        if (empty($template_name)) {
            jsonResponse(['success' => false, 'message' => 'Template name is required'], 400);
        }

        // Build fields dynamically - use 'name' column to match database schema
        $fields = [
            'outlet_id', 'name', 'font_size', 'paper_size', 'header_text', 'footer_text',
            'show_logo', 'show_tax_breakdown', 'show_service_charge', 'show_qr_code', 'qr_code_text',
            'logo_path', 'is_active', 'created_at'
        ];
        
        $values = [
            1, $template_name, $font_size, $paper_size, $header_text, $footer_text,
            $show_logo, $show_tax_breakdown, $show_service_charge, $show_qr_code, $qr_code_text,
            $logo_path, $is_active, 'NOW()'
        ];
        
        // Add optional fields if provided
        if ($address !== null) {
            $fields[] = 'address';
            $values[] = $address;
        }
        if ($phone !== null) {
            $fields[] = 'phone';
            $values[] = $phone;
        }
        if ($website !== null) {
            $fields[] = 'website';
            $values[] = $website;
        }
        if ($social_media !== null) {
            $fields[] = 'social_media';
            $values[] = $social_media;
        }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $sql = "INSERT INTO receipt_templates (" . implode(', ', $fields) . ") VALUES ($placeholders)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);

        jsonResponse([
            'success' => true,
            'message' => 'Template created successfully',
            'id' => $pdo->lastInsertId()
        ]);

    } catch (PDOException $e) {
        error_log("Template create error: " . $e->getMessage());
        
        // Fallback: Try without optional fields
        try {
            $stmt = $pdo->prepare("
                INSERT INTO receipt_templates (
                    outlet_id, name, font_size, paper_size, header_text, footer_text,
                    show_logo, show_tax_breakdown, show_service_charge, show_qr_code, qr_code_text,
                    logo_path, is_active, created_at
                ) VALUES (
                    1, :name, :font_size, :paper_size, :header_text, :footer_text,
                    :show_logo, :show_tax_breakdown, :show_service_charge, :show_qr_code, :qr_code_text,
                    :logo_path, :is_active, NOW()
                )
            ");

            $stmt->execute([
                'name' => $template_name,
                'font_size' => $font_size,
                'paper_size' => $paper_size,
                'header_text' => $header_text,
                'footer_text' => $footer_text,
                'show_logo' => $show_logo,
                'show_tax_breakdown' => $show_tax_breakdown,
                'show_service_charge' => $show_service_charge,
                'show_qr_code' => $show_qr_code,
                'qr_code_text' => $qr_code_text,
                'logo_path' => $logo_path,
                'is_active' => $is_active
            ]);

            jsonResponse([
                'success' => true,
                'message' => 'Template created (basic fields)',
                'id' => $pdo->lastInsertId()
            ]);
        } catch (PDOException $e2) {
            error_log("Template create fallback error: " . $e2->getMessage());
            jsonResponse(['success' => false, 'message' => 'Failed to create template'], 500);
        }
    }
    
} elseif ($method === 'PUT') {
    try {
        $pdo = getDbConnection();
        $input = json_decode(file_get_contents('php://input'), true);

        $id = $input['id'] ?? 0;
        $template_name = $input['template_name'] ?? $input['name'] ?? '';
        $font_size = $input['font_size'] ?? 'medium';
        $paper_size = $input['paper_size'] ?? '80mm';
        $header_text = $input['header_text'] ?? '';
        $footer_text = $input['footer_text'] ?? '';
        $show_logo = $input['show_logo'] ?? 1;
        $show_tax_breakdown = $input['show_tax_breakdown'] ?? 1;
        $show_service_charge = $input['show_service_charge'] ?? 1;
        $show_qr_code = $input['show_qr_code'] ?? 0;
        $qr_code_text = $input['qr_code_text'] ?? '';
        $logo_path = $input['logo_path'] ?? '';
        $is_active = $input['is_active'] ?? 1;
        
        // Optional fields (may not exist in database yet)
        $address = isset($input['address']) ? $input['address'] : null;
        $phone = isset($input['phone']) ? $input['phone'] : null;
        $website = isset($input['website']) ? $input['website'] : null;
        $social_media = isset($input['social_media']) ? $input['social_media'] : null;

        if (!$id || empty($template_name)) {
            jsonResponse(['success' => false, 'message' => 'Invalid data'], 400);
        }

        // Build dynamic query based on available fields - use 'name' column
        $fields = [
            'name = :name',
            'font_size = :font_size',
            'paper_size = :paper_size',
            'header_text = :header_text',
            'footer_text = :footer_text',
            'show_logo = :show_logo',
            'show_tax_breakdown = :show_tax_breakdown',
            'show_service_charge = :show_service_charge',
            'show_qr_code = :show_qr_code',
            'qr_code_text = :qr_code_text',
            'logo_path = :logo_path',
            'is_active = :is_active',
            'updated_at = NOW()'
        ];
        
        $params = [
            'id' => $id,
            'name' => $template_name,
            'font_size' => $font_size,
            'paper_size' => $paper_size,
            'header_text' => $header_text,
            'footer_text' => $footer_text,
            'show_logo' => $show_logo,
            'show_tax_breakdown' => $show_tax_breakdown,
            'show_service_charge' => $show_service_charge,
            'show_qr_code' => $show_qr_code,
            'qr_code_text' => $qr_code_text,
            'logo_path' => $logo_path,
            'is_active' => $is_active
        ];
        
        // Add optional fields if provided
        if ($address !== null) {
            $fields[] = 'address = :address';
            $params['address'] = $address;
        }
        if ($phone !== null) {
            $fields[] = 'phone = :phone';
            $params['phone'] = $phone;
        }
        if ($website !== null) {
            $fields[] = 'website = :website';
            $params['website'] = $website;
        }
        if ($social_media !== null) {
            $fields[] = 'social_media = :social_media';
            $params['social_media'] = $social_media;
        }

        $sql = "UPDATE receipt_templates SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        jsonResponse([
            'success' => true,
            'message' => 'Template updated successfully'
        ]);

    } catch (PDOException $e) {
        error_log("Template update error: " . $e->getMessage());
        // If column doesn't exist, try without optional fields
        try {
            $stmt = $pdo->prepare("
                UPDATE receipt_templates
                SET name = :name,
                    font_size = :font_size,
                    paper_size = :paper_size,
                    header_text = :header_text,
                    footer_text = :footer_text,
                    show_logo = :show_logo,
                    show_tax_breakdown = :show_tax_breakdown,
                    show_service_charge = :show_service_charge,
                    show_qr_code = :show_qr_code,
                    qr_code_text = :qr_code_text,
                    logo_path = :logo_path,
                    is_active = :is_active,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                'id' => $id,
                'name' => $template_name,
                'font_size' => $font_size,
                'paper_size' => $paper_size,
                'header_text' => $header_text,
                'footer_text' => $footer_text,
                'show_logo' => $show_logo,
                'show_tax_breakdown' => $show_tax_breakdown,
                'show_service_charge' => $show_service_charge,
                'show_qr_code' => $show_qr_code,
                'qr_code_text' => $qr_code_text,
                'logo_path' => $logo_path,
                'is_active' => $is_active
            ]);
            jsonResponse([
                'success' => true,
                'message' => 'Template updated (basic fields only)'
            ]);
        } catch (PDOException $e2) {
            error_log("Template update fallback error: " . $e2->getMessage());
            jsonResponse(['success' => false, 'message' => 'Failed to update template: ' . $e->getMessage()], 500);
        }
    }
    
} elseif ($method === 'DELETE') {
    try {
        $pdo = getDbConnection();
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            jsonResponse(['success' => false, 'message' => 'Template ID required'], 400);
        }
        
        $stmt = $pdo->prepare("DELETE FROM receipt_templates WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Template deleted successfully'
        ]);
        
    } catch (PDOException $e) {
        error_log("Template delete error: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Failed to delete template'], 500);
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
