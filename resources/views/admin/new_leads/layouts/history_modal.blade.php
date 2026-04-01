<style>
/* ============= SHELL ============= */
.phase-drawer-shell {
    padding: 12px 16px 18px;
    background: #f3f4f6;
    font-size: 13px;
}

.phase-drawer-shell * {
    box-sizing: border-box;
}

/* ============= TOP INFO GRID ============= */
.phase-header-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 2fr);
    gap: 10px;
    margin-bottom: 10px;
}

@media (min-width: 992px) {
    .phase-header-grid {
        grid-template-columns: minmax(0, 3fr) minmax(0, 2fr) minmax(0, 2fr) minmax(0, 2fr);
    }
}

.phase-chip {
    background: #ffffff;
    border-radius: 14px;
    padding: 10px 12px;
    box-shadow: 0 4px 10px rgba(15,23,42,0.06);
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-height: 70px;
}

.phase-chip-title {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #6b7280;
    font-weight: 600;
}

.phase-chip-main {
    font-size: 13px;
    color: #111827;
}

.phase-chip-sub {
    font-size: 12px;
    color: #6b7280;
}

/* customer + product visuals */
.phase-customer-main {
    font-weight: 600;
    text-transform: uppercase;
}

.phase-product-main {
    display: flex;
    align-items: center;
    gap: 10px;
}

.phase-product-initial {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    background: #2563eb;
    color: #ffffff;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.phase-product-avatar {
    width: 26px;
    height: 26px;
    border-radius: 999px;
    overflow: hidden;
    flex-shrink: 0;
}

.phase-product-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Note block */
.phase-note-teaser {
    cursor: pointer;
    max-height: 60px;
    overflow: hidden;
    position: relative;
}

.phase-note-teaser::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0; right: 0;
    height: 16px;
    background: linear-gradient(to bottom, rgba(255,255,255,0), #ffffff);
}

/* summary line */
.phase-summary-line {
    margin: 8px 0 2px;
    padding: 6px 10px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #e5f0ff;
    font-size: 11px;
    color: #1e293b;
}

/* close button chip */
.phase-chip-close {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ============= STAGE ACCORDION ============= */

.phase-stage-accordion {
    margin-top: 10px;
}

.phase-stage-card {
    background: #ffffff;
    border-radius: 16px;
    margin-bottom: 8px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(15,23,42,0.06);
}

/* header row */
.phase-stage-head {
    display: grid;
    grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.6fr) minmax(0, 2fr) auto;
    gap: 12px;
    padding: 10px 12px;
    cursor: pointer;
    align-items: center;
}

@media (max-width: 991.98px) {
    .phase-stage-head {
        grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.3fr) auto;
    }
    .phase-stage-head .phase-stage-next {
        grid-column: 1 / -1;
    }
}

.phase-stage-head.is-active {
    background: #f9fafb;
}

.phase-stage-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #111827;
}

.phase-stage-progress {
    margin-top: 4px;
}

.phase-stage-progress .progress {
    height: 7px;
    border-radius: 999px;
    overflow: hidden;
}

.phase-stage-progress .progress-bar {
    border-radius: 999px;
}

/* avatars stack */
.phase-avatar-stack {
    display: flex;
    align-items: center;
}

.phase-avatar-ring {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    border: 2px solid #e5e7eb;
    overflow: hidden;
    margin-left: -8px;
    background: #f9fafb;
}

.phase-avatar-ring:first-child {
    margin-left: 0;
}

.phase-avatar-ring img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* next step info */
.phase-stage-next {
    font-size: 12px;
}

.phase-stage-next .title {
    font-weight: 600;
    color: #111827;
}

.phase-stage-next .desc {
    color: #6b7280;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* stage actions */
.phase-stage-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    justify-content: flex-end;
}

.phase-stage-toggle {
    border-radius: 999px !important;
    padding: 4px 10px;
}

/* body */
.phase-stage-body {
    border-top: 1px solid #e5e7eb;
    padding: 8px 10px 10px;
}

/* accordion mechanics (custom, no Bootstrap) */
.phase-stage-panel {
    overflow: hidden;
    max-height: 0;
    opacity: 0;
    transition: max-height 0.25s ease, opacity 0.2s ease;
}

