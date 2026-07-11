INSERT INTO users (name, email, phone, password, role)
SELECT 'Admin User', 'admin@vtu.test', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'admin'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@vtu.test');
