<?php
/**
 * Stickusteak POS - QR Code Generator
 * Generate QR codes for tables
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-native/pages/login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDbConnection();

    // Handle generate QR for specific table
    if (isset($_GET['generate']) && $_GET['generate']) {
        $tableId = (int)$_GET['generate'];
        
        // Check if QR already exists
        $stmt = $pdo->prepare("SELECT id FROM qr_codes WHERE table_id = :table_id");
        $stmt->execute(['table_id' => $tableId]);
        
        if ($stmt->fetch()) {
            // QR exists, delete and regenerate
            $stmt = $pdo->prepare("DELETE FROM qr_codes WHERE table_id = :table_id");
            $stmt->execute(['table_id' => $tableId]);
        }
        
        // Generate new QR
        $qrToken = bin2hex(random_bytes(32));
        $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
        $qrUrl = $baseUrl . '/php-native/mobile/index.php?token=' . $qrToken;
        
        $stmt = $pdo->prepare("
            INSERT INTO qr_codes (table_id, qr_token, qr_url, is_active, created_at)
            VALUES (:table_id, :qr_token, :qr_url, 1, NOW())
        ");
        $stmt->execute([
            'table_id' => $tableId,
            'qr_token' => $qrToken,
            'qr_url' => $qrUrl
        ]);
        
        header('Location: /php-native/mobile/generate-qr.php?generated=' . $tableId);
        exit;
    }

    // Get all tables with QR codes
    $stmt = $pdo->query("
        SELECT t.id, t.name, t.status, q.qr_token, q.qr_url, q.is_active, q.scan_count
        FROM tables t
        LEFT JOIN qr_codes q ON t.id = q.table_id
        ORDER BY t.id
    ");
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator - Stickusteak POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { background: #f8f9fa; }
        .qr-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            position: relative;
        }
        .qr-code {
            margin: 15px auto;
        }
        .table-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }
        .scan-count {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        .btn-generate {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-qr-code me-2"></i>Table QR Codes</h2>
            <div>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print All
                </button>
                <a href="/php-native/pages/settings.php" class="btn btn-outline-secondary ms-2">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        <?php if (isset($_GET['generated'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            QR Code generated successfully for Table <?php echo (int)$_GET['generated']; ?>!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Instructions:</strong> Print these QR codes and place them on each table.
            Customers can scan to access the mobile ordering menu.
        </div>

        <div class="row">
            <?php foreach ($tables as $table): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="qr-card">
                        <a href="?generate=<?php echo $table['id']; ?>" 
                           class="btn btn-sm btn-outline-primary btn-generate"
                           title="Regenerate QR Code">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                        <div class="table-number">Table <?php echo htmlspecialchars($table['name'] ?? $table['id']); ?></div>
                        <div class="qr-code" id="qr-<?php echo $table['id']; ?>"></div>
                        <div class="scan-count">
                            <i class="bi bi-eye me-1"></i>
                            Scanned <?php echo $table['scan_count'] ?? 0; ?> times
                        </div>
                        <div class="mt-2">
                            <span class="status-badge <?php echo $table['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $table['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </div>
                        <small style="display: block; margin-top: 10px; font-size: 0.75rem; color: #6c757d; word-break: break-all;">
                            <?php echo htmlspecialchars($table['qr_url'] ?? 'No QR generated - Click regenerate'); ?>
                        </small>
                    </div>
                </div>

                <script>
                    // Generate QR code for table <?php echo $table['id']; ?>
                    <?php if ($table['qr_url']): ?>
                    new QRCode(document.getElementById('qr-<?php echo $table['id']; ?>'), {
                        text: "<?php echo htmlspecialchars($table['qr_url']); ?>",
                        width: 150,
                        height: 150,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                    <?php else: ?>
                    document.getElementById('qr-<?php echo $table['id']; ?>').innerHTML = 
                        '<div class="text-muted" style="padding: 40px 20px;">No QR generated<br><small>Click <i class="bi bi-arrow-clockwise"></i> to generate</small></div>';
                    <?php endif; ?>
                </script>
            <?php endforeach; ?>
        </div>
    </div>

    <style media="print">
        .btn, .alert { display: none !important; }
        .qr-card { page-break-inside: avoid; border: 1px solid #ddd; }
    </style>
</body>
</html>
