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


    #goalList .goal-item-text .title{
      display:inline-block;
      font-size:.9rem;
      line-height:1.25rem;
      font-weight:600;
      color:#0f172a;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
      max-width:100%;
      text-decoration:none;
      cursor:pointer;
    }

    #goalList .goal-item-text .title:hover{
      text-decoration:underline;
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
/* Tabs + actions (scoped) */
#notesCard .notes-topbar{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:.75rem;
  padding:.75rem .75rem .25rem .75rem;
}

#notesCard .notes-tabs{
  display:inline-flex;
  background:#f1f5f9;
  border:1px solid #e2e8f0;
  border-radius:999px;
  padding:4px;
  gap:4px;
}

#notesCard .notes-tab{
  display:inline-flex;
  align-items:center;
  gap:.45rem;
  padding:.35rem .7rem;
  border-radius:999px;
  font-size:.78rem;
  font-weight:700;
  color:#475569;
  transition: background .12s ease, color .12s ease, box-shadow .12s ease;
  user-select:none;
}

#notesCard .notes-tab-ic{ width:16px; height:16px; }

#notesCard .notes-tab.is-active{
  background:#fff;
  color:#0f172a;
  box-shadow:0 6px 16px rgba(15,23,42,.10);
}

#notesCard .notes-actions{
  display:flex;
  align-items:center;
  gap:.5rem;
}

#notesCard .notes-icon-btn{
  width:34px; height:34px;
  border-radius:999px;
  border:1px solid #e2e8f0;
  background:#fff;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  transition: transform .12s ease, box-shadow .12s ease, background .12s ease, border-color .12s ease;
}
#notesCard .notes-icon-btn:hover{
  background:#f8fafc;
  border-color:#cbd5e1;
  transform: translateY(-1px);
  box-shadow:0 10px 22px rgba(15,23,42,.10);
}
#notesCard .notes-ic{ width:18px; height:18px; }

/* Filters spacing */
#notesCard .notes-filters{ padding: .25rem .75rem .5rem .75rem; }

/* Expanded-in-grid mode (NOT fullscreen). We just reflow your existing grid. */
.notes-grid--expanded #dueTodayCard{ display:none !important; }

/* Make notesColumn take the “main area” (full grid width) when expanded */
.notes-grid--expanded #notesColumn{ grid-column: 1 / -1 !important; }
.notes-grid--expanded #notesColumn{ order: 0; }
.notes-grid--expanded .notes-card{ min-height: 520px; }
</style>

 <style>
/* Calendar UI (scoped) */
#calendarPanel .ptcal-toolbar{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:.6rem;
  padding:.5rem .75rem .35rem .75rem;
}
#calendarPanel .ptcal-ic{ width:18px; height:18px; }

#calendarPanel .ptcal-emp{ position:relative; flex: 1 1 auto; max-width: 52%; }
#calendarPanel .ptcal-emp-btn{
  width:100%;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:.5rem;
  border:1px solid #e2e8f0;
  background:#fff;
  border-radius:999px;
  padding:.38rem .6rem;
  font-size:.78rem;
  font-weight:800;
  color:#0f172a;
  box-shadow:0 6px 16px rgba(15,23,42,.06);
}
#calendarPanel .ptcal-emp-btn-label{
  overflow:hidden;
  white-space:nowrap;
  text-overflow:ellipsis;
  max-width: calc(100% - 24px);
}
#calendarPanel .ptcal-emp-pop{
  position:absolute;
  top: calc(100% + 8px);
  left:0;
  right:0;
  z-index:30;
  background:#fff;
  border:1px solid #e2e8f0;
  border-radius:16px;
  box-shadow:0 18px 48px rgba(15,23,42,.14);
  overflow:hidden;
}
#calendarPanel .ptcal-emp-search{ padding:.6rem .6rem .35rem .6rem; border-bottom:1px solid #eef2f7; }
#calendarPanel .ptcal-emp-search-input{
  width:100%;
  border:1px solid #e2e8f0;
  background:#f8fafc;
  border-radius:999px;
  padding:.35rem .6rem;
  font-size:.78rem;
  outline:none;
}
#calendarPanel .ptcal-emp-list{ max-height: 220px; overflow:auto; padding:.35rem; }
#calendarPanel .ptcal-emp-item{
  display:flex;
  align-items:center;
  gap:.55rem;
  padding:.45rem .5rem;
  border-radius:12px;
  cursor:pointer;
  transition: background .12s ease;
  position:relative;
}
#calendarPanel .ptcal-emp-item:hover{ background:#f8fafc; }
#calendarPanel .ptcal-emp-item input{ width:16px; height:16px; }
#calendarPanel .ptcal-emp-avatar{
  width:26px; height:26px;
  border-radius:999px;
  object-fit:cover;
  border:1px solid #e2e8f0;
}
#calendarPanel .ptcal-emp-name{
  font-size:.8rem;
  font-weight:800;
  color:#0f172a;
  line-height:1.1;
}
#calendarPanel .ptcal-emp-color{
  margin-left:auto;
  width:10px; height:10px;
  border-radius:999px;
  border:1px solid rgba(15,23,42,.10);
}
#calendarPanel .ptcal-emp-empty{
  padding:.6rem;
  font-size:.8rem;
  color:#64748b;
  text-align:center;
}
#calendarPanel .ptcal-emp-actions{
  display:flex;
  gap:.5rem;
  padding:.5rem;
  border-top:1px solid #eef2f7;
}
#calendarPanel .ptcal-emp-clear,
#calendarPanel .ptcal-emp-apply{
  flex:1;
  border-radius:999px;
  padding:.38rem .6rem;
  font-size:.78rem;
  font-weight:900;
}
#calendarPanel .ptcal-emp-clear{
  border:1px solid #e2e8f0;
  background:#fff;
  color:#334155;
}
#calendarPanel .ptcal-emp-apply{
  border:1px solid #0ea5e9;
  background:#0ea5e9;
  color:#fff;
}

#calendarPanel .ptcal-month-nav{
  display:flex; align-items:center; gap:.35rem; flex: 0 0 auto;
}
#calendarPanel .ptcal-nav-btn{
  width:34px; height:34px;
  border-radius:999px;
  border:1px solid #e2e8f0;
  background:#fff;
  display:inline-flex;
  align-items:center;
  justify-content:center;
}
#calendarPanel .ptcal-month-title{
  min-width: 120px;
  text-align:center;
  font-weight:900;
  color:#0f172a;
  font-size:.85rem;
}

#calendarPanel .ptcal-wrap{ padding: .25rem .75rem .6rem .75rem; }
#calendarPanel .ptcal-weekdays{
  display:grid;
  grid-template-columns: repeat(7, 1fr);
  gap:.35rem;
  padding:.2rem .1rem .35rem .1rem;
  font-size:.72rem;
  font-weight:900;
  color:#64748b;
  text-align:center;
}
#calendarPanel .ptcal-grid{
  display:grid;
  grid-template-columns: repeat(7, 1fr);
  gap:.35rem;
}

/* cells */
#calendarPanel .ptcal-cell{
  position:relative;
  border:1px solid #e2e8f0;
  background:#fff;
  border-radius:14px;
  height:44px;
  padding:.35rem .35rem;
  cursor:pointer;
  transition: transform .10s ease, box-shadow .10s ease, border-color .10s ease, background .10s ease;
  z-index:1;
  overflow:visible;
}
#calendarPanel .ptcal-cell:hover{
  transform: translateY(-1px);
  box-shadow:0 14px 30px rgba(15,23,42,.10);
  border-color:#cbd5e1;
}
#calendarPanel .ptcal-cell.is-out{ background:#f8fafc; color:#94a3b8; }
#calendarPanel .ptcal-cell.is-today{ border-color:#0ea5e9; }
#calendarPanel .ptcal-cell.is-selected{ border-color:#0f172a; box-shadow:0 14px 30px rgba(15,23,42,.12); }

/* number + halo behind */
#calendarPanel .ptcal-daynum{
  position:relative;
  z-index:2;
  font-size:.82rem;
  font-weight:1000;
  color:#0f172a;
}
#calendarPanel .ptcal-cell.is-out .ptcal-daynum{ color:#94a3b8; }

#calendarPanel .ptcal-halo{
  position:absolute;
  left:50%;
  top:12px;
  transform:translateX(-50%);
  width:30px;
  height:30px;
  border-radius:999px;
  background: var(--ptcal-halo-bg, transparent);
  opacity:.28;
  pointer-events:none;
}
#calendarPanel .ptcal-halo::after{
  content:"";
  position:absolute;
  inset:0;
  border-radius:999px;
  border:1px solid rgba(15,23,42,.08);
}

/* remove old dot completely if still present somewhere */
#calendarPanel .ptcal-dot{ display:none !important; }

#calendarPanel .ptcal-day{
  border-top:1px solid #eef2f7;
  padding:.65rem .75rem .75rem .75rem;
}
#calendarPanel .ptcal-day-head{
  display:flex;
  align-items:baseline;
  justify-content:space-between;
  gap:.5rem;
  margin-bottom:.35rem;
}
#calendarPanel .ptcal-day-title{
  font-weight:1000;
  color:#0f172a;
  font-size:.9rem;
}
#calendarPanel .ptcal-day-meta{
  font-size:.75rem;
  font-weight:800;
  color:#64748b;
}
#calendarPanel .ptcal-day-list{
  display:flex;
  flex-direction:column;
  gap:.45rem;
  max-height: 220px;
  overflow:auto;
  padding-right:.25rem;
}
#calendarPanel .ptcal-event{
  border:1px solid #e2e8f0;
  background:#fff;
  border-radius:14px;
  padding:.55rem .6rem;
  display:flex;
  gap:.6rem;
}
#calendarPanel .ptcal-event-bar{
  width:6px;
  border-radius:999px;
  flex: 0 0 auto;
}
#calendarPanel .ptcal-event-title{
  font-weight:1000;
  color:#0f172a;
  font-size:.82rem;
  line-height:1.1;
}
#calendarPanel .ptcal-event-sub{
  font-size:.75rem;
  color:#475569;
  margin-top:.15rem;
}
#calendarPanel .ptcal-muted{
  font-size:.82rem;
  color:#64748b;
  padding:.4rem 0;
}

/* helper */
#calendarPanel .hidden{ display:none !important; }

/* --- FIX: tooltip behind cells --- */

/* grid becomes stacking context */
#calendarPanel #ptcalGrid{
  position: relative;
  overflow: visible;
  isolation: isolate;
}

/* bring hovered cell above neighbors */
#calendarPanel .ptcal-cell:hover,
#calendarPanel .ptcal-cell:focus-visible{
  z-index: 1000;
}

/* tooltip always above */
#calendarPanel .ptcal-daytip{
  z-index: 1100 !important;
}
</style>
<style>
/* =========================================================
   PT MODAL (CUSTOM, RESPONSIVE)
   ========================================================= */
.pt-modal-overlay{
  position:fixed; inset:0;
  background:rgba(15,23,42,.45);
  backdrop-filter: blur(6px);
  z-index:9999;
  display:none;
  padding:18px;
  align-items:center;
  justify-content:center;
}
.pt-modal-overlay.is-open{ display:flex; }

.pt-modal{
  width:min(640px, 100%);
  max-height:min(88vh, 820px);
  background:#fff;
  border-radius:20px;
  box-shadow:0 30px 80px rgba(2,6,23,.35);
  overflow:hidden;
  display:flex;
  flex-direction:column;
  transform: translateY(6px);
  animation: ptModalIn .16s ease-out forwards;
}
@keyframes ptModalIn{ to { transform: translateY(0); } }

