-- ============================================
-- RestoQwen POS - Void Reasons Table
-- ============================================

USE posreato;

-- Create void reasons table
CREATE TABLE IF NOT EXISTS void_reasons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reason VARCHAR(255) NOT NULL,
    category ENUM('kitchen', 'service', 'customer', 'other') DEFAULT 'other',
    is_active TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default void reasons
INSERT INTO void_reasons (reason, category, sort_order) VALUES
('Customer cancelled order', 'customer', 1),
('Wrong item ordered', 'kitchen', 2),
('Item not available', 'kitchen', 3),
('Quality issue', 'kitchen', 4),
('Duplicate order', 'service', 5),
('Customer allergy', 'customer', 6),
('Too long wait time', 'service', 7),
('Wrong temperature', 'kitchen', 8),
('Spill/Dropped', 'service', 9),
('Manager approval', 'other', 10);

-- Add void tracking to order_items (already has is_voided, void_reason, voided_at)
-- Just need to add voided_by (user who voided)
ALTER TABLE order_items ADD COLUMN voided_by INT AFTER voided_at;
ALTER TABLE order_items ADD COLUMN void_reason_text VARCHAR(255) AFTER void_reason;

SELECT 'Void reasons table created!' AS status;
SELECT * FROM void_reasons WHERE is_active = 1 ORDER BY sort_order;
