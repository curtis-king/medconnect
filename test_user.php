<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require_once __DIR__.'/bootstrap/app.php';

$app = app();

$user = User::where('email', 'filadimitri@gmail.com')->first();

if ($user) {
    echo '✅ User exists: '.$user->name."\n";
    echo '📧 Email: '.$user->email."\n";
    echo '🔐 Hash present: '.(strlen($user->password) > 0 ? 'Yes' : 'No')."\n";
} else {
    $user = User::create([
        'name' => 'Fila Dimitri',
        'email' => 'filadimitri@gmail.com',
        'password' => Hash::make('admin123456789'),
        'phone' => '+212666666666',
    ]);
    echo '✅ Created user: '.$user->name."\n";
    echo '📧 Email: '.$user->email."\n";
}
