-- ============================================
-- RestoQwen POS - Item Notes/Remarks Table
-- ============================================

USE posreato;

-- Create item notes table for kitchen/bar instructions
CREATE TABLE IF NOT EXISTS item_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category ENUM('kitchen', 'bar', 'general') DEFAULT 'general',
    color VARCHAR(20) DEFAULT 'primary',
    is_active TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default notes
INSERT INTO item_notes (name, category, color, sort_order) VALUES
-- Kitchen Notes
('Tanpa Garam', 'kitchen', 'danger', 1),
('Kurang Garam', 'kitchen', 'warning', 2),
('Lebih Garam', 'kitchen', 'warning', 3),
('Tanpa Micin', 'kitchen', 'danger', 4),
('Pedas', 'kitchen', 'danger', 5),
('Lebih Pedas', 'kitchen', 'danger', 6),
('Tidak Pedas', 'kitchen', 'success', 7),
('Matang Sempurna', 'kitchen', 'primary', 8),
('Setengah Matang', 'kitchen', 'primary', 9),
('Mentah', 'kitchen', 'primary', 10),
('Tanpa Bawang', 'kitchen', 'danger', 11),
('Extra Keju', 'kitchen', 'info', 12),
('Tanpa Sayur', 'kitchen', 'danger', 13),
('Porsi Kecil', 'kitchen', 'warning', 14),
('Porsi Besar', 'kitchen', 'warning', 15),
-- Bar Notes
('Tanpa Es', 'bar', 'danger', 16),
('Extra Es', 'bar', 'info', 17),
('Kurang Manis', 'bar', 'warning', 18),
('Lebih Manis', 'bar', 'warning', 19),
('Tanpa Gula', 'bar', 'danger', 20),
('Hangat', 'bar', 'warning', 21),
('Dingin', 'bar', 'info', 22),
-- General Notes
('Segera', 'general', 'danger', 23),
('Jangan Terlalu Lama', 'general', 'warning', 24),
('Untuk Dibawa', 'general', 'info', 25),
('Pakai Sendok', 'general', 'primary', 26),
('Pakai Saus', 'general', 'primary', 27);

SELECT 'Item notes table created and default notes inserted!' AS status;
