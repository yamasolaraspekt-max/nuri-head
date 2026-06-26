{{-- resources/views/admin/todo/personal/profile.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Aufgabe – ' . ($task->task_title ?? 'Ohne Titel'))

@php
    $taskLeadStages = collect($leadStages ?? []);
    $taskLeadStagePayload = $taskLeadStages->map(function ($stage) {
        $subStages = collect(data_get($stage, 'activeSubStages') ?? data_get($stage, 'active_sub_stages') ?? data_get($stage, 'subStages') ?? data_get($stage, 'sub_stages') ?? []);

        return [
            'id' => data_get($stage, 'id'),
            'key' => data_get($stage, 'key'),
            'name' => data_get($stage, 'name'),
            'color' => data_get($stage, 'color') ?: '#74b2d4',
            'icon' => data_get($stage, 'icon'),
            'sub_stages' => $subStages->map(function ($subStage) {
                return [
                    'id' => data_get($subStage, 'id'),
                    'lead_stage_id' => data_get($subStage, 'lead_stage_id'),
                    'key' => data_get($subStage, 'key'),
                    'name' => data_get($subStage, 'name'),
                    'color' => data_get($subStage, 'color') ?: '#93c21c',
                    'icon' => data_get($subStage, 'icon'),
                ];
            })->values(),
        ];
    })->values();

    $profileStageContext = $leadStageContext ?? [
        'lead_stage_id' => $task->lead_stage_id,
        'lead_stage_name' => optional($task->leadStage)->name ?? optional(optional($task->leadProductList)->companyStage)->name,
        'lead_stage_color' => optional($task->leadStage)->color ?? optional(optional($task->leadProductList)->companyStage)->color ?? '#74b2d4',
        'lead_stage_sub_stage_id' => $task->lead_stage_sub_stage_id,
        'lead_stage_sub_stage_name' => optional($task->leadStageSubStage)->name ?? optional(optional($task->leadProductList)->leadStageSubStage)->name,
        'lead_stage_sub_stage_color' => optional($task->leadStageSubStage)->color ?? optional(optional($task->leadProductList)->leadStageSubStage)->color ?? '#93c21c',
    ];

    $profileStageUpdateRoute = \Illuminate\Support\Facades\Route::has('personal-tasks.lead-stage.update')
        ? route('personal-tasks.lead-stage.update', $task->id)
        : '#';

    $profileCurrentEmployeeId = $employeeId ?? (int) (auth()->user()->name ?? 0);
    $profileCreatorId = (int) ($task->assigned_by ?? 0);
    $profileControllerIds = ($controllerEmployees ?? collect())->pluck('id')->map(fn($id) => (int) $id)->all();
    $canManageStage = $profileCurrentEmployeeId > 0 && (
        $profileCurrentEmployeeId === $profileCreatorId ||
        in_array($profileCurrentEmployeeId, $profileControllerIds, true)
    );
@endphp


@section('style')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
 <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<style>
    .tp-shell {
        display: grid;
        grid-template-columns: minmax(280px, 340px) 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 1024px) {
        .tp-shell {
            grid-template-columns: 1fr;
        }
    }

    .tp-card {
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 16px 40px rgba(15,23,42,.08);
        border: 1px solid rgba(15,23,42,.06);
        padding: 1.1rem 1.25rem;
    }

    .tp-card h3 {
        margin: 0 0 .5rem;
        font-size: 1rem;
    }

    .tp-kv-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .85rem;
    }
    .tp-kv-table tr td:first-child {
        width: 40%;
        font-weight: 600;
        color: #4b5563;
        padding: .25rem .25rem .25rem 0;
        vertical-align: top;
    }
    .tp-kv-table tr td:last-child {
        padding: .25rem 0 .25rem .25rem;
        color: #111827;
    }

    .tp-pill {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        padding: .15rem .5rem;
        border-radius: 999px;
        background: #f3f4f6;
        font-size: .75rem;
        color: #374151;
        white-space: nowrap;
    }
    .tp-pill-strong {
        background: #d1fae5;
        color: #065f46;
        font-weight: 600;
    }
    .tp-avatar-ring {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 999px;
        overflow: hidden;
        background: #e5e7eb;
        font-size: .75rem;
        color: #111827;
    }
    .tp-avatar-ring + .tp-avatar-ring {
        margin-left: -10px;
        border: 2px solid #fff;
    }

    .tp-tabs {
        display: inline-flex;
        padding: .2rem;
        border-radius: 999px;
        background: #f3f4f6;
        margin-bottom: 1rem;
    }
    .tp-tabs button {
        border: none;
        background: transparent;
        border-radius: 999px;
        padding: .35rem .9rem;
        font-size: .8rem;
        cursor: pointer;
        color: #4b5563;
    }
    .tp-tabs button.is-active {
        background: #111827;
        color: #f9fafb;
    }

    .tp-keys-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .82rem;
    }
    .tp-keys-table th,
    .tp-keys-table td {
        padding: .35rem .4rem;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
    }
    .tp-keys-table th {
        font-weight: 600;
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #6b7280;
    }
    .tp-key-done {
        text-decoration: line-through;
        color: #6b7280;
    }
    .tp-badge-status {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        padding: .1rem .45rem;
        border-radius: 999px;
        font-size: .7rem;
        background: #eef2ff;
        color: #3730a3;
    }

    .tp-history-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .tp-history-item {
        display: grid;
        grid-template-columns: 90px 1fr;
        gap: .6rem;
        font-size: .8rem;
        padding: .4rem .2rem;
        border-bottom: 1px dashed #e5e7eb;
    }
    .tp-history-item time {
        color: #6b7280;
        font-size: .75rem;
    }
    .tp-history-title {
        font-weight: 600;
        margin-bottom: .1rem;
    }
    .tp-history-meta {
        font-size: .75rem;
        color: #4b5563;
    }

    .tp-comment {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: .6rem .7rem;
        margin-bottom: .5rem;
        background: #f9fafb;
    }
    .tp-comment-header {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: .25rem;
    }
    .tp-comment-name {
        font-size: .8rem;
        font-weight: 600;
        color: #111827;
    }
    .tp-comment-time {
        font-size: .7rem;
        color: #6b7280;
    }
    .tp-comment-body {
        font-size: .8rem;
        color: #111827;
        margin-top: .2rem;
    }
    .tp-comment-actions {
        margin-top: .25rem;
        font-size: .75rem;
        color: #4b5563;
        cursor: pointer;
    }
    .tp-comment-replies {
        margin-left: 1.5rem;
        margin-top: .3rem;
    }


/* =========================================================
   Task Profile Redesign v2 - Modern UI + Toast System
   Added inline in this Blade. Keeps existing IDs/routes/JS.
   ========================================================= */
:root {
    --tp-green:#93c21c;
    --tp-green-dark:#7daa17;
    --tp-blue:#74b2d4;
    --tp-ink:#0f172a;
    --tp-muted:#64748b;
    --tp-line:#e2e8f0;
    --tp-soft:#f8fafc;
    --tp-card:#ffffff;
    --tp-danger:#ef4444;
    --tp-warning:#f59e0b;
    --tp-success:#10b981;
    --tp-shadow:0 20px 60px rgba(15,23,42,.10);
    --tp-shadow-soft:0 10px 30px rgba(15,23,42,.07);
    --tp-radius:22px;
}

 
.tp-shell { 
    margin: 0 auto; 
    display: grid !important;
    grid-template-columns: minmax(320px, 390px) minmax(0, 1fr) !important;
    gap: 18px !important;
    align-items: start;
}

.tp-left-column {
    position: sticky;
    top: 82px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    max-height: calc(100vh - 100px);
    overflow: auto;
    padding-right: 4px;
}

.tp-right-column {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.tp-card {
    border: 1px solid rgba(226,232,240,.95) !important;
    border-radius: var(--tp-radius) !important;
    background: rgba(255,255,255,.92) !important;
    box-shadow: var(--tp-shadow-soft) !important;
    padding: 16px !important;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.tp-card::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--tp-green), var(--tp-blue));
    opacity: .95;
}

.tp-card h3,
.tp-card .tp-section-heading {
    color: var(--tp-ink) !important;
    font-size: 15px !important;
    font-weight: 950 !important;
    letter-spacing: -.01em;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px !important;
}

.tp-card h3::before {
    content: "";
    width: 9px;
    height: 9px;
    border-radius: 999px;
    background: var(--tp-green);
    box-shadow: 0 0 0 5px rgba(147,194,28,.13);
}

.tp-kv-table {
    border-collapse: separate !important;
    border-spacing: 0 8px !important;
}

.tp-kv-table tr {
    background: #f8fafc;
}

.tp-kv-table tr td:first-child {
    width: 42% !important;
    color: #64748b !important;
    font-size: 11px !important;
    text-transform: uppercase;
    letter-spacing: .05em;
    font-weight: 900 !important;
    border-radius: 13px 0 0 13px;
    padding: 9px 10px !important;
}

.tp-kv-table tr td:last-child {
    color: #0f172a !important;
    font-size: 13px !important;
    font-weight: 800;
    border-radius: 0 13px 13px 0;
    padding: 9px 10px !important;
}

.tp-pill,
.tp-badge-status {
    border: 1px solid #e2e8f0 !important;
    background: #fff !important;
    color: #334155 !important;
    min-height: 26px;
    padding: 0 10px !important;
    border-radius: 999px !important;
    font-size: 11px !important;
    font-weight: 900 !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.tp-pill-strong {
    background: #ecfdf5 !important;
    border-color: #bbf7d0 !important;
    color: #047857 !important;
}

.tp-avatar-ring {
    width: 34px !important;
    height: 34px !important;
    border: 2px solid #fff;
    background: linear-gradient(135deg,#e2e8f0,#f8fafc) !important;
    box-shadow: 0 8px 18px rgba(15,23,42,.12);
    font-size: 11px !important;
    font-weight: 950 !important;
    color: #334155 !important;
    transition: transform .15s ease;
}

.tp-avatar-ring:hover {
    transform: translateY(-2px) scale(1.03);
    z-index: 3;
}

.tp-avatar-ring + .tp-avatar-ring {
    margin-left: -12px !important;
}

/* Description card */
.tp-right-column > .tp-card:first-child {
    min-height: 136px;
    background:
        linear-gradient(135deg, rgba(255,255,255,.96), rgba(238,247,251,.96)) !important;
}

.tp-right-column > .tp-card:first-child div[style*="font-size"] {
    color: #334155 !important;
    font-size: 14px !important;
    line-height: 1.65 !important;
}

/* Tabs */
.tp-tabs {
    display: flex !important;
    flex-wrap: wrap;
    width: 100%;
    gap: 8px;
    background: #f1f5f9 !important;
    border: 1px solid #e2e8f0;
    border-radius: 18px !important;
    padding: 7px !important;
    margin-bottom: 14px !important;
}

.tp-tabs button {
    min-height: 40px;
    padding: 0 14px !important;
    border-radius: 13px !important;
    color: #475569 !important;
    font-size: 12px !important;
    font-weight: 950 !important;
    transition: .16s ease;
}

.tp-tabs button:hover {
    background: #fff !important;
    color: #0f172a !important;
}

.tp-tabs button.is-active {
    background: linear-gradient(135deg, #0f172a, #1e293b) !important;
    color: #fff !important;
    box-shadow: 0 12px 24px rgba(15,23,42,.18);
}

.tp-tab-panel {
    animation: tpPanelIn .18s ease both;
}

@keyframes tpPanelIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Steps */
.tp-keys-table {
    border-collapse: separate !important;
    border-spacing: 0 10px !important;
    min-width: 760px;
}

.tp-keys-table thead th {
    background: transparent !important;
    border: 0 !important;
    color: #64748b !important;
    font-size: 10px !important;
    font-weight: 950 !important;
    padding: 0 10px 0 !important;
}

.tp-keys-table tbody tr {
    box-shadow: 0 8px 22px rgba(15,23,42,.05);
}

.tp-keys-table tbody td {
    background: #fff !important;
    border-top: 1px solid #e2e8f0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 12px 10px !important;
    color: #334155;
}

.tp-keys-table tbody td:first-child {
    border-left: 1px solid #e2e8f0 !important;
    border-radius: 16px 0 0 16px;
}

.tp-keys-table tbody td:last-child {
    border-right: 1px solid #e2e8f0 !important;
    border-radius: 0 16px 16px 0;
}

.tp-key-done {
    color: #64748b !important;
    opacity: .8;
}

.js-key-toggle,
.btn,
.tp-card button,
#tp-attach-upload-btn {
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
}

.js-key-toggle:hover,
.btn:hover,
.tp-card button:hover,
#tp-attach-upload-btn:hover {
    transform: translateY(-1px);
}

/* Comments / Reports */
.tp-comment {
    border: 1px solid #e2e8f0 !important;
    border-radius: 18px !important;
    background: linear-gradient(135deg, #fff 0%, #f8fafc 100%) !important;
    padding: 12px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 8px 20px rgba(15,23,42,.05);
}

.tp-comment-header {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    border-bottom: 1px solid #edf2f7;
    padding-bottom: 8px;
    margin-bottom: 8px !important;
}

.tp-comment-name {
    color: #0f172a !important;
    font-size: 13px !important;
    font-weight: 950 !important;
}

.tp-comment-time {
    color: #94a3b8 !important;
    font-size: 11px !important;
    margin-left: auto;
}

.tp-comment-body {
    color: #334155 !important;
    font-size: 13px !important;
    line-height: 1.6 !important;
}

.tp-comment-actions {
    color: var(--tp-blue) !important;
    font-size: 12px !important;
    font-weight: 900 !important;
    margin-top: 8px !important;
}

.tp-comment-actions:hover {
    color: #0369a1 !important;
}

.tp-comment-replies {
    margin-left: 22px !important;
    margin-top: 10px !important;
    padding-left: 12px;
    border-left: 3px solid #e2e8f0;
}

.tp-reply-form textarea,
#tp-report-form textarea,
.tp-card textarea,
.tp-card input[type="text"],
.tp-card select {
    border: 1px solid #dbeafe !important;
    border-radius: 14px !important;
    background: #f8fafc !important;
    color: #0f172a !important;
    outline: 0 !important;
    transition: .16s ease;
}

.tp-reply-form textarea:focus,
#tp-report-form textarea:focus,
.tp-card textarea:focus,
.tp-card input[type="text"]:focus,
.tp-card select:focus {
    background: #fff !important;
    border-color: var(--tp-blue) !important;
    box-shadow: 0 0 0 4px rgba(116,178,212,.16) !important;
}

/* Attachments */
#tp-attach-dropzone {
    border: 1.5px dashed #cbd5e1 !important;
    border-radius: 18px !important;
    padding: 16px !important;
    background: linear-gradient(135deg,#f8fafc,#eef7fb) !important;
    color: #475569 !important;
    font-weight: 850;
    cursor: pointer;
}

#tp-attach-dropzone:hover {
    background: #eef7fb !important;
    border-color: var(--tp-blue) !important;
}

#tp-attach-list .tp-attach-item {
    padding: 9px 8px !important;
    border-radius: 12px;
    border: 1px solid transparent !important;
}

