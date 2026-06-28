<?php

namespace App\Http\Controllers;

use App\Models\GroupSet;
use App\Models\ProductMasterSet;
use Illuminate\Http\Request;
use DB;
class GroupSetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {
        $search = request()->query('search');

        if($search){
            $data['masters']=ProductMasterSet::all();
            $data['data'] = DB::table('group_sets')
                        ->where('group_set', 'like', "%$search")
                        ->select('*')
                        ->paginate(20);
            $data['sets']=DB::table('group_set_product_master_set')
                                    ->join('group_sets', 'group_sets.id', '=', 'group_set_product_master_set.group_set_id')
                                    ->join('product_master_sets as master', 'master.id', '=', 'group_set_product_master_set.product_master_set_id')
                                    ->select('master.setname', 'group_set_product_master_set.*', 
                                            'master.phase_id', 'master.price', 'master.employee_price', 'master.employee_percent', 'master.material_price', 'master.material_percent' , 'master.id as master_id',   
                                            )
                                    ->get();

            return view('admin.offer.group.group', $data);
        }
        else {
            $data['masters']=ProductMasterSet::all();

            $data['sets']=DB::table('group_set_product_master_set')
            ->join('group_sets', 'group_sets.id', '=', 'group_set_product_master_set.group_set_id')
                 ->join('product_master_sets as master', 'master.id', '=', 'group_set_product_master_set.product_master_set_id')
            ->select('master.setname', 'group_set_product_master_set.*', 
                                            'master.price', 'master.employee_price', 'master.employee_percent', 'master.material_price', 'master.material_percent' , 'master.id as master_id', 'master.phase_id'   
                                            )
            ->get();
            $data['data'] = DB::table('group_sets')
            ->select('*')
            ->paginate(20);

            return view('admin.offer.group.group', $data);
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
        $request->validate([
            'group_set' => 'required', 
            'master_set' => 'required|array',
        ]);
        

     $master = DB::table('product_master_sets')
    ->whereIn('id', $request->master_set) 
    ->get();

        // Summing the prices and percentages
        $totalPrice = $master->sum('price'); 
        $totalMaterialPrice = $master->sum('material_price');
        $totalEmployeePrice = $master->sum('employee_price');
        $totalMaterialPercent = $master->sum('material_percent');
        $totalEmployeePercent = $master->sum('employee_percent');

        // Calculate the percentage of the material and employee
        $materialPercentageOfTotal = ($totalMaterialPrice / $totalPrice) * 100;
        $employeePercentageOfTotal = ($totalEmployeePrice / $totalPrice) * 100;

        
        $data = new GroupSet;
        $data->group_set = $request->group_set;
        $data->content = $request->content;
        $data->status = "Published";
        $data->total = $totalPrice;
        $data->employee_price = $totalEmployeePrice;
        $data->material_price = $totalMaterialPercent;
        $data->employee_percent = $employeePercentageOfTotal;
        $data->material_percent = $materialPercentageOfTotal;

        $data->save();
        
       
        $data->master()->sync($request->master_set, false);
        
        return redirect()->back()->with('save-msg', 'The record saved successfully');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(GroupSet $groupSet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GroupSet $groupSet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GroupSet $groupSet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = GroupSet::find($id);
        $data->delete();
        return redirect()->back()->with('delete_msg', 'The record deleted successfully');
    }
}
