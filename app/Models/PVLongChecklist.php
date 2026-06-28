<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PVLongChecklist extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'desired_size',
        'pv_rafters',
        'evu_max_size',
        'note',
        'year_of_construction',
        'number_of_modules',
        'module_manufacturer',
        'type_designation',
        'kwp_size',
        'inverter',
        'system_conversion',
        'damage_defect',
        'complete_dismantling',
        'insurance_damage',
        'customer_keeps_modules',
        'customer_keeps_inverter',
        'mieterstrommodell',
        'waermepumpe',
        'echeck',
        'anzahl_we',
        'wallbox',
        'zaehlerschrank',
        'position_hak',
        'abstand_wechselrichter',
        'abstand_neuer_zaehlerschrank',
        'cabinet_size',
        'erdung',
        'zaehler_abmeldung',
        'anzahl_zaehl_plaetze',
        'fi_anzahl',
        'na_schutz',
        'rundsteuerempfaenger',
        'zaehleradapterplatte',
        'ac_ueberspannungsschutz',
        'sls_schalter',
        'apz_feld',
        'trenn_relais',
        'potentialausgleichsschiene',
        'wlan',
        'lan',
        'steckdose',
        'sonstiges',
        'sonstiges_input',
    ];

    /**
     * Get the product that owns the checklist.
     */
    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

     public function pVRoofPlans()
    {
        return $this->hasMany(PVRoofPlan::class, 'roof_id');
    }
}