.phase-stage-panel.is-open {
    opacity: 1;
}

/* ============= ACTIVITIES TABLE ============= */

.phase-activities-wrap {
    width: 100%;
    overflow-x: auto;
}

.phase-activities-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.phase-activities-table thead th {
    white-space: nowrap;
    padding: 6px 8px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 600;
    color: #4b5563;
}

.phase-activities-table tbody td {
    padding: 6px 8px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: top;
}

/* phase sub-header row */
.phase-row-header {
    background: #f1f5f9;
}

.phase-row-header td {
    font-weight: 600;
    font-size: 12px;
    color: #0f172a;
}

/* activity title + desc */
.activity-main-title {
    font-weight: 600;
    color: #111827;
    margin-bottom: 2px;
}

.activity-main-desc {
    font-size: 11px;
    color: #6b7280;
}

/* status pill group */
.status-pill-group {
    display: inline-flex;
    flex-direction: column;
    gap: 2px;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border-radius: 999px;
    padding: 3px 8px;
    border: 1px solid transparent;
    font-size: 11px;
    cursor: pointer;
    user-select: none;
    transition: background-color 0.15s ease, border-color 0.15s ease,
                box-shadow 0.15s ease, transform 0.15s ease, opacity 0.15s ease;
    opacity: 0.6;
    position: relative;
}

/* small visual dot */
.status-pill::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: transparent;
}

.status-pill.is-active {
    opacity: 1;
    box-shadow: 0 0 0 1px rgba(15,23,42,0.10);
    transform: translateY(-1px);
}

/* strong color per state when active */
.status-pill-open.is-active {
    border-color: #60a5fa;
    background: #e0f2fe;
    color: #0f172a;
}

.status-pill-half.is-active {
    border-color: #facc15;
    background: #fef3c7;
    color: #92400e;
}

.status-pill-done.is-active {
    border-color: #22c55e;
    background: #dcfce7;
    color: #14532d;
}

/* dot uses current text color when active */
.status-pill.is-active::before {
    background: currentColor;
}

/* hide original radios */
.status-pill input[type="radio"] {
    display: none;
}

.status-pill-open {
    border-color: #e5e7eb;
    background: #f9fafb;
    color: #4b5563;
}

.status-pill-half {
    border-color: #fbbf24;
    background: #fffbeb;
    color: #92400e;
}

.status-pill-done {
    border-color: #22c55e;
    background: #ecfdf3;
    color: #166534;
}

.status-pill.is-active {
    box-shadow: 0 0 0 1px rgba(15,23,42,0.08);
}

/* duration editors */
.duration-wrapper {
    font-size: 11px;
}

