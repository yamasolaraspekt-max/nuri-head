<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\LeaveDay;
use Illuminate\Http\Request;
use DB;

class LeaveDayController extends Controller
{
    public function __construct(){
        // MASTER-01 P1-IDOR: HR-Rollen-Gate (permission:Employee), enforced mit heutigen user_rolls-Grants
        $this->middleware('permission:Employee,add')->only(['store']);
        $this->middleware('permission:Employee,update')->only(['update', 'active', 'deactive']);
        $this->middleware('permission:Employee,delete')->only(['destroy']);
        $this->middleware('auth');
    }
     public function index()
    {
        $search=request()->query('search');
        
        if($search){
            $data=DB::table('leave_days')
                    ->where('year', 'LIKE', "%$search%")
                    ->select('leave_days.*')
                    ->orderby('year', 'desc')
                    ->paginate(10);
                    return view('admin.employee.holiday.leave_day')->with('data', $data);
        }
        else{
            $data=DB::table('leave_days')
            ->select('leave_days.*')
            ->orderby('year', 'desc')

            ->paginate(10);
            return view('admin.employee.holiday.leave_day')->with('data', $data);

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $this->validate($request,[
            'year'=>'required|unique:leave_days',
            'leave_day'=>'required',
         ]);

       $data=new LeaveDay;
       $data->year=$request->year;
       $data->leave_day=$request->leave_day;
       $data->status="Not published";
       $data->save();
        return back()->with('save_msg', 'Die Frietags wurde erfolgreich hinzugefügt');
    }

    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request,[
            'year'=>'required',
            'leave_day'=>'required',
         ]);
         $id=request()->input('id');
       $data=LeaveDay::find($id);
       $data->year=$request->year;
       $data->leave_day=$request->leave_day;
       // FIX P2-32: Status beim Bearbeiten NICHT hart auf "Not published" zuruecksetzen.
       // Sonst verliert ein aktives (Published) Urlaubsjahr beim Bearbeiten still seinen
       // Status. Aktivieren/Deaktivieren erfolgt bewusst ueber active()/deactive().
       $data->save();
        return back()->with('save_msg', 'Die Frietags wurde erfolgreich hinzugefügt');

    }

    public function active($id){
        $duplicate = LeaveDay::where('status', '=', 'Published')->first();

        if($duplicate==NUll){
            LeaveDay::find($id)->update(['status' => 'Published']);

        }
        else{
            return redirect()->back()->with('delete_msg', 'The record is already activated');
           
        }
        return redirect()->to('leave_day_view')->with('save_msg', 'The record activated successfully');
    }

    public function deactive($id){
    
            LeaveDay::find($id)->update(['status' => 'Unpublished']);
      
        return redirect()->to('leave_day_view')->with('save_msg', 'The record deactivated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=LeaveDay::find($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'Frietags erfolgreich gelöscht');
    }

   
}
