<?php

namespace Tests\Feature\Api\V1;

use App\Models\AuthToken;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiAuthenticationFlowTest extends TestCase
{
    #[Test]
    public function api_login_returns_valid_token_with_simple_auth(): void
    {
        // Create a test user with unique email
        $email = 'apitest-'.uniqid().'@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password123'),
        ]);

        // Test login endpoint
        $response = $this->postJson('/api/v1/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        // Verify response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'success',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'role',
                        'avatar_url',
                    ],
                    'token',
                    'token_type',
                    'expires_in',
                    'expires_at',
                ],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token_type', 'Bearer');

        $token = $response->json('data.token');
        $this->assertNotEmpty($token, 'Token should not be empty');
        $this->assertIsString($token, 'Token should be a string');

        // Verify token is stored in database
        $storedToken = AuthToken::where('token', $token)->first();
        $this->assertNotNull($storedToken, 'Token should be stored in database');
        $this->assertEquals($user->id, $storedToken->user_id);
        $this->assertEquals($email, $storedToken->email);
        $this->assertEquals($user->role, $storedToken->role);

        // Test that we can access protected endpoint with this token
        $profileResponse = $this->getJson('/api/v1/profile', [
            'Authorization' => "Bearer {$token}",
        ]);

        $profileResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'role',
                    'created_at',
                ],
            ])
            ->assertJsonPath('data.email', $email);
    }

    #[Test]
    public function token_is_stored_with_expiration_date(): void
    {
        $user = User::factory()->create();

        // Create token via login
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $response->json('data.token');
        $storedToken = AuthToken::where('token', $token)->first();

        $this->assertNotNull($storedToken->expires_at, 'Token should have an expiration date');
        $this->assertTrue($storedToken->expires_at->isFuture(), 'Token expiration should be in the future');
    }

    #[Test]
    public function old_tokens_are_revoked_on_new_login(): void
    {
        $email = 'apitest-'.uniqid().'@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password123'),
        ]);

        // First login
        $response1 = $this->postJson('/api/v1/login', [
            'email' => $email,
            'password' => 'password123',
        ]);
        $token1 = $response1->json('data.token');

        // Second login
        $response2 = $this->postJson('/api/v1/login', [
            'email' => $email,
            'password' => 'password123',
        ]);
        $token2 = $response2->json('data.token');

        // Old token should not exist
        $oldToken = AuthToken::where('token', $token1)->first();
        $this->assertNull($oldToken, 'Old token should be revoked');

        // New token should exist
        $newToken = AuthToken::where('token', $token2)->first();
        $this->assertNotNull($newToken, 'New token should exist');
    }

    #[Test]
    public function profile_requires_valid_bearer_token(): void
    {
        // Test without token
        $response = $this->getJson('/api/v1/profile');
        $response->assertStatus(401);

        // Test with invalid token
        $response = $this->getJson('/api/v1/profile', [
            'Authorization' => 'Bearer invalid-token-here',
        ]);
        $response->assertStatus(401);
    }

    #[Test]
    public function expired_token_is_rejected(): void
    {
        $user = User::factory()->create();

        // Create an expired token manually
        $token = AuthToken::generateToken();
        AuthToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'role' => $user->role,
            'email' => $user->email,
            'expires_at' => now()->subHour(),
        ]);

        // Try to use expired token
        $response = $this->getJson('/api/v1/profile', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Token expired');

        // Token should be deleted
        $deletedToken = AuthToken::where('token', $token)->first();
        $this->assertNull($deletedToken, 'Expired token should be deleted');
    }

    #[Test]
    public function logout_revokes_current_token(): void
    {
        $email = 'apitest-'.uniqid().'@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password123'),
        ]);

        // Login
        $loginResponse = $this->postJson('/api/v1/login', [
            'email' => $email,
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Logout
        $logoutResponse = $this->postJson('/api/v1/logout', [], [
            'Authorization' => "Bearer {$token}",
        ]);

        $logoutResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // Token should be deleted
        $deletedToken = AuthToken::where('token', $token)->first();
        $this->assertNull($deletedToken, 'Token should be deleted after logout');
    }
}
