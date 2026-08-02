# ⇒ FEHLERKLASSEN — der Träger von R9

**Angelegt:** 29.07.2026, 10:40 · **Vom:** Planner · **Anlass:** Yamas Frage *„was lernt ihr aus
dieser Erfahrung, welche Regeln müssen optimiert werden"* — und der Befund bei der Auswertung:
**vier Klassen hatten die Zwei überschritten, ohne dass jemand mitgezählt hat.**

---

## Wozu diese Datei

**R9 lautet:** *bei der zweiten Wiederholung derselben Fehlerklasse kommt eine technische Barriere,
kein dritter Vorsatz.*

**R9 hat keinen Träger gehabt.** Jedes Mal fiel die Wiederholung erst auf, wenn sich zufällig jemand
erinnerte — und am 29.07. habe ich die Lehre zu einer Klasse aufgeschrieben und **zwanzig Minuten
später dieselbe Klasse selbst wiederholt.** Das ist kein Willensmangel, das ist ein fehlender Zähler.

**Regel für diese Datei:** wer einen Befund einordnet, trägt ihn hier ein — **vor** dem Ledger-Eintrag,
nicht danach. Der Zähler wird erhöht, das Datum überschrieben. **Steht der Zähler nach dem Eintrag
auf 2 oder höher und die Barriere-Spalte ist leer, ist der nächste Schritt eine Barriere und nicht
die Behebung des Einzelfalls.**

**Was eine Barriere von einer Regel unterscheidet:** eine Regel verlangt Aufmerksamkeit im richtigen
Moment. **Eine Barriere braucht keine.** `git commit -- <pfade>` kann fremde Arbeit nicht mitnehmen —
egal, wie müde jemand ist.

---

## Register

