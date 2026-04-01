<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\TimeManagementPlan;
use App\Models\TimeManagementEntry;
use Carbon\Carbon;

class TimeManagementSeed extends Seeder
{
    public function run(): void
    {
        $now   = now();
        $year  = $now->year;
        $month = $now->month;

        $startTime    = '07:30';
        $endTime      = '16:00';
        $breakMinutes = 30;

        // compute hours for one day
        $startMinutes = $this->timeToMinutes($startTime);
        $endMinutes   = $this->timeToMinutes($endTime);
        $dailyMinutes = max(0, $endMinutes - $startMinutes - $breakMinutes);
        $dailyHours   = $dailyMinutes / 60;

        Employee::chunk(100, function ($employees) use ($year, $month, $startTime, $endTime, $breakMinutes, $dailyHours) {
            foreach ($employees as $employee) {
                // one plan per employee + year + month
                $plan = TimeManagementPlan::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'year'        => $year,
                        'month'       => $month,
                    ],
                    [
                        'target_hours'    => $employee->working_hour ?? 0,
                        'hourly_rate'     => $employee->salary_per_hour ?? 0,
                        'working_type'    => $employee->working_type ?? null,
                        'status'          => 'approved', // or 'draft' / 'pending'
                        'scheduled_hours' => 0,
                        'comment'         => 'Auto-seeded Standardarbeitszeiten 07:30–16:00 (Mo–Fr).',
                    ]
                );

                // remove existing entries for this plan (no date filter needed)
                $plan->entries()->delete();

                $daysInMonth     = Carbon::create($year, $month, 1)->daysInMonth;
                $scheduledHours  = 0;

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::create($year, $month, $day);

                    // Skip weekends
                    if ($date->isWeekend()) {
                        continue;
                    }

                    TimeManagementEntry::create([
                        'plan_id'       => $plan->id,
                        'work_date'     => $date->toDateString(),   // <-- USE work_date
                        'start_time'    => $startTime,
                        'end_time'      => $endTime,
                        'break_minutes' => $breakMinutes,
                        'hours'         => $dailyHours,
                    ]);

                    $scheduledHours += $dailyHours;
                }

                $plan->update([
                    'scheduled_hours' => $scheduledHours,
                ]);
            }
        });
    }

    protected function timeToMinutes(string $time): int
    {
        [$h, $m] = explode(':', $time);
        return ((int) $h) * 60 + (int) $m;
    }
}
