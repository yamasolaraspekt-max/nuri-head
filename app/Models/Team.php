<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'department_id', 'status', 'description'
    ];

    /* Relationships */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function membersAll() // all roles
    {
        return $this->hasMany(TeamMember::class)->with(['employee','position'])->orderBy('sort_order');
    }

    public function leader()
    {
        return $this->hasOne(TeamMember::class)->where('role', 'leader')->with(['employee','position']);
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class)->where('role', 'member')->with(['employee','position'])->orderBy('sort_order');
    }

    public function reserves()
    {
        return $this->hasMany(TeamMember::class)->where('role', 'reserve')->with(['employee','position'])->orderBy('sort_order');
    }

    public function teamMembers()
    {
        return $this->hasMany(\App\Models\TeamMember::class, 'employee_id');
    }
}
