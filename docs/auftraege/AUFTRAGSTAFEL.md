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


## 1b. Zwei Nummernkreise — Legende (damit niemand mehr sucht)

Es laufen **zwei** Bezeichnungen nebeneinander. Sie meinen dasselbe:

| Welle / Slice | Tafelposten | Gegenstand |
|---|---|---|
| **Welle A1** | **AUF-1** | Werkzeug-Präsentationsschicht (`c0ffe31`) — abgenommen 25.07. |
| **Welle A2** | **AUF-4** | Leiste liest die Präsentationsschicht (`acdb987`) — **Abnahme offen** |
| **Welle A3** | noch kein Posten | Kontext-Zone + Anheften, im A2-Auftrag §3 abgetrennt |
| **T1** | AUF-2 / AUF-3 | Token-Konsolidierung + `decke → bau` (`9ec3b25`) |
| **T2a / T2b** | AUF-9 / AUF-10 | Farbwert-Kommentare · Palette in `geometry/` |
| **Dashboard v2** | AUF-12 | Batch 1 (`f6bdfc2`) · Batch 2 (`5092b10`) |
| **N1 / N2 / N3** | AUF-15a / AUF-16 / AUF-19 | Nacharbeit aus dem Batch-1-Votum |
| **I1 / I2 / I3** | AUF-21 | Icons ablegen · Katalog tauschen · Anheften + Zonen |
| **L1 … L7** | Layout-Fahrplan | **L1 = Welle A2 = AUF-4** · L4 = AUF-25 |

**Merksatz: „Welle A2", „L1" und „AUF-4" sind derselbe Posten.**

## 1c. ⚡ AKTIV — genau ein Posten, und nur der wird gezogen

**Regel (Yama, 25.07.):** Auf dieser Tafel trägt **genau ein** Posten die Markierung **`⚡ AKTIV`**.
**Der Generator zieht nur diesen.** Alles andere wartet, auch wenn es kleiner, reizvoller oder
schneller erledigt wäre.

**Warum:** Am 25.07. wurde AUF-30 gezogen — ausdrücklich als *nicht blockierend* eingetragen —
während der Posten, an dem Yamas Ziel hing, offen liegen blieb. Nicht aus Nachlässigkeit, sondern
weil beides gleich aussah. Ein Wort auf der Tafel unterscheidet sie.

**Wer setzt es:** der Planner, nach Yamas Fokus. **Wer verschiebt es:** niemand sonst.
Ist der aktive Posten **berichtet**, rückt die Markierung auf den nächsten der Kette — **nicht erst nach der Abnahme.** Korrektur vom 25.07.: die erste Fassung verlangte die Abnahme, das hätte den Generator bei jeder Abnahme leerlaufen lassen. Der Zweck der Markierung ist, **Themenwechsel** zu verhindern, nicht die Kette zu serialisieren. Abnahmen laufen parallel. **Fällt eine Abnahme rot aus, geht die Markierung zurück** auf den beanstandeten Posten.
**Ist der aktive Posten blockiert**, wird das gemeldet — die Markierung wandert *nicht* still weiter.

