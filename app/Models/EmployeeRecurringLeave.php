<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeRecurringLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'title',
        'description',
        'event_kind',
        'type',
        'frequency',
        'interval',
        'weekdays',
        'day_of_week',
        'week_interval',
        'month_interval',
        'duration_days',
        'day_of_month',
        'all_day',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'is_active',
        'exdates',
        'rdates',
    ];

    protected $casts = [
        'weekdays' => 'array',
        'day_of_week' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'all_day' => 'boolean',
        'is_active' => 'boolean',
        'duration_days' => 'integer',
        'week_interval' => 'integer',
        'month_interval' => 'integer',
        'interval' => 'integer',
        'day_of_month' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function exdates(): HasMany
    {
        return $this->hasMany(EmployeeRecurringLeaveExdate::class, 'leave_id', 'id');
    }

    public function overrides(): HasMany
    {
        return $this->hasMany(EmployeeRecurringLeaveOverride::class, 'employee_recurring_leave_id', 'id');
    }

    public function generateOccurrences(Carbon $from, Carbon $to): array
    {
        $exdates = $this->exdates()
            ->select('date')
            ->get()
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $overrides = $this->overrides()->get();
        $overridesByOriginal = $overrides->keyBy(fn($override) => Carbon::parse($override->original_date)->toDateString());
        $movedToDates = $overrides
            ->filter(fn($override) => !$override->is_cancelled && $override->new_date)
            ->pluck('new_date')
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $occurrences = [];
        $cursor = $from->copy()->startOfDay();
        $windowEnd = $to->copy()->startOfDay();

        while ($cursor->lte($windowEnd)) {
            $date = $cursor->toDateString();

            if ($this->start_date && $cursor->lt(Carbon::parse($this->start_date)->startOfDay())) {
                $cursor->addDay();
                continue;
            }

            if ($this->end_date && $cursor->gt(Carbon::parse($this->end_date)->startOfDay())) {
                break;
            }

            if ($override = $overridesByOriginal->get($date)) {
                if ($override->is_cancelled) {
                    $this->emitSingle($occurrences, $date, $from, $to, [
                        'original_date' => $date,
                        'status' => 'cancelled',
                        'status_label' => 'Abgesagt',
                        'is_cancelled' => true,
                        'overridden' => true,
                        'all_day' => (bool) $this->all_day,
                        'start_time' => $this->all_day ? null : $this->start_time,
                        'end_time' => $this->all_day ? null : $this->end_time,
                        'title' => $this->title ?: $this->defaultTitle(),
                        'description' => $this->description,
                    ]);

                    $cursor->addDay();
                    continue;
                }

                $emitDate = $override->new_date ? Carbon::parse($override->new_date)->toDateString() : $date;
                $allDay = is_null($override->new_all_day) ? (bool) $this->all_day : (bool) $override->new_all_day;
                $startTime = $override->new_start_time ?: $this->start_time;
                $endTime = $override->new_end_time ?: $this->end_time;
                $title = $override->new_title ?: ($this->title ?: $this->defaultTitle());
                $description = $override->new_description ?: $this->description;
                $durationDays = $override->new_duration_days ?: ($this->duration_days ?: 1);
                $status = $emitDate !== $date ? 'moved' : 'changed';
                if ($emitDate === $date && ($override->new_start_time || $override->new_end_time || !is_null($override->new_all_day))) {
                    $status = 'time_changed';
                }

                $this->emitSpan($occurrences, $emitDate, $durationDays, $from, $to, [
                    'original_date' => $date,
                    'status' => $status,
                    'status_label' => $this->statusLabel($status),
                    'is_cancelled' => false,
                    'overridden' => true,
                    'all_day' => $allDay,
                    'start_time' => $allDay ? null : $startTime,
                    'end_time' => $allDay ? null : $endTime,
                    'title' => $title,
                    'description' => $description,
                ]);

                $cursor->addDay();
                continue;
            }

            if (!$this->matchesByRule($cursor)) {
                $cursor->addDay();
                continue;
            }

            if ($exdates->has($date)) {
                $this->emitSingle($occurrences, $date, $from, $to, [
                    'original_date' => $date,
                    'status' => 'skipped',
                    'status_label' => 'Übersprungen',
                    'is_cancelled' => true,
                    'overridden' => false,
                    'all_day' => (bool) $this->all_day,
                    'start_time' => $this->all_day ? null : $this->start_time,
                    'end_time' => $this->all_day ? null : $this->end_time,
                    'title' => $this->title ?: $this->defaultTitle(),
                    'description' => $this->description,
                ]);
                $cursor->addDay();
                continue;
            }

            if ($movedToDates->has($date)) {
                $cursor->addDay();
                continue;
            }

            $this->emitSpan($occurrences, $date, $this->duration_days ?: 1, $from, $to, [
                'original_date' => $date,
                'status' => 'normal',
                'status_label' => 'Regulär',
                'is_cancelled' => false,
                'overridden' => false,
                'all_day' => (bool) $this->all_day,
                'start_time' => $this->all_day ? null : $this->start_time,
                'end_time' => $this->all_day ? null : $this->end_time,
                'title' => $this->title ?: $this->defaultTitle(),
                'description' => $this->description,
            ]);

            $cursor->addDay();
        }

        usort($occurrences, function (array $a, array $b) {
            $dateCompare = strcmp($a['date'], $b['date']);
            if ($dateCompare !== 0) {
                return $dateCompare;
            }

            return strcmp($a['start_time'] ?? '00:00', $b['start_time'] ?? '00:00');
        });

        return $occurrences;
    }

    protected function emitSpan(array &$occurrences, string $startDate, int $durationDays, Carbon $from, Carbon $to, array $data): void
    {
        $durationDays = max(1, $durationDays);

        for ($i = 0; $i < $durationDays; $i++) {
            $date = Carbon::parse($startDate)->addDays($i)->toDateString();
            $this->emitSingle($occurrences, $date, $from, $to, $data);
        }
    }

    protected function emitSingle(array &$occurrences, string $date, Carbon $from, Carbon $to, array $data): void
    {
        if ($date < $from->toDateString() || $date > $to->toDateString()) {
            return;
        }

        $occurrences[] = array_merge($data, [
            'date' => $date,
            'event_kind' => $this->event_kind ?: 'absence',
            'event_kind_label' => $this->eventKindLabel(),
            'duration_days' => $this->duration_days ?: 1,
        ]);
    }

    protected function matchesByRule(Carbon $date): bool
    {
        if ($this->type === 'weekly') {
            $days = $this->weekdays ?? $this->day_of_week ?? [];
            $days = array_map('intval', $days ?: []);

            if (!in_array((int) $date->dayOfWeekIso, $days, true)) {
                return false;
            }

            $interval = (int) ($this->week_interval ?: $this->interval ?: 1);

            if ($interval > 1) {
                $baseWeek = Carbon::parse($this->start_date)->startOfWeek();
                $currentWeek = $date->copy()->startOfWeek();
                return $baseWeek->diffInWeeks($currentWeek) % $interval === 0;
            }

            return true;
        }

        if ($this->type === 'monthly' && $this->day_of_month) {
            $monthInterval = (int) ($this->month_interval ?: $this->interval ?: 1);

            if ((int) $date->day !== (int) $this->day_of_month) {
                return false;
            }

            if ($monthInterval > 1) {
                $base = Carbon::parse($this->start_date)->startOfMonth();
                $current = $date->copy()->startOfMonth();
                return $base->diffInMonths($current) % $monthInterval === 0;
            }

            return true;
        }

        if ($this->type === 'interval' && $this->interval) {
            $base = Carbon::parse($this->start_date)->startOfDay();
            $diff = $base->diffInDays($date->copy()->startOfDay());
            return $diff % max(1, (int) $this->interval) === 0;
        }

        if ($this->type === 'one_time') {
            return $date->toDateString() === Carbon::parse($this->start_date)->toDateString();
        }

        return true;
    }

    public function defaultTitle(): string
    {
        return match ($this->event_kind) {
            'home_office' => 'Home Office',
            'note' => 'Notiz',
            default => 'Abwesenheit',
        };
    }

    public function eventKindLabel(): string
    {
        return match ($this->event_kind) {
            'home_office' => 'Home Office',
            'note' => 'Notiz',
            default => 'Abwesenheit',
        };
    }

    protected function statusLabel(string $status): string
    {
        return match ($status) {
            'moved' => 'Verschoben',
            'changed' => 'Geändert',
            'time_changed' => 'Zeit geändert',
            'cancelled' => 'Abgesagt',
            'skipped' => 'Übersprungen',
            default => 'Regulär',
        };
    }
}