#tp-attach-list .tp-attach-item:hover {
    background: #f8fafc;
    border-color: #e2e8f0 !important;
}

.tp-attach-open,
.tp-attach-delete {
    border-radius: 999px !important;
    padding: 4px 8px !important;
    font-weight: 900;
}

/* Modals */
.modal-content {
    border: 0 !important;
    border-radius: 22px !important;
    overflow: hidden;
    box-shadow: 0 30px 90px rgba(15,23,42,.28) !important;
}

.modal-header {
    background: linear-gradient(135deg,#f8fafc,#eef7fb) !important;
    border-bottom: 1px solid #e2e8f0 !important;
}

.modal-title {
    color: #0f172a !important;
    font-weight: 950 !important;
}

.modal-footer {
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
}

/* Toaster */
.tp-toast-wrap {
    position: fixed;
    right: 18px;
    bottom: 18px;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
}

.tp-toast-item {
    width: min(380px, calc(100vw - 32px));
    border-radius: 18px;
    background: rgba(15,23,42,.96);
    color: #fff;
    box-shadow: 0 22px 60px rgba(15,23,42,.28);
    border: 1px solid rgba(255,255,255,.10);
    display: grid;
    grid-template-columns: 38px 1fr 28px;
    gap: 10px;
    align-items: start;
    padding: 12px;
    pointer-events: auto;
    transform: translateY(12px) scale(.98);
    opacity: 0;
    animation: tpToastIn .18s ease forwards;
    overflow: hidden;
}

.tp-toast-item::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    height: 3px;
    width: 100%;
    background: currentColor;
    opacity: .85;
    animation: tpToastLife 4.6s linear forwards;
}

.tp-toast-icon {
    width: 38px;
    height: 38px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,.10);
    color: #fff;
    font-size: 18px;
    font-weight: 950;
}

.tp-toast-title {
    font-size: 13px;
    font-weight: 950;
    margin-bottom: 2px;
}

.tp-toast-text {
    font-size: 12px;
    line-height: 1.45;
    color: rgba(255,255,255,.82);
}

.tp-toast-close {
    width: 28px;
    height: 28px;
    border: 0;
    background: rgba(255,255,255,.08);
    color: #fff;
    border-radius: 10px;
    cursor: pointer;
    line-height: 1;
}

.tp-toast-item.is-success { color: #22c55e; }
.tp-toast-item.is-error { color: #ef4444; }
.tp-toast-item.is-warning { color: #f59e0b; }
.tp-toast-item.is-info { color: #38bdf8; }

@keyframes tpToastIn {
    to { transform: translateY(0) scale(1); opacity: 1; }
}

@keyframes tpToastOut {
    to { transform: translateY(10px) scale(.98); opacity: 0; }
}

@keyframes tpToastLife {
    from { transform: scaleX(1); transform-origin: left center; }
    to { transform: scaleX(0); transform-origin: left center; }
}

.tp-scroll-thin::-webkit-scrollbar,
.tp-left-column::-webkit-scrollbar,
.tp-tab-panel::-webkit-scrollbar,
.tp-card::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.tp-scroll-thin::-webkit-scrollbar-thumb,
.tp-left-column::-webkit-scrollbar-thumb,
.tp-tab-panel::-webkit-scrollbar-thumb,
.tp-card::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 999px;
}

@media (max-width: 1024px) {
    .tp-shell {
        grid-template-columns: 1fr !important;
        padding: 12px;
    }

    .tp-left-column {
        position: static;
        max-height: none;
        overflow: visible;
    }
}

@media (max-width: 640px) {
    .tp-card { padding: 13px !important; border-radius: 18px !important; }
    .tp-tabs { display: grid !important; grid-template-columns: 1fr 1fr; }
    .tp-tabs button { width: 100%; justify-content: center; }
    .tp-kv-table tr td:first-child,
    .tp-kv-table tr td:last-child { display: block; width: 100% !important; border-radius: 12px !important; }
}



/* =========================================================
   Brand-like Team Modal Fix (oc modal system)
   - independent from Bootstrap modal JS
   - same visual language as brand blade
   ========================================================= */
.oc-modal-backdrop,
#tp-modal-team,
#tp-modal-controllers {
    position: fixed !important;
    inset: 0 !important;
    z-index: 12000 !important;
    background: rgba(17,24,39,.55) !important;
    backdrop-filter: blur(3px);
    opacity: 0;
    pointer-events: none;
    transition: opacity .22s ease;
    display: none !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 18px !important;
    overflow: auto !important;
}

#tp-modal-team.is-open,
#tp-modal-controllers.is-open {
    opacity: 1 !important;
    pointer-events: auto !important;
    display: flex !important;
}

#tp-modal-team .modal-dialog,
#tp-modal-controllers .modal-dialog {
    margin: 0 !important;
    width: 100% !important;
    max-width: 760px !important;
    transform: translateY(12px) scale(.985);
    transition: transform .22s ease;
}

#tp-modal-controllers .modal-dialog {
    max-width: 620px !important;
}

#tp-modal-team.is-open .modal-dialog,
#tp-modal-controllers.is-open .modal-dialog {
    transform: translateY(0) scale(1);
}

#tp-modal-team .modal-content,
#tp-modal-controllers .modal-content {
    border: 1px solid rgba(229,231,235,.9) !important;
    border-radius: 16px !important;
    box-shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12) !important;
    overflow: hidden !important;
    background: #fff !important;
}

#tp-modal-team .modal-header,
#tp-modal-controllers .modal-header {
    display: flex;
    gap: 12px;
    align-items: center;
    justify-content: space-between;
    padding: 16px 18px !important;
    border-bottom: 1px solid #e5e7eb !important;
    background: #fafafa !important;
}

#tp-modal-team .modal-title,
#tp-modal-controllers .modal-title {
    font-weight: 900 !important;
    font-size: 16px !important;
    line-height: 1.2;
    margin: 0;
    color: #111827 !important;
}

#tp-modal-team .modal-body,
#tp-modal-controllers .modal-body {
    padding: 20px 18px !important;
    max-height: 72vh;
    overflow-y: auto;
}

#tp-modal-team .modal-footer,
#tp-modal-controllers .modal-footer {
    padding: 14px 18px !important;
    border-top: 1px solid #e5e7eb !important;
    background: #fafafa !important;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.tp-team-manage-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    cursor: pointer;
    transition: all .2s ease-in-out;
}
.tp-team-manage-btn:hover {
    background: #f9fafb;
    color: #1f2937;
    border-color: #d1d5db;
}
.tp-team-manage-btn.primary {
    color: #93c21c;
    border-color: #f4fae7;
    background: #f4fae7;
}
.tp-team-manage-btn.primary:hover { border-color:#93c21c; }

.tp-team-section {
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    background: #fff;
    padding: 12px;
    margin-top: 14px;
}
.tp-team-section-title {
    font-size: 11px;
    font-weight: 900;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.tp-team-chip-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.tp-team-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 38px;
    border: 1px solid #e5e7eb;
    border-radius: 999px;
    background: #f9fafb;
    padding: 4px 6px 4px 4px;
    color: #1f2937;
    font-size: 12px;
    font-weight: 800;
}
.tp-team-chip-avatar {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #e5e7eb;
    font-size: 10px;
    font-weight: 900;
    color: #374151;
}
.tp-team-chip-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.tp-team-chip-remove {
    width: 24px;
    height: 24px;
    border: 0;
    border-radius: 999px;
    background: #fef2f2;
    color: #ef4444;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-weight: 900;
    line-height: 1;
}
.tp-team-chip-remove:hover {
    background: #ef4444;
    color: #fff;
}
.tp-team-key-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 10px;
    margin-top: 8px;
    background: #f9fafb;
}
.tp-team-key-title {
    font-size: 12px;
    font-weight: 900;
    color: #111827;
    margin-bottom: 8px;
}
.tp-empty-soft {
    font-size: 12px;
    color: #9ca3af;
    background: #f9fafb;
    border: 1px dashed #d1d5db;
    border-radius: 12px;
    padding: 10px;
}
#tp-modal-team .select2-container,
#tp-modal-controllers .select2-container {
    width: 100% !important;
}



/* =========================================================
   FIX: Profile custom modals above all content areas
   ========================================================= */
#tp-modal-team,
#tp-modal-controllers {
    z-index: 2147483000 !important;
    isolation: isolate !important;
}

#tp-modal-team.is-open,
#tp-modal-controllers.is-open {
    display: flex !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
}

#tp-modal-team .modal-dialog,
#tp-modal-controllers .modal-dialog {
    position: relative !important;
    z-index: 2147483001 !important;
}

body.tp-modal-open {
    overflow: hidden !important;
}

body.tp-modal-open .main-menu,
body.tp-modal-open .header-navbar,
body.tp-modal-open .app-content,
body.tp-modal-open .content-wrapper,
body.tp-modal-open .content-body {
    transform: none !important;
}

/* Remove accidental Bootstrap backdrop conflicts for task profile modals */
body.tp-modal-open .modal-backdrop {
    display: none !important;
}

/* =========================================================
   FIX: realtime comments / replies styling
   ========================================================= */
.tp-live-report-shell {
    display: grid;
    gap: 14px;
}

.tp-live-comment-form,
.tp-live-reply-form {
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    background: #f8fafc;
    padding: 12px;
}

