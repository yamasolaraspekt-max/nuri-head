@extends('admin.layouts.app')

@section('title', 'Persönliche Aufgaben')

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">

<style>
    .pt-layout {
        display:flex;
        flex-direction:column;
        gap:1rem;
    }
    .pt-header {
        display:flex;
        flex-wrap:wrap;
        gap:.75rem;
        justify-content:space-between;
        align-items:center;
    }
    .pt-tabs {
        display:flex;
        flex-wrap:wrap;
        gap:.25rem;
    }
    .pt-tab {
        font-size:12px;
        padding:.3rem .75rem;
        border-radius:999px;
        border:1px solid #e5e7eb;
        background:#f9fafb;
        cursor:pointer;
    }
    .pt-tab.is-active {
        background:#111827;
        color:#fff;
        border-color:#111827;
    }
    .pt-view-toggle {
        display:inline-flex;
        border-radius:999px;
        border:1px solid #e5e7eb;
        overflow:hidden;
    }
    .pt-view-toggle button {
        font-size:12px;
        padding:.25rem .75rem;
        border:none;
        background:transparent;
        cursor:pointer;
    }
    .pt-view-toggle button.is-active {
        background:#111827;
        color:#fff;
    }
    .pt-filters {
        display:flex;
        flex-wrap:wrap;
        gap:.5rem;
        font-size:12px;
    }
    .pt-filters select,
    .pt-filters input {
        font-size:12px;
        padding:.25rem .4rem;
        border-radius:.5rem;
        border:1px solid #e5e7eb;
    }

    /* Board */
    .pt-board {
        display:grid;
        grid-template-columns:repeat(3,minmax(0,1fr));
        gap:.75rem;
    }
    @media (max-width:900px){
        .pt-board{grid-template-columns:1fr;}
    }
    .pt-column {
        background: #f1f1f1;
        border-right: 2px dashed #c5c5c5;
        padding: .5rem;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 220px);
    }
    .pt-column-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .25rem;
        font-size: 15px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #636363;
        background: #cfe09b;
        padding: 12px;
        font-weight: 500;
    }
    .pt-column-body {
        flex:1;
        overflow-y:auto;
        padding-right:.25rem;
        display:flex;
        flex-direction:column;
        gap:.5rem;
    }

    /* Card */
    .pt-card {
        position:relative;
        border-radius:0;
        border:1px solid #e5e7eb;
        background:#fff;
        padding:.55rem .7rem .6rem;
        font-size:12px;
        cursor:grab; 
        border-left:4px solid var(--pt-card-color,#0f172a);
        transition:box-shadow .15s ease, transform .15s ease, background .15s ease;
    }
    .pt-card:hover {
        box-shadow:0 18px 45px rgba(15,23,42,.1);
        transform:translateY(-1px);
    }
    .pt-card.is-pending {
        background:#f9fafb;
        border-left-color:#ff7561;
    }
    .pt-card-header {
        display:flex;
        justify-content:space-between;
        gap:.75rem;
        margin-bottom:.25rem;
    }
    .pt-card-title-wrap {
        flex:1;
        min-width:0;
    }
    .pt-card-title {
        font-weight:600;
        font-size:13px;
        margin-bottom:.15rem;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }
    .pt-card-meta {
        display:flex;
        flex-wrap:wrap;
        gap:.25rem .45rem;
        color:#6b7280;
    }
    .pt-card-side {
        display:flex;
        flex-direction:column;
        align-items:flex-end;
        gap:.25rem;
    }
    .pt-card-color-picker {
        width:20px;
        height:20px;
        padding:0;
        border-radius:999px;
        border:none;
        background:transparent;
    }
    .pt-card-row {
        display:flex;
        align-items:center;
        gap:.4rem;
        margin-top:.2rem;
    }
    .pt-card-label {
        font-size:11px;
        color:#9ca3af;
        display:flex;
        align-items:center;
        gap:.25rem;
        min-width:70px;
    }
    .pt-card-value {
        font-size:12px;
        color:#374151;
        min-width:0;
    }
    .pt-card-desc {
        font-size:11px;
        color:#6b7280;
        margin-top:.25rem;
    }
    .pt-card-people {
        display:flex;
        align-items:center;
    }
    .pt-card-footer {
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-top:.4rem;
    }
    .pt-card-footer-meta {
        display:flex;
        flex-wrap:wrap;
        gap:.25rem;
    }

    .pt-pill {
        border-radius:999px;
        padding:.1rem .45rem;
        border:1px solid #e5e7eb;
        font-size:10px;
        background:#f9fafb;
        display:inline-flex;
        align-items:center;
        gap:.2rem;
    }
    .pt-pill-warn {
        border-color:#f97316;
        color:#c2410c;
        background:#ffedd5;
    }
    .pt-pill-light {
        background:#f3f4f6;
        border-color:#e5e7eb;
        color:#4b5563;
    }

    .pt-badge-accept {
        border-radius:999px;
        padding:.08rem .5rem;
        font-size:10px;
        border:1px solid #22c55e;
        background:#dcfce7;
        color:#166534;
        display:inline-flex;
        align-items:center;
        gap:.2rem;
    }
    .pt-badge-pending {
        border-radius:999px;
        padding:.08rem .5rem;
        font-size:10px;
        border:1px solid #f97373;
        background:#fee2e2;
        color:#b91c1c;
        display:inline-flex;
        align-items:center;
        gap:.2rem;
    }

    .pt-avatars {
        display:flex;
        align-items:center;
    }
    .pt-avatar {
        width:22px;
        height:22px;
        border-radius:999px;
        border:2px solid #fff;
        overflow:hidden;
        margin-left:-6px;
        background:#e5e7eb;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:11px;
        color:#374151;
    }
    .pt-avatar:first-child {margin-left:0;}

    .pt-card-actions {
        display:flex;
        gap:.25rem;
    }
    .pt-icon-btn {
        border:none;
        background:#f9fafb;
        border-radius:999px;
        padding:.2rem .4rem;
        font-size:11px;
        cursor:pointer;
        display:inline-flex;
        align-items:center;
        justify-content:center;
    }
    .pt-icon-btn:hover {
        background:#e5e7eb;
    }
    .pt-icon-xs {
        width:13px;
        height:13px;
    }

    /* List */
    .pt-table {
        width:100%;
        border-collapse:collapse;
        font-size:12px;
    }
    .pt-table thead {
        background:#f9fafb;
    }
    .pt-table th,
    .pt-table td {
        padding:.4rem .5rem;
        border-bottom:1px solid #e5e7eb;
        vertical-align:top;
    }
    .pt-table th {
        text-align:left;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#6b7280;
    }
    .pt-row-pending {
        background:#fef2f2;
    }
    .pt-icon-col {
        display:flex;
        flex-direction:column;
        gap:.25rem;
        align-items:center;
        justify-content:flex-start;
        padding-top:.35rem;
    }
    .pt-icon-col span {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:22px;
        height:22px;
        border-radius:999px;
        border:1px solid #e5e7eb;
        background:#fff;
    }
    .pt-badge-age {
        padding:.1rem .4rem;
        border-radius:999px;
        font-size:10px;
        border:1px solid #f97316;
        color:#c2410c;
        background:#ffedd5;
    }

    .pt-dropdown {
        position:relative;
        display:inline-block;
    }
    .pt-dropdown-menu {
        position:absolute;
        right:0;
        top:120%;
        background:#fff;
        border-radius:.6rem;
        border:1px solid #e5e7eb;
        min-width:180px;
        padding:.25rem 0;
        box-shadow:0 12px 30px rgba(15,23,42,0.08);
        display:none;
        z-index:30;
    }
    .pt-dropdown-menu button {
        width:100%;
        padding:.25rem .75rem;
        font-size:12px;
        border:none;
        background:transparent;
        text-align:left;
        cursor:pointer;
        display:flex;
        gap:.35rem;
        align-items:center;
    }
    .pt-dropdown-menu button:hover {
        background:#f3f4f6;
    }

    /* Modal */
    .pt-modal-backdrop {
        position:fixed;
        inset:0;
        background:rgba(15,23,42,.45);
        display:none;
        align-items:center;
        justify-content:center;
        z-index:40;
    }
    .pt-modal {
        background:#fff;
        border-radius:1rem;
        padding:1rem;
        width:100%;
        max-width:380px;
        box-shadow:0 20px 50px rgba(15,23,42,0.25);
        display:flex;
        flex-direction:column;
        gap:.5rem;
    }
    .pt-modal textarea {
        width:100%;
        min-height:90px;
        font-size:12px;
        padding:.5rem;
        border-radius:.75rem;
        border:1px solid #e5e7eb;
    }
    .pt-modal-footer {
        display:flex;
        justify-content:flex-end;
        gap:.5rem;
        margin-top:.5rem;
    }
    .btn-primary {
        background:#111827;
        color:#fff;
        border-radius:.75rem;
        border:none;
        padding:.25rem .8rem;
        font-size:12px;
        cursor:pointer;
    }
    .btn-secondary {
        background:#f3f4f6;
        color:#374151;
        border-radius:.75rem;
        border:none;
        padding:.25rem .7rem;
        font-size:12px;
        cursor:pointer;
    }
    .swal2-container {
        z-index: 3000 !important;
    }

    @keyframes ptHighlightPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(147,194,28,0.6);
            border-color: #93c21c;
            background-color: #fefce8;
        }
        50% {
            box-shadow: 0 0 0 8px rgba(147,194,28,0);
            background-color: #ffffff;
        }
        100% {
            box-shadow: 0 0 0 0 rgba(147,194,28,0);
            border-color: #e5e7eb;
        }
    }
 
    /* highlight newly created / updated task */
    .pt-highlight {
        position: relative;
        animation: ptHighlightPulse 1.2s ease-out 0s 3;
        box-shadow:
            0 0 0 2px #93c21c,
            0 0 0 10px rgba(147, 194, 28, 0.35);
    }

    @keyframes ptHighlightPulse {
        0% {
            transform: scale(1);
            box-shadow:
                0 0 0 0 rgba(147, 194, 28, 0.45),
                0 0 0 0 rgba(147, 194, 28, 0.0);
        }
        50% {
            transform: scale(1.02);
            box-shadow:
                0 0 0 4px rgba(147, 194, 28, 0.45),
                0 0 0 14px rgba(147, 194, 28, 0.15);
        }
        100% {
            transform: scale(1);
            box-shadow:
                0 0 0 0 rgba(147, 194, 28, 0.0),
                0 0 0 0 rgba(147, 194, 28, 0.0);
        }
    }

    /* make SweetAlert always above sidebar / slide-in */
    .swal2-container {
        z-index: 3000 !important;
    }


