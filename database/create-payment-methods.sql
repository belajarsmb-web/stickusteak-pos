-- ============================================
-- RestoQwen POS - Payment Methods Update
-- ============================================

USE posreato;

-- Add new columns
ALTER TABLE payment_methods ADD COLUMN code VARCHAR(50) AFTER name;
ALTER TABLE payment_methods ADD COLUMN icon VARCHAR(50) AFTER type;
ALTER TABLE payment_methods ADD COLUMN sort_order INT AFTER is_active;

-- Insert sample payment methods
INSERT INTO payment_methods (name, code, type, icon, sort_order, is_active) VALUES
('Tunai / Cash', 'CASH', 'cash', 'bi-cash', 1, 1),
('QRIS', 'QRIS', 'ewallet', 'bi-qr-code', 2, 1),
('GoPay', 'GOPAY', 'ewallet', 'bi-wallet2', 3, 1),
('OVO', 'OVO', 'ewallet', 'bi-wallet2', 4, 1),
('Dana', 'DANA', 'ewallet', 'bi-wallet2', 5, 1),
('ShopeePay', 'SHOPEEPAY', 'ewallet', 'bi-wallet2', 6, 1),
('Debit Card', 'DEBIT', 'card', 'bi-credit-card', 7, 1),
('Credit Card', 'CREDIT', 'card', 'bi-credit-card-2', 8, 1),
('BCA Transfer', 'BCA', 'bank_transfer', 'bi-bank', 9, 1),
('Mandiri Transfer', 'MANDIRI', 'bank_transfer', 'bi-bank', 10, 1),
('BNI Transfer', 'BNI', 'bank_transfer', 'bi-bank', 11, 1),
('BRI Transfer', 'BRI', 'bank_transfer', 'bi-bank', 12, 1);

SELECT 'Payment methods updated!' AS status;
SELECT id, name, code, type, icon FROM payment_methods WHERE is_active = 1 ORDER BY sort_order;
