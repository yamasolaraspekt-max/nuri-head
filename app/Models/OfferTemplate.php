<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferTemplate extends Model
{
    protected $fillable = [
        'name',
        'created_by',

        'department_id',
        'article_group_id',
        'brand_id',
        'distributor_id',
        'leistung',

        'brand_color',
        'brand_mode',
        'brand_logo_url',
        'company_name',
        'cover_text_html',
        'sections',
        'placed_images',
        'biography_data',
        'description',

        'is_favorite',
        'favorite_by_employee_id',
        'favorite_at',

        'stamp',
        'stamped_by_employee_id',
        'stamped_at',

        'usage_count',
        'last_used_by_employee_id',
        'last_used_at',
    ];

   protected $casts = [
        'sections'               => 'array',
        'placed_images'          => 'array',
        'biography_data'         => 'array',
        'leistung'               => 'decimal:2',
        'is_favorite'            => 'boolean',
        'usage_count'            => 'integer',
        'favorite_at'            => 'datetime',
        'stamped_at'             => 'datetime',
        'last_used_at'           => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'article_group_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    public function favoritedByEmployees()
    {
        return $this->belongsToMany(Employee::class, 'offer_template_favorites', 'offer_template_id', 'employee_id')->withTimestamps();
    }

    public function stampedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'stamped_by_employee_id');
    }

    public function lastUsedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'last_used_by_employee_id');
    }
}