<?php

// app/Models/ReminderEvent.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderEvent extends Model
{
  protected $fillable = [
    'reminder_id','event','old_next_remind_at','new_next_remind_at',
    'note','actor_employee_id'
  ];

  protected $casts = [
    'old_next_remind_at' => 'datetime',
    'new_next_remind_at' => 'datetime',
  ];

  public function reminder()
  {
    return $this->belongsTo(Reminder::class);
  }
}