| ID | Fehlerklasse | × | zuletzt | Barriere | Zustand |
|---|---|---|---|---|---|
| **F-01** | **Suche nach Muster statt nach Menge** — geerbte Zusagen werden über einen Suchbegriff gesucht und übersehen, wenn sie ihn nicht enthalten | **4** | 29.07. 09:59 | **R12** — die Quittung nennt die **Liste der Testdateien, die die betroffene Datei einlesen**; für `HausplanerApp.tsx` waren das am 29.07. **22** — seit AUF-48 sind es **16 direkt, 29 indirekt über `__tests__/_zerlegteApp.ts`, 35 zusammen**, gemessen mit `bash scripts/bestand.sh resources/planner/hausplaner/app/HausplanerApp.tsx` *(korrigiert 30.07. 23:20 — die 22 galt für eine 2308-Zeilen-Datei, die AUF-48 in acht Module zerlegt hat; belegt mit `git grep` am Stand `c8ef4a6d`. Die Regel selbst ist davon unberührt, nur ihre Beispielzahl war eingefroren.)* | ✅ steht |
| **F-02** | **Sperre, die mehr sperrt als ihr Grund trägt** — gesperrt wird ein Meilenstein statt der Bedingung | **3** | 29.07. 10:05 | **R10** — die Sperre nennt den **Zustand**, der sie aufhebt, und endet mit dem **Bau**, nicht der Abnahme | ✅ steht |
| **F-03** | **Messung älter als der Baum** — gemessen, dann minutenlang geschrieben, während andere committen | **4** | 30.07. 06:30 | **`scripts/auftrag-pruefen.mjs` S-10** — der Validator merkt sich `HEAD` vor und nach dem Lauf; hat sich der Baum bewegt, gibt er **exit 1** und sagt, dass jede Zahl aus einem Baum stammt, den es nicht mehr gibt | ✅ **Barriere steht (01.08. 19:2x)** — R14 verlangte, dass jemand daran denkt. Jetzt tut es das Werkzeug |
| **F-04** | **Zahl oder Artefakt behauptet statt gemessen** — ein Auftrag nennt eine Datei, Zusage oder Zahl, die niemand geprüft hat | **5** | 30.07. 06:25 | **R11** + **`scripts/auftrag-pruefen.mjs` S-08** — der `ausgangswert` im Blatt wird gegen die frische Messung gehalten; weicht er ab, sagt es der Validator | ✅ **Barriere steht (01.08. 19:1x)** — beim ersten Lauf gefunden: PB-023/K-03 nannte 0, gemessen **1**. Meine eigene Grundzahl, nie geprueft |
| **F-05** | **Beifang im Index** — ein Commit nimmt die gestagte Arbeit eines anderen mit | **2** | 29.07. 01:13 | **R13** — `git commit -- <pfade>` statt `git add` + `git commit`; der Index bleibt unberührt | ✅ steht |
| **F-06** | **Zusage prüft Gestalt statt Wirkung** — sie hält den gebauten Zustand fest und geht beim Umbau rot, ohne dass ein Fehler vorliegt | **6** | 29.07. 09:59 | **R2** (bestehend) + die generische Zusage über alle Dateien statt Einzelzusagen je Scheibe | ✅ steht, belegt |
| **F-07** | **Bestand nicht gemessen, sondern nachgebaut** — ein Auftrag beschreibt als *zu bauen*, was schon steht, oder als *zu ändern*, was ein abgenommener Vorgänger bewusst so entschieden hat | **5** | 30.07. 06:45 | **`scripts/auftrag-pruefen.mjs` S-07** — jedes Kriterium eines Blattes mit `status: bereit/aktiv` wird **vor dem Bau** gefahren; erfuellt die Messung das Ziel schon, gibt der Validator **exit 1** | ✅ **Barriere steht (01.08. 19:1x)** — beim ersten Lauf sofort gefunden: Z-02 und PB-023 waren gebaut, ihre Blaetter standen noch auf `bereit`/`aktiv` |
| **F-08** | **Leerlauf eines Bauenden** — der nächste Auftrag fehlt oder eine Sperre ist zu eng | **2** | 29.07. 10:03 | **R16 + `scripts/auftrag-pruefen.mjs` S-06** — beim Pruefen der Auftragsmenge zaehlt der Validator die baubaren Blaetter (`aktiv`/`bereit`) und gibt **exit 1**, wenn es weniger als zwei sind | ✅ **Barriere steht (01.08. 12:4x)** — gemessen: zwei baubare -> exit 0, ein baubares plus ein gesperrtes -> **exit 1**. 4 Zusagen, darunter: `ruht` zaehlt NICHT als baubar |
| **F-08b** | **Eine Entscheidung aendert den Auftrag — und steht nur in Tafel und Ledger, nicht im Blatt.** Anfangs nur am Feld `status:` bemerkt; am 29.07. um 21:18 hat es **den Umfang** getroffen: der Generator baute T2 nach dem Blatt, sieben Minuten nachdem die Erweiterung in der Tafel stand | **4** | 29.07. 21:18 | **`scripts/auftrag-pruefen.mjs` S-09** — ein Kopf ohne `status` wird gemeldet. Die Lage des Blattes muss IM Blatt stehen, nicht nur in Tafel und Ledger | ✅ **Barriere steht (01.08. 19:2x)** — am 01.08. trugen 17 Blaetter `aktiv`, obwohl die meisten gebaut waren: derselbe Fall, andersherum |
| **F-11** | **Zusage prueft eine Zeichenkette ohne Wortgrenze** — die Mutation `x` ⇒ `xy` bleibt gruen, weil `xy` das `x` enthaelt | **2** | 29.07. 21:00 | **`scripts/zaehle.mjs --wort`** — setzt Wortgrenzen, die auch Bindestrich und Unterstrich achten | ✅ **Barriere steht (01.08. 11:5x)** — gemessen: `hp-ok` in `hp-ok · hp-ok-menue` ohne Grenze **2**, mit Grenze **1** | ❌ offen · *beide Male vom Pruefenden an der EIGENEN Mutation gefunden* |
| **F-12** | **Der vorlaufende Baum kostet eine Messung** — der Pruefende misst einen Stand, den der Bauende schon verlassen hat | **4** | 30.07. 06:38 | **dieselbe Barriere wie F-03** — S-10 im Validator. *Beide Klassen sind derselbe Vorgang aus zwei Blickwinkeln: der Pruefende misst einen Stand, den der Bauende schon verlassen hat* | ✅ **Barriere steht (01.08. 19:2x)** — ausdruecklich mit F-03 zusammengelegt statt doppelt gebaut |
| **F-13** | **Kriterium mit Vorher-Bezug, dessen Vorher-Wert niemand festhalten musste** — das Blatt verlangt *„waechst gegenueber vorher“*, der Ausgangswert steht nirgends; beim Abnehmen ist er nicht mehr zu beschaffen | **2** | 29.07. 21:40 | **`vorher_wert_pflicht`** — traegt ein Kriterium einen Vorher-Bezug, **haelt der Generator den Ausgangswert in der Readiness-Quittung fest, VOR dem Bau**, in einer Zeile. Ohne diese Zeile ist der Bau nicht begonnen | ✅ steht (ab AUF-83-T3, K-08) |
| **F-14** | **Der Schreibvorgang scheitert, der Commit gelingt trotzdem** — ein Python-Heredoc bricht an einem Anführungszeichen im Fließtext ab (`SyntaxError`), das nachfolgende `git commit` läuft mit `rc=0` durch und committet nur, was vorher schon geschrieben war. **Das Ergebnis sieht aus wie Erfolg** | **3** | 30.07. 06:58 | **`scripts/commit-pruefen.sh`** — prueft VOR dem Commit, dass jeder Pfad existiert, nicht leer ist, wirklich geaendert wurde und syntaktisch traegt (`node --check`, YAML-Kopf). Schlaegt eines fehl, gibt es **keinen Commit** | ✅ **Barriere steht (01.08. 19:3x)** — gemessen: fehlende Datei -> exit 1, unveraenderte Datei -> exit 1. Raeumt im selben Aufruf die Lock-Reste (F-10) |
| **F-09** | **Text wird gemessen, nicht Absicht** — eine verbotene Zeichenfolge steht im Kommentar, der erklärt, warum sie verboten ist; die Zusage zählt sie mit | **8** | 01.08. 10:5x | **`scripts/zaehle.mjs`** — zaehlt mit abgezogenen Kommentaren; `--mit-kommentaren` muss man ausdruecklich verlangen | ✅ **Barriere steht (01.08. 11:5x)** — 14 Zusagen in `scripts/__tests__/zaehle.test.mjs`. **Die Barriere hatte selbst zwei Loecher** (gefunden 13:0x, zwei Stunden nach ihrer Meldung): ein `//` in einer Zeichenkette machte echten Code unsichtbar — **der Zaehler meldete zu WENIG**, und ein Kriterium „erwartet 0" waere gruen geworden. Beide behoben, beide als Zusage festgenagelt | ❌ offen |
| **F-10** | **Lock-Reste auf dem Mount** — `unlink` scheitert, jeder Commit lässt `HEAD.lock` liegen, ein anderer räumt sie weg | **28** | 29.07. 10:28 | **Nicht behebbar auf diesem Mount** — `unlink` ist dort nicht erlaubt, jeder Commit laesst `HEAD.lock` liegen. `commit-pruefen.sh` raeumt sie im SELBEN Aufruf nach `.git/_locks_beiseite/<datum>/` | ⚠ **gemildert, nicht behoben — und das bleibt so.** *Das gehoert benannt statt als offener Posten mitgeschleppt: eine Klasse, deren Ursache ausserhalb unserer Reichweite liegt, wird verwaltet, nicht geloest* |

