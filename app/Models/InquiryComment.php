<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryComment extends Model
{
    
    protected $fillable = [
        'inquiry_id',
        'employee_id',
        'comment',
        'parent_id',
        'likes',
        'dislikes',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function parent()
    {
        return $this->belongsTo(InquiryComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(InquiryComment::class, 'parent_id');
    }

}
