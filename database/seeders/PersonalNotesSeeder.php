<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PersonalNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $now = Carbon::now();

        PersonalNote::create([
            'user_id' => 1, // Replace with an existing user ID
            'title' => 'Test Reminder',
            'note' => 'This is a test reminder.',
            'reminder_date' => $now->toDateString(),
            'reminder_time' => $now->addMinutes(5)->toTimeString(),
            'is_notified' => false,
        ]);
    }
}
