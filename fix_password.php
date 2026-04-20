<?php

// Generate the correct password hash
$plainPassword = 'password';
$passwordHash = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);

echo 'Generated password hash: '.$passwordHash."\n\n";

// Verify it works
if (password_verify($plainPassword, $passwordHash)) {
    echo "✅ Verification successful!\n\n";
}

// Update the database
$db = new PDO('mysql:host=127.0.0.1;dbname=zer;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$stmt = $db->prepare("UPDATE users SET password = ? WHERE email = 'patient@test.com'");
$stmt->execute([$passwordHash]);

echo "✅ User password updated in database\n";
echo "   Email: patient@test.com\n";
echo "   Password: password\n";
