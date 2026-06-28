<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use Carbon\Carbon;

class EmployeeTimeSchedule extends Model
{
    protected $fillable = [
        'employee_id',
        'day_of_week',
        'is_working_day',
        'start_time',
        'end_time',
        'break_minutes',
        'work_minutes',
    ];

    protected $casts = [
        'is_working_day' => 'boolean',
    ];

    public const DAYS = [
        1 => 'Montag',
        2 => 'Dienstag',
        3 => 'Mittwoch',
        4 => 'Donnerstag',
        5 => 'Freitag',
        6 => 'Samstag',
        7 => 'Sonntag',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTotalHoursAttribute()
    {
        $h = intdiv($this->work_minutes, 60);
        $m = $this->work_minutes % 60;
        return sprintf('%02d:%02d', $h, $m);
    }

    public function getBreakHoursAttribute()
    {
        $h = intdiv($this->break_minutes, 60);
        $m = $this->break_minutes % 60;
        return sprintf('%02d:%02d', $h, $m);
    }

    // -----------------------------
    // Default week creator
    // -----------------------------
    public static function createDefaultWeekForEmployee(Employee $employee): void
    {
        $defaultStart = $employee->daily_start_time ?? '07:30:00';
        $defaultEnd   = $employee->daily_end_time   ?? '16:00:00';

        $start = Carbon::parse($defaultStart);
        $end   = Carbon::parse($defaultEnd);
        $grossMinutes = $end->diffInMinutes($start);
        $breakMinutes = self::calculateBreakMinutes($grossMinutes);
        $workMinutes  = max($grossMinutes - $breakMinutes, 0);

        for ($d = 1; $d <= 7; $d++) {
            // default: Monday–Friday working, weekend off
            $isWorkingDay = $d <= 5;

            $s = $isWorkingDay ? $defaultStart : null;
            $e = $isWorkingDay ? $defaultEnd   : null;
            $b = $isWorkingDay ? $breakMinutes : 0;
            $w = $isWorkingDay ? $workMinutes  : 0;

            self::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'day_of_week' => $d,
                ],
                [
                    'is_working_day' => $isWorkingDay,
                    'start_time'     => $s,
                    'end_time'       => $e,
                    'break_minutes'  => $b,
                    'work_minutes'   => $w,
                ]
            );
        }
    }

    // same rules as controller
    public static function calculateBreakMinutes(int $grossMinutes): int
    {
        if ($grossMinutes <= 360) {
            return 0;   // <= 6h
        }
        if ($grossMinutes <= 540) {
            return 30;  // > 6h && <= 9h
        }
        return 45;      // > 9h
    }
}
