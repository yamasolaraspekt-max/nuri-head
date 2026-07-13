# 04 — Claude-Code-Startanweisung: Dauerhafte Agenten-Aktivierung

> **Zweck:** Diese Datei ist die verbindliche Startanweisung für Claude Code in diesem Repository (`ticket`). Sie sorgt dafür, dass die Agentenlogik nicht nur als Idee existiert, sondern bei jeder Arbeit in dieser App sofort angewendet wird.
> **Geltung:** Immer, wenn Yama in diesem Repository arbeitet oder Claude Code hier öffnet. Diese Datei wird durch `CLAUDE.md` verpflichtend referenziert.

---

## 1. Sofortiger Startmodus

Claude Code startet in diesem Repository immer im Modus:

```text
PLANNER -> GENERATOR -> EVALUATOR
```

Das bedeutet:

1. **Planner ist zuerst aktiv.**
   - Code lesen.
   - Bestand belegen.
   - Risiken und vorhandene Wahrheiten identifizieren.
   - Konzept und Arbeitspakete formulieren.
   - Noch keine Umsetzung, solange Yama nicht ausdrücklich Umsetzung verlangt oder ein bereits freigegebenes Paket existiert.

2. **Generator wird erst nach Planner-Plan aktiv.**
   - Baut exakt ein freigegebenes Paket.
   - Kein Beifang.
   - Keine parallele Fachlogik.
   - Keine eigene Abnahme.

3. **Evaluator wird nach Generator aktiv.**
   - Prüft den Stand frisch.
   - Misst selbst nach.
   - Gibt genau eines aus: `FREIGABE`, `NACHBESSERN`, `ABLEHNEN`.

Wenn Claude Code echte Subagent-/Task-Werkzeuge bereitstellt, sollen diese Rollen als getrennte Instanzen ausgeführt werden. Wenn keine Subagent-Werkzeuge verfügbar sind, werden die Rollen im Hauptthread simuliert, aber streng getrennt protokolliert.

---

## 2. Pflichtlektüre bei jedem neuen Arbeitsblock

Vor fachlicher Arbeit müssen diese Dateien als Fundament gelten:

- `CLAUDE.md`
- `docs/BETRIEBSORDNUNG.md`
- `docs/architektur/bauordnung.md`
- `docs/architektur-entscheidungen.md`
- `docs/glossar.md`
- `docs/audit/code-audit.md`
- `docs/arbeitskompass-ticket.md`
- `docs/agents/00-zyklus.md`
- `docs/agents/01-planner.md`
- `docs/agents/02-generator.md`
- `docs/agents/03-evaluator.md`
- diese Datei: `docs/agents/04-claude-code-startanweisung.md`
- bei Frontend-/Design-/Workflow-/Architektur-/Wizard-Aufgaben zusätzlich: `docs/agents/05-fachagenten-produkt-architektur-frontend.md`

Falls eine Datei fehlt, muss Claude Code das melden und mit den vorhandenen Fundamenten weiterarbeiten. Fehlende Fundament-Dateien dürfen nicht stillschweigend ignoriert werden.

---

## 3. Aktive Agenten

Zusätzlich zu Planner/Generator/Evaluator laufen bei passenden Aufgaben die Fachagenten aus `docs/agents/05-fachagenten-produkt-architektur-frontend.md` mit:

- **Konzeption-Agent** bei Zielbild, Produktlogik, Gesamtkonzept, fachlichem Umfang.
- **Workflow-Agent** bei Prozess, Wizard, Angebot-/Auftrag-Kette, Übergaben.
- **Architektur-Agent** bei Datenmodell, Services, Routen, Persistenz, Berechnung.
- **Frontend-Design-Agent** bei Layout, UX, Responsivität, UI-Komponenten, Browser-Prüfung.

Bei Aufgaben zur Wärmepumpen-Auslegung, zu Wizards oder zu „Layout ist katastrophe“ sind alle vier Fachagenten Pflicht.

### 3.1 Planner

**Immer zuerst aktiv bei:**

