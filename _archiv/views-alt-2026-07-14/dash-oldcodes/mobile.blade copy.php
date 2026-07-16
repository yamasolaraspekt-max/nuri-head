@extends('admin.layouts.app')

@section('title') Employee Dashboard @endsection

@section('style')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{ asset('css/icon.min.css')}}" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<style>
    :root {
        /* LIGHT THEME (default) */
        --bg-app: #eff6ff;
        --bg-shell: #ffffff;
        --bg-card: #ffffff;
        --bg-card-soft: #e5e7eb;
        --border-subtle: rgba(148, 163, 184, 0.45);
        --border-strong: rgba(148, 163, 184, 0.75);
        --text-main: #2c3e50;
        --text-muted: #6b7280;
        --accent-green: #8fc73e;
        --accent-blue: #74b2d4;
        --accent-pink: #e50656;
        --accent-soft-green: rgba(143, 199, 62, 0.18);
        --accent-soft-blue: rgba(116, 178, 212, 0.18);
        --shadow-soft: 0 18px 40px rgba(148, 163, 184, 0.35);
        --radius-lg: 18px;
        --radius-full: 999px;
    }

    [data-theme="dark"] {
        --bg-app: #020617;
        --bg-shell: #2c3e50;
        --bg-card: #111827;
        --bg-card-soft: #1f2933;
        --border-subtle: rgba(148, 163, 184, 0.4);
        --border-strong: rgba(148, 163, 184, 0.8);
        --text-main: #e5e7eb;
        --text-muted: #9ca3af;
        --accent-green: #8fc73e;
        --accent-blue: #74b2d4;
        --accent-pink: #e50656;
        --accent-soft-green: rgba(143, 199, 62, 0.2);
        --accent-soft-blue: rgba(116, 178, 212, 0.2);
        --shadow-soft: 0 18px 40px rgba(15, 23, 42, 0.7);
    }

    body {
        background: radial-gradient(circle at top left, var(--bg-card-soft), var(--bg-app));
        color: var(--text-main);
    }

    /* Layout shell */
    .dashboard-shell {
        background: transparent;
        min-height: calc(100vh - 4rem);
        border-radius: 1.5rem 1.5rem 0 0; 
        /* padding: 0.75rem 0.75rem 4.5rem; */
        position: relative;
        overflow: hidden;
    }

    @media (min-width: 768px) {
        .dashboard-shell {
            margin-top: .25rem;
            padding: 1.25rem 1.25rem 1.5rem;
            border-radius: 1.5rem;
        }
    }

    /* Header / profile */
    .app-header-card {
        background: white;
        border-radius: 1.25rem; 
        padding: .85rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
    }

    .app-header-user {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .app-header-avatar {
        width: 3.1rem;
        height: 3.1rem;
        border-radius: var(--radius-full);
        border: 2px solid var(--accent-blue); 
        object-fit: cover;
    }

    .app-header-meta small {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
    }

    .app-header-meta h1 {
        font-size: .95rem;
        color: var(--text-main);
        font-weight: 600;
        line-height: 1.2;
        display: flex;
        align-items: center;
        gap: .35rem;
    }

    .badge-pill {
        font-size: 0.65rem;
        padding: .1rem .5rem;
        border-radius: var(--radius-full);
        background: var(--accent-soft-blue);
        border: 1px solid var(--accent-blue);
        color: var(--text-main);
    }

    .chip-day {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        padding: .3rem .7rem;
        border-radius: var(--radius-full);
        background: var(--bg-card-soft);
        border: 1px solid var(--border-subtle);
        color: var(--text-main);
        font-size: .7rem;
        font-weight: 500;
    }

    .chip-day i {
        font-size: .9rem;
        color: #facc15;
    }

    .chip-cta {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .5rem .7rem;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--accent-blue), var(--accent-green));
        color: white;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: .08em; 
    }

    .chip-cta img {
        width: 1.7rem;
        height: 1.7rem;
    }

    /* KPI card */
    .kpi-card {
        background: var(--bg-card);
        border-radius: 1.25rem; 
        
        padding: .9rem .9rem 1rem;
        color: var(--text-main);
        position: relative;
        overflow: hidden;
    }

    .kpi-card::before {
        content: "";
        position: absolute;
        inset: -40%;
        background: white;
        opacity: 0.9;
        pointer-events: none;
    }

    .kpi-header {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        position: relative;
        z-index: 1;
    }

    .kpi-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: .4rem;
        text-transform: uppercase;
    }

    .kpi-title i {
        font-size: 14px;
        color: var(--accent-green);
    }

    .kpi-sub {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: .1rem;
    }

    .kpi-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 6px;
        border-radius: var(--radius-full);
        background: #cfe09b;
        font-size: .68rem;
        font-weight: 500;
        height: 23px;
        color: var(--text-main);
        backdrop-filter: blur(10px);
    }

    .kpi-badge-dot {
        width: .35rem;
        height: .35rem;
        border-radius: var(--radius-full);
        background: var(--accent-green); 
    }

    .kpi-chip-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin-top: .65rem;
        position: relative;
        z-index: 1;
    }

    .kpi-pill {
        display: flex;
        align-items: center;
        gap: .45rem;
        padding: .4rem .8rem;
        border-radius: var(--radius-full);
        border: 1px solid var(--border-subtle);
        background: var(--bg-card-soft);
        font-size: .7rem;
        color: var(--text-main);
        flex: 1;
    }

    .kpi-pill span.label {
        color: var(--text-muted);
    }

    .kpi-pill span.value {
        font-weight: 600;
    }

    .kpi-progress-shell {
        margin-top: .55rem;
        position: relative;
        z-index: 1;
    }

    .kpi-progress-track {
        width: 100%;
        height: .55rem;
        border-radius: var(--radius-full);
        background: var(--bg-card-soft);
        border: 1px solid var(--border-subtle);
        overflow: hidden;
        position: relative;
    }

    .kpi-progress-bar {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--accent-green), var(--accent-blue));
        width: 0%;
        transition: width .45s ease-out;
    }

    .kpi-progress-meta {
        display: flex;
        justify-content: space-between;
        margin-top: .35rem;
        font-size: .65rem;
        color: var(--text-muted);
    }

    /* Filter chips */
    .goal-filter-strip {
        display: flex;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
        border-radius: 20px;
        background: linear-gradient(135deg, var(--accent-blue), var(--accent-green));
        width: 471px;
        gap: .35rem;
        padding: 7px; 
    }
    .goal-filter-strip::-webkit-scrollbar { display: none; }

    .goal-filter-chip { 
        color: white;
        font-size: 10px;
        padding: 2.52px;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        white-space: nowrap;
        transition: all .18s ease-out;
    }

    .goal-filter-chip span.count {
        font-size: .6rem;
        color: white;
    }

    .goal-filter-chip.active { 
        border-color: transparent;
        color: white;
        font-size: 11px;
        font-weight: 600
    }

    /* Sort row */
    .goal-sort-shell {
        font-size: .68rem;
        color: var(--text-muted);
    }

    .goal-sort-shell select { 
        border-radius: 22px !important; 
        background: var(--bg-card-soft);
        color: var(--text-main);
        padding: 10px;
        font-size: .68rem;
    }

    .goal-sort-shell select:focus {
        outline: none; 
    }

    /* Goal list */
    #goalList {
        margin-top: .6rem;
        max-height: 19rem;
        overflow-y: auto;
        padding-right: .15rem;
    }

    #goalList::-webkit-scrollbar { width: 4px; }
    #goalList::-webkit-scrollbar-thumb {
        background: var(--border-subtle);
        border-radius: var(--radius-full);
    }

    .goal-item {
        border-radius: 1rem;
        padding: .45rem .6rem;
        margin-bottom: .35rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        border: 1px solid #e8e6e6;
        background: var(--bg-card);
    }

    .goal-item-main {
        display: flex;
        align-items: flex-start;
        gap: .4rem;
    }

    .goal-item-text {
        font-size: .7rem;
        color: var(--text-main);
    }

    .goal-item-text .title {
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: .2rem;
    }

    .goal-item-text .meta {
        margin-top: .12rem;
        font-size: .64rem;
        color: var(--text-muted);
    }

    .goal-item-tag {
        padding: .2rem .65rem;
        border-radius: var(--radius-full);
        font-size: .63rem;
        font-weight: 500;
        color: #0b1020;
        white-space: nowrap;
    }

    /* Tabs card */
    .tab-card {
        background: var(--bg-card);
        border-radius: 1.25rem; 
        
        padding: .85rem .9rem 1rem;
    }

    .tab-strip-wrap {
        overflow-x: auto;
        padding-bottom: .2rem;
        -ms-overflow-style: none;
        scrollbar-width: none;
        margin: 0 -.4rem;
    }
    .tab-strip-wrap::-webkit-scrollbar { display: none; }

    .tab-strip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .18rem;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--accent-blue), var(--accent-green));
        border: 1px solid var(--border-subtle);
    }

    .tab-button {
        border-radius: 999px;
        padding: .35rem .7rem;
        border: none;
        background: transparent;
        font-size: 10px;
        color: white;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        white-space: nowrap;
        position: relative;
        transition: all .16s ease-out;
    }

    .tab-button i {
        font-size: .95rem;
    }

    .tab-button.is-active { 
        color: #ffffff; 
        font-size: 11px;
        font-weight: 600
    }

    .tab-button.is-active i {
        color: #ffffff;
    }

    /* Search row */
    .search-shell {
        margin-top: .75rem;
        display: grid;
        gap: .4rem;
    }

    @media (min-width: 640px) {
        .search-shell {
            grid-template-columns: minmax(0, 2fr);
        }
    }

    .search-input {
        width: 100%;
        border-radius: 999px;
        border: 1px solid var(--border-subtle);
        background: var(--bg-card-soft);
        color: var(--text-main);
        padding: .35rem .85rem;
        padding-left: 2.1rem;
        font-size: .7rem;
        position: relative;
    }

    .search-input::placeholder {
        color: var(--text-muted);
    }

    .search-input:focus {
        outline: none; 
    }

    .search-shell-inner {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .4rem;
    }

    .search-shell-inner input,
    .search-shell-inner select {
        border-radius: 999px;
        border: 1px solid var(--border-subtle);
        background: var(--bg-card-soft);
        color: var(--text-main);
        padding: .35rem .7rem;
        font-size: .7rem;
    }

    .search-shell-inner input:focus,
    .search-shell-inner select:focus {
        outline: none;
        box-shadow: 0 0 0 1px var(--accent-blue);
    }

    .search-icon {
        position: absolute;
        inset-inline-start: .95rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: .9rem;
        color: var(--text-muted);
        pointer-events: none;
    }

    .tab-panel { margin-top: .7rem; }

    /* Notes card */
    .notes-card {
        background: var(--bg-card);
        border-radius: 1.25rem; 
        padding: .85rem .9rem 1rem;
        color: var(--text-main);

        /* make the card fill the full sidebar cell */
        height: 100%;
        min-height: 100%;
        display: flex;
        flex-direction: column;
    }


    .notes-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        margin-bottom: .55rem;
    }

    .notes-header h2 {
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: .35rem;
        color: var(--text-main);
        text-transform:uppercase;
    }

    .notes-header h2 i {
        font-size: 1rem;
        color: #93c21c;
    }

    .notes-actions {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }

    .btn-ghost {
        font-size: .65rem;
        padding: .25rem .55rem;
        border-radius: 999px;
        border: 1px solid var(--border-subtle);
        background: var(--bg-card-soft);
        color: var(--text-main);
    }

    .btn-primary-soft {
        font-size: .65rem;
        padding: .25rem .65rem;
        border-radius: 999px;
        border: 1px solid var(--accent-green);
        background: var(--accent-soft-green);
        color: var(--text-main);
    }

   .notes-body {
        /* let the inner container stretch fully */
        flex: 1;
        border-radius: 1rem;  
        padding: .5rem .4rem;
        overflow: hidden;

        /* important: allow child to grow */
        min-height: 0;
    }

    .notes-scroll {
        height: 100%;
        max-height: none;
        overflow-y: auto;
        padding-right: .2rem;
    }

    .notes-scroll::-webkit-scrollbar { width: 4px; }
        .notes-scroll::-webkit-scrollbar-thumb {
            background: var(--border-subtle);
            border-radius: 999px;
        }

    #personal-note-list {
        list-style: none;
        margin: 0;
        padding: 0;
        width:325px;
    }

    #personal-note-list > li.list-group-item {
        background: var(--bg-card) !important;
        border-radius: 1rem;
        border: 1px solid var(--border-strong);
        margin: .35rem .1rem !important;
        padding: .45rem .55rem !important;
        position: relative;
        overflow: hidden;
    }

    #personal-note-list > li.list-group-item::before {
        content: "";
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        width: .18rem;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(180deg, var(--accent-green), var(--accent-blue));
        opacity: .8;
    }

    #personal-note-list .title-field {
        font-size: .72rem !important;
        color: var(--text-main) !important;
        margin-bottom: .15rem;
    }

    #personal-note-list .note-field,
    #personal-note-list p {
        font-size: .65rem !important;
        color: var(--text-muted) !important;
    }

    .complete {
        text-decoration: line-through 2px #e83e8c;
        opacity: .8;
    }

    /* Calendar mini container */
    .calendar-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: .9rem;
        margin-top: .5rem;
    }

    .fc-calendar {
        width: 100%;
        max-width: 17rem;
        border-radius: 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        padding: .45rem;
    }

    .fc .fc-toolbar { display: none !important; }
    .fc .fc-day-today { background: rgba(116, 178, 212, 0.15) !important; }

    .fc-daygrid-day.haveEvent .fc-daygrid-day-number {
        background-color: var(--accent-green);
        color: white;
        border-radius: 999px;
        width: 1.3rem;
        height: 1.3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: .7rem;
    }

    .fc-daygrid-day-frame {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fc-daygrid-day.selected-day {
        outline: 2px solid var(--accent-blue);
        outline-offset: 1px;
    }

    .fc-event-title { font-size: 0 !important; }
    .fc-event-main {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #eventDetailsCard { max-width: 100%; margin-top: .4rem; }

    /* Bottom nav (mobile) */
    .bottom-nav {
        position: fixed;
        inset-inline: 0;
        bottom: 0;
        z-index: 50;
        padding: .15rem .8rem env(safe-area-inset-bottom);
        background: var(--bg-card);
        border-top: 1px solid var(--border-subtle);
        backdrop-filter: blur(18px); 
    }

    .bottom-nav-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .25rem;
    }

    .bottom-nav .tab-button {
        flex: 1;
        flex-direction: column;
        border-radius: 999px;
        padding: .35rem .15rem;
        font-size: .63rem;
        background: transparent;
        color: var(--text-muted);
        box-shadow: none;
    }

    .bottom-nav .tab-button i { font-size: 1.05rem; }

    .bottom-nav .tab-button.is-active {
        background: radial-gradient(circle at top, var(--accent-soft-blue), var(--accent-soft-green));
        color: var(--text-main); 
    }

    .swal-wide {
        width: 800px !important;
        max-width: 95% !important;
    }

    .swal-wide .table { width: 100%; table-layout: auto; }
    .swal-wide .table th,
    .swal-wide .table td {
        white-space: nowrap;
        text-align: left;
        padding: 8px;
        font-size: .75rem;
    }

    .dragging {
        opacity: 0.8;
        transform: rotate(-3deg);
        transition: transform 0.2s;
    }

    .gu-mirror {
        position: fixed !important;
        margin: 0 !important;
        z-index: 9999 !important;
        opacity: 0.8 !important;
        transform: rotate(-3deg);
    }

    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    #deadline_area, .end_time_area, .repeated_area, .reminder_area, .add_calendar_area {
        display: none;
    }

    .calendar {
        width: 100%;
        max-width: 100%;
        border-radius: 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        padding: .5rem;
    }

    /* Optional: FullCalendar tweaks */
    .fc {
        font-size: 0.75rem;
    }

    .fc .fc-toolbar {
        padding: 0.25rem 0.25rem;
    }

    .fc .fc-toolbar-title {
        font-size: 0.85rem;
    }

    @keyframes pulse-red {
        0% { transform: scale(1); color: #e50656; }
        50% { transform: scale(1.22); color: #74072fff; }
        100% { transform: scale(1); color: #e50656; }
    }
</style>
  
<style>
/* ---------- CONTAINER ---------- */
.sa-bn {
    display: flex;
    align-items: stretch;
    width: 100%;
    height: 52px;
    margin-top: 16px;
    border-radius: 14px;
    overflow: hidden;
    background: linear-gradient(135deg, var(--accent-blue), var(--accent-green)); 
    color: #f9fafb;
    position: relative;
}

/* ---------- LEFT BADGE ---------- */
.sa-bn-label {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 0 16px;
    background: #e50656;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 0.78rem;
    letter-spacing: 0.08em;
    border-right: 1px solid rgba(248, 250, 252, 0.35);
    white-space: nowrap;
}
.sa-bn-label-icon {
    font-size: 1rem;
}

/* ---------- MAIN AREA ---------- */
.sa-bn-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 4px 10px 4px 14px;
}

/* scrolling line */
.sa-bn-track {
    flex: 1;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
}

/* the animated text */
.sa-bn-text {
    position: absolute;
    left: 10px;
    white-space: nowrap;
    font-weight: 600;
    font-size: 0.9rem;
    letter-spacing: 0.02em;
    color: white;
    animation: saTicker 14s linear infinite;
}

/* ticker animation */
@keyframes saTicker {
    0%   { transform: translateX(0%); }
    100% { transform: translateX(-200%); }
}

/* meta row */
.sa-bn-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.7rem;
    color: #9ca3af;
    margin-top: -2px;
}

/* type pill */
.sa-bn-pill {
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid rgb(255 255 255 / 50%);
    background: rgb(15 23 42 / 0%);
    font-weight: 500;
    color: white;
}

/* time */
.sa-bn-time {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    opacity: 0.9;
    color:white;
}
.sa-bn-time i {
    font-size: 0.8rem;
}

#breakingNewsTimeText {
    color:white;   
}
/* ---------- CONTROLS ---------- */
.sa-bn-controls {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 0 8px 0 4px;
}

.sa-bn-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 999px; 
    background: rgba(15, 23, 42, 0);
    color: white;
    cursor: pointer;
    font-size: 1.5rem;
    transition: all 0.15s ease;
}
.sa-bn-btn:hover { 
    border-color: transparent;
    border: 2px solid white;
    border-radius: 50% !important;
    transform: translateY(-1px);
}
.sa-bn-btn i {
    pointer-events: none;
}

/* ---------- PAUSE STATE ---------- */
.sa-bn.is-paused .sa-bn-text {
    animation-play-state: paused;
}

#btn-open-holiday-modal {
        padding: 11px;
    border-radius: 39px !important;
}
</style>

<!-- Holiday Style  -->
 <style>
    /* Overlay */
.holiday-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 1050;
    background: rgba(15, 23, 42, 0.4); /* dark overlay */
    backdrop-filter: blur(4px);
    display: flex;
    justify-content: center;
    align-items: flex-end; /* bottom sheet on mobile */
}

