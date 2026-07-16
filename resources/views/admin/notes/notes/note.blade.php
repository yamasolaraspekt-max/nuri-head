@extends('admin.layouts.app')
@section('title', 'Persönliche To-Dos')

@section('style')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">

        <style>
            :root{
                --app-bg:#f3f4f6;
                --card-bg:#ffffff;
                --text-main:#1f2937;
                --text-muted:#6b7280;
                --border:#e5e7eb;
                --primary:var(--sa-accent);
                --primary-hover:var(--sa-accent-hover);
                --primary-light:var(--sa-accent-light);
                --blue:#74b2d4;
                --blue-light:#eff6ff;
                --success:#10b981;
                --success-light:#ecfdf5;
                --warning:#f59e0b;
                --warning-light:#fffbeb;
                --danger:#ef4444;
                --danger-light:#fef2f2;
                --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
                --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
                --radius:16px;
                --transition:all .2s ease-in-out;
            }

            body{
                background:var(--app-bg);
            }

            .note-wrap{
                font-family: Inter, system-ui, -apple-system, sans-serif;
                color:var(--text-main); 
            }

            .note-header{ 
                margin-bottom:18px;
            }

            .note-titlebar{
                display:flex;
                justify-content:space-between;
                align-items:flex-end;
                gap:16px;
                flex-wrap:wrap;
                margin-bottom:16px;
            }

            .note-title{
                font-size:28px;
                font-weight:900;
                letter-spacing:-.02em;
                color:#111827;
            }

            .note-sub{
                font-size:14px;
                color:var(--text-muted);
                margin-top:4px;
            }

            .note-breadcrumb{
                display:flex;
                align-items:center;
                gap:8px;
                flex-wrap:wrap;
                margin-top:10px;
                font-size:13px;
                color:var(--text-muted);
            }

            .note-breadcrumb a{
                color:var(--text-muted);
                text-decoration:none;
                font-weight:700;
            }

            .note-breadcrumb .current{
                color:#111827;
                font-weight:900;
            }

            .note-btn{
                background:var(--primary);
                color:#fff;
                border:none;
                border-radius:12px;
                padding:11px 16px;
                font-weight:800;
                display:inline-flex;
                align-items:center;
                gap:8px;
                cursor:pointer;
                transition:var(--transition);
            }

            .note-btn:hover{
                background:var(--primary-hover);
            }

            .note-btn-soft{
                background:#fff;
                color:var(--text-main);
                border:1px solid var(--border);
                border-radius:12px;
                padding:10px 14px;
                font-weight:700;
                cursor:pointer;
                transition:var(--transition);
            }

            .note-btn-soft:hover{
                background:#f9fafb;
            }

            .note-btn-icon{
                width:40px;
                height:40px;
                border-radius:12px;
                border:1px solid var(--border);
                background:#fff;
                display:inline-flex;
                align-items:center;
                justify-content:center;
                cursor:pointer;
                transition:var(--transition);
                color:var(--text-muted);
            }

            .note-btn-icon:hover{
                background:#f9fafb;
                color:#111827;
            }

            .note-btn-icon.active{
                background:var(--primary-light);
                color:var(--primary-hover);
                border-color:#d7ebb0;
            }

            .note-toolbar{
                background:#fff;
                border:1px solid var(--border);
                border-radius:18px;
                box-shadow:var(--shadow-sm);
                padding:16px;
                display:flex;
                justify-content:space-between;
                align-items:flex-end;
                gap:16px;
                flex-wrap:wrap;
                margin-bottom:18px;
            }

            .note-toolbar-left,
            .note-toolbar-right{
                display:flex;
                align-items:flex-end;
                gap:12px;
                flex-wrap:wrap;
            }

            .note-filter-block{
                display:flex;
                flex-direction:column;
                gap:6px;
                min-width:180px;
            }

            .note-filter-block.search{
                min-width:340px;
                flex:1;
            }

            .note-filter-label{
                font-size:11px;
                font-weight:900;
                text-transform:uppercase;
                color:var(--text-muted);
                letter-spacing:.06em;
            }

            .note-input{
                width:100%;
                min-width:260px;
                padding:11px 14px 11px 40px;
                border-radius:12px;
                border:1px solid var(--border);
                background:#f9fafb;
                font-size:14px;
                outline:none;
                transition:var(--transition);
                background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z'/%3E%3C/svg%3E");
                background-repeat:no-repeat;
                background-position:12px center;
                background-size:18px;
            }

            .note-input:focus,
            .note-select:focus{
                background:#fff;
                border-color:var(--primary);
                box-shadow:0 0 0 3px var(--primary-light);
            }

            .note-select{
                min-width:180px;
                padding:11px 14px;
                border-radius:12px;
                border:1px solid var(--border);
                background:#f9fafb;
                font-size:14px;
                outline:none;
                transition:var(--transition);
            }

            .note-tabs{
                display:flex;
                gap:8px;
                flex-wrap:wrap;
                margin-bottom:18px;
            }

            .note-tab{
                padding:10px 16px;
                border-radius:999px;
                border:1px solid var(--border);
                background:#fff;
                color:#374151;
                font-weight:800;
                cursor:pointer;
                transition:var(--transition);
            }

            .note-tab.active{
                background:var(--primary-light);
                border-color:#d7ebb0;
                color:#4d6d11;
            }

            .note-surface{
                background:#fff;
                border:1px solid var(--border);
                border-radius:20px;
                box-shadow:var(--shadow-sm);
                overflow:hidden;
            }

            .note-surface-head{
                padding:16px 18px;
                border-bottom:1px solid var(--border);
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:12px;
                flex-wrap:wrap;
            }

            .note-surface-title{
                font-size:14px;
                font-weight:900;
                color:#111827;
                letter-spacing:.02em;
                text-transform:uppercase;
            }

            .note-view-switch{
                display:flex;
                gap:8px;
                align-items:center;
            }

            .note-content{
                padding:18px;
            }

            .sticky-grid{
                display:grid;
                grid-template-columns:repeat(auto-fill, minmax(270px, 1fr));
                gap:18px;
            }

            .sticky-note{
                min-height:250px;
                border-radius:18px;
                padding:18px;
                position:relative;
                box-shadow:0 10px 25px rgba(15, 23, 42, .08);
                border:1px solid rgba(255,255,255,.55);
                transition:var(--transition);
                display:flex;
                flex-direction:column;
                overflow:hidden;
            }

            .sticky-note:hover{
                transform:translateY(-3px);
                box-shadow:0 18px 35px rgba(15, 23, 42, .14);
            }

            .sticky-top{
                display:flex;
                justify-content:space-between;
                align-items:flex-start;
                gap:12px;
                margin-bottom:12px;
            }

            .sticky-title{
                font-size:17px;
                font-weight:900;
                color:#111827;
                line-height:1.3;
                word-break:break-word;
            }

            .sticky-check{
                width:20px;
                height:20px;
                margin-top:2px;
                cursor:pointer;
            }

            .sticky-body{
                font-size:14px;
                line-height:1.6;
                color:#374151;
                white-space:pre-line;
                word-break:break-word;
                flex:1;
                margin-bottom:16px;
            }

            .sticky-meta{
                display:flex;
                flex-wrap:wrap;
                gap:8px;
                margin-top:auto;
                margin-bottom:14px;
            }

            .sticky-pill{
                display:inline-flex;
                align-items:center;
                gap:6px;
                padding:6px 10px;
                border-radius:999px;
                font-size:12px;
                font-weight:800;
                background:rgba(255,255,255,.65);
                color:#374151;
                border:1px solid rgba(0,0,0,.05);
            }

            .sticky-actions{
                display:flex;
                flex-wrap:wrap;
                gap:8px;
                justify-content:flex-end;
            }

            .sticky-action{
                width:36px;
                height:36px;
                border-radius:10px;
                border:none;
                background:rgba(255,255,255,.72);
                color:#374151;
                display:inline-flex;
                align-items:center;
                justify-content:center;
                cursor:pointer;
                transition:var(--transition);
            }

            .sticky-action:hover{
                background:#fff;
                transform:translateY(-1px);
            }

            .list-wrap{
                display:flex;
                flex-direction:column;
                gap:12px;
            }

            .list-head{
                display:grid;
                grid-template-columns:60px 70px minmax(220px,1.4fr) minmax(260px,2fr) 120px 120px 140px 110px 170px;
                gap:14px;
                align-items:center;
                padding:0 8px 10px;
                color:var(--text-muted);
                font-size:11px;
                font-weight:900;
                text-transform:uppercase;
                letter-spacing:.06em;
            }

            .list-row{
                display:grid;
                grid-template-columns:60px 70px minmax(220px,1.4fr) minmax(260px,2fr) 120px 120px 140px 110px 170px;
                gap:14px;
                align-items:center;
                background:#fff;
                border:1px solid var(--border);
                border-left:8px solid #ddd;
                border-radius:16px;
                padding:14px 12px;
                transition:var(--transition);
                cursor:grab;
            }

            .list-row:hover{
                box-shadow:var(--shadow-sm);
                border-color:#d9dee7;
            }

            .list-cell{
                min-width:0;
                font-size:14px;
                color:#374151;
            }

            .list-title{
                font-weight:800;
                color:#111827;
            }

            .list-desc{
                white-space:nowrap;
                overflow:hidden;
                text-overflow:ellipsis;
                color:var(--text-muted);
            }

            .list-badge{
                display:inline-flex;
                align-items:center;
                justify-content:center;
                padding:6px 10px;
                border-radius:999px;
                font-size:12px;
                font-weight:900;
                white-space:nowrap;
            }

            .list-badge.done{
                background:var(--success-light);
                color:#047857;
            }

            .list-badge.pending{
                background:#f3f4f6;
                color:#4b5563;
            }

            .list-actions{
                display:flex;
                gap:8px;
                flex-wrap:wrap;
                justify-content:flex-end;
            }

            .empty-state{
                padding:70px 20px;
                text-align:center;
                color:var(--text-muted);
                font-size:15px;
            }

            .complete{
                text-decoration:line-through 2px #ef4444;
                opacity:.7;
            }

            .note-modal-backdrop{
                position:fixed;
                inset:0;
                background:rgba(15,23,42,.55);
                backdrop-filter:blur(3px);
                z-index:1400;
                display:flex;
                align-items:center;
                justify-content:center;
                padding:18px;
                opacity:0;
                pointer-events:none;
                transition:.2s ease;
            }

            .note-modal-backdrop.open{
                opacity:1;
                pointer-events:auto;
            }

            .note-modal{
                width:100%;
                max-width:760px;
                background:#fff;
                border-radius:20px;
                box-shadow:var(--shadow);
                overflow:hidden;
                transform:translateY(10px) scale(.985);
                transition:.2s ease;
            }

            .note-modal-backdrop.open .note-modal{
                transform:translateY(0) scale(1);
            }

            .note-modal-head{
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:12px;
                padding:16px 20px;
                border-bottom:1px solid var(--border);
                background:#fafafa;
            }

            .note-modal-title{
                font-size:16px;
                font-weight:900;
                color:#111827;
                margin:0;
            }

            .note-modal-body{
                padding:20px;
                max-height:75vh;
                overflow-y:auto;
            }

            .note-modal-foot{
                padding:14px 20px;
                border-top:1px solid var(--border);
                background:#fafafa;
                display:flex;
                justify-content:flex-end;
                gap:10px;
                flex-wrap:wrap;
            }

            .note-grid-2{
                display:grid;
                grid-template-columns:repeat(2, minmax(0,1fr));
                gap:16px;
            }

            @media (max-width: 900px){
                .note-grid-2{
                    grid-template-columns:1fr;
                }

                .list-head{
                    display:none;
                }

                .list-row{
                    grid-template-columns:1fr;
                    gap:10px;
                }

                .list-actions{
                    justify-content:flex-start;
                }
            }

            .note-group{
                display:flex;
                flex-direction:column;
                gap:6px;
            }

            .note-label{
                font-size:13px;
                font-weight:800;
                color:#111827;
            }

            .note-field,
            .note-area,
            .note-file,
            .note-select-modal{
                width:100%;
                border:1px solid var(--border);
                border-radius:12px;
                padding:11px 12px;
                font-size:14px;
                outline:none;
                transition:var(--transition);
                background:#fff;
            }

            .note-field:focus,
            .note-area:focus,
            .note-file:focus,
            .note-select-modal:focus{
                border-color:var(--primary);
                box-shadow:0 0 0 3px var(--primary-light);
            }

            .note-area{
                min-height:120px;
                resize:vertical;
            }

            .toggle-row{
                display:grid;
                grid-template-columns:1fr auto;
                gap:14px;
                align-items:center;
                padding:12px 14px;
                border:1px solid var(--border);
                border-radius:14px;
                background:#fafafa;
            }

            .toggle-row-title{
                font-size:14px;
                font-weight:800;
                color:#111827;
            }

            .toggle-row-sub{
                font-size:12px;
                color:var(--text-muted);
                margin-top:2px;
            }

            .switch{
                position:relative;
                width:48px;
                height:28px;
            }

            .switch input{
                display:none;
            }

            .slider{
                position:absolute;
                inset:0;
                background:#d1d5db;
                border-radius:999px;
                cursor:pointer;
                transition:.2s ease;
            }

            .slider::before{
                content:"";
                position:absolute;
                width:22px;
                height:22px;
                left:3px;
                top:3px;
                background:#fff;
                border-radius:50%;
                transition:.2s ease;
                box-shadow:0 2px 6px rgba(0,0,0,.12);
            }

            .switch input:checked + .slider{
                background:var(--primary);
            }

            .switch input:checked + .slider::before{
                transform:translateX(20px);
            }

            .hidden-row{
                display:none;
            }

            /* Select2 inside custom modal fix */
        .note-modal .select2-container {
            width: 100% !important;
        }

        .note-modal .select2-container--default .select2-selection--single {
            height: 46px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fff;
            display: flex;
            align-items: center;
        }

        .note-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #111827;
            line-height: 44px;
            padding-left: 12px;
            padding-right: 32px;
            font-size: 14px;
        }

        .note-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px;
            right: 8px;
        }

       .note-modal {
        position: relative;
    }

    .note-modal .select2-container {
        width: 100% !important;
    }

    .note-modal .select2-container--default .select2-selection--single {
        height: 46px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fff;
        display: flex;
        align-items: center;
    }

    .note-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #111827;
        line-height: 44px;
        padding-left: 12px;
        padding-right: 32px;
        font-size: 14px;
    }

    .note-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
        right: 8px;
    }

    .note-modal .select2-dropdown {
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 16px 30px rgba(15, 23, 42, .18);
    }

    .note-modal .select2-results__option {
        padding: 10px 12px;
        font-size: 14px;
    }

    .note-modal .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: var(--primary);
        color: #fff;
    }

    .select2-container--open {
        z-index: 1700 !important;
    }

    .select2-dropdown {
        z-index: 1700 !important;
    }
 

        </style>