.pt-modal__header{
  padding:14px 16px;
  background:#f8fafc;
  border-bottom:1px solid #e2e8f0;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
}
.pt-modal__title{
  font-weight:800;
  color:#0f172a;
  font-size:15px;
}
.pt-modal__close{
  width:38px; height:38px;
  border-radius:12px;
  border:1px solid #e2e8f0;
  background:#fff;
  display:flex; align-items:center; justify-content:center;
  cursor:pointer;
}
.pt-modal__close:hover{ background:#f1f5f9; }

.pt-modal__body{
  padding:14px 16px;
  overflow:auto;
}
.pt-modal__footer{
  padding:14px 16px;
  border-top:1px solid #e2e8f0;
  background:#fff;
}

.pt-form-grid{ display:grid; grid-template-columns:1fr; gap:12px; }
.pt-row-2{ display:grid; grid-template-columns:1fr 1fr; gap:12px; }

.pt-label{
  font-size:12px;
  font-weight:800;
  color:#64748b;
  margin-bottom:6px;
  display:block;
}
.pt-input, .pt-textarea, .pt-select{
  width:100%;
  border:1px solid #e2e8f0;
  border-radius:14px;
  padding:12px 12px;
  font-size:14px;
  color:#0f172a;
  outline:none;
  background:#fff;
}
.pt-input:focus, .pt-textarea:focus, .pt-select:focus{
  border-color:#94a3b8;
  box-shadow:0 0 0 4px rgba(148,163,184,.25);
}
.pt-textarea{ min-height:92px; resize:vertical; }

.pt-colors{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.pt-color{
  width:34px; height:34px;
  border-radius:999px;
  border:2px solid rgba(255,255,255,.9);
  box-shadow:0 10px 20px rgba(2,6,23,.10);
  cursor:pointer;
  position:relative;
  transform: translateY(0);
  transition: transform .12s ease, box-shadow .12s ease;
}
.pt-color:hover{ transform: translateY(-1px); box-shadow:0 16px 34px rgba(2,6,23,.14); }
.pt-color.is-active::after{
  content:"";
  position:absolute; inset:0;
  border-radius:999px;
  box-shadow: inset 0 0 0 2px rgba(15,23,42,.75);
}

.pt-actions{ display:flex; gap:10px; }
.pt-btn{
  width:100%;
  border:none;
  border-radius:14px;
  padding:12px 14px;
  font-weight:900;
  cursor:pointer;
}
.pt-btn-primary{
  background:#0b2a5b; /* brand-dark fallback */
  color:#fff;
  box-shadow:0 16px 40px rgba(2,6,23,.20);
}
.pt-btn-primary:disabled{ opacity:.6; cursor:not-allowed; }
.pt-btn-ghost{
  background:#f1f5f9;
  color:#0f172a;
}

/* Mobile bottom-sheet behavior */
@media (max-width: 640px){
  .pt-modal-overlay{
    padding:0;
    align-items:flex-end;
  }
  .pt-modal{
    width:100%;
    max-height:92vh;
    border-radius:18px 18px 0 0;
  }
  .pt-row-2{ grid-template-columns:1fr; }
}

/* =========================================================
   SELECT2 (MULTI) - make it match
   ========================================================= */
.select2-container{ width:100% !important; }
.select2-container--default .select2-selection--multiple{
  min-height:46px;
  border:1px solid #e2e8f0 !important;
  border-radius:14px !important;
  padding:6px 10px !important;
}
.select2-container--default.select2-container--focus .select2-selection--multiple{
  border-color:#94a3b8 !important;
  box-shadow:0 0 0 4px rgba(148,163,184,.25) !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice{
  border:none !important;
  border-radius:999px !important;
  padding:4px 10px !important;
  background:#0f172a !important;
  color:#fff !important;
  font-weight:800;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
  color:#fff !important;
  margin-right:6px !important;
}
.select2-dropdown{
  border:1px solid #e2e8f0 !important;
  border-radius:14px !important;
  box-shadow:0 18px 50px rgba(2,6,23,.18) !important;
  overflow:hidden;
  z-index:10000; /* above overlay */
}
.select2-results__option{ padding:10px 12px !important; }
.app-content .content {
    margin-left: 5px !important;
}
</style>
<style>
    .lazy-toggle-btn{
        width:100%;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1rem;
        padding:14px 18px;
        border:none;
        border-radius:18px;
        background:linear-gradient(135deg, var(--accent-blue), var(--accent-green));
        color:#fff;
        font-weight:700;
        font-size:.92rem;
        box-shadow:0 12px 30px rgba(15,23,42,.10);
        cursor:pointer;
        transition:all .2s ease;
    }

    .lazy-toggle-btn:hover{
        transform:translateY(-1px);
        box-shadow:0 16px 36px rgba(15,23,42,.14);
    }

    .lazy-toggle-btn__left,
    .lazy-toggle-btn__right{
        display:inline-flex;
        align-items:center;
        gap:.6rem;
    }

    .lazy-toggle-btn__left i,
    .lazy-toggle-btn__right i{
        font-size:1rem;
    }

    .lazy-toggle-status{
        font-size:.78rem;
        font-weight:600;
        opacity:.95;
    }

    .lazy-toggle-arrow{
        transition:transform .2s ease;
    }

    .lazy-toggle-btn.is-open .lazy-toggle-arrow{
        transform:rotate(180deg);
    }

    .lazy-toggle-panel{
        margin-top:12px;
        border-radius:18px;
        overflow:hidden;
    }

    .lazy-toggle-panel.hidden{
        display:none;
    }

    .lazy-toggle-loader{
        background:#fff;
        border:1px solid #e2e8f0;
        border-radius:18px;
        padding:18px;
        text-align:center;
        color:#64748b;
        font-size:.85rem;
    }
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
                                    /* Header Container */
                                    .dashboard-header{
                                        background:#fff !important;
                                        border-radius:16px !important;
                                        box-shadow:0 2px 10px rgba(0,0,0,0.03) !important;
                                        border:1px solid #e2e8f0 !important;
                                        margin-bottom:1.5rem !important;
                                        position:relative;
                                    }

                                    /* ONE ROW ALWAYS */
                                    .dashboard-header-row{
                                        display:flex !important;
                                        align-items:center !important;
                                        justify-content:space-between !important;
                                        gap:14px !important;
                                        flex-wrap:nowrap !important;
                                        overflow-x:auto !important;
                                        -webkit-overflow-scrolling:touch;
                                    }
                                    .dashboard-header-row::-webkit-scrollbar{ height:6px; }
                                    .dashboard-header-row::-webkit-scrollbar-thumb{ background:#e2e8f0; border-radius:999px; }

                                    .dash-left{
                                        display:flex !important;
                                        align-items:center !important;
                                        gap:12px !important;
                                        flex:0 0 auto !important;
                                        min-width:260px;
                                        padding-right:6px;
                                    }

                                    .dash-middle{
                                        flex:1 1 auto !important;
                                        min-width:320px;
                                    }

                                    .dash-right{
                                        flex:0 0 auto !important;
                                        min-width:max-content;
                                    }

                                    .dash-stats{
                                        border-top:1px solid #e2e8f0 !important;
                                        margin-top:12px !important;
                                        padding-top:10px !important;
                                    }

                                    /* --- LCD TIMER SCREEN STYLE --- */
                                    .lcd-container{
                                        background:#f1f5f9 !important;
                                        border:2px inset #e2e8f0 !important;
                                        border-radius:12px !important;
                                        padding:6px 12px !important;
                                        display:flex !important;
                                        align-items:center !important;
                                        justify-content:space-between !important;
                                        gap:12px !important;
                                        box-shadow:inset 0 2px 4px rgba(0,0,0,0.03) !important;
                                        width:100% !important;
                                    }

                                    /* --- BUTTONS ROW --- */
                                    .btn-row{
                                        display:flex !important;
                                        align-items:center !important;
                                        gap:10px !important;
                                        flex-wrap:nowrap !important;
                                        justify-content:flex-end !important;
                                    }

                                    .kbd-btn,
                                    a.kbd-btn,
                                    button.kbd-btn{
                                        position:relative !important;
                                        display:inline-flex !important;
                                        align-items:center !important;
                                        justify-content:center !important;
                                        gap:8px !important;
                                        background:#ffffff !important;

                                        border:1px solid #cbd5e1 !important;
                                        border-bottom:4px solid #cbd5e1 !important;
                                        border-radius:10px !important;

                                        padding:8px 16px !important;
                                        color:#475569 !important;
                                        font-size:0.8rem !important;
                                        font-weight:600 !important;
                                        transition:all 0.1s ease-out !important;
                                        user-select:none !important;
                                        cursor:pointer !important;

                                        height:42px !important;
                                        width:auto !important;
                                        min-width:120px;
                                        text-decoration:none !important;
                                        white-space:nowrap !important;
                                        line-height:1 !important;
                                    }

                                    .kbd-btn--icon{
                                        width:42px !important;
                                        min-width:42px !important;
                                        padding:0 !important;
                                    }

                                    .kbd-btn:hover,
                                    a.kbd-btn:hover,
                                    button.kbd-btn:hover{
                                        background:#f8fafc !important;
                                        transform:translateY(-1px) !important;
                                    }

                                    .kbd-btn:active,
                                    a.kbd-btn:active,
                                    button.kbd-btn:active{
                                        border-bottom-width:0px !important;
                                        margin-top:4px !important;
                                        transform:none !important;
                                        box-shadow:inset 0 2px 4px rgba(0,0,0,0.05) !important;
                                    }

                                    .kbd-icon{ font-size:1.1rem !important; transition:transform 0.2s !important; }
                                    .kbd-btn:hover .kbd-icon{ transform:scale(1.1) !important; }

                                    .kbd-badge{
                                        position:absolute !important;
                                        top:-4px !important;
                                        right:-4px !important;
                                        background:#ef4444 !important;
                                        color:#fff !important;
                                        font-size:9px !important;
                                        font-weight:700 !important;
                                        height:16px !important;
                                        min-width:16px !important;
                                        border-radius:4px !important;
                                        display:flex !important;
                                        align-items:center !important;
                                        justify-content:center !important;
                                        padding:0 4px !important;
                                        border:2px solid #fff !important;
                                        z-index:10 !important;
                                    }

                                    /* --------- MOBILE RESPONSIVE FIXES --------- */
                                    /* Keep one row (scrollable), but reduce minimum widths + make timer readable */
                                    @media (max-width: 640px){
                                        .dash-left{ min-width:210px !important; }
                                        .dash-middle{ min-width:250px !important; }

                                        .lcd-container{
                                        padding:6px 10px !important;
                                        gap:10px !important;
                                        }

                                        /* timer text: smaller + less spacing so it doesn't look broken on mobile */
                                        #timerDisplay{
                                        font-size: clamp(18px, 6vw, 26px) !important;
                                        letter-spacing: .10em !important;
                                        }

                                        #timerStatus{
                                        font-size: 9px !important;
                                        padding: 2px 6px !important;
                                        }

                                        #currentTaskInput{
                                        font-size: 11px !important;
                                        margin-top: 4px !important;
                                        }

                                        .kbd-btn{
                                        min-width:104px !important;
                                        padding:8px 12px !important;
                                        font-size:.75rem !important;
                                        }
                                    }
                                </style>

                                <style>
                                    /* menu button in header top-right */
                                    .dash-header-menu{
                                        position:absolute !important;
                                        top:13px !important;   /* mobile default */
                                        right:12px !important;
                                        z-index:2 !important;
                                    }

                                    /* keep your previous desktop placement */
                                    @media (min-width: 1024px){
                                        .dash-header-menu{
                                        top:13px !important;
                                        right:12px !important;
                                        }
                                    }

                                    /* panel is fixed (set by JS) so it never gets clipped by any overflow */
                                    .hmenu{
                                        position:fixed !important;
                                        width:270px !important;
                                        background:#fff !important;
                                        border:1px solid #e2e8f0 !important;
                                        border-radius:14px !important;
                                        box-shadow:0 18px 40px rgba(15,23,42,.12) !important;
                                        padding:8px !important;
                                        z-index:99999 !important;
                                    }

                                    .hmenu-item{
                                        width:100% !important;
                                        display:flex !important;
                                        align-items:center !important;
                                        justify-content:space-between !important;
                                        gap:10px !important;
                                        padding:10px 10px !important;
                                        border-radius:12px !important;
                                        color:#0f172a !important;
                                        font-weight:700 !important;
                                        font-size:13px !important;
                                        background:transparent !important;
                                        text-decoration:none !important;
                                        border:1px solid transparent !important;
                                        cursor:pointer !important;
                                    }
                                    .hmenu-item:hover{
                                        background:#f8fafc !important;
                                        border-color:#e2e8f0 !important;
                                    }
                                    .hmenu-left{ display:flex !important; align-items:center !important; gap:10px !important; min-width:0; }
                                    .hmenu-text{ white-space:nowrap !important; overflow:hidden !important; text-overflow:ellipsis !important; }
                                    .hmenu-right{ font-size:11px !important; font-weight:900 !important; color:#64748b !important; padding:2px 8px !important; border:1px solid #e2e8f0 !important; border-radius:999px !important; background:#fff !important; }
                                    .hmenu-ico{ font-size:18px !important; color:#475569 !important; }
                                    .hmenu-sep{ height:1px !important; background:#e2e8f0 !important; margin:6px 0 !important; }

                                    /* theme icon swap */
                                    [data-theme="dark"] .hmenu-ico--sun{ display:none !important; }
                                    [data-theme="dark"] .hmenu-ico--moon{ display:inline-block !important; }
                                    [data-theme="light"] .hmenu-ico--sun{ display:inline-block !important; }
                                    [data-theme="light"] .hmenu-ico--moon{ display:none !important; }
                                </style>

                                <style>
                                    /* MOBILE: stack (no scroll), profile centered, timer under it */
                                    @media (max-width: 640px){
                                    .dashboard-header-row{
                                        flex-direction: column !important;
                                        align-items: stretch !important;
                                        justify-content: flex-start !important;
                                        flex-wrap: nowrap !important;
                                        overflow-x: visible !important;
                                        gap: 10px !important;
                                    }

                                    .dash-left,
                                    .dash-middle,
                                    .dash-right{
                                        min-width: 0 !important;
                                        width: 100% !important;
                                        flex: 0 0 auto !important;
                                        padding-right: 0 !important;
                                    }

                                    /* center profile area */
                                    .dash-left{
                                        justify-content: left !important;
                                        text-align: center !important;
                                        padding-top: 8px !important;
                                        padding-bottom: 2px !important;
                                    }

                                    .dash-left .min-w-0{
                                        text-align: center !important;
                                    }

                                    /* keep timer nice on mobile */
                                    .lcd-container{
                                        width: 100% !important;
                                        padding: 8px 10px !important;
                                    }

                                    #timerDisplay{
                                        font-size: clamp(18px, 7vw, 28px) !important;
                                        letter-spacing: .08em !important;
                                    }

                                    #currentTaskInput{
                                        font-size: 12px !important;
                                    }

                                    /* optional: give the stats a bit more air */
                                    .dash-stats{
                                        margin-top: 10px !important;
                                        padding-top: 10px !important;
                                    }
                                    }
                                </style>

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

                     
                        <header class="dashboard-header p-2 relative">
                        {{-- TOP-RIGHT MENU --}}
                        <div class="dash-header-menu" data-hmenu>
                            <button type="button" class="kbd-btn kbd-btn--icon" title="Menü" data-hmenu-btn>
                            <i class="ri-settings-3-line kbd-icon"></i>
                            </button>

                            <div class="hmenu hidden" data-hmenu-panel>
                            {{-- Theme toggle --}}
                            <button type="button" class="hmenu-item" data-theme-toggle>
                                <span class="hmenu-left">
                                <i class="ri-sun-line hmenu-ico hmenu-ico--sun"></i>
                                <i class="ri-moon-line hmenu-ico hmenu-ico--moon"></i>
                                <span class="hmenu-text">Theme</span>
                                </span>
                                <span class="hmenu-right" data-theme-label>Hell</span>
                            </button>

                            <div class="hmenu-sep"></div>

                            {{-- Urlaub --}}
                            <a href="{{ url('employee_notifications/'.auth()->user()->name) }}" class="hmenu-item">
                                <span class="hmenu-left">
                                <i class="ri-calendar-event-line hmenu-ico"></i>
                                <span class="hmenu-text">Urlaub Übersicht</span>
                                </span>
                                <i class="ri-arrow-right-s-line"></i>
                            </a>

                            <button type="button" class="hmenu-item" id="btn-open-holiday-modal-menu">
                                <span class="hmenu-left">
                                <i class="ri-flight-takeoff-line hmenu-ico"></i>
                                <span class="hmenu-text">Urlaub beantragen</span>
                                </span>
                                <i class="ri-arrow-right-s-line"></i>
                            </button>

                            <div class="hmenu-sep"></div>

                            {{-- Kunden / Projekte / Bericht (added to menu) --}}
                            <button type="button" class="hmenu-item" onclick="openMyModal('customers')">
                                <span class="hmenu-left">
                                <i class="ri-group-line hmenu-ico"></i>
                                <span class="hmenu-text">Kunden</span>
                                </span>
                                <span class="hmenu-right">{{ (int)($myCustomerCount ?? 0) }}</span>
                            </button>

                            <button type="button" class="hmenu-item" onclick="openMyModal('projects')">
                                <span class="hmenu-left">
                                <i class="ri-briefcase-line hmenu-ico"></i>
                                <span class="hmenu-text">Projekte</span>
                                </span>
                                <span class="hmenu-right">{{ (int)($myProjectCount ?? 0) }}</span>
                            </button>

                            <a href="{{ url('employee_daily_plan') }}" class="hmenu-item">
                                <span class="hmenu-left">
                                <i class="ri-file-list-3-line hmenu-ico"></i>
                                <span class="hmenu-text">Bericht</span>
                                </span>
                                <i class="ri-arrow-right-s-line"></i>
                            </a>
                            </div>
                        </div>

                        {{-- ONE ROW --}}
                        <div class="dashboard-header-row">
                            {{-- LEFT --}}
                            <div class="dash-left">
                            <div class="relative shrink-0">
                                <img src="{{ $image_path }}" alt="Profilbild"
                                class="w-14 h-14 rounded-xl object-cover border-2 border-white shadow-sm ring-1 ring-slate-200">
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Willkommen</p>
                                <h1 class="text-lg font-bold text-slate-800 leading-tight truncate">{{ $full_name }}</h1>
                            </div>
                            </div>
 
 
                        </div>

                        {{-- BELOW: STATS ROW --}}
                        <div class="dash-stats">
                            <div class="flex flex-wrap justify-center sm:justify-start gap-x-4 gap-y-2">
                            <div class="flex items-center gap-1.5" title="Urlaubstage: Genutzt / Gesamt">
                                <i class="ri-suitcase-2-line text-emerald-500"></i>
                                <span>Urlaub: <span class="text-slate-900 font-bold">{{ $vacationDaysUsed ?? 0 }}/{{ $annualLeaveTotal ?? 0 }}</span></span>
                            </div>
                            <div class="flex items-center gap-1.5" title="Verbleibender Urlaub">
                                <i class="ri-leaf-line text-emerald-500"></i>
                                <span>Rest: <span class="text-slate-900 font-bold">{{ $vacationDaysRemain ?? 0 }}</span></span>
                            </div>
                            <div class="flex items-center gap-1.5" title="Krankheitstage">
                                <i class="ri-stethoscope-line text-rose-500"></i>
                                <span>Krank: <span class="text-slate-900 font-bold">{{ $sickDays ?? 0 }}</span></span>
                            </div>
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
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-2 md:gap-5" id="plannerGrid">
                        {{-- LEFT: KPI + TABS --}}
                        <div  id="focusColumn"  class="space-y-4 h-full lg:col-span-3">
                            {{-- KPI / Today Card --}}
                            <section id="dueTodayCard" class="kpi-card h-full ">
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

                           
                        </div>  
                            <aside id="notesColumn" class="space-y-4 h-full lg:col-span-2">
                                <section id="notesCard" class="notes-card h-full flex flex-col relative">

                                    <!-- Tabs + actions -->
                                    <div class="notes-topbar">
                                    <div class="notes-tabs" role="tablist" aria-label="Notizen / Kalender">
                                        <button type="button" class="notes-tab is-active" role="tab"
                                                data-notes-tab="notes" aria-selected="true">
                                        <!-- sticky-note svg -->
                                        <svg viewBox="0 0 24 24" class="notes-tab-ic" aria-hidden="true">
                                            <path d="M4 3h16v14l-4 4H4z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            <path d="M20 17h-4v4" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                        </svg>
                                        <span>Notizen</span>
                                        </button>

                                        <button type="button" class="notes-tab" role="tab"
                                                data-notes-tab="calendar" aria-selected="false">
                                        <!-- calendar svg -->
                                        <svg viewBox="0 0 24 24" class="notes-tab-ic" aria-hidden="true">
                                            <path d="M7 3v3M17 3v3M4 7h16M5 5h14a1 1 0 0 1 1 1v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a1 1 0 0 1 1-1z"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span>Kalender</span>
                                        </button>
                                    </div>

                                    <div class="notes-actions">
                                        <!-- Expand (toggles into focus-area / grid mode, NOT fullscreen) -->
                                        <button id="notesExpandBtn" type="button" class="notes-icon-btn" title="Erweitern" aria-pressed="false">
                                        <!-- maximize svg -->
                                        <svg viewBox="0 0 24 24" class="notes-ic" aria-hidden="true">
                                            <path d="M8 3H3v5M16 3h5v5M8 21H3v-5M16 21h5v-5"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        </button>

                                        <!-- Add note -->
                                      <!-- 2) Change ONLY the PLUS button in your notes topbar -->
                                            <button id="notesPlusBtn" type="button"
                                                    onclick="handleNotesPlus()"
                                                    class="btn btn-icon btn-icon rounded-circle btn-primary waves-effect waves-light">
                                            <i class="feather icon-plus"></i>
                                            </button>

                                    </div>
                                    </div>

                                    <!-- Filters (only for Notizen tab) -->
                                    <div id="notesFilters" class="notes-filters">
                                    <div class="flex items-center gap-2 w-full">
                                        <select id="noteCategoryFilter" onchange="fetchPersonalNotes()"
                                                class="form-select text-xs rounded-full border-slate-200 py-1 px-2 bg-slate-50 flex-1">
                                        <option value="all">Alle Kategorien</option>
                                        </select>

                                        <button type="button" onclick="openCategoryModal()" title="Kategorie hinzufügen"
                                                class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-full w-6 h-6 flex items-center justify-center transition">
                                        <i class="ri-add-line"></i>
                                        </button>
                                    </div>

                                    <input type="text" id="noteSearch" placeholder="Suchen..." onkeyup="debounceNoteSearch()"
                                            class="w-full text-xs rounded-full border border-slate-200 bg-slate-50 px-3 py-1 mt-2 focus:outline-none focus:border-blue-400">
                                    </div>

                                    <!-- Body -->
                                    <div class="notes-body flex-1 overflow-hidden relative">
                                        <!-- NOTES PANEL -->
                                        <div id="notesPanel" role="tabpanel">
                                            <ul id="personal-note-list" class="notes-scroll h-full pb-10">
                                            <li class="text-center text-xs text-gray-400 mt-4">Laden...</li>
                                            </ul>
                                        </div>

                                            <!-- CALENDAR PANEL (empty for now) -->
                                        <div id="calendarPanel" role="tabpanel" class="hidden h-full">
                                            <!-- Employees multi-select -->
                                            <div class="ptcal-toolbar">
                                                <div class="ptcal-emp" id="ptcalEmpSelect">
                                                <button type="button" class="ptcal-emp-btn" aria-haspopup="listbox" aria-expanded="false">
                                                    <span class="ptcal-emp-btn-label">Mitarbeiter wählen</span>
                                                    <svg viewBox="0 0 24 24" class="ptcal-ic" aria-hidden="true">
                                                    <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>

                                                <div class="ptcal-emp-pop hidden" role="listbox" aria-multiselectable="true">
                                                    <div class="ptcal-emp-search">
                                                    <input id="ptcalEmpSearch" type="text" placeholder="Suchen..." class="ptcal-emp-search-input">
                                                    </div>
                                                    <div id="ptcalEmpList" class="ptcal-emp-list">
                                                    <!-- filled by JS -->
                                                    <div class="ptcal-emp-empty">Keine Mitarbeiter geladen</div>
                                                    </div>
                                                    <div class="ptcal-emp-actions">
                                                    <button type="button" class="ptcal-emp-clear" id="ptcalEmpClear">Zurücksetzen</button>
                                                    <button type="button" class="ptcal-emp-apply" id="ptcalEmpApply">Anwenden</button>
                                                    </div>
                                                </div>
                                                </div>

                                                <div class="ptcal-month-nav">
                                                <button type="button" class="ptcal-nav-btn" id="ptcalPrev" title="Zurück">
                                                    <svg viewBox="0 0 24 24" class="ptcal-ic" aria-hidden="true">
                                                    <path d="M15 18l-6-6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <div class="ptcal-month-title" id="ptcalMonthTitle">–</div>
                                                <button type="button" class="ptcal-nav-btn" id="ptcalNext" title="Weiter">
                                                    <svg viewBox="0 0 24 24" class="ptcal-ic" aria-hidden="true">
                                                    <path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                </div>
                                            </div>

                                            <!-- Calendar grid -->
                                            <div class="ptcal-wrap">
                                                <div class="ptcal-weekdays">
                                                <div>Mo</div><div>Di</div><div>Mi</div><div>Do</div><div>Fr</div><div>Sa</div><div>So</div>
                                                </div>
                                                <div id="ptcalGrid" class="ptcal-grid"></div>
                                            </div>

                                            <!-- Day events -->
                                            <div class="ptcal-day">
                                                <div class="ptcal-day-head">
                                                <div class="ptcal-day-title" id="ptcalDayTitle">–</div>
                                                <div class="ptcal-day-meta" id="ptcalDayMeta"></div>
                                                </div>
                                                <div id="ptcalDayList" class="ptcal-day-list">
                                                <div class="ptcal-muted">Datum wählen…</div>
                                                </div>
                                            </div>
                                            </div>
                                    </div>

                                </section>
                            </aside>

                        <div id="noteModal" class="custom-modal-overlay">
                            <div class="custom-modal-container" style="max-width: 500px; height: auto; max-height: 90vh;">
                                <div class="custom-modal-header">
                                    <h3 class="custom-modal-title" id="noteModalTitle">Neue Notiz</h3>
                                    <button class="custom-modal-close" onclick="closeNoteModal()">&times;</button>
                                </div>
                                <div class="custom-modal-body">
                                    <form id="noteForm" onsubmit="submitNoteForm(event)">
                                        <input type="hidden" name="note_id" id="noteId">
                                        
                                        <div class="flex flex-col gap-4">
                                            <div>
                                                <label class="text-xs font-bold text-slate-600 uppercase">Titel</label>
                                                <input type="text" name="title" id="noteTitle" required class="w-full rounded-lg border-slate-200 text-sm p-2 mt-1">
                                            </div>

                                            <div>
                                                <label class="text-xs font-bold text-slate-600 uppercase">Inhalt</label>
                                                <textarea name="note" id="noteBody" rows="3" class="w-full rounded-lg border-slate-200 text-sm p-2 mt-1"></textarea>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="text-xs font-bold text-slate-600 uppercase">Kategorie</label>
                                                    <select name="category_id" id="noteCategorySelect" required class="w-full rounded-lg border-slate-200 text-sm p-2 mt-1 bg-white">
                                                        </select>
                                                </div>
                                                <div>
                                                    <label class="text-xs font-bold text-slate-600 uppercase">Priorität</label>
                                                    <select name="priority" id="notePriority" class="w-full rounded-lg border-slate-200 text-sm p-2 mt-1 bg-white">
                                                        <option value="low">Niedrig</option>
                                                        <option value="medium" selected>Mittel</option>
                                                        <option value="high">Hoch</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="text-xs font-bold text-slate-600 uppercase">Deadline (Optional)</label>
                                                <input type="date" name="deadline" id="noteDeadline" class="w-full rounded-lg border-slate-200 text-sm p-2 mt-1">
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end gap-2">
                                            <button type="button" onclick="closeNoteModal()" class="px-4 py-2 rounded-full border text-slate-600 text-xs font-bold">Abbrechen</button>
                                            <button type="submit" class="px-4 py-2 rounded-full bg-emerald-500 text-white text-xs font-bold hover:bg-emerald-600">Speichern</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div id="categoryModal" class="custom-modal-overlay">
                            <div class="custom-modal-container" style="max-width: 400px; height: auto;">
                                <div class="custom-modal-header">
                                    <h3 class="custom-modal-title">Neue Kategorie</h3>
                                    <button class="custom-modal-close" onclick="closeCategoryModal()">&times;</button>
                                </div>
                                <div class="custom-modal-body">
                                    <form id="categoryForm" onsubmit="submitCategoryForm(event)">
                                        <div class="flex flex-col gap-4">
                                            <div>
                                                <label class="text-xs font-bold text-slate-600 uppercase">Name</label>
                                                <input type="text" id="catName" required class="w-full rounded-lg border-slate-200 text-sm p-2 mt-1">
                                            </div>
                                            <div>
                                                <label class="text-xs font-bold text-slate-600 uppercase">Farbe</label>
                                                <input type="color" id="catColor" value="#74b2d4" class="w-full h-10 rounded-lg border-slate-200 mt-1 cursor-pointer">
                                            </div>
                                        </div>
                                        <div class="mt-6 flex justify-end gap-2">
                                            <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 rounded-full border text-slate-600 text-xs font-bold">Abbrechen</button>
                                            <button type="submit" class="px-4 py-2 rounded-full bg-blue-500 text-white text-xs font-bold hover:bg-blue-600">Erstellen</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div id="modal-overlay"
                            class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[9999] flex items-end sm:items-center justify-center p-0 sm:p-4 transition-opacity">
                        </div>
                    </div>

                    <div id="goalBulkBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-4 z-50 transition-transform duration-300 translate-y-[150%]">
                      <div class="flex items-center gap-2">
                          <span class="font-bold text-sm" id="bulkCount">0</span>
                          <span class="text-xs text-slate-400">ausgewählt</span>
                      </div>
                      <div class="w-px h-4 bg-slate-700"></div>
                      
                      <button type="button" id="btnBulkReport" class="flex items-center gap-2 hover:text-blue-300 transition text-sm font-semibold">
                          <i class="ri-file-text-line"></i> Bericht & Erledigen
                      </button>
                      
                      <div class="w-px h-4 bg-slate-700"></div>
                      
                      <button type="button" id="btnBulkCancel" class="text-slate-400 hover:text-white transition text-xs">
                          Abbrechen
                      </button>
                    </div>
 
                    <div class="mt-4">
                        <button
                            type="button"
                            id="toggleOverdue48hBtn"
                            class="lazy-toggle-btn"
                            data-loaded="false"
                            data-open="false"
                        >
                            <span class="lazy-toggle-btn__left">
                                <i class="ri-time-line"></i>
                                <span>Überfällige Aufgaben (48h)</span>
                            </span>

                            <span class="lazy-toggle-btn__right">
                                <span class="lazy-toggle-status">Anzeigen</span>
                                <i class="ri-arrow-down-s-line lazy-toggle-arrow"></i>
                            </span>
                        </button>

                        <div id="overdue48hWrapper" class="lazy-toggle-panel hidden"></div>
                    </div>
 

                </div> 
                 
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
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    const AUTH_USER_ID = "{{ auth()->user()->name }}"; // Safe string injection for ID
    const BASE_URL_EMPLOYEE_IMG = "{{ asset('images/employee') }}/";
    const DEFAULT_USER_IMG = "{{ asset('images/gender/male.png') }}";
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
 
{{-- KPI + GOAL LIST (DUE TODAY) --}}
<script>
(() => {
    'use strict';

    let allItems = [];
    let currentFilter = 'all';
    let currentSort = 'due_asc';

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const qs  = (sel, root = document) => root.querySelector(sel);
    const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeText(value) {
        return value == null ? '' : String(value);
    }

    function toDateOrNull(value) {
        if (!value) return null;
        const date = new Date(value);
        return Number.isNaN(date.getTime()) ? null : date;
    }

    function toDateTimeOrNull(dateValue, timeValue = '') {
        if (!dateValue) return null;
        const safeTime = timeValue ? String(timeValue).slice(0, 5) : '00:00';
        const date = new Date(`${dateValue}T${safeTime}`);
        return Number.isNaN(date.getTime()) ? toDateOrNull(dateValue) : date;
    }

    function formatDateDE(value) {
        const date = toDateOrNull(value);
        return date ? date.toLocaleDateString('de-DE') : '';
    }

    function formatDateTimeDE(dateValue, timeValue = '') {
        const date = toDateTimeOrNull(dateValue, timeValue);
        if (!date) return '–';

        return date.toLocaleDateString('de-DE', {
            weekday: 'short',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            ...(timeValue ? { hour: '2-digit', minute: '2-digit' } : {})
        });
    }

    function clamp(num, min, max) {
        num = Number(num) || 0;
        return Math.max(min, Math.min(max, num));
    }

    function safeArray(value) {
        return Array.isArray(value) ? value : [];
    }

    function extractProductName(productValue) {
        if (!productValue) return '';

        if (Array.isArray(productValue)) {
            return productValue
                .map(item => item?.name || item?.title || item?.product_name || '')
                .filter(Boolean)
                .join(', ');
        }

        if (typeof productValue === 'object') {
            return productValue.name || productValue.title || productValue.product_name || '';
        }

        let value = String(productValue).trim();
        if (!value) return '';

        for (let i = 0; i < 3; i++) {
            try {
                const parsed = JSON.parse(value);

                if (Array.isArray(parsed)) {
                    return parsed
                        .map(item => item?.name || item?.title || item?.product_name || '')
                        .filter(Boolean)
                        .join(', ');
                }

                if (parsed && typeof parsed === 'object') {
                    return parsed.name || parsed.title || parsed.product_name || '';
                }

                if (typeof parsed === 'string') {
                    value = parsed;
                    continue;
                }
            } catch (e) {
                break;
            }
        }

        const match = value.match(/"name"\s*:\s*"([^"]+)"/i);
        if (match) return match[1];

        return '';
    }

    function createProductBadge(productValue) {
        const productName = extractProductName(productValue);
        if (!productName) return null;

        const badge = document.createElement('span');
        badge.className = 'goal-product-badge';
        badge.innerHTML = `<i class="ri-box-3-line"></i><span>${escapeHtml(productName)}</span>`;
        return badge;
    }

    function ensureGoalStyles() {
        if (qs('#goal-script-styles')) return;

        const style = document.createElement('style');
        style.id = 'goal-script-styles';
        style.textContent = `
            #goalList .goal-item{
                display:flex;
                align-items:stretch;
                justify-content:space-between;
                gap:.9rem;
                padding:.75rem .85rem;
                border:1px solid rgba(148,163,184,.35);
                border-radius:14px;
                background:#fff;
                margin-bottom:.55rem;
            }
            #goalList .goal-item-main{
                display:flex;
                gap:.65rem;
                align-items:flex-start;
                min-width:0;
                flex:1 1 auto;
            }
            #goalList .goal-icon{
                width:34px;
                height:34px;
                border-radius:12px;
                display:grid;
                place-items:center;
                flex:0 0 auto;
                background:rgba(241,245,249,.9);
                border:1px solid rgba(148,163,184,.25);
            }
            #goalList .goal-icon svg{width:18px;height:18px;display:block}
            #goalList .goal-item-text{min-width:0;flex:1}
            #goalList .goal-item-text .title{
                font-size:.9rem;
                line-height:1.25rem;
                font-weight:600;
                color:#0f172a;
                text-decoration:none;
                display:block;
            }
            #goalList .goal-item-text .meta{
                margin-top:.2rem;
                font-size:.75rem;
                line-height:1.05rem;
                color:#64748b;
                display:flex;
                gap:.45rem;
                align-items:center;
                flex-wrap:wrap;
            }
            #goalList .goal-meta-chip,
            #goalList .goal-product-badge{
                display:inline-flex;
                align-items:center;
                gap:.35rem;
                padding:.28rem .6rem;
                border-radius:999px;
                font-size:.72rem;
                font-weight:600;
                border:1px solid #e2e8f0;
                background:#f8fafc;
                color:#334155;
                white-space:nowrap;
            }
            #goalList .goal-product-badge{
                background:#ecfdf5;
                color:#166534;
                border-color:#86efac;
            }
            #goalList .goal-product-badge span{
                max-width:180px;
                overflow:hidden;
                text-overflow:ellipsis;
                white-space:nowrap;
            }
            #goalList .goal-extra{
                margin-top:.45rem;
                display:flex;
                flex-wrap:wrap;
                gap:.45rem;
            }
            #goalList .goal-detail-btn,
            #goalList .goal-map-btn,
            #goalList .goal-action-btn{
                display:inline-flex;
                align-items:center;
                gap:.35rem;
                padding:.34rem .65rem;
                border-radius:999px;
                font-size:.7rem;
                font-weight:600;
                text-decoration:none;
                border:1px solid #e2e8f0;
                background:#fff;
                color:#334155;
                cursor:pointer;
            }
            #goalList .goal-detail-btn{
                background:#eff6ff;
                color:#1d4ed8;
                border-color:#bfdbfe;
            }
            #goalList .goal-map-btn{
                background:#ecfeff;
                color:#0f766e;
                border-color:#a5f3fc;
            }
            #goalList .goal-item-side{
                display:flex;
                flex-direction:column;
                align-items:flex-end;
                gap:.45rem;
                flex:0 0 auto;
            }
            #goalList .goal-item-tag{
                display:inline-flex;
                align-items:center;
                justify-content:center;
                font-size:.7rem;
                padding:.28rem .6rem;
                border-radius:999px;
                color:#fff;
                text-decoration:none;
                line-height:1;
            }
            #goalList .goal-empty{
                padding:1rem;
                text-align:center;
                color:#64748b;
                border:1px dashed #cbd5e1;
                border-radius:14px;
                background:#f8fafc;
            }
        `;
        document.head.appendChild(style);
    }

    function showToast(message = 'Als erledigt markiert!') {
        const toast = qs('#toastSuccess');
        if (!toast) return;
        toast.textContent = message;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 2500);
    }

    function updateTodayBadge(percent) {
        const textEl = qs('#todayPercentText');
        const bar = qs('#todayProgressBar');
        const p = clamp(percent, 0, 100);

        if (textEl) textEl.textContent = `${p}%`;
        if (bar) bar.style.width = `${p}%`;
    }

    function isOverdue48(dueDate, dueTime = '') {
        const date = toDateTimeOrNull(dueDate, dueTime);
        if (!date) return false;
        return (Date.now() - date.getTime()) > (48 * 60 * 60 * 1000);
    }

    function updateCountersView(items) {
        const list = safeArray(items);

        const counts = {
            all: list.length,
            lead: list.filter(i => i.type === 'lead').length,
            anfrage: list.filter(i => i.type === 'inquiry').length,
            aufgabe: list.filter(i => i.type === 'personal_task').length,
            appointment: list.filter(i => i.type === 'appointment').length,
            rest: list.filter(i => !['lead','inquiry','personal_task','appointment'].includes(i.type)).length,
        };

        Object.entries(counts).forEach(([key, val]) => {
            const el = qs(`#count-${key}`);
            if (el) el.textContent = `(${val})`;
        });

        const openToday = list.length;
        const overdue = list.filter(i => isOverdue48(i.due_date, i.due_time)).length;

        const openEl = qs('#kpi-open-today');
        const overdueEl = qs('#kpi-overdue');

        if (openEl) openEl.textContent = openToday;
        if (overdueEl) overdueEl.textContent = overdue;
    }

    function setActiveFilterChip() {
        qsa('.goal-filter-chip').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-filter') === currentFilter);
        });
    }

    function getFilteredItems() {
        switch (currentFilter) {
            case 'aufgabe':
                return allItems.filter(i => i.type === 'personal_task');
            case 'anfrage':
                return allItems.filter(i => i.type === 'inquiry');
            case 'appointment':
                return allItems.filter(i => i.type === 'appointment');
            case 'lead':
                return allItems.filter(i => i.type === 'lead');
            case 'rest':
                return allItems.filter(i => !['lead','personal_task','inquiry','appointment'].includes(i.type));
            default:
                return [...allItems];
        }
    }

    function mapPriority(priority) {
        const p = String(priority || '').toLowerCase();
        if (p.includes('very') || p.includes('sehr')) return 4;
        if (p.includes('high') || p.includes('dring')) return 3;
        if (p.includes('low') || p.includes('niedrig')) return 1;
        return 2;
    }

    function sortItems(items) {
        return [...items].sort((a, b) => {
            const da = toDateTimeOrNull(a?.due_date, a?.due_time);
            const db = toDateTimeOrNull(b?.due_date, b?.due_time);
            const pa = mapPriority(a?.priority);
            const pb = mapPriority(b?.priority);

            switch (currentSort) {
                case 'due_desc':
                    if (da && db) return db - da;
                    return 0;
                case 'prio_desc':
                    return pb - pa;
                case 'prio_asc':
                    return pa - pb;
                case 'due_asc':
                default:
                    if (da && db) return da - db;
                    return 0;
            }
        });
    }

    function getItemLink(item) {
        switch (item.type) {
            case 'appointment': return `/appointment_details/${item.id}`;
            case 'personal_task': return `/personal-tasks/${item.id}/profile/`;
            case 'problem': return `/problem/profile/${item.id}`;
            case 'ticket_task': return `/ticket_task_details/${item.id}`;
            case 'inquiry': return `/inquiry_show/${item.id}`;
            case 'lead': return `/lead/product/${item.id}`;
            case 'leave': return '#';
            default: return '#';
        }
    }

    async function postJSON(url, payload) {
        const response = await fetch(url, {
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

        const data = await response.json().catch(() => ({}));
        if (!response.ok || data.success === false) {
            throw new Error(data.message || `HTTP ${response.status}`);
        }
        return data;
    }

    async function fetchDueToday() {
        const response = await fetch('/my/due-today', {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data?.message || `HTTP ${response.status}`);
        return data || {};
    }

    function svgIcon(type) {
        const base = `viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"`;
        const icons = {
            personal_task: `<svg ${base}><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 4.3 2.6 18.6A2 2 0 0 0 4.3 21h15.4a2 2 0 0 0 1.7-2.4L13.7 4.3a2 2 0 0 0-3.4 0Z"/></svg>`,
            appointment: `<svg ${base}><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>`,
            inquiry: `<svg ${base}><path d="M9.1 9a3 3 0 1 1 4.9 2.3c-.9.7-1.5 1.1-1.5 2.7"/><path d="M12 17h.01"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>`,
            lead: `<svg ${base}><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="8" r="4"/></svg>`,
            leave: `<svg ${base}><path d="M12 2v20"/><path d="M7 7c2 0 3-1 5-1s3 1 5 1v7c-2 0-3-1-5-1s-3 1-5 1Z"/></svg>`,
            problem: `<svg ${base}><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 4.3 2.6 18.6A2 2 0 0 0 4.3 21h15.4a2 2 0 0 0 1.7-2.4L13.7 4.3a2 2 0 0 0-3.4 0Z"/></svg>`,
            default: `<svg ${base}><path d="M12 20v-6"/><path d="M12 8h.01"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>`
        };
        return icons[type] || icons.default;
    }

    function badgeColor(item) {
        if (item.type === 'appointment') return '#ef4444';
        if (item.type === 'inquiry') return '#f97316';
        if (item.type === 'lead') return '#0ea5e9';
        if (item.type === 'leave') return '#8b5cf6';
        if (item.type === 'problem') return '#f43f5e';
        return '#8fc73e';
    }

    function iconTone(item) {
        if (item.type === 'appointment') return '#ef4444';
        if (item.type === 'inquiry') return '#f97316';
        if (item.type === 'lead') return '#0ea5e9';
        if (item.type === 'problem') return '#f43f5e';
        if (item.type === 'leave') return '#8b5cf6';
        return '#16a34a';
    }

    function renderGoalList() {
        ensureGoalStyles();

        const goalList = qs('#goalList');
        if (!goalList) return;

        const items = sortItems(getFilteredItems());
        goalList.innerHTML = '';

        if (!items.length) {
            goalList.innerHTML = `<div class="goal-empty">Keine Einträge gefunden.</div>`;
            return;
        }

        const frag = document.createDocumentFragment();

        items.forEach(item => {
            const wrap = document.createElement('div');
            wrap.className = 'goal-item';
            wrap.dataset.id = item.id ?? '';
            wrap.dataset.type = item.type ?? '';

            const main = document.createElement('div');
            main.className = 'goal-item-main';

            const iconBox = document.createElement('div');
            iconBox.className = 'goal-icon';
            iconBox.style.color = iconTone(item);
            iconBox.innerHTML = svgIcon(item.type);

            const text = document.createElement('div');
            text.className = 'goal-item-text';

            const title = document.createElement('a');
            title.className = 'title';
            title.href = getItemLink(item);
            title.textContent = item.title || '—';

            const meta = document.createElement('div');
            meta.className = 'meta';

            if (item.due_date) {
                const due = document.createElement('span');
                due.className = 'goal-meta-chip';
                due.innerHTML = `<i class="ri-time-line"></i><span>${escapeHtml(formatDateTimeDE(item.due_date, item.due_time))}</span>`;
                meta.appendChild(due);
            }

            if (item.customer_name) {
                const customer = document.createElement('span');
                customer.className = 'goal-meta-chip';
                customer.innerHTML = `<i class="ri-user-3-line"></i><span>${escapeHtml(item.customer_name)}</span>`;
                meta.appendChild(customer);
            }

            if (item.product_info) {
                const productBadge = createProductBadge(item.product_info);
                if (productBadge) meta.appendChild(productBadge);
            }

            if (item.full_address) {
                const address = document.createElement('span');
                address.className = 'goal-meta-chip';
                address.innerHTML = `<i class="ri-map-pin-line"></i><span>${escapeHtml(item.full_address)}</span>`;
                meta.appendChild(address);
            }

            const extra = document.createElement('div');
            extra.className = 'goal-extra';

            if (item.type === 'appointment') {
                const detailsBtn = document.createElement('a');
                detailsBtn.href = getItemLink(item);
                detailsBtn.className = 'goal-detail-btn';
                detailsBtn.innerHTML = `<i class="ri-eye-line"></i><span>Details</span>`;
                extra.appendChild(detailsBtn);
            }

            if (item.map_url) {
                const mapBtn = document.createElement('a');
                mapBtn.href = item.map_url;
                mapBtn.target = '_blank';
                mapBtn.rel = 'noopener noreferrer';
                mapBtn.className = 'goal-map-btn';
                mapBtn.innerHTML = `<i class="ri-map-pin-2-line"></i><span>Google Maps</span>`;
                extra.appendChild(mapBtn);
            }

            const doneLabel = document.createElement('label');
            doneLabel.className = 'goal-action-btn';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'mark-done-checkbox mr-2';

            const span = document.createElement('span');
            span.textContent = 'Erledigt';

            doneLabel.appendChild(checkbox);
            doneLabel.appendChild(span);
            extra.appendChild(doneLabel);

            text.appendChild(title);
            if (meta.childNodes.length) text.appendChild(meta);
            if (item.description) {
                const desc = document.createElement('div');
                desc.style.marginTop = '.35rem';
                desc.style.fontSize = '.76rem';
                desc.style.color = '#475569';
                desc.textContent = item.description;
                text.appendChild(desc);
            }
            if (extra.childNodes.length) text.appendChild(extra);

            main.appendChild(iconBox);
            main.appendChild(text);

            const side = document.createElement('div');
            side.className = 'goal-item-side';

            const tag = document.createElement('a');
            tag.href = getItemLink(item);
            tag.className = 'goal-item-tag';
            tag.style.background = badgeColor(item);
            tag.textContent = (item.label || item.type || 'Eintrag').replace(/_/g, ' ');
            side.appendChild(tag);

            wrap.appendChild(main);
            wrap.appendChild(side);

            frag.appendChild(wrap);
        });

        goalList.appendChild(frag);
    }

    async function loadDueToday() {
        try {
            const data = await fetchDueToday();
            updateTodayBadge(data.percent ?? 0);
            allItems = safeArray(data.items);
            updateCountersView(allItems);
            setActiveFilterChip();
            renderGoalList();
        } catch (error) {
            console.error('Error loading due today:', error);
            const goalList = qs('#goalList');
            if (goalList) {
                goalList.innerHTML = `<div class="goal-empty">Fehler beim Laden der Daten.</div>`;
            }
        }
    }

    document.addEventListener('click', async (e) => {
        const chip = e.target.closest('.goal-filter-chip');
        if (chip) {
            currentFilter = chip.dataset.filter || 'all';
            setActiveFilterChip();
            renderGoalList();
            return;
        }
    });

    document.addEventListener('change', async (e) => {
        if (e.target?.id === 'goalSortSelect') {
            currentSort = e.target.value || 'due_asc';
            renderGoalList();
            return;
        }

        if (!e.target.classList.contains('mark-done-checkbox')) return;

        const checkbox = e.target;
        const container = checkbox.closest('.goal-item');
        if (!container) return;

        const id = container.dataset.id;
        const type = container.dataset.type;

        try {
            await postJSON('/my/mark-done', { id, type });
            showToast('Als erledigt markiert!');
            loadDueToday();
        } catch (err) {
            console.error(err);
            checkbox.checked = false;
            alert(err.message || 'Fehler beim Speichern.');
        }
    });

    ensureGoalStyles();
    loadDueToday();
    setInterval(loadDueToday, 10 * 60 * 1000);
})();
</script>
<script>
(() => {
    'use strict';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function extractProductName(productValue) {
        if (!productValue) return '';

        // already object/array
        if (Array.isArray(productValue)) {
            return productValue
                .map(item => item?.name || item?.title || item?.product_name || '')
                .filter(Boolean)
                .join(', ');
        }

        if (typeof productValue === 'object') {
            return productValue.name || productValue.title || productValue.product_name || '';
        }

        let value = String(productValue).trim();
        if (!value) return '';

        // remove wrapping quotes if whole payload is quoted
        if (
            (value.startsWith('"') && value.endsWith('"')) ||
            (value.startsWith("'") && value.endsWith("'"))
        ) {
            value = value.slice(1, -1);
        }

        // try multiple JSON parses because your value is sometimes double-encoded
        for (let i = 0; i < 3; i++) {
            try {
                const parsed = JSON.parse(value);

                if (Array.isArray(parsed)) {
                    return parsed
                        .map(item => item?.name || item?.title || item?.product_name || '')
                        .filter(Boolean)
                        .join(', ');
                }

                if (parsed && typeof parsed === 'object') {
                    return parsed.name || parsed.title || parsed.product_name || '';
                }

                if (typeof parsed === 'string') {
                    value = parsed;
                    continue;
                }
            } catch (e) {
                break;
            }
        }

        // fallback: try regex for `"name":"..."`
        const match = value.match(/"name"\s*:\s*"([^"]+)"/i);
        if (match) return match[1];

        return value;
    }

    function createProductBadge(productValue) {
        const productName = extractProductName(productValue);
        if (!productName) return null;

        const badge = document.createElement('span');
        badge.className = 'goal-product-badge';
        badge.innerHTML = `
            <i class="ri-box-3-line"></i>
            <span>${escapeHtml(productName)}</span>
        `;
        return badge;
    }

    // expose globally if needed
    window.extractProductName = extractProductName;
    window.createProductBadge = createProductBadge;
})();
</script>


