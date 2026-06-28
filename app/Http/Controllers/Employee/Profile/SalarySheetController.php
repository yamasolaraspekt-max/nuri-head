<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\SalarySheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
class SalarySheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $data = DB::table('salary_sheets')
                        ->join('employees', 'employees.id', '=', 'salary_sheets.emp_id')
                        ->select('salary_sheets.*', 'employees.name', 'employees.lastname', 'employees.image')
                        ->where('salary_sheets.emp_id', '=', $id)
                        ->paginate(20);
                return view('admin.employee.salary.employee_salary')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function salary($id)
    {
     
        $holiday_table = DB::table('holidays')
                            ->where('status', '=', 'Published')
                            ->select('holiday')
                            ->value('holiday');
        if ($holiday_table == null) {
            return redirect()->back()->with('delete_msg', 'Please configure the holidays first');
        }
    
        $employees = DB::table('employees')
                        ->where('status', '=', 'Active')
                        ->where('id', $id)
                        ->first();
        
        // Working Hour
        $per_week = DB::table('employees')
                        ->select('id', 'working_hour', 'salary_per_hour', 'leave', 'remaining_day', 'sick_leave', 'sick_leave_remaining')
                        ->where('employees.status', '=', 'Active')
                        ->where('id', $id)
                        ->first();

        $per_week_value = $per_week->working_hour;
        $per_week_value = $per_week->working_hour;
        $wege_per_hour = $per_week->salary_per_hour;
        $per_day = $per_week_value / 5;
        $per_year = $per_week_value * 52;

        //Calcualte if the leave/Vocation is remaining or not
        if($per_week->leave == $per_week->remaining_day){
            $holiday_hour = $per_week->remaining_day * $per_day;
        }
        else{
            $holiday_remaining = $per_week->leave - $per_week->remaining_day;
            $holiday_hour = $holiday_remaining * $per_day;
        }


        //Calcualte if the sick leave remaining or not
        if($per_week->sick_leave == $per_week->sick_leave_remaining){
            $sick_leave_hour_t = $per_week->sick_leave_remaining * $per_day;
        }
        else{
            $sick_day_remaining = $per_week->sick_leave - $per_week->sick_leave_remaining;
            $sick_leave_hour_t = $sick_day_remaining * $per_day;
        }

        $sick_leave = $per_week->sick_leave;
        $sick_leave_hour = $sick_leave_hour_t * $per_day;
        $wege_monthly = $per_year * $wege_per_hour / 12; // Assuming wege_per_hour is constant for all employees
        $health_insurance = $sick_leave_hour * $wege_per_hour * 0.65;
        $shared_wege = $sick_leave_hour * $wege_per_hour * 0.35;
        $wege_yearly = $per_year * $wege_per_hour;
        $public_holiday_hour = $holiday_table * $per_day;
        $remaining_working_hour = $per_year - $holiday_hour - $sick_leave_hour - $public_holiday_hour;
        $unproductive_working_hour = 21 * $per_day;
        $productive_hour = $remaining_working_hour - $unproductive_working_hour;
        $additional_cost_monthly = $per_year * ($wege_per_hour * 0.1998) / 12;
        $additional_cost_yearly = $per_year * ($wege_per_hour * 0.1998);
        $gross_salary = $per_year * ($wege_per_hour + $wege_per_hour * 0.1998);
        $productive_hour_wege = $gross_salary/ $productive_hour ;
        $total_monthly_salary = $gross_salary / 12;

        $this_month = Carbon::now()->isoFormat('MMM');
        $this_year = Carbon::now()->isoFormat('YYYY');
        

        $duplicate = DB::table('salary_sheets')->where('emp_id', '=', $id)->where('salary_month', '=', $this_month )->where('salary_year', '=', $this_year)->first();
      
        if($duplicate){
            return redirect()->back()->with('delete_msg', 'The record is duplicated');
        }
        else{
            SalarySheet::create([
                'emp_id'                    => $id,
                'per_day'                   => $per_day,
                'per_week'                  => $per_week_value,
                'per_year'                  => $per_year,
                'holiday'                   => $holiday_table,
                'holiday_hour'              => $holiday_hour,
                'sick_leave'                => $sick_leave,
                'sick_leave_hour'          => $sick_leave_hour,
                'health_insurance'          => $health_insurance,
                'shared_wage'               => $shared_wege,
                'public_holiday'            => $holiday_hour,
                'public_holiday_hour'       => $public_holiday_hour,
                'remaining_working_hour'    => $remaining_working_hour,
                'unproductive_working_day'  => 21,
                'unproductive_working_hour' => $unproductive_working_hour,
                'productive_hour'           => $productive_hour,
                'wege_per_hour'             => $wege_per_hour,
                'monthly_salary'            => $wege_monthly,
                'labor_cost_hour'           => $wege_yearly,
                'additional_cost'           => $wege_per_hour * 0.1998,
                'additional_cost_monthly'   => $additional_cost_monthly,
                'additional_cost_yearly'    => $additional_cost_yearly,
                'plus_additional_wage_cost' => $wege_per_hour + $wege_per_hour * 0.1998,
                'gross_salary'              => $gross_salary,
                'productive_hour_wege'      => $productive_hour_wege,
                'total_monthly_salary'      => $total_monthly_salary,
                'status'                    => 'Published',
                'salary_month'                    => $this_month,
                'salary_year'                    => $this_year,
            ]);
        }
        return redirect()->to('salary_sheet/'.$id)->with('save_msg', 'The record updated');
    }
}
