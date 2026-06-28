<?php

namespace App\Http\Controllers;

use App\Models\AppointmentComment;
use Illuminate\Http\Request;
use DB;
class AppointmentCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index($id)
     {
        $data = DB::table('appointment_comments')
                        ->join('employees', 'employees.id', '=', 'appointment_comments.comment_by')
                        ->select('appointment_comments.*', 'employees.name', 'employees.lastname', 'employees.gender', 'employees.image')
                        ->where('appointment_comments.appointment_id', $id)
                        ->get();
 

     return response()->json($data, 200);
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
         
        $validate = $request->validate([
            'appointment_id'   =>  'required|exists:main_appointments,id',
            'comment'   =>  'required|string', 
        ]);

        $data = new AppointmentComment;
        $data->appointment_id = $request->appointment_id;
        $data->comment_by = auth()->user()->name;
        $data->comment = $request->comment;
        $data->status = 'Published';
        $data->save();
 
        return response()->json(['success' => true, 'message' => 'Comment saved']);
    }

     public function reply(Request $request)
    {
        $validate = $request->validate([
            'appointment_id'   =>  'required|exists:main_appointments,id',
            'parent_id'   =>  'required|exists:appointment_comments,id',
            'comment'   =>  'required|string', 
        ]);

        $data = new AppointmentComment;
        $data->appointment_id = $request->appointment_id;
        $data->comment_by = auth()->user()->name;
        $data->parent_id = $request->parent_id;
        $data->comment = $request->comment;
        $data->status = 'Published';
        $data->save();
 
        return response()->json(['success' => true, 'message' => 'Comment saved']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AppointmentComment $appointmentComment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppointmentComment $appointmentComment)
    {
        //
    }
}
