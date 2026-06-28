<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use App\Models\BegFundings;
use Illuminate\Http\Request;

class BegFundingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $fundings = BegFundings::latest()->paginate(10);
        return view('admin.customer_economic_calculation.beg_fundings.index')->with('fundings', $fundings);
    }

    public function create()
    {
        return view('beg_fundings.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'heating_type.*' => 'required|string',
            'is_older_than_20_years.*' => 'required|in:0,1',
            'total_percentage.*' => 'required|numeric|min:0|max:100',
            'max_funding_amount.*' => 'required|numeric|min:0',
        ];
    
        $messages = [
            'heating_type.*.required' => 'Bitte wählen Sie einen Heizungstyp.',
            'is_older_than_20_years.*.required' => 'Bitte geben Sie an, ob die Heizung älter als 20 Jahre ist.',
            'total_percentage.*.required' => 'Bitte geben Sie den Gesamtprozentsatz an.',
            'total_percentage.*.numeric' => 'Der Gesamtprozentsatz muss eine Zahl sein.',
            'max_funding_amount.*.required' => 'Bitte geben Sie den maximalen Förderbetrag ein.',
            'max_funding_amount.*.numeric' => 'Der Förderbetrag muss eine Zahl sein.',
        ];
    
        $validator = \Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
    
        foreach ($request->heating_type as $i => $type) {
            BegFunding::create([
                'heating_type' => $type,
                'is_older_than_20_years' => $request->is_older_than_20_years[$i],
                'uses_speed_bonus' => isset($request->uses_speed_bonus[$i]),
                'uses_efficiency_bonus' => isset($request->uses_efficiency_bonus[$i]),
                'uses_income_bonus' => isset($request->uses_income_bonus[$i]),
                'total_percentage' => $request->total_percentage[$i],
                'max_funding_amount' => $request->max_funding_amount[$i],
            ]);
        }
    
        return response()->json(['success' => true]);
    }
    

    public function edit(BegFundings $begFunding)
    {
        return view('beg_fundings.edit', compact('begFunding'));
    }

    public function update(Request $request, BegFundings $begFunding)
    {
        $request->validate([
            'heating_type' => 'required|string',
            'basis_percentage' => 'required|numeric',
            'max_funding_amount' => 'required|numeric',
        ]);

        $begFunding->update($request->all());
        return redirect()->route('beg-fundings.index')->with('success', 'Eintrag aktualisiert');
    }

    public function destroy(BegFundings $begFunding)
    {
        $begFunding->delete();
        return redirect()->route('beg-fundings.index')->with('success', 'Eintrag gelöscht');
    }
}
