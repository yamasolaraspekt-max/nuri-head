<?php

 
namespace App\Services;

use App\Models\CustomerHistory;
use App\Models\TimeSummary;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TimeSummaryService
{
  public function recompute(int $customerId, int $alternativeId, int $productId, int $sectionId, ?int $phaseId = null): array
  {
    $base = CustomerHistory::query()
      ->where([
        ['customer_id',$customerId],
        ['alternative_id',$alternativeId],
        ['product_id',$productId],
        ['section_id',$sectionId],
      ])
      ->when($phaseId, fn($q) => $q->where('phase_id',$phaseId))
      ->whereNotNull('activity_id');

    // one row per activity: latest id within scope
    $latestIds = (clone $base)->selectRaw('MAX(id) as id')->groupBy('activity_id')->pluck('id');
    /** @var Collection<int,CustomerHistory> $rows */
    $rows = CustomerHistory::whereIn('id', $latestIds)->get(['plan_time','is_time','is_done','done_reason','done_date']);

    $toMin = function($t): int {
      if (!$t) return 0;
      if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', trim($t), $m)) {
        $h = (int)($m[1]??0); $i = (int)($m[2]??0); $s = (int)($m[3]??0);
        return $h*60 + $i + intdiv($s,60);
      }
      return 0;
    };

    $plan=$actual=$cap=$done=$half=$over=0; $count=0; $latestDone=null;

    foreach ($rows as $r) {
      $p = $toMin($r->plan_time);
      $a = $toMin($r->is_time);

      // if half done and no is_time, derive from percent of plan
      if ($a === 0 && $r->done_reason) {
        $jr = is_array($r->done_reason) ? $r->done_reason
             : (is_string($r->done_reason) ? json_decode($r->done_reason, true) : []);
        $percent = (int)($jr['percent'] ?? 0);
        if ($percent > 0 && $p > 0) $a = (int) round($p * $percent / 100);
        if ($percent > 0 && $percent < 100) $half++;
      }

      $plan += $p;
      $actual += $a;
      $cap += min($a, $p);
      $over += ($a > $p) ? 1 : 0;
      $done += ($r->is_done == 1) ? 1 : 0;
      $count++;

      if ($r->done_date) {
        $d = Carbon::parse($r->done_date);
        if (!$latestDone || $d->gt($latestDone)) $latestDone = $d;
      }
    }

    $diff = $actual - $plan;
    $percent = $plan > 0 ? (int) round($cap / $plan * 100) : 0;

    $payload = [
      'plan_minutes'           => $plan,
      'actual_minutes'         => $actual,
      'diff_minutes'           => $diff,
      'completed_cap_minutes'  => $cap,
      'weighted_percent'       => $percent,
      'activities_count'       => $count,
      'done_activities_count'  => $done,
      'half_activities_count'  => $half,
      'overruns_count'         => $over,
      'latest_done_date'       => $latestDone?->toDateString(),
    ];

    TimeSummary::updateOrCreate([
      'customer_id'  => $customerId,
      'alternative_id'=> $alternativeId,
      'product_id'   => $productId,
      'section_id'   => $sectionId,
      'scope'        => $phaseId ? 'phase' : 'total',
      'phase_id'     => $phaseId,
    ], $payload);

    return $payload;
  }

  public function recomputeBoth(int $customerId, int $alternativeId, int $productId, int $sectionId, int $phaseId): array
  {
    $phase  = $this->recompute($customerId,$alternativeId,$productId,$sectionId,$phaseId);
    $total  = $this->recompute($customerId,$alternativeId,$productId,$sectionId,null);
    return compact('phase','total');
  }
}
