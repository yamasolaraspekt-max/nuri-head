# Planner ↔ Kanban — die zwei offenen NICHT-VERIFIZIERT-Punkte hart geklärt

> **Reine Analyse (nur Lesen), kein Code geändert.** Klärt die zwei in `planner-kanban-zuordnung-hart-geprueft.md` offen gelassenen Punkte (JSON-`meta`-Rück-Referenz; echte Daten) + liest `pmoUpsertTemplatePlannerItem`/Umfeld **vollständig** (statt nur grep). Strenge Vorgabe: wörtliche Belege, „NICHT VERIFIZIERT" wo unsicher, kein „passt".
>
> **Antwort vorweg: NEIN — es existiert keine nutzbare Zuordnung** planner_item(`phase_activity`) ↔ kanban-Karte. Dreifach bestätigt: (1) der Sync setzt **keine** `meta`-Rück-Referenz, (2) die kanban-`meta` enthält nur **Strings** (kein Karten-/Item-ID), (3) app-weiter Grep findet **keinen** Verknüpfungs-Query. Echte Daten sind **nicht messbar** (DB leer). → **Der einfache Fix bleibt unsicher; Weg 2 (Progressbar aus `planner_items` rechnen) ist der sichere.**

---

## Punkt 1 — JSON-`meta`: wird eine Rück-Referenz geschrieben?

**NEIN, auf beiden Seiten nicht.**

**(a) planner_items.meta — der Template-Sync setzt es GAR NICHT.** `pmoUpsertTemplatePlannerItem` (Z.768–818, **vollständig gelesen**) ist der einzige Schreiber des `phase_activity`-Items. Wörtlich der Schlüssel + alle gesetzten Felder:

```php
$item = PlannerItem::query()->firstOrNew([
    'plan_id'     => (int) $plan->id,
    'source_type' => $sourceType,
    'source_id'   => $sourceId,
]);
...
$item->title = $payload['title'] ?? ...;
$item->description = $payload['description'] ?? ...;
$item->duration_minutes = ...;
$item->sort_order = ...;
$item->status = ...;
$item->planned_start_at = ...; $item->planned_end_at = ...;
$item->done_at = ...; $item->done_by_employee_id = ...;
$item->save();
```

→ **Kein `$item->meta = …`** in der ganzen Methode (gezielte Gegenprobe: `awk … /->meta/` → leer). Für ein `phase_activity`-Item gibt es also **keine** meta-Referenz auf eine kanban-Karte.

**(b) kanban_lead_tasks.meta — enthält nur Strings, keine ID.** `KanbanLeadTaskController::storeFromTemplate` (Z.410–419, gelesen), wörtlich:

```php
'meta' => [
    'source'         => 'task_phase_template',
    'phase_name'     => $phase->phase_name,
    'activity_title' => $activity?->title,
],
```

→ `source`/`phase_name`/`activity_title` — **menschenlesbare Strings**, **kein** `planner_item_id`, **keine** numerische Referenz. Ein Match wäre höchstens über `activity_title` (Freitext) denkbar — das ist **kein** verlässlicher Schlüssel (nicht eindeutig, nicht stabil). **NICHT nutzbar.**

**(c) app-weiter Broad-Check (nicht nur die 2 Controller):**
- „`planner_items`/`PlannerItem` + `meta`" über ganz `app/` → **0 Treffer** (kein meta-Write mit Kanban-Bezug irgendwo).
- „`kanban_lead_tasks` per `phase_activity_id` im Planner-Kontext gesucht" → **0 Treffer**.
- Alle `planner_item_id`-Referenzen zeigen auf **Planner-Kindtabellen** (`planner_item_employees`, `planner_item_assets`, Dependencies/History/Comments/Materials) — **keine** auf `kanban_lead_tasks`.

## Punkt 2 — Echte Daten: ist die Zuordnung faktisch eindeutig?

**NICHT MESSBAR — die lokale DB ist leer.** Query + Ergebnis wörtlich:

```
kanban_lead_tasks total: 0
  davon mit phase_activity_id: 0
planner_items total: 0
  davon source_type=phase_activity: 0
Gewerk+phase_activity-Paare mit >1 kanban-Karte: 0
```

→ Die „0 Dubletten" sind **kein** Beleg für Eindeutigkeit — es gibt schlicht **0 Zeilen**. Empirisch lässt sich weder „in der Praxis 1:1" noch „in der Praxis Dubletten" belegen. **NICHT VERIFIZIERT (mangels Daten).** Es bleibt nur der **Schema-Befund** (kein Unique + `create()` ohne Dedup → Mehrfachheit erlaubt) aus dem Vorbefund.

## Punkt 3 — syncAndLoad/Umfeld: verknüpft der Sync irgendwo planner_item ↔ kanban-Karte?

**NEIN.** `pmoUpsertTemplatePlannerItem` (vollständig gelesen, s. Punkt 1) berührt `kanban_lead_tasks` **nicht** — kein Read, kein Write, kein Link. Die **einzige** planner↔kanban-Verbindung im Controller ist der `kanban_task`-Pfad (die 7 `kanban_lead_task_id`-Stellen), alle im `source_type='kanban_task'`-Kontext — wörtlich u. a.:

```php
'kanban_task' => $this->pmoUpdateKanbanTaskSourceStatus($sourceId, $status, $employeeId, $note),   // :1775
...
if ($data['type'] === 'kanban_task') { $table = 'kanban_lead_tasks'; ... }                          // storeProjectWorkItem :4741
```

