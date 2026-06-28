<?php
namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use App\Models\DailyReportWorkPlace;
use App\Models\Branch;
use App\Models\BranchAddress;
use Illuminate\Http\Request; 

use DB;

class DailyReportWorkPlaceController extends Controller
{
    public function index()
    {
        $places = DailyReportWorkPlace::latest()->get();
        $branches = DB::table('branches')
                            ->join('branch_addresses', 'branch_addresses.branch_id', 'branches.id')
                            ->select('branches.branch', 'branch_addresses.name', 'branch_addresses.id', 'branch_addresses.full_address', 'branch_addresses.latitude', 'branch_addresses.longitude')
                            ->get();
        return view('admin.daily_report.work_place.work_place')->with([
            'places'    =>  $places,
            'branches'    =>  $branches,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        DailyReportWorkPlace::create($data);
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $place = DailyReportWorkPlace::findOrFail($id);
        return response()->json($place);
    }

    public function update(Request $request, $id)
    {
        $place = DailyReportWorkPlace::findOrFail($id);
        $place->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        DailyReportWorkPlace::destroy($id);
        return response()->json(['success' => true]);
    }

    public function getBranchAddress($branchId)
    {
        $address = BranchAddress::where('branch_id', $branchId)->first();
        return response()->json($address);
    }

public function getReport()
{
    return DailyReportWorkPlace::select('id', 'place_name', 'type')->get();
}
}

