<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use App\Models\CustomProcess;
use Illuminate\Http\Request;
use DB;

class CustomProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = CustomProcess::where('employee_id', auth()->user()->name)->get();

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
        try {
            $request->validate([
                'stage_name' => 'required'
            ]);

            $data = CustomProcess::create([
                'employee_id' => auth()->user()->name, // Use ID instead of name
                'stage_name' => $request->stage_name,
                'status' => 'new'
            ]);

            return response()->json(['success' => true, 'message' => 'Die neue Bühne ist erfolgreich erstellt']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(CustomProcess $customProcess)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomProcess $customProcess)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomProcess $customProcess)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // 🔹 Ensure ID is numeric
        if (!is_numeric($id)) {
            return response()->json(['success' => false, 'message' => 'Invalid stage ID.'], 400);
        }

        $stage = CustomProcess::find($id);

        if (!$stage) {
            return response()->json(['success' => false, 'message' => 'Stage not found.'], 404);
        }

        // 🔹 Check if any leads are assigned to this stage
        $leads = DB::table('lead_product_lists')
            ->where('status', $stage->stage_name)
            ->get();

        if ($leads->count() > 0) {
            // 🔹 Move all leads to the "Unknown" stage
            DB::table('lead_product_lists')
                ->where('status', $stage->stage_name)
                ->update(['status' => 'unknown']);

            // 🔹 Ensure the "Unknown" stage exists
            $unknownStage = CustomProcess::where('stage_name', 'unknown')->first();
            if (!$unknownStage) {
                CustomProcess::create([
                    'employee_id' => auth()->user()->id,
                    'stage_name' => 'unknown',
                    'status' => 'new'
                ]);
            }
        }

        // 🔹 Delete the stage after moving leads
        $stage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stage deleted successfully. Leads moved to "Unknown" stage if needed.'
        ]);
    }


}
