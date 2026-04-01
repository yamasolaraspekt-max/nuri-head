@php
    // Guard: bail early
    if (empty($act)) return;

    $activity = is_object($act->activity ?? null) ? $act->activity : $act;
    $phase    = is_object($act->phase ?? null) ? $act->phase : null;

    $activityId = $activity->id ?? null;
    $phaseId    = $phase->id ?? null;
    $indent     = ($level ?? 0) * 20;
    if (!$activityId || !$phaseId) return;

    // --- SAFE HELPERS (avoid "Array to string conversion") ---
    $firstOr = function ($val, $fallback = null) {
        return is_array($val) ? ($val[0] ?? $fallback) : ($val ?? $fallback);
    };
    $toStr = function ($val, $fallback = '') {
        if (is_array($val)) return implode(',', array_map('strval', $val));
        return (string)($val ?? $fallback);
    };
    $imgUrl = function ($img, $dir = 'employees', $fallback = 'default.png') use ($firstOr) {
        $img = $firstOr($img, $fallback) ?: $fallback;
        return asset("images/{$dir}/{$img}");
    };
    $fileUrl = function ($file) use ($firstOr) {
        $f = $firstOr($file, null);
        return $f ? asset('uploads/'.$f) : null;
    };

    // Children
    $subActs = $allActivities->filter(function ($item) use ($activityId) {
        $inner = is_object($item->activity ?? null) ? $item->activity : $item;
        return ($inner->parent_id ?? null) == $activityId;
    });
@endphp

@php
  $reason = is_array($act->done_reason) ? $act->done_reason : json_decode($act->done_reason ?? '{}', true);
@endphp

 
<tr
  class="activities-phase {{ (isset($currentActivityId) && $activityId == $currentActivityId) ? 'bg-gray text-black' : '' }}"
  data-activity-id="{{ $activityId }}"
  data-phase-id="{{ $phaseId }}"
  data-customer-id="{{ $customer_id }}"
  data-alternative-id="{{ $alternative_id }}"
  data-product-id="{{ $productId }}"
  data-service-id="{{ $serviceId }}"
