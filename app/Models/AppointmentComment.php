<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointment_id',
        'comment_by',
        'comment',
        'status',
        'parent_id',
    ];

    /**
     * The appointment this comment belongs to.
     */
    public function appointment()
    {
        return $this->belongsTo(MainAppointment::class, 'appointment_id');
    }

    /**
     * The employee (author) who wrote the comment.
     * This is the relation you are eager-loading as "employee".
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'comment_by');
    }

    /**
     * Parent comment (for threaded comments).
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Child comments (replies).
     */
    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
