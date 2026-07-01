# Progressbar zeitgewichtet + Soll-Ist — hart geprüft

> **Reine Analyse (nur Lesen), kein Code geändert.** Frage: (a) kann der Montage-Fortschritt **zeitgewichtet** (nach `duration_minutes`) statt nach Anzahl gerechnet werden? (b) dazu **zwei Werte** — Balken zeitgewichtet, Zähler nach Anzahl. (c) ein **Soll-Ist-Zeitvergleich** (in Zeit / Verzug / Puffer). Baut auf `progressbar-bau-befund.md`. Begriffe: `glossar.md`.
>
> **Kurzfazit — ehrlich, nicht beschönigt:**
> - **Zeitgewichtung ist RECHNERISCH sauber** (flache planner_items, `SUM(done)/SUM(all)`), **aber die Zeit-Daten sind heute unbrauchbar:** `phase_activities.duration` liegt als `time`-Wert `00:00:05` vor, wird im Sync mit `(int)` gecastet → **0** → fällt auf **Default 60**. → Jeder Schritt wöge gleich (60). **Zeitgewichtung ist ohne Daten-/Konvertierungs-Fix eine Scheingenauigkeit.**
> - **Zähler (Anzahl) sind sauber baubar** aus derselben Menge.
> - **Soll-Ist ist HEUTE NICHT baubar:** `planned_end_at` wird beim Sync **auf NULL gesetzt** (nur manuelles Planen füllt es) → kein „Soll" für die Montage-Schritte. Ohne konsequent gesetzte geplante Termine ist der ganze Verzug-Vergleich **Theorie.**

---

## 1. Zeit-Felder auf `planner_items` (wörtlich)

```php
$table->unsignedInteger('duration_minutes')->default(60);        // NICHT nullable, Default 60
$table->timestamp('planned_start_at')->nullable()->index();
$table->timestamp('planned_end_at')->nullable()->index();
// ALTER (add_status…): started_at / paused_at / stopped_at (alle nullable)
```
→ Zeitfelder vorhanden: `duration_minutes` (Soll-Dauer, **Default 60**), `planned_start_at`/`planned_end_at` (geplant), `started_at`/`stopped_at`/`paused_at` (Ist-Ausführung). **Kein `parent_id`** auf planner_items → **flach** (grep parent_id = 0).

## 2. Wird `duration_minutes` gefüllt — und woraus?

`pmoUpsertTemplatePlannerItem:786` (wörtlich): `$item->duration_minutes = max(1, (int) ($payload['duration_minutes'] ?? $item->duration_minutes ?? 60));` → **immer ≥ 1, nie null/0.**

Quelle im Sync:
- **phase_activity** (`:533`): `$duration = (int) ($activity->duration ?? 0); if ($duration <= 0) $duration = (int)($phase->count ?? 0); if ($duration <= 0) $duration = 60;`
- **task_phase** (`:512`): `'duration_minutes' => max(1, (int) ($phase->duration_minutes ?? $phase->count ?? 60))` — `task_phases` hat **kein** `duration_minutes` → immer `count`/60.

**⚠️ Konvertierungs-Bug:** `phase_activities.duration` ist ein **`time`-Feld** (`$table->time('duration')->nullable();`, Typ HH:MM:SS). `(int) '00:00:05'` in PHP = **0** (nur führende Ziffern). → für JEDE zeit-typisierte Aktivität ergibt der Cast 0 → Fallback `count`/60. **Die echte Dauer wird nie korrekt in Minuten übernommen.**

## 3. Der kritische Fall „kein Zeit" — empirisch belegt

Die alte Sorge „Schritt ohne Zeit zählt 0" **tritt nicht ein** (Default 60 + `max(1,…)`). Das **echte** Problem ist umgekehrt — **alles wird 60**:

Echte Daten (`phase_activities`, 49 Zeilen, alle `duration` gesetzt):
```
duration-Beispiel: [00:00:05]  (int)=0
duration-Beispiel: [00:00:07]  (int)=0
duration-Beispiel: [00:00:12]  (int)=0
duration_type-Werte: [hours]
```
→ **Alle 49 casten zu 0 → Fallback 60.** Zusätzlich Format-Widerspruch: `duration_type='hours'`, Wert aber `00:00:05` (= 5 Sekunden). **Die Dauer-Daten sind inkonsistent/unbrauchbar.** `planner_items` = **0 Zeilen** (noch kein Montage-Plan gesynct) → live nicht messbar, aber die Pipeline erzeugt garantiert 60-Werte.

