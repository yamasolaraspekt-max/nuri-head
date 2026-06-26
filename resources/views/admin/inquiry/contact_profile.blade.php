@extends('admin.layouts.app')

@section('title', 'Anfrage Aufnahme')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        :root{
            --inq-bg:#f6f8fc;
            --inq-card:#ffffff;
            --inq-line:#e7edf5;
            --inq-line-strong:#d8e2ee;
            --inq-text:#18212f;
            --inq-muted:#6b7280;
            --inq-primary:#73b2d4;
            --inq-primary-soft:#eef7fb;
            --inq-success:#93c21c;
            --inq-success-soft:#f5fbe8;
            --inq-verified:#28c76f;
            --inq-verified-soft:#f0fdf4;
            --inq-danger:#ea5455;
            --inq-danger-soft:#fff2f2;
            --inq-warning:#f4a459;
            --inq-warning-soft:#fff7ef;
            --inq-shadow:0 18px 45px rgba(15,23,42,.06);
            --inq-radius:20px;
        }

        .app-content .content-wrapper{
            background:var(--inq-bg);
        }

        .avatar-lg{
            width:64px;
            height:64px;
            object-fit:cover;
            border-radius:50%;
        }

        .avatar-sm{
            width:34px;
            height:34px;
            object-fit:cover;
            border-radius:50%;
        }

        .inq-shell{
            display:flex;
            flex-direction:column;
            gap:22px;
        }

        .inq-card{
            background:var(--inq-card);
            border:1px solid var(--inq-line);
            border-radius:var(--inq-radius);
            box-shadow:var(--inq-shadow);
            overflow:hidden;
        }

        .inq-card-header{
            padding:18px 22px 0;
        }

        .inq-card-body{
            padding:22px;
        }

        .inq-hero{
            position:relative;
            overflow:hidden;
        }

        .inq-hero::before{
            content:"";
            position:absolute;
            inset:0 0 auto 0;
            height:110px;
            background:linear-gradient(135deg, rgba(115,178,212,.15), rgba(147,194,28,.08));
            pointer-events:none;
        }

        .inq-hero-grid{
            position:relative;
            display:grid;
            grid-template-columns:1.4fr .9fr .8fr;
            gap:18px;
            align-items:stretch;
        }

        .inq-identity{
            display:flex;
            gap:16px;
            align-items:flex-start;
            padding:22px;
            border:1px solid var(--inq-line);
            border-radius:18px;
            background:rgba(255,255,255,.88);
            backdrop-filter:blur(6px);
        }

        .inq-iconbox{
            width:68px;
            height:68px;
            border-radius:18px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:var(--inq-primary-soft);
            color:var(--inq-primary);
            flex:0 0 68px;
        }

        .inq-title{
            font-size:1.35rem;
            font-weight:800;
            color:var(--inq-text);
            line-height:1.2;
            margin:0 0 8px;
        }

        .inq-subtle{
            color:var(--inq-muted);
            font-size:.92rem;
        }

        .inq-stars i{
            margin-right:2px;
        }

        .inq-sidebox{
            padding:18px;
            border:1px solid var(--inq-line);
            border-radius:18px;
            background:#fff;
            height:100%;
        }

        .inq-sidebox .label{
            display:block;
            font-size:.78rem;
            font-weight:700;
            letter-spacing:.04em;
            text-transform:uppercase;
            color:var(--inq-muted);
            margin-bottom:8px;
        }

        .inq-sidebox .value{
            color:var(--inq-text);
            font-weight:600;
        }

        .inq-note-box{
            margin-top:12px;
            padding:12px 14px;
            border-radius:14px;
            background:var(--inq-primary-soft);
            color:#3c4b5e;
            font-size:.9rem;
            line-height:1.45;
        }

        .inq-stats{
            display:flex;
            flex-direction:column;
            gap:14px;
            justify-content:center;
            height:100%;
        }

        .inq-stat{
            padding:16px 18px;
            border:1px solid var(--inq-line);
            border-radius:18px;
            background:#fff;
        }

        .inq-stat .label{
            display:block;
            color:var(--inq-muted);
            font-size:.8rem;
            text-transform:uppercase;
            letter-spacing:.05em;
            margin-bottom:6px;
        }

        .inq-sidebar-card{
            height:100%;
        }

        .inq-section-title{
            font-size:1rem;
            font-weight:800;
            color:var(--inq-text);
            margin:0;
        }

        .inq-list{
            list-style:none;
            padding:0;
            margin:0;
        }

        .inq-list li{
            padding:12px 0;
            border-bottom:1px dashed var(--inq-line);
        }

        .inq-list li:last-child{
            border-bottom:0;
            padding-bottom:0;
        }

        .inq-label{
            display:block;
            font-size:.75rem;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.05em;
            color:var(--inq-muted);
            margin-bottom:4px;
        }

        .inq-value{
            color:var(--inq-text);
            font-weight:600;
            line-height:1.45;
        }

        .inq-next-step{
            margin-top:16px;
            padding:14px;
            border-radius:16px;
            background:var(--inq-primary-soft);
            border:1px solid #dceaf2;
            color:var(--inq-text);
            font-size:.92rem;
        }

        .inq-tabs-card .card-header{
            background:transparent;
            border-bottom:1px solid var(--inq-line);
            padding:18px 22px;
        }

        .inq-tabs-card .nav-pills{
            gap:10px;
            flex-wrap:wrap;
        }

        .inq-tabs-card .nav-pills .nav-link{
            border-radius:999px;
            padding:.72rem 1rem;
            background:#fff;
            border:1px solid var(--inq-line);
            color:var(--inq-text);
            font-weight:700;
        }

        .inq-tabs-card .nav-pills .nav-link.active{
            background:var(--inq-primary-soft);
            color:#245b77;
            border-color:#cfe3ee;
        }

        .inq-tabs-card .card-body{
            padding:22px;
        }

        /* Kanban */
        .kanban-container{
            overflow-x:auto;
            padding-bottom:18px;
        }

        .kanban-board{
            display:flex;
            gap:20px;
            min-width:1040px;
            align-items:stretch;
        }

        .kanban-column{
            flex:1;
            min-height:430px;
            display:flex;
            flex-direction:column;
            background:#f8fafc;
            border:1px solid var(--inq-line);
            border-radius:20px;
            padding:14px;
        }

        .kanban-header{
            font-weight:800;
            text-transform:uppercase;
            letter-spacing:.06em;
            text-align:center;
            padding:12px 14px;
            border-radius:14px;
            margin-bottom:14px;
            color:#fff;
            box-shadow:0 8px 18px rgba(15,23,42,.08);
        }

        #kanban-Unpublished .kanban-header{ background:#73b2d4; }
        #kanban-progress .kanban-header{ background:#93c21c; }
        #kanban-verified .kanban-header{ background:#80c98a; }
        #kanban-junk .kanban-header{ background:#ea5455; }

        .kanban-list{
            flex:1;
            min-height:250px;
        }

        .kanban-card{
            background:#fff;
            border:1px solid var(--inq-line);
            border-radius:18px;
            padding:16px;
            margin-bottom:12px;
            cursor:grab;
            transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease;
            box-shadow:0 10px 24px rgba(15,23,42,.04);
        }

        .kanban-card:hover{
            transform:translateY(-3px);
            box-shadow:0 16px 28px rgba(15,23,42,.08);
        }

        .kanban-card:active{
            cursor:grabbing;
        }

        .kanban-card-verified{
            border:2px solid var(--inq-verified);
            background:var(--inq-verified-soft);
        }

        .process-icons{
            display:flex;
            justify-content:flex-start;
            gap:10px;
            margin-top:12px;
            flex-wrap:wrap;
        }

        .process-step{
            display:flex;
            flex-direction:column;
            align-items:center;
            width:52px;
        }

        .circle-badge{
            width:34px;
            height:34px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:800;
            font-size:13px;
            color:#fff;
            background:#7DC242;
            margin-bottom:4px;
            position:relative;
            z-index:2;
            box-shadow:0 8px 16px rgba(125,194,66,.25);
        }

        .connector-line{
            width:100%;
            height:2px;
            background:#ddd;
            position:absolute;
            top:16px;
            left:50%;
            z-index:1;
            display:none;
        }

        .profile-border-green{
            border:2px solid #7DC242;
        }

        .profile-border-orange{
            border:2px solid #f4a459;
        }

        .step-text{
            font-size:9px;
            color:#666;
            text-align:center;
            line-height:1.1;
        }

        /* Table */
        .table-responsive{
            overflow-x:visible;
        }

        .inq-table-wrap{
            border:1px solid var(--inq-line);
            border-radius:18px;
            overflow:hidden;
            background:#fff;
        }

        .inq-table-wrap .table{
            margin-bottom:0;
        }

        .inq-table-wrap thead th{
            background:#f8fafc;
            border-color:var(--inq-line);
            color:var(--inq-text);
            font-size:.8rem;
            text-transform:uppercase;
            letter-spacing:.05em;
            font-weight:800;
            vertical-align:middle;
        }

        .inq-table-wrap tbody td{
            vertical-align:middle;
            border-color:var(--inq-line);
        }

        .select2-container .select2-selection--single{
            height:38px;
            line-height:38px;
            border:1px solid #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered{
            line-height:38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow{
            height:36px;
        }

        /* Verify */
        .inq-verify-box{
            border:1px solid var(--inq-line);
            border-radius:18px;
            background:#fff;
            padding:18px;
            height:100%;
        }

        .inq-verify-box.primary{
            background:linear-gradient(180deg, #fff, #fbfdff);
            border-color:#d9e9f3;
        }

        .inq-verify-box.soft{
            background:#fafcff;
        }

        /* Timeline */
        .timeline{
            list-style:none;
            padding-left:20px;
            border-left:2px solid #e9ecef;
        }

        .timeline-item{
            position:relative;
            margin-bottom:20px;
            padding-left:20px;
        }

        .timeline-item::before{
            content:'';
            position:absolute;
            left:-26px;
            top:5px;
            width:14px;
            height:14px;
            border-radius:50%;
            background:#fff;
            border:3px solid #7367f0;
        }

        /* Comments */
        .comment-card{
            background:#f8fafc;
            border-radius:14px;
            padding:15px;
            margin-bottom:15px;
            border:1px solid var(--inq-line);
        }

        .comment-actions button{
            font-size:.8rem;
            padding:2px 8px;
        }

        /* Reports */
        .report-card{
            border:1px solid var(--inq-line);
            border-radius:16px;
            padding:14px;
            background:#fff;
            margin-bottom:12px;
            box-shadow:0 8px 20px rgba(15,23,42,.03);
        }

        .report-card .meta{
            font-size:12px;
            color:#6b7280;
        }

        .report-actions .btn{
            padding:.25rem .5rem;
        }

        /* Animation */
        @keyframes flash{
            0%{background:#c3f3c3;}
            50%{background:#a8e6a8;}
            100%{background:#c3f3c3;}
        }

        .animated.flash{
            animation:flash 2s ease-in-out 1;
        }

        @media (max-width: 1199.98px){
            .inq-hero-grid{
                grid-template-columns:1fr;
            }
        }

        @media (max-width: 991.98px){
            .inq-identity{
                flex-direction:column;
                align-items:flex-start;
            }

            .inq-tabs-card .nav-pills .nav-link{
                width:100%;
                justify-content:flex-start;
            }
        }

            /* --- New product flow design for kanban --- */
        .kanban-products-flow{
        display:flex;
        flex-direction:column;
        gap:10px;
        margin-top:10px;
    }

    .kanban-product-flow{
        padding:8px 10px;
        border:1px solid var(--inq-line);
        border-radius:12px;
        background:#fbfdff;
    }

    .kanban-flow-top{
        display:flex;
        align-items:center;
        justify-content:flex-start;
        gap:0;
        margin-bottom:6px;
    }

    .kanban-node{
        width:38px;
        height:38px;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        overflow:hidden;
        flex:0 0 38px;
        position:relative;
        z-index:2;
        background:#fff;
    }

    .kanban-node.product-node{
        background:#7DC242;
        color:#fff;
        font-weight:800;
        font-size:13px;
        border:3px solid #7DC242;
        box-shadow:0 5px 10px rgba(125,194,66,.18);
    }

    .kanban-node.employee-node{
        border:3px solid #7DC242;
        box-shadow:0 5px 10px rgba(15,23,42,.06);
        background:#fff;
    }

    .kanban-node.employee-node.field{
        border-color:#f4a459;
    }

    .kanban-node img{
        width:100%;
        height:100%;
        object-fit:cover;
        border-radius:50%;
    }

    .kanban-flow-line{
        width:16px;
        height:3px;
        background:#7DC242;
        border-radius:999px;
        flex:0 0 16px;
        margin:0 -1px;
        position:relative;
        z-index:1;
    }

    .kanban-flow-line.to-field{
        background:#f4a459;
    }

    .kanban-flow-body{
        padding-left:1px;
    }

    .kanban-product-title{
        font-size:11px;
        font-weight:800;
        color:#4b4b4b;
        line-height:1.1;
        margin-bottom:2px;
    }

    .kanban-product-meta{
        font-size:10px;
        color:#a8b1bf;
        font-weight:700;
        line-height:1.15;
    }

    .kanban-empty-avatar{
        width:100%;
        height:100%;
        display:flex;
        align-items:center;
        justify-content:center;
        color:#94a3b8;
        background:#f8fafc;
        font-size:13px;
    }

    .kanban-card .kanban-owner{
        display:flex;
        align-items:center;
        gap:6px;
        margin-bottom:8px;
    }

    .kanban-card .kanban-owner small{
        color:#6b7280;
        font-weight:600;
        font-size:11px;
    }

    /* =========================================================
       FORCE TABS HORIZONTAL
    ========================================================= */

    .inq-tabs-card .card-header{
        background:transparent;
        border-bottom:1px solid var(--inq-line);
        padding:18px 22px;
        overflow:hidden;
    }

    .inq-tabs-card .nav-pills,
    .inq-tabs-card .card-header-pills{
        display:flex !important;
        flex-direction:row !important;
        flex-wrap:nowrap !important;
        align-items:center !important;
        gap:10px;
        margin:0 !important;
        padding:0 !important;
        width:100%;
        overflow-x:auto;
        overflow-y:hidden;
        white-space:nowrap;
        scrollbar-width:thin;
    }

    .inq-tabs-card .nav-pills .nav-item,
    .inq-tabs-card .card-header-pills .nav-item{
        display:block !important;
        width:auto !important;
        flex:0 0 auto !important;
        margin:0 !important;
    }

    .inq-tabs-card .nav-pills .nav-link,
    .inq-tabs-card .card-header-pills .nav-link{
        display:inline-flex !important;
        align-items:center;
        justify-content:center;
        gap:7px;
        width:auto !important;
        min-width:max-content;
        border-radius:999px;
        padding:.72rem 1rem;
        background:#fff;
        border:1px solid var(--inq-line);
        color:var(--inq-text);
        font-weight:700;
        white-space:nowrap;
    }

    .inq-tabs-card .nav-pills .nav-link.active,
    .inq-tabs-card .card-header-pills .nav-link.active{
        background:var(--inq-primary-soft);
        color:#245b77;
        border-color:#cfe3ee;
    }

    /* Mobile: keep horizontal scroll, not vertical */
    @media (max-width: 991.98px){
        .inq-tabs-card .card-header{
            padding:14px;
        }

        .inq-tabs-card .nav-pills,
        .inq-tabs-card .card-header-pills{
            flex-direction:row !important;
            flex-wrap:nowrap !important;
            overflow-x:auto;
        }

        .inq-tabs-card .nav-pills .nav-link,
        .inq-tabs-card .card-header-pills .nav-link{
            width:auto !important;
            justify-content:center !important;
            padding:.65rem .9rem;
            font-size:.82rem;
        }
    }

    </style>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="app-content"> 
    <div class="content-wrapper"> 
        <div class="content-body">
            <div class="inq-shell">
                {{-- HERO --}}
                <div class="inq-card inq-hero">
                    <div class="inq-card-body">
                        <div class="inq-hero-grid">
                            <div class="inq-identity">
                                <div class="inq-iconbox">
                                    <i class="feather icon-user font-large-1"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <h4 class="inq-title">
                                        {{ $data->firma ?? '' }}
                                        @if($data->firma && ($data->name || $data->lastname)) - @endif
                                        {{ $data->name ?? '' }} {{ $data->lastname ?? '' }}
                                    </h4>

                                    <div class="d-flex flex-wrap align-items-center mb-2" style="gap:10px;">
                                        <span class="badge badge-light-primary">{{ $data->pre_type ?? 'Typ nicht definiert' }}</span>
                                    </div>

                                    <div class="inq-stars mb-2">
                                        @for($i = 0; $i < 5; $i++)
                                            <i class="feather icon-star {{ $i < 4 ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>

                                    <div class="inq-subtle">
                                        <i class="feather icon-map-pin mr-50"></i>
                                        {{ $data->street ?? '' }} {{ $data->postcode ?? '' }} {{ $data->city ?? '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="inq-sidebox">
                                <span class="label">Standort & Hinweis</span>
                                <div class="value">
                                    {{ $data->street ?? '-' }}<br>
                                    {{ $data->postcode ?? '' }} {{ $data->city ?? '' }}
                                </div>

                                @if($data->note)
                                    <div class="inq-note-box mt-3">
                                        <small class="d-block text-muted mb-1">Notiz</small>
                                        <span class="font-italic">"{{ Str::limit($data->note, 110) }}"</span>
                                    </div>
                                @endif
                            </div>

                            <div class="inq-stats">
                                <div class="inq-stat">
                                    <span class="label">Status</span>
                                    <span class="badge badge-pill badge-primary" id="status">
                                        @if($data->status == "Unpublished") Neue
                                        @elseif($data->status == "progress") In Bearbeitung
                                        @elseif($data->status == "junk") Junk
                                        @elseif($data->status == "Published" || $data->status == "verified") Verifiziert
                                        @else {{ $data->status }}
                                        @endif
                                    </span>
                                </div>

                                <div class="inq-stat">
                                    <span class="label">Priorität</span>
                                    @php
$prioClass = match ($data->periority) {
    'very_high' => 'badge-danger',
    'high' => 'badge-warning',
    'low' => 'badge-secondary',
    default => 'badge-info'
};
$prioLabel = match ($data->periority) {
    'high' => 'Dringend',
    'very_high' => 'Sehr Dringend',
    'low' => 'Niedrig',
    default => 'Normal'
};
                                    @endphp
                                    <span class="badge {{ $prioClass }}">{{ $prioLabel }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- SIDEBAR --}}
                    <div class="col-lg-3 col-md-12 mb-4">
                        <div class="inq-card inq-sidebar-card">
                            <div class="inq-card-header">
                                <h4 class="inq-section-title">Details</h4>
                            </div>

                            <div class="inq-card-body">
                                <ul class="inq-list">
                                    <li>
                                        <span class="inq-label">E-Mail</span>
                                        <a href="mailto:{{ $data->email }}" class="inq-value text-dark">{{ $data->email ?? '-' }}</a>
                                    </li>

                                    <li>
                                        <span class="inq-label">Telefon</span>
                                        <span class="inq-value">{{ $data->phone ?? '-' }}</span>
                                    </li>

                                    <li>
                                        <span class="inq-label">Erstellt am</span>
                                        <span class="inq-value">{{ \Carbon\Carbon::parse($data->created_at)->locale('de')->isoFormat('D. MMM YYYY, HH:mm') }}</span>
                                    </li>

                                    <li>
                                        <span class="inq-label">Verfasser</span>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/employee/' . $data->emp_image) }}" class="avatar-sm mr-2" alt="Avatar">
                                            <span class="inq-value">{{ $data->emp_name }} {{ $data->emp_lastname }}</span>
                                        </div>
                                    </li>
                                </ul>

                                @if($data->next_step)
                                    <div class="inq-next-step">
                                        <strong class="d-block mb-1">Nächster Schritt</strong>
                                        {{ $data->next_step }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- MAIN --}}
                    <div class="col-lg-9 col-md-12">
                        <div class="card inq-tabs-card inq-card">
                            <div class="card-header">
                                <ul class="nav nav-pills card-header-pills" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="kanban-tab" data-toggle="tab" href="#kanban-view" role="tab">
                                            <i class="feather icon-trello"></i> KANBAN
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="product-tab" data-toggle="tab" href="#product-view" role="tab">
                                            <i class="feather icon-box"></i> PRODUKTE
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="verify-tab" data-toggle="tab" href="#verify-view" role="tab">
                                            <i class="feather icon-check-circle"></i> VERIFIZIERUNG
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="report-tab" data-toggle="tab" href="#report-view" role="tab">
                                            <i class="feather icon-file-text"></i> REPORTS
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="comment-tab" data-toggle="tab" href="#comment-view" role="tab">
                                            <i class="feather icon-message-square"></i> KOMMENTAR
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="activity-tab" data-toggle="tab" href="#activity-view" role="tab">
                                            <i class="feather icon-activity"></i> LOGS
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">
                                    {{-- TAB 1: KANBAN --}}
                                    <div class="tab-pane fade show active" id="kanban-view" role="tabpanel">
                                        <div class="kanban-container">
                                            <div class="kanban-board">
                                                @foreach(['Unpublished' => 'Neu', 'progress' => 'In Bearbeitung', 'verified' => 'Verifizieren', 'junk' => 'Junk'] as $status => $label)
                                                    @php
    $alreadyVerified = in_array($data->status, ['Published', 'verified'], true);

    $belongsHere =
        $data->status === $status
        || ($status === 'verified' && $alreadyVerified)
        || ($status === 'Unpublished' && $data->status === 'Draft');
                                                    @endphp

                                                    <div class="kanban-column" id="kanban-{{ $status }}" data-status="{{ $status }}">
                                                        <div class="kanban-header">{{ $label }}</div>

                                                        <div class="kanban-list" id="kanban-list-{{ $status }}" data-status="{{ $status }}">
                                                            @if($belongsHere)
                                                                <div class="kanban-card draggable {{ $alreadyVerified ? 'kanban-card-verified' : '' }}"
                                                                     data-id="{{ $data->id }}"
                                                                     data-already-verified="{{ $alreadyVerified ? '1' : '0' }}">

                                                                    <div class="d-flex justify-content-between mb-2">
                                                                        <strong class="text-primary">{{ $data->firma ?? $data->name }}</strong>
                                                                        @if($alreadyVerified)
                                                                            <i class="feather icon-check-circle text-success" title="Verifiziert"></i>
                                                                        @endif
                                                                    </div>

                                                                    <small class="text-muted">
                                                                        <i class="feather icon-map-pin"></i> {{ $data->city ?? '' }}
                                                                    </small>

                                                                    <hr class="my-2">

                                                                    <div class="kanban-owner">
                                                                        <img src="{{ asset('images/employee/' . $data->emp_image) }}" class="avatar-sm" alt="Owner">
                                                                        <small>{{ $data->emp_name }} {{ $data->emp_lastname }}</small>
                                                                    </div>

                                                                   @php
        $openItems = collect($productList)
            ->where('inquiry_id', $data->id)
            ->values();

        $male = asset('images/gender/male.png');
        $female = asset('images/gender/female.png');

        $empImg = fn($img, $gender) =>
            ($img && file_exists(public_path('images/employee/' . $img)))
            ? asset('images/employee/' . $img)
            : (strtolower($gender ?? '') === 'female' ? $female : $male);

        $initials = function ($text) {
            $text = trim((string) $text);
            if ($text === '')
                return 'PR';
            $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
            if (!$words || count($words) === 0)
                return 'PR';

            if (count($words) === 1) {
                return strtoupper(mb_substr($words[0], 0, 2));
            }

            return strtoupper(
                mb_substr($words[0], 0, 1) .
                mb_substr($words[1], 0, 1)
            );
        };
                                                                    @endphp

                                                                    @if($openItems->count() > 0)
                                                                        <div class="kanban-products-flow">
                                                                            @foreach($openItems as $p)
                                                                                @php
                $productTitle = $p->article_group ?: 'Produkt';
                $productShort = !empty($p->initial) ? strtoupper($p->initial) : $initials($productTitle);
                $departmentName = $p->department_name ?: 'Unbekannte Abteilung';
                $insideName = trim(($p->ename ?? '') . ' ' . ($p->elastname ?? ''));
                $fieldName = trim(($p->fname ?? '') . ' ' . ($p->flastname ?? ''));
                                                                                @endphp

                                                                                <div class="kanban-product-flow">
                                                                                    <div class="kanban-flow-top">
                                                                                        <div class="kanban-node product-node" title="{{ $productTitle }}">
                                                                                            {{ $productShort }}
                                                                                        </div>

                                                                                        <div class="kanban-flow-line"></div>

                                                                                        <div class="kanban-node employee-node select-employee"
                                                                                            style="cursor:pointer"
                                                                                            data-type="employee"
                                                                                            data-id="{{ $p->id }}"
                                                                                            title="{{ $insideName ?: 'Innendienst wählen' }}">
                                                                                            @if(!empty($p->ename) || !empty($p->elastname))
                                                                                                <img src="{{ $empImg($p->eimage, $p->egender) }}" alt="{{ $insideName }}">
                                                                                            @else
                                                                                                <div class="kanban-empty-avatar">
                                                                                                    <i class="feather icon-user"></i>
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>

                                                                                        <div class="kanban-flow-line to-field"></div>

                                                                                        <div class="kanban-node employee-node field select-employee"
                                                                                            style="cursor:pointer"
                                                                                            data-type="field_employee"
                                                                                            data-id="{{ $p->id }}"
                                                                                            title="{{ $fieldName ?: 'Außendienst wählen' }}">
                                                                                            @if(!empty($p->fname) || !empty($p->flastname))
                                                                                                <img src="{{ $empImg($p->fimage, $p->fgender) }}" alt="{{ $fieldName }}">
                                                                                            @else
                                                                                                <div class="kanban-empty-avatar">
                                                                                                    <i class="feather icon-user"></i>
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="kanban-flow-body">
                                                                                        <div class="kanban-product-title">{{ $productTitle }}</div>
                                                                                        <div class="kanban-product-meta">{{ $departmentName }}</div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="alert alert-light mt-2 mb-0">
                                            <i class="feather icon-info mr-1"></i>
                                            Ziehen Sie die Karte in eine andere Spalte, um den Status zu ändern oder die Verifizierung zu starten.
                                        </div>
                                    </div>

                                    {{-- TAB 2: PRODUKTE --}}
                                    <div class="tab-pane fade" id="product-view" role="tabpanel">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                            <h5 class="mb-0">Produktliste</h5>
                                            @if($data->status != "Published")
                                                <button type="button" class="btn btn-success mt-1 mt-sm-0" id="addNewProductRow">
                                                    <i class="feather icon-plus"></i> Produkt hinzufügen
                                                </button>
                                            @endif
                                        </div>

                                        <form id="tabProductForm">
                                            @csrf
                                            <input type="hidden" name="inquiry_id" value="{{ $data->id }}">

                                            <div class="inq-table-wrap table-responsive">
                                                <table class="table table-hover table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="min-width:150px;">Produkt</th>
                                                            <th style="min-width:150px;">Service</th>
                                                            <th style="min-width:150px;">Abteilung</th>
                                                            <th style="min-width:200px;">Innendienst</th>
                                                            <th style="min-width:200px;">Außendienst</th>
                                                            <th style="min-width:150px;">Termin</th>
                                                            <th style="width:50px;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="productRowsTab">
                                                        @forelse ($productList as $row)
                                                            @php
    $male = asset('images/gender/male.png');
    $female = asset('images/gender/female.png');
    $empImg = fn($img, $gender) => ($img && file_exists(public_path('images/employee/' . $img)))
        ? asset('images/employee/' . $img)
        : (strtolower($gender ?? '') === 'female' ? $female : $male);
                                                            @endphp
                                                            <tr data-row-id="{{ $row->id }}">
                                                                <td class="align-middle font-weight-bold">{{ $products->firstWhere('id', $row->product_id)?->article_group }}</td>
                                                                <td class="align-middle">{{ $row->phase_section }}</td>
                                                                <td class="align-middle">{{ $departments->firstWhere('id', $row->department_id)?->department_name }}</td>

                                                                <td class="align-middle">
                                                                    <div class="d-flex align-items-center select-employee" style="cursor:pointer" data-type="employee" data-id="{{ $row->id }}">
                                                                        <img src="{{ $empImg($row->eimage, $row->egender) }}" class="avatar-sm mr-2">
                                                                        <span>{{ $row->ename ?? 'Wählen' }} {{ $row->elastname ?? '' }}</span>
                                                                    </div>
                                                                </td>

                                                                <td class="align-middle">
                                                                    <div class="d-flex align-items-center select-employee" style="cursor:pointer" data-type="field_employee" data-id="{{ $row->id }}">
                                                                        <img src="{{ $empImg($row->fimage, $row->fgender) }}" class="avatar-sm mr-2">
                                                                        <span>{{ $row->fname ?? 'Wählen' }} {{ $row->flastname ?? '' }}</span>
                                                                    </div>
                                                                </td>

                                                                <td class="align-middle">
                                                                    @if(!empty($row->appointment_date))
                                                                        <span class="badge badge-light-primary">{{ \Carbon\Carbon::parse($row->appointment_date)->format('d.m.Y H:i') }}</span>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>

                                                                <td class="align-middle text-center">
                                                                    <button type="button" class="btn btn-icon btn-flat-danger delete-tab-product" data-id="{{ $row->id }}" data-product="{{ $row->product_id }}">
                                                                        <i class="feather icon-trash-2"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center text-muted p-4">Keine Produkte vorhanden.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="text-right mt-3">
                                                <button type="submit" class="btn btn-primary">Alle Speichern</button>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- TAB 3: VERIFIZIERUNG --}}
                                    <div class="tab-pane fade" id="verify-view" role="tabpanel">
                                        @php
$fullAddress = trim(($data->street ?? '') . ' ' . ($data->postcode ?? '') . ' ' . ($data->city ?? ''));
$lead = DB::table('new_leads')
    ->whereNull('deleted_at')
    ->whereRaw('LOWER(name) = ?', [strtolower($data->name ?? '')])
    ->whereRaw('LOWER(lastname) = ?', [strtolower($data->lastname ?? '')])
    ->whereRaw('LOWER(full_address) = ?', [strtolower($fullAddress)])
    ->first();

$targetType = null;
$targetLabel = null;
$targetUrl = null;
if ($lead) {
    $targetType = 'Lead';
    $targetLabel = 'Lead #' . $lead->id;
    $targetUrl = url('new_lead_profile/' . $lead->id);
} else {
    $dName = $data->firma ?: trim(($data->name ?? '') . ' ' . ($data->lastname ?? ''));
    $dist = DB::table('distributors')->whereRaw('LOWER(name) = ?', [strtolower($dName)])->first();
    $brand = DB::table('brands')->whereRaw('LOWER(name) = ?', [strtolower($dName)])->first();
    if ($dist) {
        $targetType = 'Lieferant';
        $targetLabel = $dist->name;
        $targetUrl = route('distributors.index');
    } elseif ($brand) {
        $targetType = ($brand->type === 'brand' ? 'Hersteller' : $brand->type);
        $targetLabel = $brand->name;
        $targetUrl = route('brand.info');
    }
}
$canReverify = in_array($data->status, ['Published', 'verified']);
                                        @endphp

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="inq-verify-box primary">
                                                    <h6 class="text-primary font-weight-bold">Aktueller Status</h6>
                                                    <hr>
                                                    <dl class="row mb-0">
                                                        <dt class="col-sm-4">Verifiziert als:</dt>
                                                        <dd class="col-sm-8">
                                                            <span class="badge badge-lg badge-primary">{{ $data->pre_type ?? 'Unbekannt' }}</span>
                                                        </dd>

                                                        <dt class="col-sm-4">Verknüpfung:</dt>
                                                        <dd class="col-sm-8">
                                                            @if($targetType)
                                                                <strong class="text-success">{{ $targetType }}: {{ $targetLabel }}</strong>
                                                                @if($targetUrl)
                                                                    <a href="{{ $targetUrl }}" target="_blank" class="ml-2 btn btn-sm btn-icon btn-outline-secondary">
                                                                        <i class="feather icon-external-link"></i>
                                                                    </a>
                                                                @endif
                                                            @else
                                                                <span class="text-warning">
                                                                    <i class="feather icon-alert-triangle"></i> Keine Datenbank-Verknüpfung gefunden.
                                                                </span>
                                                            @endif
                                                        </dd>
                                                    </dl>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="inq-verify-box soft">
                                                    <h6 class="font-weight-bold">Korrektur / Neu-Verifizierung</h6>
                                                    <hr>
                                                    @if($canReverify)
                                                        <p class="small text-muted">
                                                            War die automatische Zuordnung falsch? Hier können Sie den Datensatztyp manuell ändern. Die bestehende Verknüpfung wird überschrieben.
                                                        </p>
                                                        <form id="reverifyForm" class="d-flex">
                                                            <select name="type" id="verifyType" class="form-control mr-2">
                                                                <option value="">-- Neuen Typ wählen --</option>
                                                                @foreach(['Lead', 'Lieferant', 'Hersteller', 'Geschäftspartner', 'Architekt', 'Nachunternehmer', 'Bank', 'Versicherung', 'Bewerber', 'others'] as $opt)
                                                                    <option value="{{ $opt }}" {{ $opt === $data->pre_type ? 'disabled' : '' }}>{{ $opt }}</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="submit" class="btn btn-warning text-nowrap">Speichern</button>
                                                        </form>
                                                    @else
                                                        <div class="alert alert-warning mb-0">
                                                            Bitte verifizieren Sie die Anfrage zuerst über das Kanban-Board (ziehen nach "Verifizieren").
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- TAB 4: REPORTS --}}
                                    <div class="tab-pane fade" id="report-view" role="tabpanel">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                            <div>
                                                <h5 class="mb-0">Inquiry Reports</h5>
                                                <small class="text-muted">Reports zur Anfrage (anlegen, bearbeiten, löschen)</small>
                                            </div>

                                            <button type="button" class="btn btn-primary mt-1 mt-sm-0" id="btnAddReport">
                                                <i class="feather icon-plus"></i> Neuer Report
                                            </button>
                                        </div>

                                        <div id="reportsList">
                                            <div class="text-center p-3 text-muted">Noch keine Reports geladen.</div>
                                        </div>
                                    </div>

                                    {{-- TAB 5: KOMMENTARE --}}
                                    <div class="tab-pane fade" id="comment-view" role="tabpanel">
                                        <form id="commentForm" class="mb-4">
                                            <div id="quill-editor" style="height:100px;background:white;"></div>
                                            <div class="text-right mt-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="feather icon-send"></i> Senden
                                                </button>
                                            </div>
                                        </form>

                                        <div id="comments-section" class="mt-4"></div>
                                    </div>

                                    {{-- TAB 6: LOGS --}}
                                    <div class="tab-pane fade" id="activity-view" role="tabpanel">
                                        <h5 class="mb-3">Verlauf & Benachrichtigungen</h5>
                                        <div id="notification-content" class="timeline-wrapper">
                                            <div class="text-center p-3">
                                                <i class="feather icon-loader fa-spin"></i> Lade...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /main --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- REPORT MODAL --}}
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalTitle">Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="report_id" value="">

                <div class="row">
                    <div class="col-md-6">
                        <label class="small text-muted mb-1">Report Datum</label>
                        <input type="datetime-local" class="form-control" id="report_date">
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted mb-1">Fällig bis</label>
                        <input type="datetime-local" class="form-control" id="due_date">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="small text-muted mb-1">Report Inhalt</label>
                    <div id="report-quill" style="height:180px;background:#fff;"></div>
                </div>

                <div class="mt-3">
                    <label class="small text-muted mb-1">Meta (optional JSON)</label>
                    <textarea class="form-control" id="report_meta" rows="3" placeholder='{"key":"value"}'></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-success" id="btnSaveReport">
                    <i class="feather icon-save"></i> Speichern
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // --- 1. KANBAN LOGIC ---
       document.addEventListener('DOMContentLoaded', function () {
        ['Unpublished', 'progress', 'verified', 'junk', 'Draft'].forEach(status => {
            const list = document.getElementById('kanban-list-' + status);
            if (!list) return;

            new Sortable(list, {
            group: 'kanban-group',
            animation: 150,
            draggable: '.draggable',
            handle: '.draggable',
            ghostClass: 'bg-light-secondary',
            onAdd: function (evt) {
                const id = evt.item?.dataset?.id;
                const newStatus = evt.to?.dataset?.status;

                if (!id || !newStatus) return;

                if (newStatus === 'verified') {
                const card = evt.item;
                const alreadyVerified = card.dataset.alreadyVerified === '1';

                if (alreadyVerified) {
                    updateStatus(id, 'verified');
                    Swal.fire({ icon:'info', title:'Bereits verifiziert', timer:1500, showConfirmButton:false });
                    return;
                }

                Swal.fire({
                    title: 'Anfrage verifizieren',
                    input: 'select',
                    inputOptions: {
                    'Lead': 'Lead', 'Kunde': 'Kunde', 'Lieferant': 'Lieferant',
                    'Hersteller': 'Hersteller', 'Geschäftspartner': 'Geschäftspartner',
                    'Architekt': 'Architekt', 'Nachunternehmer': 'Nachunternehmer',
                    'Bank': 'Bank', 'Versicherung': 'Versicherung', 'Bewerber': 'Bewerber',
                    'others': 'Sonstiges'
                    },
                    inputPlaceholder: 'Wählen Sie einen Typ',
                    showCancelButton: true,
                    confirmButtonText: 'Verifizieren',
                    cancelButtonText: 'Abbrechen',
                    inputValidator: (value) => !value ? 'Bitte wählen Sie einen Typ!' : null
                }).then((result) => {
                    if (!result.isConfirmed) return location.reload();

                    $.ajax({
                    url: `/inquiry/${id}/verify`,
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content'), type: result.value, option: result.value },
                    success: function(res) {
                        Swal.fire({ icon:'success', title:'Verifiziert!', timer:1500, showConfirmButton:false })
                        .then(() => {
                            if(res.redirect_url) window.location.href = res.redirect_url;
                            else if(res.lead_id) window.location.href = `{{ route('new.lead.view') }}?highlight_id=${res.lead_id}`;
                            else location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire('Fehler', 'Validierung fehlgeschlagen', 'error').then(() => location.reload());
                    }
                    });
                });

                } else {
                updateStatus(id, newStatus);
                }
            }
            });
        });
        });


        function updateStatus(id, status) {
            fetch(`/inquiry/${id}/status`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ status })
            }).then(res => res.json()).then(res => {
                if (!res.success) Swal.fire('Fehler', "Konnte nicht gespeichert werden", 'error');
                else {
                     const badge = document.getElementById('status');
                     if(badge) badge.textContent = status;
                }
            });
        }

        // --- 2. PRODUCT TAB LOGIC ---
        const SERVICES = @json($serviceList);
        const PRODUCTS = @json($products);
        const DEPARTMENTS = @json($departments);
        const URL_EMPLOYEES = '{{ route("inquiry.department.employees") }}';

        let tabRowIndex = 1000;
        function buildRow(index) {
            const productOpts = PRODUCTS.map(p => `<option value="${p.id}">${p.article_group}</option>`).join('');
            const deptOpts    = DEPARTMENTS.map(d => `<option value="${d.id}">${d.department_name}</option>`).join('');
            return `
                <tr data-index="${index}">
                    <td><select name="product_id[]" class="form-control tab-product" data-index="${index}"><option value="">Produkt wählen</option>${productOpts}</select></td>
                    <td><select name="service_id[]" class="form-control tab-service" data-index="${index}"><option value="">Erst Produkt wählen</option></select></td>
                    <td><select name="department_id[]" class="form-control tab-department" data-index="${index}"><option value="">Abteilung wählen</option>${deptOpts}</select></td>
                    <td><select name="employee_id[]" class="form-control tab-employee" data-index="${index}"><option value="">Innendienst</option></select></td>
                    <td><select name="field_employee[]" class="form-control tab-field" data-index="${index}"><option value="">Außendienst</option></select></td>
                    <td><input type="datetime-local" name="appointment_date[]" class="form-control" data-index="${index}"></td>
                    <td class="text-center"><button type="button" class="btn btn-icon btn-flat-danger removeRow"><i class="feather icon-trash"></i></button></td>
                </tr>`;
        }

        function initRow(index) {
            const $el = (sel) => $(`.${sel}[data-index="${index}"]`);
            ['tab-product', 'tab-service', 'tab-department', 'tab-employee', 'tab-field'].forEach(c => $el(c).select2({width:'100%'}));

            $el('tab-product').on('change', function() {
                const pid = $(this).val();
                const $srv = $el('tab-service');
                $srv.empty().append('<option value="">Service wählen</option>');
                if(pid) {
                    SERVICES.filter(s => s.product_id == pid).forEach(s => $srv.append(`<option value="${s.id}">${s.phase_section}</option>`));
                    fetchEmployeesAuto(pid, null, null, index);
                }
            });

            const updateEmp = () => fetchEmployeesAuto($el('tab-product').val(), $el('tab-service').val(), $el('tab-department').val(), index);
            $el('tab-service').on('change', updateEmp);
            $el('tab-department').on('change', updateEmp);
        }

        function fetchEmployeesAuto(pid, sid, did, index) {
            if(!pid) return;
            $.post(URL_EMPLOYEES, { _token: '{{ csrf_token() }}', product_id: pid, service_id: sid, department_id: did, stage: 'inquiry' })
            .done(res => {
                const fill = ($s, data) => { 
                    $s.empty().append('<option value="">Wählen</option>'); 
                    (data||[]).forEach(e => $s.append(`<option value="${e.id}">${e.name} ${e.lastname}</option>`)); 
                    $s.trigger('change.select2');
                };
                fill($(`.tab-employee[data-index="${index}"]`), res.internal_employees);
                fill($(`.tab-field[data-index="${index}"]`), res.external_employees?.length ? res.external_employees : res.internal_employees);

                if(res.department_id && !$(`.tab-department[data-index="${index}"]`).val()) {
                    $(`.tab-department[data-index="${index}"]`).val(res.department_id).trigger('change.select2');
                }
            });
        }

        $('#addNewProductRow').click(() => {
            $('#productRowsTab').append(buildRow(++tabRowIndex));
            initRow(tabRowIndex);
        });

        $(document).on('click', '.removeRow', function() { $(this).closest('tr').remove(); });

        $('#tabProductForm').submit(function(e) {
            e.preventDefault();
            let data = $(this).serialize();
            $.post('{{ route("inquiry.products.save") }}', data)
             .done(res => Swal.fire('Gespeichert', res.message, 'success').then(() => location.reload()))
             .fail(xhr => Swal.fire('Fehler', 'Pflichtfelder prüfen', 'error'));
        });

        $('.delete-tab-product').click(function() {
            const id = $(this).data('id');
            const pid = $(this).data('product');
            Swal.fire({ title: 'Löschen?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ja' })
            .then(res => {
                if(res.isConfirmed) {
                    $.ajax({ url: '{{ route("inquiry.products.delete") }}', method: 'DELETE', data: { _token: '{{ csrf_token() }}', id: id, product_id: pid } })
                    .done(() => location.reload());
                }
            });
        });

        // --- 3. INLINE EMPLOYEE ASSIGNMENT (Avatars) ---
        $('.select-employee').click(async function() {
            const type = $(this).data('type');
            const id = $(this).data('id');

            const res = await fetch('/getAllEmployees');
            const emps = await res.json();

            let options = '';
            emps.forEach(e => options += `<option value="${e.emp_id}">${e.name} ${e.lastname}</option>`);

            Swal.fire({
                title: type === 'employee' ? 'Innendienst wählen' : 'Außendienst wählen',
                html: `<select id="swalEmp" class="form-control">${options}</select>`,
                didOpen: () => $('#swalEmp').select2({ dropdownParent: $('.swal2-container'), width: '100%' }),
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                preConfirm: () => {
                    return fetch(`/inquiry-products/${id}/update-employee`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ type: type, employee_id: $('#swalEmp').val() })
                    }).then(response => {
                        if (!response.ok) throw new Error(response.statusText);
                        return response.json();
                    }).catch(error => Swal.showValidationMessage(`Request failed: ${error}`));
                }
            }).then(res => { if(res.isConfirmed) location.reload(); });
        });

        // --- 4. RE-VERIFY FORM ---
        $('#reverifyForm').submit(function(e) {
            e.preventDefault();
            const type = $('#verifyType').val();
            if(!type) return Swal.fire('Fehler', 'Typ wählen', 'warning');

            Swal.fire({ title: 'Sicher?', text: 'Daten werden überschrieben.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ja' })
            .then(res => {
                if(res.isConfirmed) {
                    $.post('{{ url("inquiry/" . $data->id . "/reverify") }}', { _token: '{{ csrf_token() }}', type: type })
                    .done(res => {
                        if(res.redirect_url) window.location.href = res.redirect_url;
                        else if(res.target && res.target.url) window.location.href = res.target.url;
                        else location.reload();
                    })
                    .fail(() => Swal.fire('Fehler', 'Fehlgeschlagen', 'error'));
                }
            });
        });

        // --- 5. COMMENTS & LOGS ---
        var quill = new Quill('#quill-editor', { theme: 'snow', modules: { toolbar: [ ['bold','italic','underline'], [{ 'list':'ordered'},{ 'list':'bullet'}] ] } });

        function loadComments() {
            $.get('/inquiry/{{ $data->id }}/comments', function(data) {
                $('#comments-section').empty();
                data.forEach(c => {
                    $('#comments-section').append(`
                        <div class="comment-card">
                            <div class="d-flex justify-content-between">
                                <strong>${c.employee?.name || 'User'}</strong>
                                <small class="text-muted">${new Date(c.created_at).toLocaleString()}</small>
                            </div>
                            <div class="mt-2">${c.comment}</div>
                        </div>
                    `);
                });
            });
        }

        $('#commentForm').submit(function(e) {
            e.preventDefault();
            $.post('/inquiry/{{ $data->id }}/comments', { _token: '{{ csrf_token() }}', comment: quill.root.innerHTML })
            .done(() => { quill.setContents([]); loadComments(); Swal.fire('Gesendet', '', 'success'); });
        });

        $('a[href="#comment-view"]').on('shown.bs.tab', loadComments);

        $('a[href="#activity-view"]').on('shown.bs.tab', function() {
            $.get(`/inquiry/get/notification/{{ $data->id }}`, html => $('#notification-content').html(html));
        });
    </script>

    <script>
      // -----------------------------
      // REPORTS (AJAX CRUD)
      // -----------------------------
      const INQUIRY_ID = {{ (int) $data->id }};
      const REPORTS_INDEX_URL  = "{{ route('inquiry.reports.index', ['inquiry' => $data->id]) }}";
      const REPORTS_STORE_URL  = "{{ route('inquiry.reports.store', ['inquiry' => $data->id]) }}";
      const REPORTS_UPDATE_URL = "{{ route('inquiry.reports.update', ['report' => 0]) }}";
      const REPORTS_DELETE_URL = "{{ route('inquiry.reports.destroy', ['report' => 0]) }}";

      const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      function toLocalInputValue(dt) {
        if (!dt) return '';
        const d = new Date(dt);
        const pad = (n) => String(n).padStart(2,'0');
        return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
      }

      function escapeHtml(s){
        return String(s ?? '')
          .replaceAll('&','&amp;')
          .replaceAll('<','&lt;')
          .replaceAll('>','&gt;')
          .replaceAll('"','&quot;')
          .replaceAll("'","&#039;");
      }

      let reportQuill;
      document.addEventListener('DOMContentLoaded', function(){
        reportQuill = new Quill('#report-quill', {
          theme: 'snow',
          modules: { toolbar: [ ['bold','italic','underline'], [{ list:'ordered'},{ list:'bullet'}], ['link'] ] }
        });

        $('a[href="#report-view"]').on('shown.bs.tab', loadReports);

        $('#btnAddReport').on('click', function(){
          openReportModalCreate();
        });

        $('#btnSaveReport').on('click', saveReport);

        $(document).on('click', '.btn-edit-report', function(){
          const raw = $(this).attr('data-report');
          const report = JSON.parse(raw);
          openReportModalEdit(report);
        });

        $(document).on('click', '.btn-delete-report', function(){
          const id = $(this).data('id');
          deleteReport(id);
        });
      });

      function loadReports(){
        $('#reportsList').html(`<div class="text-center p-3"><i class="feather icon-loader fa-spin"></i> Lade...</div>`);

        $.get(REPORTS_INDEX_URL)
          .done(res => {
            const rows = (res && res.data) ? res.data : [];
            if (!rows.length) {
              $('#reportsList').html(`<div class="text-center p-3 text-muted">Keine Reports vorhanden.</div>`);
              return;
            }

            const html = rows.map(r => {
              const reporter = r.reporter ? `${escapeHtml(r.reporter.name ?? '')} ${escapeHtml(r.reporter.lastname ?? '')}`.trim() : '—';
              const repDate = r.report_date ? new Date(r.report_date).toLocaleString() : '—';
              const dueDate = r.due_date ? new Date(r.due_date).toLocaleString() : '—';
              const metaStr = r.meta ? escapeHtml(JSON.stringify(r.meta)) : '';
              const payload = escapeHtml(JSON.stringify(r));

              return `
                <div class="report-card">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="meta"><strong>Reporter:</strong> ${reporter}</div>
                      <div class="meta"><strong>Report Datum:</strong> ${repDate}</div>
                      <div class="meta"><strong>Fällig:</strong> ${dueDate}</div>
                      ${metaStr ? `<div class="meta mt-1"><strong>Meta:</strong> ${metaStr}</div>` : ''}
                    </div>

                    <div class="report-actions text-right">
                      <button type="button" class="btn btn-sm btn-outline-primary btn-edit-report" data-report="${payload}">
                        <i class="feather icon-edit"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger btn-delete-report" data-id="${r.id}">
                        <i class="feather icon-trash-2"></i>
                      </button>
                    </div>
                  </div>

                  <hr class="my-2">
                  <div class="report-body">${r.report ?? ''}</div>
                </div>
              `;
            }).join('');

            $('#reportsList').html(html);
          })
          .fail(() => {
            $('#reportsList').html(`<div class="alert alert-danger">Reports konnten nicht geladen werden.</div>`);
          });
      }

      function openReportModalCreate(){
        $('#reportModalTitle').text('Neuer Report');
        $('#report_id').val('');
        $('#report_date').val(toLocalInputValue(new Date().toISOString()));
        $('#due_date').val('');
        $('#report_meta').val('');
        reportQuill.root.innerHTML = '';
        $('#reportModal').modal('show');
      }

      function openReportModalEdit(r){
        $('#reportModalTitle').text('Report bearbeiten');
        $('#report_id').val(r.id);
        $('#report_date').val(toLocalInputValue(r.report_date));
        $('#due_date').val(toLocalInputValue(r.due_date));
        $('#report_meta').val(r.meta ? JSON.stringify(r.meta) : '');
        reportQuill.root.innerHTML = r.report ?? '';
        $('#reportModal').modal('show');
      }

      function saveReport(){
        const id = $('#report_id').val();
        const payload = {
          _token: csrf,
          report_date: $('#report_date').val() || null,
          due_date: $('#due_date').val() || null,
          report: reportQuill.root.innerHTML || null,
          meta: $('#report_meta').val() || null,
        };

        const isEdit = !!id;
        const url = isEdit ? REPORTS_UPDATE_URL.replace('/0', '/' + id) : REPORTS_STORE_URL;
        const method = isEdit ? 'PUT' : 'POST';

        $.ajax({
          url,
          method,
          data: payload
        })
        .done(() => {
          $('#reportModal').modal('hide');
          Swal.fire({ icon:'success', title:'Gespeichert', timer:1200, showConfirmButton:false });
          loadReports();
        })
        .fail(xhr => {
          const msg = xhr?.responseJSON?.message || 'Speichern fehlgeschlagen.';
          Swal.fire('Fehler', msg, 'error');
        });
      }

      function deleteReport(id){
        Swal.fire({
          title: 'Report löschen?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ja, löschen',
          cancelButtonText: 'Abbrechen'
        }).then(r => {
          if (!r.isConfirmed) return;

          $.ajax({
            url: REPORTS_DELETE_URL.replace('/0', '/' + id),
            method: 'DELETE',
            data: { _token: csrf }
          })
          .done(() => {
            Swal.fire({ icon:'success', title:'Gelöscht', timer:1000, showConfirmButton:false });
            loadReports();
          })
          .fail(() => Swal.fire('Fehler', 'Löschen fehlgeschlagen.', 'error'));
        });
      }
    </script>

    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Anfrageliste',
                url: "{{ url('inquiry_view') }}",
            },
            {
                label: '{{ $data->firma ?? '' }} - {{ $data->name ?? '' }} {{ $data->lastname ?? '' }}',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endsection