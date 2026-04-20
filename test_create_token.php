<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Carbon;

// Get the test user
$user = User::where('email', 'patient@test.com')->firstOrFail();

// Create a token
echo "Creating token for user: {$user->name}\n";
$token = $user->createToken('test-device', ['access'], Carbon::now()->addHours(24));

if ($token && isset($token->plainTextToken)) {
    echo "✅ Token created successfully!\n";
    echo "Plain text token: {$token->plainTextToken}\n";

    // Verify it's in the database
    $count = \DB::table('personal_access_tokens')
        ->where('tokenable_id', $user->id)
        ->count();

    echo "Tokens in DB for this user: $count\n";
} else {
    echo "❌ Failed to create token\n";
}
