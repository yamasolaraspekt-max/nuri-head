<?php
namespace App\Http\Controllers\Phase;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\TaskPhase;
use App\Models\PhaseActivities;
use App\Models\ArticleGroup;
use App\Models\PhaseSection;

class PhaseCopyController extends Controller
{
    // Step 1: Load current phase + its activities + target dropdown data
    public function loadPhaseCopyData($phaseId)
    {
        // 📦 Load the full phase, including activities and stage
        $phase = TaskPhase::with(['activities', 'stage'])->findOrFail($phaseId);
    
        // 🗺 Translate section names (optional, for UI clarity)
        $sectionTranslation = [
            'complete'    => 'Komplettlösung',
            'montage'     => 'Montage',
            'product'     => 'Produkt',
            'plan'        => 'Planung',
            'maintenance' => 'Wartung',
            'repair'      => 'Reparatur',
            'others'      => 'Sonstiges',
        ];
    
        // 📚 Load related phase sections for the same product
        $sections = PhaseSection::where('product_id', $phase->product_id)
            ->get()
            ->map(function ($section) use ($sectionTranslation) {
                return [
                    'id'            => $section->id,
                    'phase_section' => $sectionTranslation[$section->phase_section] ?? ucfirst($section->phase_section),
                ];
            });
    
        // 📦 Get available products (id + display name)
        $products = ArticleGroup::select('id', 'article_group')->get();
    
        // 🧩 Prepare phase info payload
        $phaseData = [
            'id'            => $phase->id,
            'phase_name'    => $phase->phase_name,
            'version'       => $phase->version,
            'stage_id'      => $phase->stage_id,
            'stage'         => $phase->stage,
            'product_id'    => $phase->product_id,
            'section_id'    => $phase->section_id,
            'section_name'  => $phase->section_name,
        ];
    
        // 📝 Activities: keep only minimal needed data
        $activities = $phase->activities->map(function ($activity) {
            return [
                'id'    => $activity->id,
                'title' => $activity->title,
            ];
        });
    
        return response()->json([
            'phase'      => $phaseData,
            'activities' => $activities,
            'products'   => $products,
            'sections'   => $sections,
        ]);
    }
    
    
    public function copyPhaseAndActivities(Request $request)
    {
        // 🔍 Log input for debug
        \Log::info('📥 Copy request:', $request->all());
    
        // ✅ Validate incoming request
                $validated =$request->validate([
                    'target_product_id' => 'required|exists:article_groups,id',
                    'target_section_id' => 'required|exists:phase_sections,id',
                    'target_version'    => 'required|string',
                    'target_stage_id'   => 'required|exists:stages,id',
                    'target_phase_id'   => 'required|exists:task_phases,id',  
                    'activities'        => 'required|array|min:1',
                ]);
            
        
    
        $copied = 0;
    
        foreach ($validated['activities'] as $activityId) {
            $original = PhaseActivities::find($activityId);
    
            if ($original) {
                // 🔁 Update original's copy count
                $original->copy_count = ($original->copy_count ?? 0) + 1;
                $original->save();
    
                // 📦 Create a clone for the new phase
                $clone = $original->replicate();
                $clone->product_id  = $validated['target_product_id'];
                $clone->section_id  = $validated['target_section_id'];
                $clone->phase_id    = $validated['target_phase_id'];
                $clone->parent_id   = null;
                $clone->copy_from   = $original->id;
                $clone->copy_count  = null; // Reset for the new record
                $clone->created_at  = now();
                $clone->updated_at  = now();
                $clone->save();
    
                $copied++;
            }
        }
    
        // ✅ Respond with success
        return response()->json([
            'success' => true,
            'message' => "$copied Aktivitäten erfolgreich kopiert.",
        ]);
    }
    
    
}
