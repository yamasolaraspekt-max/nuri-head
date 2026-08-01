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
| **F-03** | **Messung älter als der Baum** — gemessen, dann minutenlang geschrieben, während andere committen | **4** | 30.07. 06:30 | **R14** — `git log -1` + `git status` **unmittelbar** vor dem Schreiben; bei Tafelzeilen zuerst prüfen, ob der Bauende sie schon gesetzt hat · **4. Ausprägung 30.07. 06:30 (Planner, an sich selbst gefunden):** das T5-Blatt trug als Datum den 29.07. um 21:50 — geschrieben wurde es am **30.07. gegen 06:05**, nach einer Sitzungspause von rund acht Stunden. **Aufgefallen ist es am Commit-Zeitstempel, nicht beim Schreiben** | ⚠ Regel, keine Barriere — die Uhrzeit müsste aus `git log` kommen, nicht aus dem Kopf |
| **F-04** | **Zahl oder Artefakt behauptet statt gemessen** — ein Auftrag nennt eine Datei, Zusage oder Zahl, die niemand geprüft hat | **5** | 30.07. 06:25 | **R11** — jedes Kriterium trägt den Befehl, der es belegt, **und der Planner hat ihn gefahren**, bevor das Blatt liegt · **4. Auspraegung 29.07. 21:30:** die Grundgesamtheit von T3 (13 Bedienelemente) stammte aus der Inventur vom **25.07.** und war beim Schreiben des Blattes vier Tage alt — AUF-43 hatte zwei der drei P1 laengst erfuellt. **Gemeldet hat es der Generator, nicht ich** | ⚠→✅ **KORRIGIERT 30.07. 22:25:** `scripts/auftrag-pruefen.sh` **steht aus** war falsch — die Datei liegt seit `2d37d141` (30.07. 10:51) in HEAD und ist gruen abgenommen (AUF-87). *Die Zelle hat den Zustand von vor dem Bau festgehalten; der Pruefer hat es gemessen.* |
| **F-05** | **Beifang im Index** — ein Commit nimmt die gestagte Arbeit eines anderen mit | **2** | 29.07. 01:13 | **R13** — `git commit -- <pfade>` statt `git add` + `git commit`; der Index bleibt unberührt | ✅ steht |
| **F-06** | **Zusage prüft Gestalt statt Wirkung** — sie hält den gebauten Zustand fest und geht beim Umbau rot, ohne dass ein Fehler vorliegt | **6** | 29.07. 09:59 | **R2** (bestehend) + die generische Zusage über alle Dateien statt Einzelzusagen je Scheibe | ✅ steht, belegt |
| **F-07** | **Bestand nicht gemessen, sondern nachgebaut** — ein Auftrag beschreibt als *zu bauen*, was schon steht, oder als *zu ändern*, was ein abgenommener Vorgänger bewusst so entschieden hat | **5** | 30.07. 06:45 | **R15** — vor jedem Entwurf die **Shell der Heimat-App** messen (Layout, Seitenleisten, Navigationseinträge, Klappzustände) **und** `docs/planner/` lesen · **4. und 5. Ausprägung 30.07.:** T3 versprach drei Zeilen zu bauen, die seit AUF-34/AUF-27/AUF-70 stehen — und K-01 wollte zurückdrehen, was AUF-70 mit **vier abgenommenen Zusagen** verriegelt hat · **R17 (neu, 30.07. 06:50)** — *der Auftrag* nennt im YAML-Kopf die **abgenommenen Zusagen, die dieselbe Fläche verriegeln** (`geerbte_zusagen`), gefunden mit demselben Befehl wie R12. **Der Planner fährt ihn, bevor das Blatt liegt** — nicht der Bauende, nachdem er es gelesen hat · | ⚠ Regel, keine Barriere — R15 und R17 verlangen beide Aufmerksamkeit im richtigen Moment; erst der Validator macht daraus ein Tor |
| **F-08** | **Leerlauf eines Bauenden** — der nächste Auftrag fehlt oder eine Sperre ist zu eng | **2** | 29.07. 10:03 | **R16** — mindestens **zwei baubare Aufträge** liegen jederzeit vor der Front | ⚠ Regel, keine Barriere |
| **F-08b** | **Eine Entscheidung aendert den Auftrag — und steht nur in Tafel und Ledger, nicht im Blatt.** Anfangs nur am Feld `status:` bemerkt; am 29.07. um 21:18 hat es **den Umfang** getroffen: der Generator baute T2 nach dem Blatt, sieben Minuten nachdem die Erweiterung in der Tafel stand | **4** | 29.07. 21:18 | **Das Blatt wird ZUERST nachgezogen, dann Tafel und Ledger.** Wer eine Entscheidung schreibt, oeffnet das Auftragsblatt als erstes — nicht als letztes | ⚠ Regel, aber sie aendert die **Reihenfolge des Schreibens**, nicht die Aufmerksamkeit |
| **F-11** | **Zusage prueft eine Zeichenkette ohne Wortgrenze** — die Mutation `x` ⇒ `xy` bleibt gruen, weil `xy` das `x` enthaelt | **2** | 29.07. 21:00 | — | ❌ offen · *beide Male vom Pruefenden an der EIGENEN Mutation gefunden* |
| **F-12** | **Der vorlaufende Baum kostet eine Messung** — der Pruefende misst einen Stand, den der Bauende schon verlassen hat | **4** | 30.07. 06:38 | **R10-Zusatz** + **R18 (neu)** — eine Sperre endet mit dem Bau, **es sei denn, der Folgeauftrag zerstoert einen Pruefstand**, den die offene Abnahme braucht · **R18, 30.07.:** solange eine Sichtprobe **beauftragt** ist, bewegt niemand `public/hausplaner/*` und keine Blade, die ohne Bau sofort wirkt — **beauftragt zaehlt wie laufend** | ⚠ Regel · **beim dritten Mal wurde eine Messung vernichtet; beim vierten hat der Bauende es SELBST gesehen und zurueckgestellt, bevor es jemand merkte — erste Auspraegung, die niemanden etwas gekostet hat** |
| **F-13** | **Kriterium mit Vorher-Bezug, dessen Vorher-Wert niemand festhalten musste** — das Blatt verlangt *„waechst gegenueber vorher“*, der Ausgangswert steht nirgends; beim Abnehmen ist er nicht mehr zu beschaffen | **2** | 29.07. 21:40 | **`vorher_wert_pflicht`** — traegt ein Kriterium einen Vorher-Bezug, **haelt der Generator den Ausgangswert in der Readiness-Quittung fest, VOR dem Bau**, in einer Zeile. Ohne diese Zeile ist der Bau nicht begonnen | ✅ steht (ab AUF-83-T3, K-08) |
| **F-14** | **Der Schreibvorgang scheitert, der Commit gelingt trotzdem** — ein Python-Heredoc bricht an einem Anführungszeichen im Fließtext ab (`SyntaxError`), das nachfolgende `git commit` läuft mit `rc=0` durch und committet nur, was vorher schon geschrieben war. **Das Ergebnis sieht aus wie Erfolg** | **3** | 30.07. 06:58 | Jeder Fließtext geht in einen dreifach angeführten Rohstring, nie in Zeichenkettenverkettung. **Und die eigentliche Barriere: nach jedem Schreibskript `git status` lesen, BEVOR committet wird** | ⚠ Regel · *dieselbe Klasse wie „verdächtig statt Fehlschlag" in AUF-87: der Befehl endet mit 0 und hat nichts getan* |
| **F-09** | **Text wird gemessen, nicht Absicht** — eine verbotene Zeichenfolge steht im Kommentar, der erklärt, warum sie verboten ist; die Zusage zählt sie mit | **8** | 01.08. 10:5x | — |  ❌ offen |
| **F-10** | **Lock-Reste auf dem Mount** — `unlink` scheitert, jeder Commit lässt `HEAD.lock` liegen, ein anderer räumt sie weg | **28** | 29.07. 10:28 | Locks werden **im selben Aufruf** beiseitegelegt wie der Commit | ⚠ mildert, behebt nicht |

| **F-15** | **Die Wahrheit wandert in eine zweite Datei, und zwischen beiden liegt nichts** — ein Inline-Stil wird von den Bauteil-Zusagen mitgelesen; eine Klasse verlagert das Sichtbare in die Stilschicht, und ein Tippfehler im Klassennamen macht ein Element ungestylt, ohne dass ein Testlauf rot wird | **2** | 01.08. 10:50 | **Stil-Brücken-Test ist Pflichtteil jedes Blattes**, das Stile in Klassen verlagert — er prüft *benutzt ⇔ definiert*, *Regel trägt die ersetzten Eigenschaften*, *Klasse sitzt am richtigen Element* | ✅ Barriere steht (AUF-38-P1 `eigenschaftenPanelStil.test.ts`, P2 `gruppenzeileUndSchieneStil.test.ts`) |

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
