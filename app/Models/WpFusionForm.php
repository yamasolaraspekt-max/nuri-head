<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpFusionForm extends Model
{
    protected $fillable = ['form_id', 'views', 'submissions_count', 'data'];

 public function submissions()
{
    return $this->hasMany(WpFusionFormSubmission::class, 'form_id', 'form_id');
}

}

 