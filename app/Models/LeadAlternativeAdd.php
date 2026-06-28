<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableLead;

class LeadAlternativeAdd extends Model
{
    use AuditableLead;
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'street',
        'postcode',
        'city',
        'main',
        'lat',
        'lon',
        'elevation',
        'address_no',
        'object_name',
        'periority',
        'document',
        'note',
        'appointment',
        'appointment_by',
        'appointment_confirmed',

        'annual_consumption',
        'annual_heating_energy_consumption',
        'annual_heating_energy_consumption_kwh',
        'heating_energy_unit',
        'total_heat_consumption',
        'total_electricity_consumption',
        'electricity_price',
        'feed_in_tariff',
        'old_heating_price',

        'roof_type',
        'roof_age',
        'roof_pitch',
        'roof_direction',
        'roof_covering',
        'roof_remark',

        'house_year',
        'building_type',
        'building_condition',
        'building_length',
        'building_width',
        'facade_height',
        'total_window_area',

        'heating_system_age',
        'heating_system_year',
        'heating_system_type',
        'heating_type',
        'heating_age_group',
        'old_heating_power',
        'heat_distribution',
        'flow_temperature',
        'heating_load_calculation',
        'heating_notes',
        'heating_remark',

        'electric_car',
        'electric_car_plan',
        'electric_car_count',
        'car_kilo',
        'company_vehicle',
        'bidirectional_car',

        'wallbox_count',
        'wallbox_location',
        'charging_power',
        'access_control',

        'total_number',
        'objective',
        'number_we',
        'living_space',
        'unusable_space',
        'number_people',
        'number_stories',

        'installation_location',
        'installation_location_extra',
        'installation_location_power',

        'status',
        'request_date',
        'project_date',
        'full_address',
        'object_remark',
        'energy_remark',
        'car_remark',
        'stage',

        'fireplace',
        'wood_consumption',
        'fireplace_value',

        'is_owner',
        'owner_count',
        'owner_occupied_units',
        'rented_units',
        'owners_below_40k',
        'owners_above_40k',
        'owner_occupied_below_40k',
        'owner_occupied_above_40k',
        'rented_below_40k',
        'rented_above_40k',

        'is_living_inside',
        'income',
        'income_taxed',
        'income_level',

        'insolation',
        'insolation_thickness',
        'external_insulation_thickness',
        'insolation_type',
        'insolation_matarial',
        'insolation_age',

        'masonry',
        'masonry_thickness',

        'roof_insulation_type',
        'roof_insulation_thickness',
        'roof_insulation_year',

        'basement_insulation_type',
        'basement_insulation_thickness',
        'basement_insulation_year',

        'window_glazing',
        'window_frame',
        'window_year',
        'door_year',
        'door_condition',
        'ventilation_type',

        'usage_type',
        'natural_refrigerant',
        'investment_costs',
        'calculated_subsidy',
        'calculated_credit_need',
        'calculated_rate',
        'recommended_program',
        'subsidy_quote',

        'solar_module_kwp',
        'has_pump_upgrade',
        'balcony_modules',
        'hydraulic_only',
        'solar_thermal',
        'solar_thermal_area',
        'solar_thermal_simulation',

        'heating_circuits_count',
        'pipe_system_count',
        'pipe_system_material',
        'circulation_line',
        'heating_pipe_dimension',
        'water_pipe_dimension',
        'circulation_pipe_dimension',

        'quantity',
        'consumption',
        'bathroom_count',
        'bathtub_count',
        'hot_water_generation',
        'hot_water_tank_liters',
        'heat_pump_pipe_length',
        'basement_ceiling_height',
        'door_width_for_installation',
        'heat_pump_investment_costs',
        'heat_pump_subsidy_percent',

        'heavy_current_cable',
        'network_cable',
        'groundwork',

        'power_household',
        'power_heatpump',
        'power_electric_car',
        'power_other',
        'power_total',

        'meter_cabinet',
        'meter_cabinet_action',
        'meter_count',
        'sls_switch',
        'apz_field',
        'ac_surge_protection',
        'enwg_14a_ready',
        'grid_reserve',
        'cabinet_size',

        'tenant_model',
        'load_management',
        'network_wlan',

        'documents_invoices',
        'documents_roof_images',
        'documents_meter_images',
        'documents_window_images',
        'documents_heating_images',
        'documents_facade_images',
        'site_visit_needed',
        'ready_for_offer',

        'signature_confirm',
        'signature_name',
        'signature_date',

