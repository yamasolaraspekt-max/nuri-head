<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\MainAppointment;
use App\Models\MainAppointmentEmployee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TicketAppointmentController extends Controller
{
    public function employees(Request $request): JsonResponse
    {
        $search = trim((string) $request->get('q', ''));

        $employees = Employee::query()
            ->select('id', 'name', 'lastname', 'image', 'daily_start_time', 'daily_end_time', 'status_msg')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($x) use ($search) {
                    $x->where('name', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(COALESCE(name,''),' ',COALESCE(lastname,'')) LIKE ?", ["%{$search}%"]);
                });
            })
            ->orderBy('name')
            ->limit(30)
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'text' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    'image' => $employee->image
                        ? asset('images/employee/' . $employee->image)
                        : asset('images/gender/male.png'),
                    'daily_start_time' => $employee->daily_start_time,
                    'daily_end_time' => $employee->daily_end_time,
                    'status_msg' => $employee->status_msg,
                ];
            });

        return response()->json([
            'results' => $employees,
        ]);
    }

    public function index(Request $request, int $problem): JsonResponse
    {
        $appointments = MainAppointment::with(['employees:id,name,lastname,image', 'createdBy:id,name,lastname', 'changedBy:id,name,lastname'])
            ->where('problem_id', $problem)
            ->orderByDesc('start_date')
            ->orderByDesc('start_time')
            ->get()
            ->map(fn (MainAppointment $appointment) => $this->formatAppointment($appointment));

        return response()->json([
            'success' => true,
            'appointments' => $appointments,
        ]);
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $data = $this->validateAppointmentPayload($request, false);

        $appointmentId = $request->integer('appointment_id') ?: null;
        $employeeIds = collect($data['employee_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values();

        if ($employeeIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'has_conflicts' => false,
                'conflicts' => [],
                'message' => 'Keine Mitarbeiter ausgewählt.',
            ]);
        }

        $conflicts = $this->findEmployeeConflicts(
            $employeeIds->all(),
            $data['start_date'],
            $data['end_date'],
            $data['start_time'],
            $data['end_time'],
            $appointmentId
        );

        return response()->json([
            'success' => true,
            'has_conflicts' => count($conflicts) > 0,
            'conflicts' => $conflicts,
        ]);
    }

    public function store(Request $request, int $problem): JsonResponse
    {
        $data = $this->validateAppointmentPayload($request, false);

        $forceSave = $request->boolean('force_save');
        $employeeIds = collect($data['employee_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values();

        $conflicts = $this->findEmployeeConflicts(
            $employeeIds->all(),
            $data['start_date'],
            $data['end_date'],
            $data['start_time'],
            $data['end_time'],
            null
        );

        if (!$forceSave && count($conflicts) > 0) {
            return response()->json([
                'success' => false,
                'requires_force' => true,
                'message' => 'Es gibt Terminkonflikte. Bitte Zeitraum ändern oder trotzdem speichern.',
                'conflicts' => $conflicts,
            ], 409);
        }

        $currentEmployeeId = (int) auth()->user()->name;

        $appointment = DB::transaction(function () use ($data, $problem, $employeeIds, $currentEmployeeId, $forceSave) {
            $appointment = MainAppointment::create($this->buildAppointmentData(
                data: $data,
                problemId: $problem,
                currentEmployeeId: $currentEmployeeId,
                forceSave: $forceSave,
                existing: null
            ));

            $this->syncEmployees($appointment, $employeeIds->all());

            return $appointment->fresh(['employees:id,name,lastname,image', 'createdBy:id,name,lastname', 'changedBy:id,name,lastname']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Termin wurde gespeichert.',
            'appointment' => $this->formatAppointment($appointment),
        ]);
    }

    public function update(Request $request, int $problem, MainAppointment $appointment): JsonResponse
    {
        abort_unless((int) $appointment->problem_id === (int) $problem, 404);

        $data = $this->validateAppointmentPayload($request, true);

        $forceSave = $request->boolean('force_save');
        $employeeIds = collect($data['employee_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values();

        $conflicts = $this->findEmployeeConflicts(
            $employeeIds->all(),
            $data['start_date'],
            $data['end_date'],
            $data['start_time'],
            $data['end_time'],
            $appointment->id
        );

        if (!$forceSave && count($conflicts) > 0) {
            return response()->json([
                'success' => false,
                'requires_force' => true,
                'message' => 'Es gibt Terminkonflikte. Bitte Zeitraum ändern oder trotzdem speichern.',
                'conflicts' => $conflicts,
            ], 409);
        }

        $currentEmployeeId = (int) auth()->user()->name;

        $appointment = DB::transaction(function () use ($appointment, $data, $problem, $employeeIds, $currentEmployeeId, $forceSave) {
            $appointment->update($this->buildAppointmentData(
                data: $data,
                problemId: $problem,
                currentEmployeeId: $currentEmployeeId,
                forceSave: $forceSave,
                existing: $appointment
            ));

            $this->syncEmployees($appointment, $employeeIds->all());

            return $appointment->fresh(['employees:id,name,lastname,image', 'createdBy:id,name,lastname', 'changedBy:id,name,lastname']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Termin wurde aktualisiert.',
            'appointment' => $this->formatAppointment($appointment),
        ]);
    }

    public function destroy(int $problem, MainAppointment $appointment): JsonResponse
    {
        abort_unless((int) $appointment->problem_id === (int) $problem, 404);

        DB::transaction(function () use ($appointment) {
            MainAppointmentEmployee::where('appointment_id', $appointment->id)->delete();
            $appointment->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Termin wurde gelöscht.',
        ]);
    }

    private function validateAppointmentPayload(Request $request, bool $updating): array
    {
        return $request->validate([
            'appointment_id' => ['nullable', 'integer', 'exists:main_appointments,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'status' => ['nullable', 'string', 'max:50'],
            'priority' => ['nullable', 'string', 'max:50'],
            'customer_id' => ['nullable', 'integer', 'exists:new_leads,id'],
            'product_id' => ['nullable', 'integer', 'exists:article_groups,id'],
            'full_address' => ['nullable', 'string', 'max:500'],
            'street' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'phone' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:255'],

            /*
            |--------------------------------------------------------------------------
            | Travel values
            |--------------------------------------------------------------------------
            | Do NOT save these into main_appointments.total_time.
            | total_time often is integer/time in existing systems, so text like
            | "7 mins · 5.3 km" causes SQL warning 1265.
            */
            'travel_duration_text' => ['nullable', 'string', 'max:100'],
            'travel_distance_text' => ['nullable', 'string', 'max:100'],
            'travel_summary' => ['nullable', 'string', 'max:255'],

            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'force_save' => ['nullable', 'boolean'],
        ]);
    }

    private function buildAppointmentData(array $data, int $problemId, int $currentEmployeeId, bool $forceSave, ?MainAppointment $existing): array
    {
        $startDate = Carbon::parse($data['start_date'])->format('Y-m-d');
        $endDate = Carbon::parse($data['end_date'] ?? $data['start_date'])->format('Y-m-d');

        $totalMinutes = $this->calculateAppointmentMinutes(
            $startDate,
            $endDate,
            $data['start_time'],
            $data['end_time']
        );

        $travelLine = trim((string) ($data['travel_summary'] ?? ''));
        if ($travelLine === '') {
            $duration = trim((string) ($data['travel_duration_text'] ?? ''));
            $distance = trim((string) ($data['travel_distance_text'] ?? ''));
            $travelLine = trim($duration . ($duration && $distance ? ' · ' : '') . $distance);
        }

        $note = trim((string) ($data['note'] ?? ''));
        if ($travelLine !== '') {
            $note .= ($note !== '' ? "\n\n" : '') . 'Anfahrt: ' . $travelLine;
        }

        return [
            'created_by' => $existing?->created_by ?: $currentEmployeeId,
            'changed_by' => $currentEmployeeId,
            'name' => $data['name'] ?: ('Termin für Ticket #' . $problemId),
            'note' => $note,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'status' => $data['status'] ?? 'planned',
            'priority' => $data['priority'] ?? 'normal',
            'appointment_type' => 'ticket',
            'type' => 'ticket',
            'source' => 'ticket',
            'problem_id' => $problemId,
            'customer_id' => $data['customer_id'] ?? $existing?->customer_id,
            'products' => !empty($data['product_id']) ? [(int) $data['product_id']] : ($existing?->products ?? []),
            'full_address' => $data['full_address'] ?? null,
            'street' => $data['street'] ?? null,
            'postcode' => $data['postcode'] ?? null,
            'city' => $data['city'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | FIX
            |--------------------------------------------------------------------------
            | total_time gets ONLY appointment duration minutes.
            | It must never get route text like "7 mins · 5.3 km".
            */
            'total_time' => $totalMinutes,

            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'change_date' => now(),
            'change_reason' => $forceSave
                ? 'Saved from ticket profile with forced conflict override'
                : 'Saved from ticket profile',
        ];
    }

    private function calculateAppointmentMinutes(string $startDate, string $endDate, string $startTime, string $endTime): int
    {
        $start = Carbon::parse($startDate . ' ' . $startTime);
        $end = Carbon::parse($endDate . ' ' . $endTime);

        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        return $start->diffInMinutes($end);
    }

    private function syncEmployees(MainAppointment $appointment, array $employeeIds): void
    {
        $employeeIds = collect($employeeIds)->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();

        MainAppointmentEmployee::where('appointment_id', $appointment->id)
            ->whereNotIn('employee_id', $employeeIds ?: [0])
            ->delete();

        foreach ($employeeIds as $employeeId) {
            MainAppointmentEmployee::updateOrCreate(
                [
                    'appointment_id' => $appointment->id,
                    'employee_id' => $employeeId,
                ],
                [
                    'status' => 'assigned',
                    'reason' => null,
                ]
            );
        }
    }

    private function findEmployeeConflicts(array $employeeIds, string $startDate, ?string $endDate, string $startTime, string $endTime, ?int $ignoreAppointmentId): array
    {
        if (empty($employeeIds)) {
            return [];
        }

        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate = Carbon::parse($endDate ?: $startDate)->format('Y-m-d');

        $employees = Employee::query()
            ->select('id', 'name', 'lastname', 'image', 'daily_start_time', 'daily_end_time')
            ->whereIn('id', $employeeIds)
            ->get()
            ->keyBy('id');

        $conflicts = [];

        foreach ($employeeIds as $employeeId) {
            $employee = $employees->get($employeeId);

            if (!$employee) {
                continue;
            }

            $employeeName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

            if ($employee->daily_start_time && $employee->daily_end_time) {
                $workStart = substr((string) $employee->daily_start_time, 0, 5);
                $workEnd = substr((string) $employee->daily_end_time, 0, 5);

                if ($startTime < $workStart || $endTime > $workEnd) {
                    $conflicts[] = [
                        'employee_id' => $employeeId,
                        'employee_name' => $employeeName,
                        'type' => 'working_time',
                        'message' => "{$employeeName} arbeitet normalerweise {$workStart} - {$workEnd}.",
                        'appointment_id' => null,
                    ];
                }
            }

            $appointmentConflicts = MainAppointment::query()
                ->join('main_appointment_employees as mae', 'mae.appointment_id', '=', 'main_appointments.id')
                ->where('mae.employee_id', $employeeId)
                ->whereNull('main_appointments.deleted_at')
                ->when($ignoreAppointmentId, fn ($q) => $q->where('main_appointments.id', '!=', $ignoreAppointmentId))
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('main_appointments.start_date', [$startDate, $endDate])
                        ->orWhereBetween('main_appointments.end_date', [$startDate, $endDate])
                        ->orWhere(function ($x) use ($startDate, $endDate) {
                            $x->where('main_appointments.start_date', '<=', $startDate)
                                ->where('main_appointments.end_date', '>=', $endDate);
                        });
                })
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->where('main_appointments.start_time', '<', $endTime)
                        ->where('main_appointments.end_time', '>', $startTime);
                })
                ->select(
                    'main_appointments.id',
                    'main_appointments.name',
                    'main_appointments.start_date',
                    'main_appointments.end_date',
                    'main_appointments.start_time',
                    'main_appointments.end_time',
                    'main_appointments.status'
                )
                ->get();

            foreach ($appointmentConflicts as $conflict) {
                $conflicts[] = [
                    'employee_id' => $employeeId,
                    'employee_name' => $employeeName,
                    'type' => 'appointment',
                    'appointment_id' => $conflict->id,
                    'message' => "{$employeeName} hat bereits einen Termin: {$conflict->name} ({$conflict->start_time} - {$conflict->end_time}).",
                    'appointment' => [
                        'id' => $conflict->id,
                        'name' => $conflict->name,
                        'start_date' => optional(Carbon::parse($conflict->start_date))->format('d.m.Y'),
                        'start_time' => substr((string) $conflict->start_time, 0, 5),
                        'end_time' => substr((string) $conflict->end_time, 0, 5),
                        'status' => $conflict->status,
                    ],
                ];
            }
        }

        return $conflicts;
    }

    private function formatAppointment(MainAppointment $appointment): array
    {
        return [
            'id' => $appointment->id,
            'name' => $appointment->name,
            'note' => $appointment->note,
            'start_date' => optional($appointment->start_date)->format('Y-m-d'),
            'end_date' => optional($appointment->end_date)->format('Y-m-d'),
            'start_time' => $appointment->start_time ? substr((string) $appointment->start_time, 0, 5) : null,
            'end_time' => $appointment->end_time ? substr((string) $appointment->end_time, 0, 5) : null,
            'status' => $appointment->status,
            'priority' => $appointment->priority,
            'customer_id' => $appointment->customer_id,
            'products' => $appointment->products,
            'full_address' => $appointment->full_address,
            'street' => $appointment->street,
            'postcode' => $appointment->postcode,
            'city' => $appointment->city,
            'latitude' => $appointment->latitude,
            'longitude' => $appointment->longitude,
            'total_time' => $appointment->total_time,
            'phone' => $appointment->phone,
            'email' => $appointment->email,
            'employees' => $appointment->employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    'image' => $employee->image
                        ? asset('images/employee/' . $employee->image)
                        : asset('images/gender/male.png'),
                ];
            })->values(),
            'created_by' => $appointment->createdBy
                ? trim(($appointment->createdBy->name ?? '') . ' ' . ($appointment->createdBy->lastname ?? ''))
                : null,
            'changed_by' => $appointment->changedBy
                ? trim(($appointment->changedBy->name ?? '') . ' ' . ($appointment->changedBy->lastname ?? ''))
                : null,
            'created_at_human' => optional($appointment->created_at)->diffForHumans(),
        ];
    }
}