.tp-live-comment-form textarea,
.tp-live-reply-form textarea {
    width: 100%;
    min-height: 82px;
    resize: vertical;
    border: 1px solid #dbeafe !important;
    border-radius: 14px !important;
    background: #fff !important;
    padding: 10px 12px;
    outline: none;
}

.tp-live-comment-form textarea:focus,
.tp-live-reply-form textarea:focus {
    border-color: #74b2d4 !important;
    box-shadow: 0 0 0 4px rgba(116,178,212,.16) !important;
}

.tp-live-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 8px;
}

.tp-live-btn {
    border: 1px solid #e5e7eb;
    border-radius: 999px;
    min-height: 34px;
    padding: 0 13px;
    background: #fff;
    color: #1f2937;
    font-size: 12px;
    font-weight: 900;
    cursor: pointer;
}

.tp-live-btn.primary {
    background: #93c21c;
    border-color: #93c21c;
    color: #fff;
}

.tp-live-btn:hover {
    transform: translateY(-1px);
}

.tp-comments-list-live {
    display: grid;
    gap: 10px;
}

.tp-comment.is-new {
    animation: tpCommentNew .9s ease both;
}

@keyframes tpCommentNew {
    from { box-shadow: 0 0 0 4px rgba(147,194,28,.28); transform: translateY(-2px); }
    to { box-shadow: 0 8px 20px rgba(15,23,42,.05); transform: translateY(0); }
}

.tp-comment-empty-live {
    border: 1px dashed #cbd5e1;
    border-radius: 18px;
    background: #f8fafc;
    padding: 18px;
    color: #64748b;
    font-size: 13px;
    font-weight: 800;
    text-align: center;
}


/* =========================================================
   CRM Stage card for task profile
   ========================================================= */
.tp-stage-card::before {
    background: linear-gradient(90deg, #74b2d4, #93c21c) !important;
}

.tp-stage-current {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 12px;
}

.tp-stage-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    min-height: 30px;
    padding: 0 11px;
    border-radius: 999px;
    background: #fff;
    border: 1px solid #dbeafe;
    color: #0f172a;
    font-size: 12px;
    font-weight: 950;
}

.tp-stage-badge .dot {
    width: 9px;
    height: 9px;
    border-radius: 999px;
    background: var(--tp-green);
    box-shadow: 0 0 0 4px rgba(147,194,28,.13);
}

.tp-stage-muted {
    color: #64748b;
    font-size: 12px;
    font-weight: 800;
    margin-top: 6px;
}

.tp-stage-form {
    border-top: 1px dashed #e2e8f0;
    padding-top: 12px;
    margin-top: 12px;
}

.tp-stage-select-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 9px;
}

.tp-stage-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 10px;
}

.tp-stage-actions .btn {
    border-radius: 999px !important;
    font-weight: 900;
}

.tp-stage-select-row .select2-container {
    width: 100% !important;
}

.tp-stage-select-row .select2-container--default .select2-selection--single {
    min-height: 39px;
    border-radius: 14px !important;
    border-color: #dbeafe !important;
    background: #f8fafc !important;
}

.tp-stage-select-row .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px;
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
}

.tp-stage-select-row .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px;
}

</style>
@endsection