>
  <td>{{ $loop->iteration }}</td>
  <td style="padding-left: {{ (int)$indent }}px;">
    <strong>{{ $toStr($activity->title) }}</strong><br>
    <small class="text-muted">{{ $toStr($activity->description) }}</small>
  </td>

    @php
    $reason = is_array($act->done_reason) ? $act->done_reason : json_decode($act->done_reason ?? '{}', true);
    @endphp

       <!-- Dropdown for Status -->
     <td class="text-center align-middle">
        <!-- Radio Buttons -->
        <div class=" " style="    display: flex;flex-direction: column;  justify-content: space-between; lign-items: flex-start;">
            <div class="form-check form-check-inline">
                <input class="form-check-input status-option" type="radio"
                    name="status-{{ $activityId }}"
                    value="1"
                    data-activity-id="{{ $activityId }}"
                    data-phase-id="{{ $phaseId }}"
                    {{ $act->is_done == '1' ? 'checked' : '' }}>
                <label class="form-check-label">
                    <i data-feather="check-circle" class="text-success mr-1"></i> Komplett
                </label>
            </div>
            <div class="form-check form-check-inline">
            <input class="form-check-input status-option" type="radio"
                    name="status-{{ $activityId }}"
                    value="half"
                    data-activity-id="{{ $activityId }}"
                    data-phase-id="{{ $phaseId }}"
                    {{ $act->is_done == 'half' ? 'checked' : '' }}>
                <label class="form-check-label">
                    <i data-feather="alert-circle" class="text-warning mr-1"></i> Teilweise    
                    @if(!empty($reason['percent']))  {{ $reason['percent'] }}% 
                    <i class="feather icon-info  show-done-history" style="    font-size: 16px;color: #8fc73e;" data-activity-id="{{ $activityId }}"
                        data-phase-id="{{ $phaseId }}" ></i> @endif 
                </label>
            </div>
        </div> 
    </td>
 
    <td>
        <div class="duration-wrapper" data-activity-id="{{ $activityId }}">
                <span class="duration-display">
                {{ $toStr($activity->duration, 'Unbekannt') }}
                <i class="feather icon-edit text-primary ml-1 edit-duration-btn" style="cursor:pointer;"></i>
                </span>
                <span class="duration-edit d-none">
                <input type="time"
                        class="form-control form-control-sm duration-input"
                        data-type="plan_time"
                        value="{{ $toStr($activity->duration) }}"
                        style="width:100px;display:inline-block;">
                <button class="btn btn-sm btn-success save-duration-btn">
                    <i class="feather icon-check"></i>
                </button>
                </span>
            </div>
        </td>

        @php
            // "09:30:00" -> "09:30"
            $isTime = $act->is_time ? substr((string)$act->is_time, 0, 5) : '';

            // Parse time from strings like "-HH:MM[:SS]" or "YYYY-MM-DD HH:MM:SS"
            $toMinutes = function ($t) {
                if ($t === null || $t === '') return null;
                $s = trim((string)$t);

                // Handle leading sign
                $neg = false;
                if (substr($s,0,1) === '-') { $neg = true; $s = ltrim($s, '-'); }

                // If it's a datetime, keep only the last "HH:MM[:SS]"
                if (strpos($s, ' ') !== false) {
                    $parts = explode(' ', $s);
                    $s = end($parts);
                }

                if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $s, $m)) {
                    $mins = ((int)$m[1]) * 60 + (int)$m[2];
                    return $neg ? -$mins : $mins;
                }
                return null;
            };

            // Signed minutes -> "-HH:MM:00"
            $fmtSigned = function ($mins) {
                if ($mins === null) return null;
                $neg = $mins < 0; $mins = abs($mins);
                return ($neg ? '-' : '') . sprintf('%02d:%02d:00', intdiv($mins, 60), $mins % 60);
            };

            // Prefer row plan_time; fallback to activity duration
            $planSrc = $act->plan_time ?? ($activity->duration ?? null);
            $planM   = $toMinutes($planSrc);
            $isM     = $toMinutes($act->is_time ?? null);

            // Normalize stored d_time: if it's a datetime, strip the date part
            $dRaw = (string)($act->d_time ?? '');
            if ($dRaw && preg_match('/\b(\d{2}:\d{2}:\d{2})$/', $dRaw, $mm)) {
                $dRaw = $mm[1]; // keep only H:i:s
            }
            $dTimeDisplay = $dRaw ?: null;

            // Compute diff if missing
            $diffM = $toMinutes($dTimeDisplay);
            if ($diffM === null && $planM !== null && $isM !== null) {
                $diffM = $isM - $planM;               // positive = over plan
                $dTimeDisplay = $fmtSigned($diffM);   // e.g. "-00:15:00"
            }

            $diffClass = $diffM === null
                ? ''
                : ($diffM > 0 ? 'text-danger' : ($diffM < 0 ? 'text-success' : 'text-muted'));

            // % vs plan (server-side fallback)
            if ($diffM !== null && $planM !== null && $planM > 0) {
                $pct = round(($diffM / $planM) * 100);
                $percentDisplay = ($pct > 0 ? '+' : '') . $pct . '%';
                $percentClass   = $pct > 0 ? 'text-danger' : ($pct < 0 ? 'text-success' : 'text-muted');
            } else {
                $percentDisplay = '-';
                $percentClass   = 'text-muted';
            }

            // Phase share (server-side fallback). Requires $allActivities & $phaseId
            $shareDisplay = '-';
            if (isset($allActivities, $phaseId) && $phaseId) {
                $sumAbs = 0;
                foreach ($allActivities as $pi) {
                    $piPhaseId = (is_object($pi->phase ?? null) ? $pi->phase->id : ($pi->phase_id ?? null));
                    if ($piPhaseId != $phaseId) continue;

                    $piPlanM = $toMinutes($pi->plan_time ?? (is_object($pi->activity ?? null) ? ($pi->activity->duration ?? null) : ($pi->duration ?? null)));
                    $piIsM   = $toMinutes($pi->is_time ?? null);
                    $piDiffM = $toMinutes($pi->d_time ?? null);

                    if ($piDiffM === null && $piPlanM !== null && $piIsM !== null) {
                        $piDiffM = $piIsM - $piPlanM;
                    }
                    if ($piDiffM !== null) $sumAbs += abs($piDiffM);
                }
                if ($diffM !== null && $sumAbs > 0) {
                    $shareDisplay = round((abs($diffM) / $sumAbs) * 100) . '%';
                }
            }
        @endphp

        <td>
        <input type="time"
                class="form-control form-control-sm"
                data-type="is_time"
                value="{{ $isTime }}">
        </td>

        <td class="d-time-cell {{ $diffClass }}">
        <p class="mb-0">
            <small class="d-percent-cell {{ $percentClass }}">{{ $percentDisplay }}</small>
        </p>
        {{ $dTimeDisplay ?? '' }}
        <p class="mb-0 mt-0">
            <small class="d-share-cell text-muted">{{ $shareDisplay }}</small>
        </p>
        </td>



    <td><input type="date" name="history[{{ $activityId }}][done_date]" value="{{ !empty($act->done_date) ? \Carbon\Carbon::parse($act->done_date)->format('Y-m-d') : '' }}" class="form-control form-control-sm"></td>

    @php
    $markByInitials = '–';
    $tooltip = 'Unbekannt';
    $avatar  = $imgUrl(null); // default

    if (!empty($act->marked_by)) {
        $employee = is_numeric($act->marked_by)
            ? \App\Models\Employee::find($act->marked_by)
            : \App\Models\Employee::where('name', $act->marked_by)->first();

        if ($employee) {
            $markByInitials = strtoupper(substr($employee->name, 0, 1) . substr($employee->lastname, 0, 1));
            $tooltip = trim(($employee->name ?? '').' '.($employee->lastname ?? ''));
            $avatar  = $imgUrl($employee->image, 'employees');
        }
    }
    @endphp

    <td class="mark-by-cell">
    <span class="badge badge-light-primary" data-toggle="tooltip" data-html="true" title="{{ $toStr($tooltip) }}">
        {{ $markByInitials }}
    </span>
    </td>


    @php
        $selectedEmployeeId = $act->done_by ?? null;
        $suggested = DB::table('customer_suggest_employees as cse')
            ->join('employees as e', 'e.id', '=', 'cse.employee_id')
            ->where('cse.customer_id', $customer_id)
            ->where('cse.alternative_id', $alternative_id)
            ->where('cse.product_id', $productId)
            ->where('cse.phase_id', $phaseId)
            ->where('e.status', 'Active')
            ->select('e.id', 'e.name', 'e.lastname', 'e.image', 'cse.role')
            ->get();

        $emp_list = $suggested->isEmpty()
            ? DB::table('employees')->where('status', 'Active')->get()->map(function ($e) { $e->role = null; return $e; })
            : $suggested;

        if ($selectedEmployeeId && !$emp_list->pluck('id')->contains($selectedEmployeeId)) {
            $selected = DB::table('employees')->where('id', $selectedEmployeeId)->first();
            if ($selected) {
                $selected->role = null;
                $emp_list->push($selected);
            }
        }
    @endphp

        <td>
        <select name="done_by" class="form-control employeeDone done-by-select"
                data-activity-id="{{ $activityId }}" data-phase-id="{{ $phaseId }}">
            <option value="">-- Bitte wählen --</option>
            @foreach($emp_list as $emp)
            @php $empImg = $imgUrl($emp->image ?? null, 'employees'); @endphp
            <option value="{{ $emp->id }}"
                    @if($act->done_by == $emp->id) selected @endif
                    data-image="{{ $empImg }}">
                {{ $toStr($emp->name) }} {{ $toStr($emp->lastname) }}
                @if(!empty($emp->role)) – {{ ucfirst($toStr($emp->role)) }} @endif
            </option>
            @endforeach
        </select>
        </td>


   <td>
        <div class="d-flex align-items-center">
            @php $doc = $fileUrl($act->has_document ?? null); @endphp
            @if($doc)
            <a href="{{ $doc }}" target="_blank" class="mr-1" title="Dokument anzeigen">
                <i class="feather icon-file file-icons"></i>
            </a>
            @endif

            <form action="/activity-document-upload" method="POST" enctype="multipart/form-data" class="upload-form d-flex align-items-center">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            <input type="hidden" name="alternative_id" value="{{ $alternative->id }}">
            <input type="hidden" name="phase_id" value="{{ $phaseId }}">
            <input type="hidden" name="task_id" value="{{ $activityId }}">
            <input type="hidden" name="stage" value="{{ $stage }}">

            <label class="upload-icon m-0" title="Datei hochladen">
                <i class="feather icon-upload-cloud upload-icons"></i>
                <input type="file" name="document" class="d-none" onchange="uploadActivityFile(this)">
            </label>
            </form>
            </div>
    </td>


    <td>
        <textarea class="form-control form-control-sm note-textarea"
                    rows="2"
                    data-activity-id="{{ $activityId }}"
                    data-phase-id="{{ $phaseId }}"
                    placeholder="Write note...">{{ is_array($act->notes) ? implode("\n", array_map('strval',$act->notes)) : $toStr($act->notes) }}</textarea>
        </td>

</tr>

@foreach ($subActs as $child)
    @include('admin.new_leads.layouts._activity_row', [
        'act' => $child,
        'allActivities' => $allActivities,
        'level' => $level + 1,
        'currentActivityId' => $currentActivityId
    ])
@endforeach

 