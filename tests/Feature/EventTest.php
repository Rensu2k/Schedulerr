<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
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

    protected function withAuth(): static
    {
        return $this->withSession([
            'user_id' => $this->user->id,
            'user_name' => $this->user->full_name ?? $this->user->name,
            'user_email' => $this->user->email,
        ]);
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/events');
        $response->assertStatus(401);
    }

    public function test_index_returns_events_when_authenticated(): void
    {
        Event::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Test Event',
            'date' => now()->format('Y-m-d'),
            'time' => '09:00',
            'status' => 'upcoming',
        ]);

        $response = $this->withAuth()->getJson('/api/events');
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $response->assertJsonStructure(['data' => [['id', 'title', 'date', 'status']]]);
    }

    public function test_store_creates_event(): void
    {
        $response = $this->withAuth()->postJson('/api/events', [
            'title' => 'New Training',
            'date' => now()->addDays(5)->format('Y-m-d'),
            'location' => 'Room A',
            'classification' => 'Staff',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('events', ['title' => 'New Training']);
    }

    public function test_store_updates_event(): void
    {
        $event = Event::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Original Title',
            'date' => now()->format('Y-m-d'),
            'time' => '09:00',
            'status' => 'upcoming',
        ]);

        $response = $this->withAuth()->postJson('/api/events', [
            'id' => $event->id,
            'title' => 'Updated Title',
            'date' => $event->date,
            'status' => 'upcoming',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Updated Title']);
    }

    public function test_store_returns_404_for_nonexistent_event_update(): void
    {
        $response = $this->withAuth()->postJson('/api/events', [
            'id' => '00000000-0000-0000-0000-000000000000',
            'title' => 'Updated',
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(404);
    }

    public function test_destroy_deletes_event(): void
    {
        $event = Event::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'To Delete',
            'date' => now()->format('Y-m-d'),
            'time' => '09:00',
            'status' => 'upcoming',
        ]);

        $response = $this->withAuth()->deleteJson('/api/events/' . $event->id);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_event(): void
    {
        $response = $this->withAuth()->deleteJson('/api/events/00000000-0000-0000-0000-000000000000');
        $response->assertStatus(404);
    }
}
