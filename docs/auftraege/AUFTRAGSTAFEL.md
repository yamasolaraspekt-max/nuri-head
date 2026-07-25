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

**Rohr-Regel (Planner, 25.07., nach zwei zerbrochenen Zeilen):** Ein `|` im Zellentext spaltet die
Tabellenzelle — **auch innerhalb von Backticks**. AUF-47 und AUF-51 hatten dadurch 9 statt 7 Felder, der
Status stand in der falschen Spalte, und die Tafel las sich für jede Instanz falsch. Rohr-Zeichen in
Code-Beispielen **umschreiben**, nicht maskieren (`\|` überlebt das nächste Skript nicht). **Nach jedem
Tafel-Schreiben prüfen: jede `| **AUF-`-Zeile hat genau 7 Felder.**

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
**21** Arbeitsvorrat · **1** Abnahme · **1** bei Yama · **37** im Archiv — Summe geprüft, 60.

> **25.07., 23:15 — vier Fragen in Yamas Vertretung entschieden.** Er hat ausdrücklich delegiert
> („nimm deine Empfehlungen an"). **Das ist die Ausnahme, nicht die Regel** (§5: keine Instanz entscheidet
> eine Willensfrage in seiner Vertretung) — deshalb steht an jeder der vier Zeilen, dass die Entscheidung
> vom Planner stammt und wie sie zurückzudrehen ist. Zwei ändern nichts (AUF-7, AUF-8: keine Aktion),
> zwei werden gebaut (**AUF-55**, **AUF-56**). Bei Yama liegt jetzt **ein** Posten: stopp-1 Teil I,
> der einzige außerhalb des Hausplaners.

> **25.07., 23:00 — fünf Willensfragen entschieden.** AUF-41 (eigenes Import-Recht) · AUF-15b (Farb-Kriterium
> auf die geänderten Zeilen) · AUF-5 und AUF-10 (Farbe als Parameter) · AUF-17 (Strg+K bleibt die Palette).
> Zwei davon brauchen Bau: **AUF-53** und **AUF-54**. Eine (AUF-17) bestätigt den Ist-Zustand und kostet
> nichts. Bei Yama liegen damit noch **fünf** statt zehn.

> **Nachmessung 25.07., 22:50 — der Abnahme-Stapel war leer, nicht voll.** Yama bat darum, den
> Evaluator zur Abarbeitung anzuschreiben. Vor dem Schreiben gemessen: **sieben der sieben Posten
> hatten bereits ein committetes Votum** (`7293a2d` · `5dea3c2` · `a2188e6` · `7fe6627` · `ea61ea1` ·
> `134eb0e` · `33ad6d6`). Die Tafel hinkte hinterher, nicht der Evaluator. **Zum zweiten Mal heute**
> derselbe Fehler von mir — beim ersten Mal waren es acht Posten (`32d2ecc`). Die Mahnung wäre an
> jemanden gegangen, der schneller war als sein Register.

> **Reihenfolge in 3a ist ab hier Yamas Befundliste**, nicht die Nummernfolge: AUF-39 (Wizard erfindet
> Zustände) · AUF-43 (Geschoss-Bedienung) · AUF-45 (niemand sieht, wo man anfängt) — das sind genau die
> drei Dinge, die ihm beim Hinsehen aufgefallen sind. Danach das Kleine und Mechanische.

