# ⇒ GENERATOR — AUFTRAG AUF-34: Arbeitsbereiche statt 22 Gruppen nebeneinander

**Vorher gelesen:** HEAD `26163bb` · `git log -2` · Tafelzeile AUF-27 (gezogen, läuft) ·
`app/state/uiState.ts:17,23,26,33,39` · `app/tools/toolRegistry.ts:13,30,47` ·
`app/tools/paketAdapter.ts` (`supportedWorkspaces: []`) ·
`app/tools/werkzeugPaket.ts` (Kategorien gezählt) · `~/Downloads/dashboard-tools-v1.html`
(Abschnitt „Workspace-Vorlage")

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Tafel:** AUF-34 · **Spur:** **A**
**Vorbedingung: AUF-27 muss berichtet sein** — beide fassen `HausplanerApp.tsx` an.
**Freigabe:** Yama, 25.07., auf den Planner-Vorschlag „Arbeitsbereiche statt Kosmetik".

---

## Ziel & Entscheidung

**Gemessen nach I4:** Die obere Leiste trägt **22 Gruppen nebeneinander** und läuft bei 1440 px
über **drei Zeilen**. Die Gruppen sind dabei sehr ungleich: `Bearbeiten` **13** Werkzeuge,
`Architektur` **9**, `Import` **8** — aber `TGA` und `Sanitär` **je eines**.
Zweiundzwanzig gleichrangige Menüs, teils für ein einziges Werkzeug.

**Die fehlende Ebene existiert bereits im Code und wird nicht benutzt:**
`uiState.ts:23` führt `activeWorkspace`, `:33` setzt ihn auf `WORKSPACE_ARCHITEKTUR`,
`toolRegistry.ts:13` definiert **genau einen** Wert. Die Registry-Werkzeuge tragen
`supportedWorkspaces`, die **101 Paket-Werkzeuge tragen `supportedWorkspaces: []`** — also
„überall gültig", weil der Adapter das Feld nie füllt.

### Entscheidung

**Die Leiste zeigt nur die Gruppen des gewählten Arbeitsbereichs.** Dazu ein Wähler oben, wie in
Yamas Entwurf `dashboard-tools-v1.html` („Workspace-Vorlage").

**Zwei Klassen von Gruppen — das ist der Kern:**

| Klasse | Gruppen | Verhalten |
|---|---|---|
| **durchgängig** (8) | `Auswahl` · `Bearbeiten` · `Ansicht` · `Messen` · `Prüfung` · `Zusammenarbeit` · `Workflow` · `System` | in **jedem** Arbeitsbereich sichtbar |
| **gebunden** (11) | siehe Tabelle unten | nur im zugehörigen Arbeitsbereich |

**Fünf Arbeitsbereiche, alle aus Yamas Entwurf — keiner erfunden:**

| Arbeitsbereich | zeigt zusätzlich zu den durchgängigen |
|---|---|
| **Import & Nachzeichnen** | `Import` (8) |
| **Architektur** | `Architektur` (9) · `Zeichnen` (6) · `CAD` (5) · `Material` (3) · `Fassade` (2) · `Bad` (3) · `Küche` (3) |
| **Bauphysik** | `Bauphysik` (4) |
| **Heizung** | `Heizung` (5) · `TGA` (1) · `Sanitär` (1) |
| **Elektro · PV** | `Elektro` (5) · `PV` (3) |

**Standard bleibt `Architektur`** — der heutige einzige Wert, damit niemand nach dem Umbau vor einer
anderen Leiste steht als vorher.

## Drei Lücken, gemessen und ausdrücklich **nicht** überklebt

1. **„Dach" ist im Entwurf ein Arbeitsbereich, im Paket aber keine Kategorie.** Die Dachwerkzeuge
   (`dach`, Gaube, Dachfenster) liegen in `Architektur`. Einen Arbeitsbereich „Dach" zu bauen hieße,
   die Kategorie aufzuteilen — **das ist nicht Gegenstand dieses Auftrags** und wird zurückgegeben.
2. **„Heizlast" ist im Entwurf ein Arbeitsbereich, ist aber kein Werkzeugbereich, sondern ein
   Rechenweg.** Er gehört zu den 13 Engines und damit zu L2/L3, nicht in die Werkzeugleiste.
   **Nicht Gegenstand.**
3. **`Bad` und `Küche` haben in Yamas Entwurf keinen Arbeitsbereich.** Ich ordne sie fachlich dem
   Ausbau zu und hänge sie vorläufig an **Architektur**. **Willensfrage an Yama**, ob sie später
   einen eigenen Bereich „Ausbau" bekommen — sie blockiert diesen Auftrag nicht.

## Nahtstellen

- `app/tools/paketAdapter.ts` — füllt `supportedWorkspaces` je Werkzeug aus der Kategorie-Zuordnung
  oben. **Leere Liste heißt weiterhin „durchgängig"** — die bestehende Bedeutung wird **nicht**
  geändert, nur endlich benutzt.
- `app/dashboard/werkzeugGruppen.ts` — filtert die Gruppen nach `activeWorkspace`.
- `app/HausplanerApp.tsx` — der Wähler und die gefilterte Gruppenzeile.
- **Wiederverwenden:** `activeWorkspace`/`setActiveWorkspace` aus `uiState.ts` — **kein zweiter
  Zustand**, keine Kopie im Store, kein Feld im Szenendokument.

## Was NICHT dazugehört

- Keine neue Kategorie im Paket, keine Kategorie aufteilen (siehe Lücke 1).
- Kein Arbeitsbereich außerhalb der fünf oben.
- Keine Änderung an den 110 Werkzeugen selbst, an ihren IDs, Icons oder Zuständen.
- `store/*`, `domain/*`, `geometry/*`, `renderers/*`, Zod, Schema, PHP, `public/*`.
- Die linke Schiene — die ist AUF-27.

## Kantenliste

1. **Arbeitsbereich ohne gebundene Gruppe** (z. B. Bauphysik zeigt nur 1 gebundene + 8 durchgängige):
   die Leiste darf nicht leer wirken. Prüfen, wie sie aussieht, und im Bericht sagen.
2. **Ein Werkzeug ist angeheftet und sein Arbeitsbereich wird verlassen:** bleibt es in der linken
   Leiste? **Entscheidung: ja** — Anheften ist persönlich und schlägt den Bereichsfilter. Es zeigt
   dann seinen Zustand wie jedes andere.
3. **Wechsel des Arbeitsbereichs darf das aktive Werkzeug nicht stillschweigend abwählen.** Ist es
   im neuen Bereich nicht verfügbar, wird das **angezeigt**, nicht weggeräumt.
4. **Der gewählte Bereich überlebt einen Neuladen** — falls gespeichert, dann **nicht** im
   Szenendokument. `localStorage` oder Nutzer-Setting.
5. **Schmale Fenster:** der Wähler und die Gruppenzeile brechen um, sie kappen nicht (AUF-26).

## Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` (**ohne Regen**) · `test:hausplaner` — **Exit 0**.
   `build:hausplaner` mit Ergebnis berichten.
2. Testzahl vorher/nachher, **Namen-Mengen verglichen**, kein verschwundener Test.
3. Ein Test belegt: **8 durchgängige** Gruppen erscheinen in **jedem** der fünf Arbeitsbereiche.
4. Ein Test belegt je Arbeitsbereich die **erwartete Gruppenmenge** aus der Tabelle oben — fünf
   Fälle, fest verdrahtet.
5. Ein Test belegt: **Summe über alle Bereiche = 22 Gruppen, 110 Werkzeuge**, kein Werkzeug fällt
   heraus, keines erscheint doppelt in derselben Ansicht.
6. Ein Test belegt Kante 2: ein angeheftetes Werkzeug bleibt beim Bereichswechsel sichtbar.
7. **Gegen-Beweis, selbst geführt:** eine gebundene Gruppe versehentlich als durchgängig markieren →
   mindestens ein Test **muss** rot werden. Danach zurückbauen, `git diff` leer.
8. `git diff` zeigt null Zeilen in `store/*`, `domain/*`, `geometry/*`, `renderers/*`, `public/*`.
9. **0 rohe Farbwerte in den geänderten Zeilen.**
10. **Spalte „Sieht Yama das?": `sichtbar`** ⇒ **Browser-Sichtprobe ist Teil der Abnahme**, mit
    genannter Fensterbreite bei **1440** und **1024** px. Erwartung, die belegt werden muss:
    die Gruppenzeile passt bei 1440 px in **eine** Zeile.

## Guardrails

- Posten **auf der Tafel ziehen, bevor** die erste Zeile geschrieben wird. **Erst nachdem AUF-27
  berichtet ist** — beide fassen `HausplanerApp.tsx` an.
- **Ein Commit**, Pfadangabe zwingend. **Nie `-A`, nie `.`**, `-m` **vor** dem `--`.
- `.git/*.lock` nur per `mv` nach `.git/_locks_beiseite/<datum>/`.
- **Kein Push, kein Merge, kein Deploy. „umgesetzt", nie „abgenommen".**

## Bericht

`## ⇒ GENERATOR-BERICHT — AUF-34 Arbeitsbereiche`, mit den zehn Kriterien als Rohausgabe, der
Gruppen-je-Bereich-Tabelle, der Beobachtung zu Kante 1, dem Gegen-Beweis aus Kriterium 7 und dem
Commit-Hash. Die drei Lücken oben werden **zurückgegeben**, nicht gelöst.

---

## NACHTRAG Planner, 25.07. — zwei Befunde aus der Sichtprobe, beide gehören hierher

**Vorher gelesen:** HEAD `da50af4` · Sichtprobe `objekt/203` bei 1440 px, Gruppe „Bearbeiten" geöffnet

**(a) Der ~1375-px-Defekt gehört zu diesem Auftrag, nicht zu AUF-26.** Der Evaluator hat AUF-26 per
iframe bei **1440 / 1371 / 371 px** gemessen: alle vier Panel-Reiter sichtbar, keiner geklippt, das
Panel ist fest 268 px breit. **Damit ist meine frühere Zuordnung widerlegt** — nicht das Panel kappt,
sondern die **dreizeilige Gruppenzeile** treibt die Seite in den waagerechten Überlauf und schiebt
das Panel aus dem Bild. Die Ursache ist also genau das, was dieser Auftrag behebt.

**Zusätzliches Abnahmekriterium 11:** bei **1371 px** (nicht nur 1440 und 1024) darf die Seite
**keinen waagerechten Überlauf** haben — `document.documentElement.scrollWidth <=
document.documentElement.clientWidth`. Rohausgabe im Bericht.

**(b) Die Gruppen-Menüs sind zu schmal.** In der Gruppe „Bearbeiten" bricht die Beschriftung
**Buchstabe für Buchstabe** um — „K-o-p-i-e-r-e-n" untereinander, ebenso „Löschen" und
„Duplizieren". Ursache: die Kürzel-Kästchen (`Ctrl/Cmd+C`, `Delete`, `Ctrl+D`) beanspruchen die
Breite, der Text weicht. **Ein Wort, das senkrecht steht, ist unlesbar** — derselbe Fehler wie die
Kappung, nur andersherum.

**Zusätzliches Abnahmekriterium 12:** In **keinem** Gruppen-Menü bricht ein einzelnes Wort um.
Das Menü ist so breit, dass Label und Kürzel nebeneinander passen (Mindestbreite, oder Kürzel in
eine zweite Zeile — entscheide und sag es im Bericht). Belegt per Sichtprobe mit Screenshot der
Gruppe **Bearbeiten**, das ist die dichteste (13 Werkzeuge, drei davon mit Kürzel).
