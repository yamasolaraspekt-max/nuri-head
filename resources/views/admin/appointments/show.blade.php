@extends('admin.layouts.app')

@section('title', 'Termin – Profil')

@section('style')
    {{-- Quill editor --}}
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

    <style>
        :root {
            --app-green: #93c21c;
            --app-green-soft: #cfe09b;
            --app-blue: #74b2d4;
            --app-blue-soft: #e3effb;
            --app-black: #020617;
            --app-white: #ffffff;

            --radius-lg: 14px;
            --radius-xl: 18px;
            --shadow-soft: 0 18px 50px rgba(15, 23, 42, 0.14);
        }

        body {
            background: #f3f4f6;
        }

        .appointment-profile-page {
            max-width: 2000px;
            margin: 1.5rem auto;
            padding: 1.25rem 1.5rem 2rem;
            background: var(--app-white);
            border-radius: 24px;
            box-shadow: var(--shadow-soft);
        }

        .ap-header {
            display: flex;
            flex-wrap: wrap;
            gap: 0.9rem;
            align-items: flex-start;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.9rem;
            margin-bottom: 1.1rem;
        }

        .ap-header-main {
            flex: 1;
            min-width: 0;
        }

        .ap-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--app-black);
            margin: 0 0 0.1rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .ap-title-color-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            box-shadow: 0 0 0 2px #e5e7eb;
        }

        .ap-subline {
            font-size: 0.82rem;
            color: #6b7280;
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            align-items: center;
        }

        .ap-subline span {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .ap-tag {
            padding: 0.1rem 0.6rem;
            border-radius: 999px;
            font-size: 0.7rem;
            border: 1px solid rgba(148, 163, 184, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            background: rgba(248, 250, 252, 0.9);
        }

        .ap-badge-status {
            border-radius: 999px;
            padding: 0.18rem 0.7rem;
            font-size: 0.75rem;
            border: 1px solid #e5e7eb;
            text-transform: capitalize;
        }

        .ap-badge-status--planned {
            background: #e0f2fe;
            border-color: #60a5fa;
            color: #1d4ed8;
        }

        .ap-badge-status--in_progress {
            background: #ede9fe;
            border-color: #a855f7;
            color: #5b21b6;
        }

        .ap-badge-status--done {
            background: var(--app-green-soft);
            border-color: var(--app-green);
            color: #14532d;
        }

        .ap-badge-status--archived {
            background: #f3f4f6;
            border-color: #9ca3af;
            color: #4b5563;
        }

        .ap-badge-status--junk {
            background: #fee2e2;
            border-color: #ef4444;
            color: #b91c1c;
        }

        .ap-badge-priority {
            border-radius: 999px;
            padding: 0.16rem 0.6rem;
            font-size: 0.72rem;
            border: 1px solid #e5e7eb;
            text-transform: capitalize;
            background: #f9fafb;
            color: #4b5563;
        }

        .ap-badge-priority--veryhigh,
        .ap-badge-priority--high {
            background: #fef3c7;
            border-color: #f97316;
            color: #92400e;
        }

        .ap-badge-priority--medium {
            background: #dcfce7;
            border-color: #22c55e;
            color: #166534;
        }

        .ap-time-badge {
            border-radius: 999px;
            padding: 0.18rem 0.7rem;
            font-size: 0.72rem;
            border: 1px solid rgba(148, 163, 184, 0.6);
            background: linear-gradient(90deg, var(--app-blue-soft), #f9fafb);
            color: #1f2937;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .ap-header-actions {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            align-items: flex-end;
        }

        .ap-back-link {
            font-size: 0.75rem;
            color: #6b7280;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
        }

        .ap-back-link:hover {
            color: #111827;
        }

        .ap-primary-btn {
            border-radius: 999px;
            border: none;
            padding: 0.35rem 0.85rem;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(135deg, var(--app-green), var(--app-blue));
            color: var(--app-white);
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.25);
        }

        .ap-select {
            border-radius: 999px;
            padding: 0.28rem 0.7rem;
            border: 1px solid #e5e7eb;
            font-size: 0.76rem;
            background: #f9fafb;
            color: #111827;
        }

        /* Layout */

        .ap-layout {
            display: grid;
            grid-template-columns: minmax(0, 2.1fr) minmax(0, 2fr);
            gap: 0.9rem;
        }

        @media (max-width: 960px) {
            .ap-layout {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .ap-card {
            background: #f9fafb;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            padding: 0.85rem 0.95rem;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 0.7rem;
        }

        .ap-card-title {
            font-size: 0.82rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #6b7280;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Reports */

        .ap-report-editor-wrapper {
            border-radius: 12px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            overflow: hidden;
        }

        .ap-report-editor-wrapper .ql-toolbar {
            border: none;
            border-bottom: 1px solid #e5e7eb;
        }

        .ap-report-editor-wrapper .ql-container {
            border: none;
        }

        .ap-report-editor-wrapper .ql-editor {
            min-height: 80px;
            font-size: 0.8rem;
        }

        .ap-report-form-footer {
            margin-top: 0.4rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 0.4rem;
        }

        .ap-report-form-footer .ap-inline-inputs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            align-items: center;
        }

        .ap-inline-text-input {
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 0.25rem 0.6rem;
            font-size: 0.78rem;
            background: #ffffff;
        }

        .ap-date-input {
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 0.25rem 0.6rem;
            font-size: 0.78rem;
            background: #ffffff;
        }

        .ap-report-list {
            margin-top: 0.7rem;
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            max-height: 340px;
            overflow-y: auto;
        }

        .ap-report-item {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 0.55rem 0.7rem;
            font-size: 0.78rem;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 0.4rem;
        }

        .ap-avatar {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #e5e7eb;
        }

        .ap-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .ap-report-meta {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 0.4rem;
            margin-bottom: 0.15rem;
        }

        .ap-report-author {
            font-weight: 600;
            color: #111827;
            font-size: 0.78rem;
        }

        .ap-report-time {
            font-size: 0.7rem;
            color: #9ca3af;
        }

        .ap-report-content {
            color: #4b5563;
            font-size: 0.78rem;
        }

        .ap-report-content p {
            margin: 0 0 0.15rem;
        }

        .ap-report-next {
            margin-top: 0.25rem;
            padding: 0.3rem 0.45rem;
            border-radius: 10px;
            background: #eff6ff;
            border: 1px dashed #bfdbfe;
            font-size: 0.75rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            align-items: center;
            color: #1f2937;
        }

        .ap-report-next-label {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 0.7rem;
            color: #1d4ed8;
        }

        .ap-report-next-due {
            margin-left: auto;
            font-size: 0.72rem;
            color: #b45309;
        }

        .ap-report-actions {
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
        }

        .ap-reaction-btn {
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            padding: 0.18rem 0.55rem;
            background: #f9fafb;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            color: #4b5563;
        }

        .ap-reaction-btn.is-active {
            background: #e0f2fe;
            border-color: #3b82f6;
            color: #1d4ed8;
        }

        .ap-reaction-count {
            font-weight: 600;
        }

        /* Notification timeline */

        .ap-timeline {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            max-height: 240px;
            overflow-y: auto;
        }

        .ap-timeline-item {
            display: grid;
            grid-template-columns: 16px 1fr;
            gap: 0.4rem;
            font-size: 0.76rem;
        }

        .ap-timeline-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            margin-top: 0.2rem;
            background: #94a3b8;
        }

        .ap-timeline-dot--due {
            background: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.3);
        }

        .ap-timeline-dot--status {
            background: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.32);
        }

        .ap-timeline-dot--created {
            background: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.28);
        }

        .ap-timeline-title {
            font-weight: 500;
            color: #111827;
        }

        .ap-timeline-text {
            color: #6b7280;
        }

        .ap-timeline-meta {
            font-size: 0.7rem;
            color: #9ca3af;
            margin-top: 0.05rem;
        }

        /* Details */

        .ap-detail-row {
            display: grid;
            grid-template-columns: 120px minmax(0, 1fr);
            gap: 0.35rem 0.6rem;
            font-size: 0.78rem;
        }

        .ap-detail-label {
            color: #6b7280;
        }

        .ap-detail-value {
            color: #111827;
        }

        /* Employees */

        .ap-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .ap-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.55rem 0.2rem 0.25rem;
            border-radius: 999px;
            background: #e5f4d5;
            border: 1px solid #a3e635;
            font-size: 0.75rem;
            color: #194125;
        }

        .ap-chip .ap-avatar-small {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.8);
            background: #e5e7eb;
        }

        .ap-chip .ap-avatar-small img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .ap-chip-remove {
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 0.8rem;
            cursor: pointer;
        }

        .ap-employee-add-row {
            margin-top: 0.5rem;
            display: flex;
            gap: 0.3rem;
            align-items: center;
        }

        .ap-employee-select {
            flex: 1;
            border-radius: 999px;
            padding: 0.25rem 0.6rem;
            border: 1px solid #d1d5db;
            font-size: 0.78rem;
            background: #ffffff;
        }

        .ap-secondary-btn {
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            padding: 0.3rem 0.7rem;
            font-size: 0.76rem;
            background: #0f172a;
            color: #e5e7eb;
            cursor: pointer;
        }

        /* Comments */

        .ap-comment-form textarea {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            padding: 0.45rem 0.6rem;
            font-size: 0.8rem;
            resize: vertical;
            min-height: 60px;
            background: #ffffff;
        }

        .ap-comment-list {
            margin-top: 0.7rem;
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            max-height: 260px;
            overflow-y: auto;
        }

        .ap-comment-item {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.65rem;
            font-size: 0.78rem;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 0.35rem;
        }

        .ap-comment-meta {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 0.4rem;
            margin-bottom: 0.05rem;
        }

        .ap-comment-author {
            font-weight: 600;
            color: #111827;
        }

        .ap-comment-time {
            font-size: 0.7rem;
            color: #9ca3af;
        }

        .ap-comment-text {
            color: #4b5563;
            white-space: pre-line;
        }

        .ap-icon-svg {
            width: 14px;
            height: 14px;
            display: inline-block;
        }

        .ap-report-comments {
            margin-top: 0.4rem;
            padding-top: 0.35rem;
            border-top: 1px dashed #e5e7eb;
        }

        .ap-report-comment-list {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            max-height: 120px;
            overflow-y: auto;
            margin-bottom: 0.35rem;
        }

        .ap-report-comment-item {
            font-size: 0.74rem;
            background: #f9fafb;
            border-radius: 10px;
            padding: 0.3rem 0.45rem;
            border: 1px solid #e5e7eb;
        }

        .ap-report-comment-author {
            font-weight: 600;
            color: #111827;
        }

        .ap-report-comment-time {
            font-size: 0.68rem;
            color: #9ca3af;
            margin-left: 0.4rem;
        }

        .ap-report-comment-text {
            color: #4b5563;
            margin-top: 0.05rem;
            white-space: pre-line;
        }

        .ap-report-comment-form {
            display: flex;
            gap: 0.3rem;
            align-items: flex-start;
        }

        .ap-report-comment-input {
            flex: 1;
            font-size: 0.76rem;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            padding: 0.25rem 0.4rem;
            resize: vertical;
            min-height: 34px;
        }

        .ap-report-comment-save-btn {
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            padding: 0.25rem 0.7rem;
            font-size: 0.74rem;
            background: #0f172a;
            color: #e5e7eb;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .ap-report-header-meta {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .ap-report-stats {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .ap-report-count {
            font-size: 0.76rem;
            color: #4b5563;
            padding: 0.14rem 0.55rem;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.18);
        }

        .ap-report-author-avatars {
            display: inline-flex;
            align-items: center;
            padding-left: 0.25rem;
        }

        .ap-report-author-avatar {
            width: 24px;
            height: 24px;
            border-radius: 999px;
            overflow: hidden;
            border: 2px solid #f9fafb;
            margin-left: -6px;
            box-shadow: 0 0 0 1px #e5e7eb;
            background: #e5e7eb;
        }

        .ap-report-author-avatar:first-child {
            margin-left: 0;
        }

        .ap-report-author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .ap-report-search-input {
            border-radius: 999px;
            border: 1px solid #d1d5db;
            padding: 0.22rem 0.7rem;
            font-size: 0.76rem;
            min-width: 180px;
            background: #ffffff;
        }

        .ap-creator-badge{
            gap: .45rem;
            }

            .ap-creator-avatar{
            width: 20px;
            height: 20px;
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid rgba(148,163,184,.7);
            background: #e5e7eb;
            flex: 0 0 auto;
            }

            .ap-creator-avatar img{
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            }

            .ap-creator-badge{
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            }

            .ap-creator-avatar{
            width: 18px;
            height: 18px;
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid rgba(148,163,184,.7);
            background: #e5e7eb;
            flex: 0 0 auto;
            }
            .ap-creator-avatar img{
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            }

            /* =========================
            Unified badge sizing
            ========================= */
            .ap-badge-status,
            .ap-badge-priority,
            .ap-time-badge{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 30px;                 /* <- one size */
            padding: 0 12px;              /* <- same horizontal padding */
            border-radius: 999px;
            font-size: 12px;              /* <- same text size */
            line-height: 1;
            gap: 6px;
            white-space: nowrap;
            }

            /* icons / emoji consistent size */
            .ap-badge-status i,
            .ap-badge-priority i,
            .ap-time-badge i{
            font-size: 13px;
            line-height: 1;
            }

            /* Creator badge avatar fits same height */
            .ap-creator-badge{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            }

            .ap-creator-avatar{
            width: 20px;                  /* inside 30px chip */
            height: 20px;
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid rgba(148,163,184,.7);
            background: #e5e7eb;
            flex: 0 0 auto;
            }
            .ap-creator-avatar img{
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            }

            /* badges row alignment */
            .ap-badges-row{
            display: flex;
            flex-wrap: wrap;
            align-items: center;  /* <- fixes vertical alignment */
            gap: .4rem;
            }

            /* ensure creator badge uses the same baseline as others */
            .ap-time-badge.ap-creator-badge{
            gap: 8px;
            }

            /* avatar centered inside the chip */
            .ap-time-badge.ap-creator-badge .ap-creator-avatar{
            align-self: center;
            display: inline-flex;
            }




        @media (max-width: 960px) {
            .ap-report-header-meta {
                justify-content: space-between;
                width: 100%;
            }

            .ap-report-search-input {
                width: 100%;
            }
        }
    </style>
