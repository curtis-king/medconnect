<?php

function apiCall($method, $path, $data = null, $token = null)
{
    $url = 'http://localhost:8000'.$path;

    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => [
                'Content-Type: application/json',
                $token ? "Authorization: Bearer {$token}" : '',
            ],
            'content' => $data ? json_encode($data) : null,
            'ignore_errors' => true,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);

    return json_decode($response, true);
}

echo "=== COMPLETE AUTHENTICATION WORKFLOW TEST ===\n\n";

// Step 1: Login
echo "[1/3] Testing LOGIN endpoint...\n";
$loginResponse = apiCall('POST', '/api/v1/login', [
    'email' => 'patient@test.com',
    'password' => 'password',
]);

if (! $loginResponse || ! isset($loginResponse['data']['token'])) {
    echo "❌ LOGIN FAILED\n";
    var_dump($loginResponse);
    exit(1);
}

$token = $loginResponse['data']['token'];
$userName = $loginResponse['data']['user']['name'];
echo "✅ LOGIN SUCCESS\n";
echo "   User: {$userName}\n";
echo '   Token: '.substr($token, 0, 30)."...\n\n";

// Step 2: Access protected route with token
echo "[2/3] Testing protected route /api/v1/me with token...\n";
$meResponse = apiCall('GET', '/api/v1/me', null, $token);

if (! $meResponse || ! isset($meResponse['user'])) {
    echo "❌ PROTECTED ROUTE FAILED\n";
    var_dump($meResponse);
    exit(1);
}

echo "✅ PROTECTED ROUTE SUCCESS\n";
echo '   Authenticated as: '.$meResponse['user']['name']."\n";
echo '   Email: '.$meResponse['user']['email']."\n\n";

// Step 3: Logout
echo "[3/3] Testing LOGOUT endpoint...\n";
$logoutResponse = apiCall('POST', '/api/v1/logout', [], $token);

if (! $logoutResponse || ! isset($logoutResponse['message'])) {
    echo "❌ LOGOUT FAILED\n";
    var_dump($logoutResponse);
    exit(1);
}

echo "✅ LOGOUT SUCCESS\n";
echo '   Message: '.$logoutResponse['message']."\n\n";

echo "=== ✅ ALL TESTS PASSED - AUTHENTICATION SYSTEM IS FULLY OPERATIONAL ===\n";