.duration-display {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.duration-edit {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* diff cell */
.d-time-cell {
    min-width: 80px;
}

.d-time-cell small {
    display: block;
}

/* done-by + marker badge */
.mark-by-cell .badge {
    font-size: 11px;
}

/* note textarea */
.note-textarea {
    min-width: 160px;
}

/* responsive tweaks */
@media (max-width: 767.98px) {
    .phase-chip {
        min-height: auto;
    }
    .note-textarea {
        min-width: 120px;
    }
}
</style>

@php
    use Illuminate\Support\Str;

    $services = [
        'complete'   => 'Komplettlösung',
        'montage'    => 'Montage',
        'product'    => 'Produkt',
        'plan'       => 'Planung',
        'maintenance'=> 'Wartung',
        'repair'     => 'Reparatur',
        'emergency'  => 'Notdienst',
        'others'     => 'Sonstiges'
    ];

    $interests = [
        'intent'   => 'Kaufabsicht',
        'interest' => 'Kaufinteresse',
        'option'   => 'Kaufoption'
    ];

    $translatedStages = [
        'lead'      => 'Lead',
        'Lead'      => 'Lead',
        'open'      => 'Lead',
        'offer'     => 'Angebot',
        'deal'      => 'Auftrag',
        'project'   => 'Montage',
        'ticket'    => 'Ticket',
        'review'    => 'Auswertung',
        'archive'   => 'Archiv',
        'completed' => 'Abgeschlossen',
        'junk'      => 'Junk',
        'cancel'    => 'Absage',
        'pause'     => 'Pause',
    ];

    $allStages = array_keys($groupedPhases);

    // active phase for summary
    $activeGroup = $groupedPhases[$currentStageKey] ?? collect();
    if ($activeGroup->isEmpty()) {
        $activeGroup = collect($groupedPhases)->first(fn($c) => $c && $c->count()) ?? collect();
    }
    $headerFirstItem = $activeGroup->first();
    $activePhaseId   = optional(optional($headerFirstItem)->phase)->id;

    // time summary helpers
    $fmtHM = function ($mins) {
        if ($mins === null) return '--:--';
        $mins = (int)$mins;
        $sign = $mins < 0 ? '-' : '';
        $mins = abs($mins);
        return $sign . sprintf('%02d:%02d', intdiv($mins, 60), $mins % 60);
    };

    // cached phase summaries
    $sumPlan = $sumIs = $sumDiff = null;
    $sumPct  = null;

    if (!empty($activePhaseId ?? null) && isset($timeSummariesPhase[$activePhaseId])) {
        $ts = $timeSummariesPhase[$activePhaseId];
        $sumPlan = (int) $ts->plan_minutes;
        $sumIs   = (int) $ts->actual_minutes;
        $sumDiff = (int) $ts->diff_minutes;
        $sumPct  = $ts->weighted_percent ?? ($sumPlan > 0 ? round(($sumDiff / $sumPlan) * 100) : null);
    }

    $diffCls   = $sumDiff > 0 ? 'text-danger' : ($sumDiff < 0 ? 'text-success' : 'text-muted');
    $pctCls    = is_null($sumPct) ? 'text-muted' : ($sumPct > 0 ? 'text-danger' : ($sumPct < 0 ? 'text-success' : 'text-muted'));
    $iconName  = is_null($sumPct) ? 'minus-circle' : ($sumDiff > 0 ? 'thumbs-down' : ($sumDiff < 0 ? 'thumbs-up' : 'check-circle'));
    $iconClass = is_null($sumPct) ? 'text-muted' : ($sumDiff > 0 ? 'text-danger' : ($sumDiff < 0 ? 'text-success' : 'text-secondary'));

    // stage list + overall progress
    $allStages       = collect($groupedPhases)->keys();
    $totalActivities = collect($groupedPhases)->flatten(1)->filter(fn($r) => $r->activity)->count();
    $doneActivities  = collect($groupedPhases)->flatten(1)->filter(fn($r) => $r->is_done == 1)->count();
    $overallPercent  = $totalActivities > 0 ? round(($doneActivities / $totalActivities) * 100) : 0;

    // employees
    $employees = \App\Models\Employee::where('status', 'Active')
          ->select('id', 'name', 'lastname', 'image')
          ->orderBy('name')
          ->get();

    $roleColors = [
        'team'          => '#17a2b8',
        'leader'        => '#28a745',
        'representative'=> '#ffc107',
        'monteur'       => '#007bff',
        'obermonteur'   => '#6610f2',
        'helper'        => '#6c757d',
        'innendienst'   => '#fd7e14',
        'aussendienst'  => '#20c997',
        'bauleiter'     => '#dc3545',
        'buchhaltung'   => '#343a40',
        'techniker'     => '#6f42c1',
        'controller'    => '#e83e8c',
    ];

    // suggested employees
    $allSuggested = \App\Models\CustomerSuggestEmployee::with(['employee','department'])
        ->where('customer_id', $customer_id)
        ->where('alternative_id', $alternative_id)
        ->where('product_id', $productId)
        ->get();
@endphp

<div class="phase-drawer-shell">

    {{-- ========== TOP SUMMARY STRIP ========== --}}
    <div class="phase-header-grid">
        {{-- Customer --}}
        <div class="phase-chip">
            <div class="phase-chip-title">Kunde</div>
            <div class="phase-chip-main phase-customer-main">
                {{ $customer->title }} {{ $customer->name }} {{ $customer->lastname }}
            </div>
            @if($customer->firma)
                <div class="phase-chip-sub">Firma: {{ $customer->firma }}</div>
            @endif
            <div class="phase-chip-sub">
                Angelegt am {{ \Carbon\Carbon::parse($customer->created_at)->isoFormat('DD.MM.YYYY') }}
                · Quelle: {{ $customer->source }}
            </div>
        </div>

        {{-- Adresse --}}
        <div class="phase-chip">
            <div class="phase-chip-title">Adresse</div>
            <div class="phase-chip-main">
                {{ $customer->street }}<br>
                {{ $customer->postcode }} {{ $customer->city }}
            </div>
            <div class="phase-chip-sub">
                {{ $customer->email }}<br>
                {{ $customer->phone }}@if($customer->mobile) · {{ $customer->mobile }}@endif
            </div>
        </div>

        {{-- Produkt / Status --}}
        <div class="phase-chip">
            <div class="phase-chip-title">Produkt & Status</div>
            <div class="phase-product-main">
                <div class="phase-product-initial">
                    {{ $productList->initial ?? 'NA' }}
                </div>
                @if($productList->image)
                    <div class="phase-product-avatar">
                        <img src="{{ asset('images/employee/'.$productList->image) }}" alt="">
                    </div>
                @endif
                <div>
                    <div class="phase-chip-main">
                        {{ $productList->department_name ?? 'Keine Abteilung' }}
                    </div>
                    <div class="phase-chip-sub">
                        {{ $services[$productList->phase_section] ?? $productList->phase_section }}
                        ·
                        {{ $interests[$productList->interest] ?? $productList->interest }}
                    </div>
                </div>
            </div>

            <div class="phase-summary-line mt-2">
                <span><strong>Gesamt:</strong> {{ $doneActivities }}/{{ $totalActivities }}</span>
                <span>·</span>
                <span><strong>{{ $overallPercent }}%</strong> erledigt</span>
                @if($activePhaseId)
                    <span>·</span>
                    <span>
                        <strong>P:</strong> {{ $fmtHM($sumPlan) }}
                        · <strong>I:</strong> {{ $fmtHM($sumIs) }}
                        · <strong>D:</strong> <span class="{{ $diffCls }}">{{ $sumDiff > 0 ? '+' : '' }}{{ $fmtHM($sumDiff) }}</span>
                        · <strong>%:</strong> <span class="{{ $pctCls }}">{{ is_null($sumPct) ? '--' : (($sumPct > 0 ? '+' : '').$sumPct.'%') }}</span>
                    </span>
                    <i class="feather icon-{{ $iconName }} {{ $iconClass }}"></i>
                @endif
            </div>
        </div>

        {{-- Notiz + Phase + Close --}}
        <div class="phase-chip">
            <div class="phase-chip-title">Projekt-Notiz & Phase</div>
            <div id="noteContainer"
                 class="mb-2"
                 data-customer="{{ $customer_id }}"
                 data-alternative="{{ $alternative_id }}"
                 data-product="{{ $productId }}"
                 data-title="{{ $note->title ?? '' }}"
                 data-description="{{ $note->description ?? '' }}">
                @if($note)
                    <div id="noteView" onclick="openNoteEditor()" style="cursor:pointer">
                        <div class="phase-chip-main" id="noteTitle">{{ $note->title }}</div>
                        <div class="phase-note-teaser" id="noteDescription">
                            {!! nl2br(e($note->description)) !!}
                        </div>
                    </div>
                @else
                    <div class="text-muted phase-note-teaser" onclick="openNoteEditor()" style="cursor:pointer">
                        <i class="fas fa-pen"></i> Klicken Sie hier, um eine Notiz hinzuzufügen
                    </div>
                @endif
            </div>

            <div class="d-flex align-items-center justify-content-between mt-auto">
                <div>
                    <div class="phase-chip-title mb-1">Aktuelle Phase</div>
                    <div class="phase-chip-main">
                        {{ $translatedStages[$stage] ?? ucfirst($stage) }}
                    </div>
                </div>
                <div class="phase-chip-close">
                    <button class="btn btn-outline-primary closePhase"
                            onclick="closePhaseSidebar()" aria-label="Seitenleiste schließen">
                        ×
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== STAGE ACCORDION ========== --}}
    <div class="phase-stage-accordion" data-nx-accordion="stages">
        @foreach ($allStages as $stageKey)
            @php
                /** @var \Illuminate\Support\Collection $phaseGroup */
                $phaseGroup = $groupedPhases[$stageKey] ?? collect();

                $firstItem = $phaseGroup->first();

                $activeStageKey = $stage ?? $currentStageKey;
                $isCurrentStage = ($activeStageKey === $stageKey);

                $stageLabel = strtoupper($translatedStages[$stageKey] ?? $stageKey);

                $total     = $phaseGroup->whereNotNull('activity')->count();
                $doneCount = $phaseGroup->filter(fn($r) => $r->is_done == 1 && $r->activity)->count();
                $pct       = $total ? round(($doneCount / $total) * 100) : 0;

                $phaseId        = optional(optional($firstItem)->phase)->id;
                $productIdBlock = optional($firstItem)->product_id ?? $productId;
                $stageId        = optional($firstItem)->stage_id ?? null;

                $safeStageKey = Str::slug($stageKey,'-') ?: 'stage';
                $panelId      = "stage-panel-{$safeStageKey}-{$loop->index}";

                $suggestedEmployees = $phaseId
                    ? $allSuggested->where('phase_id', $phaseId)
                    : collect();

                $phasesInStage = $phaseGroup->groupBy(fn($r) => optional($r->phase)->id);

                $nextRealActivity = null;
                if ($phaseId) {
                    $phaseActsForPhase = \App\Models\PhaseActivities::where('phase_id', $phaseId)
                        ->orderBy('sort_order')
                        ->get();

                    foreach ($phaseActsForPhase as $act) {
                        $isDone = \App\Models\CustomerHistory::where([
                            ['customer_id',    $customer_id],
                            ['alternative_id', $alternative_id],
                            ['product_id',     $productIdBlock],
                            ['phase_id',       $phaseId],
                            ['activity_id',    $act->id],
                            ['section_id',     $serviceId],
                            ['is_done',        1],
                        ])->exists();

                        if (! $isDone) {
                            $nextRealActivity = $act;
                            break;
                        }
                    }
                }
            @endphp

            <div class="phase-stage-card" data-stage-key="{{ $stageKey }}">
                {{-- HEAD --}}
                <div class="phase-stage-head {{ $isCurrentStage ? 'is-active' : '' }}"
                     role="button"
                     data-panel-id="{{ $panelId }}"
                     aria-expanded="{{ $isCurrentStage ? 'true' : 'false' }}">

                    {{-- Title + progress --}}
                    <div>
                        <div class="phase-stage-title">{{ $stageLabel }}</div>
                        <div class="phase-stage-progress">
                            <div class="progress">
                                <div class="progress-bar bg-success"
                                     style="width: {{ $pct }}%">
                                    {{ $doneCount }}/{{ $total }} · {{ $pct }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mitarbeiter avatars --}}
                    <div>
                        <div class="text-muted mb-1 font-weight-bold" style="font-size:11px;">Mitarbeiter</div>
                        <div class="phase-avatar-stack">
                            @if($phaseId)
                                @foreach($suggestedEmployees->take(6) as $sug)
                                    @php
                                        $emp   = $sug->employee;
                                        $color = $roleColors[$sug->role ?? ''] ?? '#cbd5e1';
                                    @endphp
                                    @if($emp)
                                        <button type="button"
                                                class="btn p-0 border-0 bg-transparent edit-suggested-employee"
                                                title="{{ $emp->name }} {{ $emp->lastname }} – {{ $sug->department->department_name ?? '—' }} ({{ $sug->role ?? '—' }})"
                                                data-suggestion-id="{{ $sug->id }}"
                                                data-employee-id="{{ $emp->id }}"
                                                data-employee-name="{{ $emp->name }} {{ $emp->lastname }}"
                                                data-customer-id="{{ $customer_id }}"
                                                data-alternative-id="{{ $alternative_id }}"
                                                data-product-id="{{ $productIdBlock }}"
                                                data-phase-id="{{ $phaseId }}"
                                                data-role="{{ $sug->role }}"
                                                data-department-id="{{ $sug->department_id }}">
                                            <div class="phase-avatar-ring" style="border-color: {{ $color }}">
                                                <img src="{{ asset('images/employee/'.$emp->image) }}"
                                                     alt="{{ $emp->name }}">
                                            </div>
                                        </button>
                                    @endif
                                @endforeach

                                <button type="button"
                                        class="btn btn-sm btn-warning ml-1 suggest-employees-btn"
                                        data-customer-id="{{ $customer_id }}"
                                        data-alternative-id="{{ $alternative_id }}"
                                        data-product-id="{{ $productIdBlock }}"
                                        data-phase-id="{{ $phaseId }}">
                                    <i class="feather icon-user-plus"></i>
                                </button>
                            @else
                                <span class="text-muted" style="font-size:11px;">
                                    Keine Phase / Mitarbeiterzuordnung vorhanden
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Next step --}}
                    <div class="phase-stage-next">
                        <div class="text-muted mb-1 font-weight-bold" style="font-size:11px;">Nächster Schritt</div>

                        @if($phaseGroup->isEmpty())
                            <div class="text-muted">
                                Für diese Phase wurden noch keine Aufgaben definiert.
                            </div>
                        @else
                            @if($nextRealActivity)
                                <div class="title">{{ $nextRealActivity->title }}</div>
                                <div class="desc" title="{{ $nextRealActivity->description }}">
                                    {{ $nextRealActivity->description }}
                                </div>
                            @else
                                <div class="text-muted">Alle Schritte erledigt 🎉</div>
                            @endif
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="phase-stage-actions">
                        @if($firstItem && !empty($productIdBlock))
                            <button type="button" style="border-radius: 50% !important; padding: 12px;"
                                    class="btn btn-outline-danger change_stages"
                                    data-customer-id="{{ $customer_id }}"
                                    data-alternative-id="{{ $alternative_id }}"
                                    data-product-id="{{ $productIdBlock }}"
                                    data-phase-id="{{ $phaseId }}"
                                    data-stage="{{ $stageId }}"
                                    data-service="{{ $firstItem->service ?? null }}"
                                    data-service-id="{{ $firstItem->service_id ?? null }}"
                                    data-employee-id="{{ $firstItem->employee_id ?? 0 }}"
                                    data-department-id="{{ $firstItem->department_id ?? 0 }}">
                                <i class="feather icon-git-branch"></i>
                            </button>
                        @endif

                        <button type="button"
                                class="btn btn-outline-primary phase-stage-toggle"
                                data-panel-id="{{ $panelId }}"
                                aria-expanded="{{ $isCurrentStage ? 'true' : 'false' }}">
                            <i class="feather icon-chevron-{{ $isCurrentStage ? 'up' : 'down' }}"></i>
                        </button>
                    </div>
                </div>

                {{-- BODY / PANEL --}}
                <div id="{{ $panelId }}"
                     class="phase-stage-panel {{ $isCurrentStage ? 'is-open' : '' }}"
                     role="region">
                    <div class="phase-stage-body">
                        <div class="phase-activities-wrap">
                            <table class="phase-activities-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Aufgabe</th>
                                    <th>Status</th>
                                    <th>Plan</th>
                                    <th>Ist</th>
                                    <th>Diff</th>
                                    <th>Erledigt am</th>
                                    <th>Markiert von</th>
                                    <th>Zuständig</th>
                                    <th>Dokument</th>
                                    <th>Notiz</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($phaseGroup->isEmpty())
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-3">
                                            Für diese Phase wurden noch keine Aufgaben definiert.
                                        </td>
                                    </tr>
                                @else
                                    @foreach($phasesInStage as $phaseIdBlock => $rows)
                                        @php
                                            $phaseObj = optional($rows->first())->phase;
                                        @endphp
                                        @if($phaseObj)
                                            {{-- Phase header row --}}
                                            <tr class="phase-row-header">
                                                <td colspan="11">
                                                    <strong>{{ $phaseObj->phase_name }}</strong>
                                                    @if($phaseObj->phase_description)
                                                        <span class="text-muted">
                                                            · {{ $phaseObj->phase_description }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif

                                        @foreach($rows as $index => $row)
                                            @php
                                                $activity = $row->activity;
                                                if (!$activity) continue;

                                                $history = $phaseActs
                                                    ->firstWhere(function ($h) use ($row, $activity) {
                                                        return $h->phase_id    == $row->phase->id
                                                            && $h->activity_id == $activity->id;
                                                    });

                                                $isDone = (string) $row->is_done === '1';
                                                $isHalf = (string) $row->is_done === 'half'
                                                    || (!empty($row->done_reason) && is_array($row->done_reason));

                                                if ($isDone) {
                                                    $statusValue = 'done';
                                                } elseif ($isHalf) {
                                                    $statusValue = 'half';
                                                } elseif (!empty($row->is_time)) {
                                                    $statusValue = 'half';
                                                } else {
                                                    $statusValue = 'open';
                                                }

                                                $doneBy   = $history?->done_by ? \App\Models\Employee::find($history->done_by) : null;
                                                $markedBy = $history?->marked_by ? \App\Models\Employee::find($history->marked_by) : null;
                                            @endphp

                                            <tr class="activities-phase"
                                                data-activity-id="{{ $activity->id }}"
                                                data-phase-id="{{ $row->phase->id }}"
                                                data-customer-id="{{ $customer_id }}"
                                                data-alternative-id="{{ $alternative_id }}"
                                                data-product-id="{{ $productId }}"
                                                data-service-id="{{ $serviceId }}">
                                                {{-- # --}}
                                                <td>{{ $activity->sort_order ?? $loop->iteration }}</td>

                                                {{-- Titel + Beschreibung --}}
                                                <td style="padding-left: 0;">
                                                    <div class="activity-main-title">
                                                        {{ $activity->title }}
                                                    </div>
                                                    @if($activity->description)
                                                        <div class="activity-main-desc">
                                                            {{ $activity->description }}
                                                        </div>
                                                    @endif
                                                </td>

                                                {{-- Status (Offen / Teilweise / Komplett) --}}
                                                <td class="text-center align-middle">
                                                    <div class="status-pill-group">
                                                        {{-- Offen --}}
                                                        <label class="status-pill status-pill-open {{ $statusValue === 'open' ? 'is-active' : '' }}">
                                                            <input class="status-option"
                                                                   type="radio"
                                                                   name="status-{{ $activity->id }}"
                                                                   value="open"
                                                                   data-activity-id="{{ $activity->id }}"
                                                                   data-phase-id="{{ $row->phase->id }}"
                                                                   {{ $statusValue === 'open' ? 'checked' : '' }}>
                                                            <i data-feather="circle"></i>
                                                            <span>Offen</span>
                                                        </label>

                                                        {{-- Teilweise --}}
                                                        <label class="status-pill status-pill-half {{ $statusValue === 'half' ? 'is-active' : '' }}">
                                                            <input class="status-option"
                                                                   type="radio"
                                                                   name="status-{{ $activity->id }}"
                                                                   value="half"
                                                                   data-activity-id="{{ $activity->id }}"
                                                                   data-phase-id="{{ $row->phase->id }}"
                                                                   {{ $statusValue === 'half' ? 'checked' : '' }}>
                                                            <i data-feather="alert-circle"></i>
                                                            <span>Teilweise</span>
                                                        </label>

                                                        {{-- Komplett --}}
                                                        <label class="status-pill status-pill-done {{ $statusValue === 'done' ? 'is-active' : '' }}">
                                                            <input class="status-option"
                                                                   type="radio"
                                                                   name="status-{{ $activity->id }}"
                                                                   value="1"
                                                                   data-activity-id="{{ $activity->id }}"
                                                                   data-phase-id="{{ $row->phase->id }}"
                                                                   {{ $statusValue === 'done' ? 'checked' : '' }}>
                                                            <i data-feather="check-circle"></i>
                                                            <span>Komplett</span>
                                                        </label>
                                                    </div>
                                                </td>

                                                {{-- Plan-Zeit (editable) --}}
                                                <td>
                                                    <div class="duration-wrapper" data-activity-id="{{ $activity->id }}">
                                                        <span class="duration-display">
                                                            {{ $row->plan_time ?? $activity->duration ?? '00:00:00' }}
                                                            <i class="feather icon-edit text-primary ml-1 edit-duration-btn" style="cursor:pointer;"></i>
                                                        </span>
                                                        <span class="duration-edit d-none">
                                                            <input type="time"
                                                                   class="form-control form-control-sm duration-input"
                                                                   data-type="plan_time"
                                                                   value="{{ $row->plan_time ?? $activity->duration ?? '00:00:00' }}"
                                                                   style="width:100px;display:inline-block;">
                                                            <button class="btn btn-sm btn-success save-duration-btn">
                                                                <i class="feather icon-check"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>

                                                {{-- Ist-Zeit --}}
                                                <td>
                                                    <input type="time"
                                                           class="form-control form-control-sm"
                                                           data-type="is_time"
                                                           value="{{ $row->is_time ?? '' }}">
                                                </td>

                                                {{-- Diff / Prozent --}}
                                                <td class="d-time-cell">
                                                    <p class="mb-0">
                                                        <small class="d-percent-cell text-muted">-</small>
                                                    </p>
                                                    <p class="mb-0 mt-0">
                                                        <small class="d-share-cell text-muted">-</small>
                                                    </p>
                                                </td>

                                                {{-- Datum erledigt --}}
                                                <td>
                                                    <input type="date"
                                                           name="history[{{ $activity->id }}][done_date]"
                                                           value="{{ $row->done_date ? \Carbon\Carbon::parse($row->done_date)->format('Y-m-d') : '' }}"
                                                           class="form-control form-control-sm">
                                                </td>

                                                {{-- Markiert von --}}
                                                <td class="mark-by-cell">
                                                    @if($markedBy)
                                                        <span class="badge badge-light-primary"
                                                              data-toggle="tooltip"
                                                              data-html="true"
                                                              title="{{ $markedBy->name }} {{ $markedBy->lastname }}">
                                                            {{ \Illuminate\Support\Str::limit($markedBy->name.' '.$markedBy->lastname, 10) }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-light-secondary"
                                                              data-toggle="tooltip"
                                                              data-html="true"
                                                              title="Unbekannt">
                                                          –
                                                      </span>
                                                    @endif
                                                </td>

                                                {{-- Zuständig --}}
                                                <td>
                                                    <select name="done_by"
                                                            class="form-control employeeDone done-by-select"
                                                            data-activity-id="{{ $activity->id }}"
                                                            data-phase-id="{{ $row->phase->id }}">
                                                        <option value="">-- Bitte wählen --</option>
                                                        @foreach($employees as $emp)
                                                            <option value="{{ $emp->id }}"
                                                                    data-image="{{ asset('images/employee/'.$emp->image) }}"
                                                                    {{ $doneBy && $doneBy->id == $emp->id ? 'selected' : '' }}>
                                                                {{ $emp->name }} {{ $emp->lastname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                {{-- Dokument upload --}}
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <form action="{{ url('/activity-document-upload') }}"
                                                              method="POST"
                                                              enctype="multipart/form-data"
                                                              class="upload-form d-flex align-items-center">
                                                            @csrf
                                                            <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                                                            <input type="hidden" name="alternative_id" value="{{ $alternative_id }}">
                                                            <input type="hidden" name="phase_id" value="{{ $row->phase->id }}">
                                                            <input type="hidden" name="task_id" value="{{ $activity->id }}">
                                                            <input type="hidden" name="stage" value="{{ $stageKey }}">

                                                            <label class="upload-icon m-0" title="Datei hochladen">
                                                                <i class="feather icon-upload-cloud upload-icons"></i>
                                                                <input type="file"
                                                                       name="document"
                                                                       class="d-none"
                                                                       onchange="uploadActivityFile(this)">
                                                            </label>
                                                        </form>
                                                    </div>
                                                </td>

                                                {{-- Notiz --}}
                                                <td>
                                                    <textarea class="form-control form-control-sm note-textarea"
                                                              rows="2"
                                                              data-activity-id="{{ $activity->id }}"
                                                              data-phase-id="{{ $row->phase->id }}"
                                                              placeholder="Notiz eingeben...">{{ is_array($row->notes) ? implode(' ', $row->notes) : ($row->notes ?? '') }}</textarea>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
