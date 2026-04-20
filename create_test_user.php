<?php

require 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Hashing\Hash;

try {
    // Boot the application
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    // Create test user
    $user = User::firstOrCreate(
        ['email' => 'test@example.com'],
        [
            'name' => 'Test User',
            'password' => Hash::make('test123'),
            'phone' => '+212612345678',
            'role' => 'patient',
            'email_verified_at' => now(),
        ]
    );

    echo "\n✅ Test user created/found:\n";
    echo '  Email: '.$user->email."\n";
    echo '  ID: '.$user->id."\n";
    echo "  Use password: 'test123'\n\n";
} catch (Exception $e) {
    echo "\n❌ Error: ".$e->getMessage()."\n";
    exit(1);
}
