<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$client = new GuzzleHttp\Client;

// 1. Login
$res = $client->post('http://localhost:8000/api/v1/login', [
    'json' => ['email' => 'test@example.com', 'password' => 'password'],
]);
$data = json_decode($res->getBody(), true);
$token = $data['data']['token'];
echo 'Login OK, Token: '.$token."\n";

// 2. Get profile
$res = $client->get('http://localhost:8000/api/v1/me', [
    'headers' => ['Authorization' => 'Bearer '.$token],
]);
echo 'GET /me: '.$res->getStatusCode()."\n";
if ($res->getStatusCode() === 200) {
    $me = json_decode($res->getBody(), true);
    echo 'User: '.($me['user']['name'] ?? 'N/A')."\n";
}

// 3. Update profile
$res = $client->put('http://localhost:8000/api/v1/profile', [
    'headers' => ['Authorization' => 'Bearer '.$token],
    'json' => ['name' => 'Test Modified', 'city' => 'Paris'],
]);
echo 'PUT /profile: '.$res->getStatusCode()."\n";
if ($res->getStatusCode() === 200) {
    echo 'Updated: '.$res->getBody()."\n";
}
