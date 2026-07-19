# Arbeitskompass — ticket

**Stand:** 2026-07-19
**Zweck:** Diese Datei beantwortet jederzeit die Fragen: *Woran arbeiten wir gerade? Wann ist es fertig? Was kommt als Nächstes? Was ist geparkt?*

> **Rang/Funktion:** Dieser Arbeitskompass ist ein **Navigations- und Statusdokument**. Er verweist auf die führenden Fahrpläne, ADRs und Startblöcke. Er **ersetzt oder überstimmt keine ranghöhere Governance** (BETRIEBSORDNUNG, `CLAUDE.md`, `STRAENGE.md`, ratifizierte ADRs) und **bestimmt nicht eigenständig den Scope eines Umsetzungsslices** — das tut der freigegebene **Startblock** des aktiven Slices. Bei Widersprüchen gilt die festgelegte Quellenhierarchie.

> **Regel:** Wenn eine größere Aufgabe beginnt oder endet, wird diese Datei zuerst/zuletzt aktualisiert. Keine neue große Baustelle ohne Eintrag hier oder bewusste Yama-Entscheidung.

> **Systemweite Methode:** **① konzeptionell optimieren → ② Workflow bestimmen → ③ vorhandene Bausteine verknüpfen → ④ erst dann automatisieren.** Keine Automatisierung vor Konzept + Workflow + Verknüpfungsplan. Keine zweite Berechnung, keine zweite Wahrheit, kein UI-Flicken über falscher Fachlogik.
> Details: [`docs/systemoptimierung-fahrplan.md`](systemoptimierung-fahrplan.md) ist verbindlich für Inventur, kritische Bewertung, Optimierungsvorschlag, Verknüpfung, Workflow, Automatisierung, Umsetzung und Abnahme. Vorher gilt [`docs/system-kapitelplan.md`](system-kapitelplan.md): erst das gesamte System in Kapitel/Domänen einordnen, dann einzelne Kapitel tief bearbeiten.
> Vor jedem Kapitel/Slice liefert Claude Code zuerst einen **Startblock**: Wo fängt er an, was macht er, was ist das Ziel, welche Dateien/Services liest er, was macht er nicht, wo endet das Kapitel. **Standard-Arbeitsmodus:** Planner → Generator → unabhängiger Evaluator; kein automatischer Folgeslice.

---

## Aktueller Arbeitsstand — Stand 2026-07-15

