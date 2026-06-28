<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhaseSection extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'phase_section', 'status', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function profitabilityCalculations()
    {
        return $this->hasMany(ProfitabilityCalculation::class, 'service_id');
    }

      public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }

    public function getGermanNameAttribute() {
        $map = [
            'complete' => 'Komplett',
            'maintanence' => 'Wartung', // noted typo in your prompt, kept as is
            'maintenance' => 'Wartung',
            'montage' => 'Montage',
            'product' => 'Produkt',
            'plan' => 'Planung',
            'repair' => 'Reparatur'
        ];
        return $map[strtolower($this->phase_section)] ?? ucfirst($this->phase_section);
    }

    public function taskPhases() {
        return $this->hasMany(TaskPhase::class, 'section_id')->orderBy('order');
    }

    public function stages()
    {
        return $this->hasMany(Stage::class, 'phase_section_id')->orderBy('sort_order');
    }

}