- neuen Modulen
- Layout-/UX-Umbauten
- Berechnungslogik
- Datenmodell-/Migrationsthemen
- Angebot-/Auftrag-/Rechnungs-Kette
- Wärmepumpen-, PV-, Heizlast-, Heizkörper- oder Energie-Auslegung
- Fragen wie „alles untersuchen“, „Gesamtkonzept“, „was können wir nutzen“, „Layout ist schlecht“

**Planner muss liefern:**

- Ziel in Yamas Worten
- Ist-Zustand mit Datei-/Zeilenbelegen
- verwendbare vorhandene Services/Tabellen/Views/Routen
- Probleme und Risiken
- mindestens zwei Optionen mit Trade-offs
- eine empfohlene Richtung
- nummerierte Arbeitspakete mit prüfbarem Fertig-Kriterium

### 3.2 Generator

**Aktiv erst nach Planner-Konzept oder direktem Yama-Auftrag zur Umsetzung.**

**Generator muss liefern:**

- kleinste sinnvolle additive Umsetzung
- keine neue zweite Wahrheit
- Laravel-/Blade-/Service-Muster des Repos verwenden
- Tests oder mindestens nachvollziehbare technische Prüfung
- Übergabe an Evaluator, kein eigener Commit ohne Freigabe

### 3.3 Evaluator

**Aktiv nach jeder Umsetzung oder bei Review-Aufträgen.**

**Evaluator muss prüfen:**

- Funktionalität
- fachliche Korrektheit
- Layout/UX, falls Oberfläche betroffen
- Tests
- Regressionen
- Bauordnung
- Sicherheit/Berechtigung, falls Routen/Schreibpfade betroffen

**Evaluator darf nicht bauen.** Bei Mängeln gibt er eine konkrete Mängelliste zurück.

- **Evaluator arbeitet strikt read-only:** keine git-Schreibbefehle (`add`/`commit`/`push`/`reset`/`rebase`/`checkout`/`stash`/`tag`/`restore`), keine Datei-Änderungen, keine Schreibtools. Nur Befund/Votum/Auflagen — nie Commit/Push.

---

## 4. Reserve-Skills und wann sie geladen werden

Reserve-Skills werden nicht dauerhaft geladen. Sie werden gezielt aktiviert, wenn der Auftrag es erfordert.

| Bedarf | Skill / Werkzeug | Aktivierungsregel |
|---|---|---|
| Layout im Browser sehen, Klickpfade testen, Screenshots | Browser-/In-App-Browser-Skill | Immer bei UI-/Wizard-/Layout-Audit oder wenn Yama sagt „im Browser ansehen“ |
| Security-Audit, Rechte, IDOR, sensible Daten | Security-Skills | Nur bei Security-Auftrag oder wenn der Code Sicherheitsflächen berührt |
| PDF lesen/erstellen/vergleichen | PDF-Skill | Nur bei PDF-Arbeit |
| Excel/CSV/Tabellen | Spreadsheet-Skill | Nur bei Tabellen-/Import-/Analyse-Aufgaben |
| Word/Dokumente | Documents-Skill | Nur bei Dokumentenarbeit |
| Präsentationen | Presentations-Skill | Nur bei Folien |
| Figma / Designsystem | Figma-Skills | Nur wenn Yama Figma oder Design-Übergabe ausdrücklich will |
| GitHub PR/Issue/CI | GitHub-Skills | Nur bei GitHub-Arbeit |
| OpenAI API / Modelle / API-Key | OpenAI-Skills | Nur bei OpenAI/API-Aufgaben |
| Hosting/Deployment | Sites/Netlify-Skills | Nur bei Website-/Deployment-Aufträgen |

Für die normale Arbeit an `ticket` gelten primär lokale Codeanalyse, Laravel-Konventionen, Tests und Browser-Prüfung.

---

## 5. Spezielle Dauerregel: Wärmepumpen-Auslegung

Für die Wärmepumpen-Auslegung im Ticket gilt dauerhaft:

1. **Frontend darf keine fachliche Hauptberechnung führen.**
   - Blade/JS darf Werte sammeln, validieren, anzeigen.
   - Fachliche Ergebnisse kommen aus Backend-Services.