**Systemweite Bestandsaufnahme abgeschlossen.** Die systemweite Read-only-Analyse für Konfiguration, Auslegung und 3D-Gebäudeplaner ist **abgeschlossen, unabhängig evaluiert und unter [`docs/configuration/`](configuration/) dokumentiert** (ADR-0001 „Bedarf führt, Produkt folgt", Gesamtarchitektur, Anforderungs-/Lückenmatrix, Modul-/Abhängigkeitsmatrix, Umsetzungsfahrplan, Vollständigkeitsbericht, Evaluator-Akte). Sie bildet die Architektur- und Lückengrundlage für die folgenden Slices.

### Aktiver Strang: WP Stufe 3b — kontrollierte Folgeintegration

**Abgeschlossen (committet/geprüft):**
- WP Stufe 3a — Auslegungsketten-Orchestrator (`WpAuslegungsketteService`, service-only).
- P0 — Stale-Konflikthärtung der Anforderungsprofil-Versionskette.
- P1a — Charakterisierung der bestehenden WP-Ergebnis-/Kosten-/Förder-/Dokument-/Heizlastpfade (Golden Master).
- P1b-1 — verhaltensgleiche Extraktion der Kostenlogik (`WpCostingService`).

**Nächster möglicher Slice:**
- P1b-2 — verhaltensgleiche Extraktion der Förderungslogik (`WpFundingAssessmentService`). **Wird hier NICHT freigegeben** — nur mit eigenem Startblock + Yama-Freigabe.

**Gesperrt (jeweils nur mit eigenem Startblock):**
- P1b-3 — Dokument-/View-Model-Service.
- P1b-4 — Gesamtparität und Restduplikation.
- M-1 — paralleles erstmaliges Profilanlegen (Race/Unique-Index, additive Migration).
- M-2 — HTTP-409-Vertrag für stale Profilversionen.
- P1c — Orchestrator-Verdrahtung, Ranking, Auswahl und UI (inkl. Korrektur der Geräte-Unabhängigkeit A1).

### Nebenstrang: Hausplaner + FiBu Stufe (ii) — Stand 2026-07-19

**Committet (bis `d99cbb7`), jeweils unabhängig evaluiert:**
- `c3fcf75` — Hausplaner Save-Fix (B2/T2): v2+Dach wird persistiert, kein stiller Verlust mehr.
  Persistenz über `$request->input('scene')` (ungeschnitten) statt der von `validate()` beschnittenen
  Nutzlast; Gate hält (422 an vier Kanten, schreibt dabei nichts), 409-Konflikt und Snapshot geprüft.
- `77e25f7` — FiBu Stufe (ii): Kontenrahmen-Seed marker-vollständig (`accounting_clients` trägt jetzt
  `imported_from`), Rückbau restlos; Eigenschafts-Test mit belegter Diskriminierung.
- `e9048da` — Hausplaner UI: helle CRM-CI, Dach-Optik, Studio-Einstieg in der Tools-Navi.
  **Achtung:** Dieser Commit hat den „Hausplaner öffnen"-Button aus der Gebäudeakte entfernt.
- `da7db05` / `d99cbb7` — Existing-Code-First-Regel + Skills/Agenten; Accounting-/Planner-Doku.

**Lehre aus vier Evaluator-Runden (gilt weiter):** Ein grüner Test ist kein Beleg, solange nicht
gezeigt ist, dass er *fehlschlagen kann*. Dreimal in Folge waren mitgelieferte Tests ohne
Diskriminierungskraft (Hash-Abbruch vor der ersten Assertion; zirkuläre Marker-Assertion). Seitdem
ist der Gegen-Beweis — Fix zurückdrehen, Test muss rot werden — fester Bestandteil jeder Abnahme.

**In Arbeit (uncommittet, Evaluator läuft):**
- Scheibe 1 — P2 Größen-Cap (`MAX_SCENE_BYTES = 2_000_000`, Check vor der Action). **Abgenommen**,
  wartet auf Commit-Freigabe.
- Scheibe 2 — Gebäude-Auswahl (`hausplaner.index`) als Ersatz für den entfernten Akte-Button, plus
  Reuse-Fix: kanonischer Scope `LeadAlternativeAdd::scopeGebaeudeSuche` statt der Kopie von
  `Customer/ObjektakteController::index`. **Risiko:** der Reuse-Fix fasst produktiven Bestandscode
  ohne vorherige Testabdeckung an — `/objekte` muss unbeschädigt bleiben.

**Offen — Entscheidungen bei Yama:**
- **Sichtbarkeit:** Darf jeder mit `Hausplaner,read` *jedes* Gebäude sehen? `lead_alternative_adds`
  hat keinen Mandanten-/Filial-Anker; `/objekte` ist heute sogar nur `auth`. Empfehlung: konsistent
  zu `/objekte` lassen; eine echte Mandantensicht ist ein eigenes Fundament-Projekt (Anker-Spalte +
  Global Scope) und betrifft dann alle Objektlisten, nicht nur den Hausplaner.
- **B6/C2 — Browser-Abnahme steht aus** (helle CI, Satteldach-Render, PAGEERROR-Stack). Die
  Hypothese „PAGEERROR stammt aus dem Alt-Bundle `public/planer/planer.js`" ist auf Quellebene
  **widerlegt**: beide Bundles haben Null-Guards am Mount, kein `addEventListener` hängt an einem
  ungeprüften DOM-Lookup. Ursache weiter unbekannt, braucht echten Browserlauf.
- **P3 Styleguide-Tokens:** `studio.blade.php` und `main.tsx` nutzen 15 Hex-Werte. Nur 5 haben ein
  `--sa-*`-Token; für Neutral-/Ink-/Border-Farben existiert **gar keins**. Konsequenz nach
  UI-Bauordnung: erst die fehlenden Tokens im Styleguide anlegen, dann umstellen — eigener Posten.
- **Alt-Bundle `public/planer/planer.js`** (16.07., nur noch von `hausplaner.dachplaner` geladen):
  zweites React-Bundle neben dem aktuellen. Kandidat für Abriss mit Rückfallpfad, eigener Posten.

**Fortschreibung 2026-07-19 (spät):**
- **Abgeschlossen + committet:** Abriss Alt-Dachplaner (`a88845e`, Konserve `1cba744` davor),
  P4 Warn-Tokens (`52f1254`), Navi-Aufräumung −76 Stubs (`d596ce3`), Zielbild EIN Planer
  (`2634caa`), **W-A Übernehmen-Knopf (`7b18ed4`)** — alle unabhängig evaluiert.
- **Nullpunkt-Messwelle gefahren** (echter Browser via Puppeteer, ticket.test):
  Bericht + Referenzszenen in `~/Documents/planner-handover/referenzszenen/`.
  Kernbefunde: SP-5-PAGEERROR stammt aus der CRM-Shell (chat-Bundle), nicht dem Planer;
  **Studio lädt nie** (projectId 0 vs. Zod positive — ROT, Spec liegt vor); 8d70e4b formal
  abgenommen; B1-10-Minuten-Test wartet auf Yama als Proband.
- **Specs vorgelegt** (planner-handover/): Render-Welle 1 (RoomEnvironment + eine Sonne),
  Studio-Fix (1 Blade-Zeile), UX-Welle 1 + Render-Welle 2 in Arbeit.
- **Backlog FS-4–FS-9** nachgetragen (`docs/_playground-archiv/_auftraege/folge-scheiben-backlog.md`):
  Giebeldreiecke, --sa-ink-Token, Orbit/Pick, Konflikt-Dialog zweiarmig, Lead-500 ohne
  Reverb, Shell-Konsole.

---

## WP-Fachgrundsätze (dauerhaft)

Die Auslegung ist **bedarfsgeführt, nicht produktgeführt.** Der Benutzer wählt nicht zuerst eine Wärmepumpe. Reihenfolge:

```text
1. Objekt erfassen
2. PLZ / Ort als Klimadaten-Schlüssel erfassen (bzw. aus Objekt/Kundenkontext übernehmen)
3. Verbrauch / Bestand / Nutzerverhalten erfassen
4. technische Randbedingungen erfassen
5. Heizlast / Energiebedarf berechnen
6. mehrere passende Wärmepumpen-Alternativen als Ranking vorschlagen
7. je Alternative Bivalenz, Deckungsanteil, E-Stab-Anteil, Betriebsstunden, JAZ und Stromverbrauch bewerten
8. empfohlene Alternative begründet in Angebot/Kalkulation übernehmen
```

Der Zustand „erst Wärmepumpe auswählen, danach Verbrauch/Objekt" ist fachlich falsch und darf nicht als Ziel-Workflow übernommen werden (deckt ADR-0001; Korrektur der heutigen Geräte-Unabhängigkeit A1 ist P1c).

- **Bedarf und Operanden zuerst; Hersteller/Produkt sind Filter bzw. Ergebnis, nicht Startanker.**
- **Klima-/Standortdaten**, soweit für die Berechnung erforderlich (NAT, Heizgradtage, mittlere Außentemperatur, Klima-Bins), aus PLZ/Ort ableiten. Ohne belastbare Klimadaten ist die Auslegung nur eine grobe Schätzung.
- **Reuse statt Neubau — führende Bausteine nutzen, nicht parallel implementieren:** `KlimaPlzService`/`klima_plz`, `KlimaBinService`, `VerbrauchsService`, `HeizlastRechner`/`HeizlastProjektService`, `WaermepumpenMatchService`, `WpKennlinieService`, `BivalenzService`, `CatalogDeviceRepository` (eine Katalog-Wahrheit). Der Wizard baut diese Logik **nicht** in JavaScript nach — er sammelt Eingaben, ruft Backend-Services und zeigt Ergebnis/Ranking.
- **Keine zweite Rechenwahrheit.**

**Automatisierung nur mit Operanden-Gate:** Eine serverseitige Vor-Auslegung darf **nur** erfolgen, wenn die erforderlichen Operanden vollständig und ausreichend belastbar sind. Sie erzeugt einen **nachvollziehbaren Vorschlag mit Datenqualitäts- und Warnhinweisen**, trifft **keine verbindliche Produktauswahl**; **Nutzerbestätigung und technische Freigabe bleiben erforderlich**. **Keine stillen Defaults** für fachlich erforderliche Operanden. Fehlende Pflichtdaten werden als Arbeitslisten-/Follow-up-Hinweis sichtbar gemacht.

---

## Danach — nächste große Themen (Reihenfolge-Disziplin)

Nach dem WP-Strang kommt nicht automatisch die nächste spontane Baustelle. Grobreihenfolge (je eigener Startblock, Yama entscheidet):

1. WP Stufe 3b weiterführen (P1b-2 ff.) **oder** bewusst parken.
2. Rückfluss Montage/Planner → Büro aus [`docs/fahrplan-ticket-crm.md`](fahrplan-ticket-crm.md) wieder aufnehmen.
3. Kundenprofil-/Objekt-Profil-Redesign erst mit eigener Bestandsaufnahme.
4. Große Status-/Stage-Ablösungen nur einzeln und geplant.
5. Hygiene/404/tote Views nur als Lückenfüller.

Umsetzungsreihenfolge der systemweiten Slices: siehe [`docs/configuration/umsetzungsfahrplan.md`](configuration/umsetzungsfahrplan.md).

---

## Parkplatz — nicht jetzt

Wichtig, aber aktuell nicht Hauptfokus:

- vollständiger CRM-Status-Umbau
- Cross-Gewerk-Intelligenz
- komplette Kundenprofil-Neugestaltung
- Accounting/FiBu-Ausbau
- Navigation-Großumbau
- Alt-Code-Entfernung ohne Lebendprüfung
- LiDAR / 3D-Gebäudeplaner-Ausbau (spätere Welle laut `docs/configuration/umsetzungsfahrplan.md`)

---

## Arbeitsregel gegen Überblicksverlust

Bei jeder neuen Aufgabe fragt Claude Code zuerst:

1. Passt die Aufgabe zum aktuellen Fokus?
2. Ist sie ein Schritt im aktuellen Fahrplan?
3. Ist sie ein Blocker?
4. Oder gehört sie in den Parkplatz?

Ist die Aufgabe nicht klar einordenbar, wird **nicht gebaut**, sondern der Arbeitskompass aktualisiert und Yama entscheidet. Der aktive **Startblock** bleibt maßgeblich für den konkreten Slice-Scope.

---

## Kurzstatus für den Start eines neuen Chats

```text
Aktiver Strang im ticket: WP Stufe 3b (kontrollierte Folgeintegration).
Abgeschlossen: 3a, P0, P1a, P1b-1 (Kostenservice).
Nächster möglicher Slice: P1b-2 (Förderungsservice) — nur mit eigenem Startblock + Yama-Freigabe.
Systemweite Analyse liegt unter docs/configuration/ (ADR-0001 + Matrizen + Fahrplan).
Bedarfsgeführt vor produktgeführt. Reuse statt Neubau. Kein automatischer Folgeslice.
Siehe docs/arbeitskompass-ticket.md.
```
