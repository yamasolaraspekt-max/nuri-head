<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpFusionFormField extends Model
{
    protected $fillable = ['id', 'form_id', 'field_name', 'field_label', 'data'];
}
