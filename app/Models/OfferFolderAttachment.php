<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferFolderAttachment extends Model
{
    use SoftDeletes;

    protected $table = 'offer_folder_attachments';

    protected $fillable = [
        'offer_folder_id',
        'offer_id',
        'title',
        'original_name',
        'file_name',
        'file_path',
        'file_url',
        'mime_type',
        'extension',
        'file_size',
        'file_type',
        'document_type',
        'notice',
        'sort_order',  
    ];

    protected $casts = [
        'offer_folder_id' => 'integer',
        'offer_id'        => 'integer',
        'file_size'       => 'integer',
        'sort_order'      => 'integer',
    ];

    public function folder()
    {
        return $this->belongsTo(OfferFolder::class, 'offer_folder_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }
}