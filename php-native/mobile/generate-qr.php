<?php
/**
 * Mobile POS - Generate QR Code for Tables
 * Generates QR code that links to mobile ordering page
 */

require_once __DIR__ . '/../config/database.php';

// Get all tables
$pdo = getDbConnection();
$tables = $pdo->query("SELECT id, table_number, name FROM tables WHERE is_active = 1 ORDER BY table_number")->fetchAll();

// Base URL for mobile ordering
$baseUrl = 'http://localhost/php-native/mobile/order.php';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR Codes - Tables</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .qr-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        .qr-card h5 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
        .qr-code img {
            max-width: 180px;
            max-height: 180px;
        }
        .btn-download {
            margin-top: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
        }
        .btn-download:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍽️ QR Code Generator</h1>
            <p>Generate QR codes for table ordering</p>
        </div>

        <div class="row">
            <?php foreach ($tables as $table): ?>
                <?php
                $tableId = $table['id'];
                $tableNumber = $table['table_number'];
                $tableName = $table['name'] ?? 'Table';
                $qrUrl = $baseUrl . '?table_id=' . $tableId;
                $qrApi = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrUrl);
                ?>
                <div class="col-md-4 col-sm-6">
                    <div class="qr-card">
                        <h5>🍽️ <?php echo htmlspecialchars($tableName); ?></h5>
                        <div class="qr-code">
                            <img src="<?php echo $qrApi; ?>" alt="QR Code">
                        </div>
                        <p class="mt-3 text-muted" style="font-size: 0.9rem;">
                            <?php echo htmlspecialchars($qrUrl); ?>
                        </p>
                        <a href="<?php echo $qrApi; ?>" download="qr-table-<?php echo $tableId; ?>.png" class="btn btn-download">
                            📥 Download QR
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($tables)): ?>
            <div class="alert alert-warning text-center">
                No tables found. Please add tables first.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
