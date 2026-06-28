<?php

namespace App\Http\Controllers\Employee\Profile;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeePostcodeList;

class EmployeePostcodeListController extends Controller
{
    public function __construct(){ 
        $this->middleware('auth');
    }

 
    public function index(Request $request, $employee)
    {
        $search = $request->input('search');

        $lists = EmployeePostcodeList::where('employee_id', $employee)
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('postcode_from', 'like', "%$search%")
                    ->orWhere('postcode_to', 'like', "%$search%")
                    ->orWhere('country', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return view('admin.employee.employee.create.location_data', compact('lists'))->render();
        }

        return view('admin.employee.employee.create.index', compact('lists'));
    }


   public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'postcode_from' => 'required|string|max:2',
            'postcode_to' => 'nullable|string|max:2',
            'country' => 'nullable|string|max:255',
        ]);

        if (empty($data['postcode_to'])) {
            $data['postcode_to'] = $data['postcode_from'];
        }

        // Correct validation: allow equal
        if ((int) $data['postcode_from'] > (int) $data['postcode_to']) {
            return response()->json([
                'error' => 'Postleitzahl "Von" muss kleiner oder gleich "Bis" sein.'
            ], 422);
        }

        // Prevent duplicate
        $exists = EmployeePostcodeList::where('employee_id', $data['employee_id'])
                    ->where('postcode_from', $data['postcode_from'])
                    ->where('postcode_to', $data['postcode_to'])
                    ->where('country', $data['country'])
                    ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'Ein gleicher Postleitzahlenbereich existiert bereits für diesen Mitarbeiter.'
            ], 422);
        }

        $record = EmployeePostcodeList::create($data);

        return response()->json([
            'success' => 'Postleitzahl erfolgreich erstellt.',
            'id' => $record->id
        ]);
    }



    public function edit($id)
    {
        $list = EmployeePostcodeList::findOrFail($id);
        return response()->json($list);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'postcode_from' => 'required|string|max:2',
            'postcode_to' => 'nullable|string|max:2',
            'country' => 'nullable|string|max:255',
            'employee_id' => 'required|exists:employees,id',

        ]);

        if (empty($data['postcode_to'])) {
            $data['postcode_to'] = $data['postcode_from'];
        }

        $list = EmployeePostcodeList::findOrFail($id);
        $list->update($data);

        return response()->json(['success' => 'Postcode updated successfully.']);
    }

    public function destroy($id)
    {
        $list = EmployeePostcodeList::findOrFail($id);
        $list->delete();

        return response()->json(['success' => 'Postcode deleted successfully.']);
    }
}