.holiday-modal-hidden {
    display: none;
}

/* Modal container */
.holiday-modal-container {
    width: 100%;
    max-width: 640px;
    margin: 0 8px 8px 8px;
    background: #ffffff;
    border-radius: 24px 24px 0 0;
    max-height: 90vh;
    display: flex;
    flex-direction: column; 
    overflow: hidden;
}

@media (min-width: 768px) {
    .holiday-modal-overlay {
        align-items: center; /* centered on desktop */
    }

    .holiday-modal-container {
        margin: 0 16px;
        border-radius: 18px;
    }
}

/* Header */
.holiday-modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.holiday-modal-kicker {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    color: #93C21C; /* emerald */
    margin: 0 0 2px 0;
}

.holiday-modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
}

.holiday-modal-close-btn {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    border: none;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s ease, color 0.15s ease;
}

.holiday-modal-close-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}

/* Body */
.holiday-modal-body {
    padding: 16px 20px 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    overflow-y: auto;
}

/* Remaining card */
.holiday-remaining-card {
    background: #f8fafc;
    border-radius: 18px;
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

@media (min-width: 640px) {
    .holiday-remaining-card {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.holiday-remaining-label {
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #64748b;
    margin: 0 0 2px 0;
}

.holiday-remaining-value {
    font-size: 15px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
}

.holiday-remaining-year {
    text-align: left;
}

@media (min-width: 640px) {
    .holiday-remaining-year {
        text-align: right;
    }
}

.holiday-remaining-year-value {
    font-size: 14px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
}

/* History section */
.holiday-history-section {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.holiday-history-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.holiday-history-title {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.holiday-history-count {
    font-size: 11px;
    color: #64748b;
}

/* History table */
.holiday-history-table-wrapper {
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    max-height: 160px;
    overflow: hidden;
    overflow-y: auto;
}

.holiday-history-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.holiday-history-table thead {
    background: #f8fafc;
}

.holiday-history-table thead th {
    padding: 6px 12px;
    text-align: left;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.holiday-history-table tbody td {
    padding: 6px 12px;
    border-top: 1px solid #f1f5f9;
    color: #1e293b;
}

.holiday-history-empty {
    text-align: center;
    font-size: 11px;
    color: #64748b;
}

/* Status pills (set via JS by adding classes if you want) */
.holiday-status-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 2px 8px;
    font-size: 10px;
    font-weight: 500;
}

.holiday-status-pending {
    background: #fef3c7;
    color: #92400e;
}

.holiday-status-approved {
    background: #d1fae5;
    color: #6e6e6e;
}

.holiday-status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

/* Form */
.holiday-form {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.holiday-form-row {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

@media (min-width: 640px) {
    .holiday-form-row {
        flex-direction: row;
    }
}

.holiday-form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.holiday-form-label {
    font-size: 12px;
    font-weight: 500;
    color: #334155;
}

.holiday-form-control {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    font-size: 13px;
    padding: 7px 10px;
    box-shadow: none;
}

.holiday-form-control:focus {
    border-color: #93c21c; 
    outline: none;
}

/* Footer buttons */
.holiday-form-footer {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-top: 4px;
}

.btn.btn-icon {
    padding:0.4rem !important;
}
@media (min-width: 640px) {
    .holiday-form-footer {
        flex-direction: row;
        justify-content: flex-end;
        align-items: center;
    }
}

.holiday-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 16px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
}

.holiday-btn-outline {
    background: #ffffff;
    color: #475569;
    border-color: #e2e8f0;
}

.holiday-btn-outline:hover {
    background: #f8fafc;
}

.holiday-btn-primary {
    background: #93c21c;
    color: #ffffff;  
}

.holiday-btn-primary:hover {
    background: #059669;
    border-color: #059669;
}

.app-header-card {
    background: white;
    border-radius: 1.25rem; 
    padding: .85rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap; /* allow wrapping on narrow screens */
}

/* Fix for blurry text on goal items */
.goal-item {
    /* Existing styles... */
    /* Add these: */
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    transform: translateZ(0); /* Forces GPU layer, sometimes fixes blur */
    backface-visibility: hidden; /* Helps with rendering sharpness */
}

/* Ensure text colors have high enough contrast */
.goal-item-text .title {
    color: var(--text-main); /* Ensure this variable is dark enough */
    text-shadow: none; /* Remove any shadows that might look like blur */
}

/* If using Tailwind, ensure base text color isn't too light */
/* make header stack & actions wrap nicely on small screens */
@media (max-width: 639px) { /* < sm */
    .app-header-card {
        align-items: flex-start;
    }

    .app-header-user {
        width: 100%;
    }

    .app-header-actions {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: .35rem;
    }

    .app-header-actions .chip-cta,
    .app-header-actions #themeToggle {
        flex: 1 1 calc(50% - .35rem); /* two items per row on very small screens */
        justify-content: center;
    }

    .app-header-meta h1 {
        font-size: 0.9rem;
    }
}

/* optional: slightly relax the chip padding on very small screens */
@media (max-width: 480px) {
    .chip-cta {
        padding: .4rem .6rem;
        font-size: 0.65rem;
    }

    .chip-cta img {
        width: 1.4rem;
        height: 1.4rem;
    }
}


 </style>

 <style>
/* ---------- CONTAINER ---------- */
.sa-bn {
    display: flex;
    align-items: stretch;
    width: 100%;
    height: 52px;
    margin-top: 16px;
    border-radius: 14px;
    overflow: hidden;
    background: linear-gradient(135deg, var(--accent-blue), var(--accent-green));
    color: #f9fafb;
    position: relative;
}

/* ---------- LEFT BADGE ---------- */
.sa-bn-label {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 0 16px;
    background: #e50656; /* default (old) style */
    font-weight: 800;
    text-transform: uppercase;
    font-size: 0.78rem;
    letter-spacing: 0.08em;
    border-right: 1px solid rgba(248, 250, 252, 0.35);
    white-space: nowrap;
}

/* Breaking news label background variants (no circles) */
.sa-bn-label--breaking-info {
    background: #0ea5e9;
}
.sa-bn-label--breaking-warning {
    background: #f59e0b;
}
.sa-bn-label--breaking-danger {
    background: #ef4444;
}
.sa-bn-label--breaking-success {
    background: #22c55e;
}

/* blinking icon only for breaking */
.sa-bn-label-icon {
    font-size: 1.1rem;
}
.sa-bn-label-blink {
    animation: saBnBlink 1.2s ease-in-out infinite;
}
@keyframes saBnBlink {
    0%   { transform: scale(1);     opacity: 1;   }
    50%  { transform: scale(1.15);  opacity: 0.6; }
    100% { transform: scale(1);     opacity: 1;   }
}

/* ---------- MAIN AREA ---------- */
.sa-bn-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 4px 10px 4px 14px;
}

/* scrolling line */
.sa-bn-track {
    flex: 1;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
}

/* the animated text */
.sa-bn-text {
    position: absolute;
    left: 10px;
    white-space: nowrap;
    font-weight: 600;
    font-size: 0.9rem;
    letter-spacing: 0.02em;
    color: white;
    animation: saTicker 14s linear infinite;
}

/* ticker animation */
@keyframes saTicker {
    0%   { transform: translateX(0%); }
    100% { transform: translateX(-200%); }
}

/* meta row */
.sa-bn-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.7rem;
    color: #9ca3af;
    margin-top: -2px;
}

/* type pill */
.sa-bn-pill {
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid rgb(255 255 255 / 50%);
    background: rgb(15 23 42 / 0%);
    font-weight: 500;
    color: white;
}

/* time */
.sa-bn-time {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    opacity: 0.9;
    color: white;
}
.sa-bn-time i {
    font-size: 0.8rem;
}
#breakingNewsTimeText {
    color: white;
}

/* ---------- CONTROLS ---------- */
.sa-bn-controls {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 0 8px 0 4px;
}

.sa-bn-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0);
    color: white;
    cursor: pointer;
    font-size: 1.5rem;
    transition: all 0.15s ease;
}
.sa-bn-btn:hover {
    border-color: transparent;
    border: 2px solid white;
    border-radius: 50% !important;
    transform: translateY(-1px);
}
.sa-bn-btn i {
    pointer-events: none;
}

/* ---------- PAUSE STATE ---------- */
.sa-bn.is-paused .sa-bn-text {
    animation-play-state: paused;
}

/* ---------- CREATOR ---------- */
.sa-bn-creator {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-left: 10px;
}

.sa-bn-avatar {
    width: 26px;
    height: 26px;
    border-radius: 999px;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.1);
}

.sa-bn-creator-name {
    font-size: 11px;
    color: rgba(15, 23, 42, 0.75);
    white-space: nowrap;
}

/* ---------- AUDIO AREA ---------- */
.sa-bn-audio {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 4px;
    font-size: 0.75rem;
}

.sa-bn-audio-wave {
    position: relative;
    flex: 1;
    height: 18px;
    border-radius: 999px;
    overflow: hidden;
}

/* subtle "wave" background */
.sa-bn-audio-wave-bg {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(
            135deg,
            rgba(148, 163, 184, 0.3) 0%,
            rgba(148, 163, 184, 0.15) 45%,
            rgba(148, 163, 184, 0.3) 100%
        );
    opacity: 0.75;
}

