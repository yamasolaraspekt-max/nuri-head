<?php

namespace App\Http\Controllers;
use App\Models\DailyReport;
use DB;
use App\Models\DailyReportTime;
use Carbon\Carbon;
use App\Models\LeadEmail;


use Illuminate\Http\Request;

class WebsiteController extends Controller
{

    public function createQR(){
        return view('admin.daily_report.qr.qr');
    }
     public function readQR($type)
    {
        
        return view('admin.daily_report.qr.qr_check')->with('type', $type);
    }


 
 public function checkQR(Request $request)
{
    $lastname = $request->lastname;
    $code = $request->code;
    $type = $request->type;
    $lat = (float) $request->lat;
    $lon = (float) $request->lon;
    $car_number = $request->car_number ?? null;
    $ip = $request->ip();

    // ✅ Check if user exists
    $employee = DB::table('employees')
        ->where('lastname', $lastname)
        ->where('code', $code)
        ->first();

    if (!$employee) {
        return response()->json(['error' => 'Lastname or Code is incorrect.']);
    }

    $employeeId = $employee->id;

    // ✅ Check if already checked in today
    $existingReport = DailyReport::where('employee_id', $employeeId)
        ->where('start_date', now()->toDateString())
        ->whereNotIn('status',['paused', 'ended'])
        ->first();

    if ($existingReport) {
        return response()->json([
            'error' => "You are already checked in at " . $existingReport->created_at->format('H:i d.m.Y'),
            'lat' => (float) $existingReport->lat,
            'lon' => (float) $existingReport->lon,
            'map' => true,
            'employee_id' => $employeeId,
            'type'  =>  $type

        ]);
    }

    // ✅ Determine workplace
    $tolerance = 0.0005;

    if ($type === 'Office') {
        // Check for nearby branch
        $workplace = DB::table('daily_report_work_places')
            ->where('type', 'branch')
            ->get()
            ->first(function ($place) use ($lat, $lon, $tolerance) {
                return abs($place->lat - $lat) <= $tolerance && abs($place->lon - $lon) <= $tolerance;
            });

        if (!$workplace) {
            return response()->json(['error' => 'You are not at the registered office location.']);
        }
    } else {
        // For Car/Customer/etc: find any nearby location (no type filter)
        $workplace = DB::table('daily_report_work_places')
            ->get()
            ->first(function ($place) use ($lat, $lon, $tolerance) {
                return abs($place->lat - $lat) <= $tolerance && abs($place->lon - $lon) <= $tolerance;
            });

        if (!$workplace) {
            return response()->json(['error' => 'No matching workplace found at your location.']);
        }
    }

    $workPlaceId = $workplace->id;

    // ✅ Create daily_report
    $report = DailyReport::create([
        'employee_id' => $employeeId,
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
        'lat' => $lat,
        'lon' => $lon,
        'ip' => $ip,
        'status' => 'started'
    ]);

    // ✅ Create daily_report_times
    DailyReportTime::create([
        'daily_report_id' => $report->id,
        'work_place_id' => $workPlaceId,
        'start_time' => now()->format('H:i:s'), 
        'status' => 'started',
        'lat' => $lat,
        'lon' => $lon,
        'ip' => $ip,
        'work_status' => $type,
        'address' => null
    ]);

    return response()->json(['success' => 'Check-in successful!']);
}


public function getPlan($employee_id)
{
    $today = Carbon::today();
    $allEvents = collect();

    // PERSONAL TASKS (today only)
    $tasks = DB::table('employees_personal_tasks')
        ->leftJoin('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
        ->leftJoin('employees as emp', 'emp.id', '=', 'employees_personal_tasks.employee_id')
        ->select(
            'personal_tasks.id',
            'personal_tasks.task_title as title',
            'personal_tasks.description',
            'personal_tasks.due_date as start_date',
            'personal_tasks.due_date as end_date',
            'personal_tasks.due_time as start_time',
            'personal_tasks.due_time as end_time',
            'personal_tasks.task_status as status',
            'employees_personal_tasks.id as emp_personal_id',
            'emp.id as employee_id',
            'emp.name',
            'emp.lastname',
            'emp.image',
            'emp.gender',
            'emp.daily_start_time',
            'emp.daily_end_time',
            'emp.working_hour',
            DB::raw("'task' as type"),
            DB::raw('NULL as full_address'),
            DB::raw('NULL as latitude'),
            DB::raw('NULL as longitude'), 

        )
        ->whereNull('personal_tasks.deleted_at')
        ->whereDate('personal_tasks.due_date', $today)
        ->where('employees_personal_tasks.employee_id', $employee_id)
        ->get();

    $allEvents = $allEvents->merge($tasks);

    // APPOINTMENTS (today only)
    $appointments = DB::table('main_appointment_employees')
        ->leftJoin('main_appointments', 'main_appointments.id', '=', 'main_appointment_employees.appointment_id')
        ->leftJoin('employees as emp', 'emp.id', '=', 'main_appointment_employees.employee_id')
        ->select(
            'main_appointments.id',
            'main_appointments.start_date',
            'main_appointments.end_date',
            'main_appointments.full_address', 
            'main_appointments.latitude',
            'main_appointments.longitude',
            'main_appointments.status',
            DB::raw("COALESCE(main_appointments.start_time, '00:00:00') as start_time"),
            DB::raw("COALESCE(main_appointments.end_time, '23:59:59') as end_time"),
            'emp.id as employee_id',
            'emp.name',
            'emp.lastname',
            'emp.image',
            'emp.gender',
            'emp.daily_start_time',
            'emp.daily_end_time',
            'emp.working_hour',
            DB::raw('NULL as emp_personal_id'),
            DB::raw("'appointment' as type")
        )
        ->whereNull('main_appointments.deleted_at')
        ->whereDate('main_appointments.start_date', $today)
        ->where('main_appointment_employees.employee_id', $employee_id)
        ->get();

    $allEvents = $allEvents->merge($appointments);

    // If no events, return empty
    if ($allEvents->isEmpty()) {
        return response()->json([
            'employee_id' => $employee_id,
            'worked_minutes' => 0,
            'expected_minutes' => 0,
            'remaining_minutes' => 0,
            'progress' => 0,
            'events' => []
        ]);
    }

    // Use the first event to get employee info
    $employee = $allEvents->first();

    // Calculate expected working time
    $dailyStart = $employee->daily_start_time ?? '07:30:00';
    $dailyEnd = $employee->daily_end_time ?? '16:00:00';
    $expectedMinutes = Carbon::parse($dailyEnd)->diffInMinutes(Carbon::parse($dailyStart));

    // Calculate actual worked minutes
    $workedMinutes = $allEvents->sum(function ($e) {
        $start = Carbon::parse("{$e->start_date} {$e->start_time}");
        $end = Carbon::parse("{$e->end_date} {$e->end_time}");
        if ($start->equalTo($end)) {
            $end = $start->copy()->addHour();
        }
        return $end->diffInMinutes($start);
    });

    return response()->json([
        'employee_id' => $employee->employee_id,
        'name' => $employee->name,
        'lastname' => $employee->lastname,
        'image' => $employee->image,
        'gender' => $employee->gender,
        'worked_minutes' => $workedMinutes,
        'expected_minutes' => $expectedMinutes,
        'remaining_minutes' => max($expectedMinutes - $workedMinutes, 0),
        'progress' => round(($workedMinutes / $expectedMinutes) * 100, 1),
        'events' => $allEvents->values(),
    ]);
}

public function getPlanForm($employee_id){ 
      $today = Carbon::today();
    $data['employee'] = DB::table('employees')
                        ->where('id', $employee_id)
                        ->select('id', 'name', 'lastname', 'image', 'daily_start_time', 'daily_end_time', 'working_hour', 'status')
                        ->first();
    
    $data['plan'] = DB::table('daily_reports')
        ->leftJoin('daily_report_times', 'daily_report_times.daily_report_id', '=', 'daily_reports.id')
        ->where('daily_reports.employee_id', $employee_id)
        ->where('daily_reports.start_date', Carbon::today())
        ->where('daily_reports.status', '!=', 'ended')
        ->select(
            'daily_reports.start_date',
            'daily_reports.end_date',
            'daily_reports.lat',
            'daily_reports.lon',
            'daily_reports.ip',
            'daily_reports.status',
            'daily_reports.id as daily_report_id',
            'daily_report_times.id as daily_times_id',
            'daily_report_times.work_place_id',
            'daily_report_times.task_id',
            'daily_report_times.appointment_id',
            'daily_report_times.lat as times_lat',
            'daily_report_times.lon as times_lon',
            'daily_report_times.ip as times_ip',
            'daily_report_times.status as times_status'
        )
        ->orderByDesc('daily_report_times.id') // 👈 pick the latest one
        ->limit(1)
        ->first(); // 👈 return just one

 

   
    return view('admin.daily_report.plan.plan', $data);
}


public function autoCheckout(Request $request)
{
    $now = Carbon::now();
    $today = $now->toDateString();
    $lat = $request->lat;
    $lon = $request->lon;

    $reports = DailyReport::whereDate('start_date', $today)
        ->whereIn('status', ['start', 'pause'])
        ->get();

    foreach ($reports as $report) {
        $report->update(['status' => 'ended']);

        DailyReportTime::where('daily_report_id', $report->id)
            ->whereNull('end_time')
            ->update([
                'end_time' => $now->toTimeString(),
                'lat' => $lat,
                'lon' => $lon,
                'ip' => $request->ip(),
                'status' => 'ended',
                'work_status' => 'auto-checked-out'
            ]);
    }

    return response()->json([
        'success' => 'All pending check-ins have been automatically checked out.'
    ]);
}

public function autoCheckoutEmp(Request $request)
{
    $now = Carbon::now();
    $today = $now->toDateString();
    $lat = $request->lat;
    $lon = $request->lon;
    $code = $request->code;

    if (!$code) {
        return response()->json(['error' => 'Employee code is required.'], 400);
    }

    $employee = DB::table('employees')->where('code', $code)->first();

    if (!$employee) {
        return response()->json(['error' => 'Invalid employee code.'], 404);
    }

    $employee_id = $employee->id;

    $report = DailyReport::where('employee_id', $employee_id)
        ->whereDate('start_date', $today)
        ->whereIn('status', ['started', 'paused'])
        ->first();

    if (!$report) {
        return response()->json([
            'error' => 'No active check-in found for this employee.'
        ], 404);
    }

    $report->update(['status' => 'ended']);

    DailyReportTime::where('daily_report_id', $report->id)
        ->whereNull('end_time')
        ->update([
            'end_time' => $now->toTimeString(),
            'lat' => $lat,
            'lon' => $lon,
            'ip' => $request->ip(),
            'status' => 'ended',
            'work_status' => 'checked-out'
        ]);

     

    return response()->json([
        'success' => 'Employee has been automatically checked out.'
    ]);
}

public function getTime($daily_report_id, $daily_times_id) {

    $data = DB::table('daily_reports')
                ->join('daily_report_times', 'daily_report_times.daily_report_id', '=', 'daily_reports.id')
                ->where('daily_reports.id', $daily_report_id)
                ->where('daily_report_times.id', $daily_times_id)
                ->where('daily_reports.status', '!=', 'ended')
                ->select('daily_reports.start_date', 'daily_reports.end_date', 'daily_reports.lat', 'daily_reports.lon', 'daily_reports.ip', 'daily_reports.status', 'daily_reports.id as daily_report_id'
                            ,'daily_report_times.id as daily_times_id', 'daily_report_times.work_place_id', 'daily_report_times.task_id', 'daily_report_times.appointment_id', 'daily_report_times.lat as times_lat', 'daily_report_times.lon as times_lon',
                            'daily_report_times.ip as times.ip', 'daily_report_times.status as status', 'daily_report_times.start_time', 'daily_report_times.end_time'
                        )
                ->get();

      return response()->json($data, 200);

}

 
 public function startWork(Request $request)
{
    try {
        $request->validate([
            'daily_report_id' => 'required|exists:daily_reports,id',
            'id' => 'required|integer',
            'type' => 'required|in:task,appointment',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'status' => 'nullable|string',
            'work_status' => 'nullable|string',
        ]);

        $lat = $request->lat;
        $lon = $request->lon;
        $type = $request->work_status ?? 'Customer'; // default to 'Customer' if not given
        $tolerance = 0.0005;

        // Try to find existing workplace
        if ($type === 'Office') {
            $workplace = DB::table('daily_report_work_places')
                ->where('type', 'branch')
                ->get()
                ->first(function ($place) use ($lat, $lon, $tolerance) {
                    return abs($place->lat - $lat) <= $tolerance && abs($place->lon - $lon) <= $tolerance;
                });

            if (!$workplace) {
                return response()->json(['error' => 'You are not at the registered office location.']);
            }
        } else {
            $workplace = DB::table('daily_report_work_places')
                ->get()
                ->first(function ($place) use ($lat, $lon, $tolerance) {
                    return abs($place->lat - $lat) <= $tolerance && abs($place->lon - $lon) <= $tolerance;
                });

            // If no nearby workplace found, create one
            if (!$workplace) {
                $workplaceId = DB::table('daily_report_work_places')->insertGetId([
                    'lat' => $lat,
                    'lon' => $lon,
                    'type' => $type,
                    'address' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $workplace = (object)['id' => $workplaceId, 'type' => $type];
            }
        }

        $data = [
            'daily_report_id' => $request->daily_report_id,
            'work_place_id' => $workplace->id,
            'lat' => $lat,
            'lon' => $lon,
            'ip' => $request->ip(),
            'status' => 'started',
            'work_status' => $workplace->type,
            'start_time' => now(),
        ];

        if ($request->type === 'appointment') {
            $data['appointment_id'] = $request->id;
        } else {
            $data['task_id'] = $request->id;
        }

        DailyReportTime::create($data);

        return response()->json(['success' => 'Work has been started.']);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function getEmailDetails($id)
{
    $email = LeadEmail::find($id);

    if (!$email) {
        return response()->json(['message' => 'Email not found.'], 404);
    }

    return response()->json([
        'subject' => $email->subject ?? '(Kein Betreff)',
        'from'    => $email->from,
        'domain'  => $email->domain,
        'date'    => optional($email->date)->format('d.m.Y H:i'),
        'body'    => html_entity_decode($email->body),
        'status'  => $email->status,
    ]);
}



}