@section('content')
    <div class="app-content"> 

        <div class="content-wrapper"> 
            <div class="content-body">
                <div class="tp-shell">

                    {{-- LEFT COLUMN: META + PEOPLE + ATTACHMENTS --}}
                    <div class="tp-left-column">

                        {{-- Meta --}}
                        @php
                            $statusLabels = [
                                'open' => 'Offen',
                                'new' => 'Neu',
                                'send' => 'Gesendet',
                                'accepted' => 'Angenommen',
                                'on_progress' => 'In Bearbeitung',
                                'on_going' => 'In Bearbeitung',
                                'working' => 'In Bearbeitung',
                                'completed' => 'Erledigt',
                                'pause' => 'Pausiert',
                                'cancel' => 'Abgebrochen',
                                'junk' => 'Papierkorb',
                                'rejected' => 'Abgelehnt',
                            ];

                            $priorityLabels = [
                                'normal' => 'Normal',
                                'medium' => 'Mittel',
                                'high' => 'Hoch',
                                'very high' => 'Sehr wichtig',
                                'very_high' => 'Sehr wichtig',
                                'low' => 'Niedrig',
                            ];

                            $statusValue = strtolower((string) ($task->task_status ?? 'open'));
                            $priorityValue = strtolower((string) ($task->priority ?? 'normal'));

                            $statusLabel = $statusLabels[$statusValue] ?? ucfirst($statusValue);
                            $priorityLabel = $priorityLabels[$priorityValue] ?? ucfirst($priorityValue);

                            $isPublic = (bool) $task->public;

                            $creator = $task->assignedBy ?? null;
                        @endphp

                        <div class="tp-card mb-1">
                            <h3>Überblick</h3>

                            <table class="tp-kv-table">
                                <tbody>
                                    <tr>
                                        <td>Aufgaben-ID</td>
                                        <td>#{{ $task->id }}</td>
                                    </tr>

                                    <tr>
                                        <td>Status</td>
                                        <td>
                                            <span class="tp-pill tp-pill-strong">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Priorität</td>
                                        <td>
                                            <span class="tp-pill">
                                                <i data-feather="flag" style="width:12px;height:12px;"></i>
                                                {{ $priorityLabel }}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Sichtbarkeit</td>
                                        <td>
                                            @if($isPublic)
                                                <span class="tp-pill">
                                                    <i data-feather="unlock" style="width:12px;height:12px;"></i>
                                                    Öffentlich
                                                </span>
                                            @else
                                                <span class="tp-pill">
                                                    <i data-feather="lock" style="width:12px;height:12px;"></i>
                                                    Privat
                                                </span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Erstellt von</td>
                                        <td>
                                            @if($creator)
                                                <div style="display:flex;align-items:center;gap:.4rem;">
                                                    <div class="tp-avatar-ring">
                                                        @if($creator->image)
                                                            <img src="{{ asset('images/employee/' . $creator->image) }}"
                                                                style="width:100%;height:100%;object-fit:cover;">
                                                        @else
                                                            {{ mb_substr($creator->name ?? '', 0, 1) }}{{ mb_substr($creator->lastname ?? '', 0, 1) }}
                                                        @endif
                                                    </div>

                                                    <div style="font-size:.8rem;">
                                                        {{ trim(($creator->name ?? '') . ' ' . ($creator->lastname ?? '')) ?: 'Unbekannt' }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Nicht angegeben</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Erstellt am</td>
                                        <td>
                                            {{ $task->created_at ? $task->created_at->format('d.m.Y H:i') . ' Uhr' : 'Nicht angegeben' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Fällig am</td>
                                        <td>
                                            @if($task->due_date)
                                                {{ $task->due_date->format('d.m.Y') }}

                                                @if($task->due_time)
                                                    um {{ \Illuminate\Support\Str::of($task->due_time)->beforeLast(':') }} Uhr
                                                @endif
                                            @else
                                                Nicht angegeben
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Geplante Dauer</td>
                                        <td>
                                            @if($task->total_time || $task->total_day)
                                                @if($task->total_time)
                                                    {{ number_format((float) $task->total_time, 2, ',', '.') }} Std.
                                                @endif

                                                @if($task->total_day)
                                                    ({{ number_format((float) $task->total_day, 2, ',', '.') }} Tage)
                                                @endif
                                            @else
                                                Nicht angegeben
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Customer --}}
                        <div class="tp-card mb-1">
                            <h3>Kunde / Auftrag</h3>
                            @if($task->customer)
                                <div style="font-size:.85rem;">
                                    <div><strong>{{ $task->customer->customer_no }}</strong></div>
                                    <div>
                                        {{ $task->customer->lastname }} {{ $task->customer->name }}
                                    </div>
                                    <div style="color:#6b7280;">
                                        {{ $task->customer->postcode }} {{ $task->customer->city }}
                                    </div>
                                </div>
                            @else
                                <div style="font-size:.8rem;color:#9ca3af;">Kein Kunde verknüpft</div>
                            @endif
                        </div>

                        {{-- CRM Stage / Sub Stage --}}
                        <div class="tp-card mb-1 tp-stage-card" id="tp-stage-card"
                             data-update-url="{{ $profileStageUpdateRoute }}">
                            <h3>CRM Stage</h3>

                            <div class="tp-stage-current" id="tp-stage-current">
                                <span class="tp-stage-badge"
                                      style="border-color:{{ data_get($profileStageContext, 'lead_stage_color') ?: '#74b2d4' }}">
                                    <span class="dot" style="background:{{ data_get($profileStageContext, 'lead_stage_color') ?: '#74b2d4' }}"></span>
                                    <span id="tp-stage-current-name">
                                        {{ data_get($profileStageContext, 'lead_stage_name') ?: 'Keine Stage' }}
                                    </span>
                                </span>

                                <span class="tp-stage-badge"
                                      style="border-color:{{ data_get($profileStageContext, 'lead_stage_sub_stage_color') ?: '#93c21c' }}">
                                    <span class="dot" style="background:{{ data_get($profileStageContext, 'lead_stage_sub_stage_color') ?: '#93c21c' }}"></span>
                                    <span id="tp-sub-stage-current-name">
                                        {{ data_get($profileStageContext, 'lead_stage_sub_stage_name') ?: 'Keine Sub Stage' }}
                                    </span>
                                </span>
                            </div>

                            @if($task->leadProductList)
                                <div class="tp-stage-muted">
                                    Quelle: Kundenprodukt #{{ $task->leadProductList->id }}
                                </div>
                            @else
                                <div class="tp-stage-muted">
                                    Diese Aufgabe ist keinem Kundenprodukt zugeordnet.
                                </div>
                            @endif

                            @if($canManageStage)
                                <div class="tp-stage-form">
                                    <div class="tp-stage-select-row">
                                        <div>
                                            <label class="tp-stage-muted" for="tp-lead-stage-select">Stage ändern</label>
                                            <select id="tp-lead-stage-select" class="form-control">
                                                <option value="">Stage automatisch übernehmen</option>
                                                @foreach($taskLeadStagePayload as $stage)
                                                    <option value="{{ data_get($stage, 'id') }}"
                                                        data-color="{{ data_get($stage, 'color') }}"
                                                        @selected((string) data_get($profileStageContext, 'lead_stage_id') === (string) data_get($stage, 'id'))>
                                                        {{ data_get($stage, 'name') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="tp-stage-muted" for="tp-lead-sub-stage-select">Sub Stage ändern</label>
                                            <select id="tp-lead-sub-stage-select" class="form-control">
                                                <option value="">Zuerst Stage wählen</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="tp-stage-actions">
                                        <button type="button" class="btn btn-sm btn-primary" id="tp-stage-save">
                                            Stage speichern
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @php
    // Current employee (Employee ID)
    $currentEmployeeId = $employeeId ?? (int) (auth()->user()->name ?? 0);

    // Creator as Employee ID (assigned_by is stored as employee.id as string)
    $creatorId = (int) ($task->assigned_by ?? 0);

    // Controller IDs as array
    $controllerIds = $controllerEmployees
        ? $controllerEmployees->pluck('id')->map(fn($id) => (int) $id)->all()
        : [];

    // Allowed to manage team if: creator OR one of the controllers
    $canManageTeam = $currentEmployeeId > 0 && (
        $currentEmployeeId === $creatorId ||
        in_array($currentEmployeeId, $controllerIds, true)
    );
                        @endphp

                        {{-- People --}}
                        <div class="tp-card mb-1">
                            <div class="d-flex justify-content-between align-items-center mb-25">
                                <h3 class="mb-0">Team</h3>

                                @if($canManageTeam)
                                    <button type="button"
                                            class="tp-team-manage-btn primary"
                                            data-toggle="modal"
                                            data-target="#tp-modal-team"
                                            title="Team verwalten">
                                        <i class="feather icon-edit"></i>
                                    </button>
                                @endif
                            </div>


                            {{-- Controllers (Verantwortliche, global) --}}
                            <div style="font-size:.8rem;margin-bottom:.35rem;">
                                Verantwortliche
                                <button type="button"
                                        class="tp-team-manage-btn primary ml-25"
                                        data-toggle="modal"
                                        data-target="#tp-modal-controllers"
                                        title="Verantwortliche ändern">
                                    <i class="feather icon-edit-2"></i>
                                </button>
                            </div>
                            @if($controllerEmployees->count())
                                <div>
                                    @foreach($controllerEmployees as $emp)
                                        <div class="tp-avatar-ring" title="{{ $emp->name }} {{ $emp->lastname }}">
                                            @if($emp->image)
                                                <img src="{{ asset('images/employee/' . $emp->image) }}"
                                                    style="width:100%;height:100%;object-fit:cover;">
                                            @else
                                                {{ mb_substr($emp->name, 0, 1) }}{{ mb_substr($emp->lastname, 0, 1) }}
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="font-size:.8rem;color:#9ca3af;">Kein Kontroller definiert</div>
                            @endif

                            {{-- Global employees for the whole task --}}
                            <div style="font-size:.8rem;margin:.75rem 0 .35rem;">
                                Mitarbeiter (gesamte Aufgabe)
                            </div>
                            @if($task->employees->count())
                                <div>
                                    @foreach($task->employees as $emp)
                                        <div class="tp-avatar-ring" title="{{ $emp->name }} {{ $emp->lastname }}">
                                            @if($emp->image)
                                                <img src="{{ asset('images/employee/' . $emp->image) }}"
                                                    style="width:100%;height:100%;object-fit:cover;">
                                            @else
                                                {{ mb_substr($emp->name, 0, 1) }}{{ mb_substr($emp->lastname, 0, 1) }}
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="font-size:.8rem;color:#9ca3af;">Keine Mitarbeiter zugewiesen</div>
                            @endif

                            {{-- Employees per key --}}
                            <div style="font-size:.8rem;margin:.75rem 0 .35rem;">
                                Mitarbeiter nach Schritt
                            </div>
                            <div style="font-size:.78rem;">
                                @forelse($keys as $key)
                                    @php
        $assignedIds = (array) ($key->employee_id ?? []);
        if (is_string($key->employee_id) && $key->employee_id !== '') {
            $assignedIds = json_decode($key->employee_id, true) ?: [];
        }
        $assignedEmps = $assignedIds
            ? \App\Models\Employee::whereIn('id', $assignedIds)->get()
            : collect();
                                    @endphp

                                    <div style="margin-bottom:.35rem;">
                                        <div style="font-weight:600;">
                                            #{{ $loop->iteration }} – {{ $key->task ?? 'Schritt ohne Titel' }}
                                        </div>
                                        @if($assignedEmps->count())
                                            <div style="margin-top:.15rem;">
                                                @foreach($assignedEmps as $emp)
                                                    <div class="tp-avatar-ring" title="{{ $emp->name }} {{ $emp->lastname }}">
                                                        @if($emp->image)
                                                            <img src="{{ asset('images/employee/' . $emp->image) }}"
                                                                style="width:100%;height:100%;object-fit:cover;">
                                                        @else
                                                            {{ mb_substr($emp->name, 0, 1) }}{{ mb_substr($emp->lastname, 0, 1) }}
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div style="font-size:.75rem;color:#9ca3af;margin-top:.1rem;">
                                                Keine Mitarbeiter für diesen Schritt zugewiesen
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div style="font-size:.8rem;color:#9ca3af;">Keine Aufgabenschritte definiert.</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Modal: Controllers (Verantwortliche) --}}
                        <div class="modal fade" id="tp-modal-controllers" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Verantwortliche für diese Aufgabe</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="tp-form-controllers">
                                            @csrf
                                            <div class="form-group">
                                                <label for="tp-controllers-select">Mitarbeiter auswählen</label>
                                                <select id="tp-controllers-select"
                                                        name="controllers[]"
                                                        class="form-control"
                                                        multiple
                                                        style="width:100%;">
                                                    @php
    $currentControllers = $controllerEmployees->pluck('id')->all();
                                                    @endphp
                                                    @foreach($allEmployees as $emp)
                                                        <option value="{{ $emp->id }}"
                                                                data-image="{{ $emp->image ? asset('images/employee/' . $emp->image) : '' }}"
                                                                @if(in_array($emp->id, $currentControllers)) selected @endif>
                                                            {{ $emp->name }} {{ $emp->lastname }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">
                                                    Diese Mitarbeiter sind für die gesamte Aufgabe verantwortlich.
                                                </small>
                                            </div>
                                        </form>

                                        <div class="tp-team-section">
                                            <div class="tp-team-section-title">
                                                <span>Aktuelle Verantwortliche</span>
                                                <small>{{ $controllerEmployees->count() }} ausgewählt</small>
                                            </div>
                                            <div class="tp-team-chip-list" id="tp-current-controllers-list">
                                                @forelse($controllerEmployees as $emp)
                                                    <span class="tp-team-chip" data-controller-chip="{{ $emp->id }}">
                                                        <span class="tp-team-chip-avatar">
                                                            @if($emp->image)
                                                                <img src="{{ asset('images/employee/' . $emp->image) }}" alt="{{ $emp->name }} {{ $emp->lastname }}">
                                                            @else
                                                                {{ mb_substr($emp->name, 0, 1) }}{{ mb_substr($emp->lastname, 0, 1) }}
                                                            @endif
                                                        </span>
                                                        <span>{{ $emp->name }} {{ $emp->lastname }}</span>
                                                        <button type="button"
                                                                class="tp-team-chip-remove js-controller-remove"
                                                                data-employee-id="{{ $emp->id }}"
                                                                title="Entfernen">×</button>
                                                    </span>
                                                @empty
                                                    <div class="tp-empty-soft">Keine Verantwortlichen ausgewählt.</div>
                                                @endforelse
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button"
                                                class="btn btn-secondary btn-sm"
                                                data-dismiss="modal">
                                            Abbrechen
                                        </button>
                                        <button type="button"
                                                id="tp-controllers-save"
                                                class="btn btn-primary btn-sm">
                                            Speichern
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal: Team (Mitarbeiter für Aufgabe / Schritte) --}}
                        <div class="modal fade" id="tp-modal-team" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Team-Mitglieder hinzufügen</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="tp-form-team">
                                            @csrf

                                            {{-- Scope: whole task vs specific keys --}}
                                            <div class="form-group">
                                                <label class="d-block mb-50">Zuweisungsbereich</label>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio"
                                                        class="custom-control-input"
                                                        id="tp-scope-task"
                                                        name="scope"
                                                        value="task"
                                                        checked>
                                                    <label class="custom-control-label" for="tp-scope-task">
                                                        gesamte Aufgabe
                                                    </label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio"
                                                        class="custom-control-input"
                                                        id="tp-scope-keys"
                                                        name="scope"
                                                        value="keys">
                                                    <label class="custom-control-label" for="tp-scope-keys">
                                                        ausgewählte Schritte
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Employees --}}
                                            <div class="form-group">
                                                <label for="tp-employees-select">Mitarbeiter auswählen</label>
                                                <select id="tp-employees-select"
                                                        name="employee_ids[]"
                                                        class="form-control"
                                                        multiple
                                                        style="width:100%;">
                                                    @foreach($allEmployees as $emp)
                                                        <option value="{{ $emp->id }}"
                                                                data-image="{{ $emp->image ? asset('images/employee/' . $emp->image) : '' }}">
                                                            {{ $emp->name }} {{ $emp->lastname }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">
                                                    Diese Mitarbeiter werden der Aufgabe oder den gewählten Schritten hinzugefügt.
                                                </small>
                                            </div>

                                            {{-- Keys (only shown when scope = keys) --}}
                                            <div class="form-group" id="tp-keys-wrapper" style="display:none;">
                                                <label for="tp-keys-select">Aufgabenschritte auswählen</label>
                                                <select id="tp-keys-select"
                                                        name="key_ids[]"
                                                        class="form-control"
                                                        multiple
                                                        style="width:100%;">
                                                    @foreach($keys as $key)
                                                        <option value="{{ $key->id }}">
                                                            #{{ $loop->iteration }} – {{ $key->task ?? 'Schritt ohne Titel' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">
                                                    Die Mitarbeiter werden nur diesen Schritten zugewiesen.
                                                </small>
                                            </div>
                                        </form>

                                        <div class="tp-team-section">
                                            <div class="tp-team-section-title">
                                                <span>Aktuelle Mitarbeiter der gesamten Aufgabe</span>
                                                <small>{{ $task->employees->count() }} zugewiesen</small>
                                            </div>
                                            <div class="tp-team-chip-list" id="tp-current-task-employees-list">
                                                @forelse($task->employees as $emp)
                                                    <span class="tp-team-chip" data-task-employee-chip="{{ $emp->id }}">
                                                        <span class="tp-team-chip-avatar">
                                                            @if($emp->image)
                                                                <img src="{{ asset('images/employee/' . $emp->image) }}" alt="{{ $emp->name }} {{ $emp->lastname }}">
                                                            @else
                                                                {{ mb_substr($emp->name, 0, 1) }}{{ mb_substr($emp->lastname, 0, 1) }}
                                                            @endif
                                                        </span>
                                                        <span>{{ $emp->name }} {{ $emp->lastname }}</span>
                                                        <button type="button"
                                                                class="tp-team-chip-remove js-task-employee-remove"
                                                                data-employee-id="{{ $emp->id }}"
                                                                title="Entfernen">×</button>
                                                    </span>
                                                @empty
                                                    <div class="tp-empty-soft">Keine globalen Mitarbeiter zugewiesen.</div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="tp-team-section">
                                            <div class="tp-team-section-title">
                                                <span>Mitarbeiter pro Schritt</span>
                                                <small>{{ $keys->count() }} Schritte</small>
                                            </div>
                                            @forelse($keys as $key)
                                                @php
        $assignedIdsForModal = (array) ($key->employee_id ?? []);
        if (is_string($key->employee_id) && $key->employee_id !== '') {
            $assignedIdsForModal = json_decode($key->employee_id, true) ?: [];
        }
        $assignedEmpsForModal = $assignedIdsForModal
            ? \App\Models\Employee::whereIn('id', $assignedIdsForModal)->get()
            : collect();
                                                @endphp
                                                <div class="tp-team-key-card" data-key-card="{{ $key->id }}">
                                                    <div class="tp-team-key-title">#{{ $loop->iteration }} – {{ $key->task ?? 'Schritt ohne Titel' }}</div>
                                                    <div class="tp-team-chip-list">
                                                        @forelse($assignedEmpsForModal as $emp)
                                                            <span class="tp-team-chip" data-key-employee-chip="{{ $key->id }}-{{ $emp->id }}">
                                                                <span class="tp-team-chip-avatar">
                                                                    @if($emp->image)
                                                                        <img src="{{ asset('images/employee/' . $emp->image) }}" alt="{{ $emp->name }} {{ $emp->lastname }}">
                                                                    @else
                                                                        {{ mb_substr($emp->name, 0, 1) }}{{ mb_substr($emp->lastname, 0, 1) }}
                                                                    @endif
                                                                </span>
                                                                <span>{{ $emp->name }} {{ $emp->lastname }}</span>
                                                                <button type="button"
                                                                        class="tp-team-chip-remove js-key-employee-remove"
                                                                        data-key-id="{{ $key->id }}"
                                                                        data-employee-id="{{ $emp->id }}"
                                                                        title="Vom Schritt entfernen">×</button>
                                                            </span>
                                                        @empty
                                                            <div class="tp-empty-soft">Keine Mitarbeiter für diesen Schritt.</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="tp-empty-soft">Keine Schritte vorhanden.</div>
                                            @endforelse
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button"
                                                class="btn btn-secondary btn-sm"
                                                data-dismiss="modal">
                                            Abbrechen
                                        </button>
                                        <button type="button"
                                                id="tp-team-save"
                                                class="btn btn-primary btn-sm">
                                            Speichern
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- Attachments (only list – upload kannst du aus alter Logik übernehmen) --}}
                         @php
    $attachmentData = $task->attachments->map(function ($file) {
        return [
            'id' => $file->id,
            'image_name' => $file->image_name,
            'file_type' => $file->file_type,
            'url' => asset('images/task/personal/document/' . $file->image),
        ];
    })->values();
                        @endphp

                        <div class="tp-card">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
                                <h3 class="mb-0">Anhänge</h3>

                                <div style="display:flex;gap:.35rem;align-items:center;">
                                    <input type="text"
                                        id="tp-attach-search"
                                        placeholder="Suche..."
                                        style="font-size:.8rem;padding:.2rem .45rem;border-radius:999px;border:1px solid #e5e7eb;min-width:140px;">
                                    <button type="button"
                                            id="tp-attach-upload-btn"
                                            style="font-size:.8rem;border-radius:999px;border:none;padding:.3rem .8rem;background:#e5f3d0;color:#111827;cursor:pointer;">
                                        Datei wählen
                                    </button>
                                </div>
                            </div>

                            {{-- hidden input for manual select --}}
                            <input type="file" id="tp-attach-file-input" multiple style="display:none;">

                            {{-- drag and drop area --}}
                            <div id="tp-attach-dropzone"
                                style="border:1px dashed #d1d5db;border-radius:.75rem;padding:.6rem .75rem;font-size:.8rem;text-align:center;margin-bottom:.6rem;background:#f9fafb;">
                                Dateien hierher ziehen oder klicken, um hochzuladen.
                            </div>

                            {{-- list --}}
                            @if($task->attachments->count())
                                <ul id="tp-attach-list"
                                    style="list-style:none;margin:0;padding:0;font-size:.8rem;">
                                    @foreach($task->attachments as $file)
                                        <li class="tp-attach-item"
                                            data-index="{{ $loop->index }}"
                                            data-id="{{ $file->id }}"
                                            data-name="{{ $file->image_name }}"
                                            style="display:flex;justify-content:space-between;align-items:center;padding:.25rem 0;border-bottom:1px solid #f3f4f6;cursor:pointer;">
                                            <div style="display:flex;align-items:center;gap:.25rem;">
                                                <i data-feather="file" style="width:14px;height:14px;"></i>
                                                <span>{{ $file->image_name }}</span>
                                                <span style="color:#9ca3af;">({{ $file->file_type }})</span>
                                            </div>
                                            <div style="display:flex;align-items:center;gap:.25rem;">
                                                <button type="button"
                                                        class="tp-attach-open"
                                                        style="border:none;background:none;font-size:.75rem;color:#2563eb;cursor:pointer;">
                                                    Öffnen
                                                </button>
                                                <button type="button"
                                                        class="tp-attach-delete"
                                                        style="border:none;background:none;font-size:.75rem;color:#b91c1c;cursor:pointer;">
                                                    Löschen
                                                </button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div id="tp-attach-empty"
                                    style="font-size:.8rem;color:#9ca3af;">
                                    Keine Dateien hochgeladen
                                </div>
                                <ul id="tp-attach-list"
                                    style="list-style:none;margin:0;padding:0;font-size:.8rem;display:none;"></ul>
                            @endif
                        </div>

                        {{-- Viewer modal --}}
                        <div id="tp-attach-modal-backdrop"
                            style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.65);z-index:9999;align-items:center;justify-content:center;">
                            <div id="tp-attach-modal"
                                style="background:white;border-radius:1rem;max-width:900px;width:96%;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 60px rgba(15,23,42,.35);">
                                <div style="display:flex;align-items:center;justify-content:space-between;padding:.6rem .9rem;border-bottom:1px solid #e5e7eb;">
                                    <div id="tp-attach-modal-title"
                                        style="font-size:.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        Datei
                                    </div>
                                    <div style="display:flex;align-items:center;gap:.35rem;">
                                        <button type="button"
                                                id="tp-attach-prev"
                                                style="border:none;background:none;font-size:1rem;cursor:pointer;">
                                            ‹
                                        </button>
                                        <button type="button"
                                                id="tp-attach-next"
                                                style="border:none;background:none;font-size:1rem;cursor:pointer;">
                                            ›
                                        </button>
                                        <button type="button"
                                                id="tp-attach-modal-close"
                                                style="border:none;background:none;font-size:1.1rem;cursor:pointer;">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                                <div id="tp-attach-modal-body"
                                    style="flex:1;overflow:auto;background:#080b10;display:flex;align-items:center;justify-content:center;padding:1rem;">
                                    {{-- content injected via JS --}}
                                </div>
                                <div id="tp-attach-modal-meta"
                                    style="padding:.5rem .9rem;font-size:.75rem;color:#6b7280;border-top:1px solid #111827;">
                                </div>
                            </div>
                        </div>

                        {{-- JS data --}}
                        <script>
                            window.tpAttachments = @json($attachmentData);
                            window.tpAttachmentsStoreUrl   = "{{ route('personal-tasks.attachments.store', $task->id) }}";
                            window.tpAttachmentDeleteRoute = "{{ route('personal-tasks.attachments.destroy', 0) }}"; // id will be replaced in JS
                        </script>


                    </div>

                    {{-- RIGHT COLUMN: DESCRIPTION + TABS --}}
                    <div class="tp-right-column">
                        <div class="tp-card mb-1">
                            <h3>Beschreibung</h3>
                            <div style="font-size:.85rem;color:#111827;">
                                {{ $task->description ?: 'Keine Beschreibung hinterlegt.' }}
                            </div>
                        </div>

                        <div class="tp-card">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="tp-tabs" id="tp-tabs">
                                    <button type="button" data-tab="steps" class="is-active">Schritte</button>
                                    <button type="button" data-tab="history">Verlauf</button>
                                    <button type="button" data-tab="reports">Berichte</button>
                                    <button type="button" data-tab="notifications">Benachrichtigung</button>
                                </div>
                            </div>

                            {{-- TABS CONTENT --}}
                            <div id="tp-tab-steps" class="tp-tab-panel">
                                @include('admin.todo.personal.profile_steps', ['task' => $task, 'keys' => $keys])
                            </div>

                            <div id="tp-tab-history" class="tp-tab-panel" style="display:none;">
                                @include('admin.todo.personal.profile_history', ['history' => $history])
                            </div>

                            <div id="tp-tab-reports" class="tp-tab-panel" style="display:none;">
                                <div id="tp-comments-live"
                                     data-task-id="{{ $task->id }}"
                                     data-list-url="{{ route('personal.task.comment.view', $task->id) }}"
                                     data-store-url="{{ route('personal.task.comment.store') }}"
                                     data-reply-url="{{ route('personal.task.comment.reply') }}">
                                    @include('admin.todo.personal.profile_reports', [
        'task' => $task,
        'comments' => $comments,
        'employeeId' => $employeeId,
    ])
                                </div>
                            </div>

                            <div id="tp-tab-notifications" class="tp-tab-panel" style="display:none;">
                                 <div class="tp-card" id="tp-notification-card" data-task-id="{{ $task->id }}">
                                    <h3>Benachrichtigungen</h3>

                                    <ul id="tp-notifications-list"
                                        class="activity-timeline timeline-left list-unstyled"
                                        style="margin:0;padding:0;font-size:.8rem;">
                                        <li>
                                            <div class="timeline-info">
                                                <p class="font-weight-bold">Lade Benachrichtigungen...</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div> 
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script') 

