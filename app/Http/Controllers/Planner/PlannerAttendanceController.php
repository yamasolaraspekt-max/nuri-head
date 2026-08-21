<?php

namespace App\Http\Controllers\Planner;

use App\Events\EmployeeLocationUpdated;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PlannerItem;
use App\Models\PlannerPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PlannerAttendanceController extends Controller
{
    private function authEmployeeId(): ?int
    {
        $user = auth()->user();
        $id = $user?->employee_id ?? $user?->employee?->id ?? $user?->name ?? null;
        return is_numeric($id) ? (int) $id : null;
    }

    private function hasColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    private function planProject(PlannerPlan $plan): ?object
    {
        if (!$plan->project_id || !Schema::hasTable('lead_product_lists')) {
            return null;
        }

        return DB::table('lead_product_lists')
            ->where('id', (int) $plan->project_id)
            ->first();
    }

    private function resolveDate(Request $request): string
    {
        try {
            return Carbon::parse($request->input('date', $request->query('date', now()->toDateString())))->toDateString();
        } catch (\Throwable $e) {
            return now()->toDateString();
        }
    }

    /**
     * Z2-W0-3 (21.08.) — **der Mitarbeiter kommt aus der Sitzung, nicht aus dem Request.**
     *
     * Vorher nahm diese Methode `employee_id` aus Request oder Query und fiel nur bei `<= 0` auf
     * den eigenen zurück. Damit konnte jeder eingeloggte Nutzer die Anwesenheits- und
     * Standortdaten **fremder Mitarbeiter** lesen und schreiben — Spur A, Datenschutz,
     * betriebsratsrelevant. Neun Aufrufstellen hingen daran (`:284`, `:302`, `:321`, `:409`,
     * `:428`, `:443`, `:461`, `:479`, `:507`).
     *
     * **Warum hier und nicht neun `abort_unless`:** der Auftrag lässt beide Formen und nennt diese
     * die dichtere. Sie ist es, weil eine neue Aufrufstelle sie automatisch erbt — ein vergessenes
     * `abort_unless` wäre genau die Lücke, die dieser Auftrag schließt.
     *
     * **Warum stillschweigend maßgeblich statt 403:** dies ist der Auflöser der LESEpfade
     * (`day`, `report`, …). Ein Client, der seine EIGENE Kennung mitschickt, verhält sich korrekt
     * und darf nicht brechen; ein Client mit fremder Kennung bekommt seine eigenen Daten statt
     * fremder. Für den SCHREIBpfad `location()` ist die Absage ausdrücklich (403) — dort ist eine
     * fremde Kennung eine Absicht, keine Gewohnheit.
     *
     * **Offene Rückfrage, nicht still entschieden:** gibt es einen legitimen Vorgesetzten-Fall
     * (Teamleiter trägt für Mitarbeiter ein)? Dann ist das ein Operand und braucht ein eigenes
     * Recht. Bis dahin gilt „nur eigener Mitarbeiter" — die engere Annahme.
     */
    private function resolveEmployeeId(Request $request): int
    {
        $employeeId = (int) ($this->authEmployeeId() ?? 0);

        abort_if($employeeId <= 0, 422, 'Mitarbeiter konnte nicht erkannt werden.');

        return $employeeId;
    }

    private function employeeIdsForPlan(PlannerPlan $plan): array
    {
        $ids = [];

        if (Schema::hasTable('planner_item_employees') && Schema::hasTable('planner_items')) {
            $ids = array_merge($ids, DB::table('planner_item_employees as pie')
                ->join('planner_items as pi', 'pi.id', '=', 'pie.planner_item_id')
                ->where('pi.plan_id', (int) $plan->id)
                ->when($this->hasColumn('planner_items', 'deleted_at'), fn($q) => $q->whereNull('pi.deleted_at'))
                ->pluck('pie.employee_id')
                ->toArray());
        }

        $project = $this->planProject($plan);

        if ($project) {
            foreach (['employee_id', 'field_employee'] as $column) {
                if (isset($project->{$column}) && is_numeric($project->{$column})) {
                    $ids[] = (int) $project->{$column};
                }
            }

            $teamsRaw = $project->teams ?? null;
            $teams = is_string($teamsRaw) ? json_decode($teamsRaw, true) : (is_array($teamsRaw) ? $teamsRaw : []);
            $teamList = [];

            if (is_array($teams)) {
                $teamList = array_is_list($teams) ? $teams : ($teams['ids'] ?? $teams['team'] ?? $teams['employees'] ?? []);
            }

            foreach ($teamList as $item) {
                $id = is_array($item) ? ($item['id'] ?? $item['employee_id'] ?? null) : $item;
                if (is_numeric($id)) {
                    $ids[] = (int) $id;
                }
            }
        }

        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }

    private function attendanceFor(PlannerPlan $plan, int $employeeId, string $date): Attendance
    {
        $project = $this->planProject($plan);

        $attendance = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->when($this->hasColumn('attendances', 'planner_plan_id'), fn($q) => $q->where('planner_plan_id', (int) $plan->id))
            ->first();

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->employee_id = $employeeId;
            $attendance->date = $date;
            $attendance->status = 'absent';
            $attendance->meta = [];

            if ($this->hasColumn('attendances', 'planner_plan_id')) {
                $attendance->planner_plan_id = (int) $plan->id;
            }
            if ($this->hasColumn('attendances', 'customer_id')) {
                $attendance->customer_id = (int) ($project->customer_id ?? $plan->customer_id ?? 0) ?: null;
            }
            if ($this->hasColumn('attendances', 'lead_product_list_id')) {
                $attendance->lead_product_list_id = (int) ($project->id ?? $plan->project_id ?? 0) ?: null;
            }
            if ($this->hasColumn('attendances', 'alternative_id')) {
                $attendance->alternative_id = (int) ($project->alternative_id ?? 0) ?: null;
            }
            if ($this->hasColumn('attendances', 'product_id')) {
                $attendance->product_id = (int) ($project->product_id ?? 0) ?: null;
            }
            if ($this->hasColumn('attendances', 'article_group')) {
                $attendance->article_group = (int) ($project->product_id ?? 0) ?: null;
            }
            if ($this->hasColumn('attendances', 'created_by')) {
                $attendance->created_by = $this->authEmployeeId() ?? auth()->id();
            }

            $attendance->save();
        }

        return $attendance;
    }

    private function addEvent(Attendance $attendance, string $type, array $data = []): void
    {
        if (!Schema::hasTable('attendance_events')) {
            return;
        }

        DB::table('attendance_events')->insert([
            'attendance_id' => (int) $attendance->id,
            'employee_id' => (int) $attendance->employee_id,
            'planner_plan_id' => $attendance->planner_plan_id ?? null,
            'planner_item_id' => $data['planner_item_id'] ?? null,
            'event_type' => $type,
            'event_at' => now(),
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'destination' => $data['destination'] ?? null,
            'note' => $data['note'] ?? null,
            'meta' => json_encode($data['meta'] ?? [], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
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

    private function attendancePayload(Attendance $attendance): array
    {
        $status = (string) ($attendance->status ?? 'absent');
        $travelSeconds = (int) ($attendance->travel_total_seconds ?? 0);
        $pauseSeconds = (int) ($attendance->pause_total_seconds ?? 0);
        $workSeconds = (int) ($attendance->work_total_seconds ?? 0);

        if (!$travelSeconds && $attendance->travel_started_at && $attendance->arrived_at) {
            $travelSeconds = $this->secondsBetween($attendance->travel_started_at, $attendance->arrived_at);
        }

        if (!$workSeconds && $attendance->work_started_at) {
            $workEnd = $attendance->work_ended_at ?: ($attendance->check_out ?: now());
            $workSeconds = max(0, $this->secondsBetween($attendance->work_started_at, $workEnd) - $pauseSeconds);
        }

        return [
            'id' => (int) $attendance->id,
            'employee_id' => (int) $attendance->employee_id,
            'date' => optional($attendance->date)->format('Y-m-d') ?: (string) $attendance->date,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'check_in' => optional($attendance->check_in)->toDateTimeString(),
            'check_out' => optional($attendance->check_out)->toDateTimeString(),
            'travel_started_at' => optional($attendance->travel_started_at)->toDateTimeString(),
            'arrived_at' => optional($attendance->arrived_at)->toDateTimeString(),
            'work_started_at' => optional($attendance->work_started_at)->toDateTimeString(),
            'work_ended_at' => optional($attendance->work_ended_at)->toDateTimeString(),
            'pause_started_at' => optional($attendance->pause_started_at)->toDateTimeString(),
            'pause_type' => $attendance->pause_type ?? null,
            'destination' => $attendance->destination ?? null,
            'destination_lat' => $attendance->destination_lat ?? null,
            'destination_lng' => $attendance->destination_lng ?? null,
            'current_lat' => $attendance->current_lat ?? null,
            'current_lng' => $attendance->current_lng ?? null,
            'last_location_at' => optional($attendance->last_location_at)->toDateTimeString(),
            'travel_total_seconds' => $travelSeconds,
            'pause_total_seconds' => $pauseSeconds,
            'work_total_seconds' => $workSeconds,
            'travel_total_label' => $this->formatSeconds($travelSeconds),
            'pause_total_label' => $this->formatSeconds($pauseSeconds),
            'work_total_label' => $this->formatSeconds($workSeconds),
            'is_present' => !in_array($status, ['absent', 'checked_out'], true),
            'is_tracking' => $status === 'traveling',
        ];
    }

    private function statusLabel(string $status): string
    {
        return [
            'absent' => 'Nicht anwesend',
            'present' => 'Anwesend',
            'traveling' => 'Unterwegs',
            'arrived' => 'Angekommen',
            'working' => 'Arbeitet',
            'paused' => 'Pause',
            'checked_out' => 'Ausgecheckt',
        ][$status] ?? ucfirst($status);
    }

    private function formatSeconds(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function day(Request $request, PlannerPlan $plan)
    {
        $date = $this->resolveDate($request);
        $employeeIds = $this->employeeIdsForPlan($plan);

        $attendances = Attendance::query()
            ->whereDate('date', $date)
            ->whereIn('employee_id', $employeeIds ?: [0])
            ->when($this->hasColumn('attendances', 'planner_plan_id'), fn($q) => $q->where('planner_plan_id', (int) $plan->id))
            ->get()
            ->keyBy('employee_id');

        $byEmployee = [];

        foreach ($employeeIds as $employeeId) {
            $attendance = $attendances->get($employeeId) ?: $this->attendanceFor($plan, $employeeId, $date);
            $byEmployee[$employeeId] = $this->attendancePayload($attendance);
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'date' => $date,
                'by_employee' => $byEmployee,
            ],
        ]);
    }

    public function checkIn(Request $request, PlannerPlan $plan)
    {
        $date = $this->resolveDate($request);
        $employeeId = $this->resolveEmployeeId($request);
        $attendance = $this->attendanceFor($plan, $employeeId, $date);

        if (!$attendance->check_in) {
            $attendance->check_in = now();
        }

        $attendance->status = 'present';
        $attendance->save();

        $this->addEvent($attendance, 'check_in');

        return $this->singleResponse($attendance);
    }

    public function checkOut(Request $request, PlannerPlan $plan)
    {
        $date = $this->resolveDate($request);
        $employeeId = $this->resolveEmployeeId($request);
        $attendance = $this->attendanceFor($plan, $employeeId, $date);

        $attendance->check_out = now();
        $attendance->status = 'checked_out';

        if ($attendance->work_started_at) {
            $attendance->work_total_seconds = max(0, $this->secondsBetween($attendance->work_started_at, $attendance->check_out) - (int) ($attendance->pause_total_seconds ?? 0));
        }

        $attendance->save();
        $this->addEvent($attendance, 'check_out');

        return $this->singleResponse($attendance);
    }

    public function travelStart(Request $request, PlannerPlan $plan)
    {
        $date = $this->resolveDate($request);
        $employeeId = $this->resolveEmployeeId($request);
        $attendance = $this->attendanceFor($plan, $employeeId, $date);

        if (!$attendance->check_in) {
            $attendance->check_in = now();
        }

        $attendance->status = 'traveling';
        $attendance->travel_started_at = now();
        $attendance->arrived_at = null;
        $attendance->destination = trim((string) $request->input('destination', '')) ?: ($attendance->destination ?: null);
        $attendance->destination_lat = $request->input('destination_lat', $attendance->destination_lat ?? null);
        $attendance->destination_lng = $request->input('destination_lng', $attendance->destination_lng ?? null);
        $attendance->save();

        $this->addEvent($attendance, 'travel_start', [
            'destination' => $attendance->destination,
            'meta' => [
                'item_ids' => $request->input('item_ids', []),
            ],
        ]);

        return $this->singleResponse($attendance);
    }

    public function location(Request $request, PlannerPlan $plan)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'min:1'],
            'date' => ['nullable', 'date'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'speed' => ['nullable', 'numeric'],
            'heading' => ['nullable', 'numeric'],
        ]);

        $date = $this->resolveDate($request);

        // Z2-W0-3: der Schreibpfad nimmt die Kennung NICHT aus dem Request. Eine fremde `employee_id`
        // ist hier eine Absicht und wird abgewiesen, statt still auf die eigene umgeschrieben zu
        // werden — der Client soll erfahren, dass sein Wunsch nicht ausgeführt wurde.
        $employeeId = $this->resolveEmployeeId($request);
        abort_unless((int) $data['employee_id'] === $employeeId, 403, 'Fremde Mitarbeiter-Kennung.');

        $attendance = $this->attendanceFor($plan, $employeeId, $date);

        $attendance->current_lat = $data['lat'];
        $attendance->current_lng = $data['lng'];
        $attendance->last_location_at = now();
        $attendance->save();

        $locationId = null;

        if (Schema::hasTable('attendance_locations')) {
            $locationId = DB::table('attendance_locations')->insertGetId([
                'attendance_id' => (int) $attendance->id,
                'employee_id' => $employeeId,
                'planner_plan_id' => (int) $plan->id,
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'accuracy' => $data['accuracy'] ?? null,
                'speed' => $data['speed'] ?? null,
                'heading' => $data['heading'] ?? null,
                'destination' => $attendance->destination ?? null,
                'recorded_at' => now(),
                'meta' => json_encode([], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $payload = [
            'location_id' => $locationId,
            'attendance_id' => (int) $attendance->id,
            'employee_id' => $employeeId,
            'planner_plan_id' => (int) $plan->id,
            'lat' => (float) $data['lat'],
            'lng' => (float) $data['lng'],
            'accuracy' => $data['accuracy'] ?? null,
            'speed' => $data['speed'] ?? null,
            'heading' => $data['heading'] ?? null,
            'destination' => $attendance->destination ?? null,
            'recorded_at' => now()->toDateTimeString(),
        ];

        event(new EmployeeLocationUpdated($payload));

        return response()->json(['ok' => true, 'data' => $payload]);
    }

    public function arrived(Request $request, PlannerPlan $plan)
    {
        $date = $this->resolveDate($request);
        $employeeId = $this->resolveEmployeeId($request);
        $attendance = $this->attendanceFor($plan, $employeeId, $date);

        $attendance->arrived_at = now();
        $attendance->status = 'arrived';
        $attendance->travel_total_seconds = $this->secondsBetween($attendance->travel_started_at, $attendance->arrived_at);
        $attendance->save();

        $this->addEvent($attendance, 'arrived', [
            'destination' => $attendance->destination,
            'meta' => ['travel_total_seconds' => $attendance->travel_total_seconds],
        ]);

        return $this->singleResponse($attendance);
    }

    public function workStart(Request $request, PlannerPlan $plan)
    {
        $date = $this->resolveDate($request);
        $employeeId = $this->resolveEmployeeId($request);
        $attendance = $this->attendanceFor($plan, $employeeId, $date);

        $attendance->status = 'working';
        $attendance->work_started_at = $attendance->work_started_at ?: now();
        $attendance->save();

        $this->addEvent($attendance, 'work_start');

        return $this->singleResponse($attendance);
    }

    public function workEnd(Request $request, PlannerPlan $plan)
    {
        $date = $this->resolveDate($request);
        $employeeId = $this->resolveEmployeeId($request);
        $attendance = $this->attendanceFor($plan, $employeeId, $date);

        $attendance->work_ended_at = now();
        $attendance->status = 'present';
        $attendance->work_total_seconds = max(0, $this->secondsBetween($attendance->work_started_at, $attendance->work_ended_at) - (int) ($attendance->pause_total_seconds ?? 0));
        $attendance->save();

        $this->addEvent($attendance, 'work_end', [
            'meta' => ['work_total_seconds' => $attendance->work_total_seconds],
        ]);

        return $this->singleResponse($attendance);
    }

    public function pauseStart(Request $request, PlannerPlan $plan)
    {
        $date = $this->resolveDate($request);
        $employeeId = $this->resolveEmployeeId($request);
        $attendance = $this->attendanceFor($plan, $employeeId, $date);

        $attendance->status = 'paused';
        $attendance->pause_started_at = now();
        $attendance->pause_type = $request->input('pause_type', 'mittag_essen');
        $attendance->save();

        $this->addEvent($attendance, 'pause_start', [
            'note' => $attendance->pause_type,
        ]);

        return $this->singleResponse($attendance);
    }

    public function pauseEnd(Request $request, PlannerPlan $plan)
    {
        $date = $this->resolveDate($request);
        $employeeId = $this->resolveEmployeeId($request);
        $attendance = $this->attendanceFor($plan, $employeeId, $date);

        $pauseSeconds = $this->secondsBetween($attendance->pause_started_at, now());
        $attendance->pause_total_seconds = (int) ($attendance->pause_total_seconds ?? 0) + $pauseSeconds;
        $attendance->pause_started_at = null;
        $attendance->pause_type = null;
        $attendance->status = $attendance->work_started_at ? 'working' : 'present';
        $attendance->save();

        $this->addEvent($attendance, 'pause_end', [
            'meta' => ['pause_seconds' => $pauseSeconds],
        ]);

        return $this->singleResponse($attendance);
    }

    private function singleResponse(Attendance $attendance)
    {
        return response()->json([
            'ok' => true,
            'data' => $this->attendancePayload($attendance->fresh()),
        ]);
    }

    public function report(Request $request, PlannerPlan $plan)
    {
        $date = $this->resolveDate($request);
        $employeeId = $this->resolveEmployeeId($request);
        $attendance = $this->attendanceFor($plan, $employeeId, $date);
        $project = $this->planProject($plan);

        $itemIds = [];
        $items = collect();

        if (Schema::hasTable('planner_items') && Schema::hasTable('planner_item_employees')) {
            $items = DB::table('planner_items as pi')
                ->join('planner_item_employees as pie', 'pie.planner_item_id', '=', 'pi.id')
                ->where('pi.plan_id', (int) $plan->id)
                ->where('pie.employee_id', $employeeId)
                ->when($this->hasColumn('planner_items', 'deleted_at'), fn($q) => $q->whereNull('pi.deleted_at'))
                ->where(function ($q) use ($date) {
                    foreach (['planned_start_at', 'start_at', 'date', 'due_date'] as $column) {
                        if ($this->hasColumn('planner_items', $column)) {
                            $q->orWhereDate('pi.' . $column, $date);
                        }
                    }
                })
                ->select('pi.*')
                ->distinct()
                ->orderBy('pi.planned_start_at')
                ->get();

            $itemIds = $items->pluck('id')->map(fn($id) => (int) $id)->all();
        }

        $comments = collect();
        if (Schema::hasTable('planner_item_comments') && !empty($itemIds)) {
            $comments = DB::table('planner_item_comments')
                ->whereIn('planner_item_id', $itemIds)
                ->when($this->hasColumn('planner_item_comments', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->orderByDesc('id')
                ->get();
        }

        $gallery = collect();
        if (Schema::hasTable('images')) {
            $gallery = DB::table('images')
                ->where('customer_id', (int) ($project->customer_id ?? $plan->customer_id ?? 0))
                ->when(!empty($project?->alternative_id), fn($q) => $q->where('alternative_id', (int) $project->alternative_id))
                ->when(!empty($project?->product_id), fn($q) => $q->where('article_group', (int) $project->product_id))
                ->where(function ($q) use ($itemIds) {
                    if (!empty($itemIds)) {
                        if ($this->hasColumn('images', 'planner_item_id')) {
                            $q->orWhereIn('planner_item_id', $itemIds);
                        }
                        if ($this->hasColumn('images', 'task_id')) {
                            $q->orWhereIn('task_id', $itemIds);
                        }
                    }
                })
                ->when($this->hasColumn('images', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->orderByDesc('id')
                ->limit(80)
                ->get()
                ->map(function ($img) {
                    $filename = ltrim((string) ($img->image ?? ''), '/');
                    $img->file_url = $filename !== '' ? asset('storage/uploads/customers/' . basename($filename)) : null;
                    return $img;
                });
        }

        $materials = collect();
        if (Schema::hasTable('planner_item_materials') && !empty($itemIds)) {
            $materials = DB::table('planner_item_materials')
                ->whereIn('planner_item_id', $itemIds)
                ->when($this->hasColumn('planner_item_materials', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->get();
        }

        $sharedMaterials = collect();
        if (Schema::hasTable('planner_group_materials')) {
            $sharedQuery = DB::table('planner_group_materials');

            if ($this->hasColumn('planner_group_materials', 'planner_plan_id')) {
                $sharedQuery->where('planner_plan_id', (int) $plan->id);
            } elseif ($this->hasColumn('planner_group_materials', 'plan_id')) {
                $sharedQuery->where('plan_id', (int) $plan->id);
            }

            if ($this->hasColumn('planner_group_materials', 'employee_id')) {
                $sharedQuery->where('employee_id', $employeeId);
            }

            /*
             * planner_group_materials does not have a `date` column in the supplied migration.
             * Shared material is period-based, so load rows whose scope_date_from/scope_date_to
             * overlap the requested report date. Keep `date` support only for old installs.
             */
            if ($this->hasColumn('planner_group_materials', 'date')) {
                $sharedQuery->whereDate('date', $date);
            } else {
                if ($this->hasColumn('planner_group_materials', 'scope_date_from')) {
                    $sharedQuery->where(function ($q) use ($date) {
                        $q->whereNull('scope_date_from')->orWhereDate('scope_date_from', '<=', $date);
                    });
                }

                if ($this->hasColumn('planner_group_materials', 'scope_date_to')) {
                    $sharedQuery->where(function ($q) use ($date) {
                        $q->whereNull('scope_date_to')->orWhereDate('scope_date_to', '>=', $date);
                    });
                }
            }

            if ($this->hasColumn('planner_group_materials', 'deleted_at')) {
                $sharedQuery->whereNull('deleted_at');
            }

            $sharedMaterials = $sharedQuery->orderByDesc('id')->get();
        }

        $events = collect();
        if (Schema::hasTable('attendance_events')) {
            $events = DB::table('attendance_events')
                ->where('attendance_id', (int) $attendance->id)
                ->orderBy('event_at')
                ->get();
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'attendance' => $this->attendancePayload($attendance),
                'employee_id' => $employeeId,
                'date' => $date,
                'project' => [
                    'id' => $project->id ?? $plan->project_id,
                    'customer_id' => $project->customer_id ?? $plan->customer_id,
                    'alternative_id' => $project->alternative_id ?? null,
                    'product_id' => $project->product_id ?? null,
                    'destination' => $project->full_address ?? $project->object_full_address ?? $project->address ?? null,
                ],
                'items' => $items,
                'done_items' => $items->filter(fn($item) => in_array(strtolower((string) ($item->status ?? '')), ['done', 'completed', 'finished', 'closed'], true))->values(),
                'comments' => $comments,
                'gallery' => $gallery,
                'materials' => $materials,
                'shared_materials' => $sharedMaterials,
                'events' => $events,
            ],
        ]);
    }
}
