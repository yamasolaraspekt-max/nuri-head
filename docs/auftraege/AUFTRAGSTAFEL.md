# ⇒ AUFTRAGSTAFEL — der Abholplatz für Generator, Evaluator und Repo-Aufsicht

**Angelegt:** 25.07.2026 · **Vom:** Planner · **Anlass:** Yama, 25.07.: *„so kannst du für den
generator aufgaben hinterlegen dass er sich holen kann, ausserdem habe ich fest gestellt dass der
wächter pausiert weil die verbindung nicht da ist."*

**Warum diese Datei existiert:** Bisher lagen Aufträge verstreut in `docs/auftraege/*.md` und wurden
im Ledger *erwähnt*. Wer neu startete, musste 1300 Ledger-Zeilen lesen, um zu wissen, was für ihn
offen ist. Das ist keine Übergabe, das ist Archäologie. Die Tafel ist das **Register**: eine Seite,
auf der jede Rolle in zehn Sekunden sieht, was sie abholen darf.

---

**Neue Sitzung?** Zuerst `docs/WIEDEREINSTIEG-HAUSPLANER.md` lesen — eine Seite, die sagt, in
welcher Reihenfolge nachgesehen wird und was gerade Ballbesitz ist. Dann hierher zurück.

---

## 0. Die Tafel läuft ohne Verbindung — das ist Absicht