<script>
    /* =========================================================
       Task Profile Toaster
       Usage: tpToast('success'|'error'|'warning'|'info', message, title)
       ========================================================= */
    (function () {
        function ensureToastWrap() {
            let wrap = document.getElementById('tp-toast-wrap');
            if (!wrap) {
                wrap = document.createElement('div');
                wrap.id = 'tp-toast-wrap';
                wrap.className = 'tp-toast-wrap';
                document.body.appendChild(wrap);
            }
            return wrap;
        }

        function iconFor(type) {
            return ({ success: '✓', error: '!', warning: '⚠', info: 'i' })[type] || 'i';
        }

        function titleFor(type) {
            return ({ success: 'Gespeichert', error: 'Fehler', warning: 'Hinweis', info: 'Info' })[type] || 'Info';
        }

        window.tpToast = function (type, message, title, timeout) {
            type = type || 'info';
            const wrap = ensureToastWrap();
            const item = document.createElement('div');
            item.className = 'tp-toast-item is-' + type;
            item.innerHTML = `
                <div class="tp-toast-icon">${iconFor(type)}</div>
                <div>
                    <div class="tp-toast-title">${window.escapeHTML ? window.escapeHTML(title || titleFor(type)) : (title || titleFor(type))}</div>
                    <div class="tp-toast-text">${window.escapeHTML ? window.escapeHTML(message || '') : (message || '')}</div>
                </div>
                <button type="button" class="tp-toast-close" aria-label="Schließen">×</button>
            `;
            wrap.appendChild(item);

            const close = function () {
                item.style.animation = 'tpToastOut .16s ease forwards';
                setTimeout(() => item.remove(), 180);
            };

            item.querySelector('.tp-toast-close')?.addEventListener('click', close);
            setTimeout(close, timeout || 4800);
        };

        window.tpToastSuccess = (message, title) => window.tpToast('success', message, title);
        window.tpToastError = (message, title) => window.tpToast('error', message, title);
        window.tpToastWarning = (message, title) => window.tpToast('warning', message, title);
        window.tpToastInfo = (message, title) => window.tpToast('info', message, title);

        // Replace browser alerts with the designed toaster for this profile page.
        window.tpNativeAlert = window.tpNativeAlert || window.alert;
        window.alert = function (message) {
            window.tpToast('warning', String(message || 'Hinweis'));
        };
    })();
</script>

