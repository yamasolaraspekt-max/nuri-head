<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferPdfPrint extends Model
{
    protected $fillable = [
        'offer_id', 'offer_folder_id', 'employee_id', 
        'file_name', 'file_path', 'is_active', 'status'
    ];

    public function offer() { return $this->belongsTo(Offer::class); }
    public function folder() { return $this->belongsTo(OfferFolder::class, 'offer_folder_id'); }
    public function creator() { return $this->belongsTo(Employee::class, 'employee_id'); }
}