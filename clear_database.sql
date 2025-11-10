-- Clear Database SQL Script
-- This will remove all user registration and login data
-- WARNING: This action cannot be undone!

-- Start transaction for safety
START TRANSACTION;

-- Clear user-related data in correct order (respecting foreign keys)
DELETE FROM otp_codes;
DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE user_id IS NOT NULL);
DELETE FROM orders WHERE user_id IS NOT NULL;
DELETE FROM cart_items WHERE cart_id IN (SELECT id FROM carts WHERE user_id IS NOT NULL);
DELETE FROM carts WHERE user_id IS NOT NULL;
DELETE FROM wishlists WHERE user_id IS NOT NULL;
DELETE FROM reviews WHERE user_id IS NOT NULL;
DELETE FROM shop_products WHERE shop_id IN (SELECT id FROM shops WHERE vendor_id IN (SELECT id FROM vendors WHERE id NOT IN (1)));
DELETE FROM shops WHERE vendor_id IN (SELECT id FROM vendors WHERE id NOT IN (1));
DELETE FROM users WHERE role IN ('seller', 'customer', 'staff', 'manager');
DELETE FROM vendors WHERE id NOT IN (1); -- Keep default vendor

-- Reset auto-increment counters
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE otp_codes AUTO_INCREMENT = 1;
ALTER TABLE vendors AUTO_INCREMENT = 2;
ALTER TABLE orders AUTO_INCREMENT = 1;
ALTER TABLE products AUTO_INCREMENT = 1;

-- Commit the transaction
COMMIT;

-- Show final counts
SELECT 'users' as table_name, COUNT(*) as record_count FROM users
UNION ALL
SELECT 'otp_codes', COUNT(*) FROM otp_codes
UNION ALL
SELECT 'vendors', COUNT(*) FROM vendors
UNION ALL
SELECT 'orders', COUNT(*) FROM orders
UNION ALL
SELECT 'products', COUNT(*) FROM products;
