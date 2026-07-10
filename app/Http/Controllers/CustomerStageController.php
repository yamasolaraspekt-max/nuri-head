<?php

namespace App\Http\Controllers;

use App\Models\CustomerStage;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;

class CustomerStageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Customer-Rest: Belegkette-Gate (permission:Customer)
        $this->middleware('permission:Customer,delete')->only(['updateCustomerStage']);
        $this->middleware('permission:Customer,update')->only(['updateSingleCustomerStage', 'initializeCustomerStage']);
    }

    /**
     * Display a listing of the resource.
     */

    public function check(Request $request)
    {
        $customerId     = $request->customer_id;
        $alternativeId  = $request->alternative_id;
        $productId      = $request->product_id;

        // Check if already initialized
        $exists = DB::table('customer_stages')
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return response()->json(['exists' => true]);
        }

        // Step 1: Get the default version
        $defaultVersion = DB::table('task_phases')
            ->where('product_id', $productId)
            ->where('status', 'default')
            ->value('version');

        if (!$defaultVersion) {
            Log::warning("No default version found for product ID $productId");
            return response()->json(['exists' => false, 'error' => 'Keine Standardversion gefunden']);
        }

        // Step 2: Get all default phases
        $phases = DB::table('task_phases')
            ->where('product_id', $productId)
            ->where('version', $defaultVersion)
            ->get();

        $insertData = [];

        foreach ($phases as $phase) {
            $activities = DB::table('phase_activities')
                ->where('phase_id', $phase->id)
                ->get();

            foreach ($activities as $activity) {
                $insertData[] = [
                    'customer_id'    => $customerId,
                    'alternative_id' => $alternativeId,
                    'product_id'     => $productId,
                    'section_id'     => $phase->section_id ?? null,
                    'phase_id'       => $phase->id,
                    'task_id'        => $activity->id,
                    'version'        => $defaultVersion,
                    'status'         => 'active',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }
        }

        // Step 3: Insert if data exists
        if (!empty($insertData)) {
            DB::table('customer_stages')->insert($insertData);
            Log::info("Inserted customer stages for customer=$customerId, product=$productId", $insertData);
        } else {
            Log::warning("No insert data generated for customer=$customerId, product=$productId");
        }

        return response()->json(['exists' => false, 'inserted' => count($insertData)]);
    }



    public function initialize(Request $request)
    {
        $customerId = $request->customer_id;
        $alternativeId = $request->alternative_id;
        $productId = $request->product_id;

        // Prevent duplicate insert
        $alreadyExists = DB::table('customer_stages')
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->exists();

        if ($alreadyExists) {
            return response()->json(['status' => 'already_initialized']);
        }

        $version = DB::table('task_phases')
            ->where('product_id', $productId)
            ->where('status', 'default')
            ->value('version');

        if (!$version) {
            return response()->json(['error' => 'No default version found'], 404);
        }

        $phases = DB::table('task_phases')
            ->where('product_id', $productId)
            ->where('version', $version)
            ->get();

        $inserts = [];

        foreach ($phases as $phase) {
            $activities = DB::table('phase_activities')->where('phase_id', $phase->id)->get();

            foreach ($activities as $activity) {
                $inserts[] = [
                    'customer_id'     => $customerId,
                    'alternative_id'  => $alternativeId,
                    'product_id'      => $productId,
                    'section_id'      => $phase->section_id,
                    'phase_id'        => $phase->id,
                    'task_id'         => $activity->id,
                    'version'         => $version,
                    'status'          => 'active',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
        }

        if (!empty($inserts)) {
            DB::table('customer_stages')->insert($inserts);
        }

        return response()->json(['status' => 'initialized', 'count' => count($inserts)]);
    }


   public function initializeCustomerStage(Request $request)
    {
        $customerId = $request->customer_id;
        $alternativeId = $request->alternative_id;
        $productId = $request->product_id;

        \Log::info("⚙️ Initializing Customer Stage", [
            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product_id' => $productId,
        ]);

        // 1. Fetch default stages
        $defaultStages = DB::table('stages')
            ->where('product_id', $productId)
            ->where('default', 'yes')
            ->whereNull('deleted_at')
            ->get();

        \Log::info("📦 Fetched Default Stages", ['count' => $defaultStages->count(), 'ids' => $defaultStages->pluck('id')]);

        if ($defaultStages->isEmpty()) {
            \Log::warning("❗ No active default stages found for product {$productId}");
            return response()->json(['success' => false, 'message' => 'Keine aktiven Standardphasen gefunden.']);
        }

        foreach ($defaultStages as $stage) {
            \Log::info("➡️ Processing Stage", ['stage_id' => $stage->id, 'version' => $stage->version]);

            // 2. Get related task phases
            $taskPhases = DB::table('task_phases')
                ->where('stage_id', $stage->id)
                ->whereNull('deleted_at')
                ->get();

            \Log::info("🔍 Found Phases for Stage {$stage->id}", ['count' => $taskPhases->count(), 'phase_ids' => $taskPhases->pluck('id')]);

            if ($taskPhases->isEmpty()) {
                \Log::warning("⚠️ No task phases found for stage {$stage->id}");
                continue;
            }

            foreach ($taskPhases as $phase) {
                \Log::info("↪️ Processing Phase", ['phase_id' => $phase->id, 'section_id' => $phase->section_id]);

                // 3. Get the first activity for the phase
                $firstActivity = DB::table('phase_activities')
                    ->where('phase_id', $phase->id)
                    ->whereNull('deleted_at')
                    ->orderBy('sort_order', 'asc')
                    ->first();

                if ($firstActivity) {
                    \Log::info("✅ First Activity Found", ['task_id' => $firstActivity->id, 'title' => $firstActivity->title]);
                } else {
                    \Log::warning("⛔ No activities found for phase {$phase->id}");
                }

                // 4. Insert into customer_stages
                DB::table('customer_stages')->insert([
                    'customer_id' => $customerId,
                    'alternative_id' => $alternativeId,
                    'product_id' => $productId,
                    'section_id' => $phase->section_id,
                    'phase_id' => $phase->id,
                    'task_id' => $firstActivity->id ?? null,
                    'stage_id' => $stage->id,
                    'version' => $stage->version,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                \Log::info("📝 Inserted into customer_stages", [
                    'customer_id' => $customerId,
                    'alternative_id' => $alternativeId,
                    'product_id' => $productId,
                    'section_id' => $phase->section_id,
                    'phase_id' => $phase->id,
                    'task_id' => $firstActivity->id ?? null,
                    'stage_id' => $stage->id,
                    'version' => $stage->version,
                ]);
            }
        }

        \Log::info("🎉 Initialization Complete");

        return response()->json(['success' => true, 'message' => 'Phasen wurden gespeichert.']);
    }



 
    public function getStagesAndVersions(Request $request)
    {
        $productId = $request->product_id;

        // Get base stage info
        $stages = DB::table('stages')
            ->where('product_id', $productId)
            ->where('status', 'Published')
            ->whereNull('deleted_at')
            ->select('id', 'stage', 'version')
            ->get();

        // Enrich each stage with related phase_id and activity_id if found
        $stages = $stages->map(function ($stage) use ($productId) {
            $phase = DB::table('task_phases')
                ->where('stage_id', $stage->id)
                ->where('product_id', $productId)
                ->first();

            $activity = null;
            if ($phase) {
                $activity = DB::table('phase_activities')
                    ->where('stage_id', $stage->id)
                    ->where('phase_id', $phase->id)
                    ->first();
            }

            return [
                'id' => $stage->id,
                'stage' => $stage->stage,
                'version' => $stage->version,
                'phase_id' => $phase->id ?? null,
                'task_id' => $activity->id ?? null,
            ];
        });

        return response()->json($stages);
    }


 public function updateCustomerStage(Request $request)
{
    DB::table('customer_stages')
        ->where('customer_id', $request->customer_id)
        ->where('alternative_id', $request->alternative_id)
        ->where('product_id', $request->product_id)
        ->delete(); // Optional: or update existing

    $defaultStages = DB::table('stages')
        ->where('product_id', $request->product_id)
        ->where('version', $request->version)
        ->whereNull('deleted_at')
        ->get();

    foreach ($defaultStages as $stage) {
        DB::table('customer_stages')->insert([
            'customer_id' => $request->customer_id,
            'alternative_id' => $request->alternative_id,
            'product_id' => $request->product_id,
            'section_id' => null, // or from stage if exists
            'phase_id' => null,   // depends on logic
            'task_id' => null,
            'version' => $stage->version,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return response()->json(['success' => true]);
}


public function updateSingleStage(Request $request)
{
    $validated = $request->validate([
        'customer_id'     => 'required|integer',
        'alternative_id'  => 'required|integer',
        'product_id'      => 'required|integer',
        'phase_id'        => 'nullable|integer',
        'stage_id'        => 'nullable|integer',
        'version'         => 'nullable|string',
    ]);

    // Optional: log the incoming request for debugging
    \Log::info('Updating customer stage:', $validated);

    $updated = DB::table('customer_stages')
        ->where('customer_id', $validated['customer_id'])
        ->where('alternative_id', $validated['alternative_id'])
        ->where('product_id', $validated['product_id'])
        ->where('phase_id', $validated['phase_id'])
        ->update([ 
            'version'    => $validated['version'],
            'updated_at' => now(),
        ]);

    if ($updated === 0) {
        // No rows matched the WHERE clause
        return response()->json([
            'message' => 'Keine passende Phase gefunden oder bereits aktuell.',
        ], 404);
    }

    return response()->json(['message' => 'Phase wurde erfolgreich aktualisiert.']);
}

 public function updateSingleCustomerStage(Request $request)
{
    \Log::info('🔧 Requested Update Data:', $request->all());

    // Step 1: Fetch current customer_stage entry based on old identifiers
    $current = DB::table('customer_stages')
        ->where('customer_id', $request->customer_id)
        ->where('alternative_id', $request->alternative_id)
        ->where('product_id', $request->product_id)
        ->where('stage_id', $request->old_stage_id) 
        ->where('phase_id', $request->old_phase_id) 
        ->first();

    \Log::info('🧾 Current Customer Stage Entry:', [$current]);

    if (!$current) {
        return response()->json(['error' => 'Aktuelle Phase nicht gefunden.'], 404);
    }

    // Step 2: Fetch new stage
    $newStage = DB::table('stages')
        ->where('id', $request->stage_id)
        ->where('version', $request->version)
        ->first();

    if (!$newStage) {
        return response()->json(['error' => 'Neue Phase nicht gefunden.'], 404);
    }

    // Step 3: Get new phase — either selected explicitly or fallback to first of new stage
    $newPhase = $request->selected_phase_id
        ? DB::table('task_phases')->where('id', $request->selected_phase_id)->first()
        : DB::table('task_phases')
            ->where('stage_id', $newStage->id)
            ->orderBy('order')
            ->first();

    // Step 4: Get new task — either selected explicitly or first from phase
    $newTask = $request->selected_task_id
        ? DB::table('phase_activities')->where('id', $request->selected_task_id)->first()
        : ($newPhase
            ? DB::table('phase_activities')
                ->where('phase_id', $newPhase->id)
                ->orderBy('sort_order')
                ->first()
            : null);

    // Step 5: Update record
    DB::table('customer_stages')
        ->where('id', $current->id)
        ->update([
            'version'     => $newStage->version,
            'stage_id'    => $newStage->id,
            'phase_id'    => $newPhase->id ?? null,
            'section_id'  => $newPhase->section_id ?? null,
            'task_id'     => $newTask->id ?? null,
            'status'      => 'active',
            'updated_at'  => now(),
        ]);

    // Step 6: Log success
    \Log::info('✅ Updated customer_stage:', [
        'id'             => $current->id,
        'old_phase_id'   => $request->phase_id,
        'new_stage_id'   => $newStage->id,
        'new_phase_id'   => $newPhase->id ?? null,
        'new_task_id'    => $newTask->id ?? null,
        'version'        => $newStage->version,
        'section_id'     => $newPhase->section_id ?? null,
    ]);

    return response()->json(['success' => true]);
}


}
