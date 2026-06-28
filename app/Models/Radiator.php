<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radiator extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
         'name',
        'description',
        
        // Electric Daten
        'dc_nennleistung_kw',
        'max_dc_leistung_kw',
        'dc_nennspanuung_v',
        'max_eingangsspannung_v',
        'max_eingangsstrom_a',
        'max_kurzschlussstrom_dc_a',
        'anzahl_dc_eingange',

        //Electric daten AC
        'ac_nennleistung_kw',
        'max_ac_leistung_kva',
        'ac_nennspannung_v',
        'anzahl_phasen',
       'trafo',

        //Electric Daten - Sonstinge
        'anderung',
        'min_einspeiseleistung_w',
        'standby_verbruahe_w',
        'nachtverbrauch_w',

        // MPP Tracker
        'leistungs_lower_20',
        'leistungs_greater_20',
        'parallelbetrieb',
        'anzahl_mpp_tracker',
       'identisch_electisch',
        'max_eingangsstrom_mpp_a',
        'max_kurzschlussstrom_mpp_a',
        'max_eingangsleistung_mpp_kw',
        'min_mpp_spannung_v',
        'max_mpp_spannung_v',
    ];
} 
