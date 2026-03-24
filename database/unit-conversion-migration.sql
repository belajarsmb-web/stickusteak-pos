-- ============================================
-- Unit Conversion System
-- Add unit conversion support to inventory
-- ============================================

USE posreato;

-- Add unit conversion columns to inventory_items table
ALTER TABLE inventory_items 
ADD COLUMN base_unit VARCHAR(10) DEFAULT 'pcs' AFTER unit,
ADD COLUMN conversion_rate DECIMAL(10,6) DEFAULT 1 AFTER base_unit;

-- Update existing items: set base_unit = unit, conversion_rate = 1
UPDATE inventory_items SET 
base_unit = unit,
conversion_rate = 1
WHERE base_unit IS NULL OR base_unit = '';

-- Add comment to columns
ALTER TABLE inventory_items 
COMMENT = 'Inventory items with unit conversion support';

-- Create unit conversion reference table
CREATE TABLE IF NOT EXISTS unit_conversions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    from_unit VARCHAR(10) NOT NULL,
    to_unit VARCHAR(10) NOT NULL,
    conversion_factor DECIMAL(10,6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_conversion (from_unit, to_unit)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert standard conversions
INSERT INTO unit_conversions (from_unit, to_unit, conversion_factor) VALUES
('kg', 'gr', 1000.000000),
('gr', 'kg', 0.001000),
('l', 'ml', 1000.000000),
('ml', 'l', 0.001000),
('pcs', 'pcs', 1.000000)
ON DUPLICATE KEY UPDATE conversion_factor = VALUES(conversion_factor);

SELECT 'Unit conversion system installed successfully!' AS status;
