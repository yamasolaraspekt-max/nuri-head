@extends('admin.layouts.app')

@section('title') Zeitmanagement – Zeitpläne aller Mitarbeiter @endsection

@section('content')
<div class="app-content"> 

    <div class="content-wrapper">
        {{-- Page header --}}
        <div class="content-header row">
            <div class="content-header-left col-md-8 col-12 mb-2">
                <h2 class="content-header-title mb-0">
                    Zeitpläne – Übersicht aller Mitarbeiter
                </h2>
                <p class="mb-0 text-muted">
                    Prüfe eingereichte Zeitpläne, vergleiche Soll/Ist-Stunden und genehmige oder lehne ab.
                </p>
            </div>
        </div>

        <div class="content-body">
            {{-- Filters --}}
            <section class="tm-slot-filters mb-1">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-3 col-sm-6 mb-1">
                                <label for="tmSlotsMonth">Monat</label>
                                <input type="month"
                                       id="tmSlotsMonth"
                                       class="form-control"
                                       value="{{ request('month', now()->format('Y-m')) }}">
                          
                            </div>
                            <div class="col-md-3 col-sm-6 mb-1">
                                <label for="tmFilterStatus">Status</label>
                                <select id="tmFilterStatus" class="form-control">
                                    <option value="">Alle</option>
                                    <option value="draft">Entwurf</option>
                                    <option value="pending">Zur Prüfung</option>
                                    <option value="approved">Genehmigt</option>
                                    <option value="rejected">Abgelehnt</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-1">
                                <label for="tmFilterSearch">Mitarbeiter</label>
                                <div class="input-group">
                                    <input type="text" id="tmFilterSearch"
                                           class="form-control"
                                           placeholder="Name, Abteilung, Stichwort …">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="feather icon-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6 mb-1 text-right">
                                <button type="button" id="tmClearFilters" class="btn btn-outline-secondary btn-block">
                                    Filter zurücksetzen
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Cards --}}
            <section class="tm-slot-cards">
                <div class="row" id="tmSlotsContainer">
                    @forelse($employees as $employee)
                        @php
    /** @var \App\Models\Employee $employee */

    // Controller should ideally pass $plans keyed by employee_id
    // e.g. $plans[employee_id] = TimeManagementPlan for selected month
    $plan = isset($plans) ? ($plans[$employee->id] ?? null) : ($employee->currentTimePlan ?? null);

    $status = $plan->status ?? 'draft';

    switch ($status) {
        case 'pending':
            $statusLabel = 'Zur Prüfung';
            $statusClass = 'badge-light-warning';
            break;
        case 'approved':
            $statusLabel = 'Genehmigt';
            $statusClass = 'badge-light-success';
            break;
        case 'rejected':
            $statusLabel = 'Abgelehnt';
            $statusClass = 'badge-light-danger';
            break;
        default:
            $statusLabel = 'Entwurf';
            $statusClass = 'badge-light-secondary';
    }

    $workingType = $plan->working_type ?? $employee->working_type;
    $hourlyRate = (float) ($plan->hourly_rate ?? $employee->salary_per_hour ?? 0);
    $targetHours = (float) ($plan->target_hours ?? $employee->working_hour ?? 0);
    $scheduledHours = (float) ($plan->scheduled_hours ?? 0);
    $diffHours = $scheduledHours - $targetHours;
    $estimatedPay = $scheduledHours * $hourlyRate;
    $planMonth = $plan->month ?? null;
    $planYear = $plan->year ?? null;
                        @endphp

                        <div class="col-xl-4 col-lg-6 col-md-6 col-12 tm-slot-card-wrapper p-1">
                            <div class="tm-slot-card card h-100"
                                 data-plan-id="{{ $plan->id ?? '' }}"
                                 data-status="{{ $status }}"
                                 data-employee-name="{{ Str::lower($employee->name . ' ' . $employee->lastname) }}">
                                <div class="card-body">
                                    {{-- Header: avatar + name + status --}}
                                    <div class="d-flex justify-content-between align-items-start mb-75">
                                        <div class="d-flex align-items-center">
                                            <div class="tm-avatar mr-75"
                                                 style="background-color: {{ $employee->color ?? '#e5e7eb' }};">
                                                <span>
                                                    {{ strtoupper(mb_substr($employee->name, 0, 1) . mb_substr($employee->lastname, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="mb-25">
                                                    {{ $employee->name }} {{ $employee->lastname }}
                                                </h5>
                                                <div class="text-muted tm-subline">
                                                    {{ $workingType ?: 'Arbeitsmodell unbekannt' }}
                                                    @if(!empty($employee->branch))
                                                        · {{ $employee->branch }}
                                                    @endif
                                                </div>
                                                @if($planMonth && $planYear)
                                                    <div class="text-muted tm-subline">
                                                        Plan: {{ str_pad($planMonth, 2, '0', STR_PAD_LEFT) }} / {{ $planYear }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="badge {{ $statusClass }} tm-status-badge">
                                            <span class="tm-status-text">{{ $statusLabel }}</span>
                                        </span>
                                    </div>

                                    {{-- KPI grid --}}
                                    <div class="tm-kpi-grid mb-1">
                                        <div class="tm-kpi-item">
                                            <div class="tm-kpi-label">Vertragliche Std./Monat</div>
                                            <div class="tm-kpi-value">
                                                {{ number_format((float) ($employee->working_hour ?? 0), 2, ',', '.') }} h
                                            </div>
                                        </div>
                                        <div class="tm-kpi-item">
                                            <div class="tm-kpi-label">Zielstunden (Plan)</div>
                                            <div class="tm-kpi-value">
                                                {{ number_format($targetHours, 2, ',', '.') }} h
                                            </div>
                                        </div>
                                        <div class="tm-kpi-item">
                                            <div class="tm-kpi-label">Geplante Stunden</div>
                                            <div class="tm-kpi-value">
                                                {{ number_format($scheduledHours, 2, ',', '.') }} h
                                            </div>
                                        </div>
                                        <div class="tm-kpi-item">
                                            <div class="tm-kpi-label">Differenz</div>
                                            <div class="tm-kpi-value {{ $diffHours >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $diffHours >= 0 ? '+' : '' }}{{ number_format($diffHours, 2, ',', '.') }} h
                                            </div>
                                        </div>
                                        <div class="tm-kpi-item">
                                            <div class="tm-kpi-label">Stundenlohn</div>
                                            <div class="tm-kpi-value">
                                                {{ number_format($hourlyRate, 2, ',', '.') }} €
                                            </div>
                                        </div>
                                        <div class="tm-kpi-item">
                                            <div class="tm-kpi-label">Vorauss. Monatslohn</div>
                                            <div class="tm-kpi-value font-weight-bold">
                                                {{ number_format($estimatedPay, 2, ',', '.') }} €
                                            </div>
                                        </div>
                                    </div>

                                                                       {{-- Comment + actions --}}
                                    <div class="tm-comment-block mb-1">
                                        <label class="tm-comment-label">
                                            Kommentar (für Genehmigung / Ablehnung)
                                        </label>
                                        <textarea
                                            class="form-control form-control-sm tm-comment-input"
                                            rows="2"
                                            placeholder="Optionaler Kommentar für den Mitarbeiter …"
                                            @if(!$plan) disabled @endif
                                        >{{ $plan->comment ?? '' }}</textarea>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="tm-meta">
                                            <small class="text-muted">
                                                @if($plan && $plan->updated_at)
                                                    Aktualisiert: {{ $plan->updated_at->format('d.m.Y H:i') }}
                                                @else
                                                    Noch kein Plan gespeichert.
                                                @endif
                                            </small>
                                        </div>
                                        <div class="tm-actions d-flex align-items-center">
                                            {{-- NEW: button to open individual time management page --}}
                                            <a href="{{ route('time_management.index', $employee) }}"
                                               class="btn btn-sm btn-outline-primary mr-25">
                                                <i class="feather icon-calendar mr-25"></i>
                                                Plan ansehen
                                            </a>

                                            @if($plan)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger tm-action-btn"
                                                        data-action="reject">
                                                    <i class="feather icon-x mr-25"></i> Ablehnen
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-success tm-action-btn ml-25"
                                                        data-action="approve">
                                                    <i class="feather icon-check mr-25"></i> Genehmigen
                                                </button>
                                            @else
                                                <span class="badge badge-light-secondary">
                                                    Kein Plan für diesen Monat
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                Es wurden keine Mitarbeiter gefunden.
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('style')
<style>
    .tm-slot-filters .card {
        border-radius: 0.75rem;
        border-color: #e5e7eb;
    }

    .tm-slot-card {
        border-radius: 0.9rem;
        border: 1px solid #e5e7eb;
        transition: box-shadow 0.15s ease, transform 0.1s ease;
    }
    .tm-slot-card:hover {
        box-shadow: 0 10px 25px rgba(15,23,42,0.12);
        transform: translateY(-2px);
    }

    .tm-avatar {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e5e7eb;
        color: #111827;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .tm-subline {
        font-size: 0.75rem;
    }

    .tm-status-badge {
        font-size: 0.7rem;
        padding: 0.35rem 0.6rem;
    }

    .tm-kpi-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        grid-column-gap: 0.75rem;
        grid-row-gap: 0.5rem;
        margin-top: 0.5rem;
    }
    .tm-kpi-item {
        padding: 0.45rem 0.55rem;
        border-radius: 0.5rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
    }
    .tm-kpi-label {
        font-size: 0.7rem;
        color: #6b7280;
        margin-bottom: 0.1rem;
    }
    .tm-kpi-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: #111827;
    }

    .tm-comment-label {
        font-size: 0.75rem;
        color: #4b5563;
        margin-bottom: 0.2rem;
    }
    .tm-comment-input {
        resize: vertical;
    }

    .tm-action-btn {
        font-size: 0.75rem;
    }

    @media (max-width: 767.98px) {
        .tm-kpi-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container       = document.getElementById('tmSlotsContainer');
    const filterSearch    = document.getElementById('tmFilterSearch');
    const filterStatus    = document.getElementById('tmFilterStatus');
    const clearFiltersBtn = document.getElementById('tmClearFilters');
    const csrfMeta        = document.querySelector('meta[name="csrf-token"]');
    const csrfToken       = csrfMeta ? csrfMeta.getAttribute('content') : '';

    const statusUrlTemplate = "{{ route('time_management.status', ['plan' => 'PLAN_ID']) }}";

    function logError(tag, err) {
        console.error('[TimeSlots][' + tag + ']', err);
    }

    // --- Filtering -------------------------------------------------------------
    function applyFilters() {
        const q = (filterSearch?.value || '').trim().toLowerCase();
        const st = (filterStatus?.value || '').trim();

        const cards = container.querySelectorAll('.tm-slot-card');

        cards.forEach(card => {
            const employeeName = (card.dataset.employeeName || '').toLowerCase();
            const cardStatus   = card.dataset.status || '';

            let visible = true;

            if (q && !employeeName.includes(q)) {
                visible = false;
            }
            if (st && cardStatus !== st) {
                visible = false;
            }

            card.closest('.tm-slot-card-wrapper').style.display = visible ? '' : 'none';
        });
    }

    if (filterSearch) {
        filterSearch.addEventListener('input', function () {
            applyFilters();
        });
    }
    if (filterStatus) {
        filterStatus.addEventListener('change', function () {
            applyFilters();
        });
    }
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function () {
            if (filterSearch) filterSearch.value = '';
            if (filterStatus) filterStatus.value = '';
            applyFilters();
        });
    }

    // --- Status update (approve / reject) -------------------------------------
    async function postJson(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });
        if (!res.ok) {
            const text = await res.text();
            throw new Error('HTTP ' + res.status + ': ' + text);
        }
        return await res.json();
    }

    function updateCardStatusUI(card, newStatus) {
        const badge = card.querySelector('.tm-status-badge');
        const text  = card.querySelector('.tm-status-text');

        let label = 'Entwurf';
        let cls   = 'badge-light-secondary';

        if (newStatus === 'pending') {
            label = 'Zur Prüfung';
            cls   = 'badge-light-warning';
        } else if (newStatus === 'approved') {
            label = 'Genehmigt';
            cls   = 'badge-light-success';
        } else if (newStatus === 'rejected') {
            label = 'Abgelehnt';
            cls   = 'badge-light-danger';
        }

        card.dataset.status = newStatus;

        if (badge && text) {
            badge.classList.remove(
                'badge-light-secondary',
                'badge-light-warning',
                'badge-light-success',
                'badge-light-danger'
            );
            badge.classList.add(cls);
            text.textContent = label;
        }
    }

    async function handleStatusChange(card, action) {
        const planId = card.dataset.planId;
        if (!planId) {
            alert('Kein Plan für diesen Mitarbeiter vorhanden.');
            return;
        }

        const newStatus = action === 'approve' ? 'approved' : 'rejected';
        const commentEl = card.querySelector('.tm-comment-input');
        const comment   = commentEl ? commentEl.value : '';

        const url = statusUrlTemplate.replace('PLAN_ID', planId);

        try {
            const data = await postJson(url, {
                status: newStatus,
                comment: comment
            });
            updateCardStatusUI(card, data.status || newStatus);
            applyFilters(); // re-evaluate filters based on new status
        } catch (err) {
            logError('updateStatus', err);
            alert('Fehler beim Aktualisieren des Status.');
        }
    }

    if (container) {
        container.addEventListener('click', function (e) {
            const btn = e.target.closest('.tm-action-btn');
            if (!btn) return;

            const action = btn.dataset.action;
            if (!action) return;

            const card = btn.closest('.tm-slot-card');
            if (!card) return;

            handleStatusChange(card, action);
        });
    }

    // Initial filter (just to normalize)
    applyFilters();
});
</script>
@endsection

@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Mitarbeiterliste',
                url: "{{ url('emp?status_tab=active')}}",
            },
            {
                label: 'Zeitpläne',
                url: "{{url()->current()}}",
                clickable: false
            }

        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush
