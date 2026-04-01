<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Nuri Head 2.2</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- External Libs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- HTML2PDF for PDF Generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brand: {
                            dark: '#164191',   // Tiefblau
                            sky: '#74b2d4',    // Himmelblau
                            action: '#93c21c', // Frischgrün
                            light: '#cfe09b',  // Blassgrün
                            bg: '#f8fafc'      // Ultrahelles Blaugrau
                        }
                    },
                    boxShadow: {
                        'glow': '0 0 20px rgba(116, 178, 212, 0.3)',
                        'card': '0 10px 30px -10px rgba(22, 65, 145, 0.1)',
                        'floating': '0 -10px 40px rgba(0,0,0,0.08)'
                    }
                }
            }
        }
    </script>

    <style>
        /* Modern reset for mobile heights */
        html, body { 
            height: 100%; 
            height: 100dvh; /* Dynamic viewport height */
            background-color: #f8fafc; 
            -webkit-tap-highlight-color: transparent;
            overflow: hidden; /* Prevent body scroll, handle in views */
        }
        
        /* View Transitions */
        .view-screen {
            position: absolute; width: 100%; height: 100%; top: 0; left: 0;
            opacity: 0; visibility: hidden; transform: scale(0.98);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            background-color: #f8fafc; 
            overflow-y: auto; overflow-x: hidden;
            display: flex; flex-direction: column;
            padding-bottom: env(safe-area-inset-bottom); /* Safe area for swipe bars */
        }
        .view-screen.active { opacity: 1; visibility: visible; transform: scale(1); z-index: 10; }

        /* Custom UI Elements */
        .glass-panel { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .glass-dark { background: rgba(22, 65, 145, 0.2); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        
        /* Checkbox Animation */
        .task-checkbox:checked + div { background-color: #93c21c; border-color: #93c21c; transform: scale(1.05); }
        .task-checkbox:checked + div i { display: block; }
        
        /* Confetti Animation */
        @keyframes confetti-fall {
            0% { transform: translateY(-100%) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
        .confetti { position: absolute; width: 10px; height: 10px; background: #93c21c; animation: confetti-fall 3s linear infinite; }
        .confetti:nth-child(2n) { background: #74b2d4; width: 12px; height: 12px; animation-duration: 2.5s; }
        .confetti:nth-child(3n) { background: #ef4444; width: 8px; height: 8px; animation-duration: 3.2s; }
        .confetti:nth-child(4n) { background: #f97316; width: 14px; height: 14px; animation-duration: 2.8s; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 0px; background: transparent; }
        
        /* Timer Animation */
        .timer-active { animation: pulse-red 1s infinite; color: #ef4444; }
        @keyframes pulse-red { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        
        /* Slide Over Profile */
        #profile-panel { transition: transform 0.3s ease-in-out; }
        #profile-panel.open { transform: translateX(0); }

        /* Chat Bubbles */
        .chat-bubble-in { background-color: #f1f5f9; color: #334155; border-radius: 12px 12px 12px 0; }
        .chat-bubble-out { background-color: #164191; color: white; border-radius: 12px 12px 0 12px; }

        /* Timeline Styles */
        .timeline-line { position: absolute; left: 15px; top: 24px; bottom: -24px; width: 2px; background: #e2e8f0; z-index: 0; }
        .timeline-item:last-child .timeline-line { display: none; }

        /* Signature Pad */
        canvas.signature-canvas { touch-action: none; background: #fff; border-radius: 1rem; border: 2px dashed #cbd5e1; cursor: crosshair; width: 100%; height: 150px; }
        
        /* Voice Wave */
        .voice-wave { display: flex; gap: 4px; align-items: center; justify-content: center; height: 20px; }
        .voice-bar { width: 4px; background: #fff; animation: sound 0s linear infinite; border-radius: 4px; height: 4px;}
        .voice-active .voice-bar { animation-duration: 0.4s; }
        @keyframes sound { 0% { height: 4px; } 50% { height: 20px; } 100% { height: 4px; } }

        /* PDF Report Styles */
        .pdf-page {
            background: white;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            display: none; /* Hidden by default */
        }
        .pdf-visible { display: block; }
        
        .break-inside-avoid { page-break-inside: avoid; }

        /* Utilities for Safe Area */
        .pb-safe { padding-bottom: calc(1.5rem + env(safe-area-inset-bottom)); }
        .bottom-safe { bottom: env(safe-area-inset-bottom); }
    </style>
</head>
<body class="text-slate-600 h-full w-full overflow-hidden relative selection:bg-brand-sky selection:text-white">

    <!-- HIDDEN CAMERA INPUT -->
    <input type="file" id="hidden-camera-input" accept="image/*" capture="environment" class="hidden">

    <!-- MODAL OVERLAY -->
    <div id="modal-overlay" class="fixed inset-0 z-[1000] hidden flex items-center justify-center p-6 bg-black/40 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div id="modal-content" class="bg-white w-full max-w-sm rounded-[2rem] shadow-2xl p-6 transform scale-90 transition-transform duration-300 max-h-[85vh] overflow-y-auto">
            <!-- Injected via JS -->
        </div>
    </div>
    
    <!-- FULL SCREEN REPORT MODAL -->
    <div id="report-modal" class="fixed inset-0 z-[1200] hidden bg-slate-900/90 overflow-y-auto p-4 animate-fade-in">
        <div class="flex justify-end mb-4">
            <button onclick="closeReportModal()" class="text-white bg-white/10 hover:bg-white/20 p-2 rounded-full w-10 h-10 flex items-center justify-center transition"><i class="fa-solid fa-times"></i></button>
        </div>
        <div id="report-preview-area" class="bg-white rounded shadow-2xl mx-auto max-w-2xl min-h-[800px] p-8">
            <!-- Content injected here for preview/download -->
        </div>
        <div class="fixed bottom-6 right-6 flex gap-4 z-50">
            <button onclick="downloadPDF()" class="bg-brand-action text-white px-6 py-4 rounded-full font-bold shadow-xl hover:scale-105 transition flex items-center gap-2"><i class="fa-solid fa-file-pdf"></i> PDF herunterladen</button>
        </div>
    </div>

    <!-- PROFILE SLIDE OVER -->
    <div id="profile-panel" class="fixed inset-y-0 right-0 w-80 bg-white z-[1100] shadow-2xl transform translate-x-full p-6 flex flex-col border-l border-slate-100">
        <button onclick="toggleProfile()" class="absolute top-6 left-6 text-slate-400 hover:text-brand-dark"><i class="fa-solid fa-times text-xl"></i></button>
        <div class="text-center mt-12 mb-8">
            <img id="profile-img-large" src="" class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-slate-50 shadow-lg">
            <h2 id="profile-name-large" class="text-xl font-bold text-brand-dark">User Name</h2>
            <p id="profile-role-large" class="text-sm text-slate-400 font-bold uppercase">Role</p>
        </div>
        <div class="space-y-3 flex-1">
             <div class="bg-slate-50 p-4 rounded-2xl flex justify-between items-center">
                 <span class="font-bold text-slate-600">Schichten Gesamt</span>
                 <span class="font-black text-brand-dark">142</span>
             </div>
             <button onclick="viewAttendanceHistory()" class="w-full bg-blue-50 text-brand-dark font-bold py-4 rounded-2xl hover:bg-blue-100 transition flex items-center justify-between px-6">
                 <span><i class="fa-solid fa-clock-rotate-left mr-2"></i> Anwesenheitshistorie</span>
                 <i class="fa-solid fa-chevron-right text-xs"></i>
             </button>
        </div>
        <button onclick="logout()" class="w-full bg-slate-100 text-slate-500 font-bold py-4 rounded-2xl hover:bg-slate-200 transition mt-4"><i class="fa-solid fa-power-off mr-2"></i> Abmelden</button>
    </div>

    <!-- VIEW 1: AUTHENTICATION -->
    <div id="view-login" class="view-screen active items-center justify-center p-6 bg-white">
        <!-- Abstract Background Shapes -->
        <div class="absolute top-0 left-0 w-full h-1/2 bg-brand-dark rounded-b-[3rem] z-0"></div>
        <div class="absolute top-20 right-10 w-32 h-32 bg-brand-sky rounded-full blur-3xl opacity-50 z-0"></div>

        <div class="w-full max-w-md bg-white/90 backdrop-blur-xl rounded-[2.5rem] shadow-2xl p-8 relative z-10 border border-white/50">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-brand-action rounded-2xl mx-auto mb-4 flex items-center justify-center text-white text-3xl shadow-lg shadow-green-500/30 transform rotate-3">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-brand-dark">Nuri Head</h1>
                <p class="text-slate-400 text-sm mt-1">Wählen Sie Ihr Profil</p>
            </div>

            <!-- Search -->
            <div id="step-search" class="transition-all duration-300">
                <div class="relative group">
                    <input type="text" id="employee-input" placeholder="Mitarbeiter suchen..." 
                           class="w-full pl-12 pr-4 py-5 bg-slate-50 border-2 border-slate-100 focus:bg-white focus:border-brand-sky rounded-2xl outline-none transition-all font-bold text-brand-dark placeholder-slate-400 shadow-sm"
                           onkeyup="handleSearch(this.value)" onclick="handleSearch(this.value)">
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-5 text-slate-400"></i>
                    <div id="employee-list" class="absolute top-full left-0 w-full bg-white mt-3 rounded-2xl shadow-xl border border-slate-100 hidden z-50 max-h-48 overflow-y-auto p-2"></div>
                </div>
                <!-- Quick Select -->
                <div class="mt-4 grid grid-cols-4 gap-2" id="quick-select-container">
                    <!-- Injected via JS -->
                </div>
            </div>

            <!-- PIN -->
            <div id="step-pin" class="hidden flex-col items-center animate-fade-in-up">
                <div class="flex items-center gap-4 bg-slate-50 p-2 pr-4 rounded-full w-full border border-slate-100 mb-8 cursor-pointer hover:bg-slate-100 transition" onclick="resetLogin()">
                    <img id="pin-avatar" src="" class="w-12 h-12 rounded-full border-2 border-white shadow-sm object-cover">
                    <div class="flex-1">
                        <h3 id="pin-name" class="font-bold text-brand-dark text-sm">User Name</h3>
                        <p id="pin-role" class="text-xs text-brand-sky font-bold">Role</p>
                    </div>
                    <i class="fa-solid fa-times-circle text-slate-300 text-xl"></i>
                </div>

                <div class="flex gap-4 mb-8 justify-center">
                    <div class="w-3 h-3 rounded-full bg-slate-200 pin-dot"></div>
                    <div class="w-3 h-3 rounded-full bg-slate-200 pin-dot"></div>
                    <div class="w-3 h-3 rounded-full bg-slate-200 pin-dot"></div>
                    <div class="w-3 h-3 rounded-full bg-slate-200 pin-dot"></div>
                </div>

                <div class="grid grid-cols-3 gap-3 w-full max-w-[260px]">
                    <button onclick="enterPin(1)" class="h-16 rounded-2xl font-bold text-xl text-brand-dark bg-white shadow-sm border border-slate-100 active:bg-brand-sky active:text-white transition">1</button>
                    <button onclick="enterPin(2)" class="h-16 rounded-2xl font-bold text-xl text-brand-dark bg-white shadow-sm border border-slate-100 active:bg-brand-sky active:text-white transition">2</button>
                    <button onclick="enterPin(3)" class="h-16 rounded-2xl font-bold text-xl text-brand-dark bg-white shadow-sm border border-slate-100 active:bg-brand-sky active:text-white transition">3</button>
                    <button onclick="enterPin(4)" class="h-16 rounded-2xl font-bold text-xl text-brand-dark bg-white shadow-sm border border-slate-100 active:bg-brand-sky active:text-white transition">4</button>
                    <button onclick="enterPin(5)" class="h-16 rounded-2xl font-bold text-xl text-brand-dark bg-white shadow-sm border border-slate-100 active:bg-brand-sky active:text-white transition">5</button>
                    <button onclick="enterPin(6)" class="h-16 rounded-2xl font-bold text-xl text-brand-dark bg-white shadow-sm border border-slate-100 active:bg-brand-sky active:text-white transition">6</button>
                    <button onclick="enterPin(7)" class="h-16 rounded-2xl font-bold text-xl text-brand-dark bg-white shadow-sm border border-slate-100 active:bg-brand-sky active:text-white transition">7</button>
                    <button onclick="enterPin(8)" class="h-16 rounded-2xl font-bold text-xl text-brand-dark bg-white shadow-sm border border-slate-100 active:bg-brand-sky active:text-white transition">8</button>
                    <button onclick="enterPin(9)" class="h-16 rounded-2xl font-bold text-xl text-brand-dark bg-white shadow-sm border border-slate-100 active:bg-brand-sky active:text-white transition">9</button>
                    <button onclick="enterPin('del')" class="h-16 rounded-2xl flex items-center justify-center text-red-400 bg-transparent active:scale-95 transition"><i class="fa-solid fa-delete-left text-xl"></i></button>
                    <button onclick="enterPin(0)" class="h-16 rounded-2xl font-bold text-xl text-brand-dark bg-white shadow-sm border border-slate-100 active:bg-brand-sky active:text-white transition">0</button>
                    <button onclick="submitPin()" class="h-16 rounded-2xl flex items-center justify-center text-white bg-brand-action shadow-lg shadow-green-500/30 active:scale-95 transition"><i class="fa-solid fa-arrow-right text-xl"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- VIEW 2: DASHBOARD -->
    <div id="view-dashboard" class="view-screen bg-brand-bg">
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-30 px-6 py-4 flex justify-between items-center rounded-b-[2rem] shadow-sm border-b border-white">
            <div class="flex items-center gap-3">
                <img id="dash-avatar" onclick="toggleProfile()" src="" class="w-10 h-10 rounded-full border-2 border-brand-action shadow-md cursor-pointer">
                <div>
                    <h2 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Schicht-Timer</h2>
                    <h1 class="text-xl font-extrabold text-brand-dark leading-tight font-mono" id="global-shift-timer">00:00:00</h1>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="performCheckOut()" class="h-10 px-4 rounded-full bg-red-50 text-red-500 font-bold text-xs flex items-center gap-2 hover:bg-red-100 transition border border-red-100 shadow-sm">
                    <i class="fa-solid fa-sign-out-alt"></i> Ausstempeln
                </button>
                <button onclick="openModal('notifications')" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-brand-dark relative">
                    <i class="fa-regular fa-bell"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                </button>
            </div>
        </header>

        <div class="p-6 pb-40">
            
            <!-- PROGRESS SUMMARY CARD -->
            <div class="bg-gradient-to-r from-brand-dark to-blue-800 rounded-[2rem] p-6 text-white shadow-lg shadow-blue-900/20 mb-8 relative overflow-hidden">
                <div class="absolute -right-5 -top-5 w-32 h-32 bg-white opacity-10 rounded-full"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-end mb-4">
                        <div>
                            <p class="text-blue-200 text-xs font-bold uppercase tracking-wider mb-1">Tagesfortschritt</p>
                            <h3 class="text-2xl font-bold" id="dash-greeting">Guten Morgen!</h3>
                        </div>
                        <div class="text-right">
                             <span class="text-3xl font-black" id="dash-progress-text">0/3</span>
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="w-full bg-black/20 h-2 rounded-full overflow-hidden mb-4">
                        <div id="dash-progress-bar" class="bg-brand-action h-full rounded-full transition-all duration-1000" style="width: 0%"></div>
                    </div>
                    <!-- Action Buttons on Card -->
                    <div class="flex gap-2">
                        <button onclick="renderTasks(); alert('Erfolgreich synchronisiert!');" class="bg-white/20 hover:bg-white/30 text-white text-[10px] font-bold py-2 px-4 rounded-xl transition flex items-center gap-2 backdrop-blur-sm">
                            <i class="fa-solid fa-arrows-rotate"></i> Sync
                        </button>
                        <button onclick="openModal('team')" class="bg-white/20 hover:bg-white/30 text-white text-[10px] font-bold py-2 px-4 rounded-xl transition flex items-center gap-2 backdrop-blur-sm">
                            <i class="fa-solid fa-users"></i> Team
                        </button>
                         <button onclick="shareDailyProgress()" class="bg-white/20 hover:bg-white/30 text-white text-[10px] font-bold py-2 px-4 rounded-xl transition flex items-center gap-2 backdrop-blur-sm">
                            <i class="fa-solid fa-share-nodes"></i> Teilen
                        </button>
                    </div>
                </div>
            </div>

            <!-- TABS -->
            <div class="flex bg-white p-1 rounded-2xl shadow-sm mb-6">
                <button onclick="switchDashTab('todo')" id="tab-todo" class="flex-1 py-3 rounded-xl text-sm font-bold bg-slate-100 text-brand-dark transition shadow-sm">Aufgaben</button>
                <button onclick="switchDashTab('history')" id="tab-history" class="flex-1 py-3 rounded-xl text-sm font-bold text-slate-400 hover:text-slate-600 transition">Verlauf</button>
            </div>

            <div id="task-container" class="space-y-5 min-h-[200px]">
                <!-- Tasks Injected JS -->
            </div>
            
        </div>

        <!-- FLOATING HELP BUTTON -->
        <button onclick="openModal('help')" class="fixed bottom-6 right-6 w-14 h-14 bg-white text-red-500 rounded-full shadow-2xl flex items-center justify-center text-2xl z-40 border-2 border-red-50 animate-bounce-slow hover:scale-105 transition">
            <i class="fa-solid fa-headset"></i>
        </button>
    </div>

    <!-- VIEW 3: PLAN ATTENDANCE (VORBEREITUNG) -->
    <div id="view-attendance" class="view-screen bg-white">
        <div class="p-6 pb-40">
            <button onclick="navTo('view-dashboard')" class="mb-4 w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-brand-dark hover:text-white transition shadow-sm"><i class="fa-solid fa-arrow-left"></i></button>
            
            <h1 class="text-3xl font-extrabold text-brand-dark leading-tight mb-1" id="att-plan-title">Service A</h1>
            <p class="text-slate-400 font-medium mb-6">Vorbereitung für diesen Auftrag.</p>

            <!-- TABS -->
            <div class="flex bg-slate-100 p-1 rounded-xl mb-6">
                <button onclick="switchAttTab('team')" id="att-tab-team" class="flex-1 py-3 rounded-lg text-sm font-bold bg-white text-brand-dark shadow-sm transition">Team</button>
                <button onclick="switchAttTab('material')" id="att-tab-material" class="flex-1 py-3 rounded-lg text-sm font-bold text-slate-400 hover:text-slate-600 transition">Material</button>
            </div>

            <!-- TEAM CONTENT -->
            <div id="att-content-team">
                <button onclick="markAllPresent()" class="w-full mb-6 py-4 border-2 border-dashed border-brand-action/50 text-brand-action rounded-2xl font-bold hover:bg-brand-action/5 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check-double"></i> Alle anwesend markieren
                </button>
                <div id="attendance-list" class="space-y-4"></div>
            </div>

            <!-- MATERIAL CONTENT -->
            <div id="att-content-material" class="hidden">
                <div class="bg-slate-50 p-4 rounded-[1.5rem] border border-slate-100">
                    <div id="material-list-items" class="space-y-2 mb-4">
                        <!-- Items Injected Here -->
                    </div>
                    
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text" id="material-input" list="material-suggestions" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-600 outline-none focus:border-brand-sky" placeholder="Material hinzufügen...">
                            <datalist id="material-suggestions">
                                <option value="Laptop"></option>
                                <option value="Werkzeugkoffer"></option>
                                <option value="Helm"></option>
                                <option value="Leiter"></option>
                                <option value="Kabel"></option>
                                <option value="Reinigungsset"></option>
                                <option value="Bohrmaschine"></option>
                                <option value="Sicherheitsschuhe"></option>
                            </datalist>
                        </div>
                        <button onclick="addMaterial()" class="bg-brand-action text-white w-12 h-12 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/20 active:scale-95 transition flex-shrink-0"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
            </div>

        </div>
        <!-- FIXED BOTTOM BAR with Safe Area -->
        <div class="fixed bottom-0 w-full p-6 bg-white border-t border-slate-50 shadow-floating z-40 rounded-t-[2.5rem] pb-safe">
            <button onclick="confirmAttendanceAndStart()" class="w-full bg-brand-dark text-white font-bold text-lg py-5 rounded-2xl shadow-xl shadow-blue-900/20 active:scale-95 transition flex items-center justify-center gap-3">
                <span>Bestätigen & Start</span> <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- VIEW 4: SETUP (ORIGIN) -->
    <div id="view-setup" class="view-screen bg-white">
        <div class="p-6 pb-48">
            <button onclick="navTo('view-attendance')" class="mb-6 w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-brand-dark hover:text-white transition shadow-sm"><i class="fa-solid fa-arrow-left"></i></button>
            <h1 class="text-3xl font-extrabold text-brand-dark mb-2">Abfahrt von?</h1>
            <p class="text-slate-500 mb-8 font-medium">Route für <span id="setup-plan-name" class="text-brand-sky"></span></p>

            <div class="grid gap-4">
                <div onclick="selectOrigin(this, 'office')" class="origin-card p-5 border-2 border-slate-100 rounded-[2rem] cursor-pointer hover:border-brand-sky hover:bg-brand-sky/5 transition flex items-center gap-5 group">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 text-brand-dark flex items-center justify-center text-2xl group-hover:bg-brand-dark group-hover:text-white transition shadow-sm"><i class="fa-solid fa-building"></i></div>
                    <div class="flex-1">
                        <h3 class="font-bold text-slate-800 text-lg">Hauptbüro</h3>
                        <select class="mt-1 w-full text-sm bg-transparent text-slate-500 font-semibold outline-none" onclick="event.stopPropagation()">
                            <option>Hauptquartier - NY</option><option>Westflügel - NJ</option>
                        </select>
                    </div>
                </div>

                <div onclick="selectOrigin(this, 'gps')" class="origin-card p-5 border-2 border-slate-100 rounded-[2rem] cursor-pointer hover:border-brand-sky hover:bg-brand-sky/5 transition flex items-center gap-5 group">
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-2xl group-hover:bg-orange-500 group-hover:text-white transition shadow-sm"><i class="fa-solid fa-location-crosshairs"></i></div>
                    <div class="flex-1">
                        <h3 class="font-bold text-slate-800 text-lg">Aktueller Standort</h3>
                        <p class="text-xs text-slate-400 font-bold mt-1" id="gps-status">Tippen für GPS</p>
                    </div>
                </div>
                
                 <div onclick="selectOrigin(this, 'customer')" class="origin-card p-5 border-2 border-slate-100 rounded-[2rem] cursor-pointer hover:border-brand-sky hover:bg-brand-sky/5 transition flex items-center gap-5 group">
                    <div class="w-16 h-16 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl group-hover:bg-purple-600 group-hover:text-white transition shadow-sm"><i class="fa-solid fa-user-tie"></i></div>
                    <div class="flex-1">
                        <h3 class="font-bold text-slate-800 text-lg">Kundenstandort</h3>
                        <select class="mt-1 w-full text-sm bg-transparent text-slate-500 font-semibold outline-none" onclick="event.stopPropagation()">
                            <option>Kunden wählen...</option><option>Acme Corp</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="route-preview" class="fixed bottom-0 left-0 w-full bg-white shadow-floating rounded-t-[2.5rem] p-8 pb-safe transform translate-y-full transition-transform z-50">
                <div class="flex justify-between items-center mb-8">
                    <div><p class="text-xs text-slate-400 font-bold uppercase">Ankunftszeit</p><h2 class="text-3xl font-extrabold text-brand-dark">24 Min</h2></div>
                    <div class="text-right"><p class="text-xs text-slate-400 font-bold uppercase">Entfernung</p><h2 class="text-2xl font-bold text-slate-600">12.8 km</h2></div>
                </div>
                <button onclick="startActiveMode()" class="w-full bg-brand-action text-white font-bold text-lg py-5 rounded-2xl shadow-lg shadow-green-500/30 active:scale-95 transition">Fahrt starten <i class="fa-solid fa-car ml-2"></i></button>
            </div>
        </div>
    </div>

    <!-- VIEW 5: ACTIVE MAP -->
    <div id="view-active" class="view-screen overflow-hidden">
        <div id="active-map" class="full-map bg-slate-200"></div>
        <div class="absolute top-6 left-6 right-6 z-[400] glass-panel p-5 rounded-[2rem] shadow-lg flex items-center justify-between">
            <div><p class="text-[10px] text-slate-500 font-black uppercase tracking-widest">Zielort</p><h3 class="font-extrabold text-brand-dark text-lg leading-tight" id="active-dest-name">Acme Corp HQ</h3></div>
            <div class="bg-brand-action text-white px-4 py-2 rounded-xl text-xs font-bold animate-pulse shadow-lg shadow-green-500/20">Unterwegs</div>
        </div>
        <div class="absolute bottom-0 w-full z-[400] bottom-sheet p-8 pb-safe">
            <div class="w-16 h-1.5 bg-slate-200 rounded-full mx-auto mb-8"></div>
            <div class="grid grid-cols-3 gap-4">
                <button onclick="openModal('pause')" class="h-24 rounded-3xl bg-orange-50 text-orange-500 hover:bg-orange-100 transition flex flex-col items-center justify-center gap-2"><i class="fa-solid fa-pause text-2xl"></i><span class="font-bold text-xs">Pause</span></button>
                <button onclick="openModal('stop')" class="h-24 rounded-3xl bg-red-50 text-red-500 hover:bg-red-100 transition flex flex-col items-center justify-center gap-2"><i class="fa-solid fa-ban text-2xl"></i><span class="font-bold text-xs">Stopp</span></button>
                <button onclick="handleArrive()" class="h-24 rounded-3xl bg-brand-dark text-white shadow-lg shadow-blue-900/20 active:scale-95 transition flex flex-col items-center justify-center gap-2"><i class="fa-solid fa-flag-checkered text-2xl"></i><span class="font-bold text-xs">Angekommen</span></button>
            </div>
        </div>
    </div>

    <!-- VIEW 6: CHECKLIST -->
    <div id="view-checklist" class="view-screen bg-brand-bg">
        <header class="bg-brand-dark text-white p-8 pb-16 rounded-b-[3rem] shadow-card relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
            <div class="relative z-10">
                <h2 class="text-brand-sky text-xs font-black uppercase tracking-widest mb-2">Vor-Ort-Ausführung</h2>
                <h1 class="text-3xl font-extrabold mb-1" id="checklist-title">Kundenbesprechung</h1>
                <p class="text-sm opacity-80 font-medium"><i class="fa-solid fa-location-dot mr-1"></i> Eingecheckt: <span id="checkin-time"></span></p>
            </div>
        </header>

        <div class="p-6 -mt-10 pb-40 relative z-20">
            <div class="bg-white rounded-[2.5rem] shadow-card p-6 mb-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-700 text-lg">Checkliste</h3>
                    <div class="text-xs bg-slate-100 px-3 py-1.5 rounded-full font-bold text-slate-500">Gesamt: <span id="total-minutes">0</span> min</div>
                </div>

                <div id="checklist-container" class="space-y-4"></div>

                <div class="mt-6 pt-4 border-t border-slate-50">
                    <div class="flex gap-3">
                        <input type="text" id="new-task-input" placeholder="Neue Aufgabe..." class="flex-1 bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-brand-sky focus:bg-white transition">
                        <button onclick="addNewTaskInline()" class="bg-brand-sky text-white w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg shadow-sky-500/30 hover:bg-brand-dark transition"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
            </div>

            <!-- Integrated Controls -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                 <button onclick="openModal('expense')" class="bg-white py-4 rounded-[1.5rem] text-slate-600 font-bold hover:bg-slate-50 transition flex items-center justify-center gap-2 border border-slate-100 shadow-sm"><i class="fa-solid fa-receipt text-green-500"></i> Ausgaben</button>
                 <div class="bg-white rounded-[1.5rem] p-1.5 border border-slate-100 flex shadow-sm">
                     <button onclick="openModal('pause')" class="flex-1 rounded-2xl text-orange-500 hover:bg-orange-50 transition flex items-center justify-center"><i class="fa-solid fa-pause"></i></button>
                     <div class="w-px bg-slate-100 my-2"></div>
                     <button onclick="openModal('stop')" class="flex-1 rounded-2xl text-red-500 hover:bg-red-50 transition flex items-center justify-center"><i class="fa-solid fa-ban"></i></button>
                 </div>
            </div>

            <button onclick="finishJob()" class="w-full bg-brand-action text-white font-bold text-xl py-5 rounded-[2rem] shadow-xl shadow-green-500/30 active:scale-95 transition flex items-center justify-center gap-3">
                <i class="fa-solid fa-clipboard-check"></i> Auftrag erledigt
            </button>
        </div>
    </div>

    <!-- VIEW 7: REPORT -->
    <div id="view-report" class="view-screen bg-white p-6 overflow-y-auto pb-40">
        <h1 class="text-3xl font-extrabold text-brand-dark mb-4">Abschlussbericht</h1>
        
        <!-- Voice Report Section -->
        <div class="bg-slate-50 rounded-[2.5rem] p-6 mb-4 border border-slate-100 h-64 relative">
            <textarea id="report-text" class="w-full h-full bg-transparent border-none resize-none focus:outline-none text-slate-600 font-medium placeholder-slate-400 text-lg" placeholder="Notizen eingeben oder diktieren..."></textarea>
            <div id="voice-indicator" class="hidden absolute bottom-6 left-6 voice-wave">
                <div class="voice-bar"></div><div class="voice-bar"></div><div class="voice-bar"></div><div class="voice-bar"></div>
            </div>
            <button onclick="toggleVoice()" id="mic-btn" class="absolute bottom-6 right-6 w-14 h-14 bg-brand-dark rounded-full text-white shadow-xl shadow-blue-900/30 flex items-center justify-center active:scale-90 transition z-10"><i class="fa-solid fa-microphone text-xl"></i></button>
        </div>

        <!-- Signature Section -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-2 px-2">
                <h3 class="font-bold text-slate-700">Digitale Unterschrift</h3>
                <button onclick="clearSignature('signature-pad')" class="text-xs font-bold text-red-400">Löschen</button>
            </div>
            <canvas id="signature-pad" class="signature-canvas"></canvas>
            <p class="text-xs text-slate-400 mt-2 text-center">Oben unterschreiben zur Bestätigung</p>
        </div>

        <button onclick="completeAndNext()" class="w-full bg-brand-dark text-white font-bold text-xl py-5 rounded-[2rem] shadow-xl shadow-blue-900/20 active:scale-95 transition mb-safe">Speichern & Weiter</button>
        <div class="h-12"></div>
    </div>

    <!-- VIEW 8: AWARD / SUMMARY -->
    <div id="view-summary" class="view-screen bg-gradient-to-br from-brand-dark via-blue-900 to-slate-900 items-center justify-center p-8 relative overflow-hidden">
        <div id="confetti-container" class="absolute inset-0 pointer-events-none"></div>

        <div class="text-center w-full max-w-lg mx-auto relative z-10">
            <div class="w-32 h-32 bg-gradient-to-tr from-brand-action to-green-300 rounded-full flex items-center justify-center text-white text-5xl shadow-2xl shadow-green-500/50 mx-auto mb-8 animate-bounce border-4 border-white/20">
                <i class="fa-solid fa-trophy"></i>
            </div>
            
            <h1 class="text-4xl font-black text-white mb-2 tracking-tight drop-shadow-lg">Mission Erfüllt!</h1>
            <p class="text-blue-200 text-lg mb-10 font-medium">Hervorragende Arbeit heute.</p>

            <div class="glass-dark rounded-[2.5rem] p-8 mb-8 text-left shadow-2xl">
                <div class="flex justify-between items-center mb-8 border-b border-white/10 pb-6">
                    <div>
                        <p class="text-[10px] text-blue-300 font-black uppercase tracking-widest mb-1">Gesamtdauer</p>
                        <h2 class="text-3xl font-bold text-white">4h 30m</h2>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-blue-300 font-black uppercase tracking-widest mb-1">Erledigte Aufgaben</p>
                        <h2 class="text-4xl font-black text-brand-action drop-shadow-lg" id="total-completed-count">0</h2>
                    </div>
                </div>
                
                <h3 class="font-bold text-white mb-4 text-sm uppercase tracking-wide opacity-80">Tagesprotokoll</h3>
                <div id="summary-list" class="space-y-3 max-h-48 overflow-y-auto pr-2"></div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                 <button onclick="location.reload()" class="bg-white/10 text-white font-bold py-4 rounded-2xl hover:bg-white hover:text-brand-dark transition border border-white/20 backdrop-blur-md">App schließen</button>
                 <button onclick="viewReportFromSummary()" class="bg-brand-action text-white font-bold py-4 rounded-2xl shadow-lg transition"><i class="fa-solid fa-file-pdf mr-2"></i> Bericht</button>
            </div>
        </div>
    </div>

    <!-- VIEW 9: JOURNEY HISTORY -->
    <div id="view-journey-history" class="view-screen bg-white">
        <div class="relative h-1/2 bg-slate-200">
            <button onclick="navTo('view-dashboard')" class="absolute top-6 left-6 z-[1000] w-10 h-10 rounded-full bg-white text-slate-700 shadow-md flex items-center justify-center hover:bg-slate-50"><i class="fa-solid fa-arrow-left"></i></button>
            <div id="history-map" class="w-full h-full"></div>
        </div>
        <div class="h-1/2 bg-white rounded-t-[2.5rem] -mt-10 relative z-10 p-8 shadow-floating flex flex-col">
            <div class="w-12 h-1 bg-slate-200 rounded-full mx-auto mb-6"></div>
            <h2 class="text-2xl font-extrabold text-brand-dark mb-4" id="history-title">Fahrtenbuch</h2>
            <div id="journey-timeline" class="flex-1 overflow-y-auto space-y-0 relative pl-2 pb-20">
                <!-- Injected via JS -->
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        /* --- DATA & PERSISTENCE --- */
        // UPDATED EMPLOYEE LIST
        const employees = [
            { id: 1, name: "Sadid, Ramin", role: "Projektmanager", img: "https://i.pravatar.cc/150?img=11", status: "Verfügbar" },
            { id: 2, name: "Nuri, Yama", role: "Senior Agent", img: "https://i.pravatar.cc/150?img=60", status: "Beschäftigt" },
            { id: 3, name: "Rasuli, Ferdaus", role: "Sicherheitsbeauftragter", img: "https://i.pravatar.cc/150?img=33", status: "Verfügbar" },
            { id: 4, name: "Nuri, Kathrin", role: "Logistik-Koord.", img: "https://i.pravatar.cc/150?img=44", status: "Im Urlaub" }
        ];

        // Default tasks for Reset
        const defaultTasks = [
            { 
                id: 101, title: "Server-Wartung", location: "TechCorp HQ", time: "09:00 AM", team: [1,2], status: "pending", report: "", 
                delegatedTo: null, attendance: {}, 
                events: [], expenses: [],
                materials: ["Schraubendreher", "Kabelbinder", "Reinigungstuch"],
                checklist: [{ txt: "Spannung prüfen", done: false, duration: 15, remark: "", hasPhoto: false, photo: null }, { txt: "Lüfter reinigen", done: false, duration: 30, remark: "", hasPhoto: false, photo: null }] 
            },
            { 
                id: 102, title: "Kundenbesprechung", location: "Café Innenstadt", time: "11:30 AM", team: [1], status: "pending", report: "", 
                delegatedTo: null, attendance: {}, 
                events: [], expenses: [],
                materials: ["Laptop", "Präsentationsmappe", "Vertragsunterlagen"],
                checklist: [{ txt: "Vertrag unterschreiben", done: false, duration: 60, remark: "", hasPhoto: false, photo: null }] 
            },
            { 
                id: 103, title: "Standortinspektion", location: "Lagerhalle 13", time: "02:00 PM", team: [3,4], status: "pending", report: "", 
                delegatedTo: null, attendance: {}, 
                events: [], expenses: [],
                materials: ["Helm", "Warnweste", "Maßband"],
                checklist: [{ txt: "Bereich vermessen", done: false, duration: 20, remark: "", hasPhoto: false, photo: null }, { txt: "Schäden fotografieren", done: false, duration: 10, remark: "", hasPhoto: false, photo: null }] 
            },
        ];

        let tasks = [];
        let attendanceLog = [];
        let selectedEmployee = null;
        let currentPin = "";
        let map = null;
        let historyMap = null;
        let activeTaskId = null;
        let activeTimer = null; 
        let shiftStartTime = null;
        let shiftInterval = null;
        let activeDashTab = 'todo';
        let signaturePadContext = null;
        let isDrawing = false;
        let isListening = false;
        let recognition = null;
        let chatMessages = [
            { sender: 'bot', text: 'WorkForce Support online. Wie kann ich helfen?', time: 'Jetzt' }
        ];
        
        // Reminder System Variables
        const OFFICIAL_WORK_END_HOUR = 17; // 5 PM
        let lastReminderTime = 0;
        let reminderInterval = null;

        // Initialization
        window.onload = function() {
            // Force reset on load if version mismatch (simple way to ensure new names appear)
            const v = localStorage.getItem('wf_version');
            if(v !== '2.9') {
                localStorage.clear();
                localStorage.setItem('wf_version', '2.9');
            }
            
            loadData();
            document.getElementById('hidden-camera-input').addEventListener('change', handleCameraInput);
            initSignaturePad('signature-pad'); // Init report signature pad
            initVoice();
            
            // Start reminder check loop
            reminderInterval = setInterval(checkShiftEnd, 60000); // Check every minute
        };

        function loadData() {
            const stored = localStorage.getItem('wf_tasks');
            if (stored) { tasks = JSON.parse(stored); } 
            else { tasks = JSON.parse(JSON.stringify(defaultTasks)); }
            
            const storedShift = localStorage.getItem('wf_shift_start');
            if(storedShift) {
                shiftStartTime = parseInt(storedShift);
                startShiftTimerDisplay();
            }
            
            const storedAtt = localStorage.getItem('wf_attendance_log');
            if (storedAtt) { attendanceLog = JSON.parse(storedAtt); }
        }

        function saveData() { 
            localStorage.setItem('wf_tasks', JSON.stringify(tasks));
            localStorage.setItem('wf_attendance_log', JSON.stringify(attendanceLog));
        }

        /* --- ATTENDANCE SYSTEM --- */
        function performCheckIn(userId) {
            const today = new Date().toLocaleDateString();
            const openSession = attendanceLog.find(log => log.userId === userId && log.date === today && !log.checkOut);
            
            if (!openSession) {
                const newLog = {
                    id: Date.now(),
                    userId: userId,
                    date: today,
                    checkIn: new Date().toISOString(),
                    checkOut: null
                };
                attendanceLog.push(newLog);
                saveData();
                alert(`Erfolgreich eingecheckt um ${new Date().toLocaleTimeString()}`);
            }
        }

        function performCheckOut() {
            if(!selectedEmployee) return;
            const today = new Date().toLocaleDateString();
            const sessionIndex = attendanceLog.findIndex(log => log.userId === selectedEmployee.id && log.date === today && !log.checkOut);
            
            if (sessionIndex > -1) {
                attendanceLog[sessionIndex].checkOut = new Date().toISOString();
                saveData();
                
                localStorage.removeItem('wf_shift_start');
                clearInterval(shiftInterval);
                document.getElementById('global-shift-timer').innerText = "00:00:00";
                
                alert(`Erfolgreich ausgestempelt um ${new Date().toLocaleTimeString()}\nSchicht beendet.`);
                location.reload(); 
            } else {
                alert("Kein aktiver Check-In gefunden.");
            }
        }

        function checkShiftEnd() {
            if (!selectedEmployee) return;
            const today = new Date().toLocaleDateString();
            const openSession = attendanceLog.find(log => log.userId === selectedEmployee.id && log.date === today && !log.checkOut);
            
            if (openSession) {
                const now = new Date();
                if (now.getHours() >= OFFICIAL_WORK_END_HOUR) {
                    if (Date.now() - lastReminderTime > 10 * 60 * 1000) {
                        alert("⚠️ OFFIZIELLE ARBEITSZEIT BEENDET ⚠️\n\nBitte sofort ausstempeln, wenn Sie fertig sind.");
                        lastReminderTime = Date.now();
                    }
                }
            }
        }

        function viewAttendanceHistory() {
            const myLogs = attendanceLog.filter(l => l.userId === selectedEmployee.id).reverse();
            const content = document.getElementById('modal-content');
            
            let html = `
                <div class="text-left">
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Anwesenheitshistorie</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
            `;
            
            if(myLogs.length === 0) {
                html += `<p class="text-slate-400 italic text-center">Keine Einträge gefunden.</p>`;
            } else {
                myLogs.forEach(log => {
                    const inTime = new Date(log.checkIn).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    const outTime = log.checkOut ? new Date(log.checkOut).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'Aktiv';
                    const date = new Date(log.checkIn).toLocaleDateString();
                    
                    let durationStr = "-";
                    if(log.checkOut) {
                        const diffMs = new Date(log.checkOut) - new Date(log.checkIn);
                        const hrs = Math.floor(diffMs / 3600000);
                        const mins = Math.floor((diffMs % 3600000) / 60000);
                        durationStr = `${hrs}h ${mins}m`;
                    }

                    html += `
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-slate-700">${date}</span>
                                <span class="text-xs font-bold ${log.checkOut ? 'text-slate-400' : 'text-green-500'}">${log.checkOut ? 'Abgeschlossen' : 'Aktiv'}</span>
                            </div>
                            <div class="flex justify-between text-xs text-slate-500">
                                <span>Kommen: ${inTime}</span>
                                <span>Gehen: ${outTime}</span>
                            </div>
                            <div class="text-right mt-1 text-xs font-bold text-brand-dark">Dauer: ${durationStr}</div>
                        </div>
                    `;
                });
            }
            html += `</div><button onclick="closeModal()" class="w-full mt-4 bg-brand-dark text-white font-bold py-3 rounded-xl">Schließen</button></div>`;
            content.innerHTML = html;
            openModal('custom_html'); 
        }

        /* --- JOB CANCELLATION LOGIC --- */
        function initCancelJob(id = null) {
            if(id) activeTaskId = id;
            openModal('cancel_job');
            // Wait for modal to render then init canvas
            setTimeout(() => {
                initSignaturePad('cancel-signature-pad');
            }, 100);
        }
        
        function openTaskMenu(taskId) {
            activeTaskId = taskId;
            const content = document.getElementById('modal-content');
            content.innerHTML = `
                <div class="text-left">
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Optionen</h3>
                    <div class="space-y-3">
                        <button onclick="openModal('delegate', ${taskId})" class="w-full bg-white border-2 border-slate-100 text-slate-700 font-bold py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-slate-50 transition">
                            <i class="fa-solid fa-user-plus text-brand-dark"></i> Aufgabe delegieren
                        </button>
                        <button onclick="initCancelJob(${taskId})" class="w-full bg-white border-2 border-red-100 text-red-500 font-bold py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-red-50 transition">
                            <i class="fa-solid fa-ban"></i> Auftrag stornieren
                        </button>
                    </div>
                    <button onclick="closeModal()" class="w-full mt-4 text-slate-400 font-bold py-3">Abbrechen</button>
                </div>
            `;
            openModal('custom_html');
        }

        function submitCancellation() {
            const reason = document.getElementById('cancel-reason').value;
            const canvas = document.getElementById('cancel-signature-pad');
            
            // Basic check if signed (compare to empty canvas)
            const blank = document.createElement('canvas');
            blank.width = canvas.width; blank.height = canvas.height;
            if(canvas.toDataURL() === blank.toDataURL()) {
                // alert("Please sign to confirm cancellation.");
                // return;
            }
            
            if(!reason || reason === "") { alert("Bitte geben Sie einen Stornierungsgrund an."); return; }

            const taskIndex = tasks.findIndex(t => t.id === activeTaskId);
            if(taskIndex > -1) {
                tasks[taskIndex].status = 'cancelled';
                tasks[taskIndex].cancellationReason = reason;
                tasks[taskIndex].signature = canvas.toDataURL(); // Save auth signature
                tasks[taskIndex].report = `Auftrag storniert. Grund: ${reason}`;
            }
            saveData();
            
            activeTaskId = null;
            clearInterval(activeTimer);
            closeModal();
            renderTasks();
            navTo('view-dashboard');
        }

        /* --- LOG EVENT HELPER --- */
        function logEvent(taskId, type, meta = {}) {
            const task = tasks.find(t => t.id === taskId);
            if (!task.events) task.events = [];
            
            const baseLat = 40.7128; 
            const baseLng = -74.0060;
            const offset = (Math.random() - 0.5) * 0.02; 
            
            task.events.push({
                type: type,
                time: new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}),
                lat: baseLat + offset,
                lng: baseLng + offset,
                meta: meta
            });
            saveData();
        }

        /* --- LOGIC --- */
        function navTo(viewId) {
            document.querySelectorAll('.view-screen').forEach(el => el.classList.remove('active'));
            document.getElementById(viewId).classList.add('active');
        }
        
        function switchDashTab(tab) {
            activeDashTab = tab;
            const btnTodo = document.getElementById('tab-todo');
            const btnHist = document.getElementById('tab-history');
            
            if(tab === 'todo') {
                btnTodo.className = "flex-1 py-3 rounded-xl text-sm font-bold bg-slate-100 text-brand-dark transition shadow-sm";
                btnHist.className = "flex-1 py-3 rounded-xl text-sm font-bold text-slate-400 hover:text-slate-600 transition";
            } else {
                btnHist.className = "flex-1 py-3 rounded-xl text-sm font-bold bg-slate-100 text-brand-dark transition shadow-sm";
                btnTodo.className = "flex-1 py-3 rounded-xl text-sm font-bold text-slate-400 hover:text-slate-600 transition";
            }
            renderTasks();
        }

        function shareDailyProgress() {
            const completed = tasks.filter(t => t.status === 'completed' || t.status === 'cancelled').length;
            const total = tasks.length;
            const text = `WorkForce Update: Ich habe heute ${completed}/${total} Aufgaben erledigt.`;
            copyToClipboard(text);
        }

        function copyToClipboard(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";  
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                alert("Bericht in die Zwischenablage kopiert:\n" + text);
            } catch (err) {
                prompt("Kopieren nicht möglich. Bitte manuell kopieren:", text);
            }
            document.body.removeChild(textArea);
        }

        function openModal(type, taskId = null) {
            const overlay = document.getElementById('modal-overlay');
            const content = document.getElementById('modal-content');
            overlay.classList.remove('hidden');
            setTimeout(() => { overlay.classList.remove('opacity-0'); content.classList.remove('scale-90'); }, 10);

            if(type === 'custom_html') {
                // Content set by caller
            } else if (type === 'cancel_job') {
                content.innerHTML = `
                    <div class="text-left">
                        <div class="flex items-center gap-3 mb-4 text-red-500">
                            <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                            <h3 class="text-xl font-bold text-slate-800">Auftrag stornieren</h3>
                        </div>
                        <p class="text-sm text-slate-500 mb-4">Bitte Grund und Unterschrift angeben.</p>
                        
                        <div class="mb-4">
                            <label class="text-xs font-bold text-slate-400 uppercase">Grund</label>
                            <select id="cancel-reason" class="w-full bg-slate-50 border border-red-100 rounded-xl p-3 text-sm font-bold text-slate-700 outline-none mt-1">
                                <option value="">Grund wählen...</option>
                                <option>Kunde hat storniert</option>
                                <option>Standort unzugänglich</option>
                                <option>Falsche Adresse/Details</option>
                                <option>Fehlende Ausrüstung</option>
                                <option>Wetterbedingungen</option>
                                <option>Sonstiges</option>
                            </select>
                        </div>
                        
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-xs font-bold text-slate-400 uppercase">Unterschrift zur Autorisierung</label>
                                <button onclick="clearSignature('cancel-signature-pad')" class="text-xs font-bold text-red-400">Löschen</button>
                            </div>
                            <canvas id="cancel-signature-pad" class="signature-canvas"></canvas>
                        </div>

                        <button onclick="submitCancellation()" class="w-full bg-red-500 text-white font-bold py-4 rounded-2xl shadow-lg active:scale-95 transition">Stornierung bestätigen</button>
                        <button onclick="closeModal()" class="w-full mt-2 text-slate-400 font-bold py-3">Zurück</button>
                    </div>`;
            } else if(type === 'pause') {
                content.innerHTML = `
                    <div class="text-center">
                        <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center text-orange-500 text-3xl mx-auto mb-6"><i class="fa-solid fa-pause"></i></div>
                        <h3 class="text-2xl font-bold text-slate-800 mb-2">Fahrt pausieren</h3>
                        <p class="text-slate-500 mb-6 font-medium">Dauer wählen.</p>
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <button class="bg-white border-2 border-slate-100 hover:border-brand-sky hover:text-brand-sky py-3 rounded-2xl text-sm font-bold transition">15m</button>
                            <button class="bg-white border-2 border-slate-100 hover:border-brand-sky hover:text-brand-sky py-3 rounded-2xl text-sm font-bold transition">30m</button>
                            <button class="bg-white border-2 border-slate-100 hover:border-brand-sky hover:text-brand-sky py-3 rounded-2xl text-sm font-bold transition">1h</button>
                        </div>
                        <button onclick="logEvent(${activeTaskId}, 'pause', {reason: 'Break'}); closeModal();" class="w-full bg-brand-dark text-white font-bold py-4 rounded-2xl shadow-lg">Pause bestätigen</button>
                    </div>`;
            } else if (type === 'stop') {
                content.innerHTML = `
                    <div class="text-center">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center text-red-500 text-3xl mx-auto mb-6"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <h3 class="text-2xl font-bold text-slate-800 mb-2">Fahrt beenden</h3>
                        <p class="text-slate-500 mb-6 font-medium">Grund wählen.</p>
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <button onclick="logEvent(${activeTaskId}, 'stop', {reason: 'Traffic'}); closeModal();" class="p-4 border-2 border-slate-100 rounded-2xl hover:bg-red-50 hover:border-red-200 transition flex flex-col items-center gap-2"><i class="fa-solid fa-traffic-light text-red-400 text-xl"></i><span class="text-xs font-bold">Verkehr</span></button>
                            <button onclick="logEvent(${activeTaskId}, 'stop', {reason: 'Other'}); closeModal();" class="p-4 border-2 border-slate-100 rounded-2xl hover:bg-red-50 hover:border-red-200 transition flex flex-col items-center gap-2"><i class="fa-solid fa-question text-red-400 text-xl"></i><span class="text-xs font-bold">Sonstiges</span></button>
                        </div>
                    </div>`;
            } else if (type === 'expense') {
                const modalHTML = renderExpenseModalContent(activeTaskId);
                content.innerHTML = modalHTML;
            } else if (type === 'delegate') {
                activeTaskId = taskId; 
                content.innerHTML = `
                    <div class="text-left">
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Aufgabe delegieren</h3>
                        <p class="text-sm text-slate-400 mb-4">Weisen Sie diese Aufgabe einem Kollegen zu.</p>
                        <div class="space-y-2 max-h-60 overflow-y-auto mb-4">
                            ${employees.filter(e => e.id !== selectedEmployee.id).map(e => `
                                <div onclick="assignDelegate(${e.id})" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 cursor-pointer">
                                    <img src="${e.img}" class="w-10 h-10 rounded-full">
                                    <div>
                                        <div class="font-bold text-slate-700">${e.name}</div>
                                        <div class="text-xs text-slate-400 font-bold">${e.role}</div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                        <button onclick="closeModal()" class="w-full bg-slate-100 text-slate-500 font-bold py-3 rounded-xl">Abbrechen</button>
                    </div>`;
            } else if (type === 'team') {
                content.innerHTML = `
                    <div class="text-left">
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Mein Team</h3>
                        <p class="text-sm text-slate-400 mb-4">Aktueller Status der Kollegen.</p>
                        <div class="space-y-3 max-h-80 overflow-y-auto">
                            ${employees.map(e => {
                                let statusColor = 'bg-green-100 text-green-600';
                                if(e.status === 'Beschäftigt') statusColor = 'bg-orange-100 text-orange-600';
                                if(e.status === 'Im Urlaub') statusColor = 'bg-red-100 text-red-600';
                                return `
                                <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100">
                                    <img src="${e.img}" class="w-12 h-12 rounded-full border border-slate-100">
                                    <div class="flex-1">
                                        <div class="font-bold text-slate-700">${e.name}</div>
                                        <div class="text-xs text-slate-400 font-bold">${e.role}</div>
                                    </div>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold ${statusColor}">${e.status}</span>
                                </div>`;
                            }).join('')}
                        </div>
                        <button onclick="closeModal()" class="w-full mt-4 bg-brand-dark text-white font-bold py-3 rounded-xl">Schließen</button>
                    </div>`;
            } else if (type === 'chat') {
                 renderChat(content);
            } else if (type === 'alert') {
                content.innerHTML = `
                     <div class="text-center">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center text-red-500 text-3xl mx-auto mb-6"><i class="fa-solid fa-xmark"></i></div>
                        <h3 class="text-2xl font-bold text-slate-800 mb-2">Unvollständig</h3>
                        <p class="text-slate-500 mb-6 font-medium">Bitte alle Checklistenpunkte erledigen.</p>
                        <button onclick="closeModal()" class="w-full bg-slate-200 text-slate-600 font-bold py-4 rounded-2xl">Okay</button>
                    </div>`;
            } else if (type === 'help') {
                content.innerHTML = `
                    <div class="text-center">
                        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center text-blue-500 text-3xl mx-auto mb-6"><i class="fa-solid fa-life-ring"></i></div>
                        <h3 class="text-2xl font-bold text-slate-800 mb-2">Hilfe anfordern</h3>
                        <p class="text-slate-500 mb-6 font-medium">Benötigen Sie Unterstützung?</p>
                        <div class="space-y-3">
                             <button onclick="openModal('chat')" class="w-full bg-blue-500 text-white font-bold py-4 rounded-2xl shadow-lg flex items-center justify-center gap-2"><i class="fa-solid fa-comments"></i> Live-Chat</button>
                             <button onclick="closeModal(); alert('Dispatch notified!');" class="w-full bg-white border-2 border-slate-100 text-slate-600 font-bold py-4 rounded-2xl hover:bg-slate-50">Vorgesetzten anrufen</button>
                        </div>
                    </div>`;
            } else if (type === 'notifications') {
                 content.innerHTML = `
                    <div class="text-left">
                        <h3 class="text-xl font-bold text-slate-800 mb-4">Benachrichtigungen</h3>
                        <div class="space-y-4">
                             <div class="flex gap-3 items-start border-b border-slate-50 pb-3">
                                 <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-circle-info"></i></div>
                                 <div>
                                     <p class="text-sm font-bold text-slate-700">Planaktualisierung</p>
                                     <p class="text-xs text-slate-400">Task #103 location changed.</p>
                                 </div>
                             </div>
                             <div class="flex gap-3 items-start">
                                 <div class="w-8 h-8 rounded-full bg-green-50 text-green-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-check"></i></div>
                                 <div>
                                     <p class="text-sm font-bold text-slate-700">Genehmigt</p>
                                     <p class="text-xs text-slate-400">Ihr Urlaubsantrag wurde genehmigt.</p>
                                 </div>
                             </div>
                        </div>
                        <button onclick="closeModal()" class="w-full mt-6 bg-slate-100 text-slate-500 font-bold py-3 rounded-xl">Schließen</button>
                    </div>`;
            }
        }
        function closeModal() {
            const overlay = document.getElementById('modal-overlay');
            const content = document.getElementById('modal-content');
            overlay.classList.add('opacity-0'); content.classList.add('scale-90');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }

        /* --- TASK DETAILS LOGIC --- */
        function viewTaskDetails(taskId) {
            const task = tasks.find(t => t.id === taskId);
            const teamMembers = task.team.map(uid => employees.find(e => e.id === uid).name).join(', ');
            
            // Auto-fill materials if missing (legacy data support)
            if (!task.materials) {
                const defaultTask = defaultTasks.find(dt => dt.id === taskId);
                task.materials = defaultTask ? defaultTask.materials : ["Keine Materialien gelistet"];
                saveData(); // Save back to prevent future issues
            }

            const materials = task.materials.join('</li><li>');
            const checklistItems = task.checklist.map(i => i.txt).join('</li><li>');

            const content = document.getElementById('modal-content');
            content.innerHTML = `
                <div class="text-left">
                    <h3 class="text-2xl font-extrabold text-brand-dark mb-4">Aufgabendetails</h3>
                    
                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-slate-400 uppercase">Aufgabe</h4>
                        <p class="font-bold text-slate-800 text-lg">${task.title}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                         <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase">Zeit</h4>
                            <p class="font-bold text-slate-700">${task.time}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase">Adresse</h4>
                            <p class="font-bold text-slate-700">${task.location}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-slate-400 uppercase mb-1">Team</h4>
                        <p class="text-sm font-medium text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">${teamMembers}</p>
                    </div>

                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-slate-400 uppercase mb-1">Benötigte Materialien</h4>
                        <ul class="list-disc list-inside text-sm font-medium text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <li>${materials}</li>
                        </ul>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-xs font-bold text-slate-400 uppercase mb-1">Aufgabenliste</h4>
                        <ul class="list-decimal list-inside text-sm font-medium text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <li>${checklistItems}</li>
                        </ul>
                    </div>

                    <button onclick="closeModal()" class="w-full bg-brand-dark text-white font-bold py-4 rounded-2xl shadow-lg active:scale-95 transition">Schließen</button>
                </div>
            `;
            openModal('custom_html');
        }

        /* --- REPORT GENERATION LOGIC --- */
        function openTaskReport(taskId) {
            const task = tasks.find(t => t.id === taskId);
            const modal = document.getElementById('report-modal');
            const area = document.getElementById('report-preview-area');
            
            const expenseTotal = task.expenses ? task.expenses.reduce((a,b) => a + parseFloat(b.amount), 0) : 0;
            const teamHTML = task.team.map(uid => {
                const e = employees.find(emp => emp.id === uid);
                const att = task.attendance?.[uid]?.status || 'Unknown';
                // Translate attendance statuses
                let deAtt = att.toUpperCase();
                if(att === 'present') deAtt = 'ANWESEND';
                if(att === 'sick') deAtt = 'KRANK';
                if(att === 'leave') deAtt = 'URLAUB';
                
                return `<li>${e.name} - <span class="font-bold">${deAtt}</span></li>`;
            }).join('');

            // Material List for Report
            const materialsListHTML = (task.materials && task.materials.length > 0)
                ? task.materials.map(m => `<li>${m}</li>`).join('')
                : '<li>Keine Materialien erfasst</li>';

            const checklistHTML = task.checklist.map(item => `
                <div class="mb-4 break-inside-avoid">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid ${item.done ? 'fa-square-check text-green-600' : 'fa-square text-gray-300'}"></i>
                        <span class="font-bold text-slate-700">${item.txt}</span>
                        <span class="text-xs text-slate-400 ml-auto">${item.duration || 0} min</span>
                    </div>
                    ${item.remark ? `<p class="text-xs text-slate-500 italic ml-6 mb-1">Note: ${item.remark}</p>` : ''}
                    ${item.photoData ? `<img src="${item.photoData}" class="w-32 h-auto rounded border border-slate-200 ml-6 mt-1 shadow-sm">` : ''}
                </div>
            `).join('');

            const expenseHTML = (task.expenses && task.expenses.length > 0) ? `
                <table class="w-full text-sm text-left mt-2 border-collapse break-inside-avoid">
                    <thead><tr class="bg-slate-100 text-slate-600"><th class="p-2 border-b">Kategorie</th><th class="p-2 border-b">Beschr.</th><th class="p-2 border-b">Wer</th><th class="p-2 border-b text-right">Betrag</th></tr></thead>
                    <tbody>
                        ${task.expenses.map(ex => {
                            const emp = employees.find(e => e.id == ex.employeeId) || {name:'Unbekannt'};
                            return `<tr><td class="p-2 border-b">${ex.cat}</td><td class="p-2 border-b">${ex.reason}</td><td class="p-2 border-b">${emp.name.split(',')[0]}</td><td class="p-2 border-b text-right font-bold">$${ex.amount}</td></tr>`;
                        }).join('')}
                    </tbody>
                    <tfoot><tr><td colspan="3" class="p-2 font-bold text-right">Gesamt</td><td class="p-2 font-bold text-right text-green-600">$${expenseTotal.toFixed(2)}</td></tr></tfoot>
                </table>
            ` : '<p class="text-sm text-slate-400 italic">Keine Ausgaben erfasst.</p>';

            const journeyHTML = (task.events && task.events.length > 0) ? `
                <div class="mb-8 p-4 bg-blue-50 rounded-xl border border-blue-100 break-inside-avoid">
                    <h3 class="font-bold text-blue-800 mb-3 border-b border-blue-200 pb-2">Fahrtenbuch</h3>
                    <table class="w-full text-xs text-left">
                        <thead><tr class="text-blue-400 uppercase"><th class="pb-2">Zeit</th><th class="pb-2">Ereignis</th><th class="pb-2">Details</th></tr></thead>
                        <tbody>
                            ${task.events.map(e => `
                                <tr>
                                    <td class="py-1 font-bold text-slate-600">${e.time}</td>
                                    <td class="py-1"><span class="bg-white border border-blue-200 px-2 py-0.5 rounded text-blue-600 font-bold text-[10px]">${e.type.toUpperCase()}</span></td>
                                    <td class="py-1 text-slate-500">${e.meta ? JSON.stringify(e.meta).replace(/{|}|"/g,'') : '-'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            ` : '<p class="text-sm text-slate-400 italic">Keine Fahrtdaten aufgezeichnet.</p>';

            // Handle Cancelled State
            let headerBadge = '';
            if (task.status === 'cancelled') {
                headerBadge = `<div class="bg-red-500 text-white px-4 py-2 rounded-full inline-block font-bold text-xl mb-4 border-4 border-red-200">AUFTRAG STORNIERT</div>`;
            }

            const content = `
                <div class="text-center border-b-2 border-brand-dark pb-6 mb-6">
                    ${headerBadge}
                    <h1 class="text-3xl font-extrabold text-brand-dark uppercase tracking-wide">Auftragsbericht</h1>
                    <p class="text-slate-400 font-bold">#${task.id} - ${task.title}</p>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase mb-1">Ort</h4>
                        <p class="font-bold text-slate-800 text-lg">${task.location}</p>
                    </div>
                    <div class="text-right">
                        <h4 class="text-xs font-bold text-slate-400 uppercase mb-1">Datum</h4>
                        <p class="font-bold text-slate-800 text-lg">${new Date().toLocaleDateString()}</p>
                    </div>
                </div>

                <div class="mb-8 p-4 bg-slate-50 rounded-xl border border-slate-100 break-inside-avoid">
                    <h3 class="font-bold text-slate-700 mb-3 border-b border-slate-200 pb-2">Teamanwesenheit</h3>
                    <ul class="list-disc list-inside text-sm text-slate-600 space-y-1">
                        ${teamHTML}
                    </ul>
                </div>

                <div class="mb-8 p-4 bg-slate-50 rounded-xl border border-slate-100 break-inside-avoid">
                    <h3 class="font-bold text-slate-700 mb-3 border-b border-slate-200 pb-2">Verwendete Materialien</h3>
                    <ul class="list-disc list-inside text-sm text-slate-600 space-y-1">
                        ${materialsListHTML}
                    </ul>
                </div>
                
                ${journeyHTML}

                <div class="mb-8">
                    <h3 class="font-bold text-brand-dark text-xl mb-4 flex items-center gap-2"><i class="fa-solid fa-list-check"></i> Checklisten-Ausführung</h3>
                    ${checklistHTML}
                </div>

                <div class="mb-8 break-inside-avoid">
                    <h3 class="font-bold text-brand-dark text-xl mb-4 flex items-center gap-2"><i class="fa-solid fa-receipt"></i> Ausgaben</h3>
                    ${expenseHTML}
                </div>

                <div class="mb-8 p-4 ${task.status === 'cancelled' ? 'bg-red-50 border-red-100' : 'bg-yellow-50 border-yellow-100'} rounded-xl border break-inside-avoid">
                    <h3 class="font-bold ${task.status === 'cancelled' ? 'text-red-700' : 'text-yellow-700'} mb-2">
                        ${task.status === 'cancelled' ? 'Stornierungsgrund' : 'Abschließende Notizen'}
                    </h3>
                    <p class="text-slate-700 text-sm italic">"${task.report || task.cancellationReason || 'Keine Notizen vorhanden.'}"</p>
                </div>

                <div class="mt-12 text-center break-inside-avoid">
                    ${task.signature ? `<img src="${task.signature}" class="mx-auto h-24 mb-2 border-b border-slate-300 pb-2">` : '<div class="h-24 border-b border-slate-300 mb-2"></div>'}
                    <p class="text-xs font-bold text-slate-400 uppercase">
                        ${task.status === 'cancelled' ? 'Stornierung autorisiert von' : 'Unterschrieben & Geprüft'}
                    </p>
                </div>
            `;

            area.innerHTML = content;
            modal.classList.remove('hidden');
        }

        function closeReportModal() {
            document.getElementById('report-modal').classList.add('hidden');
        }

        function downloadPDF() {
            const element = document.getElementById('report-preview-area');
            const opt = {
              margin:       0.5,
              filename:     `WorkForce_Report_${activeTaskId || 'Job'}.pdf`,
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }

        function viewReportFromSummary() {
            const completed = tasks.filter(t => t.status === 'completed' || t.status === 'cancelled');
            if (completed.length > 0) {
                const lastTask = completed[completed.length - 1]; 
                openTaskReport(lastTask.id);
            }
        }

        /* --- EXPENSE LOGIC --- */
        let selectedExpCategory = null;
        
        function renderExpenseModalContent(taskId) {
            const task = tasks.find(t => t.id === taskId);
            const taskExpenses = task.expenses || [];
            const teamMembers = task.team.map(uid => employees.find(e => e.id === uid));
            
            const listHtml = taskExpenses.length > 0 ? 
                `<div class="mb-4 bg-slate-50 rounded-xl p-3 max-h-32 overflow-y-auto border border-slate-100">
                    <h4 class="text-xs font-bold text-slate-400 uppercase mb-2">Erfasste Posten</h4>
                    ${taskExpenses.map((ex, idx) => {
                        const emp = employees.find(e => e.id == ex.employeeId) || {name: 'Unbekannt'};
                        return `<div class="flex justify-between items-center text-sm border-b border-slate-100 last:border-0 pb-2 mb-2 last:mb-0 last:pb-0">
                            <div>
                                <div class="font-bold text-slate-700">${ex.cat} - $${ex.amount}</div>
                                <div class="text-[10px] text-slate-500">${emp.name.split(',')[0]} • ${ex.reason}</div>
                            </div>
                            <button onclick="removeExpense(${idx})" class="text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                        </div>`;
                    }).join('')}
                 </div>` : '<div class="text-center text-slate-400 text-sm mb-4 italic">Keine Ausgaben erfasst.</div>';

            return `
                <div class="text-left">
                    <h3 class="text-xl font-bold text-slate-800 mb-2 flex items-center gap-2"><i class="fa-solid fa-receipt text-green-500"></i> Projektausgaben</h3>
                    ${listHtml}
                    <div class="space-y-3 border-t border-slate-100 pt-4">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase">Mitarbeiter</label>
                            <select id="exp-employee" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-sm font-bold text-slate-700 outline-none">
                                ${teamMembers.map(e => `<option value="${e.id}">${e.name}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase">Kategorie</label>
                            <div class="grid grid-cols-3 gap-2 mt-1">
                                <button onclick="selectExpenseCat(this, 'Treibstoff')" class="exp-cat bg-slate-50 border border-slate-200 py-2 rounded-xl text-xs font-bold hover:bg-brand-light hover:border-brand-action transition">Treibstoff</button>
                                <button onclick="selectExpenseCat(this, 'Verpflegung')" class="exp-cat bg-slate-50 border border-slate-200 py-2 rounded-xl text-xs font-bold hover:bg-brand-light hover:border-brand-action transition">Verpflegung</button>
                                <button onclick="selectExpenseCat(this, 'Material')" class="exp-cat bg-slate-50 border border-slate-200 py-2 rounded-xl text-xs font-bold hover:bg-brand-light hover:border-brand-action transition">Material</button>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase">Grund / Beschreibung</label>
                            <input type="text" id="exp-reason" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2 text-sm font-bold outline-none focus:border-green-400" placeholder="z.B. 20 Liter, Mittagessen Team...">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase">Betrag ($)</label>
                            <input type="number" id="exp-amount" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-lg font-bold outline-none focus:border-green-400" placeholder="0.00">
                        </div>
                        <button onclick="addExpense()" class="w-full bg-brand-dark text-white font-bold py-3 rounded-2xl shadow-lg mt-2">Eintrag hinzufügen</button>
                        <button onclick="closeModal()" class="w-full text-slate-400 text-xs font-bold py-2 hover:text-slate-600">Fertig</button>
                    </div>
                </div>`;
        }
        
        function selectExpenseCat(btn, cat) {
            document.querySelectorAll('.exp-cat').forEach(b => b.classList.remove('bg-brand-light', 'border-brand-action'));
            btn.classList.add('bg-brand-light', 'border-brand-action');
            selectedExpCategory = cat;
        }

        function addExpense() {
            const amount = document.getElementById('exp-amount').value;
            const reason = document.getElementById('exp-reason').value;
            const empId = document.getElementById('exp-employee').value;
            if(!amount || !selectedExpCategory || !empId) { alert("Bitte Mitarbeiter, Kategorie und Betrag wählen."); return; }
            
            const task = tasks.find(t => t.id === activeTaskId);
            if(!task.expenses) task.expenses = [];
            task.expenses.push({
                cat: selectedExpCategory, 
                amount: amount,
                reason: reason || "Keine Beschreibung",
                employeeId: empId,
                timestamp: new Date().toISOString()
            });
            saveData();
            document.getElementById('modal-content').innerHTML = renderExpenseModalContent(activeTaskId);
            selectedExpCategory = null; 
        }

        function removeExpense(idx) {
             const task = tasks.find(t => t.id === activeTaskId);
             if(task.expenses) {
                 task.expenses.splice(idx, 1);
                 saveData();
                 document.getElementById('modal-content').innerHTML = renderExpenseModalContent(activeTaskId);
             }
        }

        /* --- CHAT LOGIC --- */
        function renderChat(container) {
             container.innerHTML = `
                <div class="flex flex-col h-[400px]">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-2">
                        <div class="flex items-center gap-2">
                             <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs"><i class="fa-solid fa-robot"></i></div>
                             <h3 class="font-bold text-slate-800">Dispatch Support</h3>
                        </div>
                        <button onclick="closeModal()" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-times"></i></button>
                    </div>
                    <div id="chat-messages" class="flex-1 overflow-y-auto space-y-3 p-2 bg-slate-50 rounded-xl mb-3">
                        ${chatMessages.map(m => `
                            <div class="flex gap-2 ${m.sender === 'me' ? 'justify-end' : ''}">
                                ${m.sender === 'bot' ? `<div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 text-[10px] flex-shrink-0"><i class="fa-solid fa-headset"></i></div>` : ''}
                                <div class="${m.sender === 'me' ? 'chat-bubble-out' : 'chat-bubble-in'} p-3 text-sm shadow-sm max-w-[80%]">
                                    ${m.text}
                                    <div class="text-[9px] opacity-60 text-right mt-1">${m.time}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="flex gap-2 pt-2">
                        <input type="text" id="chat-input" class="flex-1 border-2 border-slate-100 rounded-xl px-3 py-2 text-sm focus:border-blue-500 outline-none" placeholder="Nachricht eingeben...">
                        <button onclick="sendChatMessage()" class="bg-blue-600 text-white w-10 h-10 rounded-xl flex-shrink-0 hover:bg-blue-700 transition"><i class="fa-solid fa-paper-plane"></i></button>
                    </div>
                </div>
            `;
            setTimeout(() => {
                const box = document.getElementById('chat-messages');
                box.scrollTop = box.scrollHeight;
            }, 50);
        }

        function sendChatMessage() {
            const input = document.getElementById('chat-input');
            const text = input.value.trim();
            if(!text) return;

            chatMessages.push({ sender: 'me', text: text, time: new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) });
            renderChat(document.getElementById('modal-content'));
            
            setTimeout(() => {
                chatMessages.push({ sender: 'bot', text: 'Danke. Ein Mitarbeiter meldet sich gleich.', time: new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) });
                 renderChat(document.getElementById('modal-content'));
            }, 1000);
        }

        /* --- DELEGATE & REP LOGIC --- */
        function assignDelegate(userId) {
            const task = tasks.find(t => t.id === activeTaskId);
            task.delegatedTo = userId;
            saveData();
            closeModal();
            renderTasks();
            alert("Aufgabe erfolgreich delegiert!");
        }

        function reclaimTask(taskId) {
            if(confirm("Aufgabe zurückfordern?")) {
                const task = tasks.find(t => t.id === taskId);
                task.delegatedTo = null;
                saveData();
                renderTasks();
            }
        }
        
        function updateAttendanceStatus(taskId, uid, status) {
            const task = tasks.find(t => t.id === taskId);
            if (!task.attendance) task.attendance = {};
            if (!task.attendance[uid]) task.attendance[uid] = {};
            
            task.attendance[uid].status = status;
            saveData();
            
            const card = document.getElementById(`att-card-${uid}`);
            const buttons = card.querySelectorAll('button.att-btn');
            buttons.forEach(btn => btn.className = 'att-btn flex-1 py-3 rounded-xl text-xs font-bold text-slate-400 transition');
            
            const activeBtn = Array.from(buttons).find(b => b.dataset.status === status);
            if(status === 'present') activeBtn.className = 'att-btn flex-1 py-3 rounded-xl text-xs font-bold bg-brand-action text-white shadow-sm transition';
            if(status === 'sick') activeBtn.className = 'att-btn flex-1 py-3 rounded-xl text-xs font-bold bg-orange-400 text-white shadow-sm transition';
            if(status === 'leave') activeBtn.className = 'att-btn flex-1 py-3 rounded-xl text-xs font-bold bg-red-400 text-white shadow-sm transition';

            const repDiv = document.getElementById(`rep-select-${uid}`);
            if (status === 'sick' || status === 'leave') {
                repDiv.classList.remove('hidden');
            } else {
                repDiv.classList.add('hidden');
                task.attendance[uid].rep = null;
                const select = repDiv.querySelector('select');
                select.value = "";
                saveData();
            }
        }
        
        function assignRep(taskId, uid, repId) {
            const task = tasks.find(t => t.id === taskId);
            if (!task.attendance) task.attendance = {};
            if (!task.attendance[uid]) task.attendance[uid] = {};
            
            task.attendance[uid].rep = repId;
            saveData();
        }

        /* --- PROFILE --- */
        function toggleProfile() {
            const p = document.getElementById('profile-panel');
            p.classList.toggle('translate-x-full');
            p.classList.toggle('open');
            if(selectedEmployee) {
                document.getElementById('profile-img-large').src = selectedEmployee.img;
                document.getElementById('profile-name-large').innerText = selectedEmployee.name;
                document.getElementById('profile-role-large').innerText = selectedEmployee.role;
            }
        }
        function logout() {
            localStorage.removeItem('wf_shift_start');
            location.reload();
        }

        /* --- AUTH & TIMER --- */
        function handleSearch(val) {
            const list = document.getElementById('employee-list');
            if(val.length < 2) { list.classList.add('hidden'); return; }
            const matches = employees.filter(e => e.name.toLowerCase().includes(val.toLowerCase()));
            list.innerHTML = matches.map(e => `<div onclick="selectUser(${e.id})" class="p-3 hover:bg-slate-50 cursor-pointer flex items-center gap-3 border-b border-slate-50 last:border-0"><img src="${e.img}" class="w-10 h-10 rounded-full object-cover"><div><div class="font-bold text-slate-700">${e.name}</div><div class="text-xs text-slate-400 font-bold">${e.role}</div></div></div>`).join('');
            list.classList.remove('hidden');
        }
        
        setTimeout(() => {
            const quickCont = document.getElementById('quick-select-container');
            quickCont.innerHTML = employees.map(e => `
                <div onclick="selectUser(${e.id})" class="cursor-pointer flex flex-col items-center">
                    <img src="${e.img}" class="w-10 h-10 rounded-full object-cover border border-slate-100">
                    <span class="text-[9px] font-bold mt-1 text-slate-500">${e.name.split(',')[0]}</span>
                </div>
            `).join('');
        }, 100);

        function selectUser(id) {
            selectedEmployee = employees.find(e => e.id === id);
            document.getElementById('step-search').classList.add('hidden');
            document.getElementById('step-pin').classList.remove('hidden');
            document.getElementById('step-pin').classList.add('flex');
            document.getElementById('pin-avatar').src = selectedEmployee.img;
            document.getElementById('pin-name').innerText = selectedEmployee.name;
            document.getElementById('pin-role').innerText = selectedEmployee.role;
        }
        function resetLogin() {
            selectedEmployee = null; currentPin = ""; updatePinDots();
            document.getElementById('step-search').classList.remove('hidden');
            document.getElementById('step-pin').classList.add('hidden');
            document.getElementById('step-pin').classList.remove('flex');
            document.getElementById('employee-input').value = "";
        }
        function enterPin(num) {
            if(num === 'del') currentPin = currentPin.slice(0, -1); else if (currentPin.length < 4) currentPin += num;
            updatePinDots();
        }
        function updatePinDots() {
            document.querySelectorAll('.pin-dot').forEach((dot, idx) => {
                if(idx < currentPin.length) { dot.classList.add('bg-brand-dark'); dot.classList.remove('bg-slate-200'); }
                else { dot.classList.remove('bg-brand-dark'); dot.classList.add('bg-slate-200'); }
            });
        }
        function submitPin() {
            if(currentPin.length === 4) {
                document.getElementById('dash-avatar').src = selectedEmployee.img;
                document.getElementById('dash-greeting').innerText = `Hello, ${selectedEmployee.name.split(',')[1]}!`;
                if(!shiftStartTime) {
                    shiftStartTime = Date.now();
                    localStorage.setItem('wf_shift_start', shiftStartTime);
                }
                
                performCheckIn(selectedEmployee.id);
                startShiftTimerDisplay();
                renderTasks();
                navTo('view-dashboard');
            }
        }
        
        function startShiftTimerDisplay() {
            if(shiftInterval) clearInterval(shiftInterval);
            const display = document.getElementById('global-shift-timer');
            shiftInterval = setInterval(() => {
                const diff = Date.now() - shiftStartTime;
                const hrs = Math.floor(diff / (1000 * 60 * 60));
                const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const secs = Math.floor((diff % (1000 * 60)) / 1000);
                display.innerText = `${pad(hrs)}:${pad(mins)}:${pad(secs)}`;
            }, 1000);
        }
        function pad(n) { return n < 10 ? '0'+n : n; }

        /* --- DASHBOARD --- */
        function renderTasks() {
            const container = document.getElementById('task-container');
            const totalTasks = tasks.length;
            const completedCount = tasks.filter(t => t.status === 'completed' || t.status === 'cancelled').length;
            const percent = totalTasks === 0 ? 0 : (completedCount / totalTasks) * 100;
            document.getElementById('dash-progress-text').innerText = `${completedCount}/${totalTasks}`;
            document.getElementById('dash-progress-bar').style.width = `${percent}%`;

            let visibleTasks = [];
            if(activeDashTab === 'todo') {
                visibleTasks = tasks.filter(t => t.status !== 'completed' && t.status !== 'cancelled');
            } else {
                visibleTasks = tasks.filter(t => t.status === 'completed' || t.status === 'cancelled');
            }
            
            if(visibleTasks.length === 0) {
                 container.innerHTML = `<div class="text-center py-10 opacity-50"><i class="fa-solid fa-clipboard-list text-4xl mb-2"></i><p>Keine ${activeDashTab === 'todo' ? 'ausstehenden' : 'erledigten'} Aufgaben.</p></div>`;
                 if (activeDashTab === 'history' && visibleTasks.length === 0) return;
                 return;
            }

            const listHtml = visibleTasks.map(t => {
                let delegateUI = '';
                let mainAction = '';
                if (t.delegatedTo) {
                    const assignee = employees.find(e => e.id === t.delegatedTo);
                    delegateUI = `
                        <div class="mt-3 flex items-center gap-2 bg-slate-50 p-2 rounded-xl border border-dashed border-slate-300">
                             <img src="${assignee.img}" class="w-6 h-6 rounded-full grayscale">
                             <span class="text-xs text-slate-500 font-bold flex-1">Delegiert an ${assignee.name.split(',')[0]}</span>
                             <button onclick="reclaimTask(${t.id})" class="text-[10px] font-bold text-red-400 hover:text-red-500 underline">Zurückfordern</button>
                        </div>
                    `;
                    mainAction = `<button disabled class="bg-slate-200 text-slate-400 px-6 py-3 rounded-xl text-sm font-bold flex items-center gap-2 cursor-not-allowed">Ausstehend</button>`;
                } else {
                    mainAction = `
                        <div class="flex gap-2 w-full mt-2">
                             <button onclick="viewTaskDetails(${t.id})" class="bg-blue-50 text-blue-600 font-bold py-3 px-4 rounded-xl text-xs flex items-center justify-center gap-2 hover:bg-blue-100 transition"><i class="fa-solid fa-circle-info"></i> Info</button>
                             <button onclick="prepareTask(${t.id})" class="bg-brand-dark text-white font-bold py-3 px-4 rounded-xl text-xs flex-1 shadow-lg shadow-blue-900/20 hover:bg-brand-sky transition flex items-center justify-center gap-2">Vorbereiten <i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    `;
                }

                // Handle Cancelled Appearance
                let statusClasses = '';
                let statusIcon = '';
                let titleStrikethrough = '';
                
                if (t.status === 'completed') {
                    statusClasses = 'text-green-500 flex items-center gap-1';
                    statusIcon = '<i class="fa-solid fa-check-circle"></i> Erledigt';
                    titleStrikethrough = 'line-through opacity-50';
                } else if (t.status === 'cancelled') {
                    statusClasses = 'text-red-500 flex items-center gap-1';
                    statusIcon = '<i class="fa-solid fa-ban"></i> Storniert';
                    titleStrikethrough = 'line-through opacity-50 text-red-300';
                }

                return `
                <div class="bg-white rounded-[2rem] shadow-card border border-slate-50 relative group transition transform active:scale-98 flex overflow-hidden" data-id="${t.id}">
                    <div class="drag-handle w-12 bg-slate-50 flex items-center justify-center cursor-grab active:cursor-grabbing border-r border-slate-100">
                        <i class="fa-solid fa-grip-vertical text-slate-300 text-lg"></i>
                    </div>
                    <div class="flex-1 p-5 relative">
                        ${(t.status !== 'completed' && t.status !== 'cancelled' && !t.delegatedTo) ? `
                        <button onclick="openTaskMenu(${t.id})" class="absolute top-4 right-4 text-slate-400 hover:text-brand-dark p-2 -mr-2 -mt-2"><i class="fa-solid fa-ellipsis-vertical text-xl"></i></button>
                        ` : ''}
                        
                        <div class="flex justify-between items-start mb-2 pr-6">
                            <div>
                                <span class="text-[10px] font-black ${t.status === 'completed' ? 'text-green-500 bg-green-50' : (t.status === 'cancelled' ? 'text-red-500 bg-red-50' : 'text-brand-sky bg-sky-50')} px-3 py-1.5 rounded-full mb-3 inline-block uppercase tracking-wider">${t.time}</span>
                                <h3 class="font-extrabold text-xl text-slate-800 leading-tight ${titleStrikethrough}">${t.title}</h3>
                                <p class="text-sm text-slate-400 flex items-center gap-1 mt-1 font-semibold"><i class="fa-solid fa-map-pin text-brand-sky"></i> ${t.location}</p>
                            </div>
                        </div>
                        <div class="flex -space-x-3 mb-2">${t.team.map(uid => `<img src="${employees.find(e=>e.id===uid).img}" class="w-8 h-8 rounded-full border-2 border-white shadow-sm">`).join('')}</div>
                        
                        ${delegateUI}
                        ${(t.status !== 'completed' && t.status !== 'cancelled') ? `
                        <div class="pt-2 border-t border-slate-50">
                            ${mainAction}
                        </div>` : `
                        <div class="pt-4 mt-2 border-t border-slate-50 flex items-center justify-end flex-wrap gap-2">
                             <span class="text-xs font-bold ${statusClasses}">${statusIcon}</span>
                             <button onclick="openTaskReport(${t.id})" class="text-[10px] font-bold bg-brand-dark/10 text-brand-dark px-3 py-1 rounded-full hover:bg-brand-dark hover:text-white transition flex items-center gap-1"><i class="fa-solid fa-file-pdf"></i> Vollständiger Bericht</button>
                             <button onclick="viewJourney(${t.id})" class="text-[10px] font-bold bg-brand-sky/10 text-brand-sky px-3 py-1 rounded-full hover:bg-brand-sky hover:text-white transition">Fahrt ansehen</button>
                        </div>`}
                    </div>
                </div>
            `}).join('');
            
            if(activeDashTab === 'history' && visibleTasks.length > 0) {
                const btnHtml = `<button onclick="viewDailyRoute()" class="w-full mb-6 bg-white border-2 border-brand-dark text-brand-dark font-bold py-4 rounded-[1.5rem] shadow-sm flex items-center justify-center gap-2 hover:bg-brand-dark hover:text-white transition"><i class="fa-solid fa-map-location-dot text-xl"></i> Vollständiges Tagesfahrtenbuch</button>`;
                container.innerHTML = btnHtml + listHtml;
            } else {
                container.innerHTML = listHtml;
                if(activeDashTab === 'todo') new Sortable(container, { animation: 150, handle: '.drag-handle' });
            }
        }

        /* --- ATTENDANCE TAB LOGIC --- */
        function switchAttTab(tab) {
            const teamTab = document.getElementById('att-tab-team');
            const matTab = document.getElementById('att-tab-material');
            const teamContent = document.getElementById('att-content-team');
            const matContent = document.getElementById('att-content-material');
            
            if(tab === 'team') {
                teamTab.classList.replace('text-slate-400', 'bg-white');
                teamTab.classList.replace('hover:text-slate-600', 'text-brand-dark');
                teamTab.classList.add('shadow-sm');
                
                matTab.classList.replace('bg-white', 'text-slate-400');
                matTab.classList.replace('text-brand-dark', 'hover:text-slate-600');
                matTab.classList.remove('shadow-sm');
                
                teamContent.classList.remove('hidden');
                matContent.classList.add('hidden');
            } else {
                matTab.classList.replace('text-slate-400', 'bg-white');
                matTab.classList.replace('hover:text-slate-600', 'text-brand-dark');
                matTab.classList.add('shadow-sm');
                
                teamTab.classList.replace('bg-white', 'text-slate-400');
                teamTab.classList.replace('text-brand-dark', 'hover:text-slate-600');
                teamTab.classList.remove('shadow-sm');
                
                matContent.classList.remove('hidden');
                teamContent.classList.add('hidden');
            }
        }

        /* --- FLOW --- */
        function prepareTask(taskId) {
            activeTaskId = taskId;
            const task = tasks.find(t => t.id === taskId);
            document.getElementById('att-plan-title').innerText = task.title;
            
            // Reset to Team tab
            switchAttTab('team');

            const list = document.getElementById('attendance-list');
            list.innerHTML = task.team.map(uid => {
                const emp = employees.find(e => e.id === uid);
                const userAtt = task.attendance?.[uid] || { status: 'pending' };
                const isSick = userAtt.status === 'sick';
                const isLeave = userAtt.status === 'leave';
                const isPresent = userAtt.status === 'present';
                const assignedRep = userAtt.rep ? employees.find(e => e.id == userAtt.rep) : null;

                return `<div id="att-card-${uid}" class="bg-slate-50 p-4 rounded-3xl flex flex-col gap-4 border border-slate-100 member-card">
                    <div class="flex items-center gap-4"><img src="${emp.img}" class="w-14 h-14 rounded-full border-2 border-white shadow-sm"><div class="flex-1"><h3 class="font-bold text-slate-800">${emp.name}</h3><p class="text-xs text-brand-sky font-bold uppercase">${emp.role}</p></div></div>
                    <div class="flex bg-white rounded-2xl p-1.5 border border-slate-100 shadow-sm">
                        <button onclick="updateAttendanceStatus(${taskId}, ${uid}, 'present')" data-status="present" class="att-btn flex-1 py-3 rounded-xl text-xs font-bold ${isPresent ? 'bg-brand-action text-white shadow-sm' : 'text-slate-400'} transition">Anwesend</button>
                        <button onclick="updateAttendanceStatus(${taskId}, ${uid}, 'sick')" data-status="sick" class="att-btn flex-1 py-3 rounded-xl text-xs font-bold ${isSick ? 'bg-orange-400 text-white shadow-sm' : 'text-slate-400'} transition">Krank</button>
                        <button onclick="updateAttendanceStatus(${taskId}, ${uid}, 'leave')" data-status="leave" class="att-btn flex-1 py-3 rounded-xl text-xs font-bold ${isLeave ? 'bg-red-400 text-white shadow-sm' : 'text-slate-400'} transition">Urlaub</button>
                    </div>
                    
                    <div id="rep-select-${uid}" class="${(isSick || isLeave) ? '' : 'hidden'} mt-1 p-3 bg-red-50 rounded-2xl border border-red-100 animate-fade-in-up">
                        <p class="text-[10px] text-red-400 font-bold uppercase mb-2 flex items-center gap-1"><i class="fa-solid fa-user-shield"></i> Vertretung wählen</p>
                        <select onchange="assignRep(${taskId}, ${uid}, this.value)" class="w-full text-sm bg-white border border-red-200 rounded-xl p-3 outline-none font-bold text-slate-600">
                            <option value="">${assignedRep ? 'Aktuell: ' + assignedRep.name : 'Vertretung wählen...'}</option>
                            ${employees.filter(e => e.id !== uid).map(e => `<option value="${e.id}">${e.name} (${e.role})</option>`).join('')}
                        </select>
                    </div>
                </div>`;
            }).join('');
            
            // Render Materials
            renderMaterialList();
            
            navTo('view-attendance');
        }

        /* --- MATERIAL LIST LOGIC --- */
        function renderMaterialList() {
            const task = tasks.find(t => t.id === activeTaskId);
            // Default to legacy materials array if exists, or empty
            if(!task.materials) task.materials = [];
            
            const container = document.getElementById('material-list-items');
            if(task.materials.length === 0) {
                container.innerHTML = '<p class="text-sm text-slate-400 italic text-center py-2">Keine Materialien hinzugefügt.</p>';
            } else {
                container.innerHTML = task.materials.map((mat, i) => `
                    <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                        <span class="font-bold text-slate-700 text-sm">${mat}</span>
                        <button onclick="deleteMaterial(${i})" class="text-red-400 hover:text-red-600 w-8 h-8 flex items-center justify-center bg-red-50 rounded-lg transition"><i class="fa-solid fa-trash-can text-xs"></i></button>
                    </div>
                `).join('');
            }
        }
        
        function addMaterial() {
            const input = document.getElementById('material-input');
            const val = input.value.trim();
            if(val) {
                const task = tasks.find(t => t.id === activeTaskId);
                if(!task.materials) task.materials = [];
                task.materials.push(val);
                saveData();
                renderMaterialList();
                input.value = "";
            }
        }
        
        function deleteMaterial(idx) {
            const task = tasks.find(t => t.id === activeTaskId);
            if(task.materials) {
                task.materials.splice(idx, 1);
                saveData();
                renderMaterialList();
            }
        }

        /* ... existing functions ... */
        function markAllPresent() { 
            const task = tasks.find(t => t.id === activeTaskId);
            task.team.forEach(uid => updateAttendanceStatus(activeTaskId, uid, 'present'));
        }
        function confirmAttendanceAndStart() {
            document.getElementById('setup-plan-name').innerText = tasks.find(t => t.id === activeTaskId).title;
            navTo('view-setup');
        }
        function selectOrigin(el, type) {
            document.querySelectorAll('.origin-card').forEach(c => { c.classList.remove('border-brand-sky', 'bg-brand-sky/5'); c.classList.add('border-slate-100'); });
            el.classList.remove('border-slate-100'); el.classList.add('border-brand-sky', 'bg-brand-sky/5');
            document.getElementById('route-preview').classList.remove('translate-y-full');

            if(type === 'gps') {
                document.getElementById('gps-status').innerText = "Standort wird ermittelt...";
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => { document.getElementById('gps-status').innerText = `Lat: ${pos.coords.latitude.toFixed(2)}, Lng: ${pos.coords.longitude.toFixed(2)}`; },
                        (err) => { document.getElementById('gps-status').innerText = "Standortzugriff verweigert"; alert("GPS-Zugriff erforderlich."); }
                    );
                }
            }
        }
        function startActiveMode() {
            logEvent(activeTaskId, 'start');
            document.getElementById('active-dest-name').innerText = tasks.find(t => t.id === activeTaskId).location;
            navTo('view-active');
            setTimeout(() => { if(!map) { map = L.map('active-map', { zoomControl: false }).setView([40.7128, -74.0060], 14); L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map); } }, 500);
        }
        function handleArrive() {
            logEvent(activeTaskId, 'arrive');
            const task = tasks.find(t => t.id === activeTaskId);
            document.getElementById('checklist-title').innerText = task.title;
            document.getElementById('checkin-time').innerText = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            renderChecklist(task);
            navTo('view-checklist');
        }

        /* --- JOURNEY HISTORY LOGIC --- */
        function viewJourney(taskId) {
            const task = tasks.find(t => t.id === taskId);
            renderJourneyMap(task.events || [], `Fahrtenbuch: ${task.title}`);
        }

        function viewDailyRoute() {
            const completed = tasks.filter(t => t.status === 'completed');
            if(completed.length === 0) { alert("Kein Verlauf verfügbar."); return; }
            
            let allEvents = [];
            completed.forEach(t => {
                if(t.events) {
                    // Add task title to meta for aggregated view
                    const taskEvents = t.events.map(e => ({...e, taskTitle: t.title}));
                    allEvents = allEvents.concat(taskEvents);
                }
            });
            renderJourneyMap(allEvents, "Tägliches Routenprotokoll");
        }

        function renderJourneyMap(events, title) {
            navTo('view-journey-history');
            document.getElementById('history-title').innerText = title;
            
            setTimeout(() => {
                if(!historyMap) {
                    historyMap = L.map('history-map', {zoomControl: false}).setView([40.7128, -74.0060], 13);
                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(historyMap);
                }
                
                historyMap.eachLayer((layer) => { if (!layer._url) historyMap.removeLayer(layer); });

                if(events.length > 0) {
                    const latlngs = events.map(e => [e.lat, e.lng]);
                    // Draw lines
                    L.polyline(latlngs, {color: '#164191', weight: 4, dashArray: '5, 10'}).addTo(historyMap);
                    
                    // Add Markers
                    events.forEach(e => {
                        let color = '#74b2d4'; 
                        if(e.type === 'pause') color = '#f97316';
                        if(e.type === 'stop') color = '#ef4444';
                        if(e.type === 'arrive') color = '#93c21c';
                        if(e.type === 'start') color = '#164191';
                        
                        const icon = L.divIcon({
                            html: `<div style="background:${color}" class="w-4 h-4 rounded-full border-2 border-white shadow-md"></div>`,
                            className: 'bg-transparent',
                            iconSize: [16, 16]
                        });
                        L.marker([e.lat, e.lng], {icon: icon}).addTo(historyMap).bindPopup(`${e.type.toUpperCase()} @ ${e.time}`);
                    });
                    
                    historyMap.fitBounds(latlngs, {padding: [50, 50]});
                }
            }, 300);

            // Render Timeline
            const tl = document.getElementById('journey-timeline');
            if(!events || events.length === 0) {
                tl.innerHTML = '<p class="text-center text-slate-400 mt-10">Keine Fahrereignisse aufgezeichnet.</p>';
            } else {
                tl.innerHTML = events.map((e, i) => {
                    let icon = 'fa-play'; 
                    let color = 'text-brand-sky bg-sky-50';
                    let title = 'Fahrt gestartet';
                    let desc = e.taskTitle ? `Aufgabe: ${e.taskTitle}` : ''; 
                    
                    if(e.type === 'pause') { icon = 'fa-pause'; color = 'text-orange-500 bg-orange-50'; title = 'Pausiert: ' + e.meta.reason; }
                    if(e.type === 'stop') { icon = 'fa-ban'; color = 'text-red-500 bg-red-50'; title = 'Gestoppt: ' + e.meta.reason; }
                    if(e.type === 'arrive') { icon = 'fa-flag-checkered'; color = 'text-green-500 bg-green-50'; title = 'Am Standort angekommen'; }

                    return `
                        <div class="timeline-item relative pl-8 pb-8">
                            <div class="timeline-line"></div>
                            <div class="absolute left-0 top-0 w-8 h-8 rounded-full ${color} flex items-center justify-center z-10 border-4 border-white shadow-sm">
                                <i class="fa-solid ${icon} text-[10px]"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-400 block mb-1">${e.time}</span>
                                <h4 class="font-bold text-slate-800 text-sm">${title}</h4>
                                <p class="text-[10px] text-slate-400 mt-1">${desc}</p>
                                <p class="text-[10px] text-slate-300">Lat: ${e.lat.toFixed(4)}, Lng: ${e.lng.toFixed(4)}</p>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }

        /* --- CAMERA & OTHER --- */
        let activePhotoIdx = null;
        function triggerCamera(idx) {
            activePhotoIdx = idx;
            document.getElementById('hidden-camera-input').click();
        }
        function handleCameraInput(e) {
            const file = e.target.files[0];
            if (file && activePhotoIdx !== null) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const base64Data = event.target.result;
                    const task = tasks.find(t => t.id === activeTaskId);
                    task.checklist[activePhotoIdx].hasPhoto = true;
                    task.checklist[activePhotoIdx].photoData = base64Data; // Store actual photo
                    saveData();
                    renderChecklist(task);
                };
                reader.readAsDataURL(file);
            }
        }


        /* --- CHECKLIST & TIMER --- */
        function renderChecklist(task) {
            const list = document.getElementById('checklist-container');
            list.innerHTML = task.checklist.map((item, i) => `
                <div class="flex flex-col gap-3 p-4 bg-slate-50 rounded-[1.5rem] border border-slate-100 group transition-all">
                    <div class="flex items-center gap-4">
                        <label class="relative flex items-center justify-center w-7 h-7 cursor-pointer">
                            <input type="checkbox" class="absolute opacity-0 task-checkbox" ${item.done ? 'checked' : ''} onchange="toggleTaskDone(${i})">
                            <div class="w-7 h-7 border-2 border-slate-300 rounded-lg transition-all flex items-center justify-center bg-white"><i class="fa-solid fa-check text-white text-xs hidden"></i></div>
                        </label>
                        <div class="flex-1"><div class="text-sm font-bold text-slate-700">${item.txt}</div></div>
                        
                        <!-- Timer & Duration -->
                        <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-lg px-2 py-1 shadow-sm ${item.isTiming ? 'border-red-400' : ''}">
                            <button onclick="toggleTimer(${i})" class="${item.isTiming ? 'timer-active' : 'text-slate-400'} hover:text-brand-dark transition"><i class="fa-solid ${item.isTiming ? 'fa-stop-circle' : 'fa-play-circle'}"></i></button>
                            <input type="number" value="${item.duration || 0}" class="w-8 text-xs font-bold text-center outline-none text-slate-600" onchange="updateDuration(${i}, this.value)">
                            <span class="text-[10px] text-slate-400">m</span>
                        </div>

                        <!-- Real Camera Trigger -->
                        <button onclick="triggerCamera(${i})" class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-brand-dark hover:border-brand-dark transition flex items-center justify-center shadow-sm ${item.hasPhoto ? 'bg-green-100 border-green-200 text-green-600' : ''}"><i class="fa-solid ${item.hasPhoto ? 'fa-check' : 'fa-camera'} text-xs"></i></button>
                        <button onclick="toggleDetails(${i})" class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-brand-sky hover:border-brand-sky transition flex items-center justify-center shadow-sm"><i class="fa-solid fa-pen text-xs"></i></button>
                    </div>
                    <div id="detail-${i}" class="hidden p-3 bg-white border border-slate-100 rounded-2xl shadow-inner">
                        <p class="text-[10px] font-bold text-slate-400 mb-2 uppercase">Anmerkungen</p>
                        <textarea class="w-full text-sm p-3 bg-slate-50 rounded-xl border border-slate-100 outline-none font-medium text-slate-600" rows="2" placeholder="Spezifische Notizen hinzufügen..." onchange="updateItemRemark(${i}, this.value)">${item.remark || ''}</textarea>
                    </div>
                </div>
            `).join('');
            calcTotalTime();
        }

        /* --- STOPWATCH LOGIC --- */
        function toggleTimer(idx) {
            const task = tasks.find(t => t.id === activeTaskId);
            
            if (task.checklist[idx].isTiming) {
                // Stop Timer
                task.checklist[idx].isTiming = false;
                clearInterval(activeTimer);
                activeTimer = null;
            } else {
                // Start Timer
                // Stop others first
                task.checklist.forEach(i => i.isTiming = false);
                clearInterval(activeTimer);
                
                task.checklist[idx].isTiming = true;
                activeTimer = setInterval(() => {
                    task.checklist[idx].duration = (parseInt(task.checklist[idx].duration) || 0) + 1;
                    renderChecklist(task); // Re-render to show time tick
                }, 60000); // Update every minute for demo (use 1000 for seconds)
            }
            saveData();
            renderChecklist(task);
        }

        function toggleTaskDone(idx) { 
            const task = tasks.find(t => t.id === activeTaskId); 
            task.checklist[idx].done = !task.checklist[idx].done; 
            saveData(); 
        }
        
        function updateDuration(idx, val) { 
            const task = tasks.find(t => t.id === activeTaskId); 
            task.checklist[idx].duration = parseInt(val) || 0; 
            saveData();
            calcTotalTime(); 
        }
        
        function toggleDetails(idx) { document.getElementById(`detail-${idx}`).classList.toggle('hidden'); }
        function updateItemRemark(idx, val) { 
            const task = tasks.find(t => t.id === activeTaskId); 
            task.checklist[idx].remark = val; 
            saveData();
        }
        
        function calcTotalTime() { 
            const task = tasks.find(t => t.id === activeTaskId); 
            const total = task.checklist.reduce((acc, curr) => acc + (parseInt(curr.duration) || 0), 0); 
            document.getElementById('total-minutes').innerText = total; 
        }
        
        function addNewTaskInline() { 
            const input = document.getElementById('new-task-input'); 
            if(input.value.trim()) { 
                const task = tasks.find(t => t.id === activeTaskId); 
                task.checklist.push({ txt: input.value, done: false, duration: 15, remark: "", hasPhoto: false, photo: null }); 
                saveData();
                renderChecklist(task); 
                input.value = ""; 
            } 
        }
        
        function finishJob() { 
            // VALIDATION
            const task = tasks.find(t => t.id === activeTaskId); 
            const allDone = task.checklist.every(i => i.done);
            
            if(!allDone) {
                openModal('alert');
                return;
            }
            
            navTo('view-report');
            // Resize canvas
            setTimeout(() => {
                const canvas = document.getElementById('signature-pad');
                // canvas.width = canvas.parentElement.offsetWidth;
                canvas.width = canvas.parentElement.offsetWidth; // Ensure proper width
                canvas.height = 150;
            }, 100);
        }
        
        function completeAndNext() { 
            const notes = document.getElementById('report-text').value; 
            const taskIndex = tasks.findIndex(t => t.id === activeTaskId); 
            
            // Capture Signature
            const canvas = document.getElementById('signature-pad');
            const signatureData = canvas.toDataURL('image/png');

            if(taskIndex > -1) { 
                tasks[taskIndex].status = "completed"; 
                tasks[taskIndex].report = notes;
                tasks[taskIndex].signature = signatureData; 
            } 
            
            saveData();
            
            activeTaskId = null; 
            clearInterval(activeTimer);
            document.getElementById('report-text').value = ""; 
            clearSignature('signature-pad');
            renderTasks(); 
            navTo('view-dashboard'); 
        }

        /* --- SIGNATURE LOGIC --- */
        function initSignaturePad(canvasId = 'signature-pad') {
            const canvas = document.getElementById(canvasId);
            if(!canvas) return;
            
            // Ensure width is set correctly based on container
            canvas.width = canvas.parentElement.offsetWidth;
            canvas.height = 150;

            const ctx = canvas.getContext('2d');
            ctx.lineWidth = 2;
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#164191';

            let isDrawingLocal = false;

            const startDraw = (e) => {
                isDrawingLocal = true;
                const rect = canvas.getBoundingClientRect();
                const x = (e.clientX || e.touches[0].clientX) - rect.left;
                const y = (e.clientY || e.touches[0].clientY) - rect.top;
                ctx.beginPath();
                ctx.moveTo(x, y);
            };

            const draw = (e) => {
                if (!isDrawingLocal) return;
                // e.preventDefault(); // Stop scrolling (Removed default prevent here, handled by passive: false)
                const rect = canvas.getBoundingClientRect();
                const clientX = e.clientX || (e.touches && e.touches[0].clientX);
                const clientY = e.clientY || (e.touches && e.touches[0].clientY);
                
                if (clientX && clientY) {
                    const x = clientX - rect.left;
                    const y = clientY - rect.top;
                    ctx.lineTo(x, y);
                    ctx.stroke();
                }
            };

            const stopDraw = () => { isDrawingLocal = false; };

            // Remove old listeners to prevent duplication if init called multiple times
            const newCanvas = canvas.cloneNode(true);
            canvas.parentNode.replaceChild(newCanvas, canvas);
            
            // Add listeners with passive: false for touch events to allow preventDefault
            newCanvas.addEventListener('mousedown', startDraw);
            newCanvas.addEventListener('touchstart', (e) => { e.preventDefault(); startDraw(e); }, { passive: false });
            
            newCanvas.addEventListener('mousemove', draw);
            newCanvas.addEventListener('touchmove', (e) => { e.preventDefault(); draw(e); }, { passive: false });
            
            newCanvas.addEventListener('mouseup', stopDraw);
            newCanvas.addEventListener('touchend', stopDraw);
        }

        function clearSignature(canvasId) {
            const canvas = document.getElementById(canvasId);
            if(canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        }

        /* --- JOB CANCELLATION LOGIC --- */
        function initCancelJob(id = null) {
            if(id) activeTaskId = id;
            openModal('cancel_job');
            // Wait for modal to render then init canvas
            setTimeout(() => {
                initSignaturePad('cancel-signature-pad');
            }, 100);
        }
        
        function openTaskMenu(taskId) {
            activeTaskId = taskId;
            const content = document.getElementById('modal-content');
            content.innerHTML = `
                <div class="text-left">
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Optionen</h3>
                    <div class="space-y-3">
                        <button onclick="openModal('delegate', ${taskId})" class="w-full bg-white border-2 border-slate-100 text-slate-700 font-bold py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-slate-50 transition">
                            <i class="fa-solid fa-user-plus text-brand-dark"></i> Aufgabe delegieren
                        </button>
                        <button onclick="initCancelJob(${taskId})" class="w-full bg-white border-2 border-red-100 text-red-500 font-bold py-4 rounded-2xl flex items-center justify-center gap-2 hover:bg-red-50 transition">
                            <i class="fa-solid fa-ban"></i> Auftrag stornieren
                        </button>
                    </div>
                    <button onclick="closeModal()" class="w-full mt-4 text-slate-400 font-bold py-3">Abbrechen</button>
                </div>
            `;
            openModal('custom_html');
        }

        function submitCancellation() {
            const reason = document.getElementById('cancel-reason').value;
            const canvas = document.getElementById('cancel-signature-pad');
            
            // Basic check if signed (compare to empty canvas)
            const blank = document.createElement('canvas');
            blank.width = canvas.width; blank.height = canvas.height;
            if(canvas.toDataURL() === blank.toDataURL()) {
                // alert("Please sign to confirm cancellation.");
                // return;
            }
            
            if(!reason || reason === "") { alert("Bitte geben Sie einen Stornierungsgrund an."); return; }

            const taskIndex = tasks.findIndex(t => t.id === activeTaskId);
            if(taskIndex > -1) {
                tasks[taskIndex].status = 'cancelled';
                tasks[taskIndex].cancellationReason = reason;
                tasks[taskIndex].signature = canvas.toDataURL(); // Save auth signature
                tasks[taskIndex].report = `Auftrag storniert. Grund: ${reason}`;
            }
            saveData();
            
            activeTaskId = null;
            clearInterval(activeTimer);
            closeModal();
            renderTasks();
            navTo('view-dashboard');
        }

        /* --- VOICE TO TEXT SIMULATION --- */
        function initVoice() {
             // Check for standard or webkit speech recognition
             const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
             
             if (SpeechRecognition) {
                recognition = new SpeechRecognition();
                recognition.continuous = false;
                recognition.interimResults = false;

                recognition.onresult = function(event) {
                    const text = event.results[0][0].transcript;
                    const area = document.getElementById('report-text');
                    area.value += (area.value ? " " : "") + text;
                    toggleVoice(); // turn off ui
                };
                
                recognition.onerror = function() { 
                    // Fallback to simulation on error
                    runVoiceSimulation();
                };
                recognition.onend = function() { if(isListening) toggleVoice(); };
             }
        }
        
        function runVoiceSimulation() {
             // Simulate typing for fallback
             setTimeout(() => {
                const area = document.getElementById('report-text');
                const phrases = ["Auftrag erfolgreich erledigt.", "Standort ist sauber.", "Kunde war zufrieden.", "Keine Probleme festgestellt."];
                const text = phrases[Math.floor(Math.random() * phrases.length)];
                area.value += (area.value ? " " : "") + text;
                toggleVoice(); // turn off ui
            }, 1500);
        }
        
        function toggleVoice() {
            isListening = !isListening;
            const ind = document.getElementById('voice-indicator');
            const btn = document.getElementById('mic-btn');
            
            if(isListening) {
                ind.classList.remove('hidden');
                ind.classList.add('voice-active');
                btn.classList.add('bg-red-500', 'animate-pulse');
                
                if(recognition) {
                    try {
                        recognition.start();
                    } catch(e) {
                        runVoiceSimulation();
                    }
                } else {
                    runVoiceSimulation();
                }
            } else {
                ind.classList.add('hidden');
                ind.classList.remove('voice-active');
                btn.classList.remove('bg-red-500', 'animate-pulse');
                if(recognition) {
                    try { recognition.stop(); } catch(e){}
                }
            }
        }

        /* --- SUMMARY PAGE LOGIC --- */
        function showSummary() {
            navTo('view-summary');
            const confettiContainer = document.getElementById('confetti-container');
            confettiContainer.innerHTML = Array(30).fill('<div class="confetti" style="left:'+Math.random()*100+'%; animation-delay:'+Math.random()*2+'s"></div>').join('');
            
            const completed = tasks.filter(t => t.status === 'completed' || t.status === 'cancelled');
            document.getElementById('total-completed-count').innerText = completed.length;
            
            const container = document.getElementById('summary-list');
            container.innerHTML = completed.map(t => {
                // Calculate detailed expense html
                let expenseHtml = '';
                if (t.expenses && t.expenses.length > 0) {
                     const total = t.expenses.reduce((a,b) => a + parseFloat(b.amount), 0);
                     expenseHtml = `
                        <div class="mt-2 pt-2 border-t border-white/20">
                            <div class="text-[10px] text-green-300 font-bold mb-1">AUSGABEN ($${total.toFixed(2)})</div>
                            ${t.expenses.map(ex => {
                                 const emp = employees.find(e => e.id == ex.employeeId) || {name: 'Unbekannt'};
                                 return `<div class="text-[10px] text-white/70 flex justify-between">
                                    <span>${ex.cat}: ${ex.reason}</span>
                                    <span>$${ex.amount} (${emp.name.split(',')[0]})</span>
                                 </div>`;
                            }).join('')}
                        </div>
                     `;
                }
                
                let statusBadge = '';
                if(t.status === 'cancelled') {
                    statusBadge = '<span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded ml-2">STORNIERT</span>';
                }

                return `
                <div class="border-l-4 ${t.status === 'cancelled' ? 'border-red-500' : 'border-brand-action'} pl-4 py-2 bg-white/5 rounded-r-xl mb-2">
                    <h4 class="font-bold text-white text-lg flex items-center">${t.title} ${statusBadge}</h4>
                    <p class="text-xs text-blue-200 opacity-70 mb-1"><i class="fa-solid fa-location-dot"></i> ${t.location}</p>
                    <div class="text-xs text-white/60 italic">"${t.report || t.cancellationReason || 'Erledigt.'}"</div>
                    ${expenseHtml}
                </div>
            `}).join('');
        }
    </script>
</body>
</html>