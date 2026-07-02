# Qualifikations-Fundament — Achsen-Analyse (Schritt 1, reine Analyse)

> **Nur Lesen/Analyse, kein Bau.** Vertieft die zwei Autoritäts-Kandidaten-Achsen aus `mitarbeiter-hierarchie-bestandsaufnahme.md` (f63d015), damit Yama die Grundsatz-Entscheidung „welche Achse trägt Autorität" treffen kann. Stand 2026-07-02, Belege wörtlich.

> **⚠️ Zwei ehrliche Korrekturen zum Vorbefund (f63d015):**
> 1. **Achse A ist NICHT „nicht geroutet/dormant".** `PositionController` hat eine **voll geroutete** Hierarchie-UI: `/position/hierarchy` (board/save/auto-generate/**check**). Korrekt bleibt: die Matrix-**Daten** sind leer (0 Zeilen), und die Logik ist **Costing-verdrahtet** (OfferFolder/Deal), nicht Aufgaben-Autorität.
> 2. **Achse B trägt HEUTE SCHON Autorität:** `supervisor` routet Urlaubs-Genehmigungen (`LeaveController`) und steuert Zeitplan-Sicht (`TimeManagementController`). Das war im Vorbefund untererfasst.

---

## ACHSE A — `position_qualifications` (rollen/rang)

### A1. Struktur + die 26 Einträge + sort_order (Gleichstände!)
`DESCRIBE`: `id`, `name` (varchar **UNIQUE**), `default_price` (decimal 12,2), **`sort_order` (int unsigned)**, `status`, timestamps, `deleted_at`.

**26 Einträge auf nur 15 `sort_order`-Werten — strukturelle 2er-Paare (Costing-Tiers):**
```
1  Geschäftsführung            9  Projektplaner, Controlling
2  Management, Elektromeister  10 Lagerist, Außendienst
3  Meister, Elektrofachkraft   11 Buchhalter, Innendienst
4  Anlagenmechaniker SHK, Geselle  12 Bürokraft, Buchhaltung
5  PV-Monteur, Helfer          13 Marketing
6  Dachmonteur, Techniker      14 Verwaltung
7  Disponent, Planer           15 Ausbildung
8  Vertriebsberater, Designer
```
→ **Keine strikte 1-pro-Stufe-Ordnung:** sort 2–12 tragen je **2** Quals (ein „generischer" + ein „trade-spezifischer": Meister/Elektrofachkraft, PV-Monteur/Helfer …). Das ist ein **Stundensatz-Rang** (default_price je Tier), **keine Kommando-Kette**.

**Vokabular vs. Yamas 9 Stufen:** vorhanden Helfer, „Ausbildung"(=Azubi, **ohne 1/2/3-Split**), Meister/Geselle, PV-/Dach-Monteur. **FEHLEN: generischer Monteur, Obermonteur, Bauleiter, Projektleiter, Abteilungsleiter** (die liegen ad-hoc, s. Vorbefund #7).

