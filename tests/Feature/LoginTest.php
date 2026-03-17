<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'Password1',
        ]);
    }

    public function test_login_page_returns_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_login_success_redirects_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'Password1',
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    public function test_login_failure_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_login_failure_with_empty_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_logout_clears_session_and_redirects(): void
    {
        $user = User::where('email', 'user@example.com')->first();
        $response = $this->withSession([
            'user_id' => $user->id,
            'user_name' => $user->full_name ?? $user->name,
            'user_email' => $user->email,
        ])->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_redirect_to_dashboard_when_already_logged_in(): void
    {
        $user = User::where('email', 'user@example.com')->first();
        $response = $this->withSession([
            'user_id' => $user->id,
            'user_name' => $user->full_name ?? $user->name,
            'user_email' => $user->email,
        ])->get('/');

        $response->assertRedirect(route('dashboard'));
    }
}
