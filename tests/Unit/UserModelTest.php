<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_status_constants()
    {
        $this->assertEquals('active', User::STATUS_ACTIVE);
        $this->assertEquals('inactive', User::STATUS_INACTIVE);
    }

    public function test_user_has_status_array()
    {
        $expected = [
            'active' => 'Actif',
            'inactive' => 'Inactif',
        ];

        $this->assertEquals($expected, User::STATUSES);
    }

    public function test_user_fillable_includes_new_fields()
    {
        $user = new User;
        $fillable = $user->getFillable();

        $this->assertContains('status', $fillable);
        $this->assertContains('profile', $fillable);
        $this->assertContains('role', $fillable);
    }

    public function test_user_is_active_method()
    {
        $activeUser = User::factory()->active()->create();
        $inactiveUser = User::factory()->inactive()->create();

        $this->assertTrue($activeUser->isActive());
        $this->assertFalse($inactiveUser->isActive());
    }

    public function test_user_is_inactive_method()
    {
        $activeUser = User::factory()->active()->create();
        $inactiveUser = User::factory()->inactive()->create();

        $this->assertFalse($activeUser->isInactive());
        $this->assertTrue($inactiveUser->isInactive());
    }

    public function test_user_get_status_label_method()
    {
        $activeUser = User::factory()->active()->create();
        $inactiveUser = User::factory()->inactive()->create();

        $this->assertEquals('Actif', $activeUser->getStatusLabel());
        $this->assertEquals('Inactif', $inactiveUser->getStatusLabel());
    }

    public function test_user_scopes()
    {
        User::factory()->active()->count(3)->create();
        User::factory()->inactive()->count(2)->create();

        $this->assertEquals(3, User::active()->count());
        $this->assertEquals(2, User::inactive()->count());
    }

    public function test_user_factory_states()
    {
        $activeUser = User::factory()->active()->create();
        $inactiveUser = User::factory()->inactive()->create();
        $adminUser = User::factory()->admin()->create();

        $this->assertEquals('active', $activeUser->status);
        $this->assertEquals('inactive', $inactiveUser->status);
        $this->assertEquals('admin', $adminUser->role);
    }
}
