# KONZEPT — Evidenzbasierte Planung

**Gültig ab 30.07.2026, 07:30 CEST. Von Yama vorgegeben, vom Planner in den Bestand eingepasst.**

> Dieses Konzept **ersetzt** `docs/planner/massnahmenplan-2026-07-30.md` als operative Grundlage.
> Der Maßnahmenplan bleibt als Vorstufe im Repo — er war richtig in der Richtung und zu dünn in
> der Konstruktion.

---

## 0. Der Kernbefund, den ich annehme

**Die Ursache ist kein Charaktermangel, sondern ein schlecht konstruiertes Arbeitssystem.**

> *Menschen und Agenten sollen sorgfältig handeln, obwohl der Prozess schnelles Schreiben belohnt,
> Regeln versteckt, Messungen veralten lässt und Fehler nicht mechanisch verhindert.*

**Das aktuelle System vermischt fünf Tätigkeiten in einem Rhythmus:** beobachten · Bestand
verstehen · entscheiden · Auftrag formulieren · Fortschritt melden. Der 3-Minuten-Takt drückt alle
fünf in denselben Takt. **Der Planner optimiert dadurch auf sichtbare Aktivität statt auf
Entscheidungsqualität.**

**Das deckt sich mit der Messung dieses Tages** und erklärt sie besser als meine eigene Diagnose:
fünf Rückweisungen, vier bereits erfüllte Kriterien, 1 139 Zeilen Auftragstext, neun Regeln an
einem Ort, an dem sie niemand liest.

---

## 1. Die neue Grundformel

```text
Bestand → Messung → Abweichung → Entscheidung → erst dann Auftrag
```

**Nicht:** `Idee → Beschreibung → Generator soll den Bestand prüfen`

*Genau diese falsche Reihenfolge hat heute dreimal zu einer Rückweisung geführt — und jedes Mal
war es der Generator, der den Bestand nachgeholt hat, den ich hätte messen müssen.*

---

## 2. Die Rolle des Planners, neu definiert

**Der Planner ist nicht derjenige, der am meisten Text schreibt.** Seine zentrale Leistung ist:

> **Die Reduzierung von Unsicherheit vor Beginn der Implementierung.**

Er verantwortet: korrekten Ist-Zustand · klare Abgrenzung · Abhängigkeitsverständnis ·
Risikobewertung · Prüfbarkeit · Priorisierung · **Vermeidung unnötiger Arbeit** · Schutz
bestehender Funktionen · eindeutige Übergabe.

### Erfolg wird ab sofort so gemessen

**Nicht:** Anzahl Blätter · Umfang · Antwortgeschwindigkeit.

**Sondern (Ausgangswerte vom 30.07., 00:00–07:20):**

| Kennzahl | heute | Ziel bis 06.08. |
|---|---|---|
| Rejection Rate (zurückgewiesene Aufträge) | **5** | ≤ 1/Tag |
| Already-Satisfied Rate (Kriterien schon erfüllt) | **4** | **0** |
| Stale-Measurement Rate (veraltete Zahlen) | **5** (F-04) | **0** |
| Unverifiable-Criterion Rate | nicht erhoben | wird ab AUF-87 erhoben |
| Conflict-Miss Rate (übersehene Ticket-Konflikte) | **1** (AUF-70) | **0** |
| Scope-Change Rate (nachträgliche Planänderungen) | **3** | ≤ 1 |
| No-Build-Detection Rate (vermiedene Arbeit) | **4 Kriterien + 1 Auftrag** | steigend |

---

## 3. Korrektur meiner eigenen Regel R19 — sie war zu grob

**Mein Verbot „keine Zahl mehr im Auftrag" löst ein Problem und erzeugt ein neues.** Zahlen sind
oft notwendig: betroffene Routen, Dateien, Testanzahl, Performancegrenze, Objektpopulation.

**Das Problem ist nicht die Zahl. Das Problem ist die unbelegte oder veraltete Zahl.**

### R19 (neu gefasst) — Messblock statt Verbot

```yaml
measurement:
  command: "grep -c 'data-schiene' resources/planner/hausplaner/app/HausplanerApp.tsx"
  observed_value: 2
  observed_at_commit: "39855b52"
  observed_at: "2026-07-30T07:17:00+02:00"
  freshness_rule: "must_match_current_head"
  purpose: "scope boundary"
```

