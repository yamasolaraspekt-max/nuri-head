<?php

namespace App\Http\Controllers\Employee\Position;
use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\Qualification;
use Illuminate\Http\Request;
use Redirect;

class QualificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
public function emp_qualification(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'qual.*.degree' => 'required',
        'qual.*.major' => 'required',
        'qual.*.institution' => 'required',
        'qual.*.q_start_year' => 'required|date',
        'qual.*.q_end_year' => 'required|date',
    ], [
        'qual.*.degree.required' => 'Abschluss des Mitarbeiters erforderlich',
        'qual.*.major.required' => 'Hauptfach des Mitarbeiters erforderlich',
        'qual.*.institution.required' => 'Institution des Mitarbeiters erforderlich',
        'qual.*.q_start_year.required' => 'Startjahr erforderlich',
        'qual.*.q_end_year.required' => 'Abschlussjahr erforderlich',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()->toArray()
        ]);
    }

    foreach ($request->qual as $qualification) {
        Qualification::create($qualification);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Die Qualifikation wurde erfolgreich hinzugefügt'
    ]);
}

 

     

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'degree' => 'required',
            'major' => 'required',
            'institution' => 'required',
            'q_start_year' => 'required|date',
            'q_end_year' => 'required|date',
        ], [
            'degree.required' => 'Abschluss des Mitarbeiters erforderlich',
            'major.required' => 'Hauptfach des Mitarbeiters erforderlich',
            'institution.required' => 'Institution des Mitarbeiters erforderlich',
            'q_start_year.required' => 'Startjahr erforderlich',
            'q_end_year.required' => 'Abschlussjahr erforderlich',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        $data = Qualification::find($request->id);
        $data->emp_id = $request->emp_id;
        $data->degree = $request->degree;
        $data->major = $request->major;
        $data->institution = $request->institution;
        $data->q_start_year = $request->q_start_year;
        $data->q_end_year = $request->q_end_year;
        $data->grade = $request->grade;
        $data->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Die Qualifikationsinformationen wurden erfolgreich aktualisiert'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $education = FurtherEducation::find($id);

        if ($education) {
            $education->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Der Eintrag wurde erfolgreich gelöscht'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Eintrag nicht gefunden'
            ]);
        }
    }

}
