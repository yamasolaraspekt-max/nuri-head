<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeRecurringLeave;
use App\Models\EmployeeSick;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardEmployeeStatusController extends Controller
{
    public function index(): JsonResponse
    {
        $today = Carbon::today(config('app.timezone'));

        $employees = Employee::query()
            ->select([
                'id',
                'name',
                'lastname',
                'image',
                'status',
            ])
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereIn(DB::raw('LOWER(status)'), [
                        'active',
                        'aktiv',
                    ]);
            })
            ->orderBy('name')
            ->orderBy('lastname')
            ->get();

        $activeEmployeeIds = $employees
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $absences = $this->todayAbsences($today, $activeEmployeeIds);

        $absentEmployeeIds = $absences
            ->pluck('employee_id')
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $activeEmployees = $employees
            ->reject(fn($employee) => $absentEmployeeIds->contains((int) $employee->id))
            ->values();

        $inactiveEmployees = $employees
            ->filter(fn($employee) => $absentEmployeeIds->contains((int) $employee->id))
            ->values();

        return response()->json([
            'success' => true,
            'date' => $today->toDateString(),

            'summary' => [
                'active' => $activeEmployees->count(),
                'inactive' => $inactiveEmployees->count(),
                'sick' => $absences->where('type', 'sick')->count(),
                'holiday' => $absences->where('type', 'holiday')->count(),
                'recurring' => $absences->where('type', 'recurring')->count(),
                'total' => $employees->count(),
            ],

            'active' => $activeEmployees
                ->map(fn($employee) => $this->employeePayload($employee))
                ->values(),

            'inactive' => $inactiveEmployees
                ->map(function ($employee) use ($absences) {
                    $absence = $absences->first(function ($item) use ($employee) {
                        return (int) ($item['employee_id'] ?? 0) === (int) $employee->id;
                    });

                    return array_merge($this->employeePayload($employee), [
                        'absence_type' => $absence['type'] ?? 'inactive',
                        'absence_label' => $absence['absence_label'] ?? $absence['label'] ?? 'Abwesend',
                        'absence_type_label' => $absence['absence_type_label'] ?? null,
                        'absence_kind' => $absence['absence_kind'] ?? null,
                        'absence_from' => $absence['absence_from'] ?? null,
                        'absence_to' => $absence['absence_to'] ?? null,
                        'absence_reason' => $absence['absence_reason'] ?? null,
                        'absence_time_from' => $absence['absence_time_from'] ?? null,
                        'absence_time_to' => $absence['absence_time_to'] ?? null,
                        'absence_source' => $absence['source'] ?? null,
                        'absence_source_id' => $absence['source_id'] ?? null,
                    ]);
                })
                ->values(),
        ]);
    }

    private function todayAbsences(Carbon $today, Collection $activeEmployeeIds): Collection
    {
        if ($activeEmployeeIds->isEmpty()) {
            return collect();
        }

        $sickAbsences = $this->todaySickAbsences($today, $activeEmployeeIds);
        $holidayAbsences = $this->todayHolidayAbsences($today, $activeEmployeeIds);
        $recurringAbsences = $this->todayRecurringLeaveAbsences($today, $activeEmployeeIds);

        /*
         |--------------------------------------------------------------------------
         | Priority
         |--------------------------------------------------------------------------
         | If one employee has multiple absence records today, we keep only one.
         | Sick has highest priority, then holiday, then recurring leave.
         */
        return collect()
            ->merge($sickAbsences)
            ->merge($holidayAbsences)
            ->merge($recurringAbsences)
            ->unique('employee_id')
            ->values();
    }

    private function todaySickAbsences(Carbon $today, Collection $activeEmployeeIds): Collection
    {
        return EmployeeSick::query()
            ->select([
                'id',
                'emp_id',
                'start_date',
                'end_date',
                'status',
                'status_msg',
                'created_at',
            ])
            ->whereIn('emp_id', $activeEmployeeIds)
            ->whereDate('start_date', '<=', $today->toDateString())
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today->toDateString());
            })

            /*
             |--------------------------------------------------------------------------
             | Important Fix
             |--------------------------------------------------------------------------
             | Sick records should be counted for today unless they are rejected,
             | cancelled, deleted, or declined.
             |
             | Do not require accepted/approved/send only, because many valid sick
             | records can have status like pending, submitted, new, null, etc.
             */
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereNotIn(DB::raw('LOWER(status)'), [
                        'rejected',
                        'abgelehnt',
                        'declined',
                        'cancelled',
                        'canceled',
                        'storniert',
                        'deleted',
                        'gelöscht',
                        'removed',
                    ]);
            })
            ->get()
            ->map(function (EmployeeSick $sick) {
                return [
                    'employee_id' => (int) $sick->emp_id,
                    'type' => 'sick',
                    'label' => 'Krank',
                    'source' => 'employee_sicks',
                    'source_id' => (int) $sick->id,

                    'absence_kind' => 'sick',
                    'absence_label' => 'Krank',
                    'absence_type_label' => 'Krankmeldung',
                    'absence_from' => $this->formatDate($sick->start_date),
                    'absence_to' => $sick->end_date ? $this->formatDate($sick->end_date) : null,
                    'absence_reason' => $sick->status_msg,
                    'absence_time_from' => optional($sick->created_at)->format('H:i'),
                    'absence_time_to' => null,
                ];
            })
            ->toBase()
            ->values();
    }

    private function todayHolidayAbsences(Carbon $today, Collection $activeEmployeeIds): Collection
    {
        return Leave::query()
            ->select([
                'id',
                'emp_id',
                'leave_type',
                'start_date',
                'end_date',
                'approved',
                'status',
                'reason',
                'description',
            ])
            ->whereIn('emp_id', $activeEmployeeIds)
            ->whereDate('start_date', '<=', $today->toDateString())
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereNotIn(DB::raw('LOWER(status)'), [
                        'rejected',
                        'abgelehnt',
                        'declined',
                        'cancelled',
                        'canceled',
                        'storniert',
                        'deleted',
                        'gelöscht',
                    ]);
            })
            ->where(function ($query) {
                $query->whereIn(DB::raw('LOWER(approved)'), [
                    'yes',
                    'ja',
                    '1',
                    'true',
                    'accepted',
                    'approved',
                    'genehmigt',
                ])
                    ->orWhereIn(DB::raw('LOWER(status)'), [
                        'accepted',
                        'approved',
                        'active',
                        'genehmigt',
                        'bestätigt',
                        'bestaetigt',
                    ]);
            })
            ->get()
            ->map(function (Leave $leave) {
                $label = $this->holidayLabel($leave);

                return [
                    'employee_id' => (int) $leave->emp_id,
                    'type' => $this->holidayType($leave),
                    'label' => $label,
                    'source' => 'leaves',
                    'source_id' => (int) $leave->id,

                    'absence_kind' => $this->holidayType($leave),
                    'absence_label' => $label,
                    'absence_type_label' => $leave->leave_type ?: $label,
                    'absence_from' => $this->formatDate($leave->start_date),
                    'absence_to' => $this->formatDate($leave->end_date),
                    'absence_reason' => $leave->reason ?: $leave->description,
                    'absence_time_from' => null,
                    'absence_time_to' => null,
                ];
            })
            ->toBase()
            ->values();
    }

    private function todayRecurringLeaveAbsences(Carbon $today, Collection $activeEmployeeIds): Collection
    {
        return EmployeeRecurringLeave::query()
            ->with(['exdates', 'overrides'])
            ->whereIn('employee_id', $activeEmployeeIds)
            ->where('is_active', true)
            ->whereDate('start_date', '<=', $today->toDateString())
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today->toDateString());
            })
            ->get()
            ->flatMap(function (EmployeeRecurringLeave $leave) use ($today) {
                if (!method_exists($leave, 'generateOccurrences')) {
                    return collect();
                }

                $occurrences = $leave->generateOccurrences(
                    $today->copy(),
                    $today->copy()
                );

                return collect($occurrences)->map(function ($occurrence) use ($leave) {
                    return [
                        'employee_id' => (int) $leave->employee_id,
                        'type' => 'recurring',
                        'label' => $occurrence['title'] ?? $leave->title ?? 'Wiederkehrende Abwesenheit',
                        'source' => 'employee_recurring_leaves',
                        'source_id' => (int) $leave->id,

                        'absence_kind' => 'recurring',
                        'absence_label' => $occurrence['title'] ?? $leave->title ?? 'Wiederkehrende Abwesenheit',
                        'absence_type_label' => $leave->title ?? 'Wiederkehrende Abwesenheit',
                        'absence_from' => $occurrence['date'] ?? null,
                        'absence_to' => $occurrence['date'] ?? null,
                        'absence_reason' => $occurrence['description'] ?? $leave->description,
                        'absence_time_from' => $occurrence['start_time'] ?? null,
                        'absence_time_to' => $occurrence['end_time'] ?? null,
                    ];
                });
            })
            ->toBase()
            ->values();
    }

    private function holidayType(Leave $leave): string
    {
        $type = mb_strtolower(trim((string) $leave->leave_type));

        if (
            str_contains($type, 'krank') ||
            str_contains($type, 'sick')
        ) {
            return 'sick';
        }

        return 'holiday';
    }

    private function holidayLabel(Leave $leave): string
    {
        $type = mb_strtolower(trim((string) $leave->leave_type));

        if (
            str_contains($type, 'krank') ||
            str_contains($type, 'sick')
        ) {
            return 'Krank';
        }

        if (
            str_contains($type, 'urlaub') ||
            str_contains($type, 'holiday') ||
            str_contains($type, 'vacation') ||
            str_contains($type, 'annual')
        ) {
            return 'Urlaub';
        }

        if (
            str_contains($type, 'sonder') ||
            str_contains($type, 'special')
        ) {
            return 'Sonderurlaub';
        }

        if (
            str_contains($type, 'unbezahlt') ||
            str_contains($type, 'unpaid')
        ) {
            return 'Unbezahlter Urlaub';
        }

        if (
            str_contains($type, 'homeoffice') ||
            str_contains($type, 'home office') ||
            str_contains($type, 'home-office')
        ) {
            return 'Homeoffice';
        }

        return $leave->leave_type ?: 'Urlaub';
    }

    private function employeePayload(Employee $employee): array
    {
        $fullName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

        return [
            'id' => (int) $employee->id,
            'name' => $fullName ?: 'Unbekannt',
            'image' => $this->employeeImage($employee->image),
            'profile_url' => url('employee_profile/' . $employee->id),
        ];
    }

    private function employeeImage(?string $image): string
    {
        if (!$image) {
            return asset('images/default-avatar.png');
        }

        if (
            str_starts_with($image, 'http://') ||
            str_starts_with($image, 'https://')
        ) {
            return $image;
        }

        return asset('images/employee/' . ltrim($image, '/'));
    }

    private function formatDate($date): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }
}