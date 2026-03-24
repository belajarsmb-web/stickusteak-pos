-- ============================================
-- RestoQwen POS - Modifier Groups & Modifiers
-- For cooking instructions, add-ons, etc.
-- ============================================

USE posreato;

-- Modifier Groups (e.g., "Steak Temperature", "Spice Level", "Add-ons")
CREATE TABLE IF NOT EXISTS modifier_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    selection_type ENUM('single', 'multiple', 'required_single', 'required_multiple') DEFAULT 'single',
    min_selections INT DEFAULT 0,
    max_selections INT DEFAULT 0,
    is_active TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Modifiers (individual options within a group)
CREATE TABLE IF NOT EXISTS modifiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modifier_group_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price_adjustment DECIMAL(10,2) DEFAULT 0.00,
    is_active TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (modifier_group_id) REFERENCES modifier_groups(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Link modifier groups to menu categories
CREATE TABLE IF NOT EXISTS modifier_group_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modifier_group_id INT NOT NULL,
    category_id INT NOT NULL,
    is_required TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (modifier_group_id) REFERENCES modifier_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_group_category (modifier_group_id, category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample modifier groups for steak restaurant
INSERT INTO modifier_groups (name, description, selection_type, min_selections, max_selections, sort_order) VALUES
('Steak Temperature', 'Choose how you want your steak cooked', 'required_single', 1, 1, 1),
('Spice Level', 'Choose your preferred spice level', 'single', 0, 1, 2),
('Side Sauce', 'Choose additional sauce', 'multiple', 0, 3, 3),
('Add-ons', 'Extra items to add to your order', 'multiple', 0, 5, 4);

-- Insert modifiers for Steak Temperature
INSERT INTO modifiers (modifier_group_id, name, price_adjustment, sort_order) VALUES
(1, 'Rare', 0.00, 1),
(1, 'Medium Rare', 0.00, 2),
(1, 'Medium', 0.00, 3),
(1, 'Medium Well', 0.00, 4),
(1, 'Well Done', 0.00, 5),
(1, 'Overcooked', 0.00, 6);

-- Insert modifiers for Spice Level
INSERT INTO modifiers (modifier_group_id, name, price_adjustment, sort_order) VALUES
(2, 'No Spice', 0.00, 1),
(2, 'Mild', 0.00, 2),
(2, 'Medium', 0.00, 3),
(2, 'Hot', 0.00, 4),
(2, 'Extra Hot', 0.00, 5);

-- Insert modifiers for Side Sauce
INSERT INTO modifiers (modifier_group_id, name, price_adjustment, sort_order) VALUES
(3, 'Black Pepper Sauce', 5000.00, 1),
(3, 'Mushroom Sauce', 5000.00, 2),
(3, 'Garlic Butter', 3000.00, 3),
(3, 'BBQ Sauce', 3000.00, 4),
(3, 'Chimichurri', 5000.00, 5);

-- Insert modifiers for Add-ons
INSERT INTO modifiers (modifier_group_id, name, price_adjustment, sort_order) VALUES
(4, 'Extra Cheese', 10000.00, 1),
(4, 'Extra Bacon', 15000.00, 2),
(4, 'Fried Egg', 8000.00, 3),
(4, 'Mashed Potato', 15000.00, 4),
(4, 'Grilled Vegetables', 18000.00, 5),
(4, 'French Fries', 12000.00, 6);

-- Link modifier groups to categories
-- Steak Temperature for Premium Steaks category (id=1)
INSERT INTO modifier_group_categories (modifier_group_id, category_id, is_required) VALUES
(1, 1, 1),  -- Steak Temperature required for Premium Steaks
(2, 1, 0),  -- Spice Level optional for Premium Steaks
(3, 1, 0),  -- Side Sauce optional for Premium Steaks
(4, 1, 0),  -- Add-ons optional for Premium Steaks
(2, 2, 0),  -- Spice Level optional for Burgers
(3, 2, 0),  -- Side Sauce optional for Burgers
(4, 2, 0),  -- Add-ons optional for Burgers
(4, 3, 0);  -- Add-ons optional for Side Dishes

SELECT 'Modifier tables created and sample data inserted!' AS status;
SELECT 'Modifier Groups:' AS info;
SELECT * FROM modifier_groups;
SELECT 'Modifiers:' AS info;
SELECT mg.name as group_name, m.name as modifier_name, m.price_adjustment 
FROM modifiers m 
JOIN modifier_groups mg ON m.modifier_group_id = mg.id 
ORDER BY mg.sort_order, m.sort_order;