<script>

     window.currentEmployeeId = {{ (int) ($employeeId ?? 0) }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const tpLeadStageOptions = @json($taskLeadStagePayload);
    const tpInitialStageContext = @json($profileStageContext);
    const tpLeadStageContextUrl = @json(\Illuminate\Support\Facades\Route::has('personal-tasks.lead-stage-context') ? route('personal-tasks.lead-stage-context') : '#');

    function tpEscape(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (m) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m];
        });
    }

    function tpStageById(stageId) {
        return (tpLeadStageOptions || []).find(stage => String(stage.id) === String(stageId)) || null;
    }

    function tpSubStageById(stage, subStageId) {
        if (!stage || !Array.isArray(stage.sub_stages)) return null;
        return stage.sub_stages.find(subStage => String(subStage.id) === String(subStageId)) || null;
    }

    function tpDestroySelect2(selector) {
        if (!window.jQuery || !$.fn.select2) return;
        const $el = $(selector);
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
    }

    function tpInitSelect2(selector, placeholder) {
        if (!window.jQuery || !$.fn.select2) return;
        $(selector).select2({
            width: '100%',
            placeholder: placeholder,
            allowClear: true,
        });
    }

    function tpSetSubStageOptions(stageId, selectedSubStageId = '') {
        const select = document.getElementById('tp-lead-sub-stage-select');
        if (!select) return [];

        const stage = tpStageById(stageId);
        const subStages = stage && Array.isArray(stage.sub_stages) ? stage.sub_stages : [];

        tpDestroySelect2('#tp-lead-sub-stage-select');

        select.innerHTML = '<option value="">Sub Stage automatisch übernehmen</option>';

        subStages.forEach(subStage => {
            const option = new Option(subStage.name || ('Sub Stage #' + subStage.id), subStage.id);
            option.dataset.color = subStage.color || '#93c21c';
            option.dataset.key = subStage.key || '';
            select.appendChild(option);
        });

        select.disabled = !stageId || !subStages.length;

        if (selectedSubStageId && subStages.some(item => String(item.id) === String(selectedSubStageId))) {
            select.value = String(selectedSubStageId);
        } else {
            select.value = '';
        }

        tpInitSelect2('#tp-lead-sub-stage-select', select.disabled ? 'Keine Sub Stages' : 'Sub Stage wählen');

        return subStages;
    }

    async function tpLoadRemoteSubStages(stageId, selectedSubStageId = '') {
        const local = tpSetSubStageOptions(stageId, selectedSubStageId);
        if (!stageId || local.length || !tpLeadStageContextUrl || tpLeadStageContextUrl === '#') {
            return;
        }

        try {
            const response = await fetch(`${tpLeadStageContextUrl}?lead_stage_id=${encodeURIComponent(stageId)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            const data = await response.json();
            const remoteSubStages = Array.isArray(data.sub_stage_options) ? data.sub_stage_options : [];

            if (remoteSubStages.length) {
                const stage = tpStageById(stageId);
                if (stage) {
                    stage.sub_stages = remoteSubStages;
                }
                tpSetSubStageOptions(stageId, selectedSubStageId);
            }
        } catch (error) {
            console.warn('Profile sub stages could not be loaded:', error);
        }
    }

    function tpRefreshStageBadges(context) {
        const stageName = context?.lead_stage_name || 'Keine Stage';
        const subStageName = context?.lead_stage_sub_stage_name || 'Keine Sub Stage';

        const stageNameEl = document.getElementById('tp-stage-current-name');
        const subStageNameEl = document.getElementById('tp-sub-stage-current-name');

        if (stageNameEl) stageNameEl.textContent = stageName;
        if (subStageNameEl) subStageNameEl.textContent = subStageName;
    }

    (function initProfileLeadStage() {
        const stageSelect = document.getElementById('tp-lead-stage-select');
        const subStageSelect = document.getElementById('tp-lead-sub-stage-select');
        const saveBtn = document.getElementById('tp-stage-save');
        const card = document.getElementById('tp-stage-card');

        if (!stageSelect || !subStageSelect) {
            return;
        }

        tpInitSelect2('#tp-lead-stage-select', 'Stage wählen');
        tpSetSubStageOptions(stageSelect.value || tpInitialStageContext?.lead_stage_id || '', tpInitialStageContext?.lead_stage_sub_stage_id || '');

        if (window.jQuery && $.fn.select2) {
            $('#tp-lead-stage-select')
                .off('change.tpProfileStage select2:select.tpProfileStage select2:clear.tpProfileStage')
                .on('change.tpProfileStage select2:select.tpProfileStage select2:clear.tpProfileStage', function () {
                    tpLoadRemoteSubStages(this.value || '', '');
                });
        } else {
            stageSelect.addEventListener('change', () => tpLoadRemoteSubStages(stageSelect.value || '', ''));
        }

        saveBtn?.addEventListener('click', async () => {
            const updateUrl = card?.dataset.updateUrl || '#';

            if (!updateUrl || updateUrl === '#') {
                tpToastError('Route personal-tasks.lead-stage.update fehlt.');
                return;
            }

            saveBtn.disabled = true;

            try {
                const response = await fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        lead_stage_id: stageSelect.value || null,
                        lead_stage_sub_stage_id: subStageSelect.value || null,
                    }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Stage konnte nicht gespeichert werden.');
                }

                tpRefreshStageBadges(data.context || {});
                tpToastSuccess(data.message || 'CRM Stage wurde gespeichert.');
            } catch (error) {
                console.error('Profile stage save failed:', error);
                tpToastError(error.message || 'Stage konnte nicht gespeichert werden.');
            } finally {
                saveBtn.disabled = false;
            }
        });
    })();



    // Tabs
    (function() {
        const tabs = document.querySelectorAll('#tp-tabs button');
        const panels = {
            steps:   document.getElementById('tp-tab-steps'),
            history: document.getElementById('tp-tab-history'),
            reports: document.getElementById('tp-tab-reports'),
        };

        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                tabs.forEach(b => b.classList.toggle('is-active', b === btn));
                Object.keys(panels).forEach(key => {
                    panels[key].style.display = (key === tab) ? 'block' : 'none';
                });
            });
        });
    })();
 
    // Toggle key (complete / undo) with SweetAlert, done_status + work_progress
   // Toggle key (complete / undo) with SweetAlert, done_status + work_progress
        document.addEventListener('click', function (e) {
            const row = e.target.closest('.js-key-toggle-row');
            if (!row) return;

            const toggleBtn = e.target.closest('.js-key-toggle');
            if (!toggleBtn) return;

            const keyId  = row.dataset.keyId;
            const isDone = row.dataset.completed === '1';

            // -------------------------
            // 1) Check if current user is assigned to this key
            // -------------------------
            const meIdRaw = window.currentEmployeeId || 0;
            const meId    = parseInt(meIdRaw, 10);

            let assignedIds = [];
            if (row.dataset.employeeIds) {
                try {
                    assignedIds = JSON.parse(row.dataset.employeeIds);
                } catch (err) {
                    console.warn('Invalid employeeIds on key row', err, row.dataset.employeeIds);
                }
            }

            const isMember = meId > 0 && Array.isArray(assignedIds)
                ? assignedIds.map(Number).includes(meId)
                : false;

            if (!isMember) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nicht dein Schritt',
                    text: 'Du bist diesem Aufgabenschritt nicht zugeordnet und kannst ihn nicht bearbeiten.',
                });
                return;
            }

            // -------------------------
            // 2) Existing logic: undo or complete / partial
            // -------------------------

            // --- UNDO COMPLETED STEP ---
            if (isDone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Schritt zurücksetzen?',
                    text: 'Dieser Aufgabenschritt wird wieder als offen markiert.',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, zurücksetzen',
                    cancelButtonText: 'Abbrechen',
                }).then(result => {
                    if (!result.isConfirmed) return;

                    fetch("{{ url('/personal-task-keys') }}/" + keyId + "/toggle", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ mode: 'undo' }),
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data || !data.success) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Fehler',
                                text: 'Der Schritt konnte nicht zurückgesetzt werden.',
                            });
                            return;
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Zurückgesetzt',
                            timer: 1200,
                            showConfirmButton: false,
                        }).then(() => location.reload());
                    })
                    .catch(err => {
                        console.error('Key toggle undo error', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler',
                            text: 'Beim Speichern ist ein Fehler aufgetreten.',
                        });
                    });
                });

                return;
            }

            // --- COMPLETE / PARTIAL STEP ---
            Swal.fire({
                title: 'Aufgabenschritt aktualisieren',
                html: `
                    <div style="text-align:left;margin-bottom:.5rem;">
                        <label for="swal-done-status" style="font-size:.8rem;">Status</label>
                        <select id="swal-done-status" class="swal2-select" style="width:100%;">
                            <option value="complete">Vollständig erledigt</option>
                            <option value="part">Teilweise erledigt</option>
                        </select>
                    </div>
                    <div id="swal-progress-wrap" style="text-align:left;margin-bottom:.5rem;display:none;">
                        <label for="swal-progress" style="font-size:.8rem;">Fortschritt in %</label>
                        <input id="swal-progress"
                            type="number"
                            class="swal2-input"
                            value="50"
                            min="1"
                            max="99"
                            style="width:100%;box-sizing:border-box;">
                    </div>
                    <div style="text-align:left;">
                        <label for="swal-submit_time" style="font-size:.8rem;">Istzeit (Stunden, z.B. 1.5)</label>
                        <input id="swal-submit_time"
                            type="number"
                            class="swal2-input"
                            value="1"
                            min="0"
                            step="0.25"
                            style="width:100%;box-sizing:border-box;">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                cancelButtonText: 'Abbrechen',
                focusConfirm: false,
                didOpen: () => {
                    const statusSel    = document.getElementById('swal-done-status');
                    const progressWrap = document.getElementById('swal-progress-wrap');

                    const updateVisibility = () => {
                        progressWrap.style.display =
                            statusSel.value === 'part' ? 'block' : 'none';
                    };

                    statusSel.addEventListener('change', updateVisibility);
                    updateVisibility();
                },
                preConfirm: () => {
                    const statusSel   = document.getElementById('swal-done-status');
                    const submitInput = document.getElementById('swal-submit_time');
                    const progInput   = document.getElementById('swal-progress');

                    const doneStatus  = statusSel.value;
                    const submitTime  = parseFloat(submitInput.value);

                    if (isNaN(submitTime) || submitTime < 0) {
                        Swal.showValidationMessage('Bitte eine gültige Istzeit angeben (>= 0).');
                        return false;
                    }

                    let workProgress = 100;
                    if (doneStatus === 'part') {
                        workProgress = parseInt(progInput.value, 10);
                        if (isNaN(workProgress) || workProgress <= 0 || workProgress >= 100) {
                            Swal.showValidationMessage('Bitte Fortschritt zwischen 1 und 99 % eingeben.');
                            return false;
                        }
                    }

                    return {
                        done_status: doneStatus,
                        work_progress: workProgress,
                        submit_time: submitTime,
                    };
                },
            }).then(result => {
                if (!result.isConfirmed || !result.value) return;

                const payload = {
                    mode: 'complete',
                    done_status:   result.value.done_status,
                    work_progress: result.value.work_progress,
                    submit_time:   result.value.submit_time,
                };

                fetch("{{ url('/personal-task-keys') }}/" + keyId + "/toggle", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                })
                .then(res => res.json())
                .then(data => {
                    if (!data || !data.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler',
                            text: 'Der Aufgabenschritt konnte nicht aktualisiert werden.',
                        });
                        return;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Gespeichert',
                        timer: 1200,
                        showConfirmButton: false,
                    }).then(() => location.reload());
                })
                .catch(err => {
                    console.error('Key toggle save error', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler',
                        text: 'Beim Speichern ist ein Fehler aufgetreten.',
                    });
                });
            });
        });

    // Comments (reports) - realtime, no page reload
    (function () {
        const live = document.getElementById('tp-comments-live');
        if (!live) return;

        const taskId = live.dataset.taskId;
        const listUrl = live.dataset.listUrl;
        const storeUrl = live.dataset.storeUrl;
        const replyUrl = live.dataset.replyUrl;

        function esc(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (m) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m];
            });
        }

        function employeeName(item) {
            return [item.name, item.lastname].filter(Boolean).join(' ').trim() || 'Unbekannt';
        }

        function avatar(item) {
            const name = employeeName(item);
            const initials = name.split(/\s+/).filter(Boolean).map(p => p.charAt(0)).join('').slice(0, 2).toUpperCase() || '?';
            if (item.image) {
                const img = String(item.image).startsWith('http') || String(item.image).startsWith('/')
                    ? item.image
                    : '{{ asset('images/employee') }}/' + item.image;
                return `<span class="tp-avatar-ring"><img src="${esc(img)}" alt="${esc(name)}" style="width:100%;height:100%;object-fit:cover;"></span>`;
            }
            return `<span class="tp-avatar-ring">${esc(initials)}</span>`;
        }

        function dateText(value) {
            if (!value) return '';
            try { return new Date(value).toLocaleString('de-DE'); } catch { return ''; }
        }

        function commentHtml(comment, replies = [], isNew = false) {
            return `
                <article class="tp-comment ${isNew ? 'is-new' : ''}" data-comment-id="${esc(comment.id)}">
                    <div class="tp-comment-header">
                        ${avatar(comment)}
                        <span class="tp-comment-name">${esc(employeeName(comment))}</span>
                        <span class="tp-comment-time">${esc(dateText(comment.created_at))}</span>
                    </div>
                    <div class="tp-comment-body">${esc(comment.comment).replace(/\n/g, '<br>')}</div>
                    <button type="button" class="tp-comment-actions js-reply-toggle">Antworten</button>
                    <form class="tp-live-reply-form tp-reply-form" data-comment-id="${esc(comment.id)}" style="display:none;margin-top:10px;">
                        <textarea name="comment" placeholder="Antwort schreiben..."></textarea>
                        <div class="tp-live-form-actions">
                            <button type="button" class="tp-live-btn js-reply-cancel">Abbrechen</button>
                            <button type="submit" class="tp-live-btn primary js-reply-send">Antwort speichern</button>
                        </div>
                    </form>
                    <div class="tp-comment-replies">
                        ${replies.map(reply => commentHtml(reply, [], false)).join('')}
                    </div>
                </article>
            `;
        }

        function buildTree(items) {
            const byParent = {};
            (items || []).forEach(item => {
                const parent = item.parent_id ? String(item.parent_id) : 'root';
                if (!byParent[parent]) byParent[parent] = [];
                byParent[parent].push(item);
            });
            return { roots: byParent.root || [], byParent };
        }

        function renderComments(items, newestId = null) {
            const { roots, byParent } = buildTree(items);
            live.innerHTML = `
                <div class="tp-live-report-shell">
                    <form id="tp-report-form" class="tp-live-comment-form">
                        <textarea name="comment" placeholder="Kommentar / Bericht schreiben..."></textarea>
                        <div class="tp-live-form-actions">
                            <button type="submit" class="tp-live-btn primary">Kommentar speichern</button>
                        </div>
                    </form>
                    <div class="tp-comments-list-live">
                        ${roots.length ? roots.map(comment => commentHtml(comment, byParent[String(comment.id)] || [], String(comment.id) === String(newestId))).join('') : '<div class="tp-comment-empty-live">Noch keine Kommentare vorhanden.</div>'}
                    </div>
                </div>
            `;
            if (window.feather) feather.replace();
        }

        async function fetchJson(url, options = {}) {
            const res = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(options.headers || {})
                },
                ...options,
            });
            const text = await res.text();
            let data = null;
            try { data = text ? JSON.parse(text) : null; } catch { data = text; }
            if (!res.ok) {
                throw new Error((data && data.message) ? data.message : 'HTTP ' + res.status);
            }
            return data;
        }

        async function reloadComments(newestId = null) {
            const data = await fetchJson(listUrl);
            renderComments(Array.isArray(data) ? data : (data.data || []), newestId);
        }

        live.addEventListener('submit', async function (event) {
            const form = event.target.closest('#tp-report-form, .tp-live-reply-form, .tp-reply-form');
            if (!form) return;
            event.preventDefault();
            event.stopPropagation();

            const textarea = form.querySelector('textarea[name="comment"]');
            const comment = (textarea?.value || '').trim();
            if (!comment) {
                window.tpToastWarning ? tpToastWarning('Bitte zuerst einen Kommentar schreiben.') : alert('Bitte zuerst einen Kommentar schreiben.');
                return;
            }

            const isReply = form.matches('.tp-live-reply-form, .tp-reply-form') && form.dataset.commentId;
            const payload = isReply
                ? { task_id: taskId, parent_id: form.dataset.commentId, comment }
                : { task_id: taskId, comment };

            try {
                const data = await fetchJson(isReply ? replyUrl : storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                textarea.value = '';
                await reloadComments(data?.comment?.id || data?.id || null);
                window.tpToastSuccess ? tpToastSuccess(isReply ? 'Antwort gespeichert.' : 'Kommentar gespeichert.') : null;
            } catch (err) {
                console.error('comment/reply save error', err);
                window.tpToastError ? tpToastError(err.message || 'Kommentar konnte nicht gespeichert werden.') : alert('Kommentar konnte nicht gespeichert werden.');
            }
        }, true);

        live.addEventListener('click', function (event) {
            const replyToggle = event.target.closest('.js-reply-toggle');
            if (replyToggle) {
                event.preventDefault();
                const wrap = replyToggle.closest('.tp-comment');
                const form = wrap?.querySelector('.tp-reply-form');
                if (form) form.style.display = form.style.display === 'none' || !form.style.display ? 'block' : 'none';
                return;
            }

            const cancel = event.target.closest('.js-reply-cancel');
            if (cancel) {
                event.preventDefault();
                const form = cancel.closest('.tp-reply-form');
                if (form) form.style.display = 'none';
            }
        }, true);

        // Initial realtime render. This also fixes old partial reply buttons.
        reloadComments().catch(err => console.warn('initial comments load failed', err));
    })();

    if (window.feather) {
        feather.replace();
    } 

    const controllersUrl   = "{{ route('personal-tasks.team.controllers', $task->id) }}";
    const employeesTaskUrl = "{{ route('personal-tasks.team.employees', $task->id) }}";
    const employeesKeysUrl = "{{ route('personal-tasks.team.employees-keys', $task->id) }}";

    // (existing code for tabs, key toggle, comments ... keep it)

    // -------------------------------
    // Team – scope toggle (task / keys)
    // -------------------------------
    (function() {
        const scopeRadios  = document.querySelectorAll('input[name="scope"]');
        const keysWrapper  = document.getElementById('tp-keys-wrapper');

        if (!scopeRadios.length || !keysWrapper) return;

        function refreshScope() {
            const scope = document.querySelector('input[name="scope"]:checked')?.value || 'task';
            keysWrapper.style.display = scope === 'keys' ? 'block' : 'none';
        }

        scopeRadios.forEach(r => r.addEventListener('change', refreshScope));
        refreshScope();
    })();

    $('#tp-controllers-select, #tp-employees-select, #tp-keys-select').select2({
        width: '100%'
    });


   // -------------------------------
    // Save controllers
    // -------------------------------
    (function() {
        const canManageTeam = @json($canManageTeam); // <--- HERE

        const btn = document.getElementById('tp-controllers-save');
        if (!btn) return;

        btn.addEventListener('click', () => {
            if (!canManageTeam) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keine Berechtigung',
                    text: 'Sie dürfen die Verantwortlichen für diese Aufgabe nicht ändern.',
                });
                return;
            }

            const select = document.getElementById('tp-controllers-select');
            if (!select) return;

            const values = Array.from(select.options)
                .filter(o => o.selected)
                .map(o => parseInt(o.value, 10))
                .filter(v => !isNaN(v));

            fetch(controllersUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ controllers: values }),
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) {
                    tpToastError('Verantwortliche konnten nicht aktualisiert werden.');
                    return;
                }
                closeTaskModal(document.getElementById('tp-modal-controllers'));
                location.reload();
            })
            .catch(err => {
                console.error('controllers save error', err);
                tpToastError('Fehler beim Speichern der Verantwortlichen.');
            });
        });
    })();

    // -------------------------------
    // Save team (employees)
    // -------------------------------
    (function() {
        const canManageTeam = @json($canManageTeam); // <--- AND HERE

        const btn = document.getElementById('tp-team-save');
        if (!btn) return;

        btn.addEventListener('click', () => {
            if (!canManageTeam) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keine Berechtigung',
                    text: 'Sie dürfen das Team für diese Aufgabe nicht ändern.',
                });
                return;
            }

            const form          = document.getElementById('tp-form-team');
            const scopeInput    = form.querySelector('input[name="scope"]:checked');
            const employeesSel  = document.getElementById('tp-employees-select');
            const keysSel       = document.getElementById('tp-keys-select');

            const scope         = scopeInput ? scopeInput.value : 'task';

            const employeeIds = Array.from(employeesSel.options)
                .filter(o => o.selected)
                .map(o => parseInt(o.value, 10))
                .filter(v => !isNaN(v));

            if (!employeeIds.length) {
                tpToastWarning('Bitte mindestens einen Mitarbeiter wählen.');
                return;
            }

            let url     = employeesTaskUrl;
            let payload = { employee_ids: employeeIds };

            if (scope === 'keys') {
                const keyIds = Array.from(keysSel.options)
                    .filter(o => o.selected)
                    .map(o => parseInt(o.value, 10))
                    .filter(v => !isNaN(v));

                if (!keyIds.length) {
                    tpToastWarning('Bitte mindestens einen Aufgabenschritt wählen.');
                    return;
                }

                url = employeesKeysUrl;
                payload.key_ids = keyIds;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) {
                    tpToastError('Team konnte nicht aktualisiert werden.');
                    return;
                }
                closeTaskModal(document.getElementById('tp-modal-team'));
                location.reload();
            })
            .catch(err => {
                console.error('team save error', err);
                tpToastError('Fehler beim Speichern des Teams.');
            });
        });
    })();
    if (window.feather) {
        feather.replace();
    }


    (function () {
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const attachments      = window.tpAttachments || [];
        const storeUrl         = window.tpAttachmentsStoreUrl;
        const deleteRouteProto = window.tpAttachmentDeleteRoute; // ends with /0

        const fileInput  = document.getElementById('tp-attach-file-input');
        const uploadBtn  = document.getElementById('tp-attach-upload-btn');
        const dropzone   = document.getElementById('tp-attach-dropzone');
        const searchInput= document.getElementById('tp-attach-search');
        const listEl     = document.getElementById('tp-attach-list');
        const emptyEl    = document.getElementById('tp-attach-empty');

        const modalBackdrop = document.getElementById('tp-attach-modal-backdrop');
        const modalBody     = document.getElementById('tp-attach-modal-body');
        const modalMeta     = document.getElementById('tp-attach-modal-meta');
        const modalTitle    = document.getElementById('tp-attach-modal-title');
        const btnClose      = document.getElementById('tp-attach-modal-close');
        const btnPrev       = document.getElementById('tp-attach-prev');
        const btnNext       = document.getElementById('tp-attach-next');

        let currentIndex = null;

        function reloadPage() {
            window.location.reload();
        }

        // -----------------------
        // Upload helpers
        // -----------------------
        function uploadFiles(files) {
            if (!files || !files.length) return;

            const formData = new FormData();
            Array.from(files).forEach(file => {
                formData.append('files[]', file);
            });

            fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) {
                    tpToastError('Upload fehlgeschlagen.');
                    return;
                }
                // simplest: reload to sync list + JS data
                reloadPage();
            })
            .catch(err => {
                console.error('upload error', err);
                tpToastError('Fehler beim Hochladen.');
            });
        }

        // Click → file input
        if (uploadBtn && fileInput) {
            uploadBtn.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', e => {
                if (!e.target.files.length) return;
                uploadFiles(e.target.files);
            });
        }

        // Drag & drop
        if (dropzone) {
            ['dragenter','dragover'].forEach(ev => {
                dropzone.addEventListener(ev, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.style.background = '#eef3ff';
                    dropzone.style.borderColor = '#93c5fd';
                });
            });

            ['dragleave','drop'].forEach(ev => {
                dropzone.addEventListener(ev, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.style.background = '#f9fafb';
                    dropzone.style.borderColor = '#d1d5db';
                });
            });

            dropzone.addEventListener('drop', e => {
                const files = e.dataTransfer.files;
                uploadFiles(files);
            });

            // click on dropzone = open file dialog
            dropzone.addEventListener('click', () => {
                if (fileInput) fileInput.click();
            });
        }

        // -----------------------
        // Search
        // -----------------------
        if (searchInput && listEl) {
            searchInput.addEventListener('input', () => {
                const q = searchInput.value.toLowerCase();
                const items = listEl.querySelectorAll('.tp-attach-item');

                items.forEach(li => {
                    const name = li.dataset.name ? li.dataset.name.toLowerCase() : '';
                    const text = li.textContent.toLowerCase();
                    const match = !q || name.includes(q) || text.includes(q);
                    li.style.display = match ? 'flex' : 'none';
                });
            });
        }

        // -----------------------
        // Viewer
        // -----------------------
        function openViewer(index) {
            if (!attachments.length) return;
            if (index < 0 || index >= attachments.length) return;

            currentIndex = index;
            const att = attachments[index];

            modalTitle.textContent = att.image_name || 'Datei';
            modalMeta.textContent  = att.file_type ? `Typ: ${att.file_type}` : '';

            const ext = (att.file_type || '').toLowerCase();
            let innerHtml = '';

            if (['jpg','jpeg','png','gif','webp','bmp','svg'].includes(ext)) {
                innerHtml = `<img src="${att.url}"
                                   style="max-width:100%;max-height:80vh;object-fit:contain;border-radius:.5rem;">`;
            } else if (ext === 'pdf') {
                innerHtml = `<iframe src="${att.url}"
                                      style="width:100%;height:80vh;border:none;border-radius:.5rem;background:white;"></iframe>`;
            } else {
                innerHtml = `
                    <div style="color:white;text-align:center;font-size:.9rem;">
                        <p>Dieser Dateityp kann hier nicht direkt angezeigt werden.</p>
                        <p><a href="${att.url}" target="_blank" style="color:#93c5fd;">Im neuen Tab öffnen</a></p>
                    </div>`;
            }

            modalBody.innerHTML = innerHtml;
            modalBackdrop.style.display = 'flex';
        }

        function closeViewer() {
            modalBackdrop.style.display = 'none';
            currentIndex = null;
        }

        if (btnClose) {
            btnClose.addEventListener('click', closeViewer);
        }
        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', e => {
                if (e.target === modalBackdrop) {
                    closeViewer();
                }
            });
        }

        if (btnPrev) {
            btnPrev.addEventListener('click', e => {
                e.stopPropagation();
                if (currentIndex === null || !attachments.length) return;
                const nextIndex = (currentIndex - 1 + attachments.length) % attachments.length;
                openViewer(nextIndex);
            });
        }

        if (btnNext) {
            btnNext.addEventListener('click', e => {
                e.stopPropagation();
                if (currentIndex === null || !attachments.length) return;
                const nextIndex = (currentIndex + 1) % attachments.length;
                openViewer(nextIndex);
            });
        }

        if (listEl) {
            listEl.addEventListener('click', e => {
                const openBtn = e.target.closest('.tp-attach-open');
                const deleteBtn = e.target.closest('.tp-attach-delete');
                const item = e.target.closest('.tp-attach-item');

                if (!item) return;

                const index = parseInt(item.dataset.index, 10);

                if (deleteBtn) {
                    const id = item.dataset.id;
                    if (!id) return;

                    if (!confirm('Anhang wirklich löschen?')) return;

                    const deleteUrl = deleteRouteProto.replace(/0$/, id);

                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data || !data.success) {
                            tpToastError('Löschen fehlgeschlagen.');
                            return;
                        }
                        reloadPage();
                    })
                    .catch(err => {
                        console.error('delete error', err);
                        tpToastError('Fehler beim Löschen.');
                    });

                    return;
                }

                // click anywhere on item or "Öffnen"
                if (openBtn || e.target.closest('.tp-attach-item')) {
                    openViewer(index);
                }
            });
        }

        if (window.feather) {
            feather.replace();
        }
    })();