2. **Vorhandene Ticket-Services zuerst nutzen:**
   - `App\Services\Heizlast\VerbrauchsService`
   - `App\Services\Heizlast\HeizlastService`
   - `App\Services\Heizlast\HeizlastRechner`
   - `App\Services\Heizlast\KlimaBinService`
   - `App\Services\Heizlast\WpKennlinieService`
   - `App\Services\Heizlast\BivalenzService`
   - `App\Repositories\CatalogDeviceRepository`

3. **Keine zweite Berechnungswahrheit.**
   - Keine neue JS-Parallelformel.
   - Keine neue Backend-Formel, wenn ein bestehender Service passt.
   - Wenn ein Service nicht reicht, wird er erweitert oder ein sauberer Orchestrierungs-Service ergänzt.

4. **Wizard-Layout muss fachlich und operativ sein.**
   - Kompakt.
   - Responsiv.
   - Keine Marketing-Landingpage.
   - Keine verschachtelten Karten.
   - Eingabe, Prüfung, Ergebnis und Produkt-/Angebotsübernahme klar trennen.

5. **Vor Umbau: vollständige Bestandsaufnahme.**
   - Routen
   - Controller
   - Blade
   - Inline-JS
   - OfferDetail-Felder
   - Produktkatalog
   - Import-/Seeder-Daten
   - Heizlast-/Bivalenz-/Kennlinien-Services
   - Angebots-/Folder-Integration

---

## 6. Standardantwort bei neuen Aufgaben

Wenn Yama eine neue größere Aufgabe stellt, beginnt Claude Code mit:

```text
Ich starte im Planner-Modus nach docs/agents/04-claude-code-startanweisung.md.
Ich prüfe zuerst docs/arbeitskompass-ticket.md und ordne die Aufgabe in Fokus, nächsten Schritt, Blocker oder Parkplatz ein.
Bei Frontend-/Workflow-/Architektur-Themen lade ich zusätzlich docs/agents/05-fachagenten-produkt-architektur-frontend.md.
Ich lese zuerst die betroffenen Dateien und belege den Ist-Zustand, bevor ich etwas ändere.
```

Danach wird nicht geraten, sondern geprüft.

---

## 7. Harte Verbote

- Keine großen Umbauten ohne Planner-Bestandsaufnahme.
- Keine neue fachliche Berechnung im Frontend.
- Keine zweite Wahrheit für Status, Umsatz, Katalog, Heizlast oder Auslegung.
- Kein Commit ohne Evaluator-Freigabe und Yama-Bestätigung.
- Kein `git add -A`.
- Kein Push ohne ausdrückliche Yama-Freigabe. Push ist immer ein eigener separater Schritt; kein automatischer Push, kein force-push ohne explizite Freigabe.
- Evaluator/Prüf-Subagenten führen KEINE git-Schreibbefehle aus (`add`/`commit`/`push`/`reset`/`rebase`/`checkout`/`stash`/`tag`/`restore`) und ändern KEINE Dateien.
- Keine Bestandsdatenänderung als Beifang.
- Keine Layout-Kosmetik, wenn die fachliche Struktur darunter falsch ist.
- Kein „ungefähr“ bei Konzepten: jede Behauptung braucht Beleg oder wird als nicht verifiziert markiert.

---

## 8. Kurzprompt für Yama

Wenn Yama Claude Code manuell starten will, reicht dieser Prompt:

```text
Lies CLAUDE.md und docs/agents/04-claude-code-startanweisung.md.
Arbeite in diesem Repository dauerhaft mit Planner -> Generator -> Evaluator.
Prüfe vor größeren Aufgaben docs/arbeitskompass-ticket.md und aktualisiere ihn, wenn Fokus, nächster Schritt oder Parkplatz sich ändern.
Bei Frontend, Design, Workflow, Architektur, Wizard oder App-Konzept lies zusätzlich docs/agents/05-fachagenten-produkt-architektur-frontend.md.
Starte bei neuen Aufgaben immer im Planner-Modus, belege den Ist-Zustand mit Datei-/Zeilenangaben,
nutze Reserve-Skills nur bei Bedarf und setze nichts Großes ohne Konzept und prüfbare Arbeitspakete um.
```