| **F-15** | **Die Wahrheit wandert in eine zweite Datei, und zwischen beiden liegt nichts** — ein Inline-Stil wird von den Bauteil-Zusagen mitgelesen; eine Klasse verlagert das Sichtbare in die Stilschicht, und ein Tippfehler im Klassennamen macht ein Element ungestylt, ohne dass ein Testlauf rot wird | **2** | 01.08. 10:50 | **Stil-Brücken-Test ist Pflichtteil jedes Blattes**, das Stile in Klassen verlagert — er prüft *benutzt ⇔ definiert*, *Regel trägt die ersetzten Eigenschaften*, *Klasse sitzt am richtigen Element* | ✅ Barriere steht (AUF-38-P1 `eigenschaftenPanelStil.test.ts`, P2 `gruppenzeileUndSchieneStil.test.ts`) |
| **F-16** | **Das Werkzeug faehrt aus, was es nur lesen sollte** — `auftrag-pruefen.mjs` wertet JEDEN `yaml`-Block in `docs/auftraege/` als Blatt aus, PB-019 sucht `status:` + `aktiv` als reinen TEXT ueber die ganze Datei, und jeder `pruefung.befehl` ging ungefiltert an die Shell. Zwei Auswirkungen, beide am 01.08. beim ERSTEN Verzeichnislauf gefunden: `AUFTRAGSSCHEMA.md` (Doku, kein Auftrag) zaehlte als **zweites aktives Blatt** und sperrte ueber S-01 jeden Lauf — und ueber `docs/auftraege/` standen **46** `pruefung.befehl` mit `npm run`, die der Validator alle ausfuehrte. Der Lauf kam auf der Geraete-VM in 45 s nicht durch | **1** | 01.08. 19:4x | **`GATE_MUSTER` in `scripts/auftrag-pruefen.mjs`** — `npm run`, `npx`, `yarn`, `pnpm`, `php artisan`, `composer` werden als **GATE** gemeldet statt gefahren; sie gehoeren zum Generator, nicht in einen Struktur-Validator. **6 neue Zusagen, darunter zwei ROTE Gegenproben** (`echo zwei` laeuft weiter, `npm` ohne `run` ist kein Gate). Messung: 27 Blaetter in **3,6 s** statt Timeout. *Fuer den zweiten Teil — Doku-Papier als Auftrag — gilt heute nur eine **Regel**: Doku benutzt ` ```text ` statt ` ```yaml ` und keinen Beispielwert `aktiv` hinter `status:`. Barriere bei der zweiten Wiederholung (R9)* | ⚠ **halb** — Gate-Barriere steht (01.08. 19:5x), Doku-als-Auftrag ist noch eine Regel |
| **F-17** | **Ein unbekannter `typ:` verschwindet lautlos aus dem Bericht** — der Validator listet nur die Typen, die er kennt. Ein Kriterium mit `typ: verschoben` (oder einem Tippfehler wie `pressence`) taucht **gar nicht** auf: nicht als OK, nicht als NICHT MASCHINELL, nicht als Fehler. Gemessen 01.08.: Z-05 meldete nach dem Verschieben 7 Eintraege statt 9, ohne ein Wort dazu. **Ein Tippfehler im `typ` macht ein P1-Kriterium unsichtbar** | **1** | 01.08. 19:4x | **noch keine Barriere — heute nur eine Regel:** wer einen `typ` schreibt, prueft im Bericht nach, dass die Zahl der Eintraege stimmt. Die Barriere waere: unbekannter `typ` = Fehlschlag, nicht Schweigen. **W-08 K-04 schliesst sie fuer den neuen `typ: verweis` mit** | ❌ offen |
| **F-18** | **Die Erlaubnisliste fragt an drei Stellen wieder das PROGRAMM statt das ZIEL** — derselbe Satz, den W-01 fuer `bash`/`sh` eingebaut hat (`auftrag-pruefen.mjs:256`), gilt fuer `node`, `sed` und `awk` nicht: sie stehen als blanke Namen auf der Liste. Gemessen vom Pruefer am 02.08.: `node /tmp/fremd.mjs`, `node -e "…"`, `sed -i 's/a/b/' datei` und `awk 'BEGIN{system("x")}'` kommen alle durch. **`bash` faellt durch und `node -e` nicht** — dabei sagt `node -e` noch weniger ueber seine Wirkung. Zweite Gestalt derselben Klasse wie F-16: *der Text eines Befehls sagt nichts ueber seine Wirkung* | **2** | 02.08. 11:3x | **W-07** (`aebe57b6`): `skriptZielErlaubt` bekommt das erste Nicht-Flag-Wort statt `woerter[1]` und bedient `bash`, `sh` UND `node`; `sed -i` faellt in jeder Schreibweise; `awk` fliegt von der Liste (0 Verwendungen, 1 offene Tuer). **Ein einziger Zielpruefer, kein zweiter** (K-05) | ⚠ geschnitten, nicht gebaut |
| **F-19** | **Ein Wert, der in N Blaetter kopiert ist, wird bei jeder Korrektur nur zur Haelfte erreicht** — der L-01-Anker steht ausgeschrieben in **18** Blaettern. Am 02.08. war er zweimal falsch, und beide Male kostete die Korrektur **sechs** Ersetzungen mit Zeilennummern: `86f8e222` (v2) und `5163cac2` (v3). **Zwoelf Splice-Gelegenheiten fuer zwei Erkenntnisse** — und zwoelf Blaetter tragen den alten Anker weiter, weil sie `ruht`/`abgenommen`/ohne Kopf sind. Dieselbe Klasse wie die feste `101` in sieben Zusagen (W-05) und der doppelte Name `PAKET_WERKZEUGE`: **eine Wahrheit, N-mal getippt** | **2** | 02.08. 12:3x | **W-08** (`6ea45e05`): der Anker zieht nach `docs/auftraege/ANKER-BROWSER.md`, die Blaetter tragen drei Zeilen Verweis. **S-11** haelt ihn dort und greift nur bei `aktiv · bereit · gebaut · entwurf · gesperrt` — die Sperre sitzt am UEBERGANG aus dem Archiv, nicht am Ruhezustand | ⚠ geschnitten, nicht gebaut |
| **F-20** | **Das Kriterium misst gegen einen Boden, den es nicht gibt** — nicht der WERT ist veraltet (das ist F-08/S-08), sondern die GRUNDLAGE fehlt ganz. Zwei Gestalten am selben Tag: **W-07** trug im Kopf *„Basis: HEAD beim Ziehen"*, und der Planner hat gegen den ARBEITSBAUM gemessen — `skriptZielErlaubt` existiert an HEAD gar nicht (`ZielErlaubt` an HEAD **0**, im Arbeitsbaum **3**); wer nach dem Blatt baut, misst Ausgangswert 0 statt 3 und der Zeilenverweis zeigt ins Leere. **S-10** hat drei P1-Kriterien auf `tests/fixtures/auftraege/*` — **das Verzeichnis existiert nicht**, alle drei melden FEHLSCHLAG, seit sie geschrieben wurden. *Im Blatt stand sogar „gemessen am Arbeitsbaum" — hingeschrieben und die Folge nicht gezogen.* **Die Zahl war richtig; der Boden war es nicht** | **2** | 02.08. 14:1x | **Heute eine Regel, Barriere benannt:** ein Blatt, dessen Ausgangswerte nicht an HEAD gelten, traegt ein eigenes **`vorbedingung:`**-Feld im Kopf (so jetzt in W-07), und der `gegenbeweis` fuehrt den Pruefbefehl dafuer mit (`git show HEAD:<pfad> \| grep -o <muster> \| wc -l`). **Die Barriere waere eine Validator-Sperre S-12:** jeder `ausgangswert` wird gegen HEAD **und** gegen den Arbeitsbaum gemessen; weichen sie ab, ist das eine Meldung. *Gefunden hat beide der Evaluator bzw. der Schlusslauf — nicht der Schreibende* | ⚠ Regel, Barriere noch nicht geschnitten |
| **F-21** | **Das Blatt trifft sich selbst** — ein Kriterium sucht ein Muster, das im eigenen Blatt woertlich zitiert steht, und misst damit sich selbst mit. **W-08 K-05** forderte *„0 Blaetter mit MONTIEREN"* und zitierte `2  MONTIEREN` drei Zeilen weiter oben als Suchmuster: gemessen **7** statt **6**, das Soll per rohem `grep` **unerreichbar**. Zweite Gestalt vom 01.08.: **W-01-02** nannte den echten Push-Wrapper in einer Zusage — dasselbe Muster eine Ebene hoeher, dort haette das Blatt den Befehl nicht nur getroffen, sondern ausgefuehrt. *Verwandt: der erste Klasse-A-Zaehler meldete 16 statt 8, weil Praefix-Matching `verschieben` auf `dachVerschneidung.ts` zog* | **2** | 02.08. 14:1x | **Heute eine Regel:** wer ein Muster ueber `docs/auftraege/` zaehlt, misst es ERST gegen das eigene Blatt. Faellt es auf sich selbst, entweder ueber das VERZEICHNIS messen (so K-01/K-02 in W-08) oder eine **benannte** Ausnahme mit Blattnamen und Grund setzen — **nie eine stille Filterung**, sonst wird das Soll passend gemacht statt gemessen. *Im selben Blatt zweimal verschieden behandelt: bei K-01/K-02 bewusst vermieden, bei K-05 uebersehen* | ⚠ Regel, R9 gilt beim naechsten Mal |