@endsection
@php
    $creatorUser = $appointment->createdBy;
    $creatorEmp  = $creatorUser?->employee;

    $creatorName = $creatorEmp
        ? trim($creatorEmp->name.' '.$creatorEmp->lastname)
        : ($creatorUser->name ?? '–');

    $creatorImg = ($creatorEmp && $creatorEmp->image)
        ? asset('images/employee/'.$creatorEmp->image)
        : asset('images/gender/male.png');
@endphp

@section('content')
    @php
        $authUser       = auth()->user();
        $authEmployeeId = optional($authUser)->name ?? optional($authUser)->id;
        $employeeMap    = $allEmployees->keyBy('id');
    @endphp

    {{-- simple icon sprite for report comment button --}}
    <svg width="0" height="0" style="position:absolute;visibility:hidden">
        <symbol id="icon-comment" viewBox="0 0 20 20">
            <path d="M3 3h14a1 1 0 0 1 1 1v9.5a1 1 0 0 1-1 1H9l-3.5 3.5A1 1 0 0 1 4 17v-2H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" />
        </symbol>
    </svg>

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper">
            <div class="content-header row">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Terminprofil</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('customer.appointments.index') }}">Termine</a>
                            </li>
                            <li class="breadcrumb-item active">
                                #{{ $appointment->id }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <div class="appointment-profile-page"
                     data-appointment-id="{{ $appointment->id }}"
                     data-status="{{ $appointment->status ?? 'planned' }}">

                    {{-- HEADER --}}
                    <div class="ap-header">
                        <div class="ap-header-main">
                            <div class="ap-title">
                                @if($appointment->color)
                                    <span class="ap-title-color-dot" style="background: {{ $appointment->color }};"></span>
                                @endif
                                <span>{{ $appointment->name }}</span>
                            </div>

                            <div class="ap-subline">
                                <span>
                                    <i class="fa fa-calendar"></i>
                                    {{ $appointment->start_date?->format('d.m.Y') }}
                                    @if($appointment->start_time)
                                        – {{ \Illuminate\Support\Str::of($appointment->start_time)->substr(0,5) }}
                                    @endif
                                    @if($appointment->end_date || $appointment->end_time)
                                        &nbsp;→
                                        {{ $appointment->end_date?->format('d.m.Y') ?? '' }}
                                        @if($appointment->end_time)
                                            {{ \Illuminate\Support\Str::of($appointment->end_time)->substr(0,5) }}
                                        @endif
                                    @endif
                                </span>

                                @if($appointment->customer)
                                    <span>
                                        <i class="fa fa-user"></i>
                                        {{ $appointment->customer->name }} {{ $appointment->customer->lastname }}
                                    </span>
                                @endif

                                @if($appointment->city || $appointment->street)
                                    <span>
                                        <i class="fa fa-map-marker"></i>
                                        {{ $appointment->street }}
                                        @if($appointment->postcode || $appointment->city)
                                            , {{ $appointment->postcode }} {{ $appointment->city }}
                                        @endif
                                    </span>
                                @endif
                            </div>

                            <div class="mt-50 ap-badges-row">

                                <span class="ap-badge-status ap-badge-status--{{ $appointment->status ?? 'planned' }}">
                                    {{ $appointment->status ?? 'planned' }}
                                </span>

                                @php
                                    $priorityKey = strtolower(str_replace(' ', '', $appointment->priority ?? ''));
                                @endphp
                                @if($appointment->priority)
                                    <span class="ap-badge-priority ap-badge-priority--{{ $priorityKey }}">
                                        Priorität: {{ $appointment->priority }}
                                    </span>
                                @endif

                                @if($timeBadgeLabel)
                                    <span class="ap-time-badge">
                                        <i class="fa fa-clock-o"></i>
                                       Erstellt am: {{ \Carbon\Carbon::parse($appointment->created_at) }}
                                    </span>
                                @endif

                               <span class="ap-time-badge ap-creator-badge" title="Erstellt von {{ $creatorName }}">
                                    <span class="ap-creator-avatar">
                                        <img src="{{ $creatorImg }}" alt="{{ $creatorName }}">
                                    </span>
                                    <span>Erstellt von: {{ $creatorName }}</span>
                                </span>

                            </div>

                        </div>

                        <div class="ap-header-actions">
                            <a href="{{ route('customer.appointments.index') }}" class="ap-back-link">
                                <i class="fa fa-angle-left"></i> Zurück zur Übersicht
                            </a>

                            {{-- Status selector --}}
                            <div>
                                <select id="apStatusSelect" class="ap-select">
                                    @foreach(['planned' => 'Geplant', 'in_progress' => 'In Arbeit', 'done' => 'Erledigt', 'archived' => 'Archiviert', 'junk' => 'Junk'] as $key => $label)
                                        <option value="{{ $key }}" @selected(($appointment->status ?? 'planned') === $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="ap-secondary-btn" id="apStatusSaveBtn">
                                    Status speichern
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- MAIN LAYOUT --}}
                    <div class="ap-layout">
                        {{-- LEFT COLUMN: Reports + Notifications --}}
                        <div>
                            @php
                                $reportCount   = $reports->count();
                                $reportAuthors = $reports->pluck('employee')->filter()->unique('id');
                            @endphp

                            {{-- REPORTS --}}
                            <div class="ap-card">
                                <div class="ap-card-title">
                                    <span>Report</span>

                                    <div class="ap-report-header-meta">
                                        <div class="ap-report-stats">
                                            <span class="ap-report-count">
                                                <span id="apReportCountFiltered">{{ $reportCount }}</span> /
                                                <span id="apReportCountTotal">{{ $reportCount }}</span> Reports
                                            </span>

                                            <div class="ap-report-author-avatars">
                                                @foreach($reportAuthors as $author)
                                                    <div class="ap-report-author-avatar"
                                                         title="{{ $author->name }} {{ $author->lastname }}">
                                                        <img src="{{ $author->image
                                                                ? asset('images/employee/'.$author->image)
                                                                : asset('images/employee/default.png') }}"
                                                             alt="{{ $author->name }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="ap-report-search">
                                            <input type="search"
                                                   id="apReportSearchInput"
                                                   class="ap-report-search-input"
                                                   placeholder="Reports durchsuchen …">
                                        </div>
                                    </div>
                                </div>

                                {{-- Report form --}}
                                <form class="ap-report-form" id="apReportForm">
                                    @csrf

                                    <div class="ap-report-editor-wrapper">
                                        <div id="apReportEditor"></div>
                                    </div>

                                    <div class="ap-report-form-footer">
                                        <div class="ap-inline-inputs">
                                            <input type="date"
                                                   name="report_date"
                                                   id="apReportDate"
                                                   class="ap-date-input"
                                                   value="{{ now()->toDateString() }}">

                                            <input type="text"
                                                   name="next_step"
                                                   id="apNextStepInput"
                                                   class="ap-inline-text-input"
                                                   placeholder="Nächster Schritt kurz …">

                                            <input type="date"
                                                   name="due_date"
                                                   id="apDueDateInput"
                                                   class="ap-date-input"
                                                   placeholder="Fälligkeitsdatum">
                                        </div>

                                        <button type="button" class="ap-primary-btn" id="apReportSaveBtn">
                                            Speichern
                                        </button>
                                    </div>
                                </form>

                                {{-- Report list --}}
                                <div class="ap-report-list" id="apReportList">
                                    @foreach($reports as $r)
                                        @php
                                            $reaction = $r->reactionOf($authEmployeeId);
                                            $likes    = $r->likes_count ?? 0;
                                            $dislikes = $r->dislikes_count ?? 0;
                                        @endphp
                                        <div class="ap-report-item" data-report-id="{{ $r->id }}">
                                            <div class="ap-avatar">
                                                <img src="{{ $r->employee->image ? asset('images/employee/'.$r->employee->image) : asset('images/employee/default.png') }}"
                                                     alt="{{ $r->employee->name }}">
                                            </div>
                                            <div>
                                                <div class="ap-report-meta">
                                                    <span class="ap-report-author">
                                                        {{ $r->employee->name }} {{ $r->employee->lastname }}
                                                    </span>
                                                    <span class="ap-report-time">
                                                        {{ $r->created_at->diffForHumans() }}
                                                    </span>
                                                </div>

                                                <div class="ap-report-content">
                                                    {!! $r->report !!}
                                                </div>

                                                @if($r->next_step || $r->due_date)
                                                    <div class="ap-report-next">
                                                        <span class="ap-report-next-label">Next Step</span>
                                                        <span>{{ $r->next_step ?? '–' }}</span>

                                                        @if($r->due_date)
                                                            <span class="ap-report-next-due">
                                                                Fällig: {{ $r->due_date->format('d.m.Y') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- reactions --}}
                                                <div class="ap-report-actions"
                                                     data-report-id="{{ $r->id }}"
                                                     data-reaction="{{ $reaction ?? '' }}">
                                                    <button type="button"
                                                            class="ap-reaction-btn ap-report-like-btn {{ $reaction === 'like' ? 'is-active' : '' }}">
                                                        <span>👍</span>
                                                        <span class="ap-reaction-count">{{ $likes }}</span>
                                                    </button>
                                                    <button type="button"
                                                            class="ap-reaction-btn ap-report-dislike-btn {{ $reaction === 'dislike' ? 'is-active' : '' }}">
                                                        <span>👎</span>
                                                        <span class="ap-reaction-count">{{ $dislikes }}</span>
                                                    </button>
                                                </div>

                                                {{-- comments per report --}}
                                                <div class="ap-report-comments"
                                                     data-report-id="{{ $r->id }}">
                                                    <div class="ap-report-comment-list">
                                                        @foreach($r->comment_items ?? [] as $item)
                                                            @php
                                                                $commentEmployee = isset($item['employee_id'])
                                                                    ? $employeeMap->get($item['employee_id'])
                                                                    : null;

                                                                $authorName = $item['author_name']
                                                                    ?? ($commentEmployee
                                                                        ? $commentEmployee->name.' '.$commentEmployee->lastname
                                                                        : ('Mitarbeiter #'.($item['employee_id'] ?? '')));
                                                            @endphp
                                                            <div class="ap-report-comment-item">
                                                                <div>
                                                                    <span class="ap-report-comment-author">
                                                                        {{ $authorName }}
                                                                    </span>
                                                                    @if(!empty($item['created_at_human']))
                                                                        <span class="ap-report-comment-time">
                                                                            {{ $item['created_at_human'] }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="ap-report-comment-text">
                                                                    {{ $item['text'] ?? '' }}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="ap-report-comment-form">
                                                        <textarea class="ap-report-comment-input"
                                                                  placeholder="Kommentar zu diesem Report …"></textarea>
                                                        <button type="button"
                                                                class="ap-report-comment-save-btn">
                                                            <svg class="ap-icon-svg" aria-hidden="true">
                                                                <use xlink:href="#icon-comment"></use>
                                                            </svg>
                                                            <span>Speichern</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- NOTIFICATIONS --}}
                            <div class="ap-card">
                                <div class="ap-card-title">
                                    <span>Aktivitäten / Benachrichtigungen</span>
                                </div>

                                <ul class="ap-timeline" id="apNotificationTimeline">
                                    @forelse($notificationItems as $n)
                                        @php
                                            $kind = $n['kind'] ?? 'generic';
                                            $dotClass = 'ap-timeline-dot';
                                            if ($kind === 'due')        $dotClass .= ' ap-timeline-dot--due';
                                            elseif ($kind === 'status') $dotClass .= ' ap-timeline-dot--status';
                                            elseif ($kind === 'created')$dotClass .= ' ap-timeline-dot--created';
                                        @endphp
                                        <li class="ap-timeline-item">
                                            <div class="{{ $dotClass }}"></div>
                                            <div>
                                                <div class="ap-timeline-title">{{ $n['title'] }}</div>
                                                @if($n['message'])
                                                    <div class="ap-timeline-text">{{ $n['message'] }}</div>
                                                @endif
                                                <div class="ap-timeline-meta">{{ $n['created_at'] }}</div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="ap-timeline-item">
                                            <div class="ap-timeline-dot"></div>
                                            <div>
                                                <div class="ap-timeline-text">Noch keine Benachrichtigungen.</div>
                                            </div>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Details + Employees + Comments --}}
                        <div>
                            {{-- DETAILS --}}
                            <div class="ap-card">
                                <div class="ap-card-title">
                                    <span>Details</span>
                                </div>
                                <div class="ap-detail-row">
                                    <div class="ap-detail-label">Art</div>
                                    <div class="ap-detail-value">
                                        {{ $appointment->appointment_type ?? '–' }}
                                    </div>

                                    <div class="ap-detail-label">Ausführung</div>
                                    <div class="ap-detail-value">
                                        {{ $appointment->execution_type ?? '–' }}
                                    </div>

                                    <div class="ap-detail-label">Kontakt</div>
                                    <div class="ap-detail-value">
                                        {{ $appointment->phone ?? '–' }}
                                        @if($appointment->email)
                                            · {{ $appointment->email }}
                                        @endif
                                    </div>

                                    <div class="ap-detail-label">Adresse</div>
                                    <div class="ap-detail-value">
                                        {{ $appointment->full_address
                                            ?? trim(($appointment->street ?? '').', '.($appointment->postcode ?? '').' '.($appointment->city ?? '')) ?: '–' }}
                                    </div>

                                    <div class="ap-detail-label">Nächster Schritt</div>
                                    <div class="ap-detail-value">
                                        {{ $appointment->next_step ?? '–' }}
                                    </div>
                                </div>
                            </div>

                            {{-- EMPLOYEES --}}
                            <div class="ap-card">
                                <div class="ap-card-title">
                                    <span>Teilnehmende Mitarbeiter</span>
                                </div>

                                <div class="ap-chip-list" id="apEmployeeChipList">
                                    @foreach($appointment->employees as $e)
                                        <div class="ap-chip" data-employee-id="{{ $e->id }}">
                                            <span class="ap-avatar-small">
                                                <img src="{{ $e->image ? asset('images/employee/'.$e->image) : asset('images/employee/default.png') }}"
                                                     alt="{{ $e->name }}">
                                            </span>
                                            <span>{{ $e->name }} {{ $e->lastname }}</span>
                                            <button type="button" class="ap-chip-remove apRemoveEmployeeBtn">
                                                &times;
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="ap-employee-add-row">
                                    <select id="apEmployeeSelect" class="ap-employee-select">
                                        <option value="">Mitarbeiter auswählen …</option>
                                        @foreach($allEmployees as $e)
                                            <option value="{{ $e->id }}">
                                                {{ $e->name }} {{ $e->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="ap-secondary-btn" id="apAddEmployeeBtn">
                                        Hinzufügen
                                    </button>
                                </div>
                            </div>

                            {{-- COMMENTS (global for appointment) --}}
                            <div class="ap-card">
                                <div class="ap-card-title">
                                    <span>Kommentare</span>
                                </div>

                                <form class="ap-comment-form" id="apCommentForm">
                                    @csrf
                                    <textarea name="comment" id="apCommentText" placeholder="Kommentar hinzufügen …"></textarea>
                                    <div class="text-right mt-25">
                                        <button type="button" class="ap-secondary-btn" id="apCommentSaveBtn">
                                            Speichern
                                        </button>
                                    </div>
                                </form>

                                <div class="ap-comment-list" id="apCommentList">
                                    @foreach($comments as $c)
                                        <div class="ap-comment-item">
                                            <div class="ap-avatar">
                                                <img src="{{ $c->employee->image ? asset('images/employee/'.$c->employee->image) : asset('images/employee/default.png') }}"
                                                     alt="{{ $c->employee->name }}">
                                            </div>
                                            <div>
                                                <div class="ap-comment-meta">
                                                    <span class="ap-comment-author">
                                                        {{ $c->employee->name }} {{ $c->employee->lastname }}
                                                    </span>
                                                    <span class="ap-comment-time">
                                                        {{ $c->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <div class="ap-comment-text">
                                                    {{ $c->comment }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div> {{-- /right column --}}
                    </div> {{-- /ap-layout --}}

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- Quill --}}
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    <script>
        (function () {
            "use strict";

            const appointmentId = document.querySelector('.appointment-profile-page')?.dataset.appointmentId;
            const csrfToken     = document.querySelector('meta[name="csrf-token"]')?.content || '';

            const routes = {
                updateStatus: "{{ url('customer/appointments') }}/" + appointmentId + "/status",
                addEmployee:  "{{ url('customer/appointments') }}/" + appointmentId + "/employees",
                removeEmployeeBase: "{{ url('customer/appointments') }}/" + appointmentId + "/employees",
                storeReport:  "{{ route('customer.appointments.reports.store', $appointment) }}",
                storeComment: "{{ route('customer.appointments.comments.store', $appointment) }}",
                notifications: "{{ route('customer.appointments.notifications', $appointment) }}",
                reactReportBase: "{{ url('customer/appointments/'.$appointment->id.'/reports') }}",
                reportCommentBase: "{{ url('customer/appointments/'.$appointment->id.'/reports') }}",
            };

            const employeeImageBaseUrl    = "{{ asset('images/employee') }}";
            const employeeDefaultImageUrl = "{{ asset('images/employee/default.png') }}";

            const statusSelect = document.getElementById('apStatusSelect');
            const statusBtn    = document.getElementById('apStatusSaveBtn');

            const reportForm   = document.getElementById('apReportForm');
            const reportList   = document.getElementById('apReportList');

            const reportDate    = document.getElementById('apReportDate');
            const nextStepInput = document.getElementById('apNextStepInput');
            const dueDateInput  = document.getElementById('apDueDateInput');
            const reportBtn     = document.getElementById('apReportSaveBtn');

            const reportSearchInput   = document.getElementById('apReportSearchInput');
            const reportCountFiltered = document.getElementById('apReportCountFiltered');

            const empSelect    = document.getElementById('apEmployeeSelect');
            const empAddBtn    = document.getElementById('apAddEmployeeBtn');
            const empChipList  = document.getElementById('apEmployeeChipList');

            const commentText  = document.getElementById('apCommentText');
            const commentBtn   = document.getElementById('apCommentSaveBtn');
            const commentList  = document.getElementById('apCommentList');

            const notifTimeline = document.getElementById('apNotificationTimeline');

            let quill = null;
            const reportEditorEl = document.getElementById('apReportEditor');
            if (reportEditorEl && window.Quill) {
                quill = new Quill('#apReportEditor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'header': [false, 3, 4] }],
                            ['link'],
                            ['clean']
                        ]
                    }
                });
            }

            function showToast(msg, type) {
                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        icon: type || 'success',
                        title: msg || 'Gespeichert',
                    });
                } else {
                    alert(msg);
                }
            }

            function applyReportFilter() {
                if (!reportList) return;

                const query = (reportSearchInput?.value || '').trim().toLowerCase();
                const items = reportList.querySelectorAll('.ap-report-item');

                let visible = 0;

                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    const match = !query || text.includes(query);

                    item.style.display = match ? 'grid' : 'none';
                    if (match) visible++;
                });

                if (reportCountFiltered) {
                    reportCountFiltered.textContent = visible;
                }
            }

            if (reportSearchInput) {
                reportSearchInput.addEventListener('input', applyReportFilter);
            }

            // STATUS UPDATE
            if (statusBtn && statusSelect) {
                statusBtn.addEventListener('click', async function () {
                    const status = statusSelect.value;
                    try {
                        const resp = await fetch(routes.updateStatus, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ status }),
                        });

                        if (!resp.ok) throw new Error();

                        showToast('Status aktualisiert.', 'success');
                    } catch (e) {
                        console.error(e);
                        showToast('Status konnte nicht gespeichert werden.', 'error');
                    }
                });
            }

            // ADD EMPLOYEE
            if (empAddBtn && empSelect) {
                empAddBtn.addEventListener('click', async function () {
                    const val  = empSelect.value;
                    const text = empSelect.options[empSelect.selectedIndex]?.text || '';
                    if (!val) return;

                    try {
                        const resp = await fetch(routes.addEmployee, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ employee_id: val }),
                        });
                        if (!resp.ok) throw new Error();

                        const chip = document.createElement('div');
                        chip.className = 'ap-chip';
                        chip.dataset.employeeId = val;
                        chip.innerHTML = `
                            <span class="ap-avatar-small">
                                <img src="${employeeDefaultImageUrl}" alt="">
                            </span>
                            <span>${text}</span>
                            <button type="button" class="ap-chip-remove apRemoveEmployeeBtn">&times;</button>
                        `;
                        empChipList.appendChild(chip);
                        showToast('Mitarbeiter hinzugefügt.', 'success');
                    } catch (e) {
                        console.error(e);
                        showToast('Mitarbeiter konnte nicht hinzugefügt werden.', 'error');
                    }
                });
            }

            // REMOVE EMPLOYEE (delegated)
            if (empChipList) {
                empChipList.addEventListener('click', async function (e) {
                    const btn = e.target.closest('.apRemoveEmployeeBtn');
                    if (!btn) return;
                    const chip = btn.closest('.ap-chip');
                    if (!chip) return;
                    const empId = chip.dataset.employeeId;
                    if (!empId) return;

                    try {
                        const resp = await fetch(routes.removeEmployeeBase + '/' + empId, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            }
                        });
                        if (!resp.ok) throw new Error();

                        chip.remove();
                        showToast('Mitarbeiter entfernt.', 'success');
                    } catch (err) {
                        console.error(err);
                        showToast('Mitarbeiter konnte nicht entfernt werden.', 'error');
                    }
                });
            }

            // STORE REPORT
            if (reportBtn && reportForm) {
                reportBtn.addEventListener('click', async function () {
                    let html = '';
                    if (quill) {
                        html = quill.root.innerHTML.trim();
                        if (html === '<p><br></p>') {
                            html = '';
                        }
                    }
                    if (!html) return;

                    const dateVal  = reportDate?.value || '';
                    const nextStep = nextStepInput?.value.trim() || '';
                    const dueDate  = dueDateInput?.value || '';

                    try {
                        const fd = new FormData();
                        fd.append('report', html);
                        if (dateVal)  fd.append('report_date', dateVal);
                        if (nextStep) fd.append('next_step', nextStep);
                        if (dueDate)  fd.append('due_date', dueDate);

                        const resp = await fetch(routes.storeReport, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: fd,
                        });

                        if (!resp.ok) throw new Error();

                        const data = await resp.json();
                        if (!data.success) throw new Error();

                        const r = data.report;

                        const div   = document.createElement('div');
                        div.className = 'ap-report-item';
                        div.dataset.reportId = r.id;

                        const imgUrl = r.employee && r.employee.image
                            ? (employeeImageBaseUrl + '/' + r.employee.image)
                            : employeeDefaultImageUrl;

                        const hasNext = (r.next_step && r.next_step.length) || r.due_date;

                        div.innerHTML = `
                            <div class="ap-avatar">
                                <img src="${imgUrl}" alt="${r.employee.name}">
                            </div>
                            <div>
                                <div class="ap-report-meta">
                                    <span class="ap-report-author">${r.employee.name} ${r.employee.lastname}</span>
                                    <span class="ap-report-time">${r.created_at}</span>
                                </div>
                                <div class="ap-report-content">${r.report}</div>
                                ${
                                    hasNext
                                        ? `<div class="ap-report-next">
                                            <span class="ap-report-next-label">Next Step</span>
                                            <span>${r.next_step ?? '–'}</span>
                                            ${
                                                r.due_date
                                                    ? `<span class="ap-report-next-due">Fällig: ${r.due_date}</span>`
                                                    : ''
                                            }
                                        </div>`
                                        : ''
                                }
                                <div class="ap-report-actions"
                                     data-report-id="${r.id}"
                                     data-reaction="${r.reaction || ''}">
                                    <button type="button"
                                            class="ap-reaction-btn ap-report-like-btn ${r.reaction === 'like' ? 'is-active' : ''}">
                                        <span>👍</span>
                                        <span class="ap-reaction-count">${r.likes_count}</span>
                                    </button>
                                    <button type="button"
                                            class="ap-reaction-btn ap-report-dislike-btn ${r.reaction === 'dislike' ? 'is-active' : ''}">
                                        <span>👎</span>
                                        <span class="ap-reaction-count">${r.dislikes_count}</span>
                                    </button>
                                </div>

                                <div class="ap-report-comments" data-report-id="${r.id}">
                                    <div class="ap-report-comment-list"></div>
                                    <div class="ap-report-comment-form">
                                        <textarea class="ap-report-comment-input"
                                                  placeholder="Kommentar zu diesem Report …"></textarea>
                                        <button type="button"
                                                class="ap-report-comment-save-btn">
                                            <svg class="ap-icon-svg" aria-hidden="true">
                                                <use xlink:href="#icon-comment"></use>
                                            </svg>
                                            <span>Speichern</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        reportList.prepend(div);
                        applyReportFilter();

                        if (quill) {
                            quill.setContents([]);
                        }
                        if (nextStepInput) nextStepInput.value = '';
                        if (dueDateInput)  dueDateInput.value  = '';

                        showToast('Report gespeichert.', 'success');
                    } catch (e) {
                        console.error(e);
                        showToast('Report konnte nicht gespeichert werden.', 'error');
                    }
                });
            }

            // REACT TO REPORT (like / dislike)
            if (reportList) {
                reportList.addEventListener('click', async function (e) {
                    const likeBtn    = e.target.closest('.ap-report-like-btn');
                    const dislikeBtn = e.target.closest('.ap-report-dislike-btn');
                    const btn        = likeBtn || dislikeBtn;
                    if (!btn) return;

                    const wrapper = btn.closest('.ap-report-actions');
                    if (!wrapper) return;

                    const reportId = wrapper.dataset.reportId;
                    if (!reportId) return;

                    const reaction = likeBtn ? 'like' : 'dislike';

                    try {
                        const resp = await fetch(routes.reactReportBase + '/' + reportId + '/react', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ reaction }),
                        });

                        if (!resp.ok) throw new Error();

                        const data = await resp.json();
                        if (!data.success) throw new Error();

                        const likeSpan    = wrapper.querySelector('.ap-report-like-btn .ap-reaction-count');
                        const dislikeSpan = wrapper.querySelector('.ap-report-dislike-btn .ap-reaction-count');

                        if (likeSpan)    likeSpan.textContent    = data.likes_count;
                        if (dislikeSpan) dislikeSpan.textContent = data.dislikes_count;

                        wrapper.querySelectorAll('.ap-reaction-btn').forEach(function (b) {
                            b.classList.remove('is-active');
                        });
                        if (data.reaction === 'like' && wrapper.querySelector('.ap-report-like-btn')) {
                            wrapper.querySelector('.ap-report-like-btn').classList.add('is-active');
                        } else if (data.reaction === 'dislike' && wrapper.querySelector('.ap-report-dislike-btn')) {
                            wrapper.querySelector('.ap-report-dislike-btn').classList.add('is-active');
                        }
                    } catch (err) {
                        console.error(err);
                        showToast('Reaktion konnte nicht gespeichert werden.', 'error');
                    }
                });
            }

            // STORE GLOBAL COMMENT
            if (commentBtn) {
                commentBtn.addEventListener('click', async function () {
                    const text = commentText.value.trim();
                    if (!text) return;

                    try {
                        const fd = new FormData();
                        fd.append('comment', text);

                        const resp = await fetch(routes.storeComment, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: fd,
                        });

                        if (!resp.ok) throw new Error();

                        const data = await resp.json();
                        if (!data.success) throw new Error();

                        const c = data.comment;
                        const imgUrl = c.employee && c.employee.image
                            ? (employeeImageBaseUrl + '/' + c.employee.image)
                            : employeeDefaultImageUrl;

                        const div = document.createElement('div');
                        div.className = 'ap-comment-item';
                        div.innerHTML = `
                            <div class="ap-avatar">
                                <img src="${imgUrl}" alt="${c.employee.name}">
                            </div>
                            <div>
                                <div class="ap-comment-meta">
                                    <span class="ap-comment-author">${c.employee.name} ${c.employee.lastname}</span>
                                    <span class="ap-comment-time">${c.created_at}</span>
                                </div>
                                <div class="ap-comment-text">${c.comment}</div>
                            </div>
                        `;
                        commentList.prepend(div);
                        commentText.value = '';
                        showToast('Kommentar gespeichert.', 'success');
                    } catch (e) {
                        console.error(e);
                        showToast('Kommentar konnte nicht gespeichert werden.', 'error');
                    }
                });
            }

            // COMMENT UNDER REPORT (delegated on reportList)
            if (reportList) {
                reportList.addEventListener('click', async function (e) {
                    const btn = e.target.closest('.ap-report-comment-save-btn');
                    if (!btn) return;

                    const wrapper = btn.closest('.ap-report-comments');
                    if (!wrapper) return;

                    const reportId = wrapper.dataset.reportId;
                    if (!reportId) return;

                    const textarea = wrapper.querySelector('.ap-report-comment-input');
                    if (!textarea) return;

                    const text = textarea.value.trim();
                    if (!text) return;

                    try {
                        const resp = await fetch(routes.reportCommentBase + '/' + reportId + '/comments', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ text }),
                        });

                        if (!resp.ok) throw new Error();

                        const data = await resp.json();
                        if (!data.success) throw new Error();

                        const items = data.commentItems || [];
                        const last  = items[items.length - 1];

                        const listEl = wrapper.querySelector('.ap-report-comment-list');
                        if (last && listEl) {
                            const div = document.createElement('div');
                            div.className = 'ap-report-comment-item';
                            div.innerHTML = `
                                <div>
                                    <span class="ap-report-comment-author">
                                        ${last.author_name || ('Mitarbeiter #' + (last.employee_id || ''))}
                                    </span>
                                    ${last.created_at_human
                                        ? `<span class="ap-report-comment-time">${last.created_at_human}</span>`
                                        : ''
                                    }
                                </div>
                                <div class="ap-report-comment-text">${last.text || ''}</div>
                            `;
                            listEl.appendChild(div);
                        }

                        textarea.value = '';
                        showToast('Kommentar gespeichert.', 'success');
                    } catch (err) {
                        console.error(err);
                        showToast('Kommentar konnte nicht gespeichert werden.', 'error');
                    }
                });
            }

            // REFRESH NOTIFICATIONS
            async function refreshNotifications() {
                if (!routes.notifications || !notifTimeline) return;
                try {
                    const resp = await fetch(routes.notifications, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!resp.ok) return;

                    const json  = await resp.json();
                    const items = json.data || [];
                    notifTimeline.innerHTML = '';

                    if (!items.length) {
                        const li = document.createElement('li');
                        li.className = 'ap-timeline-item';
                        li.innerHTML = `
                            <div class="ap-timeline-dot"></div>
                            <div><div class="ap-timeline-text">Noch keine Benachrichtigungen.</div></div>
                        `;
                        notifTimeline.appendChild(li);
                        return;
                    }

                    items.forEach(n => {
                        let dotClass = 'ap-timeline-dot';
                        if (n.kind === 'due')      dotClass += ' ap-timeline-dot--due';
                        else if (n.kind === 'status')  dotClass += ' ap-timeline-dot--status';
                        else if (n.kind === 'created') dotClass += ' ap-timeline-dot--created';

                        const li = document.createElement('li');
                        li.className = 'ap-timeline-item';
                        li.innerHTML = `
                            <div class="${dotClass}"></div>
                            <div>
                                <div class="ap-timeline-title">${n.title}</div>
                                ${n.message ? `<div class="ap-timeline-text">${n.message}</div>` : ''}
                                <div class="ap-timeline-meta">${n.created_at}</div>
                            </div>
                        `;
                        notifTimeline.appendChild(li);
                    });
                } catch (e) {
                    console.error(e);
                }
            }

            setInterval(refreshNotifications, 60000);
        })();
    </script>
@endsection
