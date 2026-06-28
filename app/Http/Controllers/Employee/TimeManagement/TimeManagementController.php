<?php
namespace App\Http\Controllers\Employee\TimeManagement;
use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\TimeManagementPlan;
use App\Models\TimeManagementEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;  
class TimeManagementController extends Controller
{
    public function index(Employee $employee, Request $request)
    {
        // FIX P0-11: nur eigener Plan, dessen Vorgesetzter (employees.supervisor) oder Admin.
        $me = (int) (auth()->user()->name ?? 0);
        abort_unless(
            auth()->user()->is_admin
                || ($me > 0 && $me === (int) $employee->id)
                || ($me > 0 && $me === (int) ($employee->supervisor ?? 0)),
            403,
            'Kein Zugriff auf diesen Zeitplan.'
        );

        return view('admin.employee.time_management.time', [
            'employee' => $employee,
        ]);
    }

    public function loadMonth(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year'        => 'required|integer|min:2000',
            'month'       => 'required|integer|between:1,12',
        ]);

        $employee = Employee::findOrFail($data['employee_id']);

        $plan = TimeManagementPlan::with('entries')
            ->where('employee_id', $employee->id)
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->first();

        return response()->json([
            'plan' => $plan ? [
                'id'              => $plan->id,
                'status'          => $plan->status,
                'target_hours'    => (float) $plan->target_hours,
                'scheduled_hours' => (float) $plan->scheduled_hours,
                'working_type'    => $plan->working_type,
                'hourly_rate'     => (float) $plan->hourly_rate,
                'comment'         => $plan->comment,
            ] : null,
            'entries' => $plan
                ? $plan->entries->map(function (TimeManagementEntry $e) {
                    return [
                        'date'          => $e->work_date->format('Y-m-d'),
                        'start_time'    => $e->start_time,
                        'end_time'      => $e->end_time,
                        'break_minutes' => $e->break_minutes,
                        'hours'         => (float) $e->hours,
                    ];
                })->values()
                : [],
            'employee' => [
                'id'             => $employee->id,
                'working_type'   => $employee->working_type,
                'salary_per_hour'=> $employee->salary_per_hour ? (float) $employee->salary_per_hour : 0,
                'working_hour'   => $employee->working_hour ? (float) $employee->working_hour : 0, // interpret as monthly hours
            ],
        ]);
    }

 public function save(Request $request)
{
    $data = $request->validate([
        'employee_id'          => 'required|exists:employees,id',
        'year'                 => 'required|integer|min:2000',
        'month'                => 'required|integer|between:1,12',
        'target_hours'         => 'nullable|numeric|min:0',
        'days'                 => 'array',
        'days.*.date'          => 'required|date',
        'days.*.start_time'    => 'required|date_format:H:i',
        'days.*.end_time'      => 'required|date_format:H:i',
        'days.*.break_minutes' => 'nullable|integer|min:0|max:600',
    ]);

    $employee = Employee::findOrFail($data['employee_id']);

    return DB::transaction(function () use ($data, $employee) {

        // ---------------------------------------------------------------------
        // 1) Load existing plan or create new
        // ---------------------------------------------------------------------
        $plan = TimeManagementPlan::firstOrNew([
            'employee_id' => $employee->id,
            'year'        => $data['year'],
            'month'       => $data['month'],
        ]);

        // ---------------------------------------------------------------------
        // 2) If plan already APPROVED and user edits it =>
        //    reopen as DRAFT, remove approval info
        // ---------------------------------------------------------------------
        if ($plan->exists && $plan->status === 'approved') {
            $plan->status      = 'draft';
            $plan->approved_by = null;
            $plan->approved_at = null;
        }

        if (! $plan->exists) {
            $plan->status = 'draft';
        }

        // ---------------------------------------------------------------------
        // 3) Set snapshot values (from employee at save time)
        // ---------------------------------------------------------------------
        $target = $data['target_hours'] ?? null;
        if ($target === null && $employee->working_hour) {
            $target = $employee->working_hour;
        }

        $plan->working_type = $employee->working_type;
        $plan->hourly_rate  = $employee->salary_per_hour ?? 0;
        $plan->target_hours = $target ?? 0;
        $plan->save();

        // ---------------------------------------------------------------------
        // 4) Rebuild entries for this month
        // ---------------------------------------------------------------------
        TimeManagementEntry::where('plan_id', $plan->id)->delete();

        $totalHours = 0;

        foreach ($data['days'] ?? [] as $day) {
            $start = Carbon::createFromFormat('H:i', $day['start_time']);
            $end   = Carbon::createFromFormat('H:i', $day['end_time']);
            $break = (int)($day['break_minutes'] ?? 0);

            if ($end->lessThanOrEqualTo($start)) {
                continue;
            }

            $diffMinutes = $end->diffInMinutes($start) - $break;
            if ($diffMinutes <= 0) {
                continue;
            }

            $hours = round($diffMinutes / 60, 2);
            $totalHours += $hours;

            TimeManagementEntry::create([
                'plan_id'       => $plan->id,
                'work_date'     => $day['date'],
                'start_time'    => $day['start_time'],
                'end_time'      => $day['end_time'],
                'break_minutes' => $break,
                'hours'         => $hours,
            ]);
        }

        $plan->scheduled_hours = $totalHours;
        $plan->save();

        return response()->json([
            'success'         => true,
            'plan_id'         => $plan->id,
            'status'          => $plan->status,           // <<< important
            'scheduled_hours' => $totalHours,
            'target_hours'    => (float) $plan->target_hours,
            'hourly_rate'     => (float) $plan->hourly_rate,
            'working_type'    => $plan->working_type,
        ]);
    });
}

    public function submit(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:time_management_plans,id',
        ]);

        $plan = TimeManagementPlan::with('employee')->findOrFail($data['plan_id']);

        // FIX P0-11: nur der eigene Zeitplan (oder Admin) darf eingereicht werden.
        $me = (int) (auth()->user()->name ?? 0);
        abort_unless(
            auth()->user()->is_admin || ($me > 0 && $me === (int) $plan->employee_id),
            403,
            'Nur der eigene Zeitplan darf eingereicht werden.'
        );

        if ($plan->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Der Zeitplan ist bereits genehmigt.',
            ], 422);
        }

        $plan->status  = 'pending';
        $plan->comment = null;
        $plan->save();

        return response()->json([
            'success' => true,
            'status'  => $plan->status,
        ]);
    }

    public function updateStatus(Request $request, TimeManagementPlan $plan)
    {
        $data = $request->validate([
            'status'  => 'required|in:approved,rejected',
            'comment' => 'nullable|string|max:2000',
        ]);

        // FIX P0-11: genehmigen/ablehnen nur Admin oder Vorgesetzter; kein Selbst-Genehmigen.
        $plan->loadMissing('employee');
        $me = (int) (auth()->user()->name ?? 0);
        $isAdmin = (bool) auth()->user()->is_admin;
        $isSupervisor = $me > 0 && $me === (int) ($plan->employee->supervisor ?? 0);
        abort_unless($isAdmin || $isSupervisor, 403, 'Nur Admin oder Vorgesetzter darf genehmigen/ablehnen.');
        abort_if(!$isAdmin && $me === (int) $plan->employee_id, 403, 'Der eigene Zeitplan darf nicht selbst genehmigt werden.');

        $plan->status      = $data['status'];
        $plan->comment     = $data['comment'] ?? null;
        $plan->approved_by = Auth::id();
        $plan->approved_at = now();
        $plan->save();

        return response()->json([
            'success' => true,
            'status'  => $plan->status,
        ]);
    }

    public function slotsIndex(Request $request)
    {
        // FIX P0-11: Gesamtuebersicht nur fuer Admin oder Vorgesetzte (mind. ein zugeordneter Mitarbeiter).
        $me = (int) (auth()->user()->name ?? 0);
        abort_unless(
            auth()->user()->is_admin
                || ($me > 0 && Employee::where('supervisor', $me)->exists()),
            403,
            'Kein Zugriff auf die Zeitplan-Gesamtuebersicht.'
        );

        // Expected format: YYYY-MM (from <input type="month">)
        $monthInput = $request->input('month');

        if ($monthInput && preg_match('/^\d{4}-\d{2}$/', $monthInput)) {
            [$year, $month] = explode('-', $monthInput);
            $year  = (int) $year;
            $month = (int) $month;
        } else {
            $now   = now();
            $year  = (int) $now->year;
            $month = (int) $now->month;
            $monthInput = $now->format('Y-m');
        }

        // All employees you want to see in the overview
        // add where() clauses if you want to limit to active / branch etc.
        $employees = Employee::orderBy('name')
            ->where('status', 'Active')
            ->orderBy('lastname')
            ->get();

        // All plans for this month, keyed by employee_id
        $plans = TimeManagementPlan::where('year', $year)
            ->where('month', $month)
            ->get()
            ->keyBy('employee_id');

        return view('admin.employee.time_management.time_slots', [
            'employees' => $employees,
            'plans'     => $plans,
            'year'      => $year,
            'month'     => $month,
        ]);
    }

    public function load(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year'        => 'required|integer|min:2000|max:2100',
            'month'       => 'required|integer|min:1|max:12',
        ]);

        $employee = Employee::findOrFail($data['employee_id']);

        $plan = TimeManagementPlan::with('entries')
            ->where('employee_id', $employee->id)
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->first();

        $entries = [];
        if ($plan) {
            $entries = $plan->entries
                ->sortBy('date')
                ->map(function (TimeManagementEntry $e) {
                    return [
                        'date'          => Carbon::parse($e->date)->format('Y-m-d'),
                        'start_time'    => $e->start_time ? substr($e->start_time, 0, 5) : null,
                        'end_time'      => $e->end_time ? substr($e->end_time, 0, 5) : null,
                        'break_minutes' => (int) $e->break_minutes,
                        'hours'         => (float) $e->hours,
                    ];
                })
                ->values()
                ->all();
        }

        return response()->json([
            'employee' => [
                'id'             => $employee->id,
                'name'           => $employee->name,
                'lastname'       => $employee->lastname,
                'working_hour'   => $employee->working_hour,
                'salary_per_hour'=> $employee->salary_per_hour,
                'working_type'   => $employee->working_type,
            ],
            'plan' => $plan ? [
                'id'              => $plan->id,
                'employee_id'     => $plan->employee_id,
                'year'            => $plan->year,
                'month'           => $plan->month,
                'target_hours'    => (float) $plan->target_hours,
                'hourly_rate'     => (float) $plan->hourly_rate,
                'working_type'    => $plan->working_type,
                'scheduled_hours' => (float) $plan->scheduled_hours,
                'status'          => $plan->status,
                'comment'         => $plan->comment,
                'updated_at'      => optional($plan->updated_at)->toDateTimeString(),
            ] : null,
            'entries' => $entries,
        ]);
    }
}