</style>
 
 
<style>
    .pt-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    padding: 0.6rem 0.9rem;
    border-radius: 9999px;
    background: #f9fafb;
    border: 1px solid #cfe09b;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
}

/* Text + selects */
.pt-filters input[type="text"],
.pt-filters select {
    min-width: 130px;
    border-radius: 9999px;
    border: 1px solid #cfe09b;
    padding: 0.35rem 0.9rem;
    font-size: 0.85rem;
    color: #374151;           /* dark gray text */
    background-color: #ffffff;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
}

/* Slightly smaller for the sort direction select */
.pt-filters select[name="dir"] {
    min-width: 70px;
    text-align: center;
}

.pt-filters input[type="text"]::placeholder {
    color: #9ca3af;
}

/* Hover + focus states */
.pt-filters input[type="text"]:hover,
.pt-filters select:hover {
    background-color: #fefefb;
    border-color: #93c21c;
}

.pt-filters input[type="text"]:focus,
.pt-filters select:focus {
    border-color: #93c21c;
    box-shadow: 0 0 0 2px rgba(147, 194, 28, 0.18);
}

/* Filter button */
.pt-filters .btn-secondary {
    border-radius: 9999px;
    border: none;
    padding: 0.4rem 1.2rem;
    font-size: 0.85rem;
    font-weight: 600;
    background: linear-gradient(135deg, #93c21c, #cfe09b);
    color: #1f2933;
    box-shadow: 0 12px 30px rgba(147, 194, 28, 0.35);
    cursor: pointer;
    transition: transform 0.12s ease, box-shadow 0.12s ease, filter 0.12s ease;
    white-space: nowrap;
}

.pt-filters .btn-secondary:hover {
    filter: brightness(0.96);
    transform: translateY(-1px);
    box-shadow: 0 16px 35px rgba(147, 194, 28, 0.45);
}

.pt-filters .btn-secondary:active {
    transform: translateY(0);
    box-shadow: 0 8px 18px rgba(147, 194, 28, 0.25);
}

/* Priority / color row tweaks */
.nt-priority-cell {
    text-align: right;
}

.nt-priority-wrapper {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}

.nt-priority-label {
    font-size: .75rem;
    color: #4b5563;
    white-space: nowrap;
}

/* Responsive: stack on small screens */
@media (max-width: 768px) {
    .pt-filters {
        border-radius: 18px;
    }
    .pt-filters input[type="text"],
    .pt-filters select,
    .pt-filters .btn-secondary {
        flex: 1 1 100%;
    }
}

</style>

<style>
    /* ---- Slide-in shell ---- */
    .new_task {
        position: fixed;
        top: 0;
        right: -100%;
        width: 1200px;
        max-width: 100%;
        height: 100vh;
        z-index: 1300;
        display: none;
        font-size: 13px;
        color: #111827;
    }

    .new_task_card {
        position: relative;
        height: 100%;
        background: #f9fafb;
        box-shadow: 0 25px 60px rgba(15,23,42,.32);
        border-radius: 1.25rem 0 0 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    /* Header */
    .nt-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .9rem 1.2rem;
        background: linear-gradient(135deg, #93c21c, #cfe09b);
        color: #0b1120;
    }

    .nt-header-left {
        display: flex;
        flex-direction: column;
        gap: .1rem;
    }

    .nt-header-title {
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    .nt-header-sub {
        font-size: .75rem;
        opacity: .9;
    }

    .nt-header-actions {
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    .nt-close-btn {
        border: none;
        border-radius: 999px;
        padding: .2rem .55rem;
        background: rgba(15,23,42,.12);
        color: #0b1120;
        font-size: .8rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        transition: background .15s ease, transform .15s ease;
    }

    .nt-close-btn:hover {
        background: rgba(15,23,42,.22);
        transform: translateY(-1px);
    }

    /* Body layout */
    .nt-body {
        flex: 1;
        overflow-y: auto;
        padding: 1rem 1.1rem 0.75rem;
        background: radial-gradient(circle at top left, #eef5d8 0, #f9fafb 50%, #f3f4f6 100%);
    }

    .nt-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(0, 1.1fr);
        gap: .9rem;
    }

    @media (max-width: 900px) {
        .nt-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    .nt-section {
        background: #ffffff;
        border-radius: 1rem;
        padding: .75rem .85rem;
        box-shadow: 0 14px 35px rgba(15,23,42,.08);
        border: 1px solid rgba(209,213,219,.7);
        margin-bottom: .7rem;
    }

    .nt-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .5rem;
    }

    .nt-section-title {
        font-size: .8rem;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #6b7280;
    }

    .nt-section-badge {
        font-size: .7rem;
        padding: .1rem .5rem;
        border-radius: 999px;
        background: #ecfccb;
        color: #3f6212;
        border: 1px solid #a3e635;
    }

    .nt-field-label {
        font-size: .8rem;
        font-weight: 500;
        margin-bottom: .15rem;
        color: #374151;
    }

    .nt-input,
    .nt-textarea,
    .nt-select {
        width: 100%;
        border-radius: .7rem;
        border: 1px solid #d1d5db;
        font-size: .8rem;
        padding: .4rem .6rem;
        background: #f9fafb;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
    }

    .nt-textarea {
        resize: vertical;
        min-height: 60px;
    }

    .nt-input:focus,
    .nt-textarea:focus,
    .nt-select:focus {
        background: #ffffff;
        border-color: #93c21c;
        box-shadow: 0 0 0 1px rgba(147,194,28,.4);
    }

    .nt-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .5rem;
        margin-bottom: .4rem;
    }

    .nt-row-4 {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .5rem;
        margin-bottom: .4rem;
    }

    @media (max-width: 768px) {
        .nt-row, .nt-row-4 {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    /* Switch line */
    .nt-switch-row {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: .75rem;
        flex-wrap: wrap;
        margin-top: .25rem;
    }

    .nt-switch-label {
        font-size: .75rem;
        color: #4b5563;
        display: flex;
        align-items: center;
        gap: .25rem;
    }

    /* Side settings */
    .nt-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .nt-chip {
        border-radius: 999px;
        padding: .18rem .55rem;
        font-size: .74rem;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        color: #4b5563;
    }

    .nt-chip strong {
        font-weight: 600;
    }

    /* Settings table keep, but restyle rows */
    #accordionWrapa1 .table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
        font-size: .8rem;
    }

    #accordionWrapa1 .table tr {
        border-bottom: 1px solid #e5e7eb;
    }

    #accordionWrapa1 .table td {
        padding: .35rem .3rem;
        vertical-align: middle;
    }

    #accordionWrapa1 .table tr:first-child td {
        border-top: none;
    }

    #accordionWrapa1 .table tr:nth-child(odd) {
        background: #f9fafb;
    }

    /* Color + toggles row */
    .nt-top-right-row {
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
        gap: .6rem;
        flex-wrap: wrap;
        margin-top: .35rem;
    }

    .nt-inline-toggle {
        font-size: .7rem;
        display: flex;
        flex-direction: column;
        gap: .1rem;
        align-items: flex-start;
    }

    .nt-inline-toggle p {
        margin-bottom: 0;
        font-size: .75rem;
        color: #4b5563;
    }

    /* Task keys section */
    #key_task th,
    #key_task td {
        font-size: .75rem;
        vertical-align: top;
    }

    #key_task thead th {
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #6b7280;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    #key_task .task-duration {
        font-size: .75rem;
    }

    #key_total_time {
        font-size: .7rem;
        color: #4b5563;
    }

    /* Footer buttons */
    .nt-footer {
        padding: .6rem .9rem .8rem;
        border-top: 1px solid rgba(209,213,219,.8);
        background: #f9fafb;
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
    }

    .nt-btn {
        border-radius: 999px;
        padding: .32rem .9rem;
        font-size: .8rem;
        display: inline-flex;
        align-items: center;
        gap: .28rem;
        border: none;
        cursor: pointer;
        transition: box-shadow .12s ease, transform .12s ease, background-color .12s ease;
    }

    .nt-btn-primary {
        background: linear-gradient(135deg, #111827, #1f2937);
        color: #f9fafb;
        box-shadow: 0 12px 30px rgba(15,23,42,.35);
    }

    .nt-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 16px 40px rgba(15,23,42,.45);
    }

    .nt-btn-ghost {
        background: transparent;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }

    .nt-btn-ghost:hover {
        background: #e5e7eb;
    }

    .nt-btn-danger {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .nt-btn-danger:hover {
        background: #fecaca;
    }

    .nt-btn i {
        font-size: .9rem;
    }
</style>

@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">ALLGEMEINE AUFGABEN</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        
        <div class="content-body">
            <div class="pt-layout">
                @include('admin.todo.personal.simple_stats_cards', ['stats' => $stats ?? null])

                {{-- Header --}}
                <div class="pt-header">
                    <div class="pt-tabs">
                        @php
                            $tab  = $tab  ?? request('tab', 'my');
                            $view = $view ?? request('view', 'board');

                            $filters = $filters ?? [
                                'search'   => request('search'),
                                'status'   => request('status'),
                                'priority' => request('priority'),
                                'public'   => request('public'),
                                'isReport' => request('is_report'),
                                'sort'     => request('sort', 'created_at'),
                                'dir'      => request('dir', 'desc'),
                            ];

                            $tabs = [
                                'my'        => 'Meine Jobs',
                                'created'   => 'Erstellt von mir',
                                'completed' => 'Erledigt',
                                'paused'    => 'Pausiert',
                                'rejected'  => 'Abgelehnt',
                                'archived'  => 'Archiv',
                            ];
                        @endphp

                        @foreach($tabs as $key => $label)
                            <a href="{{ route('personal-tasks.index', array_merge(request()->query(), ['tab' => $key])) }}"
                            class="pt-tab {{ $tab === $key ? 'is-active' : '' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <div style="display:flex; gap:.5rem; align-items:center;">
                        <form method="GET" action="{{ route('personal-tasks.index') }}" class="pt-filters">
                            <input type="hidden" name="tab" value="{{ $tab }}">
                            <input type="hidden" name="view" value="{{ $view }}">

                            <input type="text" name="search" placeholder="Suche..."
                                value="{{ $filters['search'] ?? '' }}">

                            <select name="priority">
                                <option value="">Priorität</option>
                                <option value="low"    @selected(($filters['priority'] ?? '')==='low')>Niedrig</option>
                                <option value="normal" @selected(($filters['priority'] ?? '')==='normal')>Normal</option>
                                <option value="high"   @selected(($filters['priority'] ?? '')==='high')>Hoch</option>
                                <option value="urgent" @selected(($filters['priority'] ?? '')==='urgent')>Dringend</option>
                            </select>

                            <select name="public">
                                <option value="">Sichtbarkeit</option>
                                <option value="1" @selected(($filters['public'] ?? '') === '1')>Öffentlich</option>
                                <option value="0" @selected(($filters['public'] ?? '') === '0')>Privat</option>
                            </select>

                            <select name="is_report">
                                <option value="">Report?</option>
                                <option value="1" @selected(($filters['isReport'] ?? '') === '1')>Ja</option>
                                <option value="0" @selected(($filters['isReport'] ?? '') === '0')>Nein</option>
                            </select>

                            <select name="sort">
                                <option value="created_at" @selected(($filters['sort'] ?? '')==='created_at')>Erstellt</option>
                                <option value="due_date"   @selected(($filters['sort'] ?? '')==='due_date')>Fällig</option>
                                <option value="priority"   @selected(($filters['sort'] ?? '')==='priority')>Priorität</option>
                                <option value="task_title" @selected(($filters['sort'] ?? '')==='task_title')>Titel</option>
                            </select>

                            <select name="dir">
                                <option value="desc" @selected(($filters['dir'] ?? '')==='desc')>↓</option>
                                <option value="asc"  @selected(($filters['dir'] ?? '')==='asc')>↑</option>
                            </select>

                            <button type="submit" class="btn-secondary">Filtern</button>
                        </form>

                        <div class="pt-view-toggle">
                            <a href="{{ route('personal-tasks.index', array_merge(request()->query(), ['view' => 'board'])) }}">
                                <button type="button" class="{{ $view === 'board' ? 'is-active' : '' }}">Board</button>
                            </a>
                            <a href="{{ route('personal-tasks.index', array_merge(request()->query(), ['view' => 'list'])) }}">
                                <button type="button" class="{{ $view === 'list' ? 'is-active' : '' }}">Liste</button>
                            </a>
                        </div>

                        <button type="button"
                                class="btn-primary create_new_task"
                                style="
                                    display:inline-flex;
                                    align-items:center;
                                    gap:.35rem;
                                    border-radius:9999px;
                                    border:none;
                                    padding:.45rem 1rem;
                                    font-size:.85rem;
                                    font-weight:600;
                                    background:linear-gradient(135deg,#93c21c,#cfe09b);
                                    color:#111827;
                                    box-shadow:0 10px 25px rgba(147,194,28,.35);
                                    cursor:pointer;
                                    white-space:nowrap;
                                ">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            Neue Aufgabe
                        </button>
                    </div>
                </div>

                {{-- Board view --}}
                @if($view === 'board')
                    @php
                        if ($tab === 'rejected') {
                            $columnMeta = [
                                'rejected' => ['label' => 'Abgelehnt', 'color' => '#ef4444'],
                            ];
                        } elseif ($tab === 'archived') {
                            $columnMeta = [
                                'archived' => ['label' => 'Archiv', 'color' => '#9ca3af'],
                            ];
                        } else {
                            $columnMeta = [
                                'open'        => ['label' => 'Offen',     'color' => '#93c21c'],
                                'in_progress' => ['label' => 'In Arbeit', 'color' => '#93c21c'],
                                'completed'   => ['label' => 'Erledigt',  'color' => '#74b2d4'],
                            ];
                        }
                    @endphp

                    <div class="pt-board" id="pt-board"
                        data-status-url="{{ route('personal-tasks.status', ['task' => '__ID__']) }}">
                        @foreach($columnMeta as $key => $meta)
                            @php
                                /** @var \Illuminate\Support\Collection $items */
                                $items = $columns[$key] ?? collect();
                            @endphp

                            <div class="pt-column" data-column="{{ $key }}">
                                <div class="pt-column-header">
                                    <span>{{ $meta['label'] }}</span>
                                    <span style="width:8px;height:8px;border-radius:999px;background:{{ $meta['color'] }};"></span>
                                </div>
                                <div class="pt-column-body">
                                    @foreach($items as $task)
                                        @include('admin.todo.personal._task_card', [
                                            'task'       => $task,
                                            'employeeId' => $employeeId,
                                        ])
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- List view --}}
                @if($view === 'list')
                    <div style="overflow-x:auto;">
                        <table class="pt-table">
                            <thead>
                            <tr>
                                <th style="width:60px;">Info</th>
                                <th>Job</th>
                                <th>Ersteller / Status</th>
                                <th>Controller</th>
                                <th>Mitarbeiter</th>
                                <th>Kunde / Objekt</th>
                                <th>Fällig / Zeiten</th>
                                <th>Aktion</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tasks as $task)
                                @php
                                    $ageHours   = \Carbon\Carbon::now()->diffInHours($task->created_at);
                                    $isOlder48  = $ageHours >= 48;

                                    $myPivot    = $employeeId
                                        ? $task->employees->firstWhere('id', $employeeId)
                                        : null;

                                    $myStatus   = $myPivot && $myPivot->pivot ? $myPivot->pivot->status : null;
                                    $rowPending = $myPivot && $myStatus !== 'accepted';

                                    $rejectedEmployees = $task->employees->filter(function ($e) {
                                        return $e->pivot && $e->pivot->status === 'rejected';
                                    });
                                @endphp

                                <tr data-task-id="{{ $task->id }}" class="{{ $rowPending ? 'pt-row-pending' : '' }}">
                                    {{-- icon column --}}
                                    <td>
                                        <div class="pt-icon-col">
                                            <span title="{{ $task->public ? 'Öffentlich' : 'Privat' }}">
                                                <i data-feather="{{ $task->public ? 'unlock' : 'lock' }}" class="pt-icon-xs"></i>
                                            </span>
                                            <span title="Priorität: {{ $task->priority ?? 'Normal' }}">
                                                <i data-feather="flag" class="pt-icon-xs"></i>
                                            </span>
                                            @if($isOlder48)
                                                <span title="> 48 Std. offen">
                                                    <i data-feather="clock" class="pt-icon-xs"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- job title + desc --}}
                                    <td>
                                        <div style="display:flex;align-items:flex-start;gap:.4rem;">
                                            <input type="color"
                                                class="js-task-color"
                                                value="{{ $task->color ?? '#0f172a' }}"
                                                data-task-id="{{ $task->id }}"
                                                style="width:18px;height:18px;border:none;padding:0;margin-top:2px;">
                                            <div>
                                                <div style="font-weight:600;font-size:13px;">
                                                    {{ $task->task_title ?? 'Ohne Titel' }}
                                                </div>
                                                <div style="font-size:11px;color:#6b7280;">
                                                    {{ \Illuminate\Support\Str::limit($task->description, 90) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- creator + accepted badge + rejection reasons --}}
                                    <td>
                                        <div style="display:flex;flex-direction:column;gap:.2rem;">
                                            @if($task->assignedBy)
                                                <span class="pt-pill pt-pill-light">
                                                    <i data-feather="edit-3" class="pt-icon-xs"></i>
                                                    {{ $task->assignedBy->name }} {{ $task->assignedBy->lastname }}
                                                </span>
                                            @endif

                                            @if($myStatus === 'accepted')
                                                <span class="pt-badge-accept">
                                                    <i data-feather="check-circle" class="pt-icon-xs"></i>
                                                    Job akzeptiert
                                                </span>
                                            @elseif($myStatus === 'rejected')
                                                <span class="pt-badge-pending">
                                                    <i data-feather="x-circle" class="pt-icon-xs"></i>
                                                    Job von dir abgelehnt
                                                </span>
                                            @elseif($myPivot)
                                                <span class="pt-badge-pending">
                                                    <i data-feather="alert-triangle" class="pt-icon-xs"></i>
                                                    Noch nicht akzeptiert
                                                </span>
                                            @endif

                                            <span class="pt-pill pt-pill-light">
                                                <i data-feather="clock" class="pt-icon-xs"></i>
                                                {{ $task->created_at->format('d.m.Y H:i') }}
                                            </span>
                                        </div>

                                        @if($tab === 'rejected' && $rejectedEmployees->count())
                                            <div style="margin-top:.35rem;font-size:11px;color:#b91c1c;">
                                                @foreach($rejectedEmployees as $re)
                                                    <div>
                                                        <strong>{{ $re->name }} {{ $re->lastname }}:</strong>
                                                        {{ $re->pivot->reason ?? $re->pivot->change_reason ?? 'kein Grund angegeben' }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>

                                    {{-- controllers --}}
                                    <td>
                                        @if(method_exists($task, 'controllers') && $task->controllers && $task->controllers->count())
                                            <div class="pt-avatars">
                                                @foreach($task->controllers as $ctrl)
                                                    <div class="pt-avatar" title="{{ $ctrl->name }} {{ $ctrl->lastname }}">
                                                        @if($ctrl->image)
                                                            <img src="{{ asset('images/employee/'.$ctrl->image) }}" alt=""
                                                                style="width:100%;height:100%;object-fit:cover;">
                                                        @else
                                                            {{ mb_substr($ctrl->name,0,1) }}{{ mb_substr($ctrl->lastname,0,1) }}
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span style="font-size:11px;color:#9ca3af;">—</span>
                                        @endif
                                    </td>

                                    {{-- employees --}}
                                    <td>
                                        @if($task->employees->count())
                                            <div class="pt-avatars">
                                                @foreach($task->employees as $emp)
                                                    <div class="pt-avatar" title="{{ $emp->name }} {{ $emp->lastname }}">
                                                        @if($emp->image)
                                                            <img src="{{ asset('images/employee/'.$emp->image) }}" alt=""
                                                                style="width:100%;height:100%;object-fit:cover;">
                                                        @else
                                                            {{ mb_substr($emp->name,0,1) }}{{ mb_substr($emp->lastname,0,1) }}
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span style="font-size:11px;color:#9ca3af;">—</span>
                                        @endif
                                    </td>

                                    {{-- customer --}}
                                    <td>
                                        @if($task->customer)
                                            <div style="font-size:12px;">
                                                {{ $task->customer->customer_no }} /
                                                {{ $task->customer->lastname }} {{ $task->customer->name }}
                                            </div>
                                            <div style="font-size:11px;color:#6b7280;">
                                                {{ $task->customer->city }}
                                            </div>
                                        @else
                                            <span style="font-size:11px;color:#9ca3af;">kein Kunde</span>
                                        @endif
                                    </td>

                                    {{-- due + info --}}
                                    <td>
                                        <div style="font-size:12px;">
                                            @if($task->due_date)
                                                {{ $task->due_date->format('d.m.Y') }}
                                                @if($task->due_time)
                                                    {{ $task->due_time }}
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </div>
                                        <div style="margin-top:.25rem;display:flex;flex-direction:column;gap:.15rem;">
                                            <span class="pt-pill">Status: {{ $task->task_status ?? 'offen' }}</span>
                                            <span class="pt-pill">Prio: {{ $task->priority ?? 'Normal' }}</span>
                                            @if(!empty($task->is_report))
                                                <span class="pt-pill">
                                                    <i data-feather="clipboard" class="pt-icon-xs"></i>
                                                    Report
                                                </span>
                                            @endif
                                            @if($isOlder48)
                                                <span class="pt-badge-age">> 48 Std. offen</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- actions --}}
                                    <td>
                                        <div class="pt-dropdown">
                                            <button type="button" class="pt-icon-btn js-dropdown-toggle">
                                                <i data-feather="more-horizontal" class="pt-icon-xs"></i>
                                            </button>
                                            <div class="pt-dropdown-menu" style="display:none;">
                                                <button type="button" class="js-status-btn" data-status="on_progress">
                                                    <i data-feather="play" class="pt-icon-xs"></i> Starten / Fortsetzen
                                                </button>
                                                <button type="button" class="js-status-btn" data-status="pause">
                                                    <i data-feather="pause" class="pt-icon-xs"></i> Pausieren
                                                </button>
                                                <button type="button" class="js-status-btn" data-status="completed">
                                                    <i data-feather="check" class="pt-icon-xs"></i> Abschließen
                                                </button>
                                                <button type="button" class="js-status-btn" data-status="cancel">
                                                    <i data-feather="x" class="pt-icon-xs"></i> Abbrechen
                                                </button>
                                                <button type="button" class="js-archive-btn">
                                                    <i data-feather="archive" class="pt-icon-xs"></i>
                                                    {{ $task->archived_at ? 'Aus Archiv holen' : 'Archivieren' }}
                                                </button>
                                                <button type="button" class="js-accept-btn">
                                                    <i data-feather="thumbs-up" class="pt-icon-xs"></i> Job akzeptieren
                                                </button>
                                                <button type="button" class="js-open-reject-modal">
                                                    <i data-feather="x-circle" class="pt-icon-xs"></i> Job ablehnen
                                                </button>
                                                <button type="button"
                                                        class="js-edit-task-btn"
                                                        data-edit-url="{{ route('personal.task.edit', $task->id) }}">
                                                    <i data-feather="edit" class="pt-icon-xs"></i> Bearbeiten
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top:.5rem;">
                        {{ $tasks->links() }}
                    </div>
                @endif
            </div>


            {{-- Reject modal --}}
            <div class="pt-modal-backdrop" id="pt-reject-backdrop">
                <div class="pt-modal">
                    <div style="font-weight:600;font-size:14px;">Job ablehnen</div>
                    <div style="font-size:12px;color:#6b7280;">
                        Bitte gib den Grund für die Ablehnung an. Dieser Grund wird im Reiter
                        <strong>Abgelehnt</strong> sichtbar sein.
                    </div>
                    <textarea id="pt-reject-reason" placeholder="Begründung..."></textarea>
                    <div class="pt-modal-footer">
                        <button class="btn-secondary" type="button" id="pt-reject-cancel">Abbrechen</button>
                        <button class="btn-primary"  type="button" id="pt-reject-save">Ablehnen</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="new_task">
    <div class="card new_task_card">
        {{-- Header --}}
        <div class="nt-header">
            <div class="nt-header-left">
                <div class="nt-header-title">
                    ALLGEMEINE AUFGABE
                </div>
                <div class="nt-header-sub">
                    Neue persönliche Aufgabe mit Kunde, Verantwortlichen und Schritten anlegen.
                </div>
            </div>
            <div class="nt-header-actions">
                <button type="button" class="nt-close-btn close_task_window">
                    <i class="feather icon-x"></i>
                    Schließen
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body p-0 nt-body">
            <form id="task_form">
                @csrf

                <div class="nt-grid">
                    {{-- LEFT --}}
                    <div>
                        {{-- Grunddaten --}}
                        <div class="nt-section">
                            <div class="nt-section-header">
                                <div class="nt-section-title">Grunddaten</div>
                                <span class="nt-section-badge">
                                    <i class="feather icon-clipboard"></i> Aufgabe
                                </span>
                            </div>

                            <div class="nt-row">
                                <div>
                                    <label class="nt-field-label" for="task_title">Aufgabentitel</label>
                                    <input type="text" id="task_title" name="task_title" class="nt-input">
                                </div>
                                <div>
                                    <label class="nt-field-label">Farbe & Sichtbarkeit</label>
                                    <div class="nt-top-right-row">
                                        {{-- Hidden color --}}
                                        <input type="hidden" name="color" id="color" value="#8fc73e">

                                        {{-- Color dropdown (IDs kept) --}}
                                        <div class="btn-group dropup dropdown-icon-wrapper" id="color_drop_down">
                                            <button type="button"
                                                    class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split"
                                                    data-toggle="dropdown"
                                                    aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="fa fa-square" id="colorIcon" style="color:#8fc73e;"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @php
                                                    $colors = [
                                                        '#8fc73e' => 'Grün',
                                                        '#ff0000' => 'Rot',
                                                        '#0000ff' => 'Blau',
                                                        '#ffff00' => 'Gelb',
                                                        '#ff00ff' => 'Magenta',
                                                        '#00ffff' => 'Cyan',
                                                        '#000000' => 'Schwarz',
                                                        '#ffffff' => 'Weiß',
                                                        '#808080' => 'Grau',
                                                        '#ffa500' => 'Orange',
                                                        '#800080' => 'Lila',
                                                        '#8b4513' => 'Braun',
                                                        '#4682b4' => 'Stahlblau',
                                                        '#5f9ea0' => 'Kadettenblau',
                                                        '#d2691e' => 'Schokoladenbraun',
                                                        '#2e8b57' => 'Seegrün',
                                                        '#dc143c' => 'Karmesinrot',
                                                        '#7fffd4' => 'Aquamarin',
                                                        '#9932cc' => 'Dunkles Lila',
                                                        '#ff6347' => 'Tomate',
                                                    ];
                                                @endphp
                                                @foreach($colors as $hex => $label)
                                                    <span class="dropdown-item" data-value="{{ $hex }}">
                                                        <i class="fa fa-square"
                                                           style="color:{{ $hex }};@if($hex==='#ffffff')border:1px solid #ccc;@endif"></i>
                                                        {{ $label }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Öffentlich --}}
                                        <div class="nt-inline-toggle">
                                            <p class="mb-0">Öffentlich</p>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       id="customSwitch10"
                                                       name="public"
                                                       checked>
                                                <label class="custom-control-label" for="customSwitch10"></label>
                                            </div>
                                        </div>

                                        {{-- Kunde --}}
                                        <div class="nt-inline-toggle">
                                            <p class="mb-0">Kunde</p>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       id="customerSwitch"
                                                       name="is_customer"
                                                       value="0">
                                                <label class="custom-control-label" for="customerSwitch"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="nt-field-label" for="description">Beschreibung</label>
                                <textarea name="description"
                                          id="description"
                                          class="nt-textarea"
                                          rows="2"></textarea>
                            </div>
                        </div>

                        {{-- Kunde & Objekt --}}
                        <div class="nt-section">
                            <div class="nt-section-header">
                                <div class="nt-section-title">Kunde & Objekt</div>
                                <span class="nt-section-badge">
                                    <i class="feather icon-user-check"></i> Optional
                                </span>
                            </div>

                            <div id="customerSelectContainer" style="display:none;">
                                <label class="nt-field-label" for="customerLeadProductSelect">
                                    Wähle Kunde & Objekt
                                </label>
                                <select class="nt-select"
                                        id="customerLeadProductSelect"
                                        name="customer_id"
                                        style="width: 100%;">
                                    <option value="">Auswählen...</option>
                                </select>
                                <input type="hidden" name="alternative_id" id="select_alternative_id">
                                <input type="hidden" name="product_id"     id="select_product_id">
                            </div>
                        </div>

                        {{-- Team --}}
                        <div class="nt-section">
                            <div class="nt-section-header">
                                <div class="nt-section-title">Team</div>
                                <span class="nt-section-badge">
                                    <i class="feather icon-users"></i> Zuweisung
                                </span>
                            </div>

                            <div class="nt-row">
                                <div id="task_employee_section">
                                    <label class="nt-field-label" for="employee">Zugewiesen an</label>
                                    <select name="employee[]" id="employee" class="employee nt-select" multiple style="width:100%">
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}"
                                                    data-image="{{ asset('images/employee/'.$emp->image) }}"
                                                    data-checked="false">
                                                {{ $emp->name }} {{ $emp->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="nt-field-label" for="controller">Kontroller</label>
                                    <select name="controller[]" id="controller" class="employee nt-select" multiple style="width:100%">
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}"
                                                    data-image="{{ asset('images/employee/'.$emp->image) }}"
                                                    data-checked="false">
                                                {{ $emp->name }} {{ $emp->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Aufgabenschritte --}}
                        <div class="nt-section">
                            <div class="nt-section-header"
                                 data-toggle="collapse"
                                 data-target="#collapseTaskKeys"
                                 aria-expanded="false"
                                 aria-controls="collapseTaskKeys"
                                 style="cursor:pointer;">
                                <div class="nt-section-title">
                                    <i class="feather icon-list"></i> Aufgabenschritte
                                </div>
                                <span class="nt-section-badge">
                                    <i class="feather icon-chevron-down"></i> Details
                                </span>
                            </div>

                            <div id="collapseTaskKeys" class="collapse">
                                <div class="table-responsive">
                                    <table class="table" id="key_task">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Aufgabenschritte</th>
                                            <th style="width: 90px;">
                                                Dauer
                                                <br>
                                                <small><code id="key_total_time">0 Stunden</code></small>
                                            </th>
                                            <th style="width: 135px;">Zugewiesen</th>
                                            <th>Beschreibung</th>
                                            <th style="width: 80px;">Aktion</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <input type="text"
                                                       name="key[0][task]"
                                                       class="form-control form-control-sm">
                                            </td>
                                            <td>
                                                <input type="number"
                                                       name="key[0][duration]"
                                                       class="form-control form-control-sm task-duration">
                                            </td>
                                            <td>
                                                <select name="key[0][employee_id][]"
                                                        class="form-control form-control-sm employee-select"
                                                        multiple
                                                        style="width:100%">
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}"
                                                                data-image="{{ asset('images/employee/'.$employee->image) }}">
                                                            {{ $employee->name }} {{ $employee->lastname }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <textarea name="key[0][key_description]"
                                                          class="form-control form-control-sm"></textarea>
                                            </td>
                                            <td>
                                                <button type="button"
                                                        class="btn btn-sm btn-primary add-task-steps">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger remove-task-steps">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div>
                        {{-- Einstellungen --}}
                        <div class="nt-section">
                            <div class="nt-section-header">
                                <div class="nt-section-title">
                                    <i class="feather icon-settings"></i> Einstellungen
                                </div>
                            </div>

                            <div id="accordionWrapa1" role="tablist" aria-multiselectable="true">
                                <div class="accordion" id="nt-settings-accordion">
                                    <div class="card mb-0" style="border:none;background:transparent;">
                                        <div class="card-content">
                                            <div class="card-body p-0">
                                                <div class="accordion-default collapse-bordered">
                                                    <div class="card collapse-header" style="border:none;">
                                                        <div id="heading1"
                                                             class="card-header collapse-header p-0"
                                                             data-toggle="collapse"
                                                             role="button"
                                                             data-target="#accordion1"
                                                             aria-expanded="true"
                                                             aria-controls="accordion1"
                                                             style="background:transparent;border:none;">
                                                            <span class="lead collapse-title">
                                                                <span class="nt-chip">
                                                                    <i class="feather icon-settings"></i>
                                                                    Wiederholung, Erinnerung & Prio
                                                                </span>
                                                            </span>
                                                        </div>

                                                        <div id="accordion1"
                                                             role="tabpanel"
                                                             data-parent="#accordionWrapa1"
                                                             aria-labelledby="heading1"
                                                             class="collapse show">
                                                            <div class="card-content">
                                                                <div class="card-body p-0 mt-1">
                                                                    <table class="table mb-0">
                                                                        {{-- Wiederholung --}}
                                                                        <tr>
                                                                            <td>
                                                                                <i class="feather icon-refresh-cw"></i>
                                                                                Wiederholung
                                                                            </td>
                                                                            <td class="text-right">
                                                                                <div class="custom-control custom-switch mr-2 mb-0">
                                                                                    <input type="checkbox"
                                                                                           class="custom-control-input"
                                                                                           id="repeated"
                                                                                           name="repeated">
                                                                                    <label class="custom-control-label" for="repeated"></label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>

                                                                        <tr class="repeated_area">
                                                                            <td colspan="2">
                                                                                <select name="repeat" class="form-control form-control-sm" id="wiederholung">
                                                                                    <option value="">Häufigkeit auswählen</option>
                                                                                    <option value="minute">Minütlich</option>
                                                                                    <option value="hourly">Stündlich</option>
                                                                                    <option value="daily">Täglich</option>
                                                                                    <option value="weekly">Wöchentlich</option>
                                                                                    <option value="monthly">Monatlich</option>
                                                                                    <option value="quarterly">Vierteljährlich</option>
                                                                                    <option value="yearly">Jährlich</option>
                                                                                </select>
                                                                            </td>
                                                                        </tr>

                                                                        {{-- Erinnerung --}}
                                                                        <tr>
                                                                            <td>
                                                                                <i class="fa fa-clock-o"></i>
                                                                                Erinnerung
                                                                            </td>
                                                                            <td class="text-right">
                                                                                <div class="custom-control custom-switch mr-2 mb-0">
                                                                                    <input type="checkbox"
                                                                                           class="custom-control-input"
                                                                                           id="reminder_check"
                                                                                           name="reminder_check">
                                                                                    <label class="custom-control-label" for="reminder_check"></label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>

                                                                        <tr class="reminder_area">
                                                                            <td colspan="2">
                                                                                <label class="nt-field-label mb-0">Datum</label>
                                                                                <input type="date" name="reminder_date" class="form-control form-control-sm">
                                                                                <label class="nt-field-label mt-1 mb-0">Zeit</label>
                                                                                <input type="time" name="reminder_time" class="form-control form-control-sm">
                                                                            </td>
                                                                        </tr>

                                                                        {{-- Priorität (nicer layout) --}}
                                                                        <tr>
                                                                            <td>
                                                                                <i class="feather icon-flag"></i>
                                                                                Priorität
                                                                            </td>
                                                                            <td class="nt-priority-cell">
                                                                                <input type="hidden" name="priority" value="normal">
                                                                                <div class="nt-priority-wrapper" id="priority_select">
                                                                                    <span class="nt-priority-label">Standard</span>
                                                                                    <div class="btn-group dropup dropdown-icon-wrapper">
                                                                                        <button type="button"
                                                                                                class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                                                                                                data-toggle="dropdown"
                                                                                                aria-haspopup="true"
                                                                                                aria-expanded="false">
                                                                                            <i class="fa fa-battery-empty"></i>
                                                                                        </button>
                                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                                            <span class="dropdown-item" data-value="normal">
                                                                                                <i class="fa fa-battery-empty"></i> Keiner
                                                                                            </span>
                                                                                            <span class="dropdown-item" data-value="medium">
                                                                                                <i class="fa fa-battery-half"></i> Medium
                                                                                            </span>
                                                                                            <span class="dropdown-item" data-value="high">
                                                                                                <i class="fa fa-battery-full"></i> Hoch
                                                                                            </span>
                                                                                            <span class="dropdown-item" data-value="very high">
                                                                                                <i class="fa fa-fire warning"></i> Sehr wichtig
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div> {{-- /accordion1 --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> {{-- /accordion --}}
                            </div>
                        </div>

                        {{-- Zeitplanung --}}
                        <div class="nt-section">
                            <div class="nt-section-header">
                                <div class="nt-section-title">
                                    <i class="feather icon-calendar"></i> Zeitplanung
                                </div>
                            </div>

                            <div class="nt-row-4">
                                <div>
                                    <label class="nt-field-label" for="due_date">Fälligkeitsdatum</label>
                                    <input type="date" id="due_date" class="nt-input" name="due_date">
                                    <input type="hidden" name="same_id" value="same">
                                    <input type="hidden"
                                           id="start_date"
                                           name="start_date"
                                           class="nt-input"
                                           value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                </div>
                                <div>
                                    <label class="nt-field-label" for="due_time">Fälligkeitsuhrzeit</label>
                                    <input type="time" id="due_time" class="nt-input" name="due_time">
                                </div>
                                <div>
                                    <label class="nt-field-label" for="total_day">Gesamt Tage</label>
                                    <input type="number" id="total_day" class="nt-input" name="total_day">
                                </div>
                                <div>
                                    <label class="nt-field-label" for="total_time">Gesamtstunden</label>
                                    <input type="number" id="total_time" class="nt-input" name="total_time">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="nt-footer modal-footer">
                    <button type="button"
                            class="nt-btn nt-btn-danger close_task_window">
                        <i class="feather icon-x"></i>
                        Abbrechen
                    </button>

                    <button type="button"
                            class="nt-btn nt-btn-primary save-task-close">
                        <i class="feather icon-save"></i>
                        Speichern & schließen
                    </button>

                    <button type="button"
                            class="nt-btn nt-btn-primary save-task-continue">
                        <i class="feather icon-save"></i>
                        Speichern & weiter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
 
@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let rejectTaskId   = null;
    let rejectBackdrop = null;
    let rejectReason   = null;
    let rejectCancel   = null;
    let rejectSave     = null;

    document.addEventListener('DOMContentLoaded', () => {
        if (window.feather) {
            feather.replace();
        }

        cacheRejectModalElements();
        initDropdowns();
        initDragDrop();
        initStatusButtons();
        initListActions();
        initColorChangeListener();
        highlightNewTask();
    });

    function cacheRejectModalElements() {
        rejectBackdrop = document.getElementById('pt-reject-backdrop');
        rejectReason   = document.getElementById('pt-reject-reason');
        rejectCancel   = document.getElementById('pt-reject-cancel');
        rejectSave     = document.getElementById('pt-reject-save');

        if (rejectCancel && rejectBackdrop) {
            rejectCancel.addEventListener('click', () => {
                rejectBackdrop.style.display = 'none';
                rejectTaskId = null;
            });
        }

        if (rejectSave && rejectBackdrop) {
            rejectSave.addEventListener('click', () => {
                if (!rejectTaskId) return;
                const reason = (rejectReason && rejectReason.value ? rejectReason.value : '').trim();
                if (!reason) {
                    alert('Bitte einen Grund angeben.');
                    return;
                }

                const urlTpl = "{{ route('personal-tasks.reject', ['task' => '__ID__']) }}";
                const url    = urlTpl.replace('__ID__', rejectTaskId);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept'      : 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ reason })
                })
                    .then(() => {
                        rejectBackdrop.style.display = 'none';
                        rejectTaskId = null;
                        location.reload();
                    })
                    .catch(err => console.error('Reject error', err));
            });
        }
    }

    function highlightNewTask() {
        const url         = new URL(window.location.href);
        const highlightId = url.searchParams.get('highlight');
        if (!highlightId) return;

        const card = document.querySelector('.pt-card[data-task-id="' + highlightId + '"]');
        const row  = document.querySelector('tr[data-task-id="' + highlightId + '"]');
        const el   = card || row;

        if (el) {
            el.classList.add('pt-highlight');
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        url.searchParams.delete('highlight');
        window.history.replaceState({}, '', url.toString());
    }

    function getTaskIdFromElement(el) {
        if (!el) return null;
        const tr   = el.closest('tr');
        const card = el.closest('.pt-card');
        return tr ? tr.dataset.taskId : (card ? card.dataset.taskId : null);
    }

    // -----------------------------
    // Dropdown (list actions menu)
    // -----------------------------
    function initDropdowns() {
        document.addEventListener('click', function (e) {
            const clickedDropdown = e.target.closest('.pt-dropdown');
            const toggle          = e.target.closest('.js-dropdown-toggle');

            // Close all other dropdowns
            document.querySelectorAll('.pt-dropdown-menu').forEach(menu => {
                const wrapper = menu.closest('.pt-dropdown');
                if (!wrapper || wrapper !== clickedDropdown) {
                    menu.style.display = 'none';
                }
            });

            // Toggle current dropdown
            if (toggle && clickedDropdown) {
                e.preventDefault();
                const menu  = clickedDropdown.querySelector('.pt-dropdown-menu');
                const open  = menu && menu.style.display === 'block';
                if (menu) {
                    menu.style.display = open ? 'none' : 'block';
                }
            }
        });
    }

    // -----------------------------
    // Drag & Drop (Board)
    // -----------------------------
    function initDragDrop() {
        document.querySelectorAll('.pt-card').forEach(card => {
            card.addEventListener('dragstart', e => {
                e.dataTransfer.setData('text/plain', card.dataset.taskId || '');
            });
        });

        document.querySelectorAll('.pt-column-body').forEach(colBody => {
            colBody.addEventListener('dragover', e => {
                e.preventDefault();
            });

            colBody.addEventListener('drop', e => {
                e.preventDefault();
                const taskId = e.dataTransfer.getData('text/plain');
                if (!taskId) return;

                const card = document.querySelector('.pt-card[data-task-id="' + taskId + '"]');
                if (!card) return;

                const columnContainer = colBody.closest('.pt-column');
                if (!columnContainer) return;

                const columnKey = columnContainer.dataset.column;
                colBody.prepend(card);

                let status = 'open';
                if (columnKey === 'in_progress') status = 'on_progress';
                if (columnKey === 'completed')   status = 'completed';

                updateStatus(taskId, status);
            });
        });
    }

    // -----------------------------
    // Status update (shared)
    // -----------------------------
    function updateStatus(taskId, status) {
        const urlTpl = "{{ route('personal-tasks.status', ['task' => '__ID__']) }}";
        const url    = urlTpl.replace('__ID__', taskId);

        console.log('updateStatus →', { taskId, status, url });

        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept'      : 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status })
        })
            .then(async response => {
                let data = null;
                try {
                    data = await response.json();
                } catch (e) {}

                if (!response.ok) {
                    console.error('Status update HTTP error', response.status, data);
                    if (window.Swal) {
                        Swal.fire({
                            icon : 'error',
                            title: 'Status-Update fehlgeschlagen',
                            text : 'HTTP ' + response.status + ' – Details in der Konsole.',
                        });
                    } else {
                        alert('Status-Update fehlgeschlagen (HTTP ' + response.status + ').');
                    }
                    throw new Error('HTTP ' + response.status);
                }

                console.log('Status updated response:', data);
                return data;
            })
            .catch(err => {
                console.error('Status update error', err);
                return null;
            });
    }

    // -----------------------------
    // Status buttons (Board + List)
    // -----------------------------
    function initStatusButtons() {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-status-btn');
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const taskId = getTaskIdFromElement(btn);
            const status = btn.dataset.status;

            if (!taskId || !status) {
                console.warn('No taskId/status for status button.');
                return;
            }

            updateStatus(taskId, status).then(data => {
                // Close all menus after action
                document.querySelectorAll('.pt-dropdown-menu').forEach(m => m.style.display = 'none');

                if (data && data.success) {
                    location.reload();
                } else {
                    if (window.Swal) {
                        Swal.fire({
                            icon : 'error',
                            title: 'Status konnte nicht aktualisiert werden',
                            text : 'Bitte Konsole/Netzwerk prüfen.',
                        });
                    } else {
                        alert('Status konnte nicht aktualisiert werden.');
                    }
                }
            });
        });
    }

    // -----------------------------
    // Other list/board actions
    // -----------------------------
    function initListActions() {
        // Archive
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-archive-btn');
            if (!btn) return;

            e.preventDefault();
            const taskId = getTaskIdFromElement(btn);
            if (!taskId) return;

            const urlTpl = "{{ route('personal-tasks.archive', ['task' => '__ID__']) }}";
            const url    = urlTpl.replace('__ID__', taskId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept'      : 'application/json',
                },
            })
                .then(() => location.reload())
                .catch(err => console.error('Archive error', err));
        });

        // Accept job
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-accept-btn');
            if (!btn) return;

            e.preventDefault();
            const taskId = getTaskIdFromElement(btn);
            if (!taskId) return;

            const urlTpl = "{{ route('personal-tasks.accept', ['task' => '__ID__']) }}";
            const url    = urlTpl.replace('__ID__', taskId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept'      : 'application/json',
                },
            })
                .then(() => location.reload())
                .catch(err => console.error('Accept error', err));
        });

        // Open reject modal
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-open-reject-modal');
            if (!btn) return;

            e.preventDefault();
            const taskId = getTaskIdFromElement(btn);
            if (!taskId) return;

            if (rejectBackdrop && rejectReason) {
                rejectTaskId         = taskId;
                rejectReason.value   = '';
                rejectBackdrop.style.display = 'flex';
            }
        });

        // Edit task
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-edit-task-btn');
            if (!btn) return;

            e.preventDefault();
            const url = btn.dataset.editUrl;
            if (url) {
                window.location.href = url;
            }
        });
    }

    // -----------------------------
    // Color change (card + list)
    // -----------------------------
    function initColorChangeListener() {
        document.addEventListener('change', function (e) {
            const input = e.target.closest('.js-task-color');
            if (!input) return;

            const taskId = input.dataset.taskId;
            if (!taskId) return;

            const urlTpl = "{{ route('personal-tasks.color', ['task' => '__ID__']) }}";
            const url    = urlTpl.replace('__ID__', taskId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept'      : 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ color: input.value })
            })
                .then(() => {
                    if (window.feather) feather.replace();
                })
                .catch(err => console.error('Color update error', err));
        });
    }