> **Keine behauptete Zahl ohne reproduzierbare Messung und Bindung an einen konkreten
> Repository-Stand.**

**Vor Ausführung wird die Messung automatisch wiederholt.** Weicht `observed_at_commit` vom
aktuellen HEAD ab, ist der Auftrag **nicht planungsreif** — es sei denn, die Wiederholung liefert
denselben Wert.

*Das ist besser als mein Verbot: es erhält die Information und bindet sie an einen Beweis.
`population_at_writing_ALT` in den vier offenen Blättern wird auf diese Form umgestellt.*

---

## 4. Vier mechanische Gates vor jedem Auftrag

### Gate A — Bestandsnachweis

**Nur Suchtreffer reichen nicht.** Der Planner unterscheidet:
`gesucht → gefunden → gelesen → verstanden → bewiesen`

```yaml
inventory:
  files_read:
    - path: "..."
      reason: "..."
  existing_behaviour:
    - claim: "..."
      evidence: { file: "...", lines: "...", test: "..." }
  related_tickets:
    - { id: "AUF-70", relationship: "conflict" }
```

*Hätte Gate A am 30.07. um 06:14 gegolten, wäre `AUF-70` als `conflict` aufgefallen, bevor das
Blatt lag — statt zwei Stunden später durch den Generator.*

### Gate B — Abweichungsnachweis

```yaml
gap:
  existing:  ["..."]
  missing:   ["..."]
  must_preserve: ["..."]
```

**Ohne nachgewiesene Abweichung kein Auftrag.** *Vier Kriterien an AUF-83-T3 hätten Gate B nicht
passiert — sie standen unter `existing`, nicht unter `missing`.*

### Gate C — Wirkungs- und Abhängigkeitsnachweis

```text
Änderung → direkte Wirkung → indirekte Wirkung → mögliche Regression → notwendiger Gegenbeweis
```

Beispiel aus unserem Bestand:

```text
Fensterbreite ändern
  → Wandöffnung ändert sich
  → Netto-Wandfläche ändert sich
  → Heizlast wird veraltet
  → Mengen werden veraltet
  → 2D und 3D müssen synchron bleiben
```

### Gate D — Prüfbarkeitsnachweis

**Nicht zulässig:** *„Die Bedienung soll besser sein."* · *„Das Layout soll professionell wirken."*

**Zulässig:**

```yaml
criterion:
  id: C-04
  statement: "Escape stellt den Zustand vor dem Drag wieder her."
  verification: { type: e2e, command: "npm run test:hausplaner:dom -- --filter=move-wall-cancel" }
  counterexample:
    action: "Drag starten, Geometrie verändern, Escape drücken"
    expected: "Dokumenthash entspricht Ausgangszustand"
```

---

## 5. Beweisbudget statt Textbudget

| Bereich | Mindestnachweis |
|---|---|
| Bestand | Code **und** vorhandene Tests gelesen |
| Verhalten | aktueller Zustand reproduziert |
| Scope | betroffene **und** ausgeschlossene Dateien |
| Konflikte | Tickets, Zusagen, offene Arbeiten |
| Abhängigkeiten | direkte und indirekte Wirkungen |
| Prüfbarkeit | Kriterien mit Befehlen |
| Regression | mindestens ein Gegenbeweis |
| Risiko | Daten, Rechte, Migration, UI, Performance |

**Fehlt ein Beleg, lautet der Status `NICHT PLANUNGSREIF`** — nicht *„der Planner soll vorsichtiger
sein"*.

---

## 6. Drei getrennte Phasen

```text
Phase 1  DISCOVERY   read-only. Kein Bauauftrag erlaubt.      → discovery-report.md
Phase 2  DECISION    kein Bau / korrigieren / erweitern /
                     konsolidieren / neu bauen                → decision-record.md
Phase 3  BUILD       erst jetzt der Generator-Auftrag         → build-contract.md
```

**Damit wird verhindert, dass sich die gewünschte Lösung schon während der Bestandsaufnahme
verfestigt.** *Genau das ist mir bei AUF-88 fast passiert: der Master-Prompt beschrieb dreizehn
Phasen, und ich hätte sie beinahe als dreizehn Blätter übersetzt, bevor ich gemessen hatte, dass
das BuildingDocument längst existiert.*

**Der Standardfall in Phase 2 ist nicht „neu bauen".**

---

## 7. Neue Rolle: der Plan Reviewer

