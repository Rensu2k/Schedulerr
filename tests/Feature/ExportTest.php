<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'Password1',
        ]);
    }

    public function test_export_requires_authentication(): void
    {
        $response = $this->post(route('export.summary'));
        $response->assertRedirect(route('login'));
    }

    public function test_export_returns_file_when_authenticated(): void
    {
        Event::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Test Event',
            'date' => now()->format('Y-m-d'),
            'time' => '09:00',
            'status' => 'upcoming',
        ]);

        $response = $this->withSession([
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
        ])->post(route('export.summary'), [
            'month' => 'all',
            'year' => date('Y'),
        ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
