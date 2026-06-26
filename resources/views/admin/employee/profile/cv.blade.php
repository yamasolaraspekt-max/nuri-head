@extends('admin.layouts.app')

@section('title') Lebenslauf @endsection

@section('style')
    <style>
        :root {
            --cv-blue: #74b2d4;
            --cv-green: #93c21c;
            --cv-orange: #f8ac00;
            --cv-pink: #e50656;
            --cv-dark: #1f2937;
            --cv-muted: #64748b;
            --cv-border: #e5e7eb;
            --cv-bg: #f8fafc;
            --cv-card: #ffffff;
            --cv-soft-blue: rgba(116, 178, 212, .12);
            --cv-soft-green: rgba(147, 194, 28, .14);
            --cv-radius: 24px;
        }

        .cv-page {
            background:
                radial-gradient(circle at top left, rgba(116,178,212,.16), transparent 34%),
                radial-gradient(circle at top right, rgba(147,194,28,.13), transparent 28%),
                var(--cv-bg);
            min-height: 100vh;
            padding-bottom: 40px;
        }

        .cv-shell {
            max-width: 1180px;
            margin: 0 auto;
        }

        .cv-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .cv-titlebox h2 {
            margin: 0;
            color: var(--cv-dark);
            font-weight: 950;
            letter-spacing: .02em;
        }

        .cv-titlebox p {
            margin: 5px 0 0;
            color: var(--cv-muted);
            font-size: 13px;
            font-weight: 700;
        }

        .cv-print-btn {
            border: none;
            border-radius: 999px;
            padding: 11px 18px;
            background: linear-gradient(135deg, var(--cv-blue), #5e9fc5);
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 14px 30px rgba(116,178,212,.28);
            cursor: pointer;
            transition: all .18s ease;
        }

        .cv-print-btn:hover {
            transform: translateY(-1px);
            color: #fff;
            box-shadow: 0 18px 38px rgba(116,178,212,.35);
        }

        .cv-document {
            background: var(--cv-card);
            border: 1px solid rgba(226,232,240,.9);
            border-radius: 32px;
            box-shadow: 0 28px 70px rgba(15,23,42,.10);
            overflow: hidden;
        }

        .cv-hero {
            position: relative;
            padding: 34px;
            background:
                linear-gradient(135deg, rgba(116,178,212,.18), rgba(147,194,28,.16)),
                #ffffff;
            border-bottom: 1px solid var(--cv-border);
            display: grid;
            grid-template-columns: 170px 1fr;
            gap: 28px;
            align-items: center;
        }

        .cv-hero::after {
            content: "";
            position: absolute;
            right: -70px;
            top: -70px;
            width: 210px;
            height: 210px;
            border-radius: 999px;
            background: rgba(116,178,212,.22);
            filter: blur(8px);
            pointer-events: none;
        }

        .cv-photo-wrap {
            position: relative;
            z-index: 1;
        }

        .cv-photo {
            width: 155px;
            height: 155px;
            border-radius: 32px;
            object-fit: cover;
            border: 6px solid #fff;
            box-shadow: 0 22px 40px rgba(15,23,42,.18);
            background: #f1f5f9;
        }

        .cv-status-dot {
            position: absolute;
            right: 6px;
            bottom: 6px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--cv-green);
            border: 5px solid #fff;
            box-shadow: 0 8px 18px rgba(147,194,28,.35);
        }

        .cv-namebox {
            position: relative;
            z-index: 1;
            min-width: 0;
        }

        .cv-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            padding: 7px 11px;
            border-radius: 999px;
            background: #fff;
            color: var(--cv-blue);
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .08em;
            box-shadow: 0 8px 20px rgba(15,23,42,.06);
        }

        .cv-name {
            margin: 0;
            color: #111827;
            font-size: clamp(30px, 4vw, 48px);
            line-height: 1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .cv-position {
            margin: 12px 0 0;
            color: var(--cv-muted);
            font-size: 16px;
            font-weight: 800;
        }

        .cv-hero-meta {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .cv-hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 11px;
            background: rgba(255,255,255,.78);
            border: 1px solid rgba(226,232,240,.9);
            border-radius: 999px;
            color: #334155;
            font-size: 12px;
            font-weight: 800;
        }

        .cv-body-grid {
            display: grid;
            grid-template-columns: 340px 1fr;
            min-height: 600px;
        }

        .cv-sidebar {
            padding: 28px;
            background:
                linear-gradient(180deg, #ffffff, #f8fafc);
            border-right: 1px solid var(--cv-border);
        }

        .cv-main {
            padding: 28px;
            background: #ffffff;
        }

        .cv-section {
            margin-bottom: 28px;
        }

        .cv-section:last-child {
            margin-bottom: 0;
        }

        .cv-section-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .cv-section-icon {
            width: 36px;
            height: 36px;
            border-radius: 14px;
            background: var(--cv-soft-blue);
            color: var(--cv-blue);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .cv-section-icon.green {
            background: var(--cv-soft-green);
            color: var(--cv-green);
        }

        .cv-section-title {
            margin: 0;
            color: #111827;
            font-size: 14px;
            font-weight: 950;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .cv-info-list {
            display: grid;
            gap: 10px;
        }

        .cv-info-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px;
            border: 1px solid var(--cv-border);
            border-radius: 16px;
            background: #fff;
        }

        .cv-info-item i,
        .cv-info-item svg {
            width: 17px;
            height: 17px;
            color: var(--cv-blue);
            flex: 0 0 auto;
            margin-top: 1px;
        }

        .cv-info-item span {
            display: block;
            color: var(--cv-muted);
            font-size: 10px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 3px;
        }

        .cv-info-item strong {
            display: block;
            color: #111827;
            font-size: 13px;
            font-weight: 850;
            word-break: break-word;
        }

        .cv-timeline {
            position: relative;
            display: grid;
            gap: 14px;
        }

        .cv-timeline-item {
            position: relative;
            padding: 16px;
            border: 1px solid rgba(226,232,240,.95);
            border-radius: 18px;
            background:
                radial-gradient(circle at top left, rgba(116,178,212,.08), transparent 40%),
                #ffffff;
        }

        .cv-timeline-title {
            margin: 0;
            color: #111827;
            font-size: 14px;
            font-weight: 950;
        }

        .cv-timeline-sub {
            margin: 5px 0 0;
            color: var(--cv-blue);
            font-size: 12px;
            font-weight: 850;
        }

        .cv-timeline-date {
            margin: 8px 0 0;
            color: var(--cv-muted);
            font-size: 12px;
            font-weight: 750;
        }

        .cv-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .cv-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 11px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 12px;
            font-weight: 850;
        }

        .cv-chip.green {
            background: rgba(147,194,28,.12);
            border-color: rgba(147,194,28,.22);
            color: #5f8512;
        }

        .cv-chip.blue {
            background: rgba(116,178,212,.12);
            border-color: rgba(116,178,212,.24);
            color: #407fa3;
        }

        .cv-skill-list {
            display: grid;
            gap: 14px;
        }

        .cv-skill {
            padding: 14px;
            background: #ffffff;
            border: 1px solid var(--cv-border);
            border-radius: 18px;
        }

        .cv-skill-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 9px;
        }

        .cv-skill-name {
            color: #111827;
            font-size: 13px;
            font-weight: 950;
        }

        .cv-skill-percent {
            color: var(--cv-green);
            font-size: 12px;
            font-weight: 950;
        }

        .cv-progress {
            height: 9px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .cv-progress-bar {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--cv-green), var(--cv-blue));
            min-width: 6%;
        }

        .cv-card-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .cv-mini-card {
            padding: 15px;
            border: 1px solid var(--cv-border);
            border-radius: 18px;
            background: #fff;
        }

        .cv-mini-card span {
            display: block;
            color: var(--cv-muted);
            font-size: 10px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 5px;
        }

        .cv-mini-card strong {
            color: #111827;
            font-size: 13px;
            font-weight: 900;
        }

        .cv-empty {
            padding: 16px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: var(--cv-muted);
            font-size: 12px;
            font-weight: 800;
            text-align: center;
        }

        @media (max-width: 991px) {
            .cv-hero {
                grid-template-columns: 1fr;
                text-align: center;
                justify-items: center;
            }

            .cv-hero-meta {
                justify-content: center;
            }

            .cv-body-grid {
                grid-template-columns: 1fr;
            }

            .cv-sidebar {
                border-right: 0;
                border-bottom: 1px solid var(--cv-border);
            }
        }

        @media (max-width: 575px) {
            .cv-page {
                padding: 0;
            }

            .cv-document {
                border-radius: 20px;
            }

            .cv-hero,
            .cv-sidebar,
            .cv-main {
                padding: 20px;
            }

            .cv-card-grid {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            body {
                background: #fff !important;
            }

            .header-navbar-shadow,
            .content-header,
            .cv-toolbar,
            .main-menu,
            .sidebar-left,
            .sidebar-right,
            .top-header,
            .mobile-nav,
            .quick-sider,
            .overlay {
                display: none !important;
            }

            .app-content,
            .content-wrapper,
            .content-body,
            .cv-page,
            .cv-shell {
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
                width: 100% !important;
            }

            .cv-document {
                box-shadow: none !important;
                border-radius: 0 !important;
                border: none !important;
            }

            .cv-body-grid {
                grid-template-columns: 300px 1fr;
            }

            .cv-sidebar {
                border-right: 1px solid #ddd;
                border-bottom: 0;
            }

            .cv-section {
                break-inside: avoid;
            }
        }
    </style>
@endsection

@section('content')
    @php
$fullName = trim(($data->name ?? '') . ' ' . ($data->lastname ?? ''));
$fullName = $fullName ?: 'Mitarbeiter';

$positionTitle = optional($positions->first())->position ?? ($data->contract_type ?? 'Mitarbeiter');
$branchName = $data->branch ?? '—';

$profileImage = !empty($data->image)
    ? asset('images/employee/' . $data->image)
    : 'https://ui-avatars.com/api/?name=' . urlencode($fullName) . '&background=74b2d4&color=fff';

$mainAddress = $addresses->first() ?? null;
$addressLine = $mainAddress
    ? trim(($mainAddress->street ?? '') . ', ' . ($mainAddress->postcode ?? '') . ' ' . ($mainAddress->city ?? ''))
    : null;
    @endphp

    <div class="app-content cv-page"> 

        <div class="content-wrapper">
            

            <div class="content-body">
                <div class="cv-shell">
                    <div class="cv-toolbar">
                        <div class="cv-titlebox">
                            <h2>Lebenslauf</h2>
                            <p>Professionelles Mitarbeiterprofil mit Qualifikationen, Fähigkeiten und Erfahrung</p>
                        </div>

                        <button type="button" class="cv-print-btn" onclick="window.print()">
                            <i data-lucide="printer"></i>
                            Drucken / PDF
                        </button>
                    </div>

                    <article class="cv-document">
                        <header class="cv-hero">
                            <div class="cv-photo-wrap">
                                <img
                                    src="{{ $profileImage }}"
                                    alt="{{ $fullName }}"
                                    class="cv-photo"
                                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($fullName) }}&background=74b2d4&color=fff'"
                                >
                                <span class="cv-status-dot"></span>
                            </div>

                            <div class="cv-namebox">
                                <span class="cv-kicker">
                                    <i data-lucide="badge-check" style="width:15px;height:15px;"></i>
                                    Mitarbeiterprofil
                                </span>

                                <h1 class="cv-name">{{ $fullName }}</h1>

                                <p class="cv-position">
                                    {{ $positionTitle }} · {{ $branchName }}
                                </p>

                                <div class="cv-hero-meta">
                                    @if(!empty($data->contract_type))
                                        <span class="cv-hero-chip">
                                            <i data-lucide="file-signature" style="width:14px;height:14px;"></i>
                                            {{ $data->contract_type }}
                                        </span>
                                    @endif

                                    @if(!empty($data->contract_date))
                                        <span class="cv-hero-chip">
                                            <i data-lucide="calendar" style="width:14px;height:14px;"></i>
                                            Seit {{ \Carbon\Carbon::parse($data->contract_date)->format('d.m.Y') }}
                                        </span>
                                    @endif

                                    @if(!empty($data->country))
                                        <span class="cv-hero-chip">
                                            <i data-lucide="globe" style="width:14px;height:14px;"></i>
                                            {{ $data->country }}
                                        </span>
                                    @endif

                                    @if(!empty($data->working_hour))
                                        <span class="cv-hero-chip">
                                            <i data-lucide="clock" style="width:14px;height:14px;"></i>
                                            {{ $data->working_hour }} Std.
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </header>

                        <div class="cv-body-grid">
                            <aside class="cv-sidebar">
                                <section class="cv-section">
                                    <div class="cv-section-head">
                                        <span class="cv-section-icon">
                                            <i data-lucide="contact"></i>
                                        </span>
                                        <h3 class="cv-section-title">Kontakt</h3>
                                    </div>

                                    <div class="cv-info-list">
                                        @if(!empty($data->phone))
                                            <div class="cv-info-item">
                                                <i data-lucide="phone"></i>
                                                <div>
                                                    <span>Telefon</span>
                                                    <strong>{{ $data->phone }}</strong>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!empty($data->email))
                                            <div class="cv-info-item">
                                                <i data-lucide="mail"></i>
                                                <div>
                                                    <span>E-Mail</span>
                                                    <strong>{{ $data->email }}</strong>
                                                </div>
                                            </div>
                                        @endif

                                        @if($addressLine)
                                            <div class="cv-info-item">
                                                <i data-lucide="map-pin"></i>
                                                <div>
                                                    <span>Adresse</span>
                                                    <strong>{{ $addressLine }}</strong>
                                                </div>
                                            </div>
                                        @endif

                                        @if(empty($data->phone) && empty($data->email) && !$addressLine)
                                            <div class="cv-empty">Keine Kontaktdaten vorhanden.</div>
                                        @endif
                                    </div>
                                </section>

                                <section class="cv-section">
                                    <div class="cv-section-head">
                                        <span class="cv-section-icon green">
                                            <i data-lucide="languages"></i>
                                        </span>
                                        <h3 class="cv-section-title">Sprachen</h3>
                                    </div>

                                    @if($language->count())
                                        <div class="cv-chip-list">
                                            @foreach($language as $lang)
                                                <span class="cv-chip blue">
                                                    <i data-lucide="message-circle" style="width:14px;height:14px;"></i>
                                                    {{ $lang->language }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @elseif($languages->count())
                                        <div class="cv-chip-list">
                                            @foreach($languages as $lang)
                                                <span class="cv-chip blue">
                                                    <i data-lucide="message-circle" style="width:14px;height:14px;"></i>
                                                    {{ $lang->language }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="cv-empty">Keine Sprachen hinterlegt.</div>
                                    @endif
                                </section>

                                <section class="cv-section">
                                    <div class="cv-section-head">
                                        <span class="cv-section-icon">
                                            <i data-lucide="award"></i>
                                        </span>
                                        <h3 class="cv-section-title">Fähigkeiten</h3>
                                    </div>

                                    @if($skills->count())
                                        <div class="cv-skill-list">
                                            @foreach($skills as $skill)
                                                @php
        $rawResult = (int) ($skill->result ?? 0);
        $percent = min(100, max(8, $rawResult + 70));
                                                @endphp

                                                <div class="cv-skill">
                                                    <div class="cv-skill-top">
                                                        <span class="cv-skill-name">{{ $skill->article_group }}</span>
                                                        <span class="cv-skill-percent">{{ $percent }}%</span>
                                                    </div>

                                                    <div class="cv-progress">
                                                        <div class="cv-progress-bar" style="width: {{ $percent }}%;"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="cv-empty">Keine Fähigkeiten hinterlegt.</div>
                                    @endif
                                </section>

                                <section class="cv-section">
                                    <div class="cv-section-head">
                                        <span class="cv-section-icon green">
                                            <i data-lucide="car"></i>
                                        </span>
                                        <h3 class="cv-section-title">Führerschein</h3>
                                    </div>

                                    @if($employee_license->count())
                                        <div class="cv-chip-list">
                                            @foreach($employee_license as $licenseItem)
                                                <span class="cv-chip green">
                                                    {{ $licenseItem->initials ?? $licenseItem->de_name ?? 'Lizenz' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @elseif($license->count())
                                        <div class="cv-chip-list">
                                            @foreach($license as $licenseItem)
                                                <span class="cv-chip green">
                                                    {{ $licenseItem->license_no ?? 'Führerschein' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="cv-empty">Keine Führerscheindaten vorhanden.</div>
                                    @endif
                                </section>
                            </aside>

                            <main class="cv-main">
                                <section class="cv-section">
                                    <div class="cv-section-head">
                                        <span class="cv-section-icon">
                                            <i data-lucide="briefcase-business"></i>
                                        </span>
                                        <h3 class="cv-section-title">Arbeitserfahrung bei {{ $branchName }}</h3>
                                    </div>

                                    @if($positions->count())
                                        <div class="cv-timeline">
                                            @foreach($positions as $position)
                                                <div class="cv-timeline-item">
                                                    <h4 class="cv-timeline-title">{{ $position->position }}</h4>

                                                    @if(!empty($position->department_name))
                                                        <p class="cv-timeline-sub">{{ $position->department_name }}</p>
                                                    @endif

                                                    <p class="cv-timeline-date">
                                                        <i data-lucide="calendar" style="width:14px;height:14px;"></i>
                                                        @if(!empty($data->contract_date))
                                                            {{ \Carbon\Carbon::parse($data->contract_date)->format('d.m.Y') }} – Heute
                                                        @else
                                                            Aktuelle Position
                                                        @endif
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="cv-empty">Keine Positionen hinterlegt.</div>
                                    @endif
                                </section>

                                <section class="cv-section">
                                    <div class="cv-section-head">
                                        <span class="cv-section-icon green">
                                            <i data-lucide="graduation-cap"></i>
                                        </span>
                                        <h3 class="cv-section-title">Bildungshintergrund</h3>
                                    </div>

                                    @if($qualifications->count())
                                        <div class="cv-timeline">
                                            @foreach($qualifications as $quali)
                                                <div class="cv-timeline-item">
                                                    <h4 class="cv-timeline-title">
                                                        {{ $quali->degree }}
                                                        @if(!empty($quali->major))
                                                            · {{ $quali->major }}
                                                        @endif
                                                    </h4>

                                                    @if(!empty($quali->institution))
                                                        <p class="cv-timeline-sub">{{ $quali->institution }}</p>
                                                    @endif

                                                    <p class="cv-timeline-date">
                                                        {{ $quali->q_start_year ?? '—' }} – {{ $quali->q_end_year ?? 'Heute' }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="cv-empty">Kein Bildungshintergrund hinterlegt.</div>
                                    @endif
                                </section>

                                <section class="cv-section">
                                    <div class="cv-section-head">
                                        <span class="cv-section-icon">
                                            <i data-lucide="book-open-check"></i>
                                        </span>
                                        <h3 class="cv-section-title">Weiterbildungen</h3>
                                    </div>

                                    @if($feducation->count())
                                        <div class="cv-timeline">
                                            @foreach($feducation as $edu)
                                                <div class="cv-timeline-item">
                                                    <h4 class="cv-timeline-title">
                                                        {{ $edu->title ?? $edu->course ?? $edu->name ?? 'Weiterbildung' }}
                                                    </h4>

                                                    @if(!empty($edu->institution))
                                                        <p class="cv-timeline-sub">{{ $edu->institution }}</p>
                                                    @endif

                                                    <p class="cv-timeline-date">
                                                        {{ $edu->start_date ?? $edu->year ?? '—' }}
                                                        @if(!empty($edu->end_date))
                                                            – {{ $edu->end_date }}
                                                        @endif
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="cv-empty">Keine Weiterbildungen hinterlegt.</div>
                                    @endif
                                </section>

                                <section class="cv-section">
                                    <div class="cv-section-head">
                                        <span class="cv-section-icon green">
                                            <i data-lucide="layers"></i>
                                        </span>
                                        <h3 class="cv-section-title">Abteilungen & Stammdaten</h3>
                                    </div>

                                    <div class="cv-card-grid">
                                        <div class="cv-mini-card">
                                            <span>Filiale</span>
                                            <strong>{{ $branchName }}</strong>
                                        </div>

                                        <div class="cv-mini-card">
                                            <span>Vertragstyp</span>
                                            <strong>{{ $data->contract_type ?? '—' }}</strong>
                                        </div>

                                        <div class="cv-mini-card">
                                            <span>Nationalität / Land</span>
                                            <strong>{{ $data->country ?? '—' }}</strong>
                                        </div>

                                        <div class="cv-mini-card">
                                            <span>Arbeitsmodell</span>
                                            <strong>{{ $data->working_type ?? '—' }}</strong>
                                        </div>
                                    </div>

                                    @if($department->count())
                                        <div class="cv-chip-list" style="margin-top:14px;">
                                            @foreach($department as $dep)
                                                <span class="cv-chip green">
                                                    <i data-lucide="building" style="width:14px;height:14px;"></i>
                                                    {{ $dep->department_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </section>

                                <section class="cv-section">
                                    <div class="cv-section-head">
                                        <span class="cv-section-icon">
                                            <i data-lucide="sparkles"></i>
                                        </span>
                                        <h3 class="cv-section-title">Weitere Fähigkeiten</h3>
                                    </div>

                                    @if($otherskill->count())
                                        <div class="cv-chip-list">
                                            @foreach($otherskill as $skill)
                                                <span class="cv-chip">
                                                    {{ $skill->skill ?? $skill->name ?? $skill->other_skill ?? 'Fähigkeit' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="cv-empty">Keine weiteren Fähigkeiten hinterlegt.</div>
                                    @endif
                                </section>
                            </main>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        });
    </script>
@endpush


@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Mitarbeiter-Profil',
                url: "{{ url()->current() }}",
                clickable: false
            },
            {
                label: '{{ $data->name }} {{ $data->lastname }}',
                url: "{{ url('employee_profile/'.$data->id) }}", 
            },
             {
                label: 'LEBENSLAUF',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush