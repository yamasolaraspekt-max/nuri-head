<?php

namespace App\Http\Controllers;

use App\Models\LeadProductChecklistValue;
use App\Models\ProductFormula;
use Illuminate\Http\Request;

class LeadProductChecklistValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    public function initChecklistRender(Request $request)
    {
        \Log::info('Checklist init request', $request->all());

        $request->validate([
            'customer_id' => 'required|exists:new_leads,id',
            'alternative_id' => 'required|exists:lead_alternative_adds,id',
            'product_id' => 'required|exists:article_groups,id',
            'lead_product_list_id' => 'required|exists:lead_product_lists,id',
        ]);
        


        $formulas = ProductFormula::where('product_id', $request->product_id)->get();
        $html = '';

        foreach ($formulas as $formula) {
            // Create or find the checklist value (temporary init)
            $value = LeadProductChecklistValue::firstOrCreate([
                'lead_product_list_id' => $request->lead_product_list_id,
                'product_formula_id' => $formula->id,
                'customer_id' => $request->customer_id,
                'alternative_id' => $request->alternative_id,
                'product_id' => $request->product_id,
                'section_name' => $formula->section_name,
            ], [
                'filled_values' => json_encode([]),
                'formula_snapshot' => $formula->fields,
                'formula_version' => $formula->version,
            ]);

            // Render test block per formula
            $html .= view('admin.new_leads.checklists.checklist', [
                'formula' => $formula,
                'filled_values' => json_decode($value->filled_values, true) ?? [] // ✅ FIXED HERE
            ])->render();

            
        }

        return response()->json([
            'html' => $html,
            'success' => true,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function save(Request $request)
    {
        $request->validate([
            'lead_product_list_id' => 'required|exists:lead_product_lists,id',
            'values' => 'required|array',
        ]);
    
        $checklists = LeadProductChecklistValue::where('lead_product_list_id', $request->lead_product_list_id)->get();
    
        foreach ($checklists as $checklist) {
            $fields = json_decode($checklist->formula_snapshot, true);
            $sectionData = [];
    
            foreach ($fields as $field) {
                $name = $field['name'];
                if (isset($request->values[$name])) {
                    $sectionData[$name] = $request->values[$name];
                }
            }
    
            $checklist->update([
                'filled_values' => json_encode($sectionData),
            ]);
        }
    
        return response()->json(['success' => true]);
    }
    
    public function saveChecklist(Request $request)
    {
        \Log::info('Saving checklist', $request->all());
    
        $request->validate([
            'lead_product_list_id' => 'required|exists:lead_product_lists,id',
            'filled_values' => 'required|array',
            'customer_id' => 'required|exists:new_leads,id',
            'alternative_id' => 'required|exists:lead_alternative_adds,id',
            'product_id' => 'required|exists:article_groups,id',
        ]);
    
        $formulas = ProductFormula::where('product_id', $request->product_id)->get();
    
        foreach ($formulas as $formula) {
            $fields = json_decode($formula->fields, true);
            $fieldNames = array_column($fields, 'name');
    
            $filteredValues = [];
            foreach ($fieldNames as $name) {
                if (isset($request->filled_values[$name])) {
                    $filteredValues[$name] = $request->filled_values[$name];
                }
            }
    
            LeadProductChecklistValue::updateOrCreate([
                'lead_product_list_id' => $request->lead_product_list_id,
                'product_formula_id' => $formula->id,
                'customer_id' => $request->customer_id,
                'alternative_id' => $request->alternative_id,
                'product_id' => $request->product_id,
                'section_name' => $formula->section_name,
            ], [
                'filled_values' => json_encode($filteredValues),
            ]);
        }
    
        return response()->json(['success' => true]);
    }
    


    /**
     * Display the specified resource.
     */
    public function show(LeadProductChecklistValue $leadProductChecklistValue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeadProductChecklistValue $leadProductChecklistValue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeadProductChecklistValue $leadProductChecklistValue)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeadProductChecklistValue $leadProductChecklistValue)
    {
        //
    }
}
