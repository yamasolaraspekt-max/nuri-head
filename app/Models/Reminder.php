<?php

// app/Models/Reminder.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'employee_id','entity_type','entity_id',
    'status','next_remind_at','last_reminded_at',
    'created_by','meta'
  ];

  protected $casts = [
    'next_remind_at' => 'datetime',
    'last_reminded_at' => 'datetime',
    'meta' => 'array',
  ];

  public function events()
  {
    return $this->hasMany(ReminderEvent::class);
  }
}