**Ausnahme, eng:** Ein Posten, der den aktiven **direkt entsperrt**, darf vorgezogen werden. Er wird
dabei ausdrücklich als solcher benannt („entsperrt AUF-x"), sonst ist es keine Ausnahme, sondern
Themenwechsel.

---

## 1d. Spalte „Sieht Yama das?" — zwei Werte, Pflicht beim Berichten

Jeder Posten trägt beim Bericht einen von zwei Werten:

| Wert | heißt |
|---|---|
| **`sichtbar`** | Nach dem nächsten Build ändert sich für Yama etwas auf dem Schirm. **Dann ist eine Browser-Sichtprobe Teil der Abnahme, nicht danach.** |
| **`Vorarbeit`** | Notwendig, aber unsichtbar. Datenmodell, Adapter, Testinfra, Zustandslogik. |

**Warum:** `ccdc93b` („I3", die sechs Werkzeug-Zustände) war saubere, notwendige Arbeit — und für
Yama auf dem Schirm **null Veränderung**, weil alle 110 Werkzeuge auf `versteckt` blieben. Es hat
drei Runden gedauert, das zu bemerken. Mit dieser Spalte hätte der Bericht `Vorarbeit` sagen müssen,
und es wäre in derselben Minute klar gewesen.

**Kein Urteil, nur eine Tatsache.** `Vorarbeit` ist nicht schlechter als `sichtbar` — die Hälfte
jedes Bauwerks ist Fundament. Aber Yama muss es unterscheiden können, ohne zu fragen.
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

**Vier Tabellen statt einer — geordnet danach, wer als Nächstes handeln muss.** In einer Tabelle mit 39
Zeilen war der eine Posten, der gezogen werden darf, nicht mehr zu finden. Kein Posten ist verschwunden:
**11** Arbeitsvorrat · **6** Abnahme · **10** bei Yama · **17** im Archiv — Summe geprüft, 44.

> **Befund 25.07., 21:05 (Bestandsaufnahme):** **L5 und L6 kamen auf dieser Tafel überhaupt nicht vor** —
> null Treffer, gemessen. Sie standen im Layout-Fahrplan, aber niemand konnte sie ziehen; sie wären nie
> gebaut worden, egal wie lange die Kette läuft. Als **AUF-39** und **AUF-40** nachgetragen. Lehre: ein
> Fahrplanschritt ohne Tafelzeile existiert für die Kette nicht.

> **Nachgeführt 25.07., 20:47:** Der Abnahme-Stapel stand auf 10, obwohl der Evaluator in
> `docs/abnahme-evaluator-haertung-2026-07-25.md` längst **13 Voten** committet hatte. Acht Posten waren
> abgenommen und standen trotzdem als „wartet auf Evaluator". Ursache: die Statusspalte wurde gepflegt,
> statt gegen das Votum gemessen. Ab jetzt gilt für diese Tafel: **ein Status ist erst gepflegt, wenn er
> gegen das Abnahme-Dokument gehalten wurde**, nicht gegen die vorige Tafelzeile.

---

### 3a. Arbeitsvorrat — hier wird gezogen

**Der erste Posten trägt ⚡ und ist der einzige, der gezogen werden darf** (§1c) — alles darunter ist
Reihenfolge, kein Angebot. `GESPERRT`: Vorbedingung nicht erfüllt, Grund steht in der Zeile.

| Nr | Auftrag | Rolle | Status | Datei / Beleg |
|---|---|---|---|---|
| **AUF-35a** ⚡ **AKTIV** | **„Markieren" — Flächen- und Zonenauswahl** (Yama, 25.07., aus Architektensicht). Drei Vorgänge, nur einer existiert: Objektauswahl **da** · **Flächenauswahl fehlt vollständig** (Wandseite, einzelne Dachfläche — Voraussetzung für Material/Fassade/Dachdeckung) · **Zone markieren**: Schema kennt **6** Typen (`room · underfloor_heating · pv_area · maintenance_area · sound_area · restricted_area`), das Paket hat dafür **ein** Werkzeug (`raum`), Grep `ZONE` in den Commands → **0 Treffer**. **Das ist die Naht zwischen Geometrie und Fachmodulen** und erklärt, warum die 13 Engines keinen Eingang haben. **Vor L2/L3, nach AUF-34.** Planner misst zuerst drei Fragen, dann Auftrag | Planner → Generator | `OFFEN` ⚡ — **AKTIV**, AUF-36 ist berichtet. Auftragsdatei liegt seit `12c3288`/`239cefd`; Messung der drei Fragen aus §2 gehört in den Bericht | Planner-Block „Markieren ist kein Label" |
| **AUF-33** | **Die 13 Rechen-Engines zu den Fachplaner-Flächen** — `engine-fbh · -heizkoerper · -heizkreis · -abwasser · -kueche · -pv · -uwert · -fensterprodukt · -sparren · -treppe · -holzmengen · -holzbauteile · -schifter` wandern zu den 19 L4-Flächen; danach fällt der Übergangs-Reiter „Fachplaner" ersatzlos weg. Gemessen: 3 klare Paare, 10 Engines ohne Entsprechung, 16 L4-Flächen ohne Engine — **keine Doppelung, zwei Sortierungen** | Planner → Generator | `OFFEN` — **entsperrt: Yama, 25.07. — das Panel-Muster wird die Treppe.** Auftrag liegt: **zwei Stufen**, L2 (nur `engine-treppe`) wird abgenommen, bevor L3 die übrigen 12 kopiert | `generator-auftrag-auf33-engine-panels.md` |
| **AUF-29** | **Blinde Gegenzeichnung A2** — die A2-Abnahme (`728ae69`) ist belastbar, stammt aber von einer anchored Instanz (vom Evaluator selbst offengelegt). Die frische Instanz, die AUF-1 zog, zeichnet gegen: erst messen, dann lesen. **Blockiert nichts** — eine zusätzliche unabhängige Abnahme kann eine Freigabe nur härten, nie weichmachen | Evaluator, frische Instanz | `OFFEN` — nicht blockierend | Planner-Entscheidung 25.07. im Ledger |
| **AUF-18** | **Drei zurückgegebene Punkte einordnen** — (a) `RouteNode` (Leitungen) hat keine Gruppe im Projektbaum; §32 legt sechs fest, heute erzeugt kein Werkzeug Routen. (b) Befund-Historie mit `grund`/Zeitstempel/Bauteilbezug braucht eine Store-Änderung → Kandidat v3. (c) `Enter` auf `loeschen`/`duplizieren` ruft die vorhandenen Funktionen — vom Auftrag nicht ausbuchstabiert, Rückbau wäre eine Zeile | Planner | `OFFEN` | Generator-Bericht Dashboard v2 Batch 2, Abschnitt „Zurückgegeben" |
| **AUF-22** | **Kollisionsschutz zur Regel machen** — am 25.07. haben zwei Generator-Instanzen (nativ + Cowork) gleichzeitig an `generator-auftrag-dashboard-v2-nacharbeit.md` gearbeitet; `HausplanerApp.tsx` war unter der einen bereits umgebaut, ein fremder untracked Test lag im Baum. Nur weil beide freiwillig vorher auf der Tafel gezogen haben (`c3249d4`, `ca4153b`), ist nichts überschrieben worden. §1 der Tafel schreibt das Ziehen vor — durchgesetzt wird es von nichts. Vorschlag zu bewerten: Ziehen als Vorbedingung im Auftragstext jedes Generator-Auftrags, plus Pflicht-`git status`-Prüfung auf fremde untracked Dateien vor dem ersten Schreibzugriff | Planner | `OFFEN` | Generator-Bericht Nacharbeit, Abschnitt „Kollision" |
| **AUF-35b** | **Flächen- und Zonenauswahl** — Wandseite und einzelne Dachfläche greifen (fehlt vollständig); Zone markieren für die **6** Schema-Typen, von denen das Paket nur `raum` kennt und die Commands **keinen**. **Die Naht zu den 13 Engines.** Braucht AUF-35a als Fundament | Planner → Generator | `GESPERRT` — bis AUF-35a durch ist | Planner-Block „Markieren ist kein Label" |
| **AUF-28** | **15 falsche Versprechen aus der Navi nehmen** (Spur A) — die Zone `weitere` zeigt dem Nutzer „Links ausrichten · Hand · Zoom · Freie Transformation …" als `in Entwicklung`. Die Icon-Inventur belegt: DTP-Erbe, kommt nie. Ausführbarer Teil: die 15 Regeln in `toolPresentation.ts` von `weitere` auf `versteckt`; die Navi braucht dann einen ehrlichen Leerzustand. **Willensteil bleibt bei Yama:** womit `weitere` neu belegt wird (Kandidat: Fach-Werkzeuge aus dem 110er-Paket, AUF-21/I2) | Generator | `GESPERRT` — bis A2 abgenommen (`toolPresentation.ts`) | dito, B2 |
| **AUF-38** | **Inline-Styles ablösen** (Entscheidung Yama, 25.07., aus AUF-14) — 331 `style={{` in 35 Dateien wandern in eine echte Stilschicht; `build:hausplaner` erzeugt künftig `public/hausplaner/hausplaner.css`, die vorhandene `@if (file_exists(…))`-Bewachung in beiden Blades greift dann von selbst. **In Scheiben, nach Datei geschnitten** (HausplanerApp 132 · GuidedView 41 · ConfigWizard 39 · HausplanerStudio 34 · FachFlaeche 27 · StartView 20 · Rest 38) — nie zwei Scheiben gleichzeitig, weil dieselben Dateien der Werkzeugleiste gehören. **Wert- und verhaltenstreu:** kein gerenderter Farbwert ändert sich, `studioDaten.ts` bleibt die Quelle | Planner → Generator | `GESPERRT` — Planner schreibt zuerst den Auftrag (Schnitt, Reihenfolge, Wertgleichheits-Nachweis); Umsetzung erst nach AUF-36 und AUF-35a, sonst kollidiert sie mit der Werkzeugleiste | Ledger-Block „Zwei Willensentscheidungen" (25.07.) |
| **AUF-39** | **L5 — Wizard-Schritte aus dem Modell ableiten** statt hartkodierter Status/Prüfungen. Steht seit `5af3e18` im Layout-Fahrplan und hatte bis 25.07. **keinen Posten** — gemessen: null Treffer auf `L5` in dieser Tafel. Guardrail aus dem Fahrplan: an vorhandene Services andocken, **kein zweiter Snapshot-/Hash-/Projektions-Mechanismus** | Planner → Generator | `GESPERRT` — hinter der Werkzeugleiste (AUF-36/35a) und hinter L2/L3 (AUF-33); Planner misst zuerst nach, wo die Schritte heute wirklich stehen | `docs/fahrplan-frontend-layout-hausplaner.md` Z. 90 |
| **AUF-40** | **L6 — Start/Zuletzt an echte Projekte + Konfigurator-Paket serverseitig speichern.** Gemessen offen: `app/StartView.tsx:75` rendert die Demo-Liste `ZULETZT`, `app/ConfigWizard.tsx` lädt das `ConfiguratorPackage` als JSON **herunter**, statt es zu speichern. Hatte bis 25.07. ebenfalls **keinen Posten** (null Treffer auf `L6`). Braucht Backend-Anschluss, blockiert kein Layout | Planner → Generator | `GESPERRT` — hinter AUF-39; berührt als einziger Layout-Posten `app/Http/` und `routes/`, deshalb eigener Auftrag mit Yamas Fach-Freigabe vor dem Bau | `docs/fahrplan-frontend-layout-hausplaner.md` Z. 91 |
| **AUF-42** | **`viewport.ready` ist heute eine Vereinfachung** — die Fähigkeit wird gesetzt, sobald `HausplanerApp` rendert; einen echten Renderer-Bereitschaftszustand führt der Store nicht. Der Generator hat das bei AUF-36 offengelegt statt es für gemessen auszugeben. **Folgenlos, solange es keine Ladeanimation gibt** — der Posten existiert, damit die Vereinfachung nicht später für eine Messung gehalten wird | Planner → Generator | `GESPERRT` — klein, ohne Dringlichkeit; erst wenn ein Ladezustand sichtbar wird | Ledger „GENERATOR-BERICHT AUF-36", Rückgabe 3 |

---

### 3b. Abnahme-Stapel — berichtet, wartet auf Prüfung

**Fünf Posten, davon drei reine Sichtprobe.** `91d9592` hat AUF-27 **und** AUF-34 ausgeliefert — beide
Sichtproben sind damit führbar (iframe 1440/1024/375, §8 in `docs/agents/06-laufzeiten-und-takt.md`).
**AUF-21/I1** ist der einzige Teil des Icon-Pakets ohne Votum, **AUF-30** der einzige Posten ohne jede
Prüfung. Niemand nimmt eigene Arbeit ab (§1.4).

| Nr | Auftrag | Rolle | Status | Datei / Beleg |
|---|---|---|---|---|
| **AUF-36** | **Funktionsvertrag der 110 Werkzeuge einhängen** — `~/Downloads/hausplaner_svg_tool_functions.zip`. Je Werkzeug `commandId · family · inputs · outputs · preconditions · sideEffects · undoable · auditRequired · serviceMethod`. **Vokabulare sind klein und damit abbildbar: 12 Vorbedingungen, 11 Seiteneffekte, 9 Familien, 110 commandIds.** Bindend: `preconditions` gehen als **Daten** in `resolveToolState` (kein zweites `resolveDisabledReasons`); `commandId`/`undoable`/`auditRequired` sind **Metadaten**, die Ausführung bleibt bei `applyCommand` mit inversen Patches (kein `runTool` daneben, sonst gehen Undo und `CommandAbgelehnt` verloren) | Planner → Generator | `BERICHTET` — umgesetzt `5d98131` (Vertrag) + `d106445` (Nachbesserung), Bundles `9a4623b`/`368f2d7`. Alle 12 Vorbedingungen zugeordnet, 5 heute unerfüllbar mit Grund; keine zweite Engine, kein `runTool`. Tore 0/0/0, 830→853 Tests. **Die Sichtprobe fand einen Anzeigefehler, den das Gate nicht sah** — behoben und verriegelt. Drei Rückgaben (Import-Recht, vier Fach-Operanden, `viewport.ready`). **Ballbesitz → Evaluator** | `generator-auftrag-auf36-funktionsvertrag.md` |
| **AUF-37** | **Bundle-Rebuild für AUF-27** — `894954a` (Reiter) ist **nicht ausgeliefert**: kein Rebuild danach, das servierte Bundle kennt die drei Reiter nicht. Zweites Bundle-Loch nach Batch 2. Rebuild als **eigener Commit** direkt nach dem Code-Commit, Bericht mit Rohausgabe: Größe/Zeitstempel + `grep -c` auf eine Zeichenkette aus dem Slice + Quell-Commit. Danach holt der Evaluator die Sichtprobe nach (iframe, 1440/1024/375) | Generator (nativ, x64) | `BERICHTET — wartet auf Evaluator` (`91d9592`) — Bundle trägt **beide** Slices, per `grep -c` belegt (7 Zeichenketten aus AUF-27 und AUF-34), Commit-Umfang 1 Datei. Entriegelt die Sichtproben von AUF-27 **und** AUF-34 | Bundle-Regel `docs/agents/06-laufzeiten-und-takt.md` §8 |
| **AUF-34** | **Arbeitsbereiche statt 22 Gruppen nebeneinander** — die Leiste läuft bei 1440 px über drei Zeilen; `Bearbeiten` hat 13 Werkzeuge, `TGA` und `Sanitär` je eines. Die Ebene existiert ungenutzt im Code (`uiState.ts:23 activeWorkspace`, genau **ein** Wert; Paket-Werkzeuge tragen `supportedWorkspaces: []`). Entscheidung: 8 durchgängige Gruppen + 11 gebundene auf **fünf** Arbeitsbereiche aus Yamas Entwurf. Drei Lücken benannt und zurückgegeben (Dach, Heizlast, Bad/Küche) | Generator | `BERICHTET — wartet auf Evaluator` — **FREIGABE MIT AUFLAGE** (`7fe6627`): Bilanz 15/110 ohne Verlust, Mutation 4 rot, 830/830. Auflage (Bundle) **erfüllt** durch `91d9592`. Offen sind nur die zwei deferred Sichtprobe-Kriterien: kein waagerechter Überlauf bei 1371 px, keine Wortumbrüche im Menü | Planner-Vorschlag, von Yama angenommen 25.07. |
| **AUF-27** | **Linke Spalte macht drei Jobs** (Spur A) — Werkzeuge + Fähigkeiten + Projektbrowser teilen sich 220 px und **eine** Scroll-Höhe; der Projektbrowser ist erst nach 20 Scroll-Ticks sichtbar. Verstoß gegen „ein Hauptjob je Fläche" und „Sidebar = Navigation, keine Daten". Entscheidung: drei Abschnitte mit je eigener Scroll-Höhe, Werkzeuge fix oben, Fähigkeiten und Projekt mit Kopf und Zähler | Generator | `BERICHTET — wartet auf Evaluator` — **FREIGABE MIT AUFLAGE** (`ea61ea1`), Auflage **erfüllt**: `91d9592` liefert `894954a` aus (`hp-schiene-panel`, `Fachplaner` je 1× im Bundle). Es fehlt nur noch die Sichtprobe (iframe 1440/1024/375) | dito, B1 |
| **AUF-21** | **Werkzeug-Paket einsortieren (I1–I4)** — 110 SVGs nach `public/hausplaner/icons/tools/`; Katalog-Austausch mit Adapter Paket→`ToolDefinition` (Konflikt-Regel: der neue Code passt sich dem Bestand an); die 47 DTP-Reste belegt stilllegen; `canPin`/`priority` in die Zonen-Kuratierung führen. **Gesperrt bis AUF-20** | Planner → Generator | `I1 offen` — **I2** (`289ccc8`), **I3** (`ccdc93b`) und **I4** (`4932b36`) sind **FREIGABE** (Evaluator-Härtung 25.07.); **I1** (`7bbf9ff`, 110 Icons ablegen) ist der einzige Teil ohne Votum | `docs/planner/inventur-werkzeug-icons-2026-07-25.md` §7 | **Neue Priorität 25.07.: I2 + I3 sind Schritt 3 und 4 zur fertigen Werkzeugleiste — Vorrang vor L2/L3.**
| **AUF-30** | **Render-Pfad-Testinfra** — `node --experimental-strip-types` lädt keine `.tsx`, es gibt kein DOM; deshalb importiert keine der 80+ Testdateien eine `.tsx`. Auflage 3 aus dem A1-Votum ist damit **nicht erfüllbar**, nicht verletzt. Weg: esbuild-Loader in `test-hooks.mjs` (esbuild liegt in `node_modules`). Bis dahin gilt die Browser-Sichtprobe als Ersatzbeleg | Generator | `BERICHTET — wartet auf Evaluator` (Commit siehe Ledger) — Auflage 3 jetzt erfüllbar | A1-Votum Auflage 3, A2-Bericht |

---

### 3c. Bei Yama — Willensfragen

**Elf Fragen, von denen keine die Kette blockiert.** Sie stehen hier, weil keine Instanz sie in Yamas
Vertretung entscheidet (§5) — nicht, weil auf sie gewartet würde.

| Nr | Auftrag | Rolle | Status | Datei / Beleg |
|---|---|---|---|---|
| **AUF-5** | Die zwei vom Generator **zurückgegebenen** Punkte einordnen: `treppeSvg.ts:38`, `szene.ts:16` | Planner | `BERICHTET — wartet auf Yama` | Ledger „SCHRITT 2 ERGEBNIS" (Z. 1171); Einordnung im Ledger-Block „AUF-5 eingeordnet" (`e676023`) |
| **AUF-6** | stopp-1 Teil I — Re-Check fahren, Teil I schließen, Dokument nachziehen | — | `BEI YAMA` | Yamas Auftragsdatei, Auftrag 3 |
| **AUF-7** | `auto/hausplaner-ui-3a` — mergen oder bewusst überschreiben (fork `f3e38d6`, lokal `df0dbdb`) | — | `BEI YAMA` | Yamas Auftragsdatei, Auftrag 4 |
| **AUF-8** | Branch-Hygiene — welche der 27 Branches dürfen weg | — | `BEI YAMA` | Yamas Auftragsdatei, Auftrag 5 |
| **AUF-10** | **Posten T2b** — Palette in `geometry/`: soll die Treppen-Lauflinie überhaupt Markenfarbe sein, und darf `geometry/` Farben kennen? (`treppeSvg.ts:35-42`, neun Aufrufstellen, kein Parameter) | — | `BEI YAMA` | Ledger-Block „AUF-5 eingeordnet" (`e676023`), Abschnitt Willensfrage |
| **AUF-13** | **Tote Naht Snapshots** — `objekt.blade.php:94` setzt `data-snapshots-url`, `routes/web.php:5003-5008` liefern drei Routen, `main.tsx:63` liest ausschließlich `dataset.speichernUrl`. Willensfrage: welcher Version wird die Anbindung zugeordnet — oder wird die Fläche bis dahin ehrlich als `in_entwicklung` ausgewiesen? | — | `BEI YAMA` | Ledger-Block „Planner-Messung 25.07." (gemessen gegen `f60b923`) |
| **AUF-15b** | **K6 neu schneiden** — der Evaluator hat belegt: `app/` enthält **30** rohe Farbwerte außerhalb `studioDaten.ts` (ConfigWizard 2 · StartView 3 · DreiDBereich 4 · GuidedView 15 · HausplanerStudio 6). T1 war auf `HausplanerApp.tsx` geschnitten (50→0), nicht auf `app/*` (80→30). Entweder wird das Kriterium künftig auf die **geänderten Zeilen** bezogen (so formuliert es die `frontend-entwickler`-Linse ohnehin), oder die Ablösung der 30 Restwerte wird ein eigener beauftragter Posten | Planner → dann Yama | `BEI YAMA` — Willensfrage: eigener Posten oder Kriterium umformulieren | Evaluator-Votum Dashboard v2 Batch 1 |
| **AUF-17** | **`Strg+K` war belegt** — `toolFuerShortcut('k')` liefert `decke`, und der Kürzel-Zweig in `taste()` prüfte keine Modifikatoren; vor `5092b10` setzte `Strg+K` das Werkzeug „Decke". Der Generator hat den Palette-Zweig davor einsortiert: `Strg+S` speichert unverändert, `K` allein setzt weiter „Decke", `Strg/⌘+K` öffnet die Palette. Bewusste Verhaltensänderung für genau eine Kombination — soll die Palette diese Kombination behalten? | — | `BEI YAMA` | Generator-Bericht Dashboard v2 Batch 2 |
| **AUF-23** | **Elevation-/Overlay-Tokens fehlen** — 16 der 36 verbliebenen Rohwerte sind Schatten/Scrim (`rgba(28,40,48,.05)` u. a.); `studioDaten.ts` kennt keine Elevation-Rolle. Dazu: `T.surface` trägt nach N1 zwei Rollen (Fläche + Text-auf-Farbe, Kandidat `T.onFilled`), und ~8 Werte sind „nah dran" an vorhandenen Tokens — ihre Angleichung wäre eine **sichtbare** Farbänderung, also eine Willensfrage | — | `BEI YAMA` | Generator-Bericht Nacharbeit, Operanden-Gate |
| **AUF-41** | **Import-Recht fehlt im CRM** — der Generator hat es bei AUF-36 gemessen und zurückgegeben: `routes/web.php` kennt nur `Hausplaner,read` und `Hausplaner,update`, ein `Hausplaner,import` gibt es nicht. Die **acht** Import-Werkzeuge sind deshalb heute gesperrt mit dem Grund „Keine Berechtigung zum Importieren." Willensfrage: hängt Import an `update`, oder bekommt er ein eigenes Recht? **Planner-Empfehlung: eigenes Recht** — Import zieht fremde Daten (IFC/DXF/Bilder) ins Modell; wer zeichnen darf, muss nicht importieren dürfen. Berührt `routes/` und die Rechteverwaltung, also Tor 1 | — | `BEI YAMA` | Ledger „GENERATOR-BERICHT AUF-36", Rückgabe 1 |

---

### 3d. Abgeschlossen — im Archiv

Abgenommen, entschieden oder entfallen, wortgleich in **`docs/auftraege/AUFTRAGSTAFEL-ARCHIV.md`** —
**17 Posten**: AUF-1 · AUF-2 · AUF-3 · AUF-4 · AUF-9 · AUF-11 · AUF-12 · AUF-14 · AUF-15a · AUF-16 ·
AUF-19 · AUF-20 · AUF-24 · AUF-25 · AUF-26 · AUF-31 · AUF-32. Nicht gelöscht, nur nicht mehr im Weg.

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
