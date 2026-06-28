<?php

namespace App\Http\Controllers\Customer\ProfitCalculator;
use App\Http\Controllers\Controller;

use App\Models\ProfitabilityData;
use Illuminate\Http\Request;

class ProfitabilityDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function save(Request $request)
    {
        $query = ProfitabilityData::where('p_id', $request->p_id ?? null)
                                    ->where('customer_id', $request->customer_id ?? null)
                                    ->where('alternative_id', $request->alternative_id ?? null)
                                    ->where('product_id', $request->product_id ?? null);
    
        $profitability = $query->first();
    
        if (!$profitability) {
            $profitability = new ProfitabilityData();
            $profitability->p_id = $request->p_id;
            $profitability->customer_id = $request->customer_id;
            $profitability->alternative_id = $request->alternative_id;
            $profitability->product_id = $request->product_id;
        }
    
        // Only update the fields present in the request
        foreach ($request->except('_token') as $key => $value) {
            if (!is_null($value)) {
                $profitability->{$key} = $value;
            }
        }
    
        $profitability->save();
    
        return response()->json(['success' => true, 'message' => 'Gespeichert']);
    }
    
    /**
     * Store a newly created resource in storage.
     */
        public function analyze($p_id)
        {
            $data = ProfitabilityData::where('p_id', $p_id)->firstOrFail();
        
            // ✅ Step 1: Pull all needed prices with fallbacks
            $electricity_price = $data->electricity_price ?? 0.30;       // €/kWh
            $wp_price          = $data->electricity_price ?? 0.08;       // €/kWh for Wärmepumpe
            $ev_price          = $data->electricity_price ?? 0.10;       // €/kWh for EV
        
            $gas_price         = $data->gas_price ?? 0.11;
            $fuel_price = $data->fuel_price ?? 1.43;
        
            // ✅ Step 2: Old costs (Alt)
            $old_costs = [
                'electricity' => ($data->household_power ?? 4000) * $electricity_price,
                'heating'     => ($data->heating_energy ?? 25000) * $gas_price,
                'fuel'        => ($data->ev_count ?? 2) * 2500 * 0.075 * $fuel_price // 7.5L/100km per car
            ];
            $old_costs['total'] = array_sum($old_costs);
        
            // ✅ Step 3: New costs (Neu)
            $new_costs = [
                'electricity' => ($data->household_power ?? 4000) * 0.06, // assumed savings through PV
                'heating'     => ($data->wp_consumption ?? 6000) * $wp_price,
                'fuel'        => ($data->ev_consumption ?? 5000) * $ev_price,
            ];
            $new_costs['total'] = array_sum($new_costs);
        
            // ✅ Step 4: Annual + 25-Year Saving
            $saving_year = $old_costs['total'] - $new_costs['total'];
            $saving_25y  = $saving_year * 25;
        
            // ✅ Step 5: CO₂ Factors
            $co2_factors = [
                'electricity' => $data->co2_factor_electricity ?? 0.4,  // kg/kWh
                'oil'         => $data->co2_factor_oil ?? 2.6,          // kg/L
                'gas'         => $data->co2_factor_gas ?? 0.25,         // kg/kWh
                'fuel'        => 2.43                                   // kg/L (Benzin/Diesel)
            ];
        
            // ✅ Step 6: CO₂ emissions before
            $co2_old = [
                'electricity' => ($data->household_power ?? 4000) * $co2_factors['electricity'],
                'heating'     => ($data->heating_energy ?? 25000) * $co2_factors['gas'],
                'fuel'        => 2 * 2000 * $co2_factors['fuel'], // 2x autos * 2000L/year each
            ];
            $co2_old['total'] = array_sum($co2_old);
        
            // ✅ Step 7: CO₂ emissions after (0 assumed)
            $co2_new   = 0;
            $co2_saved = $co2_old['total'];
        
            return view('admin.checklist.profitablity_calculation.partials.result', [
                'old_costs'   => $old_costs,
                'new_costs'   => $new_costs,
                'saving_year' => $saving_year,
                'saving_25y'  => $saving_25y,
                'co2_old'     => $co2_old['total'],
                'co2_new'     => $co2_new,
                'co2_saved'   => $co2_saved,
                'data'        => $data
            ]);
        }
    

    /**
     * Display the specified resource.
     */
    public function getProfitabilityData(Request $request)
    {
        $data = ProfitabilityCalculation::where('id', $request->p_id)->first();
    
        return response()->json([
            'pv_module_area' => $data->pv_module_area,
            'pv_power_kwp' => $data->pv_power_kwp,
            'pv_self_use' => $data->pv_self_use,
            'battery_capacity' => $data->battery_capacity,
            'autarky_level' => $data->autarky_level,
            'jaz_value' => $data->jaz_value,
            'wp_consumption' => $data->wp_consumption,
            'ev_count' => $data->ev_count,
            'ev_consumption' => $data->ev_consumption,
            'household_power' => $data->household_power,
            'heating_energy' => $data->heating_energy,
            'building_type' => $data->building_type,
            'electricity_price' => $data->electricity_price,
            'oil_price' => $data->oil_price,
            'gas_price' => $data->gas_price,
            'district_heating_price' => $data->district_heating_price,
            'energy_inflation' => $data->energy_inflation,
            'fuel_price' => $data->fuel_price,
            'co2_factor_electricity' => $data->co2_factor_electricity,
        ]);
    }
    

    public function savePvgisData(Request $request)
{
    $pvgis = session('pvgis_data');

    if (!$pvgis || !isset($pvgis['outputs']['totals']['fixed'])) {
        return back()->with('error_msg', 'Keine PVGIS-Daten vorhanden.');
    }

    $totals = $pvgis['outputs']['totals']['fixed'];

    ProfitabilityData::updateOrCreate(
        [
            'p_id' => $request->p_id,
            'customer_id' => $request->customer_id,
            'alternative_id' => $request->alternative_id,
            'product_id' => $request->product_id,
        ],
        [
            'pv_energy_yield' => $totals['E_y'] ?? null,
            'pv_irradiation'  => $totals['H(i)_y'] ?? null,
            'pv_total_loss'   => $totals['l_total'] ?? null,
        ]
    );

    return back()->with('save_msg', 'PVGIS-Daten erfolgreich gespeichert.');
}

}
