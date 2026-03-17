<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $year = now()->year;

        $events = [
            [
                'id'          => 'fe0a1b2c-3d4e-5f6a-7b8c-9d0e1f2a3b4c',
                'title'       => 'Department Sync',
                'date'        => "$year-02-05",
                'time'        => '09:30',
                'description' => 'Quick sync for all department heads.',
                'status'      => 'completed',
            ],
            [
                'id'          => 'ae1b2c3d-4e5f-6a7b-8c9d-0e1f2a3b4c5d',
                'title'       => 'UTPRAS Meeting',
                'date'        => "$year-02-08",
                'time'        => '11:00',
                'description' => 'Discussion on program registration requirements.',
                'status'      => 'cancelled',
            ],
            [
                'id'          => 'be2c3d4e-5f6a-7b8c-9d0e-1f2a3b4c5d6e',
                'title'       => 'Faculty Orientation',
                'date'        => "$year-02-10",
                'time'        => '08:00',
                'description' => 'Annual faculty orientation for the new semester.',
                'status'      => 'completed',
            ],
            [
                'id'          => 'ce3d4e5f-6a7b-8c9d-0e1f-2a3b4c5d6e7f',
                'title'       => 'Student Council Meeting',
                'date'        => "$year-02-12",
                'time'        => '10:00',
                'description' => 'Monthly student council general assembly.',
                'status'      => 'completed',
            ],
            [
                'id'          => 'de4e5f6a-7b8c-9d0e-1f2a-3b4c5d6e7f8a',
                'title'       => 'Thesis Defense Panel',
                'date'        => "$year-02-14",
                'time'        => '13:00',
                'description' => 'Scheduled defense panel for graduating students.',
                'status'      => 'completed',
            ],
            [
                'id'          => 'ee5f6a7b-8c9d-0e1f-2a3b-4c5d6e7f8a9b',
                'title'       => 'Midterm Planning',
                'date'        => "$year-02-20",
                'time'        => '14:00',
                'description' => 'Planning session for midterm examinations.',
                'status'      => 'completed',
            ],
            [
                'id'          => 'fe6a7b8c-9d0e-1f2a-3b4c-5d6e7f8a9b0c',
                'title'       => 'Staff Workshop',
                'date'        => "$year-02-25",
                'time'        => '09:00',
                'description' => 'Skill development workshop for administrative staff.',
                'status'      => 'completed',
            ],
            [
                'id'          => 'ae7b8c9d-0e1f-2a3b-4c5d-6e7f8a9b0c1d',
                'title'       => 'Monthly Review',
                'date'        => "$year-02-28",
                'time'        => '15:30',
                'description' => 'End of month review and reporting.',
                'status'      => 'completed',
            ],
        ];

        foreach ($events as $event) {
            DB::table('events')->updateOrInsert(
                ['id' => $event['id']],
                array_merge($event, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
