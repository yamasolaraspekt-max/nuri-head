<?php

namespace App\Http\Controllers\Customer\Climate;
use App\Http\Controllers\Controller;

use App\Models\ClimateLocation;
use App\Models\ClimateMonthlyData;
use App\Models\ClimateSolarMonthlyData;
use App\Models\ClimateStation;
use App\Models\LeadAlternativeAdd;
use App\Models\NewLeads;
use Illuminate\Http\Request;

class ClimateStationController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function show($customer_id, $alternative_id)
    {
        $lead = NewLeads::findOrFail($customer_id);

        $alternative = LeadAlternativeAdd::where('lead_id', $customer_id)
            ->findOrFail($alternative_id);

        $autoPostcode = $alternative->postcode ?: $lead->postcode;

        return view('admin.climate.index', [
            'lead'          => $lead,
            'alternative'   => $alternative,
            'autoPostcode'  => $autoPostcode,
            'customerId'    => $customer_id,
            'alternativeId' => $alternative_id,
        ]);
    }

    public function data(Request $request, $customer_id, $alternative_id)
    {
        $lead = NewLeads::findOrFail($customer_id);

        $alternative = LeadAlternativeAdd::where('lead_id', $customer_id)
            ->findOrFail($alternative_id);

        $fallbackPostcode = trim((string) ($alternative->postcode ?: $lead->postcode));

        $postcode   = trim((string) $request->get('postcode', ''));
        $country    = trim((string) $request->get('country', ''));
        $city       = trim((string) $request->get('city', ''));
        $stationId  = trim((string) $request->get('station_id', ''));

        if ($postcode === '' && $fallbackPostcode !== '') {
            $postcode = $fallbackPostcode;
        }

        // Base filter datasets
        $locationsQuery = ClimateLocation::query();

        $allPostcodes = ClimateLocation::query()
            ->select('postcode')
            ->whereNotNull('postcode')
            ->distinct()
            ->orderBy('postcode')
            ->pluck('postcode')
            ->values();

        $allCountries = ClimateLocation::query()
            ->select('country_code')
            ->whereNotNull('country_code')
            ->where('country_code', '!=', '')
            ->distinct()
            ->orderBy('country_code')
            ->pluck('country_code')
            ->values();

        // Resolve by postcode first (highest priority)
        $selectedLocation = null;

        if ($postcode !== '') {
            $selectedLocation = ClimateLocation::query()
                ->where('postcode', $postcode)
                ->first();
        }

        // Fallback search by country + city if postcode not matched
        if (! $selectedLocation) {
            $filteredLocationQuery = ClimateLocation::query();

            if ($country !== '') {
                $filteredLocationQuery->where('country_code', $country);
            }

            if ($city !== '') {
                $filteredLocationQuery->where('name', $city);
            }

            $selectedLocation = $filteredLocationQuery->first();
        }

        // Auto-fill filters from selected location
        if ($selectedLocation) {
            $postcode = $selectedLocation->postcode ?: $postcode;
            $country  = $selectedLocation->country_code ?: $country;
            $city     = $selectedLocation->name ?: $city;

            if ($stationId === '') {
                $stationId = (string) ($selectedLocation->station_01_id ?: '');
            }
        }

        // Build dependent option lists
        $cityQuery = ClimateLocation::query();
        if ($country !== '') {
            $cityQuery->where('country_code', $country);
        }
        if ($postcode !== '') {
            $cityQuery->where('postcode', $postcode);
        }

        $allCities = $cityQuery
            ->select('name')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->distinct()
            ->orderBy('name')
            ->pluck('name')
            ->values();

        $stationIdCandidates = [];

        $stationSourceLocations = ClimateLocation::query();

        if ($postcode !== '') {
            $stationSourceLocations->where('postcode', $postcode);
        }
        if ($country !== '') {
            $stationSourceLocations->where('country_code', $country);
        }
        if ($city !== '') {
            $stationSourceLocations->where('name', $city);
        }

        foreach ($stationSourceLocations->get() as $loc) {
            foreach ([$loc->station_01_id, $loc->station_02_id, $loc->station_03_id] as $candidate) {
                if ($candidate) {
                    $stationIdCandidates[] = (string) $candidate;
                }
            }
        }

        $stationIdCandidates = array_values(array_unique($stationIdCandidates));

        if (empty($stationIdCandidates)) {
            $stationIdCandidates = ClimateStation::query()
                ->orderBy('name')
                ->pluck('station_id')
                ->map(fn ($v) => (string) $v)
                ->values()
                ->all();
        }

        $stationOptions = ClimateStation::query()
            ->whereIn('station_id', $stationIdCandidates)
            ->orderBy('name')
            ->get(['id', 'station_id', 'name', 'region', 'country_code', 'lat', 'lon', 'elevation'])
            ->map(function ($st) {
                return [
                    'station_id' => (string) $st->station_id,
                    'name'       => $st->name,
                    'region'     => $st->region,
                    'country'    => $st->country_code,
                    'lat'        => $st->lat !== null ? (float) $st->lat : null,
                    'lon'        => $st->lon !== null ? (float) $st->lon : null,
                    'alt'        => $st->elevation !== null ? (float) $st->elevation : null,
                ];
            })
            ->values();

        // Resolve station
        $selectedStation = null;

        if ($stationId !== '') {
            $selectedStation = ClimateStation::query()
                ->where('station_id', $stationId)
                ->first();
        }

        if (! $selectedStation && $selectedLocation && $selectedLocation->station_01_id) {
            $selectedStation = ClimateStation::query()
                ->where('station_id', $selectedLocation->station_01_id)
                ->first();

            $stationId = $selectedLocation->station_01_id;
        }

        if (! $selectedStation) {
            $selectedStation = ClimateStation::query()->orderBy('name')->first();
            $stationId = $selectedStation?->station_id ? (string) $selectedStation->station_id : '';
        }

        if (! $selectedStation) {
            return response()->json([
                'success' => true,
                'filters' => [
                    'postcodes' => $allPostcodes,
                    'countries' => $allCountries,
                    'cities'    => [],
                    'stations'  => [],
                    'selected'  => [
                        'postcode'   => $postcode,
                        'country'    => $country,
                        'city'       => $city,
                        'station_id' => $stationId,
                    ],
                ],
                'location' => null,
                'station'  => null,
                'monthly'  => [],
                'solar'    => [],
                'charts'   => [],
                'summary'  => null,
                'message'  => 'No climate station found.',
            ]);
        }

        // Monthly thermal data: prefer detailed LTA (2004-2023), fallback to source-lta
        $monthlyRows = ClimateMonthlyData::query()
            ->where('climate_station_id', $selectedStation->id)
            ->where('dataset_scope', 'lta')
            ->whereIn('dataset_label', ['2004-2023', 'source-lta'])
            ->orderByRaw("CASE WHEN dataset_label = '2004-2023' THEN 0 ELSE 1 END")
            ->orderBy('month_num')
            ->get();

        $monthlyByMonth = [];
        foreach ($monthlyRows as $row) {
            if (! isset($monthlyByMonth[$row->month_num])) {
                $monthlyByMonth[$row->month_num] = $row;
            }
        }

        ksort($monthlyByMonth);

        $monthly = collect($monthlyByMonth)->map(function ($m) {
            return [
                'month'                => $m->month,
                'month_num'            => (int) $m->month_num,
                'days_count'           => $m->days_count !== null ? (int) $m->days_count : null,
                'temp'                 => $m->avg_temp !== null ? round((float) $m->avg_temp, 1) : 0,
                'temp_heating_days'    => $m->avg_temp_heating_days !== null ? round((float) $m->avg_temp_heating_days, 1) : null,
                'heatingDays'          => $m->heating_days !== null ? round((float) $m->heating_days, 1) : 0,
                'coolingDays'          => $m->cooling_days !== null ? round((float) $m->cooling_days, 1) : 0,
                'degreeDays'           => $m->degree_days !== null ? round((float) $m->degree_days, 1) : 0,
            ];
        })->values();

        // Solar data (LTA)
        $solarRows = ClimateSolarMonthlyData::query()
            ->where('climate_station_id', $selectedStation->id)
            ->where('dataset_scope', 'lta')
            ->whereIn('dataset_label', ['2004-2023', 'source-lta'])
            ->where('row_kind', 'month')
            ->orderByRaw("CASE WHEN dataset_label = '2004-2023' THEN 0 ELSE 1 END")
            ->orderBy('month_num')
            ->get();

        $solarHorizontal = [];
        $solarVertical = [];
        $solarTilted45 = [];

        foreach ($solarRows as $row) {
            $key = ($row->month_num ?? 0) . '|' . ($row->orientation ?? 'Hor');

            $item = [
                'month_num'    => $row->month_num ? (int) $row->month_num : null,
                'month'        => $row->month,
                'orientation'  => $row->orientation,
                'value_kwh_m2' => $row->value_kwh_m2 !== null ? round((float) $row->value_kwh_m2, 1) : 0,
            ];

            if ($row->surface_type === 'horizontal') {
                if (! isset($solarHorizontal[$key])) {
                    $solarHorizontal[$key] = $item;
                }
            } elseif ($row->surface_type === 'vertical') {
                if (! isset($solarVertical[$key])) {
                    $solarVertical[$key] = $item;
                }
            } elseif ($row->surface_type === 'tilted') {
                if (! isset($solarTilted45[$key])) {
                    $solarTilted45[$key] = $item;
                }
            }
        }
        // Horizontal monthly chart source
        $horizontalMonthly = collect($solarHorizontal)
            ->filter(fn ($r) => ($r['orientation'] ?? 'Hor') === 'Hor')
            ->sortBy('month_num')
            ->values();

        // South-facing vertical and tilted for simulation cards
        $southVerticalMonthly = collect($solarVertical)
            ->filter(fn ($r) => in_array($r['orientation'], ['S', 'South'], true))
            ->sortBy('month_num')
            ->values();

        $southTiltedMonthly = collect($solarTilted45)
            ->filter(fn ($r) => in_array($r['orientation'], ['S', 'South'], true))
            ->sortBy('month_num')
            ->values();

        $totalHeatingDays = round((float) $monthly->sum('heatingDays'), 1);
        $totalCoolingDays = round((float) $monthly->sum('coolingDays'), 1);
        $totalDegreeDays  = round((float) $monthly->sum('degreeDays'), 1);
        $avgTemp          = $monthly->count() ? round((float) $monthly->avg('temp'), 1) : 0;
        $neutralDays      = max(0, round(365 - $totalHeatingDays - $totalCoolingDays, 1));

        $totalSolarHorizontal = round((float) $horizontalMonthly->sum('value_kwh_m2'), 1);
        $totalSolarVerticalS  = round((float) $southVerticalMonthly->sum('value_kwh_m2'), 1);
        $totalSolarTiltedS    = round((float) $southTiltedMonthly->sum('value_kwh_m2'), 1);

        return response()->json([
            'success' => true,

            'filters' => [
                'postcodes' => $allPostcodes,
                'countries' => $allCountries,
                'cities'    => $allCities,
                'stations'  => $stationOptions,
                'selected'  => [
                    'postcode'   => $postcode,
                    'country'    => $country,
                    'city'       => $city,
                    'station_id' => $stationId,
                ],
            ],

            'location' => $selectedLocation ? [
                'postcode'             => $selectedLocation->postcode,
                'country'              => $selectedLocation->country_code,
                'city'                 => $selectedLocation->name,
                'lat'                  => $selectedLocation->lat !== null ? (float) $selectedLocation->lat : null,
                'lon'                  => $selectedLocation->lon !== null ? (float) $selectedLocation->lon : null,
                'alt'                  => $selectedLocation->elevation !== null ? (float) $selectedLocation->elevation : null,
                'station_01_id'        => $selectedLocation->station_01_id,
                'station_02_id'        => $selectedLocation->station_02_id,
                'station_03_id'        => $selectedLocation->station_03_id,
                'distance_station_01'  => $selectedLocation->distance_station_01 !== null ? (float) $selectedLocation->distance_station_01 : null,
                'distance_station_02'  => $selectedLocation->distance_station_02 !== null ? (float) $selectedLocation->distance_station_02 : null,
                'distance_station_03'  => $selectedLocation->distance_station_03 !== null ? (float) $selectedLocation->distance_station_03 : null,
            ] : null,

            'station' => [
                'id'         => $selectedStation->id,
                'station_id' => (string) $selectedStation->station_id,
                'name'       => $selectedStation->name,
                'region'     => $selectedStation->region ?: 'Germany',
                'country'    => $selectedStation->country_code,
                'lat'        => $selectedStation->lat !== null ? (float) $selectedStation->lat : null,
                'lon'        => $selectedStation->lon !== null ? (float) $selectedStation->lon : null,
                'alt'        => $selectedStation->elevation !== null ? (float) $selectedStation->elevation : null,
            ],

            'monthly' => $monthly,

            'solar' => [
                'horizontal' => $horizontalMonthly,
                'vertical_s' => $southVerticalMonthly,
                'tilted_s_45'=> $southTiltedMonthly,
            ],

            'charts' => [
                'months' => $monthly->pluck('month')->values(),
                'temperature' => $monthly->pluck('temp')->values(),
                'heatingDays' => $monthly->pluck('heatingDays')->values(),
                'coolingDays' => $monthly->pluck('coolingDays')->values(),
                'degreeDays'  => $monthly->pluck('degreeDays')->values(),
                'solarHorizontal' => $horizontalMonthly->pluck('value_kwh_m2')->values(),
                'solarVerticalS'  => $southVerticalMonthly->pluck('value_kwh_m2')->values(),
                'solarTiltedS45'  => $southTiltedMonthly->pluck('value_kwh_m2')->values(),
                'pie' => [
                    'labels' => ['Heiztage', 'Kühltage', 'Neutrale Tage'],
                    'values' => [$totalHeatingDays, $totalCoolingDays, $neutralDays],
                ],
                'sunSimulation' => [
                    'horizontal' => $totalSolarHorizontal,
                    'vertical_s' => $totalSolarVerticalS,
                    'tilted_s_45'=> $totalSolarTiltedS,
                ],
            ],

            'summary' => [
                'totalHeatingDays' => $totalHeatingDays,
                'totalCoolingDays' => $totalCoolingDays,
                'neutralDays'      => $neutralDays,
                'avgTemp'          => $avgTemp,
                'totalDegreeDays'  => $totalDegreeDays,
                'totalSolar'       => $totalSolarHorizontal,
                'totalSolarVerticalS' => $totalSolarVerticalS,
                'totalSolarTiltedS45' => $totalSolarTiltedS,
            ],
        ]);
    }
}