/* progress bar */
.sa-bn-audio-progress {
    position: absolute;
    inset: 0;
    width: 0%;
    background: linear-gradient(90deg, #22c55e, #a3e635);
    opacity: 0.9;
    pointer-events: none;
}

/* small handle */
.sa-bn-audio-handle {
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 4px;
    height: 80%;
    border-radius: 999px;
    background: #111827;
    opacity: 0.9;
    pointer-events: none;
}

/* range slider is invisible but clickable for seeking */
.sa-bn-audio-range {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
}

/* time text */
.sa-bn-audio-time {
    min-width: 80px;
    text-align: right;
    color: #e5e7eb;
    display: inline-flex;
    gap: 3px;
}

/* hide helper */
.hidden {
    display: none !important;
}
</style>

<style>
    /* HEADER CONTAINER */
    .app-header-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
        border: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* Desktop Layout: Side by Side */
    @media (min-width: 1024px) {
        .app-header-card {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    /* USER SECTION */
    .app-header-user {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .app-header-avatar {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        flex-shrink: 0;
    }

    .app-header-meta h1 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        line-height: 1.2;
    }

    .app-header-meta small {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: block;
        margin-bottom: 0.1rem;
    }

    /* CHIP DAY (Date Display) */
    .chip-day {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 0.2rem 0.7rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* ACTIONS / BUTTONS AREA */
    .app-header-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
    }

    /* THEME TOGGLE BUTTON */
    .btn-icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 50%;
        background: #ffffff;
        color: #64748b;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-icon-circle:hover {
        background: #f1f5f9;
        color: #0f172a;
        transform: rotate(15deg);
    }

    /* CHIP CTA BUTTONS */
    .chip-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.6rem 1.1rem;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        white-space: nowrap;
    }

    .chip-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
    }

    /* Button Color Variants */
    .chip-blue   { background: #eff6ff; color: #2563eb; border-color: #dbeafe; } 
    .chip-purple { background: #faf5ff; color: #7c3aed; border-color: #e9d5ff; } 
    .chip-cyan   { background: #ecfeff; color: #0891b2; border-color: #cffafe; } 
    .chip-teal   { background: #f0fdfa; color: #0d9488; border-color: #ccfbf1; } 

    /* MODAL STYLES */
    .custom-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.65); z-index: 9999; 
        display: none; justify-content: center; align-items: center; 
        opacity: 0; transition: opacity 0.2s ease;
        backdrop-filter: blur(4px);
    }
    .custom-modal-overlay.open { display: flex; opacity: 1; }

    .custom-modal-container {
        background: #ffffff; width: 95%; max-width: 850px; max-height: 85vh;
        border-radius: 16px; 
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        display: flex; flex-direction: column; overflow: hidden;
        transform: scale(0.96); transition: transform 0.2s ease;
    }
    .custom-modal-overlay.open .custom-modal-container { transform: scale(1); }

    .custom-modal-header {
        background: #ffffff; 
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .custom-modal-title { font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0; }
    .custom-modal-close { 
        background: transparent; border: none; color: #94a3b8; 
        font-size: 1.5rem; line-height: 1; cursor: pointer; transition: color 0.2s;
    }
    .custom-modal-close:hover { color: #ef4444; }

    .custom-modal-body { 
        padding: 1.5rem; overflow-y: auto; background: #f8fafc; 
    }
</style>
<style>
    /* Custom Modal CSS */
    .custom-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none;
        justify-content: center; align-items: center; opacity: 0;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(4px);
    }
    .custom-modal-overlay.open { display: flex; opacity: 1; }
    
    .custom-modal-container {
        background: #fff; width: 90%; max-width: 1000px; height: 85vh; /* Fixed height for scroll */
        border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        display: flex; flex-direction: column; overflow: hidden;
        transform: translateY(20px); transition: transform 0.3s ease;
    }
    .custom-modal-overlay.open .custom-modal-container { transform: translateY(0); }

    .custom-modal-header {
        background: #ffffff; padding: 15px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex; justify-content: space-between; align-items: center; gap: 15px;
    }
    .custom-modal-title { font-size: 1.25rem; font-weight: 700; margin: 0; color: #1e293b; white-space: nowrap; }
    .custom-modal-close { background: none; border: none; color: #64748b; font-size: 1.5rem; cursor: pointer; transition: color 0.2s; }
    .custom-modal-close:hover { color: #ef4444; }

    /* Search Input Style */
    .custom-modal-search-wrapper { flex-grow: 1; max-width: 400px; position: relative; }
    .custom-modal-search {
        width: 100%; padding: 8px 12px 8px 35px; border-radius: 99px;
        border: 1px solid #cbd5e1; background: #f8fafc; outline: none; transition: all 0.2s;
        font-size: 0.9rem;
    }
    .custom-modal-search:focus { border-color: #74b2d4; background: #fff; box-shadow: 0 0 0 3px rgba(116, 178, 212, 0.1); }
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

    .custom-modal-body { padding: 20px; overflow-y: auto; background: #f8f9fa; flex-grow: 1; }
    
    /* Content Styling (Inside Modal) */
    .customer-card { background: white; border-radius: 8px; padding: 15px; margin-bottom: 15px; border-left: 5px solid #74b2d4; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .house-item { margin-top: 10px; padding: 10px; background: #f1f5f9; border-radius: 6px; }
    .status-badge { font-weight: bold; color: #74b2d4; }
</style>
 
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        <div class="content-body px-0 md:px-4 py-2 md:py-4">
            @php
                $user = DB::table('employees')
                    ->select('name', 'lastname', 'image')
                    ->where('id', auth()->user()->name)
                    ->first();

                $full_name  = $user ? ($user->name . ' ' . $user->lastname) : 'Benutzer';
                $image_path = $user && $user->image
                    ? asset('images/employee/' . $user->image)
                    : asset('images/default-user.png');
            @endphp

            <div class="dashboard-shell -mx-2 md:mx-0">
                <div class="max-w-7xl mx-auto space-y-4 md:space-y-6 pt-2">
                    @php
                        $today        = \Carbon\Carbon::now();
                        $todayLabel   = $today->translatedFormat('D, d.m');
                        $currentYear  = $today->year;

                        $isProgrammer = DB::table('user_rolls')
                            ->where('user_id', auth()->user()->name)
                            ->where('item_id', 'Programmer')
                            ->exists();
                    @endphp

                    {{-- =========================
                        HEADER / USER SUMMARY
                    ========================== --}}
                     <style>
                        /* --- 1. Butter Smooth Button Design --- */
                        .header-btn {
                            position: relative;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            gap: 0.6rem;
                            height: 2.75rem; /* Fixed height for uniformity */
                            padding: 0 1.25rem;
                            background-color: #ffffff;
                            border: 1px solid #e2e8f0;
                            border-radius: 12px !important;
                            color: #64748b;
                            font-size: 0.875rem;
                            font-weight: 600;
                            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                            white-space: nowrap;
                            text-decoration: none;
                            user-select: none;
                            cursor: pointer;
                        }

                        .header-btn:hover {
                            background-color: #f8fafc;
                            border-color: #cbd5e1;
                            color: #0f172a;
                            transform: translateY(-1px);
                            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
                        }

                        .header-btn:active {
                            transform: translateY(0);
                            background-color: #f1f5f9;
                        }

                        /* Icon sizes */
                        .header-btn i, .header-btn svg, .header-btn img {
                            font-size: 1.15rem;
                            width: 1.15rem;
                            height: 1.15rem;
                            object-fit: contain;
                            color: inherit;
                        }

                        /* specific colors for icons on hover to make them pop */
                        .header-btn:hover .text-icon-blue { color: #3b82f6; }
                        .header-btn:hover .text-icon-purple { color: #a855f7; }
                        .header-btn:hover .text-icon-teal { color: #14b8a6; }

                        /* --- 2. Notification Badge --- */
                        .btn-badge {
                            position: absolute;
                            top: -3px;
                            right: -6px;
                            background-color: #ef4444;
                            color: white;
                            font-size: 0.65rem;
                            font-weight: 700;
                            min-width: 18px;
                            height: 18px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 0 5px;
                            border-radius: 99px;
                            border: 2px solid #ffffff; 
                            z-index: 10;
                        }

                        /* --- 3. Icon Only Button (Theme Toggle) --- */
                        .btn-icon-only {
                            padding: 0;
                            width: 2.75rem; /* Match height of other buttons */
                            aspect-ratio: 1/1;
                            border-radius: 50%;
                        }

                        /* --- 4. Mobile Horizontal Scroll for Buttons --- */
                        .actions-scroll-container {
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            overflow-x: auto;
                            padding-bottom: 4px; /* Space for scrollbar or shadow */
                            -webkit-overflow-scrolling: touch;
                            scrollbar-width: none; /* Firefox */
                        }
                        .actions-scroll-container::-webkit-scrollbar {
                            display: none; /* Chrome/Safari */
                        }
                    </style>

                    <header class="bg-white border border-slate-200 rounded-2xl shadow-sm p-2 mb-6 flex flex-col xl:flex-row xl:items-center justify-between gap-6 relative overflow-hidden">
                        
                        {{-- ================= LEFT SIDE: USER INFO ================= --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-5 z-10 w-full xl:w-auto">
                            
                            {{-- Avatar --}}
                            <div class="relative shrink-0 mx-auto sm:mx-0">
                                <img src="{{ $image_path }}" alt="Profilbild" 
                                    class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md ring-1 ring-slate-100">
                                {{-- Online Status Dot --}}
                                <div class="absolute bottom-1 right-1 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></div>
                            </div>

                            {{-- Meta Info --}}
                            <div class="flex-1 text-center sm:text-left">
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Willkommen zurück</p>
                                <h1 class="text-xl font-bold text-slate-800 leading-tight mb-2">{{ $full_name }}</h1>

                                {{-- Info Chips & Stats --}}
                                <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-5 text-xs font-medium text-slate-600">
                                    
                                    {{-- Today Label --}}
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-50 text-slate-700 border border-slate-200">
                                        <i class="ri-calendar-event-line text-slate-400"></i> {{ $todayLabel }}
                                    </span>

                                    {{-- Vertical Divider (Hidden on small mobile) --}}
                                    <div class="hidden sm:block w-px h-4 bg-slate-300"></div>

                                    {{-- Stats Grid --}}
                                    <div class="flex flex-wrap justify-center sm:justify-start gap-x-4 gap-y-2">
                                        <div class="flex items-center gap-1.5" title="Urlaubstage: Genutzt / Gesamt">
                                            <i class="ri-suitcase-2-line text-emerald-500"></i>
                                            <span>Urlaub: <span class="text-slate-900 font-bold">{{ $vacationDaysUsed ?? 0 }}/{{ $annualLeaveTotal ?? 0 }}</span></span>
                                        </div>

                                        <div class="flex items-center gap-1.5" title="Verbleibender Urlaub">
                                            <i class="ri-leaf-line text-emerald-500"></i>
                                            <span>Rest: <span class="text-slate-900 font-bold">{{ $vacationDaysRemain ?? 0 }}</span></span>
                                        </div>

                                        <div class="flex items-center gap-1.5" title="Nächster Urlaub">
                                            <i class="ri-flight-takeoff-line text-indigo-500"></i>
                                            <span>Nächster: <span class="text-slate-900 font-bold">{{ !empty($nextLeaveStart) ? \Carbon\Carbon::parse($nextLeaveStart)->format('d.m.') : '–' }}</span></span>
                                        </div>

                                        <div class="flex items-center gap-1.5" title="Krankheitstage">
                                            <i class="ri-stethoscope-line text-rose-500"></i>
                                            <span>Krank: <span class="text-slate-900 font-bold">{{ $sickDays ?? 0 }}</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================= RIGHT SIDE: ACTION BUTTONS ================= --}}
                        <div class="w-full xl:w-auto border-t xl:border-t-0 border-slate-100 pt-2 xl:pt-0">
                            
                            <div class="actions-scroll-container xl:justify-end">
                                
                                {{-- Theme Toggle --}}
                                <button type="button" id="themeToggle" class="header-btn btn-icon-only flex-shrink-0" title="Design wechseln">
                                    <i id="themeToggleIcon" class="ri-moon-line"></i>
                                </button>

                                {{-- Divider --}}
                                <div class="w-px h-8 bg-slate-200 mx-1 flex-shrink-0 hidden xl:block"></div>

                                {{-- My Customers --}}
                                <button type="button" onclick="openMyModal('customers')" class="header-btn">
                                    <i class="ri-group-line text-icon-blue"></i>
                                    <span>Meine Kunden</span>
                                    @if(isset($myCustomerCount) && $myCustomerCount > 0)
                                        <span class="btn-badge">{{ $myCustomerCount }}</span>
                                    @endif
                                </button>

                                {{-- My Projects --}}
                                <button type="button" onclick="openMyModal('projects')" class="header-btn">
                                    <i class="ri-briefcase-line text-icon-purple"></i>
                                    <span>Meine Projekte</span>
                                    @if(isset($myProjectCount) && $myProjectCount > 0)
                                        <span class="btn-badge">{{ $myProjectCount }}</span>
                                    @endif
                                </button>

                                {{-- Daily Report --}}
                                <a href="{{ url('employee_daily_plan') }}" class="header-btn">
                                    <img src="{{ asset('images/icons/report.png') }}" alt="Report">
                                    <span>Tagesbericht</span>
                                </a>

                                {{-- Holiday Request --}}
                                <button type="button" id="btn-open-holiday-modal" class="header-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="text-icon-teal">
                                        <path d="M5 4h14v16H5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M9 2v4M15 2v4M8 11h3M8 15h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <span>Urlaub</span>
                                </button>
                            </div>
                        </div>
                    </header>

                {{-- DYNAMIC MODAL CONTAINER --}}
                <div id="dynamicDataModal" class="custom-modal-overlay">
                    <div class="custom-modal-container">
                        <div class="custom-modal-header">
                            <h3 class="custom-modal-title" id="modalTitle">Laden...</h3>
                            
                            <div class="custom-modal-search-wrapper">
                                <i class="feather icon-search search-icon"></i>
                                <input type="text" id="modalSearchInput" class="custom-modal-search" placeholder="Suche..." onkeyup="searchModalData()">
                            </div>

                            <button class="custom-modal-close" onclick="closeMyModal()">&times;</button>
                        </div>
                        <div class="custom-modal-body" id="modalContent">
                            <div class="flex justify-center p-10">Laden...</div>
                        </div>
                    </div>
                </div>

                    {{-- =========================
                        BREAKING NEWS BAR
                    ========================== --}}
                   <div id="breakingNewsBar" class="sa-bn hidden">
                        <div class="sa-bn-label" id="bnLabel">
                            <!-- ICON CHANGES DYNAMICALLY IN JS -->
                            <i class="ri-flashlight-fill sa-bn-label-icon" id="bnMainIcon"></i>
                        </div>

                        <div class="sa-bn-main">
                            <div class="sa-bn-track">
                                <span id="breakingNewsText" class="sa-bn-text"></span>
                            </div>

                            <!-- AUDIO WAVEFORM / PROGRESS (only when sound exists) -->
                            <div id="bnAudioWrapper" class="sa-bn-audio hidden">
                                <div class="sa-bn-audio-wave">
                                    <div class="sa-bn-audio-wave-bg"></div>
                                    <div class="sa-bn-audio-progress" id="bnAudioProgress"></div>
                                    <div class="sa-bn-audio-handle" id="bnAudioHandle"></div>

                                    <!-- invisible range for seeking -->
                                    <input type="range"
                                        id="bnAudioSeek"
                                        min="0" max="100" step="0.1"
                                        class="sa-bn-audio-range">
                                </div>
                                <div class="sa-bn-audio-time">
                                    <span id="bnAudioCurrent">0:00</span>
                                    <span>/</span>
                                    <span id="bnAudioDuration">0:00</span>
                                </div>
                            </div>

                            <div class="sa-bn-meta">
                                <span id="breakingNewsType" class="sa-bn-pill"></span>
                                <span class="sa-bn-time">
                                    <i class="ri-time-line"></i>
                                    <span id="breakingNewsTimeText"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Creator avatar only -->
                        <div class="sa-bn-creator">
                            <img id="breakingNewsCreatorImage" class="sa-bn-avatar" src="" alt="" style="display:none;">
                            <span id="breakingNewsCreatorName" class="sa-bn-creator-name hidden" ></span>
                        </div>

                        <!-- CONTROLS: prev / play-pause / next -->
                        <div class="sa-bn-controls">
                            <button id="bnPrev" class="sa-bn-btn" type="button" title="Vorherige">
                                <i class="ri-skip-back-mini-line"></i>
                            </button>
                            <button id="bnPlayPause" class="sa-bn-btn" type="button" title="Pause">
                                <i class="ri-pause-mini-line" id="bnPlayPauseIcon"></i>
                            </button>
                            <button id="bnNext" class="sa-bn-btn" type="button" title="Nächste">
                                <i class="ri-skip-forward-mini-line"></i>
                            </button>
                        </div>
                    </div>

                    {{-- =========================
                        URLAUBS-ANTRAG MODAL
                    ========================== --}}
                    <div id="holiday-request-modal" class="holiday-modal-overlay holiday-modal-hidden">
                        <div class="holiday-modal-container">
                            {{-- Modal Header --}}
                            <div class="holiday-modal-header">
                                <div>
                                    <p class="holiday-modal-kicker">Urlaub</p>
                                    <h2 class="holiday-modal-title">Urlaub beantragen</h2>
                                </div>
                                <button
                                    type="button"
                                    id="holiday-modal-close"
                                    class="holiday-modal-close-btn">
                                    ✕
                                </button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="holiday-modal-body">
                                {{-- Verbleibender Urlaub --}}
                                <div class="holiday-remaining-card">
                                    <div>
                                        <p class="holiday-remaining-label">Verbleibender Urlaub</p>
                                        <p class="holiday-remaining-value">
                                            <span id="holiday-remaining">–</span> Tage
                                        </p>
                                    </div>
                                    <div class="holiday-remaining-year">
                                        <p class="holiday-remaining-label">Jahr</p>
                                        <p class="holiday-remaining-year-value" id="holiday-year">–</p>
                                    </div>
                                </div>

                                {{-- Bisherige Anträge --}}
                                <div class="holiday-history-section">
                                    <div class="holiday-history-header">
                                        <h3 class="holiday-history-title">
                                            Bisherige Anträge (dieses Jahr)
                                        </h3>
                                        <span class="holiday-history-count" id="holiday-history-count"></span>
                                    </div>

                                    <div class="holiday-history-table-wrapper">
                                        <table class="holiday-history-table">
                                            <thead>
                                                <tr>
                                                    <th>Von</th>
                                                    <th>Bis</th>
                                                    <th>Tage</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="holiday-history-body">
                                                <tr>
                                                    <td colspan="4" class="holiday-history-empty">
                                                        Keine Daten geladen …
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Neuer Antrag --}}
                                <form id="holiday-request-form" class="holiday-form">
                                    @csrf

                                    {{-- Datum --}}
                                    <div class="holiday-form-row">
                                        <div class="holiday-form-group">
                                            <label class="holiday-form-label">Startdatum</label>
                                            <input
                                                type="date"
                                                name="start_date"
                                                class="form-control holiday-form-control">
                                        </div>

                                        <div class="holiday-form-group">
                                            <label class="holiday-form-label">Enddatum</label>
                                            <input
                                                type="date"
                                                name="end_date"
                                                class="form-control holiday-form-control">
                                        </div>
                                    </div>

                                    {{-- Grund / Art --}}
                                    <div class="holiday-form-group">
                                        <label class="holiday-form-label">Grund / Art des Urlaubs</label>
                                        <select
                                            name="reason"
                                            class="form-control holiday-form-control">
                                            <option value="Persönlicher Urlaub">Persönlicher Urlaub</option>
                                            <option value="Jahresurlaub">Jahresurlaub</option>
                                            <option value="Elternzeit">Elternzeit</option>
                                            <option value="Trauerurlaub">Trauerurlaub</option>
                                        </select>
                                    </div>

                                    {{-- Notiz --}}
                                    <div class="holiday-form-group">
                                        <label class="holiday-form-label">Notiz</label>
                                        <textarea
                                            name="note"
                                            rows="3"
                                            class="form-control holiday-form-control"
                                            placeholder="Optional: Zusätzliche Informationen für deinen Vorgesetzten"></textarea>
                                    </div>

                                    {{-- Footer --}}
                                    <div class="holiday-form-footer">
                                        <button
                                            type="button"
                                            id="holiday-modal-cancel"
                                            class="holiday-btn holiday-btn-outline">
                                            Abbrechen
                                        </button>
                                        <button
                                            type="submit"
                                            class="holiday-btn holiday-btn-primary">
                                            Antrag senden
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- =========================
                        MAIN GRID
                    ========================== --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-5">
                        {{-- LEFT: KPI + TABS --}}
                        <div class="space-y-4 lg:col-span-2">
                            {{-- KPI / Today Card --}}
                            <section id="dueTodayCard" class="kpi-card">
                                <div class="kpi-header">
                                    <div>
                                        <div class="kpi-title">
                                            <i class="ri-focus-3-line"></i>
                                            <span>Fokus heute</span>
                                        </div>
                                        <p class="kpi-sub">
                                            Alle heute fälligen Aufgaben, Termine, Anfragen &amp; Tickets auf einen Blick.
                                        </p>
                                    </div>

                                    <span id="todayPercentBadge" class="kpi-badge">
                                        <span class="kpi-badge-dot"></span>
                                        <span id="todayPercentText">0%</span>
                                    </span>
                                </div>

                                <div class="kpi-chip-row">
                                    <div class="kpi-pill">
                                        <span class="label">Offen heute</span>
                                        <span class="value" id="kpi-open-today">–</span>
                                    </div>
                                    <div class="kpi-pill">
                                        <span class="label">Überfällig</span>
                                        <span class="value" id="kpi-overdue">–</span>
                                    </div>
                                </div>

                                <div class="kpi-progress-shell">
                                    <div class="kpi-progress-track">
                                        <div class="kpi-progress-bar" id="todayProgressBar"></div>
                                    </div>
                                    <div class="kpi-progress-meta">
                                        <span>Start</span>
                                        <span>100%</span>
                                    </div>
                                </div>

                                {{-- Filterchips + Sortierung --}}
                                <div class="mt-1 gap-2 relative z-10 d-flex"  >
                                    <div id="goalFilterBar" class="goal-filter-strip no-scrollbar">
                                        <button type="button" class="goal-filter-chip active" data-filter="all">
                                            Alles
                                            <span class="count" id="count-all">(0)</span>
                                        </button>
                                        <button type="button" class="goal-filter-chip" data-filter="lead">
                                            Leads
                                            <span class="count" id="count-lead">(0)</span>
                                        </button>
                                        <button type="button" class="goal-filter-chip" data-filter="anfrage">
                                            Anfragen
                                            <span class="count" id="count-anfrage">(0)</span>
                                        </button>
                                        <button type="button" class="goal-filter-chip" data-filter="aufgabe">
                                            Aufgaben
                                            <span class="count" id="count-aufgabe">(0)</span>
                                        </button>
                                        <button type="button" class="goal-filter-chip" data-filter="appointment">
                                            Termine
                                            <span class="count" id="count-appointment">(0)</span>
                                        </button>
                                        <button type="button" class="goal-filter-chip" data-filter="rest">
                                            Sonstiges
                                            <span class="count" id="count-rest">(0)</span>
                                        </button>
                                    </div>
                                   
                                    <div class="goal-sort-shell"> 
                                        <select id="goalSortSelect">
                                            <option value="due_asc">Fälligkeit ↑</option>
                                            <option value="due_desc">Fälligkeit ↓</option>
                                            <option value="prio_desc">Priorität hoch → niedrig</option>
                                            <option value="prio_asc">Priorität niedrig → hoch</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Ziele / Aufgaben Liste --}}
                                <div id="goalList"></div>
                            </section>

                            {{-- Tabs + Filter + Inhalte --}}
                            <section class="tab-card">
                                {{-- Tab-Leiste --}}
                                <div class="tab-strip-wrap">
                                    <div id="tabs" class="tab-strip">
                                        @if($isProgrammer)
                                            <button class="tab-button" data-tab="admin">
                                                <i class="ri-shield-user-line"></i>
                                                <span>Admin</span>
                                            </button>
                                        @endif

                                        <button class="tab-button is-active" data-tab="all">
                                            <i class="ri-dashboard-2-line"></i>
                                            <span>Allgemein</span>
                                        </button>

                                        <button class="tab-button" data-tab="tasks">
                                            <i class="ri-checkbox-circle-line"></i>
                                            <span>Aufgaben</span>
                                        </button>

                                        <button class="tab-button" data-tab="appointments">
                                            <i class="ri-calendar-check-line"></i>
                                            <span>Termine</span>
                                        </button>

                                        <button class="tab-button" data-tab="calendar">
                                            <i class="ri-calendar-line"></i>
                                            <span>Kalender</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Suchzeile / Filter --}}
                                <div class="search-shell relative">
                                    <div class="relative">
                                        <i class="ri-search-2-line search-icon"></i>
                                        <input
                                            type="text"
                                            id="searchBar"
                                            class="search-input"
                                            placeholder="Suche nach Titel, Kunde, Nummer …">
                                    </div>

                                    <div class="search-shell-inner">
                                        <input
                                            type="date"
                                            id="searchDate"
                                            value="{{ $today->format('Y-m-d') }}">

                                        <select id="searchOrder" name="range">
                                            <option value="all">Alle</option>
                                            <option value="today">Heute</option>
                                            <option value="week">Diese Woche</option>
                                            <option value="month">Dieser Monat</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Tab Panels --}}
                                <div id="tab-content" class="mt-3 space-y-3">
                                    <div class="tab-panel hidden" id="tab-admin">
                                        @includeWhen($isProgrammer, 'admin.dashboard.employee.partials.admin')
                                    </div>

                                    <div class="tab-panel" id="tab-all">
                                        @include('admin.dashboard.employee.partials.all')
                                    </div>

                                    <div class="tab-panel hidden" id="tab-tasks"></div>
                                    <div class="tab-panel hidden" id="tab-appointments"></div>
                                    <div class="tab-panel hidden" id="tab-projects"></div>
                                    <div class="tab-panel hidden" id="tab-offers"></div>

                                    <div class="tab-panel hidden" id="tab-calendar">
                                        @include('admin.dashboard.employee.partials.calendar')
                                    </div>
                                </div>
                            </section>
                        </div>

                        {{-- RIGHT: NOTES --}}
                        <aside class="space-y-4">
                            <section class="notes-card">
                                <div class="notes-header">
                                    <h2>
                                        <i class="ri-sticky-note-line"></i>
                                        <span>Meine Notizen</span>
                                    </h2>
                                    <div class="notes-actions">
                                        <button type="button" class="trash_box btn btn-icon btn-icon rounded-circle btn-primary   waves-effect waves-light  ">
                                            <i class="feather icon-trash"></i>
                                           
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-icon btn-icon rounded-circle btn-primary   waves-effect waves-light  "
                                            data-toggle="modal"
                                            data-target="#newNote">
                                            <i class="feather icon-plus"></i> 
                                        </button>
                                    </div>
                                </div>

                                <div class="notes-body">
                                    <div class="notes-scroll">
                                        
                                    </div>
                                </div>
                            </section>
                        </aside>
                    </div>
                </div>

                {{-- STICKY BOTTOM NAV (MOBILE ONLY) --}}
                <nav class="bottom-nav md:hidden">
                    <div class="bottom-nav-inner">
                        <button class="tab-button is-active" data-tab="all">
                            <i class="ri-dashboard-2-line"></i>
                            <span>Übersicht</span>
                        </button>
                        <button class="tab-button" data-tab="tasks">
                            <i class="ri-checkbox-circle-line"></i>
                            <span>Aufgaben</span>
                        </button>
                        <button class="tab-button" data-tab="appointments">
                            <i class="ri-calendar-check-line"></i>
                            <span>Termine</span>
                        </button>
                        <button class="tab-button" data-tab="calendar">
                            <i class="ri-calendar-line"></i>
                            <span>Kalender</span>
                        </button>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>

            
<div id="toastSuccess"
     class="hidden fixed bottom-5 right-5 bg-green-600 text-white text-sm rounded px-4 py-2 shadow-xl z-50">
    Als erledigt markiert!
</div>
@endsection

@section('script')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script>
    const currentUserName = @json(auth()->user()->name);
</script>

{{-- THEME TOGGLE --}}
<script>
    (function () {
        const STORAGE_KEY = 'employee-dashboard-theme';

        function applyTheme(theme) {
            const root = document.documentElement;
            const icon = document.getElementById('themeToggleIcon');

            const resolved = (theme === 'light' || theme === 'dark') ? theme : 'light';
            root.setAttribute('data-theme', resolved);

            if (icon) {
                if (resolved === 'light') {
                    icon.classList.remove('ri-moon-line');
                    icon.classList.add('ri-sun-line');
                } else {
                    icon.classList.remove('ri-sun-line');
                    icon.classList.add('ri-moon-line');
                }
            }
        }

        const storedTheme = localStorage.getItem(STORAGE_KEY) || 'light';
        applyTheme(storedTheme);

        const toggleBtn = document.getElementById('themeToggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                const current = document.documentElement.getAttribute('data-theme') || 'light';
                const next = current === 'dark' ? 'light' : 'dark';
                localStorage.setItem(STORAGE_KEY, next);
                applyTheme(next);
            });
        }
    })();
</script>


 
{{-- TABS + GLOBAL FILTERS + MINI CALENDAR --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (window.feather) feather.replace();

    const searchBar   = document.getElementById('searchBar');
    const searchDate  = document.getElementById('searchDate');
    const searchOrder = document.getElementById('searchOrder');

    let activeTab = 'all';

    if (searchDate && !searchDate.value) {
        searchDate.value = new Date().toISOString().split('T')[0];
    }

    document.querySelectorAll('.tab-button[data-tab]').forEach(btn => {
        btn.addEventListener('click', function () {
            const tab = this.dataset.tab;
            if (!tab) return;
            activeTab = tab;
            switchTab(tab);
        });
    });

    if (searchBar)   searchBar.addEventListener('input', debounce(applyFilter, 300));
    if (searchDate)  searchDate.addEventListener('change', applyFilter);
    if (searchOrder) searchOrder.addEventListener('change', applyFilter);

    function switchTab(tab) {
        document.querySelectorAll('.tab-button[data-tab]').forEach(t => t.classList.remove('is-active'));
        const btns = document.querySelectorAll(`.tab-button[data-tab="${tab}"]`);
        btns.forEach(b => b.classList.add('is-active'));

        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        const panel = document.getElementById(`tab-${tab}`);
        if (panel) {
            panel.classList.remove('hidden');
            loadTabContent(tab);
        }
    }

    function getFilterUrl(tab) {
        const keyword      = searchBar ? searchBar.value : '';
        const selectedDate = searchDate ? searchDate.value : '';
        const range        = searchOrder ? (searchOrder.value || 'all') : 'all';

        const params = new URLSearchParams({
            tab,
            search: keyword,
            date: selectedDate,
            range
        });

        if (tab === 'notes') {
            requestAnimationFrame(() => {
                if (document.getElementById('personal-note-list')) {
                    loadNotes();
                }
            });
        }

        return `/dashboard/load-tab?${params.toString()}`;
    }

    function applyFilter() {
        loadTabContent(activeTab);
    }

    function loadTabContent(tab) {
        const panel = document.getElementById(`tab-${tab}`);
        if (!panel) return;

        // Static tab "all" & "calendar" already loaded by Blade; for dynamic tabs, load
        if (['tasks', 'appointments', 'projects', 'offers', 'admin'].includes(tab)) {
            panel.innerHTML = `<div class="p-4 text-center text-gray-400 text-xs">🔄 Inhalte werden geladen …</div>`;
            fetch(getFilterUrl(tab))
                .then(res => res.text())
                .then(html => {
                    panel.innerHTML = html;
                    if (window.feather) feather.replace();

                    const noteList = document.getElementById('personal-note-list');
                    if (noteList) {
                        new Sortable(noteList, {
                            handle: '.drag-handle',
                            animation: 150
                        });
                    }

                    if (tab === 'notes') {
                        requestAnimationFrame(() => {
                            if (document.getElementById('personal-note-list')) {
                                loadNotes();
                            }
                        });
                    }

                    if (tab === 'calendar') {
                        initEmployeeCalendar();
                    }
                })
                .catch(err => {
                    console.error('Error loading content:', err);
                    panel.innerHTML = `<div class="text-red-400 p-4 text-xs">❌ Fehler beim Laden.</div>`;
                });
        } else if (tab === 'calendar') {
            initEmployeeCalendar();
        }
    }

    // Calendar
    const today = new Date();
    let currentDate = new Date(today.getFullYear(), today.getMonth(), 1);
    let allEvents = [];

    function initQuarterCalendar() {
        setTimeout(() => {
            renderQuarterCalendars(currentDate);

            document.getElementById('prevQuarter')?.addEventListener('click', () => {
                // BEFORE: currentDate.setMonth(currentDate.getMonth() - 3);
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderQuarterCalendars(currentDate);
            });

            document.getElementById('nextQuarter')?.addEventListener('click', () => {
                // BEFORE: currentDate.setMonth(currentDate.getMonth() + 3);
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderQuarterCalendars(currentDate);
            });
        }, 50);
    }


    function loadEvents(start, end) {
        return fetch(`/get_personal_task_calendar_mini?start_date=${start}&end_date=${end}`)
            .then(res => res.json())
            .then(res => {
                return res.data.map(ev => {
                    const endDate = ev.due_date || ev.end_date || ev.start_date;
                    const endTime = ev.end_time || '23:59:59';
                    return {
                        id: ev.id,
                        title: ev.title,
                        start: `${ev.start_date}T${ev.start_time ?? '00:00:00'}`,
                        end: `${endDate}T${endTime}`,
                        color: ev.taskColor || '#74b2d4',
                        type: ev.type,
                        extendedProps: ev
                    };
                });
            });
    }

   function renderEventCard(events) {
    const container = document.getElementById('eventDetailsCard');
    const dateLabel = document.getElementById('eventDetailsDateLabel');

    if (!container) return;

    container.innerHTML = '';

    if (!events || !events.length) {
        if (dateLabel) {
            dateLabel.textContent = 'Kein Eintrag an diesem Tag';
        }
        container.innerHTML = `
            <div class="text-center text-xs text-gray-400 py-3 border border-dashed border-slate-200 rounded-xl bg-slate-50/80">
                ❌ Keine Einträge an diesem Tag.
            </div>`;
        return;
    }

    // assume all events are same date for the clicked day
    const any = events[0];
    const p0  = any.extendedProps || {};
    const dateRaw =
        p0.start_date ||
        (any.start ? any.start.split('T')[0] : null) ||
        (p0.due_date || null);

    if (dateLabel && dateRaw) {
        const d = new Date(dateRaw);
        dateLabel.textContent = `Ausgewählt: ${d.toLocaleDateString('de-DE', {
            weekday: 'long',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        })}`;
    }

    events.forEach(event => {
        const p = event.extendedProps || {};
        const type = p.type || event.type || '';

        let icon = '📌';
        let typeLabel = 'Event';
        let typeColor = '#74b2d4';
        let chipBg    = 'rgba(116,178,212,0.12)';

        switch (type) {
            case 'task':
            case 'personal_task':
                icon = '📝';
                typeLabel = 'Aufgabe';
                typeColor = '#cfe09b';
                chipBg = 'rgba(232,62,140,0.12)';
                break;
            case 'appointment':
                icon = '📅';
                typeLabel = 'Termin';
                typeColor = '#cfe09b';
                chipBg = 'rgba(116,178,212,0.12)';
                break;
            case 'holiday':
            case 'recurring_leave':
                icon = '🌴';
                typeLabel = 'Urlaub';
                typeColor = '#74b2d4';
                chipBg = 'rgba(143,199,62,0.12)';
                break;
            case 'sick':
                icon = '🤒';
                typeLabel = 'Krank';
                typeColor = '#e50656';
                chipBg = 'rgba(143,199,62,0.12)';
                break;
            case 'public_holiday':
                icon = '🏛️';
                typeLabel = 'Feiertag';
                typeColor = '#e50656';
                chipBg = 'rgba(255,87,51,0.12)';
                break;
        }

        const startDate = p.start_date || (event.start ? event.start.split('T')[0] : '');
        const endDate   = p.end_date   || (event.end   ? event.end.split('T')[0]   : startDate);
        const startTime = (p.start_time && p.start_time !== '00:00:00') ? p.start_time.slice(0,5) : null;
        const endTime   = (p.end_time   && p.end_time   !== '23:59:59') ? p.end_time.slice(0,5)   : null;

        const isAllDay  = !startTime && !endTime;
        const dateText  = (startDate === endDate)
            ? new Date(startDate).toLocaleDateString('de-DE')
            : `${new Date(startDate).toLocaleDateString('de-DE')} – ${new Date(endDate).toLocaleDateString('de-DE')}`;

        let timeText = 'Ganztägig';
        if (!isAllDay) {
            if (startTime && endTime) timeText = `${startTime} – ${endTime} Uhr`;
            else if (startTime)       timeText = `${startTime} Uhr`;
        }

        const addressParts = [];
        if (p.street)   addressParts.push(p.street);
        if (p.postcode) addressParts.push(p.postcode);
        if (p.city)     addressParts.push(p.city);
        const addressText = addressParts.join(', ');

        const employees = Array.isArray(p.employees) ? p.employees : [];
        const employeesHtml = employees.length
            ? employees.map(emp => {
                const name = `${emp.name || ''} ${emp.lastname || ''}`.trim();
                if (!name) return '';
                return `<span class="event-staff-pill bg-slate-100 text-slate-700 border border-slate-200">${name}</span>`;
              }).join('')
            : `<span class="text-slate-400 text-[0.68rem]">Keine Mitarbeiter zugeordnet</span>`;

        // Decide details URL
        let detailsUrl = '';
        if (type === 'appointment') {
            detailsUrl = `/appointment_details/${event.id}`;
        } else if (type === 'task' || type === 'personal_task') {
            detailsUrl = `/personal_task_details/${event.id}`;
        }

        const phoneHtml = p.phone
            ? `<span class="event-meta-pill"><i class="ri-phone-line text-[0.75rem]"></i>${p.phone}</span>`
            : '';
        const emailHtml = p.email
            ? `<span class="event-meta-pill"><i class="ri-mail-line text-[0.75rem]"></i>${p.email}</span>`
            : '';

        const locationHtml = addressText
            ? `<span class="event-meta-pill"><i class="ri-map-pin-line text-[0.75rem]"></i>${addressText}</span>`
            : '';

        const statusHtml = p.status
            ? `<span class="event-meta-pill"><i class="ri-bar-chart-line text-[0.75rem]"></i>Status: ${p.status}</span>`
            : '';

        const priorityHtml = p.priority
            ? `<span class="event-meta-pill"><i class="ri-flashlight-line text-[0.75rem]"></i>Prio: ${p.priority}</span>`
            : '';

        const contactTypeHtml = p.contact_type
            ? `<span class="event-meta-pill"><i class="ri-user-voice-line text-[0.75rem]"></i>${p.contact_type}</span>`
            : '';

        const description = p.description || '';

        const cardHtml = `
            <article class="event-card">
                <div class="event-card-header">
                    <div class="flex-1">
                        <div class="flex items-start gap-2">
                            <div class="text-lg leading-none">${icon}</div>
                            <div>
                                <h3 class="text-[0.8rem] font-semibold text-slate-800">
                                    ${event.title || 'Ohne Titel'}
                                </h3>
                                <p class="text-[0.68rem] text-slate-500 mt-0.5">
                                    ${description || ''}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0">
                        <span class="event-chip"
                              style="color:${typeColor};background:${chipBg};border:1px solid ${typeColor}33;">
                            <span class="w-1.5 h-1.5 rounded-full" style="background:${typeColor};"></span>
                            ${typeLabel}
                        </span>
                    </div>
                </div>

                <div class="event-meta-row">
                    <span class="event-meta-pill">
                        <i class="ri-calendar-line text-[0.75rem]"></i>
                        ${dateText}
                    </span>
                    <span class="event-meta-pill">
                        <i class="ri-time-line text-[0.75rem]"></i>
                        ${timeText}
                    </span>
                    ${statusHtml}
                    ${priorityHtml}
                    ${contactTypeHtml}
                    ${locationHtml}
                    ${phoneHtml}
                    ${emailHtml}
                </div>

                <div class="event-staff-row">
                    ${employeesHtml}
                </div>

                ${detailsUrl ? `
                <div class="mt-1 flex justify-end">
                    <a href="${detailsUrl}"
                       class="inline-flex items-center gap-1 text-[0.7rem] text-sky-600 hover:text-sky-700 hover:underline">
                        Details ansehen
                        <i class="ri-arrow-right-line text-[0.8rem]"></i>
                    </a>
                </div>` : ''}
            </article>
        `;

        container.insertAdjacentHTML('beforeend', cardHtml);
    });
}


        // Single employee calendar
    let employeeCalendar = null;

    function initEmployeeCalendar() {
        const calEl = document.getElementById('employeeCalendar');
        if (!calEl) return;

        // prevent double-initialization when switching tabs
        if (calEl.dataset.initialized === '1') return;
        calEl.dataset.initialized = '1';

        employeeCalendar = new FullCalendar.Calendar(calEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            locale: 'de',
            firstDay: 1,
            height: 'auto',
            contentHeight: 'auto',
            handleWindowResize: true,
            dayMaxEvents: true,
            events: function (fetchInfo, successCallback, failureCallback) {
                // Only the date part, no timezone
                const start = fetchInfo.startStr.slice(0, 10); // "YYYY-MM-DD"
                const end   = fetchInfo.endStr.slice(0, 10);   // "YYYY-MM-DD"

                fetch(`/get_personal_task_calendar_mini?start_date=${encodeURIComponent(start)}&end_date=${encodeURIComponent(end)}`)
                    .then(res => res.json())
                    .then(res => {
                        allEvents = (res.data || []).map(ev => {
                            const endDate = ev.due_date || ev.end_date || ev.start_date;
                            const endTime = ev.end_time || '23:59:59';
                            return {
                                id: ev.id,
                                title: ev.title,
                                start: `${ev.start_date}T${ev.start_time ?? '00:00:00'}`,
                                end: `${endDate}T${endTime}`,
                                color: ev.taskColor || '#74b2d4',
                                type: ev.type,
                                extendedProps: ev
                            };
                        });

                        successCallback(allEvents);

                        const todayStr = new Date().toISOString().split('T')[0];
                        const todayEvents = allEvents.filter(e => {
                            const due = e.extendedProps?.start_date || (e.start?.split('T')[0]);
                            return due === todayStr;
                        });
                        renderEventCard(todayEvents);
                    })
                    .catch(err => {
                        console.error('Calendar event load error', err);
                        failureCallback(err);
                    });
            },

           dateClick(info) {
                const clickedDate = info.dateStr;
                const matched = allEvents.filter(e => {
                    const p = e.extendedProps || {};
                    const start = p.start_date || (e.start ? e.start.split('T')[0] : null);
                    const end   = p.end_date   || (e.end   ? e.end.split('T')[0]   : start);
                    // treat a day as "matching" if clickedDate lies between start and end (inclusive)
                    return clickedDate >= start && clickedDate <= end;
                });
                renderEventCard(matched);
            },

            eventDidMount(info) {
                // turn the event into a small colored dot
                info.el.innerHTML = '';
                const dot = document.createElement('div');
                dot.classList.add('fc-daygrid-event-dot');
                dot.style.backgroundColor = info.event.backgroundColor || '#74b2d4';
                info.el.appendChild(dot);

                // highlight cell as "has event"
                const dayEl = info.el.closest('.fc-daygrid-day');
                if (dayEl) {
                    dayEl.classList.add('haveEvent');
                }
            }

        });

        employeeCalendar.render();
    }


   function renderQuarterCalendars(baseDate = new Date(today.getFullYear(), today.getMonth(), 1)) {
        const wrapper   = document.getElementById('calendarWrapper');
        const eventCard = document.getElementById('eventDetailsCard');

        if (!wrapper || wrapper.offsetParent === null) return;

        wrapper.innerHTML = '';
        if (eventCard) eventCard.innerHTML = '';

        // only this month
        const startRange = new Date(baseDate.getFullYear(), baseDate.getMonth(), 1);
        const endRange   = new Date(baseDate.getFullYear(), baseDate.getMonth() + 1, 0);
        const startStr   = startRange.toISOString().split('T')[0];
        const endStr     = endRange.toISOString().split('T')[0];

        loadEvents(startStr, endStr).then(events => {
            allEvents = events;

            const monthDate = new Date(baseDate.getFullYear(), baseDate.getMonth(), 1);
            const calYear   = monthDate.getFullYear();
            const calMonth  = monthDate.getMonth();

            const calendarBox = document.createElement('div');
            calendarBox.classList.add('fc-calendar');

            const header = document.createElement('div');
            header.className = 'text-center text-[0.7rem] font-semibold text-slate-700 mb-1.5';
            header.textContent = `${monthDate.toLocaleString('de-DE', { month: 'long' })} ${calYear}`;
            calendarBox.appendChild(header);

            const calendarEl = document.createElement('div');
            calendarBox.appendChild(calendarEl);
            wrapper.appendChild(calendarBox);

            const filteredEvents = events.filter(e => {
                const evStart    = new Date(e.start);
                const evEnd      = new Date(e.end || e.start);
                const monthStart = new Date(calYear, calMonth, 1);
                const monthEnd   = new Date(calYear, calMonth + 1, 0);
                return evEnd >= monthStart && evStart <= monthEnd;
            });

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: monthDate.toISOString().split('T')[0],
                headerToolbar: false,
                height: 'auto',
                firstDay: 1,
                locale: 'de',
                fixedWeekCount: false,
                showNonCurrentDates: false,
                events: filteredEvents,

                dayCellDidMount(info) {
                    const cellDate = info.date.toISOString().split('T')[0];

                    const hasEvent = allEvents.some(ev => {
                        const due = ev.extendedProps?.due_date || (ev.end?.split('T')[0]);
                        return due === cellDate;
                    });

                    if (hasEvent) info.el.classList.add('haveEvent');

                    const todayStr = new Date().toISOString().split('T')[0];
                    if (cellDate === todayStr) {
                        info.el.classList.add('selected-day');
                    }
                },

                dateClick(info) {
                    document.querySelectorAll('.fc-daygrid-day')
                        .forEach(el => el.classList.remove('selected-day'));
                    info.dayEl.classList.add('selected-day');

                    const clickedDate = info.dateStr;
                    const matched = allEvents.filter(e => {
                        const due = e.extendedProps?.due_date || (e.end?.split('T')[0]);
                        return due === clickedDate;
                    });
                    renderEventCard(matched);
                },

                eventDidMount(info) {
                    info.el.innerHTML = '';
                    const dot = document.createElement('div');
                    dot.style.width = '6px';
                    dot.style.height = '6px';
                    dot.style.borderRadius = '50%';
                    dot.style.backgroundColor = info.event.backgroundColor || '#74b2d4';
                    dot.style.margin = '0 auto';
                    info.el.appendChild(dot);
                }
            });

            calendar.render();

            const todayStr = new Date().toISOString().split('T')[0];
            const todayEvents = allEvents.filter(e => {
                const due = e.extendedProps?.due_date || (e.end?.split('T')[0]);
                return due === todayStr;
            });
            renderEventCard(todayEvents);
        });
    }


    function debounce(func, delay) {
        let timer;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, arguments), delay);
        };
    }

    // Init default tab
    const defaultBtn =
        document.querySelector('.tab-button.is-active[data-tab]') ||
        document.querySelector('.tab-button[data-tab="all"]') ||
        document.querySelector('.tab-button[data-tab]');

    if (defaultBtn) {
        activeTab = defaultBtn.dataset.tab;
        switchTab(activeTab);
    }
});
</script>

