<?php

use App\Models\AuthToken;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    #[Test]
    public function user_can_get_profile_data(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'profile-test-'.uniqid().'@example.com',
            'phone' => '1234567890',
            'city' => 'Paris',
        ]);

        // Create auth token
        $token = AuthToken::generateToken();
        AuthToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'role' => $user->role,
            'email' => $user->email,
            'expires_at' => now()->addHours(24),
        ]);

        // Get profile
        $response = $this->getJson('/api/v1/profile', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', 'John Doe')
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.phone', '1234567890')
            ->assertJsonPath('data.city', 'Paris')
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'role',
                    'status',
                    'city',
                    'address',
                    'avatar_url',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    #[Test]
    public function user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'email' => 'profile-update-'.uniqid().'@example.com',
        ]);

        // Create auth token
        $token = AuthToken::generateToken();
        AuthToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'role' => $user->role,
            'email' => $user->email,
            'expires_at' => now()->addHours(24),
        ]);

        // Update profile
        $response = $this->patchJson('/api/v1/profile', [
            'name' => 'Jane Doe',
            'phone' => '9876543210',
            'city' => 'Lyon',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Profil mis à jour avec succès')
            ->assertJsonPath('data.name', 'Jane Doe')
            ->assertJsonPath('data.phone', '9876543210')
            ->assertJsonPath('data.city', 'Lyon');

        // Verify in database
        $user->refresh();
        $this->assertEquals('Jane Doe', $user->name);
        $this->assertEquals('9876543210', $user->phone);
    }

    #[Test]
    public function user_cannot_update_email_to_existing_email(): void
    {
        $user1 = User::factory()->create([
            'email' => 'existing-'.uniqid().'@example.com',
        ]);
        $user2 = User::factory()->create([
            'email' => 'another-'.uniqid().'@example.com',
        ]);

        // Create auth token for user2
        $token = AuthToken::generateToken();
        AuthToken::create([
            'user_id' => $user2->id,
            'token' => $token,
            'role' => $user2->role,
            'email' => $user2->email,
            'expires_at' => now()->addHours(24),
        ]);

        // Try to update to existing email
        $response = $this->patchJson('/api/v1/profile', [
            'email' => $user1->email,
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function user_can_change_password(): void
    {
        $user = User::factory()->create([
            'email' => 'password-change-'.uniqid().'@example.com',
            'password' => bcrypt('oldpassword'),
        ]);

        // Create auth token
        $token = AuthToken::generateToken();
        AuthToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'role' => $user->role,
            'email' => $user->email,
            'expires_at' => now()->addHours(24),
        ]);

        // Change password
        $response = $this->postJson('/api/v1/profile/change-password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Mot de passe changé avec succès');
    }

    #[Test]
    public function user_cannot_change_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'email' => 'password-wrong-'.uniqid().'@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        // Create auth token
        $token = AuthToken::generateToken();
        AuthToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'role' => $user->role,
            'email' => $user->email,
            'expires_at' => now()->addHours(24),
        ]);

        // Try to change with wrong current password
        $response = $this->postJson('/api/v1/profile/change-password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Mot de passe actuel incorrect');
    }

    #[Test]
    public function unauthenticated_user_cannot_access_profile(): void
    {
        // Try to access profile without token
        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(401);
    }
}