**Warum F-15 hier steht, obwohl die Barriere schon greift:** *die Zahl `7 von 8 Mutationen kamen durch`
ist am 31.07. (P1) und am 01.08. (P2) **auf die Ziffer genau zweimal** aufgetreten — und es gab keine
Klasse dafür (`grep -ci mutation FEHLERKLASSEN.md` → 1, und das war ein Nebensatz). **Eine Klasse, die
erst nach der Barriere eingetragen wird, hat ihren Zweck verfehlt** — sie soll die Wiederholung sichtbar
machen, bevor jemand sie zufällig bemerkt. Eingetragen auf Befund PB-012.

---

## F-14 hat jetzt eine Barriere, keinen Absatz mehr

**Befund PB-012:** *„die Barriere von F-14 ist ein Absatz — nach zwei Stunden gebrochen; R9 verlangt mehr."*
**Das stimmt.** Ein Absatz, der um Sorgfalt bittet, ist kein Schutz.

**F-14 lautet:** der Schreibvorgang scheitert (Python-Heredoc bricht an einem Anführungszeichen ab), das
nachfolgende `git commit` läuft trotzdem — und committet den *alten* Stand als wäre er der neue.

**Die Barriere, ab sofort verbindlich für alle drei Rollen:**

1. **Schreiben und Committen sind NIE derselbe Aufruf.** Kein `python3 - << 'ENDE' … ENDE && git commit`.
   Der Schreibaufruf endet, sein Rückgabewert wird gesehen, erst dann kommt der nächste.
