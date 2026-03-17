<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'Password1',
            'full_name' => 'Test User',
        ]);
    }

    protected function withAuth(): static
    {
        return $this->withSession([
            'user_id' => $this->user->id,
            'user_name' => $this->user->full_name ?? $this->user->name,
            'user_email' => $this->user->email,
        ]);
    }

    public function test_profile_page_requires_authentication(): void
    {
        $response = $this->get(route('profile'));
        $response->assertRedirect(route('login'));
    }

    public function test_profile_page_loads_when_authenticated(): void
    {
        $response = $this->withAuth()->get(route('profile'));
        $response->assertStatus(200);
    }

    public function test_profile_update_succeeds(): void
    {
        $response = $this->withAuth()->post(route('profile.update'), [
            'full_name' => 'Updated Name',
            'email' => 'user@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->user->refresh();
        $this->assertEquals('Updated Name', $this->user->full_name);
    }

    public function test_add_user_requires_admin(): void
    {
        $response = $this->withAuth()->post(route('profile.add-user'), [
            'full_name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'Password1',
        ]);

        $response->assertStatus(403);
    }

    public function test_add_user_succeeds_when_admin(): void
    {
        $this->user->update(['is_admin' => true]);

        $response = $this->withAuth()->post(route('profile.add-user'), [
            'full_name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'Password1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }
}
