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
**11** Arbeitsvorrat · **10** Abnahme · **11** bei Yama · **7** im Archiv — Summe geprüft, 39.

---

### 3a. Arbeitsvorrat — hier wird gezogen

**Der erste Posten trägt ⚡ und ist der einzige, der gezogen werden darf** (§1c) — alles darunter ist
Reihenfolge, kein Angebot. `GESPERRT`: Vorbedingung nicht erfüllt, Grund steht in der Zeile.

| Nr | Auftrag | Rolle | Status | Datei / Beleg |
|---|---|---|---|---|
| **AUF-34** ⚡ **AKTIV** | **Arbeitsbereiche statt 22 Gruppen nebeneinander** — die Leiste läuft bei 1440 px über drei Zeilen; `Bearbeiten` hat 13 Werkzeuge, `TGA` und `Sanitär` je eines. Die Ebene existiert ungenutzt im Code (`uiState.ts:23 activeWorkspace`, genau **ein** Wert; Paket-Werkzeuge tragen `supportedWorkspaces: []`). Entscheidung: 8 durchgängige Gruppen + 11 gebundene auf **fünf** Arbeitsbereiche aus Yamas Entwurf. Drei Lücken benannt und zurückgegeben (Dach, Heizlast, Bad/Küche) | Generator | `IN ARBEIT — Generator (nativ)` — gezogen 25.07., AUF-27 ist berichtet (`894954a`). Auftrag `generator-auftrag-auf34-arbeitsbereiche.md` inkl. **Nachtrag 2 (15 Themen statt 22 Kategorien)**. **sichtbar** ⇒ Sichtprobe Teil der Abnahme | Planner-Vorschlag, von Yama angenommen 25.07. |
| **AUF-37** | **Bundle-Rebuild für AUF-27** — `894954a` (Reiter) ist **nicht ausgeliefert**: kein Rebuild danach, das servierte Bundle kennt die drei Reiter nicht. Zweites Bundle-Loch nach Batch 2. Rebuild als **eigener Commit** direkt nach dem Code-Commit, Bericht mit Rohausgabe: Größe/Zeitstempel + `grep -c` auf eine Zeichenkette aus dem Slice + Quell-Commit. Danach holt der Evaluator die Sichtprobe nach (iframe, 1440/1024/375) | Generator (nativ, x64) | `OFFEN` — klein, blockiert die AUF-27-Sichtprobe | Bundle-Regel `docs/agents/06-laufzeiten-und-takt.md` §8 |
| **AUF-36** | **Funktionsvertrag der 110 Werkzeuge einhängen** — `~/Downloads/hausplaner_svg_tool_functions.zip`. Je Werkzeug `commandId · family · inputs · outputs · preconditions · sideEffects · undoable · auditRequired · serviceMethod`. **Vokabulare sind klein und damit abbildbar: 12 Vorbedingungen, 11 Seiteneffekte, 9 Familien, 110 commandIds.** Bindend: `preconditions` gehen als **Daten** in `resolveToolState` (kein zweites `resolveDisabledReasons`); `commandId`/`undoable`/`auditRequired` sind **Metadaten**, die Ausführung bleibt bei `applyCommand` mit inversen Patches (kein `runTool` daneben, sonst gehen Undo und `CommandAbgelehnt` verloren) | Planner → Generator | `OFFEN` — **nach AUF-34** | Planner-Bewertung 25.07., Messung am ausgepackten Paket |
| **AUF-35a** | **„Markieren" — Flächen- und Zonenauswahl** (Yama, 25.07., aus Architektensicht). Drei Vorgänge, nur einer existiert: Objektauswahl **da** · **Flächenauswahl fehlt vollständig** (Wandseite, einzelne Dachfläche — Voraussetzung für Material/Fassade/Dachdeckung) · **Zone markieren**: Schema kennt **6** Typen (`room · underfloor_heating · pv_area · maintenance_area · sound_area · restricted_area`), das Paket hat dafür **ein** Werkzeug (`raum`), Grep `ZONE` in den Commands → **0 Treffer**. **Das ist die Naht zwischen Geometrie und Fachmodulen** und erklärt, warum die 13 Engines keinen Eingang haben. **Vor L2/L3, nach AUF-34.** Planner misst zuerst drei Fragen, dann Auftrag | Planner → Generator | `OFFEN` — Messung zuerst | Planner-Block „Markieren ist kein Label" |
| **AUF-3** | **T1-Abnahme** (Token-Konsolidierung + `decke → bau`) | Evaluator | `OFFEN` — Vorbedingung erfüllt: Hash `9ec3b25` | `evaluator-auftrag-t1-token-konsolidierung-und-decke.md` **inkl. §10** |
| **AUF-29** | **Blinde Gegenzeichnung A2** — die A2-Abnahme (`728ae69`) ist belastbar, stammt aber von einer anchored Instanz (vom Evaluator selbst offengelegt). Die frische Instanz, die AUF-1 zog, zeichnet gegen: erst messen, dann lesen. **Blockiert nichts** — eine zusätzliche unabhängige Abnahme kann eine Freigabe nur härten, nie weichmachen | Evaluator, frische Instanz | `OFFEN` — nicht blockierend | Planner-Entscheidung 25.07. im Ledger |
| **AUF-18** | **Drei zurückgegebene Punkte einordnen** — (a) `RouteNode` (Leitungen) hat keine Gruppe im Projektbaum; §32 legt sechs fest, heute erzeugt kein Werkzeug Routen. (b) Befund-Historie mit `grund`/Zeitstempel/Bauteilbezug braucht eine Store-Änderung → Kandidat v3. (c) `Enter` auf `loeschen`/`duplizieren` ruft die vorhandenen Funktionen — vom Auftrag nicht ausbuchstabiert, Rückbau wäre eine Zeile | Planner | `OFFEN` | Generator-Bericht Dashboard v2 Batch 2, Abschnitt „Zurückgegeben" |
| **AUF-22** | **Kollisionsschutz zur Regel machen** — am 25.07. haben zwei Generator-Instanzen (nativ + Cowork) gleichzeitig an `generator-auftrag-dashboard-v2-nacharbeit.md` gearbeitet; `HausplanerApp.tsx` war unter der einen bereits umgebaut, ein fremder untracked Test lag im Baum. Nur weil beide freiwillig vorher auf der Tafel gezogen haben (`c3249d4`, `ca4153b`), ist nichts überschrieben worden. §1 der Tafel schreibt das Ziehen vor — durchgesetzt wird es von nichts. Vorschlag zu bewerten: Ziehen als Vorbedingung im Auftragstext jedes Generator-Auftrags, plus Pflicht-`git status`-Prüfung auf fremde untracked Dateien vor dem ersten Schreibzugriff | Planner | `OFFEN` | Generator-Bericht Nacharbeit, Abschnitt „Kollision" |
| **AUF-35b** | **Flächen- und Zonenauswahl** — Wandseite und einzelne Dachfläche greifen (fehlt vollständig); Zone markieren für die **6** Schema-Typen, von denen das Paket nur `raum` kennt und die Commands **keinen**. **Die Naht zu den 13 Engines.** Braucht AUF-35a als Fundament | Planner → Generator | `GESPERRT` — bis AUF-35a durch ist | Planner-Block „Markieren ist kein Label" |
| **AUF-28** | **15 falsche Versprechen aus der Navi nehmen** (Spur A) — die Zone `weitere` zeigt dem Nutzer „Links ausrichten · Hand · Zoom · Freie Transformation …" als `in Entwicklung`. Die Icon-Inventur belegt: DTP-Erbe, kommt nie. Ausführbarer Teil: die 15 Regeln in `toolPresentation.ts` von `weitere` auf `versteckt`; die Navi braucht dann einen ehrlichen Leerzustand. **Willensteil bleibt bei Yama:** womit `weitere` neu belegt wird (Kandidat: Fach-Werkzeuge aus dem 110er-Paket, AUF-21/I2) | Generator | `GESPERRT` — bis A2 abgenommen (`toolPresentation.ts`) | dito, B2 |
| **AUF-33** | **Die 13 Rechen-Engines zu den Fachplaner-Flächen** — `engine-fbh · -heizkoerper · -heizkreis · -abwasser · -kueche · -pv · -uwert · -fensterprodukt · -sparren · -treppe · -holzmengen · -holzbauteile · -schifter` wandern zu den 19 L4-Flächen; danach fällt der Übergangs-Reiter „Fachplaner" ersatzlos weg. Gemessen: 3 klare Paare, 10 Engines ohne Entsprechung, 16 L4-Flächen ohne Engine — **keine Doppelung, zwei Sortierungen** | Planner → Generator | `GESPERRT` — bis L2 entschieden ist (welche Engine wird das Panel-Muster; von Yama zurückgestellt) | Planner-Block „Der Begriff Fähigkeiten wird abgeschafft" |