@endsection

@section('content')
    <div class="note-wrap">
        <div class="note-header">
            <div class="note-titlebar">
                <div>
                    <div class="note-title">PERSÖNLICHE TO-DOS</div>
                    <div class="note-sub">Verwalte deine Notizen als Sticky Notes oder in einer kompakten Liste.</div>

                    <div class="note-breadcrumb">
                        <a href="{{ url('/') }}">Home</a>
                        <span>›</span>
                        <span class="current">Persönliche To-Dos</span>
                    </div>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button type="button" class="note-btn" onclick="resetNoteForm(); openNoteModal('newNoteModal')">
                        <i data-feather="plus"></i>
                        Neue Notiz
                    </button>

                    <button type="button" class="note-btn-soft category_search" onclick="openNoteModal('categorySearchModal')">
                        <i data-feather="slack"></i>
                        Kategorie
                    </button>

                    <button type="button" class="note-btn-soft trash_box">
                        <i data-feather="trash-2"></i>
                        Papierkorb
                    </button>
                </div>
            </div>
        </div>

        <form id="search_form" action="javascript:void(0);" class="note-toolbar">
            <div class="note-toolbar-left" style="flex:1;">
                <div class="note-filter-block search">
                    <label class="note-filter-label">Suche</label>
                    <input type="text" id="search_input" name="search" class="note-input" placeholder="Suche nach Titel, Inhalt oder Kategorie">
                </div>
            </div>

            <div class="note-toolbar-right">
                <div class="note-filter-block">
                    <label class="note-filter-label">Filter</label>
                    <select id="quick_filter" class="note-select">
                        <option value="">Alle</option>
                        <option value="date">Neueste zuerst</option>
                        <option value="sort">Manuelle Reihenfolge</option>
                        <option value="calendar">Nur mit Kalender</option>
                        <option value="reminder">Nur mit Erinnerung</option>
                        <option value="repeat">Nur wiederholende</option>
                    </select>
                </div>

                <button type="submit" class="note-btn-soft" id="search_button">Suchen</button>
                <button type="button" class="note-btn-soft" id="reset_notes">Zurücksetzen</button>
            </div>
        </form>

        <div class="note-tabs" id="noteStatusTabs">
            <button type="button" class="note-tab active" data-status="open">Offen</button>
            <button type="button" class="note-tab" data-status="done">Erledigt</button>
        </div>

        <div class="note-surface">
            <div class="note-surface-head">
                <div class="note-surface-title">Meine Notizen</div>

                <div class="note-view-switch">
                    <button type="button" class="note-btn-icon active" id="cardViewBtn" title="Kartenansicht">
                        <i data-feather="grid"></i>
                    </button>
                    <button type="button" class="note-btn-icon" id="listViewBtn" title="Listenansicht">
                        <i data-feather="list"></i>
                    </button>
                </div>
            </div>

            <div class="note-content">
                <div id="notesRenderArea"></div>
            </div>
        </div>
    </div>

    {{-- Create modal --}}
    <div class="note-modal-backdrop" id="newNoteModal">
        <div class="note-modal">
            <div class="note-modal-head">
                <h3 class="note-modal-title" id="noteModalTitle">Neue Notiz</h3>
                <button type="button" class="note-btn-icon" onclick="closeNoteModal('newNoteModal')">
                    <i data-feather="x"></i>
                </button>
            </div>

            <form id="save_note_form">
                @csrf
                <input type="hidden" name="check_availability" id="check_availability" value="false">
                <input type="hidden" name="color" id="color" value="#fff59d">
                <input type="hidden" name="priority" id="priority" value="normal">

                <div class="note-modal-body">
                    <div class="note-grid-2">
                        <div class="note-group" style="grid-column:1/-1;">
                            <label class="note-label">Titel *</label>
                            <input type="text" class="note-field" name="title" id="note_title" placeholder="Titel eingeben">
                        </div>

                        <div class="note-group" style="grid-column:1/-1;">
                            <label class="note-label">Beschreibung</label>
                            <textarea class="note-area" name="note" placeholder="Notiz eingeben"></textarea>
                        </div>

                        <div class="note-group">
                            <label class="note-label">Kategorie</label>
                            <div style="display:flex;gap:8px;">
                                <select class="note-select-modal category_id" id="category_id" name="category_id" style="flex:1;"></select>
                                <button type="button" class="note-btn-soft add_category">+</button>
                            </div>
                        </div>

                        <div class="note-group">
                            <label class="note-label">Farbe</label>
                            <select class="note-select-modal" id="color_picker">
                                <option value="#fff59d">Gelb</option>
                                <option value="#ffccbc">Orange</option>
                                <option value="#f8bbd0">Pink</option>
                                <option value="#d1c4e9">Lila</option>
                                <option value="#bbdefb">Blau</option>
                                <option value="#b2dfdb">Türkis</option>
                                <option value="#c8e6c9">Grün</option>
                                <option value="#eeeeee">Grau</option>
                            </select>
                        </div>

                        <div class="toggle-row" style="grid-column:1/-1;">
                            <div>
                                <div class="toggle-row-title">Datum aktivieren</div>
                                <div class="toggle-row-sub">Fälligkeitsdatum für diese Notiz setzen</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="deadline">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="note-group hidden-row" id="deadline_area" style="grid-column:1/-1;">
                            <label class="note-label">Fälligkeitsdatum</label>
                            <input type="date" class="note-field" name="deadline">
                        </div>

                        <div class="toggle-row" style="grid-column:1/-1;">
                            <div>
                                <div class="toggle-row-title">Zeit aktivieren</div>
                                <div class="toggle-row-sub">Optional eine Endzeit setzen</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="end_time">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="note-group hidden-row end_time_area" style="grid-column:1/-1;">
                            <label class="note-label">Endzeit</label>
                            <input type="time" class="note-field" name="end_time">
                        </div>

                        <div class="toggle-row" style="grid-column:1/-1;">
                            <div>
                                <div class="toggle-row-title">Zum Kalender hinzufügen</div>
                                <div class="toggle-row-sub">Erstellt zusätzlich einen persönlichen Task</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="add_calendar" name="add_calendar" value="1">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="note-group hidden-row" id="add_calendar_area" style="grid-column:1/-1;">
                            <label class="note-label">Kalenderdatum</label>
                            <input type="date" class="note-field" name="add_calendar_date">
                        </div>

                        <div class="toggle-row" style="grid-column:1/-1;">
                            <div>
                                <div class="toggle-row-title">Wiederholen</div>
                                <div class="toggle-row-sub">Notiz automatisch wiederholen</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="repeated">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="note-group hidden-row repeated_area" style="grid-column:1/-1;">
                            <label class="note-label">Häufigkeit</label>
                            <select name="repeat" class="note-select-modal">
                                <option value="">Häufigkeit auswählen</option>
                                <option value="minute">Minütlich</option>
                                <option value="hourly">Stündlich</option>
                                <option value="daily">Täglich</option>
                                <option value="weekly">Wöchentlich</option>
                                <option value="monthly">Monatlich</option>
                                <option value="quarterly">Vierteljährlich</option>
                                <option value="yearly">Jährlich</option>
                            </select>
                        </div>

                        <div class="toggle-row" style="grid-column:1/-1;">
                            <div>
                                <div class="toggle-row-title">Erinnerung</div>
                                <div class="toggle-row-sub">Datum und Zeit für Erinnerung setzen</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="reminder_check">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="note-grid-2 hidden-row reminder_area" style="grid-column:1/-1;">
                            <div class="note-group">
                                <label class="note-label">Erinnerungsdatum</label>
                                <input type="date" class="note-field" name="reminder_date">
                            </div>
                            <div class="note-group">
                                <label class="note-label">Erinnerungszeit</label>
                                <input type="time" class="note-field" name="reminder_time">
                            </div>
                        </div>

                        <div class="note-group">
                            <label class="note-label">Priorität</label>
                            <select class="note-select-modal" id="priority_picker">
                                <option value="normal">Keiner</option>
                                <option value="medium">Medium</option>
                                <option value="high">Hoch</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="note-modal-foot">
                    <button type="button" class="note-btn-soft" onclick="closeNoteModal('newNoteModal')">Abbrechen</button>
                    <button type="button" class="note-btn" id="save_note_button">Speichern</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Category search modal --}}
    <div class="note-modal-backdrop" id="categorySearchModal">
        <div class="note-modal" style="max-width:520px;">
            <div class="note-modal-head">
                <h3 class="note-modal-title">Suche nach Kategorie</h3>
                <button type="button" class="note-btn-icon" onclick="closeNoteModal('categorySearchModal')">
                    <i data-feather="x"></i>
                </button>
            </div>

            <div class="note-modal-body">
                <div class="note-group">
                    <label class="note-label">Kategorie wählen</label>
                    <select name="category_id" class="note-select-modal select-category" style="width:100%">
                        @foreach ($category as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="note-modal-foot">
                <button type="button" class="note-btn-soft" onclick="closeNoteModal('categorySearchModal')">Schließen</button>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>

    <script>
        let currentStatus = 'open';
        let currentFilter = '';
        let currentView = localStorage.getItem('notes_view') || 'card';
        let currentSearch = '';
        let currentEditNoteId = null;
        let notesCache = [];

        const repeatTranslations = {
            "": "Häufigkeit auswählen",
            "minute": "Minütlich",
            "hourly": "Stündlich",
            "daily": "Täglich",
            "weekly": "Wöchentlich",
            "monthly": "Monatlich",
            "quarterly": "Vierteljährlich",
            "yearly": "Jährlich"
        };

        function featherRefresh() {
            if (window.feather) feather.replace();
        }

        function openNoteModal(id) {
            const modal = document.getElementById(id);

            if (!modal) return;

            modal.classList.add('open');

            setTimeout(function() {
                initNoteSelect2();
                featherRefresh();
            }, 50);
        }

        function closeNoteModal(id) {
            document.getElementById(id)?.classList.remove('open');
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return $('<div>').text(text).html();
        }

        function formatDate(date) {
            if (!date) return '—';
            try {
                return new Date(date).toLocaleDateString('de-DE', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } catch (e) {
                return date;
            }
        }

        function getStatusBadge(note) {
            if (!note.is_done) {
                return `<span class="list-badge pending">Offen</span>`;
            }
            return `<span class="list-badge done">Erledigt</span>`;
        }

        function resetNoteForm() {
                currentEditNoteId = null;

                $('#noteModalTitle').text('Neue Notiz');
                $('#save_note_button').text('Speichern');

                $('#save_note_form')[0].reset();

                $('#check_availability').val('false');
                $('#color').val('#fff59d');
                $('#priority').val('normal');
                $('#color_picker').val('#fff59d');
                $('#priority_picker').val('normal');

                $('#category_id').val('').trigger('change');

                $('#deadline_area').addClass('hidden-row');
                $('.end_time_area').addClass('hidden-row');
                $('#add_calendar_area').addClass('hidden-row');
                $('.repeated_area').addClass('hidden-row');
                $('.reminder_area').addClass('hidden-row');
            }

            function fillNoteForm(note) {
                currentEditNoteId = note.id;

                $('#noteModalTitle').text('Notiz bearbeiten');
                $('#save_note_button').text('Aktualisieren');

                $('#note_title').val(note.title || '');
                $('[name="note"]').val(note.note || '');

                $('#color').val(note.color || '#fff59d');
                $('#color_picker').val(note.color || '#fff59d');

                $('#priority').val(note.priority || 'normal');
                $('#priority_picker').val(note.priority || 'normal');

                if (note.category_id) {
                    $('#category_id').val(note.category_id).trigger('change');
                } else {
                    $('#category_id').val('').trigger('change');
                }

                if (note.deadline) {
                    $('#deadline').prop('checked', true);
                    $('#deadline_area').removeClass('hidden-row');
                    $('[name="deadline"]').val(note.deadline);
                } else {
                    $('#deadline').prop('checked', false);
                    $('#deadline_area').addClass('hidden-row');
                    $('[name="deadline"]').val('');
                }

                if (note.end_time) {
                    $('#end_time').prop('checked', true);
                    $('.end_time_area').removeClass('hidden-row');
                    $('[name="end_time"]').val(note.end_time);
                } else {
                    $('#end_time').prop('checked', false);
                    $('.end_time_area').addClass('hidden-row');
                    $('[name="end_time"]').val('');
                }

                if (note.repeat) {
                    $('#repeated').prop('checked', true);
                    $('.repeated_area').removeClass('hidden-row');
                    $('[name="repeat"]').val(note.repeat);
                } else {
                    $('#repeated').prop('checked', false);
                    $('.repeated_area').addClass('hidden-row');
                    $('[name="repeat"]').val('');
                }

                if (note.reminder_date || note.reminder_time) {
                    $('#reminder_check').prop('checked', true);
                    $('.reminder_area').removeClass('hidden-row');
                    $('[name="reminder_date"]').val(note.reminder_date || '');
                    $('[name="reminder_time"]').val(note.reminder_time || '');
                } else {
                    $('#reminder_check').prop('checked', false);
                    $('.reminder_area').addClass('hidden-row');
                    $('[name="reminder_date"]').val('');
                    $('[name="reminder_time"]').val('');
                }

                if (note.add_calendar_date) {
                    $('#add_calendar').prop('checked', true);
                    $('#add_calendar_area').removeClass('hidden-row');
                    $('[name="add_calendar_date"]').val(note.add_calendar_date);
                } else {
                    $('#add_calendar').prop('checked', false);
                    $('#add_calendar_area').addClass('hidden-row');
                    $('[name="add_calendar_date"]').val('');
                }

                openNoteModal('newNoteModal');
            }

        function getStickyBg(color) {
            const map = {
                '#fff59d': '#fff9c4',
                '#ffccbc': '#ffe0b2',
                '#f8bbd0': '#fce4ec',
                '#d1c4e9': '#ede7f6',
                '#bbdefb': '#e3f2fd',
                '#b2dfdb': '#e0f2f1',
                '#c8e6c9': '#e8f5e9',
                '#eeeeee': '#f5f5f5'
            };
            return map[(color || '').toLowerCase()] || color || '#fff9c4';
        }

        function stickyMeta(note) {
            let html = '';

            if (note.category_name) {
                html += `<span class="sticky-pill"><i data-feather="tag" style="width:12px;height:12px;"></i> ${escapeHtml(note.category_name)}</span>`;
            }
            if (note.deadline) {
                html += `<span class="sticky-pill"><i data-feather="calendar" style="width:12px;height:12px;"></i> ${formatDate(note.deadline)}</span>`;
            }
            if (note.end_time) {
                html += `<span class="sticky-pill"><i data-feather="clock" style="width:12px;height:12px;"></i> ${escapeHtml(note.end_time)}</span>`;
            }
            if (note.reminder_date || note.reminder_time) {
                html += `<span class="sticky-pill"><i data-feather="bell" style="width:12px;height:12px;"></i> Erinnerung</span>`;
            }
            if (note.repeat) {
                html += `<span class="sticky-pill"><i data-feather="refresh-ccw" style="width:12px;height:12px;"></i> ${repeatTranslations[note.repeat] || note.repeat}</span>`;
            }

            return html;
        }

        function renderCardView(notes) {
            if (!notes.length) {
                return `<div class="empty-state">Keine Notizen gefunden.</div>`;
            }

            let html = `<div class="sticky-grid" id="notesSortableGrid">`;

            notes.forEach((note) => {
                const bg = getStickyBg(note.color);
                html += `
                    <div class="sticky-note" data-id="${note.id}" style="background:${bg};">
                        <div class="sticky-top">
                            <div class="sticky-title ${note.is_done ? 'complete' : ''}">
                                ${escapeHtml(note.title || 'Ohne Titel')}
                            </div>
                            <input type="checkbox" class="sticky-check done-checkbox" data-id="${note.id}" ${note.is_done ? 'checked' : ''}>
                        </div>

                        <div class="sticky-body ${note.is_done ? 'complete' : ''}">
                            ${escapeHtml(note.note || 'Keine Beschreibung')}
                        </div>

                        <div class="sticky-meta">
                            ${stickyMeta(note)}
                        </div>

                        <div class="sticky-actions">
                            <button type="button" class="sticky-action change-date" data-id="${note.id}" title="Datum ändern">
                                <i data-feather="calendar"></i>
                            </button>
                            <button type="button" class="sticky-action change-time" data-id="${note.id}" title="Zeit ändern">
                                <i data-feather="clock"></i>
                            </button>
                            <button type="button" class="sticky-action note-color" data-id="${note.id}" title="Farbe">
                                <i data-feather="droplet"></i>
                            </button>
                            <button type="button" class="sticky-action note-settings" data-id="${note.id}" title="Bearbeiten">
                                <i data-feather="edit-3"></i>
                            </button>
                            <button type="button" class="sticky-action delete_note" data-id="${note.id}" title="Löschen">
                                <i data-feather="trash-2"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            html += `</div>`;
            return html;
        }

        function renderListView(notes) {
            if (!notes.length) {
                return `<div class="empty-state">Keine Notizen gefunden.</div>`;
            }

            let html = `
                <div class="list-wrap">
                    <div class="list-head">
                        <div>#</div>
                        <div>Erledigt</div>
                        <div>Titel</div>
                        <div>Beschreibung</div>
                        <div>Frist</div>
                        <div>Endzeit</div>
                        <div>Kategorie</div>
                        <div>Status</div>
                        <div style="text-align:right;">Aktionen</div>
                    </div>
                    <div id="notesSortableList">
            `;

            notes.forEach((note, index) => {
                html += `
                    <div class="list-row" data-id="${note.id}" style="border-left-color:${note.color || '#ddd'};">
                        <div class="list-cell">${index + 1}</div>

                        <div class="list-cell">
                            <input type="checkbox" class="done-checkbox" data-id="${note.id}" ${note.is_done ? 'checked' : ''}>
                        </div>

                        <div class="list-cell">
                            <div class="list-title ${note.is_done ? 'complete' : ''}">${escapeHtml(note.title || 'Ohne Titel')}</div>
                        </div>

                        <div class="list-cell">
                            <div class="list-desc ${note.is_done ? 'complete' : ''}">${escapeHtml(note.note || 'Keine Beschreibung')}</div>
                        </div>

                        <div class="list-cell">${formatDate(note.deadline)}</div>
                        <div class="list-cell">${escapeHtml(note.end_time || '—')}</div>
                        <div class="list-cell">${escapeHtml(note.category_name || 'Standard')}</div>
                        <div class="list-cell">${getStatusBadge(note)}</div>

                        <div class="list-cell">
                            <div class="list-actions">
                                <button type="button" class="note-btn-icon change-date" data-id="${note.id}" title="Datum ändern">
                                    <i data-feather="calendar"></i>
                                </button>
                                <button type="button" class="note-btn-icon change-time" data-id="${note.id}" title="Zeit ändern">
                                    <i data-feather="clock"></i>
                                </button>
                                <button type="button" class="note-btn-icon note-color" data-id="${note.id}" title="Farbe">
                                    <i data-feather="droplet"></i>
                                </button>
                                <button type="button" class="note-btn-icon note-settings" data-id="${note.id}" title="Bearbeiten">
                                    <i data-feather="edit-3"></i>
                                </button>
                                <button type="button" class="note-btn-icon delete_note" data-id="${note.id}" title="Löschen">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += `</div></div>`;
            return html;
        }

        function renderNotes(notes) {
            const area = $('#notesRenderArea');
            if (currentView === 'card') {
                area.html(renderCardView(notes));
            } else {
                area.html(renderListView(notes));
            }

            $('#cardViewBtn').toggleClass('active', currentView === 'card');
            $('#listViewBtn').toggleClass('active', currentView === 'list');

            initializeSortable();
            featherRefresh();
        }

        function getEndpoint() {
            if (currentSearch) {
                return {
                    url: "{{ route('notes.search') }}",
                    data: {
                        search: currentSearch,
                        status: currentStatus
                    }
                };
            }

            if (currentFilter) {
                return {
                    url: "{{ route('notes.filter') }}",
                    data: {
                        filter: currentFilter,
                        status: currentStatus
                    }
                };
            }

            return {
                url: "{{ route('notes') }}",
                data: {
                    status: currentStatus
                }
            };
        }

        function loadNotes() {
            const endpoint = getEndpoint();

            $.ajax({
                url: endpoint.url,
                method: 'GET',
                data: endpoint.data,
               success: function (response) {
                    notesCache = response.notes || [];
                    renderNotes(notesCache);
                },
                error: function() {
                    Swal.fire('Fehler', 'Notizen konnten nicht geladen werden.', 'error');
                }
            });
        }

        $(document).on('click', '.note-settings', function() {
            const noteId = String($(this).data('id'));

            const note = notesCache.find(item => String(item.id) === noteId);

            if (!note) {
                Swal.fire('Fehler', 'Notizdaten konnten nicht gefunden werden. Bitte Seite neu laden.', 'error');
                return;
            }

            resetNoteForm();
            fillNoteForm(note);
        });

        function initializeSortable() {
            const sortableContainer = currentView === 'card'
                ? document.getElementById('notesSortableGrid')
                : document.getElementById('notesSortableList');

            if (!sortableContainer || currentStatus !== 'open') return;

            Sortable.create(sortableContainer, {
                animation: 150,
                onEnd: function() {
                    const order = [];
                    sortableContainer.querySelectorAll('[data-id]').forEach(el => {
                        order.push(el.getAttribute('data-id'));
                    });

                    $.ajax({
                        url: "{{ route('notes.updateOrder') }}",
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            order: order,
                            _token: "{{ csrf_token() }}"
                        }),
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            if (window.toastr) toastr.success(res.message || 'Reihenfolge aktualisiert');
                        }
                    });
                }
            });
        }

        function loadCategories(selectedId = null) {
            $.ajax({
                url: "{{ route('notes.fetch.category') }}",
                method: "GET",
                success: function(data) {
                    const select = $('#category_id');

                    select.empty();
                    select.append('<option value=""></option>');

                    data.forEach(category => {
                        select.append(`<option value="${category.id}">${escapeHtml(category.category_name)}</option>`);
                    });

                    setTimeout(function() {
                        initNoteSelect2();

                        if (selectedId) {
                            select.val(selectedId).trigger('change');
                        } else {
                            select.val('').trigger('change');
                        }
                    }, 50);
                }
            });
        }

        $(document).ready(function() {
            function initNoteSelect2() {
                if ($('#category_id').hasClass('select2-hidden-accessible')) {
                    $('#category_id').select2('destroy');
                }

                $('#category_id').select2({
                    dropdownParent: $('#newNoteModal'),
                    placeholder: 'Wählen Sie eine Kategorie',
                    allowClear: true,
                    width: '100%'
                });

                if ($('.select-category').hasClass('select2-hidden-accessible')) {
                    $('.select-category').select2('destroy');
                }

                $('.select-category').select2({
                    dropdownParent: $('#categorySearchModal'),
                    placeholder: 'Kategorie auswählen',
                    allowClear: true,
                    width: '100%'
                });
            }

            loadCategories();
            loadNotes();
            featherRefresh();

            $('#cardViewBtn').on('click', function() {
                currentView = 'card';
                localStorage.setItem('notes_view', 'card');
                loadNotes();
            });

            $('#listViewBtn').on('click', function() {
                currentView = 'list';
                localStorage.setItem('notes_view', 'list');
                loadNotes();
            });

            $(document).on('click', '#noteStatusTabs .note-tab', function() {
                $('#noteStatusTabs .note-tab').removeClass('active');
                $(this).addClass('active');
                currentStatus = $(this).data('status');
                loadNotes();
            });

            $('#quick_filter').on('change', function() {
                currentFilter = $(this).val();
                loadNotes();
            });

            $('#search_form').on('submit', function(e) {
                e.preventDefault();
                currentSearch = $('#search_input').val().trim();
                loadNotes();
            });

            $('#reset_notes').on('click', function() {
                currentSearch = '';
                currentFilter = '';
                $('#search_input').val('');
                $('#quick_filter').val('');
                loadNotes();
            });

            $('#color_picker').on('change', function() {
                $('#color').val($(this).val());
            });

            $('#priority_picker').on('change', function() {
                $('#priority').val($(this).val());
            });

            $('#deadline').on('change', function() {
                $('#deadline_area').toggleClass('hidden-row', !this.checked);
            });

            $('#end_time').on('change', function() {
                $('.end_time_area').toggleClass('hidden-row', !this.checked);
            });

            $('#repeated').on('change', function() {
                $('.repeated_area').toggleClass('hidden-row', !this.checked);
            });

            $('#reminder_check').on('change', function() {
                $('.reminder_area').toggleClass('hidden-row', !this.checked);
            });

            $('#add_calendar').on('change', function() {
                $('#add_calendar_area').toggleClass('hidden-row', !this.checked);
            });

            $(document).on('click', '.done-checkbox', function() {
                const noteId = $(this).data('id');
                const isDone = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: `{{ url('/notes_done') }}/${noteId}`,
                    method: 'PUT',
                    data: {
                        _token: "{{ csrf_token() }}",
                        is_done: isDone
                    },
                    success: function() {
                        loadNotes();
                    },
                    error: function() {
                        Swal.fire('Fehler', 'Status konnte nicht geändert werden.', 'error');
                    }
                });
            });

             $('#save_note_button').on('click', function () {
                const isEdit = currentEditNoteId !== null;

                let ajaxUrl = "{{ route('notes.store') }}";
                let ajaxMethod = 'POST';

                if (isEdit) {
                    ajaxUrl = `{{ url('/notes_update') }}/${currentEditNoteId}`;
                    ajaxMethod = 'PUT';
                }

                $.ajax({
                    url: ajaxUrl,
                    method: ajaxMethod,
                    data: $('#save_note_form').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function () {
                        Swal.fire(
                            'Erfolgreich',
                            isEdit ? 'Notiz wurde aktualisiert.' : 'Notiz wurde gespeichert.',
                            'success'
                        );

                        resetNoteForm();
                        closeNoteModal('newNoteModal');
                        loadNotes();
                    },
                    error: function (xhr) {
                        if (xhr.status === 409 && xhr.responseJSON.availability) {
                            let tableHtml = `
                    <div style="max-height:400px;overflow:auto;text-align:left;">
                        <p>Es gibt bestehende Aufgaben im angegebenen Zeitraum:</p>
                        <table class="table table-bordered">
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
                                        <td>${task.name || ''} ${task.lastname || ''}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;

                            Swal.fire({
                                title: 'Konflikte erkannt',
                                html: tableHtml,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Trotzdem speichern',
                                cancelButtonText: 'Abbrechen'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#check_availability').val('true');
                                    $('#save_note_button').trigger('click');
                                }
                            });
                        } else {
                            const errors = xhr.responseJSON?.errors || {};
                            const msg = Object.values(errors).flat().join('<br>') || xhr.responseJSON?.message || 'Die Notiz konnte nicht gespeichert werden.';
                            Swal.fire('Fehler', msg, 'error');
                        }
                    }
                });
            });
            $(document).on('click', '.delete_note', function() {
                const noteId = $(this).data('id');

                Swal.fire({
                    title: 'Bist du sicher?',
                    text: 'Diese Aktion kann nicht rückgängig gemacht werden.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, löschen',
                    cancelButtonText: 'Abbrechen'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('/notes_delete') }}/${noteId}`,
                            method: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function() {
                                loadNotes();
                            },
                            error: function() {
                                Swal.fire('Fehler', 'Notiz konnte nicht gelöscht werden.', 'error');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.change-date', function() {
                const noteId = $(this).data('id');

                Swal.fire({
                    title: 'Neues Datum',
                    input: 'date',
                    showCancelButton: true,
                    confirmButtonText: 'Speichern',
                    cancelButtonText: 'Abbrechen'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $.ajax({
                            url: `{{ url('/note_change_date') }}/${noteId}`,
                            method: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}",
                                deadline: result.value
                            },
                            success: function() {
                                loadNotes();
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.change-time', function() {
                const noteId = $(this).data('id');

                Swal.fire({
                    title: 'Neue Zeit',
                    input: 'time',
                    showCancelButton: true,
                    confirmButtonText: 'Speichern',
                    cancelButtonText: 'Abbrechen'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $.ajax({
                            url: `{{ url('/note_change_time') }}/${noteId}`,
                            method: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}",
                                end_time: result.value
                            },
                            success: function() {
                                loadNotes();
                            },
                            error: function(xhr) {
                                const msg = xhr.responseJSON?.message || 'Zeit konnte nicht geändert werden.';
                                Swal.fire('Fehler', msg, 'error');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.note-color', function() {
                const noteId = $(this).data('id');

                Swal.fire({
                    title: 'Farbe wählen',
                    input: 'select',
                    inputOptions: {
                        '#fff59d': 'Gelb',
                        '#ffccbc': 'Orange',
                        '#f8bbd0': 'Pink',
                        '#d1c4e9': 'Lila',
                        '#bbdefb': 'Blau',
                        '#b2dfdb': 'Türkis',
                        '#c8e6c9': 'Grün',
                        '#eeeeee': 'Grau'
                    },
                    inputPlaceholder: 'Farbe auswählen',
                    showCancelButton: true,
                    confirmButtonText: 'Speichern',
                    cancelButtonText: 'Abbrechen'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $.ajax({
                            url: `{{ url('/note_change_color') }}/${noteId}`,
                            method: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}",
                                color: result.value
                            },
                            success: function() {
                                loadNotes();
                            }
                        });
                    }
                });
            });

            $(document).on('change', '.select-category', function() {
                const categoryId = $(this).val();
                if (!categoryId) return;

                $.ajax({
                    url: "{{ route('notes.search.category') }}",
                    method: 'GET',
                    data: { category_id: categoryId },
                    success: function(response) {
                        renderNotes(response.notes || []);
                        closeNoteModal('categorySearchModal');
                    }
                });
            });

            $(document).on('click', '.trash_box', function() {
                $.ajax({
                    url: "{{ route('notes.trash') }}",
                    method: "GET",
                    success: function(response) {
                        const html = `
                            <div style="max-height:500px;overflow:auto;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Titel</th>
                                            <th>Kategorie</th>
                                            <th>Erstellt am</th>
                                            <th>Aktionen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${(response.notes || []).map(note => `
                                            <tr>
                                                <td>${escapeHtml(note.title)}</td>
                                                <td>${escapeHtml(note.category_name || 'Standard')}</td>
                                                <td>${formatDate(note.created_at)}</td>
                                                <td style="display:flex;gap:8px;flex-wrap:wrap;">
                                                    <button class="btn btn-success btn-sm recover-note" data-id="${note.id}">Wiederherstellen</button>
                                                    <button class="btn btn-danger btn-sm permanent-delete" data-id="${note.id}">Dauerhaft löschen</button>
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;

                        Swal.fire({
                            title: 'Papierkorb',
                            html: html,
                            width: 900,
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: 'Schließen'
                        });
                    }
                });
            });

            $(document).on('click', '.recover-note', function() {
                const noteId = $(this).data('id');

                $.ajax({
                    url: `{{ url('/notes_recover') }}/${noteId}`,
                    method: 'PUT',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function() {
                        Swal.close();
                        loadNotes();
                    }
                });
            });

            $(document).on('click', '.permanent-delete', function() {
                const noteId = $(this).data('id');

                $.ajax({
                    url: `{{ url('/notes_permanent_delete') }}/${noteId}`,
                    method: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function() {
                        Swal.close();
                        loadNotes();
                    }
                });
            });

            @if(Session::has('save_msg'))
                Swal.fire('Erfolg', "{{ session('save_msg') }}", 'success');
            @endif

            @if(Session::has('update_msg'))
                Swal.fire('Erfolg', "{{ session('update_msg') }}", 'success');
            @endif

            @if(Session::has('delete_msg'))
                Swal.fire('Info', "{{ session('delete_msg') }}", 'info');
            @endif
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('note-modal-backdrop')) {
                e.target.classList.remove('open');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.note-modal-backdrop.open').forEach(el => el.classList.remove('open'));
            }
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
                label: 'Persönliche To-Dos',
                url: "{{ url()->current()}}",
                clickable: false
            }

        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush