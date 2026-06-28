<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MobileCalendarController extends Controller
{
    public function index()
    {
        $customers = DB::table('new_leads')
            ->select('id', 'name', 'lastname', 'firma', 'email', 'phone', 'street', 'city', 'postcode')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('admin.todo.personal.mobile', compact('customers'));
    }

    public function getEmployees()
    {
        $employees = DB::table('employees')
            ->select('id', 'name', 'lastname', 'image', 'gender', 'color')
            ->where('status', 'Active')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get()
            ->map(function ($emp) {
                return [
                    'id'     => (string) $emp->id,
                    'name'   => trim($emp->name . ' ' . $emp->lastname),
                    'avatar' => $emp->image ? asset('images/employee/' . $emp->image) : null,
                    'color'  => $emp->color ?? '#164194',
                ];
            });

        return response()->json($employees);
    }

    public function getEvents(Request $request)
    {
        $employeeIds = $request->input('employee_ids', []);
        $date        = $request->input('date');

        $base        = $date ? Carbon::parse($date) : Carbon::now();
        $startWindow = (clone $base)->startOfWeek()->subWeeks(2);
        $endWindow   = (clone $base)->endOfWeek()->addWeeks(4);

        if (in_array('all', $employeeIds) || empty($employeeIds)) {
            $targetEmpIds = DB::table('employees')->where('status', 'Active')->pluck('id')->toArray();
        } else {
            $targetEmpIds = array_values(array_filter($employeeIds, fn ($x) => $x !== 'all'));
        }

        $allEvents = collect();

        // ----------------------------
        // 1) Appointments
        // ----------------------------
        $appointments = DB::table('main_appointments as ma')
            ->leftJoin('new_leads as nl', 'nl.id', '=', 'ma.customer_id')
            ->select(
                'ma.id',
                'ma.name as title',

                // IMPORTANT: keep both so frontend can prefer note
                'ma.note as note',
                'ma.note as description',

                'ma.start_date', 'ma.end_date',
                'ma.start_time', 'ma.end_time',
                'ma.color',
                'ma.full_address', 'ma.street', 'ma.city',
                'ma.status', 'ma.public', 'ma.is_report',
                'ma.appointment_type',
                'ma.execution_type',
                'ma.created_by',
                'ma.phone as a_phone',
                'ma.email as a_email',

                'nl.email as c_email',
                'nl.phone as c_phone',
                'nl.name as c_name', 'nl.lastname as c_lastname', 'nl.firma as c_firma'
            )
            ->whereNull('ma.deleted_at')
            ->where(function ($q) use ($startWindow, $endWindow) {
                $q->whereBetween('ma.start_date', [$startWindow->toDateString(), $endWindow->toDateString()])
                  ->orWhereBetween('ma.end_date',   [$startWindow->toDateString(), $endWindow->toDateString()]);
            })
            ->where(function ($q) use ($targetEmpIds) {
                $q->whereIn('ma.created_by', $targetEmpIds)
                  ->orWhereIn('ma.report_by', $targetEmpIds)
                  ->orWhereExists(function ($sub) use ($targetEmpIds) {
                      $sub->select(DB::raw(1))
                          ->from('main_appointment_employees as mae')
                          ->whereColumn('mae.appointment_id', 'ma.id')
                          ->whereIn('mae.employee_id', $targetEmpIds);
                  });
            })
            ->get();

        $apptIds = $appointments->pluck('id')->toArray();
        $attendeesMap = [];

        if (!empty($apptIds)) {
            $attendeesRaw = DB::table('main_appointment_employees as mae')
                ->join('employees as e', 'e.id', '=', 'mae.employee_id')
                ->whereIn('mae.appointment_id', $apptIds)
                ->select('mae.appointment_id', 'e.name', 'e.lastname', 'e.image', 'e.id')
                ->get();

            foreach ($attendeesRaw as $att) {
                $attendeesMap[$att->appointment_id][] = [
                    'id'     => (string) $att->id,
                    'name'   => trim($att->name . ' ' . $att->lastname),
                    'avatar' => $att->image ? asset('images/employee/' . $att->image) : null,
                ];
            }
        }

        foreach ($appointments as $apt) {
            $custName = trim(($apt->c_firma ?? '') . ' ' . ($apt->c_name ?? '') . ' ' . ($apt->c_lastname ?? ''));

            $start = ($apt->start_date ?? Carbon::now()->toDateString()) . ' ' . ($apt->start_time ?? '00:00:00');
            $end   = ($apt->end_date ?? $apt->start_date ?? Carbon::now()->toDateString()) . ' ' . ($apt->end_time ?? '23:59:59');

            $allEvents->push([
                'id'               => (string) $apt->id,
                'type'             => 'appointment',

                'title'            => $apt->title ?? '',
                'note'             => $apt->note,           // <— MAIN
                'description'      => $apt->description,    // backward compatibility

                'start'            => $start,
                'end'              => $end,

                'address'          => $apt->full_address ?? trim(($apt->street ?? '') . ' ' . ($apt->city ?? '')),
                'color'            => $apt->color ?? '#164194',

                'ownerId'          => (string) $apt->created_by,

                'isPublic'         => ($apt->public == 'true' || $apt->public == 1),
                'needsReport'      => ($apt->is_report == 'true' || $apt->is_report == 1),

                'status'           => $apt->status ?? 'pending',
                'customerName'     => $custName ?: 'No Client',

                'attendees'        => $attendeesMap[$apt->id] ?? [],

                // NEW
                'appointment_type' => $apt->appointment_type,
                'execution_type'   => $apt->execution_type,

                'customerEmail'    => $apt->c_email ?? null,
                'customerPhone'    => $apt->c_phone ?? null,

                'email'            => $apt->a_email ?? ($apt->c_email ?? null),
                'phone'            => $apt->a_phone ?? ($apt->c_phone ?? null),
            ]);
        }

        // ----------------------------
        // 2) Personal Tasks (unchanged)
        // ----------------------------
        $tasks = DB::table('personal_tasks as pt')
            ->join('employees_personal_tasks as ept', 'ept.task_id', '=', 'pt.id')
            ->select('pt.id', 'pt.task_title', 'pt.description', 'pt.due_date', 'pt.due_time', 'pt.priority', 'ept.employee_id')
            ->whereNull('pt.deleted_at')
            ->whereBetween('pt.due_date', [$startWindow->toDateString(), $endWindow->toDateString()])
            ->whereIn('ept.employee_id', $targetEmpIds)
            ->get();

        foreach ($tasks as $task) {
            $allEvents->push([
                'id'          => 'task-' . $task->id,
                'type'        => 'task',
                'title'       => $task->task_title,
                'description' => $task->description,
                'start'       => $task->due_date . ' ' . ($task->due_time ?? '09:00:00'),
                'end'         => $task->due_date . ' ' . ($task->due_time ? Carbon::parse($task->due_time)->addHour()->format('H:i:s') : '10:00:00'),
                'color'       => '#93c21c',
                'ownerId'     => (string) $task->employee_id,
                'isPublic'    => false,
                'address'     => 'Task',
                'attendees'   => [],
                'status'      => 'pending',
            ]);
        }

        // leaves, sicks, public holidays (keep your existing code)
        // ...

        return response()->json($allEvents);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'appointment_type' => 'nullable|string|max:255',
            'execution_type'   => 'nullable|string|max:255',

            'start_date'       => 'required|date',
            'start_time'       => 'required',
            'end_time'         => 'required',
            'address'          => 'nullable|string|max:255',
            'customer_id'      => 'nullable|integer',

            'color'            => 'nullable|string|max:50',
            'public'           => 'nullable|boolean',
            'needs_report'     => 'nullable|boolean',
            'attendees'        => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // FIX: use employee id (NOT user name)
            $employeeId = (int) Auth::id();

            $apptId = DB::table('main_appointments')->insertGetId([
                'created_by'       => $employeeId,
                'name'             => $data['title'],
                'note'             => $data['description'] ?? null,
                'appointment_type' => $data['appointment_type'] ?? null,
                'execution_type'   => $data['execution_type'] ?? null,

                'full_address'     => $data['address'] ?? null,
                'start_date'       => Carbon::parse($data['start_date'])->toDateString(),
                'end_date'         => Carbon::parse($data['start_date'])->toDateString(),
                'start_time'       => $data['start_time'],
                'end_time'         => $data['end_time'],
                'customer_id'      => $data['customer_id'] ?? null,
                'color'            => $data['color'] ?? '#164194',
                'public'           => !empty($data['public']) ? 'true' : 'false',
                'is_report'        => !empty($data['needs_report']) ? 'true' : 'false',
                'status'           => 'pending',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $attendees = $data['attendees'] ?? [];

            // always include creator
            if (!in_array((string) $employeeId, array_map('strval', $attendees), true)) {
                $attendees[] = (string) $employeeId;
            }

            $pivotData = [];
            foreach ($attendees as $attId) {
                $attId = (string) $attId;
                if ($attId === 'all' || $attId === '') continue;

                $pivotData[] = [
                    'appointment_id' => $apptId,
                    'employee_id'    => (int) $attId,
                    'status'         => 'send',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }

            if (!empty($pivotData)) {
                DB::table('main_appointment_employees')->insert($pivotData);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Termin erstellt']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
