<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Zubehör-Kategorie (Ventile, Köpfe, Adapter, Hahnblöcke, …). Reine Stammdaten. */
class AccessoryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function accessories()
    {
        return $this->hasMany(Accessory::class, 'accessory_category_id', 'id');
    }
}