### A2. „can-perform-below"-Logik — geroutet, aber costing-genutzt
Vollständig geroutet (`routes/web.php:2262-2266`, Kommentar „Qualification hierarchy / replacement matrix"): `hierarchyBoard`, `hierarchySave`, `hierarchyAutoGenerate`, `hierarchyCheck`.
- **`hierarchyAutoGenerate`** (Docblock wörtlich): „Lower sort_order = stronger/higher qualification … Meister can perform everyone below." Baut die Matrix aus `sort_order` (performer ≤ required → allowed).
- **`hierarchyCheck(performer, required, hours)`**: `$rule = PositionQualificationHierarchy::ruleFor(...)`; `$allowed = $rule && (bool)$rule->allowed`; dann **`efficiency_factor`, `cost_factor`, `hourlyCost`, `totalCost`**. → Das **`allowed`-Bit IST ein Autoritäts-Signal** („darf X Arbeit Y ausführen"), aber der Apparat ist **primär ein Kosten-Rechner**.
- **Genutzt wird die Matrix in `OfferFolderController:3688` + `DealController:2717`** (Angebots-/Auftrags-Kalkulation) — **nicht** in der Aufgaben-Zuweisung. → Achse A ist heute **Costing-verdrahtet**, nicht Aufgaben-Autorität.
- **Matrix-Daten = 0 Zeilen** → `ruleFor` liefert `null` → `$allowed = false` für ALLES, bis jemand die Matrix per UI/auto-generate füllt. → **Mechanik wired, Wirkung heute null.**

### A3. Personen-Link (der Rang IST auf Menschen)
`employees.qualification_id` → `position_qualifications.id` (**loser int, KEIN FK**; de-facto 15/15-Match). **50/51 MA** verknüpft. Verteilung (nur die **generische** Leiter wird real genutzt, nicht die Trade-Paare):
```
GF 1 · Management 1 · Meister 9 · Geselle 15 · Helfer 2 · Techniker 1 · Planer 1
· Designer 3 · Controlling 3 · Außendienst 1 · Innendienst 4 · Buchhaltung 3
· Marketing 1 · Verwaltung 2 · Ausbildung 3
```
→ Montage-Kern real: **Meister 9 + Geselle 15 + Helfer 2 + Ausbildung 3**. Robustheit: **ohne FK**, aber Daten konsistent; ein falscher/gelöschter Qual-Wert würde nicht DB-seitig gefangen.

### A4. Kann A „wer muss geprüft werden" tragen?
Teilweise: A hat **Rang** + ein **`allowed`-Bit** (darf ausführen). **Was fehlt:** (a) das Vokabular für Yamas Kommando-Rollen; (b) eine strikte Ordnung (Ties auflösen); (c) das Konzept **„geprüft werden" ≠ „ausführen"** (heute nur execute-allowed, keine Prüf-Schwelle); (d) Entkopplung von Costing (efficiency/cost dominieren). A liefert das „OB darf/kann", nicht das „muss geprüft werden".

---

## ACHSE B — `employees.supervisor` (personen-basiert)

### B5. Struktur + Baum (Tiefe, Ketten)
`employees.supervisor` = **`int null`, KEIN DB-FK** (nur App-Validierung `exists:employees,id` in `EmployeeController:738`). Self-referenz = Reports-to.
- **49/51 belegt · 2 Wurzeln · Max-Tiefe 2** → ein **flacher 3-Ebenen-Baum**. Ketten-Verteilung: Tiefe 0 = 2, Tiefe 1 = 15, Tiefe 2 = 34.
- **16 verschiedene Chefs**; Spitze **Markus (122) = 15 Direkte**, danach mehrere mit je 3. → Ein Haupt-Chef + ~15 Mittel-Zuständige.

### B6. Wird der Baum genutzt? — JA, für Autorität
- **`LeaveController:1202`**: `'request_to' => $employee->supervisor ?? $employee->id` → **Urlaubsanträge gehen an den Vorgesetzten** (Genehmigungs-Routing).
- **`TimeManagementController:16/21/224/246`**: „nur eigener Plan, dessen Vorgesetzter (`employees.supervisor`) oder Admin" → **Zeitplan-Sicht/Zugriff** über supervisor gesteuert; `Employee::where('supervisor',$me)->exists()` = „bin ich Chef von jemandem?".
- → **Der supervisor-Baum ist bereits das gelebte „wer genehmigt/darf sehen"-Achse.** Nicht bloß ein Datenfeld.

### B7. Kann B „wer prüft" tragen?
**Ja, plausibel und teils schon real** (Leave/Time). **Vorteil:** echte Person-zu-Person-Zuständigkeit, gepflegt (49/51), bereits für Genehmigungen genutzt. **Nachteil:** **personen- statt rollen-basiert** (ein supervisor ist nicht zwingend höher qualifiziert im relevanten Gewerk); **flach** (3 Ebenen); **kein FK**; kein Rang-Bezug (Baum kennt keine Qualifikation).

---

## VERGLEICH + ENTSCHEIDUNGSGRUNDLAGE (Fakten, keine erzwungene Empfehlung)

### 8. Welche Achse trägt welchen Teil von „aus Qualifikation folgt: wer darf + wer prüft"?
| Teil der Regel | Passt zu | Beleg-Lage |
|---|---|---|
| **OB Prüfung nötig** (Qualifikations-Schwelle) | **Achse A** (Rang) | A hat Rang + allowed-Bit, ABER costing-getönt, Matrix leer, Ties, Kommando-Vokabular fehlt |
| **WER prüft** (zuständige Person) | **Achse B** (supervisor) | B routet **heute schon** Genehmigungen (Leave/Time) — direkt anschlussfähig |

**Kombinationsmöglichkeiten + Konsequenzen:**
- **(K1) A = OB, B = WER** (arbeitsteilig): passt am besten zur Fakten-Lage (A=Rang existiert, B=Genehmigungs-Routing existiert). **Konsequenz:** man braucht in A eine **saubere Autoritäts-Ordnung** (Ties/Vokabular klären) UND muss klären, wie „nächsthöher qualifizierter Prüfer" mit dem **supervisor**-Baum zusammenspielt (der Chef ist evtl. NICHT der fachlich Höhere). **Risiko:** zwei Achsen driften auseinander (Chef ≠ fachlicher Prüfer).
- **(K2) A trägt beides** (Rang entscheidet OB **und** „nächsthöher Qualifizierter im Gewerk" = WER): rein rollen-/rang-basiert, ignoriert den gepflegten supervisor-Baum. **Konsequenz:** man müsste „nächsthöher Qualifizierter" berechnen (A hat keine Person-Routing-Logik heute) und den existierenden Genehmigungs-Baum (Leave/Time) parallel führen → **Doppelung/Wildwuchs-Gefahr**.
- **(K3) B trägt beides** (der Vorgesetzte entscheidet OB + WER): nutzt nur den Baum, ignoriert Qualifikation. **Konsequenz:** „OB Prüfung nötig" wäre **nicht qualifikationsbasiert** — widerspricht Yamas Kern („aus Qualifikation folgt"). **Passt fachlich am schlechtesten.**

→ **Die Fakten stützen K1 am stärksten** (beide Achsen tun schon das, was ihr Teil verlangt), erzwingen es aber nicht — die Kopplung „fachlicher Rang ↔ Genehmigungs-Chef" ist der offene Knackpunkt. **Yama entscheidet.**

### 9. Kleinster achsen-unabhängiger erster Baustein
**Unabhängig von A/B/Kombination gebraucht:** eine **saubere, strikt geordnete Autoritäts-Rang-Achse pro Person**, entkoppelt vom Costing. Denn *jede* Variante (Schwelle ODER Abstand; A-solo ODER K1) braucht „welchen Rang hat diese Person, und wie ordnen sich Ränge". Heute liefert `position_qualifications` das **fast** (50/51 verknüpft), aber **costing-rang mit Ties + ohne Kommando-Rollen**. → Der sichere erste Schritt wäre, diese **Rang-Ordnung + das fehlende Vokabular zu klären** (nicht bauen — erst Yamas Entscheidung, ob man `position_qualifications` erweitert oder eine getrennte Kommando-Ordnung führt). *(Der zweite, hier NICHT analysierte Baustein — „Aufgabe trägt Mindest-Anforderung" — ist Gegenstand der Folge-Analyse.)*

---

## Gelesen / NICHT gelesen (ehrlich)
**Geprüft (wörtlich/live):** `DESCRIBE position_qualifications`; 26 Einträge + sort_order-Gruppierung (15 Tiers, 2er-Paare); `PositionController` `hierarchyAutoGenerate`/`hierarchyCheck`/`hierarchySave` + deren **Routen** (`/position/hierarchy/*`); Nutzung von `PositionQualificationHierarchy` in Offer/Deal; `employees.qualification_id`-Verteilung (50/51, per Stufe); `employees.supervisor` DB-Typ (int, **kein FK**) + Baum-Analyse (Tiefe 0/1/2 = 2/15/34, 16 Chefs, Top Markus 15); supervisor-Nutzung in `LeaveController`/`TimeManagementController` (wörtlich).

**NUR gegrept / NICHT VERIFIZIERT:**
- Ob `hierarchyCheck` je aus einem **Aufgaben**-Kontext (nicht Costing) aufgerufen wird — gefunden nur Offer/Deal; ein versteckter Aufruf nicht 100 % ausgeschlossen.
- Ob der supervisor-Baum **fachlich** korrekt ist (Chef = höher qualifiziert?) — **nicht** geprüft; die Baum-Daten sagen nichts über Qualifikations-Verhältnis Chef↔Untergebener.
- `default_price`/`efficiency_factor`-Nutzung im Detail (nur Existenz + Offer/Deal-Referenz).
- Ob `qualification_id` semantisch fix `position_qualifications` meint (kein FK; Daten matchen).

## Selbstkritik / Risiken
- **Ich habe zwei eigene Vorbefunde korrigiert** (A geroutet; B live-genutzt). Das zeigt: der erste Durchgang (f63d015) war an diesen Punkten zu schnell — die Route-/Nutzungs-Prüfung hier ist die belastbarere. Ehrlich: „dormant" war für den **Code** falsch, nur für die **Daten** (Matrix leer) richtig.
- **K1 „stützt die Fakten am stärksten" ist eine Beobachtung, keine Empfehlung** — der eigentliche Konflikt (fachlicher Rang ≠ Genehmigungs-Chef) ist ungelöst und könnte K2 attraktiver machen, wenn Yama „Prüfer = fachlich Höherer" will statt „Prüfer = Vorgesetzter".
- **Achse A ist tief mit Costing verwoben** — sie für Autorität zu nutzen heißt, ein Kosten-Modell mit Autoritäts-Semantik zu überladen; sauberer wäre evtl. eine getrennte Rang-Dimension. Das ist eine echte Architektur-Frage, die ich hier nur benenne.

---

*Reine Analyse — nichts geändert. Querverweise: `mitarbeiter-hierarchie-bestandsaufnahme.md` (7 Systeme, hier 2 korrigiert), `architektur-entscheidungen.md` (Weiche 6 PL-Prüfschritt), `rueckfluss-stufe1-bauplan.md` (1c).*
