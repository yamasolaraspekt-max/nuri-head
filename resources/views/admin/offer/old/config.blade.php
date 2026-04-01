<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart CRM v14 - Task Management</title>
    
    <!-- External Frameworks -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    colors: { primary: '#4f46e5', secondary: '#0f172a', surface: '#ffffff', background: '#f8fafc' },
                    animation: { 'fadeIn': 'fadeIn 0.3s ease-in-out' },
                    keyframes: { fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } } }
                }
            }
        }
    </script>

    <style>
        /* CORE STYLES */
        body { background-color: #f1f5f9; color: #0f172a; overflow: hidden; height: 100vh; display: flex; flex-direction: column; }
        
        /* TRANSITIONS & ANIMATIONS */
        .stage-view { display: none; height: 100%; width: 100%; flex-direction: column; opacity: 0; transform: translateY(10px); transition: opacity 0.4s ease, transform 0.4s ease; }
        .stage-view.active { display: flex; opacity: 1; transform: translateY(0); }
        
        /* CUSTOM SCROLLBAR */
        .scroller::-webkit-scrollbar { width: 6px; height: 6px; }
        .scroller::-webkit-scrollbar-track { background: transparent; }
        .scroller::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .scroller::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* INTERACTIVE ELEMENTS */
        .btn-primary { @apply bg-indigo-600 text-white shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all active:scale-95; }
        
        /* RANGE SLIDER */
        input[type=range] { -webkit-appearance: none; width: 100%; background: transparent; }
        input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; height: 16px; width: 16px; border-radius: 50%; background: #ffffff; border: 2px solid #4f46e5; cursor: pointer; margin-top: -6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.1s; }
        input[type=range]::-webkit-slider-thumb:hover { transform: scale(1.2); }
        input[type=range]::-webkit-slider-runnable-track { width: 100%; height: 4px; background: #e2e8f0; border-radius: 2px; }

        /* ACCORDION ANIMATION */
        .accordion-content { transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out; max-height: 0; opacity: 0; overflow: hidden; }
        .accordion-content.open { max-height: 500px; opacity: 1; }
        .accordion-btn.active { @apply bg-slate-50 text-indigo-700; }
        .accordion-btn:hover { @apply bg-slate-50; }
        .accordion-icon { transition: transform 0.3s ease; }
        .accordion-btn.active .accordion-icon { transform: rotate(180deg); }

        /* MODAL */
        .modal-overlay { background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); }

        /* DRAG AND DROP */
        .draggable-source { cursor: grab; }
        .draggable-source:active { cursor: grabbing; }
        .drop-zone { transition: all 0.2s; min-height: 100px; }
        .drop-zone.drag-over { background-color: #e0e7ff; border-color: #4f46e5; transform: scale(1.01); }

        /* --- OPTIMIZER LAYOUT SPECIFIC --- */
        .a4-page { width: 210mm; min-height: 297mm; background: white; box-shadow: 0 0 20px rgba(0,0,0,0.1); margin: 0 auto; padding: 20mm; }
        @media screen and (max-width: 1024px) { .a4-page { width: 100%; margin: 0; padding: 15px; box-shadow: none; } }
    </style>
</head>
<body>

    <!-- TOP NAVIGATION BAR -->
    <header id="main-header" class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0 z-40 relative transition-transform duration-300">
        <div class="flex items-center gap-4">
            <button id="btn-reset" onclick="App.reset()" class="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-red-500 transition flex items-center justify-center" title="Reset Flow">
                <i class="fa-solid fa-power-off"></i>
            </button>
            <div class="h-6 w-px bg-slate-200"></div>
            <div>
                <h1 class="font-bold text-slate-800 leading-tight">SmartQuote <span class="text-indigo-600">Pro</span></h1>
                <div class="text-[10px] text-slate-400 font-mono tracking-wider uppercase">AI Configurator v14</div>
            </div>
        </div>

        <!-- PROGRESS STEPS -->
        <div class="hidden md:flex items-center gap-2">
            <div class="step-item flex items-center gap-2 text-xs font-bold text-slate-400 select-none cursor-pointer" id="step-1" onclick="App.goto(1)">
                <div class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center transition-colors">1</div>
                <span>Projekt</span>
            </div>
            <div class="w-8 h-px bg-slate-200"></div>
            <div class="step-item flex items-center gap-2 text-xs font-bold text-slate-400 select-none pointer-events-none opacity-50" id="step-2" onclick="App.goto(2)">
                <div class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center transition-colors">2</div>
                <span>Analyse</span>
            </div>
            <div class="w-8 h-px bg-slate-200"></div>
            <div class="step-item flex items-center gap-2 text-xs font-bold text-slate-400 select-none pointer-events-none opacity-50" id="step-3" onclick="App.goto(3)">
                <div class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center transition-colors">3</div>
                <span>Strategie</span>
            </div>
            <div class="w-8 h-px bg-slate-200"></div>
            <div class="step-item flex items-center gap-2 text-xs font-bold text-slate-400 select-none pointer-events-none opacity-50" id="step-4" onclick="App.goto(4)">
                <div class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center transition-colors">4</div>
                <span>Optimizer</span>
            </div>
            <div class="w-8 h-px bg-slate-200"></div>
            <div class="step-item flex items-center gap-2 text-xs font-bold text-slate-400 select-none pointer-events-none opacity-50" id="step-5" onclick="App.goto(5)">
                <div class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center transition-colors">5</div>
                <span>Planung</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-right hidden lg:block">
                <div class="text-xs font-bold text-slate-700">Max Mustermann</div>
                <div class="text-[10px] text-slate-400">Vertrieb Nord</div>
            </div>
            <div class="w-9 h-9 bg-slate-100 rounded-full border border-slate-200 flex items-center justify-center text-slate-500">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>
    </header>

    <!-- WORKSPACE WRAPPER -->
    <div class="flex flex-1 overflow-hidden">
        
        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 relative overflow-hidden bg-slate-50 flex flex-col">

            <!-- STAGE 1: PROJECT -->
            <div id="view-1" class="stage-view active p-8 scroller overflow-y-auto">
                <div class="max-w-5xl mx-auto w-full py-10">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-slate-900 mb-4">Neues Projekt starten</h2>
                        <p class="text-slate-500 max-w-lg mx-auto">Wählen Sie das Gewerk.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="group bg-white rounded-2xl p-8 shadow-sm border border-slate-200 hover:shadow-xl hover:border-indigo-200 hover:-translate-y-1 transition-all cursor-pointer relative overflow-hidden" onclick="App.selectType('wp')">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition"><i class="fa-solid fa-fire text-9xl text-indigo-600"></i></div>
                            <div class="w-16 h-16 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 text-2xl mb-6 group-hover:scale-110 transition"><i class="fa-solid fa-fire-flame-curved"></i></div>
                            <h3 class="text-xl font-bold text-slate-800 mb-2">Wärmepumpe</h3>
                            <p class="text-sm text-slate-500 mb-6">Monoblock & Split-Systeme.</p>
                            <div class="flex items-center text-indigo-600 text-sm font-bold">Starten <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition"></i></div>
                        </div>
                        <div class="group bg-white rounded-2xl p-8 shadow-sm border border-slate-200 hover:shadow-xl hover:border-amber-200 hover:-translate-y-1 transition-all cursor-pointer relative overflow-hidden" onclick="App.selectType('pv')">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition"><i class="fa-solid fa-solar-panel text-9xl text-amber-500"></i></div>
                            <div class="w-16 h-16 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500 text-2xl mb-6 group-hover:scale-110 transition"><i class="fa-solid fa-sun"></i></div>
                            <h3 class="text-xl font-bold text-slate-800 mb-2">Photovoltaik</h3>
                            <p class="text-sm text-slate-500 mb-6">Dachbelegung & Speicher.</p>
                            <div class="flex items-center text-amber-600 text-sm font-bold">Starten <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition"></i></div>
                        </div>
                        <div class="group bg-slate-50 rounded-2xl p-8 border border-dashed border-slate-300 flex flex-col items-center justify-center text-center opacity-60 cursor-not-allowed">
                            <div class="w-16 h-16 bg-slate-200 rounded-xl flex items-center justify-center text-slate-400 text-2xl mb-4"><i class="fa-solid fa-bath"></i></div>
                            <h3 class="text-lg font-bold text-slate-500">Sanitär</h3>
                            <div class="mt-2 px-2 py-1 bg-slate-200 text-[10px] font-bold uppercase tracking-wide text-slate-500 rounded">Coming Soon</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STAGE 2: ANALYSIS -->
            <div id="view-2" class="stage-view p-8 scroller overflow-y-auto">
                <div class="max-w-4xl mx-auto w-full py-6">
                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-900">Gebäude-Daten</h2>
                            <p class="text-slate-500">Parameter für die Heizlastberechnung.</p>
                        </div>
                        <div class="bg-white border border-slate-200 px-4 py-2 rounded-lg text-sm shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-location-arrow text-indigo-500"></i> 
                            <span class="font-mono text-slate-600">Norm-Außentemp: -12°C</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Beheizte Wohnfläche</label>
                                <div class="relative">
                                    <input type="number" id="inp-area" value="160" class="w-full border border-slate-300 rounded-lg pl-4 pr-12 py-3 font-mono text-lg focus:ring-2 focus:ring-indigo-500 outline-none transition" oninput="Logic.calcLoad()">
                                    <span class="absolute right-4 top-3.5 text-slate-400 font-bold text-sm">m²</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Jahresverbrauch</label>
                                <div class="relative">
                                    <input type="number" id="inp-usage" value="2500" class="w-full border border-slate-300 rounded-lg pl-4 pr-16 py-3 font-mono text-lg focus:ring-2 focus:ring-indigo-500 outline-none transition" oninput="Logic.calcLoad()">
                                    <span class="absolute right-4 top-3.5 text-slate-400 font-bold text-sm">Liter/m³</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Gebäudestandard</label>
                                <select class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                                    <option>Bestand (unsaniert)</option>
                                    <option>Teilsaniert</option>
                                    <option>Neubau / KFW 55</option>
                                </select>
                            </div>
                        </div>
                        <div class="bg-indigo-50 rounded-xl p-6 border border-indigo-100 flex flex-col justify-between">
                            <div>
                                <h4 class="font-bold text-indigo-900 mb-2"><i class="fa-solid fa-calculator mr-2"></i>Live Berechnung</h4>
                                <p class="text-xs text-indigo-700/80 leading-relaxed">Basierend auf der Schweizer Formel.</p>
                            </div>
                            <div class="mt-8 text-center">
                                <div class="text-sm font-bold text-indigo-400 uppercase tracking-widest mb-1">Heizlast</div>
                                <div class="text-5xl font-mono font-bold text-indigo-600 tracking-tighter" id="val-load">10.4</div>
                                <div class="text-sm font-bold text-indigo-400 mt-1">kW</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end">
                        <button onclick="App.goto(3)" class="btn-primary px-8 py-3 rounded-xl font-bold flex items-center gap-2">
                            Weiter zur Strategie <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- STAGE 3: STRATEGY -->
            <div id="view-3" class="stage-view p-8 scroller overflow-y-auto">
                <div class="max-w-6xl mx-auto w-full py-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 h-full">
                        <div class="lg:col-span-7 flex flex-col">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-slate-900">Strategische Ausrichtung</h2>
                                <p class="text-slate-500">Justieren Sie die Gewichtung für das KI-Matching.</p>
                            </div>
                            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 space-y-8 flex-1">
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <label class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-sack-dollar text-emerald-500"></i> Profit & Marge</label>
                                        <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 rounded" id="disp-margin">50%</span>
                                    </div>
                                    <input type="range" min="0" max="100" value="50" oninput="Logic.updateStrategy('margin', this.value)">
                                </div>
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <label class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-wrench text-amber-500"></i> Montage-Aufwand</label>
                                        <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 rounded" id="disp-install">30%</span>
                                    </div>
                                    <input type="range" min="0" max="100" value="30" oninput="Logic.updateStrategy('install', this.value)">
                                </div>
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <label class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-shield-halved text-blue-500"></i> Sicherheit (RMA)</label>
                                        <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 rounded" id="disp-risk">80%</span>
                                    </div>
                                    <input type="range" min="0" max="100" value="80" oninput="Logic.updateStrategy('risk', this.value)">
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-5 flex flex-col">
                            <div class="bg-slate-100 rounded-2xl p-6 border border-slate-200 h-full flex flex-col">
                                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Live Ranking Vorschau</h3>
                                <div id="strategy-list" class="flex-1 space-y-3 overflow-hidden relative"></div>
                                <div class="mt-6 pt-6 border-t border-slate-200/50">
                                    <button onclick="App.runAI()" class="w-full btn-primary py-4 rounded-xl font-bold text-lg shadow-lg flex justify-center items-center gap-2">
                                        <i class="fa-solid fa-wand-magic-sparkles"></i> Matching Starten
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STAGE 4: CRM OPTIMIZER -->
            <div id="view-4" class="stage-view h-full w-full">
                <!-- Optimizer Header -->
                <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 z-20 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <button onclick="App.goto(3)" class="text-slate-400 hover:text-indigo-600 mr-2"><i class="fa-solid fa-arrow-left"></i></button>
                        <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-lg">C</div>
                        <div>
                            <h1 class="font-bold text-slate-800 leading-tight">CRM Optimizer <span class="text-blue-600">v2.0</span></h1>
                            <div class="text-xs text-slate-400">Sets, Phasen & KI-Pricing</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col items-end mr-4">
                            <span class="text-xs text-slate-500 uppercase font-bold">Gesamtmarge</span>
                            <span class="text-lg font-bold text-emerald-600" id="global-margin">0.0%</span>
                        </div>
                        <button onclick="Optimizer.toggleMonteur()" class="flex items-center gap-2 px-4 py-2 bg-slate-800 text-white rounded hover:bg-slate-700 transition">
                            <span>📱</span> Monteur
                        </button>
                        <button onclick="App.goto(5)" class="btn-primary px-4 py-2 rounded text-sm font-bold flex items-center gap-2">
                            Planung <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </header>
                <!-- Content Area -->
                <div class="flex flex-1 overflow-hidden h-full">
                    <!-- Left: Library -->
                    <aside class="w-80 bg-white border-r border-slate-200 flex flex-col z-10 flex-shrink-0">
                        <div class="p-4 border-b border-slate-100 bg-slate-50">
                            <h3 class="font-bold text-slate-700">Smart Sets</h3>
                            <p class="text-xs text-slate-500">Intelligente Baugruppen</p>
                        </div>
                        <div class="flex-1 overflow-y-auto p-4 space-y-4 scroller" id="optimizer-lib"></div>
                        <div id="ai-suggestion-box" class="p-4 bg-indigo-50 border-t border-indigo-100 hidden">
                            <div class="flex items-start gap-2">
                                <span class="text-lg">💡</span>
                                <div>
                                    <h5 class="text-sm font-bold text-indigo-800">Upsell Chance!</h5>
                                    <p class="text-xs text-indigo-700 mt-1">Zu diesem Set wird oft ein <strong>Wartungsvertrag</strong> verkauft.</p>
                                    <button onclick="Optimizer.addMaintenanceContract()" class="mt-2 text-xs bg-indigo-600 text-white px-3 py-1 rounded shadow hover:bg-indigo-700">Hinzufügen (+180€)</button>
                                </div>
                            </div>
                        </div>
                    </aside>
                    <!-- Center: Document -->
                    <div class="flex-1 bg-slate-200 overflow-y-auto p-8 flex justify-center relative custom-scroll" id="main-area">
                        <div id="quote-document" class="a4-page flex flex-col transition-all duration-300">
                            <div class="border-b-2 border-slate-800 pb-4 mb-6 flex justify-between items-end">
                                <h1 class="text-3xl font-bold text-slate-900">Angebot</h1>
                                <div class="text-right">
                                    <div class="text-sm text-slate-500">Musterkunde GmbH</div>
                                    <div class="text-sm font-bold bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded inline-block mt-1">Status: Entwurf</div>
                                </div>
                            </div>
                            <div id="quote-list" class="space-y-6 flex-1">
                                <div class="text-center text-slate-400 italic mt-20">Starten Sie, indem Sie links ein Set auswählen.</div>
                            </div>
                            <div class="mt-8 border-t border-slate-300 pt-4 flex justify-between items-start">
                                <div class="w-1/2">
                                    <h4 class="font-bold text-sm text-slate-700 mb-2">Phasen-Planung (Logistik)</h4>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span> Phase 1 (Rohbau)</span>
                                            <span class="font-mono" id="sum-phase-1">0,00 €</span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-500"></span> Phase 2 (Endmontage)</span>
                                            <span class="font-mono" id="sum-phase-2">0,00 €</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-1/3 text-right">
                                    <div class="flex justify-between mb-1 text-sm"><span>Netto</span><span class="font-bold" id="total-net">0,00 €</span></div>
                                    <div class="flex justify-between mb-1 text-sm text-slate-500"><span>MwSt</span><span id="total-tax">0,00 €</span></div>
                                    <div class="flex justify-between text-xl font-bold border-t border-slate-800 pt-2"><span>Gesamt</span><span id="total-gross">0,00 €</span></div>
                                </div>
                            </div>
                        </div>
                        <div id="monteur-overlay" class="fixed inset-y-0 right-0 w-full max-w-sm bg-slate-900 text-white transform translate-x-full transition-transform duration-300 z-50 shadow-2xl overflow-y-auto">
                           <div class="p-6 bg-slate-800 flex justify-between items-center sticky top-0 z-10">
                                <h2 class="font-bold text-lg">👷‍♂️ Monteur App</h2>
                                <button onclick="Optimizer.toggleMonteur()" class="text-slate-400 hover:text-white">✕ Schließen</button>
                            </div>
                            <div class="p-6 space-y-8">
                                <div class="bg-white p-4 rounded-lg flex flex-col items-center text-slate-900 text-center">
                                    <div class="w-32 h-32 bg-slate-200 mb-2 flex items-center justify-center text-xs text-slate-400 border-2 border-dashed border-slate-300">[QR Code]</div>
                                    <p class="font-bold text-sm">Projekt #2024-99</p>
                                </div>
                                <div><h3 class="font-bold text-red-400 uppercase text-sm tracking-wider mb-2">Phase 1: Rohbau</h3><div id="monteur-list-p1" class="space-y-3"></div></div>
                                <div class="opacity-75"><h3 class="font-bold text-green-400 uppercase text-sm tracking-wider mb-2">Phase 2: Finale</h3><div id="monteur-list-p2" class="space-y-3"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STAGE 5: RESOURCE PLANNING -->
            <div id="view-5" class="stage-view h-full w-full">
                <!-- Planning Header -->
                <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 z-20 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <button onclick="App.goto(4)" class="text-slate-400 hover:text-indigo-600 mr-2"><i class="fa-solid fa-arrow-left"></i></button>
                        <div class="w-8 h-8 bg-purple-600 rounded flex items-center justify-center text-white font-bold text-lg">P</div>
                        <div>
                            <h1 class="font-bold text-slate-800 leading-tight">Ressourcen & <span class="text-purple-600">Planung</span></h1>
                            <div class="text-xs text-slate-400">Team, Fuhrpark & Zeitplan</div>
                        </div>
                    </div>
                </header>
                
                <div class="flex flex-1 overflow-hidden h-full">
                    <!-- Resource Pool (Draggable) -->
                    <aside class="w-80 bg-slate-50 border-r border-slate-200 flex flex-col z-10 flex-shrink-0">
                        <div class="p-4 border-b border-slate-200">
                            <h3 class="font-bold text-slate-700">Verfügbare Ressourcen</h3>
                            <p class="text-xs text-slate-500">Drag & Drop in die Phasen</p>
                        </div>
                        <div class="flex-1 overflow-y-auto p-4 space-y-6 scroller">
                            <div>
                                <h4 class="text-[10px] uppercase font-bold text-slate-400 mb-3 tracking-wider">Mitarbeiter (pro Std)</h4>
                                <div class="space-y-2" id="resource-pool-staff"></div>
                            </div>
                            <div>
                                <h4 class="text-[10px] uppercase font-bold text-slate-400 mb-3 tracking-wider">Fuhrpark & Assets (pro Tag)</h4>
                                <div class="space-y-2" id="resource-pool-assets"></div>
                            </div>
                        </div>
                    </aside>

                    <!-- Planning Board (Drop Zones) -->
                    <main class="flex-1 bg-white overflow-y-auto p-8 relative scroller">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 h-full">
                            <!-- Phase 1 Drop Zone -->
                            <div class="flex flex-col h-full">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-red-500"></div><h3 class="font-bold text-lg text-slate-800">Phase 1: Rohbau</h3></div>
                                    <div class="text-xs font-mono bg-red-50 text-red-600 px-2 py-1 rounded border border-red-100 font-bold" id="cost-p1">0.00 €</div>
                                </div>
                                <div id="drop-p1" class="flex-1 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl p-4 space-y-3 drop-zone relative" ondragover="ResourcePlanner.allowDrop(event)" ondrop="ResourcePlanner.drop(event, 1)" ondragleave="ResourcePlanner.leave(event)">
                                    <div class="absolute inset-0 flex items-center justify-center text-slate-300 pointer-events-none empty-msg"><span class="text-sm">Ressourcen hier ablegen</span></div>
                                </div>
                            </div>

                            <!-- Phase 2 Drop Zone -->
                            <div class="flex flex-col h-full">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-green-500"></div><h3 class="font-bold text-lg text-slate-800">Phase 2: Finale</h3></div>
                                    <div class="text-xs font-mono bg-green-50 text-green-600 px-2 py-1 rounded border border-green-100 font-bold" id="cost-p2">0.00 €</div>
                                </div>
                                <div id="drop-p2" class="flex-1 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl p-4 space-y-3 drop-zone relative" ondragover="ResourcePlanner.allowDrop(event)" ondrop="ResourcePlanner.drop(event, 2)" ondragleave="ResourcePlanner.leave(event)">
                                    <div class="absolute inset-0 flex items-center justify-center text-slate-300 pointer-events-none empty-msg"><span class="text-sm">Ressourcen hier ablegen</span></div>
                                </div>
                            </div>
                        </div>
                    </main>

                    <!-- Summary Panel -->
                    <aside class="w-72 bg-white border-l border-slate-200 flex flex-col p-6 z-10">
                        <h3 class="font-bold text-slate-800 mb-6">Kostenschätzung</h3>
                        <div class="space-y-6 flex-1">
                            <div><label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Montage Budget</label><div class="text-2xl font-bold text-slate-700 font-mono" id="budget-calc">0.00 €</div><div class="text-xs text-slate-500 mt-1">Aus Angebot & Strategie.</div></div>
                            <div class="h-px bg-slate-100"></div>
                            <div><label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Interne Kosten</label><div class="text-2xl font-bold text-purple-600 font-mono" id="cost-total">0.00 €</div></div>
                            <div class="bg-slate-50 p-4 rounded-lg border border-slate-100" id="profit-box">
                                <div class="flex justify-between items-center mb-1"><span class="text-sm font-bold text-slate-700">Montage-Gewinn</span><span class="font-bold text-emerald-500" id="profit-val">0.00 €</span></div>
                                <div class="w-full bg-slate-200 rounded-full h-2 mt-2"><div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: 0%" id="profit-bar"></div></div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

        </main>

        <!-- GLOBAL SIDEBAR -->
        <aside class="w-80 bg-white border-l border-slate-200 flex flex-col z-30 flex-shrink-0 scroller overflow-y-auto shadow-xl">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><div><h3 class="font-bold text-slate-700">Projekt Übersicht</h3><p class="text-xs text-slate-500">Daten-Steuerzentrale</p></div><i class="fa-solid fa-server text-slate-300"></i></div>
            <div class="border-b border-slate-100"><button onclick="App.goto(2); Sidebar.toggle('acc-proj', this)" class="accordion-btn w-full p-4 flex items-center justify-between text-slate-700 hover:bg-slate-50 transition"><div class="flex items-center gap-3 font-bold text-sm"><div class="w-6 h-6 rounded bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="fa-solid fa-building"></i></div>Gebäude</div><i class="fa-solid fa-chevron-down text-xs text-slate-400 accordion-icon"></i></button><div id="acc-proj" class="accordion-content"><div class="p-4 bg-slate-50 space-y-4 border-t border-slate-100 shadow-inner"><div><label class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Fläche (m²)</label><input type="number" id="sb-area" class="w-full border border-slate-300 rounded px-2 py-1 text-sm bg-white" oninput="Sidebar.syncProject()"></div><div><label class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Verbrauch</label><input type="number" id="sb-usage" class="w-full border border-slate-300 rounded px-2 py-1 text-sm bg-white" oninput="Sidebar.syncProject()"></div><div class="bg-white p-2 rounded border border-indigo-100 flex justify-between items-center"><span class="text-xs text-indigo-900 font-bold">Heizlast:</span><span class="text-sm font-mono font-bold text-indigo-600" id="sb-load">10.4 kW</span></div></div></div></div>
            <div class="border-b border-slate-100"><button onclick="App.goto(3); Sidebar.toggle('acc-strat', this)" class="accordion-btn w-full p-4 flex items-center justify-between text-slate-700 hover:bg-slate-50 transition"><div class="flex items-center gap-3 font-bold text-sm"><div class="w-6 h-6 rounded bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-sliders"></i></div>Strategie</div><i class="fa-solid fa-chevron-down text-xs text-slate-400 accordion-icon"></i></button><div id="acc-strat" class="accordion-content"><div class="p-4 bg-slate-50 space-y-4 border-t border-slate-100 shadow-inner"><div><div class="flex justify-between text-[10px] uppercase font-bold text-slate-500 mb-1"><span>Marge</span><span id="sb-disp-margin">50%</span></div><input type="range" id="sb-margin" min="0" max="100" oninput="Sidebar.syncStrategy('margin', this.value)"></div><div><div class="flex justify-between text-[10px] uppercase font-bold text-slate-500 mb-1"><span>Install</span><span id="sb-disp-install">30%</span></div><input type="range" id="sb-install" min="0" max="100" oninput="Sidebar.syncStrategy('install', this.value)"></div><div><div class="flex justify-between text-[10px] uppercase font-bold text-slate-500 mb-1"><span>Risk</span><span id="sb-disp-risk">80%</span></div><input type="range" id="sb-risk" min="0" max="100" oninput="Sidebar.syncStrategy('risk', this.value)"></div></div></div></div>
            <div class="border-b border-slate-100"><button onclick="App.goto(4); Sidebar.toggle('acc-fin', this)" class="accordion-btn active w-full p-4 flex items-center justify-between text-slate-700 hover:bg-slate-50 transition"><div class="flex items-center gap-3 font-bold text-sm"><div class="w-6 h-6 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-chart-pie"></i></div>Kalkulation</div><i class="fa-solid fa-chevron-down text-xs text-slate-400 accordion-icon"></i></button><div id="acc-fin" class="accordion-content open"><div class="p-4 bg-slate-50 space-y-6 border-t border-slate-100 shadow-inner"><div><h4 class="text-xs font-bold text-slate-400 uppercase mb-2">Margen-Verteilung</h4><div class="h-40 w-full relative"><canvas id="marginChart"></canvas></div></div><div class="bg-white p-3 rounded border border-slate-200"><h4 class="text-xs font-bold text-slate-700 mb-2">Mischkalkulation</h4><div class="space-y-3"><div><div class="flex justify-between text-xs mb-1"><span>Sichtbar</span><span class="font-bold text-red-500">15%</span></div><input type="range" class="w-full h-1 bg-slate-200 rounded appearance-none cursor-pointer" min="5" max="30" value="15" oninput="Optimizer.updateCalculation()"></div><div><div class="flex justify-between text-xs mb-1"><span>Kleinmat.</span><span class="font-bold text-green-600">45%</span></div><input type="range" class="w-full h-1 bg-slate-200 rounded appearance-none cursor-pointer" min="20" max="60" value="45" oninput="Optimizer.updateCalculation()"></div></div></div></div></div></div>
        </aside>

    </div>

    <!-- MODALS -->
    
    <!-- Price Modal -->
    <div id="price-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 modal-overlay" onclick="Optimizer.closePriceModal()"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fadeIn">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50"><h3 class="font-bold text-slate-800">Preis Kalkulation</h3><button onclick="Optimizer.closePriceModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-times"></i></button></div>
            <div class="p-6 space-y-5">
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Artikel</label><div id="pm-name" class="font-bold text-slate-800 text-lg leading-tight">Name</div></div>
                <div class="grid grid-cols-2 gap-4"><div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Einkauf (EK)</label><div class="relative"><input type="number" id="pm-ek" class="w-full border border-slate-300 rounded p-2 pl-3 font-mono focus:ring-2 focus:ring-indigo-500 outline-none" oninput="Optimizer.calcModal()"><span class="absolute right-3 top-2 text-slate-400 text-xs font-bold">€</span></div></div><div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Marge</label><div class="relative"><input type="number" id="pm-margin" class="w-full border border-slate-300 rounded p-2 pl-3 font-mono focus:ring-2 focus:ring-indigo-500 outline-none" oninput="Optimizer.calcModal()"><span class="absolute right-3 top-2 text-slate-400 text-xs font-bold">%</span></div></div></div>
                <div class="bg-indigo-50 rounded p-4 flex justify-between items-center"><span class="text-sm font-bold text-indigo-900">Verkaufspreis (Netto)</span><span id="pm-vk" class="text-xl font-bold text-indigo-600 font-mono">0.00 €</span></div>
                <div class="pt-4 flex flex-col gap-2"><button onclick="Optimizer.savePrice('local')" class="w-full py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow transition">Nur für dieses Angebot</button><button onclick="Optimizer.savePrice('global')" class="w-full py-3 bg-white text-slate-600 border border-slate-200 rounded-lg font-bold hover:bg-slate-50 transition text-sm">Im Set speichern (Global)</button></div>
            </div>
        </div>
    </div>

    <!-- Planning Modal -->
    <div id="planning-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 modal-overlay" onclick="ResourcePlanner.closePlanningModal()"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate-fadeIn flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-800">Einsatzplanung</h3>
                <button onclick="ResourcePlanner.closePlanningModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-times"></i></button>
            </div>
            <div class="p-6 space-y-6 overflow-y-auto flex-1">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ressource</label>
                    <div id="pl-name" class="font-bold text-slate-800 text-lg">Name</div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Start-Datum</label>
                    <input type="date" id="pl-date" class="w-full border border-slate-300 rounded p-2 text-sm focus:ring-2 focus:ring-purple-500 outline-none">
                </div>
                
                <div class="border-t border-slate-100 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase">Aufgaben-Liste</label>
                        <button onclick="ResourcePlanner.openTemplateModal()" class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded font-bold border border-indigo-100 hover:bg-indigo-100"><i class="fa-solid fa-layer-group mr-1"></i>Aus Vorlagen wählen</button>
                    </div>
                    
                    <div id="task-list-container" class="space-y-2 mb-3">
                        <!-- Dynamic Tasks -->
                    </div>

                    <!-- Custom Task Adder -->
                    <div class="bg-slate-50 p-3 rounded border border-slate-200">
                        <div class="text-[10px] font-bold text-slate-400 uppercase mb-2">Manuelle Aufgabe hinzufügen</div>
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-6"><input type="text" id="new-task-name" placeholder="Bezeichnung" class="w-full border border-slate-300 rounded px-2 py-1 text-xs"></div>
                            <div class="col-span-4"><div class="relative"><input type="number" id="new-task-dur" placeholder="Std" class="w-full border border-slate-300 rounded px-2 py-1 text-xs pr-6"><span class="absolute right-2 top-1 text-[10px] text-slate-400">h</span></div></div>
                            <div class="col-span-2"><button onclick="ResourcePlanner.addCustomTask()" class="w-full bg-slate-200 text-slate-600 rounded px-2 py-1 text-xs font-bold hover:bg-slate-300">+</button></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100 bg-slate-50">
                <button onclick="ResourcePlanner.savePlanning()" class="w-full py-3 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 shadow transition">Speichern & Schließen</button>
            </div>
        </div>
    </div>

    <!-- Template Selection Modal (New) -->
    <div id="template-modal" class="fixed inset-0 z-[60] hidden">
        <div class="absolute inset-0 modal-overlay" onclick="ResourcePlanner.closeTemplateModal()"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fadeIn flex flex-col max-h-[80vh]">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-indigo-50">
                <h3 class="font-bold text-indigo-900">Aufgaben-Vorlagen</h3>
                <button onclick="ResourcePlanner.closeTemplateModal()" class="text-indigo-400 hover:text-indigo-600"><i class="fa-solid fa-times"></i></button>
            </div>
            <div class="p-4 overflow-y-auto flex-1 space-y-2" id="template-list">
                <!-- Templates injected here -->
            </div>
            <div class="p-4 border-t border-slate-100 bg-white">
                <button onclick="ResourcePlanner.addTasksFromTemplate()" class="w-full py-2 bg-indigo-600 text-white rounded font-bold hover:bg-indigo-700">Ausgewählte hinzufügen</button>
            </div>
        </div>
    </div>

    <!-- LOGIC -->
    <script>
        // --- DATA STORE ---
        const DB = {
            products: [
                { id: 'p1', name: 'Vaillant aroTHERM plus', type: 'wp', desc: 'Luft/Wasser-Wärmepumpe Monoblock. R290 Kältemittel.', stats: { margin: 85, install: 90, risk: 95 }, has_upsell: true, omd_img: 'https://placehold.co/100x100?text=Vaillant', bom: [{ name: 'Vaillant aroTHERM plus 75/6', type: 'main', phase: 2, ek: 6000, margin: 15, doc: 'manual.pdf' }, { name: 'Hydraulikstation VWZ', type: 'main', phase: 2, ek: 2000, margin: 15 }, { name: 'UniTOWER Speicher', type: 'main', phase: 1, ek: 3200, margin: 20, doc: 'dim.pdf' }, { name: 'Anschluss-Set Verrohrung', type: 'small', phase: 1, ek: 400, margin: 40 }, { name: 'Montagematerial Klein', type: 'small', phase: 1, ek: 150, margin: 50 }] },
                { id: 'p2', name: 'Daikin Altherma 3 H HT', type: 'wp', desc: 'Split-System für hohe Vorlauftemperaturen. Flüsterleise.', stats: { margin: 70, install: 60, risk: 85 }, has_upsell: true, omd_img: 'https://placehold.co/100x100?text=Daikin', bom: [{ name: 'Daikin Altherma 3 H HT Außen', type: 'main', phase: 2, ek: 6500, margin: 15, doc: 'datasheet.pdf' }, { name: 'Inneneinheit Standgerät', type: 'main', phase: 1, ek: 4500, margin: 20 }, { name: 'Kältemittel-Leitung 10m', type: 'small', phase: 1, ek: 250, margin: 45 }] },
                { id: 'bath-set', name: 'Gäste-WC "Modern"', type: 'san', desc: 'Geberit Element + Villeroy & Boch Keramik', stats: { margin: 60, install: 80, risk: 99 }, has_upsell: false, omd_img: 'https://placehold.co/100x100?text=WC', bom: [{ name: 'Geberit Duofix (Vorwand)', type: 'main', phase: 1, ek: 140, margin: 20, doc: 'montage.pdf' }, { name: 'Schallschutz-Set', type: 'small', phase: 1, ek: 12, margin: 60 }, { name: 'V&B O.Novo Keramik', type: 'main', phase: 2, ek: 80, margin: 30, doc: 'care.pdf' }, { name: 'Grohe Eurosmart Armatur', type: 'main', phase: 2, ek: 65, margin: 30 }] }
            ],
            resources: [
                { id: 'r1', name: 'Meister', role: 'Staff', rate: 65, unit: 'h', icon: 'fa-user-tie' },
                { id: 'r2', name: 'Obermonteur', role: 'Staff', rate: 55, unit: 'h', icon: 'fa-user-gear' },
                { id: 'r3', name: 'Bauhelfer', role: 'Staff', rate: 35, unit: 'h', icon: 'fa-user' },
                { id: 'r4', name: 'Azubi (3. LJ)', role: 'Staff', rate: 25, unit: 'h', icon: 'fa-graduation-cap' },
                { id: 'a1', name: 'Service-Bus', role: 'Asset', rate: 80, unit: 'Tag', icon: 'fa-van-shuttle' },
                { id: 'a2', name: 'Hubsteiger 12m', role: 'Asset', rate: 150, unit: 'Tag', icon: 'fa-truck-pickup' },
                { id: 'a3', name: 'Spezialwerkzeug', role: 'Asset', rate: 40, unit: 'Tag', icon: 'fa-toolbox' }
            ],
            taskTemplates: [
                { id: 't1', name: 'Baustelleneinrichtung', dur: 2, desc: 'Werkzeug bereitstellen, Schutzmaßnahmen' },
                { id: 't2', name: 'Demontage Altgerät', dur: 4, desc: 'Rückbau, Entsorgung vorbereiten' },
                { id: 't3', name: 'Kernbohrung erstellen', dur: 3, desc: 'Durchführung Kernbohrung für Leitungen' },
                { id: 't4', name: 'Leitungsverlegung Kälte', dur: 6, desc: 'Verlegung und Isolierung Kältemittelleitungen' },
                { id: 't5', name: 'Hydraulischer Anschluss', dur: 5, desc: 'Anbindung Heizkreis, Pressen' },
                { id: 't6', name: 'Elektroanschluss', dur: 3, desc: 'Verdrahtung Außeneinheit & Inneneinheit' },
                { id: 't7', name: 'Inbetriebnahme & Test', dur: 2, desc: 'Füllen, Entlüften, Probelauf' },
                { id: 't8', name: 'Einweisung Kunde', dur: 1, desc: 'Erklärung Bedienung, Übergabeprotokoll' }
            ]
        };

        // --- APP STATE ---
        const App = {
            view: 1,
            config: { type: null, load: 10.4 },
            strategy: { margin: 50, install: 30, risk: 80 },

            init: () => {
                Logic.calcLoad();
                Logic.refreshStrategy();
                Optimizer.init();
                Optimizer.renderLib();
                ResourcePlanner.init(); 
                Sidebar.init();
            },

            goto: (step) => {
                const header = document.getElementById('main-header');
                if(step === 4 || step === 5) { header.classList.add('-translate-y-full', 'mb-[-4rem]'); } else { header.classList.remove('-translate-y-full', 'mb-[-4rem]'); }
                document.querySelectorAll('.stage-view').forEach(el => el.classList.remove('active'));
                const target = document.getElementById(`view-${step}`);
                if(target) {
                    target.classList.add('active');
                    App.view = step;
                    for(let i=1; i<=5; i++) {
                        const dot = document.getElementById(`step-${i}`);
                        if(!dot) continue;
                        const circle = dot.querySelector('div');
                        if(i === step) { dot.classList.remove('opacity-50', 'pointer-events-none'); dot.classList.add('text-indigo-600'); circle.classList.add('border-indigo-600', 'text-indigo-600', 'bg-indigo-50'); circle.innerHTML = i; } 
                        else if(i < step) { dot.classList.remove('opacity-50', 'pointer-events-none'); dot.classList.add('text-emerald-500'); circle.classList.add('border-emerald-500', 'bg-emerald-500', 'text-white'); circle.innerHTML = '<i class="fa-solid fa-check"></i>'; } 
                        else { dot.classList.add('opacity-50', 'pointer-events-none'); dot.classList.remove('text-indigo-600', 'text-emerald-500'); circle.classList.remove('border-indigo-600', 'text-indigo-600', 'bg-indigo-50', 'border-emerald-500', 'bg-emerald-500', 'text-white'); circle.innerHTML = i; }
                    }
                    if(step === 5) ResourcePlanner.updateCost(); 
                }
            },
            selectType: (type) => { App.config.type = type; App.goto(2); Sidebar.toggle('acc-proj', document.querySelector('button[onclick*="acc-proj"]')); },
            runAI: () => { App.goto(4); Sidebar.toggle('acc-fin', document.querySelector('button[onclick*="acc-fin"]')); },
            reset: () => { window.location.reload(); }
        };

        // --- BUSINESS LOGIC ---
        const Logic = {
            calcLoad: () => {
                let u = parseFloat(document.getElementById('inp-usage').value);
                let a = parseFloat(document.getElementById('inp-area').value);
                const load = ((u / 250) + (a * 0.05)).toFixed(1);
                App.config.load = load;
                document.getElementById('val-load').innerText = load;
                document.getElementById('sb-load').innerText = load + ' kW';
            },
            updateStrategy: (key, val) => {
                App.strategy[key] = parseInt(val);
                document.getElementById(`disp-${key}`).innerText = val + '%';
                document.getElementById(`sb-${key}`).value = val;
                document.getElementById(`sb-disp-${key}`).innerText = val + '%';
                Logic.refreshStrategy();
                if(ResourcePlanner) ResourcePlanner.updateCost();
            },
            refreshStrategy: () => {
                const list = document.getElementById('strategy-list');
                if(!list) return;
                list.innerHTML = '';
                const scored = DB.products.filter(p => p.type !== 'san').map(p => {
                    const score = ((p.stats.margin * App.strategy.margin) + (p.stats.install * App.strategy.install) + (p.stats.risk * App.strategy.risk)) / (App.strategy.margin + App.strategy.install + App.strategy.risk);
                    return { ...p, score: Math.round(score) };
                }).sort((a,b) => b.score - a.score);
                scored.forEach((p, i) => { list.innerHTML += `<div class="flex items-center gap-4 bg-white p-3 rounded-lg border border-slate-200"><div class="font-bold text-slate-300 w-4">#${i+1}</div><div class="flex-1"><div class="flex justify-between text-xs font-bold text-slate-700 mb-1"><span>${p.name}</span><span>${p.score}% Match</span></div><div class="h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-indigo-600 rounded-full" style="width: ${p.score}%"></div></div></div></div>`; });
            }
        };

        // --- SIDEBAR LOGIC ---
        const Sidebar = {
            init: () => {
                document.getElementById('sb-area').value = document.getElementById('inp-area').value;
                document.getElementById('sb-usage').value = document.getElementById('inp-usage').value;
                document.getElementById('sb-load').innerText = App.config.load + ' kW';
                document.getElementById('sb-margin').value = App.strategy.margin;
                document.getElementById('sb-install').value = App.strategy.install;
                document.getElementById('sb-risk').value = App.strategy.risk;
                document.getElementById('sb-disp-margin').innerText = App.strategy.margin + '%';
                document.getElementById('sb-disp-install').innerText = App.strategy.install + '%';
                document.getElementById('sb-disp-risk').innerText = App.strategy.risk + '%';
            },
            toggle: (id, btn) => {
                const content = document.getElementById(id);
                const isOpen = content.classList.contains('open');
                document.querySelectorAll('.accordion-content').forEach(el => el.classList.remove('open'));
                document.querySelectorAll('.accordion-btn').forEach(el => el.classList.remove('active'));
                if(!isOpen) { content.classList.add('open'); btn.classList.add('active'); }
            },
            syncProject: () => { document.getElementById('inp-area').value = document.getElementById('sb-area').value; document.getElementById('inp-usage').value = document.getElementById('sb-usage').value; Logic.calcLoad(); },
            syncStrategy: (key, val) => { document.getElementById(`disp-${key}`).parentElement.nextElementSibling.value = val; Logic.updateStrategy(key, val); }
        };

        // --- OPTIMIZER MODULE ---
        const Optimizer = {
            currentQuote: [], editingItem: null, chartInstance: null,
            init: () => { Optimizer.updateDashboard(); },
            renderLib: () => {
                const lib = document.getElementById('optimizer-lib');
                lib.innerHTML = '';
                DB.products.forEach(p => {
                    const isRec = p === DB.products[0]; 
                    lib.innerHTML += `<div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm hover:shadow-lg transition-all cursor-pointer group relative overflow-hidden" onclick="Optimizer.addSet('${p.id}')"><div class="flex gap-4 items-start"><img src="${p.omd_img}" class="w-16 h-16 rounded-lg bg-slate-100 object-cover border border-slate-100"><div class="flex-1">${isRec ? '<div class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold w-fit mb-1">Empfehlung</div>' : ''}<h4 class="font-bold text-sm text-slate-800 leading-tight">${p.name}</h4><p class="text-[11px] text-slate-500 mt-1 line-clamp-2">${p.desc}</p></div></div><div class="mt-3 flex items-center justify-between"><div class="flex gap-1"><span class="text-[9px] font-bold bg-red-50 text-red-600 px-1.5 py-0.5 rounded border border-red-100">Phase 1</span><span class="text-[9px] font-bold bg-green-50 text-green-600 px-1.5 py-0.5 rounded border border-green-100">Phase 2</span></div><div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-colors"><i class="fa-solid fa-plus text-xs"></i></div></div></div>`;
                });
            },
            addSet: (id) => {
                const setSource = DB.products.find(p => p.id === id);
                if(!setSource) return;
                const set = JSON.parse(JSON.stringify(setSource));
                set.bom.forEach(i => i.active = true);
                Optimizer.currentQuote.push(set);
                Optimizer.renderQuote();
                Optimizer.updateDashboard();
                if(set.has_upsell) { const box = document.getElementById('ai-suggestion-box'); box.classList.remove('hidden'); box.classList.add('animate-bounce'); setTimeout(() => box.classList.remove('animate-bounce'), 1000); }
            },
            addMaintenanceContract: () => {
                 Optimizer.currentQuote.push({ id: 'service', name: 'Wartungsvertrag "Sorglos"', omd_img: 'https://placehold.co/100x100?text=Service', bom: [{ name: 'Jährliche Inspektion (Abo)', type: 'service', phase: 2, ek: 80, margin: 125, doc: 'contract.pdf', active: true }] });
                document.getElementById('ai-suggestion-box').classList.add('hidden');
                Optimizer.renderQuote();
                Optimizer.updateDashboard();
            },
            renderQuote: () => {
                const list = document.getElementById('quote-list');
                list.innerHTML = '';
                let p1Sum = 0, p2Sum = 0;
                Optimizer.currentQuote.forEach((set, setIndex) => {
                    let setPrice = 0, setEK = 0;
                    let activeCount = 0;
                    set.bom.forEach(i => {
                        if(i.active) {
                            const price = i.ek * (1 + (i.margin / 100));
                            setPrice += price;
                            setEK += i.ek;
                            if(i.phase === 1) p1Sum += price;
                            if(i.phase === 2) p2Sum += price;
                            activeCount++;
                        }
                    });
                    const totalMargin = setPrice > 0 ? ((setPrice - setEK) / setPrice) * 100 : 0;
                    let marginColor = totalMargin > 30 ? 'bg-emerald-500' : (totalMargin < 15 ? 'bg-red-500' : 'bg-amber-500');
                    list.innerHTML += `<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden transition-all hover:shadow-md"><div class="p-5 flex gap-5 relative"><div class="w-1.5 absolute left-0 top-0 bottom-0 ${marginColor}"></div>${set.omd_img ? `<img src="${set.omd_img}" class="w-24 h-24 rounded-lg bg-slate-100 object-cover border border-slate-100">` : ''}<div class="flex-1"><div class="flex justify-between items-start"><div><h3 class="font-bold text-lg text-slate-800">${set.name}</h3><div class="flex gap-2 mt-1.5"><span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full font-medium border border-slate-200">${activeCount} Positionen aktiv</span>${set.id === 'service' ? '<span class="text-xs bg-indigo-100 text-indigo-700 px-2.5 py-0.5 rounded-full font-bold">Abo</span>' : ''}</div></div><div class="text-right"><div class="font-bold text-xl text-slate-900">${setPrice.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})} €</div><div class="flex items-center gap-2 justify-end mt-1"><span class="text-[10px] text-slate-400 font-bold uppercase">Marge</span><div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full ${marginColor}" style="width: ${Math.min(100, totalMargin)}%"></div></div><span class="text-xs font-bold ${totalMargin > 30 ? 'text-emerald-600' : 'text-slate-600'}">${totalMargin.toFixed(0)}%</span></div></div></div><div class="mt-4 flex justify-between items-center border-t border-slate-50 pt-3"><button onclick="Optimizer.toggleDetails(${setIndex})" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition"><i class="fa-solid fa-list-check"></i> Details & Anpassung</button><button onclick="Optimizer.removeSet(${setIndex})" class="text-xs text-slate-300 hover:text-red-500 font-medium transition">Entfernen</button></div></div></div><div id="bom-details-${setIndex}" class="hidden border-t border-slate-100 bg-slate-50/50 p-4"><div class="text-[10px] uppercase font-bold text-slate-400 mb-2 tracking-wider">Stückliste & Konfiguration</div><div class="space-y-2">${set.bom.map((item, itemIdx) => `<div class="flex items-center justify-between bg-white p-2 rounded border border-slate-200 ${!item.active ? 'opacity-50 grayscale' : ''}"><div class="flex items-center gap-3"><input type="checkbox" ${item.active ? 'checked' : ''} onchange="Optimizer.toggleItem(${setIndex}, ${itemIdx})" class="rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer"><div><div class="text-sm font-medium text-slate-700">${item.name}</div><div class="text-[10px] text-slate-400 flex gap-2"><span>${item.phase === 1 ? 'Phase 1 (Rohbau)' : 'Phase 2 (Endmontage)'}</span>${item.doc ? `<span class="text-blue-400"><i class="fa-regular fa-file"></i> Doc</span>` : ''}</div></div></div><div class="flex items-center gap-3"><div class="text-xs font-bold text-slate-600 text-right"><div>${(item.ek * (1 + (item.margin/100))).toFixed(2)} €</div><div class="text-[9px] text-slate-400 font-normal">EK: ${item.ek}</div></div><button onclick="Optimizer.openPriceModal(${setIndex}, ${itemIdx})" class="w-6 h-6 rounded flex items-center justify-center bg-slate-100 hover:bg-indigo-100 text-slate-400 hover:text-indigo-600 transition"><i class="fa-solid fa-pencil text-xs"></i></button></div></div>`).join('')}</div></div></div>`;
                });
                const totalNet = p1Sum + p2Sum;
                document.getElementById('sum-phase-1').innerText = p1Sum.toLocaleString(undefined, {minimumFractionDigits:2}) + ' €';
                document.getElementById('sum-phase-2').innerText = p2Sum.toLocaleString(undefined, {minimumFractionDigits:2}) + ' €';
                document.getElementById('total-net').innerText = totalNet.toLocaleString(undefined, {minimumFractionDigits:2}) + ' €';
                document.getElementById('total-tax').innerText = (totalNet * 0.19).toLocaleString(undefined, {minimumFractionDigits:2}) + ' €';
                document.getElementById('total-gross').innerText = (totalNet * 1.19).toLocaleString(undefined, {minimumFractionDigits:2}) + ' €';
                Optimizer.renderMonteur();
                if(ResourcePlanner) ResourcePlanner.updateCost();
            },
            toggleDetails: (index) => { const el = document.getElementById(`bom-details-${index}`); if(el.classList.contains('hidden')) { el.classList.remove('hidden'); el.classList.add('animate-fadeIn'); } else { el.classList.add('hidden'); } },
            toggleItem: (setIndex, itemIndex) => { const item = Optimizer.currentQuote[setIndex].bom[itemIndex]; item.active = !item.active; Optimizer.renderQuote(); Optimizer.updateDashboard(); },
            openPriceModal: (setIdx, itemIdx) => { const item = Optimizer.currentQuote[setIdx].bom[itemIdx]; Optimizer.editingItem = { setIdx, itemIdx }; document.getElementById('pm-name').innerText = item.name; document.getElementById('pm-ek').value = item.ek; document.getElementById('pm-margin').value = item.margin; Optimizer.calcModal(); document.getElementById('price-modal').classList.remove('hidden'); },
            closePriceModal: () => { document.getElementById('price-modal').classList.add('hidden'); Optimizer.editingItem = null; },
            calcModal: () => { const ek = parseFloat(document.getElementById('pm-ek').value) || 0; const margin = parseFloat(document.getElementById('pm-margin').value) || 0; const vk = ek * (1 + (margin/100)); document.getElementById('pm-vk').innerText = vk.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' €'; },
            savePrice: (mode) => { if(!Optimizer.editingItem) return; const { setIdx, itemIdx } = Optimizer.editingItem; const newEk = parseFloat(document.getElementById('pm-ek').value); const newMargin = parseFloat(document.getElementById('pm-margin').value); Optimizer.currentQuote[setIdx].bom[itemIdx].ek = newEk; Optimizer.currentQuote[setIdx].bom[itemIdx].margin = newMargin; if(mode === 'global') { const setId = Optimizer.currentQuote[setIdx].id; const dbSet = DB.products.find(p => p.id === setId); if(dbSet) { const itemName = Optimizer.currentQuote[setIdx].bom[itemIdx].name; const dbItem = dbSet.bom.find(i => i.name === itemName); if(dbItem) { dbItem.ek = newEk; dbItem.margin = newMargin; Optimizer.renderLib(); } } } Optimizer.renderQuote(); Optimizer.updateDashboard(); Optimizer.closePriceModal(); },
            renderMonteur: () => { const p1C = document.getElementById('monteur-list-p1'); const p2C = document.getElementById('monteur-list-p2'); p1C.innerHTML = ''; p2C.innerHTML = ''; Optimizer.currentQuote.forEach(set => { set.bom.forEach(item => { if(item.type === 'service' || !item.active) return; const html = `<div class="bg-slate-700 p-3 rounded flex justify-between items-center border border-slate-600"><div><div class="text-sm font-bold text-slate-200">${item.name}</div>${item.doc ? `<div class="text-xs text-blue-300 mt-1 flex items-center gap-1">📄 ${item.doc}</div>` : ''}</div><input type="checkbox" class="w-5 h-5 rounded bg-slate-800 border-slate-500 text-blue-500"></div>`; if(item.phase === 1) p1C.innerHTML += html; else p2C.innerHTML += html; }); }); },
            removeSet: (idx) => { Optimizer.currentQuote.splice(idx, 1); Optimizer.renderQuote(); Optimizer.updateDashboard(); },
            updateDashboard: () => { let tEK = 0, tVK = 0; Optimizer.currentQuote.forEach(s => s.bom.forEach(i => { if(i.active) { tEK += i.ek; tVK += i.ek * (1 + (i.margin/100)); } })); const margin = tVK > 0 ? ((tVK - tEK) / tVK * 100) : 0; document.getElementById('global-margin').innerText = margin.toFixed(1) + '%'; const ctx = document.getElementById('marginChart'); if(!ctx) return; if(Optimizer.chartInstance) Optimizer.chartInstance.destroy(); Optimizer.chartInstance = new Chart(ctx, { type: 'doughnut', data: { labels: ['Marge', 'Kosten'], datasets: [{ data: [margin, 100-margin], backgroundColor: ['#10b981', '#e2e8f0'], borderWidth: 0 }] }, options: { cutout: '70%', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } } }); },
            toggleMonteur: () => { document.getElementById('monteur-overlay').classList.toggle('translate-x-full'); },
            updateCalculation: () => { const el = document.getElementById('global-margin'); el.classList.add('text-indigo-600'); setTimeout(() => el.classList.remove('text-indigo-600'), 300); }
        };

        // --- RESOURCE PLANNER MODULE ---
        const ResourcePlanner = {
            plans: { 1: [], 2: [] },
            editingResource: null,

            init: () => {
                const staffC = document.getElementById('resource-pool-staff');
                const assetC = document.getElementById('resource-pool-assets');
                staffC.innerHTML = ''; assetC.innerHTML = '';
                DB.resources.forEach(r => {
                    const el = document.createElement('div');
                    el.className = 'bg-white border border-slate-200 p-2 rounded flex items-center gap-3 cursor-grab draggable-source hover:shadow-md hover:border-purple-300 transition';
                    el.draggable = true;
                    el.ondragstart = (e) => ResourcePlanner.drag(e, r.id);
                    el.innerHTML = `<div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-slate-500"><i class="fa-solid ${r.icon}"></i></div><div class="flex-1"><div class="text-sm font-bold text-slate-700">${r.name}</div><div class="text-xs text-slate-400">${r.rate}€ / ${r.unit}</div></div>`;
                    if(r.role === 'Staff') staffC.appendChild(el); else assetC.appendChild(el);
                });
            },
            drag: (ev, id) => { ev.dataTransfer.setData("text", id); ev.dataTransfer.effectAllowed = "copy"; },
            allowDrop: (ev) => { ev.preventDefault(); ev.currentTarget.classList.add('drag-over'); },
            leave: (ev) => { ev.currentTarget.classList.remove('drag-over'); },
            drop: (ev, phaseId) => {
                ev.preventDefault(); ev.currentTarget.classList.remove('drag-over');
                const id = ev.dataTransfer.getData("text");
                const res = DB.resources.find(r => r.id === id);
                if(res) {
                    const defaultAmount = res.role === 'Staff' ? 8 : 1;
                    // Init with empty tasks array
                    ResourcePlanner.plans[phaseId].push({ ...res, amount: defaultAmount, date: '', tasks: [] });
                    ResourcePlanner.renderPhase(phaseId);
                    ResourcePlanner.updateCost();
                }
            },
            renderPhase: (phaseId) => {
                const container = document.getElementById(`drop-p${phaseId}`);
                container.innerHTML = `<div class="absolute inset-0 flex items-center justify-center text-slate-300 pointer-events-none empty-msg ${ResourcePlanner.plans[phaseId].length > 0 ? 'hidden' : ''}"><span class="text-sm">Ressourcen hier ablegen</span></div>`;
                ResourcePlanner.plans[phaseId].forEach((item, idx) => {
                    // Task Summary Logic
                    const taskCount = item.tasks ? item.tasks.length : 0;
                    const taskSummary = taskCount > 0 
                        ? `<span class="bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded text-[10px] font-bold">${taskCount} Aufgabe(n)</span>` 
                        : '';

                    container.innerHTML += `
                        <div class="bg-white p-3 rounded shadow-sm border border-slate-200 group relative mb-2">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs"><i class="fa-solid ${item.icon}"></i></div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-700 flex items-center gap-2">${item.name} ${taskSummary}</div>
                                        <div class="text-[10px] text-slate-400">Kostet: ${item.rate}€ / ${item.unit}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button onclick="ResourcePlanner.openPlanningModal(${phaseId}, ${idx})" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:text-purple-600 hover:bg-purple-50 transition" title="Termin & Aufgaben"><i class="fa-solid fa-calendar-days text-xs"></i></button>
                                    <button onclick="ResourcePlanner.remove(${phaseId}, ${idx})" class="w-7 h-7 rounded flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition"><i class="fa-solid fa-times text-xs"></i></button>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mt-3 mb-1">
                                <div class="flex items-center border border-slate-200 rounded overflow-hidden w-20">
                                    <input type="number" value="${item.amount}" onchange="ResourcePlanner.updateAmount(${phaseId}, ${idx}, this.value)" class="w-full text-center text-sm p-1 outline-none font-mono font-bold text-slate-700">
                                    <div class="bg-slate-100 text-[10px] text-slate-500 px-1 py-1 border-l border-slate-200 font-bold">${item.unit}</div>
                                </div>
                                ${item.date ? `<div class="flex-1 flex gap-2 text-[10px] bg-slate-50 p-1.5 rounded border border-slate-100 text-slate-600 items-center"><span class="whitespace-nowrap"><i class="fa-regular fa-clock text-slate-400 mr-1"></i>${new Date(item.date).toLocaleDateString('de-DE', {day:'2-digit', month:'2-digit'})}</span></div>` : ''}
                            </div>
                        </div>
                    `;
                });
            },
            updateAmount: (phaseId, idx, val) => { ResourcePlanner.plans[phaseId][idx].amount = parseFloat(val); ResourcePlanner.updateCost(); },
            remove: (phaseId, idx) => { ResourcePlanner.plans[phaseId].splice(idx, 1); ResourcePlanner.renderPhase(phaseId); ResourcePlanner.updateCost(); },
            
            // --- PLANNING MODAL ---
            openPlanningModal: (phaseId, idx) => {
                const item = ResourcePlanner.plans[phaseId][idx];
                ResourcePlanner.editingResource = { phaseId, idx };
                document.getElementById('pl-name').innerText = item.name;
                document.getElementById('pl-date').value = item.date || '';
                ResourcePlanner.renderTaskList(item.tasks || []);
                document.getElementById('planning-modal').classList.remove('hidden');
            },
            closePlanningModal: () => {
                document.getElementById('planning-modal').classList.add('hidden');
                ResourcePlanner.editingResource = null;
            },
            
            // --- TASK LIST RENDERING INSIDE MODAL ---
            renderTaskList: (tasks) => {
                const container = document.getElementById('task-list-container');
                container.innerHTML = '';
                if(!tasks || tasks.length === 0) {
                    container.innerHTML = '<div class="text-xs text-slate-400 italic text-center py-2">Keine Aufgaben zugewiesen.</div>';
                    return;
                }
                tasks.forEach((task, i) => {
                    container.innerHTML += `
                        <div class="flex items-center justify-between bg-slate-50 p-2 rounded border border-slate-200 text-sm">
                            <div>
                                <div class="font-bold text-slate-700">${task.name}</div>
                                <div class="text-[10px] text-slate-500">${task.desc || ''}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="bg-white border border-slate-300 rounded px-1.5 py-0.5 text-xs font-mono">${task.dur}h</span>
                                <button onclick="ResourcePlanner.removeTask(${i})" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    `;
                });
            },

            addCustomTask: () => {
                if(!ResourcePlanner.editingResource) return;
                const name = document.getElementById('new-task-name').value;
                const dur = parseFloat(document.getElementById('new-task-dur').value);
                if(!name || isNaN(dur)) return;

                const { phaseId, idx } = ResourcePlanner.editingResource;
                ResourcePlanner.plans[phaseId][idx].tasks = ResourcePlanner.plans[phaseId][idx].tasks || [];
                ResourcePlanner.plans[phaseId][idx].tasks.push({ name, dur, desc: 'Manuell' });
                
                // Clear Inputs
                document.getElementById('new-task-name').value = '';
                document.getElementById('new-task-dur').value = '';
                
                // Re-render list
                ResourcePlanner.renderTaskList(ResourcePlanner.plans[phaseId][idx].tasks);
            },

            removeTask: (taskIdx) => {
                const { phaseId, idx } = ResourcePlanner.editingResource;
                ResourcePlanner.plans[phaseId][idx].tasks.splice(taskIdx, 1);
                ResourcePlanner.renderTaskList(ResourcePlanner.plans[phaseId][idx].tasks);
            },

            savePlanning: () => {
                if(!ResourcePlanner.editingResource) return;
                const { phaseId, idx } = ResourcePlanner.editingResource;
                
                // Save Date
                ResourcePlanner.plans[phaseId][idx].date = document.getElementById('pl-date').value;
                
                // Auto-calc total hours based on tasks
                const tasks = ResourcePlanner.plans[phaseId][idx].tasks || [];
                if(tasks.length > 0) {
                    const totalDur = tasks.reduce((sum, t) => sum + t.dur, 0);
                    ResourcePlanner.plans[phaseId][idx].amount = totalDur;
                }

                ResourcePlanner.renderPhase(phaseId);
                ResourcePlanner.updateCost();
                ResourcePlanner.closePlanningModal();
            },

            // --- TEMPLATE MODAL LOGIC ---
            openTemplateModal: () => {
                document.getElementById('template-modal').classList.remove('hidden');
                const container = document.getElementById('template-list');
                container.innerHTML = '';
                DB.taskTemplates.forEach(t => {
                    container.innerHTML += `
                        <label class="flex items-start gap-3 p-3 border border-slate-200 rounded hover:bg-slate-50 cursor-pointer transition">
                            <input type="checkbox" value="${t.id}" class="mt-1 rounded text-indigo-600 focus:ring-indigo-500 template-checkbox">
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <span class="font-bold text-sm text-slate-700">${t.name}</span>
                                    <span class="text-xs font-mono bg-slate-100 px-1.5 rounded">${t.dur}h</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">${t.desc}</p>
                            </div>
                        </label>
                    `;
                });
            },

            closeTemplateModal: () => {
                document.getElementById('template-modal').classList.add('hidden');
            },

            addTasksFromTemplate: () => {
                const checkboxes = document.querySelectorAll('.template-checkbox:checked');
                const { phaseId, idx } = ResourcePlanner.editingResource;
                
                checkboxes.forEach(cb => {
                    const t = DB.taskTemplates.find(temp => temp.id === cb.value);
                    if(t) {
                        ResourcePlanner.plans[phaseId][idx].tasks = ResourcePlanner.plans[phaseId][idx].tasks || [];
                        // Push copy
                        ResourcePlanner.plans[phaseId][idx].tasks.push({ name: t.name, dur: t.dur, desc: t.desc });
                    }
                });

                ResourcePlanner.renderTaskList(ResourcePlanner.plans[phaseId][idx].tasks);
                ResourcePlanner.closeTemplateModal();
            },

            updateCost: () => {
                let cost1 = 0; let cost2 = 0;
                ResourcePlanner.plans[1].forEach(i => cost1 += i.rate * i.amount);
                ResourcePlanner.plans[2].forEach(i => cost2 += i.rate * i.amount);
                document.getElementById('cost-p1').innerText = cost1.toFixed(2) + ' €';
                document.getElementById('cost-p2').innerText = cost2.toFixed(2) + ' €';
                document.getElementById('cost-total').innerText = (cost1 + cost2).toFixed(2) + ' €';
                let offerTotal = 0;
                Optimizer.currentQuote.forEach(set => set.bom.forEach(i => { if(i.active) offerTotal += i.ek * (1 + (i.margin/100)); }));
                const strategyPercent = App.strategy.install / 100;
                const budget = offerTotal * strategyPercent; 
                document.getElementById('budget-calc').innerText = budget.toFixed(2) + ' €';
                const profit = budget - (cost1 + cost2);
                document.getElementById('profit-val').innerText = profit.toFixed(2) + ' €';
                document.getElementById('profit-val').className = profit >= 0 ? 'font-bold text-emerald-500' : 'font-bold text-red-500';
                const bar = document.getElementById('profit-bar');
                if(budget > 0) {
                    const percent = Math.max(0, Math.min(100, ((budget - (cost1 + cost2)) / budget) * 100));
                    bar.style.width = percent + '%';
                    bar.className = profit >= 0 ? 'bg-emerald-500 h-2 rounded-full transition-all duration-500' : 'bg-red-500 h-2 rounded-full transition-all duration-500';
                }
            }
        };

        // --- INIT ---
        window.addEventListener('DOMContentLoaded', App.init);

    </script>
</body>
</html>