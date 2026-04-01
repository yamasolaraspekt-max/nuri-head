@extends('admin.layouts.app')
@section('title') Abteilungsprofil @endsection

@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js/dist/Chart.min.css">

<style>

  :root {
    --dpb4-blue:      #74b2d4;
    --dpb4-green:     #93c21c;
    --dpb4-lightgreen:#cfe09b;
    --dpb4-paleblue:  #c0d8ea;
}

/* Ring / Hero */
.dpb4-ring {
    border-radius: 1.25rem;
    background: linear-gradient(135deg, var(--dpb4-blue), var(--dpb4-green));
    color: #fff;
}

/* Glass / Cards */
.dpb4-glass {
    background: linear-gradient(180deg, rgba(255,255,255,.12), rgba(255,255,255,.03));
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.16);
    border-radius: 1rem;
}

.dpb4-soft-card {
    background: var(--dpb4-paleblue);
    border-radius: 1rem;
    border: 1px solid rgba(148,163,184,.5);
}

/* Progressbars: use palette instead of random gradients */
.progress-bar,
#dpb4-exp-dist-bar {
    background-color: var(--dpb4-blue) !important;
    background-image: none !important;
}

/* Badges – tint with the palette */
.dpb4-badge-info    { background: rgba(116,178,212,.15);  color:#074873; }
.dpb4-badge-primary { background: rgba(116,178,212,.15);  color:#074873; }
.dpb4-badge-success { background: rgba(147,194,28,.15);   color:#4a6e00; }
.dpb4-badge-warning { background: rgba(207,224,155,.3);   color:#6b6b00; }
.dpb4-badge-danger  { background: rgba(239,68,68,.15);    color:#991b1b; }
.dpb4-badge-secondary { background: rgba(192,216,234,.4); color:#374151; }

    /* ==== GENERELLES LAYOUT / HINTERGRUND ==== */
    .dpb4-aurora {
        background:
            radial-gradient(1200px 600px at 10% 10%, rgba(99,102,241,.12), transparent 40%),
            radial-gradient(1000px 500px at 90% 0%, rgba(16,185,129,.12), transparent 40%),
            radial-gradient(800px 400px at 50% 100%, rgba(236,72,153,.10), transparent 40%),
            linear-gradient(180deg, #0b1020, #0b1020);
        color: #e5e7eb;
    }

    .dpb4-ring {
        border-radius: 1.25rem;
        background: linear-gradient(135deg, #93c21c, #cfe09b);
        color: #fff;
    }

    .dpb4-glass {
        background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.03));
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 1rem;
    }

    .dpb4-soft-card {
        background: rgba(255,255,255,.06);
        border-radius: 1rem;
        border: 1px solid rgba(255,255,255,.16);
    }

    .dpb4-dot {
        width: .5rem;
        height: .5rem;
        border-radius: 9999px;
        display: inline-block;
    }

    .dpb4-btn-soft {
        border: 1px solid rgba(255,255,255,.25);
        background: rgba(255, 255, 255, 1);
        color: #253c68ff;
    }

    .dpb4-btn-soft:hover {
        background: rgba(255,255,255,.18);
        color: #ffffff;
    }

    .dpb4-badge-soft {
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,.25);
        background: rgba(255,255,255,.09);
        font-size: .75rem;
        padding: .15rem .55rem;
    }

    .dpb4-badge-emerald {
        background: rgba(16,185,129,.18);
        color: #a7f3d0;
        border-color: rgba(16,185,129,.35);
    }

    /* Avatare */
    .dpb4-avatar-24 {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid rgba(255,255,255,.18);
    }

    .dpb4-avatar-36 {
        width: 36px;
        height: 36px;
        border-radius: .75rem;
        object-fit: cover;
        border: 1px solid rgba(255,255,255,.18);
    }

    /* Tabellen */
    .dpb4-table thead th {
        background: rgba(15,23,42,0.9);
        color: #e5e7eb;
        border-color: rgba(148, 163, 184, 0.4);
    }

    .dpb4-table td,
    .dpb4-table th {
        border-color: rgba(148, 163, 184, 0.35);
        vertical-align: middle;
    }

    /* Kanban-ähnliches Grid für Projekte (falls genutzt) */
    .dpb4-kanban {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        grid-gap: 1rem;
    }

    @media (max-width: 1199.98px) {
        .dpb4-kanban {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .dpb4-kanban {
            grid-template-columns: 1fr;
        }
    }

    /* Tabs */
    .dpb4-tab-pane.d-none {
        display: none !important;
    }

    /* Kalender-Grid */
    .dpb4-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        grid-gap: .5rem;
        text-align: center;
    }

    .dpb4-calendar-header {
        font-weight: 600;
        color: #6b7280;
        font-size: .8rem;
    }

    .day-cell {
        padding: .25rem;
        cursor: pointer;
    }

    .dpb4-day-number {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .dpb4-day-number-today {
        border: 2px solid #4f46e5;
        background: #fff;
        color: #111827;
    }

    .dpb4-day-number-has-events {
        background: #3b82f6;
        color: #fff;
    }

    /* Events-Liste (Kalender rechts) */
    #dayEventsPanel.d-none {
        display: none !important;
    }

    .dpb4-event-card {
        border-left: 4px solid #74b2d4; 
        padding: .75rem;
        background: rgba(255, 255, 255, 1);
        margin-bottom: .5rem;
    }

    /* Aufgaben / Sprint Snapshot Badges */
    .dpb4-badge-info    { background: rgba(59,130,246,.12);  color:#2563eb;  border-radius:999px; padding:.15rem .5rem; }
    .dpb4-badge-primary { background: rgba(79,70,229,.12);   color:#4f46e5;  border-radius:999px; padding:.15rem .5rem; }
    .dpb4-badge-success { background: rgba(16,185,129,.12);  color:#059669;  border-radius:999px; padding:.15rem .5rem; }
    .dpb4-badge-warning { background: rgba(245,158,11,.12);  color:#d97706;  border-radius:999px; padding:.15rem .5rem; }
    .dpb4-badge-danger  { background: rgba(239,68,68,.12);   color:#b91c1c;  border-radius:999px; padding:.15rem .5rem; }
    .dpb4-badge-secondary { background: rgba(148,163,184,.12); color:#4b5563; border-radius:999px; padding:.15rem .5rem; }

    /* Kleine Helfer */
    .dpb4-text-muted {
        color: #646464ff;
    }

    .dpb4-text-black {
        color: #e5e7eb;
    }

    .dpb4-section-title {
        font-weight: 600;
        font-size: 1rem;
    }

    /* Tickets – Toolbar + Filter-Layout */
.dpb4-ticket-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: .75rem;
}

.dpb4-ticket-filters {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
}

.dpb4-ticket-filter {
    min-width: 160px;
}

@media (max-width: 767.98px) {
    .dpb4-ticket-toolbar {
        flex-direction: column;
        align-items: stretch;
    }
    .dpb4-ticket-filters {
        width: 100%;
        flex-direction: column;
    }
    .dpb4-ticket-filter {
        width: 100%;
    }
}

/* Mobile Kartenansicht für Tickets */
  .dpb4-ticket-card {
      border-radius: .75rem;
      border: 1px solid rgba(148,163,184,.45);
      background: rgba(15,23,42,.5);
      padding: .75rem;
  }

  .dpb4-ticket-card + .dpb4-ticket-card {
      margin-top: .5rem;
  }

  .dpb4-ticket-card-label {
      font-size: .7rem;
      text-transform: uppercase;
      letter-spacing: .05em;
      color: #9ca3af;
      margin-bottom: .15rem;
  }

  /* === TEAM TAB – OWN COLORS & CONTRAST === */

/* Background + base text in the Team tab */
#dpb4-tab-team {
    background: #f7fafc; /* very light */
    color: #0b1020;
}

/* Team filter buttons */
#dpb4-tab-team .dpb4-btn-soft {
    background: var(--dpb4-paleblue);
    border-color: var(--dpb4-blue);
    color: #074873;
}

  #dpb4-tab-team .dpb4-btn-soft:hover,
  #dpb4-tab-team .dpb4-btn-soft:focus {
      background: #93c21c;
      border-color: var(--dpb4-blue);
      color: #ffffff;
  }

  /* Team cards */
  #dpb4-tab-team .dpb4-soft-card {
      background: #ffffff;
      border-color: var(--dpb4-paleblue);
  }

  /* Text colors inside cards */
  #dpb4-tab-team .font-weight-bold,
  #dpb4-tab-team .badge,
  #dpb4-tab-team .small,
  #dpb4-tab-team span {
      color: #0b1020;
  }

  #dpb4-tab-team .dpb4-text-muted {
      color: #4b5563;
  }


</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        {{-- Breadcrumbs --}}
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">ABTEILUNG</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('/') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ url('/department') }}">Abteilungen</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Abteilungsprofil
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- INHALT --}}
        <div class="content-body">

            {{-- HERO / HEADERBEREICH --}}
            <div id="dpb4-hero" class="dpb4-ring mb-4">
                <div class="p-2 p-lg-2">
                    <div class="row align-items-center">
                        {{-- Abteilungsinformationen --}}
                        <div class="col-lg-8 d-flex align-items-start">
                            <div class="mr-3">
                                <div class="d-flex align-items-center justify-content-center"
                                     style="width:64px;height:64px;border-radius:1rem;background:#1e3a8a;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-white" width="32" height="32" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 21h18M6 21V4h12v17M9 8h6M9 12h6M9 16h6" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <h1 class="h3 mb-0 mr-2 text-white">{{ $department->department_name }}</h1>
                                    <span class="dpb4-badge-soft dpb4-badge-emerald">
                                        {{ $department->status == 'Published' ? 'Aktiv' : 'Inaktiv' }}
                                    </span>
                                </div>
                                <p class="mt-1 mb-2 text-black">
                                    {{ $department->description ?: 'Keine Beschreibung hinterlegt.' }}
                                </p>
                                <div class="small text-black">
                                    <span class="mr-3 d-inline-flex align-items-center">
                                        <svg class="mr-1" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 22s7-5.686 7-12a7 7 0 10-14 0c0 6.314 7 12 7 12z" stroke="currentColor" stroke-width="1.5"/>
                                            <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                        {{ $department->branch }}
                                    </span>
                                    <span class="mr-3 d-inline-flex align-items-center">
                                        <svg class="mr-1" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M3 21h18M6 21V4h12v17M9 8h6M9 12h6M9 16h6" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                        Abteilungs-ID: {{ $department->id }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Abteilungsleiter --}}
                        <div class="col-lg-4 mt-3 mt-lg-0">
                            <div class="dpb4-glass p-3">
                                <div class="d-flex align-items-center">
                                    <img class="dpb4-avatar-36 mr-3"
                                         src="{{ $department->emp_image ? asset('images/employee/' . $department->emp_image) : 'https://via.placeholder.com/36' }}"
                                         alt="Abteilungsleiter">
                                    <div>
                                        <div class="text-uppercase small dpb4-text-muted">Abteilungsleiter</div>
                                        <div class="h6 mb-1 text-white">
                                            {{ $department->emp_name }} {{ $department->emp_lastname }}
                                        </div>
                                        <div class="small">
                                            <span class="dpb4-dot" style="background:#34d399;"></span>
                                            Verfügbar
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex">
                                    <button class="btn btn-sm dpb4-btn-soft mr-2">Nachricht</button>
                                    <button class="btn btn-sm dpb4-btn-soft mr-2">Termin planen</button>
                                    <button class="btn btn-sm dpb4-btn-soft">Anrufen</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KPI-Karten --}}
                    <div class="row mt-4">
                        <div class="col-6 col-sm-3 mb-2 mb-sm-0">
                            <div class="dpb4-glass p-3 h-100">
                                <div class="small dpb4-text-muted">Aktive Projekte</div>
                                <div class="h3 mb-0">{{ $projectsCount ?? 0 }}</div>
                                <div class="small text-success">+2 in diesem Monat</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mb-2 mb-sm-0">
                            <div class="dpb4-glass p-3 h-100">
                                <div class="small dpb4-text-muted">Offene Tickets</div>
                                <div class="h3 mb-0">{{ $ticketsCount ?? 0 }}</div>
                                <div class="small text-warning">SLA-Risiko: {{ $slaRiskCount ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mt-2 mt-sm-0">
                            <div class="dpb4-glass p-3 h-100">
                                <div class="small dpb4-text-muted">Teammitglieder</div>
                                <div class="h3 mb-0">{{ $employees->count() }}</div>
                                <div class="small dpb4-text-muted">Mitarbeiter im Team</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 mt-2 mt-sm-0">
                            <div class="dpb4-glass p-3 h-100">
                                <div class="small dpb4-text-muted">Termine (7 Tage)</div>
                                <div class="h3 mb-0">{{ $appointmentsCount ?? 0 }}</div>
                                <div class="small" style="color:#f0abfc;">davon 3 extern</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABS --}}
            <div id="dpb4-tabs" class="dpb4-glass mb-3 p-1">
                <ul class="nav nav-pills align-items-center" id="dpb4-tab-nav">
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link py-2 px-3 dpb4-tab-link active" data-tab="projects">
                            Projekte
                            <span class="badge badge-light ml-2" id="dpb4-count-projects">{{ $projectsCount ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link py-2 px-3 dpb4-tab-link" data-tab="tickets">
                            Tickets
                            <span class="badge badge-light ml-2" id="dpb4-count-tickets">{{ $ticketsCount ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link py-2 px-3 dpb4-tab-link" data-tab="team">
                            Team
                            <span class="badge badge-light ml-2" id="dpb4-count-team">{{ $employees->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link py-2 px-3 dpb4-tab-link" data-tab="tasks">
                            Aufgaben
                            <span class="badge badge-light ml-2" id="dpb4-count-tasks">0</span>
                        </a>
                    </li>
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link py-2 px-3 dpb4-tab-link" data-tab="calendar">
                            Kalender
                            <span class="badge badge-light ml-2" id="dpb4-count-calendar">{{ $appointmentsCount ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link py-2 px-3 dpb4-tab-link" data-tab="expenses">
                            Kosten
                            <span class="badge badge-light ml-2" id="dpb4-count-expenses">0</span>
                        </a>
                    </li>

                    <li class="ml-auto d-none d-sm-flex align-items-center">
                        <input type="text" class="form-control  mr-2" placeholder="Globale Suche…">
                        <select class="form-control ">
                            <option>Alle</option>
                            <option>Aktiv</option>
                            <option>Archiviert</option>
                        </select>
                    </li>
                </ul>
            </div>

              <div class="row">
                {{-- LINKE SPALTE --}}
                <div class="col-12">

                    {{-- TAB: PROJEKTE (nur Platzhalter / statisch) --}}
                    <div id="dpb4-tab-projects" class="dpb4-glass p-3 mb-3 dpb4-tab-pane">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="dpb4-section-title">Projekte</div>
                        </div>
                        <p class="mb-0 dpb4-text-muted">
                             
                        </p>
                    </div>

                    {{-- TAB: TICKETS --}}
                     <div id="dpb4-tab-tickets" class="dpb4-glass p-3 mb-3 dpb4-tab-pane d-none">
                        {{-- Filter / Toolbar --}}
                        <div class="dpb4-ticket-toolbar mb-3">
                            <div class="dpb4-ticket-filters">
                                <div class="dpb4-ticket-filter">
                                    <label for="dpb4-ticket-priority" class="small dpb4-text-muted d-block mb-1">
                                        Priorität
                                    </label>
                                    <select id="dpb4-ticket-priority" class="form-control ">
                                        <option value="">Alle Prioritäten</option>
                                        <option value="Critical">Kritisch</option>
                                        <option value="High">Hoch</option>
                                        <option value="Medium">Mittel</option>
                                        <option value="Low">Niedrig</option>
                                    </select>
                                </div>

                                <div class="dpb4-ticket-filter">
                                    <label for="dpb4-ticket-status" class="small dpb4-text-muted d-block mb-1">
                                        Status
                                    </label>
                                    <select id="dpb4-ticket-status" class="form-control ">
                                        <option value="">Alle Status</option>
                                        <option value="Open">Offen</option>
                                        <option value="In Progress">In Bearbeitung</option>
                                        <option value="Waiting">Warten</option>
                                        <option value="Resolved">Gelöst</option>
                                    </select>
                                </div>

                                <div class="dpb4-ticket-filter">
                                    <label for="dpb4-ticket-sort" class="small dpb4-text-muted d-block mb-1">
                                        Sortierung
                                    </label>
                                    <select id="dpb4-ticket-sort" class="form-control ">
                                        <option value="sla">Nach SLA sortieren</option>
                                        <option value="priority">Nach Priorität sortieren</option>
                                        <option value="updated">Nach Aktualisierung sortieren</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Desktop: Tabelle --}}
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover dpb4-table mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Betreff</th>
                                    <th>Anfragender</th>
                                    <th>Priorität</th>
                                    <th>Status</th>
                                    <th>SLA</th>
                                    <th class="text-right">Aktion</th>
                                </tr>
                                </thead>
                                <tbody id="dpb4-tickets-body">
                                {{-- wird per AJAX gefüllt --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile: Kartenansicht --}}
                        <div class="d-block d-md-none" id="dpb4-tickets-cards">
                            {{-- wird per AJAX gefüllt --}}
                        </div>
                    </div>

                   
                    {{-- TAB: TEAM --}}
                    <div id="dpb4-tab-team" class="dpb4-glass p-3 mb-3 dpb4-tab-pane d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="dpb4-section-title">Team</div>
                            <div class="small dpb4-text-muted">
                                {{ $employees->count() }} Teammitglieder
                            </div>
                        </div>

                        <div class="row" id="dpb4-team-grid">
                            {{-- wird per AJAX gefüllt --}}
                        </div>
                    </div>


                    {{-- TAB: AUFGABEN --}}
                    <div id="dpb4-tab-tasks" class="dpb4-glass p-3 mb-3 dpb4-tab-pane d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="dpb4-section-title">Aufgaben</div>
                            <div class="d-flex">
                                <select id="dpb4-task-filter" class="form-control  mr-2">
                                  <option value="">Alle</option>
                                  <option value="open">Offen</option>
                                  <option value="in_progress">In Bearbeitung</option>
                                  <option value="completed">Erledigt</option>
                                  <option value="rejected">Abgelehnt</option>
                                  <option value="junk">Junk</option>
                              </select>

                                <select id="dpb4-task-sort" class="form-control ">
                                    <option value="due">Nach Fälligkeitsdatum sortieren</option>
                                    <option value="priority">Nach Priorität sortieren</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Liste links --}}
                            <div class="col-md-6">
                                <div class="dpb4-soft-card p-3 h-100">
                                    <div class="d-flex justify-content-between">
                                        <div class="font-weight-bold">Meine Aufgaben</div>
                                        <div class="small dpb4-text-muted">
                                            <span id="dpb4-task-count">0</span> Einträge
                                        </div>
                                    </div>

                                    <ul class="list-unstyled mt-2 mb-0" id="dpb4-task-list">
                                        {{-- wird per AJAX gefüllt --}}
                                    </ul>
                                </div>
                            </div>

                            {{-- Snapshot rechts --}}
                            <div class="col-md-6 mt-3 mt-md-0">
                                <div class="dpb4-soft-card p-3 h-100">
                                    <div class="font-weight-bold mb-2">Sprint-Übersicht</div>

                                      <div class="row text-center mt-1">
                                        <div class="col-4 mb-2">
                                            <div class="dpb4-badge-info">Offen</div>
                                            <div class="h3 mt-1 mb-0" id="dpb4-metric-open">0</div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class="dpb4-badge-primary">In Bearbeitung</div>
                                            <div class="h3 mt-1 mb-0" id="dpb4-metric-inprogress">0</div>
                                        </div>
                                        <div class="col-4 mb-2">
                                            <div class="dpb4-badge-success">Erledigt</div>
                                            <div class="h3 mt-1 mb-0" id="dpb4-metric-completed">0</div>
                                        </div>
                                        <div class="col-4 mt-3">
                                            <div class="dpb4-badge-warning">Abgelehnt</div>
                                            <div class="h3 mt-1 mb-0" id="dpb4-metric-rejected">0</div>
                                        </div>
                                        <div class="col-4 mt-3">
                                            <div class="dpb4-badge-danger">Junk</div>
                                            <div class="h3 mt-1 mb-0" id="dpb4-metric-junk">0</div>
                                        </div>
                                        <div class="col-4 mt-3">
                                            <div class="dpb4-badge-secondary">Sonstige</div>
                                            <div class="h3 mt-1 mb-0" id="dpb4-metric-other">0</div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="small dpb4-text-muted mb-1">Burndown</div>
                                        <div class="progress" style="height:6px;">
                                            <div id="dpb4-burndown-bar" class="progress-bar" role="progressbar"
                                                 style="width:0%;background-image:linear-gradient(90deg,#3b82f6,#10b981);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB: KALENDER --}}
                    <div id="dpb4-tab-calendar" class="dpb4-glass p-3 mb-3 dpb4-tab-pane d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Kalender</h5>
                            <div>
                                <button id="prevMonth" class="btn btn-sm dpb4-btn-soft mr-2">&laquo; Vorheriger Monat</button>
                                <button id="nextMonth" class="btn btn-sm dpb4-btn-soft">Nächster Monat &raquo;</button>
                            </div>
                        </div>

                        <h5 id="calendarTitle" class="mb-2"></h5>

                        <div id="calendar" class="dpb4-calendar-grid mb-2">
                            {{-- wird per JS aufgebaut --}}
                        </div>

                        <div id="dayEventsPanel" class="mt-2 d-none">
                            <h6 class="font-weight-bold mb-1">
                                <span id="selectedDate" class="text-dark"></span>
                            </h6>
                            <div id="eventCards"></div>
                        </div>
                    </div>
                </div>

                {{-- RECHTE SPALTE: KOSTEN --}}  
                  <div id="dpb4-tab-expenses" class="dpb4-glass p-3 mb-3 dpb4-tab-pane d-none"> 
                      <h5 class="mb-3">Abteilungskosten – Übersicht</h5>

                      <div class="row text-center">
                          <div class="col-12 col-md-4 mb-3">
                              <div class="dpb4-soft-card p-3">
                                  <div class="small dpb4-text-muted">Monatlich</div>
                                  <div class="h4 text-primary" id="dpb4-exp-monthly">0 €</div>
                              </div>
                          </div>
                          <div class="col-12 col-md-4 mb-3">
                              <div class="dpb4-soft-card p-3">
                                  <div class="small dpb4-text-muted">Quartal</div>
                                  <div class="h4 text-info" id="dpb4-exp-quarterly">0 €</div>
                              </div>
                          </div>
                          <div class="col-12 col-md-4 mb-3">
                              <div class="dpb4-soft-card p-3">
                                  <div class="small dpb4-text-muted">Jährlich</div>
                                  <div class="h4 text-success" id="dpb4-exp-yearly">0 €</div>
                              </div>
                          </div>
                      </div>

                      <div class="mt-3">
                          <div class="small dpb4-text-muted mb-1">Verteilung (Monat / Jahr)</div>
                          <div class="progress" style="height:8px;">
                              <div id="dpb4-exp-dist-bar" class="progress-bar bg-primary" role="progressbar" style="width:0%;"></div>
                          </div>
                          <div class="d-flex justify-content-between small dpb4-text-muted mt-1">
                              <span>Monat</span><span>Jahr</span>
                          </div>
                      </div>

                      <div class="mt-4">
                          <canvas id="expenseChart" height="140"></canvas>
                      </div>
                  </div>
                 
            </div> {{-- /row --}}
        </div> {{-- /content-body --}}
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const departmentId = {{ $department->id }};
    let dpb4Tickets = [];
    let dpb4Team = [];
    let dpb4Tasks = [];
    let dpb4Expenses = { monthly:0, quarterly:0, yearly:0 };
    let dpb4ExpenseChart = null;

    /* ==== HELFER: BADGE-KLASSEN ==== */
    function priorityBadgeClass(p) {
        if (p === 'Critical') return 'badge badge-danger';
        if (p === 'High')     return 'badge badge-warning';
        if (p === 'Medium')   return 'badge badge-primary';
        return 'badge badge-secondary';
    }

    function priorityDotStyle(p) {
        if (p === 'Critical') return 'background:#f87171;';
        if (p === 'High')     return 'background:#fbbf24;';
        if (p === 'Medium')   return 'background:#818cf8;';
        return 'background:#9ca3af;';
    }

    function statusBadgeClass(s) {
        const v = (s || '').toString().toLowerCase();

        if (['open', 'in_progress', 'in progress', 'new', 'offen'].includes(v)) {
            return 'badge badge-info';
        }
        if (['review', 'pending', 'waiting'].includes(v)) {
            return 'badge badge-warning';
        }
        if (['done', 'completed', 'resolved'].includes(v)) {
            return 'badge badge-success';
        }
        if (['blocked', 'cancel', 'rejected', 'junk'].includes(v)) {
            return 'badge badge-danger';
        }
        return 'badge badge-light';
    }

    /* ==== TABS ==== */
    function initTabs() {
        $('.dpb4-tab-link').on('click', function (e) {
            e.preventDefault();
            const tab = $(this).data('tab');

            $('.dpb4-tab-link').removeClass('active');
            $(this).addClass('active');

            $('.dpb4-tab-pane').addClass('d-none');
            $('#dpb4-tab-' + tab).removeClass('d-none');
        });
    }

    /* ==== TICKETS PER AJAX LADEN ==== */
    function loadTickets() {
        const url = "{{ url('get/department/ticket') }}/" + departmentId;

        $.getJSON(url, function (res) {
            if (!res || !res.tickets) {
                return;
            }

            dpb4Tickets = res.tickets.map(function (t) {
                return {
                    id: t.id,
                    code: t.ticket_no ? '#' + t.ticket_no : '#' + t.id,
                    subject: t.title || '(Kein Titel)',
                    product: t.product || '',
                    priority: t.priority || 'Medium',
                    status: t.status || 'Open',
                    sla: t.sla || '—',
                    slaProgress: t.sla_progress || 0,
                    deptCount: t.dept_employee_count || 0,
                    requester: {
                        name: (t.dept_employees && t.dept_employees.length)
                            ? (t.dept_employees[0].name)
                            : 'Unzugeordnet',
                        avatar: (t.dept_employees && t.dept_employees.length && t.dept_employees[0].image)
                            ? t.dept_employees[0].image
                            : 'https://i.pravatar.cc/40?u=' + t.id
                    },
                    updated: t.updated_at || new Date().toISOString().slice(0, 10)
                };
            });

            $('#dpb4-count-tickets').text(dpb4Tickets.length);
            renderTickets();
        });
    }

    function renderTickets() {
        const priorityFilter = $('#dpb4-ticket-priority').val() || '';
        const statusFilter   = $('#dpb4-ticket-status').val() || '';
        const sortMode       = $('#dpb4-ticket-sort').val() || 'sla';

        let list = dpb4Tickets.slice();

        if (priorityFilter) {
            list = list.filter(t => t.priority === priorityFilter);
        }
        if (statusFilter) {
            list = list.filter(t => t.status === statusFilter);
        }

        if (sortMode === 'priority') {
            const order = { Critical: 0, High: 1, Medium: 2, Low: 3 };
            list.sort((a, b) => (order[a.priority] || 9) - (order[b.priority] || 9));
        } else if (sortMode === 'updated') {
            list.sort((a, b) => new Date(b.updated) - new Date(a.updated));
        } else {
            list.sort((a, b) => b.slaProgress - a.slaProgress);
        }

        // Desktop: Tabellen-HTML
        let htmlTable = '';
        // Mobile: Karten-HTML
        let htmlCards = '';

        list.forEach(function (t) {
            // Tabelle (md+)
            htmlTable += `
                <tr>
                    <td class="text-muted">${t.code}</td>
                    <td>
                        <div class="font-weight-bold text-dark">${t.subject}</div>
                        <div class="small text-muted">${t.product || '&nbsp;'}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="${t.requester.avatar}" class="dpb4-avatar-24 mr-2" alt="">
                            <span>${t.requester.name}</span>
                        </div>
                    </td>
                    <td>
                        <span class="${priorityBadgeClass(t.priority)}">
                            <span class="dpb4-dot mr-1" style="${priorityDotStyle(t.priority)}"></span>
                            ${t.priority}
                        </span>
                    </td>
                    <td>
                        <span class="${statusBadgeClass(t.status)}">${t.status}</span>
                    </td>
                    <td style="min-width:160px;">
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 mr-2" style="height:6px;">
                                <div class="progress-bar"
                                    role="progressbar"
                                    style="width:${t.slaProgress}%;background-image:linear-gradient(90deg,#f43f5e,#f59e0b);">
                                </div>
                            </div>
                            <span class="small text-muted">${t.sla}</span>
                        </div>
                    </td>
                    <td class="text-right">
                        <a class="btn btn-outline-secondary btn-sm"
                          href="/problem/profile/${t.id}">
                            Details
                        </a>
                    </td>
                </tr>
            `;

            // Kartenlayout (xs–sm)
            htmlCards += `
                <div class="dpb4-ticket-card">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="small text-muted">${t.code}</div>
                        <a href="/problem/profile/${t.id}" class="btn btn-xs btn-outline-secondary">
                            Details
                        </a>
                    </div>

                    <div class="mb-2">
                        <div class="dpb4-ticket-card-label">Betreff</div>
                        <div class="font-weight-bold text-white">${t.subject}</div>
                        <div class="small text-muted">${t.product || ''}</div>
                    </div>

                    <div class="mb-2">
                        <div class="dpb4-ticket-card-label">Anfragender</div>
                        <div class="d-flex align-items-center">
                            <img src="${t.requester.avatar}" class="dpb4-avatar-24 mr-2" alt="">
                            <span class="small text-black">${t.requester.name}</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between mb-2">
                        <div class="mr-2 mb-2">
                            <div class="dpb4-ticket-card-label">Priorität</div>
                            <span class="${priorityBadgeClass(t.priority)}">
                                <span class="dpb4-dot mr-1" style="${priorityDotStyle(t.priority)}"></span>
                                ${t.priority}
                            </span>
                        </div>
                        <div class="mb-2">
                            <div class="dpb4-ticket-card-label">Status</div>
                            <span class="${statusBadgeClass(t.status)}">${t.status}</span>
                        </div>
                    </div>

                    <div>
                        <div class="dpb4-ticket-card-label">SLA</div>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 mr-2" style="height:6px;">
                                <div class="progress-bar"
                                    role="progressbar"
                                    style="width:${t.slaProgress}%;background-image:linear-gradient(90deg,#f43f5e,#f59e0b);">
                                </div>
                            </div>
                            <span class="small text-muted">${t.sla}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        $('#dpb4-tickets-body').html(htmlTable);
        $('#dpb4-tickets-cards').html(htmlCards);
    }
 
    /* ==== TEAM PER AJAX LADEN ==== */
       
          function loadTeam() {
            const url = "{{ route('department.profile.json', ['id' => $department->id]) }}";

            console.log('[TEAM] Request URL:', url);

            $.getJSON(url)
                .done(function (res) {
                    console.log('[TEAM] API response:', res);

                    if (!res || !res.employees) {
                        console.warn('[TEAM] No employees in response');
                        return;
                    }

                    dpb4Team = res.employees.map(function (e) {
                        console.log('[TEAM] Mapping employee:', e);

                        return {
                            id: e.id,
                            name: e.name + ' ' + e.lastname,
                            role: e.position || '—',
                            status: e.status === 'Active' ? 'online' : 'offline',
                            email: e.email || '',
                            avatar: e.image
                                ? "{{ asset('images/employee') }}/" + e.image
                                : '/images/default-avatar.svg',

                            departmentCount:        e.department_count        || 0,
                            positionCount:          e.position_count          || 0,
                            leaveDays:              e.leave_days              || 0,
                            sickDays:               e.sick_days               || 0,
                            recurringRules:         e.recurring_rules         || 0,
                            recurringWeeklyDays:    e.recurring_weekly_days   || 0,
                        };
                    });

                    console.log('[TEAM] dpb4Team after mapping:', dpb4Team);

                    $('#dpb4-count-team').text(dpb4Team.length);
                    renderTeam();
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.error('[TEAM] API error:', textStatus, errorThrown);
                    console.error('[TEAM] Response text:', jqXHR.responseText);
                });
        }



        function renderTeam() {
            const list = dpb4Team.slice();

            let html = '';
            list.forEach(function (m) {
                const statusText  = m.status === 'online' ? 'Online' : 'Offline';
                const statusColor = m.status === 'online' ? '#34d399' : '#6b7280';

                const recurringText = m.recurringRules > 0
                    ? `<strong>${m.recurringWeeklyDays}</strong> Tage/Woche (wiederkehrend)`
                    : 'Keine wiederkehrenden Abwesenheiten';

                html += `
                    <div class="col-sm-6 col-xl-4 mb-3">
                        <div class="dpb4-soft-card p-2 h-100">
                            <div class="d-flex">
                                <img src="${m.avatar}" alt="${m.name}" class="dpb4-avatar-36 mr-3">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="font-weight-bold text-dark mr-2">${m.name}</div>
                                        <span class="badge badge-light">${m.role}</span>
                                    </div>
                                    <div class="small dpb4-text-muted">${m.email}</div>
                                    <div class="small mt-1">
                                        <span class="dpb4-dot mr-1" style="background:${statusColor};"></span>
                                        ${statusText}
                                    </div>

                                    <div class="small mt-2">
                                        <div class="d-flex flex-wrap">
                                            <span class="mr-3">
                                                <strong>${m.departmentCount}</strong> Abteilungen
                                            </span>
                                            <span class="mr-3">
                                                <strong>${m.positionCount}</strong> Positionen
                                            </span>
                                        </div>
                                        <div class="d-flex flex-wrap mt-1">
                                            <span class="mr-3">
                                                <strong>${m.leaveDays}</strong> Urlaubstage
                                            </span>
                                            <span class="mr-3">
                                                <strong>${m.sickDays}</strong> Kranktage
                                            </span>
                                        </div>
                                        <div class="mt-1">
                                            ${recurringText}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <a class="btn btn-sm dpb4-btn-soft" href="/employee_profile/${m.id}">
                                    Profil öffnen
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#dpb4-team-grid').html(html);
        }

    /* ==== AUFGABEN PER AJAX LADEN ==== */
      function loadTasks() {
          const url = "{{ url('department') }}/" + departmentId + "/tasks/json";

          $.getJSON(url, function (res) {
              if (!res || !res.tasks) return;

              dpb4Tasks = res.tasks.map(function (t) {
                  const rawStatus = (t.status || '').toString().toLowerCase().trim();
                  let status = 'open';

                  if (['open', 'offen', 'new'].includes(rawStatus)) {
                      status = 'open';
                  } else if (['in progress', 'in_progress', 'progress', 'in bearbeitung'].includes(rawStatus)) {
                      status = 'in_progress';
                  } else if (['done', 'completed', 'erledigt'].includes(rawStatus)) {
                      status = 'completed';
                  } else if (['rejected', 'reject', 'abgelehnt'].includes(rawStatus)) {
                      status = 'rejected';
                  } else if (['junk', 'spam'].includes(rawStatus)) {
                      status = 'junk';
                  } else {
                      status = 'other';
                  }

                  return {
                      id: t.id,
                      title: t.title,
                      // normalized status used everywhere:
                      status: status,
                      priority: t.priority,
                      start: t.start,
                      due: t.due,
                      assignees: t.assignees || []
                  };
              });

              $('#dpb4-count-tasks').text(dpb4Tasks.length);
              renderTasks();
          });
      }


      function taskStatusLabel(status) {
          switch ((status || '').toString().toLowerCase()) {
              case 'open':
                  return 'Offen';
              case 'in_progress':
                  return 'In Bearbeitung';
              case 'completed':
                  return 'Erledigt';
              case 'rejected':
                  return 'Abgelehnt';
              case 'junk':
                  return 'Junk';
              default:
                  return 'Sonstige';
          }
      }


      function renderTasks() {
          const statusFilter = $('#dpb4-task-filter').val() || '';
          const sortMode     = $('#dpb4-task-sort').val() || 'priority';

          // Work on a copy
          let list = dpb4Tasks.slice();

          // Filter by normalized status
          if (statusFilter) {
              list = list.filter(t => (t.status || '') === statusFilter);
          }

          // Sort
          if (sortMode === 'priority') {
              const order = { Critical: 0, High: 1, Medium: 2, Low: 3 };
              list.sort((a, b) => (order[a.priority] || 9) - (order[b.priority] || 9));
          } else if (sortMode === 'due') {
              list.sort((a, b) => new Date(a.due) - new Date(b.due));
          }

          $('#dpb4-task-count').text(list.length);

          // === Metrics über alle Tasks (nicht nur gefilterte Liste) ===
          const all = dpb4Tasks;
          const open       = all.filter(t => t.status === 'open').length;
          const inProgress = all.filter(t => t.status === 'in_progress').length;
          const completed  = all.filter(t => t.status === 'completed').length;
          const rejected   = all.filter(t => t.status === 'rejected').length;
          const junk       = all.filter(t => t.status === 'junk').length;
          const other      = all.filter(t => !['open','in_progress','completed','rejected','junk'].includes(t.status)).length;

          const total      = Math.max(all.length, 1);
          const burndown   = Math.round((completed / total) * 100);

          $('#dpb4-metric-open').text(open);
          $('#dpb4-metric-inprogress').text(inProgress);
          $('#dpb4-metric-completed').text(completed);
          $('#dpb4-metric-rejected').text(rejected);
          $('#dpb4-metric-junk').text(junk);
          $('#dpb4-metric-other').text(other);
          $('#dpb4-burndown-bar').css('width', burndown + '%');

          // === List-HTML ===
          let html = '';
          list.forEach(function (task) {
              const assigneesHtml = (task.assignees || []).map(a =>
                  `<img src="${a.avatar}" class="dpb4-avatar-24 ml-1" alt="${a.name}">`
              ).join('');

              html += `
                  <li class="dpb4-soft-card p-2 mb-2">
                      <div class="d-flex justify-content-between">
                          <div>
                              <div class="d-flex align-items-center">
                                  <div class="small text-dark mr-2">${task.title || 'Unbekannt'}</div>
                                  <span class="${statusBadgeClass(task.status)}">
                                      ${taskStatusLabel(task.status)}
                                  </span>
                              </div>
                          </div>
                          <div class="small text-muted">${task.due || ''}</div>
                      </div>
                      <div class="d-flex justify-content-between mt-2 small">
                          <span class="${priorityBadgeClass(task.priority)}">
                              <span class="dpb4-dot mr-1" style="${priorityDotStyle(task.priority)}"></span>
                              ${task.priority || ''}
                          </span>
                          <div class="d-flex">
                              ${assigneesHtml}
                          </div>
                      </div>
                  </li>
              `;
          });

          $('#dpb4-task-list').html(html);
      }

    /* ==== KOSTEN PER AJAX LADEN ==== */
    function loadExpenses() {
      const url = "{{ url('department') }}/" + departmentId + "/expense/json";
      console.log('[EXP] Request URL:', url);

      $.getJSON(url)
          .done(function (res) {
              console.log('[EXP] Raw response:', res);

              if (!res) {
                  console.warn('[EXP] No response object');
                  return;
              }

              // Accept both shapes:
              // 1) { expenses: { monthly:..., quarterly:..., yearly:... } }
              // 2) { monthly:..., quarterly:..., yearly:... }
              let exp = null;

              if (res.expenses && typeof res.expenses === 'object') {
                  exp = res.expenses;
              } else if (typeof res === 'object') {
                  exp = res;
              }

              if (!exp) {
                  console.warn('[EXP] Could not find expenses structure in response');
                  return;
              }

              dpb4Expenses = exp;

              const m = Number(exp.monthly   ?? exp.month    ?? 0);
              const q = Number(exp.quarterly ?? exp.quarter  ?? 0);
              const y = Number(exp.yearly    ?? exp.year     ?? 0);

              console.log('[EXP] Parsed values -> monthly:', m, 'quarterly:', q, 'yearly:', y);

              $('#dpb4-exp-monthly').text(m.toLocaleString('de-DE') + ' €');
              $('#dpb4-exp-quarterly').text(q.toLocaleString('de-DE') + ' €');
              $('#dpb4-exp-yearly').text(y.toLocaleString('de-DE') + ' €');

              // simple counter on the tab badge
              $('#dpb4-count-expenses').text(y > 0 || m > 0 || q > 0 ? 1 : 0);

              const dist = (y > 0) ? (m / y * 100) : 0;
              $('#dpb4-exp-dist-bar').css('width', dist.toFixed(1) + '%');

              initExpenseChart(m, q, y);
          })
          .fail(function (jqXHR, textStatus, errorThrown) {
              console.error('[EXP] API error:', textStatus, errorThrown);
              console.error('[EXP] Response text:', jqXHR.responseText);
          });
  }

    function initExpenseChart(m, q, y) {
      const ctx = document.getElementById('expenseChart');
      if (!ctx) return;

      if (dpb4ExpenseChart) {
          dpb4ExpenseChart.destroy();
      }

      dpb4ExpenseChart = new Chart(ctx, {
          type: 'bar',
          data: {
              labels: ['Monat', 'Quartal', 'Jahr'],
              datasets: [{
                  label: 'Kosten (€)',
                  data: [m, q, y],
                  backgroundColor: [
                      '#74b2d4', // Monat
                      '#c0d8ea', // Quartal
                      '#93c21c'  // Jahr
                  ],
                  borderRadius: 8
              }]
          },
          options: {
              responsive: true,
              plugins: {
                  legend: { display: false },
                  tooltip: {
                      callbacks: {
                          label: function (ctx) {
                              return ctx.raw.toLocaleString('de-DE') + ' €';
                          }
                      }
                  }
              },
              scales: {
                  y: {
                      beginAtZero: true,
                      ticks: {
                          callback: function (val) {
                              return val.toLocaleString('de-DE') + ' €';
                          }
                      },
                      grid: {
                          color: 'rgba(192,216,234,0.4)' // #c0d8ea
                      }
                  },
                  x: {
                      grid: { display: false }
                  }
              }
          }
      });
  }


    /* ==== KALENDER PER AJAX LADEN ==== */
    let allEvents = [];
    let currentDate = new Date();

    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    function loadCalendar() {
        const url = "{{ route('department.calendar', ':id') }}".replace(':id', departmentId);

        $.getJSON(url, function (data) {
            allEvents = data.data || [];
            renderCalendar(currentDate, allEvents);
        });
    }

    function renderCalendar(date, events) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        $('#calendarTitle').text(
            date.toLocaleDateString('de-DE', { month: 'long', year: 'numeric' })
        );

        const $cal = $('#calendar');
        $cal.empty();

        const weekdays = ['So','Mo','Di','Mi','Do','Fr','Sa'];
        weekdays.forEach(function (d) {
            $cal.append('<div class="dpb4-calendar-header">' + d + '</div>');
        });

        for (let i = 0; i < firstDay; i++) {
            $cal.append('<div></div>');
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const thisDate = new Date(year, month, d);
            const dateStr = formatDate(thisDate);
            const eventsForDay = events.filter(ev => ev.start_date && ev.start_date.startsWith(dateStr));

            let numberClass = 'dpb4-day-number';
            if (formatDate(new Date()) === dateStr) {
                numberClass += ' dpb4-day-number-today';
            } else if (eventsForDay.length > 0) {
                numberClass += ' dpb4-day-number-has-events';
            }

            const inner = `<div class="${numberClass}">${d}</div>`;
            const cell = `<div class="day-cell" data-date="${dateStr}">${inner}</div>`;

            $cal.append(cell);
        }

        $('#prevMonth').off('click').on('click', function () {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate, allEvents);
        });

        $('#nextMonth').off('click').on('click', function () {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate, allEvents);
        });

        $cal.find('.day-cell[data-date]').on('click', function () {
            const dateStr = $(this).data('date');
            showDayEvents(dateStr);
        });
    }

    function showDayEvents(dateStr) {
        const eventsForDay = allEvents.filter(ev => ev.start_date && ev.start_date.startsWith(dateStr));
        const panel = document.getElementById('dayEventsPanel');
        const selectedDateEl = document.getElementById('selectedDate');
        const eventCards = document.getElementById('eventCards');

        if (!eventsForDay.length) {
            panel.classList.add('d-none');
            return;
        }

        panel.classList.remove('d-none');

        selectedDateEl.textContent = new Date(dateStr).toLocaleDateString('de-DE', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        let html = `
            <div class="p-2 mb-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class=" font-weight-semibold mb-0">Termine</h5>
                    <div class="d-flex">
                        <input class="form-control  mr-2" placeholder="Suche…">
                        <select class="form-control ">
                            <option value="">Alle Typen</option>
                            <option value="Task">Aufgabe</option>
                            <option value="Appointment">Termin</option>
                            <option value="Holiday">Feiertag</option>
                            <option value="Sick">Krank</option>
                        </select>
                    </div>
                </div>
        `;

        eventsForDay.forEach(function (ev) {
            const color = ev.taskColor || '#74b2d4';
            const startTime = ev.start_time || 'Ganztägig';
            const endTime = ev.end_time ? '– ' + ev.end_time : '';
            const desc = ev.description || 'Keine Beschreibung';

            const employees = (ev.employees || []).map(emp => `
                <img src="{{ asset('images/employee') }}/${emp.image}"
                     class="dpb4-avatar-24 mr-1"
                     title="${emp.name} ${emp.lastname}"
                     alt="">
            `).join('');

            html += `
                <div class="dpb4-event-card" style="border-color:${color};">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex">
                            <div class=""
                                 style="width:40px;height:40px;border-radius:.5rem;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="text-muted">
                                    <path d="M8 7V3m8 4V3M5 11h14M6 5h12a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2z"
                                          stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-weight-bold text-white">${ev.title}</div>
                                <div class="text-sm dpb4-text-muted">${desc}</div>
                                <div class="text-xs dpb4-text-muted mt-1">
                                    ${startTime} ${endTime}
                                </div>
                                <div class="mt-2 d-flex">
                                    ${employees}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="{{ url('appointment_details') }}/${ev.id}"
                               class="btn btn-sm dpb4-btn-soft">
                                Details
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        eventCards.innerHTML = html;
    }

 

    /* ==== DOKUMENT READY ==== */
    $(function () {
        initTabs();  
          // Force initial tab state (projects visible, others hidden)
        $('#dpb4-tab-nav .dpb4-tab-link[data-tab="projects"]').trigger('click');

        loadTickets();
        loadTeam();
        loadTasks();
        loadExpenses();
        loadCalendar(); 
        
        $('#dpb4-ticket-priority, #dpb4-ticket-status, #dpb4-ticket-sort').on('change', renderTickets);
        $('#dpb4-task-filter, #dpb4-task-sort').on('change', renderTasks);
    });
</script>
@endsection
