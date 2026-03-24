-- ============================================
-- Create Tickets Table for Table Sessions
-- ============================================

USE posreato;

-- Create tickets table
CREATE TABLE IF NOT EXISTS tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_id INT NOT NULL,
    ticket_number VARCHAR(50) NOT NULL,
    status ENUM('open', 'closed', 'paid') DEFAULT 'open',
    opened_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,
    customer_name VARCHAR(255) DEFAULT '',
    customer_phone VARCHAR(50) DEFAULT '',
    total_amount DECIMAL(10,2) DEFAULT 0,
    INDEX idx_ticket_table (table_id),
    INDEX idx_ticket_status (status),
    INDEX idx_ticket_number (ticket_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SELECT 'Tickets table created successfully!' AS status;
SELECT COUNT(*) as total_tickets FROM tickets;
SELECT 'Table structure:' AS info;
DESCRIBE tickets;