{{-- NOTES CRUD, COLORS, FILTERS --}}
<script>
    function loadNotes() {
        $.ajax({
            url: "{{ route('notes') }}",
            method: "GET",
            success: function(response) {
                const noteList = $('#personal-note-list');
                if (!noteList.length) return;
                noteList.empty();

                response.notes.forEach(note => {
                    noteList.append(`
                        <li class="list-group-item" data-id="${note.id}">
                            <div class="media relative" style="cursor:pointer;">
                                <div class="mr-2 pt-1">
                                    <fieldset>
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                            <input type="checkbox" class="done-checkbox" data-id="${note.id}" ${note.is_done ? 'checked' : ''}>
                                            <span class="vs-checkbox vs-checkbox-sm">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </fieldset>
                                </div>

                               

                                ${note.reminder_date || note.reminder_time ? `
                                    <small class="no-reminder-icon-top"
                                           data-id="${note.id}"
                                           data-toggle="tooltip"
                                           title="Erinnerung: ${note.reminder_date || ''} ${note.reminder_time || ''}">
                                        <i class="feather icon-bell" style="font-size:16px;color:#74b2d4;"></i>
                                    </small>` : ''}

                                ${note.repeat ? `
                                    <small class="no-repeat-icon-top"
                                           data-id="${note.id}"
                                           data-toggle="tooltip"
                                           title="Wiederholung: ${note.repeat}">
                                        <i class="fa fa-refresh" style="font-size:16px;color:#8fc73e;"></i>
                                    </small>` : ''}

                                <div class="media-body">
                                    <div class="relative">
                                        <span class="badge badge-warning editing-badge" style="position:absolute; top:-18px; left:0; display:none;">Editing…</span>
                                        <h5 class="mt-0 title-field ${note.is_done == 1 ? 'complete' : ''}"
                                            data-id="${note.id}"
                                            data-field="title">
                                            ${note.title}
                                        </h5>
                                    </div>
                                    <div class="relative">
                                        <span class="badge badge-warning editing-badge" style="position:absolute; top:-18px; left:0; display:none;">Editing…</span>
                                        <p class="note-field"
                                           data-id="${note.id}"
                                           data-field="note">${note.note}</p>
                                    </div>

                                    <div class="date flex flex-wrap gap-2 mt-1 text-xs">
                                        <p class="mr-1 change-date" data-id="${note.id}">
                                            <small> 
                                                ${note.deadline || 'Kein Fälligkeitsdatum'}
                                            </small>
                                        </p>
                                        <p class="mr-1 change-time" data-id="${note.id}">
                                              ${note.end_time || 'Keine Endzeit'}</small>
                                        </p>
                                        <p class="mr-1 updateCategoryModal"
                                           data-category-id="${note.category_id}"
                                           data-id="${note.id}">
                                            <small>  ${note.category_name || 'Standard'}</small>
                                        </p>
                                    </div>
                                </div>  
                            </div>
                            <div class="media-footer"  >
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle drag-handle" data-id="${note.id}">
                                        <i class="feather icon-move"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle note-color" data-id="${note.id}">
                                        <i class="feather icon-aperture"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle delete_note" data-id="${note.id}">
                                        <i class="feather icon-trash"></i>
                                    </button>
                                </div>
                        </li>
                    `);
                });

                Sortable.create(document.getElementById('personal-note-list'), {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function() {
                        const order = [];
                        $('#personal-note-list li').each(function() {
                            order.push($(this).data('id'));
                        });
                        updateOrder(order);
                    }
                });
            },
            error: function() {
                Swal.fire('Fehler', 'Notizen konnten nicht geladen werden. Bitte versuchen Sie es erneut.', 'error');
            }
        });
    }

    const saveNoteButton       = $('#save_note_button');
    const saveNoteModal        = $('#newNote');
    const saveNoteForm         = $('#save_note_form');
    const updateCategoryModal  = $('#updateCategoryModal');
    const categorySelect       = $('#update_category_id');

    $(document).ready(function() {
        loadNotes();

        $('.filter').select2({
            placeholder: 'Filter',
            allowClear: true,
            templateResult: formatState,
            templateSelection: formatState,
            escapeMarkup: function(markup) { return markup; }
        });

        $('.filter').on('change', function() {
            var selectedFilter = $(this).val();
            fetchFilteredNotes(selectedFilter);
        });
    });

    $(document).on('click', '.updateCategoryModal', function() {
        const noteId     = $(this).data('id');
        const categoryId = $(this).data('category-id');

        $.ajax({
            url: `{{ url('/fetch_note_category') }}`,
            method: "GET",
            success: function(response) {
                categorySelect.empty();
                response.forEach(category => {
                    const isSelected = category.id === categoryId ? 'selected' : '';
                    categorySelect.append(`<option value="${category.id}" ${isSelected}>${category.category_name}</option>`);
                });

                updateCategoryModal.data('note-id', noteId);
                updateCategoryModal.modal('show');
            },
            error: function() {
                Swal.fire('Fehler', 'Kategorien konnten nicht geladen werden.', 'error');
            }
        });
    });

    $('#update_category').on('click', function() {
        const noteId             = updateCategoryModal.data('note-id');
        const selectedCategoryId = categorySelect.val();

        if (!selectedCategoryId) {
            Swal.fire('Fehler', 'Bitte wählen Sie eine Kategorie aus.', 'error');
            return;
        }

        $.ajax({
            url: `{{ url('/fetch_note_category') }}`,
            method: "PUT",
            data: { _token: "{{ csrf_token() }}" },
            success: function() {
                Swal.fire('Erfolgreich', 'Die Kategorie wurde aktualisiert.', 'success');
                updateCategoryModal.modal('hide');
                loadNotes();
            },
            error: function() {
                Swal.fire('Fehler', 'Die Kategorie konnte nicht aktualisiert werden.', 'error');
            }
        });
    });

    $(document).on('click', '.no-repeat-icon-top', function() {
        const noteId = $(this).data('id');

        Swal.fire({
            title: 'Sind Sie sicher?',
            text: "Wiederholung für diese Notiz entfernen?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, entfernen',
            cancelButtonText: 'Abbrechen',
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/notes_no_repeat') }}/" + noteId,
                    method: "PUT",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function() {
                        Swal.fire('Erfolgreich!', 'Die Wiederholung wurde entfernt.', 'success');
                        loadNotes();
                    },
                    error: function() {
                        Swal.fire('Fehler', 'Die Wiederholung konnte nicht entfernt werden.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.no-reminder-icon-top', function() {
        const noteId = $(this).data('id');

        Swal.fire({
            title: 'Sind Sie sicher?',
            text: "Erinnerung für diese Notiz deaktivieren?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, entfernen',
            cancelButtonText: 'Abbrechen',
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/notes_no_reminder') }}/" + noteId,
                    method: "PUT",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function() {
                        Swal.fire('Erfolgreich!', 'Die Erinnerungsoption wurde entfernt.', 'success');
                        loadNotes();
                    },
                    error: function() {
                        Swal.fire('Fehler', 'Die Erinnerungsoption konnte nicht entfernt werden.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('change', '.done-checkbox', function() {
        const noteId = $(this).data('id');
        const isDone = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: `{{ url('/notes_done') }}/${noteId}`,
            method: "PUT",
            data: {
                _token: "{{ csrf_token() }}",
                is_done: isDone,
            },
            success: function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Status aktualisiert',
                    text: `Die Aufgabe wurde ${isDone ? 'als erledigt' : 'als unerledigt'} markiert.`,
                });
                loadNotes();
            },
            error: function() {
                Swal.fire('Fehler', 'Status konnte nicht aktualisiert werden.', 'error');
            }
        });
    });

    $(document).on('dblclick', '.title-field, .note-field', function() {
        const $element      = $(this);
        const id            = $element.data('id');
        const field         = $element.data('field');
        const originalValue = $element.text();
        const badge         = $element.siblings('.editing-badge');

        badge.show();

        const input = $(`<input type="text" class="form-control form-control-sm" value="${originalValue}">`);
        $element.replaceWith(input);
        input.focus();

        input.on('blur keydown', function(e) {
            if (e.type === 'blur' || e.key === 'Enter') {
                const newValue = input.val().trim();

                if (newValue === originalValue || newValue === '') {
                    input.replaceWith($element);
                    badge.hide();
                    return;
                }

                $.ajax({
                    url: field === 'title'
                        ? `{{ url('/notes_update_name') }}/${id}`
                        : `{{ url('/notes_update_note') }}/${id}`,
                    method: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                        [field]: newValue
                    },
                    success: function() {
                        $element.text(newValue);
                        input.replaceWith($element);
                        badge.hide();
                        Swal.fire('Erfolgreich', 'Die Notiz wurde aktualisiert.', 'success');
                    },
                    error: function() {
                        input.replaceWith($element);
                        badge.hide();
                        Swal.fire('Fehler', 'Die Notiz konnte nicht aktualisiert werden.', 'error');
                    }
                });
            }
        });
    });

    $('#save_note_form').on('keydown', function(event) {
        if (event.key === 'Enter') event.preventDefault();
    });

    // Create / save new note
    $(document).on('click', '#save_note_button', function (e) {
        e.preventDefault();

        const $form    = $('#save_note_form');
        const formData = $form.serialize();

        $.ajax({
            url: "{{ route('notes.store') }}",
            method: "POST",
            data: formData,
            success: function () {
                Swal.fire('Erfolgreich', 'Die Notiz wurde gespeichert.', 'success');

                $('#newNote').modal('hide');
                $form[0].reset();

                // Reset availability flag for next time
                $('#check_availability').val('false');

                // reload list in sidebar
                loadNotes();
            },
            error: function (xhr) {
                console.error('Note create error', xhr.status, xhr.responseText);

                // Konflikt-Fall mit vorhandenen Aufgaben
                if (xhr.status === 409 && xhr.responseJSON && xhr.responseJSON.availability) {
                    const tableHtml = `
                        <div style="max-height: 500px; overflow-y: auto; padding: 10px;">
                            <p>Es gibt bestehende Aufgaben im angegebenen Zeitraum:</p>
                            <table class="table table-bordered" style="width: 100%; font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>Titel</th>
                                        <th>Startdatum</th>
                                        <th>Enddatum</th>
                                        <th>Mitarbeiter</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${xhr.responseJSON.availability.map(task => `
                                        <tr>
                                            <td>${task.task_title}</td>
                                            <td>${task.start_date}</td>
                                            <td>${task.end_date}</td>
                                            <td>${(task.name || '') + ' ' + (task.lastname || '')}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                            <p>Möchten Sie trotzdem fortfahren?</p>
                        </div>
                    `;

                    Swal.fire({
                        title: 'Konflikte erkannt!',
                        html: tableHtml,
                        icon: 'warning',
                        customClass: { popup: 'swal-wide' },
                        showCancelButton: true,
                        confirmButtonText: 'Trotzdem speichern',
                        cancelButtonText: 'Abbrechen',
                    }).then(result => {
                        if (result.isConfirmed) {
                            $('#check_availability').val('true');
                            $('#save_note_button').trigger('click');
                        }
                    });
                } else {
                    const errors = xhr.responseJSON && xhr.responseJSON.errors
                        ? Object.values(xhr.responseJSON.errors).join('<br>')
                        : 'Die Notiz konnte nicht gespeichert werden.';
                    Swal.fire('Fehler', errors, 'error');
                }
            }
        });
    });

    // Color picker in note modal
    $(document).on('click', '#newNote .dropdown-item', function () {
        const color = $(this).data('value');
        $('#color').val(color);
        $('#colorIcon').css('color', color);
    });

    // Load categories when modal opens
    $('#newNote').on('show.bs.modal', function () {
        const select = $('#category_id');

        $.ajax({
            url: `{{ url('/fetch_note_category') }}`, 
            method: "GET",
            success: function (response) {
                select.empty();
                (response.categories || []).forEach(cat => {
                    select.append(
                        `<option value="${cat.id}">${cat.category_name}</option>`
                    );
                });
            },
            error: function () {
                Swal.fire('Fehler', 'Kategorien konnten nicht geladen werden.', 'error');
            }
        });
    });


    function toggleTodoCollapse(wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const content = wrapper.querySelector('.toggle-content');
        const arrow   = wrapper.querySelector('.arrow');
        if (!content) return;

        const isHidden = content.style.display === 'none' || !content.style.display;
        content.style.display = isHidden ? 'block' : 'none';
        if (arrow) arrow.textContent = isHidden ? '▼' : '▶';
    }

    function toggleSection(id) {
        const row = document.getElementById(id);
        if (!row) return;
        const isHidden = row.style.display === 'none' || !row.style.display;
        row.style.display = isHidden ? 'table-row' : 'none';
    }

    function togglePriorityDropdown() {
        const dd = document.getElementById('priorityDropdown');
        if (!dd) return;
        dd.style.display = (dd.style.display === 'none' || !dd.style.display) ? 'block' : 'none';
    }

    function setPriorityDropdown(value, label) {
        $('#priority').val(value);
        $('#priorityDropdownBtn').text(label + ' ▼');
        $('#priorityDropdown').hide();
    }

    // close priority dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const dropdown   = document.getElementById('priorityDropdown');
        const dropdownBtn = document.getElementById('priorityDropdownBtn');
        if (!dropdown || !dropdownBtn) return;

        if (!dropdown.contains(e.target) && !dropdownBtn.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });





    $(document).on('click', '.note-color', function() {
        const noteId       = $(this).data('id');
        const currentColor = $(this).find('i').css('color');

        const colors = [
            '#8fc73e', '#e83e8c', '#74b2d4', '#2c3e50', '#eff6ff',
            '#ff0000', '#0000ff', '#ffff00', '#ff00ff', '#00ffff'
        ];

        let colorOptions = colors.map(color => `
            <div style="display:inline-block; margin:5px;">
                <button class="color-btn" data-color="${color}" style="background-color:${color}; border:none; width:30px; height:30px; border-radius:50%;"></button>
            </div>
        `).join('');

        Swal.fire({
            title: 'Wählen Sie eine Farbe',
            html: `
                <div style="display:flex; flex-wrap:wrap; justify-content:center;">${colorOptions}</div>
                <p style="margin-top:10px; text-align:center;">
                    Aktuelle Farbe:
                    <span style="color:${currentColor}; font-weight:bold;">${currentColor}</span>
                </p>
            `,
            showCancelButton: true,
            cancelButtonText: 'Abbrechen',
            showConfirmButton: false,
            didOpen: () => {
                $('.color-btn').on('click', function() {
                    const selectedColor = $(this).data('color');

                    $.ajax({
                        url: `{{ url('/note_change_color') }}/${noteId}`,
                        method: 'PUT',
                        data: {
                            _token: "{{ csrf_token() }}",
                            color: selectedColor
                        },
                        success: function() {
                            Swal.fire('Erfolgreich', 'Die Farbe wurde aktualisiert.', 'success');
                            loadNotes();
                        },
                        error: function() {
                            Swal.fire('Fehler', 'Die Farbe konnte nicht aktualisiert werden.', 'error');
                        }
                    });
                });
            }
        });
    });

    $(document).on('click', '.change-date', function() {
        const noteId = $(this).data('id');

        Swal.fire({
            title: 'Neues Datum wählen',
            html: `
                <div style="display:flex; flex-direction:column; align-items:center;">
                    <label for="new-deadline" style="margin-bottom:10px;">Neues Datum:</label>
                    <input type="date" id="new-deadline" class="form-control">
                </div>
            `,
            showCancelButton: true,
            cancelButtonText: 'Abbrechen',
            confirmButtonText: 'Speichern',
            preConfirm: () => {
                const selectedDate = document.getElementById('new-deadline').value;
                if (!selectedDate) {
                    Swal.showValidationMessage('Bitte wählen Sie ein Datum.');
                }
                return selectedDate;
            }
        }).then(result => {
            if (result.isConfirmed) {
                const selectedDate = result.value;

                $.ajax({
                    url: `{{ url('/note_change_date') }}/${noteId}`,
                    method: 'PUT',
                    data: {
                        _token: "{{ csrf_token() }}",
                        deadline: selectedDate
                    },
                    success: function() {
                        Swal.fire('Erfolgreich', 'Das Datum wurde geändert.', 'success');
                        loadNotes();
                    },
                    error: function() {
                        Swal.fire('Fehler', 'Das Datum konnte nicht geändert werden.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.change-time', function() {
        const noteId = $(this).data('id');

        Swal.fire({
            title: 'Neue Zeit wählen',
            html: `
                <div style="display:flex; flex-direction:column; align-items:center;">
                    <label for="new-end-time" style="margin-bottom:10px;">Neue Uhrzeit:</label>
                    <input type="time" id="new-end-time" class="form-control">
                </div>
            `,
            showCancelButton: true,
            cancelButtonText: 'Abbrechen',
            confirmButtonText: 'Speichern',
            preConfirm: () => {
                const selectedTime = document.getElementById('new-end-time').value;
                if (!selectedTime) {
                    Swal.showValidationMessage('Bitte wählen Sie eine Uhrzeit.');
                }
                return selectedTime;
            }
        }).then(result => {
            if (result.isConfirmed) {
                const selectedTime = result.value;

                $.ajax({
                    url: `{{ url('/note_change_time') }}/${noteId}`,
                    method: 'PUT',
                    data: {
                        _token: "{{ csrf_token() }}",
                        end_time: selectedTime
                    },
                    success: function() {
                        Swal.fire('Erfolgreich', 'Die Uhrzeit wurde geändert.', 'success');
                        loadNotes();
                    },
                    error: function() {
                        Swal.fire('Fehler', 'Die Uhrzeit konnte nicht geändert werden.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.delete_note', function() {
        const noteId = $(this).data('id');

        Swal.fire({
            title: 'Bist du sicher?',
            text: "Diese Aktion kann nicht rückgängig gemacht werden!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen',
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/notes_delete') }}/${noteId}`,
                    method: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function() {
                        Swal.fire('Gelöscht!', 'Die Notiz wurde gelöscht.', 'success');
                        loadNotes();
                    },
                    error: function() {
                        Swal.fire('Fehler', 'Die Notiz konnte nicht gelöscht werden.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.trash_box', function() {
        $.ajax({
            url: "{{ route('notes.trash') }}",
            method: "GET",
            success: function(response) {
                let tableHtml = `
                    <div style="max-height: 500px; overflow-y: auto; padding: 10px;">
                        <table class="table table-bordered" style="width: 100%; font-size: 14px;">
                            <thead>
                                <tr>
                                    <th>Titel</th>
                                    <th>Kategorie</th>
                                    <th>Erstellt am</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${response.notes.map(note => `
                                    <tr data-id="${note.id}">
                                        <td>${note.title}</td>
                                        <td>${note.category_name}</td>
                                        <td>${new Date(note.created_at).toLocaleDateString()}</td>
                                        <td>
                                            <button class="btn btn-danger btn-sm permanent-delete" data-id="${note.id}">Dauerhaft löschen</button>
                                            <button class="btn btn-success btn-sm recover-note" data-id="${note.id}">Wiederherstellen</button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>`;

                Swal.fire({
                    title: 'Papierkorb',
                    html: tableHtml,
                    showCancelButton: true,
                    cancelButtonText: 'Schließen',
                    showConfirmButton: false,
                    width: '800px',
                });
            },
            error: function() {
                Swal.fire('Fehler', 'Daten konnten nicht geladen werden.', 'error');
            }
        });
    });

    $(document).on('click', '.permanent-delete', function() {
        const noteId = $(this).data('id');
        Swal.fire({
            title: 'Bist du sicher?',
            text: 'Diese Aktion kann nicht rückgängig gemacht werden!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen',
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/notes_permanent_delete') }}/${noteId}`,
                    method: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function() {
                        Swal.fire('Erfolgreich!', 'Die Notiz wurde dauerhaft gelöscht.', 'success');
                        $(`tr[data-id="${noteId}"]`).remove();
                        loadNotes();
                    },
                    error: function() {
                        Swal.fire('Fehler', 'Die Notiz konnte nicht gelöscht werden.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.recover-note', function() {
        const noteId = $(this).data('id');
        Swal.fire({
            title: 'Bist du sicher?',
            text: 'Möchten Sie diese Notiz wiederherstellen?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ja, wiederherstellen!',
            cancelButtonText: 'Abbrechen',
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/notes_recover') }}/${noteId}`,
                    method: 'PUT',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function() {
                        Swal.fire('Erfolgreich!', 'Die Notiz wurde wiederhergestellt.', 'success');
                        $(`tr[data-id="${noteId}"]`).remove();
                        loadNotes();
                    },
                    error: function() {
                        Swal.fire('Fehler', 'Die Notiz konnte nicht wiederhergestellt werden.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.note-settings', function() {
        const noteId = $(this).data('id');

        $.ajax({
            url: `{{ url('/notes') }}/${noteId}`,
            method: "GET",
            success: function(response) {
                const note = response.note;

                $('#updateSettingModal input[name="deadline"]').val(note.deadline);
                $('#updateSettingModal input[name="end_time"]').val(note.end_time);
                $('#updateSettingModal input[name="add_calendar_date"]').val(note.add_calendar_date);
                $('#updateSettingModal select[name="repeat"]').val(note.repeat);
                $('#updateSettingModal input[name="reminder_date"]').val(note.reminder_date);
                $('#updateSettingModal input[name="reminder_time"]').val(note.reminder_time);
                $('#updateSettingModal input[name="priority"]').val(note.priority);

                $('#updateSettingModal').data('note-id', noteId).modal('show');
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire('Fehler', 'Die Notizdaten konnten nicht geladen werden.', 'error');
            }
        });
    });

    $('#save_note_settings').on('click', function() {
        const noteId = $('#updateSettingModal').data('note-id');

        $.ajax({
            url: `{{ url('/notes_update_settings') }}/${noteId}`,
            method: "PUT",
            data: {
                _token: "{{ csrf_token() }}",
                deadline: $('#updateSettingForm input[name="deadline"]').val(),
                end_time: $('#updateSettingForm input[name="end_time"]').val(),
                add_calendar_date: $('#updateSettingForm input[name="add_calendar_date"]').val(),
                repeat: $('#updateSettingForm select[name="repeat"]').val(),
                reminder_date: $('#updateSettingForm input[name="reminder_date"]').val(),
                reminder_time: $('#updateSettingForm input[name="reminder_time"]').val(),
                priority: $('#updateSettingForm input[name="priority"]').val(),
                check_emp: $('#updateSettingForm input[name="check_emp"]').val(),
            },
            success: function(response) {
                Swal.fire('Erfolgreich', response.message, 'success');
                $('#updateSettingModal').modal('hide');
                loadNotes();
            },
            error: function(xhr) {
                if (xhr.status === 409 && xhr.responseJSON.availability) {
                    const conflicts = xhr.responseJSON.availability;
                    let conflictTable = `
                        <div style="max-height: 500px; overflow-y: auto; padding: 10px;">
                            <p>Es gibt bestehende Aufgaben im angegebenen Zeitraum:</p>
                            <table class="table table-bordered" style="width: 100%; font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>Titel</th>
                                        <th>Startdatum</th>
                                        <th>Enddatum</th>
                                        <th>Mitarbeiter</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${conflicts.map(conflict => `
                                        <tr>
                                            <td>${conflict.task_title}</td>
                                            <td>${conflict.start_date}</td>
                                            <td>${conflict.end_date}</td>
                                            <td>${conflict.name || ''} ${conflict.lastname || ''}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                            <p>Möchten Sie trotzdem fortfahren?</p>
                        </div>
                    `;

                    Swal.fire({
                        title: 'Konflikte erkannt!',
                        html: conflictTable,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Trotzdem speichern',
                        cancelButtonText: 'Abbrechen',
                    }).then(result => {
                        if (result.isConfirmed) {
                            $('#check_emp').val('true');

                            $.ajax({
                                url: `{{ url('/notes_update_settings') }}/${noteId}`,
                                method: "PUT",
                                data: $('#updateSettingForm').serialize(),
                                success: function(response2) {
                                    Swal.fire('Erfolgreich', response2.message, 'success');
                                    $('#updateSettingModal').modal('hide');
                                    loadNotes();
                                },
                                error: function() {
                                    Swal.fire('Fehler', 'Die Einstellungen konnten nicht aktualisiert werden.', 'error');
                                }
                            });
                        }
                    });
                } else {
                    const errorMessage = Object.values(xhr.responseJSON.errors || {}).join('<br>') || 'Die Einstellungen konnten nicht gespeichert werden.';
                    Swal.fire('Fehler', errorMessage, 'error');
                }
            }
        });
    });

    function updateOrder(order) {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/notes/update-order',
            type: 'POST',
            data: JSON.stringify({ order }),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(response) {
                if (response.message && window.toastr) {
                    toastr.success(response.message, 'Erfolg');
                }
            },
            error: function() {
                if (window.toastr) {
                    toastr.error('Fehler beim Aktualisieren der Reihenfolge.', 'Fehler');
                }
            }
        });
    }

    function formatState(state) {
        if (!state.id) return state.text;
        var icon = '<i class="feather icon-filter"></i>';

        switch (state.id) {
            case 'date':      icon = '<i class="feather icon-calendar"></i>'; break;
            case 'sort':      icon = '<i class="fa fa-sort"></i>'; break;
            case 'calendar':  icon = '<i class="feather icon-calendar"></i>'; break;
            case 'reminder':  icon = '<i class="fa fa-bell"></i>'; break;
            case 'repeat':    icon = '<i class="feather icon-refresh-ccw"></i>'; break;
        }
        return icon + ' ' + state.text;
    }

    function fetchFilteredNotes(filter) {
        if (!filter) {
            $('#personal-note-list').html('<li class="list-group-item text-xs text-slate-400">Bitte Filter wählen.</li>');
            return;
        }

        $.ajax({
            url: '/note_view_filter',
            type: 'GET',
            data: { filter },
            dataType: 'json',
            success: function(response) {
                if (response.notes) {
                    updateNotesList(response.notes);
                    if (window.toastr) toastr.success(response.message, 'Erfolg');
                }
            },
            error: function() {
                if (window.toastr) toastr.error('Fehler beim Filtern der Notizen.', 'Fehler');
            }
        });
    }

    function updateNotesList(notes) {
        var notesList = $('#personal-note-list');
        notesList.empty();

        if (!notes.length) {
            notesList.append('<li class="list-group-item text-xs text-slate-400">Keine Notizen gefunden.</li>');
            return;
        }

        notes.forEach(function(note) {
            notesList.append(`
                <li class="list-group-item" data-id="${note.id}">
                    <div class="media relative">
                        <fieldset>
                            <div class="vs-checkbox-con vs-checkbox-primary">
                                <input type="checkbox" class="done-checkbox" data-id="${note.id}" ${note.is_done ? 'checked' : ''}>
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                            </div>
                        </fieldset>
                        
                        <div class="media-body">
                            <div class="relative">
                                <span class="badge badge-warning editing-badge" style="position:absolute; top:-18px; left:0; display:none;">Editing…</span>
                                <h5 class="mt-0 title-field ${note.is_done ? 'complete' : ''}" data-id="${note.id}" data-field="title">${note.title}</h5>
                            </div>
                            <div class="relative">
                                <span class="badge badge-warning editing-badge" style="position:absolute; top:-18px; left:0; display:none;">Editing…</span>
                                <p class="note-field" data-id="${note.id}" data-field="note">${note.note}</p>
                            </div>
                            <div class="date d-flex flex-wrap gap-2 text-xs mt-1">
                                <p class="mr-1 change-date" data-id="${note.id}">
                                    <small><i class="feather icon-calendar ${note.add_calendar_date ? 'primary' : ''}"></i> ${note.deadline || 'Kein Fälligkeitsdatum'}</small>
                                </p>
                                <p class="mr-1 change-time" data-id="${note.id}">
                                    <small><i class="feather icon-clock"></i> ${note.end_time || 'Keine Endzeit'}</small>
                                </p>
                                <p class="mr-1 updateCategoryModal" data-category-id="${note.category_id}" data-id="${note.id}">
                                    <small><i class="feather icon-slack"></i> ${note.category_name || 'Standard'}</small>
                                </p>
                            </div>
                        </div>
                        <div class="media-footer" style="position:absolute; right:-24px; top:0; display:flex; flex-direction:column;">
                            <button type="button" class="btn btn-icon rounded-circle delete_note" data-id="${note.id}">
                                <i class="feather icon-trash"></i>
                            </button>
                            <button type="button" class="btn btn-icon rounded-circle note-color" data-id="${note.id}">
                                <i class="feather icon-aperture" style="color:${note.color}"></i>
                            </button>
                            <button type="button" class="btn btn-icon rounded-circle drag-handle" data-id="${note.id}">
                                <i class="feather icon-move" style="color:${note.color}"></i>
                            </button>
                        </div>
                    </div>
                </li>
            `);
        });

        Sortable.create(document.getElementById('personal-note-list'), {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function() {
                var order = [];
                $('#personal-note-list li').each(function() {
                    order.push($(this).data('id'));
                });
                updateOrder(order);
            }
        });
    }
</script>

{{-- KPI + GOAL LIST (DUE TODAY) --}}
<script>
(() => {
  'use strict';

  /* =========================
   * State
   * ========================= */
  let allItems = [];
  let currentFilter = 'all';
  let currentSort   = 'due_asc';

  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

  /* =========================
   * Helpers
   * ========================= */
  const qs  = (sel, root = document) => root.querySelector(sel);
  const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  function toDateOrNull(val) {
    if (!val) return null;
    const d = new Date(val);
    return Number.isNaN(d.getTime()) ? null : d;
  }

  function formatDateDE(val) {
    const d = toDateOrNull(val);
    return d ? d.toLocaleDateString('de-DE') : '';
  }

  function clamp(n, a, b) {
    n = Number(n) || 0;
    return Math.max(a, Math.min(b, n));
  }

  function escapeText(s) {
    return (s == null) ? '' : String(s);
  }

  /* =========================
   * Styles (fix visibility + nicer layout)
   * ========================= */
  function ensureGoalStyles() {
    if (qs('#goal-script-styles')) return;

    const style = document.createElement('style');
    style.id = 'goal-script-styles';
    style.textContent = `
      /* Scope everything to #goalList to avoid side effects */
      #goalList .goal-item{
        display:flex;
        align-items:stretch;
        justify-content:space-between;
        gap:.9rem;
        padding:.75rem .85rem;
        border:1px solid rgba(148,163,184,.35);
        border-radius:14px;
        background:#fff;
        box-shadow:0 1px 0 rgba(15,23,42,.03);
        transition:transform .12s ease, box-shadow .12s ease, border-color .12s ease;
      }
      #goalList .goal-item:hover{
        transform:translateY(-1px);
        border-color:rgba(148,163,184,.55);
        box-shadow:0 8px 22px rgba(15,23,42,.08);
      }

      #goalList .goal-item-main{
        display:flex;
        gap:.65rem;
        align-items:flex-start;
        min-width:0;
        flex:1 1 auto;
      }

      #goalList .goal-icon{
        width:34px;height:34px;
        border-radius:12px;
        display:grid;
        place-items:center;
        flex:0 0 auto;
        background:rgba(241,245,249,.9);
        border:1px solid rgba(148,163,184,.25);
      }
      #goalList .goal-icon svg{width:18px;height:18px;display:block}

      #goalList .goal-item-text{min-width:0}
      #goalList .goal-item-text .title{
        font-size:.9rem;
        line-height:1.25rem;
        font-weight:600;
        color:#0f172a;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
        max-width:100%;
      }
      #goalList .goal-item-text .meta{
        margin-top:.2rem;
        font-size:.75rem;
        line-height:1.05rem;
        color:#64748b;
        display:flex;
        gap:.55rem;
        align-items:center;
        flex-wrap:wrap;
      }
      #goalList .goal-item-text .meta .sep{opacity:.5}

      #goalList .goal-item-text label{
        display:inline-flex;
        gap:.4rem;
        align-items:center;
        user-select:none;
        cursor:pointer;
        color:#475569;
      }
      #goalList .goal-item-text input[type="checkbox"]{
        width:14px;height:14px;
        border-radius:4px;
        border:1px solid rgba(100,116,139,.5);
      }

      #goalList .goal-item-side{
        display:flex;
        flex-direction:column;
        align-items:flex-end;
        justify-content:center;
        gap:.45rem;
        flex:0 0 auto;
      }

      #goalList .goal-item-tag{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-size:.70rem;
        padding:.28rem .6rem;
        border-radius:9999px;
        color:#fff;
        text-decoration:none;
        line-height:1;
        box-shadow:0 6px 14px rgba(15,23,42,.08);
        transition:transform .12s ease, filter .12s ease;
      }
      #goalList .goal-item-tag:hover{transform:translateY(-1px);filter:brightness(1.02)}

      /* Days passed pill (VISIBLE on light backgrounds) */
      #goalList .goal-item-days{
        display:flex;
        align-items:center;
        gap:.35rem;
        font-size:.72rem;
        line-height:1;
      }
      #goalList .goal-item-days .pill{
        display:inline-flex;
        align-items:center;
        gap:.35rem;
        padding:.28rem .55rem;
        border-radius:9999px;
        white-space:nowrap;
        background:#f1f5f9;
        color:#0f172a;
        border:1px solid rgba(148,163,184,.35);
      }
      #goalList .goal-item-days .dot{
        width:.40rem;height:.40rem;border-radius:9999px;
        background:currentColor;opacity:.9;
      }
      #goalList .goal-countup{
        display:inline-block;
        min-width:2.2ch;
        text-align:right;
        font-variant-numeric:tabular-nums;
        font-weight:700;
      }

      /* States */
      #goalList .goal-item-days.is-ok .pill{
        background:#ecfdf5;
        color:#065f46;
        border-color:rgba(16,185,129,.35);
      }
      #goalList .goal-item-days.is-future .pill{
        background:#eff6ff;
        color:#1e40af;
        border-color:rgba(59,130,246,.35);
      }
      #goalList .goal-item-days.is-late .pill{
        background:#ffe4e6;
        color:#9f1239;
        border-color:rgba(244,63,94,.35);
      }

      /* Late animation (subtle, readable) */
      #goalList .goal-item-days.is-late .dot{animation:goalDotPulse 1.1s ease-in-out infinite}
      @keyframes goalDotPulse{0%,100%{transform:scale(1);opacity:.85}50%{transform:scale(1.35);opacity:1}}

      /* Dark mode support (if your html/body toggles .dark) */
      .dark #goalList .goal-item{
        background:rgba(15,23,42,.65);
        border-color:rgba(51,65,85,.75);
        box-shadow:none;
      }
      .dark #goalList .goal-item:hover{
        border-color:rgba(71,85,105,.9);
        box-shadow:0 10px 26px rgba(0,0,0,.35);
      }
      .dark #goalList .goal-icon{background:rgba(30,41,59,.8);border-color:rgba(51,65,85,.9)}
      .dark #goalList .goal-item-text .title{color:#e2e8f0}
      .dark #goalList .goal-item-text .meta{color:#94a3b8}
      .dark #goalList .goal-item-text label{color:#cbd5e1}
      .dark #goalList .goal-item-days .pill{background:rgba(30,41,59,.8);color:#e2e8f0;border-color:rgba(51,65,85,.9)}
      .dark #goalList .goal-item-days.is-ok .pill{background:rgba(6,95,70,.22);color:#a7f3d0;border-color:rgba(16,185,129,.35)}
      .dark #goalList .goal-item-days.is-future .pill{background:rgba(30,64,175,.18);color:#bfdbfe;border-color:rgba(59,130,246,.35)}
      .dark #goalList .goal-item-days.is-late .pill{background:rgba(159,18,57,.20);color:#fecdd3;border-color:rgba(244,63,94,.35)}
    `;
    document.head.appendChild(style);
  }

  /* =========================
   * Toast / badge
   * ========================= */
  function showToast(message = 'Als erledigt markiert!') {
    const toast = qs('#toastSuccess');
    if (!toast) return;
    toast.textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 2500);
  }

  function updateTodayBadge(percent) {
    const textEl = qs('#todayPercentText');
    const bar    = qs('#todayProgressBar');
    if (!textEl || !bar) return;

    const p = clamp(percent, 0, 100);
    textEl.textContent = `${p}%`;
    bar.style.width = `${p}%`;
  }

  /* =========================
   * KPI counters
   * ========================= */
  function isOverdue48(dueDate) {
    const d = toDateOrNull(dueDate);
    if (!d) return false;
    return (Date.now() - d.getTime()) > (48 * 60 * 60 * 1000);
  }

  function updateCountersView(items) {
    const list = Array.isArray(items) ? items : [];

    const total            = list.length;
    const leadCount        = list.filter(i => i.type === 'lead').length;
    const anfrageCount     = list.filter(i => i.type === 'inquiry').length;
    const aufgabeCount     = list.filter(i => i.type === 'personal_task').length;
    const appointmentCount = list.filter(i => i.type === 'appointment').length;
    const restCount        = list.filter(i => !['lead','inquiry','personal_task','appointment'].includes(i.type)).length;

    const map = {
      all: total,
      lead: leadCount,
      anfrage: anfrageCount,
      aufgabe: aufgabeCount,
      appointment: appointmentCount,
      rest: restCount
    };

    for (const [key, val] of Object.entries(map)) {
      const el = qs(`#count-${key}`);
      if (el) el.textContent = `(${val})`;
    }

    const openToday = list.filter(i => !!i.is_today).length;
    const overdue   = list.filter(i => isOverdue48(i.due_date)).length;

    const elOpen = qs('#kpi-open-today');
    const elOver = qs('#kpi-overdue');
    if (elOpen) elOpen.textContent = openToday;
    if (elOver) elOver.textContent = overdue;
  }

  /* =========================
   * Filter / sort
   * ========================= */
  function setActiveFilterChip() {
    qsa('.goal-filter-chip').forEach(btn => {
      const f = btn.getAttribute('data-filter');
      btn.classList.toggle('active', f === currentFilter);
    });
  }

  function getFilteredItems() {
    if (!Array.isArray(allItems)) return [];
    switch (currentFilter) {
      case 'aufgabe':     return allItems.filter(i => i.type === 'personal_task');
      case 'anfrage':     return allItems.filter(i => i.type === 'inquiry');
      case 'appointment': return allItems.filter(i => i.type === 'appointment');
      case 'lead':        return allItems.filter(i => i.type === 'lead');
      case 'rest':        return allItems.filter(i => !['lead','personal_task','inquiry','appointment'].includes(i.type));
      case 'all':
      default:            return [...allItems];
    }
  }

  function mapPriority(prio) {
    if (!prio) return 1;
    const p = String(prio).toLowerCase();
    if (p.includes('very') || p.includes('sehr')) return 3;
    if (p.includes('high') || p.includes('dring')) return 2;
    if (p.includes('low')  || p.includes('niedrig')) return 0.5;
    return 1;
  }

  function sortItems(items) {
    const copy = [...items];
    copy.sort((a, b) => {
      const da = toDateOrNull(a.due_date || a.created_at);
      const db = toDateOrNull(b.due_date || b.created_at);
      const pa = mapPriority(a.priority);
      const pb = mapPriority(b.priority);

      switch (currentSort) {
        case 'due_desc':
          if (da && db) return db - da;
          if (da && !db) return -1;
          if (!da && db) return 1;
          return 0;
        case 'prio_desc':
          return pb - pa;
        case 'prio_asc':
          return pa - pb;
        case 'due_asc':
        default:
          if (da && db) return da - db;
          if (da && !db) return -1;
          if (!da && db) return 1;
          return 0;
      }
    });
    return copy;
  }

  /* =========================
   * Links
   * ========================= */
  function getItemLink(item) {
    switch (item.type) {
      case 'appointment':   return `/appointment_details/${item.id}`;
      case 'personal_task': return `/personal_task_details/${item.id}`;
      case 'problem':       return `/problem/profile/${item.id}`;
      case 'ticket_task':   return `/ticket_task_details/${item.id}`;
      case 'inquiry':       return `/inquiry/${item.id}`;
      case 'lead':          return `/lead/product/${item.id}`;
      case 'leave':         return '#';
      default:              return '#';
    }
  }

  /* =========================
   * Network
   * ========================= */
  async function postJSON(url, payload) {
    const res = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF
      },
      body: JSON.stringify(payload || {})
    });

    const ct = res.headers.get('content-type') || '';
    if (ct.includes('application/json')) {
      const data = await res.json().catch(() => ({}));
      if (!res.ok || data.success === false) {
        throw new Error(data.message || `HTTP ${res.status}`);
      }
      return data;
    }

    const text = await res.text().catch(() => '');
    let hint = `Unexpected response (Content-Type: ${ct || 'unknown'})`;
    if (res.status === 419) hint = 'CSRF token mismatch (419).';
    if (res.status === 401) hint = 'Unauthenticated (401).';
    if (res.status === 403) hint = 'Forbidden (403).';
    throw new Error(`${hint} First bytes: ${text.slice(0, 120)}`);
  }

  async function fetchDueToday() {
    const res = await fetch('/my/due-today', { credentials: 'same-origin' });
    const ct = res.headers.get('content-type') || '';
    if (!ct.includes('application/json')) {
      const t = await res.text().catch(() => '');
      throw new Error(`Expected JSON from /my/due-today. Got: ${ct || 'unknown'} ${t.slice(0, 120)}`);
    }
    const data = await res.json();
    if (!res.ok) throw new Error(data?.message || `HTTP ${res.status}`);
    return data || {};
  }

  /* =========================
   * SVG icons (no feather)
   * ========================= */
  function svgIcon(type, tone = 'slate') {
    // stroke uses currentColor; wrapper decides color
    const base = `viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"`;
    const icons = {
      personal_task: `<svg ${base}><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 4.3 2.6 18.6A2 2 0 0 0 4.3 21h15.4a2 2 0 0 0 1.7-2.4L13.7 4.3a2 2 0 0 0-3.4 0Z"/></svg>`,
      appointment:   `<svg ${base}><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>`,
      ticket_task:   `<svg ${base}><path d="M10 6h10"/><path d="M10 12h10"/><path d="M10 18h10"/><path d="M4 6h.01"/><path d="M4 12h.01"/><path d="M4 18h.01"/></svg>`,
      inquiry:       `<svg ${base}><path d="M9.1 9a3 3 0 1 1 4.9 2.3c-.9.7-1.5 1.1-1.5 2.7"/><path d="M12 17h.01"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>`,
      lead:          `<svg ${base}><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="8" r="4"/></svg>`,
      leave:         `<svg ${base}><path d="M12 2v20"/><path d="M7 7c2 0 3-1 5-1s3 1 5 1v7c-2 0-3-1-5-1s-3 1-5 1Z"/></svg>`,
      problem:       `<svg ${base}><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 4.3 2.6 18.6A2 2 0 0 0 4.3 21h15.4a2 2 0 0 0 1.7-2.4L13.7 4.3a2 2 0 0 0-3.4 0Z"/></svg>`,
      default:       `<svg ${base}><path d="M12 20v-6"/><path d="M12 8h.01"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>`
    };

    return icons[type] || icons.default;
  }

  function badgeColor(item) {
    // keep your colors, but consistent
    if (item.type === 'inquiry') return '#f97316';
    if (item.type === 'leave')   return '#8fc73e';
    return '#8fc73e'; // unified green like screenshot
  }

  function iconTone(item) {
    // readable accents
    if (item.type === 'inquiry') return '#f97316';
    if (item.type === 'appointment') return '#ef4444';
    return '#16a34a';
  }

  /* =========================
   * Days passed (below badge)
   * - based on due_date if exists; else created_at
   * - highlight if passed > 48h
   * ========================= */
  function baseDate(item) {
    return item?.due_date || item?.created_at || null;
  }

  function elapsedInfo(item) {
    const d = toDateOrNull(baseDate(item));
    if (!d) return null;

    const now = Date.now();
    const diffMs = now - d.getTime();
    const diffHours = diffMs / (1000 * 60 * 60);
    const diffDays  = diffMs / (1000 * 60 * 60 * 24);

    // future
    if (diffMs < 0) {
      const days = Math.ceil(Math.abs(diffDays));
      return { mode:'future', unit:'d', target: days, label: `in ${days} Tag${days === 1 ? '' : 'en'}` };
    }

    // < 48h -> show hours
    if (diffHours < 48) {
      const h = Math.max(0, Math.floor(diffHours));
      return { mode:'ok', unit:'h', target: h, label: `vor ${h} Std.` };
    }

    // >= 48h -> show days (late)
    const days = Math.max(0, Math.floor(diffDays));
    return { mode:'late', unit:'d', target: days, label: `vor ${days} Tag${days === 1 ? '' : 'en'}` };
  }

  function animateCount(el, target, duration = 650) {
    const t0 = performance.now();
    const start = 0;

    function frame(t) {
      const p = Math.min(1, (t - t0) / duration);
      const eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
      const v = Math.round(start + (target - start) * eased);
      el.textContent = String(v);
      if (p < 1) requestAnimationFrame(frame);
      else el.textContent = String(target);
    }
    requestAnimationFrame(frame);
  }

  /* =========================
   * Render
   * ========================= */
  function renderGoalList() {
    ensureGoalStyles();

    const goalList = qs('#goalList');
    if (!goalList) return;

    const filtered = getFilteredItems();
    const sorted   = sortItems(filtered);

    goalList.innerHTML = '';

    if (!sorted.length) {
      const empty = document.createElement('div');
      empty.className = 'text-[0.75rem] text-slate-500 italic px-1 py-1';
      empty.textContent = 'Keine Einträge für diesen Filter.';
      goalList.appendChild(empty);
      return;
    }

    const frag = document.createDocumentFragment();

    for (const item of sorted) {
      const wrap = document.createElement('div');
      wrap.className = 'goal-item';
      wrap.dataset.id = item.id ?? '';
      wrap.dataset.type = item.type ?? '';

      /* Left */
      const main = document.createElement('div');
      main.className = 'goal-item-main';

      const iconBox = document.createElement('div');
      iconBox.className = 'goal-icon';
      iconBox.style.color = iconTone(item);
      iconBox.innerHTML = svgIcon(item.type);

      const text = document.createElement('div');
      text.className = 'goal-item-text';

      const title = document.createElement('div');
      title.className = 'title';
      const titleStr = `${escapeText(item.title)}${item.description ? ': ' + escapeText(item.description) : ''}`;
      title.textContent = titleStr || '—';

      const meta = document.createElement('div');
      meta.className = 'meta';

      if (item.due_date) {
        const due = document.createElement('span');
        due.textContent = `Fällig: ${formatDateDE(item.due_date)}`;
        meta.appendChild(due);
      }

      // Actions
      const actions = document.createElement('span');
      actions.className = 'inline-flex items-center gap-2';

      if (item.type === 'leave') {
        const mkBtn = (action, label, bg) => {
          const b = document.createElement('button');
          b.type = 'button';
          b.className = 'btn-leave-action';
          b.dataset.action = action;
          b.textContent = label;
          b.style.cssText = `font-size:.72rem;padding:.28rem .55rem;border-radius:9999px;color:#fff;border:0;${bg}`;
          return b;
        };
        actions.appendChild(mkBtn('approve', 'Annehmen', 'background:#10b981;'));
        actions.appendChild(mkBtn('reject', 'Ablehnen', 'background:#f43f5e;'));
        actions.appendChild(mkBtn('not_responsible', 'Nicht zuständig', 'background:#94a3b8;'));
      } else {
        const label = document.createElement('label');
        const cb = document.createElement('input');
        cb.type = 'checkbox';
        cb.className = 'mark-done-checkbox';

        const sp = document.createElement('span');
        sp.textContent = 'Erledigt';

        label.appendChild(cb);
        label.appendChild(sp);
        actions.appendChild(label);
      }

      if (meta.childNodes.length) {
        const sep = document.createElement('span');
        sep.className = 'sep';
        sep.textContent = '•';
        meta.appendChild(sep);
      }
      meta.appendChild(actions);

      text.appendChild(title);
      text.appendChild(meta);

      main.appendChild(iconBox);
      main.appendChild(text);

      /* Right */
      const side = document.createElement('div');
      side.className = 'goal-item-side';

      const tag = document.createElement('a');
      tag.href = getItemLink(item);
      tag.className = 'goal-item-tag';
      tag.style.background = badgeColor(item);

      const labelTxt = (item.label || '').replace(/_/g, ' ').trim();
      tag.textContent = labelTxt || (item.type || 'Eintrag').replace(/_/g, ' ');

      side.appendChild(tag);

      const eInfo = elapsedInfo(item);
      if (eInfo) {
        const row = document.createElement('div');
        row.className = 'goal-item-days';
        row.classList.add(eInfo.mode === 'late' ? 'is-late' : (eInfo.mode === 'future' ? 'is-future' : 'is-ok'));

        const pill = document.createElement('div');
        pill.className = 'pill';

        const dot = document.createElement('span');
        dot.className = 'dot';

        const num = document.createElement('span');
        num.className = 'goal-countup';
        num.textContent = '0';

        const txt = document.createElement('span');
        // keep label, but replace number part dynamically
        // Example: "vor 3 Tagen" -> we want "vor " + (animated number) + " Tagen"
        // We'll build as: prefix + num + suffix
        const m = eInfo.label.match(/^(in |vor )(\d+)(.*)$/);
        const prefix = m ? m[1] : '';
        const suffix = m ? m[3] : (eInfo.unit === 'h' ? ' Std.' : ' Tage');

        txt.textContent = prefix;

        const tail = document.createElement('span');
        tail.textContent = suffix;

        pill.appendChild(dot);
        pill.appendChild(txt);
        pill.appendChild(num);
        pill.appendChild(tail);

        row.appendChild(pill);
        side.appendChild(row);

        animateCount(num, eInfo.target, eInfo.mode === 'late' ? 900 : 650);
      }

      wrap.appendChild(main);
      wrap.appendChild(side);

      frag.appendChild(wrap);
    }

    goalList.appendChild(frag);
  }

  /* =========================
   * Load
   * ========================= */
  async function loadDueToday() {
    try {
      const data = await fetchDueToday();
      updateTodayBadge(data.percent ?? 0);

      allItems = Array.isArray(data.items) ? data.items : [];

      updateCountersView(allItems);
      setActiveFilterChip();
      renderGoalList();
    } catch (err) {
      console.error('Error loading /my/due-today', err);
    }
  }

  /* =========================
   * Events (delegated)
   * ========================= */
  document.addEventListener('click', (e) => {
    // filter chip
    const chip = e.target.closest('.goal-filter-chip');
    if (chip) {
      currentFilter = chip.getAttribute('data-filter') || 'all';
      setActiveFilterChip();
      renderGoalList();
      return;
    }

    // leave actions
    const btn = e.target.closest('.btn-leave-action');
    if (!btn) return;

    const action = btn.getAttribute('data-action');
    const container = btn.closest('.goal-item');
    if (!container) return;

    const id   = container.getAttribute('data-id');
    const type = container.getAttribute('data-type');
    if (!id || type !== 'leave') return;

    const reload = (msg) => {
      showToast(msg);
      loadDueToday();
    };

    if (action === 'reject') {
      if (!window.Swal) {
        const reason = prompt('Grund der Ablehnung:');
        if (!reason || !reason.trim()) return;
        postJSON('/my/mark-done', { id, type, action, reason: reason.trim() })
          .then(() => reload('Urlaub abgelehnt.'))
          .catch(err => alert(err.message || String(err)));
        return;
      }

      Swal.fire({
        title: 'Grund der Ablehnung',
        input: 'textarea',
        inputLabel: 'Bitte den Grund für die Ablehnung eintragen.',
        inputPlaceholder: 'Grund eingeben...',
        showCancelButton: true,
        confirmButtonText: 'Speichern',
        cancelButtonText: 'Abbrechen',
        preConfirm: (value) => {
          const reason = (value || '').trim();
          if (!reason) {
            Swal.showValidationMessage('Bitte einen Grund eingeben.');
            return false;
          }
          return postJSON('/my/mark-done', { id, type, action, reason })
            .catch(err => Swal.showValidationMessage(err.message || String(err)));
        }
      }).then((result) => {
        if (result.isConfirmed) reload('Urlaub abgelehnt.');
      });

      return;
    }

    postJSON('/my/mark-done', { id, type, action })
      .then((data) => {
        if (!data.success) throw new Error(data.message ?? 'Fehler beim Speichern.');
        let msg = 'Urlaubsantrag aktualisiert.';
        if (action === 'approve') msg = 'Urlaub genehmigt.';
        if (action === 'not_responsible') msg = 'Antrag aus deiner Liste entfernt.';
        reload(msg);
      })
      .catch(err => alert(err.message || String(err)));
  });

  document.addEventListener('change', (e) => {
    // sort select
    if (e.target && e.target.id === 'goalSortSelect') {
      currentSort = e.target.value || 'due_asc';
      renderGoalList();
      return;
    }

    // done checkbox
    if (!e.target.classList.contains('mark-done-checkbox')) return;

    const checkbox  = e.target;
    const container = checkbox.closest('.goal-item');
    if (!container) return;

    const id   = container.getAttribute('data-id');
    const type = container.getAttribute('data-type');
    if (!id || !type) return;

    if (type === 'appointment') {
      // appointment requires report
      if (!window.Swal) {
        const report = prompt('Bericht schreiben:');
        if (!report || !report.trim()) {
          checkbox.checked = false;
          return;
        }
        postJSON('/my/save-appointment-report', { id, report: report.trim() })
          .then(() => { showToast(); loadDueToday(); })
          .catch(err => { alert(err.message || String(err)); checkbox.checked = false; });
        return;
      }

      Swal.fire({
        title: 'Bericht schreiben',
        html: `<div id="quill-editor" style="height:200px;"></div>`,
        showCancelButton: true,
        confirmButtonText: 'Speichern',
        cancelButtonText: 'Abbrechen',
        focusConfirm: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          if (window.Quill) {
            Swal.__quillInstance = new Quill('#quill-editor', { theme: 'snow' });
          } else {
            const host = qs('#quill-editor');
            if (host) host.innerHTML = `<textarea id="fallback-report" style="width:100%;height:200px;"></textarea>`;
          }
        },
        preConfirm: async () => {
          try {
            let reportContent = '';
            if (window.Quill && Swal.__quillInstance) {
              reportContent = Swal.__quillInstance?.root?.innerHTML || '';
              if (!reportContent || reportContent === '<p><br></p>') {
                Swal.showValidationMessage('Bitte einen Bericht schreiben.');
                return false;
              }
            } else {
              reportContent = (qs('#fallback-report')?.value || '').trim();
              if (!reportContent) {
                Swal.showValidationMessage('Bitte einen Bericht schreiben.');
                return false;
              }
            }
            const data = await postJSON('/my/save-appointment-report', { id, report: reportContent });
            if (!data.success) throw new Error(data.message ?? 'Fehler beim Speichern.');
          } catch (err) {
            Swal.showValidationMessage(err.message || String(err));
            return false;
          }
        }
      }).then(result => {
        if (!result.isConfirmed) checkbox.checked = false;
        else { showToast(); loadDueToday(); }
      });

      return;
    }

    // other types
    postJSON('/my/mark-done', { id, type })
      .then((data) => {
        if (!data.success) throw new Error(data.message ?? 'Fehler beim Speichern.');
        showToast();
        loadDueToday();
      })
      .catch(err => {
        alert(err.message || String(err));
        checkbox.checked = false;
      });
  });

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.goal-filter-chip');
    if (!btn) return;
    currentFilter = btn.getAttribute('data-filter') || 'all';
    setActiveFilterChip();
    renderGoalList();
  });

  /* =========================
   * Boot
   * ========================= */
  ensureGoalStyles();
  loadDueToday();
  setInterval(loadDueToday, 10 * 60 * 1000);
})();
</script>


