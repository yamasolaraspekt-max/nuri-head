<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\EmployeeCloth;
use Illuminate\Http\Request;
use Redirect;

class EmployeeClothController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
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
        $request->validateWithBag('clothForm', [
            'cloth.*.type'   => 'required',
            'cloth.*.size'   => 'required',
            'emp_id'         => 'required|exists:employees,id'
        ],
        [
            'cloth.*.type' => 'Typ: Das Feld ist erforderlich',
            'cloth.*.size' => 'Größe: Das Feld ist erforderlich',
        ]);

        foreach ($request->cloth as $clothItem) {
            EmployeeCloth::create([
                'emp_id' => $request->emp_id,  // Ensure emp_id is saved
                'type'   => $clothItem['type'],
                'size'   => $clothItem['size']
            ]);
        }

        return back()->with('save_msg', 'Die Kleidung wurde erfolgreich hinzugefügt')
                    ->withInput($request->all())
                    ->with('active_tab', 'cloth');
    }


    /**
     * Display the specified resource.
     */
    public function show(EmployeeCloth $employeeCloth)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeCloth $employeeCloth)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request )
    {
        $request->validate([
            'type'  =>  'required|string',
            'size'  =>  'required|string'
        ]);
        $id=$request->id;
        $data = EmployeeCloth::find($id);
        $data->type=$request->type;
        $data->size=$request->size;
        $data->status="Published";
        $data->save();
        return Redirect::route('emp.next',['id'=>$request->emp_id])->with('data', $data)->with('save_msg', 'Kleidung wird aktualisiert')->withInput($request->all())
                 ->with('active_tab', 'cloth');

    }

    /**
     * Remove the specified resource from storage.
     */
  public function destroy($id)
{
    $data = EmployeeCloth::find($id);
    if ($data) {
        $data->delete();
        return redirect()->back()->with('delete_msg', 'Kleidung wird gelöscht')->withInput()
                 ->with('active_tab', 'cloth');
    } else {
        return redirect()->back()->with('delete_msg', 'Kleidung nicht gefunden')->withInput()
                 ->with('active_tab', 'cloth');
    }
}
}
