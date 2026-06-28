<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
   protected $fillable = [
    'customer_type',
    'title',
    'firma',
    'name',
    'lastname',
    'street',
    'postcode',
    'city',
    'phone',
    'telephone',
    'email',
    'contact_person',
    'product_id',
    'number_people',
    'building_type',
    'living_space',
    'construction_year',
    'heating_type',
    'consumption',
    'underfloor_heating',
    'radiator',
    'heating_manufacture_year',
    'heating_load',
    'efficiency',
    'heating_output',
    'lat',
    'lon',
    'polygon_height',
    'polygon_width',
    'polygon_area',
    'elevation',
    'alternative_address',
    'request_date',
    'document',
    'date',
    'consultation',
    'source',
    'source_info',
    'interest_rating',
    'seriousness_rating',
    'price_information_rating',
    'periority',
    'note',
    'initial_consultation',
    'status',
    'customer',
    'annual_consumption',
    'roof_type',
    'roof_age',
    'house_year',
    'heating_system_age',
    'heating_system_year',
    'heating_system_type',
    'annual_heating_energy_consumption',
    'electric_car',
    'electric_car_plan',
    'total_number',
    'answered_number',
    'inquiry_screenshot',
    'info', // New columns
    'appointment',
    'appointment_by',
    'objective',
    'unusable_space',
    'number_we',
    'number_stories',
    'installation_location',
    'installation_location_extra',
    'tile_name',
    'annual_heating_energy_consumption_kwh', 
    'roof_pitch',
    'roof_direction',
];

 

    public function product(){
        return $this->belongsToMany('App\Models\Product');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'customer_product_lists');
    }
     public function taskDocuments()
    {
        return $this->hasMany(TaskDocument::class);
    }
     public function customerPhaseStages()
    {
        return $this->hasMany(CustomerPhaseStage::class);
    }
      public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
