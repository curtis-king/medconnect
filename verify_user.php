<?php

$db = new PDO('mysql:host=127.0.0.1;dbname=zer;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$stmt = $db->prepare("SELECT id, name, email, password FROM users WHERE email = 'patient@test.com'");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (! $user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found:\n";
echo "  ID: {$user['id']}\n";
echo "  Name: {$user['name']}\n";
echo "  Email: {$user['email']}\n";
echo "  Password Hash: {$user['password']}\n\n";

// Verify password
$plainPassword = 'password';
$passwordHash = '$2y$12$jCLMKWXFOCJE2PhL9L8MZuF7qOCkUr0ZF59e7JMqh2pX4JT0RHm9G';

if (password_verify($plainPassword, $user['password'])) {
    echo "✅ Password verification: SUCCESS\n";
} else {
    echo "❌ Password verification:FAILED\n";
    echo "   Expected to decode 'password' but hash doesn't match\n";
}