2. **Jeder Schreibaufruf endet mit einer Behauptung, die er selbst prüft** — `assert alt in t` vor dem
   Ersetzen und eine Ausgabe danach. *Ein Ersetzen, das nichts trifft, ist ein stiller Fehlschlag; ein
   `assert` macht daraus einen lauten.*
3. **Vor dem Commit steht `git status --porcelain`** und die geänderte Datei muss darin auftauchen.
   *Steht sie nicht da, hat der Schreibvorgang nichts getan — und der Commit hätte eine Lüge belegt.*

**Warum das eine Barriere und kein Vorsatz ist:** Punkt 1 und 2 machen den Fehlschlag *sichtbar, ohne dass
jemand daran denken muss*. Der Aufruf bricht ab und sagt es. Genau das hat am 30.07. gefehlt.

---

## Vier Klassen haben keine Barriere, nur eine Regel

**F-03, F-04, F-07, F-08 sind Vorsätze.** Sie verlangen, dass jemand im richtigen Moment daran denkt —
und genau das hat heute viermal nicht getragen. **Sie sind die nächste Arbeit an diesem Register**,
nicht sein Ergebnis.

**F-04 hat seit dem 01.08. ein zweites Werkzeug — `scripts/pfade-pruefen.sh`.**

*Befund PB-031: „68 von 923 genannten Code-Pfaden nicht auffindbar."* Das Skript liest alle
Markdown-Dateien, sammelt jeden in Backticks genannten Code-Pfad und meldet, welcher ins Leere zeigt.
**Gemessen 01.08.: 75 von 1016** — die Zahl des Prüfers unabhängig bestätigt.