<script>
document.addEventListener("DOMContentLoaded", () => {
    const bar        = document.getElementById("breakingNewsBar");
    const textEl     = document.getElementById("breakingNewsText");
    const typeEl     = document.getElementById("breakingNewsType");
    const timeEl     = document.getElementById("breakingNewsTimeText");
    const prevBtn    = document.getElementById("bnPrev");
    const nextBtn    = document.getElementById("bnNext");
    const playBtn    = document.getElementById("bnPlayPause");
    const playIconEl = document.getElementById("bnPlayPauseIcon");

    const creatorImgEl  = document.getElementById("breakingNewsCreatorImage");
    const creatorNameEl = document.getElementById("breakingNewsCreatorName");
    const labelIconEl   = document.getElementById("bnMainIcon");
    const labelEl       = document.getElementById("bnLabel");

    // Audio UI
    const audioWrapper    = document.getElementById("bnAudioWrapper");
    const audioSeek       = document.getElementById("bnAudioSeek");
    const audioProgress   = document.getElementById("bnAudioProgress");
    const audioHandle     = document.getElementById("bnAudioHandle");
    const audioCurrentEl  = document.getElementById("bnAudioCurrent");
    const audioDurationEl = document.getElementById("bnAudioDuration");

    if (!bar || !textEl || !typeEl || !timeEl) return;

    let items      = [];
    let index      = 0;
    let mode       = "notifications"; // "breaking" or "notifications"
    let loopTimer  = null;
    const intervalMs = 14000;

    // Ticker rotation pause flag
    let tickerPaused = false;

    // Audio
    const audio = new Audio();
    let hasAudio = false;

    // ---------- Audio helpers ----------

    function formatTime(sec) {
        if (!isFinite(sec) || sec < 0) return "0:00";
        const m = Math.floor(sec / 60);
        const s = Math.floor(sec % 60);
        return m + ":" + String(s).padStart(2, "0");
    }

    function resetAudioUI() {
        if (audioProgress) audioProgress.style.width = "0%";
        if (audioHandle)   audioHandle.style.left = "0%";
        if (audioSeek)     audioSeek.value = 0;
        if (audioCurrentEl) audioCurrentEl.textContent = "0:00";
        if (audioDurationEl) audioDurationEl.textContent = "0:00";
    }

    function updateAudioUIFromCurrentTime() {
        if (!audio.duration) return;
        const pct = (audio.currentTime / audio.duration) * 100;
        if (audioProgress) audioProgress.style.width = pct + "%";
        if (audioHandle)   audioHandle.style.left = pct + "%";
        if (audioSeek)     audioSeek.value = pct;
        if (audioCurrentEl) audioCurrentEl.textContent = formatTime(audio.currentTime);
    }

    function syncPlayIcon() {
        if (!playIconEl) return;

        if (mode === "breaking" && hasAudio) {
            // Sound mode: button controls audio
            if (!audio.paused) {
                playIconEl.classList.remove("ri-play-mini-line");
                playIconEl.classList.add("ri-pause-mini-line");
            } else {
                playIconEl.classList.remove("ri-pause-mini-line");
                playIconEl.classList.add("ri-play-mini-line");
            }
        } else {
            // Text-only or notifications: button controls ticker pause
            if (tickerPaused) {
                playIconEl.classList.remove("ri-pause-mini-line");
                playIconEl.classList.add("ri-play-mini-line");
            } else {
                playIconEl.classList.remove("ri-play-mini-line");
                playIconEl.classList.add("ri-pause-mini-line");
            }
        }
    }

    function attachAudioEvents() {
        audio.addEventListener("loadedmetadata", () => {
            if (audioDurationEl) {
                audioDurationEl.textContent = formatTime(audio.duration);
            }
        });

        audio.addEventListener("timeupdate", () => {
            updateAudioUIFromCurrentTime();
        });

        audio.addEventListener("ended", () => {
            // when finished, unpause ticker and change icon
            tickerPaused = false;
            bar.classList.remove("is-paused");
            syncPlayIcon();
        });
    }
    attachAudioEvents();

    function tryAutoPlayAudio() {
        if (!hasAudio) return;
        audio
            .play()
            .then(() => {
                tickerPaused = true;
                bar.classList.add("is-paused");
                syncPlayIcon();
            })
            .catch(() => {
                // Autoplay blocked: keep ticker paused so user can start manually
                tickerPaused = true;
                bar.classList.add("is-paused");
                syncPlayIcon();
            });
    }

    // ---------- Label / icons ----------

    function updateLabelVisual(scope, type) {
        if (!labelIconEl || !labelEl) return;

        // reset label + icon classes
        labelIconEl.className = "sa-bn-label-icon";
        labelEl.className = "sa-bn-label";

        if (scope === "breaking") {
            // background color by type
            let labelClass = "sa-bn-label--breaking-info";
            let iconClass  = "ri-megaphone-fill";

            if (type === "warning") {
                labelClass = "sa-bn-label--breaking-warning";
                iconClass  = "ri-alert-line";
            } else if (type === "danger") {
                labelClass = "sa-bn-label--breaking-danger";
                iconClass  = "ri-alarm-warning-fill";
            } else if (type === "success") {
                labelClass = "sa-bn-label--breaking-success";
                iconClass  = "ri-checkbox-circle-fill";
            }

            labelEl.classList.add(labelClass);
            labelIconEl.classList.add(iconClass, "sa-bn-label-blink");
        } else {
            // Old style: red label, neutral icon
            labelIconEl.classList.add("ri-notification-3-fill");
        }
    }

    function getScopeIcon(scope, type) {
        if (scope === "breaking") {
            if (type === "warning") return "ri-alert-line";
            if (type === "danger")  return "ri-alarm-warning-fill";
            if (type === "success") return "ri-checkbox-circle-fill";
            return "ri-megaphone-fill";
        }

        if (scope === "customer") return "ri-user-3-line";
        if (scope === "employee") return "ri-user-settings-line";
        if (scope === "project")  return "ri-layout-4-line";
        if (scope === "ticket")   return "ri-ticket-line";
        if (scope === "task")     return "ri-checkbox-circle-line";

        const mapByType = {
            inquiry:     "ri-question-answer-line",
            lead:        "ri-user-star-line",
            offer:       "ri-file-list-3-line",
            appointment: "ri-calendar-event-line",
            demo:        "ri-notification-3-line",
        };
        return mapByType[type] || "ri-notification-3-line";
    }

    function getTypeLabel(type) {
        const labels = {
            inquiry:     "Anfrage",
            lead:        "Lead",
            offer:       "Angebot",
            appointment: "Termin",
            task:        "Aufgabe",
            project:     "Projekt",
            ticket:      "Ticket",
            employee:    "Mitarbeiter",
            info:        "Info",
            demo:        "System",
            warning:     "Warnung",
            danger:      "Alarm",
            success:     "Hinweis",
        };
        return labels[type] || "Info";
    }

    // ---------- Loop ----------

    function startLoop() {
        stopLoop();
        loopTimer = setInterval(() => {
            if (!tickerPaused) showNext();
        }, intervalMs);
    }

    function stopLoop() {
        if (loopTimer) {
            clearInterval(loopTimer);
            loopTimer = null;
        }
    }

    function showNext() {
        if (!items.length) return;
        index = (index + 1) % items.length;
        showCurrent();
    }

    function showPrev() {
        if (!items.length) return;
        index = (index - 1 + items.length) % items.length;
        showCurrent();
    }

    // ---------- Render one item ----------

    function showCurrent() {
        if (!items.length) return;

        const item = items[index];

        const title   = item.title   || "Benachrichtigung";
        const message = item.message || "";
        const type    = (item.type || "info").toLowerCase();
        const scope   = item.scope || (mode === "breaking" ? "breaking" : "generic");

        const inlineIconClass = getScopeIcon(scope, type);
        const typeLabel = getTypeLabel(type);
        const timeLabel = item.performed_at_human || item.performed_at || "";

        textEl.innerHTML = `
            <span style="display:inline-flex;align-items:center;gap:6px;">
                <i class="${inlineIconClass}" style="font-size:1.1rem;"></i>
                <span>${title}</span>
                <span style="opacity:0.6;">•</span>
                <span style="opacity:0.9;">${message}</span>
            </span>
        `;

        typeEl.textContent = typeLabel;
        timeEl.textContent = timeLabel;

        updateLabelVisual(scope, type);

        // Creator avatar only for breaking
        const creatorImage = item.creator_image_url || null;
        const creatorName  = item.creator_name || "";

        if (creatorImgEl) {
            if (creatorImage && mode === "breaking") {
                creatorImgEl.src = creatorImage;
                creatorImgEl.style.display = "block";
            } else {
                creatorImgEl.style.display = "none";
            }
        }
        if (creatorNameEl) {
            if (creatorName && mode === "breaking") {
                creatorNameEl.textContent = creatorName;
            } else {
                creatorNameEl.textContent = "";
            }
        }

        // AUDIO: only for breaking items that have audio_url
        const audioUrl = (mode === "breaking") ? (item.audio_url || null) : null;
        hasAudio = !!audioUrl;

        if (audioWrapper) {
            if (hasAudio) {
                audioWrapper.classList.remove("hidden");
                audio.pause();
                audio.src = audioUrl;
                audio.currentTime = 0;
                resetAudioUI();

                // When sound exists, auto-try to play and freeze ticker
                tryAutoPlayAudio();
            } else {
                audioWrapper.classList.add("hidden");
                audio.pause();
                resetAudioUI();
                // normal ticker mode
                tickerPaused = false;
                bar.classList.remove("is-paused");
            }
        }

        // Restart text scroll animation
        textEl.style.animation = "none";
        void textEl.offsetWidth;
        textEl.style.animation = null;

        syncPlayIcon();
    }

    // ---------- Data loading ----------

    function loadBreakingNews() {
        return fetch("{{ route('breaking-news.active') }}")
            .then(r => r.json())
            .then(data => {
                const list = data.breakingNews || [];
                if (!list.length) return false;

                mode  = "breaking";
                items = list;
                index = 0;

                bar.classList.remove("hidden");
                showCurrent();
                startLoop();
                return true;
            })
            .catch(err => {
                console.error("Fetch breaking news error:", err);
                return false;
            });
    }

    function loadNotifications() {
        return fetch("{{ route('dashboard.notifications.index') }}")
            .then(r => r.json())
            .then(data => {
                if (!data.notifications || !data.notifications.length) {
                    return false;
                }

                mode  = "notifications";
                items = data.notifications;
                index = 0;

                bar.classList.remove("hidden");
                tickerPaused = false;
                bar.classList.remove("is-paused");
                showCurrent();
                startLoop();
                return true;
            })
            .catch(err => {
                console.error("Fetch notifications error:", err);
                return false;
            });
    }

    // Init: try breaking first, then fallback to notifications
    loadBreakingNews()
        .then(hasBreaking => {
            if (!hasBreaking) {
                return loadNotifications();
            }
            return true;
        })
        .catch(err => {
            console.error("Init breaking/notifications error:", err);
            loadNotifications();
        });

    // ---------- Controls ----------

    if (prevBtn) {
        prevBtn.addEventListener("click", () => {
            audio.pause();
            tickerPaused = false;
            bar.classList.remove("is-paused");
            showPrev();
            syncPlayIcon();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener("click", () => {
            audio.pause();
            tickerPaused = false;
            bar.classList.remove("is-paused");
            showNext();
            syncPlayIcon();
        });
    }

    if (playBtn) {
        playBtn.addEventListener("click", () => {
            if (mode === "breaking" && hasAudio) {
                // Sound mode: toggle audio play/pause
                if (audio.paused) {
                    audio
                        .play()
                        .then(() => {
                            tickerPaused = true;
                            bar.classList.add("is-paused");
                            syncPlayIcon();
                        })
                        .catch(() => {
                            tickerPaused = true;
                            bar.classList.add("is-paused");
                            syncPlayIcon();
                        });
                } else {
                    audio.pause();
                    tickerPaused = false;
                    bar.classList.remove("is-paused");
                    syncPlayIcon();
                }
            } else {
                // Text-only ticker mode: pause/resume scroll + rotation
                tickerPaused = !tickerPaused;
                bar.classList.toggle("is-paused", tickerPaused);
                syncPlayIcon();
            }
        });
    }

    // Seek by moving range
    if (audioSeek) {
        audioSeek.addEventListener("input", (e) => {
            if (!audio.duration) return;
            const pct = parseFloat(e.target.value || "0");
            audio.currentTime = (pct / 100) * audio.duration;
            updateAudioUIFromCurrentTime();
        });
    }

    // ---------- Realtime (Echo) for notifications only ----------

    @if(Auth::check())
    if (window.Echo) {
        Echo.private("App.Models.User.{{ Auth::id() }}")
            .notification((notif) => {
                if (mode !== "notifications") return;

                const d = notif.data || notif;

                const scope = guessScopeFromPayload(d);

                const newItem = {
                    id:           notif.id || ("live-" + Date.now()),
                    type:         (d.type || "info").toLowerCase(),
                    scope:        scope,
                    title:        d.title || "Benachrichtigung",
                    message:      d.message || "",
                    performed_at: d.performed_at || new Date().toISOString(),
                    performed_at_human: "soeben",
                    is_read:      false,
                };

                items.unshift(newItem);
                index = 0;

                if (bar.classList.contains("hidden")) {
                    bar.classList.remove("hidden");
                }
                showCurrent();
            });
    }

    function guessScopeFromPayload(d) {
        if (!d) return "generic";
        if (d.customer_id || d.lead_id) return "customer";
        if (d.emp_id || d.employee_id) return "employee";
        if (d.project_id) return "project";
        if (d.ticket_id)  return "ticket";
        if (d.task_id)    return "task";
        return "generic";
    }
    @endif
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const openBtn   = document.getElementById('btn-open-holiday-modal');
    const modal     = document.getElementById('holiday-request-modal');
    const closeBtn  = document.getElementById('holiday-modal-close');
    const cancelBtn = document.getElementById('holiday-modal-cancel');
    const form      = document.getElementById('holiday-request-form');

    if (!openBtn || !modal || !form) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const openModal = () => {
        modal.classList.remove('holiday-modal-hidden');
    };

    const closeModal = () => {
        modal.classList.add('holiday-modal-hidden');
    };

    // Load overview (remaining days + history)
    const loadOverview = () => {
        fetch("{{ route('employee.leaves.overview') }}", {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: data.message || 'Übersicht konnte nicht geladen werden.',
                });
                return;
            }

            // Remaining days & year
            document.getElementById('holiday-remaining').textContent = data.employee.remaining_day;
            document.getElementById('holiday-year').textContent      = data.year;

            // History table
            const tbody = document.getElementById('holiday-history-body');
            tbody.innerHTML = '';

            if (!data.leaves || data.leaves.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="holiday-history-empty">
                            Keine Anträge in diesem Jahr.
                        </td>
                    </tr>
                `;
                document.getElementById('holiday-history-count').textContent = '0 Anträge';
            } else {
                document.getElementById('holiday-history-count').textContent =
                    data.leaves.length + ' Anträge';

                data.leaves.forEach((l) => {
                    const tr = document.createElement('tr');
                    const start = new Date(l.start_date);
                    const end   = new Date(l.end_date);

                    const startStr = isNaN(start) ? l.start_date : start.toLocaleDateString('de-DE');
                    const endStr   = isNaN(end)   ? l.end_date   : end.toLocaleDateString('de-DE');

                    // Klassenauswahl weiterhin über den Rohstatus
                    const rawStatus = (l.status || '').toLowerCase();

                    let statusClass = 'holiday-status-pill holiday-status-pending';
                    if (rawStatus === 'approved' || rawStatus === 'accept' || rawStatus === 'accepted') {
                        statusClass = 'holiday-status-pill holiday-status-approved';
                    } else if (rawStatus === 'rejected' || rawStatus === 'reject') {
                        statusClass = 'holiday-status-pill holiday-status-rejected';
                    }

                    const statusText = l.status_label || 'Ausstehend'; // deutscher Text

                    tr.className = 'holiday-history-row';
                    tr.innerHTML = `
                        <td>${startStr}</td>
                        <td>${endStr}</td>
                        <td>${l.duration ?? '-'}</td>
                        <td>
                            <span class="${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

            }

            // Reset form
            form.reset();

            // Set min date = today
            const today = new Date().toISOString().split('T')[0];
            form.querySelector('input[name="start_date"]').setAttribute('min', today);
            form.querySelector('input[name="end_date"]').setAttribute('min', today);

            openModal();
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Fehler',
                text: 'Die Übersicht konnte nicht geladen werden.',
            });
        });
    };

    openBtn.addEventListener('click', (e) => {
        e.preventDefault();
        loadOverview();
    });

    closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    // Close when clicking outside the card
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Submit form via AJAX
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        fetch("{{ route('employee.leaves.request') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(async (response) => {
            const data = await response.json();

            if (!response.ok || !data.success) {
                let msg = data.message || 'Der Urlaubsantrag konnte nicht gespeichert werden.';

                if (data.errors) {
                    const allErrors = Object.values(data.errors).flat();
                    if (allErrors.length) {
                        msg = allErrors.join('\n');
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: msg,
                });
                return;
            }

            Swal.fire({
                icon: 'success',
                title: 'Gespeichert',
                text: data.message || 'Dein Urlaubsantrag ist jetzt "Pending".',
                timer: 2500,
                showConfirmButton: false,
            });

            closeModal();

            // Optional: reload overview if you want to update something outside
            // loadOverview();
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Fehler',
                text: 'Es ist ein unerwarteter Fehler aufgetreten.',
            });
        });
    });
});
</script>