<!-- Breaking news  -->
 <script>
/**
 * Update: employee images are in /public/images/employee
 * Your HB endpoint already returns `avatar_url` (Storage::url or asset fallback).
 * This script:
 * - Uses avatar_url if present.
 * - If not, builds URL from /images/employee/<filename> (or keeps absolute).
 * - Still merges breaking + notifications + holidays + birthdays into ONE ticker.
 */
document.addEventListener("DOMContentLoaded", () => {
  "use strict";

  const $ = (id) => document.getElementById(id);

  const bar        = $("breakingNewsBar");
  const textEl     = $("breakingNewsText");
  const typeEl     = $("breakingNewsType");
  const timeEl     = $("breakingNewsTimeText");

  const prevBtn    = $("bnPrev");
  const nextBtn    = $("bnNext");
  const playBtn    = $("bnPlayPause");
  const playIconEl = $("bnPlayPauseIcon");

  const creatorImgEl  = $("breakingNewsCreatorImage");
  const creatorNameEl = $("breakingNewsCreatorName");

  const labelIconEl = $("bnMainIcon");
  const labelEl     = $("bnLabel");

  const audioWrapper    = $("bnAudioWrapper");
  const audioSeek       = $("bnAudioSeek");
  const audioProgress   = $("bnAudioProgress");
  const audioHandle     = $("bnAudioHandle");
  const audioCurrentEl  = $("bnAudioCurrent");
  const audioDurationEl = $("bnAudioDuration");

  if (!bar || !textEl || !typeEl || !timeEl) return;

  const ROUTE_BREAKING = "{{ route('breaking-news.active') }}";
  const ROUTE_NOTIFS   = "{{ route('dashboard.notifications.index') }}";
  const ROUTE_HB       = "{{ route('dashboard.widgets.holiday-birthday') }}";

  // ✅ correct base for public/images/employee
  const EMP_IMG_BASE = "{{ asset('images/employee') }}/";

  const intervalMs = 14000;
  const refreshMs  = 60000;

  let playlist = [];
  let index    = 0;

  const sources = { breaking: [], notifs: [], specials: [] };

  let loopTimer = null;
  let paused = false;

  const audio = new Audio();
  audio.preload = "metadata";

  /* ---------------- utils ---------------- */
  const esc = (s) =>
    String(s ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");

  const linkifyEscaped = (escaped) => {
    const re = /((https?:\/\/|www\.)[^\s<]+)/gi;
    return String(escaped).replace(re, (m) => {
      const href = m.startsWith("http") ? m : `https://${m}`;
      return `<a href="${href}" target="_blank" rel="noopener noreferrer" style="text-decoration:underline">${m}</a>`;
    });
  };

  function safeKey(src, item) {
    const id = item?.id || item?._id || "";
    if (id) return `${src}:${id}`;
    const t = String(item?.title || "").slice(0, 120);
    const m = String(item?.message || "").slice(0, 200);
    const d = String(item?.performed_at || item?.performed_at_human || "").slice(0, 40);
    return `${src}:${t}|${m}|${d}`;
  }

  async function safeJsonFetch(url) {
    const r = await fetch(url, { headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" } });
    const t = await r.text().catch(() => "");
    let j = {};
    try { j = JSON.parse(t || "{}"); } catch { j = {}; }
    if (!r.ok) throw new Error(`HTTP ${r.status} ${r.statusText} ${t.slice(0, 200)}`);
    return j;
  }

  function restartTickerAnimation(el) {
    if (!el) return;
    el.style.animation = "none";
    void el.offsetHeight;
    el.style.animation = "";
  }

  function formatTime(sec) {
    if (!Number.isFinite(sec) || sec < 0) return "0:00";
    const m = Math.floor(sec / 60);
    const s = Math.floor(sec % 60);
    return `${m}:${String(s).padStart(2, "0")}`;
  }

  function fmtDateDE(iso) {
    try {
      const d = new Date(iso);
      return d.toLocaleDateString("de-DE", { weekday: "short", day: "2-digit", month: "2-digit" });
    } catch {
      return String(iso || "");
    }
  }

  function setPaused(p) {
    paused = !!p;
    bar.classList.toggle("is-paused", paused);
    syncPlayIcon();
  }

  function stopLoop() {
    if (!loopTimer) return;
    clearInterval(loopTimer);
    loopTimer = null;
  }

  function startLoop() {
    stopLoop();
    loopTimer = setInterval(() => {
      if (!paused) showNext();
    }, intervalMs);
  }

  /* ---------------- audio UI ---------------- */
  function resetAudioUI() {
    if (audioProgress)  audioProgress.style.width = "0%";
    if (audioHandle)    audioHandle.style.left = "0%";
    if (audioSeek)      audioSeek.value = 0;
    if (audioCurrentEl) audioCurrentEl.textContent = "0:00";
    if (audioDurationEl) audioDurationEl.textContent = "0:00";
  }

  function updateAudioUI() {
    if (!audio.duration || !Number.isFinite(audio.duration)) return;
    const pct = (audio.currentTime / audio.duration) * 100;
    if (audioProgress)  audioProgress.style.width = `${pct}%`;
    if (audioHandle)    audioHandle.style.left = `${pct}%`;
    if (audioSeek)      audioSeek.value = pct;
    if (audioCurrentEl) audioCurrentEl.textContent = formatTime(audio.currentTime);
  }

  audio.addEventListener("loadedmetadata", () => {
    if (audioDurationEl) audioDurationEl.textContent = formatTime(audio.duration);
  });
  audio.addEventListener("timeupdate", updateAudioUI);
  audio.addEventListener("ended", () => setPaused(false));

  if (audioSeek) {
    audioSeek.addEventListener("input", (e) => {
      if (!audio.duration || !Number.isFinite(audio.duration)) return;
      const pct = parseFloat(e.target.value || "0");
      audio.currentTime = (pct / 100) * audio.duration;
      updateAudioUI();
    });
  }

  function applyAudio(audioUrl) {
    const hasAudio = !!audioUrl;
    if (!audioWrapper) return;

    if (!hasAudio) {
      audioWrapper.classList.add("hidden");
      audio.pause();
      audio.removeAttribute("src");
      resetAudioUI();
      return;
    }

    audioWrapper.classList.remove("hidden");
    audio.pause();
    audio.src = audioUrl;
    audio.currentTime = 0;
    resetAudioUI();

    audio.play().then(() => setPaused(true)).catch(() => setPaused(true));
  }

  /* ---------------- label look (always breaking style) ---------------- */
  function updateLabelVisual(scope, type) {
    if (!labelIconEl || !labelEl) return;

    labelIconEl.className = "sa-bn-label-icon";
    labelEl.className = "sa-bn-label";

    let labelClass = "sa-bn-label--breaking-info";
    let iconClass  = "ri-megaphone-fill";

    const t = String(type || "info").toLowerCase();
    const s = String(scope || "generic").toLowerCase();

    if (s === "birthday") { labelClass = "sa-bn-label--breaking-success"; iconClass = "ri-cake-2-fill"; }
    else if (s === "holiday") { labelClass = "sa-bn-label--breaking-info"; iconClass = "ri-calendar-event-fill"; }
    else if (t === "warning") { labelClass = "sa-bn-label--breaking-warning"; iconClass = "ri-alert-line"; }
    else if (t === "danger")  { labelClass = "sa-bn-label--breaking-danger";  iconClass = "ri-alarm-warning-fill"; }
    else if (t === "success") { labelClass = "sa-bn-label--breaking-success"; iconClass = "ri-checkbox-circle-fill"; }

    labelEl.classList.add(labelClass);
    labelIconEl.classList.add(iconClass, "sa-bn-label-blink");
  }

  function getTypeLabel(type, scope) {
    const s = String(scope || "generic").toLowerCase();
    if (s === "birthday") return "Geburtstag";
    if (s === "holiday")  return "Feiertag";
    if (s === "breaking") return "Breaking";

    const labels = {
      inquiry: "Anfrage",
      lead: "Lead",
      offer: "Angebot",
      appointment: "Termin",
      task: "Aufgabe",
      project: "Projekt",
      ticket: "Ticket",
      employee: "Mitarbeiter",
      info: "Info",
      demo: "System",
      warning: "Warnung",
      danger: "Alarm",
      success: "Hinweis",
    };
    return labels[String(type || "info").toLowerCase()] || "Info";
  }

  /* ---------------- ✅ employee image resolution ---------------- */
  function normalizePublicPath(p) {
    const s = String(p || "").trim();
    if (!s) return "";
    if (s.startsWith("http://") || s.startsWith("https://") || s.startsWith("data:")) return s;

    // Storage::url(...) usually returns "/storage/...." -> keep as-is
    if (s.startsWith("/storage/")) return s;

    // already a public absolute path
    if (s.startsWith("/")) return s;

    // filename only -> /images/employee/<file>
    return EMP_IMG_BASE + s.replace(/^\/+/, "");
  }

  function resolveEmployeeImage(item) {
    // Your controller returns avatar_url already; use it first.
    const avatarUrl =
      item?.avatar_url ||
      item?.creator_image_url ||
      item?.employee_image_url ||
      item?.image_url;

    if (avatarUrl) return normalizePublicPath(avatarUrl);

    // fallback: filename field
    const file =
      item?.creator_image ||
      item?.employee_image ||
      item?.avatar ||
      item?.image ||
      item?.filename;

    return normalizePublicPath(file);
  }

  function applyCreator(item) {
    const img = resolveEmployeeImage(item);

    if (creatorImgEl) {
      if (img) {
        creatorImgEl.src = img;
        creatorImgEl.style.display = "block";
      } else {
        creatorImgEl.style.display = "none";
        creatorImgEl.removeAttribute("src");
      }
    }

    // keep name hidden (optional)
    const name = String(item?.creator_name || item?.employee_name || item?.name || "").trim();
    if (creatorNameEl) {
      creatorNameEl.textContent = name || "";
      creatorNameEl.classList.add("hidden");
    }
  }

  /* ---------------- merge playlist ---------------- */
  function rebuildPlaylist(keepCurrent = true) {
    const currentKey = (keepCurrent && playlist[index]) ? safeKey(playlist[index]._src, playlist[index]) : null;

    const merged = [];
    const seen = new Set();

    const order = [
      ["breaking", sources.breaking],
      ["specials", sources.specials],
      ["notifs", sources.notifs],
    ];

    for (const [src, list] of order) {
      for (const it of (Array.isArray(list) ? list : [])) {
        const item = { ...it, _src: src };
        const k = safeKey(src, item);
        if (seen.has(k)) continue;
        seen.add(k);
        merged.push(item);
      }
    }

    playlist = merged;

    if (!playlist.length) {
      bar.classList.add("hidden");
      stopLoop();
      return;
    }

    bar.classList.remove("hidden");

    if (currentKey) {
      const newIdx = playlist.findIndex((x) => safeKey(x._src, x) === currentKey);
      index = newIdx >= 0 ? newIdx : Math.min(index, playlist.length - 1);
    } else {
      index = Math.min(index, playlist.length - 1);
    }

    showCurrent();
    startLoop();
  }

  /* ---------------- HB -> ticker items ---------------- */
  function holidayWhen(h) {
    const d = Number.isFinite(h.days_until) ? h.days_until : null;
    if (h.is_today) return "Heute";
    if (d === 1) return "Morgen";
    if (d != null && d > 1) return `in ${d} Tagen`;
    return fmtDateDE(h.start_date);
  }

  function birthdayWhen(b) {
    const d = Number.isFinite(b.days_until) ? b.days_until : null;
    if (b.is_today || d === 0) return "Heute";
    if (d === 1) return "Morgen";
    if (d != null && d > 1) return `in ${d} Tagen`;
    return fmtDateDE(b.next_date);
  }

  function hbToSpecials(data) {
    const out = [];
    const holidays  = Array.isArray(data?.holidays)  ? data.holidays  : [];
    const birthdays = Array.isArray(data?.birthdays) ? data.birthdays : [];

    for (const h of holidays) {
      const locParts = [];
      if (h.city) locParts.push(h.city);
      if (h.state) locParts.push(h.state);
      if (h.country) locParts.push(h.country);

      const msg = [
        h.start_date ? fmtDateDE(h.start_date) : "",
        (h.end_date && h.end_date !== h.start_date) ? `– ${fmtDateDE(h.end_date)}` : "",
        locParts.length ? `• ${locParts.join(" • ")}` : "",
        h.comment ? `• ${h.comment}` : ""
      ].filter(Boolean).join(" ");

      out.push({
        id: `holiday-${h.id || (h.name + "-" + h.start_date)}`,
        scope: "holiday",
        type: "info",
        title: h.name || "Feiertag",
        message: msg,
        performed_at: h.start_date || "",
        performed_at_human: holidayWhen(h),
      });
    }

    for (const b of birthdays) {
      const age = (typeof b.age === "number" && b.age > 0) ? `(${b.age})` : "";
      const msg = (b.is_today || b.days_until === 0)
        ? `Alles Gute zum Geburtstag ${age}`.trim()
        : `Nächster: ${b.next_date ? fmtDateDE(b.next_date) : ""} ${age}`.trim();

      out.push({
        id: `birthday-${b.id || (b.name + "-" + b.next_date)}`,
        scope: "birthday",
        type: "success",
        title: b.name || "Mitarbeiter",
        message: msg,
        performed_at: b.next_date || "",
        performed_at_human: birthdayWhen(b),
        avatar_url: b.avatar_url || "", // controller sends it
      });
    }

    out.sort((a, b) => String(a.performed_at || "").localeCompare(String(b.performed_at || "")));
    return out;
  }

  /* ---------------- render ---------------- */
  function syncPlayIcon() {
    if (!playIconEl) return;

    const item = playlist[index] || {};
    const hasAudio = !!item.audio_url;

    if (hasAudio) {
      playIconEl.classList.toggle("ri-pause-mini-line", !audio.paused);
      playIconEl.classList.toggle("ri-play-mini-line", audio.paused);
      return;
    }

    playIconEl.classList.toggle("ri-play-mini-line", paused);
    playIconEl.classList.toggle("ri-pause-mini-line", !paused);
  }

  function showCurrent() {
    if (!playlist.length) return;

    const item = playlist[index] || {};
    const scope = item.scope || (item._src === "breaking" ? "breaking" : "generic");
    const type  = String(item.type || "info").toLowerCase();

    const title = esc(item.title || "Benachrichtigung");
    const message = linkifyEscaped(esc(item.message || ""));
    const timeLabel = item.performed_at_human || item.performed_at || "";

    textEl.innerHTML = `
      <span style="display:inline-flex;align-items:center;gap:8px;">
        <span style="font-weight:700;">${title}</span>
        ${item.message ? `<span style="opacity:0.55;">•</span><span style="opacity:0.95;">${message}</span>` : ``}
      </span>
    `;

    typeEl.textContent = getTypeLabel(type, scope);
    timeEl.textContent = timeLabel;

    updateLabelVisual(scope, type);
    applyCreator(item);

    applyAudio(item.audio_url || null);
    if (!item.audio_url) setPaused(false);

    restartTickerAnimation(textEl);
    syncPlayIcon();
  }

  function showNext() {
    if (!playlist.length) return;
    index = (index + 1) % playlist.length;
    showCurrent();
  }

  function showPrev() {
    if (!playlist.length) return;
    index = (index - 1 + playlist.length) % playlist.length;
    showCurrent();
  }

  /* ---------------- load all feeds ---------------- */
  async function loadBreaking() {
    try {
      const data = await safeJsonFetch(ROUTE_BREAKING);
      const list = Array.isArray(data.breakingNews) ? data.breakingNews : [];
      sources.breaking = list.map((x) => ({ ...x, scope: "breaking" }));
    } catch (e) {
      console.error("[Ticker] breaking failed:", e);
      sources.breaking = [];
    }
  }

  async function loadNotifs() {
    try {
      const data = await safeJsonFetch(ROUTE_NOTIFS);
      const list = Array.isArray(data.notifications) ? data.notifications : [];
      sources.notifs = list.map((x) => ({ ...x, scope: x.scope || "generic" }));
    } catch (e) {
      console.error("[Ticker] notifications failed:", e);
      sources.notifs = [];
    }
  }

  async function loadHB() {
    try {
      const qs = new URLSearchParams({ days: 30 });
      const data = await safeJsonFetch(`${ROUTE_HB}?${qs.toString()}`);
      sources.specials = hbToSpecials(data);
    } catch (e) {
      console.error("[Ticker] holiday/birthday failed:", e);
      sources.specials = [];
    }
  }

  async function loadAll(first = false) {
    bar.classList.remove("hidden");
    await Promise.allSettled([loadBreaking(), loadHB(), loadNotifs()]);
    rebuildPlaylist(!first);
  }

  /* ---------------- controls ---------------- */
  if (prevBtn) {
    prevBtn.addEventListener("click", () => {
      audio.pause();
      setPaused(false);
      showPrev();
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      audio.pause();
      setPaused(false);
      showNext();
    });
  }

  if (playBtn) {
    playBtn.addEventListener("click", async () => {
      const item = playlist[index] || {};
      const hasAudio = !!item.audio_url;

      if (hasAudio) {
        if (audio.paused) {
          try { await audio.play(); setPaused(true); }
          catch { setPaused(true); }
        } else {
          audio.pause();
          setPaused(false);
        }
        syncPlayIcon();
        return;
      }

      setPaused(!paused);
    });
  }

  /* ---------------- Echo realtime notifications (still works when breaking exists) ---------------- */
  @if(Auth::check())
  function guessScopeFromPayload(d) {
    if (!d) return "generic";
    if (d.customer_id || d.lead_id) return "customer";
    if (d.emp_id || d.employee_id) return "employee";
    if (d.project_id) return "project";
    if (d.ticket_id)  return "ticket";
    if (d.task_id)    return "task";
    return "generic";
  }

  if (window.Echo) {
    Echo.private("App.Models.User.{{ Auth::id() }}").notification((notif) => {
      const d = notif?.data || notif || {};
      const newItem = {
        id: notif?.id || `live-${Date.now()}`,
        type: String(d.type || "info").toLowerCase(),
        scope: guessScopeFromPayload(d),
        title: d.title || "Benachrichtigung",
        message: d.message || "",
        performed_at: d.performed_at || new Date().toISOString(),
        performed_at_human: "soeben",
        creator_name: d.creator_name || d.employee_name || "",
        // if you ever send filename only in Echo:
        creator_image: d.creator_image || d.employee_image || d.image || "",
        creator_image_url: d.creator_image_url || d.employee_image_url || "",
        audio_url: d.audio_url || null,
      };

      sources.notifs = [newItem, ...(sources.notifs || [])];
      rebuildPlaylist(true);
    });
  }
  @endif

  /* ---------------- init + refresh ---------------- */
  loadAll(true);
  setInterval(() => loadAll(false), refreshMs);
});
</script>

<!-- Breaking news  -->

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
(() => {
  'use strict';

  const qs = (s, r=document) => r.querySelector(s);

  // ----- Holiday modal elements (your existing IDs) -----
  const holidayModal   = qs('#holiday-request-modal');
  const holidayClose   = qs('#holiday-modal-close');
  const holidayCancel  = qs('#holiday-modal-cancel');
  const openFromMenu   = qs('#btn-open-holiday-modal-menu');

  // If you already have an "open holiday modal" function somewhere, we’ll call it if exists.
  function openHolidayModal() {
    if (!holidayModal) return;

    // If you have a custom opener already, prefer it
    if (typeof window.openHolidayRequestModal === 'function') {
      window.openHolidayRequestModal();
      return;
    }

    holidayModal.classList.remove('holiday-modal-hidden');
    document.body.classList.add('overflow-hidden');
  }

  function closeHolidayModal() {
    if (!holidayModal) return;

    if (typeof window.closeHolidayRequestModal === 'function') {
      window.closeHolidayRequestModal();
      return;
    }

    holidayModal.classList.add('holiday-modal-hidden');
    document.body.classList.remove('overflow-hidden');
  }

  // Menu click -> open modal
  if (openFromMenu) {
    openFromMenu.addEventListener('click', () => {
      openHolidayModal();

      // Close the header menu if present
      const menuPanel = qs('[data-hmenu-panel]');
      if (menuPanel) menuPanel.classList.add('hidden');
    });
  }

  // Close buttons
  if (holidayClose)  holidayClose.addEventListener('click', closeHolidayModal);
  if (holidayCancel) holidayCancel.addEventListener('click', closeHolidayModal);

  // Click outside container closes (overlay click)
  if (holidayModal) {
    holidayModal.addEventListener('click', (e) => {
      if (e.target === holidayModal) closeHolidayModal();
    });
  }

  // ESC closes
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeHolidayModal();
  });
})();
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
 
<script>
    const NOTE_ROUTES = {
        fetch: "{{ route('admin.notes.fetch') }}",
        store: "{{ route('admin.notes.store') }}",
        update: "{{ route('admin.notes.update') }}",
        delete: "{{ route('admin.notes.delete') }}",
        reorder: "{{ route('admin.notes.reorder') }}",
        catStore: "{{ route('admin.notes.category.store') }}"
    };

    let noteSearchTimeout = null;

    // --- Init ---
    document.addEventListener('DOMContentLoaded', () => {
        fetchPersonalNotes();
        
        // Init SortableJS for Drag & Drop
        const list = document.getElementById('personal-note-list');
        new Sortable(list, {
            animation: 150,
            ghostClass: 'dragging',
            handle: '.note-handle', // Drag via specific handle or whole item
            onEnd: function (evt) {
                saveNoteOrder();
            }
        });
    });

    // --- Fetch & Render ---
    function fetchPersonalNotes() {
        const catId = document.getElementById('noteCategoryFilter').value;
        const search = document.getElementById('noteSearch').value;

        fetch(`${NOTE_ROUTES.fetch}?category_id=${catId}&search=${search}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    renderCategories(data.categories);
                    renderNotes(data.notes);
                }
            })
            .catch(err => console.error(err));
    }

    function renderCategories(categories) {
        // Populate Filter
        const filterEl = document.getElementById('noteCategoryFilter');
        const currentVal = filterEl.value; // preserve selection
        let html = '<option value="all">Alle</option>';
        
        // Populate Modal Select
        const modalSelect = document.getElementById('noteCategorySelect');
        let modalHtml = '';

        categories.forEach(cat => {
            html += `<option value="${cat.id}">${cat.category_name}</option>`;
            modalHtml += `<option value="${cat.id}">${cat.category_name}</option>`;
        });

        // Only update innerHTML if length changed to avoid UI jump, or fully replace
        if(filterEl.options.length !== categories.length + 1) {
            filterEl.innerHTML = html;
            filterEl.value = currentVal;
        }
        modalSelect.innerHTML = modalHtml;
    }

    function renderNotes(notes) {
        const list = document.getElementById('personal-note-list');
        list.innerHTML = '';

        if(notes.length === 0) {
            list.innerHTML = `<li class="text-center text-xs text-gray-400 mt-4 italic">Keine Notizen gefunden.</li>`;
            return;
        }

        notes.forEach(note => {
            const isDone = note.is_done ? 'complete' : '';
            const checked = note.is_done ? 'checked' : '';
            const badgeColor = note.category ? (note.category.color || '#ccc') : '#ccc';
            const catName = note.category ? note.category.category_name : '';
            
            const li = document.createElement('li');
            li.className = `list-group-item group flex flex-col gap-1 cursor-default`;
            li.setAttribute('data-id', note.id);
            // Dynamic border color based on category
            li.style.borderLeft = `3px solid ${badgeColor}`;

            li.innerHTML = `
                <div class="flex items-start justify-between w-full note-handle cursor-grab active:cursor-grabbing">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <input type="checkbox" ${checked} onchange="toggleNoteStatus(${note.id}, this.checked)"
                            class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500 h-4 w-4 cursor-pointer">
                        <div class="flex flex-col min-w-0">
                            <span class="title-field ${isDone} truncate font-semibold text-slate-700 text-xs">${escapeHtml(note.title)}</span>
                            <span class="text-[10px] text-slate-400">${catName}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="editNote(${note.id})" class="mini-btn hover:text-blue-500" title="Bearbeiten">
                            <i class="ri-pencil-line"></i>
                        </button>
                        <button onclick="deleteNote(${note.id})" class="mini-btn delete hover:text-red-500" title="Löschen">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </div>
                ${note.note ? `<p class="note-field mt-1 pl-6 ${isDone}">${escapeHtml(note.note)}</p>` : ''}
                ${note.deadline ? `<div class="pl-6 mt-1 text-[10px] text-red-400 flex items-center gap-1"><i class="ri-calendar-line"></i> ${formatDate(note.deadline)}</div>` : ''}
            `;
            list.appendChild(li);
        });
    }

    // --- Actions ---

    function submitNoteForm(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('noteForm'));
        const id = document.getElementById('noteId').value;
        const url = id ? NOTE_ROUTES.update : NOTE_ROUTES.store;

        // Add ID if update
        if(id) formData.append('id', id);

        postData(url, Object.fromEntries(formData)).then(() => {
            closeNoteModal();
            fetchPersonalNotes();
            showToast(id ? 'Notiz aktualisiert' : 'Notiz erstellt');
        });
    }

    function toggleNoteStatus(id, status) {
        postData(NOTE_ROUTES.update, { id: id, is_done: status ? 1 : 0 }).then(() => {
            fetchPersonalNotes(); // Re-render to update strikethrough styling
        });
    }

    function deleteNote(id) {
        if(!confirm('Wirklich löschen?')) return;
        postData(NOTE_ROUTES.delete, { id: id }).then(() => {
            fetchPersonalNotes();
            showToast('Notiz gelöscht');
        });
    }

    function editNote(id) {
        // Find data from local DOM or fetch specific (DOM is faster for simple fields)
        // For simplicity, we assume we fetch the full list again or just grab text from UI. 
        // Ideally, pass the full object to renderNotes or fetch details. 
        // Here is a simple implementation assuming we have title in UI. 
        // Better approach: fetch fresh or use data attributes.
        
        // Quick fetch to fill form correctly
        const notes = document.querySelectorAll('#personal-note-list li');
        // This part would be better if we stored the full JSON object in a variable `currentNotes`
        // Let's assume fetchPersonalNotes updates a global `cachedNotes` variable (optional optimization).
        // For now, let's just use the title from UI for demo:
        
        const item = document.querySelector(`li[data-id="${id}"]`);
        const title = item.querySelector('.title-field').innerText;
        // const note = item.querySelector('.note-field')?.innerText || ''; 
        
        // Fill form
        document.getElementById('noteId').value = id;
        document.getElementById('noteTitle').value = title;
        document.getElementById('noteModalTitle').innerText = 'Notiz bearbeiten';
        
        // Open
        openNoteModal();
    }

    function saveNoteOrder() {
        const items = document.querySelectorAll('#personal-note-list li');
        const order = Array.from(items).map(item => item.getAttribute('data-id'));
        
        postData(NOTE_ROUTES.reorder, { order: order });
    }

    // --- Categories ---

    function submitCategoryForm(e) {
        e.preventDefault();
        const name = document.getElementById('catName').value;
        const color = document.getElementById('catColor').value;

        postData(NOTE_ROUTES.catStore, { category_name: name, color: color }).then(res => {
            if(res.success) {
                closeCategoryModal();
                fetchPersonalNotes(); // Reloads categories too
                showToast('Kategorie erstellt');
            }
        });
    }

    // --- Helpers ---
    async function postData(url, data) {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify(data)
        });
        return await res.json();
    }

    function debounceNoteSearch() {
        clearTimeout(noteSearchTimeout);
        noteSearchTimeout = setTimeout(fetchPersonalNotes, 400);
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatDate(dateStr) {
        if(!dateStr) return '';
        const d = new Date(dateStr);
        return d.toLocaleDateString('de-DE');
    }

    // --- Modal Toggles ---
    function openNoteModal() {
        document.getElementById('noteForm').reset();
        document.getElementById('noteId').value = '';
        document.getElementById('noteModalTitle').innerText = 'Neue Notiz';
        document.getElementById('noteModal').classList.add('open');
    }
    function closeNoteModal() {
        document.getElementById('noteModal').classList.remove('open');
    }
    function openCategoryModal() {
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryModal').classList.add('open');
    }
    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.remove('open');
    }
</script>
 
  <script>
(() => {
  'use strict';

  const qs  = (s, r=document) => r.querySelector(s);

  const root   = document.documentElement;
  const wrap   = qs('[data-hmenu]');
  const btn    = wrap ? qs('[data-hmenu-btn]', wrap) : null;
  const panel  = wrap ? qs('[data-hmenu-panel]', wrap) : null;
  const label  = wrap ? qs('[data-theme-label]', wrap) : null;

  if (!wrap || !btn || !panel) return;

  function setTheme(mode){
    root.setAttribute('data-theme', mode);
    try { localStorage.setItem('dash_theme', mode); } catch(_) {}
    if (label) label.textContent = (mode === 'dark') ? 'Dunkel' : 'Hell';
  }

  function getTheme(){
    try {
      const v = localStorage.getItem('dash_theme');
      if (v === 'dark' || v === 'light') return v;
    } catch(_) {}
    return 'light';
  }

  setTheme(getTheme());

  function closeMenu(){
    panel.classList.add('hidden');
  }

  function positionPanel(){
    const r = btn.getBoundingClientRect();
    panel.style.top  = Math.round(r.bottom + 8) + 'px';
    panel.style.left = Math.round(r.right - panel.offsetWidth) + 'px';

    // clamp into viewport
    const pad = 10;
    const rect = panel.getBoundingClientRect();
    let left = rect.left;
    let top  = rect.top;

    if (left < pad) left = pad;
    if (left + rect.width > window.innerWidth - pad) left = window.innerWidth - pad - rect.width;
    if (top + rect.height > window.innerHeight - pad) top = Math.max(pad, window.innerHeight - pad - rect.height);

    panel.style.left = Math.round(left) + 'px';
    panel.style.top  = Math.round(top) + 'px';
  }

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    const willOpen = panel.classList.contains('hidden');
    if (!willOpen) return closeMenu();

    panel.classList.remove('hidden');
    // wait one frame so offsetWidth is correct
    requestAnimationFrame(positionPanel);
  });

  document.addEventListener('click', (e) => {
    if (e.target.closest('[data-hmenu]')) return;
    closeMenu();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
  });

  window.addEventListener('resize', () => {
    if (!panel.classList.contains('hidden')) positionPanel();
  }, { passive:true });

  window.addEventListener('scroll', () => {
    if (!panel.classList.contains('hidden')) positionPanel();
  }, { passive:true });

  // Theme click
  panel.addEventListener('click', (e) => {
    const t = e.target.closest('[data-theme-toggle]');
    if (!t) return;
    const cur = root.getAttribute('data-theme') || 'light';
    setTheme(cur === 'dark' ? 'light' : 'dark');
  });

  // Urlaub modal from menu -> reuse your existing modal trigger if present
  const menuHolidayBtn = qs('#btn-open-holiday-modal-menu');
  if (menuHolidayBtn) {
    menuHolidayBtn.addEventListener('click', () => {
      const mainBtn = qs('#btn-open-holiday-modal'); // if you still have it elsewhere
      if (mainBtn) mainBtn.click();
      closeMenu();
    });
  }
})();
</script>





<script>
/**
 * Notes tabs + "expand into focus area" (no fullscreen)
 * Collision-safe: everything scoped, no shared global keys.
 */
(() => {
  'use strict';

  const byId = (id) => document.getElementById(id);

  const notesColumn  = byId('notesColumn');
  const notesCard    = byId('notesCard');
  const expandBtn    = byId('notesExpandBtn');
  const filters      = byId('notesFilters');
  const notesPanel   = byId('notesPanel');
  const calendarPanel= byId('calendarPanel');
  const focusCard    = byId('dueTodayCard');

  if (!notesColumn || !notesCard || !expandBtn || !focusCard) return;

  const grid = focusCard.closest('.grid') || notesColumn.closest('.grid');
  if (!grid) return;

  const tabs = Array.from(notesCard.querySelectorAll('[data-notes-tab]'));

  const st = { expanded:false, tab:'notes' };

  function setTab(nextTab){
    st.tab = (nextTab === 'calendar') ? 'calendar' : 'notes';

    tabs.forEach(btn => {
      const active = btn.getAttribute('data-notes-tab') === st.tab;
      btn.classList.toggle('is-active', active);
      btn.setAttribute('aria-selected', active ? 'true' : 'false');
    });

    if (notesPanel)    notesPanel.classList.toggle('hidden', st.tab !== 'notes');
    if (calendarPanel) calendarPanel.classList.toggle('hidden', st.tab !== 'calendar');
    if (filters)       filters.classList.toggle('hidden', st.tab !== 'notes');
  }

  function setExpanded(force){
    const next = (typeof force === 'boolean') ? force : !st.expanded;
    st.expanded = next;

    grid.classList.toggle('notes-grid--expanded', st.expanded);

    // Update button (icon swap without relying on external libs)
    expandBtn.setAttribute('aria-pressed', st.expanded ? 'true' : 'false');
    expandBtn.title = st.expanded ? 'Zurück' : 'Erweitern';

    expandBtn.innerHTML = st.expanded
      ? `
        <svg viewBox="0 0 24 24" class="notes-ic" aria-hidden="true">
          <path d="M4 10V4h6M20 14v6h-6M14 4h6v6M10 20H4v-6"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>`
      : `
        <svg viewBox="0 0 24 24" class="notes-ic" aria-hidden="true">
          <path d="M8 3H3v5M16 3h5v5M8 21H3v-5M16 21h5v-5"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>`;
  }

  // Wire events
  tabs.forEach(btn => {
    btn.addEventListener('click', () => setTab(btn.getAttribute('data-notes-tab')));
  });

  expandBtn.addEventListener('click', () => setExpanded());

  // Init
  setTab('notes');
})();
</script>
<!-- Add these 3 constants ONCE on the page (Blade) -->
<script>
  window.__PTCAL__ = {
    employeesUrl: `{{ route('get.employees.all') }}`,
    empImgBase:   `{{ asset('images/employee') }}/`,
    fallbackImg:  `{{ asset('images/gender/male.png') }}`
  };
</script>
 
<script>
/**
 * Mini calendar + responsive modal + Select2 multi employees
 * REQUIREMENTS:
 * - jQuery + Select2 loaded on the page
 * - <meta name="csrf-token" content="...">
 */
(() => {
  'use strict';

  // ---------------------------
  // DOM HELPERS
  // ---------------------------
  const $  = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));
  const pad2 = (n) => String(n).padStart(2,'0');

  const toISODate = (d) => `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`;
  const fromISODate = (s) => {
    const [y,m,d] = String(s).split('-').map(Number);
    return new Date(y, (m||1)-1, d||1);
  };

  const eachDayISO = (startISO, endISO) => {
    const a = fromISODate(startISO);
    const b = fromISODate(endISO);
    const out = [];
    const d = new Date(a.getFullYear(), a.getMonth(), a.getDate());
    const end = new Date(b.getFullYear(), b.getMonth(), b.getDate());
    while (d <= end) {
      out.push(toISODate(d));
      d.setDate(d.getDate() + 1);
    }
    return out;
  };

  const escapeHtml = (s) => String(s ?? '')
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,'&#39;');

  const normEmpId = (v) => {
    if (v === null || v === undefined) return null;
    const s = String(v).trim();
    return s === '' ? null : s;
  };

  function getISOWeekNumber(date){
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
  }

  // ---------------------------
  // ROOT ELEMENTS
  // ---------------------------
  const notesCard = $('#notesCard') || document;
  const notesPanel = $('#notesPanel', notesCard);
  const calendarPanel = $('#calendarPanel', notesCard);
  if (!notesPanel || !calendarPanel) return;

  const notesFilters = $('#notesFilters', notesCard);
  const tabs = $$('.notes-tab[data-notes-tab]', notesCard);
  const plusBtn = $('.notes-actions button.btn-primary', notesCard) || $('.notes-actions button', notesCard);

  // ---------------------------
  // URLS / DATA
  // ---------------------------
  const urlEvents     = `{{ route('dashboard.personal.get.calendar') }}`;
  const urlEmployees  = `{{ route('get.employees.all') }}`;
  const urlCreate     = `{{ route('mobile.mobile_calendar.appointments.store') }}`;

  const appointmentBaseUrl = `{{ url('customer/appointments') }}`;
  const EMP_IMG_BASE = `{{ asset('images/employee') }}`;
  const DEFAULT_AVATAR = `{{ asset('images/default-user.png') }}`;
  const CUSTOMERS = @json($customers ?? []);

  const CSRF_TOKEN = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

  // calendar dom
  const gridEl = $('#ptcalGrid', calendarPanel);
  const monthTitleEl = $('#ptcalMonthTitle', calendarPanel);
  const prevBtn = $('#ptcalPrev', calendarPanel);
  const nextBtn = $('#ptcalNext', calendarPanel);
  const dayTitleEl = $('#ptcalDayTitle', calendarPanel);
  const dayMetaEl  = $('#ptcalDayMeta', calendarPanel);
  const dayListEl  = $('#ptcalDayList', calendarPanel);

  // employee multiselect filter (calendar header)
  const empSelect = $('#ptcalEmpSelect', calendarPanel);
  const empBtn = empSelect ? $('.ptcal-emp-btn', empSelect) : null;
  const empPop = empSelect ? $('.ptcal-emp-pop', empSelect) : null;
  const empList = empSelect ? $('#ptcalEmpList', empSelect) : null;
  const empSearch = empSelect ? $('#ptcalEmpSearch', empSelect) : null;
  const empClear = empSelect ? $('#ptcalEmpClear', empSelect) : null;
  const empApply = empSelect ? $('#ptcalEmpApply', empSelect) : null;

  if (!gridEl || !monthTitleEl || !prevBtn || !nextBtn || !dayTitleEl || !dayMetaEl || !dayListEl) return;
  if (!empSelect || !empBtn || !empPop || !empList || !empSearch || !empClear || !empApply) return;

  // ---------------------------
  // STATE
  // ---------------------------
  const st = {
    activeTab: 'notes',
    month: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
    selectedISO: toISODate(new Date()),

    employees: [],            // [{id,name,image,color}]
    empById: new Map(),       // id -> employee
    selectedEmpIds: [],       // calendar filter

    eventsRaw: [],
    eventsByDay: new Map(),
    empIdsByDay: new Map(),

    createSelectedColor: '#164194',
  };

  // ---------------------------
  // TABS
  // ---------------------------
  function setActiveTab(tab){
    st.activeTab = tab === 'calendar' ? 'calendar' : 'notes';

    tabs.forEach(btn => {
      const isActive = btn.getAttribute('data-notes-tab') === st.activeTab;
      btn.classList.toggle('is-active', isActive);
      btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });

    if (st.activeTab === 'calendar') {
      notesPanel.classList.add('hidden');
      calendarPanel.classList.remove('hidden');
      if (notesFilters) notesFilters.classList.add('hidden');
    } else {
      calendarPanel.classList.add('hidden');
      notesPanel.classList.remove('hidden');
      if (notesFilters) notesFilters.classList.remove('hidden');
    }
  }

  function initTabs(){
    const current = tabs.find(t => t.classList.contains('is-active'))?.getAttribute('data-notes-tab') || 'notes';
    setActiveTab(current);

    tabs.forEach(btn => {
      btn.addEventListener('click', () => setActiveTab(btn.getAttribute('data-notes-tab') || 'notes'));
    });

    if (plusBtn) {
      try { plusBtn.removeAttribute('onclick'); } catch (_) {}
      plusBtn.onclick = null;

      plusBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (st.activeTab === 'calendar') openCreateModal();
        else if (typeof window.openNoteModal === 'function') window.openNoteModal();
      }, { capture:true });
    }
  }

  // ---------------------------
  // EMPLOYEES FETCH (with color)
  // ---------------------------
  async function fetchEmployees(){
    try{
      const res = await fetch(urlEmployees, { headers: { 'Accept':'application/json' } });
      const list = await res.json();

      const arr = Array.isArray(list) ? list : [];
      st.employees = arr.map(e => {
        const id = normEmpId(e.emp_id ?? e.id);
        const fullName = [e.name, e.lastname].filter(Boolean).join(' ').trim();
        const img = e.image ? `${EMP_IMG_BASE}/${String(e.image).replace(/^\/+/,'')}` : DEFAULT_AVATAR;
        const color = (e.color && String(e.color).trim()) ? String(e.color).trim() : '#3b82f6';
        return { id, name: fullName || ('#' + id), image: img, color };
      }).filter(e => e.id);

      st.empById = new Map(st.employees.map(e => [String(e.id), e]));

      if (!st.selectedEmpIds.length) {
        const currentEmpId = normEmpId(`{{ auth()->user()->name }}`);
        if (currentEmpId) st.selectedEmpIds = [String(currentEmpId)];
      }

      updateEmpBtnLabel();
      renderEmpList('');
    } catch (_){
      st.employees = [];
      st.empById = new Map();
      renderEmpList('');
      updateEmpBtnLabel();
    }
  }

  function renderEmpList(filterText=''){
    const q = String(filterText||'').toLowerCase().trim();
    const items = st.employees.filter(e => !q || String(e.name).toLowerCase().includes(q));

    if (!items.length){
      empList.innerHTML = `<div class="ptcal-emp-empty">Keine Treffer</div>`;
      return;
    }

    const selected = new Set(st.selectedEmpIds.map(String));

    empList.innerHTML = items.map(e => {
      const checked = selected.has(String(e.id)) ? 'checked' : '';
      return `
        <label class="ptcal-emp-item" data-emp-id="${escapeHtml(e.id)}">
          <input type="checkbox" ${checked}>
          <img class="ptcal-emp-avatar" src="${escapeHtml(e.image)}" alt="">
          <div class="ptcal-emp-name">${escapeHtml(e.name)}</div>
          <span class="ptcal-emp-color" style="background:${escapeHtml(e.color)}"></span>
        </label>
      `;
    }).join('');

    $$('.ptcal-emp-item', empList).forEach(row => {
      row.addEventListener('change', () => {
        const id = row.getAttribute('data-emp-id');
        const cb = $('input', row);
        if (!id || !cb) return;

        const exists = st.selectedEmpIds.map(String).includes(String(id));
        if (cb.checked && !exists) st.selectedEmpIds.push(String(id));
        if (!cb.checked && exists) st.selectedEmpIds = st.selectedEmpIds.filter(x => String(x) !== String(id));
        updateEmpBtnLabel();
      });
    });
  }

  function updateEmpBtnLabel(){
    const label = $('.ptcal-emp-btn-label', empSelect);
    if (!label) return;

    if (!st.selectedEmpIds.length){
      label.textContent = 'Mitarbeiter wählen';
      return;
    }

    const names = st.selectedEmpIds
      .map(id => st.empById.get(String(id))?.name || ('#' + id))
      .slice(0, 2);

    const more = st.selectedEmpIds.length > 2 ? ` +${st.selectedEmpIds.length - 2}` : '';
    label.textContent = names.join(', ') + more;
  }

  function openEmpPop(open){
    const next = (typeof open === 'boolean') ? open : empPop.classList.contains('hidden');
    empPop.classList.toggle('hidden', !next);
    empBtn.setAttribute('aria-expanded', next ? 'true' : 'false');
    if (next) {
      empSearch.value = '';
      renderEmpList('');
      empSearch.focus();
    }
  }

  // ---------------------------
  // EVENTS FETCH + INDEX
  // ---------------------------
  async function fetchEvents(){
    const employee_data = JSON.stringify(
      st.selectedEmpIds.length ? st.selectedEmpIds.map(id => ({ employee_id: id })) : []
    );

    const filter_date = toISODate(st.month);
    const qs = new URLSearchParams({ filter_date, employee_data });

    try {
      dayListEl.innerHTML = `<div class="ptcal-muted">Lade…</div>`;

      const res = await fetch(`${urlEvents}?${qs.toString()}`, {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
      });

      const json = await res.json();
      if (!json || json.success !== true || !Array.isArray(json.data)) {
        st.eventsRaw = [];
        st.eventsByDay = new Map();
        st.empIdsByDay = new Map();
        renderCalendar();
        renderDay(st.selectedISO);
        return;
      }

      st.eventsRaw = json.data;
      indexEventsByDay();
      renderCalendar();
      renderDay(st.selectedISO);
    } catch (_) {
      st.eventsRaw = [];
      st.eventsByDay = new Map();
      st.empIdsByDay = new Map();
      renderCalendar();
      renderDay(st.selectedISO);
      dayListEl.innerHTML = `<div class="ptcal-muted">Fehler beim Laden</div>`;
    }
  }

  function indexEventsByDay(){
    const dayMap = new Map();
    const empDayMap = new Map();

    for (const ev of st.eventsRaw) {
      const startISO = ev.start_date || (ev.start ? String(ev.start).slice(0,10) : null);
      const endISO   = ev.end_date   || startISO;
      if (!startISO) continue;

      const days = eachDayISO(startISO, endISO);

      const ids = new Set();
      if (Array.isArray(ev.employees)) {
        for (const p of ev.employees) {
          const id = normEmpId(p?.employee_id ?? p?.id);
          if (id) ids.add(String(id));
        }
      }
      const cb = normEmpId(ev.created_by);
      const rb = normEmpId(ev.report_by);
      if (cb) ids.add(String(cb));
      if (rb) ids.add(String(rb));

      for (const iso of days) {
        if (!dayMap.has(iso)) dayMap.set(iso, []);
        dayMap.get(iso).push(ev);

        if (!empDayMap.has(iso)) empDayMap.set(iso, new Set());
        const set = empDayMap.get(iso);
        for (const id of ids) set.add(String(id));
      }
    }

    for (const [iso, arr] of dayMap.entries()) {
      arr.sort((a,b) => String(a.start_time||'').localeCompare(String(b.start_time||'')));
    }

    st.eventsByDay = dayMap;
    st.empIdsByDay = empDayMap;
  }

  function haloBgForDay(iso){
    const set = st.empIdsByDay.get(iso);
    if (!set || !set.size) return null;

    const colors = Array.from(set)
      .map(id => st.empById.get(String(id))?.color)
      .filter(Boolean)
      .map(c => String(c).trim())
      .filter(c => c.length)
      .slice(0, 6);

    if (!colors.length) return null;
    if (colors.length === 1) return colors[0];

    const step = 100 / colors.length;
    const parts = colors.map((c,i) => `${c} ${i*step}% ${(i+1)*step}%`);
    return `conic-gradient(${parts.join(',')})`;
  }

  function getEmployeesForDay(iso){
    const set = st.empIdsByDay.get(iso);
    if (!set || !set.size) return [];
    return Array.from(set).map(id => st.empById.get(String(id))).filter(Boolean);
  }

  function removeDayTip(btn){
    const tip = $('.ptcal-daytip', btn);
    if (tip) tip.remove();
  }

  function showDayTip(btn, iso){
    removeDayTip(btn);
    const list = getEmployeesForDay(iso);
    if (!list.length) return;

    const html = `
      <div class="ptcal-daytip" style="position:absolute; left:50%; top:-6px; transform:translate(-50%,-100%);
        background:#0f172a; color:#fff; padding:10px 10px; border-radius:12px; min-width:220px;
        box-shadow:0 18px 40px rgba(2,6,23,.35); z-index:30;">
        <div style="font-weight:800; font-size:12px; margin-bottom:8px;">Mitarbeiter (${list.length})</div>
        <div style="display:flex; flex-direction:column; gap:8px;">
          ${list.map(e => `
            <div style="display:flex; align-items:center; gap:10px;">
              <img src="${escapeHtml(e.image)}" alt="" style="width:26px; height:26px; border-radius:999px; object-fit:cover;">
              <div style="display:flex; align-items:center; gap:8px; min-width:0;">
                <span style="width:10px; height:10px; border-radius:999px; background:${escapeHtml(e.color)}; flex:0 0 auto;"></span>
                <span style="font-size:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                  ${escapeHtml(e.name)}
                </span>
              </div>
            </div>
          `).join('')}
        </div>
        <div style="position:absolute; left:50%; bottom:-6px; transform:translateX(-50%);
          width:0; height:0; border-left:7px solid transparent; border-right:7px solid transparent; border-top:7px solid #0f172a;"></div>
      </div>
    `;
    btn.insertAdjacentHTML('beforeend', html);
  }

  function renderCalendar(){
    const month = st.month;
    const year = month.getFullYear();
    const m = month.getMonth();

    const title = month.toLocaleDateString('de-DE', { month:'long', year:'numeric' });
    monthTitleEl.textContent = title.charAt(0).toUpperCase() + title.slice(1);

    const first = new Date(year, m, 1);
    const firstDow = (first.getDay() + 6) % 7; // 0=Mon..6=Sun
    const start = new Date(year, m, 1 - firstDow);

    const todayISO = toISODate(new Date());
    const selectedISO = st.selectedISO;

    const cells = [];
    for (let i=0; i<42; i++){
      const d = new Date(start.getFullYear(), start.getMonth(), start.getDate() + i);
      const iso = toISODate(d);
      const isOut = d.getMonth() !== m;

      const cls = [
        'ptcal-cell',
        isOut ? 'is-out' : '',
        iso === todayISO ? 'is-today' : '',
        iso === selectedISO ? 'is-selected' : ''
      ].filter(Boolean).join(' ');

      const haloBg = haloBgForDay(iso);
      const haloStyle = haloBg ? `style="--ptcal-halo-bg:${escapeHtml(haloBg)}"` : `style="--ptcal-halo-bg:transparent"`;

      cells.push(`
        <button type="button" class="${cls}" data-iso="${iso}">
          <span class="ptcal-halo" ${haloStyle}></span>
          <div class="ptcal-daynum">${d.getDate()}</div>
        </button>
      `);
    }

    gridEl.innerHTML = cells.join('');

    $$('.ptcal-cell', gridEl).forEach(btn => {
      btn.addEventListener('click', () => {
        const iso = btn.getAttribute('data-iso');
        if (!iso) return;
        st.selectedISO = iso;
        renderCalendar();
        renderDay(iso);
      });

      btn.addEventListener('mouseenter', () => {
        const iso = btn.getAttribute('data-iso');
        if (!iso) return;
        showDayTip(btn, iso);
      });
      btn.addEventListener('mouseleave', () => removeDayTip(btn));
      btn.addEventListener('blur', () => removeDayTip(btn));
    });
  }

  function renderDay(iso){
    const d = fromISODate(iso);
    const weekNo = getISOWeekNumber(d);

    dayTitleEl.textContent =
      d.toLocaleDateString('de-DE', { weekday:'long', day:'2-digit', month:'long', year:'numeric' }) + ` • KW ${weekNo}`;

    const list = st.eventsByDay.get(iso) || [];
    dayMetaEl.textContent = list.length ? `${list.length} Termin(e)` : 'Keine Termine';

    if (!list.length){
      dayListEl.innerHTML = `<div class="ptcal-muted">Keine Termine an diesem Tag</div>`;
      return;
    }

    dayListEl.innerHTML = list.map(ev => {
      const time =
        (ev.start_time ? String(ev.start_time).slice(0,5) : '') +
        (ev.end_time ? `–${String(ev.end_time).slice(0,5)}` : '');

      const customer = ev.customer_name ? `• ${ev.customer_name}` : '';
      const type = ev.appointment_type ? `• ${ev.appointment_type}` : '';

      const employees = Array.isArray(ev.employees) ? ev.employees : [];
      const empAvatars = employees.slice(0,4).map(p => {
        const img = p?.image || DEFAULT_AVATAR;
        const name = p?.name || '';
        return `
          <div class="ptcal-ava" title="${escapeHtml(name)}" style="position:relative;">
            <img src="${escapeHtml(img)}" alt="" style="width:22px;height:22px;border-radius:999px;object-fit:cover;border:2px solid #fff;box-shadow:0 8px 18px rgba(2,6,23,.12);">
          </div>
        `;
      }).join('');

      const moreCount = employees.length > 4 ? (employees.length - 4) : 0;
      const more = moreCount ? `<div class="ptcal-ava-more" style="width:22px;height:22px;border-radius:999px;background:#e2e8f0;
        display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;border:2px solid #fff;box-shadow:0 8px 18px rgba(2,6,23,.12);" title="+${moreCount}">
        +${moreCount}</div>` : '';

      const href = `${appointmentBaseUrl}/${encodeURIComponent(ev.id)}`;
      const barColor = (ev.color && String(ev.color).trim()) ? String(ev.color).trim() : '#cf309b';

      return `
        <a class="ptcal-event" href="${escapeHtml(href)}" style="display:block; text-decoration:none;">
          <div style="display:flex; gap:10px; align-items:flex-start;">
            <div class="ptcal-event-bar" style="background:${escapeHtml(barColor)}; width:4px; border-radius:999px; flex:0 0 auto;"></div>

            <div style="flex:1; min-width:0;">
              <div class="ptcal-event-title" style="font-weight:800; color:#0f172a; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                ${escapeHtml(ev.title || 'Termin')}
              </div>
              <div class="ptcal-event-sub" style="color:#475569; font-size:12px; margin-top:2px;">
                ${escapeHtml([time, customer, type].filter(Boolean).join(' '))}
              </div>
            </div>

            <div class="ptcal-event-emps" style="display:flex; align-items:center; gap:0; justify-content:flex-end; margin-left:8px;">
              ${empAvatars}
              ${more}
            </div>
          </div>
        </a>
      `;
    }).join('');
  }

  // ---------------------------
  // CREATE MODAL (CUSTOM CSS + SELECT2 MULTI)
  // ---------------------------
  const COLOR_PRESETS = ['#164194', '#74b2d4', '#93c21c', '#cfe09b'];

  function getOverlay(){
    let overlay = $('#ptModalOverlay');
    if (!overlay){
      overlay = document.createElement('div');
      overlay.id = 'ptModalOverlay';
      overlay.className = 'pt-modal-overlay';
      document.body.appendChild(overlay);
    }
    return overlay;
  }

  function destroySelect2IfAny(){
    if (!window.jQuery) return;
    const $sel = window.jQuery('#inp-employees');
    if ($sel.length && $sel.data('select2')) {
      $sel.select2('destroy');
    }
  }

  function closeModal(){
    const overlay = getOverlay();
    destroySelect2IfAny();
    overlay.classList.remove('is-open');
    overlay.innerHTML = '';
  }
  window.closeModal = closeModal;

  function openCreateModal(){
    const overlay = getOverlay();
    overlay.classList.add('is-open');

    renderCreateModal();

    // close on click outside
    const onClick = (e) => { if (e.target === overlay) { overlay.removeEventListener('click', onClick); closeModal(); } };
    overlay.addEventListener('click', onClick);

    // esc close
    const onEsc = (e) => { if (e.key === 'Escape') { document.removeEventListener('keydown', onEsc); closeModal(); } };
    document.addEventListener('keydown', onEsc);
  }
  window.openCreateModal = openCreateModal;

  function renderCreateModal(){
    const overlay = getOverlay();

    const customerOptions = (Array.isArray(CUSTOMERS) ? CUSTOMERS : []).map(c => {
      const label = [c.firma, c.lastname, c.name].filter(Boolean).join(' ').trim() || `#${c.id}`;
      return `<option value="${escapeHtml(c.id)}">${escapeHtml(label)}</option>`;
    }).join('');

    const colorsHtml = COLOR_PRESETS.map(c => {
      const active = String(st.createSelectedColor) === String(c) ? 'is-active' : '';
      return `<button type="button" class="pt-color ${active}" data-color="${escapeHtml(c)}" style="background:${escapeHtml(c)}"></button>`;
    }).join('');

    overlay.innerHTML = `
      <div class="pt-modal" role="dialog" aria-modal="true" aria-label="Neuer Termin">
        <div class="pt-modal__header">
          <div class="pt-modal__title">Neuer Termin</div>
          <button type="button" class="pt-modal__close" id="ptCreateCloseBtn" aria-label="Close">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>
            </svg>
          </button>
        </div>

        <div class="pt-modal__body">
          <form id="ptCreateForm" class="pt-form-grid">
            <div>
              <label class="pt-label">Titel</label>
              <input class="pt-input" id="inp-title" type="text" required>
            </div>

            <div>
              <label class="pt-label">Kunde</label>
              <select class="pt-select" id="inp-customer">
                <option value="">-- Kein Kunde --</option>
                ${customerOptions}
              </select>
            </div>

            <div>
              <label class="pt-label">Farbe</label>
              <div class="pt-colors" id="color-container">${colorsHtml}</div>
            </div>

            <div class="pt-row-2">
              <div>
                <label class="pt-label">Start</label>
                <input class="pt-input" id="inp-start" type="time" value="09:00" required>
              </div>
              <div>
                <label class="pt-label">Ende</label>
                <input class="pt-input" id="inp-end" type="time" value="10:00" required>
              </div>
            </div>

            <div>
              <label class="pt-label">Adresse</label>
              <input class="pt-input" id="inp-address" type="text">
            </div>

            <div>
              <label class="pt-label">Mitarbeiter (Mehrfachauswahl)</label>
              <select id="inp-employees" multiple="multiple"></select>
            </div>

            <div>
              <label class="pt-label">Beschreibung</label>
              <textarea class="pt-textarea" id="inp-desc" rows="3"></textarea>
            </div>

            <div style="display:flex; gap:14px; align-items:center; flex-wrap:wrap;">
              <label style="display:flex; gap:10px; align-items:center; font-size:14px; color:#334155; font-weight:700;">
                <input type="checkbox" id="inp-public" style="width:18px; height:18px;"> Öffentlich
              </label>
              <label style="display:flex; gap:10px; align-items:center; font-size:14px; color:#334155; font-weight:700;">
                <input type="checkbox" id="inp-report" style="width:18px; height:18px;"> Bericht
              </label>
            </div>
          </form>
        </div>

        <div class="pt-modal__footer">
          <div class="pt-actions">
            <button type="button" class="pt-btn pt-btn-ghost" id="ptCreateCancelBtn">Abbrechen</button>
            <button type="button" class="pt-btn pt-btn-primary" id="ptCreateSubmitBtn">Erstellen</button>
          </div>
        </div>
      </div>
    `;

    bindCreateModal();
    initEmployeeSelect2();
  }

  function initEmployeeSelect2(){
    if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.select2) {
      console.warn('Select2 not found. Load jQuery + Select2 for multi employee selection.');
      return;
    }

    const overlay = getOverlay();
    const $select = window.jQuery('#inp-employees');

    // populate options (default: calendar filter employees selected)
    const preselected = new Set((st.selectedEmpIds || []).map(String));

    $select.empty();
    for (const e of st.employees) {
      const opt = new Option(e.name, String(e.id), preselected.has(String(e.id)), preselected.has(String(e.id)));
      $select.append(opt);
    }

    $select.select2({
      placeholder: 'Mitarbeiter auswählen',
      width: '100%',
      closeOnSelect: false,
      dropdownParent: window.jQuery(overlay).find('.pt-modal'),
      // render with avatar + color dot
      templateResult: (item) => {
        if (!item.id) return item.text;
        const emp = st.empById.get(String(item.id));
        if (!emp) return item.text;

        const $row = window.jQuery(`
          <div style="display:flex; align-items:center; gap:10px;">
            <img src="${escapeHtml(emp.image)}" style="width:22px; height:22px; border-radius:999px; object-fit:cover; border:1px solid #e2e8f0;">
            <span style="width:10px; height:10px; border-radius:999px; background:${escapeHtml(emp.color)}"></span>
            <span style="font-weight:800; color:#0f172a; font-size:13px;">${escapeHtml(emp.name)}</span>
          </div>
        `);
        return $row;
      },
      templateSelection: (item) => item.text
    });
  }

  function bindCreateModal(){
    const overlay = getOverlay();
    const closeBtn  = $('#ptCreateCloseBtn', overlay);
    const cancelBtn = $('#ptCreateCancelBtn', overlay);
    const submitBtn = $('#ptCreateSubmitBtn', overlay);

    closeBtn && closeBtn.addEventListener('click', closeModal);
    cancelBtn && cancelBtn.addEventListener('click', closeModal);

    // colors
    $$('.pt-color', overlay).forEach(btn => {
      btn.addEventListener('click', () => {
        const c = btn.getAttribute('data-color');
        if (!c) return;
        st.createSelectedColor = c;
        // toggle UI only (no full rerender needed)
        $$('.pt-color', overlay).forEach(x => x.classList.toggle('is-active', x === btn));
      });
    });

    submitBtn && submitBtn.addEventListener('click', submitCreate);

    const form = $('#ptCreateForm', overlay);
    if (form){
      form.addEventListener('submit', (e) => e.preventDefault());
      form.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.target && e.target.tagName !== 'TEXTAREA') {
          e.preventDefault();
          submitCreate();
        }
      });
    }
  }

  async function submitCreate(){
    const overlay = getOverlay();
    const btn = $('#ptCreateSubmitBtn', overlay);
    if (!btn) return;

    const titleEl = $('#inp-title', overlay);
    const startEl = $('#inp-start', overlay);
    const endEl   = $('#inp-end', overlay);

    const title = String(titleEl?.value || '').trim();
    const start_time = String(startEl?.value || '').trim();
    const end_time = String(endEl?.value || '').trim();

    if (!title) { titleEl?.focus(); return; }
    if (!start_time) { startEl?.focus(); return; }
    if (!end_time) { endEl?.focus(); return; }

    // employees from select2
    let attendees = [];
    if (window.jQuery) {
      attendees = (window.jQuery('#inp-employees').val() || []).map(String);
    } else {
      // fallback: read selected <option>
      attendees = $$('#inp-employees option:checked', overlay).map(o => String(o.value));
    }

    btn.disabled = true;
    const old = btn.textContent;
    btn.textContent = 'Speichert...';

    const payload = {
      title,
      description: String($('#inp-desc', overlay)?.value || ''),
      start_date: st.selectedISO,
      start_time,
      end_time,
      address: String($('#inp-address', overlay)?.value || ''),
      customer_id: String($('#inp-customer', overlay)?.value || ''),
      color: st.createSelectedColor,
      public: Boolean($('#inp-public', overlay)?.checked),
      needs_report: Boolean($('#inp-report', overlay)?.checked),
      attendees
    };

    try{
      const res = await fetch(urlCreate, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          ...(CSRF_TOKEN ? { 'X-CSRF-TOKEN': CSRF_TOKEN } : {})
        },
        body: JSON.stringify(payload)
      });

      const json = await res.json().catch(() => ({}));
      if (json && json.success) {
        closeModal();
        await fetchEvents();
      } else {
        const msg = (json && (json.message || json.error)) ? (json.message || json.error) : 'Ein Fehler ist aufgetreten.';
        alert(msg);
      }
    } catch (e){
      console.error(e);
      alert('Ein Fehler ist aufgetreten.');
    } finally {
      btn.disabled = false;
      btn.textContent = old || 'Erstellen';
    }
  }

  // ---------------------------
  // WIRE CALENDAR NAV + FILTER
  // ---------------------------
  prevBtn.addEventListener('click', () => {
    st.month = new Date(st.month.getFullYear(), st.month.getMonth()-1, 1);
    fetchEvents();
  });
  nextBtn.addEventListener('click', () => {
    st.month = new Date(st.month.getFullYear(), st.month.getMonth()+1, 1);
    fetchEvents();
  });

  empBtn.addEventListener('click', () => openEmpPop());
  document.addEventListener('click', (e) => {
    if (!empSelect.contains(e.target)) openEmpPop(false);
  });
  empSearch.addEventListener('input', () => renderEmpList(empSearch.value));
  empClear.addEventListener('click', () => {
    st.selectedEmpIds = [];
    renderEmpList(empSearch.value);
    updateEmpBtnLabel();
  });
  empApply.addEventListener('click', () => {
    openEmpPop(false);
    fetchEvents();
  });

  // ---------------------------
  // INIT
  // ---------------------------
  (async () => {
    initTabs();
    await fetchEmployees();
    renderCalendar();
    renderDay(st.selectedISO);
    await fetchEvents();
  })();
})();
</script>
 

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('toggleOverdue48hBtn');
    const wrapper = document.getElementById('overdue48hWrapper');

    if (!btn || !wrapper) return;

    const statusText = btn.querySelector('.lazy-toggle-status');
    const loadUrl = @json(route('employee.dashboard.overdue48h.partial'));

    async function loadPartialOnce() {
        wrapper.innerHTML = `<div class="lazy-toggle-loader">Lade Inhalt...</div>`;

        const response = await fetch(loadUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) {
            throw new Error(`Partial konnte nicht geladen werden. HTTP ${response.status}`);
        }

        const html = await response.text();
        wrapper.innerHTML = html;
        btn.dataset.loaded = 'true';

        if (typeof window.initOverdue48hWidget === 'function') {
            window.initOverdue48hWidget();
        }
    }

    btn.addEventListener('click', async () => {
        const isOpen = btn.dataset.open === 'true';
        const isLoaded = btn.dataset.loaded === 'true';

        if (isOpen) {
            wrapper.classList.add('hidden');
            btn.dataset.open = 'false';
            btn.classList.remove('is-open');
            if (statusText) statusText.textContent = 'Anzeigen';
            return;
        }

        if (!isLoaded) {
            try {
                await loadPartialOnce();
            } catch (error) {
                console.error(error);
                wrapper.innerHTML = `
                    <div class="lazy-toggle-loader" style="color:#dc2626;">
                        Fehler beim Laden.
                    </div>
                `;
                return;
            }
        }

        wrapper.classList.remove('hidden');
        btn.dataset.open = 'true';
        btn.classList.add('is-open');
        if (statusText) statusText.textContent = 'Ausblenden';
    });
});
</script>

