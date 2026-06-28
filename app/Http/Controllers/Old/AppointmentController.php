<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\AppointmentEmployee;
use DB;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; 



class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {

       $data['customers'] = DB::table('customers')
                        ->join('customer_alternative_adds as alternative', 'alternative.customer_id', '=', 'customers.id') 
                        ->select('customers.id', 'customers.title','customers.name', 'customers.lastname', 'alternative.main', 'customers.phone', 'customers.telephone', 'customers.email',
                                           'alternative.street', 'alternative.postcode', 'alternative.city', 
                                     
                                )
                        ->where('customers.status', '!=', 'Junk') 
                        ->get();
       $data['product']= DB::table('article_groups')->orderBy('id','asc')->get();
       $data['employee'] = DB::table('employees')
                                ->select('id', 'name', 'lastname', 'image')
                                ->where('status', 'active')
                                ->get();

        $data['phases'] = DB::table('task_phases')->orderBy('order', 'asc')->get();
 
        
        return view('admin.calendar.appointment.calender', $data);
    }

    public function getAppointments()
    {
        // Fetch appointments with related customer and product info
        $appointments = DB::table('appointments')
            ->join('customers', 'customers.id', '=', 'appointments.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'appointments.product_id')
            ->select(
                'appointments.*', 
                'customers.name as customerName',   
                'customers.lastname as customerLastname',   
                'customers.title as ctitle', 
                'customers.lat', 'customers.lon',
                'article_groups.article_group'
            )
            ->get();

        // Fetch all employees related to appointments
        $employees = DB::table('activity_employees')
            ->join('employees', 'employees.id', '=', 'activity_employees.employee_id')
            ->select('employees.name', 'employees.lastname', 'employees.image', 'activity_employees.appointment_id')
            ->distinct()  
            ->get();

        // Group employees by appointment_id
        $employeesByAppointment = $employees->groupBy('appointment_id');

        // Fetch phases and activities for each appointment
        $phases = DB::table('task_phases')
            ->join('activity_employees', 'activity_employees.phase_id', '=', 'task_phases.id')
            ->select('task_phases.*')
            ->get()
            ->groupBy('appointment_id');

        $activities = DB::table('phase_activities')
            ->join('activity_employees', 'activity_employees.activity_id', '=', 'phase_activities.id')
            ->select('phase_activities.*')
            ->get()
            ->groupBy('appointment_id');

        // Add employees, phases, and activities to their respective appointments
        foreach ($appointments as $appointment) {
            $appointment->employees = $employeesByAppointment->get($appointment->id) ?: [];
            $appointment->phases = $phases->get($appointment->id) ?: [];  // Include phases
            $appointment->activities = $activities->get($appointment->id) ?: [];  // Include activities
        }

        return response()->json($appointments);
    }

 public function getCustomer() {
    $data = DB::table('customers')
              ->join('customer_alternative_adds as alternative', 'alternative.customer_id', '=', 'customers.id')
              ->select('customers.id', 'customers.title', 'customers.name', 'customers.lastname', 'alternative.main', 'customers.phone', 'customers.telephone', 'customers.email', 'alternative.street', 'alternative.postcode', 'alternative.city')
              ->where('customers.status', '!=', 'Junk')
              ->get();

    return response()->json($data);
}
    public function getUserAppointments($user)
    {
        // Fetch appointments with related customer and product info
        $appointments = DB::table('appointments')
            ->join('customers', 'customers.id', '=', 'appointments.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'appointments.product_id')
            ->leftJoin('activity_employees', 'activity_employees.appointment_id', '=', 'appointments.id')
            ->where('activity_employees.employee_id', '=', auth()->user()->name)
            ->select(
                'appointments.*',
                'customers.name as customerName',
                'customers.lastname as customerLastname',
                'customers.title as ctitle',
                'customers.lat',
                'customers.lon',
                'article_groups.article_group',
                'activity_employees.employee_id'
            ) 
            ->get();

            Log::info('curernt appointments:', [$appointments]);
         
  

        return response()->json($appointments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function productLoad($id)
    {
        $product = DB::table('customer_product_lists')
                        ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
                        ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
                        ->where('customers.status', '!=', 'completed')
                        ->where('customers.id', '=', $id)
                        ->get();

         return response()->json($product, 200);
    }

    public function phaseLoad($product_id) {
        $phase = DB::table('task_phases')
                    ->where('product_id', $product_id)
                    ->get();

        if($phase->isEmpty()) {
            return response()->json(['message' => 'No phases found for this product'], 404);
        }

        return response()->json($phase, 200);
    }

public function activityLoad($id, $product_id, $customer_id)
{
    // Retrieve the IDs of `activity_id`s in `activity_employees` that match the criteria
    $check = DB::table('appointments')
                ->join('activity_employees as active', 'active.appointment_id', '=', 'appointments.id')
                ->where('active.phase_id', $id)
                ->where('appointments.product_id', $product_id)
                ->where('appointments.customer_id', $customer_id)
                ->pluck('active.activity_id');

    // Retrieve sub-task IDs that are specifically in `activity_employees` to exclude them later
    $checkSub = DB::table('task_sub_tasks')
                    ->join('activity_employees as active', 'active.sub_task_id', '=', 'task_sub_tasks.id')
                    ->where('task_sub_tasks.phase_id', $id)
                    ->pluck('task_sub_tasks.id');

    Log::info('The Task check: ', [$check]);
    Log::info('The Sub Task check: ', [$checkSub]);

    // Retrieve 'phase_activities' records that are not in `activity_employees` or sub-tasks in `checkSub`
    $activity = DB::table('phase_activities')
                ->join('task_phases', 'task_phases.id', '=', 'phase_activities.phase_id')
                ->join('article_groups', 'article_groups.id', '=', 'phase_activities.product_id')
                ->leftJoin('task_sub_tasks as sub_task', function($join) {
                    $join->on('sub_task.task_id', '=', 'phase_activities.id');
                })
                ->leftJoin('activity_employees as active', function($join) {
                    $join->on('active.sub_task_id', '=', 'sub_task.id');
                })
                ->select(
                    'phase_activities.id', 
                    'phase_activities.phase_id', 
                    'phase_activities.product_id', 
                    'phase_activities.title', 
                    'phase_activities.description', 
                    'article_groups.article_group', 
                    'task_phases.phase_name', 
                    DB::raw('COUNT(DISTINCT sub_task.id) as sub_task_count')  
                )
                ->where('task_phases.id', $id)
                ->where('article_groups.id', $product_id) 
                ->where(function($query) use ($check) {
                    $query->whereNull('phase_activities.id')
                          ->orWhereNotIn('phase_activities.id', $check);    // Include only sub_tasks not in `Check`
                })// Exclude phase activities in `check`
                ->where(function($query) use ($checkSub) {
                    $query->whereNull('sub_task.id')
                          ->orWhereNotIn('sub_task.id', $checkSub);  // Include only sub_tasks not in `checkSub`
                })
                ->groupBy(
                    'phase_activities.id',
                    'phase_activities.phase_id',
                    'phase_activities.product_id', 
                    'phase_activities.title', 
                    'phase_activities.description', 
                    'article_groups.article_group', 
                    'task_phases.phase_name'
                )
                ->get();

    return response()->json([
        'activities' => $activity,
    ], 200);
}



public function subTaskLoad($phase, $activity_id, $product_id, $customer_id){

$check = DB::table('appointments')
        ->join('activity_employees as active', 'active.appointment_id', '=', 'appointments.id') 
        ->where('active.phase_id', $phase)
        ->where('appointments.product_id', $product_id)
        ->where('appointments.customer_id', $customer_id)
        ->pluck('active.sub_task_id');
 

    $subTask = DB::table('task_sub_tasks')
                ->where('phase_id', $phase)
                ->where('task_id', $activity_id)
                 ->where(function($query) use ($check) {
                    $query->whereNull('id')
                          ->orWhereNotIn('id', $check);  // Include only sub_tasks not in `Check`
                })
                ->get();

    Log::info('phase', [$phase]);
    Log::info('Activity: ', [$activity_id]);
    return response()->json($subTask, 200);


}


 
    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    // Log the incoming request data
    \Log::info('Incoming Request:', $request->all());

    // Validation
     // Validation
    $validator = Validator::make($request->all(), [
        'customer_id' => 'required|exists:customers,id',
        'product_id' => 'required|exists:article_groups,id',
        'postcode' => 'required|string',
        'title' => 'nullable|string',
        'description' => 'nullable|string',
        'priority' => 'nullable|string',
        'calendar_color' => 'nullable|string', 
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'report_date' => 'required|integer', 
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i',
        'active' => 'required|array',
        'active.*.phase_id' => 'required|exists:task_phases,id',
        'active.*.activity_id' => 'required|integer',  // Ensuring it's an integer, not an array
        'active.*.employee_id' => 'required|array|min:1',
        'active.*.employee_id.*' => 'exists:employees,id',
        'active.*.sub_task_id' => 'nullable|array|min:1',
        'active.*.sub_task_id.*' => 'exists:task_sub_tasks,id',
        'employees' => 'nullable|array',
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed:', ['errors' => $validator->errors()->toArray()]);
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], 422);
    }

    \Log::info('Start Time: ' . $request->start_time);
    \Log::info('End Time: ' . $request->end_time);

    try {
        // Ensure UTC time consistency
        $startDateTime = Carbon::parse($request->start_date . ' ' . $request->start_time)->timezone('UTC');
        $endDateTime = Carbon::parse($request->end_date . ' ' . $request->end_time)->timezone('UTC');

        // Log the times for debugging
        \Log::info('Checking for existing appointment between ' . $startDateTime->format('H:i') . ' and ' . $endDateTime->format('H:i'));

        // Check for overlapping appointments on the same start and end date
        $existingAppointment = DB::table('appointments')
            ->where('start_date', '=', $request->start_date)
            ->where('end_date', '=', $request->end_date)
            ->where(function($query) use ($startDateTime, $endDateTime) {
                $query->where(function($q) use ($startDateTime, $endDateTime) {
                    $q->whereTime('start_time', '<', $endDateTime->format('H:i'))
                      ->whereTime('end_time', '>', $startDateTime->format('H:i'));
                });
            })
            ->first();

        if ($existingAppointment) {
            // Check if the new event's end time is more than 30 minutes after the existing event's end time
            $existingEndTime = Carbon::parse($existingAppointment->end_date . ' ' . $existingAppointment->end_time)->timezone('UTC');
            $existingStartTime = Carbon::parse($existingAppointment->start_date . ' ' . $existingAppointment->start_time)->timezone('UTC');

            \Log::info('Existing Appointment:', ['existingAppointment' => $existingAppointment]); 

            // Check if the new event can be accepted (must be 30 minutes apart from the existing appointment)
            if ($endDateTime->greaterThan($existingEndTime->addMinutes(30)) || $startDateTime->greaterThan($existingEndTime)) {
                // If the new event's start time is more than 30 minutes after the existing event's end time
                \Log::info('New appointment is more than 30 minutes apart from the existing event.');
            } elseif ($endDateTime->lessThan($existingStartTime->subMinutes(30))) {
                // If the new event's end time is more than 30 minutes before the existing event's start time
                \Log::info('New appointment ends at least 30 minutes before an existing event starts.');
            } else {
                // Overlapping event or within 30 minutes
                \Log::info('Overlapping event found:', ['overlapping_event' => $existingAppointment]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'An appointment already exists within 30 minutes of this time.',
                    'overlapping_event' => [
                        'title' => $existingAppointment->title,
                        'start_date' => $existingAppointment->start_date,
                        'start_time' => $existingAppointment->start_time,
                        'end_time' => $existingAppointment->end_time
                    ]
                ], 422);
            }
        }

            // Usage in your controller method
        $startDate = Carbon::parse($request->start_date); 
        if ($request->report_date == 3) {
            $report_date = $this->addBusinessDays($startDate, 3);
        } elseif ($request->report_date == 5) {
            $report_date = $this->addBusinessDays($startDate, 5);
        } else {
            $report_date = $this->addBusinessDays($startDate, 7);
        }
        // Create the appointment if no conflicts were found
        $appointment = Appointment::create([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'title' => $request->title,
            'postcode' => $request->postcode,
            'description' => $request->description,
            'priority' => $request->priority,
            'color' => $request->calendar_color,
            'start_date' => $request->start_date,
            'report_date' => $report_date,
            'report_date_type' => $request->report_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        \Log::info('Appointment created successfully', ['appointment' => $appointment]);
 
       // Sync the employees with activities, phases, and sub-tasks in the pivot table
          if ($request->has('active')) {
                foreach ($request->active as $row) {
                    $phaseId = $row['phase_id'] ?? null;
                    $activityIds = is_array($row['activity_id']) ? $row['activity_id'] : [$row['activity_id']];
                    $employeeIds = $row['employee_id'] ?? [];
                    $subTaskIds = $row['sub_task_id'] ?? null; // Allow null for sub_task_id
                    $appointmentId = $appointment->id;

                    if ($phaseId) {
                        foreach ($employeeIds as $employeeId) {
                            foreach ($activityIds as $activityId) {
                                // If sub_task_id exists, loop through it; otherwise, save without it
                                if (is_array($subTaskIds) && !empty($subTaskIds)) {
                                    foreach ($subTaskIds as $subTaskId) {
                                        $appointment->employees()->attach($employeeId, [
                                            'phase_id' => $phaseId,
                                            'activity_id' => $activityId,
                                            'sub_task_id' => $subTaskId, // Save each sub_task_id
                                            'appointment_id' => $appointmentId,
                                        ]);
                                    }
                                } else {
                                    // Save without sub_task_id
                                    $appointment->employees()->attach($employeeId, [
                                        'phase_id' => $phaseId,
                                        'activity_id' => $activityId,
                                        'sub_task_id' => null, // Explicitly set to null
                                        'appointment_id' => $appointmentId,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }


        return response()->json($appointment, 201);

    } catch (\Exception $e) {
        \Log::error('Error creating appointment', ['error' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'message' => 'Appointment creation failed!',
            'error' => $e->getMessage()
        ], 500);
    }
}



   // Inside AppointmentController or your relevant controller
        private function addBusinessDays($date, $days) {
            $currentDate = $date->copy();
            $addedDays = 0;

            while ($addedDays < $days) {
                $currentDate->addDay();

                // Check if the current date is a weekend (Saturday or Sunday)
                if (!$currentDate->isWeekend()) {
                    $addedDays++;
                }
            }

            return $currentDate->format('Y-m-d'); // Format the date as needed
        }

 


    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
   
public function edit($id)
{
    Log::info("Fetching appointment for ID: $id");

    // Fetch appointments with related customer and product info
    $appointments = DB::table('appointments')
        ->join('customers', 'customers.id', '=', 'appointments.customer_id')
        ->join('article_groups', 'article_groups.id', '=', 'appointments.product_id')
        ->select(
            'appointments.*', 
            'customers.name', 
            'customers.lastname', 
            'customers.title as ctitle', 
            'article_groups.article_group'
        )
        ->where('appointments.id', $id)
        ->get();

    Log::info('Appointments fetched:', ['appointments' => $appointments]);

    // Fetch all employees related to appointments
    $employees = DB::table('activity_employees')
        ->join('employees', 'employees.id', '=', 'activity_employees.employee_id')
        ->select('employees.name', 'employees.lastname', 'employees.image', 'activity_employees.appointment_id')
        ->where('activity_employees.appointment_id', $id)
        ->get();

    Log::info('Employees fetched:', ['employees' => $employees]);

    // Group employees by appointment_id
    $employeesByAppointment = $employees->groupBy('appointment_id');
    Log::info('Employees grouped by appointment:', ['employeesByAppointment' => $employeesByAppointment]);

    // Fetch phases and activities for each appointment
    $phases = DB::table('task_phases')
        ->join('activity_employees', 'activity_employees.phase_id', '=', 'task_phases.id')
        ->select('task_phases.*')
        ->where('activity_employees.appointment_id', $id)
        ->get()
        ->groupBy('appointment_id');

    Log::info('Phases fetched and grouped:', ['phases' => $phases]);

    $activities = DB::table('phase_activities')
        ->join('activity_employees', 'activity_employees.activity_id', '=', 'phase_activities.id')
        ->select('phase_activities.*')
        ->where('activity_employees.appointment_id', $id)
        ->get()
        ->groupBy('appointment_id');

    Log::info('Activities fetched and grouped:', ['activities' => $activities]);

    // Add employees, phases, and activities to their respective appointments
    foreach ($appointments as $appointment) {
        $appointment->employees = $employeesByAppointment->get($appointment->id) ?: [];
        $appointment->phases = $phases->get($appointment->id) ?: [];  // Include phases
        $appointment->activities = $activities->get($appointment->id) ?: [];  // Include activities

        Log::info('Final appointment data:', [
            'appointment' => $appointment, 
            'employees' => $appointment->employees, 
            'phases' => $appointment->phases,
            'activities' => $appointment->activities
        ]);
    }

    return response()->json($appointments);
}

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, $id)
{
    // Validate the incoming request data
    $validator = Validator::make($request->all(), [
        'customer_id' => 'required|exists:customers,id',
        'product_id' => 'required|exists:article_groups,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'start_time' => 'nullable|date_format:H:i',
        'end_time' => 'nullable|date_format:H:i',
        'start_time' => 'nullable|date_format:H:i',
        'end_time' => 'nullable|date_format:H:i', 
        'title' => 'nullable|string',
        'description' => 'nullable|string',
        'priority' => 'nullable|string',
        'color' => 'nullable|string',
        'active.*.phase_id' => 'required|exists:task_phases,id',
        'active.*.activity_id' => 'required|array|min:1',
        'active.*.activity_id.*' => 'exists:phase_activities,id',
        'active.*.employee_id' => 'required|array|min:1',
        'active.*.employee_id.*' => 'exists:employees,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        // Find the appointment
        $appointment = Appointment::findOrFail($id);

        // Update the appointment details
        $appointment->update([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'color' => $request->calendar_color,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        // Update pivot table data for employees, phases, and activities
        if ($request->has('active')) {
            $syncData = [];

            foreach ($request->active as $row) {
                $phaseId = $row['phase_id'] ?? null;
                $activityIds = $row['activity_id'] ?? [];
                $employeeIds = $row['employee_id'] ?? [];
                $appointmentId = $appointment->id;

                if ($phaseId) {
                    foreach ($employeeIds as $employeeId) {
                        foreach ($activityIds as $activityId) {
                            $syncData[] = [
                                'employee_id' => $employeeId,
                                'phase_id' => $phaseId,
                                'activity_id' => $activityId,
                                'appointment_id' => $appointmentId,
                            ];
                        }
                    }
                }
            }

            // Sync employees with the appointment (updating the pivot table)
            $appointment->employees()->sync([]);

            foreach ($syncData as $data) {
                $appointment->employees()->attach($data['employee_id'], [
                    'phase_id' => $data['phase_id'],
                    'activity_id' => $data['activity_id'],
                    'appointment_id' => $data['appointment_id'],
                ]);
            }
        }

        return response()->json($appointment, 200);

    } catch (\Exception $e) {
        \Log::error('Error updating appointment', ['error' => $e->getMessage()]);

        return response()->json([
            'status' => 'error',
            'message' => 'Appointment update failed!',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function edit_customer($id, $product_id) {
    try {
        // Fetch the customer data related to the appointment and product
        $customer = DB::table('appointments')  
                    ->join('customers', 'customers.id', '=', 'appointments.customer_id')
                    ->join('article_groups', 'article_groups.id', '=', 'appointments.product_id')
                    ->select('customers.name', 'customers.lastname', 'customers.id')
                    ->where('customers.id', $id)
                    ->where('article_groups.id', $product_id)
                    ->first();

        return response()->json($customer, 200);
    } catch (\Exception $e) {
        \Log::error('Error loading customer data:', ['error' => $e->getMessage()]);

        return response()->json([
            'status' => 'error',
            'message' => 'Error loading customer data',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function edit_product($id, $product_id)
    {
        try {
            // Fetch the product related to the customer
            $product = DB::table('customer_product_lists')
                ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
                ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
                ->select('article_groups.id', 'article_groups.article_group') 
                ->where('customers.id', $id)
                ->where('article_groups.id', $product_id)
                ->first();
            \Log::info('selected product: ', [$product]);

            return response()->json($product, 200);
        } catch (\Exception $e) {
            \Log::error('Error loading product data:', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error loading Product data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


public function edit_load_activity_employee($appointment_id)
{
    try {
        $activity = DB::table('activity_employees')
            ->join('employees', 'employees.id', '=', 'activity_employees.employee_id')
            ->join('task_phases', 'task_phases.id', '=', 'activity_employees.phase_id')
            ->join('task_activities', 'task_activities.id', '=', 'activity_employees.activity_id')
            ->join('appointments', 'appointments.id', '=', 'activity_employees.appointment_id')
            ->select('employees.name', 'employees.lastname', 'employees.image as employee_image',
                     'task_phases.phase_name', 'task_activities.title as activity_name', 
                     'activity_employees.*'
            )
            ->where('appointments.id', $appointment_id)
            ->get();
        \Log::info('The activity: ', [$activity]);
        return response()->json($activity, 200);
    } catch (\Exception $e) {
        \Log::error('Error loading activity employees data:', ['error' => $e->getMessage()]);

        return response()->json([
            'status' => 'error',
            'message' => 'Error loading activity employees data',
            'error' => $e->getMessage(),
        ], 500);
    }
}


   public function mini_update(Request $request, $id)
{
    // Validate the incoming request data
    $validator = Validator::make($request->all(), [
        'customer_id' => 'required|exists:customers,id',
        'product_id' => 'required|exists:article_groups,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'start_time' => 'nullable|date_format:H:i',
        'end_time' => 'nullable|date_format:H:i',
        'title' => 'nullable|string',
        'description' => 'nullable|string',
        'priority' => 'nullable|string',
        'calendar_color' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        // Find the appointment to update
        $appointment = Appointment::findOrFail($id);

        // Ensure UTC time consistency
        $startDateTime = Carbon::parse($request->start_date . ' ' . $request->start_time)->timezone('UTC');
        $endDateTime = Carbon::parse($request->end_date . ' ' . $request->end_time)->timezone('UTC');

        // Log the times for debugging
        \Log::info('Checking for existing appointment between ' . $startDateTime->format('H:i') . ' and ' . $endDateTime->format('H:i'));

        // Check for overlapping appointments on the same start and end date, excluding the current appointment being updated
        $existingAppointment = DB::table('appointments')
            ->where('start_date', '=', $request->start_date)
            ->where('end_date', '=', $request->end_date)
            ->where('id', '!=', $id)  // Exclude the current appointment from the check
            ->where(function($query) use ($startDateTime, $endDateTime) {
                $query->where(function($q) use ($startDateTime, $endDateTime) {
                    $q->whereTime('start_time', '<', $endDateTime->format('H:i'))
                      ->whereTime('end_time', '>', $startDateTime->format('H:i'));
                });
            })
            ->first();

        if ($existingAppointment) {
            // Check if the new event's end time is more than 30 minutes after the existing event's end time
            $existingEndTime = Carbon::parse($existingAppointment->end_date . ' ' . $existingAppointment->end_time)->timezone('UTC');
            $existingStartTime = Carbon::parse($existingAppointment->start_date . ' ' . $existingAppointment->start_time)->timezone('UTC');

            \Log::info('Existing Appointment:', ['existingAppointment' => $existingAppointment]);

            // Check if the new event can be accepted (must be 30 minutes apart from the existing appointment)
            if ($endDateTime->greaterThan($existingEndTime->addMinutes(30)) || $startDateTime->greaterThan($existingEndTime)) {
                // New event is more than 30 minutes after the existing event's end time
                \Log::info('New appointment is more than 30 minutes apart from the existing event.');
            } elseif ($endDateTime->lessThan($existingStartTime->subMinutes(30))) {
                // New event ends more than 30 minutes before the existing event's start time
                \Log::info('New appointment ends at least 30 minutes before an existing event starts.');
            } else {
                // Overlapping event or within 30 minutes
                \Log::info('Overlapping event found:', ['overlapping_event' => $existingAppointment]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'An appointment already exists within 30 minutes of this time.',
                    'overlapping_event' => [
                        'title' => $existingAppointment->title,
                        'start_date' => $existingAppointment->start_date,
                        'start_time' => $existingAppointment->start_time,
                        'end_time' => $existingAppointment->end_time
                    ]
                ], 422);
            }
        }

        // Update the appointment if no conflicts were found
        $appointment->update([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'color' => $request->calendar_color,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        \Log::info('Appointment updated successfully', ['appointment' => $appointment]);

        return response()->json($appointment, 200);

    } catch (\Exception $e) {
        \Log::error('Error updating appointment', ['error' => $e->getMessage()]);

        return response()->json([
            'status' => 'error',
            'message' => 'Appointment update failed!',
            'error' => $e->getMessage(),
        ], 500);
    }
}

   


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $data = Appointment::find($id);
            $data->delete();

         return response()->json($appointment, 200);
          } catch (\Exception $e) {
        \Log::error('Error updating appointment', ['error' => $e->getMessage()]);

        return response()->json([
            'status' => 'error',
            'message' => 'Appointment update failed!',
            'error' => $e->getMessage(),
        ], 500);
    }
    }
 
}