**Zwei Fallen, die beim Bauen dieses Skripts zugeschnappt sind, und beide gehören hierher:**

1. *Erster Lauf: **751** tote Verweise.* Die Papiere nennen Pfade oft relativ zur Insel-Wurzel
   (`app/dashboard/palette.ts` meint `resources/planner/hausplaner/app/…`). **Das ist F-09 in Reinform:
   die Gestalt wurde gemessen, nicht die Sache.**
2. *Zweiter Lauf: **601**.* `docs/_playground-archiv/` ist das Archiv einer **anderen App** — ihre Pfade
   gegen diesen Baum zu prüfen misst nichts.

**Hätte ich die 751 gemeldet, wäre daraus ein Alarm geworden, den niemand hätte abstellen können.**

**Für zwei davon gibt es einen naheliegenden Weg:**

- **F-04** wird zur Barriere, wenn `scripts/auftrag-pruefen.sh` existiert: es fährt jeden
  `pruefung.befehl` aus dem YAML-Kopf und meldet, welcher `exit 1` liefert oder nichts findet.
  **Ein Auftragsblatt, dessen Prüfbefehle ins Leere greifen, wäre dann nicht mehr abgebbar.**
  *Das ist der Validator, den ich seit dem 27.07. schulde.*
- **F-08** wird zur Barriere, wenn die Tafel den Zustand selbst meldet: steht in §3a kein Posten mit
  Marke, ist das ein sichtbarer Fehlzustand und keine Ruhe.

