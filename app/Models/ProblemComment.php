<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;
class ProblemComment extends Model
{
    use AuditableLead;
    protected $fillable = [
        'ticket_id',
        'employee_id',
        'parent_id',
        'comment',
        'likes',
        'ticket_task_id'
    ];

    // 🔁 Each comment belongs to a ticket (problem)
    public function problem()
    {
        return $this->belongsTo(Problem::class, 'ticket_id');
    }

    // 👨‍💼 Each comment belongs to an employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // 💬 Parent comment (for nested comments)
    public function parent()
    {
        return $this->belongsTo(ProblemComment::class, 'parent_id');
    }

    // 💬 Replies to this comment
    public function replies()
    {
        return $this->hasMany(ProblemComment::class, 'parent_id');
    }
}
