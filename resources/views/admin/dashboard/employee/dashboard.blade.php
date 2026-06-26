<!-- PAGE CONTENT (DASHBOARD) -->
        <div class="flex-1 overflow-auto p-4 md:p-8 relative">

            <div class="max-w-6xl mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4">
                    <div>
                        <h1
                            class="text-2xl md:text-3xl font-heading font-black text-gray-900 dark:text-white tracking-tight">
                            Übersicht Dashboard</h1>
                        <p class="text-sm md:text-base text-gray-500 dark:text-slate-400 mt-1">Willkommen zurück,
                            Torsten. Hier ist der aktuelle Status.</p>
                    </div>

                    <!-- Dashboard Tabs -->
                    <div
                        class="flex bg-gray-100 dark:bg-slate-800 p-1 rounded-xl shadow-inner overflow-x-auto w-full md:w-auto scrollbar-hide">
                        <button onclick="switchDashboardTab('dash-personal')"
                            class="dash-tab-btn flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-bold bg-white dark:bg-slate-700 text-brand-green shadow-sm transition whitespace-nowrap"
                            data-target="dash-personal">
                            Persönlich
                        </button>
                        <button onclick="switchDashboardTab('dash-department')"
                            class="dash-tab-btn flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-semibold text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-white transition whitespace-nowrap"
                            data-target="dash-department">
                            Abteilung
                        </button>
                        <button onclick="switchDashboardTab('dash-custom')"
                            class="dash-tab-btn flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-semibold text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-white transition whitespace-nowrap"
                            data-target="dash-custom">
                            Benutzerdefiniert
                        </button>
                    </div>
                </div>

                <!-- TAB CONTENT 1: PERSÖNLICH -->
                <div id="dash-personal" class="dash-tab-content block animate-[fadeIn_0.3s_ease-out]">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-8">
                        <div
                            class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-soft">
                            <div
                                class="w-12 h-12 bg-blue-50 dark:bg-brand-blue/20 text-brand-blue rounded-xl flex items-center justify-center mb-4">
                                <i data-lucide="user-check" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">14</h3>
                            <p
                                class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wide font-semibold mt-1">
                                Meine aktiven Leads</p>
                        </div>
                        <div
                            class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-soft">
                            <div
                                class="w-12 h-12 bg-orange-50 dark:bg-orange-500/20 text-orange-500 rounded-xl flex items-center justify-center mb-4">
                                <i data-lucide="check-square" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">5</h3>
                            <p
                                class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wide font-semibold mt-1">
                                Offene To-Do's Heute</p>
                        </div>
                        <div
                            class="bg-gradient-brand p-6 rounded-2xl border border-transparent shadow-soft text-white flex flex-col justify-between sm:col-span-2 md:col-span-1">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <i data-lucide="calendar-clock" class="w-6 h-6 text-white/80"></i>
                                    <span class="text-xs font-bold bg-white/20 px-2 py-1 rounded-md">In 45 Min</span>
                                </div>
                                <h3 class="text-lg font-bold">Kundenberatung Vor-Ort</h3>
                                <p class="text-sm text-white/80 mt-1">Familie Schmidt, Musterstraße 12</p>
                            </div>
                            <button
                                class="mt-4 bg-white text-brand-green font-bold text-xs py-2 px-4 rounded-lg hover:bg-gray-50 transition w-max">
                                Termin ansehen
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TAB CONTENT 2: ABTEILUNG -->
                <div id="dash-department" class="dash-tab-content hidden animate-[fadeIn_0.3s_ease-out]">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
                        <div
                            class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-soft">
                            <div
                                class="w-10 h-10 bg-brand-lightgreen/30 dark:bg-brand-green/20 text-brand-green rounded-xl flex items-center justify-center mb-3">
                                <i data-lucide="briefcase" class="w-5 h-5"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">42</h3>
                            <p
                                class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wide font-semibold mt-1">
                                Aktive Montagen</p>
                        </div>
                        <div
                            class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-soft">
                            <div
                                class="w-10 h-10 bg-red-50 dark:bg-red-500/20 text-red-500 rounded-xl flex items-center justify-center mb-3">
                                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">3</h3>
                            <p
                                class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wide font-semibold mt-1">
                                Kritische Tickets</p>
                        </div>
                        <div
                            class="sm:col-span-2 md:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-soft flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">89%</h3>
                                <p
                                    class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wide font-semibold mt-1">
                                    Teameffizienz (Woche)</p>
                            </div>
                            <div
                                class="w-24 md:w-32 h-16 bg-gray-50 dark:bg-slate-700 rounded-lg flex items-center justify-center border border-gray-100 dark:border-slate-600">
                                <span class="text-[10px] text-gray-400 dark:text-slate-300 font-medium">Chart
                                    Platzhalter</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB CONTENT 3: BENUTZERDEFINIERT -->
                <div id="dash-custom" class="dash-tab-content hidden animate-[fadeIn_0.3s_ease-out]">
                    <div
                        class="bg-white dark:bg-slate-800 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-2xl p-8 md:p-12 flex flex-col items-center justify-center text-center">
                        <div
                            class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center mb-4 text-gray-400 dark:text-slate-300">
                            <i data-lucide="layout-grid" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Passe dein Dashboard an</h3>
                        <p class="text-gray-500 dark:text-slate-400 max-w-md mb-6 text-sm">Füge Widgets hinzu,
                            verschiebe sie und erstelle die perfekte Übersicht für deinen täglichen Workflow.</p>
                        <button
                            class="bg-brand-slate text-white px-5 py-2.5 rounded-xl font-bold shadow-md hover:bg-gray-800 transition flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i> Widget hinzufügen
                        </button>
                    </div>
                </div>

                <!-- Shared bottom section for visual balance -->
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 mt-8">Zuletzt bearbeitet</h2>
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-soft overflow-hidden overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[500px]">
                        <thead>
                            <tr
                                class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-100 dark:border-slate-700 text-xs uppercase tracking-wider text-gray-500 dark:text-slate-300 font-semibold">
                                <th class="p-4 font-semibold">Titel / Name</th>
                                <th class="p-4 font-semibold">Typ</th>
                                <th class="p-4 font-semibold">Datum</th>
                                <th class="p-4 font-semibold text-right">Aktion</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition text-gray-900 dark:text-slate-200">
                                <td class="p-4 font-medium">Angebot WP-2024-08</td>
                                <td class="p-4"><span
                                        class="px-2 py-1 bg-blue-50 dark:bg-brand-blue/20 text-brand-blue rounded-md text-[10px] font-bold">Angebot</span>
                                </td>
                                <td class="p-4 text-gray-500 dark:text-slate-400 text-xs">Heute, 09:15</td>
                                <td class="p-4 text-right"><button
                                        class="text-brand-green hover:underline font-medium text-xs">Öffnen</button>
                                </td>
                            </tr>
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition text-gray-900 dark:text-slate-200">
                                <td class="p-4 font-medium">Kundenakte Meyer</td>
                                <td class="p-4"><span
                                        class="px-2 py-1 bg-green-50 dark:bg-brand-green/20 text-brand-green rounded-md text-[10px] font-bold">Kunde</span>
                                </td>
                                <td class="p-4 text-gray-500 dark:text-slate-400 text-xs">Gestern, 14:30</td>
                                <td class="p-4 text-right"><button
                                        class="text-brand-green hover:underline font-medium text-xs">Öffnen</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>