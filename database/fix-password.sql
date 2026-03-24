-- Fix Admin Password
-- Run this in phpMyAdmin or MySQL

-- Use posreato database
USE posreato;

-- Update admin password (hash for 'admin123')
UPDATE users 
SET password_hash = '$2y$10$YourValidHashHere' 
WHERE username = 'admin';

-- OR delete and re-insert with correct password
DELETE FROM users WHERE username = 'admin';

INSERT INTO users (id, username, email, password_hash, full_name, phone, role_id, outlet_id, is_active)
VALUES (
    1, 
    'admin', 
    'admin@restopos.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'Administrator', 
    '021-1234567', 
    1, 
    1, 
    1
);

-- Note: The password_hash above is for 'password', not 'admin123'
-- Run fix-password.php via browser to generate correct hash
