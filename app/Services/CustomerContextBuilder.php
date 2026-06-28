<?php
namespace App\Services;

use App\Models\NewLeads;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerHistory; 

class CustomerContextBuilder
{
   public function build(int $customerId, bool $includeCalcs = true): array
    {
        $lead = NewLeads::query()
            ->with([
                'alternativeAddresses' => fn($q) => $q->orderByDesc('appointment')->orderByDesc('created_at'),
                'problems'             => fn($q) => $q->latest(),
                'personalTasks'        => fn($q) => $q->latest(),
            ])->findOrFail($customerId);

        // Identity
        $identity = [
            'id'          => $lead->id,
            'customer_no' => $lead->customer_no,
            'name'        => trim(($lead->title ? $lead->title.' ' : '').($lead->name ?? '').' '.($lead->lastname ?? '')),
            'email'       => $lead->email,
            'phone'       => $lead->phone ?? $lead->telephone,
            'address'     => $lead->full_address ?? trim(($lead->street ? $lead->street.', ' : '').($lead->postcode ? $lead->postcode.' ' : '').($lead->city ?? '')),
            'geo'         => ['lat' => $lead->latitude, 'lon' => $lead->longitude, 'elevation' => $lead->elevation],
            'roof'        => [
                'polygon_area'   => $lead->polygon_area,
                'polygon_height' => $lead->polygon_height,
                'polygon_width'  => $lead->polygon_width,
            ],
            'meta'        => [
                'customer_type' => $lead->customer_type,
                'status'        => $lead->status,
                'source'        => $lead->source,
            ],
        ];

        // Latest alt-address for technical bits
        $alt = optional($lead->alternativeAddresses->first());
        $tech = [
            'roof' => [
                'type'       => $alt?->roof_type,
                'covering'   => $alt?->roof_covering,
                'pitch'      => $alt?->roof_pitch,
                'direction'  => $alt?->roof_direction,
                'tile_name'  => $alt?->tile_name,
                'elevation'  => $alt?->elevation,
                'fireplace'  => $alt?->fireplace,
            ],
            'building' => [
                'year'               => $alt?->building_year ?? $lead->house_year,
                'heated_area_m2'     => $alt?->heated_area ?? $alt?->living_space,
                'stories'            => $alt?->story_count ?? $alt?->number_stories,
                'insulation_thick_mm'=> $alt?->insolation_thickness,
                'window_year'        => $alt?->window_year,
            ],
            'consumption' => [
                'electricity_kwh_a' => $alt?->total_electricity_consumption ?? $alt?->annual_consumption,
                'heating_kwh_a'     => $alt?->total_heat_consumption ?? $alt?->annual_heating_energy_consumption_kwh,
            ],
        ];

        // Appointments from lead_alternative_adds
        $appointments = $lead->alternativeAddresses->map(function($a){
            return [
                'id'         => $a->id,
                'date'       => $a->appointment,
                'by'         => $a->appointment_by,
                'objective'  => $a->objective,
                'address'    => $a->full_address ?? trim(($a->street ? $a->street.', ' : '').($a->postcode ? $a->postcode.' ' : '').($a->city ?? '')),
                'note'       => $a->note,
            ];
        })->values();

        $nextAppointment = $lead->alternativeAddresses
            ->filter(fn($a) => !empty($a->appointment) && $a->appointment >= date('Y-m-d'))
            ->sortBy('appointment')
            ->map(fn($a)=>[
                'id'   => $a->id,
                'date' => $a->appointment,
                'by'   => $a->appointment_by,
                'objective' => $a->objective,
            ])->first();

        // Problems
        $problems = $lead->problems->map(function($p){
            return [
                'id'          => $p->id,
                'ticket_no'   => $p->ticket_no,
                'status'      => $p->status,
                'priority'    => $p->priority,
                'error_type'  => $p->error_type,
                'error_code'  => $p->error_code,
                'article'     => $p->article_name,
                'date'        => $p->date,
            ];
        })->values();

        // Personal tasks
        $tasks = method_exists($lead, 'personalTasks')
            ? $lead->personalTasks->map(function($t){
                return [
                    'id'         => $t->id,
                    'title'      => $t->task_title,
                    'status'     => $t->task_status,
                    'priority'   => $t->priority,
                    'due_date'   => $t->due_date,
                    'start_date' => $t->start_date,
                    'public'     => $t->public,
                    'assigned_by'=> $t->assigned_by,
                ];
            })->values()
            : collect();

        // Calculations only if requested
        $calc = $includeCalcs ? $this->derive($identity, $tech) : null;

        return [
            'identity'        => $identity,
            'tech'            => $tech,
            'appointments'    => $appointments,
            'next_appointment'=> $nextAppointment,
            'problems'        => $problems,
            'tasks'           => $tasks,
            'calc'            => $calc,
        ];
    }


