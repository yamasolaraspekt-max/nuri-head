<?php

namespace App\Http\Controllers;

use App\Models\EconomicCalculation;
use Illuminate\Http\Request; 
use App\Models\SolarSystem;
use App\Models\HeatPump;
use App\Models\NewLead;
use App\Models\ArticleGroup;

class EconomicCalculationController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index()
    {
        $calculations = EconomicCalculation::with('lead')->latest()->get();
        return view('admin.customer_economic_calculation.economic_calculation.index', compact('calculations'));
    }

    public function store(Request $request)
    {
        $calc = EconomicCalculation::create($request->only([
            'lead_id',
            'solar_total_cost', 'solar_savings', 'solar_roi_years',
            'wp_total_cost', 'wp_savings', 'wp_roi_years',
            'combined_savings', 'combined_roi'
        ]));

        if ($request->filled('kwp_size')) {
            SolarSystem::create($request->only([
                'lead_id', 'kwp_size', 'self_consumption_rate', 'feed_in_tariff', 'system_price', 'battery_capacity', 'battery_price'
            ]));
        }

        if ($request->filled('cop')) {
            HeatPump::create($request->only([
                'lead_id', 'type', 'cop', 'installation_cost', 'annual_costs'
            ]));
        }

        return response()->json(['message' => 'Berechnung gespeichert.']);
    }

    public function edit($id)
    {
        $calculation = EconomicCalculation::with(['lead', 'lead.solarSystems', 'lead.heatPumps'])->findOrFail($id);
        $user = Auth::user();
        return view('admin.customer_economic_calculation.economic_calculation.edit', compact('calculation', 'user'));
    }

    public function update(Request $request, $id)
    {
        $calculation = EconomicCalculation::findOrFail($id);

        // Optional permission check
        if (Auth::user()->cannot('update', $calculation)) {
            abort(403);
        }

        $calculation->update($request->only([
            'solar_total_cost', 'solar_savings', 'solar_roi_years',
            'wp_total_cost', 'wp_savings', 'wp_roi_years',
            'combined_savings', 'combined_roi'
        ]));

        if ($request->filled('kwp_size')) {
            $solar = SolarSystem::where('lead_id', $calculation->lead_id)->first();
            if ($solar) {
                $solar->update($request->only(['kwp_size', 'self_consumption_rate', 'feed_in_tariff', 'system_price', 'battery_capacity', 'battery_price']));
            }
        }

        if ($request->filled('cop')) {
            $wp = HeatPump::where('lead_id', $calculation->lead_id)->first();
            if ($wp) {
                $wp->update($request->only(['type', 'cop', 'installation_cost', 'annual_costs']));
            }
        }

        return redirect()->back()->with('update_msg', 'Berechnung + Systeme aktualisiert.');
    }

    public function destroy($id)
    {
        $calc = EconomicCalculation::findOrFail($id);

        if (Auth::user()->cannot('delete', $calc)) {
            abort(403);
        }

        $calc->delete();
        return redirect()->back()->with('delete_msg', 'Berechnung gelöscht.');
    }
}
