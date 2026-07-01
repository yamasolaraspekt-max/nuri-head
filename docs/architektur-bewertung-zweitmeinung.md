# Architektur-Bewertung (Aufgaben/Ausführung) — Zweitmeinung am Code geprüft

> **Reine Analyse (nur Lesen), kein Code geändert.** Auftrag: eine Berater-Bewertung der Aufgaben-/Ausführungs-Architektur am Code prüfen (Punkte 1–10) und eine **eigene, widersprechende-wo-nötig** Experten-Einschätzung geben. Grundlage: `struktur-systeme-verhaeltnis-befund.md` + `kanban-ebenen-montage-planner-nuriva-befund.md`. Nicht bestätigen um des Bestätigens willen.
>
> **Gesamturteil:** Die Bewertung ist **überwiegend richtig in der Diagnose, aber an drei Stellen ungenau und an einer Stelle sachlich zu stark.** Wichtigster eigener Fund: **Der Status-Rückfluss aus dem Feld landet in `customer_histories` (Audit) — NICHT zurück auf der Büro-Kanban-Karte oder der Phasen-Instanz.** Das Büro sieht an der Stelle, wo es die Aufgabe anlegt, die Feld-Erledigung **nicht**. Das ist die eigentliche Alltags-Falle — schärfer als vom Berater benannt (Punkt 5). Zwei Empfehlungen sind zu pauschal (Punkt 8, teils 10).

---

## Prüfung Punkt für Punkt

### GUT

**1. Zwei Kanban-Ebenen sauber/dynamisch — STIMMT (mit einer Fußnote).**
`lead_stages` + `lead_stage_sub_stages` sind echte, gepflegte Tabellen (`key/name/color/icon/sort_order/is_active`), nicht hartcodiert — professionell. **Fußnote:** es ist bereits die **n-te** Stage-Tabelle (`stages` 2023, `customer_stages` 2025_07, `phase_stages` 2025_07, `offer_kanban_stages` 2026_06, `lead_stages` 2026_05). Die *neue* ist sauber — aber sie steht neben mehreren älteren. Das ist selbst ein Beispiel für Punkt 10 (der Berater lobt hier, ohne die Stage-Tabellen-Wildwuchs zu sehen).

**2. Planner als Aggregator = stärkstes Element — STIMMT, sogar stärker als beschrieben.**
Beleg: der Auto-Sync (`syncAndLoad:402`) lädt die Template-Aufgaben **stage-gefiltert** (`pmoResolveProjectLeadStageId` + `pmoLoadMatchingPhaseActivityRows`, `:73–88`) und upsertet `task_phase`+`phase_activity` (`:108/:144`); zusätzlich bündelt der Planner `appointment/ticket/personal_task/master_set/kanban_task` (`:910/:990/:1069/:845/:1704`), mit Status-Rückschreibung + Foto. **Das ist das durchdachteste Teil — Zustimmung.**

**3. Arbeitsteilung Pipeline ≠ Büro ≠ Feld „im Kern gesund" — STIMMT TEILWEISE.**
Die drei Sichten sind legitim (Zustimmung). Aber „gesund" überzeichnet: die Grenze **leckt**, weil alle über dieselbe `phase_activity` laufen **und** der Status nicht zwischen ihnen abgeglichen wird (s. Punkt 4/5). Es sind drei legitime Sichten mit **undichter** Naht, nicht drei sauber getrennte.

### FALSCH / PROBLEMATISCH

