# Qualifikations-Fundament — Modell gegen den Code geprüft (reine Analyse)

> **Nur Lesen/Analyse, kein Bau.** Prüft Claudes Modell (Yamas Regel in B1/B2/B3 gegossen) **kritisch** gegen die Code-Realität — nicht bestätigen, sondern prüfen. Baut auf `qualifikation-fundament-achsen-analyse.md` (2c3d38e). Stand 2026-07-02, Belege wörtlich.

**Das geprüfte Modell:** Trennung „Können" (Qualifikation) vs. „unbeaufsichtigt Dürfen" (Autorität). B1: Aufgabe trägt Mindest-Qualifikation. B2: Person hat Qualifikation (Vermutung: gewerkespezifisch). B3: Prüfpflicht aus dem **Abstand** Anforderung↔Qualifikation. Wer prüft = supervisor; Ob geprüft = Qualifikations-Abstand.

> **⚠️ ERGEBNIS: Das Modell passt konzeptionell (Können/Dürfen-Trennung ist im Code angelegt), aber DREI seiner Annahmen decken sich NICHT mit dem Code:**
> - **FRAGE 1 (gewerkespezifisch?): NEIN — Qualifikation ist GLOBAL.** Ein Wert pro Person, keine Per-Gewerk-Stufe. B2s Kern-Vermutung trifft nicht zu.
> - **FRAGE 2 (Abstand?): NEIN — es gibt nur ein Boolean `<=`, kein Abstand, keine Schwelle.** B3 wäre komplett neu.
> - **B1 (Anforderung an der Aufgabe): FEHLT** (nur eine leere, rangfreie `activity_positions`-Struktur).
> Details + ehrliches Urteil unten.

---

## FRAGE 1 — Gewerkespezifisch oder global? (B2)

**Antwort: GLOBAL.**
- **`employees.qualification_id` = EINE Spalte** (ein Wert/Person) → globaler Rang. **Keine** n:m-Tabelle Person↔Gewerk↔Stufe (es gibt nur `skills`/`other_skills` als eigene, einzelne Felder — keine gewerk-gestufte Qualifikation).
- **`position_qualifications` hat KEINEN Gewerk-/Trade-Bezug** (`DESCRIBE`: `id, name, default_price, sort_order, status` — keine `department_id`/`trade`/`product_id`). Die 26 Ränge sind **gewerkeübergreifend flach**.
- **Gewerk existiert — aber auf Positions-/Department-Ebene, nicht auf Qualifikation:** `departments` (16) = **teils echte Gewerke** (`Heizung, Elektro, SHK, Bauelemente, Schreiner, Dachdecker, Maler, Fliesenleger, Baudekoration`) + Org-Einheiten (`Controlling, Marketing, Finanzen, Buchhaltung, Verwaltung, Management, Geschäftsführung`). `department_positions` (50) verknüpft employee↔department↔position **pro Department**.
- **ABER: jeder MA ist in GENAU EINEM Department** (`0` MA mit >1 distinct department_id). → Es gibt **heute keinen** MA, der „im PV Obermonteur, im Elektro Helfer" ist. Ein Mensch = **ein** Gewerk + **eine** globale Qualifikation.

