# INVENTUR-VERFAHREN — ständige Fortschritts- und Fehler-Inventur

> Teil des [Regelwerks](REGISTER.md) · Roster: [AGENTEN-UND-SKILLS.md](AGENTEN-UND-SKILLS.md)
> **Angelegt:** 20.08.2026 auf Yamas Auftrag. Ändert keine ARBEITSREGELN-Regel; wo beide sprechen,
> gewinnt [`docs/ARBEITSREGELN.md`](../ARBEITSREGELN.md).

## Zweck

Zwei wiederkehrende Fragen, ein Verfahren: **Wo stehen wir?** (Fortschritts-Inventur) und
**Was ist kaputt, doppelt, unplausibel, inkonsistent, abgerissen?** (Fehler-Inventur mit
Fahrplan). Gefunden wird durch Agenten, entschieden wird durch Yama, gebaut wird über die
Rollenkette — **die Inventur behebt nichts selbst.**

## Wann sie läuft

**Auf Zuruf oder zum Sitzungsende — nicht auf Zeitplan.** Dieselbe Begründung wie bei der
Repo-Aufsicht im Governance-Zyklus: der riskante Moment ist der nach getaner Arbeit, nicht eine
Uhrzeit; und eine Automatik, die regelmäßig ins Leere läuft, gewöhnt ans Wegwischen. Der Dirigent
(Hauptsitzung) startet den Lauf und wählt den Zuschnitt.

## Der Lauf — vier Phasen

**Phase 1 — Zuschnitt (Dirigent, Minuten):** Zone(n) wählen, nie „alles auf einmal":

| Zone | Umriss |
|---|---|
| Z1 Hausplaner-Insel | `resources/planner/hausplaner/` (gemessen 20.08.: 338 TS-Dateien) — Engines, Szene, Werkzeuge, Panels |
| Z2 CRM-Routen & Rechte | `routes/web.php` je Präfix + Controller + `permission:`-Bindung |
| Z3 Belegkette | Angebot → Auftrag → Rechnung: Models, Hooks, PDF, abgeleitete Werte |
| Z4 DB & Migrationen | additiv? Bestandsdaten-Risiko? Seeds gegen Testdatenbanken? |
| Z5 Docs & Prozess | Register aktuell? STATUS-Drift? Blätter, die dem Code widersprechen |

**Phase 2 — Finder, parallel und gegenseitig blind (je Zone bis zu 6):**
`fehler-finder` · `redundanz-finder` · `konsistenz-finder` · `kausalitaets-finder` ·
`plausibilitaets-finder` · `routing-finder`. Jeder bekommt im Prompt: Zone, Nicht-Ziele, und den
Satz „nur deine Linse". Nicht jede Zone braucht alle sechs (Z4 braucht selten den
Plausibilitäts-Finder, Z1 selten den Routing-Finder) — der Dirigent besetzt fähigkeitsbasiert,
keine Maximalbesetzung.

**Phase 3 — Synthese:** `inventur-schreiber` dedupliziert über die Linsen, gleicht gegen
`docs/backlog/` und `docs/STATUS.md` ab, gewichtet Wirkung × Aufwand und entwirft den **Fahrplan
in Wellen** — als ENTWURF. Fachentscheidungen (Normwerte, Geld, Recht) werden als Yama-Posten
ausgewiesen, nie eingeplant.

**Phase 4 — Ablage und Entscheidung:** Der Planner (Hauptsitzung) legt ab:
Befunde → `docs/backlog/inventur-JJJJ-MM-TT-<zone>.md` · Fahrplan-ENTWURF →
`docs/backlog/fahrplan-JJJJ-MM-TT.md` · Fortschrittsbericht des Laufs →
`docs/fortschritt/inventur-JJJJ-MM-TT.md`. **Yama entscheidet den Fahrplan**; aus entschiedenen
Posten schneidet der Planner Aufträge nach §5 — ab dort gilt die normale Rollenkette samt
Statusführung in `docs/STATUS.md`.

## Fortschritts-Inventur (der zweite Zweig)

Besetzung: `repo-inventur` (Ist-Stand: was existiert, was hat Verbraucher, was ist tot) +
`qualitaets-pruefer` (Abgleich Behauptung↔Bestand: sagen STATUS/Auftragstafel/Register die
Wahrheit über den Code?). Ergebnis nach `docs/fortschritt/`, Abweichungen als Befund nach Phase 4.

## Token-Regeln — verbindlich für jeden Lauf

1. **Zone statt Repo.** Kein Finder bekommt „das ganze Projekt". Lieber drei kleine Läufe als ein
   erschöpfender.
2. **Grep vor Read, Trefferzeilen statt Dateien.** Ein Bericht zitiert Belegzeilen, nie Dateiinhalte
   am Stück.
3. **Dedupe vor Meldung.** Jeder Finder liest zuerst das Backlog-Register; Bekanntes ist Verweis,
   kein Neufund.
4. **Besetzung nach Bedarf.** Nicht sechs Finder aus Prinzip — der Dirigent begründet die Besetzung
   in einer Zeile.
5. **Synthese nur einmal je Lauf.** Der teuerste Agent (Opus) läuft genau einmal, am Ende.
6. **Register statt Volltextsuche.** Agenten greifen über `docs/REGISTER.md` zu; wer sucht statt
   zu greifen, verbrennt Kontext.
7. **Abbruch ist ein Ergebnis.** Sprengt eine Zone das Budget, wird geteilt statt durchgezogen —
   und der Schnitt im Bericht genannt (keine stillen Kappungen).

## Modell-Zuordnung — welches Claude-Modell für welche Aufgabe

Grundsatz: **das Modell folgt der Fehlerkostenhöhe, nicht der Bequemlichkeit.** Am LIVE-System
(~3000 Kunden) wird beim Bauen und bei Sicherheit nicht gespart; gespart wird bei mechanischer
Breite.

| Stufe | Modell | Aufgaben | Agenten |
|---|---|---|---|
| Mechanik | **haiku** | Zähl- und Listenläufe mit fester Anweisung, Registerpflege-Checks | (bei Bedarf je Auftrag gesetzt, kein Dauer-Agent) |
| Breite | **sonnet** | Linsen-Finder, Handwerks- und Fach-Linsen, Standard-Reviews, Nachprüfungen mit engem Auftrag | die 6 Finder · dachdeckermeister · zimmermannmeister · maurer · statiker · ux-designer · qualitaets-pruefer · repo-inventur · test-reviewer · planner-architect · ticket-reuse-reviewer |
| Tiefe | **opus** | Synthese/Fahrplan, Sicherheit, Architektur-Urteile, adversariale Erst-Planprüfung | inventur-schreiber · security-reviewer · software-architekt |
| Voll | **Sitzungsmodell (erbt)** | Bauen am LIVE-System, Abnahmen, Integration, alles mit Schreibrechten | backend-/frontend-/fullstack-entwickler · Hauptsitzung (Dirigent/Planner) |

Regeln dazu: **(1)** Die Stufe steht als `model:` im Agenten-Frontmatter — wer einen Agenten ruft,
wählt nicht spontan billiger. Hochstufen im Einzelfall ist erlaubt, Runterstufen nicht.
**(2)** Eine Zweitrunde mit engem Auftrag (z.B. „prüfe nur R1–R10") darf eine Stufe unter der
Erstrunde laufen — der enge Auftrag ersetzt die Modelltiefe. **(3)** Bauende Agenten erben immer
das Sitzungsmodell; am LIVE-System ist das billigste Modell das, das keinen Fehler einbaut.
