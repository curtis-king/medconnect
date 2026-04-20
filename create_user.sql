-- Create test user
INSERT INTO users (name, email, password, phone, role, email_verified_at, created_at, updated_at)
VALUES (
    'Test User',
    'test@example.com',
    '$2y$12$82tRB4eaJATkXfRfb6g3/ee9Wud7BQIL.HM2Q34wM3.xyLNXB5jWK',
    '+212612345678',
    'patient',
    NOW(),
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE updated_at = NOW();

SELECT * FROM users WHERE email = 'test@example.com';