**URTEIL F1:** Qualifikation ist **global** modelliert. Für B2 (gewerkespezifische Stufe) fehlt: eine **n:m Person↔Gewerk↔Stufe**-Struktur — und die Realität (1 Gewerk/Person) legt nahe, dass B2s Per-Gewerk-Vermutung **aktuell gar nicht gebraucht** wird. *(NICHT VERIFIZIERT: ob „1 Gewerk/Person" eine harte Regel ist oder nur der aktuelle Datenstand — das Schema `department_positions` ließe mehrere zu.)*

---

## B1 — Trägt eine Aufgabe eine Mindest-Anforderung? (existiert das?)

**Antwort: FEHLT (nur rudimentär + leer + rangfrei).**
- **`kanban_lead_tasks`: 0** Qualifikations-/Skill-/Level-Feld. **`phase_activities`: 0** (kein qualif/skill/level/position-Feld).
- **`activity_positions`** (Aktivität↔Position): existiert als Struktur — `activity_id` (FK phase_activities) + `position_id` (FK **`positions`**). **Aber: 0 Zeilen**, und zeigt auf die **flache `positions`-Liste (kein Rang)**, nicht auf die rang-tragende `position_qualifications`. → Kann eine Aktivität mit Positionen **taggen**, aber **keine „Mindest-STUFE"** ausdrücken.

**URTEIL B1:** Eine echte „benötigte Mindest-Qualifikation je Aufgabe/Tätigkeitsart" **existiert nicht**. Das einzig Verwandte (`activity_positions`) ist leer und rangfrei. **B1 müsste neu** — und müsste die **rang-tragende** Achse (`position_qualifications`) referenzieren, nicht die flache `positions`.

---

## FRAGE 2 — Abstand oder Schwelle? (B3)

**Antwort: WEDER NOCH — nur ein Boolean `<=`.**
- Die „can-perform-below"-Logik (`hierarchyAutoGenerate`) wörtlich:
```php
$allowed = (int) $performer->sort_order <= (int) $required->sort_order;
PositionQualificationHierarchy::updateOrCreate([...], [
    'allowed' => $allowed, 'efficiency_factor' => 1, 'cost_factor' => 1,   // <-- HART auf 1
]);
```
→ Es **vergleicht** zwar zwei Stufen, gibt aber ein **Boolean „darf/darf nicht"** aus. **`efficiency_factor`/`cost_factor` sind hart = 1** (nicht aus einer Differenz abgeleitet). → **Kein Abstand** (`performer.sort_order − required.sort_order`) wird irgendwo berechnet oder genutzt.
- **Keine Rang-Schwelle „ab Stufe X gilt Y"** im Code (Grep: nur `sort_order` in LeadStage-Autoincrement + `qualification.default_price` als **Lohnsatz** in CostingSet — wieder Costing, keine Autoritäts-Schwelle). Autoritäts-Schwelle heute = **nur `is_admin`** (binär).

**URTEIL F2:** Die vorhandene Logik trägt **B3 NICHT**. Sie ist ein binäres can/can't (mit **manuell** setzbaren, aktuell auf 1 defaulteten Kosten-Faktoren). Weder Yamas **Abstand** („deutlich drüber = prüffrei; nah drunter = Prüfpflicht") noch die einfachere **feste Schwelle** existieren — beide wären neu. Das `allowed`-Bit + `sort_order` sind aber eine **brauchbare Basis**, auf der Abstand ODER Schwelle aufsetzen könnten.

---

## WER-PRÜFT-Achse (supervisor) — kurz bestätigt

Aus `qualifikation-fundament-achsen-analyse.md`: `supervisor` trägt „wer prüft" plausibel und **teils schon real** (`LeaveController` routet Genehmigungen an supervisor; `TimeManagementController` gated Sicht). **ABER Verknüpfung mit Qualifikation ist NICHT gegeben:** „nächsthöher Qualifizierter **im selben Gewerk**" ist **nicht** direkt berechenbar, weil Qualifikation **global** ist (kein Per-Gewerk-Rang) — man könnte nur „nächsthöher **global** Qualifizierter" ODER „supervisor" ermitteln, nicht beides gewerkspezifisch verknüpft. *(NICHT VERIFIZIERT: ob supervisor fachlich = höher qualifiziert ist; die Baum-Daten sagen nichts über das Qualifikations-Verhältnis Chef↔Untergebener.)*

---

## MODELL-URTEIL (ehrlich)

| Baustein | Status im Code | Beleg |
|---|---|---|
| **B1** Aufgabe trägt Mindest-Qualifikation | **FEHLT** (rudimentär: leere, rangfreie `activity_positions`) | kanban/phase_activities: 0 Feld; activity_positions 0 Zeilen → positions |
| **B2** Person hat Qualifikation | **TEILWEISE** — „hat Rang" JA (global, 50/51); **gewerkespezifisch NEIN** | qualification_id (1 Wert), position_qualifications ohne Gewerk, 1 Dept/Person |
| **B3** Prüfpflicht aus Abstand | **FEHLT** (nur Boolean `<=`, efficiency/cost=1, keine Schwelle) | hierarchyAutoGenerate wörtlich |
| Wer prüft (supervisor) | **VORHANDEN + genutzt** (aber nicht mit Qualifikation gekoppelt) | LeaveController/TimeManagement |
| Können/Dürfen-Trennung (Modell-Kern) | **ANGELEGT** — Qualifikation (position_qualifications) ≠ Rechte (user_rolls/is_admin) | Vorbefund |

**Wo das Modell PASST:** Die **Grund-Trennung „Können (Qualifikation) vs. Dürfen (Autorität)"** ist im Code real angelegt (Qualifikations-Achse getrennt von der Rechte-Achse `user_rolls`/`is_admin`). Und „wer prüft = supervisor" trifft auf eine **schon gelebte** Genehmigungs-Achse.

**Wo das Modell NICHT passt / zu kompliziert ist:**
- **B2 gewerkespezifisch ist ÜBERKONSTRUIERT für den Ist-Zustand:** die Daten kennen 1 Gewerk + 1 globalen Rang je Person; eine Per-Gewerk-Qualifikations-Matrix wäre viel neue Struktur für ein Szenario (MA über mehrere Gewerke gestuft), das es **heute nicht gibt**. Yama sollte prüfen, ob das real gebraucht wird oder ob **global** reicht.
- **Die Qualifikations-Achse ist Costing-verwoben:** `position_qualifications` trägt `default_price`, die Hierarchie wird in Offer/Deal für **Kosten** genutzt (`efficiency_factor`/`cost_factor`). Das Modell behandelt sie als reine Autoritäts-Achse — sie ist es nicht. Autorität hier draufzusetzen **überlädt ein Kosten-Modell**.
- **Zwei Rang-Vokabulare** (flache `positions` 24 vs. rang-`position_qualifications` 26) — das Modell reconciled sie nicht; B1 (`activity_positions`→positions) und B2/B3 (position_qualifications) zeigen **auf verschiedene Tabellen**.

**Wo das Modell zu EINFACH ist:** Es nimmt „eine Qualifikation je Person" an — was global stimmt, aber die **department/position-Ebene** (mit `department_head`/`parent_id`-Baum = eine **dritte** Hierarchie!) ignoriert. Die Realität hat mehr parallele Achsen als B1/B2/B3.

---

## Kleinster sinnvoller erster Baustein (variant-unabhängig)

**= B1: Aufgaben/Tätigkeitsarten eine „benötigte Qualifikations-Stufe" geben, die auf `position_qualifications` (rang-tragend) zeigt.**

Begründung: **Egal ob Abstand oder feste Schwelle, egal ob global oder gewerkespezifisch** — ohne eine **Anforderung an der Aufgabe** gibt es **nichts zu vergleichen** (weder Schwelle noch Abstand berechenbar). Die Personen-Seite (B2) existiert schon (global, 50/51 verknüpft); die **Aufgaben-Seite (B1) fehlt komplett**. B1 ist damit der einzige Baustein, der in **jeder** Modell-Variante zwingend gebraucht wird und heute **gar nicht** da ist. *(Sekundär, aber ebenfalls variant-unabhängig: die Rang-Ordnung von `position_qualifications` von den Costing-Ties/-Preisen entkoppeln — s. Achsen-Analyse Punkt 9.)*

**Bewusst offen für Yama (nicht entschieden):** (1) global ODER gewerkespezifisch (F1 sagt: heute global, gewerkespezifisch wäre Neubau ohne aktuellen Bedarf); (2) Abstand ODER feste Schwelle (F2 sagt: beides neu, `sort_order`+`allowed` als Basis); (3) Prüfer = supervisor (gelebt) ODER „fachlich Höherer" (nicht berechenbar, da global) ODER beides.

---

## Gelesen / NICHT gelesen (ehrlich)
**Geprüft (wörtlich/live):** `employees.qualification_id` (1 Spalte, global); `position_qualifications` DESCRIBE (kein Gewerk-Feld); `departments` Spalten+Inhalt (16, Gewerke+Org, `parent_id`/`department_head`); `department_positions` (50, 0 MA mit >1 Dept); `activity_positions` Struktur (→positions, 0 Zeilen); `kanban_lead_tasks`/`phase_activities` Spalten (0 qualif/skill/level); `hierarchyAutoGenerate` wörtlich (Boolean `<=`, efficiency/cost=1); Schwellen-Grep (nur is_admin/costing); supervisor-Nutzung (aus Vor-Doc).

**NUR gegrept / NICHT VERIFIZIERT:**
- Ob `activity_positions` als B1 **gedacht** war (leer → verlassen oder Zukunft, nicht unterscheidbar).
- Ob „1 Gewerk/Person" hart ist (Schema erlaubt mehr; nur aktueller Datenstand geprüft).
- Ob `departments.department_head`/`parent_id` eine **genutzte** Org-Hierarchie bilden (Spalten gefunden; Nutzung nicht durchtracet — potenziell die „Abteilungsleiter"-Quelle).
- Ob supervisor fachlich = höher qualifiziert (Qualifikations-Verhältnis Chef↔MA ungeprüft).

## Selbstkritik / Risiken
- **Ich prüfe hier ein Modell, das teils von mir selbst stammt** — Tendenz, es zu bestätigen. Gegengesteuert, indem ich alle drei Kern-Annahmen (gewerkespezifisch/Abstand/B1) **hart gegen Daten** gestellt habe; alle drei fallen. Das Modell ist konzeptionell sauber, aber **weiter von der Code-Realität entfernt, als es klingt**.
- **„B2 überkonstruiert" ist ein Urteil** — falls Yamas Betrieb real MA über mehrere Gewerke stuft (nur heute nicht in den Daten), wäre die Per-Gewerk-Struktur berechtigt. Ich kann nur sagen: **heute nicht abgebildet und nicht gebraucht.**
- **Der kleinste Baustein B1 setzt voraus, dass die Aufgaben-Ebene die richtige Anknüpfung ist** — es wäre auch denkbar, die Anforderung an `phase_activities` (Template) statt an `kanban_lead_tasks` (Instanz) zu hängen. Das ist eine offene Modellierungs-Frage, die ich hier nur benenne.

---

*Reine Analyse — nichts geändert. Querverweise: `qualifikation-fundament-achsen-analyse.md` (Schritt 1), `mitarbeiter-hierarchie-bestandsaufnahme.md` (f63d015), `architektur-entscheidungen.md` (Weiche 6).*
