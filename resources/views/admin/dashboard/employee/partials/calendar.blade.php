<style>
        /* ... [Keep all your existing styles here] ... */
        
        /* --- CALENDAR & TABS STYLES (Add these to your existing style block) --- */
        .dashboard-tabs {
            display: flex;
            gap: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1.25rem;
        }

        .dashboard-tab-btn {
            padding: 0.75rem 0.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: #64748b;
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: transparent;
            cursor: pointer;
        }

        .dashboard-tab-btn:hover { color: #0f172a; }
        .dashboard-tab-btn.active { color: var(--accent-blue); border-bottom-color: var(--accent-blue); }

        .calendar-wrapper {
            background: #ffffff;
            border-radius: 1.25rem;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
            height: 750px;
            display: flex;
            flex-direction: column;
        }

        .calendar-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .calendar-search { position: relative; width: 220px; }
        .calendar-search input {
            width: 100%; padding: 0.5rem 1rem 0.5rem 2.4rem;
            border-radius: 99px; border: 1px solid #e2e8f0;
            font-size: 0.8rem; background: #f8fafc; color: var(--text-main);
        }
        .calendar-search i { position: absolute; left: 0.8rem; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        .create-btn {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-green));
            color: white; padding: 0.5rem 1rem; border-radius: 99px;
            font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem;
            text-decoration: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .create-btn:hover { transform: translateY(-1px); color: white; }

        .cal-nav-btn {
            width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9;
            color: #475569; display: flex; align-items: center; justify-content: center; cursor: pointer; border:none;
        }
        .cal-nav-btn:hover { background: #e2e8f0; color: #0f172a; }

        .calendar-views { display: flex; background: #f1f5f9; padding: 3px; border-radius: 0.6rem; }
        .view-btn {
            padding: 0.35rem 0.8rem; border-radius: 0.4rem; font-size: 0.75rem; font-weight: 600;
            color: #64748b; background: transparent; border: none; cursor: pointer;
        }
        .view-btn.active { background: #ffffff; color: var(--accent-blue); box-shadow: 0 1px 3px rgba(0,0,0,0.05); }

        /* FullCalendar Tweaks */
        .fc-header-toolbar { display: none !important; }
        .fc-event { border: none !important; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .fc-daygrid-day-number { color: #334155; font-weight: 600; padding: 4px 8px !important; }
        .fc-col-header-cell-cushion { color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.7rem; padding: 10px 0 !important; }
        .fc-day-today { background-color: rgba(116, 178, 212, 0.06) !important; }
        
        .coming-soon-wrapper {
            background: #ffffff; border-radius: 1.25rem; padding: 4rem 2rem;
            text-align: center; border: 1px solid #e2e8f0; height: 400px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }

        /* --- CALENDAR EVENT CARD STYLE --- */

/* 1. Reset default FC styles to make room for custom cards */
.fc-event {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    margin-bottom: 4px !important;
}

/* 2. The Custom Card Container */
.fc-event-card {
    background: #ffffff;
    border-left: 4px solid #3b82f6; /* Default accent color */
    border-radius: 6px;
    padding: 6px 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    gap: 4px;
    width: 100%;
    overflow: hidden;
    transition: transform 0.1s;
}

.fc-event-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.12);
}

/* 3. Title & Time */
.fc-card-title {
    font-size: 0.75rem;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.fc-card-time {
    font-size: 0.65rem;
    color: #64748b;
    font-weight: 500;
}

/* 4. Avatar Stack */
.fc-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2px;
}

.fc-avatar-group {
    display: flex;
    align-items: center;
}

.fc-avatar {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 2px solid #ffffff;
    object-fit: cover;
    margin-left: -8px; /* Overlap effect */
    background: #f1f5f9;
}

.fc-avatar:first-child {
    margin-left: 0;
}

/* 5. Type Badge (e.g. "Meeting") */
.fc-type-badge {
    font-size: 0.6rem;
    padding: 1px 6px;
    border-radius: 99px;
    background: #f1f5f9;
    color: #64748b;
    text-transform: uppercase;
    font-weight: 700;
}

/* Specific view adjustments */
.fc-daygrid-event { white-space: normal !important; }
    </style>
<div class="dashboard-tabs-container">
    
    <div class="dashboard-tabs"> 
        <button class="dashboard-tab-btn active" onclick="switchTab('unreported', this)">
            <i class="ri-file-warning-line text-lg"></i> Nicht gemeldet
        </button>
    </div>

 

    <div id="tab-unreported" class="tab-content hidden">
        <div class="coming-soon-wrapper">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-50 mb-6">
                <i class="ri-time-line text-4xl text-blue-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3">Demnächst verfügbar</h3>
            <p class="text-gray-500 max-w-lg mx-auto leading-relaxed">
                Hier sehen Sie bald alle Termine, für die noch kein Bericht erstellt wurde. 
                Diese Funktion hilft Ihnen, Ihre Dokumentation aktuell zu halten.
            </p>
        </div>
    </div>

</div>

<div id="eventDetailModal" class="custom-modal-overlay" style="z-index: 9999;">
    <div class="custom-modal-container" style="max-width: 700px; height: auto; max-height: 90vh;">
        
        <div class="custom-modal-header bg-gray-50 border-b border-gray-100 px-6 py-4 flex justify-between items-center">
            <div class="flex flex-col">
                <span id="modalTypeBadge" class="self-start text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded bg-blue-100 text-blue-700 mb-1">
                    Termin
                </span>
                <h3 id="modalEventTitle" class="text-xl font-bold text-gray-800 line-clamp-1"></h3>
            </div>
            <button onclick="closeEventModal()" class="text-gray-400 hover:text-red-500 transition focus:outline-none">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        
        <div class="custom-modal-body p-6 space-y-5 bg-white overflow-y-auto">
            
            <div class="flex flex-col sm:flex-row items-stretch gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100 flex-1">
                    <div class="p-2 bg-white rounded-lg shadow-sm text-blue-500">
                        <i class="ri-calendar-event-line"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-gray-400">Datum</span>
                        <span id="modalDateRange" class="font-semibold text-gray-700"></span>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100 flex-1">
                    <div class="p-2 bg-white rounded-lg shadow-sm text-orange-500">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-gray-400">Uhrzeit</span>
                        <span id="modalTimeRange" class="font-semibold text-gray-700"></span>
                    </div>
                </div>
            </div>

            <div id="modalLocationSection" class="hidden">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wide block mb-2">Adresse / Ort</label>
                
                <div class="flex items-start gap-2 mb-3">
                    <i class="ri-map-pin-line text-gray-500 mt-0.5"></i>
                    <p id="modalAddress" class="text-sm font-medium text-gray-800"></p>
                </div>

                <div class="relative w-full h-48 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 shadow-sm group">
                    <iframe 
                        id="modalGoogleMap"
                        width="100%" 
                        height="100%" 
                        style="border:0" 
                        loading="lazy" 
                        allowfullscreen 
                        src="">
                    </iframe>
                    
                    <a id="modalMapBtn" href="#" target="_blank" class="absolute bottom-3 right-3 bg-white text-gray-800 text-xs font-bold px-3 py-2 rounded-lg shadow-md hover:bg-blue-600 hover:text-white transition flex items-center gap-2">
                        <i class="ri-direction-fill"></i> Route öffnen
                    </a>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Notiz / Beschreibung</label>
                <div id="modalDescription" class="mt-2 text-sm text-gray-600 bg-gray-50 p-4 rounded-xl border border-gray-100 min-h-[60px]">
                    Keine Notiz vorhanden.
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Mitarbeiter</label>
                <div id="modalTeam" class="flex items-center -space-x-2 mt-2 overflow-visible py-1 pl-1">
                    </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-xl">
            <button onclick="closeEventModal()" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm font-medium transition">
                Schließen
            </button>
            <a id="modalDetailLink" href="#" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5 no-underline flex items-center gap-2">
                <span>Zum Eintrag</span> <i class="ri-arrow-right-line"></i>
            </a>
        </div>
    </div>
</div>