> **Layout-Untersuchung 25.07., 22:30** (Yama: „schau dir das Layout mal an") — Sichtprobe über alle
> fünf Ebenen der Inventur bei 1440/1024/375 px, jeder Befund im Code belegt:
> **`docs/planner/ux-befund-layout-alle-ebenen-2026-07-25.md`**. Neun Befunde: drei waren bereits
> beauftragt (AUF-39, AUF-40 A+B), vier sind neu (**AUF-43 bis AUF-46**), einer hat sich beim Messen
> **widerlegt** (B9 — der Knopf, den ich abgeschnitten zu sehen glaubte, endet 13 px vor dem Rand).
> Kernsatz: die Oberfläche ist nicht kaputt, sie ist an vier Stellen **unehrlich** und an zwei Stellen
> **stumm** — sie sagt nirgends, wo man anfängt.

> **Sperren-Nachmessung 25.07., 21:40:** Alle sechs `GESPERRT`-Zeilen gegen ihren Sperrgrund gehalten.
> **AUF-28 war seit Stunden zu Unrecht gesperrt** („bis A2 abgenommen" — A2 lag längst frei) und ist
> **inzwischen ganz entfallen**: der Katalog-Tausch hat die 15 DTP-Versprechen mit erledigt. Drei weitere
> Sperren nannten eine Reihenfolge, die nicht gemessen war (AUF-38/39/40). **Nachtrag 22:10: die drei
> Auftragsdateien liegen** — AUF-38 und AUF-39 sind damit `OFFEN`, AUF-40 nur im Frontend-Teil; sein
> Backend-Teil bleibt bei Yama, weil er Migration, Route und Controller berührt.

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
| **AUF-51** | **Die Zeichenfläche ist verschiebbar erklärt, aber der Verschub wird verworfen** (Playwright meldete eine react-konva-Warnung; **Ursache selbst gemessen**) — `HausplanerApp.tsx:1280` setzt am `<Stage>` `draggable`, **ohne jeden Drag-Handler**. Die vier Node-Ebenen (Z. 1332/1386/1481/1521) haben `onDragStart` und `onDragEnd`, **die Bühne selbst hat keinen**. Zugleich sind `x={80}` und `y={hoehe - 80}` **gesteuerte Werte ohne Zustand dahinter** (`grep` nach `setPan`, `panX`, `panY`: **leer**), und `onMouseMove` ruft `setCursor(...)` — **jede Mausbewegung erzeugt ein Rendern**, das die Bühne auf `x=80` zurücksetzt. Der Nutzer schiebt, und es springt zurück. `weltPunkt` (Z. 626-634) liest `stage.x()`, also die **echte** Position — für die Dauer des Zurückspringens widersprechen sich Anzeige und Koordinate | Generator | `BERICHTET` — umgesetzt `74fdcb4`, Bundle `31f33e6`. **Pan-Zustand eingeführt** (statt `draggable` zu entfernen): `weltPunkt` las die verschobene Bühne längst korrekt. `onDragMove`+`onDragEnd` mit Herkunftsprüfung, `null` = nie verschoben (Standardlage folgt der Fensterhöhe). Tore 0/0/0/0, 930→938, Mutation 1 rot. Sichtprobe: Verschub bleibt, Koordinate stimmt wieder mit der Anzeige. **Ballbesitz → Evaluator** | Ledger „Playwright-Audit nachgemessen" (25.07.) |
| **AUF-47** | **Speichern lügt auf der Testfläche** (externe Bewertung Nr. 4, nachgemessen) — `studio.blade.php:56` setzt **bewusst kein** `data-speichern-url`; `hausplanerStore.ts:168-172` steigt daraufhin **still** aus, sobald Szene **oder** `speichernUrl` fehlen — kein Status, keine Meldung. Der Knopf ist trotzdem **grün, primär und unbedingt aktiv** (`HausplanerApp.tsx:1061-1067`, kein `disabled`). **Verschärfung aus der Sichtprobe:** die Statusplakette daneben sagt **„Gespeichert · Rev. 1"** — auf einer Fläche, die nicht speichern kann, im Widerspruch zum Warnhinweis „Testfläche — wird NICHT gespeichert" in derselben Kopfzeile. Der Playwright-Lauf bestätigt: der Knopf sendet keinen Request und bleibt „erfolgreich" | Generator | `OFFEN` — klein. Knopf sperren **und** die Plakette auf den wahren Zustand setzen; der `save()`-No-Op selbst ist gewollt und bleibt | `docs/planner/ux-befund-layout-alle-ebenen-2026-07-25.md` + Ledger „Externe Frontend-Bewertung" (25.07.) |
| **AUF-44** | **Zwei „(geplant)"-Knöpfe und das Spiegeln im leeren Plan** (Befund B2) — `title` sagt es selbst: „Auswahl um 90° drehen **(geplant)**", „Als PDF-Planblatt exportieren **(geplant)**". Dieselbe Sorte falsches Versprechen, die I2 aus dem Katalog entfernt hat, in der Icon-Zeile stehengeblieben. Dazu nehmen „Grundriss spiegeln links/rechts + oben/unten" den prominentesten Platz im Eigenschaften-Panel ein — bei **0 Räumen · 0.00 m²** | Generator | `OFFEN` — klein und mechanisch; Willensteil (ganz entfernen oder ehrlich als „geplant" ausweisen wie die Fachplaner-Flächen) hängt an AUF-45 | `docs/planner/ux-befund-layout-alle-ebenen-2026-07-25.md` |
| **AUF-49** | **Dialog-Fokus und Tastatur unvollständig** (externe Bewertung Nr. 6, nachgemessen) — `FachFlaeche` hat `role="dialog"`, `aria-modal` und Escape, aber **keinen Fokuswechsel beim Öffnen, keine Fokusfalle, keine Fokus-Rückgabe beim Schließen**. `ConfigWizard.tsx:56` trägt die Dialogsemantik gar nicht. Dazu gemessen: **8×** `role="button"`, **10×** `key === "Enter"`, **1×** Leertaste — selbstgebaute Schaltflächen reagieren fast durchweg nicht auf die Leertaste (WCAG 2.1.1) **Playwright-Lauf 25.07., nachgemessen:** `ConfigWizard.tsx` hat **weder `role="dialog"` noch `aria-modal` noch einen Escape-Handler** (`grep` = 0 Treffer). Fokus bleibt nach dem Öffnen auf dem Hintergrundknopf, der erste Tab-Sprung landet **hinter** dem Dialog, Escape schließt nicht, Fokus wird weder eingeschlossen noch zurückgegeben. **Zum Vergleich:** `FachFlaeche.tsx:125,139` (AUF-33, heute gebaut) hat Escape, `role="dialog"` und `aria-modal` — die neue Schicht ist richtig, die Schuld liegt in der alten. Dazu **Zielgrößen**: Chips 27–40 px, CAD-Schaltflächen ~30×32 px (WCAG 2.5.5 verlangt 44). | Generator | `OFFEN` — rein additiv, berührt keine Fachlogik; gehört vor die L7-Abnahmerunde | Ledger „Externe Frontend-Bewertung" (25.07.), nachgemessen |
| **AUF-46** | **Startseite läuft bei 375 px um 283 px über** (Befund B5) — gemessen im iframe: `documentElement.scrollWidth = 658` bei `innerWidth = 375`, **43 Elemente** ragen über den rechten Rand. Bei **1024 px sauber** (`scrollWidth 1009`), bei 1440 sauber. Damit ist einer der drei Pflicht-Viewports aus L7 hart verletzt **Erweitert 25.07. (externe Bewertung Nr. 5, nachgemessen):** die Navigation klappt unter 900 px ein, aber Geführte Planung, Konfigurator und Expertenmodus tragen weiterhin **feste Spaltenbreiten** (`320px`, `300px`, `220px`, Panel 268px). Der Posten definiert die Breiten **390 · 768 · 1024 · 1440** verbindlich, statt nur die 375er-Kappung zu flicken **Ursache gefunden (Playwright + Code, 25.07.):** `GuidedView.tsx:59` setzt `gridTemplateColumns: '1fr 320px'` — eine **feste zweite Spalte**. Bei 390 px passt `1fr + 320px + 20px` nicht mehr; das Aufgaben-`aside` legt sich über den Inhalt und **fängt die Zeigerereignisse ab**: „Fenster konfigurieren" ist sichtbar, aber **nicht anklickbar**. Das ist kein Schönheitsfehler, sondern eine tote Schaltfläche. Bei **768 px** kein äußerer Überlauf, aber die festen Seitenleisten lassen nur einen schmalen Streifen für 2D/3D, und Teile der Werkzeugleiste liegen in versteckten Überläufen. | Generator | `OFFEN` — Messverfahren liegt (iframe fester Breite); gehört vor die L7-Abnahmerunde, nicht hinein | `docs/planner/ux-befund-layout-alle-ebenen-2026-07-25.md` |
| **AUF-57** | **Der Wegweiser braucht einen Anlass und einen Ort** (Rückgabe des Generators aus AUF-45) — der Mechanismus ist gebaut, gemessen und verriegelt, **erscheint aber nie**: `activeLevel.exists` ist nie verletzt, weil eine Szene immer mindestens ein Geschoss trägt. **Die Zahlen meines Auftrags waren überholt** (78/44/16 stammten aus der Zeit vor AUF-34; gemessen sind es 73/53/28). Der real größte Block sind **28 Werkzeuge, die einem anderen Arbeitsbereich gehören** — nicht das Geschoss. Der Planner entscheidet Anlass und Ort; Vorschlag: ein Hinweis am Arbeitsbereich-Wähler, der die messbare Zahl nennt, statt eines globalen ersten Schritts | Planner → Generator | `OFFEN` — **Planner zuerst** (Entwurf), dann Generator. Der Mechanismus aus AUF-45 wird **wiederverwendet**, nicht ersetzt | Ledger „GENERATOR-BERICHT AUF-45", Rückgabe 2 |
| **AUF-54** | **Farbe als Parameter statt in `geometry/`** (Entscheidung Yama, 25.07., aus AUF-5 und AUF-10) — `geometry/treppeSvg.ts` führt heute sechs rohe Farbwerte, darunter `#93c21c` für die Lauflinie, und wird an **neun Stellen** ohne Farbparameter aufgerufen. Die Farbe wird künftig hereingereicht; `studioDaten.ts` bleibt die einzige Quelle. **Wert- und verhaltenstreu:** der gerenderte Farbwert bleibt zunächst derselbe, nur seine Herkunft ändert sich | Planner → Generator | `OFFEN` — Auftrag liegt. `treppeSvg.ts` führt sechs rohe Werte, neun Aufrufstellen ohne Farbparameter. **Kein gerenderter Wert ändert sich** — nur seine Herkunft. Vorarbeit | `generator-auftrag-auf54-55-56-klein.md` |
| **AUF-56** | **Zwei Elevation-Token einführen** (Entscheidung 25.07., aus AUF-23) — `studioDaten.ts` kennt keine Elevation-Rolle. Gemessen: `rgba(28,40,48,.05)` **9×**, `rgba(28,50,55,.10)` **3×**. Zwei Token lösen diese zwölf Rohwerte **wertgleich** ab. **Ausdrücklich ausgenommen:** die rund acht Werte, die nur „nah dran" an vorhandenen Token liegen — sie anzugleichen wäre eine **sichtbare** Farbänderung und bleibt Yamas Entscheidung | Generator | `OFFEN` — Auftrag liegt. Zwölf Vorkommen der zwei häufigsten Schattenwerte werden wertgleich abgelöst; die acht „nah dran"-Werte **nicht** — das bleibt Yamas Entscheidung. Vorarbeit | `generator-auftrag-auf54-55-56-klein.md` |
| **AUF-58** | **`.gitignore` für die Sichtprobe-Kladden** (Meldung des Evaluators, 25.07.) — der native Generator legt bei jeder sichtbaren Scheibe zwei Hilfsdateien an (`public/_aufNN-sichtprobe.html`, `sichtprobe-aufNN.tmp.mjs`) und räumt sie hinterher weg. **Gemessen: in der gesamten Historie wurde noch **keine** committet** — die Disziplin hat siebenmal gehalten. Der Posten ist **Vorbeugung, keine Reparatur**: zwei Zeilen in `.gitignore` (`sichtprobe-*.tmp.mjs`, `public/_auf*-sichtprobe.html`) nehmen das Risiko dauerhaft heraus, dass eine Kladde in `public/` neben dem Bundle landet | Generator | `OFFEN` — sehr klein. `.gitignore` ist Wurzelverzeichnis, deshalb **nicht** vom Planner geschrieben (Cowork schreibt nur `docs/`) | Evaluator-Meldung 25.07., Standby-Bericht |
| **AUF-55** | **Snapshot-Fläche ehrlich ausweisen** (Entscheidung 25.07., aus AUF-13) — das Blade setzt `data-snapshots-url`, drei Routen existieren, `main.tsx` liest **null** Verweise. Bis die Anbindung kommt, wird die Fläche als `in Entwicklung` gekennzeichnet — mit demselben `ZustandBadge` und demselben Blindtext-Verbot wie die Fachplaner-Flächen aus AUF-25. **Die tote URL im Blade bleibt stehen** und wird nicht entfernt: sie ist die Naht, an der die spätere Anbindung ansetzt | Planner → Generator | `OFFEN` — Auftrag liegt. **Die tote URL im Blade bleibt stehen** (Naht für die spätere Anbindung); `routes/` und `views/` null Zeilen Diff. Sichtbar | `generator-auftrag-auf54-55-56-klein.md` |
| **AUF-52** | **L3 — die übrigen zwölf Rechen-Engines nach dem Treppen-Muster** (`engine-fbh · -heizkoerper · -heizkreis · -abwasser · -kueche · -pv · -uwert · -fensterprodukt · -sparren · -holzmengen · -holzbauteile · -schifter`). Das Muster ist abgenommen (AUF-33 L2, Votum `7293a2d`): `EnginePanel → EngineFlaeche → Fachflächen-Hülle → vorhandene Engine`. **Gruppenweise, nicht alle zwölf auf einmal** — Vorschlag: `dach-zimmerei` zuerst (Sparren · Holzmengen · Holzbauteile · Schifter, vier mit Render-Bezug), dann Haustechnik, dann der Rest. **Die drei Grenzen aus AUF-33 §3 gelten unverändert:** keine Rechenlogik im Panel, kein dynamischer Import, kein Schreiben ins Modell. Jede Engine schaltet erst auf `verfuegbar`, wenn sie wirklich angeschlossen ist | Planner → Generator | `OFFEN` — Auftrag liegt: **drei Scheiben** (dach-zimmerei 4 · tga-heizung 3 · Rest 5), jede einzeln abgenommen. Gemessen: **alle zwölf Module existieren**. Ausdrücklich erwartet, dass nicht alle zwölf anschließbar sind — begründete Rückgabe ist ein gültiges Ergebnis | `generator-auftrag-auf52-l3-zwoelf-engines.md` |
| **AUF-35b** | **Flächen- und Zonenauswahl** — Wandseite und einzelne Dachfläche greifen (fehlt vollständig); Zone markieren für die **6** Schema-Typen, von denen das Paket nur `raum` kennt und die Commands **keinen**. **Die Naht zu den 13 Engines.** Braucht AUF-35a als Fundament | Planner → Generator | `OFFEN` — **Planner-Messung 25.07. teilt den Posten:** `zoneType` kommt in `commands.types.ts` und `applyCommand.ts` **0×** vor (kein Zone-Command), der 3D-Renderer kennt `zone` **0×** (Zonen werden nicht dargestellt), `scene.types.ts:179` sagt selbst „P0 aktiv NUR `room` und NUR abgeleitet". **Flächenauswahl ist baubar, Zonenauswahl nicht** — man kann nicht auswählen, was nicht dargestellt wird. Der Auftrag wird auf Wandseite und Dachfläche geschnitten | Planner-Block „Markieren ist kein Label" |
| **AUF-29** | **Blinde Gegenzeichnung A2** — die A2-Abnahme (`728ae69`) ist belastbar, stammt aber von einer anchored Instanz (vom Evaluator selbst offengelegt). Die frische Instanz, die AUF-1 zog, zeichnet gegen: erst messen, dann lesen. **Blockiert nichts** — eine zusätzliche unabhängige Abnahme kann eine Freigabe nur härten, nie weichmachen | Evaluator, frische Instanz | `OFFEN` — nicht blockierend | Planner-Entscheidung 25.07. im Ledger |
| **AUF-18** | **Drei zurückgegebene Punkte einordnen** — (a) `RouteNode` (Leitungen) hat keine Gruppe im Projektbaum; §32 legt sechs fest, heute erzeugt kein Werkzeug Routen. (b) Befund-Historie mit `grund`/Zeitstempel/Bauteilbezug braucht eine Store-Änderung → Kandidat v3. (c) `Enter` auf `loeschen`/`duplizieren` ruft die vorhandenen Funktionen — vom Auftrag nicht ausbuchstabiert, Rückbau wäre eine Zeile | Planner | `OFFEN` | Generator-Bericht Dashboard v2 Batch 2, Abschnitt „Zurückgegeben" |
| **AUF-22** | **Kollisionsschutz zur Regel machen** — am 25.07. haben zwei Generator-Instanzen (nativ + Cowork) gleichzeitig an `generator-auftrag-dashboard-v2-nacharbeit.md` gearbeitet; `HausplanerApp.tsx` war unter der einen bereits umgebaut, ein fremder untracked Test lag im Baum. Nur weil beide freiwillig vorher auf der Tafel gezogen haben (`c3249d4`, `ca4153b`), ist nichts überschrieben worden. §1 der Tafel schreibt das Ziehen vor — durchgesetzt wird es von nichts. Vorschlag zu bewerten: Ziehen als Vorbedingung im Auftragstext jedes Generator-Auftrags, plus Pflicht-`git status`-Prüfung auf fremde untracked Dateien vor dem ersten Schreibzugriff | Planner | `OFFEN` | Generator-Bericht Nacharbeit, Abschnitt „Kollision" |
| **AUF-38** | **Inline-Styles ablösen** (Entscheidung Yama, 25.07., aus AUF-14) — 331 `style={{` in 35 Dateien wandern in eine echte Stilschicht; `build:hausplaner` erzeugt künftig `public/hausplaner/hausplaner.css`, die vorhandene `@if (file_exists(…))`-Bewachung in beiden Blades greift dann von selbst. **In Scheiben, nach Datei geschnitten** (HausplanerApp 132 · GuidedView 41 · ConfigWizard 39 · HausplanerStudio 34 · FachFlaeche 27 · StartView 20 · Rest 38) — nie zwei Scheiben gleichzeitig, weil dieselben Dateien der Werkzeugleiste gehören. **Wert- und verhaltenstreu:** kein gerenderter Farbwert ändert sich, `studioDaten.ts` bleibt die Quelle | Planner → Generator | `OFFEN` — Auftrag liegt: **acht Scheiben**, Scheibe 1 (Grundgerüst) wird eigens abgenommen. **Scheibe 7 ist entsperrt** — AUF-35a ist abgenommen; sie bleibt trotzdem die letzte, weil `HausplanerApp.tsx` auch AUF-43/45/48 trägt | `generator-auftrag-auf38-inline-styles.md` |
| **AUF-40** | **L6 — Start/Zuletzt an echte Projekte + Konfigurator-Paket serverseitig speichern.** Gemessen offen: `app/StartView.tsx:75` rendert die Demo-Liste `ZULETZT`, `app/ConfigWizard.tsx` lädt das `ConfiguratorPackage` als JSON **herunter**, statt es zu speichern. Hatte bis 25.07. ebenfalls **keinen Posten** (null Treffer auf `L6`). Braucht Backend-Anschluss, blockiert kein Layout | Planner → Generator | **Teil A `OFFEN`** (Frontend: echte Projektliste, drei Karten drei Ziele, ehrlicher Leerzustand) · **Teil B `BEI YAMA`** — Migration + Route + Controller berühren Tor 1 und werden nicht ohne seine Freigabe gezogen | `generator-auftrag-auf40-start-und-persistenz.md` |
| **AUF-42** | **`viewport.ready` ist heute eine Vereinfachung** — die Fähigkeit wird gesetzt, sobald `HausplanerApp` rendert; einen echten Renderer-Bereitschaftszustand führt der Store nicht. Der Generator hat das bei AUF-36 offengelegt statt es für gemessen auszugeben. **Folgenlos, solange es keine Ladeanimation gibt** — der Posten existiert, damit die Vereinfachung nicht später für eine Messung gehalten wird | Planner → Generator | `GESPERRT` — klein, ohne Dringlichkeit; erst wenn ein Ladezustand sichtbar wird | Ledger „GENERATOR-BERICHT AUF-36", Rückgabe 3 |
| **AUF-48** | **`HausplanerApp.tsx` zerlegen** (externe Bewertung Nr. 3, nachgemessen: **2.052 Zeilen**) — bündelt Canvas, Werkzeugleisten, Auswahl, Eigenschaften, Dach, Treppe, Palette, Tastatursteuerung und Layout in einer Datei. Sie trägt zugleich **132 der 331 Inline-Styles** (AUF-38) und ist die Datei, an der heute jeder zweite Posten arbeitet — Kollisions- und Regressionsrisiko | Planner → Generator | `GESPERRT` — **bewusst, nicht aus Bequemlichkeit:** ein Schnitt durch die meistberührte Datei des Projekts während laufender Posten (AUF-33, AUF-38, AUF-43, AUF-45) erzeugt genau die Kollision, die AUF-22 verhindern soll. Erst wenn die Layout-Posten durch sind. Planner schreibt dann den Schnitt | Ledger „Externe Frontend-Bewertung" (25.07.) |
| **AUF-50** | **Die 110 Werkzeuge funktionstüchtig machen — Fahrplan in vier Stufen** (Yama, 25.07.: „sollen wir jetzt coden funktionstüchtig machen"). **Gemessen:** die Verträge nennen **110 verschiedene** `commandId`, das Modell kennt **19** Command-Typen — die IDs sind Absichtserklärungen, keine Zeiger. Aufteilung: **41 rein Ansicht/Auswahl** (kein Command, kein Schema) · **69 modellverändernd**, davon `create` **40** (je ein Knoten-/Objekttyp ⇒ **Zod + `schema:hausplaner` + Bestandsdaten**), `modify` **20**, `workflow` **15**, `assign-or-calculate` **9** (hängt an den Engines, AUF-33 L3), `import` **8** (**hängt an AUF-41**, Yamas Rechte-Entscheidung), `view` 7 · `measurement` 5 · `selection` 4 · `domain` 2. **Yamas Schnitt-Entscheidung 25.07.: nach Aufwand, innerhalb seiner 15 Bereiche** — erst die 41 ohne Modellwirkung, dann `modify`, dann `create` mit Schema-Arbeit, `import`/`assign-or-calculate` zuletzt. **Nicht Bereich 1–15 der Reihe nach**, weil die Bereiche quer zum Aufwand liegen | Planner → Generator | `GESPERRT` — **Yamas Entscheidung 25.07.: erst Layout fertig** (AUF-39 · 43 · 45 · 44 · 47). Planner schreibt danach den Stufenplan; jede Stufe wird ein eigener Posten, nicht dieser hier | Ledger „Werkzeuge funktionstüchtig — die gemessene Lücke" (25.07.) |
| **AUF-53** | **Import-Recht `Hausplaner,import` einführen** (Entscheidung Yama, 25.07., aus AUF-41) — gemessen kennt `routes/web.php` heute nur `Hausplaner,read` und `Hausplaner,update`; die **acht** Import-Werkzeuge sind deshalb dauerhaft gesperrt mit dem Grund „Keine Berechtigung zum Importieren." Das neue Recht in die Rechteverwaltung, an die Import-Routen hängen, und die Fähigkeit an `AktivierungsKontext.permissions` reichen — **die Vorbedingung `permission.import` steht bereits im Funktionsvertrag** und geht von selbst auf grün. **Kein Werkzeug wird freigeschaltet, das das Recht nicht hat** | Planner → Generator | `GESPERRT` — Auftrag fehlt (Planner). Berührt `routes/`, `app/Http/` und eine Migration, also Tor 1: **Yama hat das Ziel entschieden, den Bau gibt er separat frei** | Ledger „Fünf Willensfragen entschieden" (25.07.) |

---

### 3b. Abnahme-Stapel — berichtet, wartet auf Prüfung

**Fünf Posten, davon drei reine Sichtprobe.** `91d9592` hat AUF-27 **und** AUF-34 ausgeliefert — beide
Sichtproben sind damit führbar (iframe 1440/1024/375, §8 in `docs/agents/06-laufzeiten-und-takt.md`).
**AUF-21/I1** ist der einzige Teil des Icon-Pakets ohne Votum, **AUF-30** der einzige Posten ohne jede
Prüfung. Niemand nimmt eigene Arbeit ab (§1.4).

| Nr | Auftrag | Rolle | Status | Datei / Beleg |
|---|---|---|---|---|
| **AUF-43** | **Geschoss-Bedienung neu ordnen** (Befund B1) — gemessen: **13 Bedienelemente in einer Zeile**, vier voneinander unabhängige Aufgaben (Rückgängig · Geschoss-Navigation · Geschoss-Verwaltung · Ansichtsmodus · Speichern), und der Geschossname steht **zweimal nebeneinander** (Select 111 px + Textfeld mit demselben Wert). Höhenlage wird geführt, aber nicht gezeigt; Namen automatisch „Geschoss 3"; kein Bild vom Stapel. **Größter Einzelhebel: ein angelegtes Geschoss entsperrt 34 der 110 Werkzeuge** — das Tor dahin ist ein 111-px-Dropdown zwischen Rückgängig und Speichern | Planner → Generator | `BERICHTET — wartet auf Evaluator` — umgesetzt `43a287f`, Bundle `8fd6568`. Neun Kriterien, Sichtprobe 1440/1024/375; den **375-px-Überlauf hat er gemeldet statt geflickt** (gehört zu AUF-46) | `generator-auftrag-auf43-geschoss-bedienung.md` |


---

### 3c. Bei Yama — Willensfragen

**Elf Fragen, von denen keine die Kette blockiert.** Sie stehen hier, weil keine Instanz sie in Yamas
Vertretung entscheidet (§5) — nicht, weil auf sie gewartet würde.

| Nr | Auftrag | Rolle | Status | Datei / Beleg |
|---|---|---|---|---|
| **AUF-6** | stopp-1 Teil I — Re-Check fahren, Teil I schließen, Dokument nachziehen | — | `BEI YAMA` | Yamas Auftragsdatei, Auftrag 3 |

---

### 3d. Abgeschlossen — im Archiv

Abgenommen, entschieden oder entfallen, wortgleich in **`docs/auftraege/AUFTRAGSTAFEL-ARCHIV.md`** —
**37 Posten**: AUF-1 · AUF-2 · AUF-3 · AUF-4 · AUF-9 · AUF-11 · AUF-12 · AUF-14 · AUF-15a · AUF-16 ·
AUF-19 · AUF-20 · AUF-24 · AUF-25 · AUF-21 · AUF-26 · AUF-27 · AUF-28 · AUF-30 · AUF-31 · AUF-32 · AUF-33 · AUF-34 · AUF-35a · AUF-36 · AUF-37. Nicht gelöscht, nur nicht mehr im Weg.

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
