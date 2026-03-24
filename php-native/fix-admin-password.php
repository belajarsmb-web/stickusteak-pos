<?php
/**
 * Fix Admin Password Script
 * Run this once to set admin password
 */

require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDbConnection();
    
    // Generate proper bcrypt hash for admin123
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "Generated hash: $hash\n\n";
    
    // Update admin user password
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin' OR email = 'admin@restopos.com'");
    $stmt->execute([$hash]);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Admin password updated successfully!\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    } else {
        // Insert admin user if not exists
        $insert = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, full_name, phone, role_id, outlet_id, is_active)
            VALUES ('admin', 'admin@restopos.com', ?, 'Administrator', '021-1234567', 1, 1, 1)
            ON DUPLICATE KEY UPDATE password_hash = ?
        ");
        $insert->execute([$hash, $hash]);
        echo "✅ Admin user created/updated successfully!\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
