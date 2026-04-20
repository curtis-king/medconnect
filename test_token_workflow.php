<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Carbon;

// Get the test user
$user = User::where('email', 'patient@test.com')->firstOrFail();

echo "Test 1: Create initial token\n";
$token1 = $user->createToken('mobile-app', ['access']);
echo "✅ Token 1 created\n";

echo "\nTest 2: Delete old tokens with same name\n";
try {
    $deleted = $user->tokens()->where('name', 'mobile-app')->delete();
    echo "✅ Deleted $deleted tokens\n";
} catch (Exception $e) {
    echo '❌ Error: '.$e->getMessage()."\n";
}

echo "\nTest 3: Create new token\n";
try {
    $token2 = $user->createToken('mobile-app', ['access'], Carbon::now()->addHours(24));
    echo "✅ Token 2 created\n";
    echo "Plain text token: {$token2->plainTextToken}\n";
} catch (Exception $e) {
    echo '❌ Error: '.$e->getMessage()."\n";
}
