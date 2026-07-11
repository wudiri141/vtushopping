ALTER TABLE users MODIFY role ENUM('customer', 'user', 'admin') NOT NULL DEFAULT 'user';
UPDATE users SET role = 'user' WHERE role = 'customer';
ALTER TABLE users MODIFY role ENUM('user', 'admin') NOT NULL DEFAULT 'user';
