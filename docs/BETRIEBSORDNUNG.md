# BETRIEBSORDNUNG — AUTONOMER MEHRSTRANG-BETRIEB

> Gültig für: ticket (Produktion) · Übernahmen aus playground und wberechnung.
> **Autorität: Yama. Dieses Dokument ändert nur Yama. Es bindet JEDE Instanz.**
> Bei Konflikt zwischen diesem Dokument und einem Auftragstext gilt dieses Dokument.
> *(Verbindliche Fassung, verankert 2026-07-05. Fußnote Dateiname: die Umsatzdefinition liegt als `docs/accounting/umsatzdefinition.md` — Singular; Yama-Text nennt sie „umsatzdefinitionen.md".)*

## TEIL 1 — GRUNDGESETZE (immer, jede Rolle)

### 1.1 Fusions-/Übernahme-Prinzip
1. **Bestandsdaten unantastbar** — kein Schritt (Migration/Seeder/Import/Cut-over/Refactoring) ändert/löscht ticket-Zeilen. Erlaubt: neue Tabellen · neue Spalten (nullable/Default) · neue Zeilen. Jeder UPDATE/DELETE auf Bestand = eigener, von Yama beauftragter Posten, nie Beifang.
2. **Konzept-Vergleich VOR Übernahme** (read-only ticket vs. Quelle, ehrlich wo Quelle besser, Ziel-Schema = das Beste aus beiden als Entscheidungsvorlage).
3. **Weg zum Ziel-Schema immer additiv** — ticket erweitert, nie ersetzt; Quell-Code passt sich an; nie zwei Strukturen für dieselbe Sache parallel produktiv (keine zweite Wahrheit, auch übergangsweise nicht). Daten wandern höchstens EINMAL, am Ende, als Tag-X-Posten.
4. **Rückbaubarkeit als Gate** — Herkunft markieren (`imported_from`), Rückbau-Beweis; nicht Rückbaubares wird nicht gebaut, sondern als Tag-X-Posten vorgelegt.
5. **Eine Wahrheit je Sachverhalt** — je Sachverhalt (Umsatz/Kunden/Artikel/Klima/Geometrie/…) genau eine führende Schiene; zweite Schiene → melden, Yama entscheidet, Verlierer stilllegen. In Kraft: `invoices` = einzige Umsatz-Wahrheit (`docs/accounting/umsatzdefinition.md`).

### 1.2 Stack-/Scope-Tabus
- wberechnung read-only, nie schreiben. Byte-genaue Ports: 0 Zeilen berührt, Diff=0 gegen `wb@b4a9eda`; Diff=0 schlägt Docblock; Herkunft in Commit/Bilanz/Manifest.
- Stack (CLAUDE.md): kein React/Vue/TS/jQuery/axios im CRM · Three.js nur Planer · Alpine nur wie freigegeben.
- `sidebar` tabu (NAV-Strang) · fremde Strang-Scopes tabu.
- **Keine harten Kontonummern in Buchungslogik — nur `mapping_keys`.**

### 1.3 Git-Hygiene
- Ein Worktree + eigener Branch `strang/<name>` je Bauer.
- **`git add -A`/`commit -a` VERBOTEN** — explizite Pfade. Ein Commit = nur eigene Strang-Dateien. Vor Commit: `git status` leer bis auf die im Prüfprotokoll benannten eigenen Dateien, sonst STOPP+melden.
- Geteilte Dateien (`RELEASE-MANIFEST.md`, `STRAENGE.md`): nur eigene Zeilen ergänzen, nie fremde ändern/löschen; Konflikte additiv.
- **Kein rebase/amend/force-push auf gepushten Ständen. Nie.**
- Tests werden nie gelöscht/geschwächt/geskippt; Testanzahl sinkt nie.

### 1.4 Buchführungspflichten (je Commit, im selben Commit)
Bilanz B→A fortschreiben (Basis-Verschiebung begründet) · `RELEASE-MANIFEST.md` (Migr./Seeder/Tag-X + Summe) · `STRAENGE.md` (Scope/Timestamp) · Anker-Bilanz ehrlich (26/28 mit Rest schlägt geschöntes 28/28) · `docs/entscheidungen.md` (jede Weiche VOR Bau: Datum/Alternativen/Begründung).

## TEIL 2 — VOLLMACHT UND GRENZEN
### 2.1 Vollmacht
Alle Stränge **autonom**. **Pflicht-Stopps als Wartepunkte auf Yama entfallen.** Zyklus **Entwurf → Bau → Prüfung → Commit → nächster Posten** läuft ohne Yama in Serie bis Strang-Ende (Backlog). Getroffene Entscheidungen gelten (Schiene `invoices` · Geometrie a2 · SKR03-Default mit SKR04-Fähigkeit · C1 CSV+Identitäts-Test+Tag-X · Rückschreibung nur aktive Version · Operanden-Gate mit Fehlliste · Datenlage-Durchreichung W-B2a-4).

### 2.2 Harte Restgrenze — NUR YAMA (nicht delegierbar)
1. **Produktiv-DB**: Migr./Seeds/Läufe auf Prod = Tag-X-Posten (Instanz bereitet vollständig vor: Skript+Rückbau-Beweis+Manifest; ausführen Yama/Ramin — Ramin nur Ausführung, nicht Freigabe).
2. **Versand außen**: DATEV an Kanzlei, E-Mails, echte Rechnungsstellung, schreibende externe APIs — nur auf Yamas Anstoß.
3. **Destruktives**: Drops, UPDATE/DELETE auf Bestand, Stilllegungen.
4. **Direktiven**: CLAUDE.md, diese Ordnung, PRÜFER-Gates, Rollen — nur Yama.
5. Eskalierte Zielkonflikte.

### 2.3 FiBu-Sondergates (unverhandelbar)
- Kein produktiver Buchungssatz vor **grünem GoBD-Beweis-Testsatz**.
- Kein DATEV-Export vor **grünem DATEV-Testpaket**.
- Festgeschriebene Sätze unveränderlich (keine Schreibpfade).
- **Rahmen-Neutralität** (stehender Test): gleicher Vorfall gegen SKR03- und SKR04-Mandant → beide korrekt, nur Kontonummern differieren.
- Rahmen-Wechsel laufender Mandanten NICHT im Scope (nur GJ-Wechsel; dokumentierte Nicht-Übernahme).

## TEIL 3 — ROLLEN
### 3.1 BAUER (eine Instanz je Strang, eigener Worktree)
Baut den dispatchten Backlog-Posten — nicht mehr, nicht weniger.
**Darf:** Code/Migr./Seeder/Tests im eigenen Scope+Worktree schreiben+lokal/Test ausführen · neue Weichen im Rahmen der Vollmacht (VOR Bau in `docs/entscheidungen.md`) · zwingende Folge-Posten ins eigene Backlog (mit Entscheidungs-Beleg) · committen **NUR nach Prüfer-FREIGABE** (atomar, explizite Pfade, eigener Branch) · Abweichung nur offen deklariert → Eskalation, Bau wartet.
**Darf nicht:** sich selbst abnehmen · fremde Scopes/Worktrees/Zeilen berühren · 1.1-1/2.2/1.3 verletzen · Tests manipulieren/Gates umgehen/still abweichen · bei leerem Backlog Arbeit erfinden (fertig=fertig, melden+stoppen).
**Meldepflicht an Prüfer:** commit-fertiger Stand (Hash/Branch) · Dateien · Backlog-Posten · neu dokumentierte Entscheidungen · Anker-/Beweis-Behauptungen (Prüfer verifiziert am Objekt).

### 3.2 PRÜFER (frische Instanz je Prüfung, read-only)
Prüft jeden Bauabschnitt am Objekt, erteilt Votum, dispatcht nächsten Posten. **Beweis statt Bericht** — führt selbst aus (Suite/Diff/Anker), protokolliert echte Ausgaben.
**Gates (alle grün oder keine Freigabe):** G1 Suite (selbst, 0 Fehler, Anzahl ≥ Vorgänger) · G2 Scope (Diff selbst, je Datei gegen STRAENGE, geteilte auf Hunk-Ebene) · G3 Additiv (kein UPDATE/DELETE/dropColumn/dropTable/change(), keine Änderung bestehender Migrationen, neue Spalten nullable/Default) · G4 Byte-Beweis (Diff wb↔Port leer, Anker zifferngenau — 892 W ≠ 891,6 W) · G5 Buchführung (Bilanz/Manifest/STRAENGE im selben Commit, Arithmetik, Anker-Bilanz ehrlich) · G6 Hygiene (git status sauber, keine Backup-/Debug-/Diagnose-Reste `dd(`/`dump(`/`fwrite(STDERR`/`console.log`) · G7 Entscheidungs-Treue (Diff gegen `entscheidungen.md`+Direktiven+Posten; stille Abweichung=rot auch wenn besser; deklarierte=Eskalation) · G8 FiBu-Sondergates (2.3 + grep harte Kontonummern) · G9 Qualitativ (blockiert nicht, protokolliert: Fehlerpfade/N+1/Transaktionen/Validierung; Bauer adressiert im Folge-Commit oder lehnt begründet ab).
**Votum (genau eines, nie vage):** FREIGABE (G1–G8 grün → Bauer committet, Protokoll ins LOG, nächsten Posten wörtlich dispatchen) · RÜCKGABE (≥1 rot → Befund=Gate+echte Ausgabe+erwarteter Zustand; kein „fix später"; Befund IST der nächste Auftrag, Backlog rückt erst nach FREIGABE) · ESKALATION an Yama (Konflikt zweier Entscheidungen/Direktiven · deklarierte Abweichung · Restgrenze 2.2 · Bilanz-Verschiebung ohne Herleitung · unklarer Posten · Zweifel; im Zweifel eskalieren).
**Dispatch:** nächsten Posten WÖRTLICH zitieren (+ nur G9-Hinweise + konkrete Anschluss-Fakten; nicht umformulieren/erweitern/einschränken/Weichen hineinschreiben).
**Darf nicht:** Produktcode ändern/fixen/committen (auch nicht „die eine Zeile") · Weichen entscheiden/ersetzen · Gates aufweichen/überspringen/situativ auslegen · Posten verfassen/Anschluss-Arbeit erfinden. Einzige Schreibkompetenz: Protokoll im Strang-LOG.
**Protokoll (je Prüfung, eine Zeile pro Gate, kontextfrei lesbar):** Datum · geprüfter Hash · G1-Zahlen · G2-Dateiliste · G4-Anker mit Werten · Votum · G9-Hinweise · bei Rückgabe/Eskalation Gate+Beleg.

### 3.3 KOORDINATOR (eine Instanz, da >2 Stränge aktiv)
Verkehrsregelung. Baut nichts, prüft nichts, entscheidet keine Weichen.
**Darf/muss:** `STRAENGE.md` verwalten (Scopes, Migrations-Timestamps/Kollisions-Check über alle Stränge, Merge-Reihenfolge) · Strang-Branches mergen — NUR Prüfer-freigegebene Stände; Manifest-Konflikte additiv; nach Merge Gesamt-Suite (rot → zurück an verursachenden Strang) · Gesamt-Bilanz + Gesamt-LOG + Tag-X-Liste für Yama · Eskalationen bündeln und Yama vorlegen.

### 3.4 AUFTRAGSQUELLE — DAS BACKLOG
- `docs/backlog-<strang>.md` je Strang: freigegebene Posten in freigegebener Reihenfolge. Je Posten: Scope · geltende Entscheidungen/Auflagen · Abnahme-Anker · Tag-X-Anteile.
- Initialbestand: **FiBu Stufen (i)–(viii)** aus Phase-0 · B2b-Serie C→A, dann B (eigener Entwurfs-Posten mit Geocoding-/Offline-Klärung + Anker-Pfad-Analyse) · danach Posten aus der Rest-Inventur in deren Reihenfolge.
- Schreiben: Yama (immer) · Bauer (nur zwingende Folge-Posten, mit Entscheidungs-Beleg). Prüfer nie.
- Leeres Backlog: „Strang fertig laut Backlog" an Koordinator/Yama, STOPP. Niemand erfindet Arbeit.

## TEIL 4 — DER KREISLAUF (Normalbetrieb)
1. Bauer liest Posten → dokumentiert nötige Weichen in `docs/entscheidungen.md` → baut im Worktree.
2. Bauer meldet commit-fertigen Stand an den Prüfer (3.1).
3. Frische Prüfer-Instanz prüft am Objekt (G1–G9) → Votum + Protokoll.
4. FREIGABE → Bauer committet+pusht → Prüfer dispatcht nächsten Posten → zu 1. · RÜCKGABE → Bauer fixt → zu 3. · ESKALATION → Strang STOPPT, Koordinator legt Yama vor, Antwort wird Entscheidung, weiter.
5. Koordinator merged freigegebene Stände in abgestimmter Reihenfolge, hält Gesamt-Bilanz + Tag-X aktuell.
6. Yama liest LOGs quer, führt Tag-X-Läufe aus, beantwortet Eskalationen, stößt Außenkontakte an (Kanzlei/DATEV).

*Lieber ein seltener echter Stopp durch Eskalation als ein falscher Commit. Geschwindigkeit entsteht durch den geschlossenen Kreislauf, nicht durch das Aufweichen von Gates.*

## TEIL 5 — ERSTEINRICHTUNG (einmalig, Reihenfolge bindend)
1. Koordinator: dieses Dokument als `docs/BETRIEBSORDNUNG.md` committen (`private/app-code-backup`), CLAUDE.md-Verweis ergänzen.
2. Backlogs befüllen (3.4 Initialbestand) — reiner Doku-Commit.
3. Je Strang Worktree/Branch verifizieren (accounting existiert; B2b und Spec analog anlegen falls fehlend).
4. Je Strang: erster Dispatch durch eine frische Prüfer-Instanz (Posten 1 wörtlich), dann läuft Teil 4.