</script>

<script>
    // Tabs
    (function() {
        const tabs = document.querySelectorAll('#tp-tabs button');
        const panels = {
            steps:          document.getElementById('tp-tab-steps'),
            history:        document.getElementById('tp-tab-history'),
            reports:        document.getElementById('tp-tab-reports'),
            notifications:  document.getElementById('tp-tab-notifications'),
        };

        function showTab(tabKey) {
            // toggle button state
            tabs.forEach(btn => {
                btn.classList.toggle('is-active', btn.dataset.tab === tabKey);
            });

            // toggle panels
            Object.keys(panels).forEach(key => {
                if (!panels[key]) return;
                panels[key].style.display = (key === tabKey) ? 'block' : 'none';
            });
        }

        // click handler
        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                showTab(tab);

                // when notifications tab is opened first time, trigger load
                if (tab === 'notifications') {
                    if (window.fetchTaskNotificationsOnce) {
                        window.fetchTaskNotificationsOnce();
                    }
                }
            });
        });

        // initial tab (fallback: first button or "steps")
        const activeBtn = document.querySelector('#tp-tabs button.is-active') || tabs[0];
        const initial   = activeBtn ? activeBtn.dataset.tab : 'steps';
        showTab(initial);
    })();
</script>

<script>
    (function () {
        const $card   = $('#tp-notification-card');
        const $list   = $('#tp-notifications-list');
        const baseUrl = "{{ url('/notifications/task') }}";

        if (!$card.length || !$list.length) {
            console.warn('Notification elements not found in DOM.');
            return;
        }

        const taskId = $card.data('task-id');
        if (!taskId) {
            console.error('Task ID fehlt für Benachrichtigungen.');
            return;
        }

        let loaded = false;

        function fetchTaskNotifications(id) {
            $.ajax({
                url : baseUrl + '/' + id,
                type: 'GET',
                success: function (response) {
                    console.log('Notifications received:', response);

                    $list.empty();

                    const items = (response && response.data) ? response.data : [];

                    if (items.length === 0) {
                        $list.append(`
                            <li>
                                <div class="timeline-info">
                                    <p class="font-weight-bold">Keine Benachrichtigungen</p>
                                </div>
                            </li>
                        `);
                        return;
                    }

                    items.forEach(function (notification) {
                        const title       = notification.title   || 'Benachrichtigung';
                        const message     = notification.message || 'Keine Details verfügbar.';
                        const performedAt = notification.performed_at
                            ? new Date(notification.performed_at).toLocaleString()
                            : '';

                        $list.append(`
                            <li style="margin-bottom:.6rem;">
                                <div class="timeline-icon bg-primary">
                                    <i class="feather icon-bell font-medium-2"></i>
                                </div>
                                <div class="timeline-info">
                                    <p class="font-weight-bold">${title}</p>
                                    <span>${message}</span>
                                </div>
                                <small>${performedAt}</small>
                            </li>
                        `);
                    });
                },
                error: function (xhr) {
                    console.error('Error fetching notifications:', xhr);

                    $list.empty().append(`
                        <li>
                            <div class="timeline-info">
                                <p class="font-weight-bold text-danger">
                                    Benachrichtigungen konnten nicht geladen werden.
                                </p>
                            </div>
                        </li>
                    `);

                    if (window.Swal) {
                        Swal.fire({
                            icon : 'error',
                            title: 'Fehler',
                            text : 'Benachrichtigungen konnten nicht geladen werden. Bitte erneut versuchen.',
                        });
                    }
                }
            });
        }

        // expose "load once" function for the tabs script
        window.fetchTaskNotificationsOnce = function () {
            if (loaded) return;
            loaded = true;
            fetchTaskNotifications(taskId);
        };

        // Optional: if notifications tab is initially active, load immediately
        const activeBtn = document.querySelector('#tp-tabs button.is-active');
        if (activeBtn && activeBtn.dataset.tab === 'notifications') {
            window.fetchTaskNotificationsOnce();
        }
    })();
