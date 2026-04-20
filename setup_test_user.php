<?php

// Re-create test user with proper password hash
$db = new PDO('mysql:host=127.0.0.1;dbname=zer;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Generate a bcrypt hash for password "password"
$passwordHash = '$2y$12$jCLMKWXFOCJE2PhL9L8MZuF7qOCkUr0ZF59e7JMqh2pX4JT0RHm9G';

$db->exec("DELETE FROM users WHERE email = 'patient@test.com'");

$stmt = $db->prepare('
    INSERT INTO users (name, email, password, phone, role, status, email_verified_at, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())
');

$stmt->execute([
    'Test Patient',
    'patient@test.com',
    $passwordHash,
    '+212612345678',
    'patient',
    'active',
]);

echo "✅ Test user created: patient@test.com (password: password)\n";
