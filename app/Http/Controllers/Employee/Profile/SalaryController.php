<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    private const WEEKS_PER_YEAR      = 52.1429;
    private const MONTHS_PER_YEAR     = 12.0;
    private const AVG_WEEKS_PER_MONTH = self::WEEKS_PER_YEAR / self::MONTHS_PER_YEAR;

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $year   = $request->integer('year') ?: (int) date('Y');
        $month  = $request->integer('month') ?: (int) date('n');

        $created = $this->seedMissingSheetsForPeriod($year, $month, $search);
        $updated = $this->recomputeZeroSheetsFromEmployeeRate($year, $month, $search);

        $query = Employee::query()
            ->where('status', 'Active')
            ->with(['salaries' => function ($q) use ($year, $month) {
                $q->where('period_year', $year)->where('period_month', $month);
            }]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('lastname', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->orderBy('lastname')
            ->paginate(20)
            ->appends(compact('search', 'year', 'month'));

        return view('admin.employee.salary.salary', [
            'employees' => $employees,
            'period'    => ['year' => $year, 'month' => $month],
            'search'    => $search,
            'stats'     => ['created' => $created, 'updated' => $updated],
        ]);
    }

    public function taxDefaults(Request $request, Employee $employee)
    {
        $taxClass = strtoupper(trim((string)($employee->tax_class ?? '')));
        if ($taxClass === '') {
            $taxClass = $this->inferTaxClassFromMaritalStatus($employee->marital_status);
        }

        $defaultsByClass = ['I'=>21.000,'II'=>19.000,'III'=>12.000,'IV'=>21.000,'V'=>30.000,'VI'=>42.000];
        $income = $defaultsByClass[$taxClass] ?? 21.000;
        $socEmp = 19.700;
        $socEr  = 20.450;
        $source = 'tax_class_or_marital_default';

        try {
            $overrides = Tax::query()
                ->whereIn('tax', ['income_pct','soc_emp_pct','soc_er_pct'])
                ->where(function($q) use ($taxClass){
                    $q->whereNull('class')->orWhere('class', $taxClass);
                })
                ->get()
                ->groupBy('tax');

            $ovIncome = optional($overrides->get('income_pct')[0] ?? null)->remark;
            $ovSocEmp = optional($overrides->get('soc_emp_pct')[0]  ?? null)->remark;
            $ovSocEr  = optional($overrides->get('soc_er_pct')[0]   ?? null)->remark;

            if (is_numeric($ovIncome)) { $income = (float) $ovIncome; $source = 'tax_table_override'; }
            if (is_numeric($ovSocEmp)) { $socEmp = (float) $ovSocEmp; $source = 'tax_table_override'; }
            if (is_numeric($ovSocEr))  { $socEr  = (float) $ovSocEr;  $source = 'tax_table_override'; }
        } catch (\Throwable $e) {}

        return response()->json([
            'emp_id' => $employee->id,
            'tax_class_used' => $taxClass ?: null,
            'income_tax_rate_pct'       => round((float)$income, 3),
            'social_rate_employee_pct'  => round((float)$socEmp, 3),
            'social_rate_employer_pct'  => round((float)$socEr, 3),
            'source' => $source,
        ]);
    }

    private function inferTaxClassFromMaritalStatus(?string $marital): string
    {
        $m = strtolower(trim((string)$marital));
        return match ($m) {
            'married', 'verheiratet' => 'IV',
            'single', 'ledig' => 'I',
            'single_parent', 'alleinerziehend' => 'II',
            'widowed', 'verwitwet' => 'I',
            'separated', 'getrennt' => 'I',
            default => 'I',
        };
    }

    private function seedMissingSheetsForPeriod(int $year, int $month, string $search = ''): int
    {
        $created = 0;

        $base = Employee::query()
            ->where('status', 'Active')
            ->whereDoesntHave('salaries', fn($q) => $q->where('period_year', $year)->where('period_month', $month));

        if ($search !== '') {
            $base->where(fn($q) => $q->where('name','LIKE',"%{$search}%")->orWhere('lastname','LIKE',"%{$search}%"));
        }

        $base->chunkById(200, function($employees) use ($year, $month, &$created) {
            foreach ($employees as $emp) {
                $hourly = (float) ($emp->salary_per_hour ?? 0.0);
                $hpw    = (int)   ($emp->working_hour ?? 40);
                $wdpw   = 5;

                $derived = $this->deriveAllPeriods('hourly', $hpw, $hourly, null, null, null);
                $rates = $this->mergeEmployeeDefaultRates($emp, []);

                $baseMonthly = (float) $derived['monthly'];

                // payroll totals
                $calc = $this->computeMonthlyTotals(
                    baseMonthly: $baseMonthly,
                    isTaxed: true,
                    incomeTaxPct: (float)($rates['income'] ?? 21.0),
                    socialEmpPct: (float)($rates['socEmp'] ?? 19.7),
                    socialErPct:  (float)($rates['socEr']  ?? 20.45)
                );

                // productivity / EK (defaults)
                $kpi = $this->computeKpis(
                    hpw: $hpw,
                    wdpw: $wdpw,
                    weeksPerYear: self::WEEKS_PER_YEAR,
                    ftePct: 100.0,
                    holidayDaysYear: 0,
                    sickDaysYear: 0,
                    publicHolidaysYear: 0,
                    unproductivePct: 20.0,
                    overheadPct: 0.0,
                    employerTotalMonthly: (float)$calc['erTotal'],
                    benefitsEmployerMonthly: 0.0
                );

                Salary::create([
                    'emp_id' => $emp->id,
                    'period_year'  => $year,
                    'period_month' => $month,

                    'contract_type'=> 'hourly',
                    'working_hours_per_week' => $hpw,
                    'working_days_per_week'  => $wdpw,

                    'weeks_per_year' => self::WEEKS_PER_YEAR,
                    'fte_pct'        => 100.00,

                    'base_hourly'  => $derived['hourly'],
                    'base_weekly'  => $derived['weekly'],
                    'base_monthly' => $derived['monthly'],
                    'base_yearly'  => $derived['yearly'],

                    'is_taxed'   => true,
                    'tax_source' => 'employee_profile',
                    'tax_class'  => $emp->tax_class,
                    'health_insurer' => $emp->health_insurance,

                    'income_tax_rate_pct'      => $rates['income'],
                    'social_rate_employee_pct' => $rates['socEmp'],
                    'social_rate_employer_pct' => $rates['socEr'],

                    'gross_monthly'               => $calc['gross'],
                    'employee_deductions_monthly' => $calc['empDed'],
                    'net_monthly'                 => $calc['net'],
                    'employer_contrib_monthly'    => $calc['erContrib'],
                    'employer_total_monthly'      => $calc['erTotal'],

                    // New KPI defaults
                    'holiday_days'    => 0,
                    'sick_days'       => 0,
                    'public_holidays' => 0,
                    'unproductive_pct' => 20.000,
                    'overhead_rate_pct' => 0.000,

                    'hours_per_day'          => $kpi['hours_per_day'],
                    'planned_hours_period'   => $kpi['planned_hours_period'],
                    'absence_hours_period'   => $kpi['absence_hours_period'],
                    'effective_hours_period' => $kpi['effective_hours_period'],
                    'productive_hours_period'=> $kpi['productive_hours_period'],

                    'overhead_amount_monthly' => $kpi['overhead_amount_monthly'],
                    'fully_loaded_total_monthly' => $kpi['fully_loaded_total_monthly'],
                    'ek_productive_hourly' => $kpi['ek_productive_hourly'],

                    'currency' => 'EUR',
                    'status'   => 'active',
                ]);

                $created++;
            }
        });

        return $created;
    }

    private function recomputeZeroSheetsFromEmployeeRate(int $year, int $month, string $search = ''): int
    {
        $updated = 0;

        $rows = Salary::query()
            ->join('employees', 'employees.id', '=', 'salaries.emp_id')
            ->where('salaries.period_year', $year)
            ->where('salaries.period_month', $month)
            ->when($search !== '', function($q) use ($search) {
                $q->where(function($qq) use ($search){
                    $qq->where('employees.name','LIKE',"%{$search}%")
                       ->orWhere('employees.lastname','LIKE',"%{$search}%");
                });
            })
            ->where(function($q){
                $q->whereNull('salaries.base_hourly')->orWhere('salaries.base_hourly', 0)
                  ->orWhereNull('salaries.base_monthly')->orWhere('salaries.base_monthly', 0);
            })
            ->select('salaries.*', 'employees.salary_per_hour', 'employees.working_hour', 'employees.tax_class', 'employees.health_insurance')
            ->get();

        foreach ($rows as $s) {
            $hourly = (float) ($s->salary_per_hour ?? 0.0);
            if ($hourly <= 0) continue;

            $hpw  = (int) ($s->working_hour ?? 40);
            $wdpw = (int) ($s->working_days_per_week ?: 5);

            $derived = $this->deriveAllPeriods('hourly', $hpw, $hourly, null, null, null);

            $income = (float) ($s->income_tax_rate_pct ?? 21.0);
            $socEmp = (float) ($s->social_rate_employee_pct ?? 19.7);
            $socEr  = (float) ($s->social_rate_employer_pct ?? 20.45);

            $calc = $this->computeMonthlyTotals((float)$derived['monthly'], true, $income, $socEmp, $socEr);

            // KPIs refresh
            $kpi = $this->computeKpis(
                hpw: $hpw,
                wdpw: $wdpw,
                weeksPerYear: (float)($s->weeks_per_year ?? self::WEEKS_PER_YEAR),
                ftePct: (float)($s->fte_pct ?? 100.0),
                holidayDaysYear: (int)($s->holiday_days ?? 0),
                sickDaysYear: (int)($s->sick_days ?? 0),
                publicHolidaysYear: (int)($s->public_holidays ?? 0),
                unproductivePct: (float)($s->unproductive_pct ?? 20.0),
                overheadPct: (float)($s->overhead_rate_pct ?? 0.0),
                employerTotalMonthly: (float)$calc['erTotal'],
                benefitsEmployerMonthly: (float)($s->benefits_employer_monthly ?? 0.0)
            );

            Salary::whereKey($s->id)->update([
                'contract_type' => 'hourly',
                'working_hours_per_week' => $hpw,
                'working_days_per_week'  => $wdpw,

                'base_hourly'  => $derived['hourly'],
                'base_weekly'  => $derived['weekly'],
                'base_monthly' => $derived['monthly'],
                'base_yearly'  => $derived['yearly'],

                'gross_monthly'               => $calc['gross'],
                'employee_deductions_monthly' => $calc['empDed'],
                'net_monthly'                 => $calc['net'],
                'employer_contrib_monthly'    => $calc['erContrib'],
                'employer_total_monthly'      => $calc['erTotal'],

                'hours_per_day'          => $kpi['hours_per_day'],
                'planned_hours_period'   => $kpi['planned_hours_period'],
                'absence_hours_period'   => $kpi['absence_hours_period'],
                'effective_hours_period' => $kpi['effective_hours_period'],
                'productive_hours_period'=> $kpi['productive_hours_period'],

                'overhead_amount_monthly' => $kpi['overhead_amount_monthly'],
                'fully_loaded_total_monthly' => $kpi['fully_loaded_total_monthly'],
                'ek_productive_hourly' => $kpi['ek_productive_hourly'],
            ]);

            $updated++;
        }

        return $updated;
    }

    // ---------------------------
    // Upsert (extended)
    // ---------------------------
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'sheet_id'   => ['nullable','integer'],
            'emp_id'     => ['required','exists:employees,id'],
            'period_year'=> ['required','integer'],
            'period_month'=>['required','integer','between:1,12'],

            'contract_type' => ['required', 'in:monthly,weekly,hourly,yearly'],
            'working_hours_per_week' => ['required','integer','min:1'],
            'working_days_per_week'  => ['nullable','integer','between:1,7'],

            'base_hourly'  => ['nullable','numeric'],
            'base_weekly'  => ['nullable','numeric'],
            'base_monthly' => ['nullable','numeric'],
            'base_yearly'  => ['nullable','numeric'],

            'is_taxed'     => ['required','boolean'],
            'tax_source'   => ['required','in:employee_profile,manual'],
            'income_tax_rate_pct'      => ['nullable','numeric'],
            'social_rate_employee_pct' => ['nullable','numeric'],
            'social_rate_employer_pct' => ['nullable','numeric'],

            'gross_monthly'                => ['nullable','numeric'],
            'employee_deductions_monthly'  => ['nullable','numeric'],
            'net_monthly'                  => ['nullable','numeric'],
            'employer_contrib_monthly'     => ['nullable','numeric'],
            'employer_total_monthly'       => ['nullable','numeric'],

            // NEW fields
            'fte_pct'            => ['nullable','numeric'],
            'weeks_per_year'     => ['nullable','numeric'],
            'unproductive_pct'   => ['nullable','numeric'],
            'overhead_rate_pct'  => ['nullable','numeric'],

            'holiday_days'       => ['nullable','integer','min:0'],
            'sick_days'          => ['nullable','integer','min:0'],
            'public_holidays'    => ['nullable','integer','min:0'],

            'allowances_monthly' => ['nullable','numeric'],
            'bonuses_monthly'    => ['nullable','numeric'],
            'deductions_other_monthly' => ['nullable','numeric'],
            'benefits_employer_monthly'=> ['nullable','numeric'],

            'currency' => ['nullable','string','size:3'],
        ]);

        // ---- recompute derived periods & totals server-side (authoritative) ----
        $hpw  = (int) $data['working_hours_per_week'];
        $wdpw = (int) ($data['working_days_per_week'] ?? 5);

        $derived = $this->deriveAllPeriods(
            $data['contract_type'],
            $hpw,
            isset($data['base_hourly']) ? (float)$data['base_hourly'] : null,
            isset($data['base_weekly']) ? (float)$data['base_weekly'] : null,
            isset($data['base_monthly'])? (float)$data['base_monthly']: null,
            isset($data['base_yearly']) ? (float)$data['base_yearly'] : null
        );

        $income = (float) ($data['income_tax_rate_pct'] ?? 21.0);
        $socEmp = (float) ($data['social_rate_employee_pct'] ?? 19.7);
        $socEr  = (float) ($data['social_rate_employer_pct'] ?? 20.45);

        $calc = $this->computeMonthlyTotals((float)$derived['monthly'], (bool)$data['is_taxed'], $income, $socEmp, $socEr);

        $weeksPerYear   = (float) ($data['weeks_per_year'] ?? self::WEEKS_PER_YEAR);
        $ftePct         = (float) ($data['fte_pct'] ?? 100.0);
        $unprodPct      = (float) ($data['unproductive_pct'] ?? 20.0);
        $overheadPct    = (float) ($data['overhead_rate_pct'] ?? 0.0);

        $benefitsEr = (float) ($data['benefits_employer_monthly'] ?? 0.0);

        $kpi = $this->computeKpis(
            hpw: $hpw,
            wdpw: $wdpw,
            weeksPerYear: $weeksPerYear,
            ftePct: $ftePct,
            holidayDaysYear: (int)($data['holiday_days'] ?? 0),
            sickDaysYear: (int)($data['sick_days'] ?? 0),
            publicHolidaysYear: (int)($data['public_holidays'] ?? 0),
            unproductivePct: $unprodPct,
            overheadPct: $overheadPct,
            employerTotalMonthly: (float)$calc['erTotal'],
            benefitsEmployerMonthly: $benefitsEr
        );

        $payload = array_merge($data, [
            'working_days_per_week' => $wdpw,

            'base_hourly'  => $derived['hourly'],
            'base_weekly'  => $derived['weekly'],
            'base_monthly' => $derived['monthly'],
            'base_yearly'  => $derived['yearly'],

            'gross_monthly'               => $calc['gross'],
            'employee_deductions_monthly' => $calc['empDed'],
            'net_monthly'                 => $calc['net'],
            'employer_contrib_monthly'    => $calc['erContrib'],
            'employer_total_monthly'      => $calc['erTotal'],

            'weeks_per_year'     => $weeksPerYear,
            'fte_pct'            => $ftePct,
            'unproductive_pct'   => $unprodPct,
            'overhead_rate_pct'  => $overheadPct,

            'hours_per_day'          => $kpi['hours_per_day'],
            'planned_hours_period'   => $kpi['planned_hours_period'],
            'absence_hours_period'   => $kpi['absence_hours_period'],
            'effective_hours_period' => $kpi['effective_hours_period'],
            'productive_hours_period'=> $kpi['productive_hours_period'],
            'overhead_amount_monthly'=> $kpi['overhead_amount_monthly'],
            'fully_loaded_total_monthly'=> $kpi['fully_loaded_total_monthly'],
            'ek_productive_hourly'   => $kpi['ek_productive_hourly'],
        ]);

        $sheet = Salary::updateOrCreate(
            [
                'id' => $payload['sheet_id'] ?? null,
                'emp_id' => $payload['emp_id'],
                'period_year' => $payload['period_year'],
                'period_month'=> $payload['period_month'],
            ],
            $payload
        );

        return response()->json([
            'message' => 'Saved',
            'sheet_id'=> $sheet->id,
            'data'    => $sheet->toArray(),
        ]);
    }

    // ---------- helpers ----------

    private function deriveAllPeriods(string $contractType, int $hoursPerWeek, ?float $hourly, ?float $weekly, ?float $monthly, ?float $yearly): array
    {
        $hpw = max(1, $hoursPerWeek);

        // choose authoritative
        if (!in_array($contractType, ['hourly','weekly','monthly','yearly'], true)) $contractType = 'monthly';

        switch ($contractType) {
            case 'hourly':
                $h = max(0.0, (float) $hourly);
                $w = $h * $hpw;
                $m = $w * self::AVG_WEEKS_PER_MONTH;
                $y = $m * self::MONTHS_PER_YEAR;
                break;
            case 'weekly':
                $w = max(0.0, (float) $weekly);
                $h = $hpw ? ($w / $hpw) : 0.0;
                $m = $w * self::AVG_WEEKS_PER_MONTH;
                $y = $m * self::MONTHS_PER_YEAR;
                break;
            case 'yearly':
                $y = max(0.0, (float) $yearly);
                $m = $y / self::MONTHS_PER_YEAR;
                $w = $m / self::AVG_WEEKS_PER_MONTH;
                $h = $hpw ? ($w / $hpw) : 0.0;
                break;
            case 'monthly':
            default:
                $m = max(0.0, (float) $monthly);
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

    private function computeMonthlyTotals(float $baseMonthly, bool $isTaxed, float $incomeTaxPct, float $socialEmpPct, float $socialErPct): array
    {
        $baseMonthly = max(0.0, $baseMonthly);

        if (!$isTaxed || $baseMonthly <= 0) {
            return [
                'gross'     => round($baseMonthly, 2),
                'empDed'    => 0.0,
                'net'       => round($baseMonthly, 2),
                'erContrib' => 0.0,
                'erTotal'   => round($baseMonthly, 2),
            ];
        }

        $empDed = $baseMonthly * (($incomeTaxPct + $socialEmpPct) / 100.0);
        $net    = $baseMonthly - $empDed;

        $erC    = $baseMonthly * ($socialErPct / 100.0);
        $erT    = $baseMonthly + $erC;

        return [
            'gross'     => round($baseMonthly, 2),
            'empDed'    => round($empDed, 2),
            'net'       => round($net, 2),
            'erContrib' => round($erC, 2),
            'erTotal'   => round($erT, 2),
        ];
    }

    private function mergeEmployeeDefaultRates($e, array $in): array
    {
        $taxClass = strtoupper((string) ($e->tax_class ?? ''));
        $defaultsByClass = ['I'=>21.000,'II'=>19.000,'III'=>12.000,'IV'=>21.000,'V'=>30.000,'VI'=>42.000];
        $income = $in['income'] ?? ($defaultsByClass[$taxClass] ?? 21.000);
        $socEmp = $in['socEmp'] ?? 19.700;
        $socEr  = $in['socEr']  ?? 20.450;
        return ['income'=>$income,'socEmp'=>$socEmp,'socEr'=>$socEr];
    }

    /**
     * Computes period hours, fully loaded costs and EK €/productive hour.
     * Assumes "period" is one month using AVG_WEEKS_PER_MONTH.
     * Absences are stored yearly (days). We distribute them to month proportionally.
     */
    private function computeKpis(
        int $hpw,
        int $wdpw,
        float $weeksPerYear,
        float $ftePct,
        int $holidayDaysYear,
        int $sickDaysYear,
        int $publicHolidaysYear,
        float $unproductivePct,
        float $overheadPct,
        float $employerTotalMonthly,
        float $benefitsEmployerMonthly
    ): array {
        $wdpw = max(1, min(7, $wdpw));
        $hpw  = max(0, $hpw);

        $fte = max(0.0, min(200.0, $ftePct)) / 100.0;

        $hoursPerDay = $wdpw > 0 ? ($hpw / $wdpw) : 0.0;
        $hoursPerDay = $hoursPerDay * $fte;

        $plannedHoursMonth = ($hpw * self::AVG_WEEKS_PER_MONTH) * $fte;

        // distribute yearly absence days to month (simple proportional)
        $absenceDaysYear = max(0, $holidayDaysYear) + max(0, $sickDaysYear) + max(0, $publicHolidaysYear);
        $absenceDaysMonth = $absenceDaysYear / self::MONTHS_PER_YEAR;
        $absenceHoursMonth = $absenceDaysMonth * $hoursPerDay;

        $effectiveHoursMonth = max(0.0, $plannedHoursMonth - $absenceHoursMonth);

        $unprod = max(0.0, min(100.0, $unproductivePct)) / 100.0;
        $productiveHoursMonth = $effectiveHoursMonth * (1.0 - $unprod);

        // fully loaded monthly cost
        $baseEmployerMonthly = max(0.0, $employerTotalMonthly) + max(0.0, $benefitsEmployerMonthly);
        $overheadRate = max(0.0, $overheadPct) / 100.0;
        $overheadAmount = $baseEmployerMonthly * $overheadRate;
        $fullyLoaded = $baseEmployerMonthly + $overheadAmount;

        $ek = $productiveHoursMonth > 0 ? ($fullyLoaded / $productiveHoursMonth) : null;

        return [
            'hours_per_day' => round($hoursPerDay, 3),
            'planned_hours_period' => round($plannedHoursMonth, 3),
            'absence_hours_period' => round($absenceHoursMonth, 3),
            'effective_hours_period' => round($effectiveHoursMonth, 3),
            'productive_hours_period' => round($productiveHoursMonth, 3),

            'overhead_amount_monthly' => round($overheadAmount, 2),
            'fully_loaded_total_monthly' => round($fullyLoaded, 2),
            'ek_productive_hourly' => is_null($ek) ? null : round($ek, 4),
        ];
    }
}