<script>
window.initOverdue48hWidget = function () {
  "use strict";

  /* =========================================================================
     1) BOOT
     ========================================================================= */
  const ROOT = document.getElementById("oc-overdue");
  if (!ROOT) return;

  // prevent double init when panel is opened multiple times
  if (ROOT.dataset.initialized === "true") return;
  ROOT.dataset.initialized = "true";

  const ENDPOINTS = {
    fetch:          @json(route("admin.overdue.fetch")),
    history:        @json(route("admin.overdue.history")),
    reports:        @json(route("admin.overdue.reports.list")),
    store:          @json(route("admin.overdue.reports.store")),
    skip:           @json(route("admin.overdue.skip")),
    bulk:           @json(route("admin.overdue.reports.bulk")),
    reminderUpsert: @json(route("admin.overdue.reminders.upsert")),
    reminderBulk:   @json(route("admin.overdue.reminders.bulk")),
  };

  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  /* =========================================================================
     2) HELPERS
     ========================================================================= */
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  const esc = (s) => String(s ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");

  const debounce = (fn, ms = 250) => {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };

  const fmtDT = (s) => {
    if (!s) return "—";
    try {
      const d = new Date(String(s).replace(" ", "T"));
      return d.toLocaleString();
    } catch (_) {
      return String(s);
    }
  };

  function parseLaravel422(rawText) {
    try {
      const j = JSON.parse(rawText || "{}");
      const msg = j.message || "Validierung fehlgeschlagen.";
      if (j.errors && typeof j.errors === "object") {
        const lines = Object.values(j.errors).flat();
        return { message: msg, details: lines.join("\n") };
      }
      return { message: msg, details: "" };
    } catch (_) {
      return { message: "Fehler", details: String(rawText || "").slice(0, 500) };
    }
  }

  /* =========================================================================
     3) DOM CACHE
     ========================================================================= */
  const DOM = {
    list: $("#oc-list", ROOT),
    stats: $("#oc-stats", ROOT),

    refresh: $("#oc-refresh", ROOT),
    prev: $("#oc-prev", ROOT),
    next: $("#oc-next", ROOT),
    page: $("#oc-page", ROOT),
    search: $("#oc-search", ROOT),
    sort: $("#oc-sort", ROOT),
    perpage: $("#oc-perpage", ROOT),
    typeChecks: $$(".oc-type", ROOT),

    toastWrap: $("#oc-toast-wrap", ROOT) || document.getElementById("oc-toast-wrap"),

    // Drawer
    backdrop: $("#oc-backdrop", ROOT) || document.getElementById("oc-backdrop"),
    dClose: $("#oc-dclose", ROOT) || document.getElementById("oc-dclose"),
    dTitle: $("#oc-dtitle", ROOT) || document.getElementById("oc-dtitle"),
    dSub: $("#oc-dsub", ROOT) || document.getElementById("oc-dsub"),
    dBody: $("#oc-dbody", ROOT) || document.getElementById("oc-dbody"),
    reportBox: $("#oc-reportbox", ROOT) || document.getElementById("oc-reportbox"),
    reportText: $("#oc-report", ROOT) || document.getElementById("oc-report"),
    save: $("#oc-save", ROOT) || document.getElementById("oc-save"),

    // Skip Modal
    skipBD: $("#oc-skip-modal", ROOT) || document.getElementById("oc-skip-modal"),
    skipOK: $("#oc-skip-confirm", ROOT) || document.getElementById("oc-skip-confirm"),
    skipNO: $("#oc-skip-cancel", ROOT) || document.getElementById("oc-skip-cancel"),
    skipX: $("#oc-skip-close", ROOT) || document.getElementById("oc-skip-close"),
    skipErr: $("#oc-skip-error", ROOT) || document.getElementById("oc-skip-error"),
    skipTpl: $("#oc-skip-template", ROOT) || document.getElementById("oc-skip-template"),
    skipTxt: $("#oc-skip-reason", ROOT) || document.getElementById("oc-skip-reason"),
    skipSub: $("#oc-skip-sub", ROOT) || document.getElementById("oc-skip-sub"),
    skipMeta: $("#oc-skip-meta", ROOT) || document.getElementById("oc-skip-meta"),

    // Title Modal
    titleBD: $("#oc-title-modal", ROOT) || document.getElementById("oc-title-modal"),
    titleX: $("#oc-title-modal-close", ROOT) || document.getElementById("oc-title-modal-close"),
    titleOK: $("#oc-title-modal-ok", ROOT) || document.getElementById("oc-title-modal-ok"),
    titleTTL: $("#oc-title-modal-title", ROOT) || document.getElementById("oc-title-modal-title"),
    titleSub: $("#oc-title-modal-sub", ROOT) || document.getElementById("oc-title-modal-sub"),
    titleBody: $("#oc-title-modal-body", ROOT) || document.getElementById("oc-title-modal-body"),

    // Bulk
    selectAll: $("#oc-select-all", ROOT),
    bulkBar: $("#oc-bulk-bar", ROOT) || document.getElementById("oc-bulk-bar"),
    bulkCount: $("#oc-bulk-count", ROOT) || document.getElementById("oc-bulk-count"),
    bulkBtn: $("#oc-bulk-report-btn", ROOT) || document.getElementById("oc-bulk-report-btn"),
    bulkCancel: $("#oc-bulk-cancel", ROOT) || document.getElementById("oc-bulk-cancel"),
    bulkBackdrop: $("#oc-bulk-backdrop", ROOT) || document.getElementById("oc-bulk-backdrop"),
    bulkDClose: $("#oc-bulk-dclose", ROOT) || document.getElementById("oc-bulk-dclose"),
    bulkSave: $("#oc-bulk-save", ROOT) || document.getElementById("oc-bulk-save"),
    bulkText: $("#oc-bulk-report-text", ROOT) || document.getElementById("oc-bulk-report-text"),
    bulkListPreview: $("#oc-bulk-list-preview", ROOT) || document.getElementById("oc-bulk-list-preview"),
    bulkReminderBtn: $("#oc-bulk-reminder-btn", ROOT) || document.getElementById("oc-bulk-reminder-btn"),

    // Reminder Modal
    reminderBD: $("#oc-reminder-modal", ROOT) || document.getElementById("oc-reminder-modal"),
    reminderX: $("#oc-reminder-close", ROOT) || document.getElementById("oc-reminder-close"),
    reminderCancel: $("#oc-reminder-cancel", ROOT) || document.getElementById("oc-reminder-cancel"),
    reminderSave: $("#oc-reminder-save", ROOT) || document.getElementById("oc-reminder-save"),
    reminderSub: $("#oc-reminder-sub", ROOT) || document.getElementById("oc-reminder-sub"),
    reminderPreset: $("#oc-reminder-preset", ROOT) || document.getElementById("oc-reminder-preset"),
    reminderCustomWrap: $("#oc-reminder-custom-wrap", ROOT) || document.getElementById("oc-reminder-custom-wrap"),
    reminderDT: $("#oc-reminder-dt", ROOT) || document.getElementById("oc-reminder-dt"),
    reminderNote: $("#oc-reminder-note", ROOT) || document.getElementById("oc-reminder-note"),
    reminderErr: $("#oc-reminder-error", ROOT) || document.getElementById("oc-reminder-error"),
  };

  /* =========================================================================
     4) STATE
     ========================================================================= */
  const DEFAULT_TYPES = ["inquiry", "task", "appointment", "ticket", "lead"];

  const state = {
    page: 1,
    per_page: parseInt(DOM.perpage?.value || "48", 10) || 48,
    q: "",
    sort: DOM.sort?.value || "oldest",
    types: DOM.typeChecks.filter(x => x.checked).map(x => x.value),
    last: [],
    active: null,
    skipTarget: null,
    reminderTarget: null,
  };

  if (!state.types.length) {
    state.types = DEFAULT_TYPES.slice();
  }

  /* =========================================================================
     5) NETWORK
     ========================================================================= */
  const controllers = new Map();

  function abortKey(key) {
    const c = controllers.get(key);
    if (c) {
      try { c.abort(); } catch (_) {}
      controllers.delete(key);
    }
  }

  async function api(key, url, data = {}, method = "GET") {
    abortKey(key);

    const controller = new AbortController();
    controllers.set(key, controller);

    const headers = {
      "Accept": "application/json",
      "X-Requested-With": "XMLHttpRequest"
    };

    const opts = { method, headers, signal: controller.signal };
    let finalUrl = url;

    if (method === "GET") {
      const payload = { ...data };
      if (Array.isArray(payload.types)) payload.types = payload.types.join(",");
      const qs = new URLSearchParams(payload).toString();
      if (qs) finalUrl += (finalUrl.includes("?") ? "&" : "?") + qs;
    } else {
      headers["Content-Type"] = "application/json";
      if (CSRF) headers["X-CSRF-TOKEN"] = CSRF;
      opts.body = JSON.stringify(data);
    }

    let res, txt = "";
    try {
      res = await fetch(finalUrl, opts);
      txt = await res.text().catch(() => "");
    } finally {
      if (controllers.get(key) === controller) controllers.delete(key);
    }

    if (!res.ok) {
      const err = new Error(`HTTP ${res.status}`);
      err.status = res.status;
      err.raw = txt;
      throw err;
    }

    try { return JSON.parse(txt || "{}"); } catch (_) { return {}; }
  }

  /* =========================================================================
     6) TOAST
     ========================================================================= */
  function toast(kind, title, msg, ttlMs = 3200) {
    const wrap = DOM.toastWrap;
    if (!wrap) return;

    const icons = {
      ok:   `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
      warn: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
      bad:  `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 18L18 6M6 6l12 12"/></svg>`
    };

    const el = document.createElement("div");
    el.className = "oc-toast";
    el.innerHTML = `
      <div class="oc-toast-ic ${esc(kind)}">${icons[kind] || icons.bad}</div>
      <div style="min-width:0">
        <p class="oc-toast-ttl">${esc(title || "")}</p>
        <p class="oc-toast-msg">${esc(msg || "")}</p>
      </div>
      <button class="oc-btn-ic oc-toast-x" type="button" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    `;

    wrap.appendChild(el);

    const kill = () => { try { el.remove(); } catch (_) {} };
    el.querySelector(".oc-toast-x")?.addEventListener("click", kill);
    setTimeout(kill, ttlMs);
  }

  /* =========================================================================
     7) LABELS / ICONS / FORMAT
     ========================================================================= */
  const typeLabel = (t) => ({
    inquiry: "Anfrage",
    task: "Aufgabe",
    appointment: "Termin",
    ticket: "Ticket",
    lead: "Lead",
  }[t] || t);

  const iconSvg = (type) => {
    const map = {
      inquiry: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 10h.01M12 10h.01M16 10h.01"/><path d="M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>`,
      task: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><path d="M9 5a2 2 0 002 2h2a2 2 0 002-2"/><path d="M9 5a2 2 0 012-2h2a2 2 0 012 2"/><path d="M9 14l2 2 4-4"/></svg>`,
      appointment: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3"/><path d="M5 11h14"/><path d="M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`,
      ticket: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/><path d="M15 7v2m0 4v2m0 4v2"/></svg>`,
      lead: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>`,
    };
    return map[type] || map.inquiry;
  };

  const statusTone = (v) => {
    const k = String(v ?? "").toLowerCase().trim();
    if (["done","completed","complete","closed","verified","approved","success","won","published","active"].includes(k)) return "ok";
    if (["pending","in_progress","progress","working","running","paused","on_hold"].includes(k)) return "warn";
    if (["rejected","declined","failed","lost","cancel","canceled"].includes(k)) return "bad";
    return "";
  };

  const tStatusComposite = (v) => {
    const raw = String(v ?? "").trim();
    if (!raw) return "—";
    return raw.split("•").map(p => {
      const s = p.trim();
      return s ? (s.charAt(0).toUpperCase() + s.slice(1)) : "";
    }).filter(Boolean).join(" • ");
  };

  const recordTitle = (i) => i.task_title || i.record_title || i.title || i.name || i.subject || "—";
  const recordSubtitle = (i) => i.description || i.subtitle || "";

  const severity = (h) => (h >= 72 ? "od-high" : (h >= 48 ? "od-med" : "od-ok"));

  const formatOverdue = (h) => {
    const hours = Math.max(0, Math.floor(Number(h || 0)));
    const d = Math.floor(hours / 24);
    const r = hours % 24;
    return d > 0 ? `${d}T ${r}Std.` : `${hours} Std.`;
  };

  const reminderBadge = (rem) => {
    if (!rem) return "";
    const st = String(rem.status || "active");
    const at = rem.next_remind_at ? fmtDT(rem.next_remind_at) : "—";
    const tone = (st === "done" || st === "canceled") ? "ok" : (st === "snoozed" ? "warn" : "");
    return `<span class="oc-tag ${tone}" title="Reminder">${esc(st)} • ${esc(at)}</span>`;
  };

  /* =========================================================================
     8) TITLE MODAL (TRUNCATED TEXT)
     ========================================================================= */
  function openTitleModal(title, subtitle, body) {
    if (DOM.titleTTL) DOM.titleTTL.textContent = title || "Details";
    if (DOM.titleSub) DOM.titleSub.textContent = subtitle || "";
    if (DOM.titleBody) DOM.titleBody.textContent = body || "";
    DOM.titleBD?.classList.add("open");
  }

  function closeTitleModal() {
    DOM.titleBD?.classList.remove("open");
  }

  DOM.titleX?.addEventListener("click", closeTitleModal);
  DOM.titleOK?.addEventListener("click", closeTitleModal);
  DOM.titleBD?.addEventListener("click", (e) => { if (e.target === DOM.titleBD) closeTitleModal(); });

  /* =========================================================================
     9) BULK BAR
     ========================================================================= */
  function updateBulkBar() {
    const selected = $$(".oc-item-cb:checked", ROOT);
    const count = selected.length;

    if (DOM.bulkCount) {
      DOM.bulkCount.textContent = `${count} ausgewählt`;
    }

    if (DOM.bulkBar) {
      DOM.bulkBar.classList.toggle("open", count > 0);
    }
  }

  function clearBulkSelection() {
    if (DOM.selectAll) DOM.selectAll.checked = false;
    $$(".oc-item-cb", ROOT).forEach(cb => {
      cb.checked = false;
      cb.closest(".oc-item")?.classList.remove("selected");
    });
    updateBulkBar();
  }

  DOM.selectAll?.addEventListener("change", (e) => {
    const on = !!e.target.checked;
    $$(".oc-item-cb", ROOT).forEach(cb => {
      cb.checked = on;
      cb.closest(".oc-item")?.classList.toggle("selected", on);
    });
    updateBulkBar();
  });

  DOM.bulkCancel?.addEventListener("click", clearBulkSelection);

  DOM.bulkBtn?.addEventListener("click", () => {
    const selected = $$(".oc-item-cb:checked", ROOT);
    if (!selected.length) return;

    const names = selected.slice(0, 3).map(cb => cb.dataset.title || "—").join(", ");
    const remaining = selected.length - 3;

    if (DOM.bulkListPreview) {
      DOM.bulkListPreview.textContent = `Betrifft: ${names}${remaining > 0 ? " und " + remaining + " weitere" : ""}`;
    }

    DOM.bulkBackdrop?.classList.add("open");
  });

  DOM.bulkDClose?.addEventListener("click", () => DOM.bulkBackdrop?.classList.remove("open"));
  DOM.bulkBackdrop?.addEventListener("click", (e) => {
    if (e.target === DOM.bulkBackdrop) DOM.bulkBackdrop.classList.remove("open");
  });

  DOM.bulkSave?.addEventListener("click", async () => {
    const text = (DOM.bulkText?.value || "").trim();
    if (!text || text.length < 3) return toast("warn", "Hinweis", "Bericht ist zu kurz (mind. 3 Zeichen).");

    const items = $$(".oc-item-cb:checked", ROOT).map(cb => ({
      id: Number(cb.dataset.id),
      type: String(cb.dataset.type || ""),
    })).filter(x => x.id > 0 && x.type);

    if (!items.length) return;

    DOM.bulkSave.disabled = true;
    const oldTxt = DOM.bulkSave.textContent;
    DOM.bulkSave.textContent = "Speichere...";

    try {
      await api("bulk", ENDPOINTS.bulk, { items, report: text }, "POST");
      toast("ok", "Erfolg", `${items.length} Berichte erfolgreich gespeichert.`);

      DOM.bulkBackdrop?.classList.remove("open");
      if (DOM.bulkText) DOM.bulkText.value = "";
      clearBulkSelection();

      await load();
    } catch (e) {
      if (e?.status === 422) {
        const info = parseLaravel422(e.raw);
        toast("bad", "Validierungsfehler", info.details || info.message);
      } else {
        toast("bad", "Fehler", "Sammelbericht konnte nicht gespeichert werden.");
      }
    } finally {
      DOM.bulkSave.disabled = false;
      DOM.bulkSave.textContent = oldTxt;
    }
  });

  /* =========================================================================
     10) REMINDER MODAL
     ========================================================================= */
  function openReminderModalSingle(type, id, title) {
    state.reminderTarget = {
      mode: "single",
      items: [{ type, id, title }],
      title: `${typeLabel(type)} • ${title || ""}`
    };

    if (DOM.reminderSub) DOM.reminderSub.textContent = state.reminderTarget.title;
    if (DOM.reminderErr) DOM.reminderErr.classList.add("hidden");
    if (DOM.reminderNote) DOM.reminderNote.value = "";
    if (DOM.reminderPreset) DOM.reminderPreset.value = "120";
    if (DOM.reminderCustomWrap) DOM.reminderCustomWrap.classList.add("hidden");
    if (DOM.reminderDT) DOM.reminderDT.value = "";

    DOM.reminderBD?.classList.add("open");
  }

  function openReminderModalBulk(items) {
    const names = items.slice(0, 3).map(x => x.title || "—").join(", ");
    const more = items.length - 3;

    state.reminderTarget = {
      mode: "bulk",
      items,
      title: `Bulk • ${names}${more > 0 ? " und " + more + " weitere" : ""}`
    };

    if (DOM.reminderSub) DOM.reminderSub.textContent = state.reminderTarget.title;
    if (DOM.reminderErr) DOM.reminderErr.classList.add("hidden");
    if (DOM.reminderNote) DOM.reminderNote.value = "";
    if (DOM.reminderPreset) DOM.reminderPreset.value = "120";
    if (DOM.reminderCustomWrap) DOM.reminderCustomWrap.classList.add("hidden");
    if (DOM.reminderDT) DOM.reminderDT.value = "";

    DOM.reminderBD?.classList.add("open");
  }

  function closeReminderModal() {
    DOM.reminderBD?.classList.remove("open");
  }

  function presetToPayload(preset) {
    const now = new Date();
    const mk = (d) => d.toISOString();

    if (/^\d+$/.test(preset)) {
      return { minutes: parseInt(preset, 10), next_remind_at: null };
    }

    if (preset === "tomorrow_09") {
      const d = new Date(now);
      d.setDate(d.getDate() + 1);
      d.setHours(9, 0, 0, 0);
      return { minutes: null, next_remind_at: mk(d) };
    }

    if (preset === "next_week_09") {
      const d = new Date(now);
      d.setDate(d.getDate() + 7);
      d.setHours(9, 0, 0, 0);
      return { minutes: null, next_remind_at: mk(d) };
    }

    return { minutes: null, next_remind_at: null };
  }

  DOM.reminderPreset?.addEventListener("change", (e) => {
    const v = e.target.value;
    DOM.reminderCustomWrap?.classList.toggle("hidden", v !== "custom");
  });

  DOM.reminderX?.addEventListener("click", closeReminderModal);
  DOM.reminderCancel?.addEventListener("click", closeReminderModal);
  DOM.reminderBD?.addEventListener("click", (e) => { if (e.target === DOM.reminderBD) closeReminderModal(); });

  DOM.reminderSave?.addEventListener("click", async () => {
    const target = state.reminderTarget;
    if (!target?.items?.length) return;

    if (DOM.reminderErr) DOM.reminderErr.classList.add("hidden");

    const preset = DOM.reminderPreset?.value || "120";
    const note = (DOM.reminderNote?.value || "").trim();

    let payload = presetToPayload(preset);

    if (preset === "custom") {
      const dt = DOM.reminderDT?.value;
      if (!dt) {
        if (DOM.reminderErr) {
          DOM.reminderErr.textContent = "Bitte Datum/Uhrzeit auswählen.";
          DOM.reminderErr.classList.remove("hidden");
        }
        return;
      }
      payload = { minutes: null, next_remind_at: new Date(dt).toISOString() };
    }

    DOM.reminderSave.disabled = true;
    const oldTxt = DOM.reminderSave.textContent;
    DOM.reminderSave.textContent = "Speichere...";

    try {
      if (target.mode === "single") {
        const it = target.items[0];
        await api("reminder_upsert", ENDPOINTS.reminderUpsert, {
          type: it.type,
          id: it.id,
          minutes: payload.minutes,
          next_remind_at: payload.next_remind_at,
          note
        }, "POST");
        toast("ok", "Reminder gesetzt", "Erinnerung gespeichert.");
      } else {
        await api("reminder_bulk", ENDPOINTS.reminderBulk, {
          items: target.items.map(x => ({ type: x.type, id: x.id })),
          minutes: payload.minutes,
          next_remind_at: payload.next_remind_at,
          note
        }, "POST");
        toast("ok", "Bulk Reminder", `${target.items.length} Erinnerungen gespeichert.`);
      }

      closeReminderModal();
      await load();
    } catch (e) {
      if (e?.status === 422) {
        const info = parseLaravel422(e.raw);
        if (DOM.reminderErr) {
          DOM.reminderErr.textContent = info.details || info.message;
          DOM.reminderErr.classList.remove("hidden");
        }
      } else {
        if (DOM.reminderErr) {
          DOM.reminderErr.textContent = "Fehler beim Speichern.";
          DOM.reminderErr.classList.remove("hidden");
        }
      }
    } finally {
      DOM.reminderSave.disabled = false;
      DOM.reminderSave.textContent = oldTxt;
    }
  });

  DOM.bulkReminderBtn?.addEventListener("click", () => {
    const items = $$(".oc-item-cb:checked", ROOT).map(cb => ({
      type: String(cb.dataset.type || ""),
      id: Number(cb.dataset.id || 0),
      title: String(cb.dataset.title || "")
    })).filter(x => x.type && x.id > 0);

    if (!items.length) return;
    openReminderModalBulk(items);
  });

  /* =========================================================================
     11) RENDERING
     ========================================================================= */
  function renderStats(s) {
    if (!DOM.stats) return;
    const conf = [
      { k: "total", l: "Gesamt", c: "#111827" },
      { k: "inquiry", l: "Anfragen", c: "var(--primary)" },
      { k: "task", l: "Aufgaben", c: "var(--success)" },
      { k: "appointment", l: "Termine", c: "#0ea5e9" },
      { k: "ticket", l: "Tickets", c: "var(--warning)" },
      { k: "lead", l: "Leads", c: "#9333ea" },
    ];

    DOM.stats.innerHTML = conf.map(x => `
      <div class="oc-stat">
        <div class="oc-stat-l"><span class="oc-dot" style="background:${x.c}"></span>${x.l}</div>
        <div class="oc-stat-v">${Number(s?.[x.k] ?? 0)}</div>
      </div>
    `).join("");
  }

  function setPager(p) {
    if (!DOM.page || !DOM.prev || !DOM.next) return;

    const page = Number(p?.page ?? state.page);
    const per = Number(p?.per_page ?? state.per_page);
    const total = Number(p?.total ?? 0);
    const pages = Math.max(1, Math.ceil(total / Math.max(1, per)));

    DOM.page.textContent = `Seite ${page} von ${pages}`;
    DOM.prev.disabled = page <= 1;
    DOM.next.disabled = !p?.has_more;
  }

  function renderReports(rows) {
    if (!rows?.length) return `<div style="padding:12px;color:var(--text-muted)">Keine Berichte vorhanden.</div>`;
    return `<div class="oc-tl">${
      rows.map(r => `
        <div class="oc-tli">
          <div class="oc-tld"></div>
          <div class="oc-tldt">${esc(r.created_at || "—")}</div>
          <div style="font-weight:900;font-size:14px;margin-bottom:4px;color:#111827">${esc(r.employee_name || "—")}</div>
          <div class="oc-tlc">${esc(r.report || "")}</div>
        </div>
      `).join("")
    }</div>`;
  }

  function renderHistory(type, res) {
    const h = res?.history || {};
    let html = `<div class="oc-tl">`;
    const changes = Array.isArray(h.changes) ? h.changes : [];
    if (!changes.length) {
      html += `<div style="padding:12px;color:var(--text-muted)">Kein Verlauf vorhanden.</div>`;
    } else {
      html += changes.map(c => `
        <div class="oc-tli">
          <div class="oc-tld"></div>
          <div class="oc-tldt">${esc(c.created_at || "—")}</div>
          <div style="font-weight:900;font-size:14px;margin-bottom:4px;color:#111827">${esc(c.title || c.status || "Änderung")}</div>
          ${c.note ? `<div class="oc-tlc">${esc(c.note)}</div>` : ``}
        </div>
      `).join("");
    }
    html += `</div>`;
    return html;
  }

  function renderList(items) {
    if (!DOM.list) return;

    if (!items?.length) {
      DOM.list.innerHTML = `<div style="text-align:center;padding:60px;color:var(--text-muted)">Alles erledigt! Keine überfälligen Einträge.</div>`;
      return;
    }

    DOM.list.innerHTML = items.map(item => {
      const type = String(item.type || "").trim();
      const safeId = Number(item.id || 0);

      const title = recordTitle(item);
      const subtitle = recordSubtitle(item);

      const stDe = tStatusComposite(item.status);
      const stTone = statusTone(item.status);

      const sev = severity(item.overdue_hours);
      const timeTxt = formatOverdue(item.overdue_hours);

      const url = item.link || "#";

      const rem = item.reminder || null;
      const showRemindBtn = !rem;

      const summary = String(item.changed_summary || "—");
      const summaryShort = summary.length > 120 ? summary.slice(0, 120) + "…" : summary;
      const summaryIsLong = summary.length > 120;

      return `
        <div class="oc-item" data-id="${safeId}" data-type="${esc(type)}">
          <div class="oc-checkbox-wrap">
            <input type="checkbox" class="oc-item-cb oc-cb-custom"
              data-id="${safeId}"
              data-type="${esc(type)}"
              data-title="${esc(title)}">
          </div>

          <div class="oc-ic type-${esc(type)}" title="${esc(typeLabel(type))}">
            ${iconSvg(type)}
          </div>

          <div class="oc-main">
            <div class="oc-ttl">${esc(title)}</div>
            <div class="oc-subt">${esc(subtitle)}</div>

            <div class="oc-sum" ${summaryIsLong ? `data-oc-action="open_title" data-oc-title="Zusammenfassung" data-oc-sub="${esc(typeLabel(type))} • ${esc(title)}" data-oc-body="${esc(summary)}"` : ""} style="${summaryIsLong ? "cursor:pointer" : ""}">
              ${esc(summaryShort)}
            </div>
          </div>

          <div class="oc-meta">
            <span class="oc-tag ${stTone}">${esc(stDe)}</span>
            ${reminderBadge(rem)}
          </div>

          <div class="oc-time">
            <span class="oc-od ${sev}">
              <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 8v4l3 3"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              ${esc(timeTxt)}
            </span>
            <span style="font-size:11px;color:var(--text-muted)">Zuletzt: ${esc(item.last_activity_at || "—")}</span>
          </div>

          <div class="oc-actions">
            <a href="${esc(url)}" target="_blank" class="oc-btn-ic primary" title="Öffnen" rel="noopener">
              <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"/>
                <path d="M14 4h6m0 0v6m0-6L10 14"/>
              </svg>
            </a>

            ${showRemindBtn ? `
              <button class="oc-btn-ic" type="button"
                data-oc-action="remind"
                data-oc-type="${esc(type)}"
                data-oc-id="${safeId}"
                data-oc-title="${esc(title)}"
                title="Remind me later">
                <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/>
                  <path d="M9 17a3 3 0 0 0 6 0"/>
                </svg>
              </button>
            ` : ``}

            <button class="oc-btn-ic" type="button"
              data-oc-action="reports"
              data-oc-type="${esc(type)}"
              data-oc-id="${safeId}"
              data-oc-title="${esc(title)}"
              title="Berichte">
              <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 12h6m-6 4h6"/><path d="M7 3h6l4 4v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
              </svg>
            </button>

            <button class="oc-btn-ic" type="button"
              data-oc-action="history"
              data-oc-type="${esc(type)}"
              data-oc-id="${safeId}"
              data-oc-title="${esc(title)}"
              title="Verlauf">
              <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 3v5h5"/><path d="M3.2 12a9 9 0 1 0 2.6-6.4"/><path d="M12 7v5l3 3"/>
              </svg>
            </button>

            <button class="oc-btn-ic danger" type="button"
              data-oc-action="skip"
              data-oc-type="${esc(type)}"
              data-oc-id="${safeId}"
              data-oc-title="${esc(title)}"
              title="Überspringen">
              <svg style="width:18px;height:18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h12"/><path d="M13 6l6 6-6 6"/>
              </svg>
            </button>
          </div>
        </div>
      `;
    }).join("");

    updateBulkBar();
  }

  /* =========================================================================
     12) CORE LOAD
     ========================================================================= */
  async function load() {
    if (DOM.list) DOM.list.innerHTML = `<div style="text-align:center;padding:60px;color:var(--text-muted)">Lädt...</div>`;

    if (DOM.selectAll) DOM.selectAll.checked = false;
    updateBulkBar();

    try {
      const data = await api("fetch", ENDPOINTS.fetch, {
        q: state.q,
        sort: state.sort,
        page: state.page,
        per_page: state.per_page,
        types: state.types?.length ? state.types : DEFAULT_TYPES,
      }, "GET");

      state.last = Array.isArray(data?.items) ? data.items : [];
      renderStats(data?.stats || {});
      renderList(state.last);
      setPager(data?.pagination || {});
    } catch (e) {
      if (DOM.list) DOM.list.innerHTML = `<div style="text-align:center;padding:60px;color:var(--danger)">Fehler beim Laden.</div>`;
      toast("bad", "Fehler", "Daten konnten nicht geladen werden.");
    }
  }

  /* =========================================================================
     13) TOOLBAR EVENTS
     ========================================================================= */
  DOM.refresh?.addEventListener("click", () => load());

  DOM.prev?.addEventListener("click", () => {
    state.page = Math.max(1, state.page - 1);
    load();
  });

  DOM.next?.addEventListener("click", () => {
    state.page += 1;
    load();
  });

  DOM.search?.addEventListener("input", debounce((e) => {
    state.q = e.target.value || "";
    state.page = 1;
    load();
  }, 300));

  DOM.sort?.addEventListener("change", (e) => {
    state.sort = e.target.value || "oldest";
    state.page = 1;
    load();
  });

  DOM.perpage?.addEventListener("change", (e) => {
    state.per_page = parseInt(e.target.value, 10) || 48;
    state.page = 1;
    load();
  });

  DOM.typeChecks.forEach(cb => cb.addEventListener("change", () => {
    state.types = DOM.typeChecks.filter(x => x.checked).map(x => x.value);
    state.page = 1;
    load();
  }));

  /* =========================================================================
     14) LIST EVENTS
     ========================================================================= */
  DOM.list?.addEventListener("change", (e) => {
    if (!e.target.classList.contains("oc-item-cb")) return;

    const row = e.target.closest(".oc-item");
    row?.classList.toggle("selected", !!e.target.checked);

    if (!e.target.checked && DOM.selectAll) {
      DOM.selectAll.checked = false;
    }

    updateBulkBar();
  });

  DOM.list?.addEventListener("click", async (e) => {
    const btn = e.target.closest("[data-oc-action]");
    if (!btn) return;

    const action = btn.dataset.ocAction;

    if (action === "open_title") {
      openTitleModal(btn.dataset.ocTitle, btn.dataset.ocSub, btn.dataset.ocBody);
      return;
    }

    const type = btn.dataset.ocType;
    const id = Number(btn.dataset.ocId || 0);
    const title = btn.dataset.ocTitle || "";

    if (!action || !type || !id) return;

    state.active = { type, id, title };

    if (action === "reports") {
      if (DOM.dTitle) DOM.dTitle.textContent = "Berichte";
      if (DOM.dSub) DOM.dSub.textContent = `${typeLabel(type)} • ${title}`;
      DOM.backdrop?.classList.add("open");
      DOM.reportBox?.classList.remove("hidden");
      if (DOM.reportText) DOM.reportText.value = "";

      try {
        const res = await api("reports", ENDPOINTS.reports, { type, id }, "GET");
        if (DOM.dBody) DOM.dBody.innerHTML = renderReports(res?.rows || []);
      } catch (_) {
        toast("bad", "Fehler", "Berichte konnten nicht geladen werden.");
      }
      return;
    }

    if (action === "history") {
      if (DOM.dTitle) DOM.dTitle.textContent = "Verlauf";
      if (DOM.dSub) DOM.dSub.textContent = `${typeLabel(type)} • ${title}`;
      DOM.backdrop?.classList.add("open");
      DOM.reportBox?.classList.add("hidden");

      try {
        const res = await api("history", ENDPOINTS.history, { type, id }, "GET");
        if (DOM.dBody) DOM.dBody.innerHTML = renderHistory(type, res);
      } catch (_) {
        toast("bad", "Fehler", "Verlauf konnte nicht geladen werden.");
      }
      return;
    }

    if (action === "skip") {
      state.skipTarget = { type, id, title };

      if (DOM.skipSub) DOM.skipSub.textContent = `${typeLabel(type)} • ${title}`;
      if (DOM.skipMeta) {
        DOM.skipMeta.innerHTML = `
          <div><b>Typ:</b> ${esc(typeLabel(type))}</div>
          <div style="margin-top:6px"><b>Titel:</b> ${esc(title || "—")}</div>
        `;
      }

      if (DOM.skipErr) DOM.skipErr.classList.add("hidden");
      if (DOM.skipTxt) DOM.skipTxt.value = "";
      if (DOM.skipTpl) DOM.skipTpl.value = "";
      DOM.skipBD?.classList.add("open");
      return;
    }

    if (action === "remind") {
      openReminderModalSingle(type, id, title);
    }
  });

  /* =========================================================================
     15) DRAWER EVENTS
     ========================================================================= */
  DOM.dClose?.addEventListener("click", () => DOM.backdrop?.classList.remove("open"));
  DOM.backdrop?.addEventListener("click", (e) => { if (e.target === DOM.backdrop) DOM.backdrop.classList.remove("open"); });

  DOM.save?.addEventListener("click", async () => {
    const text = (DOM.reportText?.value || "").trim();
    if (text.length < 3) return toast("warn", "Zu kurz", "Bitte mehr Text eingeben.");
    if (!state.active?.type || !state.active?.id) return;

    DOM.save.disabled = true;

    try {
      await api("store", ENDPOINTS.store, { type: state.active.type, id: state.active.id, report: text }, "POST");
      toast("ok", "Gespeichert", "Bericht gespeichert.");
      if (DOM.reportText) DOM.reportText.value = "";

      const res = await api("reports", ENDPOINTS.reports, { type: state.active.type, id: state.active.id }, "GET");
      if (DOM.dBody) DOM.dBody.innerHTML = renderReports(res?.rows || []);

      await load();
    } catch (e) {
      if (e?.status === 422) {
        const info = parseLaravel422(e.raw);
        toast("bad", "Validierungsfehler", info.details || info.message);
      } else {
        toast("bad", "Fehler", "Konnte nicht speichern.");
      }
    } finally {
      DOM.save.disabled = false;
    }
  });

  /* =========================================================================
     16) SKIP MODAL EVENTS
     ========================================================================= */
  function closeSkip() { DOM.skipBD?.classList.remove("open"); }

  DOM.skipX?.addEventListener("click", closeSkip);
  DOM.skipNO?.addEventListener("click", closeSkip);
  DOM.skipBD?.addEventListener("click", (e) => { if (e.target === DOM.skipBD) closeSkip(); });

  DOM.skipTpl?.addEventListener("change", () => {
    const map = {
      customer_not_reachable: "Kunde nicht erreichbar.",
      waiting_external: "Warte auf Rückmeldung (extern).",
      internal_clarification: "Interne Klärung läuft.",
      rescheduled: "Follow-up wurde verschoben.",
      duplicate: "Doppelt / bereits erledigt.",
      other: ""
    };
    if (!DOM.skipTxt) return;
    DOM.skipTxt.value = map[DOM.skipTpl.value] ?? DOM.skipTxt.value;
  });

  DOM.skipOK?.addEventListener("click", async () => {
    const reason = (DOM.skipTxt?.value || "").trim();
    if (reason.length < 3) return toast("warn", "Grund fehlt", "Bitte Grund eingeben.");
    if (!state.skipTarget?.type || !state.skipTarget?.id) return;

    DOM.skipOK.disabled = true;

    try {
      await api("skip", ENDPOINTS.skip, {
        type: state.skipTarget.type,
        id: state.skipTarget.id,
        skip_reason: reason,
      }, "POST");

      toast("ok", "Übersprungen", "Eintrag entfernt.");
      closeSkip();
      if (DOM.skipTxt) DOM.skipTxt.value = "";
      await load();
    } catch (e) {
      if (e?.status === 422) {
        const info = parseLaravel422(e.raw);
        toast("bad", "Validierungsfehler", info.details || info.message);
      } else {
        toast("bad", "Fehler", "Konnte nicht überspringen.");
      }
    } finally {
      DOM.skipOK.disabled = false;
    }
  });

  /* =========================================================================
     17) INIT
     ========================================================================= */
  load();
};
</script>
@endsection
