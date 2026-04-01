 

@php
    /** @var \Illuminate\Support\Collection $phaseGroup */
    $hasRows = $phaseGroup && $phaseGroup->isNotEmpty();
@endphp

<div class="table-responsive">
  <table class="table table-bordered m-0">
    <thead class="thead-light">
      <tr>
        <th style="width:80px">Kurz</th>
        <th>Beschreibung</th>
        <th style="width:186px">Erledigt!</th>
        <th style="width:100px">Planzeit <small>(Min)</small></th>
        <th style="width:100px">Ist-zeit <small>(Min)</small></th>
        <th style="width:100px">Zeit-Abw. <small>(Min - %)</small></th>
        <th style="width:120px">Datum</th>
        <th style="width:140px">Erledigt durch</th>
        <th style="width:220px">Zuständig</th>
        <th style="width:120px">Dokument</th>
        <th>Notiz</th>
      </tr>
    </thead>
    <tbody>
    @if ($hasRows)
      @foreach ($phaseGroup->groupBy(fn($item) => optional($item->phase)->id) as $phaseIdX => $phaseActs)
        @php
          $phase         = optional($phaseActs->first())->phase;
          $allActivities = $phaseActs->whereNotNull('activity')->pluck('activity');
          $doneCountX    = $phaseActs->filter(fn($a)=>$a->is_done==1 && $a->activity)->count();
          $totalX        = $allActivities->count();
        @endphp

        @if ($phase)
          <tr class="bg-light">
            <td colspan="10">
              <strong>{{ $phase->phase_name }}</strong>
              <span class="badge badge-dark ml-2">{{ $doneCountX }} / {{ $totalX }} erledigt</span>
            </td>
          </tr>

          {{-- Root activities (children rendered inside _activity_row recursively if you do that there) --}}
          @foreach ($phaseActs->filter(fn($a)=>optional($a->activity)->parent_id === null) as $act)
            @include('admin.new_leads.layouts._activity_row', [
              'act'               => $act,
              'allActivities'     => $phaseGroup,
              'level'             => 0,

              // context objects (match what your row expects)
              'customer'          => (object)['id' => $customer_id],
              'alternative'       => (object)['id' => $alternative_id],

              'productId'         => $productId ?? null,
              'serviceId'         => $serviceId ?? null,
              'stage'             => $stage ?? null,
              'currentActivityId' => $currentActivityId ?? null,
            ])
          @endforeach

          @php
          // ——— Helpers ————————————————————————————————————————————————
          $toMinutes = function ($t) {
              if ($t === null || $t === '') return null;
              $s = trim((string)$t);

              // preserve leading sign
              $neg = false;
              if (substr($s, 0, 1) === '-') { $neg = true; $s = ltrim($s, '-'); }

              // if datetime, keep only time
              if (strpos($s, ' ') !== false) $s = explode(' ', $s)[1];

              if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $s, $m)) {
                  $mins = ((int)$m[1]) * 60 + (int)$m[2];
                  return $neg ? -$mins : $mins;
              }
              return null;
          };

          // ——— Sum for this phase ————————————————————————————————————
          $sumPlanMin = 0;   // Planzeit (in minutes)
          $sumIstMin  = 0;   // Ist-zeit (in minutes)
          $sumDiffMin = 0;   // Zeit-Abw. (in minutes)

          foreach ($phaseActs as $pi) {
              $piAct = is_object($pi->activity ?? null) ? $pi->activity : $pi;

              // Planzeit: row plan_time fallback to activity duration
              $planSrc = $pi->plan_time ?? ($piAct->duration ?? null);
              $planM   = $toMinutes($planSrc);

              // Ist-zeit: from row is_time
              $istM    = $toMinutes($pi->is_time ?? null);

              // Diff: prefer stored d_time (may be signed/time/datetime), else Ist − Plan
              $dRaw = (string)($pi->d_time ?? '');
              if ($dRaw && preg_match('/\b(-?\d{1,2}:\d{2}:\d{2})$/', $dRaw, $mm)) {
                  $dRaw = $mm[1];
              }
              $diffM = $toMinutes($dRaw);
              if ($diffM === null && $planM !== null && $istM !== null) {
                  $diffM = $istM - $planM; // positive = über Plan
              }

              if ($planM !== null) $sumPlanMin += $planM;
              if ($istM  !== null) $sumIstMin  += $istM;
              if ($diffM !== null) $sumDiffMin += $diffM;
          }

          $sumPct   = $sumPlanMin > 0 ? round(($sumDiffMin / $sumPlanMin) * 100) : null;
          $pctTxt   = $sumPct === null ? '-' : (($sumPct > 0 ? '+' : '') . $sumPct . '%');
          $diffSign = $sumDiffMin > 0 ? '+' : ''; // show + for positive
          $diffCls  = $sumDiffMin > 0 ? 'text-danger' : ($sumDiffMin < 0 ? 'text-success' : 'text-muted');
          $pctCls   = $sumPct === null ? 'text-muted' : ($sumPct > 0 ? 'text-danger' : ($sumPct < 0 ? 'text-success' : 'text-muted'));
        @endphp

        <tr class="bg-light font-weight-bold">
          <td><!-- Kurz --></td>
          <td>Summe (Phase)</td>
          <td><!-- Erledigt! --></td>

          {{-- Planzeit (Min) --}}
          <td>{{ (int) $sumPlanMin }}</td>

          {{-- Ist-zeit (Min) --}}
          <td>{{ (int) $sumIstMin }}</td>

          {{-- Zeit-Abw. (Min - %) --}}
          <td class="{{ $diffCls }}">
            {{ $diffSign }}{{ (int) $sumDiffMin }}
            <small class="{{ $pctCls }}">({{ $pctTxt }})</small>
          </td>

          {{-- Datum / Erledigt durch / Zuständig / Dokument / Notiz --}}
          <td colspan="5"></td>
        </tr>

        @endif
      @endforeach
    @else
      <tr>
        <td colspan="10" class="text-muted text-center">Keine Phasen vorhanden.</td>
      </tr>
    @endif
    </tbody>
  </table>
</div>
