<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class OfferRoofLayoutConfiguration extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'offer_id',
        'offer_folder_id',
        'offer_detail_id',
        'offer_template_id',
        'enabled',
        'title',
        'offer_number',
        'system_power_kwp',
        'module_count',
        'module_power_wp',
        'selected_roofs',
        'show_all_icons',
        'compass_image_path',
        'canvas_layout',
        'canvas_design_width',
        'canvas_design_height',
        'note',
        'footer_company',
        'meta',
        'created_by_employee_id',
        'updated_by_employee_id',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'show_all_icons' => 'boolean',
        'selected_roofs' => 'array',
        'canvas_layout' => 'array',
        'meta' => 'array',
        'module_count' => 'integer',
        'module_power_wp' => 'integer',
        'canvas_design_width' => 'integer',
        'canvas_design_height' => 'integer',
    ];

    protected $appends = [
        'compass_image_url',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function folder()
    {
        return $this->belongsTo(OfferFolder::class, 'offer_folder_id');
    }

    public function detail()
    {
        return $this->belongsTo(OfferDetail::class, 'offer_detail_id');
    }

    public function template()
    {
        return $this->belongsTo(OfferTemplate::class, 'offer_template_id');
    }

    public function getCompassImageUrlAttribute(): ?string
    {
        $path = trim((string) ($this->compass_image_path ?? ''));

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return $path;
        }

        return Storage::disk('public')->url(ltrim($path, '/'));
    }
}