</script>

<script>
    window.employeeOptions       = @json($employeeOptions ?? []);
    window.TaskActivityFeed      = @json($activityFeed ?? []);
    window.employeeImageBasePath = "{{ asset('images/employee') }}";
    window.employeeDefaultAvatar = "{{ asset('images/gender/male.png') }}";
    window.currentEmployeeId     = {{ (int)($employeeId ?? 0) }};
</script>

<script>
    $(function () {
        const employeeOptions  = window.employeeOptions || [];

        const startDateInput   = document.getElementById("start_date");
        const dueDateInput     = document.getElementById("due_date");
        const dueTimeInput     = document.getElementById("due_time");
        const totalDayInput    = document.getElementById("total_day");
        const totalTimeInput   = document.getElementById("total_time");

        let keyTaskIndex       = $('#key_task tbody tr').length || 0;

        function formatEmployeeOption(employee) {
            if (!employee.id) return employee.text || '';
            const $opt  = $(employee.element);
            const image = $opt.data('image');
            const name  = employee.text || '';
            if (!image) return name;

            return $(`
                <div style="display:flex;align-items:center;">
                    <img src="${image}"
                         style="width:20px;height:20px;border-radius:50%;margin-right:8px;object-fit:cover;">
                    <span>${name}</span>
                </div>
            `);
        }

        function formatEmployeeSelection(employee) {
            return employee.text || '';
        }

        function initEmployeeSelect2(scope) {
            const $scope = scope ? $(scope) : $(document);

            $scope
                .find('#employee, #controller, select[name^="key"][name$="[employee_id][]"]')
                .not('.select2-initialized')
                .each(function () {
                    $(this)
                        .addClass('select2-initialized')
                        .select2({
                            width            : '100%',
                            templateResult   : formatEmployeeOption,
                            templateSelection: formatEmployeeSelection,
                            escapeMarkup     : function (m) { return m; },
                            dropdownParent   : $('.new_task_card')
                        });
                });
        }

        function syncTopEmployeeFromSteps() {
            const $top = $('#employee');
            if (!$top.length) return;

            const currentTop = $top.val() || [];
            const idSet      = new Set(currentTop);

            $('select[name^="key"][name$="[employee_id][]"]').each(function () {
                const vals = $(this).val() || [];
                vals.forEach(v => idSet.add(v));
            });

            const newVals = Array.from(idSet);
            $top.val(newVals).trigger('change.select2');
        }

        initEmployeeSelect2();
        syncTopEmployeeFromSteps();

        $(document).on('change', 'select[name^="key"][name$="[employee_id][]"]', function () {
            syncTopEmployeeFromSteps();
        });

        const $customerSelect    = $('#customerLeadProductSelect');
        const $customerSwitch    = $('#customerSwitch');
        const $customerContainer = $('#customerSelectContainer');

        if ($customerSelect.length) {
            $customerSelect.select2({
                width         : '100%',
                placeholder   : 'Kunde suchen...',
                dropdownParent: $customerContainer,
                ajax: {
                    url     : '{{ route("lead.product.list.ajax") }}',
                    dataType: 'json',
                    delay   : 250,
                    data    : function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: (data.results || []).map(function (item) {
                                return {
                                    id            : item.id,
                                    text          : item.text,
                                    html          : item.html,
                                    alternative_id: item.alternative_id,
                                    product_id    : item.product_id
                                };
                            })
                        };
                    },
                    cache: true
                },
                templateResult: function (data) {
                    if (data.loading) return data.text;
                    return $(data.html);
                },
                templateSelection: function (data) {
                    if (data.alternative_id) {
                        $('#select_alternative_id').val(data.alternative_id);
                    }
                    if (data.product_id) {
                        $('#select_product_id').val(data.product_id);
                    }
                    return data.text;
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            $customerSwitch.on('change', function () {
                if ($(this).is(':checked')) {
                    $customerContainer.slideDown(150, function () {
                        $customerSelect.trigger('change.select2');
                    });
                    $(this).val(1);
                } else {
                    $customerContainer.slideUp(150);
                    $customerSelect.val(null).trigger('change');
                    $('#select_alternative_id').val('');
                    $('#select_product_id').val('');
                    $(this).val(0);
                }
            });

            if ($customerSwitch.is(':checked')) {
                $customerContainer.show();
            } else {
                $customerContainer.hide();
            }
        }

        function calculateTotalDaysAndHours() {
            if (!startDateInput || !dueDateInput) return;

            const startDate = new Date(startDateInput.value);
            const dueDate   = new Date(dueDateInput.value);

            if (!startDateInput.value || !dueDateInput.value ||
                isNaN(startDate) || isNaN(dueDate)) {
                if (totalDayInput)  totalDayInput.value  = "";
                if (totalTimeInput) totalTimeInput.value = "";
                return;
            }

            const workHoursPerDay = 24;
            let totalDays         = 0;
            let totalWorkingHours = 0;
            let tempDate          = new Date(startDate);

            while (tempDate <= dueDate) {
                const day = tempDate.getDay();
                if (day !== 0 && day !== 6) {
                    totalDays++;
                    totalWorkingHours += workHoursPerDay;
                }
                tempDate.setDate(tempDate.getDate() + 1);
            }

            if (dueTimeInput && dueTimeInput.value) {
                const [hStr, mStr] = dueTimeInput.value.split(':');
                const dueHour      = parseInt(hStr || '0', 10);
                const dueMinute    = parseInt(mStr || '0', 10);

                const remainingHours = dueHour + (dueMinute > 0 ? 1 : 0);

                let lastDay = new Date(dueDate);
                let dow     = lastDay.getDay();

                while (dow === 0 || dow === 6) {
                    lastDay.setDate(lastDay.getDate() + 1);
                    dow = lastDay.getDay();
                }

                totalWorkingHours -= workHoursPerDay;
                totalWorkingHours += remainingHours;
            }

            if (totalDayInput)  totalDayInput.value  = totalDays;
            if (totalTimeInput) totalTimeInput.value = totalWorkingHours;

            updateTotalDuration();
        }

        function updateTotalDuration() {
            let total = 0;
            $('.task-duration').each(function () {
                const val = parseInt($(this).val(), 10) || 0;
                total += val;
            });

            const allowed = parseInt($('#total_time').val(), 10) || 0;
            const diff    = allowed - total;

            $('#key_total_time').text(
                diff >= 0 ? `${diff} Std` : `Überschreitung um ${Math.abs(diff)} Std!`
            );

            if (allowed && total > allowed) {
                Swal.fire({
                    icon : "error",
                    title: "⚠ Zeitüberschreitung!",
                    text : `Die gesamte Dauer der Aufgaben beträgt ${total} Stunden, überschreitet jedoch die geplanten ${allowed} Stunden.`,
                });
            }
        }

        if (startDateInput && dueDateInput && dueTimeInput) {
            startDateInput.addEventListener("change", calculateTotalDaysAndHours);
            dueDateInput.addEventListener("change", calculateTotalDaysAndHours);
            dueTimeInput.addEventListener("change", calculateTotalDaysAndHours);
        }

        $('#key_task').on('input', '.task-duration', updateTotalDuration);

        $(document).on('click', '.add-task-steps', function () {
            keyTaskIndex++;
            const rowCount = $('#key_task tbody tr').length;

            const employeeOptionsHtml = employeeOptions.map(emp => {
                return `<option value="${emp.id}" data-image="${emp.image}">
                            ${emp.name} ${emp.lastname}
                        </option>`;
            }).join('');

            const newRowHtml = `
                <tr>
                    <td>${rowCount + 1}</td>
                    <td>
                        <input type="text"
                               name="key[${keyTaskIndex}][task]"
                               class="form-control form-control-sm">
                    </td>
                    <td>
                        <input type="number"
                               name="key[${keyTaskIndex}][duration]"
                               class="form-control form-control-sm task-duration">
                    </td>
                    <td>
                        <select name="key[${keyTaskIndex}][employee_id][]"
                                multiple
                                style="width:100%">
                            ${employeeOptionsHtml}
                        </select>
                    </td>
                    <td>
                        <textarea name="key[${keyTaskIndex}][key_description]"
                                  class="form-control form-control-sm"></textarea>
                    </td>
                    <td>
                        <button type="button"
                                class="btn btn-sm btn-primary add-task-steps">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-danger remove-task-steps">
                            <i class="fa fa-minus"></i>
                        </button>
                    </td>
                </tr>
            `;

            const $newRow = $(newRowHtml);
            $('#key_task tbody').append($newRow);

            initEmployeeSelect2($newRow);
            syncTopEmployeeFromSteps();
            updateTotalDuration();
        });

        $(document).on('click', '.remove-task-steps', function () {
            const $tbody = $('#key_task tbody');

            if ($tbody.find('tr').length <= 1) {
                Swal.fire({
                    icon : "warning",
                    title: "Achtung",
                    text : "Es muss mindestens ein Aufgabenschritt vorhanden sein.",
                });
                return;
            }

            $(this).closest('tr').remove();

            $tbody.find('tr').each(function (index) {
                $(this).find('td:first').text(index + 1);

                $(this).find('input, textarea, select').each(function () {
                    let name = $(this).attr('name');
                    if (!name) return;
                    name = name.replace(/\[\d+]/, `[${index}]`);
                    $(this).attr('name', name);
                });
            });

            syncTopEmployeeFromSteps();
            updateTotalDuration();
        });

        calculateTotalDaysAndHours();
        updateTotalDuration();
        syncTopEmployeeFromSteps();

        const collapse           = document.getElementById('collapseTaskKeys');
        const topEmployeeSection = document.getElementById('task_employee_section');

        if (collapse && topEmployeeSection) {
            $('#collapseTaskKeys').on('show.bs.collapse', function () {
                topEmployeeSection.style.display = 'none';
            });

            $('#collapseTaskKeys').on('hide.bs.collapse', function () {
                topEmployeeSection.style.display = 'block';
            });
        }

        $('#color_drop_down').on('click', '.dropdown-item', function () {
            const selectedColor = $(this).data('value');
            $('#color').val(selectedColor);
            $('#colorIcon').css('color', selectedColor);
        });

        $('#priority_select').on('click', '.dropdown-item', function () {
            const selectedPriority = $(this).data('value');
            const selectedIcon     = $(this).html();

            $('input[name="priority"]').val(selectedPriority);
            $('#priority_select button').html(selectedIcon);

            let label = 'Standard';
            if (selectedPriority === 'medium')    label = 'Medium';
            if (selectedPriority === 'high')      label = 'Hoch';
            if (selectedPriority === 'very high') label = 'Sehr wichtig';

            $('#priority_select .nt-priority-label').text(label);
        });

        const repeatedButton = document.getElementById('repeated');
        const repeatedArea   = document.querySelector('.repeated_area');
        const reminderButton = document.getElementById('reminder_check');
        const reminderArea   = document.querySelector('.reminder_area');

        if (repeatedButton && repeatedArea) {
            repeatedButton.addEventListener('change', function () {
                repeatedArea.style.display = this.checked ? 'table-row' : 'none';
            });
            repeatedArea.style.display = 'none';
        }

        if (reminderButton && reminderArea) {
            reminderButton.addEventListener('change', function () {
                reminderArea.style.display = this.checked ? 'table-row' : 'none';
            });
            reminderArea.style.display = 'none';
        }

        $('.create_new_task').on('click', function () {
            $('.new_task')
                .show()
                .animate({ right: '0' }, 400, function () {
                    $('#employee, #controller, #customerLeadProductSelect')
                        .trigger('change.select2');
                });
        });

        $('.new_task').on('click', '.close_task_window', function () {
            $('.new_task').animate({ right: '-100%' }, 400, function () {
                $(this).hide();
            });
        });

        function validateForm() {
            const errors      = [];
            const taskTitle   = $('#task_title').val().trim();
            const dueDate     = $('#due_date').val().trim();
            const keyTaskRows = $('#key_task tbody tr');

            if (!taskTitle) {
                errors.push('Bitte geben Sie einen Aufgabentitel ein.');
            }

            if (!dueDate) {
                errors.push('Bitte wählen Sie ein Fälligkeitsdatum.');
            }

            keyTaskRows.each(function () {
                const taskInput = $(this).find('input[name^="key"]').val();
                if (taskInput && taskInput.trim() !== '') {
                    return false;
                }
            });

            return errors;
        }

        function submitTaskForm(closeAfterSave) {
            const errors = validateForm();

            if (errors.length > 0) {
                Swal.fire({
                    icon : 'warning',
                    title: 'Formular ungültig',
                    html : errors.join('<br>'),
                });
                return;
            }

            const formData = $('#task_form').serialize();

            $.ajax({
                type   : 'POST',
                url    : "{{ route('personal.task.store') }}",
                data   : formData,
                success: function (response) {
                    Swal.fire({
                        icon : 'success',
                        title: 'Erfolgreich gespeichert!',
                        text : 'Die Aufgabe wurde erfolgreich gespeichert.',
                    }).then(() => {
                        if (closeAfterSave) {
                            if (response && response.task_id) {
                                const url = new URL(window.location.href);
                                url.searchParams.set('highlight', response.task_id);
                                window.location.href = url.toString();
                            } else {
                                location.reload();
                            }
                        } else {
                            $('#task_form')[0].reset();
                        }
                    });
                },
                error: function (xhr) {
                    let errorMsg = 'Ein Fehler ist aufgetreten.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon : 'error',
                        title: 'Fehler',
                        text : errorMsg,
                    });
                }
            });
        }

        $('.save-task-close').on('click', function () {
            submitTaskForm(true);
        });

        $('.save-task-continue').on('click', function () {
            submitTaskForm(false);
        });
    });
</script>
@endsection
