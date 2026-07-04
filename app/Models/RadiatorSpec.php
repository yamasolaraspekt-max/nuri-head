<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Heizkörper-Katalog (EN 442, parametrisch). Reine Stammdaten; Rechnen im Adapter/Service. */
class RadiatorSpec extends Model
{
    use HasFactory;

    protected $table = 'product_radiator_specs';

    protected $fillable = [
        'product_id',
        'hersteller',
        'typ',
        'bauart',
        'bauhoehe_mm',
        'bautiefe_mm',
        'q_norm_w_pro_m',
        'norm_bedingung',
        'exponent_n',
        'quelle',
        'imported_from',
        'aktiv',
    ];

    protected $casts = [
        'bauhoehe_mm' => 'integer',
        'bautiefe_mm' => 'integer',
        'q_norm_w_pro_m' => 'decimal:2',
        'exponent_n' => 'decimal:2',
        'aktiv' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function installations()
    {
        return $this->hasMany(RadiatorInstallation::class, 'radiator_spec_id', 'id');
    }
}