        'tile_name',
    ];

    protected $casts = [
        'main' => 'boolean',
        'is_owner' => 'boolean',
        'is_living_inside' => 'boolean',
        'natural_refrigerant' => 'boolean',
        'has_pump_upgrade' => 'boolean',
        'hydraulic_only' => 'boolean',
        'tenant_model' => 'boolean',
        'load_management' => 'boolean',
        'documents_invoices' => 'boolean',
        'documents_roof_images' => 'boolean',
        'documents_meter_images' => 'boolean',
        'documents_window_images' => 'boolean',
        'documents_heating_images' => 'boolean',
        'documents_facade_images' => 'boolean',
        'site_visit_needed' => 'boolean',
        'ready_for_offer' => 'boolean',
        'signature_confirm' => 'boolean',

        'appointment' => 'date',
        'request_date' => 'date',
        'project_date' => 'date',
        'signature_date' => 'datetime',

        'electricity_price' => 'decimal:2',
        'feed_in_tariff' => 'decimal:2',
        'old_heating_price' => 'decimal:2',
        'investment_costs' => 'decimal:2',
        'calculated_subsidy' => 'decimal:2',
        'calculated_credit_need' => 'decimal:2',
        'calculated_rate' => 'decimal:2',
        'subsidy_quote' => 'decimal:2',

        'building_length' => 'decimal:2',
        'building_width' => 'decimal:2',
        'facade_height' => 'decimal:2',
        'total_window_area' => 'decimal:2',

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | These fields are NOT always pure numbers.
        | Example value: "VWS 12-14 cm"
        | Therefore they must not be cast as decimal.
        |--------------------------------------------------------------------------
        */
        'masonry_thickness' => 'string',
        'external_insulation_thickness' => 'string',
        'roof_insulation_thickness' => 'string',
        'basement_insulation_thickness' => 'string',
        'insolation_thickness' => 'string',

        'old_heating_power' => 'decimal:2',
        'flow_temperature' => 'decimal:2',
        'heat_pump_pipe_length' => 'decimal:2',
        'basement_ceiling_height' => 'decimal:2',
        'door_width_for_installation' => 'decimal:2',
        'heat_pump_investment_costs' => 'decimal:2',
        'heat_pump_subsidy_percent' => 'decimal:2',
    ];

    public function newLead()
    {
        return $this->belongsTo(NewLeads::class, 'lead_id', 'id');
    }

    public function lead()
    {
        return $this->belongsTo(NewLeads::class, 'lead_id');
    }

    public function problems()
    {
        return $this->hasMany(Problem::class, 'alternative_id');
    }

    public function customerNotes()
    {
        return $this->hasMany(CustomerNote::class, 'alternative_id');
    }

    public function checklistValues()
    {
        return $this->hasMany(LeadProductChecklistValue::class, 'alternative_id');
    }

    public function roof()
    {
        return $this->hasOne(PVRoof::class, 'alternative_id', 'id');
    }

    public function offerComments()
    {
        return $this->hasMany(OfferComment::class, 'alternative_id');
    }

    public function products()
    {
        return $this->hasMany(\App\Models\LeadProductList::class, 'alternative_id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class);
    }

    public function latestScreenshot()
    {
        return $this->hasOne(\App\Models\Image::class, 'alternative_id')
            ->where('status', 'screenshot')
            ->latestOfMany();
    }

    public function leadProductLists()
    {
        return $this->hasMany(LeadProductList::class, 'alternative_id');
    }

    public function productLists()
    {
        return $this->hasMany(\App\Models\LeadProductList::class, 'alternative_id', 'id');
    }

    public function cardNotes()
    {
        return $this->hasMany(CustomerCardNote::class, 'alternative_id');
    }

    public function profitabilityCalculations()
    {
        return $this->hasMany(ProfitabilityCalculation::class, 'alternative_id');
    }

    public function profitabilityData()
    {
        return $this->hasMany(ProfitabilityData::class, 'alternative_id');
    }

    public function reports()
    {
        return $this->hasMany(CustomerReport::class, 'alternative_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'alternative_id');
    }

    public function suggestedEmployees()
    {
        return $this->hasMany(\App\Models\CustomerSuggestEmployee::class, 'alternative_id');
    }

    public function offerFolders()
    {
        return $this->hasMany(OfferFolder::class, 'alternative_id');
    }

    public function maintenanceContracts()
    {
        return $this->hasMany(MaintenanceContract::class, 'alternative_id');
    }

    public function maintenanceAssets()
    {
        return $this->hasMany(MaintenanceAsset::class, 'alternative_id');
    }

    public function maintenanceProtocols()
    {
        return $this->hasMany(MaintenanceProtocol::class, 'alternative_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'object_id');
    }

    public function goodsReceipts()
    {
        return $this->hasMany(\App\Models\GoodsReceipt::class, 'alternative_id');
    }

    public function customerProductInfos()
    {
        return $this->hasMany(\App\Models\CustomerProductInfo::class, 'alternative_id', 'id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->object_name ?: ('#' . $this->id);
    }

    public function rooms()
    {
        return $this->hasMany(\App\Models\LeadObjectRoom::class, 'alternative_id');
    }

    public function leadObjectRooms()
    {
        return $this->hasMany(\App\Models\LeadObjectRoom::class, 'alternative_id');
    }

    public function pvWpDetail()
    {
        return $this->hasOne(LeadAlternativePvWpDetail::class, 'lead_alternative_add_id', 'id');
    }

    public function roofs()
    {
        return $this->hasMany(\App\Models\PVRoof::class, 'alternative_id', 'id');
    }
    public function reviews()
    {
        return $this->hasMany(\App\Models\CustomerReview::class, 'alternative_id');
    }
}