<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_display_name_uses_full_name_when_available(): void
    {
        $user = User::factory()->create([
            'name' => 'username',
            'full_name' => 'Full Name',
        ]);
        $this->assertEquals('Full Name', $user->display_name);
    }

    public function test_display_name_falls_back_to_name(): void
    {
        $user = User::factory()->create([
            'name' => 'TestUser',
            'full_name' => null,
        ]);
        $this->assertEquals('TestUser', $user->display_name);
    }

    public function test_is_admin_is_cast_to_boolean(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $this->assertTrue($user->is_admin);
    }
}