<script>
    let currentModalType = ''; // To store 'customers' or 'projects'
    let searchTimer = null;

    // 1. Open Modal
    function openMyModal(type) {
        currentModalType = type;
        const modal = document.getElementById('dynamicDataModal');
        const title = document.getElementById('modalTitle');
        const input = document.getElementById('modalSearchInput');

        // Reset UI
        document.getElementById('modalContent').innerHTML = `
            <div class="flex justify-center p-10">
                <svg class="animate-spin h-10 w-10 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>`;
        
        title.innerText = (type === 'customers') ? 'Meine Kunden' : 'Meine Projekte';
        input.value = ''; // Clear search
        modal.classList.add('open');

        // Load Data
        loadModalData(type, '');
    }

    // 2. Fetch Data Function
    function loadModalData(type, query) {
        const content = document.getElementById('modalContent');
        
        fetch(`{{ route('employee.my_data') }}?type=${type}&search=${query}`)
            .then(response => response.json())
            .then(data => {
                if(data.html) {
                    content.innerHTML = data.html;
                } else {
                    content.innerHTML = '<div class="text-center py-10 text-gray-400">Keine Daten gefunden.</div>';
                }
            })
            .catch(err => {
                console.error(err);
                content.innerHTML = '<div class="text-center py-10 text-red-500">Fehler beim Laden der Daten.</div>';
            });
    }

    // 3. Search Trigger (Debounced)
    function searchModalData() {
        const query = document.getElementById('modalSearchInput').value;
        
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            // Show mini loading indicator inside content if you want, or just update
            loadModalData(currentModalType, query);
        }, 400); // Wait 400ms after typing stops
    }

    // 4. Close Modal
    function closeMyModal() {
        document.getElementById('dynamicDataModal').classList.remove('open');
    }

    // Close on outside click
    document.getElementById('dynamicDataModal').addEventListener('click', function(e) {
        if (e.target === this) closeMyModal();
    });
</script>
@endsection
