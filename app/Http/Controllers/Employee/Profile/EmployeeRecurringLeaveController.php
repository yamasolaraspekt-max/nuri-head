<?php

namespace App\Http\Controllers\Employee\Profile;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeRecurringLeave;
use App\Models\EmployeeRecurringLeaveExdate;
use App\Models\EmployeeRecurringLeaveOverride;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeRecurringLeaveController extends Controller
{
    public function index(Employee $employee): JsonResponse
    {
        $items = $employee->recurringLeaves()
            ->orderByDesc('is_active')
            ->orderBy('event_kind')
            ->orderBy('start_date')
            ->orderBy('title')
            ->get()
            ->map(fn (EmployeeRecurringLeave $leave) => $this->serializeLeave($leave));

        return response()->json([
            'ok' => true,
            'data' => $items,
            'stats' => [
                'total' => $items->count(),
                'active' => $items->where('is_active', true)->count(),
                'home_office' => $items->where('event_kind', 'home_office')->count(),
                'note' => $items->where('event_kind', 'note')->count(),
            ],
        ]);
    }

    public function store(Request $request, Employee $employee): JsonResponse
    {
        $data = $this->validateData($request);
        $data['employee_id'] = $employee->id;

        $leave = EmployeeRecurringLeave::create($data);

        return response()->json([
            'ok' => true,
            'message' => 'Wiederkehrender Termin wurde gespeichert.',
            'data' => $this->serializeLeave($leave->fresh()),
        ], 201);
    }

    public function update(Request $request, Employee $employee, EmployeeRecurringLeave $leave): JsonResponse
    {
        $this->ensureBelongsToEmployee($employee, $leave);

        if ($request->has('is_active') && count($request->except(['_token'])) === 1) {
            $leave->update(['is_active' => $request->boolean('is_active')]);

            return response()->json([
                'ok' => true,
                'message' => 'Status wurde aktualisiert.',
                'data' => $this->serializeLeave($leave->fresh()),
            ]);
        }

        $leave->update($this->validateData($request));

        return response()->json([
            'ok' => true,
            'message' => 'Wiederkehrender Termin wurde aktualisiert.',
            'data' => $this->serializeLeave($leave->fresh()),
        ]);
    }

    public function destroy(Employee $employee, EmployeeRecurringLeave $leave): JsonResponse
    {
        $this->ensureBelongsToEmployee($employee, $leave);

        DB::transaction(function () use ($leave) {
            $leave->exdates()->delete();
            $leave->overrides()->delete();
            $leave->delete();
        });

        return response()->json([
            'ok' => true,
            'message' => 'Wiederkehrender Termin wurde gelöscht.',
        ]);
    }

    public function occurrences(Request $request, Employee $employee, EmployeeRecurringLeave $leave): JsonResponse
    {
        $this->ensureBelongsToEmployee($employee, $leave);

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($validated['from'] ?? now()->toDateString())->startOfDay();
        $to = Carbon::parse($validated['to'] ?? $from->copy()->addDays(90)->toDateString())->startOfDay();

        return response()->json([
            'ok' => true,
            'data' => $leave->generateOccurrences($from, $to),
        ]);
    }

    public function exdateAdd(Request $request, Employee $employee, EmployeeRecurringLeave $leave): JsonResponse
    {
        $this->ensureBelongsToEmployee($employee, $leave);

        $data = $request->validate([
            'date' => ['required', 'date'],
        ]);

        EmployeeRecurringLeaveExdate::firstOrCreate([
            'leave_id' => $leave->id,
            'date' => Carbon::parse($data['date'])->toDateString(),
        ]);

        EmployeeRecurringLeaveOverride::where('employee_recurring_leave_id', $leave->id)
            ->whereDate('original_date', Carbon::parse($data['date'])->toDateString())
            ->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Termin wurde übersprungen.',
        ]);
    }

    public function exdateRemove(Request $request, Employee $employee, EmployeeRecurringLeave $leave): JsonResponse
    {
        $this->ensureBelongsToEmployee($employee, $leave);

        $data = $request->validate([
            'date' => ['required', 'date'],
        ]);

        EmployeeRecurringLeaveExdate::where('leave_id', $leave->id)
            ->whereDate('date', Carbon::parse($data['date'])->toDateString())
            ->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Termin wurde wiederhergestellt.',
        ]);
    }

    public function upsertOverride(Request $request, Employee $employee, EmployeeRecurringLeave $leave): JsonResponse
    {
        $this->ensureBelongsToEmployee($employee, $leave);

        $data = $request->validate([
            'original_date' => ['required', 'date'],
            'is_cancelled' => ['nullable', 'boolean'],
            'new_date' => ['nullable', 'date'],
            'new_all_day' => ['nullable', 'boolean'],
            'new_start_time' => ['nullable', 'date_format:H:i'],
            'new_end_time' => ['nullable', 'date_format:H:i', 'after:new_start_time'],
            'new_duration_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'new_title' => ['nullable', 'string', 'max:255'],
            'new_description' => ['nullable', 'string'],
        ]);

        $originalDate = Carbon::parse($data['original_date'])->toDateString();

        EmployeeRecurringLeaveExdate::where('leave_id', $leave->id)
            ->whereDate('date', $originalDate)
            ->delete();

        $payload = [
            'is_cancelled' => (bool) ($data['is_cancelled'] ?? false),
            'new_date' => !empty($data['new_date']) ? Carbon::parse($data['new_date'])->toDateString() : null,
            'new_all_day' => array_key_exists('new_all_day', $data) ? (bool) $data['new_all_day'] : null,
            'new_start_time' => $data['new_start_time'] ?? null,
            'new_end_time' => $data['new_end_time'] ?? null,
            'new_duration_days' => $data['new_duration_days'] ?? null,
            'new_title' => $data['new_title'] ?? null,
            'new_description' => $data['new_description'] ?? null,
        ];

        if ($payload['is_cancelled']) {
            $payload['new_date'] = null;
            $payload['new_all_day'] = null;
            $payload['new_start_time'] = null;
            $payload['new_end_time'] = null;
            $payload['new_duration_days'] = null;
            $payload['new_title'] = null;
            $payload['new_description'] = null;
        }

        $override = EmployeeRecurringLeaveOverride::updateOrCreate(
            [
                'employee_recurring_leave_id' => $leave->id,
                'original_date' => $originalDate,
            ],
            $payload
        );

        return response()->json([
            'ok' => true,
            'message' => $payload['is_cancelled'] ? 'Termin wurde abgesagt.' : 'Termin-Ausnahme wurde gespeichert.',
            'data' => $override,
        ]);
    }

    public function deleteOverride(Request $request, Employee $employee, EmployeeRecurringLeave $leave): JsonResponse
    {
        $this->ensureBelongsToEmployee($employee, $leave);

        $data = $request->validate([
            'original_date' => ['required', 'date'],
        ]);

        EmployeeRecurringLeaveOverride::where('employee_recurring_leave_id', $leave->id)
            ->whereDate('original_date', Carbon::parse($data['original_date'])->toDateString())
            ->delete();

        EmployeeRecurringLeaveExdate::where('leave_id', $leave->id)
            ->whereDate('date', Carbon::parse($data['original_date'])->toDateString())
            ->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Ausnahme wurde entfernt.',
        ]);
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_kind' => ['required', Rule::in(['absence', 'home_office', 'note'])],
            'type' => ['required', Rule::in(['weekly', 'monthly', 'interval', 'one_time'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'all_day' => ['nullable', 'boolean'],
            'start_time' => ['nullable', 'required_if:all_day,0', 'date_format:H:i'],
            'end_time' => ['nullable', 'required_if:all_day,0', 'date_format:H:i', 'after:start_time'],
            'day_of_week' => ['nullable', 'array'],
            'day_of_week.*' => ['integer', 'min:1', 'max:7'],
            'day_of_month' => ['nullable', 'integer', 'min:1', 'max:31'],
            'interval_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'interval' => ['nullable', 'integer', 'min:1', 'max:365'],
            'week_interval' => ['nullable', 'integer', 'min:1', 'max:52'],
            'month_interval' => ['nullable', 'integer', 'min:1', 'max:24'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['all_day'] = (bool) ($data['all_day'] ?? true);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['duration_days'] = (int) ($data['duration_days'] ?? 1);
        $data['week_interval'] = (int) ($data['week_interval'] ?? 1);
        $data['month_interval'] = (int) ($data['month_interval'] ?? 1);

        if ($data['all_day']) {
            $data['start_time'] = null;
            $data['end_time'] = null;
        }

        if (($data['type'] ?? null) === 'weekly') {
            $data['day_of_week'] = array_values(array_unique(array_map('intval', $data['day_of_week'] ?? [])));
            $data['weekdays'] = $data['day_of_week'];
            $data['interval'] = $data['week_interval'];
        }

        if (($data['type'] ?? null) === 'monthly') {
            $data['interval'] = $data['month_interval'];
        }

        if (($data['type'] ?? null) === 'interval') {
            $data['interval'] = (int) ($data['interval_days'] ?? $data['interval'] ?? 1);
        }

        if (($data['type'] ?? null) !== 'weekly') {
            $data['day_of_week'] = null;
            $data['weekdays'] = null;
        }

        if (($data['type'] ?? null) !== 'monthly') {
            $data['day_of_month'] = null;
        }

        unset($data['interval_days']);

        return $data;
    }

    private function serializeLeave(EmployeeRecurringLeave $leave): array
    {
        return [
            'id' => (int) $leave->id,
            'employee_id' => (int) $leave->employee_id,
            'title' => (string) $leave->title,
            'description' => (string) ($leave->description ?? ''),
            'event_kind' => $leave->event_kind ?: 'absence',
            'event_kind_label' => $leave->eventKindLabel(),
            'type' => (string) $leave->type,
            'type_label' => $this->typeLabel($leave->type),
            'rule_human' => $this->formatRule($leave),
            'start_date' => optional($leave->start_date)->toDateString(),
            'end_date' => optional($leave->end_date)->toDateString(),
            'period' => $this->formatPeriod($leave),
            'all_day' => (bool) $leave->all_day,
            'start_time' => $leave->start_time ? substr((string) $leave->start_time, 0, 5) : null,
            'end_time' => $leave->end_time ? substr((string) $leave->end_time, 0, 5) : null,
            'time' => $leave->all_day ? 'Ganztägig' : trim(substr((string) $leave->start_time, 0, 5) . ' – ' . substr((string) $leave->end_time, 0, 5)),
            'day_of_week' => array_values(array_map('intval', $leave->day_of_week ?? $leave->weekdays ?? [])),
            'day_of_month' => $leave->day_of_month,
            'interval' => $leave->interval,
            'week_interval' => $leave->week_interval ?: 1,
            'month_interval' => $leave->month_interval ?: 1,
            'duration_days' => $leave->duration_days ?: 1,
            'is_active' => (bool) $leave->is_active,
            'exceptions_count' => $leave->exdates()->count() + $leave->overrides()->count(),
        ];
    }

    private function ensureBelongsToEmployee(Employee $employee, EmployeeRecurringLeave $leave): void
    {
        abort_if((int) $leave->employee_id !== (int) $employee->id, 404);
    }

    private function formatPeriod(EmployeeRecurringLeave $leave): string
    {
        $start = optional($leave->start_date)->format('d.m.Y') ?: '—';
        $end = $leave->end_date ? $leave->end_date->format('d.m.Y') : 'Unbegrenzt';

        return $start . ' – ' . $end;
    }

    private function formatRule(EmployeeRecurringLeave $leave): string
    {
        return match ($leave->type) {
            'weekly' => $this->formatWeeklyRule($leave),
            'monthly' => 'Alle ' . ($leave->month_interval ?: 1) . ' Monat(e) am ' . ($leave->day_of_month ?: '—') . '.',
            'interval' => 'Alle ' . ($leave->interval ?: 1) . ' Tag(e).',
            'one_time' => 'Einmalig am ' . optional($leave->start_date)->format('d.m.Y'),
            default => '—',
        };
    }

    private function formatWeeklyRule(EmployeeRecurringLeave $leave): string
    {
        $labels = [1 => 'Mo', 2 => 'Di', 3 => 'Mi', 4 => 'Do', 5 => 'Fr', 6 => 'Sa', 7 => 'So'];
        $days = collect($leave->day_of_week ?? $leave->weekdays ?? [])
            ->map(fn ($day) => $labels[(int) $day] ?? null)
            ->filter()
            ->implode(', ');

        return 'Alle ' . ($leave->week_interval ?: 1) . ' Woche(n)' . ($days ? ' am ' . $days : '') . '.';
    }

    private function typeLabel(?string $type): string
    {
        return match ($type) {
            'weekly' => 'Wöchentlich',
            'monthly' => 'Monatlich',
            'interval' => 'Intervall',
            'one_time' => 'Einmalig',
            default => '—',
        };
    }
}
