<?php

namespace App\Http\Controllers;

use App\Models\TaskToDo;
use Illuminate\Http\Request;
use App\Models\Customer;
use DB;
use App\Models\Product;
use App\Models\ArticleGroup; 
use App\Models\TaskDocument; 
use App\Models\TaskPhase; 
use App\Models\CustomerPhaseStage; 
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;  
use Carbon\Carbon;
use Illuminate\Support\Collection;  
use App\Notifications\ProjectTaskNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;
 use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\DatabaseNotification;



class TaskToDoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        $this->middleware('auth');
    }
     public function index(Request $request)
    {
        // Retrieve query parameters for filtering if needed
        $search = $request->input('search');

        // Start query for TaskToDo
        $query = TaskToDo::query();

        // Apply search filter if provided
        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhereHas('labels', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        // Eager load related models
        $tasks = $query->with('labels')->get();

        // Pass the data to the view
       return view('admin.todo.todo_lists')->with('tasks', $tasks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function checklist($id)
    {
    
        $customer = Customer::find($id);
        if ($customer) {
            $item['customer'] = $customer;
        } else {
            return redirect()->back()->with('delete_msg', 'Der Kunde ist nicht im System');
        }

        $alternative = DB::table('customer_alternative_adds')
                        ->where('customer_id', $id)
                        ->first();

        $postcode = $alternative->postcode;
        $address_no = $alternative->address_no;

        $item['product'] = Product::all();

    
            // Retrieve selected products for the customer
            $productList = DB::table('customer_product_lists')
                ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
                ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
                ->select('article_groups.id as product_id')
                ->where('customers.id', $id)
                ->pluck('product_id')
                ->toArray();
 
            // Retrieve all articles/products to display
            $item['articles'] = ArticleGroup::all(); 


        $item['alternative'] = $alternative;

        $item['employees']=DB::table('employees')->where('status', '=', 'active')->select('id', 'name', 'lastname', 'image')->get();

        $item['task_phase'] = DB::table('task_phases')
                                ->join('phase_activities as p_active', 'p_active.phase_id','=', 'task_phases.id')
                                ->whereIn('task_phases.product_id', $productList)
                                ->where('task_phases.product_id', $id)
                                ->select('task_phases.*', 'p_active.title', 'p_active.id as p_active_id', 'p_active.description', 'task_phases.product_id', 'task_phases.order')
                                ->get();
       $item['responsibles'] = DB::table('customer_responsibles')
                ->join('employees', 'employees.id', '=', 'customer_responsibles.employee_id')
                ->join('customers', 'customers.id', '=', 'customer_responsibles.customer_id')
                ->leftJoin('customer_product_lists as lists', 'lists.customer_id', '=', 'customer_responsibles.customer_id')
                ->select('employees.id as emp_id', 'employees.name', 'employees.lastname', 'customers.id as customer_id'  , 'employees.image') 
                ->groupBy('employees.id', 'employees.name', 'employees.lastname', 'customers.id', 'employees.image')
                ->distinct()
                ->get();
 
        $item['productList'] = $productList;      

       $item['tasks'] = DB::table('task_to_dos')
                ->join('customers', 'customers.id', '=', 'task_to_dos.customer_id')
                ->join('task_phases', 'task_phases.id', '=', 'task_to_dos.phase_id')
                ->join('article_groups', 'article_groups.id', 'task_to_dos.product_id')
                ->join('phase_activities', 'phase_activities.id', 'task_to_dos.activities_id')
                ->join('employees as contact', 'contact.id', '=', 'task_to_dos.contact_person')
                ->leftJoin('employees as responsible_person', 'responsible_person.id', '=', 'task_to_dos.responsible_person')
                ->leftJoin('employees as outside_service', 'outside_service.id', '=', 'task_to_dos.outside_service')
                ->leftJoin('task_documents', 'task_documents.id', '=', 'task_to_dos.document_id')
                ->select(
                    'task_to_dos.*',
                    'customers.name as cname', 'customers.lastname as clastname',
                    'task_phases.phase_name', 'task_phases.id as phase_id',
                    'article_groups.article_group', 'article_groups.id as product_id',
                    'phase_activities.title as activity_title', 'phase_activities.id as activity_id',
                    'task_to_dos.id as task_id', 'task_to_dos.done', 'task_to_dos.note', 'task_to_dos.status', 'task_to_dos.done_date',
                    'contact.name as contact_name', 'contact.lastname as contact_lastname', 'contact.image as cimage', 'contact.id as cid',
                    'responsible_person.name as responsible_name', 'responsible_person.lastname as responsible_lastname', 'responsible_person.id as rid',
                    'responsible_person.image as rimage', 
                    'outside_service.name as outside_name', 'outside_service.lastname as outside_lastname', 'outside_service.image as oimage',
                    'task_documents.document_name', 'task_documents.document_sum', 'task_documents.document'
                    
                )
                ->get();

                
        $item['document'] = DB::Table('task_documents')
                                ->join('customers', 'customers.id', '=', 'task_documents.customer_id')
                                ->join('task_phases', 'task_phases.id', '=', 'task_documents.phase_id')
                                ->join('article_groups', 'article_groups.id', 'task_documents.product_id')
                                ->join('phase_activities', 'phase_activities.id', 'task_documents.activities_id') 
                                ->select('customers.name as cname', 'customers.lastname as clastname',
                                            'task_phases.phase_name', 'task_phases.id as phase_id',
                                            'article_groups.article_group', 'article_groups.id as product_id',
                                            'phase_activities.title as activity_title', 'phase_activities.id as activity_id',
                                            'task_documents.id as document_id','task_documents.document_name', 'task_documents.document_sum', 'task_documents.document_note', 
                                            'task_documents.document_status'
                                             )
                                        
                                ->get();

       
 
    return view('admin.todo.todo_checklist', $item);
    }

  public function getTasks($id, $product_id)
{
   $customer = Customer::find($id);
        if ($customer) {
            $item['customer'] = $customer;
        } else {
            return redirect()->back()->with('delete_msg', 'Der Kunde ist nicht im System');
        }

        $alternative = DB::table('customer_alternative_adds')
                        ->where('customer_id', $id)
                        ->first();

        $postcode = $alternative->postcode;
        $address_no = $alternative->address_no;

        $item['product'] = Product::all();

    
            // Retrieve selected products for the customer
            $productList = DB::table('customer_product_lists')
                ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
                ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
                ->select('article_groups.id as product_id')
                ->where('customers.id', $id) 
                ->pluck('product_id')
                ->toArray();
 
            // Retrieve all articles/products to display
            $item['articles'] = ArticleGroup::all(); 


        $item['alternative'] = $alternative;

        $item['employees']=DB::table('employees')->where('status', '=', 'active')->select('id', 'name', 'lastname', 'image')->get();

        $item['task_phase'] = DB::table('task_phases')
                                ->join('phase_activities as p_active', 'p_active.phase_id','=', 'task_phases.id')
                                ->where('task_phases.product_id', '=', $product_id)
                                ->select('task_phases.*', 'p_active.title', 'p_active.id as p_active_id', 'p_active.description', 'task_phases.product_id', 'task_phases.order')
                                ->get();
       $item['responsibles'] = DB::table('customer_responsibles')
                ->join('employees', 'employees.id', '=', 'customer_responsibles.employee_id')
                ->join('customers', 'customers.id', '=', 'customer_responsibles.customer_id')
                ->leftJoin('customer_product_lists as lists', 'lists.customer_id', '=', 'customer_responsibles.customer_id')
                ->select('employees.id as emp_id', 'employees.name', 'employees.lastname', 'customers.id as customer_id'  , 'employees.image') 
                ->groupBy('employees.id', 'employees.name', 'employees.lastname', 'customers.id', 'employees.image')
                ->distinct()
                ->get();
 
        $item['productList'] = $productList;      

       $item['tasks'] = DB::table('task_to_dos')
                ->join('customers', 'customers.id', '=', 'task_to_dos.customer_id')
                ->join('task_phases', 'task_phases.id', '=', 'task_to_dos.phase_id')
                ->join('article_groups', 'article_groups.id', 'task_to_dos.product_id')
                ->join('phase_activities', 'phase_activities.id', 'task_to_dos.activities_id')
                ->join('employees as contact', 'contact.id', '=', 'task_to_dos.contact_person')
                ->leftJoin('employees as responsible_person', 'responsible_person.id', '=', 'task_to_dos.responsible_person')
                ->leftJoin('employees as outside_service', 'outside_service.id', '=', 'task_to_dos.outside_service')
                ->leftJoin('task_documents', 'task_documents.id', '=', 'task_to_dos.document_id')
                ->select(
                    'task_to_dos.*',
                    'customers.name as cname', 'customers.lastname as clastname',
                    'task_phases.phase_name', 'task_phases.id as phase_id',
                    'article_groups.article_group', 'article_groups.id as product_id',
                    'phase_activities.title as activity_title', 'phase_activities.id as activity_id',
                    'task_to_dos.id as task_id', 'task_to_dos.done', 'task_to_dos.note', 'task_to_dos.status', 'task_to_dos.done_date',
                    'contact.name as contact_name', 'contact.lastname as contact_lastname', 'contact.image as cimage', 'contact.id as cid',
                    'responsible_person.name as responsible_name', 'responsible_person.lastname as responsible_lastname', 'responsible_person.id as rid',
                    'responsible_person.image as rimage', 
                    'outside_service.name as outside_name', 'outside_service.lastname as outside_lastname', 'outside_service.image as oimage',
                    'task_documents.document_name', 'task_documents.document_sum', 'task_documents.document'
                    
                )
                ->get();

                
        $item['document'] = DB::Table('task_documents')
                                ->join('customers', 'customers.id', '=', 'task_documents.customer_id')
                                ->join('task_phases', 'task_phases.id', '=', 'task_documents.phase_id')
                                ->join('article_groups', 'article_groups.id', 'task_documents.product_id')
                                ->join('phase_activities', 'phase_activities.id', 'task_documents.activities_id') 
                                ->select('customers.name as cname', 'customers.lastname as clastname',
                                            'task_phases.phase_name', 'task_phases.id as phase_id',
                                            'article_groups.article_group', 'article_groups.id as product_id',
                                            'phase_activities.title as activity_title', 'phase_activities.id as activity_id',
                                            'task_documents.id as document_id','task_documents.document_name', 'task_documents.document_sum', 'task_documents.document_note', 
                                            'task_documents.document_status'
                                             )
                                        
                                ->get();

       
 
        return view('admin.todo.todo_checklist', $item);
}

 

public function store(Request $request)
{
    try {
        $validated = $request->validate([ 
            'phase_id' => 'required|exists:task_phases,id',
            'product_id' => 'required|exists:article_groups,id',
            'activities_id' => 'required|exists:phase_activities,id',
            'address_no' => 'required',
            'done' => 'required|string',
            'done_date' => 'required|date', 
            'contact_person' => 'nullable|exists:employees,id',
            'responsible_person' => 'nullable|exists:employees,id',
            'outside_service' => 'nullable|exists:employees,id', 
            'document_note' => 'nullable|string', 
            'calendar' => 'nullable|string', 
            'document_status' => 'nullable|string', 
        ],
    [
                'done.required' => 'Please select this check mark if the task is done.',

    ]);

        $calender = $request->calendar ? "true" : "false";

        if ($request->hasFile('document')) {
            $doc_name = time() . '.' . $request->file('document')->getClientOriginalExtension();
            $request->file('document')->move('task/documents/', $doc_name);

            $data = new TaskDocument;
            $data->customer_id = $request->input('customer_id');
            $data->phase_id = $validated['phase_id'];
            $data->product_id =  $validated['product_id'];
            $data->activities_id = $validated['activities_id'];
            $data->document = $doc_name; 
            $data->document_name = $request->input('document_name');
            $data->document_sum = $request->input('document_sum');
            $data->document_note = $request->input('document_note') ?? null;
            $data->document_status = $request->input('document_status') ?? null;
            $data->save();
        }

        $task = new TaskToDo;
        $task->customer_id = $request->input('customer_id');
        $task->phase_id = $validated['phase_id'];
        $task->product_id = $validated['product_id'];
        $task->activities_id = $validated['activities_id'];
        $task->done = $request->input('done') ? 'true' : 'false'; // Handle checkbox
        $task->done_date = $validated['done_date']; 
        $task->contact_person = $validated['contact_person'];
        $task->responsible_person = $request->input('responsible_person');
        $task->outside_service = $request->input('outside_service');
        $task->note = $request->input('document_note');
        $task->status = $validated['document_status'];
        $task->document_id = $data->id ?? null;
        $task->calendar = $calender;
        $task->save(); 

        $phase = CustomerPhaseStage::where('customer_id', '=', $request->input('customer_id'))->where('phase_id', '=', $request->input('phase_id'))->first();

        if(!$phase){
            $customer_phase = new CustomerPhaseStage;
            $customer_phase->customer_id = $request->input('customer_id');
            $customer_phase->phase_id = $request->input('phase_id');
            $customer_phase->status = "Complete";
            $customer_phase->save();

        }
    

        return redirect()->back()->with('save_msg', 'Task saved successfully!');

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    }
}


    

    public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='/task/documents/'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }

    /**
     * Display the specified resource.
     */
 public function show($id, $itemId)
    {
        // Fetch the list of product IDs associated with the customer
        $productList = DB::table('customer_product_lists')
            ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
            ->select('article_groups.id as product_id')
            ->where('customers.id', $id)
            ->pluck('product_id')
            ->toArray();

            
        // Retrieve the item details
        $item = DB::table('task_phases')
            ->join('phase_activities as p_active', 'p_active.phase_id','=', 'task_phases.id')
            ->where('task_phases.id', $itemId)
            ->select('task_phases.*', 'p_active.title', 'p_active.id as p_active_id', 'p_active.description', 'task_phases.product_id', 'task_phases.order')
            ->first();

 
 
        // Check if the item belongs to the customer's product list
        if ($item && in_array($item->product_id, $productList)) {
            return response()->json($item, 200);
        } else {
            return response()->json(['error' => 'Item not found or does not belong to the customer'], 404);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
 public function load($phase, $activities, $product, $customer)
{
    $taskToDos = DB::table('task_to_dos')
        ->join('customers', 'customers.id', '=', 'task_to_dos.customer_id')
        ->join('task_phases', 'task_phases.id', '=', 'task_to_dos.phase_id')
        ->join('article_groups', 'article_groups.id', 'task_to_dos.product_id')
        ->join('phase_activities', 'phase_activities.id', 'task_to_dos.activities_id')
        ->join('employees as contact', 'contact.id', '=', 'task_to_dos.contact_person')
        ->join('employees as responsible_person', 'responsible_person.id', '=', 'task_to_dos.responsible_person')
        ->leftJoin('employees as outside_service', 'outside_service.id', '=', 'task_to_dos.outside_service')
        ->leftJoin('task_documents', 'task_documents.id', '=', 'task_to_dos.document_id')
        ->select(
            'customers.name as cname', 'customers.lastname as clastname',
            'task_phases.phase_name', 'task_phases.id as phase_id',
            'article_groups.article_group', 'article_groups.id as product_id',
            'phase_activities.title as activity_title', 'phase_activities.id as activity_id',
            'task_to_dos.id as task_id', 'task_to_dos.done', 'task_to_dos.note', 'task_to_dos.status', 'task_to_dos.done_date',
            'contact.name as contact_name', 'contact.lastname as contact_lastname', 'contact.image as cimage',
            'responsible_person.name as responsible_name', 'responsible_person.lastname as responsible_lastname', 
            'responsible_person.image as rimage', 
            'outside_service.name as outside_name', 'outside_service.lastname as outside_lastname', 'outside_service.image as oimage',
            'task_documents.document_name', 'task_documents.document_sum','task_documents.document'
        )
        ->where('task_phases.id', $phase)
        ->where('phase_activities.id', $activities)
        ->where('article_groups.id', $product)
        ->where('customers.id', $customer)
        ->first();

    return response()->json($taskToDos);
}


    /**
     * Update the specified resource in storage.
     */
   public function loadTask(Request $request, $phase_id, $customer_id, $product_id, $activity_id)
{
    $data = DB::table('task_to_dos')
                ->join('customers', 'customers.id', '=', 'task_to_dos.customer_id')
                ->join('task_phases', 'task_phases.id', '=', 'task_to_dos.phase_id')
                ->join('article_groups', 'article_groups.id', '=', 'task_to_dos.product_id')
                ->join('phase_activities', 'phase_activities.id', '=', 'task_to_dos.activities_id')
                ->join('employees as contact', 'contact.id', '=', 'task_to_dos.contact_person')
                ->leftJoin('employees as responsible', 'responsible.id', '=', 'task_to_dos.responsible_person')
                ->leftJoin('employees as outside', 'outside.id', '=', 'task_to_dos.outside_service')
                ->leftJoin('task_documents', 'task_documents.id', '=', 'task_to_dos.document_id')
                ->select(
                    'customers.name as cname', 
                    'customers.lastname as clastname',
                    'article_groups.article_group', 
                    'task_phases.phase_name', 
                    'phase_activities.title', 
                    'phase_activities.description',
                    'contact.name as contact_name', 
                    'contact.lastname as contact_lastname', 
                    'contact.image as contact_image',
                    'responsible.name as rname', 
                    'responsible.lastname as rlastname', 
                    'responsible.image as rimage',
                    'outside.name as oname', 
                    'outside.lastname as olastname', 
                    'outside.image as oimage',
                    'task_documents.document_name', 
                    'task_documents.document_sum',
                    'task_documents.document',
                    'task_to_dos.*' 
                )
                ->where('task_to_dos.phase_id', $phase_id)
                ->where('task_to_dos.customer_id', $customer_id)
                ->where('task_to_dos.product_id', $product_id)
                ->where('task_to_dos.activities_id', $activity_id)
                ->first();

    return response()->json($data);
}

public function update(Request $request, $id)
{
  
    // Validate the incoming request
    $validated = $request->validate([
        'phase_id' => 'required|exists:task_phases,id',
        'product_id' => 'required|exists:article_groups,id',
        'activities_id' => 'required|exists:phase_activities,id',
        'done' => 'nullable|string',
        'done_date' => 'nullable|date',
        'contact_person' => 'nullable|exists:employees,id',
        'responsible_person' => 'nullable|exists:employees,id',
        'outside_service' => 'nullable|exists:employees,id',
        'document_note' => 'nullable|string',
        'calendar' => 'nullable|string',
        'document_status' => 'nullable|string',
        'document_name' => 'nullable|string|max:255',
        'document_sum' => 'nullable|numeric',
        'document' => 'nullable|file|mimes:pdf|max:10240',
    ]);

    // Determine the value of the calendar checkbox
    $calendar = $request->calendar ? "true" : "false";

    // Find the task by ID
    $task = TaskToDo::findOrFail($id);

 
        // If the task already has a document, update it; otherwise, create a new one
        if ($task->document_id) {
            $document = TaskDocument::find($task->document_id);
        } else {
            $document = new TaskDocument;
        }

        $document->customer_id = $request->input('customer_id');
        $document->phase_id = $validated['phase_id'];
        $document->product_id = $validated['product_id'];
        $document->activities_id = $validated['activities_id'];
    
        $document->document_name = $request->input('document_name');
        $document->document_sum = $request->input('document_sum');
        $document->document_note = $request->input('document_note') ?? null;
        $document->document_status = $request->input('document_status') ?? null;

        if ($request->hasFile('document')) {
            $doc_name = time() . '.' . $request->file('document')->getClientOriginalExtension();
            $document->document = $doc_name;
            $request->file('document')->move('task/documents/', $doc_name);
        }
        $document->save();
 
       
  

    // Update the task fields
    $task->customer_id = $request->input('customer_id');
    $task->phase_id = $validated['phase_id'];
    $task->product_id = $validated['product_id'];
    $task->activities_id = $validated['activities_id'];
    $task->done = $request->input('done') ? 'true' : 'false'; // Handle checkbox
    $task->done_date = $validated['done_date'];
    $task->contact_person = $validated['contact_person'];
    $task->responsible_person = $request->input('responsible_person');
    $task->outside_service = $request->input('outside_service');
    $task->note = $request->input('document_note');
    $task->status = $validated['document_status'];
    $task->calendar = $calendar;
    $task->save();

    return redirect()->back()->with('update_msg', 'Task updated successfully!');
}
 


public function checkStore(Request $request)
{
    Log::info("Request Data: ", $request->all());

    try {
        // Convert "null" strings to actual null values
        $request->merge([
            'outside_company' => $request->outside_company === 'null' ? null : $request->outside_company,
            'outside_service' => $request->outside_service === 'null' ? null : $request->outside_service,
        ]);

        // Validate the request
        $validated = $request->validate([
            'customer_id' => 'required|exists:new_leads,id',
            'product_id' => 'required|exists:article_groups,id',
            'alternative' => 'required|exists:lead_alternative_adds,id',
            'phase_id' => 'required|exists:task_phases,id',
            'project_id' => 'required|exists:projects,id',
            'activities_id' => 'required|exists:phase_activities,id',
            'sub_task_id' => 'nullable|exists:task_sub_tasks,id',
            'contact_person' => 'required|exists:employees,id',
            'responsible_person' => 'nullable|exists:employees,id',
            'outside_service' => 'nullable|exists:employees,id',
            'outside_company' => 'nullable|exists:external_personals,id',
            'outside_types' => 'nullable|string',
            'reason' => 'nullable|string',
            'done_status' => 'nullable|string',
            'work_progress' => 'nullable|integer|min:0|max:100',
            'more_time' => 'nullable|date_format:H:i',
            'type' => 'required|string|in:main,sub',
        ]);

        // Create or update the task
        $task = new TaskToDo();
        $task->customer_id = $validated['customer_id'];
        $task->product_id = $validated['product_id'];
        $task->alternative = $validated['alternative'];
        $task->phase_id = $validated['phase_id'];
        $task->project_id = $validated['project_id'];
        $task->activities_id = $validated['activities_id'];
        $task->sub_task_id = $validated['sub_task_id'] ?? null;
        $task->done_date = $validated['done_date'] ?? now();
        $task->contact_person = $validated['contact_person'];
        $task->responsible_person = $validated['responsible_person'] ?? null;
        $task->outside_service = $validated['outside_service'] ?? null;
        $task->outside_company = $validated['outside_company'] ?? null;
        $task->type = $validated['type'];
        $task->outside_type = $validated['outside_types'] ?? null;
        $task->reason = $validated['reason'] ?? null;
        $task->done_status = $validated['done_status'] ?? null;
        $task->work_progress = $validated['work_progress'] ?? null;
        $task->more_time = $validated['more_time'] ?? null;
        $task->done = 'true';
        $task->save();

        if ($request->last == 'true') {
            $main_task = $task->replicate(); // Replicate the task
            $main_task->type = 'main';       // Set type to 'main'
            $main_task->save();              // Save the replicated main task
        }



           // Send Notification
        $employee = DB::table('employees')->where('id', $validated['contact_person'])->first();
        $phaseName = DB::table('task_phases')->where('id', $validated['phase_id'])->value('phase_name');

        Notification::send(auth()->user(), new ProjectTaskNotification([
            'title' => 'Aufgabe erledigt',
            'message' => "Die Aufgabe aus der $phaseName-Phase wurde von {$employee->name} {$employee->lastname} als aktiv markiert.",
            'project_id' => $validated['project_id'],
            'phase_id' => $validated['phase_id'],
            'customer_id' => $validated['customer_id'],
            'alternative_id' => $request->input('alternative'),
            'activities_id' => $validated['activities_id'],
            'sub_task_id' => $validated['sub_task_id'],
            'product_id' => $validated['product_id'],
            'type' => $validated['type'],
        ]));


        return response()->json(['message' => 'Task created successfully'], 200);
    } catch (ValidationException $e) {
        Log::error('Validation Error: ', $e->errors());
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        Log::error('Error creating task: ', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json(['message' => 'An error occurred while creating the task.'], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = TaskToDo::find($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'The record deleted successfully!'); 
    }


public function details($user){
         
    $todayLeave = \Carbon\Carbon::parse(now())->isoFormat('YYYY.MM.DD'); 
    $data['data'] = DB::table('task_to_dos')
            ->where('contact_person', '=', $user)
            ->get();

    $data['customers'] = DB::table('projects')
        ->join('new_leads', 'new_leads.id', '=', 'projects.customer_id')
        ->join('article_groups', 'article_groups.id', '=', 'projects.product_id')
        ->join('lead_alternative_adds as alternative', 'alternative.id', '=', 'projects.alternative_id') 
        ->join('employees as emp', 'emp.id', '=', 'projects.employee_id') 
        ->select(
                'new_leads.id as lead_id', 'new_leads.customer_no' ,'new_leads.title','new_leads.name', 
                'new_leads.lastname', 'alternative.main', 'new_leads.phone', 
                'new_leads.telephone', 'new_leads.email','alternative.street', 
                'alternative.postcode', 'alternative.city', 'alternative.object_name', 
                'emp.name as emp_name', 'emp.lastname as emp_lastname', 'emp.image as emp_image', 'emp.gender',
                'article_groups.article_group', 'article_groups.initial', 'article_groups.image as product_image',
                'projects.*'       
                )
        ->whereNull('projects.deleted_at') 
        ->where('alternative.status', '!=', 'complete') 
        ->get();
    
    $data['employees']=DB::table('employees')->where('status', 'active')->get();
    $data['project_employees'] = DB::table('add_employee_to_projects')
                                    ->join('employees', 'employees.id', 'add_employee_to_projects.employee_id')
                                    ->select('add_employee_to_projects.*', 'employees.name','employees.lastname', 'employees.gender', 'employees.image')
                                    ->get();
    $data['phases'] = DB::table('customer_phase_lists')
        ->join('task_phases', 'task_phases.id', '=', 'customer_phase_lists.phase_id')
        ->leftJoin('task_to_dos', function($join) {
            $join->on('task_to_dos.phase_id', '=', 'customer_phase_lists.phase_id')
                ->on('task_to_dos.customer_id', '=', 'customer_phase_lists.customer')
                ->on('task_to_dos.product_id', '=', 'customer_phase_lists.product');
        })
        ->select(
            'task_phases.phase_name',
            'customer_phase_lists.customer',
            'customer_phase_lists.product',
            'customer_phase_lists.color',
            'customer_phase_lists.id',
            'customer_phase_lists.alternative',
            'customer_phase_lists.service',
            'task_phases.order', // Include order column
            DB::raw('IFNULL(task_to_dos.done, 0) as done')
        )
        ->distinct()
        ->orderBy('task_phases.order', 'asc')
        ->get();

 
  

   $data['tasks'] = DB::table('task_to_dos')
        ->join('new_leads', 'new_leads.id', '=', 'task_to_dos.customer_id')
        ->join('task_phases', 'task_phases.id', '=', 'task_to_dos.phase_id')
        ->join('phase_activities', 'phase_activities.id', '=', 'task_to_dos.activities_id')
        ->select('task_to_dos.*', 'phase_activities.title as task_title')
        ->where('task_to_dos.done', '=', 'true')
        ->where('task_to_dos.type', '=', 'main')
        ->whereIn('task_to_dos.id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('task_to_dos')
                ->where('done', '=', 'true')
                ->groupBy('customer_id');
        })
        ->get();


       

    $data['task_docs'] = DB::table('task_documents')
        ->leftJoin('task_to_dos', 'task_to_dos.customer_id', '=', 'task_documents.customer_id')
        ->join('employees as contact', 'contact.id', '=', 'task_to_dos.contact_person')
        ->leftJoin('employees as responsible', 'responsible.id', 'task_to_dos.responsible_person') 
        ->select('task_documents.id', 'task_documents.customer_id', 'task_documents.phase_id', 'task_documents.product_id', 'task_documents.activities_id', 
            'task_documents.document_name', 'task_documents.document', 'task_documents.document_sum', 
            'task_documents.document_note', 'task_documents.document_status', 'task_documents.created_at', 
            'task_documents.updated_at', 'contact.name as cname', 'contact.lastname as clastname', 
            'contact.image as cimage', 'responsible.name as rname', 'responsible.lastname as rlastname', 
            'responsible.image as rimage') 
        ->distinct('task_documents.document')
        ->get();

   
                
        $appointments = DB::table('activity_employees')
            ->join('appointments', 'appointments.id', '=', 'activity_employees.appointment_id')
            ->leftJoin('customers', 'customers.id', '=', 'appointments.customer_id')
            ->leftJoin('customer_alternative_adds as alt', 'alt.customer_id', '=', 'customers.id')
            ->join('task_phases', 'task_phases.id', '=', 'activity_employees.phase_id')
            ->join('phase_activities as act', 'act.id', '=', 'activity_employees.activity_id')
            ->join('task_sub_tasks as sub_tasks', 'sub_tasks.id', '=', 'activity_employees.sub_task_id')
            ->join('employees as emp', 'emp.id', '=', 'activity_employees.employee_id')
            ->join('article_groups', 'article_groups.id', '=', 'appointments.product_id')
            ->leftJoin('task_sub_tasks as subTask', 'subTask.id', '=', 'activity_employees.sub_task_id')
            ->leftJoin(
                DB::raw('(SELECT * FROM task_to_dos WHERE done != "true") as does'),
                'does.activities_id', '=', 'activity_employees.activity_id'
            )
            ->select(
                'activity_employees.employee_id',
                'activity_employees.appointment_id',
                'activity_employees.id as activity_employeee_id',
                'activity_employees.phase_id as phase',
                'activity_employees.activity_id as task_id',
                'activity_employees.sub_task_id as sub_task',
                'appointments.*',
                'customers.id as customer_id',
                'customers.name as customerName',
                'customers.lastname as customerLastname',
                'customers.title as ctitle',
                'alt.lat', 'alt.lon', 'alt.postcode', 'alt.street', 'alt.city', 'alt.address_no',
                'article_groups.article_group',
                'act.title as task_title',
                'act.description as task_description',
                'act.photo',
                'does.done',
                'task_phases.phase_name',
                'sub_tasks.id as sub_task_id', 
                'sub_tasks.task_title', 
                'sub_tasks.duration',
                'sub_tasks.duration_type', 
                'sub_tasks.description as sub_description',
                'sub_tasks.photo as sub_photo'
            )
            ->where('activity_employees.employee_id', '=', auth()->user()->name)
            ->orderBy('appointments.created_at', 'desc') // Sort by most recent
            ->get();

        $structuredAppointments = $appointments->groupBy('customer_id')->map(function ($customerGroup) {
            $firstCustomer = $customerGroup->first();
            return [
                'customer_id' => $firstCustomer->customer_id,
                'customerName' => $firstCustomer->customerName,
                'customerLastname' => $firstCustomer->customerLastname,
                'ctitle' => $firstCustomer->ctitle,
                'lat' => $firstCustomer->lat,
                'lon' => $firstCustomer->lon,
                'street' => $firstCustomer->street,
                'city' => $firstCustomer->city,
                'address_no' => $firstCustomer->address_no,
                'product' => $firstCustomer->article_group,
                'phases' => $customerGroup->groupBy('phase')->map(function ($phaseGroup, $phaseId) {
                    $firstPhase = $phaseGroup->first();
                    return [
                        'phase_id' => $phaseId,
                        'phase_name' => $firstPhase->phase_name,
                        'appointments' => $phaseGroup->map(function ($appointment) use ($phaseGroup) {
                            // Sub-tasks for the current task
                            $subTasks = $phaseGroup->where('task_id', $appointment->task_id)->map(function ($subTask) {
                                return [
                                    'sub_task_id'  => $subTask->sub_task_id,
                                    'phase_id' => $subTask->phase,
                                    'task_id' => $subTask->task_id,
                                    'task_title' => $subTask->task_title,
                                    'duration' => $subTask->duration,
                                    'duration_type' => $subTask->duration_type,
                                    'description' => $subTask->sub_description,
                                    'photo' => $subTask->sub_photo,
                                ];
                            })->values();

                            return [
                                'appointment_id' => $appointment->appointment_id,
                                'customer_id' => $appointment->customer_id,
                                'product_id' => $appointment->product_id,
                                'phase_id' => $appointment->phase_id,
                                'activity_id' => $appointment->task_id,
                                'postcode' => $appointment->postcode,
                                'title' => $appointment->title,
                                'description' => $appointment->description,
                                'priority' => $appointment->priority,
                                'color' => $appointment->color,
                                'start_date' => $appointment->start_date,
                                'end_date' => $appointment->end_date,
                                'report_date' => $appointment->report_date,
                                'report_date_type' => $appointment->report_date_type,
                                'start_time' => $appointment->start_time,
                                'end_time' => $appointment->end_time,
                                'task' => [
                                    'id' => $appointment->task_id,
                                    'phase_id' => $appointment->phase_id,
                                    'product_id' => $appointment->product_id,
                                    'employee_id' => $appointment->employee_id,
                                    'title' => $appointment->task_title,
                                    'description' => $appointment->task_description,
                                    'photo' => $appointment->photo,
                                    'sub_tasks' => $subTasks,
                                ],
                            ];
                        })->values(),
                    ];
                })->values(),
            ];
        })->values();
   
    $data['appointments'] = $structuredAppointments; 
 
  
      //return  $data['appointments'];
           

       
     
        $data['to_does'] = DB::table('task_to_dos')
            ->leftJoin('employees as responsible', 'responsible.id', 'task_to_dos.responsible_person') 
            ->leftJoin('employees as contact', 'contact.id', 'task_to_dos.contact_person') 
            ->leftJoin('employees as outside_s', 'outside_s.id', 'task_to_dos.outside_service') 
            ->leftJoin('external_personals as outside_c', 'outside_c.id', 'task_to_dos.outside_company') 
            ->select(
                'task_to_dos.*', 
                'task_to_dos.activities_id', // Make sure this field exists in task_to_dos
                'responsible.name as rname', 'responsible.lastname as rlastname', 'responsible.image as rimage',
                'contact.name as cname', 'contact.lastname as clastname', 'contact.image as cimage', 
                'outside_s.name as osname', 'outside_s.lastname as oslastname', 'outside_s.image as osimage', 
                'outside_c.admin_name', 'outside_c.company_name'
            )
            ->where('responsible.id', '=', auth()->user()->name) 
            ->orWhere('contact.id', '=', auth()->user()->name) 
            ->orWhere('outside_s.id', '=', auth()->user()->name) 
            ->get();


  
      
        return view('admin.todo.task_to_do.todo_list',$data);
      }

    public function getImage($customer_id, $phase_id, $task_id, $product_id, $sub_task_id = null) {
        // Start building the query
        $query = DB::table('images')
                    ->where('customer_id', $customer_id)
                    ->where('phase_id', $phase_id)
                    ->where('task_id', $task_id)
                    ->where('article_group', $product_id);
                    
        // Conditionally add `sub_task_id` if it is provided
        if ($sub_task_id) {
            $query->where('sub_task_id', $sub_task_id);
        }

        // Execute the query and get results
        $images = $query->orderBy('id', 'desc')->get();

        // Return the response as JSON
        return response()->json($images, 200);
    }

     public function updatePastAppointmentsToToday($user)
    {
        // Define today's date
        $todayLeave = \Carbon\Carbon::parse(now())->isoFormat('YYYY.MM.DD');

        // Retrieve past appointments for the given employee
        $oldAppointments = DB::table('activity_employees')
            ->join('appointments', 'appointments.id', '=', 'activity_employees.appointment_id')
            ->join('task_to_dos as do', 'do.activities_id', '=', 'activity_employees.activity_id')
            ->where('activity_employees.employee_id', '=', $user)
            ->whereDate('appointments.end_date', '<', $todayLeave)  
            ->where('do.done', '=', 'true')
            ->where('do.done_date', '!=', $todayLeave)
            ->select('appointments.id')  
            ->get();

       
        // Update each appointment's start date to today if it's in the past
        foreach ($oldAppointments as $appointment) {
            DB::table('appointments')
                ->where('id', $appointment->id)
                ->update(['start_date' => $todayLeave]);
        }

        // Return updated appointments grouped by customer
        return DB::table('activity_employees')
            ->join('appointments', 'appointments.id', '=', 'activity_employees.appointment_id')
            ->leftJoin('customers', 'customers.id', '=', 'appointments.customer_id')
            ->leftJoin('customer_alternative_adds as alt', 'alt.customer_id', '=', 'customers.id')
            ->join('phase_activities as act', 'act.id', '=', 'activity_employees.activity_id')
            ->join('task_to_dos as do', 'do.activities_id', '=', 'activity_employees.activity_id')
            ->join('employees as emp', 'emp.id', '=', 'activity_employees.employee_id')
            ->join('article_groups', 'article_groups.id', '=', 'appointments.product_id')
            ->select(
                'activity_employees.employee_id',
                'activity_employees.appointment_id',
                'activity_employees.id as activity_employeee_id',
                'activity_employees.phase_id as phase',
                'activity_employees.activity_id as task_id',
                'appointments.*',
                'customers.id as customer_id',
                'customers.name as customerName',
                'customers.lastname as customerLastname',
                'customers.title as ctitle',
                'alt.lat', 'alt.lon', 'alt.postcode', 'alt.street', 'alt.city', 'alt.address_no',
                'article_groups.article_group',
                'act.title as task_title',
                'act.description as task_description',
                'act.photo',
                'do.done'
            )
            ->where('activity_employees.employee_id', '=', $user)
            ->whereDate('appointments.start_date', '!=', $todayLeave)  
            ->get()
            ->groupBy('customer_id');
    }

    public function getEmployees($appointment_id, $phase_id, $activity_id){
        $data = DB::table('activity_employees')
                        ->join('employees' , 'employees.id', '=', 'activity_employees.employee_id')
                        ->select('employees.name', 'employees.lastname', 'employees.image'
                                , 'activity_employees.*'
                                )
                        ->where('activity_employees.appointment_id', $appointment_id)
                        ->where('activity_employees.phase_id', $phase_id)
                        ->where('activity_employees.activity_id', $activity_id)
                        ->get();
        Log::info($data);
            
             return response()->json($data, 200);
    }

   
public function getAppointmentEmployee(Request $request, $appointment_id)
{
    $existingEmployees = DB::table('activity_employees')
                            ->where('appointment_id', $appointment_id)
                            ->pluck('employee_id');

    $availableEmployees = DB::table('employees')
                            ->whereNotIn('id', $existingEmployees)
                            ->select('id', 'name', 'lastname', 'image')
                            ->get();

    return response()->json($availableEmployees, 200);
}
 public function addEmployee(Request $request)
{
    // Log the incoming data to verify it includes activity_id
    \Log::info('Received data', $request->all());

    $appointment_id = $request->appointment_id;
    $phase_id = $request->phase_id;
    $activity_id = $request->activity_id;
    $employeeIds = $request->employee;

    if (!$activity_id) {
        return response()->json('Activity ID is missing.', 400);
    }

    try {
        foreach ($employeeIds as $employee_id) {
            // Existing logic here
        }

        return response()->json('Employee(s) added successfully');
    } catch (\Exception $e) {
        \Log::error('Error adding employees: ' . $e->getMessage());
        return response()->json('Failed to add employees', 500);
    }
}

    public function getAppointment($appointment_id, $customer_id, $product){

        $data = DB::table('appointments')
                    ->join('customers', 'customers.id', '=', 'appointments.customer_id')
                    ->join('article_groups', 'article_groups.id', 'appointments.product_id')
                    ->select('appointments.*', 'customers.title', 'customers.name as cname', 'customers.lastname as clastname','article_groups.article_group')
                    ->where('appointments.id', $appointment_id)
                    ->where('appointments.customer_id', $customer_id)
                    ->where('appointments.product_id', $product)
                    ->get();

      return response()->json($data, 200);
    }


    public function project_management($project_id, $customer_id, $product, $alternative){


    $data['data'] = DB::table('projects')
                ->join('new_leads', 'new_leads.id', '=', 'projects.customer_id')
                ->join('lead_alternative_adds as alt', 'alt.id', '=', 'projects.alternative_id')
                ->join('article_groups', 'article_groups.id', '=', 'projects.product_id')
                ->join('employees', 'employees.id', '=', 'projects.employee_id')
                ->select('projects.*',
                        'new_leads.customer_no', 'new_leads.name', 'new_leads.lastname', 'new_leads.street', 'new_leads.postcode', 'new_leads.city',
                        'alt.street as alt_street', 'alt.postcode as alt_postcode', 'alt.city as alt_city', 'article_groups.article_group',
                        'employees.name as emp_name', 'employees.lastname as emp_lastname', 'employees.image as emp_image')
                ->where('projects.customer_id', $customer_id)
                ->where('projects.alternative_id', $alternative)
                ->where('projects.product_id', $product)
                ->where('projects.id', $project_id)
                ->first();

            // Check if $data['data'] is null before accessing its properties
            if ($data['data'] === null) {
                return back()->withErrors('No matching project found.');
            }

 
                $data['comments_list'] = DB::table('project_task_comments')
                    ->join('employees', 'employees.id', '=', 'project_task_comments.comment_by')
                    ->select(
                        'project_task_comments.*',
                        'employees.name',
                        'employees.lastname',
                        'employees.gender',
                        'employees.image'
                    )
                    ->where('project_task_comments.project_id', $project_id) 
                    ->get();

        
                $data['comments_count'] = $data['comments_list']->count();

    $data['phases'] = TaskPhase::with('articleGroup') 
                    ->join('customer_phase_lists', 'customer_phase_lists.phase_id', '=', 'task_phases.id')
                    ->where('customer_phase_lists.customer', $customer_id)
                    ->where('product_id', $product) 
                    ->select('task_phases.*')
                    ->orderBy('order', 'asc')
                    ->get();

    
    $data['stages'] = DB::table('customer_phase_stages')   
                        ->join('customers', 'customers.id', '=', 'customer_phase_stages.customer_id')           
                        ->join('task_phases', 'task_phases.id', '=', 'customer_phase_stages.phase_id') 
                        ->where('customer_phase_stages.customer_id', $customer_id)
                        ->where('task_phases.product_id', $product) 
                        ->select('customer_phase_stages.*')
                        ->get(); 

    $data['task_docs'] = DB::table('task_documents')
        ->leftJoin('task_to_dos', 'task_to_dos.customer_id', '=', 'task_documents.customer_id')
        ->join('employees as contact', 'contact.id', '=', 'task_to_dos.contact_person')
        ->leftJoin('employees as responsible', 'responsible.id', 'task_to_dos.responsible_person') 
        ->select('task_documents.id', 'task_documents.customer_id', 'task_documents.phase_id', 'task_documents.product_id', 'task_documents.activities_id', 
            'task_documents.document_name', 'task_documents.document', 'task_documents.document_sum', 
            'task_documents.document_note', 'task_documents.document_status', 'task_documents.created_at', 
            'task_documents.updated_at', 'contact.name as cname', 'contact.lastname as clastname', 
            'contact.image as cimage', 'responsible.name as rname', 'responsible.lastname as rlastname', 
            'responsible.image as rimage')
        ->where('task_documents.customer_id', $customer_id)
        ->where('task_documents.product_id', $product)
        ->distinct('task_documents.document')
        ->get();

 
        $data['employees'] = DB::table('employees')->where('status', 'Active')->get();
        $data['outside'] = DB::table('external_personals')->where('status', '=', 'Published')->get();
        $data['tasks'] = DB::table('phase_activities')->where('product_id', $product)->get(); 
        
        
        $data['project_employees'] = DB::table('add_employee_to_projects')
                                    ->join('employees', 'employees.id', 'add_employee_to_projects.employee_id')
                                    ->select('add_employee_to_projects.*', 'employees.name','employees.lastname', 'employees.gender', 'employees.image')
                                    ->get();
                                    
        $data['current_user'] = DB::table('employees')->where('id','=', auth()->user()->name)->select('id', 'name', 'lastname', 'image')->first();                        
        $data['to_does'] = DB::table('task_to_dos')
                ->leftJoin('employees as responsible', 'responsible.id', 'task_to_dos.responsible_person') 
                ->leftJoin('employees as contact', 'contact.id', 'task_to_dos.contact_person') 
                ->leftJoin('employees as outside_s', 'outside_s.id', 'task_to_dos.outside_service') 
                ->leftJoin('external_personals as outside_c', 'outside_c.id', 'task_to_dos.outside_company') 
                ->where('task_to_dos.customer_id', '=', $customer_id)
                ->where('task_to_dos.alternative', '=', $alternative)
                ->select('task_to_dos.*', 'responsible.name as rname', 'responsible.lastname as rlastname', 'responsible.image as rimage',
                            'contact.name as cname', 'contact.lastname as clastname', 'contact.image as cimage', 
                            'outside_s.name as osname', 'outside_s.lastname as oslastname', 'outside_s.image as osimage', 
                            'outside_c.admin_name', 'outside_c.company_name' 
                            
                        )
                ->get();

        return view('admin.todo.task_to_do.project', $data)->with([
            'customer_data' =>  $customer_id,
            'alternative_data'   =>  $alternative,
            'product_data'   =>  $product,

        ]);

    }
    

}
