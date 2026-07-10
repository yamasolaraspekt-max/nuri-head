<?php

namespace App\Http\Controllers\Customer\ProfitCalculator;
use App\Http\Controllers\Controller;

use App\Models\ProfitabilityCalculation;
use App\Models\ProfitabilityData;
use Illuminate\Http\Request;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProfitabilityCalculationController extends Controller
{
    public function __construct(){
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,update')->only(['edit', 'saveCalculationReport', 'update']);
        $this->middleware('permission:Customer,delete')->only(['destroy']);
        $this->middleware('auth');
    }
     
    /**
     * 1. Display a table of all calculations for this customer/alternative/product
     */
    public function list($customer_id, $alternative_id, $product_id, $service_id = null)
    {
        $customer = DB::table('new_leads')->find($customer_id);
        $alternative = DB::table('lead_alternative_adds')->find($alternative_id);
        
        $calculations = ProfitabilityCalculation::with(['employee'])
            ->where('customer_id', $customer_id)
            ->where('alternative_id', $alternative_id)
            ->where('product_id', $product_id)
            ->when($service_id, function ($query) use ($service_id) {
                $query->where('service_id', $service_id);
            }, function ($query) {
                $query->whereNull('service_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.checklist.profitablity_calculation.list', compact(
            'calculations', 'customer', 'alternative', 'customer_id', 'alternative_id', 'product_id', 'service_id'
        ));
    }

    /**
     * 2. Create a new draft calculation and redirect to the editor
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required|exists:new_leads,id',
            'product_id'     => 'required|exists:article_groups,id',
            'title'          => 'required|string|max:255',
        ]);
    
        $calc = ProfitabilityCalculation::create([
            'customer_id'    => $request->customer_id,
            'alternative_id' => $request->alternative_id ?? null,
            'product_id'     => $request->product_id,
            'service_id'     => $request->service_id ?? null,
            'employee_id'    => auth()->check() ? auth()->user()->name : 1,
            'title'          => $request->title,
            'status'         => 'draft'
        ]);
    
        return redirect()->route('profitability-calculations.edit', ['id' => $calc->id])
                         ->with('save_msg', '✅ Neue Berechnung erstellt.');
    }

    /**
     * 3. Load the Calculator Editor for a specific calculation ID
     */
    public function edit($id)
    {
        $existingCalculation = ProfitabilityCalculation::findOrFail($id);
        
        $customer_id = $existingCalculation->customer_id;
        $alternative_id = $existingCalculation->alternative_id;
        $product_id = $existingCalculation->product_id;
        $service_id = $existingCalculation->service_id;

        $data['existingCalculation'] = $existingCalculation;

        /*
        |--------------------------------------------------------------------------
        | Fetch Customer + Alternative Data (Using alt.* to grab ALL columns)
        |--------------------------------------------------------------------------
        */
        $data['customer'] = DB::table('new_leads as nl')
            ->leftJoin('lead_alternative_adds as alt', function ($join) use ($alternative_id) {
                $join->on('alt.lead_id', '=', 'nl.id')
                    ->where('alt.id', '=', $alternative_id);
            })
            ->leftJoin('lead_product_lists as lpl', function ($join) use ($alternative_id, $product_id, $service_id) {
                $join->on('lpl.customer_id', '=', 'nl.id')
                    ->where('lpl.alternative_id', '=', $alternative_id)
                    ->where('lpl.product_id', '=', $product_id);

                if ($service_id) {
                    $join->where('lpl.service_id', '=', $service_id);
                } else {
                    $join->whereNull('lpl.service_id');
                }
            })
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
            ->where('nl.id', $customer_id)
            ->select([
                'nl.id as customer_id', 'nl.name as first_name', 'nl.lastname as last_name',
                'nl.full_address as customer_full_address', 'nl.street as customer_street',
                'nl.postcode as customer_postcode', 'nl.city as customer_city', 'nl.email', 'nl.phone', 'nl.telephone',
                
                'alt.*', // ⚡ Aggressively grab all Object Data to prevent missing columns
                'alt.id as alternative_id', // Secure mapping for ID
                
                'lpl.product_id', 'lpl.service_id',
                'ag.article_group', 'ag.initial',
            ])
            ->first();

        $customer = $data['customer'];

        if (! $customer) {
            abort(404, 'Kunde / Objekt / Produkt konnte nicht gefunden werden.');
        }

        /*
        |--------------------------------------------------------------------------
        | All assigned products for this object
        |--------------------------------------------------------------------------
        */
        $data['products'] = DB::table('lead_product_lists as lpl')
            ->join('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
            ->where('lpl.customer_id', $customer_id)
            ->where('lpl.alternative_id', $alternative_id)
            ->select(['ag.id', 'ag.article_group', 'ag.initial'])
            ->distinct()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Fetch PV Roof Data
        |--------------------------------------------------------------------------
        */
        $roofs = DB::table('p_v_roofs')
            ->where('alternative_id', $alternative_id)
            ->get();

        $dachseiten = [];
        if ($roofs->count() > 0) {
            foreach ($roofs as $roof) {
                $dachseiten[] = [
                    'id' => $roof->id,
                    'ausrichtung' => (string) ($roof->roof_orientation ?: 'Süd'),
                    'neigung' => (float) ($roof->roof_pitch ?: 35),
                    'eindeckung' => (string) ($roof->roof_covering_name ?: 'Ziegel'),
                    'eindeckungTyp' => (string) ($roof->roof_covering_model ?: ''),
                    'customKwp' => $roof->kwp_size ? (string) $roof->kwp_size : '',
                ];
            }
        } else {
            // Fallback empty roof
            $dachseiten[] = [
                'id' => 1, 'ausrichtung' => 'Süd', 'neigung' => 35,
                'eindeckung' => 'Ziegel', 'eindeckungTyp' => '', 'customKwp' => ''
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Derived values & Settings
        |--------------------------------------------------------------------------
        */
        $objectStreet = trim((string) ($customer?->street ?? $customer?->customer_street ?? ''));
        $objectPostcode = trim((string) ($customer?->postcode ?? $customer?->customer_postcode ?? ''));
        $objectCity = trim((string) ($customer?->city ?? $customer?->customer_city ?? ''));

        $resolvedFullAddress = $customer?->full_address ?: ($customer?->customer_full_address ?: trim("$objectStreet, $objectPostcode $objectCity"));
        
        [$includeSolar, $includeWp] = $this->detectSystemFlags($customer?->article_group);
        $hasWallbox = $data['products']->contains(fn ($p) => str_contains(mb_strtolower($p->article_group), 'wallbox')) 
                      || (int) ($customer?->wallbox_count ?? 0) > 0 
                      || (int) ($customer?->electric_car_count ?? 0) > 0;

        // Fetching the correct consumption values from your forms
       // 1. Check if the exact 'consumption' value was provided in Object Daten
        if (isset($customer->consumption) && $customer->consumption !== '') {
            // Use the exact value, NO conversion
            $finalHeizVerbrauch = (int) $customer->consumption; 
        } else {
            // 2. Fallback to old fields and convert if necessary
            $rawHeat = $customer?->total_heat_consumption ?? $customer?->annual_heating_energy_consumption_kwh ?? $this->fallbackHeatingConsumption($customer);
            $finalHeizVerbrauch = (int) $this->normalizeFuelConsumption($this->mapHeizungsArt($customer?->heating_system_type ?? ''), $rawHeat);
        }

        $rawElectricity = $customer?->power_household ?? $customer?->total_electricity_consumption ?? $this->fallbackElectricityConsumption($customer);
        $autoArt = ((int) ($customer?->power_electric_car ?? 0) > 0 || (int) ($customer?->electric_car_count ?? 0) > 0 || mb_strtolower((string)$customer?->electric_car) === 'ja') ? 'E-Auto' : 'Verbrenner';

        $jazDefault = match ($this->mapHeizsystem($customer?->heating_type ?? '')) {
            'Fußbodenheizung' => '4.5',
            'Beides' => '4.0',
            default => '3.5',
        };

        $fullName = trim((string) ($customer?->first_name ?? '') . ' ' . (string) ($customer?->last_name ?? ''));

        /*
        |--------------------------------------------------------------------------
        | Build Frontend Config
        |--------------------------------------------------------------------------
        */
        $frontendConfig = [
            'company' => 'Solar Aspekt',
            'modulePV' => (bool) $includeSolar,
            'moduleWP' => (bool) $includeWp,
            'moduleWB' => (bool) $hasWallbox,
            'name' => $fullName !== '' ? $fullName : 'Kunde',
            
            // Gebäude
            'gebaeudeArt' => $this->mapGebaeudeArt($customer?->object_type ?? ''),
            'wohneinheiten' => (int) ($customer?->number_we ?? 1),
            'selbstbewohnteWE' => ($customer?->usage_type === 'Eigennutzung') ? (int) ($customer?->number_we ?? 1) : (($customer?->usage_type === 'Beides') ? 1 : 0),
            'weUnter40k' => 0, 
            'plz' => (string) $objectPostcode,
            'fullAddress' => (string) $resolvedFullAddress,
            'street' => (string) $objectStreet,
            'city' => (string) $objectCity,
            
            // PV & Dach
            'dachseiten' => $dachseiten,

            // Heizung & Sanitär
            'heizungArt' => match ($this->mapHeizungsArt($customer?->heating_system_type ?? '')) {
                'Erdgas' => 'Gas', 'Heizöl' => 'Öl', 'Pellets' => 'Holz / Pellets', 'Nachtspeicher' => 'Nachtspeicher', default => 'Gas',
            },
            'heizungAlter' => (int) ($customer?->heating_system_age ?? $customer?->heating_system_year ?? 20),
            'heizVerbrauch' => $finalHeizVerbrauch,
            'heizSystem' => $this->mapHeizsystem($customer?->heating_type ?? ''),
            
            'warmwasserArt' => mb_strtolower($customer?->hot_water_generation ?? '') === 'dezentral' ? 'Dezentral' : 'Zentral',
            'personen' => (int) ($customer?->person_count ?? $customer?->number_people ?? 3),
            'zirkulation' => false,
            
            'rohrHeizungMaterial' => (string) ($customer?->pipe_system_material ?: 'Kupfer'),
            'rohrHeizungDN' => '28',
            'rohrWWMaterial' => (string) ($customer?->pipe_system_material ?: 'Kupfer'),
            'rohrWWDN' => '18',
            'rohrZirkulationMaterial' => (string) ($customer?->pipe_system_material ?: 'Kupfer'),
            'rohrZirkulationDN' => '15',
            
            'kaminVorhanden' => (int) ($customer?->chimney ?? 0) > 0,
            'kaminWeiterBetreiben' => false,
            'holzVerbrauch' => (float) ($customer?->wood_consumption ?? 3),
            'preisHolz' => 120,
            
            'solarthermieVorhanden' => mb_strtolower((string)($customer?->solar_thermal ?? '')) === 'ja',
            'solarthermieWeiterBetreiben' => false,
            'solarthermieArt' => 'Flachkollektor',
            'solarKollektoren' => 2,
            
            // Elektromobilität & Strom
            'hhStrom' => (int) $rawElectricity,
            'autoArt' => $autoArt,
            'fahrleistung' => (float) ($customer?->car_kilo ?: 15000),
            'verbrennerVerbrauch' => 7,
            'preisSprit' => 1.80,
            'preisStrom' => 0.35,
            'preisEinspeisung' => 0.08,
            'preisHeizMedium' => (float) $this->defaultHeatingPriceForReact($this->mapHeizungsArt($customer?->heating_system_type ?? '')),
            'inflationRate' => 3.0,
            'wartungOld' => 300, 
            'netzentgelt' => 0.10,
            
            // Kosten & KPIs
            'costWP' => (float) ($customer?->investment_costs ?: 30000),
            'costPV' => 16000,
            'costBattery' => 8000,
            'costWallbox' => 1500,
            
            'customWpKw' => $customer?->heating_load_calculation ?: '',
            'customPvKwp' => '',
            'customBatteryKwh' => '',
            'customJAZ' => $jazDefault,
            
            'discountWP' => 1000, 'discountPV' => 750, 'discountBattery' => 250, 'discountWallbox' => 150,
            'extraGrantWP' => '', 'extraGrantPV' => '', 'extraGrantBattery' => '', 'extraGrantWallbox' => '',
            'extraGrantSourceWP' => '', 'extraGrantSourcePV' => '', 'extraGrantSourceBattery' => '', 'extraGrantSourceWallbox' => '',
        ];

        /*
        |--------------------------------------------------------------------------
        | Restore Saved Config Snapshot (unless user forces reload via GET parameter)
        |--------------------------------------------------------------------------
        */
        if (!request()->has('reload_data') && !empty($existingCalculation->config_snapshot) && is_array($existingCalculation->config_snapshot)) {
            $frontendConfig = array_replace_recursive($frontendConfig, $existingCalculation->config_snapshot);
        }

        $data['frontendConfig'] = $frontendConfig;
        
        $data['pageMeta'] = [
            'calculation_id' => $existingCalculation->id,
            'customer_id' => (int) $customer_id,
            'alternative_id' => (int) $alternative_id,
            'product_id' => (int) $product_id,
            'service_id' => $service_id ? (int) $service_id : null,
        ];

        return view('admin.checklist.profitablity_calculation.profit', $data);
    }

    // Helper functions
    protected function detectSystemFlags(?string $articleGroup): array {
        $name = mb_strtolower(trim((string) $articleGroup));
        $includeSolar = str_contains($name, 'pv') || str_contains($name, 'photovoltaik') || str_contains($name, 'solar') || str_contains($name, 'speicher') || str_contains($name, 'wallbox');
        $includeWp = str_contains($name, 'wärmepumpe') || str_contains($name, 'waermepumpe') || preg_match('/\bwp\b/u', $name);
        if (! $includeSolar && ! $includeWp) { $includeSolar = true; $includeWp = true; }
        return [$includeSolar, $includeWp];
    }

    protected function splitStreetAndHouseNumber(?string $street): array {
        $street = trim((string) $street);
        if ($street === '') return ['', ''];
        if (preg_match('/^(.*?)[,\s]+(\d+[a-zA-Z]?(?:[-\/]\d+[a-zA-Z]?)?)$/u', $street, $m)) return [trim($m[1]), trim($m[2])];
        return [$street, ''];
    }

    protected function mapGebaeudeArt(?string $value): string {
        $v = mb_strtolower(trim((string) $value));
        if (str_contains($v, 'mehr') || str_contains($v, 'mfh') || str_contains($v, 'multi')) return 'Mehrfamilienhaus';
        return 'Einfamilienhaus';
    }

    protected function mapHeizungsArt(?string $value): string {
        $v = mb_strtolower(trim((string) $value));
        if (str_contains($v, 'gas')) return 'Erdgas';
        if (str_contains($v, 'öl') || str_contains($v, 'oe') || str_contains($v, 'oil')) return 'Heizöl';
        if (str_contains($v, 'pellet')) return 'Pellets';
        if (str_contains($v, 'nacht')) return 'Nachtspeicher';
        if (str_contains($v, 'fern')) return 'Fernwärme';
        return 'Erdgas';
    }

    protected function mapHeizungsAlter(?string $ageGroup, ?int $year = null): string {
        $group = mb_strtolower(trim((string) $ageGroup));
        if ($year) return (string) max(0, now()->year - $year);
        if ($group !== '') {
            if (str_contains($group, 'älter') || str_contains($group, 'alter') || str_contains($group, '>') || str_contains($group, '20')) return '25';
            return '10';
        }
        return '20';
    }

    protected function mapHeizsystem(?string $value): string {
        $v = mb_strtolower(trim((string) $value));
        if (str_contains($v, 'underfloor') || str_contains($v, 'fußboden')) return 'Fußbodenheizung';
        if (str_contains($v, 'both') || str_contains($v, 'beides')) return 'Beides';
        return 'Heizkörper';
    }

    protected function normalizeFuelConsumption(string $heizungsArt, float $rawKwhValue): int {
        $kwh = max(0, $rawKwhValue);
        return match ($heizungsArt) {
            'Heizöl' => (int) round($kwh / 10),
            'Pellets' => (int) round($kwh / 4.8),
            default => (int) round($kwh),
        };
    }

    protected function fallbackHeatingConsumption(object|null $customer): int {
        if (! empty($customer?->heated_area)) {
            $area = (float) $customer->heated_area;
            $buildingYear = (int) ($customer?->building_year ?? 0);
            $factor = 140;
            if ($buildingYear >= 2016) $factor = 50;
            elseif ($buildingYear >= 2002) $factor = 80;
            elseif ($buildingYear >= 1995) $factor = 100;
            elseif ($buildingYear >= 1979) $factor = 125;
            return (int) max(8000, round($area * $factor));
        }
        return 20000;
    }

    protected function fallbackElectricityConsumption(object|null $customer): int {
        $persons = (int) ($customer?->person_count ?? 0);
        if ($persons > 0) return (int) (($persons * 1200) + 500);
        return 4000;
    }

    protected function defaultHeatingPriceForReact(string $heizungsArt): float {
        return match ($heizungsArt) {
            'Erdgas'        => 0.11, 'Heizöl'        => 1.05, 'Pellets'       => 0.35, 'Nachtspeicher' => 0.30, 'Fernwärme'     => 0.14, default         => 0.11,
        };
    }

    public function update(Request $request) {
        $request->validate(['id' => 'required|exists:profitability_calculations,id', 'title' => 'required|string|max:255']);
        ProfitabilityCalculation::findOrFail($request->id)->update(['title' => $request->title]);
        return back()->with('updated_msg', 'Erfolgreich aktualisiert.');
    }

    public function destroy($id) {
        ProfitabilityCalculation::findOrFail($id)->delete();
        return back()->with('delete_msg', 'Erfolgreich gelöscht.');
    }

    public function saveCalculationReport(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|exists:profitability_calculations,id',
            'customer_id' => 'required|exists:new_leads,id',
            'alternative_id' => 'nullable|exists:lead_alternative_adds,id',
            'product_id' => 'required|exists:article_groups,id',
            'service_id' => 'nullable|exists:phase_sections,id',
            'title' => 'required|string|max:255',
            'status' => 'nullable|string|max:50',
            'current_electricity_cost' => 'nullable|numeric', 'current_heating_cost' => 'nullable|numeric', 'current_fuel_cost' => 'nullable|numeric',
            'current_total_yearly_cost' => 'nullable|numeric', 'current_total_25y_cost' => 'nullable|numeric',
            'future_electricity_cost' => 'nullable|numeric', 'future_heating_cost' => 'nullable|numeric', 'future_ev_cost' => 'nullable|numeric',
            'future_total_yearly_cost' => 'nullable|numeric', 'future_total_25y_cost' => 'nullable|numeric',
            'savings_per_year' => 'nullable|numeric', 'savings_over_25_years' => 'nullable|numeric',
            'investment_cost' => 'nullable|numeric', 'amortisation_years' => 'nullable|numeric', 'roi_percent' => 'nullable|numeric',
            'co2_emission_before' => 'nullable|numeric', 'co2_emission_after' => 'nullable|numeric', 'co2_saved_trees_equiv' => 'nullable|integer',
            'notes' => 'nullable|string', 'electricity_price_note' => 'nullable|string|max:255',
            'config_snapshot' => 'nullable|array', 'computed_snapshot' => 'nullable|array', 'customer_snapshot' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['ok' => false, 'message' => 'Validierungsfehler.', 'errors' => $validator->errors()], 422);
        }

        $payload = $validator->validated();
        
        // Safety check if user is logged in
        $payload['employee_id'] = auth()->check() ? auth()->user()->name : 1; 
        
        $payload['status'] = $payload['status'] ?? 'draft';

        if (!empty($payload['id'])) {
            $calculation = ProfitabilityCalculation::findOrFail($payload['id']);
            unset($payload['id']);
            $calculation->update($payload);
        } else {
            $calculation = ProfitabilityCalculation::updateOrCreate(
                [
                    'customer_id' => $payload['customer_id'],
                    'alternative_id' => $payload['alternative_id'] ?? null,
                    'product_id' => $payload['product_id'],
                    'service_id' => $payload['service_id'] ?? null,
                ],
                $payload
            );
        }

        return response()->json([
            'ok' => true,
            'message' => 'Wirtschaftlichkeitsberechnung wurde gespeichert.',
            'id' => $calculation->id,
            'status' => $calculation->status,
        ]);
    }
}