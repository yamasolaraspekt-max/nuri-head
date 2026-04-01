<!doctype html>
<html lang="de" class="h-full">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Zeiterfassung · Blade + Tailwind + Alpine</title>
  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <!-- Alpine.js -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>:root{ --ci-primary:#74b91f; }</style>
</head>
<body class="h-full" x-data="app()" x-bind:class="dark ? 'dark' : ''" x-init="init()">
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100">

    <!-- Header -->
    <header class="bg-white/70 dark:bg-gray-950/70 backdrop-blur border-b border-gray-200 dark:border-gray-800 sticky top-0 z-20">
      <div class="flex items-center justify-between px-4 pt-3 pb-2">
        <div class="flex items-center gap-2">
          <div class="h-8 w-8 rounded-xl flex items-center justify-center font-semibold" style="background:var(--ci-primary);color:#0b0b0b">Z</div>
          <div class="leading-tight">
            <div class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Zeiterfassung</div>
            <div class="text-sm font-semibold">Heute</div>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span x-html="statusChip()"></span>
          <button @click="cycleTheme()" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100" title="Hell/Dunkel/Auto umschalten">◐</button>
        </div>
      </div>
      <!-- OrgBar -->
      <div class="px-4 pb-2 flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
          <i data-lucide="building-2" class="opacity-70 w-4 h-4"></i>
          <select x-model="org" class="text-xs bg-transparent border border-gray-300 dark:border-gray-700 rounded-md px-2 py-1">
            <template x-for="o in ORGS" :key="o"><option x-text="o"></option></template>
          </select>
        </div>
        <div class="flex items-center gap-2">
          <i data-lucide="key-round" class="opacity-70 w-4 h-4"></i>
          <select x-model="role" class="text-xs bg-transparent border border-gray-300 dark:border-gray-700 rounded-md px-2 py-1">
            <template x-for="r in ROLES" :key="r"><option x-text="r"></option></template>
          </select>
          <div x-show="ENV==='STAGING'" class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-200 text-amber-900 border border-amber-300">STAGING</div>
        </div>
      </div>
      <div class="px-4 pb-3">
        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
          <i data-lucide="shield-check" class="w-4 h-4"></i> Datenschutz: Standort nur während Dienst · Audit-Log aktiv
        </div>
      </div>
    </header>

    <!-- Anomalies Banner -->
    <div class="mx-auto w-full max-w-screen-xl px-4 lg:px-6 my-3" x-show="flags.anomalies && anomalies.length" x-cloak>
      <div class="rounded-xl border border-amber-300 bg-amber-50 text-amber-900 dark:bg-amber-900/30 dark:border-amber-800 dark:text-amber-200 p-3 text-sm flex items-start gap-2">
        <i data-lucide="alert-triangle" class="w-4 h-4 mt-0.5"></i>
        <div>
          <div class="font-semibold mb-1">Auffälligkeiten</div>
          <ul class="list-disc pl-4 space-y-0.5">
            <template x-for="(a,i) in anomalies" :key="i"><li x-text="a"></li></template>
          </ul>
        </div>
      </div>
    </div>

    <!-- Shell -->
    <div class="mx-auto w-full max-w-screen-xl px-0 lg:px-6 py-4 grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-4">
      <!-- Sidebar (desktop) -->
      <aside class="hidden lg:flex lg:flex-col lg:gap-1 lg:py-4 lg:pr-2">
        <template x-for="item in navItems()" :key="item.key">
          <button @click="showTab(item.key)" :class="tab===item.key ? 'bg-gray-100 dark:bg-gray-900 border-gray-300 dark:border-gray-700' : 'border-gray-200 dark:border-gray-800'" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm border">
            <i :data-lucide="item.icon" class="w-4 h-4"></i> <span x-text="item.label"></span>
          </button>
        </template>
      </aside>

      <!-- Main -->
      <main class="w-full">
        <!-- DASHBOARD -->
        <section x-show="tab==='Dashboard'">
          <div class="grid grid-cols-2 md:grid-cols-3 gap-3 p-4">
            <button @click="onPrimaryAction()" class="col-span-2 md:col-span-3 h-14 rounded-2xl font-semibold flex items-center justify-center gap-2" style="background:var(--ci-primary);color:#0b0b0b">
              <i :data-lucide="primaryAction().icon" class="w-4 h-4"></i>
              <span x-text="primaryAction().label"></span>
            </button>
            <button @click="onSecondary('pause')" class="h-12 rounded-2xl bg-gray-100 dark:bg-gray-900 text-sm font-medium flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-800">
              <i data-lucide="coffee" class="w-4 h-4"></i> <span x-text="status==='Pause' ? 'Arbeit fortsetzen' : 'Pause starten'"></span>
            </button>
            <button @click="onSecondary('work')" class="h-12 rounded-2xl bg-gray-100 dark:bg-gray-900 text-sm font-medium flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-800">
              <i data-lucide="play" class="w-4 h-4"></i> Arbeit starten
            </button>
            <button @click="onSecondary('drive')" class="h-12 rounded-2xl bg-gray-100 dark:bg-gray-900 text-sm font-medium flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-800">
              <i data-lucide="car" class="w-4 h-4"></i> Fahrt starten
            </button>
            <button @click="endShift()" class="col-span-2 md:col-span-3 h-12 rounded-2xl bg-gray-100 dark:bg-gray-900 text-sm font-medium flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-800">
              <i data-lucide="log-out" class="w-4 h-4"></i> Schicht beenden
            </button>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 px-0 lg:px-0">
            <div class="mx-4 lg:mx-0 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="pt-4 px-4 pb-4">
                <div class="flex items-center justify-between mb-2">
                  <div class="font-semibold">Tagesjournal</div>
                  <span class="text-[10px] border rounded px-2 py-0.5">Noch nicht bestätigt</span>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-800">
                  <template x-for="row in journalRows" :key="row.time">
                    <div class="grid grid-cols-[44px_1fr_auto] items-center gap-2 py-2">
                      <div class="text-xs text-gray-500 dark:text-gray-400" x-text="row.time"></div>
                      <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full" :class="{'bg-emerald-500':row.tone==='good','bg-amber-500':row.tone==='warn','bg-gray-400':!row.tone}"></div>
                        <div class="text-sm" x-text="row.label"></div>
                      </div>
                      <div class="text-xs text-gray-500 flex items-center gap-1">
                        <i :data-lucide="row.icon" class="w-4 h-4 opacity-60"></i>
                      </div>
                    </div>
                  </template>
                </div>
                <div class="mt-3 text-xs text-gray-500 flex items-center gap-2">
                  <i data-lucide="clock" class="w-4 h-4"></i>
                  Aktive Arbeitszeit heute: <span class="font-semibold text-gray-900 dark:text-gray-100">6 h 05 min</span>
                </div>
              </div>
            </div>
            <div>
              <div class="mx-4 lg:mx-0 mb-4 overflow-hidden bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="h-40 md:h-56 relative">
                  <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(116,185,31,0.25),transparent_40%),radial-gradient(circle_at_80%_30%,rgba(15,29,47,0.4),transparent_35%)]"></div>
                  <div class="absolute inset-0 grid grid-cols-8 grid-rows-6 opacity-20">
                    <template x-for="i in 48" :key="i"><div class="border border-white/10"></div></template>
                  </div>
                  <div class="absolute top-2 left-2 flex items-center gap-2">
                    <span class="bg-white/90 text-gray-900 rounded px-2 py-0.5 text-xs">GPS aktiv</span>
                    <span class="bg-white/90 text-gray-900 rounded px-2 py-0.5 text-xs border flex items-center gap-1"><i data-lucide="navigation" class="w-3 h-3"></i> 2 m</span>
                    <template x-if="flags.etaOnMap && status==='Fahrt'"><span class="bg-white/90 text-gray-900 rounded px-2 py-0.5 text-xs">ETA <span x-text="etaMinutes"></span> min</span></template>
                  </div>
                  <div class="absolute bottom-2 left-2 bg-white/90 dark:bg-gray-900/90 text-xs rounded-xl px-2 py-1 flex items-center gap-1">
                    <i data-lucide="map-pin" class="w-3 h-3"></i> Müller GmbH, Hauptstr. 12
                  </div>
                </div>
              </div>
              <div class="mx-4 lg:mx-0 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="p-4 flex items-center justify-between">
                  <div class="text-sm">
                    <div class="font-semibold">Sync-Status</div>
                    <div class="text-xs text-gray-500">2 Einträge noch ausstehend</div>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="rounded px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-800">WLAN</span>
                    <span class="rounded px-2 py-0.5 text-xs border flex items-center gap-1"><i data-lucide="wifi-off" class="w-3 h-3"></i> Offline</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- JOURNAL -->
        <section x-show="tab==='Journal'" x-cloak>
          <div class="px-4 lg:px-0 pb-24 lg:pb-8">
            <div class="flex items-center justify-between py-3">
              <div class="font-semibold">Heute · Mi</div>
              <span class="text-[10px] border rounded px-2 py-0.5">Bestätigung ausstehend</span>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="pt-4 px-4 pb-4" id="journalList">
                <div class="space-y-2">
                  <template x-for="row in journalRowsDetail" :key="row.t">
                    <div class="grid grid-cols-[1fr_auto_auto] items-center gap-2 border-b last:border-b-0 border-gray-200 dark:border-gray-800 py-2">
                      <div>
                        <div class="text-sm font-medium" x-text="row.l"></div>
                        <div class="text-xs text-gray-500" x-text="row.t"></div>
                      </div>
                      <div class="text-xs text-gray-600 dark:text-gray-300" x-text="row.d"></div>
                      <button class="text-xs px-2 py-1">Bearbeiten</button>
                    </div>
                  </template>
                </div>
                <div class="mt-3 text-xs text-gray-500 flex items-center gap-2">
                  <i data-lucide="clock" class="w-4 h-4"></i>
                  Gesamt: <span class="font-semibold text-gray-900 dark:text-gray-100">6 h 50 min</span>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- REPORT -->
        <section x-show="tab==='Tagesbericht'" x-cloak>
          <div class="px-4 lg:px-0 pb-24 lg:pb-8">
            <div class="flex items-center justify-between py-3">
              <div class="font-semibold">Tagesbericht</div>
              <span class="text-[10px] border rounded px-2 py-0.5" :class="mandatoryOk() ? 'border-emerald-400 text-emerald-600' : ''" x-text="mandatoryOk() ? 'Pflicht erfüllt' : 'Pflichten offen'"></span>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="pt-4 px-4 pb-4">
                <div class="font-semibold mb-2">Aufgaben heute</div>
                <div class="space-y-2">
                  <template x-for="t in dailyTasks" :key="t.id">
                    <label class="flex items-center justify-between p-2 border rounded-xl border-gray-200 dark:border-gray-800">
                      <div class="flex items-center gap-2">
                        <input type="checkbox" class="accent-[var(--ci-primary)]" :checked="t.done" @change="t.done = !t.done">
                        <span class="text-sm" x-html="t.label + (t.required ? '<span class=\'ml-2 text-[10px] px-1 py-0.5 rounded bg-amber-100 text-amber-900\'>Pflicht</span>' : '')"></span>
                      </div>
                      <span class="text-xs" :class="t.done ? 'text-emerald-600' : 'text-gray-500'" x-text="t.done ? 'erledigt' : 'offen'"></span>
                    </label>
                  </template>
                </div>

                <!-- Belege Upload -->
                <div class="mt-6">
                  <div class="font-semibold mb-2">Belege</div>
                  <div class="flex flex-wrap gap-2 mb-2">
                    <button class="rounded-xl text-xs px-3 py-2 border border-gray-200 dark:border-gray-800 bg-gray-100 dark:bg-gray-900 flex items-center gap-2" @click="openUpload({scope:'daily',kind:'photo'})"><i data-lucide="camera" class="w-4 h-4"></i> Foto</button>
                    <button class="rounded-xl text-xs px-3 py-2 border border-gray-200 dark:border-gray-800 bg-gray-100 dark:bg-gray-900 flex items-center gap-2" @click="openUpload({scope:'daily',kind:'document'})"><i data-lucide="upload" class="w-4 h-4"></i> Dokument</button>
                    <button class="rounded-xl text-xs px-3 py-2 border border-gray-200 dark:border-gray-800 bg-gray-100 dark:bg-gray-900 flex items-center gap-2" @click="openUpload({scope:'daily',kind:'audio'})"><i data-lucide="mic" class="w-4 h-4"></i> Audio</button>
                  </div>
                  <div class="space-y-2">
                    <template x-if="dailyAttachments.length===0"><div class="text-xs text-gray-500">Noch keine Belege hochgeladen.</div></template>
                    <template x-for="att in dailyAttachments" :key="att.id">
                      <div class="flex items-center justify-between text-xs border rounded-xl border-gray-200 dark:border-gray-800 px-3 py-2">
                        <div class="flex items-center gap-2"><i data-lucide="paperclip" class="w-4 h-4"></i> <span x-text="att.name"></span> <span class="opacity-60" x-text="'(' + Math.round(att.size/1024) + ' KB)'"></span> <span class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-800" x-text="att.kind"></span></div>
                        <button class="text-xs px-2 py-1" @click="removeAttachment('daily', att.id)">Entfernen</button>
                      </div>
                    </template>
                  </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                  <div class="text-xs text-gray-500">Erledigt: <span x-text="dailyTasks.filter(t=>t.done).length + '/' + dailyTasks.length"></span></div>
                  <button class="rounded-xl px-3 py-2 text-sm" :disabled="policies.blockOnMandatoryTasks && !mandatoryOk()" style="background:var(--ci-primary);color:#0b0b0b" @click="confirmReport()">Tagesbericht bestätigen</button>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- EINSATZ -->
        <section x-show="tab==='Einsatz'" x-cloak>
          <div class="px-4 lg:px-0 pb-24 lg:pb-8">
            <div class="py-3 flex items-center justify-between">
              <div class="font-semibold">Aktueller Einsatz</div>
              <span class="text-[10px] border rounded px-2 py-0.5 bg-[rgba(116,185,31,0.15)] text-[rgba(116,185,31,1)]">Auftrag #A-2025-118</span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
              <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="pt-4 px-4 pb-4 space-y-2">
                  <div class="flex items-start justify-between">
                    <div>
                      <div class="font-semibold" x-text="appointments[0]?.customer || '—'"></div>
                      <div class="text-xs text-gray-500" x-text="appointments[0]?.address || ''"></div>
                    </div>
                    <span class="text-[10px] border rounded px-2 py-0.5 flex items-center gap-1"><i data-lucide="shield-check" class="w-3 h-3"></i> Geofence aktiv</span>
                  </div>
                  <div class="grid grid-cols-3 gap-2">
                    <button class="h-12 rounded-xl bg-gray-100 dark:bg-gray-900 text-sm font-medium flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-800" @click="openUpload({scope:'appointment',kind:'photo',apptId: appointments[0]?.id})"><i data-lucide="camera" class="w-4 h-4"></i> Foto</button>
                    <button class="h-12 rounded-xl bg-gray-100 dark:bg-gray-900 text-sm font-medium flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-800" @click="openUpload({scope:'appointment',kind:'document',apptId: appointments[0]?.id})"><i data-lucide="upload" class="w-4 h-4"></i> Dokument</button>
                    <button class="h-12 rounded-xl bg-gray-100 dark:bg-gray-900 text-sm font-medium flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-800" @click="openUpload({scope:'appointment',kind:'audio',apptId: appointments[0]?.id})"><i data-lucide="mic" class="w-4 h-4"></i> Sprachnotiz</button>
                    <button class="h-12 rounded-xl bg-gray-100 dark:bg-gray-900 text-sm font-medium flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-800" @click="openUpload({scope:'appointment',kind:'audio',apptId: appointments[0]?.id})"><i data-lucide="file" class="w-4 h-4"></i> Bricht</button>
                  </div>
                  <div class="text-xs text-gray-500">Alle Uploads werden offline gepuffert und bei Netz synchronisiert.</div>
                  <div class="flex items-center justify-between pt-1">
                    <button class="rounded-xl text-sm flex items-center gap-2 px-3 py-2"><i data-lucide="file-signature" class="w-4 h-4"></i> Kundensignatur</button>
                    <button class="rounded-xl text-sm px-3 py-2" style="background:var(--ci-primary);color:#0b0b0b">Arbeitsbericht</button>
                  </div>
                </div>
              </div>

              <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="pt-4 px-4 pb-4">
                  <div class="font-semibold mb-2">Einsatzplan (heute)</div>
                  <div class="space-y-2">
                    <template x-for="a in appointments" :key="a.id">
                      <div class="border border-gray-200 dark:border-gray-800 rounded-xl p-3">
                        <div class="flex items-center justify-between">
                          <div>
                            <div class="text-sm font-medium" x-text="a.customer"></div>
                            <div class="text-[11px] text-gray-500" x-text="a.address"></div>
                          </div>
                          <span class="text-[10px] border rounded px-2 py-0.5" x-text="a.time"></span>
                        </div>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                          <button class="rounded-xl text-xs flex items-center justify-center gap-1 px-3 py-2 bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-800" @click="markDepart(a.id)"><i data-lucide="car" class="w-3 h-3"></i> Abfahrt markieren</button>
                          <button class="rounded-xl text-xs flex items-center justify-center gap-1 px-3 py-2" style="background:var(--ci-primary);color:#0b0b0b" @click="markArrive(a.id)"><i data-lucide="map-pin" class="w-3 h-3"></i> Ankunft markieren</button>
                        </div>
                        <template x-if="a.departedAt && !a.arrivedAt">
                          <div class="mt-2 text-[11px] text-gray-500 flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> Unterwegs seit <span x-text="formatTime(a.departedAt)"></span></div>
                        </template>
                        <template x-if="a.departedAt && a.arrivedAt">
                          <div class="mt-2 text-[11px] text-gray-500 flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> Fahrzeit: <span x-text="computeMinutes(a.departedAt, a.arrivedAt) + ' min'"></span></div>
                        </template>

                        <!-- Attachments per appointment -->
                        <div class="mt-3">
                          <div class="text-xs font-medium mb-1 flex items-center gap-2">
                            <i data-lucide="paperclip" class="w-3 h-3"></i> Belege
                            <span class="text-[10px] border rounded px-1 py-0.5" x-text="(attachmentsByAppointment[a.id]||[]).length"></span>
                          </div>
                          <div class="flex flex-wrap gap-2 mb-2">
                            <button class="rounded-xl text-xs px-3 py-2 border border-gray-200 dark:border-gray-800 bg-gray-100 dark:bg-gray-900 flex items-center gap-2" @click="openUpload({scope:'appointment',kind:'photo',apptId:a.id})"><i data-lucide="camera" class="w-4 h-4"></i> Foto</button>
                            <button class="rounded-xl text-xs px-3 py-2 border border-gray-200 dark:border-gray-800 bg-gray-100 dark:bg-gray-900 flex items-center gap-2" @click="openUpload({scope:'appointment',kind:'document',apptId:a.id})"><i data-lucide="upload" class="w-4 h-4"></i> Dokument</button>
                            <button class="rounded-xl text-xs px-3 py-2 border border-gray-200 dark:border-gray-800 bg-gray-100 dark:bg-gray-900 flex items-center gap-2" @click="openUpload({scope:'appointment',kind:'audio',apptId:a.id})"><i data-lucide="mic" class="w-4 h-4"></i> Audio</button>
                          </div>
                          <div class="space-y-2">
                            <template x-for="att in (attachmentsByAppointment[a.id]||[])" :key="att.id">
                              <div class="flex items-center justify-between text-xs border rounded-xl border-gray-200 dark:border-gray-800 px-3 py-2">
                                <div class="flex items-center gap-2"><i data-lucide="paperclip" class="w-4 h-4"></i> <span x-text="att.name"></span> <span class="opacity-60" x-text="'(' + Math.round(att.size/1024) + ' KB)'"></span> <span class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-800" x-text="att.kind"></span></div>
                                <button class="text-xs px-2 py-1" @click="removeAttachment('appointment', att.id, a.id)">Entfernen</button>
                              </div>
                            </template>
                          </div>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="pt-4 px-4 pb-4">
                <div class="font-semibold mb-2">Checkliste</div>
                <ul class="text-sm space-y-2">
                  <li class="flex items-center gap-2"><input type="checkbox" class="accent-[var(--ci-primary)]" checked> Zählerstand dokumentiert</li>
                  <li class="flex items-center gap-2"><input type="checkbox" class="accent-[var(--ci-primary)]"> Materialverbrauch erfasst</li>
                  <li class="flex items-center gap-2"><input type="checkbox" class="accent-[var(--ci-primary)]"> Kunde informiert</li>
                </ul>
              </div>
            </div>
          </div>
        </section>

        <!-- DISPO -->
        <section x-show="tab==='Dispo'" x-cloak>
          <div class="px-4 lg:px-0 pb-24 lg:pb-8">
            <div class="py-3 flex items-center justify-between">
              <div class="font-semibold">Disposition · Live</div>
              <span class="text-[10px] border rounded px-2 py-0.5">Nur während Dienst sichtbar</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
              <template x-for="m in team" :key="m.name">
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                  <div class="py-3 px-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                      <div class="h-9 w-9 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center"><i data-lucide="user" class="w-4 h-4"></i></div>
                      <div>
                        <div class="text-sm font-medium" x-text="m.name"></div>
                        <div class="text-xs text-gray-500" x-text="m.note"></div>
                      </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium" x-html="statusChip(m.status)"></span>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </section>

        <!-- ADMIN -->
        <section x-show="tab==='Admin'" x-cloak>
          <div class="px-4 lg:px-0 pb-24 lg:pb-8">
            <div class="py-3 flex items-center justify-between">
              <div class="font-semibold flex items-center gap-2"><i data-lucide="settings" class="w-4 h-4"></i> Admin-Konsole</div>
              <span class="text-[10px] border rounded px-2 py-0.5" x-text="org"></span>
            </div>

            <div class="flex gap-2 overflow-x-auto pb-2">
              <template x-for="t in adminTabs" :key="t.t">
                <button @click="adminTab=t.t" class="px-3 py-1.5 rounded-xl text-xs border flex items-center gap-1" :class="adminTab===t.t ? 'bg-gray-100 dark:bg-gray-900 border-gray-300 dark:border-gray-700' : 'border-gray-200 dark:border-gray-800'">
                  <i :data-lucide="t.icon" class="w-4 h-4"></i> <span x-text="t.t"></span>
                </button>
              </template>
            </div>

            <div x-show="adminTab==='Organisation'" class="mb-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="pt-4 px-4 pb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                  <div class="p-3 border rounded-xl border-gray-200 dark:border-gray-800">
                    <div class="font-medium mb-2">Feature Flags</div>
                    <template x-for="(v,k) in flags" :key="k">
                      <label class="flex items-center justify-between py-1">
                        <span class="text-xs text-gray-600 dark:text-gray-300" x-text="k"></span>
                        <input type="checkbox" class="accent-[var(--ci-primary)]" :checked="v" @change="flags[k]=!flags[k]">
                      </label>
                    </template>
                  </div>
                  <div class="p-3 border rounded-xl border-gray-200 dark:border-gray-800">
                    <div class="font-medium mb-2">Datenresidenz</div>
                    <div class="flex items-center gap-2 mb-2">
                      <span class="text-xs w-24">Region</span>
                      <select class="text-xs bg-transparent border border-gray-300 dark:border-gray-700 rounded-md px-2 py-1" x-model="dataResidency.region">
                        <option value="EU">EU</option>
                        <option value="DE">Deutschland (DE)</option>
                      </select>
                    </div>
                    <label class="flex items-center justify-between py-1">
                      <span class="text-xs">BYOK/Kundenschlüssel</span>
                      <input type="checkbox" class="accent-[var(--ci-primary)]" x-model="dataResidency.byok">
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div x-show="adminTab==='Regelwerk'" class="mb-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="pt-4 px-4 pb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                  <div class="p-3 border rounded-xl border-gray-200 dark:border-gray-800 space-y-2">
                    <label class="flex items-center justify-between"><span class="text-xs">Startdaten verpflichtend</span><input type="checkbox" class="accent-[var(--ci-primary)]" x-model="policies.requireStartData"></label>
                    <label class="flex items-center justify-between"><span class="text-xs">Tracking nur während Dienst</span><input type="checkbox" class="accent-[var(--ci-primary)]" x-model="policies.trackOnlyOnDuty"></label>
                    <div class="flex items-center justify-between"><span class="text-xs">Mindestpause (min)</span><input type="number" class="w-24 text-xs rounded-md border border-gray-300 dark:border-gray-700 bg-transparent px-2 py-1" x-model.number="policies.pauseMinMinutes"></div>
                    <label class="flex items-center justify-between"><span class="text-xs">Pflichtaufgaben blocken</span><input type="checkbox" class="accent-[var(--ci-primary)]" x-model="policies.blockOnMandatoryTasks"></label>
                  </div>
                  <div class="p-3 border rounded-xl border-gray-200 dark:border-gray-800 text-xs text-gray-500">
                    Validierungslogik-Beispiele:
                    <ul class="list-disc pl-4 mt-2 space-y-1">
                      <li>Lückenassistent bei fehlenden Events</li>
                      <li>Warnung/Block bei Pause &lt; Mindestpause</li>
                      <li>Pflichtaufgaben pro Tagesbericht (Foto/Signatur)</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <div x-show="adminTab!=='Organisation' && adminTab!=='Regelwerk'" class="mb-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="pt-4 px-4 pb-4 text-xs text-gray-500">Dieser Bereich ist ein UI-Stub (Inhalte gekürzt). Fokus für Phase 1: Flags/Policies/Datenresidenz.</div>
            </div>
          </div>
        </section>
      </main>
    </div>

    <!-- Bottom Nav (mobile) -->
    <nav class="sticky bottom-0 z-10 bg-white/90 dark:bg-gray-950/90 backdrop-blur border-t border-gray-200 dark:border-gray-800 lg:hidden">
      <div class="grid" :class="navItems().length===5?'grid-cols-5':navItems().length===3?'grid-cols-3':'grid-cols-4'">
        <template x-for="item in navItems()" :key="item.key">
          <button @click="showTab(item.key)" class="flex flex-col items-center gap-1 py-2 text-xs" :class="tab===item.key?'text-gray-900 dark:text-gray-100':'text-gray-500 dark:text-gray-400'">
            <i :data-lucide="item.icon" class="w-4 h-4"></i>
            <span x-text="item.label"></span>
          </button>
        </template>
      </div>
    </nav>

    <!-- StartSheet Modal -->
    <div x-show="showStartSheet" x-cloak class="fixed inset-0 z-30 flex items-end">
      <div class="absolute inset-0 bg-black/40" @click="showStartSheet=false"></div>
      <div class="relative w-full bg-white dark:bg-gray-950 rounded-t-2xl p-4 border-t border-gray-200 dark:border-gray-800">
        <div class="h-1 w-10 bg-gray-300 dark:bg-gray-700 rounded-full mx-auto mb-3"></div>
        <div class="text-base font-semibold mb-1">Arbeitsstart · Pflichtangaben</div>
        <div class="text-xs text-gray-500 mb-3">Bitte <b>Arbeitsort</b>, <b>Firma</b> und <b>Kunde</b> wählen.</div>
        <div class="space-y-3">
          <div>
            <div class="text-xs mb-1">Arbeitsort (Standort/Depot)</div>
            <select x-model="startData.site" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
              <option value="">Bitte wählen…</option>
              <template x-for="s in STANDORTE" :key="s"><option x-text="s"></option></template>
            </select>
          </div>
          <div>
            <div class="text-xs mb-1">Firma</div>
            <select x-model="startData.company" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
              <option value="">Bitte wählen…</option>
              <template x-for="s in FIRMEN" :key="s"><option x-text="s"></option></template>
            </select>
          </div>
          <div>
            <div class="text-xs mb-1">Kunde / Projekt</div>
            <select x-model="startData.customer" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
              <option value="">Bitte wählen…</option>
              <template x-for="s in KUNDEN" :key="s"><option x-text="s"></option></template>
            </select>
          </div>
        </div>
        <div class="flex items-center justify-end gap-2 mt-4">
          <button class="px-3 py-2 text-sm" @click="showStartSheet=false">Abbrechen</button>
          <button class="rounded-xl px-3 py-2 text-sm" :disabled="!startData.site||!startData.company||!startData.customer" style="background:var(--ci-primary);color:#0b0b0b" @click="showStartSheet=false">Übernehmen & weiter</button>
        </div>
      </div>
    </div>

    <!-- Hidden file input for uploads -->
    <input type="file" class="hidden" multiple x-ref="file" @change="onFilesSelected($event.target.files); $refs.file.value=null;" />

  </div>

  <!-- Alpine App -->
  <script>
    function app(){
      return {
        // Constants / Mock Data
        ENV: 'STAGING',
        ORGS: ['Nuri Group','SOLAR ASPEKT','WERK STUDIO'],
        ROLES: ['Mitarbeiter','Dispo','Teamleitung','Admin'],
        STANDORTE: ['Hauptstandort','Depot Nord','Depot Süd'],
        FIRMEN: ['SOLAR ASPEKT','WERK STUDIO'],
        KUNDEN: ['Müller GmbH','Schmitt OHG','Bäckerei Kern'],

        // RBAC
        can(role, perm){
          const P = {
            'Dashboard:view': ['Mitarbeiter','Dispo','Teamleitung','Admin'],
            'Journal:view':   ['Mitarbeiter','Dispo','Teamleitung','Admin'],
            'Einsatz:view':   ['Mitarbeiter','Dispo','Teamleitung','Admin'],
            'Dispo:view':     ['Dispo','Teamleitung','Admin'],
            'Admin:view':     ['Teamleitung','Admin'],
          };
          return (P[perm]||[]).includes(role);
        },

        // State
        tab: 'Dashboard',
        status: 'Off Duty',
        themeMode: localStorage.getItem('themeMode') || 'auto', // light|dark|auto
        dark: window.matchMedia('(prefers-color-scheme: dark)').matches,
        org: 'Nuri Group',
        role: 'Admin',
        startData: { site:'', company:'', customer:'' },
        flags: { sso:true, auditLog:true, webhooks:true, geofenceAuto:true, etaOnMap:true, anomalies:true },
        policies: { requireStartData:true, pauseMinMinutes:30, trackOnlyOnDuty:true, blockOnMandatoryTasks:false },
        dataResidency: { region:'EU', byok:false },
        dailyTasks: [
          { id:1, label:'Zählerstand dokumentiert', required:true, done:false },
          { id:2, label:'Kundensignatur erfasst', required:true, done:false },
          { id:3, label:'Fotos hochgeladen', required:false, done:false },
        ],
        appointments: [
          { id:1, time:'09:00', customer:'Müller GmbH', address:'Hauptstr. 12', departedAt:0, arrivedAt:0 },
          { id:2, time:'12:45', customer:'Schmitt OHG', address:'Industriestr. 5', departedAt:0, arrivedAt:0 },
          { id:3, time:'15:30', customer:'Bäckerei Kern', address:'Marktplatz 3', departedAt:0, arrivedAt:0 },
        ],
        dailyAttachments: [],
        attachmentsByAppointment: {},
        uploadTarget: null,
        adminTabs: [
          { t:'Organisation', icon:'building-2' },
          { t:'Benutzer & Rollen', icon:'users' },
          { t:'Standorte & Geofences', icon:'map-pin' },
          { t:'Regelwerk', icon:'shield-check' },
          { t:'Integrationen', icon:'server' },
          { t:'Audit-Log', icon:'activity' },
          { t:'Exporte', icon:'file-spreadsheet' },
        ],
        adminTab: 'Organisation',
        anomalies: [],
        events: [],
        activeTransitId: null,

        // Derived
        mandatoryOk(){ return this.dailyTasks.filter(t=>t.required).every(t=>t.done); },
        etaMinutes(){ return (this.flags.etaOnMap && this.status==='Fahrt') ? 12 : null; },

        // UI helpers
        statusChip(s){
          s = s || this.status;
          const map = {
            'Off Duty':'bg-gray-200 text-gray-900 dark:bg-gray-800 dark:text-gray-200',
            'Fahrt':'bg-blue-100 text-blue-900 dark:bg-blue-900/40 dark:text-blue-200',
            'Beim Kunden':'bg-[rgba(116,185,31,0.15)] text-[rgba(116,185,31,1)] dark:bg-[rgba(116,185,31,0.15)] dark:text-[rgba(116,185,31,1)]',
            'Pause':'bg-amber-100 text-amber-900 dark:bg-amber-900/40 dark:text-amber-200',
          };
          return `<span class=\"px-3 py-1 rounded-full text-xs font-medium ${map[s]||'bg-gray-100'}\">${s}</span>`;
        },
        navItems(){
          const base = [
            { key:'Dashboard', label:'Dashboard', icon:'home' },
            { key:'Journal', label:'Journal', icon:'list-checks' },
            { key:'Tagesbericht', label:'Tagesbericht', icon:'clipboard-list' },
            { key:'Einsatz', label:'Einsatz', icon:'file-text' },
          ];
          const dispo = this.can(this.role,'Dispo:view') ? [{ key:'Dispo', label:'Dispo', icon:'users' }] : [];
          const admin = this.can(this.role,'Admin:view') ? [{ key:'Admin', label:'Admin', icon:'settings' }] : [];
          return [...base, ...dispo, ...admin];
        },
        showTab(k){ this.tab = k; this.$nextTick(()=> lucide.createIcons()); },

        // Actions
        primaryAction(){
          if(this.status==='Off Duty') return { label:'Schicht starten', icon:'log-in', next:'Fahrt' };
          if(this.status==='Fahrt') return { label:'Ankunft Kunde', icon:'map-pin', next:'Beim Kunden' };
          if(this.status==='Beim Kunden') return { label:'Abfahrt', icon:'car', next:'Fahrt' };
          if(this.status==='Pause') return { label:'Pause beenden', icon:'play', next:'Beim Kunden' };
          return { label:'Start', icon:'play', next:'Fahrt' };
        },
        requireStartData(){
          if(!this.policies.requireStartData) return false;
          const d = this.startData; const need = !d.site||!d.company||!d.customer;
          if(need) this.showStartSheet = true; return need;
        },
        onPrimaryAction(){
          const p = this.primaryAction();
          if((/Schicht/.test(p.label) && this.requireStartData()) || (/Arbeit/.test(p.label) && this.requireStartData())) return;
          if(this.isTransitionAllowed(this.status, p.next)) this.setStatus(p.next);
        },
        onSecondary(kind){
          if(kind==='pause') this.setStatus(this.status==='Pause' ? 'Beim Kunden' : 'Pause');
          if(kind==='work')  this.setStatus('Beim Kunden');
          if(kind==='drive') this.setStatus('Fahrt');
        },
        endShift(){ this.setStatus('Off Duty'); },

        // State machine
        isTransitionAllowed(from, to){
          const allowed = { 'Off Duty':['Fahrt'], 'Fahrt':['Beim Kunden','Off Duty'], 'Beim Kunden':['Fahrt','Pause'], 'Pause':['Beim Kunden'] };
          return (allowed[from]||[]).includes(to);
        },
        setStatus(next){
          const prev = this.status;
          if(prev===next) return;
          if(next==='Fahrt' && prev!=='Fahrt') this.logEvent('drive.started');
          if(next==='Beim Kunden' && prev!=='Beim Kunden') this.logEvent('arrived');
          if(next==='Pause' && prev!=='Pause') this.logEvent('break.started');
          if(prev==='Pause' && next!=='Pause') this.logEvent('break.ended');
          if(next==='Off Duty') this.logEvent('shift.ended');
          if(prev==='Off Duty' && next!=='Off Duty') this.logEvent('shift.started');
          this.status = next;
        },
        logEvent(type){ this.events.push({ type, ts: Date.now() }); },

        // Appointments
        markDepart(id){ this.appointments = this.appointments.map(a=>a.id===id?{...a, departedAt: Date.now(), arrivedAt:0}:a); this.activeTransitId=id; this.logEvent('departed'); this.setStatus('Fahrt'); },
        markArrive(id){ this.appointments = this.appointments.map(a=>a.id===id?{...a, arrivedAt: Date.now()}:a); if(this.activeTransitId===id) this.activeTransitId=null; this.logEvent('arrived'); this.setStatus('Beim Kunden'); },

        // Uploads
        openUpload(target){ this.uploadTarget = target; this.$refs.file.click(); },
        onFilesSelected(files){ if(!files || !this.uploadTarget) return; const items = [...files].map(f=>({ id:this.genId(), name:f.name, kind:this.uploadTarget.kind, size:f.size })); if(this.uploadTarget.scope==='daily'){ this.dailyAttachments = [...this.dailyAttachments, ...items]; } else if(this.uploadTarget.scope==='appointment' && this.uploadTarget.apptId!=null){ const id=this.uploadTarget.apptId; this.attachmentsByAppointment[id] = [ ...(this.attachmentsByAppointment[id]||[]), ...items ]; } this.$nextTick(()=> lucide.createIcons()); },
        removeAttachment(scope, id, apptId){ if(scope==='daily'){ this.dailyAttachments = this.dailyAttachments.filter(a=>a.id!==id); } else if(scope==='appointment' && apptId!=null){ this.attachmentsByAppointment[apptId] = (this.attachmentsByAppointment[apptId]||[]).filter(a=>a.id!==id); } },
        genId(){ return Math.random().toString(36).slice(2) + Date.now().toString(36); },

        // Utils
        computeMinutes(a,b){ return Math.max(0, Math.round((b-a)/60000)); },
        formatTime(ts){ const d=new Date(ts); return d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}); },

        // Journal mock data
        journalRows:[
          { time:'07:35', label:'Schicht gestartet', icon:'log-in' },
          { time:'07:40', label:'Fahrt gestartet', icon:'car' },
          { time:'08:05', label:'Ankunft Kunde (Müller GmbH)', icon:'map-pin', tone:'good' },
          { time:'08:10', label:'Arbeit gestartet', icon:'play' },
          { time:'12:05', label:'Pause gestartet', icon:'coffee' },
          { time:'12:35', label:'Pause beendet', icon:'play' },
        ],
        journalRowsDetail:[
          { t:'07:35-07:40', l:'Vorbereitung / Depot', d:'5 min' },
          { t:'07:40-08:05', l:'Fahrt zum Kunden', d:'25 min' },
          { t:'08:10-12:05', l:'Arbeit beim Kunden', d:'3 h 55 min' },
          { t:'12:05-12:35', l:'Pause', d:'30 min' },
        ],

        // Team mock
        team:[
          { name:'A. Kaya', status:'Beim Kunden', note:'Müller GmbH', ts:'08:10' },
          { name:'J. Blum', status:'Fahrt', note:'zu Schmitt OHG', ts:'08:45' },
          { name:'M. Roth', status:'Pause', note:'seit 12:05', ts:'12:05' },
        ],

        // Modal flag
        showStartSheet:false,

        // Admin
        dataResidency:{ region:'EU', byok:false },

        // Theme
        cycleTheme(){ this.themeMode = this.themeMode==='light' ? 'dark' : this.themeMode==='dark' ? 'auto' : 'light'; this.applyTheme(); },
        applyTheme(){ this.dark = (this.themeMode==='dark') || (this.themeMode==='auto' && window.matchMedia('(prefers-color-scheme: dark)').matches); localStorage.setItem('themeMode', this.themeMode); },

        // Effects
        init(){
          this.applyTheme();
          // Icons
          this.$nextTick(()=> lucide.createIcons());
          // System theme listener
          window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e=>{ if(this.themeMode==='auto'){ this.dark = e.matches; }});
          // Geofence Auto-Arrival (demo)
          this.$watch('status', (s)=>{
            if(this.flags.geofenceAuto && s==='Fahrt' && this.activeTransitId!=null){
              clearTimeout(this._arrTimer); this._arrTimer = setTimeout(()=>{ this.markArrive(this.activeTransitId); }, 8000);
            }
          });
          // Anomaly detection
          this.$watch('events', ()=>{
            if(!this.flags.anomalies){ this.anomalies = []; return; }
            const out = [];
            const lastBreakStart = [...this.events].reverse().find(e=>e.type==='break.started');
            const lastBreakEnd   = [...this.events].reverse().find(e=>e.type==='break.ended');
            if(lastBreakStart && lastBreakEnd && (lastBreakEnd.ts - lastBreakStart.ts) < this.policies.pauseMinMinutes*60000){
              out.push(`Pause kürzer als ${this.policies.pauseMinMinutes} Minuten`);
            }
            const lastDrive = [...this.events].reverse().find(e=>e.type==='drive.started' || e.type==='departed');
            const lastArrive = [...this.events].reverse().find(e=>e.type==='arrived');
            if(lastDrive && (!lastArrive || lastArrive.ts < lastDrive.ts)){
              if(Date.now() - lastDrive.ts > 15*60*1000) out.push('Fahrt ohne Ankunft (15+ min)');
            }
            this.anomalies = out;
          }, { deep:true });

          // Self tests
          console.assert(this.can('Admin','Admin:view'), 'Admin sollte Admin:view dürfen');
          console.assert(!this.can('Mitarbeiter','Admin:view'), 'Mitarbeiter darf Admin:view nicht');
          const t1 = new Date('1970-01-01T07:40:00').getTime();
          const t2 = new Date('1970-01-01T08:05:00').getTime();
          console.assert(this.computeMinutes(t1,t2)===25, 'computeMinutes sollte 25 Minuten ergeben');
          console.assert(this.isTransitionAllowed('Off Duty','Fahrt'), 'Off Duty→Fahrt erlaubt');
          console.assert(!this.isTransitionAllowed('Fahrt','Pause'), 'Fahrt→Pause NICHT erlaubt');
          console.assert(!this.mandatoryOk(), 'Pflichten anfangs nicht erfüllt');
        },
      }
    }
  </script>
</body>
</html>
