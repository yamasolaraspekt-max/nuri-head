<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 
        'category_id', 
        'deadline', 
        'end_time', 
        'add_calendar',
        'add_calendar_date', 
        'need', 
        'repeat', 
        'priority',
        'order_by', 
        'reminder_date', 
        'reminder_time', 
        'color', 
        'done_date',
        'note', 
        'is_done', 
        'user_id', 
        'is_notified'
    ];

    /**
     * Relationship to the Employee/User.
     */
    public function user()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    /**
     * Relationship to the Category.
     * This was missing and caused the error.
     */
    public function category()
    {
        return $this->belongsTo(NoteCategory::class, 'category_id');
    }
}