<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends Model
{
    use HasFactory;

    /** Core constants for conversions */
    public const WEEKS_PER_YEAR      = 52.1429;
    public const MONTHS_PER_YEAR     = 12.0;
    public const AVG_WEEKS_PER_MONTH = self::WEEKS_PER_YEAR / self::MONTHS_PER_YEAR; // ≈ 4.34524

    /** Table name */
    protected $table = 'salaries';

    /** Allow mass assignment (lock down to fields if you prefer) */
    protected $guarded = ['id'];

    /** Default attributes */
    protected $attributes = [
        'currency'   => 'EUR',
        'status'     => 'active',
        'is_taxed'   => true,
        'tax_source' => 'employee_profile',
    ];

    /** Casts: use float for numeric math, boolean for flags, array for JSON */
    protected $casts = [
        'is_taxed' => 'boolean',

        // rates
        'income_tax_rate_pct'       => 'float',
        'social_rate_employee_pct'  => 'float',
        'social_rate_employer_pct'  => 'float',

        // base pay
        'base_hourly'   => 'float',
        'base_weekly'   => 'float',
        'base_monthly'  => 'float',
        'base_yearly'   => 'float',

        // monthly results
        'gross_monthly'                => 'float',
        'employee_deductions_monthly'  => 'float',
        'net_monthly'                  => 'float',
        'employer_contrib_monthly'     => 'float',
        'employer_total_monthly'       => 'float',

        // productivity
        'productive_hours_year' => 'float',

        // JSON blob with breakdowns
        'components' => 'array',
    ];

    /** Appended (computed) attributes for convenience in views */
    protected $appends = [
        'period_label',
        'productive_hours_month',
        'cost_per_productive_hour',
    ];

    /* =========================================================================
     | Relationships
     |======================================================================== */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    /* =========================================================================
     | Scopes
     |======================================================================== */
    public function scopeForEmployee($query, int $empId)
    {
        return $query->where('emp_id', $empId);
    }

    public function scopePeriod($query, int $year, int $month)
    {
        return $query->where('period_year', $year)->where('period_month', $month);
    }

    public function scopeForPeriodArray($query, array $period) // ['year'=>Y, 'month'=>M]
    {
        return $this->scopePeriod($query, (int)$period['year'], (int)$period['month']);
    }

    /* =========================================================================
     | Accessors (Computed)
     |======================================================================== */
    public function getPeriodLabelAttribute(): string
    {
        $m = str_pad((string)($this->period_month ?? 0), 2, '0', STR_PAD_LEFT);
        $y = (string)($this->period_year ?? '');
        return $m . '.' . $y;
    }

    public function getProductiveHoursMonthAttribute(): ?float
    {
        if ($this->productive_hours_year === null) {
            // If you stash per-month inside components, we can prefer it
            $monthly = $this->components['productive_hours_month'] ?? null;
            return $monthly !== null ? round((float)$monthly, 2) : null;
        }
        return round($this->productive_hours_year / self::MONTHS_PER_YEAR, 2);
    }

    public function getCostPerProductiveHourAttribute(): ?float
    {
        $phm = $this->productive_hours_month;
        if ($phm && $phm > 0 && $this->employer_total_monthly !== null) {
            return round($this->employer_total_monthly / $phm, 2);
        }
        return null;
    }

    /* =========================================================================
     | Helpers (optional but handy)
     |======================================================================== */

    /**
     * Derive all base periods from the authoritative contract type.
     * Mirrors your controller’s logic, available for reuse.
     */
    public static function deriveAllPeriods(
        string $contractType,
        int $hoursPerWeek,
        ?float $hourly,
        ?float $weekly,
        ?float $monthly,
        ?float $yearly
    ): array {
        $hpw = max(1, $hoursPerWeek);

        // Select authoritative type if needed
        if ($contractType === 'hourly'  && $hourly  > 0) { /* ok */ }
        elseif ($contractType === 'weekly'  && $weekly  > 0) { /* ok */ }
        elseif ($contractType === 'monthly' && $monthly > 0) { /* ok */ }
        elseif ($contractType === 'yearly'  && $yearly  > 0) { /* ok */ }
        else {
            if ($monthly > 0)       { $contractType = 'monthly'; }
            elseif ($weekly > 0)    { $contractType = 'weekly'; }
            elseif ($hourly > 0)    { $contractType = 'hourly'; }
            elseif ($yearly > 0)    { $contractType = 'yearly'; }
        }

        switch ($contractType) {
            case 'hourly':
                $h = max(0.0, (float)$hourly);
                $w = $h * $hpw;
                $m = $w * self::AVG_WEEKS_PER_MONTH;
                $y = $m * self::MONTHS_PER_YEAR;
                break;
            case 'weekly':
                $w = max(0.0, (float)$weekly);
                $h = $hpw ? ($w / $hpw) : 0.0;
                $m = $w * self::AVG_WEEKS_PER_MONTH;
                $y = $m * self::MONTHS_PER_YEAR;
                break;
            case 'yearly':
                $y = max(0.0, (float)$yearly);
                $m = $y / self::MONTHS_PER_YEAR;
                $w = $m / self::AVG_WEEKS_PER_MONTH;
                $h = $hpw ? ($w / $hpw) : 0.0;
                break;
            case 'monthly':
            default:
                $m = max(0.0, (float)$monthly);
                $w = $m / self::AVG_WEEKS_PER_MONTH;
                $h = $hpw ? ($w / $hpw) : 0.0;
                $y = $m * self::MONTHS_PER_YEAR;
                break;
        }

        return [
            'hourly'  => round($h ?? 0.0, 2),
            'weekly'  => round($w ?? 0.0, 2),
            'monthly' => round($m ?? 0.0, 2),
            'yearly'  => round($y ?? 0.0, 2),
        ];
    }

    /**
     * Super-simple monthly totals calculator. Your controller can still be authoritative.
     */
    public static function computeMonthlyTotals(float $baseMonthly, bool $isTaxed, float $incomeTaxPct, float $socialEmpPct, float $socialErPct): array
    {
        $baseMonthly = max(0.0, $baseMonthly);
        if (!$isTaxed || $baseMonthly <= 0) {
            return [
                'gross'    => $baseMonthly,
                'empDed'   => 0.0,
                'net'      => $baseMonthly,
                'erContrib'=> 0.0,
                'erTotal'  => $baseMonthly,
            ];
        }
        $empDed = $baseMonthly * (($incomeTaxPct + $socialEmpPct) / 100.0);
        $net    = $baseMonthly - $empDed;
        $erC    = $baseMonthly * ($socialErPct / 100.0);
        $erT    = $baseMonthly + $erC;

        return [
            'gross'    => round($baseMonthly,2),
            'empDed'   => round($empDed,2),
            'net'      => round($net,2),
            'erContrib'=> round($erC,2),
            'erTotal'  => round($erT,2),
        ];
    }
}
