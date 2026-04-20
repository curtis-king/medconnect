<?php

system('php artisan cache:clear');

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:8000/api/v1/login',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'email' => 'patient@test.com',
        'password' => 'password',
    ]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $json = json_decode($response, true);
    echo "\n✅ LOGIN SUCCESS\n\n";
    echo "User: {$json['data']['user']['name']}\n";
    echo "Email: {$json['data']['user']['email']}\n";
    echo "Token: {$json['data']['token']}\n";
    echo "\n✨ API AUTHENTICATION IS WORKING!\n\n";
} else {
    echo "\n❌ Login failed (HTTP $httpCode)\n";
    echo "Response: $response\n";
}
