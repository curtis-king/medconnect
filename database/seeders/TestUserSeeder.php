<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer l'utilisateur de test seulement s'il n'existe pas
        if (! User::where('email', 'testuser@example.com')->exists()) {
            User::create([
                'name' => 'Test User',
                'email' => 'testuser@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
                'profile' => 'This is a test user profile.',
            ]);
        }

        // Créer l'utilisateur admin de test seulement s'il n'existe pas
        if (! User::where('email', 'adminuser@example.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'adminuser@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'profile' => 'This is an admin user profile.',
            ]);
        }
    }
}