→ Dieser Link funktioniert **nur**, wenn das Item **aus einer kanban-Karte** stammt (`source_id = kanban_lead_tasks.id`, Identitäts-Link). Für `source_type='phase_activity'` (`source_id = phase_activities.id`) gibt es **keinen** Pfad, der eine kanban-Karte kennt/anlegt/verknüpft. Die restlichen `kanban_lead_task_id`-Stellen betreffen nur Kanban-**Kindtabellen** (`kanban_lead_task_steps`/`_keys`/`_employees`).

## Punkt 4 — FAZIT: Zuordnung nutzbar?

**NEIN — nicht nutzbar.** Zusammengefasst, jeweils belegt:
- **Kein FK** (Vorbefund) · **kein meta-Link** (Punkt 1, dreifach) · **kein Query** (Punkt 1c/3) · **keine Daten** zum empirischen Gegenbeweis (Punkt 2).
- Die einzige echte planner↔kanban-Kopplung ist der **`kanban_task`-Identitäts-Link** — der greift **nicht** für Montage-Abschlüsse (die als `phase_activity` kommen).

**Damit die Fix-Frage:**
- **Einfacher Fix (kanban-Karte auf `done` setzen): NICHT sicher baubar.** Es gibt keinen verlässlichen Weg, die „richtige" Karte zu finden (kein Schlüssel; nur Freitext-`activity_title`, der nicht eindeutig ist).
- **Weg 2 (Progressbar aus erledigten `planner_items`/`phase_activities` rechnen): der sichere Weg** — er braucht **kein** Cross-Table-Matching, jeder Abschluss bewegt den Balken automatisch, weil `planner_items` bereits die Wahrheit des Feld-Abschlusses ist (`status='done'`, `done_at`, `done_by_employee_id`).
  - **Aber:** Weg 2 trägt eine **Design-Entscheidung** (kein technischer Blocker): Soll der Profil-Balken die **Feld-Ausführung** (planner_items) oder die **Büro-Planung** (kanban_lead_tasks) zeigen? Das hängt an Weiche 1/6 (welche Tabelle ist die Fortschritts-Wahrheit).

→ **Empfehlung bleibt Weg 2**, nach Weiche 1. Der einfache Fix wäre nur nach vorheriger Schaffung eines echten Links (z. B. `planner_items.meta.kanban_lead_task_id` beim Sync setzen **oder** Unique+`firstOrCreate` in `storeFromTemplate`) baubar — beides ein Umbau, kein reiner Fix.

---

## Gelesen / NICHT gelesen (ehrlich)

**Vollständig gelesen:**
- `PlannerPlanController::pmoUpsertTemplatePlannerItem` (Z.768–818, komplett) + gezielte `->meta`-Gegenprobe (leer).
- `KanbanLeadTaskController::storeFromTemplate` meta-Block (Z.410–419).
- `PlannerPlanController::storeProjectWorkItem`-Ausschnitt (Z.4740–4795).
- Alle 7 `kanban_lead_task_id`-Vorkommen (grep mit Kontext).
- App-weiter Broad-Grep: planner_items-meta-Writes, phase_activity↔kanban-Queries, planner_item_id-Referenzen.
- Echte-Daten-Query (tinker, Ergebnis 0 Zeilen).

**NICHT (vollständig) gelesen — Restlücken:**
- `syncAndLoad` (Z.402–~560) **nicht** Zeile-für-Zeile — aber der einzige Item-Schreiber ist `pmoUpsertTemplatePlannerItem` (voll gelesen, kein meta/kanban), und app-weiter Grep fand **0** meta-Writes → ein versteckter Link ist **sehr unwahrscheinlich**, aber ein hochdynamisch (variable Tabellennamen) gebauter Query ist per Grep **nicht 100 % ausgeschlossen**.
- **Frontend-JS** (Profil-Progressbar) — ob es clientseitig ein eigenes Matching macht — **nicht** gelesen (der Balken rechnet serverseitig aus `KanbanLeadTaskController::summaries`, aber ein zusätzlicher Client-Pfad ist **NICHT VERIFIZIERT**).

## Schwachstellen dieser Analyse (Selbstkritik, kein „passt")
- Die stärkste Aussage („kein Link irgendwo") ist bei einem 11k-Zeilen-Controller weiterhin **grep-gestützt** — zwei unabhängige Greps (meta; phase_activity+kanban) sind beide leer, was sie **stark** macht, aber nicht absolut.
- **Punkt 2 liefert null empirische Evidenz** (leere DB). Die Nicht-Eindeutigkeit steht **allein** auf dem Schema-Befund. Ob Produktivdaten Dubletten hätten, bleibt **offen**.
- „Weg 2 ist sicher" ist eine technische Aussage; **welche** Fortschritts-Wahrheit richtig ist, ist eine **Design-Frage** (Weiche 1/6), keine Code-Aussage.

---

*Reine Analyse — nichts geändert. Wörtliche Belege: `PlannerPlanController::pmoUpsertTemplatePlannerItem`(:768–818, kein `->meta`), Dispatch `kanban_task`(:1775), `storeProjectWorkItem`(:4741); `KanbanLeadTaskController::storeFromTemplate` meta(:410–419); app-weiter Grep (0 planner-meta-Kanban-Writes, 0 phase_activity↔kanban-Query); tinker-Zählung (alle 0). Querverweis: `planner-kanban-zuordnung-hart-geprueft.md`, `monteur-rueckfluss-vier-ziele-befund.md`, `architektur-entscheidungen.md` (Weiche 1/6).*
