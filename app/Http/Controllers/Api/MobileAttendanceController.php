<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\PlannerItem;
use App\Models\PlannerPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MobileAttendanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | POST /api/mobile/attendance/log
    | POST /api/mobile/attendance/action
    |--------------------------------------------------------------------------
    | Main endpoint used by Nuriva mobile.
    |
    | Supported action values:
    | check_in, check_out, travel_start, location, arrived,
    | work_start, work_end, pause_start, pause_end,
    | lunch_start, lunch_end, mittag_start, mittag_end,
    | task_completed.
    |--------------------------------------------------------------------------
    */
    public function log(Request $request)
    {
        return $this->action($request);
    }

    public function action(Request $request)
    {
        $employeeId = $this->resolveEmployeeId($request);

        if (!$employeeId) {
            return response()->json([
                'ok' => false,
                'message' => 'Mitarbeiter konnte nicht erkannt werden.',
                'debug' => [
                    'auth_model' => $request->user() ? get_class($request->user()) : null,
                    'auth_id' => $request->user()?->id,
                    'auth_name' => $request->user()?->name ?? null,
                    'request_employee_id' => $request->input('employee_id'),
                ],
            ], 403);
        }

        $data = $request->validate([
            'action' => ['required', 'string', 'max:80'],
            'date' => ['nullable', 'date'],

            'plan_id' => ['nullable', 'integer', 'min:1'],
            'planner_plan_id' => ['nullable', 'integer', 'min:1'],
            'planner_item_id' => ['nullable', 'integer', 'min:1'],
            'remote_planner_item_id' => ['nullable', 'integer', 'min:1'],
            'item_id' => ['nullable', 'integer', 'min:1'],

            'local_task_id' => ['nullable'],
            'source_uid' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:120'],
            'source_type' => ['nullable', 'string', 'max:120'],

            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'latitude' => ['nullable', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'speed' => ['nullable', 'numeric'],
            'heading' => ['nullable', 'numeric'],

            'destination' => ['nullable', 'string', 'max:1000'],
            'destination_lat' => ['nullable', 'numeric'],
            'destination_lng' => ['nullable', 'numeric'],
            'pause_type' => ['nullable', 'string', 'max:80'],
            'note' => ['nullable', 'string', 'max:5000'],

            'meta' => ['nullable', 'array'],
        ]);

        $action = $this->normalizeAction($data['action']);
        $date = $this->resolveDate($request);
        $plannerItemId = $this->resolvePlannerItemId($data);
        $plan = $this->resolvePlan($data, $plannerItemId);

        try {
            $attendance = DB::transaction(function () use ($request, $data, $action, $employeeId, $date, $plan, $plannerItemId) {
                $attendance = $this->attendanceFor($plan, $employeeId, $date, $data);

                $lat = $this->numOrNull($data['lat'] ?? $data['latitude'] ?? null);
                $lng = $this->numOrNull($data['lng'] ?? $data['longitude'] ?? null);

                switch ($action) {
                    case 'check_in':
                        $this->setIfColumn($attendance, 'check_in', $attendance->check_in ?: now());
                        $this->setIfColumn($attendance, 'status', 'present');
                        break;

                    case 'check_out':
                        $this->setIfColumn($attendance, 'check_out', now());
                        $this->setIfColumn($attendance, 'status', 'checked_out');

                        if ($this->hasColumn('attendances', 'work_total_seconds') && !empty($attendance->work_started_at)) {
                            $pauseSeconds = (int) ($attendance->pause_total_seconds ?? 0);
                            $attendance->work_total_seconds = max(0, $this->secondsBetween($attendance->work_started_at, $attendance->check_out) - $pauseSeconds);
                        }
                        break;

                    case 'travel_start':
                        $this->setIfColumn($attendance, 'check_in', $attendance->check_in ?: now());
                        $this->setIfColumn($attendance, 'status', 'traveling');
                        $this->setIfColumn($attendance, 'travel_started_at', now());
                        $this->setIfColumn($attendance, 'arrived_at', null);
                        $this->setIfColumn($attendance, 'destination', $data['destination'] ?? $attendance->destination ?? null);
                        $this->setIfColumn($attendance, 'destination_lat', $this->numOrNull($data['destination_lat'] ?? null));
                        $this->setIfColumn($attendance, 'destination_lng', $this->numOrNull($data['destination_lng'] ?? null));
                        $this->applyLocationToAttendance($attendance, $lat, $lng);
                        break;

                    case 'location':
                        $this->applyLocationToAttendance($attendance, $lat, $lng);
                        break;

                    case 'arrived':
                        $this->setIfColumn($attendance, 'arrived_at', now());
                        $this->setIfColumn($attendance, 'status', 'arrived');

                        if ($this->hasColumn('attendances', 'travel_total_seconds')) {
                            $attendance->travel_total_seconds = $this->secondsBetween($attendance->travel_started_at ?? null, $attendance->arrived_at ?? now());
                        }

                        $this->applyLocationToAttendance($attendance, $lat, $lng);
                        break;

                    case 'work_start':
                        $this->setIfColumn($attendance, 'check_in', $attendance->check_in ?: now());
                        $this->setIfColumn($attendance, 'status', 'working');
                        $this->setIfColumn($attendance, 'work_started_at', $attendance->work_started_at ?: now());
                        break;

                    case 'work_end':
                        $this->setIfColumn($attendance, 'work_ended_at', now());
                        $this->setIfColumn($attendance, 'status', 'present');

                        if ($this->hasColumn('attendances', 'work_total_seconds') && !empty($attendance->work_started_at)) {
                            $pauseSeconds = (int) ($attendance->pause_total_seconds ?? 0);
                            $attendance->work_total_seconds = max(0, $this->secondsBetween($attendance->work_started_at, $attendance->work_ended_at) - $pauseSeconds);
                        }
                        break;

                    case 'pause_start':
                        $this->setIfColumn($attendance, 'status', 'paused');
                        $this->setIfColumn($attendance, 'pause_started_at', now());
                        $this->setIfColumn($attendance, 'pause_type', $data['pause_type'] ?? 'pause');
                        break;

                    case 'pause_end':
                        $pauseSeconds = $this->secondsBetween($attendance->pause_started_at ?? null, now());

                        if ($this->hasColumn('attendances', 'pause_total_seconds')) {
                            $attendance->pause_total_seconds = (int) ($attendance->pause_total_seconds ?? 0) + $pauseSeconds;
                        }

                        $this->setIfColumn($attendance, 'pause_started_at', null);
                        $this->setIfColumn($attendance, 'pause_type', null);
                        $this->setIfColumn($attendance, 'status', !empty($attendance->work_started_at) ? 'working' : 'present');
                        break;

                    case 'task_completed':
                        $this->setIfColumn($attendance, 'status', 'present');
                        $this->markPlannerItemDone($plannerItemId, $employeeId);
                        break;
                }

                $attendance->save();

                if (in_array($action, ['travel_start', 'location', 'arrived'], true) && $lat !== null && $lng !== null) {
                    $this->storeLocation($attendance, $plan, $employeeId, $lat, $lng, $data);
                }

                $this->addEvent($attendance, $action, [
                    'planner_item_id' => $plannerItemId,
                    'lat' => $lat,
                    'lng' => $lng,
                    'destination' => $data['destination'] ?? $attendance->destination ?? null,
                    'note' => $data['note'] ?? ($data['pause_type'] ?? null),
                    'meta' => array_merge($data['meta'] ?? [], [
                        'source' => $data['source'] ?? 'nuriva',
                        'source_type' => $data['source_type'] ?? null,
                        'source_uid' => $data['source_uid'] ?? null,
                        'local_task_id' => $data['local_task_id'] ?? null,
                        'action' => $action,
                        'accuracy' => $data['accuracy'] ?? null,
                        'speed' => $data['speed'] ?? null,
                        'heading' => $data['heading'] ?? null,
                    ]),
                ]);

                $payload = $this->attendanceEventPayload($attendance->fresh(), $action, $plan, $plannerItemId, [
                    'lat' => $lat,
                    'lng' => $lng,
                    'accuracy' => $data['accuracy'] ?? null,
                    'speed' => $data['speed'] ?? null,
                    'heading' => $data['heading'] ?? null,
                    'destination' => $data['destination'] ?? $attendance->destination ?? null,
                    'source_uid' => $data['source_uid'] ?? null,
                    'local_task_id' => $data['local_task_id'] ?? null,
                ]);

                $this->broadcastAttendance($payload);

                return $attendance->fresh();
            });

            return response()->json([
                'ok' => true,
                'message' => $this->actionLabel($action),
                'data' => $this->attendancePayload($attendance),
            ]);
        } catch (\Throwable $e) {
            Log::error('Mobile attendance action failed', [
                'employee_id' => $employeeId,
                'action' => $action,
                'data' => $data,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Attendance update failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'line' => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/mobile/attendance/location
    |--------------------------------------------------------------------------
    */
    public function location(Request $request)
    {
        $request->merge(['action' => 'location']);

        return $this->action($request);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/mobile/attendance/sync
    |--------------------------------------------------------------------------
    | Accepts either one event or an array:
    | { events: [ {...}, {...} ] }
    |--------------------------------------------------------------------------
    */
    public function sync(Request $request)
    {
        $events = $request->input('events');

        if (!is_array($events)) {
            return $this->action($request);
        }

        $results = [];

        foreach ($events as $event) {
            if (!is_array($event)) {
                continue;
            }

            $child = Request::create($request->path(), 'POST', $event);
            $child->headers->replace($request->headers->all());
            $child->setUserResolver(fn() => $request->user());

            $response = $this->action($child);
            $results[] = $response->getData(true);
        }

        return response()->json([
            'ok' => true,
            'data' => $results,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/mobile/attendance/history
    |--------------------------------------------------------------------------
    */
    public function history(Request $request)
    {
        $employeeId = $this->resolveEmployeeId($request);

        if (!$employeeId) {
            return response()->json([
                'ok' => false,
                'message' => 'Mitarbeiter konnte nicht erkannt werden.',
            ], 403);
        }

        $date = $this->resolveDate($request);
        $from = Carbon::parse($request->input('from', $date))->startOfDay();
        $to = Carbon::parse($request->input('to', $date))->endOfDay();

        $records = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('date')
            ->limit(100)
            ->get()
            ->map(fn(Attendance $attendance) => $this->attendancePayload($attendance))
            ->values();

        $events = collect();

        if (Schema::hasTable('attendance_events')) {
            $events = DB::table('attendance_events')
                ->where('employee_id', $employeeId)
                ->whereBetween('created_at', [$from->toDateTimeString(), $to->toDateTimeString()])
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(300)
                ->get();
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'employee_id' => $employeeId,
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'records' => $records,
                'events' => $events,
            ],
        ]);
    }

    private function resolveEmployeeId(Request $request): ?int
    {
        $auth = $request->user();

        if ($auth instanceof Employee) {
            return (int) $auth->id;
        }

        if ($auth instanceof User) {
            if (!empty($auth->name) && is_numeric($auth->name)) {
                return (int) $auth->name;
            }

            if (!empty($auth->employee_id) && is_numeric($auth->employee_id)) {
                return (int) $auth->employee_id;
            }
        }

        if ($auth) {
            if (!empty($auth->name) && is_numeric($auth->name)) {
                return (int) $auth->name;
            }

            if (!empty($auth->employee_id) && is_numeric($auth->employee_id)) {
                return (int) $auth->employee_id;
            }
        }

        $requestEmployeeId = $request->input('employee_id');

        return is_numeric($requestEmployeeId) ? (int) $requestEmployeeId : null;
    }

    private function resolveDate(Request $request): string
    {
        try {
            return Carbon::parse($request->input('date', now()->toDateString()))->toDateString();
        } catch (\Throwable $e) {
            return now()->toDateString();
        }
    }

    private function resolvePlannerItemId(array $data): ?int
    {
        $id = $data['planner_item_id']
            ?? $data['remote_planner_item_id']
            ?? $data['item_id']
            ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    private function resolvePlan(array $data, ?int $plannerItemId = null): ?PlannerPlan
    {
        $planId = $data['planner_plan_id'] ?? $data['plan_id'] ?? null;

        if (!$planId && $plannerItemId && Schema::hasTable('planner_items')) {
            $planId = DB::table('planner_items')
                ->where('id', $plannerItemId)
                ->value('plan_id');
        }

        if (!$planId || !is_numeric($planId)) {
            return null;
        }

        return PlannerPlan::query()
            ->where('id', (int) $planId)
            ->first();
    }

    private function attendanceFor(?PlannerPlan $plan, int $employeeId, string $date, array $data = []): Attendance
    {
        $query = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date);

        if ($this->hasColumn('attendances', 'planner_plan_id')) {
            if ($plan) {
                $query->where('planner_plan_id', (int) $plan->id);
            } else {
                $query->whereNull('planner_plan_id');
            }
        }

        $attendance = $query->first();

        if ($attendance) {
            return $attendance;
        }

        $attendance = new Attendance();
        $attendance->employee_id = $employeeId;
        $attendance->date = $date;
        $attendance->status = 'absent';

        if ($plan) {
            $project = $this->planProject($plan);

            $this->setIfColumn($attendance, 'planner_plan_id', (int) $plan->id);
            $this->setIfColumn($attendance, 'customer_id', (int) ($project->customer_id ?? $plan->customer_id ?? 0) ?: null);
            $this->setIfColumn($attendance, 'lead_product_list_id', (int) ($project->id ?? $plan->project_id ?? 0) ?: null);
            $this->setIfColumn($attendance, 'alternative_id', (int) ($project->alternative_id ?? 0) ?: null);
            $this->setIfColumn($attendance, 'product_id', (int) ($project->product_id ?? 0) ?: null);
            $this->setIfColumn($attendance, 'article_group', (int) ($project->product_id ?? 0) ?: null);
        }

        if (!empty($data['local_task_id'])) {
            $this->setIfColumn($attendance, 'meta', [
                'local_task_id' => $data['local_task_id'],
                'source_uid' => $data['source_uid'] ?? null,
            ]);
        }

        $this->setIfColumn($attendance, 'created_by', $employeeId);

        $attendance->save();

        return $attendance;
    }

    private function planProject(PlannerPlan $plan): ?object
    {
        if (!$plan->project_id || !Schema::hasTable('lead_product_lists')) {
            return null;
        }

        return DB::table('lead_product_lists')
            ->when($this->hasColumn('lead_product_lists', 'deleted_at'), fn($query) => $query->whereNull('deleted_at'))
            ->where('id', (int) $plan->project_id)
            ->first();
    }

    private function markPlannerItemDone(?int $plannerItemId, int $employeeId): void
    {
        if (!$plannerItemId || !Schema::hasTable('planner_items')) {
            return;
        }

        $updates = [];

        if ($this->hasColumn('planner_items', 'status')) {
            $updates['status'] = 'done';
        }

        if ($this->hasColumn('planner_items', 'done_at')) {
            $updates['done_at'] = now();
        }

        if ($this->hasColumn('planner_items', 'done_by_employee_id')) {
            $updates['done_by_employee_id'] = $employeeId;
        }

        if ($this->hasColumn('planner_items', 'updated_at')) {
            $updates['updated_at'] = now();
        }

        if (!empty($updates)) {
            DB::table('planner_items')
                ->where('id', $plannerItemId)
                ->update($updates);
        }
    }

    private function applyLocationToAttendance(Attendance $attendance, ?float $lat, ?float $lng): void
    {
        if ($lat === null || $lng === null) {
            return;
        }

        $this->setIfColumn($attendance, 'current_lat', $lat);
        $this->setIfColumn($attendance, 'current_lng', $lng);
        $this->setIfColumn($attendance, 'last_location_at', now());
    }

    private function storeLocation(Attendance $attendance, ?PlannerPlan $plan, int $employeeId, float $lat, float $lng, array $data): ?int
    {
        if (!Schema::hasTable('attendance_locations')) {
            return null;
        }

        $row = [
            'attendance_id' => (int) $attendance->id,
            'employee_id' => $employeeId,
            'lat' => $lat,
            'lng' => $lng,
            'accuracy' => $data['accuracy'] ?? null,
            'speed' => $data['speed'] ?? null,
            'heading' => $data['heading'] ?? null,
            'destination' => $data['destination'] ?? $attendance->destination ?? null,
            'recorded_at' => now(),
            'meta' => json_encode([
                'source' => $data['source'] ?? 'nuriva',
                'source_uid' => $data['source_uid'] ?? null,
                'local_task_id' => $data['local_task_id'] ?? null,
            ], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($this->hasColumn('attendance_locations', 'planner_plan_id') && $plan) {
            $row['planner_plan_id'] = (int) $plan->id;
        }

        return DB::table('attendance_locations')->insertGetId($this->filterColumns('attendance_locations', $row));
    }

    private function addEvent(Attendance $attendance, string $type, array $data = []): void
    {
        if (!Schema::hasTable('attendance_events')) {
            return;
        }

        $row = [
            'attendance_id' => (int) $attendance->id,
            'employee_id' => (int) $attendance->employee_id,
            'planner_plan_id' => $attendance->planner_plan_id ?? null,
            'planner_item_id' => $data['planner_item_id'] ?? null,
            'event_type' => $type,
            'type' => $type,
            'event_at' => now(),
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'destination' => $data['destination'] ?? null,
            'note' => $data['note'] ?? null,
            'meta' => json_encode($data['meta'] ?? [], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('attendance_events')->insert($this->filterColumns('attendance_events', $row));
    }

    private function broadcastAttendance(array $payload): void
    {
        $channels = [
            'mobile.attendance',
            'planner.employee.' . (int) $payload['employee_id'],
        ];

        if (!empty($payload['planner_plan_id'])) {
            $channels[] = 'planner.plan.' . (int) $payload['planner_plan_id'];
        }

        if (class_exists(\App\Events\PlannerRealtimeEvent::class)) {
            event(new \App\Events\PlannerRealtimeEvent($channels, 'attendance.updated', $payload));
        }

        if (
            in_array($payload['action'], ['travel_start', 'location', 'arrived'], true)
            && !empty($payload['lat'])
            && !empty($payload['lng'])
            && class_exists(\App\Events\EmployeeLocationUpdated::class)
        ) {
            event(new \App\Events\EmployeeLocationUpdated($payload));
        }
    }

    private function attendanceEventPayload(Attendance $attendance, string $action, ?PlannerPlan $plan, ?int $plannerItemId, array $extra = []): array
    {
        return array_merge([
            'action' => $action,
            'attendance' => $this->attendancePayload($attendance),
            'attendance_id' => (int) $attendance->id,
            'employee_id' => (int) $attendance->employee_id,
            'planner_plan_id' => $attendance->planner_plan_id ?? ($plan?->id),
            'planner_item_id' => $plannerItemId,
            'status' => $attendance->status ?? null,
            'recorded_at' => now()->toDateTimeString(),
        ], $extra);
    }

    private function attendancePayload(Attendance $attendance): array
    {
        $travelSeconds = (int) ($attendance->travel_total_seconds ?? 0);
        $pauseSeconds = (int) ($attendance->pause_total_seconds ?? 0);
        $workSeconds = (int) ($attendance->work_total_seconds ?? 0);

        if (!$travelSeconds && !empty($attendance->travel_started_at) && !empty($attendance->arrived_at)) {
            $travelSeconds = $this->secondsBetween($attendance->travel_started_at, $attendance->arrived_at);
        }

        if (!$workSeconds && !empty($attendance->work_started_at)) {
            $end = $attendance->work_ended_at ?: ($attendance->check_out ?: now());
            $workSeconds = max(0, $this->secondsBetween($attendance->work_started_at, $end) - $pauseSeconds);
        }

        return [
            'id' => (int) $attendance->id,
            'employee_id' => (int) $attendance->employee_id,
            'date' => $attendance->date ? Carbon::parse($attendance->date)->toDateString() : null,
            'status' => $attendance->status ?? null,
            'status_label' => $this->statusLabel($attendance->status ?? null),

            'check_in' => $this->dateTimeString($attendance->check_in ?? null),
            'check_out' => $this->dateTimeString($attendance->check_out ?? null),
            'travel_started_at' => $this->dateTimeString($attendance->travel_started_at ?? null),
            'arrived_at' => $this->dateTimeString($attendance->arrived_at ?? null),
            'work_started_at' => $this->dateTimeString($attendance->work_started_at ?? null),
            'work_ended_at' => $this->dateTimeString($attendance->work_ended_at ?? null),
            'pause_started_at' => $this->dateTimeString($attendance->pause_started_at ?? null),

            'pause_type' => $attendance->pause_type ?? null,
            'destination' => $attendance->destination ?? null,
            'destination_lat' => $this->numOrNull($attendance->destination_lat ?? null),
            'destination_lng' => $this->numOrNull($attendance->destination_lng ?? null),
            'current_lat' => $this->numOrNull($attendance->current_lat ?? null),
            'current_lng' => $this->numOrNull($attendance->current_lng ?? null),
            'last_location_at' => $this->dateTimeString($attendance->last_location_at ?? null),

            'planner_plan_id' => $attendance->planner_plan_id ?? null,
            'travel_total_seconds' => $travelSeconds,
            'pause_total_seconds' => $pauseSeconds,
            'work_total_seconds' => $workSeconds,
            'travel_total_label' => $this->formatSeconds($travelSeconds),
            'pause_total_label' => $this->formatSeconds($pauseSeconds),
            'work_total_label' => $this->formatSeconds($workSeconds),
        ];
    }

    private function normalizeAction(string $action): string
    {
        $action = strtolower(trim($action));
        $action = str_replace(['-', ' '], '_', $action);

        return match ($action) {
            'start_travel', 'travel', 'drive_start', 'fahrt_start', 'fahrt_starten' => 'travel_start',
            'stop_travel', 'travel_stop', 'drive_stop', 'arrive', 'ankommen', 'angekommen' => 'arrived',
            'start_work', 'arbeit_start', 'arbeit_starten' => 'work_start',
            'stop_work', 'end_work', 'arbeit_ende', 'arbeit_beenden' => 'work_end',
            'mittag_start', 'lunch_start', 'break_start' => 'pause_start',
            'mittag_end', 'lunch_end', 'break_end' => 'pause_end',
            'complete', 'completed', 'done', 'task_done' => 'task_completed',
            default => $action,
        };
    }

    private function actionLabel(string $action): string
    {
        return match ($action) {
            'check_in' => 'Eingecheckt.',
            'check_out' => 'Ausgecheckt.',
            'travel_start' => 'Fahrt gestartet.',
            'location' => 'Standort aktualisiert.',
            'arrived' => 'Ankunft gespeichert.',
            'work_start' => 'Arbeit gestartet.',
            'work_end' => 'Arbeit beendet.',
            'pause_start' => 'Pause gestartet.',
            'pause_end' => 'Pause beendet.',
            'task_completed' => 'Aufgabe abgeschlossen.',
            default => 'Aktion gespeichert.',
        };
    }

    private function statusLabel(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'present' => 'Anwesend',
            'traveling' => 'Unterwegs',
            'arrived' => 'Angekommen',
            'working' => 'In Arbeit',
            'paused' => 'Pause',
            'checked_out' => 'Ausgecheckt',
            'absent' => 'Abwesend',
            default => $status ?: 'Unbekannt',
        };
    }

    private function secondsBetween($start, $end): int
    {
        if (!$start || !$end) {
            return 0;
        }

        try {
            return max(0, Carbon::parse($start)->diffInSeconds(Carbon::parse($end), false));
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function formatSeconds(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    private function dateTimeString($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    private function numOrNull($value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function hasColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    private function setIfColumn($model, string $column, $value): void
    {
        if ($this->hasColumn($model->getTable(), $column)) {
            $model->{$column} = $value;
        }
    }

    private function filterColumns(string $table, array $row): array
    {
        return collect($row)
            ->filter(fn($value, $column) => Schema::hasColumn($table, $column))
            ->all();
    }
}
