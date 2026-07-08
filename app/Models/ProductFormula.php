<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductFormula extends Model
{
    use HasFactory,softDeletes;

    protected $fillable = [
        'product_id',
        'section_name',
        'fields', // JSON field
        'schema_version', // FS-02: 1 = v1-Bestand, 2 = validiertes v2-Schema
        'imported_from',  // FS-02: Herkunfts-Marker (playground-Vorlagen, Rückbau-Beweis)
        'status',
        'created_by',
        'deleted_by',
        'edited_by',
        'version'
    ];


    protected $casts = [
        'fields' => 'array',
        'schema_version' => 'integer',
    ];
    /**
     * Relationship: belongs to an article group
     */
    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
    
    public function editor()
    {
        return $this->belongsTo(Employee::class, 'edited_by');
    }
    
    public function deleter()
    {
        return $this->belongsTo(Employee::class, 'deleted_by');
    }
    
    public function filledChecklists()
{
    return $this->hasMany(LeadProductChecklistValue::class, 'product_formula_id');
}

}
