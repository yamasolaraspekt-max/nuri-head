<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'team_id', 'employee_id', 'position_id', 'role', 'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function teams()
    {
        // through the team_members pivot
        return $this->belongsToMany(\App\Models\Team::class, 'team_members', 'employee_id', 'team_id')
                    ->withPivot(['role','position_id','sort_order'])
                    ->withTimestamps();
    }
}