  protected function derive(array $identity, array $tech): array
{
    // ROOF AREA
    $roofArea = $identity['roof']['polygon_area']
        ?? (($identity['roof']['polygon_height'] ?? null) && ($identity['roof']['polygon_width'] ?? null)
            ? $identity['roof']['polygon_height'] * $identity['roof']['polygon_width']
            : null);

    // PV defaults
    $moduleWatt     = 430;     // W
    $moduleArea     = 1.7;     // m²
    $packingFactor  = 0.75;    // usable fraction
    $yieldKwhPerKwp = 950;     // DE ballpark

    $pv = null;
    if ($roofArea) {
        $maxModules = (int) floor(($roofArea * $packingFactor) / $moduleArea);
        $maxKwpByRoof = round(($maxModules * $moduleWatt) / 1000, 1);

        // Consumption-based sizing (if annual consumption known)
        $annualEl = (float) ($tech['consumption']['electricity_kwh_a'] ?? 0);
        $kwpByLoad = $annualEl > 0 ? round($annualEl / $yieldKwhPerKwp, 1) : null;

        // Recommend the lower of roof limit and load-based need (if both available), else what we have
        $recommendedKwp = $kwpByLoad
            ? ($maxKwpByRoof ? round(min($kwpByLoad, $maxKwpByRoof), 1) : $kwpByLoad)
            : $maxKwpByRoof;

        $recommendedModules = $recommendedKwp ? (int) round($recommendedKwp * 1000 / $moduleWatt) : $maxModules;

        $pv = [
            'available_roof_area_m2' => $roofArea ? round($roofArea, 1) : null,
            'module_area_m2'         => $moduleArea,
            'module_watt'            => $moduleWatt,
            'packing_factor'         => $packingFactor,
            'max_modules_by_roof'    => $maxModules,
            'max_kwp_by_roof'        => $maxKwpByRoof,
            'kwp_by_load'            => $kwpByLoad,          // annual consumption / yield
            'recommended_kwp'        => $recommendedKwp,
            'recommended_modules'    => $recommendedModules,
            'assumed_yield_kwh_kwp'  => $yieldKwhPerKwp,
        ];
    }

    // Heizlast estimate by year band
    $heated = (float) ($tech['building']['heated_area_m2'] ?? 0);
    $year   = (int)   ($tech['building']['year'] ?? 0);

    $specific = match (true) {
        $year && $year < 1978       => 110, // W/m²
        $year >= 1978 && $year <= 1994 => 90,
        $year >= 1995 && $year <= 2001 => 70,
        $year >= 2002 && $year <= 2015 => 55,
        $year >= 2016               => 40,
        default                     => 75,
    };

    $heizlast_w = $heated > 0 ? round($heated * $specific) : null; // W
    $heizlast_kw = $heizlast_w ? round($heizlast_w / 1000, 1) : null;
    $wp_size_kw  = $heizlast_kw ? round($heizlast_kw * 1.15, 1) : null; // +15% Reserve

    return [
        'pv_sizing'          => $pv,
        'heizlast_w'         => $heizlast_w,
        'heizlast_kw'        => $heizlast_kw,
        'wp_size_kw'         => $wp_size_kw,
        'specific_w_per_m2'  => $specific,
    ];
}


public function addRecentWorkflow(int $customerId, ?int $limit = 8): array
{
    // last N history rows with phase/activity eager-loaded
    $rows = CustomerHistory::with([
            'phase:id,phase_name,section_name,version,stage',
            'activity:id,title,section_name,version',
        ])
        ->where('customer_id', $customerId)
        ->orderByDesc('created_at')
        ->limit($limit)
        ->get();

    $items = $rows->map(function($r){
        return [
            'at'        => optional($r->created_at)->toDateTimeString(),
            'done_date' => optional($r->done_date)->toDateString(),
            'is_done'   => $r->is_done,
            'phase'     => optional($r->phase)->phase_name,
            'phase_stage' => optional($r->phase)->stage,
            'activity'  => optional($r->activity)->title,
            'section'   => optional($r->activity)->section_name ?? optional($r->phase)->section_name,
            'notes'     => str($r->notes)->limit(120)->toString(),
            'product_id'=> $r->product_id,
        ];
    })->values()->all();

    // derive "current phase/activity" from the latest non-null entries
    $current = null;
    foreach ($rows as $r) {
        if ($r->phase || $r->activity) {
            $current = [
                'phase'     => optional($r->phase)->phase_name,
                'phase_stage' => optional($r->phase)->stage,
                'activity'  => optional($r->activity)->title,
                'section'   => optional($r->activity)->section_name ?? optional($r->phase)->section_name,
                'at'        => optional($r->created_at)->toDateTimeString(),
            ];
            break;
        }
    }

    return [
        'current' => $current,
        'recent'  => $items,
    ];
}

}