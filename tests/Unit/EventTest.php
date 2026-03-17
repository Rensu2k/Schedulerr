<?php

namespace Tests\Unit;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_day_overrides_is_cast_to_array(): void
    {
        $event = Event::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Test',
            'date' => now()->format('Y-m-d'),
            'time' => '09:00',
            'status' => 'upcoming',
            'day_overrides' => ['2024-01-15' => ['title' => 'Special Day']],
        ]);

        $this->assertIsArray($event->day_overrides);
        $this->assertEquals(['title' => 'Special Day'], $event->day_overrides['2024-01-15']);
    }

    public function test_fillable_attributes(): void
    {
        $event = new Event;
        $fillable = ['id', 'title', 'date', 'end_date', 'time', 'location', 'classification', 'description', 'status', 'color', 'day_overrides', 'recurrence_rule', 'recurrence_end'];
        $this->assertEquals($fillable, $event->getFillable());
    }

    public function test_uses_string_primary_key(): void
    {
        $event = new Event;
        $this->assertFalse($event->getIncrementing());
        $this->assertEquals('string', $event->getKeyType());
    }
}