**4. Doppelte Status-Führung, keine garantierte eine Wahrheit → Konstruktionsfehler — STIMMT im Kern, aber PRÄZISER (und teils schlimmer):**
- Für `source_type='phase_activity'` schreibt der Rückfluss `pmoUpdatePhaseActivityStatus` **ausschließlich in `customer_histories`** (`updateOrInsert`, `:66`) — **nicht** in `phase_activities`, **nicht** in `customer_phase_lists`, **nicht** in `kanban_lead_tasks`. → Die parallelen Status-Spalten werden **gar nicht reconciled** (schlimmer als „gemildert durch Rückschreiben").
- Nur für `source_type='kanban_task'` schreibt `pmoUpdateKanbanTaskSourceStatus` zurück nach `kanban_lead_tasks` (`:2307–2353`). D. h. **Rückschreiben existiert nur für die Quelle, aus der das Item stammt** — nicht quer über die drei Tabellen.
- Korrektur der Prämisse: es sind **nicht** „3 gleichwertige Wahrheiten". Es ist **1 lebende** (`planner_items`) + **1 Büro-Board** (`kanban_lead_tasks`) + **1 fast totes** (`customer_phase_lists`, s. Punkt 7) + **1 Audit** (`customer_histories`). Der Konstruktionsfehler ist real, aber die Form ist eine andere.

**5. Bruch Büro-Kanban → Planner, „gefährlichster Fehler" — STIMMT TEILWEISE, und der Berater benennt die falsche Richtung als die gefährlichste:**
- Hin-Richtung: Eine im Büro-Kanban angelegte Aufgabe erreicht Nuriva **nicht automatisch** — korrekt. **Aber unvollständig:** es gibt einen **direkten** `kanban_task`→Plan-Pfad, `storeProjectWorkItem` (`:4687`, `type ∈ {kanban_task, personal_task, appointment, ticket}`), nicht nur „Umwandlung in personal_task/appointment". Es ist also **kein Bruch, sondern ein manueller Kuratier-Schritt**.
- **Der eigentliche Alltags-Bruch ist die RÜCK-Richtung** (vom Berater nicht benannt): Feld-Erledigung landet in `customer_histories` (Audit), **nicht** auf der Büro-Kanban-Karte, aus der die Aufgabe stammt. → Das Büro sieht die Erledigung **nicht dort, wo es geplant hat**. Das ist die gefährlichere tägliche Falle.

**6. Entkoppelte Formular-Felder → verschenkte Chance — STIMMT.**
Belegt (`struktur-systeme-verhaeltnis-befund.md`): `product_formulas`/`lead_product_checklist_values` haben **keinen** `phase_id`/`activity_id`-Bezug (0 Treffer). „Verschenkte Chance" ist die richtige Stärke — es ist eine fehlende Verknüpfung, kein aktiver Fehler. Zustimmung.

### EMPFEHLUNGEN DES BERATERS

**7. Planner = die eine Ausführungs-Wahrheit; `customer_phase_lists` ablösen — STIMMT, mit Datenbeleg UND einer Korrektur:**
- **Datenbeleg pro Ablösung:** `customer_phase_lists` ist **fast dormant** — **4** Dateien in `app/` (v. a. `CustomerPhaseListController`, `NewLeadsController`), **0** Views, **1** aktive Schreibstelle. → Ablösung ist **billig und risikoarm** (der Berater hat recht, konnte es aber nicht beziffern).
- **Korrektur:** „Planner = *die eine* Wahrheit" darf **nicht** heißen, `kanban_lead_tasks` mit abzuräumen. Das Büro-Board ist eine **legitime eigene Ebene** (Planung/Kuratierung vor dem Feld). Richtig ist: **Planner = Feld-Ausführungs-Wahrheit, `kanban_lead_tasks` = Büro-Wahrheit, beide über einen sauberen Status-Vertrag verbunden**; nur `customer_phase_lists` wird abgelöst.

**8. Büro-Kanban muss AUTOMATISCH in den Planner schreiben, manueller Schritt weg — ZU STARK / so falsch.**
Der manuelle Schritt (`storeProjectWorkItem`) ist **teils ein Feature**, kein Bug: das Büro **kuratiert**, was der Monteur sieht. „Kunde anrufen", „Unterlagen prüfen", „Angebot nachfassen" sind Büro-Aufgaben, die **nicht** aufs Monteur-Tablet gehören. Alles automatisch weiterzuleiten würde Nuriva mit Büro-Rauschen fluten. **Richtig:** nicht den Kuratier-Schritt abschaffen, sondern (a) ihn **1-Klick-frictionless** machen und (b) die **Rück-Richtung** automatisch schließen (Feld-Status → Büro-Karte). Hier widerspreche ich dem Berater klar.

**9. Formular-Felder (B) an `phase_activities` (A) koppeln — STIMMT (aber niedrige Dringlichkeit).**
Sinnvoll und belegt machbar (heute 0 Verknüpfung). Aber es ist eine **Erweiterung**, kein Alltags-Schmerz — gehört hinter 7/8 in der Reihenfolge.

**10. Kernthese „Danebenbau statt Ablösen" — STIMMT TEILWEISE, an Stellen unfair.**
- **Richtig** für die Alt-Schichten: die ~11 Status-Felder (Schwäche 1) und das **dormante, nie abgelöste `customer_phase_lists`** sind genau dieses Muster.
- **Unfair** gegenüber dem *jüngsten* Code: die 2026er-Arbeit **konvergiert** — der Planner filtert nach `lead_stage_id`/`sub_stage_id`, **ingestiert** `kanban_task`, **schreibt zurück** in `kanban_lead_tasks`. Das ist Integrations-, nicht Parallel-Bau. Auch die Zeitachse stimmt nicht ganz: `customer_phase_lists` (2025-04) → Planner (2026-01) → `lead_stages`/Kanban (2026-05/06), also **~14 Monate**, nicht „April→Juni". → Die These trägt als **Warnung**, überzeichnet aber die jüngste Richtung.

---

## Eigene Experten-Einschätzung

### Welches System wird die Ausführungs-Wahrheit? — **Planner, ja — aber differenziert.**
Ich stimme mit „Planner" überein, mit stärkerer Begründung als der Berater: Planner ist als **einziges** stage-gefiltert (an die 6 Phasen gebunden), aggregiert alle Quellen, ist Nuriva-angebunden und schreibt (teilweise) zurück. Kein anderes System kann das.
**Aber die Framing-Korrektur ist wichtig:** „eine Wahrheit" heißt **pro Lebenszyklus-Ebene**, nicht „eine Tabelle":
- **Feld-Ausführung:** `planner_items` = Wahrheit.
- **Büro-Planung:** `kanban_lead_tasks` bleibt eigene Wahrheit (Kuratierung).
- **Ablösen:** `customer_phase_lists` (dormant) — ersatzlos.
- **Kern-Fix ist kein Merge, sondern ein Status-Vertrag:** Feld-Erledigung muss **auf die Büro-Karte und die Phasen-Instanz** zurücklaufen (heute nur `customer_histories`).

### Richtige Bereinigungs-Reihenfolge (mit Abhängigkeiten)
1. **ZUERST: Weiche 1 (Statusquelle) entscheiden.** Ohne definierten Status-Vertrag ist jede Reconciliation Raten. *(Blockiert alles Weitere.)*
2. **Rück-Richtung schließen:** Feld-Status (Planner) → Büro-Kanban-Karte + Phasen-Instanz, nicht nur Audit. **Höchster Alltags-ROI, geringes Risiko** — das ist der eigentliche „gefährlichste Fehler".
3. **`customer_phase_lists` ablösen** (dormant, 1 Schreibstelle, 0 Views) — billig, entfernt eine der „Wahrheiten".
4. **Hin-Richtung entschlacken:** `storeProjectWorkItem` zum 1-Klick-„an Monteur geben" machen (Kuratierung behalten, Reibung raus).
5. **ZULETZT: Felder (B) an `phase_activities` koppeln** — Erweiterung, kein Bruch.

### Was der Berater übersehen hat
- **Rückfluss = Audit-Sackgasse:** Feld-Status → `customer_histories`, nicht zurück ins Büro-Board. **Das ist der wichtigste ungenannte Punkt.**
- **Der Kuratier-Schritt hat einen guten Grund** (nicht jede Büro-Aufgabe soll zum Monteur) — Punkt 8 ignoriert das.
- **customer_phase_lists ist fast tot** (Daten: 4/0/1) — macht Ablösung leicht; der Berater vermutet nur.
- **Stage-Tabellen-Wildwuchs** (mind. 5 Stage-Tabellen) — derselbe Danebenbau, den der Berater bei Punkt 1 sogar **lobt**.
- **Nuriva-Kopplung als Zwang:** weil Nuriva **nur** `planner_items` liest, ist die Planner-Wahl faktisch schon entschieden — jede Alternative müsste Nuriva umbauen. Das stärkt Empfehlung 7 über das hinaus, was der Berater sagt.

### Zu hart / zu weich
- **Zu hart:** Punkt 8 (Kuratier-Schritt pauschal abschaffen) und Punkt 10 (Zeitachse „April→Juni" + Unfairness ggü. der konvergierenden 2026er-Arbeit).
- **Zu weich:** Punkt 4/5 — der Berater **unterschätzt** den realen Bruch: nicht „gemildert durch Rückschreiben", sondern der Rückfluss erreicht die Büro-Ebene **gar nicht**. Der echte Schmerz ist größer als beschrieben, nur an anderer Stelle (Rück- statt Hin-Richtung).

---

*Reine Analyse — nichts geändert. Belege: `PlannerPlanController` — `pmoUpdatePhaseActivityStatus`→`customer_histories`(updateOrInsert:66), `pmoUpdateKanbanTaskSourceStatus`→`kanban_lead_tasks`(:2307–2353), `syncAndLoad`(:402) mit Stage-Filter `pmoResolveProjectLeadStageId`/`pmoLoadMatchingPhaseActivityRows`(:73–88), Upsert nur `task_phase`/`phase_activity`(:108/:144), `storeProjectWorkItem`(:4687, type∈{kanban_task,personal_task,appointment,ticket}), Quellen-Aggregation(:845/910/990/1069/1704); `customer_phase_lists`-Breite: 4 Dateien app/, 0 Views, 1 aktive Schreibstelle; Stage-Tabellen `stages/customer_stages/phase_stages/offer_kanban_stages/lead_stages`; `product_formulas`↔`phase_activities` 0 Verknüpfung. Querverweis: `struktur-systeme-verhaeltnis-befund.md`, `kanban-ebenen-montage-planner-nuriva-befund.md`, `nuriva-sync-anbindung-befund.md`, `architektur-entscheidungen.md` (Weiche 1).*