---

### 3b. Abnahme-Stapel — berichtet, wartet auf Prüfung

**Zehn Posten warten auf den Evaluator** — der Stapel wächst schneller, als er abgetragen wird; das ist
der Engpass der Kette, nicht die Bauleistung. Niemand nimmt eigene Arbeit ab (§1.4). **AUF-27** hängt an
**AUF-37**: ohne Bundle-Rebuild ist die Sichtprobe nicht führbar (§8 in `docs/agents/06-laufzeiten-und-takt.md`).

| Nr | Auftrag | Rolle | Status | Datei / Beleg |
|---|---|---|---|---|
| **AUF-27** | **Linke Spalte macht drei Jobs** (Spur A) — Werkzeuge + Fähigkeiten + Projektbrowser teilen sich 220 px und **eine** Scroll-Höhe; der Projektbrowser ist erst nach 20 Scroll-Ticks sichtbar. Verstoß gegen „ein Hauptjob je Fläche" und „Sidebar = Navigation, keine Daten". Entscheidung: drei Abschnitte mit je eigener Scroll-Höhe, Werkzeuge fix oben, Fähigkeiten und Projekt mit Kopf und Zähler | Generator | `BERICHTET` — umgesetzt `894954a` (Reiter `Werkzeuge · Projekt · **Fachplaner**`, je eigene Scroll-Höhe; Reiter-Muster aus v2.2/AUF-19 nach `ReiterLeiste.tsx` extrahiert statt kopiert). Tore 0/0/0/0, 798→810 Tests, Gegen-Beweis geführt, Sichtprobe 1440+1024 px im Bericht. **Ballbesitz → Evaluator** | dito, B1 |
| **AUF-31** | **IDs der 110 eindeutschen** — `werkzeugPaket.ts` trägt die englischen Paket-IDs (`u-value`, `thermal-envelope`, `floor-heating` …), weil die Namenstabelle bei I2 noch uncommittet war. Vorlage: `docs/planner/eindeutschung-110-paket-ids.md` (führend). Umbenennen von `werkzeugPaket.ts` + den 110 Icon-Dateien; die 9 Bestands-IDs bleiben byte-genau; die 16 schema-gebundenen behalten ihren englischen Schutzwert im Adapter | Generator | `BERICHTET — wartet auf Evaluator` (Commit `2deb6a5`) — Weg 1 umgesetzt: 101 IDs deutsch, 9 zusammengeführt; **zwei Tabellenfehler gemeldet** (`ffnung`→`oeffnung`, `bergabepaket`→`uebergabepaket`) | Planner-Befund 1 zu I2 |
| **AUF-30** | **Render-Pfad-Testinfra** — `node --experimental-strip-types` lädt keine `.tsx`, es gibt kein DOM; deshalb importiert keine der 80+ Testdateien eine `.tsx`. Auflage 3 aus dem A1-Votum ist damit **nicht erfüllbar**, nicht verletzt. Weg: esbuild-Loader in `test-hooks.mjs` (esbuild liegt in `node_modules`). Bis dahin gilt die Browser-Sichtprobe als Ersatzbeleg | Generator | `BERICHTET — wartet auf Evaluator` (Commit siehe Ledger) — Auflage 3 jetzt erfüllbar | A1-Votum Auflage 3, A2-Bericht |
| **AUF-25** | **L4 — Fachplaner-Untermodule bekommen eine Fläche** statt des Toasts „Konfigurator folgt" (`HausplanerStudio.tsx` ~Z. 65). Je Fläche: Kopf · Zweck · **Feldstruktur-Vorschau** (deaktivierte Ein-/Ausgangsfelder) · Leerzustand mit `ZustandBadge`. Planner-Entscheidung zu Fahrplan §4.2: **tiefe Fläche, nicht flache** — eine flache müsste später ersetzt, eine tiefe nur verdrahtet werden. **Keine Vorbedingung**, berührt `HausplanerApp.tsx` **nicht** (dort läuft AUF-4) | Generator | `BERICHTET — wartet auf Evaluator` (Commit `17c8be2`) | `generator-auftrag-l4-fachplaner-flaechen.md` · Fahrplan §3 L4 |
| **AUF-21** | **Werkzeug-Paket einsortieren (I1–I4)** — 110 SVGs nach `public/hausplaner/icons/tools/`; Katalog-Austausch mit Adapter Paket→`ToolDefinition` (Konflikt-Regel: der neue Code passt sich dem Bestand an); die 47 DTP-Reste belegt stilllegen; `canPin`/`priority` in die Zonen-Kuratierung führen. **Gesperrt bis AUF-20** | Planner → Generator | `I4 ERLEDIGT` (`4932b36`, Freigabe/sichtbar) · I1 `UMGESETZT` (`7bbf9ff`, Abnahme offen) · I2 `BERICHTET` (`289ccc8`) · I3 `BERICHTET` (`ccdc93b`) · **I4 `BERICHTET` (`4932b36`): 110 sichtbar, 22 Gruppen, Anheften in localStorage** — Auftrag `generator-auftrag-i4-werkzeuge-sichtbar.md`, Vorbedingung AUF-31 erfüllt | `docs/planner/inventur-werkzeug-icons-2026-07-25.md` §7 | **Neue Priorität 25.07.: I2 + I3 sind Schritt 3 und 4 zur fertigen Werkzeugleiste — Vorrang vor L2/L3.**
| **AUF-19** | **Reiter-Muster vervollständigen** — B3: `role="tabpanel"`, `aria-controls`, `id`-Verknüpfung fehlen. B4: roving `tabIndex` ohne Fokusnachführung, nach ArrowRight liegen Fokus und Auswahl auseinander | Generator | **UMGESETZT** `8587ce7` — Abnahme offen (Evaluator) | `generator-auftrag-dashboard-v2-nacharbeit.md` §N3 |
| **AUF-16** | **B1: Options-Leiste wird bei jeder Mausbewegung neu gemountet** — `KontextOptionenLeiste` ist als `const` im Rumpf von `HausplanerApp` definiert (`:298`) und als Element gerendert (`:835`); `onMouseMove` (`:873`) rendert fortlaufend. Wert geht nicht verloren, betroffen sind Fokus, Tastaturbedienung und DOM-Arbeit. Das Muster hat der Auftrag selbst angeordnet — bei `OpBtn` folgenlos, bei einem `<select>` nicht. **Vor der sichtbaren Freigabe von v2 zu entscheiden**, zusammen mit der ausstehenden Sichtprobe auf x64-nativ | Generator | `BERICHTET — wartet auf Evaluator` (Commit `982384d`) | Evaluator-Votum Dashboard v2 Batch 1, Befund B1 |
| **AUF-15a** | **Die 30 rohen Farbwerte ablösen** — wertgleich auf die vorhandenen Tokens in `studioDaten.ts`, Muster wie T1 (`9ec3b25`). Kein Farbwert ändert sich, nur seine Herkunft. Operanden-Gate: fehlt ein wertgleiches Token, wird der Fall zurückgegeben, nicht erfunden | Generator | **UMGESETZT** `2d927fc` — Abnahme offen (Evaluator). Hex-Zeilen 30 → 17; 24 Werte per Operanden-Gate zurückgegeben (kein wertgleiches Token) | `generator-auftrag-dashboard-v2-nacharbeit.md` §N1 |
| **AUF-9** | **Posten T2a** — der Kommentar `renderers/three-d/szene.ts:16` nennt `#93c21c`, drei Zeilen tiefer steht `FARBE_AUSWAHL = 0xa3e635`, das Token sagt `#7fae1c`. Kommentar auf den **tatsächlichen** Wert richtigstellen; **kein** Farbwert im Code ändern | Generator | `BERICHTET — wartet auf Evaluator` (Commit `fbc5308`) | Ledger-Block „AUF-5 eingeordnet" (`e676023`), Messtabelle „drei Grüns" |
| **AUF-2** | **T1 + `decke` committen** — die sechs gestagten Dateien als **eigener** Commit mit Pfadangabe | Generator (nativ) | `BERICHTET — wartet auf Evaluator (AUF-3)` | COMMIT-FREIGABE im Ledger (Z. 1006), Dateiliste dort |

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
| **AUF-11** | **Layout-Fahrplan L1–L7** — gemessene Inventur der fünf Layout-Ebenen (17 Dateien / 3.343 Zeilen) plus Fahrplan; enthält drei Willensfragen an Yama (welche Engine wird Panel-Muster, wie tief eine leere L4-Fläche, darf L4 vorgezogen werden). **Reihenfolge abgelöst** durch AUF-12 — die Inventur bleibt gültig, die L1–L7-Abfolge ist es nicht mehr | Planner | `BEI YAMA` — Fach-Freigabe, **hinter AUF-12** | `docs/fahrplan-frontend-layout-hausplaner.md` (Commit `5af3e18`) |
| **AUF-13** | **Tote Naht Snapshots** — `objekt.blade.php:94` setzt `data-snapshots-url`, `routes/web.php:5003-5008` liefern drei Routen, `main.tsx:63` liest ausschließlich `dataset.speichernUrl`. Willensfrage: welcher Version wird die Anbindung zugeordnet — oder wird die Fläche bis dahin ehrlich als `in_entwicklung` ausgewiesen? | — | `BEI YAMA` | Ledger-Block „Planner-Messung 25.07." (gemessen gegen `f60b923`) |
| **AUF-14** | **Styling-Strategie** — `build:hausplaner` erzeugt **keine** `hausplaner.css`; der Blade-Link bleibt ungesetzt, das gesamte Styling liegt inline im TSX. Vor v2.3–v2.5 zu klären, ob das so bleibt oder ob v2 die Ablösung mitnimmt | — | `BEI YAMA` | Ledger-Block „Planner-Messung 25.07." |
| **AUF-15b** | **K6 neu schneiden** — der Evaluator hat belegt: `app/` enthält **30** rohe Farbwerte außerhalb `studioDaten.ts` (ConfigWizard 2 · StartView 3 · DreiDBereich 4 · GuidedView 15 · HausplanerStudio 6). T1 war auf `HausplanerApp.tsx` geschnitten (50→0), nicht auf `app/*` (80→30). Entweder wird das Kriterium künftig auf die **geänderten Zeilen** bezogen (so formuliert es die `frontend-entwickler`-Linse ohnehin), oder die Ablösung der 30 Restwerte wird ein eigener beauftragter Posten | Planner → dann Yama | `BEI YAMA` — Willensfrage: eigener Posten oder Kriterium umformulieren | Evaluator-Votum Dashboard v2 Batch 1 |
| **AUF-17** | **`Strg+K` war belegt** — `toolFuerShortcut('k')` liefert `decke`, und der Kürzel-Zweig in `taste()` prüfte keine Modifikatoren; vor `5092b10` setzte `Strg+K` das Werkzeug „Decke". Der Generator hat den Palette-Zweig davor einsortiert: `Strg+S` speichert unverändert, `K` allein setzt weiter „Decke", `Strg/⌘+K` öffnet die Palette. Bewusste Verhaltensänderung für genau eine Kombination — soll die Palette diese Kombination behalten? | — | `BEI YAMA` | Generator-Bericht Dashboard v2 Batch 2 |
| **AUF-23** | **Elevation-/Overlay-Tokens fehlen** — 16 der 36 verbliebenen Rohwerte sind Schatten/Scrim (`rgba(28,40,48,.05)` u. a.); `studioDaten.ts` kennt keine Elevation-Rolle. Dazu: `T.surface` trägt nach N1 zwei Rollen (Fläche + Text-auf-Farbe, Kandidat `T.onFilled`), und ~8 Werte sind „nah dran" an vorhandenen Tokens — ihre Angleichung wäre eine **sichtbare** Farbänderung, also eine Willensfrage | — | `BEI YAMA` | Generator-Bericht Nacharbeit, Operanden-Gate |

---

### 3d. Abgeschlossen — im Archiv

Abgenommen oder entfallen, wortgleich in **`docs/auftraege/AUFTRAGSTAFEL-ARCHIV.md`**:
AUF-1 · AUF-4 · AUF-12 · AUF-20 · AUF-24 · AUF-26 · AUF-32. Nicht gelöscht, nur nicht mehr im Weg.

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