```text
Planner → Plan Reviewer → Generator → Evaluator
```

**Er prüft ausschließlich den Plan, nicht den Code:** Ist der Bestand korrekt? Ist der Auftrag
notwendig? Sind widersprüchliche Tickets berücksichtigt? Sind Kriterien prüfbar? Ist der Scope
minimal? Sind bestehende Zusagen geschützt? Ist der Auftrag ausführbar?

> **Der Generator darf einen Auftrag ablehnen — aber er sollte nicht der Erste sein, der
> offensichtliche Planungsfehler entdeckt.**

**Was das braucht:** eine vierte Instanz. **Das ist eine Betriebsentscheidung und liegt bei Yama.**
Solange sie nicht existiert, übernimmt der **Evaluator** die Planprüfung als eigenen, vom
Code-Votum getrennten Vorgang — *er ist heute die einzige Rolle, die nachweislich gegen sich selbst
prüft.*

---

## 8. Der Linter — deutlich mehr als bisher geplant

`scripts/auftrag-pruefen.sh` (AUF-87) **blockiert** bei:

- fehlendem aktuellen Commit · fehlenden Bestandsdateien · fehlendem Inventurbefehl
- **Messung auf anderem Commit als HEAD**
- nicht prüfbaren Kriterien · Kriterien ohne Gegenbeweis
- fehlendem Nicht-Ziel · Scope ohne Dateigrenzen · **unbelegter Zahl**
- **Konflikt mit bestehendem Ticket** · Änderung eines bereits abgenommenen Verhaltens
- **neuem Service trotz vorhandenem Bestand**
- fehlendem Rollback- oder Undo-Test · fehlendem Persistenztest
- fehlender 2D-/3D-Prüfung · fehlender Sicherheits- oder Mandantenprüfung

**Beispielausgabe:**

```text
FAIL C-03:      Kriterium enthält „vollständig", aber keinen messbaren Prüfbefehl.
FAIL INVENTORY: Behauptung über 17 Routen gemessen auf 72a1a4a0. HEAD ist 3b6f82c1.
FAIL CONFLICT:  AUF-70 schützt das Verhalten, das C-06 verändern würde.
FAIL REUSE:     Neuer RoofGeometryService geplant, obwohl ExistingRoofGeometryService referenziert ist.
```

**Das Blatt `AUF-87` wird auf diesen Umfang erweitert.** *Es war zu klein geschnitten — drei
Meldungsstufen ohne Konflikt-, Reuse- und Freshness-Prüfung.*

---

## 9. Regelwerk auf drei Ebenen — der Ledger ist Historie

**21 233 Ledger-Zeilen sind kein aktives Regelwerk.**

```text
Ebene 1   docs/agents/regeln/kern.md        max. eine Seite, 10–15 Regeln — IMMER geladen
Ebene 2   docs/agents/regeln/planner.md
          docs/agents/regeln/generator.md
          docs/agents/regeln/evaluator.md
Ebene 3   docs/agents/regeln/{dach,datenbank,sicherheit,dreid,bedienbarkeit,import-export}.md
```

**Der Ledger bleibt Historie.** Er ist Übergabefläche und Beweisarchiv — **kein Regelwerk.**

---

## 10. Aussagetypen dürfen nicht vermischt werden

Jede relevante Aussage wird gekennzeichnet:

```text
FACT · MEASUREMENT · INFERENCE · HYPOTHESIS · DECISION · OPEN QUESTION
```

> **Eine Hypothese darf nicht als Fakt in einen Bauauftrag gelangen.**

*Mein Satz „`arbeitsbereiche.ts` kennt kein Feld für gesperrt" war eine MEASUREMENT. Mein Satz
„die 13-teilige Geschosszeile trägt vier unabhängige Aufgaben" war eine INFERENCE aus einer vier
Tage alten Inventur — und stand ununterscheidbar daneben.*

---

## 11. Generator — mechanische Pflicht

```text
erzeugen → Datei öffnen → erwartete Inhalte prüfen → Syntax prüfen
         → fachliche Wirkung prüfen → erst dann weiter
```

**Nicht:** `Befehl exit 0 → weiter`

Dazu verpflichtend: Auftrag zuerst validieren · bereits erfüllte Kriterien melden · Konflikte
blockieren · kleine Commits · Scope überwachen · Tests **vor und nach** der Änderung.

---

