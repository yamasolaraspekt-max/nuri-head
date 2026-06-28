<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
     protected $fillable = ['employee_id', 'title', 'description', 'image_path', 'status', 'main_feed', 'ticket_no', 'response'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
