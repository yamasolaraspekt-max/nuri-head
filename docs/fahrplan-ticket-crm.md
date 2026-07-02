# Fahrplan — ticket CRM (nach Abschluss customer_phase_lists-Cleanup)

**Stand: 2026-07-02.** Der `customer_phase_lists`-Strang ist komplett abgeschlossen (Commits `f51bb08` → `f6a8b4c` → `6981b7c` → `32a3146`: .files-500-Fix, Feature-Code-Cleanup, Tabellen-Drop umkehrbar, 3 tote Old-Controller entfernt). Der Entscheidungsteil des Kernprozesses steht (6 Phasen; **Weiche 1** + **Weiche 5** + **Weiche 6** entschieden und in `architektur-entscheidungen.md` dokumentiert). Heute fertig gebaut: Progressbar (Weg A, `f52ab10`), objDrawerRoot-Zerlegungs-Scheibe, .files-500-Fix, customer_phase_lists-Ablösung.

> **Ältere Fassung dieses Dokuments** (Etappen-Struktur, Stand vor den Weichen-Entscheidungen) am Ende unter „Detail-Referenz" erhalten — die granulare Vertiefungs-Liste (Planner/Master-Set/Katalog/HR mit Code-Größen) und die Schutz-Prinzipien gelten weiter.