</script>


<script>
/* =========================================================
   Brand-like custom modal + team remove actions
   Does not depend on Bootstrap modal JS.
   ========================================================= */
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const taskId = {{ (int) $task->id }};
    const canManageTeam = @json($canManageTeam);

    const urls = {
        controllersSave: "{{ route('personal-tasks.team.controllers', $task->id) }}",
        taskEmployeeDetachBase: "{{ url('admin/todo/personal/' . $task->id . '/employees') }}",
        keyEmployeeDetachBase: "{{ url('/personal-tasks/' . $task->id . '/team/keys') }}",
    };

    function toast(type, title, message) {
        if (window.tpToast) {
            window.tpToast(type, title, message);
            return;
        }
        if (type === 'error' && window.tpToastError) return window.tpToastError(message || title);
        if (type === 'warning' && window.tpToastWarning) return window.tpToastWarning(message || title);
        console[type === 'error' ? 'error' : 'log'](title, message || '');
    }

    function jsonFetch(url, options = {}) {
        return fetch(url, {
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...(options.headers || {}),
            },
            ...options,
        }).then(async (res) => {
            const text = await res.text();
            let data = {};
            try { data = text ? JSON.parse(text) : {}; } catch { data = { message: text }; }
            if (!res.ok || data.success === false) {
                throw new Error(data.message || `HTTP ${res.status}`);
            }
            return data;
        });
    }

    function openTaskModal(selector) {
        const modal = document.querySelector(selector);
        if (!modal) return;
        if (modal.parentElement !== document.body) document.body.appendChild(modal);
        modal.style.zIndex = '2147483000';
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('tp-modal-open');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            if (window.jQuery && jQuery.fn.select2) {
                jQuery(modal).find('select').each(function () {
                    const $el = jQuery(this);
                    if ($el.hasClass('select2-hidden-accessible')) $el.select2('destroy');
                    $el.select2({ width: '100%', dropdownParent: jQuery(modal) });
                });
            }
            if (window.feather) feather.replace();
        }, 30);
    }

    function closeTaskModal(modal) {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (!document.querySelector('#tp-modal-team.is-open, #tp-modal-controllers.is-open')) {
            document.body.classList.remove('tp-modal-open');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('click', function (event) {
        const openBtn = event.target.closest('[data-toggle="modal"][data-target^="#tp-modal-"]');
        if (openBtn) {
            event.preventDefault();
            event.stopPropagation();
            openTaskModal(openBtn.getAttribute('data-target'));
            return;
        }

        const closeBtn = event.target.closest('[data-dismiss="modal"], #tp-modal-team .close, #tp-modal-controllers .close');
        if (closeBtn) {
            event.preventDefault();
            closeTaskModal(closeBtn.closest('#tp-modal-team, #tp-modal-controllers'));
            return;
        }

        if (event.target.matches('#tp-modal-team.is-open, #tp-modal-controllers.is-open')) {
            closeTaskModal(event.target);
            return;
        }
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        document.querySelectorAll('#tp-modal-team.is-open, #tp-modal-controllers.is-open').forEach(closeTaskModal);
    });

    // Remove controller: deselect it and save the remaining controller list.
    document.addEventListener('click', async function (event) {
        const btn = event.target.closest('.js-controller-remove');
        if (!btn) return;
        event.preventDefault();

        if (!canManageTeam) {
            toast('warning', 'Keine Berechtigung', 'Sie dürfen die Verantwortlichen nicht ändern.');
            return;
        }

        const employeeId = parseInt(btn.dataset.employeeId || '0', 10);
        const select = document.getElementById('tp-controllers-select');
        if (!employeeId || !select) return;

        Array.from(select.options).forEach((option) => {
            if (parseInt(option.value, 10) === employeeId) option.selected = false;
        });
        if (window.jQuery && jQuery.fn.select2) jQuery(select).trigger('change');

        const remaining = Array.from(select.options)
            .filter((option) => option.selected)
            .map((option) => parseInt(option.value, 10))
            .filter(Boolean);

        try {
            await jsonFetch(urls.controllersSave, {
                method: 'POST',
                body: JSON.stringify({ controllers: remaining }),
            });
            toast('success', 'Gespeichert', 'Verantwortliche wurden aktualisiert.');
            setTimeout(() => location.reload(), 500);
        } catch (error) {
            console.error(error);
            toast('error', 'Fehler', error.message || 'Verantwortliche konnten nicht entfernt werden.');
        }
    });

    // Remove global task employee.
    document.addEventListener('click', async function (event) {
        const btn = event.target.closest('.js-task-employee-remove');
        if (!btn) return;
        event.preventDefault();

        if (!canManageTeam) {
            toast('warning', 'Keine Berechtigung', 'Sie dürfen das Team nicht ändern.');
            return;
        }

        const employeeId = parseInt(btn.dataset.employeeId || '0', 10);
        if (!employeeId) return;

        try {
            await jsonFetch(`${urls.taskEmployeeDetachBase}/${employeeId}`, { method: 'DELETE', body: JSON.stringify({}) });
            toast('success', 'Entfernt', 'Mitarbeiter wurde von der Aufgabe entfernt.');
            setTimeout(() => location.reload(), 500);
        } catch (error) {
            console.error(error);
            toast('error', 'Fehler', error.message || 'Mitarbeiter konnte nicht entfernt werden.');
        }
    });

    // Remove employee from a specific key/step.
    document.addEventListener('click', async function (event) {
        const btn = event.target.closest('.js-key-employee-remove');
        if (!btn) return;
        event.preventDefault();

        if (!canManageTeam) {
            toast('warning', 'Keine Berechtigung', 'Sie dürfen Schritt-Mitarbeiter nicht ändern.');
            return;
        }

        const keyId = parseInt(btn.dataset.keyId || '0', 10);
        const employeeId = parseInt(btn.dataset.employeeId || '0', 10);
        if (!keyId || !employeeId) return;

        try {
            await jsonFetch(`${urls.keyEmployeeDetachBase}/${keyId}/employees/${employeeId}`, {
                method: 'DELETE',
                body: JSON.stringify({}),
            });
            toast('success', 'Entfernt', 'Mitarbeiter wurde vom Schritt entfernt.');
            setTimeout(() => location.reload(), 500);
        } catch (error) {
            console.error(error);
            toast('error', 'Fehler', error.message || 'Mitarbeiter konnte nicht vom Schritt entfernt werden.');
        }
    });
})();
</script>


<script>
(function(){
    window.tpToastSuccess = window.tpToastSuccess || function(message){ if (window.tpToast) tpToast('success','Gespeichert',message); else console.log(message); };
    window.tpToastError = window.tpToastError || function(message){ if (window.tpToast) tpToast('error','Fehler',message); else alert(message); };
    window.tpToastWarning = window.tpToastWarning || function(message){ if (window.tpToast) tpToast('warning','Hinweis',message); else alert(message); };
})();
</script>

@endsection



@push('scripts')
    <script>
        window.GlobalBreadcrumbs =[
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Aufgabeliste',
                url: "{{ url('admin/todo/personal')}}", 
            },
            {
                label: '{{ $task->task_title ?? 'Ohne Titel' }}',
                url: "{{ url()->current()}}",
                clickable: false
            }

        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush