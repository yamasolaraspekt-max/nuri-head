<?php

namespace App\Http\Controllers\Phase;
use App\Http\Controllers\Controller;

use App\Models\TaskSubTask;
use Illuminate\Http\Request;
use DB;

class TaskSubTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index($task_id,$phase_id )
    {
        $search = request()->query('search');
        if($search) {
            $data = DB::table('task_sub_tasks')
                    ->join('phase_activities', 'phase_activities.id','=', 'task_sub_tasks.task_id')
                    ->join('task_phases', 'task_phases.id', '=', 'task_sub_tasks.phase_id')
                    ->select('task_sub_tasks.*', 'phase_activities.title', 'task_phases.phase_name')
                   ->where('task_phases.id', $phase_id)
                    ->where('phase_activities.id', $task_id) 
                    ->where('task_sub_tasks.task_title' , 'LIKE', "%$search%")
                    ->paginate(30);

        return view('admin.task.sub_task.sub_task')->with('data', $data);
        }
        else{
                $data = DB::table('task_sub_tasks')
                    ->join('phase_activities', 'phase_activities.id','=', 'task_sub_tasks.task_id')
                    ->join('task_phases', 'task_phases.id', '=', 'task_sub_tasks.phase_id')
                    ->select('task_sub_tasks.*', 'phase_activities.title', 'task_phases.phase_name')
                     ->where('task_phases.id', $phase_id)
                    ->where('phase_activities.id', $task_id) 
                    ->paginate(30);

        return view('admin.task.sub_task.sub_task')->with('data', $data);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(Request $request)
{
  
    $validate = $request->validate(
        [
            'task_title' => 'required|string',
            'description' => 'required|string',
            'duration' => 'required|integer',
            'duration_type' => 'required|string', 
            'photo' => 'nullable|string', 
            'phase_id' =>   'required|exists:task_phases,id',
            'task_id'  =>   'required|exists:phase_activities,id', // Ensure task_id exists
            'status'    =>  'nullable|string',
            'answered_by'   =>  'required|integer'

        ]
    );

    if($validate){
        // Debugging: Log the request data
        \Log::info('Validated Data:', $request->all());

        $data = TaskSubTask::create($request->all());
        return redirect()->back()->with('save_msg', 'Der Datensatz wird erfulgriech gespeichert');
    } else {
        \Log::info('Validation Failed:', $validate);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(TaskSubTask $taskSubTask)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskSubTask $taskSubTask)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskSubTask $taskSubTask)
    {
   
         $validate = $request->validate(
        [
            'task_title' => 'required|string',
            'description' => 'required|string',
            'duration' => 'required|integer',
            'duration_type' => 'required|string', 
            'photo' => 'nullable|string', 
            'phase_id' =>   'required|exists:task_phases,id',
            'task_id'  =>   'required|exists:phase_activities,id', 
            'status'    =>  'nullable|string',
            'id'        =>  'required|exists:task_sub_tasks,id',
           'answered_by'   =>  'required|integer'

        ]
    );

    if($validate){ 

        $data = TaskSubTask::find($validate['id'])->update($request->all());
        return redirect()->back()->with('save_msg', 'Der Datensatz wird erfulgriech gespeichert');
    } else {
        \Log::info('Validation Failed:', $validate);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = TaskSubTask::find($id);
        $data->delete();
        return redirect()->back()->with('delete_msg', 'Deleted Sucessfully');
    }
}