**Leitgedanke:** Genug aufgeräumt. Der nächste Schritt sollte wieder **Wert schaffen**, nicht nur Schulden tilgen. Das Aufräumen war richtig (Kernkrankheit „mehrere Wahrheiten"), aber der größte Alltagsnutzen liegt jetzt woanders.

---

## Ebene 1 — Wertschaffende Bau-Schulden aus den Weichen (höchster Alltagsnutzen)

### 1.1 Rückfluss mit Projektleiter-Prüfschritt — **WICHTIGSTER nächster Bau**
Entschieden (Weiche 6): Monteur meldet erledigt → läuft ins Büro als **„gemeldet"** → Projektleiter prüft → **„bestätigt"**. Heute sieht das Büro NICHT, was der Monteur draußen erledigt hat (Rückfluss endet in `customer_histories`/Audit).
- **VORAUSSETZUNG (zuerst):** der technische Link `planner_item(phase_activity)` ↔ Büro-Karte (`kanban_lead_tasks`) **fehlt heute** (hart geprüft, s. `planner-kanban-zuordnung-hart-geprueft.md`). Muss ZUERST geschaffen werden — `planner_items.meta.kanban_lead_task_id` beim Planen setzen ODER Unique-Constraint + `firstOrCreate`. Berührte Dateien (Startpunkt, noch NICHT angefasst): `KanbanLeadTask`-Model, `Planner\PlannerPlanController`, `Customer\Kanban\KanbanLeadTaskController`.
- **Dann:** Melde→Prüf→Bestätigt-Kette bauen (Büro-Karte braucht Zwischenstatus „gemeldet" ≠ „bestätigt").
- GRÖSSER + heikler als alles heute (Nuriva-nah, tief in der Aufgaben-Logik) → verdient **frischen, konzentrierten Start**, kein müdes Anreißen. **Strenger Pflicht-Stopp + Seed-Verifikation.**

### 1.2 Felder an Arbeitsschritte koppeln
Niedrige Priorität — Erweiterung (`phase_activities`-Kopplung), kein akuter Schmerz.

---

## Ebene 2 — Kundenprofil strukturell richtig machen (großer Wert, eigenes Projekt)

### 2.1 ZUERST: JS-Laufzeit verifizieren
Zeigt die `phaseSidebar` **zur Laufzeit** doch Phasen (und welches System)? Der Struktur-Befund (`kundenprofil-struktur-bestandsaufnahme.md`) gilt fürs **Blade**, nicht die AJAX-Laufzeit. Muss geklärt sein, **BEVOR** umgebaut wird — sonst Umbau für etwas, das schon existiert. *Grep = Verdacht, Live-Ausführung = Wahrheit.*

### 2.2 Profil-Redesign
Die 3 überlappenden Navigationen (Top / Bereich / Feed) konsolidieren, die **6 Phasen als Achse** einbauen, „Rechnungen" als Abschluss-Aufgabe einordnen. Markup-arm (daten-getriebene Nav), aber echtes Gestaltungsprojekt → eigener frischer Anlauf.

---

## Ebene 3 — Steuerberater-Gespräch (extern blockiert, parallel terminieren)

- **Weiche 3** (Rechnungssystem: `deal_invoices` vs `invoices` — welche Umsatzquelle ist buchhalterisch führend).
- **Weiche 4 Folgeregel** (bezahlte stornierte Rechnungen — Rückzahlung / Gutschrift / Umbuchung?). *(Storno technisch bereits umgesetzt: offen→`storniert`, bezahlt→`storniert_bezahlt_pruefen`+Warnung; die buchhalterische Folgeregel fehlt.)*
- Die **10 Controlling-Fragen** — dokumentiert in `controlling-bestandsaufnahme.md`.

→ Blockiert bis zum Gespräch. **Terminieren, dann entscheiden.**

---

## Ebene 4 — Verbleibende „mehrere Wahrheiten" (große Ablösungen, später, einzeln)

- Die **~11 Status-Felder / 12 Schreibpfade** (Weiche 1 entschieden, aber Umbau steht aus — Statusführung Phase/Zustand/Historie sauber trennen).
- **Stage-Tabellen-Wildwuchs** (`stages` / `customer_stages` / `phase_stages` / `offer_kanban_stages` / `lead_stages`).
- **Zwei Rechnungssysteme** (hängt an Ebene 3 / Steuerberater).

---

## Ebene 5 — Aufräum-Fäden (klein, wenn Zeit; kein Wert, nur Hygiene)

- **Verwaiste 404-Links + tote Blades** aufspüren. Bereits aufgedeckt: `customer_product_create`-Blade (unerreichbar, bewusst behalten) + 4 Live-Views verlinken sie ins 404; dazu die dangling `action()`-Verweise in `todo_checklist.blade.php` (jetzt 0 Renderer) + 2 Backup-Kopien. Symptom „Wegbauen ohne Aufräumen" → eigener gründlicher Strang.
- **Kundenprofil weiter zerlegen** — ab jetzt Tier-2/3 mit Blade-Logik (riskanter — nur mit Variablen-/Routen-Prüfung, jede Scheibe einzeln). Bisher erledigt: serialsOverlay, doneHistoryModal, halfDoneModal, commentSidebar, suggestEmployeesDrawer, objDrawerRoot (alle 0-Blade Tier-1 erschöpft).

---

## Empfohlene Reihenfolge

1. ✅ **Abgeschlossen:** customer_phase_lists komplett (inkl. Old-Controller-Restposten).
2. **Nächster wertschaffender Bau (frisch):** **1.1 Rückfluss-Link + Projektleiter-Prüfschritt.**
3. **Parallel/extern:** Ebene 3 — Steuerberater-Gespräch terminieren.
4. **Eigenes Projekt (frisch):** 2.1 JS-Laufzeit klären → 2.2 Profil-Redesign.
5. **Große Ablösungen (geplant, einzeln):** Ebene 4.
6. **Hygiene (Lückenfüller):** Ebene 5.

---

## Kernprinzip (schützt jeden Schritt)

**Vor jedem Bau harte Lebend-/Zuordnungs-Prüfung** — heute mehrfach bewährt: hat den Progressbar-Fix, die Zeitgewichtungs-Frage, die customer_phase_lists-Einstufung und die .files-Funktion vor Fehlern bewahrt. **Grep-Befunde sind Verdacht, Live-Ausführung ist Wahrheit.** Jeder Bau-Schritt: kleiner Auftrag → Pflicht-Stopp/Befund → Freigabe → Bau → Verifikation. Neue Funde → Backlog, nicht sofort verfolgen. Reihenfolge schlägt Neugier.

---
---

## Detail-Referenz — Vertiefungs-Kandidaten & Prinzipien (frühere Fassung, weiter gültig)

*Die granulare Liste der grob kartierten Bereiche (vor Bau erst Detail-Inventur) — geordnet nach Blockier-Wirkung. Fließt in Ebene 4/„große Ablösungen" ein:*

- **Planner / Projektmanagement** (~11k Z.) — **3 parallele Phasen-Systeme + `projects`/`planner_plans`**. Wichtigster Vertiefungs-Kandidat (dasselbe „mehrere Wahrheiten"-Muster wie beim Status; Projekt/Bauphase per Weiche 5 entschieden → Anschluss vorhanden). Vorarbeit teils erledigt: `kanban-ebenen-montage-planner-nuriva-befund.md`, `planner-kanban-zuordnung-hart-geprueft.md`, `planner-kanban-meta-daten-geprueft.md`.
- **Master-Set / Angebots-Konfiguration** (~6.700 + 25k Z.) — hängt eng am Angebots-Workflow.
- **Produktkatalog** + **Lager/Beschaffung/Großhandel** — Warenwelt; Katalog vor Beschaffung.
- **HR-Monolith** — viel Code, DB leer; niedrige Dringlichkeit.
- **Serverseitiges Angebots-/Auftrags-PDF** — echte Lücke; Priorität nach Alltags-Schmerz.
- **Legacy-Aufräumung** (`Old/`, ~58.500 Z. toter Ballast) — hoher Ordnungs-Gewinn, niedrige Dringlichkeit; nur wenn sicher 0 Live-Routen dranhängen (Muster wie beim customer_phase_lists-Cleanup: pro Datei Lebend-Check).
- **Cross-Gewerk-Intelligenz & Cockpit** (Zielbild) — die „intelligente Schicht", ganz zuletzt.

**Schutz-Prinzipien:** Nie Kernprozess-Bau (Ebene 4) bevor die Weichen stehen (jetzt: Weiche 1/5/6 ✓, offen 2/3/4). Nie einen grob kartierten Bereich bauen, bevor seine Detail-Inventur da ist. Bau-Spur und Denk-Spur laufen parallel.

---

*Ergänzt: `gesamtkonzept-ticket-crm.md` (Konzept), `crm-inventur-00-index.md` (8-Zonen-Inventur), `architektur-entscheidungen.md` (Weichen/ADRs), `workflow-sollkonzept.md` (Soll-Landkarte). Fortschreiben: erledigte Ebenen/Bauten abhaken.*
