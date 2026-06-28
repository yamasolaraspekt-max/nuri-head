<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\FurtherEducation;
use Illuminate\Http\Request;

class FurtherEducationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct(){
        $this->middleware('auth');
    }
 

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'fe.*.course' => 'required',
            'fe.*.major' => 'required',
            'fe.*.institution' => 'required',
            'fe.*.year' => 'required|date',
        ], [
            'fe.*.course.required' => 'Mitarbeiterabschluss ist erforderlich',
            'fe.*.major.required' => 'Mitarbeiter-Major ist erforderlich',
            'fe.*.institution.required' => 'Angestellte Institution ist erforderlich',
            'fe.*.year.required' => 'Jahr des Abschlusses ist erforderlich',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        foreach ($request->fe as $value) {
            FurtherEducation::create($value);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Die Mitarbeiterabschluss wurde erfolgreich hinzugefügt'
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(FurtherEducation $furtherEducation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FurtherEducation $furtherEducation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'course' => 'required',
            'major' => 'required',
            'institution' => 'required',
            'year' => 'required|date',
            'skill' => 'required',
            'description' => 'required',
        ], [
            'course.required' => 'Kurs ist erforderlich',
            'major.required' => 'Wesentlich ist erforderlich',
            'institution.required' => 'Institution ist erforderlich',
            'year.required' => 'Jahr ist erforderlich',
            'skill.required' => 'Fähigkeiten sind erforderlich',
            'description.required' => 'Beschreibung ist erforderlich',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        $data = FurtherEducation::find($request->id);
        $data->emp_id = $request->emp_id;
        $data->course = $request->course;
        $data->major = $request->major;
        $data->institution = $request->institution;
        $data->year = $request->year;
        $data->skill = $request->skill;
        $data->description = $request->description;
        $data->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Die Mitarbeiterabschluss wurden erfolgreich aktualisiert'
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