Yamas zweite Beobachtung ist die wichtigere: **der Wächter pausiert, weil die Verbindung fehlt**
(so schon im Ledger festgehalten: „Überwacher-Cron pausiert, Aufsicht läuft künftig lokal").
Eine Auftragsverteilung, die einen laufenden Prozess, einen Cron oder eine offene Sitzung braucht,
fällt damit **genau dann aus, wenn sie gebraucht wird**.

Deshalb ist die Tafel bewusst **eine Datei im Repo** und kein Dienst:

- **Hol-Prinzip, nicht Bring-Prinzip.** Niemand muss erreichbar sein. Wer arbeitsfähig ist, liest
  die Tafel und nimmt sich, was für ihn offen ist.
- **Kein Netz nötig.** Die Tafel liegt im Arbeitsbaum. Sie ist auch lesbar, wenn kein Remote
  erreichbar ist, kein Cron läuft und keine andere Instanz wach ist.
- **Der Zustand steht im Commit, nicht im Kopf einer Sitzung.** Fällt eine Instanz aus, geht
  nichts verloren — der letzte Stand ist das, was zuletzt committet wurde.
- **Die Wahrheit bleibt der Ledger.** Die Tafel ist Register und Zeiger, nicht Beleg. Weicht sie
  vom Ledger ab, gilt `docs/handoff-status.md` — und die Abweichung wird gemeldet, nicht geglättet.

---

## 1. Wie ein Auftrag abgeholt wird (vier Schritte, keine Rückfrage nötig)

1. **Lesen:** diese Tafel, dann die verlinkte Auftragsdatei **vollständig**. Der Auftrag ist die
   Spezifikation; die Tafelzeile ist nur die Überschrift.
2. **Ziehen:** in der Tabelle unten den Status von `OFFEN` auf `IN ARBEIT — <Rolle>` setzen und
   **diese eine Datei** committen:
   `git commit -m "Auftragstafel: <AUF-x> gezogen" -- docs/auftraege/AUFTRAGSTAFEL.md`
   Damit sieht jede andere Instanz, dass der Posten vergeben ist. Zwei Instanzen am selben Auftrag
   sind der teuerste Fehler dieser Woche gewesen.
3. **Arbeiten** — streng im Umfang der Auftragsdatei. Taucht Nötiges außerhalb auf: **zurückgeben**,
   nicht heimlich mitbauen (Governance: kein Beifang).
4. **Melden:** Bericht als Block in `docs/handoff-status.md`, dann Tafelstatus auf
   `BERICHTET — wartet auf <Rolle>`. **Niemand setzt seinen eigenen Auftrag auf `ERLEDIGT`** —
   das tut die abnehmende Rolle. Kein Selbst-Abnehmen, das ist die eiserne Regel.

**Staging-Regel für alle:** immer `git commit -m "…" -- <eigene Pfade>` mit ausdrücklicher
Pfadangabe. Nie `-A`, nie `.`. Grund: Am 25.07. hat ein Commit ohne Pfadangabe sechs fremde,
bereits gestagte Dateien mitgenommen — der Fehler ist im Ledger offen ausgewiesen, nicht kaschiert.
Und `-m` steht **vor** dem `--`, sonst frisst der Pathspec den Text.

**Lock-Regel:** liegen `.git/*.lock`-Reste, blockieren sie jeden Commit. Auf der Geräte-VM ist
`unlink` verboten → **`mv` nach `.git/_locks_beiseite/<sammel>/`, niemals `rm`**.

---

## 2. Statuswerte (mehr braucht es nicht)

| Status | heißt |
|---|---|
| `OFFEN` | darf sofort gezogen werden |
| `GESPERRT` | Vorbedingung nicht erfüllt — **nicht** ziehen, Sperrgrund steht in der Zeile |
| `IN ARBEIT — <Rolle>` | vergeben |
| `BERICHTET — wartet auf <Rolle>` | umgesetzt und im Ledger belegt, Abnahme steht aus |
| `ERLEDIGT` | abgenommen von der prüfenden Rolle, mit Beleg im Ledger |
| `BEI YAMA` | Willensfrage — keine Instanz entscheidet das in seiner Vertretung |

---

## 3. Die Tafel (Stand 25.07.)

> **Kein HEAD-Hash im Kopf** — er veraltet mit dem nächsten Commit; wer den Stand braucht, misst ihn
> (`git --no-optional-locks log --oneline -5`). Die Regel stammt vom Planner selbst (`2f39924`).

> **Kritischer Pfad — vier Posten hängen in einer Kette:**
> **AUF-1** (A1-Wiederholungsabnahme) → **AUF-24** (ID-Umbenennung, berührt `toolPresentation.ts`)
> → **AUF-21/I2 + I3** (Adapter, Fach-Katalog, `canPin`/`priority`) → **AUF-4** (Welle A2).
> Alles andere ist Randposten. Solange AUF-1 kein Votum hat, bewegt sich diese Kette nicht.

| Nr | Auftrag | Rolle | Status | Datei / Beleg |
|---|---|---|---|---|
| **AUF-12** | **Dashboard v2 — Flächen des Werkzeug-Dashboards** (v2.1 Kontext-Options-Leiste, v2.2 Panel-Reiter, v2.3 Projektbrowser, v2.4 Prüfungscenter, v2.5 Befehlspalette). **Vorrang vor AUF-11** — Yama, 25.07.: „wir haben dashboard design fest gelegt sollst als erstes fertig gestellt werden v1 usw". Zwei Batches, Batch 1 wird berichtet und abgenommen, bevor Batch 2 beginnt. Ändert den Store **nicht** und liegt damit **außerhalb** des Sperrbereichs von AUF-1 | Generator | ``ERLEDIGT` — Batch 1 **freigegeben mit Auflage** (Evaluator-Votum 25.07.) · **Batch 2 `BERICHTET`** (`5092b10`) — wartet auf Evaluator, Spezifikation `evaluator-auftrag-dashboard-v2-batch2.md` | `generator-auftrag-dashboard-v2-flaechen.md` (Auftrag) + `docs/fahrplan-dashboard-versionen.md` (Fahrplan v1–v6) + `evaluator-auftrag-dashboard-v2-batch1.md` (Evaluator-Spezifikation, Planner 25.07.) |
| **AUF-1** | **A1-Abnahme wiederholen** (Gegenstand `c0ffe31`), unter den zwei neuen Auflagen E1 (erst messen, dann lesen) und E2 (voller Prüfrahmen, nicht nur N1–N7) | Evaluator, **frische Instanz** | `ERLEDIGT` — **Freigabe mit Auflage** (Votum 25.07., frische Instanz). Vier Auflagen binden **A2**: Kürzel `R`/`K` verriegeln · `zoneTools` memoisieren · Render-Pfad-Test · `herkunft` entscheiden | `evaluator-auftrag-wizard-welle-a1-werkzeug-praesentation.md` + Ledger-Block „ZWEI ERGÄNZUNGEN" (Z. 1103) |
| **AUF-2** | **T1 + `decke` committen** — die sechs gestagten Dateien als **eigener** Commit mit Pfadangabe | Generator (nativ) | `BERICHTET — wartet auf Evaluator (AUF-3)` | COMMIT-FREIGABE im Ledger (Z. 1006), Dateiliste dort |
| **AUF-3** | **T1-Abnahme** (Token-Konsolidierung + `decke → bau`) | Evaluator | `OFFEN` — Vorbedingung erfüllt: Hash `9ec3b25` | `evaluator-auftrag-t1-token-konsolidierung-und-decke.md` **inkl. §10** |
| **AUF-4** | **Wizard-Welle A2** — Leiste liest die Präsentationsschicht, inkl. P9-Memoisierung | Generator | `IN ARBEIT — Generator (nativ)` — entsperrt 25.07. durch das A1-Votum. Das ist **L1** aus `fahrplan-frontend-layout-hausplaner.md` und der Engpass des gesamten Layouts. Die vier A1-Auflagen sind mit umzusetzen | `generator-auftrag-wizard-welle-a2-leiste-liest-praesentation.md` **inkl. §8** |
| **AUF-5** | Die zwei vom Generator **zurückgegebenen** Punkte einordnen: `treppeSvg.ts:38`, `szene.ts:16` | Planner | `BERICHTET — wartet auf Yama` | Ledger „SCHRITT 2 ERGEBNIS" (Z. 1171); Einordnung im Ledger-Block „AUF-5 eingeordnet" (`e676023`) |
| **AUF-6** | stopp-1 Teil I — Re-Check fahren, Teil I schließen, Dokument nachziehen | — | `BEI YAMA` | Yamas Auftragsdatei, Auftrag 3 |
| **AUF-7** | `auto/hausplaner-ui-3a` — mergen oder bewusst überschreiben (fork `f3e38d6`, lokal `df0dbdb`) | — | `BEI YAMA` | Yamas Auftragsdatei, Auftrag 4 |
| **AUF-8** | Branch-Hygiene — welche der 27 Branches dürfen weg | — | `BEI YAMA` | Yamas Auftragsdatei, Auftrag 5 |
| **AUF-9** | **Posten T2a** — der Kommentar `renderers/three-d/szene.ts:16` nennt `#93c21c`, drei Zeilen tiefer steht `FARBE_AUSWAHL = 0xa3e635`, das Token sagt `#7fae1c`. Kommentar auf den **tatsächlichen** Wert richtigstellen; **kein** Farbwert im Code ändern | Generator | `BERICHTET — wartet auf Evaluator` (Commit `fbc5308`) | Ledger-Block „AUF-5 eingeordnet" (`e676023`), Messtabelle „drei Grüns" |
| **AUF-10** | **Posten T2b** — Palette in `geometry/`: soll die Treppen-Lauflinie überhaupt Markenfarbe sein, und darf `geometry/` Farben kennen? (`treppeSvg.ts:35-42`, neun Aufrufstellen, kein Parameter) | — | `BEI YAMA` | Ledger-Block „AUF-5 eingeordnet" (`e676023`), Abschnitt Willensfrage |
| **AUF-11** | **Layout-Fahrplan L1–L7** — gemessene Inventur der fünf Layout-Ebenen (17 Dateien / 3.343 Zeilen) plus Fahrplan; enthält drei Willensfragen an Yama (welche Engine wird Panel-Muster, wie tief eine leere L4-Fläche, darf L4 vorgezogen werden). **Reihenfolge abgelöst** durch AUF-12 — die Inventur bleibt gültig, die L1–L7-Abfolge ist es nicht mehr | Planner | `BEI YAMA` — Fach-Freigabe, **hinter AUF-12** | `docs/fahrplan-frontend-layout-hausplaner.md` (Commit `5af3e18`) |
| **AUF-13** | **Tote Naht Snapshots** — `objekt.blade.php:94` setzt `data-snapshots-url`, `routes/web.php:5003-5008` liefern drei Routen, `main.tsx:63` liest ausschließlich `dataset.speichernUrl`. Willensfrage: welcher Version wird die Anbindung zugeordnet — oder wird die Fläche bis dahin ehrlich als `in_entwicklung` ausgewiesen? | — | `BEI YAMA` | Ledger-Block „Planner-Messung 25.07." (gemessen gegen `f60b923`) |
| **AUF-14** | **Styling-Strategie** — `build:hausplaner` erzeugt **keine** `hausplaner.css`; der Blade-Link bleibt ungesetzt, das gesamte Styling liegt inline im TSX. Vor v2.3–v2.5 zu klären, ob das so bleibt oder ob v2 die Ablösung mitnimmt | — | `BEI YAMA` | Ledger-Block „Planner-Messung 25.07." |
| **AUF-15b** | **K6 neu schneiden** — der Evaluator hat belegt: `app/` enthält **30** rohe Farbwerte außerhalb `studioDaten.ts` (ConfigWizard 2 · StartView 3 · DreiDBereich 4 · GuidedView 15 · HausplanerStudio 6). T1 war auf `HausplanerApp.tsx` geschnitten (50→0), nicht auf `app/*` (80→30). Entweder wird das Kriterium künftig auf die **geänderten Zeilen** bezogen (so formuliert es die `frontend-entwickler`-Linse ohnehin), oder die Ablösung der 30 Restwerte wird ein eigener beauftragter Posten | Planner → dann Yama | `BEI YAMA` — Willensfrage: eigener Posten oder Kriterium umformulieren | Evaluator-Votum Dashboard v2 Batch 1 |
| **AUF-16** | **B1: Options-Leiste wird bei jeder Mausbewegung neu gemountet** — `KontextOptionenLeiste` ist als `const` im Rumpf von `HausplanerApp` definiert (`:298`) und als Element gerendert (`:835`); `onMouseMove` (`:873`) rendert fortlaufend. Wert geht nicht verloren, betroffen sind Fokus, Tastaturbedienung und DOM-Arbeit. Das Muster hat der Auftrag selbst angeordnet — bei `OpBtn` folgenlos, bei einem `<select>` nicht. **Vor der sichtbaren Freigabe von v2 zu entscheiden**, zusammen mit der ausstehenden Sichtprobe auf x64-nativ | Generator | `BERICHTET — wartet auf Evaluator` (Commit `982384d`) | Evaluator-Votum Dashboard v2 Batch 1, Befund B1 |
| **AUF-17** | **`Strg+K` war belegt** — `toolFuerShortcut('k')` liefert `decke`, und der Kürzel-Zweig in `taste()` prüfte keine Modifikatoren; vor `5092b10` setzte `Strg+K` das Werkzeug „Decke". Der Generator hat den Palette-Zweig davor einsortiert: `Strg+S` speichert unverändert, `K` allein setzt weiter „Decke", `Strg/⌘+K` öffnet die Palette. Bewusste Verhaltensänderung für genau eine Kombination — soll die Palette diese Kombination behalten? | — | `BEI YAMA` | Generator-Bericht Dashboard v2 Batch 2 |
| **AUF-18** | **Drei zurückgegebene Punkte einordnen** — (a) `RouteNode` (Leitungen) hat keine Gruppe im Projektbaum; §32 legt sechs fest, heute erzeugt kein Werkzeug Routen. (b) Befund-Historie mit `grund`/Zeitstempel/Bauteilbezug braucht eine Store-Änderung → Kandidat v3. (c) `Enter` auf `loeschen`/`duplizieren` ruft die vorhandenen Funktionen — vom Auftrag nicht ausbuchstabiert, Rückbau wäre eine Zeile | Planner | `OFFEN` | Generator-Bericht Dashboard v2 Batch 2, Abschnitt „Zurückgegeben" |
| **AUF-15a** | **Die 30 rohen Farbwerte ablösen** — wertgleich auf die vorhandenen Tokens in `studioDaten.ts`, Muster wie T1 (`9ec3b25`). Kein Farbwert ändert sich, nur seine Herkunft. Operanden-Gate: fehlt ein wertgleiches Token, wird der Fall zurückgegeben, nicht erfunden | Generator | **UMGESETZT** `2d927fc` — Abnahme offen (Evaluator). Hex-Zeilen 30 → 17; 24 Werte per Operanden-Gate zurückgegeben (kein wertgleiches Token) | `generator-auftrag-dashboard-v2-nacharbeit.md` §N1 |
| **AUF-19** | **Reiter-Muster vervollständigen** — B3: `role="tabpanel"`, `aria-controls`, `id`-Verknüpfung fehlen. B4: roving `tabIndex` ohne Fokusnachführung, nach ArrowRight liegen Fokus und Auswahl auseinander | Generator | **UMGESETZT** `8587ce7` — Abnahme offen (Evaluator) | `generator-auftrag-dashboard-v2-nacharbeit.md` §N3 |
| **AUF-20** | **ID-Sprache entscheiden** — die gerenderte Registry ist deutsch (`wand`, `fenster`, `tuer`, `dach`, `decke`, `treppe`, `auswahl`, `loeschen`, `duplizieren`), Katalog und das neue 110er-Paket sind englisch (`wall`, `window`, `door`, …). Eine Wahrheit je Sachverhalt: entweder 9 Registry-IDs umbenennen (berührt Commands, Tests, Fixtures) oder 110 Paket-IDs. **Vor dieser Entscheidung wird kein Icon einsortiert** — sonst entstehen 110 Dateinamen, die man danach wieder anfassen muss | Planner | `ERLEDIGT` — entschieden 25.07.: **englische IDs**, Labels bleiben deutsch; bei Paket↔Schema-Konflikt gilt das Schema (`ceiling`, `stair`) | `docs/planner/inventur-werkzeug-icons-2026-07-25.md` §7 |
| **AUF-21** | **Werkzeug-Paket einsortieren (I1–I4)** — 110 SVGs nach `public/hausplaner/icons/tools/`; Katalog-Austausch mit Adapter Paket→`ToolDefinition` (Konflikt-Regel: der neue Code passt sich dem Bestand an); die 47 DTP-Reste belegt stilllegen; `canPin`/`priority` in die Zonen-Kuratierung führen. **Gesperrt bis AUF-20** | Planner → Generator | `I1 UMGESETZT` `7bbf9ff` — Abnahme offen (Evaluator); **I2 + I3 `GESPERRT`** hinter AUF-24 | `docs/planner/inventur-werkzeug-icons-2026-07-25.md` §7 |
| **AUF-22** | **Kollisionsschutz zur Regel machen** — am 25.07. haben zwei Generator-Instanzen (nativ + Cowork) gleichzeitig an `generator-auftrag-dashboard-v2-nacharbeit.md` gearbeitet; `HausplanerApp.tsx` war unter der einen bereits umgebaut, ein fremder untracked Test lag im Baum. Nur weil beide freiwillig vorher auf der Tafel gezogen haben (`c3249d4`, `ca4153b`), ist nichts überschrieben worden. §1 der Tafel schreibt das Ziehen vor — durchgesetzt wird es von nichts. Vorschlag zu bewerten: Ziehen als Vorbedingung im Auftragstext jedes Generator-Auftrags, plus Pflicht-`git status`-Prüfung auf fremde untracked Dateien vor dem ersten Schreibzugriff | Planner | `OFFEN` | Generator-Bericht Nacharbeit, Abschnitt „Kollision" |
| **AUF-23** | **Elevation-/Overlay-Tokens fehlen** — 16 der 36 verbliebenen Rohwerte sind Schatten/Scrim (`rgba(28,40,48,.05)` u. a.); `studioDaten.ts` kennt keine Elevation-Rolle. Dazu: `T.surface` trägt nach N1 zwei Rollen (Fläche + Text-auf-Farbe, Kandidat `T.onFilled`), und ~8 Werte sind „nah dran" an vorhandenen Tokens — ihre Angleichung wäre eine **sichtbare** Farbänderung, also eine Willensfrage | — | `BEI YAMA` | Generator-Bericht Nacharbeit, Operanden-Gate |
| **AUF-24** | **Die 9 Werkzeug-IDs auf Englisch umbenennen** — `auswahl→select · wand→wall · fenster→window · tuer→door · dach→roof · decke→ceiling · treppe→stair · loeschen→delete · duplizieren→duplicate`. Labels bleiben deutsch. 210 Treffer in ~30 Dateien (Registry, Aktivierung, Zonen, Commands, Fixtures, Tests). Berührt **kein** persistiertes Schema — die IDs stehen dort nicht (je 0 Treffer). **Vor I2 von AUF-21** | Generator | `GESPERRT` — berührt `toolPresentation.ts` (AUF-1-Sperrbereich); Auflösung: Ledger-Block „AUF-24 kollidiert" | `docs/planner/entscheidung-id-sprache-werkzeuge.md` |

**Zu AUF-12 (Vorrang):** Yama hat am 25.07. entschieden, dass das Dashboard-Design steht und
**zuerst fertiggestellt** wird — versionsweise, v1 ist gebaut, v2 ist dieser Auftrag. Damit rückt AUF-12
vor AUF-11: nicht weil die L1–L7-Inventur falsch wäre, sondern weil ihre **Reihenfolge** einer anderen
Entscheidung folgte. Die Zuordnung v1–v6 → L1–L7 steht in `docs/fahrplan-dashboard-versionen.md` §4;
kein Posten aus L1–L7 fällt weg, jeder bekommt eine Versionsnummer.

**Warum AUF-12 nicht hinter AUF-1 gesperrt ist:** AUF-1 sperrt AUF-4, weil A2 `toolPresentation.ts`
liest. Dashboard v2 fasst weder `toolPresentation.ts` noch den Store noch Zod an — gemessen und in §5
des Auftrags als Guardrail festgeschrieben. Die beiden Arbeitsflächen überschneiden sich nicht.

**Zu AUF-9/AUF-10:** Beide stammen aus den zwei Punkten, die der Generator mit AUF-5 zurückgegeben
hat. Sie sind **kein** T1-Nachtrag — T1 gilt für die React-Insel (`app/*`), und die ist mit 0 rohen
Farbwerten erfüllt. `geometry/` und `renderers/` liegen außerhalb dieses Scopes (Token-Scope-ADR),
deshalb eigener Posten T2. Der ausführbare Teil (T2a) ist vom Willensteil (T2b) getrennt, damit der
Generator arbeiten kann, ohne dass jemand in Yamas Vertretung eine Farbentscheidung trifft.

---

## 4. Zwei Richtigstellungen, damit niemand doppelt fragt

**(a) „A2 braucht eine Planner-Spezifikation, die es noch nicht gibt" — das stimmt nicht mehr.**
Der A2-Auftrag liegt seit `d530da3` als Datei vor und wurde mit `78d384d` um §8 erweitert
(Sperre, P9-Memoisierung, drei neue Abnahmekriterien). Wer A2 zieht, findet eine vollständige
Spezifikation vor. Der Punkt aus dem Sammelblock ist damit erledigt.

**(b) „A1 erneut abnehmen — ja oder nein?" — Yama hat das bereits entschieden: ja.**
In seiner eigenen Auftragsdatei steht es wörtlich als Auftrag 1, mit dem Zusatz
„**Kritischer Pfad: A2 bleibt blockiert, bis das durch ist.**" Das ist keine offene Frage mehr,
sondern AUF-1 oben. Der Planner hat die Divergenz zu seiner eigenen, früheren A1-Abnahme im Ledger
als Abweichungsmeldung hinterlegt und sich Yamas jüngerer Anordnung gefügt: **A1 bleibt
abgenommen, A2 wird trotzdem nicht begonnen**, bis das Wiederholungsvotum im Ledger steht. Eine
zusätzliche unabhängige Abnahme kann eine Freigabe nur härten, nie weichmachen.

---

## 5. Was die Tafel ausdrücklich **nicht** tut

- Sie **nimmt nichts ab.** Grün entsteht durch Messung und Gegen-Beweis im Ledger, nie durch einen
  Tabelleneintrag.
- Sie **entscheidet keine Willensfrage.** Was auf `BEI YAMA` steht, bleibt dort stehen, auch wenn
  es bequemer wäre, es selbst zu entscheiden.
- Sie **ersetzt den Ledger nicht.** Wer nur die Tafel liest, hat die Belege nicht gelesen.
- Sie **drängelt nicht.** Ein Auftrag auf `OFFEN` ist ein Angebot, kein Weckruf.