**F-03 und F-07 bleiben vorerst Regeln** — sie hängen an Aufmerksamkeit, und ich kenne für sie noch
keine mechanische Form. **Das gehört so benannt und nicht als erledigt geführt.**

---

## Was hier NICHT hineingehört

- **Einzelbefunde.** Ein Fehler ist erst eine Klasse, wenn er ein zweites Mal in anderer Gestalt
  auftritt. Vorher steht er im Ledger.
- **Urteile über Personen oder Rollen.** Die Spalte „bei wem" fehlt absichtlich: **fünf von sieben
  Klassen gehen auf den Planner**, und das steht im Auswertungspapier — aber der Zähler zählt die
  Klasse, nicht den Schuldigen. *Wer nach Schuld sortiert, hört auf einzutragen.*
- **Behobene Einzelfälle.** Der Zähler wird **nicht** zurückgesetzt, wenn eine Barriere steht. Er
  bleibt stehen und dokumentiert, wie teuer die Klasse war, bevor sie geschlossen wurde.

---

## Herkunft der Zahlen

Alle Zähler stammen aus der Auswertung von `docs/handoff-status.md` (29.07., 00:00–10:30), der
Auftragstafel und dem git-Protokoll — nachlesbar in
`docs/planner/lehren-2026-07-29.html`. **Sie sind gezählt, nicht geschätzt.**
