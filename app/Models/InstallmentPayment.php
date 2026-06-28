<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstallmentPayment extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'installment_id',
    'branch_id',
    'payment_amount',
    'payment_date',
    'paid_month_count',
    'payment_remained',
    'payment_status',
    'late_fee',
    'payment_method',
    'notes',
  ];

  protected $casts = [
    'payment_amount' => 'decimal:2',
    'payment_remained' => 'decimal:2',
    'late_fee' => 'decimal:2',
    'paid_month_count' => 'integer',
    'payment_date' => 'date',
  ];

  public function installment()
  {
    return $this->belongsTo(AssetInstallment::class, 'installment_id');
  }

  public function branch()
  {
    return $this->belongsTo(Branch::class, 'branch_id');
  }
}
