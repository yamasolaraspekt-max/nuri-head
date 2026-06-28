<?php

namespace App\Http\Controllers\Product\PV;
use App\Http\Controllers\Controller;

use App\Models\BackupGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
class BackupGeneratorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function __construct(){
        $this->middleware('auth');
    }
     public function load(Request $request)
    {
        $product_id = $request->input('product_id');
        $article_group_id = $request->input('article_group_id');

        $data = BackupGenerator::where('product_id', $product_id)
                        ->where('article_group_id', $article_group_id)
                        ->first();

        if ($data) {
            return response()->json($data);
        } else {
            return response()->json(['message' => 'No data found'], 404);
        }
    }

 public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'nullable|integer',
            'article_group_id' => 'nullable|integer',
            'company' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'available' => 'nullable|string',
            'version' => 'nullable|string|max:255', 
            'user_id' => 'nullable|string|max:255',
            'ac_nominal_voltage' => 'nullable|integer',
            'ac_nominal_current' => 'nullable|integer',
            'ac_nominal_power' => 'nullable|numeric',
            'max_ac_power' => 'nullable|numeric',
            'num_phases' => 'nullable|integer',
            'load_0' => 'nullable|numeric',
            'load_25' => 'nullable|numeric',
            'load_50' => 'nullable|numeric',
            'load_75' => 'nullable|numeric',
            'load_100' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $backupGenerator = BackupGenerator::updateOrCreate(
            ['product_id' => $request->product_id, 'article_group_id' => $request->article_group_id],
            $request->except(['_token']) // Exclude the CSRF token
        );
        } catch (\Exception $e) {
             Log::error('Error saving Power Optimizer configuration: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BackupGenerator $backupGenerator)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BackupGenerator $backupGenerator)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BackupGenerator $backupGenerator)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BackupGenerator $backupGenerator)
    {
        //
    }
}