## 12. Evaluator — jede Mutation besteht drei Stufen

1. Datei bleibt **syntaktisch und technisch ladbar**.
2. Mutation verändert **gezielt** die behauptete Eigenschaft.
3. Der **erwartete** Test schlägt **aus dem richtigen Grund** fehl.

```yaml
mutation:
  target_claim: "Tür bleibt an Wand gebunden"
  mutation: "hostId nach Verschiebung nicht aktualisieren"
  load_check: "npm run tsc:hausplaner"
  targeted_failure: "door-host-binding.test.ts"
  unrelated_failures_allowed: false
```

> **Ein Rot durch Syntaxfehler ist kein Gegenbeweis.**

---

## 13. Kommunikations-SLA — Stille erzeugt unnötige Parallelität

**Ein eiliger Auftrag braucht keine sofortige Lösung, aber eine sofortige Quittung.**

```text
Empfangen → verstanden / unklar → blockiert / eingeplant → nächster erwarteter Status
```

Beispiel: *„AUF-91 empfangen. Status: blockiert durch laufende Evaluation AUF-90. Nächster Status
nach Abschluss der Gegenproben."*

*`EVAL-2026-07-30-A` lag heute 40 Minuten ohne Empfangszeile — und ich habe in der Zeit einen
Wettlauf konstruiert, den es nicht gab.*

---

## 14. Rhythmus — ereignisbasiert statt taktgebunden

| | |
|---|---|
| **Wache** | alle drei Minuten: Meldungen lesen, Git-Status, Blocker aufnehmen, **Quittungen senden** |
| **Denken** | ohne Veröffentlichungszwang: lesen, messen, Konflikte prüfen, **Hypothesen widerlegen** |
| **Schreiben** | **ereignisbasiert — ein Auftrag entsteht, wenn das Readiness-Gate grün ist.** Kein Zeitziel, weder Mindest- noch Höchstmaß |

**Das ersetzt meine Selbstauflage „höchstens ein Blatt pro Stunde".** *Eine Stundengrenze ist
wieder ein Zeitmaß — und Zeitmaße waren die Ursache.*

---

## 15. Umsetzung — priorisiert

### Sofort (ohne Bau)

1. ~~R10–R18 ins operative Regelwerk~~ — **erledigt 30.07., 07:08**
2. **Schreibrate vom Wachrhythmus entkoppeln** — erledigt, jetzt ereignisbasiert statt stündlich
3. **Planungsreife-Gate einführen** — `PLANNING-BLOCKED` als Status
4. **Jede Zahl an Befehl und Commit binden** — R19 neu gefasst
5. **Plan Reviewer einführen** — *braucht eine vierte Instanz, Entscheidung bei Yama*
6. **Erzeugte Inhalte statt Exitcodes prüfen** — steht als R20
7. **Mutations-Load-Check verpflichtend** — steht als R21
8. **Empfangsquittung für eilige Aufträge** — neu, als R23

### Danach (mit Bau)

9. `auftrag-pruefen.sh` als **echter Linter** — AUF-87, erweitert
10. Discovery-, Gap- und Decision-Phase trennen — Vorlagen
11. Planner-Skill-System (`skills/planner-deep-analysis/`)
12. Kennzahlen automatisch erfassen
13. Regelverletzungen im CI blockieren
14. Beispiele guter und zurückgewiesener Pläne sammeln

---

## 16. Was ich davon annehme, und was offen bleibt

**Ich nehme alles an.** Zwei Punkte korrigieren ausdrücklich meine eigene Arbeit von heute früh,
und beide zu Recht:

- **R19 war zu grob.** Der Messblock ist besser als das Verbot.
- **Die Stundengrenze war wieder ein Zeitmaß.** Ereignisbasiert ist richtig.

**Zwei Punkte brauchen eine Entscheidung von Yama:**

1. **Der Plan Reviewer braucht eine vierte Instanz.** Bis dahin übernimmt der Evaluator die
   Planprüfung als getrennten Vorgang.
2. **Die Umsetzungspunkte 11–14 sind selbst mehrere Tage Arbeit** und konkurrieren mit der
   Layout-Kette und der Dateiplattform. Reihenfolge gehört Yama.

**Und der Satz, an dem ich mich messen lassen muss:**

> *Der Planner muss zum evidenzgebundenen Systemarchitekten werden, nicht zum schnellsten Autor
> im Team.*