## 4. Berechnung + Doppelzählung

**Rechnung:** `SUM(duration_minutes WHERE status='done') / SUM(duration_minutes) × 100` je Plan — technisch sauber, **keine** Division durch 0 (jedes Item ≥ 1). Eine Summen-Query über `duration_minutes` existiert schon (`:5725` `$items->sum(fn($item)=>(int)($item->duration_minutes ?? 0))`).

**Doppelzählung — zwei Ebenen:**
- **Ebene 1 (Phase vs Aktivitäten):** Der Sync legt **task_phase-Items UND phase_activity-Items** in denselben Plan. Summiert man ALLE → **Doppel** (Phase = Container ihrer Aktivitäten). → **Lösung:** nur `source_type='phase_activity'` zählen (Yamas „nur Arbeits-Aufgaben/-Schritte"). Löst Ebene 1.
- **Ebene 2 (Aufgabe vs Arbeitsschritt):** `pmoLoadMatchingPhaseActivityRows` lädt **alle** passenden Aktivitäten (kein `parent_id`-Filter, `:618–652`). Wenn `phase_activities` eine Eltern-Kind-Hierarchie hätten, würden Eltern **und** Kinder als Items gesynct → Doppel. **ABER echte Daten: 0 von 49 haben `parent_id`** → heute **keine** Ebene-2-Doppelzählung. *Schema erlaubt sie; bei künftiger Nutzung kehrt das Problem zurück — NICHT VERIFIZIERT für zukünftige Daten.*

## 5. Arbeitsschritt vs Aufgabe in `planner_items`

`planner_items` sind **flach** (kein `parent_id`); `source_type` unterscheidet `task_phase` (Phase-Container) von `phase_activity` (die Arbeit). Die Template-Hierarchie (`phase_activities.parent_id` = Aufgabe→Arbeitsschritt) ist heute **ungenutzt** (0 Kinder) → die `phase_activity`-Items sind die flache Arbeitsliste. → **Zählen: nur `source_type='phase_activity'`**, eine Ebene, keine Doppelung (heute).

## 6. Fazit Zeitgewichtung: **baubar NUR mit Voraussetzungen**

Rechnerisch ja. **Voraussetzungen, die HEUTE fehlen:**
1. **`phase_activities.duration` muss echte Integer-Minuten liefern** (heute `time`-Format `00:00:0X`, `duration_type='hours'` — inkonsistent).
2. **Der `(int)`-Cast im Sync muss `time`→Minuten korrekt umrechnen** (heute Bug → 0 → Default 60).
3. **Nur `source_type='phase_activity'` zählen** (Ebene-1-Doppel vermeiden); bei künftiger parent_id-Nutzung Blätter-Regel klären.
→ Ohne 1+2 ist die Zeitgewichtung **Scheingenauigkeit** (alles 60 = de facto Anzahl-Zählung, nur verkleidet).

## 7. Zwei Werte aus derselben Menge (Ergänzung 1)

**Ja, sauber trennbar** — eine Query, dieselbe Menge:
```sql
SELECT
  COUNT(*)                                              AS gesamt,
  SUM(status='done')                                    AS erledigt_anzahl,
  SUM(duration_minutes)                                 AS soll_min,
  SUM(CASE WHEN status='done' THEN duration_minutes END) AS erledigt_min
FROM planner_items
WHERE plan_id IN (<montage-plan-ids des Gewerks>)
  AND source_type='phase_activity'
  AND status <> 'cancelled';
```
- **Balken (zeitgewichtet):** `erledigt_min / soll_min × 100`.
- **Zähler (Anzahl):** `erledigt_anzahl` von `gesamt`, `offen = gesamt − erledigt_anzahl`.
→ Beide aus **einer** Menge; der Balken ist nur so gut wie `duration_minutes` (Punkt 6).

## 8. Status-Kübel für die Zähler (Ergänzung 1)

Kanonischer Status (Migration `planner_items`): `open|scheduled|in_progress|paused|done`. `pmoNormalizePlannerStatus` normalisiert viele Aliasse (u. a. `cancelled/storniert/junk/rejected`→ cancel-Klasse). Der Sync speichert **normalisiert** (`pmoUpsert` ruft `pmoNormalizePlannerStatus`).

**Kübel-Definition (damit erledigt + offen = gesamt, kein Status geht unter):**
- **erledigt** = `status = 'done'`
- **offen** = `status ∈ {open, scheduled, in_progress, paused, blocked}` (alles nicht-done, nicht-cancelled)
- **gesamt** = erledigt + offen = **alle außer `cancelled`**
→ `cancelled` wird aus `gesamt` **ausgeschlossen** (sonst verfälscht). *Design-Entscheidung: paused/blocked zählen als „offen" — bestätigen.* ⚠️ *NICHT VERIFIZIERT:* ob **alle** Schreibpfade normalisieren — `PlannerItemStateController` setzt Status direkt; falls ein Pfad rohe Werte schreibt, müsste die Zähl-Query zusätzlich normalisieren.

## 9. Geplante Termine — vorhanden, aber beim Sync NULL (Ergänzung 2)

`planned_start_at`/`planned_end_at` existieren (Punkt 1). **Der Template-Sync setzt sie auf NULL** (wörtlich, syncAndLoad-Payload `:540–552`): `'planned_start_at' => null, 'planned_end_at' => null`.

`planned_end_at` wird **nur in anderen Pfaden** gefüllt: `:801` (nur wenn Payload es liefert — der Template-Sync liefert null), `:919` (Termin-/Appointment-Pfad), `:3885` (Verschieben/Reschedule), `:4749` (`storeProjectWorkItem` manuelles Hinzufügen). → **Für die gesyncten Montage-Schritte (phase_activity) ist `planned_end_at` = NULL**, außer der Nutzer plant jeden Schritt **manuell** im Planner. **Ohne gefülltes `planned_end_at` gibt es kein „Soll".**

## 10. Tatsächlicher Verbrauch — was ist messbar? (Ergänzung 2)

- **`done_at`** = Zeitpunkt des Abhakens (kein Aufwand). ✓ vorhanden.
- **Ist-Arbeitszeit je Item:** `PlannerItemStateController` setzt **`started_at`** (`:58`), **`paused_at`** (`:84`), **`stopped_at`** (`:108`) → wenn Start/Stop genutzt wird, ist **Ist-Dauer = stopped_at − started_at (− Pausen)** ableitbar. *NICHT VERIFIZIERT, ob Nuriva diesen Timer real nutzt (Route existiert; planner_items=0).* 
- **Attendance:** `PlannerAttendanceController` (`work_started_at`, `work_total_seconds`, Pausen) → Ist-Zeit **pro Tag/Mitarbeiter**, nicht pro Aufgabe.

**Zwei Verzugs-Arten:**
- **KALENDER-Verzug** (`planned_end_at < heute` und `status ≠ done`): braucht nur `planned_end_at` + `status`. → **datenseitig möglich, ABER nur wenn `planned_end_at` gefüllt ist** (Punkt 9 → für Montage-Schritte meist NULL).
- **AUFWAND-Verzug** (Ist > Soll): braucht **erfasste Ist-Zeit** (`started_at`/`stopped_at`) **UND** ein echtes `duration_minutes`-Soll (Punkt 6 → heute 60-Default). → **doppelt blockiert.**

## 11. Puffer / Guthaben (Ergänzung 2)

„Liegt vor Plan" = (Fortschritt % ) vs (verstrichene geplante Kalenderzeit %) — braucht `planned_start_at` + `planned_end_at` + heutiges Datum + Fortschritt. Da `planned_*` beim Sync NULL sind (Punkt 9), ist **Puffer ohne konsequentes manuelles Planen nicht rechenbar.**

## 12. Fazit Soll-Ist — ehrlich

**HEUTE NICHT baubar** — nicht wegen fehlender Felder, sondern weil die **Plandaten nicht gefüllt werden:**
- `planned_end_at` = **NULL** für die gesyncten Montage-Schritte (nur manuelles Planen füllt es). → **Kein Soll, kein Kalender-Verzug, kein Puffer.**
- `duration_minutes` = **Default 60** (Konvertierungs-Bug + Garbage-Daten). → **Kein echtes Aufwand-Soll.**
- Ist-Zeit-Mechanik (`started_at/stopped_at`) **existiert**, aber Nutzung durch Nuriva **NICHT VERIFIZIERT**.

**Realistische Machbarkeit, gestaffelt:**
1. **Am ehesten:** **Kalender-Verzug** — *sobald* geplante Enddaten konsequent gesetzt werden (manuell oder auto-abgeleitet aus Start + duration). Ohne das: Theorie.
2. **Aufwand-Verzug/Puffer:** erst wenn (a) `duration_minutes` echte Minuten trägt UND (b) der Ist-Timer genutzt wird. Beides heute nicht gegeben.

**Klartext:** Werden die geplanten Termine in der Praxis nicht gefüllt, ist der ganze Verzug-Vergleich **Theorie.** Der Fortschrittsbalken (Punkt 6/7) ist der realistisch baubare Teil — der Soll-Ist-Vergleich braucht **zuerst** eine Planungs-Disziplin (Termine + echte Dauern), die datenseitig heute fehlt.

---

## Offene DESIGN-Entscheidungen für Yama
1. **Welche Ebene trägt die Zeit / was zählt:** nur `source_type='phase_activity'`? Bei künftiger Aufgabe→Arbeitsschritt-Hierarchie: nur Blätter?
2. **Was bei fehlender Dauer:** Default 60 (heute, verfälscht) **vs** Schritt gleichgewichtet (1) **vs** Pflichtfeld „Dauer" beim Anlegen einer Aktivität.
3. **Dauer-Format fixen:** `phase_activities.duration` als echte Integer-Minuten speichern + Sync-Konvertierung `time→Minuten` reparieren.
4. **Soll-Termine:** sollen `planned_end_at` **automatisch** aus `planned_start_at` + `duration_minutes` abgeleitet werden (dann ist Kalender-Verzug baubar) oder rein manuell? Ohne Auto-Ableitung bleibt „Soll" meist leer.
5. **Ist-Zeit:** soll der Monteur-Timer (`started_at/stopped_at`) verpflichtend genutzt werden (Voraussetzung für Aufwand-Verzug)?
6. **Status-Kübel:** zählen `paused`/`blocked` als „offen", `cancelled` raus aus „gesamt"? (Empfehlung: ja.)

## Gelesen / NICHT gelesen (ehrlich)
**Vollständig gelesen/gezählt:** Migrationen `planner_items` (duration_minutes/planned_*/status) + ALTER (started/paused/stopped); `phase_activities` (`time('duration')`, `duration_type`, `parent_id`); `pmoUpsertTemplatePlannerItem` (768–818); syncAndLoad-Payload task_phase (`:509–520`) + phase_activity (`:530–555`); `pmoLoadMatchingPhaseActivityRows`/`pmoLoadMatchingTaskPhaseRows` (Select/Filter); `PlannerItemStateController` (started/paused/stopped-Zeilen); `pmoNormalizePlannerStatus` (Alias-Set); echte DB-Zahlen (phase_activities 49 + duration-Beispiele + parent_id 0; planner_items/plans = 0).
**Nur gegrept / NICHT VERIFIZIERT:** ob Nuriva den Ist-Timer real aufruft; ob **alle** Status-Schreibpfade normalisieren; das genaue `planned_end_at`-Setzen in `:919/3885/6629` (nur Existenz, nicht ob es Montage-Schritte betrifft); ob `duration_type`-Logik den `(int)`-Cast irgendwo korrigiert (grep fand keine Umrechnung — aber 11k-Controller nicht Zeile-für-Zeile).

## Schwächen dieser Prüfung
- **DB ist leer bei planner_items/plans** → alle Aussagen zur Praxis („planned_end_at bleibt leer", „duration=60") sind aus dem **Code-Pfad** abgeleitet, nicht an Live-Daten gemessen. Stark, aber nicht empirisch bestätigt.
- Der `(int) time` → 0-Befund ist an 5 echten phase_activities-Werten belegt (alle 00:00:0X), aber **NICHT VERIFIZIERT**, dass es keine anders formatierten Dauern gibt.
- „Nuriva-Timer-Nutzung" ist der schwächste Punkt — Route existiert, reale Nutzung unbekannt.

---

*Reine Analyse — nichts geändert. Belege: Migrationen `create_planner_items_table`(2026_01_21: duration_minutes default 60, planned_start/end nullable), `add_status_to_planner_items`(started/paused/stopped), `create_phase_activities_table`(2023_08_31: `time('duration')`, duration_type, parent_id); `PlannerPlanController` pmoUpsertTemplatePlannerItem:786, syncAndLoad phase_activity:533 / task_phase:512, planned-null:540–552, pmoLoadMatchingPhaseActivityRows:618–652, sum:5725, planned_end_at-Setzstellen:801/919/3885/4749; `PlannerItemStateController`:58/84/108; `pmoNormalizePlannerStatus` Alias-Set; tinker: phase_activities=49 (duration 00:00:0X, (int)=0, parent_id 0, duration_type=hours), planner_items=0, planner_plans=0. Querverweis: `progressbar-bau-befund`(Chat), `kanban-ebenen-montage-planner-nuriva-befund.md`, `struktur-systeme-verhaeltnis-befund.md`, `architektur-entscheidungen.md`(Weiche 6), `glossar.md`.*
