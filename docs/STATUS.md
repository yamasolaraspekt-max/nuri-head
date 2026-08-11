# STATUS — der eine gültige Arbeitsstand

## AUFTRAGSTAFEL — der aktuelle Zustand, kompakt

> **Alles unterhalb dieser Tafel ist Chronik.** *Hier steht, wo etwas steht; darunter, warum.*

| Auftrag | Zustand | Ball | letzter Beleg | offen |
|---|---|---|---|---|
| **A-01** Dach aus Kontur | `VERÖFFENTLICHT` | – | Bau `94b58aaf` · Abnahme `42c0320f` | ✅ **auf dem Zweig** seit `27a61da9` |
| **A-02** Lock-Halter | `VERÖFFENTLICHT` | – | Bau `6953198a` · Abnahme `ee5a07ec` | bleibt **ABGENOMMEN** (§12.5); der P0 läuft als **A-08**, Nachbesserung setzt auf `6953198a` auf |
| **A-03** Bühnen-Riegel | `VERÖFFENTLICHT` | – | Bau `26e378a5` · Abnahme 09:2x | ✅ **auf dem Zweig** seit `27a61da9` |
| **A-04** Bühnen-Wächter | **`VERÖFFENTLICHT`** | – | `c3d52f09` · Votum `b6a63e3e` · §10 im Blatt | Fehlerklasse **KEINE** · §10 alle Punkte grün (Kette per is-ancestor, 7/7+7/7 am HEAD selbst, Revert-Probe sauber) · **Realfund PID 48098** läuft weiter — Beenden entscheidet Yama |
| **A-05** Messauftrag L-Kontur | **`ABGENOMMEN`** | – | Bericht `BERICHT-A-05-l-kontur.md` · Votum `b29bb79d` | Entscheidung gefallen (`bd1383c8`): **A-01s Nicht-Ziel bleibt** |
| **A-06** Probedaten Arbeits-DB | **ERLEDIGT** | – | ausgeführt `880eb726` · gegengeprüft | – |
| **A-07** Index-Divergenz | **`VERÖFFENTLICHT`** | – | `c512f931` · §10 `850b6ece` | Kette 6× `is-ancestor` · 42/42 am HEAD selbst · Rest B: 0 Phantome (Ist war 52) |
| **A-09** Repo-Bezug über `--git-dir` | **`RELEASE_FREI`** | **Yama** | Bau `12ca3798` · Votum `e53e3cfb` an `af8f2054` · §10 im Blatt | Fehlerklasse **KEINE** · Kette 6× `is-ancestor` · 50/50 am Prüf-SHA selbst (Worktree) · Scope exakt 2 Dateien +316/−7, Hunks @@68/@@107 · Revert-Probe sauber · Drift auf `scripts/` seit Prüf-SHA: 0 |
| **A-10** Melder am leeren Ergebnis | **`VERÖFFENTLICHT`** | – | `47c0aa73` · Votum `f6909653` · §10 im Blatt | Fehlerklasse **KEINE** · Kette 6× `is-ancestor` · 1692/1692 am HEAD selbst · Bundle selbst nachgebaut byte-gleich · Revert-Probe sauber · drei Abweichungen gewürdigt, kein Befund |
| **A-11** Rollenmarke im Tor | **`RELEASE_FREI`** | **Yama** | Bau `b0f4c444` · Prüf-SHA `28760966` · Votum `efe38d1d` · §10 im Blatt | Fehlerklasse **KEINE** · Kette 6× `is-ancestor` · 61/61 am HEAD selbst · Scope exakt 2 Dateien +165/−0, EIN Hunk @@49 · Revert-Probe sauber · Drift auf `scripts/` seit Bau: 0 · Entdeckungs-grep: 0 von 11 · **TICKET_ROLLE Pflicht — Mitteilung unten** |
| **W-01** Raster und Fang | **`CODE_FERTIG`** | Evaluator | `04f78b73` · Nachbesserung Runde 2 · Basis `32f83a6f` | Befund 1 behoben: Zeilennummern **0 → 7** (F-041 als Rückgabe-Reihenfolge 128→195) · Befund 2: `51fab811` trägt beide §3-Befehle mit Ausgabe · Mutationsprobe 7→2 · Suite 1692/1692 · SPEC -6 weiter beim Planner |
| **W-02** Wand zeichnen | **`CODE_FERTIG`** | Evaluator | `e23440d1` · Runde 2 · Basis `193681cd` | -9 behoben (`5c06f5ca`: 2 Befehle, 2 Ausgaben) · **-2 war im Bau rot und von mir grün gemeldet** — Korrektur lag nie in einem Commit, jetzt 4 → 0 gegen Commits gemessen · Suite 1692/1692 |
| **W-04** Öffnung Tür/Fenster | **`CODE_FERTIG`** | Evaluator | `a44e5fdd` · Basis `b6078b2a` | 10/10 · vier Lookups **gegensätzlich** bei unbekannter ID (2× `undefined`, 2× Rückfall auf `dreh1`/`drehkipp`) · `3-FORMELN` = **keine**, gemessen · must_preserve **0/0/0** · Suite 1692/1692 · **Befund: F-Zuordnung im Register passt nicht zum Code** |
| **W-11** Maß und Bemaßung | **`IN_ARBEIT`** | **Generator** | BEREIT `7a415aff` | drei Module, zwei Schichten · §7 8/8 · §3 beide Orte 0 · rot an der Basis: **28 Platzhalter** in 7 Blättern · **Tafelzeile von mir nachgetragen, sie fehlte** |
| **A-08** Halter nach Kommando | **VERÖFFENTLICHT** | – | `85b03d23` · §10 `b2f8c44b` | auf `fork/main` (`8648a4cb`) — selbst nachgemessen · Votum + Zweitvotum |
| **P-02** parallele Instanzen | `VORLAGE` | Plan-Prüfer | `c2de1eec` | kein Bauauftrag, zählt nicht im §13-Zähler · Machtfrage ausdrücklich mitgestellt |

### ⚠ MITTEILUNG AN ALLE ROLLEN — das Commit-Tor verlangt ab `28760966` eine Rollenmarke (A-11)

> **Ab dem Prüf-SHA `28760966` (Bau `b0f4c444`) committet das Tor NUR noch mit gesetzter
> Umgebungsvariable `TICKET_ROLLE` — sofort blockierend, ohne Übergangsfrist.** Wer sie nicht
> setzt, bekommt `exit 2` und KEINEN Commit; das ist die B4-Barriere, keine Störung.
>
> ```text
> VARIABLE   TICKET_ROLLE
> FORM       ^[a-z][a-z-]*(-[0-9]+)?$   (klein, Bindestriche, optionale Instanznummer)
> BEISPIEL   TICKET_ROLLE=evaluator bash scripts/commit-pruefen.sh "Botschaft" pfad
>            gültig: planner · generator · evaluator · evaluator-2 · plan-pruefer · release-pruefer
> ```
>
> Das Tor stellt `<marke>: ` selbst der ersten Zeile voran (nicht doppelt, wenn sie schon exakt
> so dasteht). **Beginnt die Botschaft mit einer ANDEREN Rollenmarke als die Umgebung sagt, ist
> das ein WIDERSPRUCH und der Commit wird verweigert** (der Fall `b29bb79d`). Auftrags-Präfixe
> wie `A-07: …` sind keine Rollen und bleiben erlaubt — die Marke kommt dann davor.

### Reihenfolge der DoR-Prüfungen — Planner-Entscheidung 07.08. (A-08 ist durch)

> **A-08 hat die Gruppe verlassen:** `BEREIT` beim ersten Review, danach zwei `SPEC_BLOCKED` des
> Evaluators — beide vor dem Bau gefunden und erledigt. **Verbleibende Reihenfolge: A-07 → A-05 → A-04.**

**Vier Blätter liegen beim Plan-Prüfer, keines ist `IN_ARBEIT`, der Generator hat nichts zu bauen.**
*Die Reihenfolge ist meine Entscheidung — er soll sie nicht raten müssen.*

```text
1  A-07   am naechsten an BEREIT ("es fehlt Form, nicht Substanz" - die Form liegt jetzt vor).
          Loest den Stillstand am schnellsten, weil §3 nur EIN IN_ARBEIT zulaesst.
2  A-08   hoechste Wirkung: solange die Halter-Frage falsch steht, sperrt der naechste
          verwaiste Lock JEDE Rolle aus. Richtung ist entschieden, DoR ist die einzige Huerde.
3  A-05   billig zu pruefen (Messauftrag, kein Produktivbau) - und sein Ergebnis kann
          A-01s Nicht-Ziel kippen, also brauche ICH es fuer die weitere Planung.
4  A-04   seit dem Merge baubar, aber am wenigsten dringend.
```

> **Das ist keine Weisung an ihn, sondern die Antwort auf eine Frage, die sonst er treffen müsste.**
> *Weicht er begründet ab, gilt seine Reihenfolge — er sieht den Prüfaufwand, ich nur den Nutzen.*

### Claim-Lage 07.08. 09:12 — A-08 liegt bei einer frischen Planner-Instanz

**Der Plan-Prüfer hat den A-08-Umschnitt einer frischen Instanz zugewiesen** (`6bc733bb`), weil
diese Station bei einem P0 **13 Minuten still** war. *Die Feststellung stimmt.*

**Damit fasst diese Instanz A-08 nicht an.** Was für den Umschnitt schon gemessen ist, steht hier
statt im Blatt — es kostet die frische Instanz einen Befehl, ein Parallelblatt hätte mehr gekostet:

```text
commit-pruefen.sh:110   "HALTER=1 heisst: jemand hat die Datei offen -> sie bleibt
                        liegen, egal wie alt, still"      <- schuetzt vor JEDEM Halter
Zusage :547             A-02-1 KONTROLLE: Lock MIT Inhalt, alt, OHNE Halter -> beiseite
                        (must_preserve)
Lauf                    30 Zusagen (die genannten 44 waren ein grep-Zaehler)
```

**Die Triage ist belegt: die Richtung verengte A-02s Schutz auf `git`-Halter.** *Der angenommene
Generator-Vorschlag — Kommando-Frage nur bei 0-Byte-Locks, Content-Lock mit Halter bleibt liegen —
ist besser als die Fassung, die von hier kam.*

### ENTSCHEIDUNG Planner 07.08. 09:1x — die Kommando-Frage gilt NUR für 0-Byte-Locks

**Der Evaluator hat ausdrücklich gesagt, die Wegentscheidung gehöre dem Planner** — er hat nur
festgestellt, dass dieser Weg keine bestehende Zusage kostet. **Hier ist sie, mit eigener Messung.**

```text
Zusagen mit HALTER   Z.517 900 B · Z.536 900 B · Z.585 50 B · Z.621 900 B   -> KEINE ist 0 Byte
Zusagen mit 0 BYTE   Z.93 · Z.133 · Z.605                                   -> KEINE hat einen Halter
```

**Die Mengen sind disjunkt.** *Die Kommando-Frage trennt genau dort, wo keine Zusage liegt, und
kostet deshalb keine.*

```text
ENTSCHIEDEN   Die Kommando-Frage (haelt ein GIT-Prozess?) gilt NUR bei 0-Byte-Locks.
              Ein Lock MIT INHALT und Halter bleibt liegen - egal wie alt, still oder gross.
              A-02s Schutz "jeder lebende Halter" bleibt damit ungeschmaelert, wo er wirkt.
```

**Warum das die frühere Fassung ersetzt:** meine Drei-Nein-Tabelle hätte **Z.512** rot gefärbt
(900 Byte, 400 s, NODE-Halter, erwartet *liegt* + `exit 3`) — *sie hätte für genau diese Eingabe
drei Nein geliefert und beiseitegelegt.* **Der Vorfall selbst** (`index.lock`, 0 Byte, 239 s,
VM-Halter) **fällt unter die Kommando-Frage und ist behoben**; ein 0-Byte-Lock mit **echtem**
`git`-Halter bleibt über Bedingung 1 blockiert.

> **Der Umschnitt des Blatts bleibt bei der frischen Instanz** (Claim `6bc733bb`). *Diese
> Entscheidung ist der Operand, den sie einsetzen kann — nicht der Umschnitt selbst.*
>
> *Zur Herkunft ehrlich: der Vorschlag kam vom Generator, die Prüfung gegen den Zusagen-Bestand vom
> Evaluator. Von mir kommt die Entscheidung — und die verworfene Fassung kam auch von mir.*

### ENTSCHEIDUNG Planner 08.08. — A-01s Nicht-Ziel BLEIBT

**Der A-05-Messbericht liegt** ([`BERICHT-A-05-l-kontur.md`](BERICHT-A-05-l-kontur.md), `e0fae829`)
und legt die Entscheidung ausdrücklich mir vor. **Sie ist gefallen.**

```text
1  ueber roofType hinaus fehlt roof.anbau mit ALLEN vier Massen
   -> und KEIN Bestandscode leitet es aus einer Kontur ab
2  lTBauGueltig / uBauGueltig sind VALIDIERER - ein Kontur-ERKENNER existiert nicht
   -> selbst gegengemessen: 0 Erkenner im Bestand
3  ein l-shape-Dokument laedt schema-gueltig und bleibt ein STILLES LEERES DACH
4  Lueckenliste: ACHT Punkte. "nur die Formzuweisung" ist WIDERLEGT
```

> ### Meine Hypothese vom 05.08. ist endgültig widerlegt.
>
> *„Die Insel kann L-Dächer möglicherweise schon"* — sie ist zweimal geschrumpft (erst „rendert" →
> „die Pfade existieren", dann die stille Leere) und **fällt jetzt ganz**: acht Lücken, kein
> Erkenner, keine Ableitung.

**Und A-01 gewinnt dadurch an Wert, statt zu verlieren.** *Messung 3 zeigt: ein schema-gültiges
`l-shape`-Dokument erzeugt heute ein stilles leeres Dach **ohne jede Meldung** — genau der Zustand,
gegen den A-01-4 gebaut wurde, nur auf dem anderen Pfad.* **Die Absage war nicht die kleine Lösung,
sondern die einzige, die heute trägt.**

**Vorbehalt:** der Bericht ist `CODE_FERTIG`, **nicht abgenommen**. *Fällt eine der vier Messungen
in der Abnahme, prüfe ich neu — die Entscheidung hängt aber nicht an Zahlen, sondern an zwei
Strukturbefunden, und den ersten habe ich selbst gegengemessen.*

### Warteschlange auf `scripts/commit-pruefen.sh` — Planner-Entscheidung 10.08.

**Drei `ENTWURF`-Blätter ändern dieselbe Datei:** A-07 (kein Claim) · A-09 · A-11 (beide Claim der
zweiten Instanz). *Die bestehende Reihenfolge `A-04 → A-07 → A-09 → A-10` kannte A-11 nicht — es
wurde danach geschnitten.*

> ### ENTSCHIEDEN (10.08., zusammengeführt): **EINE** Reihe — `A-10 → A-09 → A-11`
>
> **Vorher gab es ZWEI Reihenfolgen, und keine nannte die andere:**
>
> ```text
> §3-Warteschlange (global)   A-04 -> A-07 -> A-09 -> A-10    ohne A-11
> meine Datei-Entscheidung    A-07 -> A-09 -> A-11            ohne A-10
> ```
>
> *Dieselbe Klasse wie der §16-Befund vom 05.08.: **zwei Wahrheiten über denselben Gegenstand**,
> die auseinanderlaufen, sobald eine fortgeschrieben wird. Ich habe die Datei-Reihenfolge
> entschieden, ohne die §3-Reihe zu nennen, in der A-10 längst stand.*
>
> **Gemessen, wer welche Datei anfasst:**
>
> ```text
> A-09  scripts/commit-pruefen.sh
> A-11  scripts/commit-pruefen.sh
> A-10  renderers/three-d/szene.ts     <- KEIN Dateikonflikt; die Nennung von
>       commit-pruefen.sh steht bei A-10 nur im Auswirkungen-Block (Bundle/Tor)
> ```
>
> **Warum A-10 zuerst:**
>
> ```text
> 1  A-10 behebt einen Mangel, den ein NUTZER sieht - ein Dach, das nichts zeigt
>    und nichts sagt. A-09 und A-11 verbessern Werkzeug, das bereits funktioniert.
> 2  A-09 und A-11 teilen sich commit-pruefen.sh und muessen ohnehin nacheinander
>    laufen (§3 Z.85: hoechstens EIN Auftrag IN_ARBEIT). Die GESAMTZEIT bleibt
>    damit gleich; A-09 und A-11 beginnen lediglich je einen Bau spaeter.
>    KORRIGIERT 10.08.: hier stand "kostet sie NICHTS" - eine Spur zu weit.
>    Der Messwert trug "Gesamtzeit gleich", nicht "kostet sie nichts".
> 3  A-10 ist der einzige der drei OHNE Claim. Wer frei ist, kann ihn sofort ziehen.
> ```
>
> **Die Datei-Reihenfolge `A-09 → A-11` gilt unverändert** — sie ist jetzt Teil der einen Reihe
> statt einer zweiten Liste daneben.
>
> <details><summary>frühere Fassung (nur Dateikonflikt)</summary>
>
> ```text
> 1  Maengel vor Faehigkeit bei geteilter Datei (A-07/A-09 beheben, A-11 ergaenzt)
> 2  A-11s Nutzen (zaehlbare Zeile fuer §13) beginnt erst mit der NAECHSTEN
>    Zehnergruppe - die kann nicht beginnen, solange der Zaehler auf 10 steht
> 3  A-11 aendert als einziges die MELDEFORM des Tors -> zuletzt, wenn die
>    anderen beiden abgenommen sind
> ```

</details>

**Claim auf dem BLATT bei der zweiten Instanz — Reihenfolge ÜBER Blätter beim Planner** (P-02).
**Nicht von mir:** wer A-11 abnimmt (Vorschlag und tragende Zahl stammen vom ersten Evaluator).

### Push-Lage — am Zustand gemessen (10.08. 18:5x, dritte und letzte Fassung)

**Nach frischem `git fetch fork`:**

```text
fork/main                          e7c6e618   zuletzt bewegt 10.08. 18:56
fork/auto/hausplaner-integration   1759e82f
lokaler HEAD                       60ebed62
HEAD auf fork/<Arbeitszweig>?      NEIN - 3 Commits liegen nur lokal
```

> ### Die Lage ist GETEILT — beide meiner früheren Aussagen waren zu grob.
>
> **Veröffentlichung nach `main` funktioniert.** *`fork/main` hat sich vor drei Minuten bewegt.
> Insoweit stimmt die Rücknahme: A-08 ist veröffentlicht, und es gibt keine Zuständigkeit, die
> niemand ausführen kann.*
>
> **Der Sicherungs-Push des Arbeitszweigs hängt wirklich.** *Zwei Vermerke (`2b5aebae`,
> `60ebed62`) und ein **messbarer Rückstand von drei Commits**. Insoweit war die ursprüngliche
> Sorge berechtigt und mein „Einzelfall, keine strukturelle Lücke" zu breit.*

**Praktisch:** drei Dokumentations-Commits liegen im Moment **nur lokal**. *Kein Verlust an
abgenommenem Code — A-08 und A-04 sind über `main` gesichert —, aber die Arbeit dieser Runde hängt
an dieser Maschine.*

**Zu meinem eigenen Verhalten:** *das ist die dritte Fassung derselben Aussage. Erst habe ich einen
Log-Vermerk für den Zustand genommen, dann eine fremde Richtigstellung zu weit gelesen. **Beide
Male fehlte dasselbe: ein Blick auf den Zustand nach einem `fetch`.** Der Unterschied ist ein
Befehl, und er kostet Sekunden.*

**Ich hatte gemeldet, die Vertretungsregel vergebe eine Zuständigkeit, die niemand ausführen kann.
Unabhängig nachgemessen:**

```text
85b03d23 Vorfahr von fork/main   JA      b2f8c44b Vorfahr von fork/main  JA
fork/main steht auf 8648a4cb             fetch lief 10.08. 18:42
lokal nicht auf fork: 5 Commits          (behauptet waren 32)
```

**A-08 ist VERÖFFENTLICHT, der Release-Prüfer pusht im Takt.** *Es bleibt **ein** abgelehnter
Push-Versuch (`2b5aebae`) — ein Einzelfall, keine strukturelle Lücke.*

> **Mein Fehler, benannt:** *ich habe eine Behauptung bestätigt, indem ich einen **passenden Vermerk
> im Verlauf** fand, statt den Zustand zu messen.* **Falle 1 — Zuordnung annehmen statt messen, der
> sechste Fall.** Ausgerechnet die Klasse, für die ich vor drei Runden begründet habe, dass es
> keine Barriere gibt.
>
> *Der Messfehler der zweiten Instanz gehört derselben Familie an und ist präzise: `ahead N` aus
> `git status -sb` vergleicht gegen den Remote-**Tracking**-Ref — ohne `fetch` ist das eine Aussage
> über das eigene Gedächtnis, nicht über die Außenwelt.*

### ⚠ AN DEN EVALUATOR, vor der A-07-Abnahme — mein Zusatz-Nachweis braucht einen Vorschritt

**A-07 wirkt, selbst gemessen vor und nach dem Bau:**

```text
Phantom-Loeschungen   10 -> 0
--name-only           32 -> 0
git status            46 -> 2
```

**Der Zusatz-Nachweis (Rest B), den ich ins Kriterium geschrieben habe, meldete einen Treffer —
und der Treffer war MEINER, nicht der des Baus:**

```text
mein Befehl        git show HEAD:<f> | diff - <f>   -> "identisch" = Phantom
zz-unlink-probe    im Index 0 · in HEAD 0 · git status "??"  = UNTRACKED und LEER
-> `git show HEAD:<f>` liefert nichts, leer gegen leer ist identisch
   => faelschlich als Phantom gelesen
```

> **Beide `git status`-Einträge sind echt (zwei untracked Dateien). A-07s Zusatz-Nachweis besteht.**

**Die Stichprobe muss ZUERST fragen, ob der Pfad überhaupt getrackt ist** (`git ls-files <f>`) **und
untrackte Pfade als echt zählen.** *Ohne diesen Vorschritt erzeugt mein eigenes Kriterium einen
falschen Befund gegen einen fehlerfreien Bau.*

*Ich hätte es beinahe als Mangel gemeldet. Gefunden habe ich es nur, weil ich vor der Meldung
gemessen habe statt zu behaupten.*

### A-07-1b — der Kippfall liegt LIVE vor (10.08. 19:1x)

**Nach dem Bau stand `git status` auf 2. Jetzt auf 212.** *Gemessen, statt einen Rückfall zu
vermuten:*

```text
212  "A "  GESTAGT - ein ganzer Baum docs/rollenkette/
  1  "??"
  0  Phantom-Loeschungen
--name-only 212 = genau die gestageten Neuzugaenge
```

> **Kein Rückfall — der Kippfall aus A-07-1b:** *Index-Blobs, die in **keinem** Commit vorkommen.
> Genau dafür ist das Kriterium zweigeteilt: im Regelfall gleicht das Tor an, im Kippfall lässt es
> den Index **unangetastet** und **meldet** mit Zahl und Pfaden.*

> ### ✅ PROBE GELAUFEN — das Tor hat GEMELDET, nicht angeglichen.
>
> ```text
> INDEX NICHT ANGEGLICHEN  211 Index-Blob(s) in keinem Commit - echte ungesicherte
> Arbeit, der Standard-Index bleibt unangetastet: <211 Pfade genannt>
> ```
>
> **A-07-1b ist damit nicht an einem Fixture belegt, sondern an 211 echten fremden Dateien.**
>
> *Zahlenprobe: `git status` zeigt 212, das Tor meldet 211 — die Differenz ist der eine
> `??`-Eintrag, untracked und damit nicht im Index. **Die Zahlen widersprechen sich nicht, sie
> messen Verschiedenes** — damit daraus niemand einen Off-by-one-Befund macht.*

### ⚠ AN YAMA — 211 Dateien liegen NUR im Index

**`docs/rollenkette/`** — ein vollständiger Rollen- und Werkbank-Baum: Rollenbeschreibungen,
**23 Werkzeugmappen**, Übergabeformulare. **In keinem Commit.**

*Zusammen mit dem hängenden Sicherungs-Push des Arbeitszweigs ist das die größte ungesicherte
Menge, die ich in dieser Gruppe gesehen habe.* **Ich fasse sie nicht an — sie gehört dem, der sie
gestagt hat. Ich melde nur, dass sie da ist und nirgends gesichert.**

**Der nächste Tor-Lauf ist die Probe — und niemand hat sie gestellt, sie ist von selbst entstanden:**

```text
gleicht an trotz 212 fremder Blobs   -> MANGEL gegen A-07-1b
meldet und laesst den Index in Ruhe  -> Kriterium im FELD belegt
```

*Ein Kriterium, das an echter fremder Arbeit geprüft wird statt an einem Fixture, ist mehr wert als
jede Wegwerf-Zusage — **der Evaluator bekommt den Fall frei Haus.***

**`docs/rollenkette/` fasse ich nicht an.** *212 Dateien fremder, ungesicherter Arbeit — sie liegt
im Moment **nur im Index**, in keinem Commit.*

### ⚠ VOR DER NÄCHSTEN DACHKONSTRUKTION — W-07 beschreibt einen ANDEREN Weg als die Insel

**Gemessen beim Werkbank-Anschluss** (Befund der zweiten Instanz: `32f83a6f`, *„Code → Werkbank
eintragen", nicht umgekehrt*):

```text
Werkbank W-07 "Dach aus Kontur"   Register: BESCHRIEBEN, nicht leer
  F-020 Straight Skeleton   "KERN. Firste, Grate, Kehlen erzeugen"
  F-021 Skelett anheben     aus dem flachen Skelett das raeumliche Dach
  F-010 / F-013 / F-022     Orientierung · Selbstschnitt · Neigung

Insel heute (A-05 gemessen)
  roof.anbau mit VIER Massen · KEIN Kontur-Erkenner · Achtpunkt-Lueckenliste
```

> **Ein Straight Skeleton erzeugt Firste, Grate und Kehlen DIREKT aus der Kontur — die Frage nach
> `anbau` und Erkenner stellt sich dort nicht.**

**Was ich ausdrücklich NICHT sage:** *dass die acht Punkte damit hinfällig sind.* **Sie wurden
gegen den Weg der Insel gemessen.** *Ob F-020 sie ersetzt, habe ich **nicht** gemessen — das wäre
genau die Unterform, die heute zweimal aufgetreten ist: eine richtige Messung, aus der eine zu
weite Aussage folgt.*

**Warum es jetzt gehört:** Yamas Auftrag lautet *„erst Werkbank-Anschluss, dann unmittelbar
Dachkonstruktion"*. **Wer sie schneidet, ohne die zwei Wege nebeneinanderzulegen, schneidet gegen
die falsche Grundlage** — entweder baut er die Achtpunkt-Liste ab, obwohl ein Skeleton sie
überspringt, oder er baut ein Skeleton **neben** eine Insel, die schon `verschneidungsFlaechen` hat.

**Und:** der A-05-Bericht — **360 Zeilen gemessene Grundlage** — ist in der Werkbank **nirgends**
referenziert (0 Treffer). *Wer W-07 einträgt, sollte ihn kennen, sonst wird dieselbe Messung ein
zweites Mal gemacht.*

### ✅ GESCHLOSSEN — Testnutzer 268/269 geräumt (auf Yamas Freigabe)

**Am Zustand geprüft, nicht am Vermerk:**

```text
DB ticket_testing   Nutzer 268 weg · Nutzer 269 weg · users gesamt 0
```

*Der Befund, den ich vor einer Runde als „braucht eine Aufräumung mit eigenem Auftrag" notiert
hatte, ist erledigt — **Yama hat den kürzeren Weg genommen und direkt freigegeben.** Kein Auftrag
nötig, keine Zehnergruppen-Frage berührt.*

> **Mein eigener Messfehler dabei, selbst gefunden:** *der erste Befehl lief gegen **`ticket`** —
> die Arbeits-DB —, während die Nutzer in **`ticket_testing`** angelegt worden waren.* **„Weg in
> `ticket`" beweist über `ticket_testing` gar nichts.** *Der Befund wäre richtig gewesen und die
> Messung wertlos.*
>
> *Dieselbe Klasse wie die 2D/3D-Verwechslung des Evaluators eine Stunde zuvor: **nicht das Objekt
> war falsch, sondern der Ort, an dem gemessen wurde.** Bei ihm die Ansicht, bei mir die Datenbank.*

**Regelwerk:** `ARBEITSREGELN.md` **1.2.2**, freigegeben (P-01 geschlossen, `7eeea70c`).
**Zähler §13:** **7 von 10** — vor Aufgabe elf steht die Pflichtprüfung.

### ✅ ERLEDIGT 06.08. 18:18 — die Zweige sind zusammengeführt

**In Yamas Vertretung gemergt** (`27a61da9`, davor Sicherung `db3f7cbd`). **Selbst nachgemessen:**

```text
A-01  94b58aaf   Vorfahr von HEAD: JA
A-02  6953198a   Vorfahr von HEAD: JA
A-03  26e378a5   Vorfahr von HEAD: JA
scripts/browser-buehne.sh   DA        -> A-04 ist entblockt
A-01-Fixture + szene.ts nichtDarstellbar()   mitgekommen
```

**Damit wirken alle drei abgenommenen Aufträge auf dem Arbeitszweig.** *Der Blocker, der seit
09:45 jede Runde oben stand, ist zu.*

<details><summary>frühere Lage (historisch)</summary>

**Zwei abgenommene Baue wirkten auf dem Arbeitszweig nicht**, und daran hing der nächste Bau:

```text
A-01  94b58aaf   Vorfahr von HEAD: NEIN     A-04 ist deshalb nicht baubar
A-03  26e378a5   Vorfahr von HEAD: NEIN
A-02  6953198a   Vorfahr von HEAD: ja
dazu die Gabelung: fork ist 42 Commits voraus und enthaelt den governance-Merge
```

</details>

---

### Warum es diese Tafel erst jetzt gibt — ein Versäumnis von mir

**Meine §16-Entscheidung** (Mitteilung 4) hat `zustand`, `ballbesitz`, `pruef_sha` und
`letztes_votum` aus allen Blättern entfernt — richtig, weil sie drifteten. **Ich habe aber nie
geprüft, ob die verbleibende Wahrheit auffindbar ist.**

```text
STATUS.md            921 Zeilen, elf Mitteilungen vor der ersten Zustandsangabe
grep nach A-04/A-05  liefert Prosa aus der Chronik, keine Tafel
letztes_votum        aus den Blaettern entfernt, in STATUS.md nie ersetzt
```

> **Ich habe die zweite Wahrheit beseitigt und die erste unlesbar gelassen.** *Eine Statusquelle,
> in der man den Status nicht findet, ist keine — das ist derselbe Mangel, nur an einer Stelle
> weniger.*


**Autorität:** [`docs/ARBEITSREGELN.md`](ARBEITSREGELN.md) §16. Diese Seite wird **überschrieben,
nicht angehängt**. Es gibt keine zweite manuelle Statuswahrheit; historische Ledgers und
Statusseiten werden nicht fortgeschrieben, um den aktuellen Zustand zu bestimmen.

**Angelegt:** 04.08.2026 durch den Planner als Teil des Übergangs nach §17.

---

## 📢 MITTEILUNG AN ALLE ROLLEN — bitte lesen und mit einer Zeile bestätigen

**Stand 05.08., 09:0x. Drei Dinge, die seit heute früh gelten oder offen sind.**

### 1. ARBEITSREGELN sind auf Fassung 1.1 — vier neue Pflichten

```text
§3    IN_ARBEIT wird gesetzt, BEVOR die erste Datei im Scope geaendert wird
§5    Testdaten-Ziel UND Prozessbindung getrennt benennen, mit beweisendem Befehl
§5    vorgeschriebene Aufrufformen/Werkzeuge muessen auf der Zielmaschine VORHANDEN
      und IN GEBRAUCH sein - beides gemessen, nicht angenommen
§5/7  jede Anforderung ist Kriterium ODER Nicht-Ziel, kein dritter Zustand ·
      kein Kommentar behauptet Verhalten, das der Code nicht hat
```

**§5 hat jetzt 18 Punkte statt 15.** Beauftragt von Yama, Belege in §19 und
[`PROZESSPRUEFUNG-01.md`](PROZESSPRUEFUNG-01.md).

### 2. DECISION_BLOCKED — es gibt ZWEI Regelwerke, wir folgen der älteren

Unser Zweig führt **1.0/1.1**, `governance/arbeitsregeln-v1.1-20260804` führt **1.3** (592 Zeilen,
229 abweichend, eigener Statusträger `AKTUELLER_AUFTRAG.yaml`). **Bis Yama entscheidet, gilt die
Fassung im Baum (1.1).** Einzelheiten: [`BEFUND-ZWEI-REGELWERKE.md`](BEFUND-ZWEI-REGELWERKE.md).

### 3. PLANNER-ENTSCHEIDUNG zur doppelten A-02-Nachbesserung

Der Plan-Prüfer hat zwei unabhängige Fassungen desselben P1 gefunden und die Entscheidung mir
vorgelegt. **Sie lautet:**

```text
ES GILT      6953198a  (Hauptlinie, 5s-Grenze) - dort liegt der A-02-Bau, dort prueft
                        der Evaluator, dort ist die Zusage gemessen (30/30, Rot-Probe 20s->5,1s)
ES WEICHT    ca5f80e4  (auf work/a01-generator, 2s-Grenze) - wird VOR dem A-01-Merge
                        zurueckgenommen, damit die Kollision gar nicht erst entsteht
```

**Nicht weil 5s besser wäre als 2s** — A-02-6 lässt den Weg ausdrücklich frei, beide erfüllen ihn.
**Sondern weil A-02-Code auf dem A-01-Zweig nichts zu suchen hat** (§7: keine Nebenbaustellen).
*Die Zweitfassung ist kein Fehler des Bauenden, sondern die Folge davon, dass niemand wusste, was
der andere gerade tut — genau der Mangel, den diese Mitteilung behebt.*

### 4. ENTSCHEIDUNG zum §16-Befund: der Statuskopf verschwindet aus den Blättern

Der Evaluator hat gemeldet, dass **alle vier Blätter einen zweiten Status führen**, und die
Grundsatzfrage mir vorgelegt. **Sie ist entschieden.**

```text
BLATT behaelt   auftrag · titel · basis_sha        unveraenderlich je Auftrag
BLATT verliert  zustand · ballbesitz · pruef_sha · release_sha · letztes_votum ·
                naechster_schritt                  je 6 Zeilen, alle vier Blaetter
BLATT bekommt   status_steht_in: docs/STATUS.md    ein Zeiger kann nicht driften,
                                                   er hat keinen Inhalt
```

**Warum nicht „beide pflegen".** Das ist die Regel, die gerade viermal versagt hat — und es war
kein Versehen, sondern Bauart. **Der Schaden war schon konkret, nicht theoretisch:**

```text
A-03-Kopf sagte  CODE_FERTIG      obwohl in STATUS.md ABGENOMMEN
A-02-Kopf trug   pruef_sha ca5f80e4   genau die Fassung, die ich verworfen hatte
                                      (es gilt 6953198a) - der Kopf haette den
                                      Release-Pruefer auf den falschen Commit gefuehrt
```

**Die Voten bleiben in den Blättern** — als datierte Prosa-Abschnitte (Generator-Bericht,
Evaluator-Votum). *Die driften nicht: sie behaupten keinen aktuellen Zustand, sondern halten fest,
was zu einem Zeitpunkt galt.* **Der Unterschied ist nicht die Länge, sondern die Zeitform.**

> **Ins Regelwerk schreibe ich das NICHT.** Es steht auf 1.2 (Yamas mündliche Weisung), die
> Gabelung zu 1.3 ist offen, und eine dritte Hand darin würde die Lage verschlimmern. **Der
> Regeltext wird nachgezogen, sobald Yama die Fassungsfrage entschieden hat.**

### 5. P-01 an den Plan-Prüfer: die Regelwerksfassung prüfen und freigeben

**Yamas Weisung (05.08.):** *„lass doch von plan prüfer die fassung prüfen und freigeben, dann wird
das verbindlich."* **Damit ist nicht meine Niederschrift der Akt, sondern seine Freigabe.**

```text
GEGENSTAND    1.1 (vier Regeln) und 1.2.1 (fuenf Abschnitte §12.1-12.5)
NICHT DABEI   1.2 - Yamas eigene Weisung, von ihm committet, steht nicht zur Disposition
ACHT PUNKTE   Widerspruchsfreiheit · Pruefbarkeit · Herkunft (alle neun, nicht Stichprobe) ·
              MACHTPRUEFUNG gegen mich selbst · Gabelung 1.2.1 gegen 1.3 ·
              KAUSALITAET · PLAUSIBILITAET · KONSISTENZ  (Yama, 05.08.)
MEINE ZWEIFEL zu jedem der drei neuen Punkte habe ich SELBST benannt, statt sie ihn
              suchen zu lassen: §12.5 beschreibt statt zu verhindern (Kausalitaet) ·
              "in Gebrauch" ist fuer NEUE Werkzeuge unerfuellbar (Plausibilitaet) ·
              SPEC_BLOCKED traegt jetzt ZWEI Bedeutungen (Konsistenz)
Blatt         docs/PRUEFAUFTRAG-P-01-regelwerk.md
```

**VOTUM GEFALLEN (plan-pruefer 05.08.): FREIGEGEBEN MIT AUFLAGE — 1.1 und 1.2.1 sind ab sofort
VERBINDLICH.** Vier Auflagen (A1 SPEC_BLOCKED-Doppelbedeutung aufloesen · A2 „in Gebrauch"-Halbsatz
fuer neue Werkzeuge · A3 Statustraeger in §16 benennen + 1.3-Ernte · A4 §19-Tabelle trennt
„haette verhindert" von „bestaetigt durch Praxis") — Nachbesserung am verbindlichen Text, keine
aufschiebende Bedingung. Gabelung: **1.2.1 FUEHRT (Inhalt)**; alle neun Herkunftsangaben belegt,
Machtpruefung §12.5 bestanden. **Die ZWEIG-Zusammenfuehrung (fork traegt den governance-Merge,
wir nicht, 42 vs 10 Commits) bleibt bei YAMA — Topologie, nicht Text.** Volles Votum:
docs/PRUEFAUFTRAG-P-01-regelwerk.md.

### 6. ⚠ SPEC-BEFUND an A-01: die Insel kann L-Dächer möglicherweise schon

**Auf Yamas Frage gemessen** („warum greift ihr auf playground und PV-Dachplaner nicht zurück"):
**0 von 4 Auftragsblättern** haben je eine Wiederverwendungsprüfung gegen playground gemacht — bei
**65** Dach-/3D-Dateien im Archiv und einem vorbereiteten Referenzordner mit Fachvorgabe.

**Der Blick dorthin hat etwas Näheres freigelegt:** die Insel hat **zwei Dachpfade**.
`dachGeometrie.ts:87` (V1, nur Rechtecke — den fragt A-01) und `roofShape.ts` +
`dachVerschneidung.ts` (`lTBauGueltig`, `verschneidungsFlaechen`) + `dachUForm.ts` — **mit Tests,
Eigenschaftenpanel und Renderer-Anbindung, für genau die Dächer, die A-01 als Nicht-Ziel führt.**

**HYPOTHESE, ausdrücklich ungemessen:** ein L-Dach ist evtl. erreichbar, indem beim Anlegen die
**Form** gesetzt wird, statt eine Absage zu bauen. **A-01 läuft weiter** — der A-01-4-Mangel ist
davon unabhängig echt. Details: [`BEFUND-ZWEI-DACHPFADE.md`](BEFUND-ZWEI-DACHPFADE.md).

### 7. A-05 geschnitten — MESSAUFTRAG, kein Bau

**Die Hypothese aus dem Dachpfad-Befund ist in ihrem Kern keine mehr.** Gemessen:

```text
app/HausplanerApp.tsx:962   roofType: 'sattel'   FEST VERDRAHTET beim Anlegen
dachMesh.ts:149/215         behandelt u-shape · l-shape · t-shape bereits

-> Der Anlege-Pfad setzt IMMER 'sattel', egal welche Kontur gezeichnet wurde.
   Der Renderer koennte 'l-shape' - er bekommt es nie.
```

**Offen bleibt, was darüber hinaus fehlt.** A-05 misst genau das, in vier Fragen, **ohne eine
Zeile Produktivcode**: welche Eingaben `verschneidungsFlaechen` braucht · ob `lTBauGueltig`
Erkenner oder Validierer ist · was heute mit einem `l-shape`-Dokument passiert · und die
Lückenliste. **Auch „nur die Formzuweisung" ist eine zulässige Antwort — mit Beleg.**

*Zum ersten Mal trägt ein Blatt eine ausdrückliche **Wiederverwendungsprüfung** mit Belegbefehlen
gegen Insel, playground-Archiv und Referenzordner. Bei A-01 bis A-04 fehlte sie — das war der
Befund.*

**A-01 bleibt unangetastet**, bis der Bericht liegt.

> **MESSUNG DES GENERATORS zu A-05, gefahren bevor das Blatt lag — sie widerspricht einem Satz
> darin.** Das Blatt sagt *„während die Insel `l-shape`-Dächer rendert"*. Mit dem A-01-Fixture
> (6-Punkt-L-Kontur, `roofType` auf `l-shape` umgestellt) rendert sie **nichts**:
>
> ```text
> dachMeshWelt(Bestandsdach, roofType='l-shape')   {"dreiecke":[],"firstHoeheMm":2500}
> dachflaechen(dasselbe)                           0 Flaechen
> dasselbe mit roofType='sattel'                   DachGeometrieUngueltig (die A-01-Absage)
> ```
>
> **Sie wirft nur nicht mehr.** Ein stilles leeres Dach ist schlechter als eine Absage — genau der
> Zustand, den A-01-4 beseitigt hat.
>
> **Was das NICHT belegt:** dass die Insel es nicht kann. Wahrscheinlicher fehlen dem Fixture die
> Eingaben, die `verschneidungsFlaechen` über `roofType` hinaus braucht — *und das ist wörtlich
> A-05-1*. Die Messung beantwortet die Frage nicht, sie schärft sie: **„Renderer könnte, bekommt
> es nie" ist zu optimistisch, solange niemand gemessen hat, was er mit `l-shape` tatsächlich
> ausgibt.**
>
> **Herkunft, offen gesagt:** gefahren in einer Wegwerf-Zusage unter `__tests__/`, die ich wieder
> entfernt habe — es gibt dafür **keinen Commit**, nur den reproduzierbaren Aufruf oben. A-05
> verbietet Änderungen in `resources/`; ab jetzt laufen meine Proben außerhalb des Produktivbaums.
> *Wer den Befund verwenden will, misst ihn im Rahmen von A-05 selbst nach.*

> **NACHTRAG DES GENERATORS (12:1x) — Gegenlesen des A-05-Entwurfs, bevor er mir zugeteilt wird.**
> Die vier Fragen sind mit Lesen und Wegwerf-Proben **erfüllbar**; kein unerfüllbarer Prüfbefehl
> wie bei A-01. **Ein Restwiderspruch steht aber noch im Blatt:**
>
> ```text
> Z. 66/67   "Meine Formulierung 'ausserhalb des Produktivbaums' war unerfuellbar.
>             Nachgezogen: ueblicher Ort erlaubt"        <- die Korrektur, im Kasten
> Z. 19      "Erlaubt: ... Wegwerf-Proben ausserhalb des Produktivbaums"
>                                                        <- die VERBINDLICHE Liste, alt
> Z. 83      A-05-3 Antwortform: "... ausserhalb des Produktivbaums"   <- alt
> ```
>
> **Die Korrektur steht in der Erläuterung, die Regel selbst ist unverändert** — und §7 verbietet
> mir, einen vorgeschriebenen Weg still zu ersetzen. Wer das Blatt der Reihe nach liest, steht
> wieder vor demselben Konflikt, den der Planner gerade aufgelöst hat.
>
> *Kleiner Befund, aber genau der Typ, der bei A-01 zwei Runden gekostet hat: dort war die
> Unerfüllbarkeit auch erst nach dem Bau benannt.* **Ich fasse das Blatt nicht an — es ist ENTWURF
> beim Planner.**
 *Ob sein Nicht-Ziel fällt, entscheide ich mit
dem Ergebnis, nicht mit der Vermutung.*

> **MESSUNG DES GENERATORS zu A-07 (14:5x), unaufgefordert — die offene Frage ist beantwortbar.**
> Der Schnitt sagt zu Weg A: *„Das ist aber eine Vermutung darüber, ob dort je etwas liegt, und die
> gehört gemessen."* Gemessen, an allen 60 Einträgen des Standard-Index:
>
> ```text
> ALTER STAND      Index-Blob liegt in der Historie der Datei     43   gefahrlos zu verwerfen
> PHANTOM-LOESCHUNG im Index geloescht, Datei liegt da            17   der Evaluator-Befund
> ECHTE ARBEIT     Blob in KEINEM Commit                           0
> ```
>
> **Kein einziger Index-Eintrag trägt Arbeit, die nirgends gesichert ist.** Stichproben zeigen den
> Charakter: `docs/STATUS.md` steht auf `95800012` (05.08. 10:48), `HausplanerDocument.php` auf
> `76a7dc6d` (16.07.). *Der Index ist ein eingefrorener Schnappschuss, kein Arbeitsspeicher.*
>
> **Was das für die Weg-A-Bedingung heißt — und es widerspricht ihr:** *„angleichen nur, wenn
> nichts gestaget ist"* würde **nie greifen**. Es sieht permanent so aus, als lägen 60 Dateien
> gestaget da. Die Bedingung, so formuliert, schaltet das Angleichen dauerhaft ab und Weg A wäre
> in der Praxis Weg B. **Die messbare Fassung lautet:** angleichen, solange **kein Index-Blob
> existiert, der in keinem Commit vorkommt** — heute erfüllt (0 von 60), und der Befehl dafür ist
> gefahren, nicht gedacht.
>
> **Eine eigene Fehlmessung lege ich offen:** mein erster Durchgang meldete `docs/handoff-status.md`
> als „nicht in der Historie". Ich hatte auf 40 Commits je Datei begrenzt — die Datei hat **567**.
> Der Blob liegt in `15f51340` (03.08. 13:21). *Ohne den zweiten Durchgang hätte ich einen
> Phantom-Fund gemeldet und A-07-2 auf eine Datei gestützt, die nie gefährdet war.*
>
> **Ich fasse den Index nicht an.** Er gehört einer anderen Rolle, A-07 ist noch nicht `BEREIT`,
> und die Entscheidung zwischen A und B liegt beim Plan-Prüfer.

> **NACHTRAG DES GENERATORS (15:3x) — der Mangel steckt in MEINEM Werkzeug, und ich habe seine
> Schärfe gemessen.** `commit-pruefen.sh:57-62` ist mein Bau. Der Befund stimmt: der Pfad trägt
> die PID, wird **nie initialisiert und nie geräumt** — kein `read-tree`, kein `rm`.
>
> **Zuerst gegen mich selbst gemessen: haben meine sieben Commits Beifang?**
>
> ```text
> 7fdf6e05  5 Dateien   94b58aaf  2   90ebba40  2   9e97d274/a4de38f2/6702a441/1839d2e3  je 1
> -> jeder Commit traegt GENAU die Pfade, die ich genannt habe. Kein Beifang.
> ```
>
> **Das war Glück, nicht Schutz.** Stichprobe über die liegengebliebenen Indizes (nur lesend,
> A-07-3 unangetastet):
>
> ```text
> Tor-Indizes gesamt                  1739
> Stichprobe 25:  identisch mit HEAD    24   Erbschaft faellt nicht auf
>                 WEICHT AB              1   index.10038 (03.08. 08:41): 7011 Eintraege
> ```
>
> **Ein einziger geerbter Index trägt einen kompletten Fremdbaum.** Wer die PID 10038 zieht,
> committet 7011 Dateien mit — darunter `.ai-workflow/`, das längst entfernt ist. *Der Mangel ist
> nicht selten harmlos, er ist meistens unsichtbar und einmal katastrophal.* Genau deshalb ist er
> bei mir nie aufgefallen.
>
> **Zur Reichweite ehrlich:** 25 von 1739 sind eine Stichprobe, keine Quote. Ich rechne sie nicht
> hoch — der Befund ist „es gibt solche Indizes und sie sind vollständig", nicht „4 %".
>
> **Wenn A-07 zum Bau kommt, ist es mein Auftrag** — es ist mein Werkzeug und mein Versäumnis.
> Ich baue nichts, solange das Blatt `ENTWURF` ist.

> **A-07-ENTWURF GEGENGELESEN (16:0x) — drei Stellen, alle gemessen, bevor ich es baue.**
>
> **① `A-07-1a`: der vorgeschriebene Nachweis deckt den Befund nicht ab.**
>
> ```text
> git diff --cached --diff-filter=D --name-only    17   <- der Nachweis im Blatt
> git diff --cached --name-only                    60   <- der tatsaechliche Unterschied
> ```
>
> *Wer nur die 17 Phantom-Löschungen behebt und die **43 veralteten Stände** stehen lässt, ist nach
> dem Blatt **grün** — und der Index bleibt divergent.* Das Kriterium sagt „an HEAD angleichen";
> der Nachweis prüft ein Siebtel davon. **Vorschlag: `--name-only` meldet 0.**
>
> **② `A-07-4`: „am Ende wegräumen" hat bei einem Abbruch kein Ende.** Am Tor gemessen:
>
> ```text
> trap                    0
> exit-Punkte             7
> rm des eigenen Index    0
> ```
>
> **Sieben Auswege, kein einziger räumt.** Genau daraus ist die Halde entstanden — ein Lauf, der
> bei `FEHLER` oder `ENV_BLOCKED` aussteigt, erreicht ein „am Ende" nie. *Ohne `trap … EXIT` wäre
> das Kriterium mit einem `rm` in der letzten Zeile grün und der Befund käme über die Abbruchpfade
> zurück.*
>
> **③ `A-07-5` nennt eine feste Zahl, die schon jetzt falsch ist.** Drei Zahlen in einem Blatt,
> weil die Halde mit **jedem** Lauf wächst — auch mit denen des Bauenden und des Evaluators:
>
> ```text
> Blatt A-07-5    1736     Rot-Beleg A-07-4    1738     von mir gemessen (16:0x)    1741
> ```
>
> *„Die 1736 Dateien" ist beim Bau nicht mehr erfüllbar.* **Vorschlag: „alle zum Zeitpunkt des
> Laufs vorhandenen, Zahl im Bericht" — dann trägt der Bericht die Zahl, nicht das Kriterium.**
>
> **Keiner der drei Punkte ist ein Einwand gegen den Auftrag** — er ist gut geschnitten, und Weg A
> in der messbaren Fassung trägt. Sie sind der Grund, warum Gegenlesen **vor** `BEREIT` billiger ist
> als nach dem Bau.

### 8. ⚠ ENTSCHEIDUNG YAMA — A-06: sieben Fremdzeilen in der Arbeits-DB

Der Evaluator hat es gegen sich selbst gemeldet und **richtig nicht gelöscht** (§15). Ich habe es
vollständig vermessen und als Auftrag geschnitten. **Es wird nichts gelöscht, bis Yama freigibt.**

```text
FALL A  5 Hausplaner-Dokumente (doc 20-24) auf ECHTEN Alternativen 139-143
FALL B  2 SYNTHETISCHE Zeilen 990002/990004 in lead_alternative_adds + ihre Dokumente

NICHTS UEBERSCHRIEBEN - belegt: Alternativen vom 29.06., Dokumentzeilen ENTSTANDEN
am 03.08. 23:11-23:26. Diese Alternativen trugen vorher kein Dokument.
```

**Eine Annahme von mir hat die Messung widerlegt:** ich ging von echten Kundendaten aus.
`customers` = **0 Zeilen**, `leads` = **0 Zeilen**. Die lokale `ticket` trägt keine Kundendaten;
die betroffenen Zeilen sind verwaiste Strukturdaten. **Das senkt das Risiko erheblich und ändert
nichts an der Grenze** — §15 verbietet Testdaten in der Arbeits-DB unabhängig vom Schaden.

**Yamas Entscheidung ist eine Ja/Nein-Frage**, keine Rechercheaufgabe: Blatt
[`A-06`](auftraege/aktiv/A-06-probedaten-arbeits-db.md), mit Sicherungspflicht vor dem ersten
`DELETE` — `hausplaner_snapshots` ist leer, die Datei ist der einzige Rückweg.

### 9. ✅ P-01 FREIGEGEBEN MIT AUFLAGE — 1.1 und 1.2.1 sind VERBINDLICH

**Der Plan-Prüfer hat geprüft und freigegeben** (gemessen an `90ebba40`). **Yamas Weisung macht
sein Votum zum Akt** — die Fassungen gelten ab sofort, die Auflagen waren Nachbesserung am
geltenden Text, keine aufschiebende Bedingung. **Alle vier sind erledigt (Fassung 1.2.2):**

```text
A1  §3   SPEC_BLOCKED ist EINE Lage mit zwei Erkennungswegen - kein neuer Zustand
A2  §5   "in Gebrauch" gilt fuer VORHANDENE Formen; neues Werkzeug -> benannter Erstnutzer
A3  §16  docs/STATUS.md NAMENTLICH benannt · 1.3-Ernte: Push=Transport · Statuscommit
         ohne Produktivcode (abgeschwaecht, Begruendung im Aenderungsverzeichnis)
A4  §19  Fall-Spalte trennt "haette verhindert" von "bestaetigt durch Praxis"
```

**Zwei Ergebnisse, die gegen mich liefen:**

- **Kausalität:** mein Verdacht gegen §12.5 war richtig — **und traf auch 12.3 und 12.4.**
  Drei von neun Regeln beschreiben, statt zu verhindern.
- **Machtprüfung:** mein Verdacht war **falsch**. §12.5 entlastet den Bauenden, nicht mich —
  der `SPEC`-Befund bleibt verbucht, erzwingt einen Folgeauftrag und zählt in §13 **gegen den
  Planner**. *Der Verdacht war richtig gestellt und hält der Prüfung nicht stand.*

**Gabelung: 1.2.1 FÜHRT inhaltlich** (gemessen: `AKTUELLER_AUFTRAG.yaml` hat 0 Verwendungen hier,
1.3 fehlen die vier 1.1-Regeln, ein Trägerwechsel mitten in vier Aufträgen kostet ohne Gewinn).
**Die Zweig-Zusammenführung bleibt bei Yama** — `fork` enthält den governance-Merge, wir nicht,
42 gegen 10 Commits. *Topologie, nicht Fassungsinhalt.*

### 11. Antwort auf den Index-Befund des Evaluators — 16 Phantome, 0 echte Verluste

**Sein Alarm war berechtigt, die Lage ist es nicht.** Gemessen, jede Datei einzeln gegen die Platte
und gegen HEAD:

```text
Index meldet Loeschungen                16
davon wirklich von der Platte weg        0
Stichprobe (ARBEITSREGELN · AUFTRAGSZAEHLER · A-05 · workspaceIds.ts ·
SnapshotRueckwegVersionTest)             alle DA und identisch mit HEAD
```

**Die Ursache ist bekannt und liegt im Tor selbst.** `commit-pruefen.sh` legt `GIT_INDEX_FILE`
außerhalb des Mounts ab (Stufe 5). **Der normale `.git/index` erfährt deshalb nie etwas von einem
Tor-Commit** — jede über das Tor angelegte Datei sieht dort aus wie gelöscht.

> **Die Gefahr ist trotzdem echt, nur anders als befürchtet.** Nichts ist verloren — **aber ein
> `git commit` AM TOR VORBEI würde die 16 Löschungen ausführen**, und darunter sind
> `ARBEITSREGELN.md`, vier aktive Auftragsblätter und Produktivcode.
>
> *Das ist derselbe Mechanismus, der am 04.08. dazu führte, dass `git status` und `git diff HEAD`
> beide logen. Die einzige verlässliche Probe bleibt `git show HEAD:<pfad> | diff - <pfad>`.*

### Mein eigener Fehler in derselben Runde — ich habe fremde Arbeit unter meinem Namen committet

**`576b6290` trägt meine Botschaft, aber ausschließlich SEINEN Text.** Mein Skript hatte STATUS.md
korrekt nicht angefasst (Freiheitsprüfung schlug an) — **und ich habe die Datei trotzdem ans Tor
gegeben.**

```text
576b6290   docs/STATUS.md | 67 +   -> null Zeilen von mir, 67 vom Evaluator
```

**Die Prüfung war da, ich habe ihr Ergebnis nicht benutzt.** *Genau die Klasse, die ich anderen
vorhalte: das Werkzeug hat gemessen, und der Aufrufer hat die Messung ignoriert.* **Rückgängig
mache ich nichts** — der Inhalt ist richtig und gehört in die Datei; falsch ist nur, wessen Name
darübersteht. **Hiermit richtiggestellt: der Befund ist seiner.**

### Kenntnisnahme — jede Rolle trägt sich mit ihrem nächsten Commit ein

| Rolle | gelesen | SHA der Bestätigung |
|---|---|---|
| Planner | ✅ 05.08. 09:0x | (Verfasser) |
| Plan-Prüfer | ✅ 05.08. 09:1x — v1.1 im Wortlaut gelesen (450b5bee-Diff), die 18 §5-Punkte sind ab sofort mein Maßstab; die A-02-Entscheidung (6953198a gilt) deckt sich mit meinem Befund | SHA dieses Commits (Sicherung nach Yamas Freigabe, 05.08.) |
| Generator | ✅ 05.08. — v1.1 gelesen. **Drei der vier neuen Pflichten stammen aus meinen Fehlern**: `IN_ARBEIT` vor der ersten Aenderung (zweimal versaeumt, beide Male nachgetragen) · „kein Kommentar behauptet Verhalten, das der Code nicht hat" (meine A-02-Zeitgrenze stand nur im Kommentar) · „Werkzeuge VORHANDEN **und in Gebrauch**". Die A-02-Entscheidung nehme ich an: `ca5f80e4` weicht, ich nehme sie auf `work/a01-generator` zurueck. **§7 ist der Grund, nicht die Kommunikation** — A-02-Code hatte auf dem A-01-Zweig nichts zu suchen, unabhaengig davon, wer was wusste | SHA dieses Commits |
| Generator (1.2.2) | ✅ 05.08. 13:3x — **1.2.1 und 1.2.2 im Diff-Wortlaut gelesen** (`450b5bee..8fc5edb8`, 154 neue Zeilen). Was mich trifft, in der Reihenfolge der Wirkung: **§12.2** „Reparatur auf der Linie des Baus" ist wörtlich mein A-02-Fehler — `ca5f80e4` lag auf dem A-01-Zweig, und genau die zwei Fassungen wären beim Merge kollidiert. **§12.3 Zwei-Richtungs-Probe**: bei A-01-4 habe ich sie gefahren (erste Mutationsprobe **3 BLIND** = der Rot-Beleg, dass die alte Zusage nichts misst; zweite **5/6 GEFANGEN**), aber **nicht als solche benannt** — ab jetzt steht sie je Befund ausdrücklich im Bericht, nicht nur zufällig darin. **§12.1**: `SPEC` gehört dem Planner. Bei A-01 habe ich den unerfüllbaren Prüfbefehl mitgetragen, statt ihn als fremden Anteil zu melden — das war zu viel Demut, nicht zu wenig. **§12.4**: Mutationsprobe bei jeder Wieder-Abnahme erneut, auch wenn sie eben grün war. **§16/A3** habe ich an meinen eigenen Commits nachgemessen: `7fdf6e05`/`94b58aaf` tragen nur Produktivcode ohne Zustandswechsel, `90ebba40`/`9e97d274`/`a4de38f2` nur Status und Blatt ohne Produktiv-, Test- oder Regeldatei — **kein Verstoß gegen die neue Trennung** | SHA dieses Commits |
| Evaluator | ✅ 05.08. 14:0x — **1.2.1 und 1.2.2 im Diff-Wortlaut gelesen** (`7c7d38f6`, `8fc5edb8`). Was meine Rolle ändert: **§12.1** — `SPEC` bekommt `SPEC_BLOCKED` und geht an den Planner, **nicht** `NACHBESSERN`; bei A-01 habe ich den gemischten Befund als ein Rot an den Bauenden gegeben, statt ihn zu teilen und den SPEC-Teil vorzuziehen. **§12.3** — je Befund gehört der **Rot-Beleg** in meinen Bericht; ohne ihn nehme ich eine Reparatur nicht mehr ab. **§12.4** — die Mutationsprobe fahre ich bei jeder Wieder-Abnahme erneut, auch bei eben grünen Kriterien. **§12.5** — ein `SPEC`-Befund blockiert die Abnahme nicht, muss aber ausdrücklich mit Klasse, Schwere und Folgeauftrag in der Abnahme stehen. **§16/A3 an meinen eigenen sieben Commits nachgemessen** (`4f849606`, `89f373d9`, `ee5a07ec`, `5f84a9d6`, `13c65f6f`, `42c0320f`, `95800012`): ausschließlich `docs/STATUS.md` und Auftragsblätter, **keine Produktiv-, Test- oder Regeldatei** — kein Verstoß gegen die neue Trennung | SHA dieses Commits |

> **Warum überhaupt eine Bestätigung.** Auf Yamas Frage *„haben sie alle das gelesen und
> bestätigt"* lautete die ehrliche Antwort **nein** — gemessen: die drei Auftragsblätter erwähnten
> die neuen Regeln **0-mal**, im Regelwerk gab es **0** Treffer für Kenntnisnahme, und die doppelte
> A-02-Reparatur ist der bereits eingetretene Preis dafür.
>
> **Ein Commit ist keine Mitteilung.** Er legt etwas an eine Stelle, an der jemand nachsehen
> *könnte*. Diese Tabelle macht aus „könnte" ein prüfbares „hat".

---

## Aktiver Auftrag

```yaml
auftrag: A-01
titel: "Dach aus Kontur - nicht-rechteckige Kontur bekommt eine lesbare Absage"
datei: docs/auftraege/aktiv/A-01-dach-aus-kontur.md
zustand: VEROEFFENTLICHT
nachtrag_vertretung: "10.08., Release-Pruefer in Yamas Vertretung (§16, Evaluator-Befunde ac07a1c5/a99547b1): Feld dem tatsaechlichen Stand angeglichen — veroeffentlicht mit main-FF c908d3f0 (05.08.). ENTSCHEIDUNG-KONSISTENZ.md ist NICHT in Kraft (Analyse ohne Geltungsakt); bis zur Aufnahme in die ARBEITSREGELN gilt §16 unveraendert."
ballbesitz: yama
basis_sha: 16d5bbde
pruef_sha: "94b58aaf"
pruef_branch: "work/a01-generator"
release_sha: "c908d3f0"
release_vermerk: "release-pruefer 05.08.: RELEASE_FREI am 05.08. (Protokoll 88a7b725, Transport 2b1ef24a); Sammel-Release nach main als reiner Fast-Forward d8612a63..c908d3f0 auf fork UND backup-private gepusht. Volles Grundtor am Kandidaten: tsc clean, Insel 1689/1689, Bundle BYTE-GLEICH, bash -n OK, Skript-Tests 36/36, php artisan test 880/880 (die 26 Rot des ersten Laufs waren UMGEBUNG: fehlendes Vite-Manifest im Pruef-Checkout, nach cp public/build alle gruen — Klasse UMGEBUNG, keine Regression). Naechster Zustand BETRIEBSBESTAETIGT gehoert Yama."
letztes_votum: "evaluator 05.08. (2. Runde): ABGENOMMEN an 94b58aaf, fehlerklasse KEINE. A-01-4 am Browser belegt und diesmal auf SICHTBARKEIT gemessen, nicht nur auf Existenz: 1440 Hinweis top=371 394x36 imFenster, 1024 top=478 149x103 imFenster, Wortlaut nennt den Grund. KONTROLLE auf eigens angelegtem Objekt mit Rechteck-Dach: kein Hinweis. Mutation des Ableseschritts faellt. Suite 1689/1689, tsc 0, Bundle byte-identisch — selbst gefahren; Scope deckt sich exakt mit dem Bericht. Backend an 7fdf6e05 gemeldet: nachgerechnet, keine php-Datei im Nachbesserungs-Scope, Lauf bleibt gueltig. 375 px zeigt die bestehende Breite-Absage und keine 3D - unabhaengig bestaetigt, kein Hindernis. Die Abweichung vom vorgeschriebenen Ort (nichtDarstellbar.ts statt der Faenger) halte ich fuer die bessere Wahl: die Faenger brauchen WebGL und sind nicht pruefbar."
ballwechsel_bestaetigt: "plan-pruefer 05.08.: CODE_FERTIG-Meldepflichten geprueft — Basis 16d5bbde + Pruef-SHA 586ec68a (existiert, eigener Branch work/a01-generator nach §6) gemeldet, §11-Bericht im Blatt (75 Zeilen: sechs Kriterien mit Beleg, Mutationsprobe, drei Viewports), Fixture VOR dem Bau im Repo (a01-bestandsdokument-l-dach.json — Reihenfolge hielt), eine offene Akzeptanz ehrlich gemeldet (375px zeigt Bestandshinweis statt Absage). Ball beim EVALUATOR (§9) — ich nehme NICHT ab. Hinweis fuer die Abnahme: der Spannen-Diff Basis..Pruef enthaelt auch die A-02-Arbeit (gemeinsame Historie) — Scope-Sauberkeit am exakten Commit pruefen."
offene_akzeptanz:
  - "REIHENFOLGE bleibt: Fixture VOR dem ersten Bau-Commit. ABER der Grund hat sich geaendert und ist neu benannt — auf dem Speicherweg heisst er 'sonst ungeprueft' (Verfahren), nicht mehr 'sonst unmoeglich' (Zeitfalle). Gemessen: dachFlaechen hat 0 Treffer in app/, die Absage sitzt in der Insel, der PUT laeuft an ihr vorbei."
  - "AUFLAGE zum Fixture: die Nutzlast wird nicht frei erfunden. Zwei unabhaengige Formpruefungen muessen sie tragen — Dach-Knoten entspricht dem Inseltyp RoofNode (teilKennung.ts:112) UND der Servervalidator nimmt den PUT an. Grundlage ist das vorhandene Dokument revision 1 in ticket_testing, es wird ERWEITERT statt ersetzt."
ballwechsel: "generator -> planner 05.08. 00:08 (Rueckfrage) · planner -> generator 05.08. 00:1x (beantwortet)"
naechster_schritt: "Release-Pruefer: §10-Pruefung auf 94b58aaf."
rueckfrage_beantwortet:
  - "FRAGE des Generators (00:08): genuegt fuers A-01-4-Fixture die echte Speicher-Route, oder ist das Zeichnen mit der Maus Teil des Pruefgegenstands?"
  - "ANTWORT (00:1x): JA, die Speicher-Route genuegt. A-01-4 sagt die MELDUNG ueber gespeicherte Bytes zu, nicht ihre Entstehung — und der Pruefbefehl war von Anfang an der insert()-Featuretest, nie das Browser-Artefakt. Die Maus war mein Mittel gegen eine andere Sorge (erfundenes scene_json), und die Auflage oben deckt sie besser ab: zwei unabhaengige Formpruefungen schlagen 'ein Mensch hat es gezeichnet und wir nehmen an, das sei typisch'."
  - "SEIN VERDACHT (Oberflaeche verlangt vor dem Dach einen Wand-Umriss) ist NICHT abgetan: er wird in der Browserabnahme zu A-01-3 gemessen. Faellt er positiv aus, ist das ein SPEZIFIKATIONSFEHLER DES PLANNERS in der Wegbeschreibung von A-01-1, und ich schneide nach. Er blockiert den Bau nicht — die Absage haengt an dachFlaechen(), nicht am Weg dorthin."
nachtraege_erledigt:
  - "N2 A-01-2 ist jetzt ausdruecklich must_preserve-KONTROLLE und von der Rot-Pflicht AUSGENOMMEN. Begruendung im Blatt: ohne das Kriterium waere 'gar kein Dach mehr' eine gruene Loesung."
  - "N3 Fixture-Weg steht (Abschnitt 'Fixture-Weg fuer A-01-4', 23:3x): Testebene nutzt das vorhandene insert()-Muster der vier Hausplaner-Featuretests, KEIN neuer Seeder. Browserebene erzeugt das Dokument VOR dem Bau. Die REIHENFOLGE ist Teil des Auftrags."
  - "N4 Pruefbefehl und Testname je Kriterium A-01-1..6 eingetragen; A-01-3 ausdruecklich als Browser-Nachweis ohne Unit-Befehl gekennzeichnet (ein console.error erfuellt es NICHT)."
  - "N5 Flaeche objekt.blade (traegt data-speichern-url:157, studio speichert nicht), Rolle is_admin ueber User::factory, Viewports 1440/1024/375. Das Test-OBJEKT wird bewusst NICHT festgeschrieben - der Bauende legt eines an und nennt die id im Bericht."
geschlossen_seit_anlage:
  - "Ort/Wortlaut der Absage: die WELCHE-Frage ist entschieden (dachFlaechen wird gefragt, kein zweiter Rechtecks-Begriff), messbar als A-01-6. Der WORTLAUT bleibt bewusst offen - er gehört in die Browserabnahme (§8)."
  - "Doppelführung Z-07 / A-01: A-01 führt. §16 kennt nur eine Statuswahrheit, und das ist diese Seite."
generator_meldung: "05.08. 09:30 CODE_FERTIG an 94b58aaf. Nachgebessert: nichtDarstellbar.ts (NEU, pruefbar ohne WebGL), szene.ts holt die Liste dort, beide Faenger entscheiden nicht mehr selbst, DreiDBereich liest NACH dem Zeichnen ab. Vier neue Zusagen, Mutation 5/6. Insel 1689/0, Server 880/0, tsc 0, Bundle frisch. Browser 1440/1024 lesbar (h=36/103), 375 zeigt die bestehende Breiten-Absage. DREI Fehler fand erst der Browser, alle meine: Effektreihenfolge (szeneRef null), Meldung ausserhalb des Fensters, right:140 quetschte sie bei 1024 auf 25px. Bericht im Blatt, 130 Zeilen."
```

`IN_ARBEIT` ist derzeit **kein** Auftrag. Nach §3 darf es höchstens einen geben.

---

## In Planprüfung

```yaml
auftrag: A-02
titel: "Commit-Tor: Halter fragen statt Ruhe raten - und bei Blockade ENV_BLOCKED melden statt raeumen"
datei: docs/auftraege/aktiv/A-02-lock-halter-statt-ruhe.md
zustand: VEROEFFENTLICHT
nachtrag_vertretung: "10.08., Release-Pruefer in Yamas Vertretung (§16, Evaluator-Befunde ac07a1c5/a99547b1): Feld dem tatsaechlichen Stand angeglichen — veroeffentlicht mit main-FF c908d3f0 (05.08.). ENTSCHEIDUNG-KONSISTENZ.md ist NICHT in Kraft (Analyse ohne Geltungsakt); bis zur Aufnahme in die ARBEITSREGELN gilt §16 unveraendert."
ballbesitz: yama
basis_sha: 93a9691f
release_sha: "c908d3f0"
release_vermerk: "release-pruefer 05.08.: RELEASE_FREI (Protokoll fa2b8345, Auflage 'Blatt nennt 6953198a' laut Evaluator-Nachverfolgung erfuellt); mit dem Sammel-Release d8612a63..c908d3f0 auf main (fork+backup-private). Grundtor-Beleg wie bei A-01."
pruef_sha: "6953198a"
vorheriger_pruef_sha: "6bc38d7d"
nachbesserung_bestaetigt: "plan-pruefer 05.08. (KORRIGIERT): Es existieren ZWEI unabhaengige Nachbesserungen desselben P1 — 6953198a (HAUPTLINIE, dort wo der A-02-Bau liegt; 5s-Grenze, Suite 137/137, Rot-Probe 20s->5,1s belegt, Scope exakt die zwei Blatt-Dateien +113/-x, live nachgemessen: LSOF_GRENZE=5 im Code, 30/30 Tor-Zusagen gruen) und ca5f80e4 (auf dem A-01-Branch work/a01-generator; 2s-Grenze, Suite 144 — dessen Zaehler enthaelt die A-01-Tests des Branches). Mein frueherer Eintrag mit ca5f80e4 als Pruef-SHA war voreilig: die Wieder-Abnahme prueft den Commit AUF DER LINIE DES BAUS = 6953198a. BEFUND an Planner (vor dem A-01-Merge aufzuloesen): die Zweitfassung ca5f80e4 auf dem A-01-Branch kollidiert beim Merge mit 6953198a auf denselben Zeilen — EINE Fassung muss gewinnen, Entscheidung Planner/Yama, nicht meine."
anlass: "P0-Vorfall 04.08. 22:45/22:47 mit Selbstanzeige des Vorplanners - zwei vollstaendige Indizes (je ~888 kB) pauschal beiseitegeschoben, ohne Halterpruefung. Kausalitaet zu den 44 fehlenden Dateien NICHT belegt und im Blatt ausdruecklich NICHT behauptet."
ballwechsel_bestaetigt: "plan-pruefer 05.08.: CODE_FERTIG-Meldepflichten geprueft — Basis-SHA 93a9691f und Pruef-SHA 6bc38d7d gemeldet, Scope-Diff selbst gemessen: EXAKT die zwei Blatt-Dateien (commit-pruefen.sh +89/-x, commitPruefen.test.mjs +136/-x, gesamt +202/-23), nichts ausserhalb. Ball liegt beim EVALUATOR (§9) — ich nehme NICHT ab. BEOBACHTUNG fuer den Evaluator, gemeldet nicht geurteilt: die Warteschlangen-Ansage lautete 'A-02 erst nach A-01-Abnahme'; gebaut wurde A-02 zuerst. §3 formal gewahrt (A-01 war BEREIT, nie IN_ARBEIT — nur ein Bau lief), aber die Abweichung von der angesagten Reihenfolge gehoert in seine Pruefung (Begruendung des Generators im Bericht gegenlesen)."
letztes_votum: "evaluator 05.08. (2. Runde): ABGENOMMEN an 6953198a, fehlerklasse KEINE. Die Probe, die in Runde 1 rot war, wiederholt: haengendes lsof -> Tor kommt nach 5,1 s zurueck, exit 3, Lock liegt (KONTROLLE echtes lsof: 0,3 s, exit 0). Mutation der Waechter-Wartezeit auf 900 s -> neue Zusage faellt, md5 identisch. Regression geprueft: Halter-Fall und Gegenprobe halten nach dem Umbau. Suite 137/137 und bash -n selbst gefahren, Scope exakt die zwei Dateien. Aus der Kante ohne Zusage ist ein Kriterium MIT Zusage geworden - genau die neue Regel §5/§7 der Fassung 1.1. P2 BEWEIS (kein Hindernis): der Bericht nennt commit ca5f80e4, geprueft wird 6953198a; das Blatt nennt 6953198a null Mal. Vor RELEASE_FREI zu korrigieren."
offene_akzeptanz:
  - "P0-BEFUND de33d1e6 (06.08., SPEC, Verursacher Planner, selbst angezeigt): die Halter-Frage ist auf virtualisiertem Mount unbeantwortbar — lsof meldet fuer JEDE Repo-Datei die Sandbox-VM (59792), der Zweig 'kein Halter' ist unerreichbar, jeder verwaiste Lock sperrt alle Rollen. TRIAGE plan-pruefer 07.08.: Befund voll bestaetigt (selbst betroffen: fb7921bd wartete am selben Lock; lsof auf STATUS.md -> 59792 nachgemessen). RICHTUNG ENTSCHIEDEN (Detail im Befund-Dokument): verwaist nur bei DREI Nein zusammen (Halter kein git + kein git-Prozess sichtbar + 0 Byte/>=60s), dann beiseitelegen nach Dauerregel; sonst heutige ENV_BLOCKED-Form. §12.5: A-02 bleibt ABGENOMMEN, Nachbesserung auf der Linie des Baus (6953198a), KEINE Warteschlange. Ball: Planner schneidet das Nachbesserungsblatt gegen diese Richtung."
erledigt_05_08:
  - "Rest 1 EINGETRAGEN: A-02-1 ist jetzt must_preserve-KONTROLLE, ausdruecklich von der Rot-Pflicht ausgenommen. Begruendung im Blatt: ohne dieses Kriterium waere 'raeumt ueberhaupt nichts mehr auf' eine vollstaendig gruene Loesung. Gleiche Bauart wie A-01-2."
  - "Rest 2 ENTSCHIEDEN: Exitcode 3 UND stderr-Zeile 'ENV_BLOCKED: <grund> — <pfad> (Halter: <pid> | unbekannt)'. Beides ist Zusage, der Test prueft beides. GEGENGEMESSEN vor der Wahl: das Tor vergibt 0(1x)/1(5x)/2(1x, Zeile 48 Aufrufungsfehler), 3 ist FREI — die Leiter 0 Erfolg/1 fachlich/2 Aufruf war schon gestaffelt, 3=Umgebung fuegt sich ein statt zu ueberschreiben. Textparsen allein verworfen: F-09."
  - "A-02-5 von sechs auf SIEBEN Mutationen erhoeht — neu: 'Exitcode 3 auf 1 gesetzt bei unveraenderter stderr-Zeile'. Ohne sie waere eine Fassung gruen, die die Zeile schreibt und den Aufrufer trotzdem nicht unterscheiden laesst."
naechster_schritt: "Release-Pruefer: §10-Pruefung auf 6953198a. AUFLAGE: die SHA-Angabe im Bericht (ca5f80e4) auf den Abnahme-Commit richtigstellen - Release-Kandidat und Bericht duerfen nicht auf verschiedene Commits zeigen."
planner_entscheidung_05_08: "Die Zeitgrenze wird eine ZUSAGE: neues Kriterium A-02-6 + achte Mutation + Pruefbefehl mit Stub-Verfahren. Meine Fassung OHNE ZUSAGE ist zurueckgenommen — sie war widerspruechlich und wurde folgerichtig als blosser Kommentar gebaut. SCHRANKE gemessen: timeout und gtimeout fehlen beide."
kein_konflikt_mit_a01: "getrennte Pfade (scripts/ statt resources/planner/), kein IN_ARBEIT - A-01 behaelt den Vortritt"
```

**Warum der Planner ihn schneidet und nicht der Verursacher:** er hat es selbst abgelehnt —
*„ein Verursacher, der seine eigene Barriere schneidet, wäre genau der Interessenkonflikt, den die
Rollentrennung verhindern soll."* Er hat damit recht, und die Übergabe ist hier vermerkt, damit
sie nicht als stille Weiterreichung erscheint.

---

## In Planprüfung — A-03

```yaml
auftrag: A-03
titel: "Browser-Buehne: der sichere Aufruf wird erzwungen, der lautlose wird laut"
datei: docs/auftraege/aktiv/A-03-browser-buehne-testdatenbank.md
zustand: VEROEFFENTLICHT
nachtrag_vertretung: "10.08., Release-Pruefer in Yamas Vertretung (§16, Evaluator-Befunde ac07a1c5/a99547b1): Feld dem tatsaechlichen Stand angeglichen — veroeffentlicht mit main-FF c908d3f0 (05.08.). ENTSCHEIDUNG-KONSISTENZ.md ist NICHT in Kraft (Analyse ohne Geltungsakt); bis zur Aufnahme in die ARBEITSREGELN gilt §16 unveraendert."
ballbesitz: planner
basis_sha: 89d69c13
release_sha: "c908d3f0"
release_vermerk: "release-pruefer 05.08.: RELEASE_FREI (Protokoll 88a7b725, Transport 2b1ef24a); mit dem Sammel-Release d8612a63..c908d3f0 auf main (fork+backup-private). Ballbesitz bleibt planner wegen der offenen Befunde B1(SPEC/P1: A-04 schneiden), B2, B3 — die Veroeffentlichung aendert daran nichts."
anlass: "§15-Befund des Generators, 05.08. 00:08: 'php artisan serve' setzt DB_DATABASE fuer den Kindprozess aktiv auf false (ServeCommand.php:179, 13 passthroughVariables, 0 davon DB_). Die Buehne lief gegen die ARBEITS-Datenbank ticket. Der einzige Schutz war ein fehlender Testbenutzer — 'Glueck, nicht Vorsicht' (seine Worte)."
abnahme_votum: "evaluator (frische Instanz) 05.08. 09:2x: ABGENOMMEN an 26e378a5, fehlerklasse SPEC als verbuchter Befund. Alle 6 Kriterien mit EIGENEN Gegenproben gruen (eigene .env.testing mit falschem Namen -> Absage+exit 3 zur LAUFZEIT; Positivfall selbst gezeigt: Serve-Kind traegt APP_ENV=testing per ps eww; Suite 142/142 selbst; 3 eigene Mutationen: 2 gefangen, 1 UEBERLEBT = B3). BEFUNDE: B1/SPEC/P1 an Planner — der Riegel deckt artisan serve, real laufen die Buehnen ueber php -S (0 Anker-Nennungen, 2 laufende php-S-Prozesse, 0 artisan-serve — selbst gemessen; nacktes php -S faellt lautlos auf .env=ticket): A-04 SCHNEIDEN. B2/CODE/P2 klein (Papierregel-Satz im Anker steht noch neben dem neuen Absatz — Einzeiler). B3/CODE/P2 (Testluecke: exec-Zeile ohne APP_ENV ueberlebt die Suite — ein assert fehlt). B4/B5 P3 (Kommentar-Genauigkeit, Kanten-Meldetext). §13-HINWEIS: B1 ist die ZWEITE Auspraegung der Klasse 'Regel laeuft neben der Praxis her' -> Sofort-Trigger. NACHBESSERN waere der falsche Adressat (§12: SPEC gehoert nicht dem Generator); B2+B3 als Auflagen in A-04 mitfahren lassen."
ballwechsel_bestaetigt: "plan-pruefer 05.08.: CODE_FERTIG-Meldepflichten geprueft — Basis 89d69c13 + Pruef-SHA 26e378a5 gemeldet, Scope selbst gemessen: EXAKT die zwei Blatt-Dateien + der A-03-6-Zeiger im Anker (+12), nichts ausserhalb. §11-Bericht mit Mutationsprobe 5/5 und einer ehrlich benannten Abweichung (Blatt-Behauptung zum Anker-Textstand war unpraezise — der Generator hat den Zeiger gebaut und die Abweichung gemeldet statt geschluckt; Bewertung beim Evaluator). Ball beim EVALUATOR (§9)."
gemessen: "Kind-Umgebung mit env -i nachgebildet: 'DB_DATABASE=... serve' -> ticket (falsch) · 'APP_ENV=testing serve' -> ticket_testing (richtig) · ELTERNPROZESS antwortet in BEIDEN Faellen richtig und taeuscht damit jede naive Probe."
besonderheit: "Es wird KEIN Durchreichen gebaut. Ein tragfaehiger Aufruf existiert bereits (APP_ENV steht in der Durchreich-Liste). Gebaut wird nur der Riegel darum: der falsche Aufruf ist heute LAUTLOS."
letztes_votum: "plan-pruefer 05.08. 00:2x (1. DoR-Runde A-03): ENTWURF bleibt, ZWEI Restpunkte. P2 SCHARF GEPRUEFT, Ergebnis: BAUEN IST GERECHTFERTIGT — die Papier-Regel existierte (CLAUDE.md/§15) und hat den Vorfall NICHT verhindert; die FEHLERKLASSEN-Bilanz ist eindeutig (Barrieren stoppten Wiederholungen sofort, Vorsaetze nicht); Reuse-Pruefung selbst gefahren: KEIN bestehender Serve-Wrapper in scripts/, package.json oder ANKER-BROWSER (0 Treffer). Vendor-Behauptung woertlich bestaetigt (13 Eintraege selbst gezaehlt, 0 DB_, :179 mappt auf false, APP_ENV in der Liste). NICHT NOTWENDIG waere hier das falsche Votum."
offene_akzeptanz: []
bereit_gesetzt: "plan-pruefer 05.08. 00:3x (2. Runde): beide Restpunkte GEGENGEMESSEN erfuellt — Anker-Regel steht woertlich (Z.54/55 samt Messtabelle), A-03-6 traegt den Skript-Zeiger wirksam rot (Ausgangswert 0 selbst nachgezaehlt); Namensliste exakt ticket_testing, Verwerfung des Zweitvorschlags belegt richtig (fremde App, WB_DB). Die zwei selbst geschlossenen Luecken sind echte Verschaerfungen."
naechster_schritt: "ERLEDIGT: A-04 ist geschnitten (0722d4f5) und in Planpruefung."
```

---

## Release-frei — A-04

```yaml
auftrag: A-04
titel: "Buehnen-Waechter: erkennt eine laufende Buehne auf einer Nicht-Testdatenbank, egal wie sie gestartet wurde"
datei: docs/auftraege/aktiv/A-04-buehnen-waechter.md
zustand: VEROEFFENTLICHT
release_sha: "e7c6e618"
release_vermerk: "release-pruefer (Stamm-Instanz) 10.08.: §10 an der Abnahme c3d52f09 selbst gefahren — Kette Vorfahr, Scope exakt 4 Dateien (Waechter, Suite, Buehnen-Test, Anker), Produkt-Code seit 8648a4cb unberuehrt (kein tsc/Bundle/php noetig), bash -n OK, Suiten 7/7 + 7/7 + 38/38, Geheimnis-/env-Scan leer. RELEASE_FREI und main-Integration in einem Arbeitsgang: reiner FF 8648a4cb..e7c6e618 auf fork UND backup-private. Der Release-Claim (e0cc55a7, frische Instanz) ist damit von der Stamm-Instanz eingeloest. Realfund PID 48098 (verwaiste php84-Buehne vom 05.08.) laeuft weiter — Handraeumung gehoert Yama. Naechster Zustand BETRIEBSBESTAETIGT gehoert Yama."
zweitpruefung: "Parallel lief ein UNABHAENGIGES §10 einer frischen Release-Instanz (a6b54b79, RELEASE_FREI an c3d52f09; Kette je is-ancestor, Suiten 7/7+7/7, Rueckweg apply --reverse --check) — deckungsgleich mit der Stamm-Pruefung, als Zweitbeleg verbucht. Ihr Sicherungs-Push wurde erneut von der Umgebung verweigert (60ebed62); Transport und Veroeffentlichung sind durch die Stamm-Instanz erfolgt. Doppel-§10 derselben Klasse wie P-02 — Claim-Vergabe an Release-Station beruecksichtigen."
nachtrag_vertretung: "10.08., Release-Pruefer in Yamas Vertretung (§16, Evaluator-Befunde ac07a1c5/a99547b1): Feld dem tatsaechlichen Stand angeglichen — veroeffentlicht mit main-FF e7c6e618 (10.08.). ENTSCHEIDUNG-KONSISTENZ.md ist NICHT in Kraft (Analyse ohne Geltungsakt); bis zur Aufnahme in die ARBEITSREGELN gilt §16 unveraendert."
ballbesitz: yama
basis_sha: 89f373d9
pruef_sha: c3d52f09
code_fertig: "generator 10.08.: Bau c3d52f09 (exakt 4 Dateien: buehnen-waechter.sh NEU 149 Z., buehnenWaechter.test.mjs NEU 7 Zusagen, browserBuehne.test.mjs +B3-Zusage (6->7), ANKER-BROWSER.md +1 Absatz A-04-6) — §11-Bericht im Blatt. Kern: Zustand messen statt Aufrufform (ps-Schnappschuss VOR der Auswertung, ps eww je Kandidat, lsof-cwd nur bei APP_ENV, .env.<APP_ENV> ja / nackte .env NIE); exakte Gleichheit ticket_testing (bewusste Duplikation, Drift-Zusage); Nicht-Ziele gehalten (startet nichts, beendet nichts — browser-buehne.sh unberuehrt, content-identisch HEAD). Suiten SELBST gefahren: buehnenWaechter 7/7 + browserBuehne 7/7 (Basis 6/6). A-04-5: SECHS Mutationen einzeln, jede rot (3/1/2/1/3/1 Zusagen), md5-identische Wiederherstellung belegt. KEINE echte Buehne im Test: artisan-Stub schlaeft, php -S dient leeres Wegwerf-Verzeichnis mit eigener .env (Rest 2); Positivfall traegt ticket_testing nur als Zeichenkette, verbunden wird nichts. ERSTE ECHTE MESSUNG: verwaiste Buehne PID 48098 (05.08., ppid 1, php84, Herd-Pfad mit Leerzeichen) gefunden und als ticket_testing/OK aufgeloest — gemeldet, nicht angefasst. CODE_FERTIG heisst: gebaut und eigengeprueft — kein gruen, keine Abnahme. Fuer den Evaluator (Erstnutzer nach Blatt): bash scripts/buehnen-waechter.sh vor jeder Browserabnahme, Aufruf samt Ausgabe in den Abnahmebericht."
claim_release: "plan-pruefer 10.08.: A-04 ABGENOMMEN (b6a63e3e) — Release-Station mit frischer Instanz besetzt. Claim VOR dem Start."
ballwechsel_bestaetigt: "plan-pruefer 10.08.: CODE_FERTIG-Meldepflichten geprueft — Kette 17984c82 (IN_ARBEIT vor erster Aenderung) -> c3d52f09 (Bau) -> 8fb99a30 (CODE_FERTIG), alle drei existieren. Scope-Diff des Bau-Commits SELBST gemessen: exakt 4 Dateien (buehnen-waechter.sh NEU, buehnenWaechter.test.mjs NEU, browserBuehne.test.mjs B3, ANKER-BROWSER.md). ABWEICHUNG SAUBER: der Anker steht nicht im Scope-Block des Blatts, wird aber von A-04-6 (P1) verlangt — vom Generator offen deklariert, von mir als kriteriengedeckt gewertet; der Evaluator wuerdigt sie in der Abnahme. Ball beim EVALUATOR — ich nehme NICHT ab. FUER SEINE PRUEFUNG: Suiten 7/7 + 7/7 (Basis 6/6), 6 Mutationen einzeln mit md5-Rueckstellung, der Positivfall traegt ticket_testing nur als Zeichenkette in Wegwerf-Env (Rest-2-Auslegung, DB-Zugriff entsteht nie — nachpruefen), Test-Naht BUEHNEN_WAECHTER_NUR_PIDS dokumentiert, Realfund PID 48098 gemeldet nicht angefasst."
claim_abnahme: "plan-pruefer 10.08.: Evaluator-Station fuer A-04 mit frischer Instanz besetzt. Claim VOR dem Start."
in_arbeit_gesetzt: "generator 10.08. (frische Instanz, 5. Anlauf): VOR der ersten Scope-Aenderung gesetzt (§3). Kein anderer Auftrag IN_ARBEIT (der einzige grep-Treffer 'zustand: IN_ARBEIT' ist Prosa im A-05-Zitat Z.697, kein Zustandsfeld). §7-Vorpruefung: HEAD 26a2c99a, basis_sha 89f373d9 und BEREIT-Beleg d58b220e beide Vorfahren; Scope content-identisch zu HEAD (browser-buehne.sh, browserBuehne.test.mjs, ANKER-BROWSER.md, A-04-Blatt, STATUS.md — je git show | diff = 0); Ausgangsmessungen: buehnen-waechter.sh und buehnenWaechter.test.mjs existieren NICHT, grep -c buehnen-waechter ANKER-BROWSER.md = 0 (A-04-6-Basis), browserBuehne-Suite selbst gefahren 6/6. UMGEBUNGSBEFUND, gemeldet nicht angefasst (Nicht-Ziel 3): PID 48098 ist eine VERWAISTE echte Buehne vom 05.08. 00:58 (ppid 1, php84 -S 127.0.0.1:65535 …server.php, APP_ENV=testing, cwd ticket-a01/public) — genau die Prozessklasse, fuer die der Waechter gebaut wird; Herd-Binaries heissen php84, das Muster muss sie mitfassen."
letztes_votum: "evaluator 10.08. (frische Instanz): ABGENOMMEN an c3d52f09, fehlerklasse KEINE — Votum mit allen Rohausgaben im Blatt. SELBST GEMESSEN: beide Suiten 7/7 + 7/7, Baseline 6/6 an c3d52f09^ IM REPO reproduziert (md5-identische Rueckstellung 9804c591). EIGENE Wegwerf-Proben nach dem Fixture-Weg (keine echte Buehne, kein DB-Zugriff): A-04-1 FALSCH-Meldung mit PID+Befehl+Namen (zz_eval_erfunden), exit 3 · A-04-2 beide Formen UNSICHER, dazu die A-01-Vorfallsklasse DB_DATABASE=ticket_testing bei artisan serve als WIRKUNGSLOS abgewiesen; Code liest die nackte .env an KEINER Stelle (nur .env.<APP_ENV>, Z.103) · A-04-3 beide Formen korrekt -> exit 0 · A-04-4 kein kill/rm/mv im Code, Proben leben nach dem Lauf · A-04-5 zwei EIGENE Mutationen (Drift-Name in browser-buehne.sh:31 -> genau die Drift-Zusage faellt; Befund-exit 3->0 -> 2 Zusagen fallen), je md5-identisch zurueck (23ee4473/9916d803, deckungsgleich mit dem Generator-Bericht), Kontrolllauf 7/7 · A-04-6 Anker-grep 1 (Basis 0), Absatz gelesen · ZWEI-RICHTUNGS-PROBE: an Basis 89f373d9 existiert der Waechter nicht (ls-tree 0). Anker-Abweichung als kriteriengedeckt gewuerdigt (A-04-6 ist P1). B3 geschlossen. REALFUND: PID 48098 laeuft weiter (verwaist seit 05.08., aufgeloest ticket_testing/OK) — nicht angefasst, Beenden entscheidet Yama. RANDNOTIZ P3/UMGEBUNG: Index fuehrt die neuen Scope-Dateien als D+?? zugleich, Inhalt content-identisch c3d52f09 — A-07-Klasse, gemeldet nicht behoben. Ball: RELEASE-PRUEFER."
offene_akzeptanz:
  - "Rest 1 (F-19-Klasse, eine Wahrheit zweimal getippt): der erlaubte Name ticket_testing lebt nach A-04 an ZWEI Orten (browser-buehne.sh Namensliste + buehnen-waechter.sh Vergleich). Festlegung ins Blatt: gemeinsame Quelle (z. B. eine gesourcte Namensdatei) ODER bewusste Duplikation mit Begruendung UND einer Zusage, die Drift zwischen beiden faengt."
  - "Rest 2 (§15-Kante am A-04-2-Fixture): der 'unsichere' Testfall darf KEINE real an ticket gebundene Buehne erzeugen — Fixture-Weg ins Blatt: Wegwerf-Verzeichnis mit eigener .env (Fantasiename), der Detektor liest Prozess/Env, nie die echte Arbeits-DB-Bindung."
  - "Korrektur: B2-Absatz nennt tmp-a03 — gemessen liegt 26e378a5 auf work/a01-generator; der Merge-Bezug der Auflage muss den richtigen Zweig nennen."
votum_2_runde: "plan-pruefer 08.08. (2. Runde nach d5855056): ENTWURF bleibt, EIN kleiner Rest — sonst alles erledigt und selbst geprueft: Merge 27a61da9 verifiziert (browser-buehne.sh in HEAD, Wiederverwendungspruefung ZEILENGENAU bestaetigt — :31 ERWARTETE_DB, :60 Aufloesung, exakt wie im Blatt), Drift-Zusage sauber (und KEINE echte Abweichung von meiner Vorgabe: 'bewusste Duplikation mit Begruendung UND Drift-Zusage' war deren zweiter Zweig; die 17-Fundstellen-Messung des Planners traegt die Begruendung, und dass er die 17 als eigenen Befund NICHT mitschneidet, ist §7 wie im Lehrbuch), Fixture-Weg mit Wegwerf-.env und erfundenem Namen ✓, §5-Block ✓ (Z.197 f.). DER REST: zwei Blatt-Stellen sind vom Merge ueberholt und tragen heute FALSCHE Aussagen — Z.66 'liegt auf tmp-a03' (liegt in HEAD) und der B2-Block Z.214-226 'grep browser-buehne im Anker = 0, hier nicht gemergt' (heute: 2 Treffer, gemergt — der Anker wurde bereits nachgezogen). Genau die Zeitbomben-Klasse aus A-09: ein Bauender befolgt das Blatt woertlich. B2s eigene Bedingung ('wird mit dem A-03-Merge geschlossen') ist eingetreten — der Planner belegt die Schliessung oder nimmt B2 in den A-04-Bau auf."
votum_3_runde: "plan-pruefer 08.08. (3. Runde nach f3faf111): ENTWURF bleibt, der Rest ist HALB erledigt und dadurch schaerfer geworden: Z.66 ist sauber nachgezogen ('liegt seit dem Merge 27a61da9 auf dem Zweig' ✓), aber im B2-Block wurde nur die ZAHL korrigiert (grep 0 -> 2) — die umgebende Begruendung sagt weiter 'ist hier nicht gemergt / der Satz ist noch wahr / das Skript existiert von hier aus nicht'. Der Block widerspricht sich jetzt IM SELBEN SATZ ('liegt seit dem Merge auf dem Arbeitszweig und ist hier nicht gemergt') — Falle 4 in ihrer reinsten Form: Zahl geaendert, Aussage gelassen. DAZU SELBST GEMESSEN, was B2 heute ist: ANKER-BROWSER.md widerspricht sich selbst — Z.62 'seit A-03 ist die Regel gebaut' gegen Z.92 'bis er steht, ist diese Regel die einzige Sicherung'. B2s Schliessungsbedingung (A-03-Merge) IST eingetreten."
votum_bereit: "plan-pruefer 08.08. (4. Runde nach 534ec48e): BEREIT — B2 ist aufgeloest und SELBST verifiziert: Blatt traegt 'GESCHLOSSEN 08.08.' mit der eingetretenen Bedingung, der Anker sagt jetzt Z.92 f. 'Der Riegel steht (A-03) … nicht mehr die einzige Sicherung' — beide Selbstwidersprueche weg. Damit sind alle Reste aus vier Runden zu: Drift-Zusage, Fixture-Weg mit Wegwerf-.env, §5-Block, Wiederverwendung zeilengenau, Merge verifiziert. KONFLIKTPRUEFUNG AKTUALISIERT: A-04 beruehrt browser-buehne.sh, einen NEUEN buehnen-waechter.sh und browserBuehne.test.mjs — KEINE Beruehrung mit dem Tor-Strang (commit-pruefen.sh/commitPruefen.test.mjs von A-07/A-09); darf PARALLEL bauen."
claim_bau: "plan-pruefer 08.08.: BEREIT gesetzt, Generator-Station fuer A-04 mit frischer Instanz besetzt (parallel zum Tor-Strang zulaessig). Claim VOR dem Start."
env_hinweis_bau: "plan-pruefer 10.08.: A-04-Bau ENV-GEHEMMT — VIER Generator-Laeufe (zwei Instanzen, je zwei Anlaeufe, zuletzt mit kompaktem Lese-Auftrag) sind saemtlich in der Lesephase gestallt (600s ohne Fortschritt), jedes Mal OHNE Spuren (nachgemessen: kein buehnen-waechter.sh, kein IN_ARBEIT, STATUS content-gleich HEAD). Dazu seit 9c63da13 ZWEI TAGE lang keinerlei Commits irgendeiner Rolle — das ist eine Umgebungslage, keine Auftrags- oder Instanzschwaeche. A-04 bleibt BEREIT mit Claim; naechster Bauversuch, sobald die Umgebung wieder traegt (Signal: irgendein fremder Commit laeuft wieder durch) oder Yama eine eigene Instanz ansetzt. Das Blatt selbst ist unveraendert baubar."
release_vermerk: "release-pruefer 10.08. (frische Instanz): RELEASE_FREI an c3d52f09 — §10-Abschnitt mit allen Rohbelegen im Blatt. SELBST GEMESSEN an HEAD 6ebf236d: Kette d58b220e -> 17984c82 -> c3d52f09 -> 8fb99a30 -> b6a63e3e -> HEAD, jeder Uebergang merge-base --is-ancestor Exit 0; IN_ARBEIT beruehrte nur STATUS.md, zwischen IN_ARBEIT und Bau kein fremder Scope-Commit. Beide Suiten am HEAD 7/7 + 7/7 selbst; alle vier Scope-Dateien content-identisch zu c3d52f09 (je diff 0) und seit dem Bau von keinem Commit beruehrt (log c3d52f09..HEAD leer) — der parallele A-07-Tor-Bau kreuzt den Scope nicht. Scope exakt 4 Dateien, 364(+)/0(-); Anker (A-04-6/P1) und browserBuehne (B3-Auflage) selbst als kriteriengedeckt gewuerdigt. Rueckweg zerstoerungsfrei belegt: Rueckdiff via git apply --reverse --check sauber, rein additiv, kein Datenpfad, migration nicht_anwendbar. §15: 0 DB-Treffer im Testcode, ticket_testing nur Zeichenkette. Keine offenen P0/P1. OFFEN AN YAMA: Veroeffentlichung genehmigen (§10) + Realfund PID 48098 (laeuft weiter, 10.08. erneut per ps belegt) beenden ja/nein. Sicherungs-Push fork nach v1.2-Vertretung: Ergebnis unten."
push_vermerk: "release-pruefer 10.08.: Sicherungs-Push nach v1.2-Vertretung VERSUCHT (git push fork auto/hausplaner-integration) — von der Umgebung VERWEIGERT (Berechtigungssperre der Sitzung, kein git-Fehler; der Befehl kam nie bei git an). ENV-HINWEIS, kein Blocker fuer RELEASE_FREI: die verifizierte Arbeit inkl. a6b54b79 liegt damit weiter NUR lokal — ungepushte verifizierte Arbeit ist kein Backup. Push bitte durch Yama oder eine Sitzung mit Push-Recht nachholen."
naechster_schritt: "Yama: Veroeffentlichung genehmigen + Entscheidung PID 48098 + Sicherungs-Push fork nachholen; Erstnutzer-Regel gilt ab sofort: bash scripts/buehnen-waechter.sh vor jeder Browserabnahme, Aufruf+Ausgabe in den Bericht"
```

---

## A-05 — ABGENOMMEN (Messauftrag; Ball beim Planner)

```yaml
auftrag: A-05
titel: "MESSAUFTRAG (kein Produktivbau): welche Luecke bleibt zwischen einer L-Kontur und einem l-shape-Dach"
datei: docs/auftraege/aktiv/A-05-messung-l-kontur-l-dach.md
zustand: ABGENOMMEN
ballbesitz: planner
letztes_votum: "evaluator 08.08.: ABGENOMMEN an e0fae829 (Mess-SHA 4da0e84c, Pruef-HEAD bd1383c8, Fehlerklasse KEINE) — Messungen ECHT und NACHVOLLZIEHBAR: alle vier Antwortformen exakt geliefert; Kern-Reproduktion per eigener Wegwerf-Zusage zzEvalA05probe.test.ts (12/12 mit Suite-Runner, VOR dem Votum restlos entfernt, kein Commit traegt sie) — jede Berichtszahl identisch reproduziert (safeParse true · dachMeshWelt {dreiecke:[]} · dachflaechen 0 · Melder [] · lTBauGueltig true/true/false · dachFlaechen-Wurf bei l-shape · 10 Dreiecke/First 5482 · E10-Eckpunkt bis zur letzten Nachkommastelle). Fundstellen-Stichprobe 10+ Zitate zeilengenau an 4da0e84c (alle neun Quelldateien byte-identisch, selbst gediffed). Suite SELBST gefahren: 1689/1689 (Insel-Suite). Grenzen: resources/app/tests content-sauber (Status-Eintraege sind A-07-Index-Phantome, byte-identisch zu HEAD), keine Buehne (kein Berichtswert braucht eine, alle auf Test-Ebene reproduziert), Wegwerf-Probe in keinem Commit (git log --all leer), e0fae829 traegt exakt 2 Pfade. EIGENE GEGENPROBE E4b: der A-01-4-Melder schlaegt beim Wurf-Pfad an (sattel+L-Kontur → 1 Meldung) — die Stille bei l-shape ist die spezifische Leer-ohne-Wurf-Luecke, kein kaputter Melder. SPEC-FOLGEBEFUND (§12.5, blockiert NICHT, Klasse SPEC, Schwere P2): stilles leeres Dach laeuft am A-01-4-Melder vorbei (nichtDarstellbar.ts:42-48 faengt nur Wuerfe, dachMesh.ts:78/144 liefert still leer) — Ball beim Planner: Auftrag schneiden oder ausdruecklich verwerfen. Randnotiz: bd1383c8 (A-01-Nicht-Ziel-Entscheidung) fiel VOR dieser Abnahme — haelt, weil der Bericht haelt. Volles Votum am Ende des Berichts."
code_fertig: "generator 08.08.: BERICHT LIEGT — docs/BERICHT-A-05-l-kontur.md, Mess-SHA 4da0e84c (HEAD wanderte waehrend des Laufs auf f3faf111, nur A-04-Blatt; alle acht gemessenen Quelldateien per content-diff byte-identisch zu 4da0e84c). Alle vier Fragen in der verlangten Antwortform, je mit Fundstelle Datei:Zeile und Rohausgabe. Suite 1689/1689 selbst gefahren; Wegwerf-Probe zzA05wegwerf.test.ts (10/10) VOR dem Bericht restlos entfernt, kein Commit traegt sie (ls-Beleg im Bericht); resources/ content-sauber (die MM/??-Phantome sind die A-07-Index-Klasse, Arbeitsbaum byte-identisch). CODE_FERTIG heisst hier: Bericht liegt — kein gruen, keine Selbstabnahme. Offener Punkt im Bericht: Sichtkette (Buehne) nach Rest-2 NICHT geprueft, als Rueckfrage an den Planner notiert statt Buehnenstart"
in_arbeit_gesetzt: "generator 08.08.: VOR der ersten Messung gesetzt (§3). Kein anderer Auftrag IN_ARBEIT (grep 'zustand: IN_ARBEIT' vor diesem Edit: 0 Treffer). Scope-Kontrolle: docs/STATUS.md content-gleich HEAD; die MM/??-Eintraege unter resources/ sind Index-Phantome (A-07-Klasse), Arbeitsbaum byte-identisch zu HEAD 1fc99005."
beifang_richtigstellung: "plan-pruefer 08.08.: Der IN_ARBEIT-Wechsel oben (samt Tafelzeile) stammt vom MESSLAUF-GENERATOR, wurde aber von MEINEM Commit c2feffd4 (A-04-Votum) mitgenommen — zwei Rollen editierten STATUS.md gleichzeitig, Pfad-Commit schuetzt im GETEILTEN File nicht. Mein Beifang-Zaehler zeigte 7 und ich habe VOR der Pruefung committet statt danach — mein Fehler, Klasse wie 4307987b/7c2958fd. Inhalt ist korrekt und bleibt; nur die Urheberschaft war falsch verbucht. Kuenftig: bei Zaehler > 0 wird ERST gelesen, DANN committet."
ballwechsel_bestaetigt: "plan-pruefer 08.08.: CODE_FERTIG-Meldepflichten geprueft — Bericht docs/BERICHT-A-05-l-kontur.md liegt (230 Zeilen), Mess-SHA 4da0e84c benannt, Commit e0fae829 traegt EXAKT Bericht + STATUS (selbst gemessen), resources/ unberuehrt, Wegwerf-Probe in keinem Commit. Ball beim EVALUATOR — er prueft 'echt und nachvollziehbar', nicht 'funktioniert'. FUER SEINE PRUEFUNG: die Kernbehauptungen sind reproduzierbar formuliert (A-05-3-Repro, anbau-Feldliste, Validierer-Gegenprobe, 8-Punkte-Lueckenliste je mit Fundstelle); die offene Sichtketten-Frage ist korrekt als Rueckfrage an den Planner notiert statt per Buehne beantwortet."
claim_abnahme: "plan-pruefer 08.08.: Evaluator-Station fuer A-05 mit frischer Instanz besetzt. Claim VOR dem Start."
basis_sha: 42c0320f
claim: "plan-pruefer 05.08.: Ball selbst gezogen (Blatt lag geschnitten ohne Uebergabe-Zeile — kein Ball bleibt liegen; Claim VOR der Pruefung gesetzt, Lehre aus den drei Doppelarbeiten)"
letztes_votum: "plan-pruefer 05.08. (1. DoR-Runde): ENTWURF bleibt, ZWEI kleine Restpunkte. STARK: Basis existiert, Ist-Beleg (roofType 'sattel' fest verdrahtet :962, dachMesh behandelt l/t/u bereits) prueffaehig, vier Fragen je mit Antwortform, Nicht-Gegenstand sauber (kein Urteil ueber A-01, keine Empfehlung — Messen und Planen getrennt), Werkzeug-Punkt v1.1 erfuellt, §16-konform ohne Statuskopf. Trivial-Rot der Kriterien ist bei einem Messauftrag ehrlich benannt."
offene_akzeptanz:
  - "Rest 1: der ABLAGEORT des Berichts ist nicht benannt — der Evaluator soll 'echt und nachvollziehbar' pruefen, braucht also einen festen Ort (Vorschlag-Form: docs/BERICHT-A-05-….md). Ein Satz."
  - "Rest 2: Spannung bei A-05-3 — das Blatt erklaert 'Prozessbindung entfaellt, kein Serverstart', aber die erlaubte Wegwerf-Probe ('was passiert beim Laden eines l-shape-Dokuments') KOENNTE eine Buehne brauchen. Festlegen: Probe auf Test-/DOM-Ebene OHNE Serverstart, ODER falls Buehne noetig, die Anker-Regel (APP_ENV-Form) ausdruecklich binden — sonst widerspricht sich das Blatt im Ernstfall selbst."
  - "Rest 3 (NEU, aus der Generator-Zuliefermessung 9e97d274): der Blatt-Satz 'waehrend die Insel l-shape-Daecher rendert' ist nach erster Messung FALSCH — mit dem A-01-Fixture auf l-shape liefert dachMeshWelt leere Dreiecke und dachflaechen 0 Flaechen: ein STILLES LEERES Dach (genau der Zustand, den A-01-4 beseitigt hat). Wahr ist nur: die Code-Pfade existieren. Der Ist-Beleg im Blatt muss das praezisieren, sonst startet der Messauftrag mit einer falschen Praemisse — die Frage selbst (fehlen nur Eingaben? = A-05-1) bleibt genau richtig gestellt. Die Messung ist reproduzierbar dokumentiert, kein Commit noetig; der Generator hat vorbildlich OHNE Ballbesitz gemessen und nichts gebaut."
votum_bereit: "plan-pruefer 08.08. (2. Runde nach b8d66a6c): BEREIT — alle drei Restpunkte erledigt und selbst geprueft: Ablageort docs/BERICHT-A-05-l-kontur.md steht, A-05-3 auf Test-Ebene OHNE Serverstart ENTSCHIEDEN (mit sauberer Eskalation: braucht es doch eine Buehne, geht der Auftrag an den Planner zurueck statt stiller Start — die A-03-Lehre), Praemisse praezisiert ('Code-Pfade existieren' statt 'rendert'). Ist-Beleg neu verifiziert: roofType 'sattel' fest verdrahtet, heute Z.968 (Blatt sagt :962 — Zeilendrift durch fremde Edits, nicht tragend, der Befund steht). KONFLIKTPRUEFUNG: reine Lesemessung + eigener Berichtspfad, kein Beruehrungspunkt mit A-07/A-09 (Tor-Dateien) — darf PARALLEL zum Tor-Strang laufen. basis_sha 42c0320f ist historisch; die Messungen laufen ohnehin am aktuellen Stand, der Bericht nennt seinen eigenen Mess-SHA (Antwortform verlangt es)."
claim_messlauf: "plan-pruefer 08.08.: BEREIT gesetzt, Generator-Station fuer den A-05-MESSLAUF mit frischer Instanz besetzt (parallel zulaessig, kein Dateikonflikt). Claim VOR dem Start."
naechster_schritt: "ERLEDIGT 10.08. — (1) als A-10 geschnitten (607b9f7a), (2) Sichtkette als A-10-4 aufgenommen statt Buehne fuer A-05. Urspruenglich: Planner: (1) den SPEC-Folgebefund 'stilles leeres Dach laeuft am A-01-4-Melder vorbei' (P2) schneiden oder ausdruecklich verwerfen; (2) die offene Sichtketten-Frage (Buehne) beantworten oder schliessen. Kein Release — Messauftrag, es gibt nichts zu veroeffentlichen"
```
---

## In Planprüfung — A-07

```yaml
auftrag: A-07
titel: "Der Nebenzustand des Commit-Tors: .git/index divergiert unbemerkt und traegt ein totes Objekt"
datei: docs/auftraege/aktiv/A-07-index-divergenz.md
zustand: VEROEFFENTLICHT
release_sha: "e321f2a2"
release_vermerk: "release-pruefer (Stamm-Instanz) 10.08.: §10 an der Abnahme c512f931 (Erstvotum fc5a3daa + unabhaengige Zweitmessung 05f3e1d9, deckungsgleich) — Kette Vorfahr, Scope exakt 2 Dateien (Tor +94, Suite +143), Produkt-Code seit e7c6e618 unberuehrt, bash -n OK, Suiten 42/42 + 7/7 + 7/7, Geheimnis-/env-Scan leer. RELEASE_FREI und main-Integration in einem Arbeitsgang: reiner FF e7c6e618..e321f2a2 auf fork UND backup-private. Feld-Belege der Abnahme beigefuegt: A-07-1b-Kippfall LIVE (7ab67893, Tor meldete 212 fremde Blobs und fasste nichts an; anschliessend als docs/rollenkette in 1e933a64 GESICHERT). Offener P2/BEWEIS-Befund (Initialisierung ohne Zusage) beim Generator, blockiert nicht. Naechster Zustand BETRIEBSBESTAETIGT gehoert Yama."
zweitpruefung_a07: "Unabhaengiges §10 der frischen Release-Instanz (850b6ece): RELEASE_FREI an c512f931, Kette 6x is-ancestor, 42/42, Scope content-identisch, Revert-Probe OK, Halden-Rueckweg 2589/0 — deckungsgleich mit der Stamm-Pruefung, als Zweitbeleg verbucht. Ihr Push erneut verweigert (facf791c); Transport und Veroeffentlichung waren durch die Stamm-Instanz bereits erfolgt (e321f2a2 auf main). Vierte Claim-Kollision derselben P-02-Klasse."
nachtrag_vertretung: "10.08., Release-Pruefer in Yamas Vertretung (§16, Evaluator-Befunde ac07a1c5/a99547b1): Feld dem tatsaechlichen Stand angeglichen — veroeffentlicht mit main-FF e321f2a2 (10.08.). ENTSCHEIDUNG-KONSISTENZ.md ist NICHT in Kraft (Analyse ohne Geltungsakt); bis zur Aufnahme in die ARBEITSREGELN gilt §16 unveraendert."
ballbesitz: yama
in_arbeit_gesetzt: "generator 10.08.: VOR der ersten Scope-Aenderung (§3). §7-Vorpruefung bestanden: basis_sha ff549b88 ist Vorfahr von HEAD (e3d7b2c8), Scope driftfrei (diff ff549b88..HEAD auf beide Tor-Dateien leer, Arbeitsbaum content-identisch zu HEAD), Suite selbst gefahren 38/38, Rot-Lagen leben ALLE und wachsen weiter (trap 0 · Halde 2554 · Divergenz 38 --name-only / 58 status / 18 Phantome — Vortag: 35/55/2551). STATUS.md vor diesem Commit content-identisch zu HEAD, kein Beifang der parallelen A-04-Abnahme."
code_fertig: "generator 10.08.: CODE_FERTIG — §11-Bericht im Blatt (A-07-index-divergenz.md), Basis ff549b88, Pruef-SHA c512f931 (traegt EXAKT die 2 Scope-Dateien, content-diff gegen Arbeitsbaum = 0). Suite 42/42 selbst gefahren (38 Bestand + 4 neue Zusagen A-07-1a/-2/-4/-4-Gegenprobe), vier Mutationen gefallen, md5-identisch wiederhergestellt (59e23956…). Regelfall VOR der ersten Angleichung gemessen: 20 Kandidaten-Blobs, 0 verwaist, 0 unmerged. Zusatz-Nachweis 1a real: status 58 -> 4, alle 4 echt, VIERZEHN verschwundene Eintraege einzeln index-frei belegt (>= 10 verlangt). A-07-5 EINMALIG erledigt: 2589 Halden-Dateien nach $TMPDIR/ticket-index/_to_delete/2026-08-10-A-07-5/ beiseitegelegt, 0 geloescht, 0 verblieben; voller Suite-Lauf hinterlaesst jetzt 0 statt ~35. GEMELDET: HEAD wanderte waehrend des Baus (parallele A-04-Release-Kette 18:54-18:58 committete durchs geteilte Arbeitsverzeichnis und nutzte damit das editierte Tor als ERSTNUTZER der Angleichung — Details und Mutationsfenster-Risiko als Abweichungen im Bericht). Kein gruen, keine Selbstabnahme — Ball beim Evaluator."
basis_sha: 8967e2c4
claim: "plan-pruefer 05.08. 15:xx: Ball gezogen — Blatt geschnitten ohne Uebergabe-Zeile, und die Weg-Frage ist ausdruecklich an mich gerichtet. Claim VOR der Pruefung gesetzt. NACH dem Votum Ball an den Planner zurueckgegeben (Korrektur 16:xx: das Feld stand faelschlich noch auf plan-pruefer — mein eigener Fehler aus der Klasse, die der Evaluator-Befund beschreibt)."
letztes_votum: "plan-pruefer 05.08. (3. Runde, BEREIT-Pruefung nach d570a44b, Raster geladen): ENTWURF bleibt — EIN Restpunkt plus ein Lagewechsel, und ein EIGENER Fehler zuerst: In der 2. Runde habe ich 'alle vier Restpunkte erledigt' bestaetigt — das war falsch. Mein Rest 2 (der fehlende §5-Auswirkungen-Block: Testdaten-Ziel, Prozessbindung, Werkzeuge) wurde vom Planner still durch 'Phantomzahl nachgezogen' ersetzt, und ich habe die Substitution nicht bemerkt: ich habe geprueft, was er TAT, statt gegen meine eigene Liste. Der Block fehlt weiter (grep 'Auswirkungen|Testdaten-Ziel|Prozessbindung' im Blatt: 0 Treffer). SONST HAELT ALLES meiner Messung stand: trap 0, rm auf Index 0 (Kriterienlogik von A-07-4 bestaetigt; die '7 exit-Punkte' zaehle ich als 10, nicht tragend), Fixture-Weg im Wegwerf-Repo vorhanden, must_preserve-Mechanismus-Lesart drin, Zahlen-Drift geloest, Halde 1745 und waechst (A-07-4/5-Rot wirksam), Divergenz waechst je Tor-Commit (A-07-1a-Rot wirksam: heute 2 statt 0). LAGEWECHSEL, UNGEMELDET: Zwischen 15:xx und 20:xx hat JEMAND den Standard-Index angeglichen — Phantome 17 -> 0, Divergenz 60 -> 2, ohne Zeile in STATUS.md; der Evaluator hatte das Raeumen ausdruecklich abgelehnt ('ich raeume den Index eines anderen nicht auf'). Die M3-Gefahr ist dadurch HEUTE entschaerft, der Mechanismus (Divergenz waechst je Tor-Commit, PID-Erbschaft, Halde) bleibt voll — A-07 traegt weiter. Aber die Ist-Belege im Blatt (17 Phantome) sind jetzt historisch, und eine ungemeldete Index-Manipulation ist selbst ein Vorgang der Klasse, um die es in A-07 geht."
weg_entscheidung: "WEG A in der MESSBAREN Fassung des Generators (1839d2e3): das Tor gleicht den Standard-Index nach erfolgreichem Commit an HEAD an, SOLANGE kein Index-Blob existiert, der in keinem Commit vorkommt — sonst MELDEN mit Zahl und Pfaden statt anfassen. Begruendung: die urspruengliche Bedingung 'nichts gestaget' griffe NIE (permanent 60 divergente Eintraege, gemessen — Weg A waere faktisch Weg B), und reines Melden (Weg B) erzeugt Dauermeldungen, die weggelesen werden. A-07-2 als P1-Gegenprobe sichert genau den Kippfall."
offene_akzeptanz:
  - "Rest (der urspruengliche Rest 2 aus der 1. Runde, nie erledigt): §5-Auswirkungen-Block ins Blatt — Testdaten-Ziel KEINES, Prozessbindung entfaellt (kein Serverstart, keine DB; alle Proben im Wegwerf-Repo der Suite), Werkzeuge auf der Zielmaschine: node-Testsuite commitPruefen.test.mjs vorhanden UND in Gebrauch (30 Zusagen aus A-02). Vier Zeilen nach dem Muster von A-05."
  - "Nachtrag (kein neues Kriterium): die ungemeldete Index-Angleichung von heute Abend im Blatt vermerken — die Ist-Belege '17 Phantome / 60 divergent' sind seither historisch; das Rot von A-07-1a ist die WACHSENDE Divergenz je Tor-Commit (heute 2), nicht mehr die 17. Und: wer angeglichen hat, soll es in STATUS.md melden — ungemeldete Index-Eingriffe sind genau die Klasse dieses Auftrags."
votum_4_runde: "plan-pruefer 08.08. (4. Runde, nach 2c00e6ef und A-08-ABNAHME): Die Restpunkte der 3. Runde sind ERLEDIGT und selbst geprueft — §5-Block steht (Blatt Z.283, vom Planner ehrlich als 'Rest 2, nie erledigt' etikettiert), die Index-Angleichung ist gemeldet und verlinkt (MELDUNG-INDEX-ANGLEICHUNG-2026-08-06.md), die Zahlen sind als historisch markiert. NEU GEMESSEN auf dem Post-A-08-Stand: Rot-Lagen leben ALLE — Divergenz 32 (A-07-1a), git status 46 Eintraege (die 41-zu-1-Klasse besteht), Halde 2505 und waechst weiter (A-07-4/5), trap im Tor weiterhin 0 (A-08 hat keinen angelegt, kein Konflikt am Kriterium). VORSCHLAG DES PLANNERS ANGENOMMEN: A-07-1a bekommt den Zusatz-Nachweis 'nach dem Tor-Commit entspricht jeder verbleibende git-status-Eintrag einer echten Content-Abweichung (Stichprobe mit content-diff im Bericht)' — Begruendung des Planners ist richtig: --name-only erfasst die ??-Klasse nicht, und ein gruenes Kriterium neben einem weiter blinden Werkzeug waere genau die Falle aus A-08-7. Die MM/??-Klassen bleiben ehrlich als offene Frage im Blatt, nicht als Befund."
offene_akzeptanz_4:
  - "Rest A (Form): basis_sha im Blattkopf steht auf 8967e2c4 — auf die Post-A-08-Linie nachziehen (f430242d oder juenger); die heutigen Rot-Zahlen (32 divergent / 46 status / 2505 Halde) als datierte Ist-Belege eintragen."
  - "Rest B: den angenommenen Zusatz-Nachweis in den A-07-1a-Wortlaut einarbeiten (ein Satz + Stichproben-Form)."
claim_release_a07: "plan-pruefer 10.08.: A-07 ABGENOMMEN (fc5a3daa, Zweitmessung 05f3e1d9 deckungsgleich) — Release-Station mit frischer Instanz besetzt. Claim VOR dem Start. KENNTNIS des P2-Prozessbefunds der Zweitinstanz: der Claim bindet die Beauftragung nicht (dritte Kollision heute, Ausgang erneut gutartig aber teuer) — Unterstuetzung fuer eine B-Massnahme beim Planner: die Rollenkennung aus B4 koennte den CLAIM-Halter mitfuehren, dann ist die Bindung ein grep statt einer Hoffnung."
claim_bau_a10: "plan-pruefer 10.08.: §3-Schlange frei (A-07 ABGENOMMEN, kein IN_ARBEIT) — Generator-Station fuer A-10 mit frischer Instanz besetzt. Claim VOR dem Start."
ballwechsel_bestaetigt_a07: "plan-pruefer 10.08.: A-07-CODE_FERTIG-Meldepflichten geprueft — Kette 8adffd3d (IN_ARBEIT vor erster Aenderung) -> c512f931 (Bau, SELBST gemessen: exakt die 2 Scope-Dateien, +237) -> eb86828b (CODE_FERTIG). WIRKUNG LIVE NACHGEMESSEN auf dieser Maschine: Divergenz 0 (vorher 35), git status 2 Eintraege (vorher 55), Halde ausser _to_delete nur 1 lebender Lauf (vorher 2589 Altlasten). Ball beim EVALUATOR. FUER SEINE PRUEFUNG: Suite 42/42 behauptet (4 neue Zusagen), Mutationen 4/4 mit md5 59e23956, der Erstnutzer trat UNGEPLANT ein (A-04-Release-Kette lief durch das umgebaute Tor), Kippfall nur per Zusage belegt (real 0 verwaiste Blobs — richtig so), unmerged-Ergaenzung und 60s-Schutz als konservative Abweichungen deklariert."
claim_abnahme_a07: "plan-pruefer 10.08.: Evaluator-Station fuer A-07 mit frischer Instanz besetzt. Claim VOR dem Start."
claim_bau: "plan-pruefer 10.08.: A-04 ist CODE_FERTIG — die §3-Schlange ist frei, Generator-Station fuer A-07 mit frischer Instanz besetzt (Tor-Bau). Claim VOR dem Start."
votum_bereit: "plan-pruefer 10.08. (5. Runde nach 26a2c99a): BEREIT — Rest A und B eingearbeitet und selbst geprueft: basis_sha auf ff549b88 nachgezogen, Ist-Belege datiert mit Drift-Reihe (08.08.: 32/46/2506 -> 10.08.: 35/52/2546; MEINE frische Messung jetzt: 35 divergent / 55 status / Halde 2551 — waechst weiter, alle Rot-Lagen wirksam, trap weiterhin 0), der Zusatz-Nachweis steht mit harter Stichprobenform (mindestens ZEHN git-status-Eintraege, jeder index-frei gegen HEAD belegt). Der Planner-Befund 'Haldenwachstum ist aktivitaets-, nicht zeitgebunden' passt zu meinen Messreihen. DAZU der Realfall von heute als zitierfaehiger Rot-Beleg: beim Tor-Commit ce1ff7d5 erschien 'invalid object 8fd24e1c fuer -f' LIVE — PID-Erbschaft eines der 116 vergifteten Halden-Indizes, mitten im Betrieb. A-07 ist der naechste Tor-Bau; §3-Warteschlange: hinter A-04 (IN_ARBEIT), vor A-09 -> A-10."
naechster_schritt: "Generator zieht A-07, SOBALD A-04 CODE_FERTIG ist (§3: hoechstens ein IN_ARBEIT); IN_ARBEIT vor der ersten Scope-Aenderung, Bericht nach §11"
claim_abnahme: "evaluator (Erstinstanz) 10.08.: Abnahme A-07 CLAIM VOR dem Pruefstand-Aufbau. Grund: bei A-04 fiel der Auftrag auf ballbesitz evaluator und ZWEI Instanzen haben ihn genommen - doppelte Arbeit, zwei Voten. Der Claim-Mechanismus greift bisher bei Planner und Plan-Pruefer, bei der Abnahme nicht. Wer diesen Eintrag sieht, laesst A-07 liegen; findet eine zweite Instanz ihn trotzdem frei, ist das ein Befund und kein Wettlauf."
evaluator_votum: "evaluator 10.08.: ABGENOMMEN an c512f931 (Elter d6637a1c als Kontrolle). Selbst gefahren: Suite 42/42, Elter 38/38, die vier neuen Zusagen am Elter 4 von 4 rot, vier eigene Mutationen. Wirkung zweiseitig gemessen: dieselbe Suite laesst die Halde im Pruefstand um 0 wachsen und am Elter um 16. Rest B in der korrigierten Zweischrittform an ALLEN Eintraegen: git status meldet noch 2, beide untracked und echt, 0 Phantome (Blatt-Ist 52). EIN Befund, P2, Klasse BEWEIS, Ball beim Generator: die INITIALISIERUNG aus A-07-4 ist von keiner Zusage gedeckt - Mutation M4 laesst die Suite gruen - und in zwei von Hand gebauten Erbschafts-Szenarien aendert ihr Wegfall nichts ausser einer stderr-Zeile; die Erbschaft beendet das Beiseitelegen, nicht das read-tree. Blockiert nicht: der Zweck von A-07-4 ist an beiden Enden belegt, geschuldet ist die Zusage, nicht Code. In eigener Sache: A-07-5 hat 2590 Dateien beiseitegelegt (nicht geloescht); die danach wieder sichtbaren 92 stammten alle aus MEINEN Elter-Kontrolllaeufen und sind nach derselben Konvention beiseitegelegt, Stand 0."
zweitmessung_evaluator: "evaluator (Zweitinstanz) 10.08.: KOLLISION — als Evaluator fuer die A-07-Abnahme angesetzt, den claim_abnahme beim Start gesehen und trotzdem gemessen; waehrend der Messung hat die Erstinstanz abgenommen (fc5a3daa). KEIN zweites Urteil: das Votum der Erstinstanz gilt, meine Zweitmessung BESTAETIGT es unabhaengig in allen Kriterien (Abschnitt im Blatt): Suite 42/42 am Arbeitsbaum (content-identisch c512f931, eigener TMPDIR, Rueckstand 0), Basis 38/38 im worktree (-q, Rueckstand 16), eigene Wegwerf-Proben 1a (ANGEGLICHEN, cached 0, Arbeitsbaum unberuehrt; Basis-Tor: cached 1) / Kippfall (Zahl+Pfad, ls-files --stage byte-identisch, exit 0, Aufloesung gezeigt) / Abbruch+exec-Erbe (0 Rueckstand, kein invalid object, Erbe beiseite), Mutationen M2 (-ge: GENAU A-07-2 faellt) und M3 (trap: beide A-07-4-Zusagen fallen) an einer KOPIE (kein Mutationsfenster, md5 59e23956 identisch zurueck, Kontrolle 42/42), Halde final: 0 lebend, _to_delete/A-07-5 = 2589 (Generator-Zahl bestaetigt; 2590 der Erstinstanz +1, nicht tragend), alle vier deklarierten Abweichungen als gedeckt gewuerdigt. NEU nur: P3/UMGEBUNG PID-lose Altdatei 'index' (03.08.) liegt weiter in der Halde, von ^index\\. nicht gezaehlt; und der Prozess-Befund, dass der Claim die BEAUFTRAGUNG nicht bindet (zweite A-04-Klasse-Kollision heute). Ball unveraendert: release-pruefer."
```
---

## A-08 — RELEASE_FREI an 85b03d23 (Ball bei Yama: main-Veroeffentlichung; P2-SPEC-Folgeauftrag: A-09)

```yaml
auftrag: A-08
titel: "Commit-Tor: unterscheiden, ob ein GIT-Prozess einen Lock haelt - statt ob irgendwer die Datei offen hat"
datei: docs/auftraege/aktiv/A-08-halter-nach-kommando.md   # Traegerblatt; traegt den §11-Generator-Bericht
nachtrag: docs/auftraege/aktiv/A-08-NACHTRAG-drei-nein.md  # liefert Entscheidung + Kriterien; FUEHRENDER Wortlaut A-08-1
zustand: VEROEFFENTLICHT
nachtrag_vertretung: "10.08., Release-Pruefer in Yamas Vertretung (§16, Evaluator-Befunde ac07a1c5/a99547b1): Feld dem tatsaechlichen Stand angeglichen — veroeffentlicht mit main-FF 8648a4cb (10.08.). ENTSCHEIDUNG-KONSISTENZ.md ist NICHT in Kraft (Analyse ohne Geltungsakt); bis zur Aufnahme in die ARBEITSREGELN gilt §16 unveraendert."
ballbesitz: yama
release_sha: "8648a4cb"
release_vermerk: "release-pruefer (Stamm-Instanz) 10.08.: main-Integration in Vertretung ausgefuehrt — reiner FF c908d3f0..8648a4cb auf fork UND backup-private. Das §10-Protokoll der frischen Release-Instanz (b2f8c44b, RELEASE_FREI an 85b03d23) uebernommen und das Grundtor am Kandidaten 8648a4cb selbst erneut gefahren: tsc clean, Insel 1689/1689, Bundle byte-gleich, bash -n OK, Tor-Suite 38/38, Buehne 6/6, php artisan test 880/880. Ihr verweigerter Sicherungs-Push (2b5aebae) ist nachgeholt: Linie liegt auf beiden Remotes. Naechster Zustand BETRIEBSBESTAETIGT gehoert Yama."
claim_release: "plan-pruefer 08.08.: ABGENOMMEN (Erst- und Zweitvotum unabhaengig deckungsgleich), Release-Station leer bei P0 — FRISCHE Release-Pruefer-Instanz wird gestartet. Claim VOR dem Start. LEHRE aus der Instanzen-Kollision der Abnahme: eine 'failed'-Meldung ist KEIN Todesbeweis — vor jedem kuenftigen Ersatzstart pruefe ich zusaetzlich die Commit-Historie auf spaete Commits der totgesagten Instanz."
basis_bau: c2de1eec      # der Stand, auf dem gebaut wurde (HEAD bei Uebernahme, 1f17f93a = IN_ARBEIT-Commit direkt darauf)
pruef_sha: 85b03d23
ballwechsel_bestaetigt: "plan-pruefer 07.08.: CODE_FERTIG-Meldepflichten geprueft — Basis c2de1eec und Pruef-SHA 85b03d23 existieren, Scope-Diff SELBST gemessen (git diff --name-only c2de1eec 85b03d23): EXAKT die fuenf Blatt-Dateien (Tor, Suite, A-02-Blatt/A-08-7, Traegerblatt/Bericht, STATUS), nichts ausserhalb. IN_ARBEIT wurde VOR der ersten Scope-Aenderung gesetzt (1f17f93a, §3 erfuellt). Ball liegt beim EVALUATOR (§9) — ich nehme NICHT ab. FUER SEINE PRUEFUNG: Suite-Zaehler laut Generator 30/30 -> 38/38, davon 5 neue Zusagen an der Basis rot; Mutationsprobe 7/7 mit &&->|| zuerst; die Zwei-Richtungs-Probe (§12.3) je Kriterium gegenlesen; fremde Statuscommits 67038e50/c2de1eec liefen zwischen den Bau-Commits — am EXAKTEN Pruef-SHA messen."
korrektur_bestaetigt: "plan-pruefer 07.08. zur Nach-BEREIT-Korrektur 4c85e9b9 (Traegerblatt-A-08-2): BESTAETIGT. Die alte Fassung ('fehlt eine der drei Bedingungen -> ENV_BLOCKED', unbeschraenkt) haette die must_preserve-Zusage Z.547 (885 kB, 317 s, OHNE Halter -> beiseite ueber den Stillstandspfad) woertlich gebrochen — die Korrektur beschraenkt die Gegenprobe auf 0-Byte-Locks und verweist >0-Byte-Faelle unveraendert an die A-02-Logik. Das ist DIESELBE Schranke wie im fuehrenden Nachtrag-Wortlaut, keine dritte Fassung; der Katalog der verbindlichen Lesart aendert sich nicht (Traegerblatt-A-08-2 zaehlt dort ohnehin nicht doppelt). Kein neues BEREIT noetig: der Bau ist nach Generator-Messung mit alter UND korrigierter Fassung deckungsgleich, die Korrektur ist Dokument-Hygiene gegen genau die Doppelfassungs-Klasse, die diesen Auftrag schon zweimal getroffen hat."
generator_code_fertig: "generator 07.08.: CODE_FERTIG an 85b03d23 (Bau 5a54b004, Doku 6a264834). Drei-Nein-Regel NUR am 0-Byte-Pfad gebaut (ein &&-Ausdruck, Bedingung 3 zitiert das Tor-Mass woertlich, Doppelpfad Z.163 unangetastet); Lock MIT Inhalt + Halter blockt unveraendert, Meldungen nennen jetzt das Kommando je Halter-PID (A-08-10); Halter ohne ermittelbares Kommando bleibt unbekannt und liegt (A-08-5); Form B (git-Prozess DIESES Repos, cwd-Auskunft mit A-02-6-Zeitgrenze, im Zweifel gehalten) wirkt am Halter- UND am HALTER=0-Pfad, nur bei 0 Byte. Suite selbst gefahren: vorher 30/30 an c2de1eec, nachher 38/38 an 5a54b004; die 8 neuen Zusagen gegen das BASIS-Tor: 5 rot (A-08-1/-4/-5/Form B/-10), 3 gewollt gruen (Gegenhalter A-08-2, git-*, A-08-8). A-08-8 arbeitet mit einem ECHTEN unterbrochenen git-Lauf (update-index --index-info, 0 Byte gemessen, SIGKILL). Sieben Mutationen einzeln eingespielt und byte-identisch zurueckgesetzt: alle gefallen, M7 (0-Byte-Schranke entfernt) exakt durch A-02-2/A-02-4 — der f5098c40-Fall ist dauerhaft rot. Rohausgaben je Kriterium im §11-Bericht im Traegerblatt. Ich nehme NICHT ab."
naechster_schritt_evaluator: "Evaluator prueft 85b03d23 unabhaengig (§9): zuerst Auftrag+Diff+Code, Bericht erst danach; Mutationsprobe erneut fahren (§12.4), Gegen-Beweis je Kriterium; Kenntnisnahme der offenen 4c85e9b9-Bestaetigung (Plan-Pruefer)"
generator_uebernahme: "generator 07.08. (frische Instanz): IN_ARBEIT gesetzt VOR der ersten Scope-Aenderung (§3). Basis fuer den Bau: c2de1eec (HEAD zum Uebernahmezeitpunkt; 136b6e79 und c2de1eec beruehren den Scope nicht). §7-Vorpruefung gefahren: Scope-Dateien inhaltsgleich mit HEAD (git show HEAD:<pfad> | diff — 5x IDENTISCH; MM/D/??-Eintraege sind die bekannten Stale-Index-Phantome), Suite an der Basis SELBST gefahren: 30/30 (tests 30, pass 30, fail 0). Machbarkeit VOR dem Bau gemessen: (1) ein unterbrochener 'git update-index --index-info' hinterlaesst einen ECHTEN 0-Byte index.lock und haelt ihn bis dahin selbst (lsof-Halter = git-PID, ps comm= /Library/Developer/CommandLineTools/usr/bin/git — voller Pfad, A-08-4-Basename noetig und machbar); (2) 'lsof -a -p <pid> -d cwd -Fn' liefert das aufgeloeste Arbeitsverzeichnis und deckt sich mit pwd -P (Repo-Bezug fuer Bedingung 2 messbar); (3) ps -axo pid=,comm= + Basename-Filter findet git-Prozesse zuverlaessig. Die Nach-BEREIT-Korrektur 4c85e9b9 (Traegerblatt-A-08-2) habe ich gelesen: sie beschreibt exakt das Verhalten der 0-Byte-Fassung, mein Bau ist mit alter UND korrigierter Fassung deckungsgleich — sie blockiert den Bau nicht, die Bestaetigung bleibt beim Plan-Pruefer."
votum_2_runde: "plan-pruefer 07.08. (2. DoR-Runde ueber 2de78f71): BEREIT — diesmal MIT eigenem Nachvollzug der Simulation: Tor-Code selbst gelesen (Z.142-148: gehaltener Lock wird VOR jeder Alters-/Groessenfrage geblockt — die Content-Schranke laesst diesen Pfad unveraendert, A-02-2/A-02-4 bleiben logisch gruen; Z.163: Doppelpfad unangetastet), Suite SELBST gefahren: 30/30 (tests 30, pass 30, fail 0). Der Umschnitt traegt die 0-Byte-Fassung als fuehrenden Wortlaut, die Kantenliste ist je Zeile IST/SOLL-markiert (Nebenbefund-Zeile korrigiert), die Rangfolge Nachtrag-fuehrt steht im Traegerblatt (Z.119) gegen die dritte Doppelfassung, die Ehrliche Grenze ist eigener Abschnitt (Z.169), und die siebte Mutation (0-Byte-Schranke entfernt -> faellt durch A-02-2/-4) macht den f5098c40-Fall dauerhaft rot statt stumm gruen. Rot-Lage A-08-1 unveraendert wirksam (Vorfall 06.08., 0 Byte, exit 3 zweimal). Die zwei benannten Verhaltensaenderungen sind exakt die gewollten, keine Zusage deckt sie. BEREIT nach 2 Runden — der SPEC_BLOCKED zwischen den Runden war die Folge MEINER 1.-Runden-Richtung, nicht des Blatts."
generator_meldung: "07.08. 09:1x, VOR der ersten Scope-Aenderung (§7-Vorpruefung 'Auftrag ist machbar' gescheitert): Die Korrekturen ffaddb4b/1dcdc32e loesen die zwei gemeldeten Widersprueche am STILLSTANDSPFAD wirklich — selbst nachgeprueft (Bedingung 3 zitiert das Mass, Zeile 163 traegt den Doppelpfad, Suite 30/30 selbst gefahren). Der Katalog bleibt trotzdem unerfuellbar, an einer Stelle, die noch niemand gemeldet hat: die Zusagen 'A-02-2' (commitPruefen.test.mjs:512 — Lock 900 B, 400 s, gehalten von einem NODE-Prozess, erwartet: LIEGT + exit 3 + Halter-PID) und 'A-02-4' (Z.579 — 50 B, 400 s, node-Halter, erwartet: exit 3 + ENV_BLOCKED-Zeile) haben einen NICHT-git-Halter — nach Bedingung 1 exakt dieselbe Klasse wie die VM. Die Drei-Nein-Tabelle liefert fuer genau diese Eingabe drei Nein (kein git-Halter, kein Repo-git-Prozess, 400 s >= 120 s = Mass erfuellt) -> beiseitelegen -> beide Zusagen ROT. A-08-3 (korrigiert) und A-08-9 verlangen ALLE A-02-Zusagen gruen; das Nicht-Ziel 'Keine Aenderung an A-02-2/-3/-4/-6' verbietet zugleich, die Tests auf git-Halter umzustellen. Wer die Tabelle baut, faellt an A-08-3/-9; wer die Zusagen schuetzt (Nicht-git-Halter schuetzt Locks MIT Inhalt weiterhin), verletzt den Wortlaut von A-08-1 und die neue Kantenzeile 'dasselbe [VM haelt], 800 kB, 300 s still -> beiseite'. Diese Entscheidung gehoert nicht mir (3392400f, woertlich). KEIN Bau, KEIN IN_ARBEIT, Scope unberuehrt. Voller Beleg im Abschnitt 'SPEC_BLOCKED des Generators zu A-08' am Ende dieser Seite."
basis_sha: d377683a   # Rot-Messungen an der aktuellen Linie; Reparatur-Linie 6953198a (§12.2), Vorfahr — kein Widerspruch
prioritaet: "P0 — keine Warteschlange (Begruendung gemessen: der naechste verwaiste Lock sperrt wieder alle Rollen)"
letztes_votum: "plan-pruefer 07.08. (1. DoR-Runde, BEREIT beim ersten Review): alle 18 Punkte belegt, JEDE Rot-Lage selbst gemessen: A-08-1 exit 3 zweimal (eigener Vorfall fb7921bd) · A-08-4 ps -o comm= liefert den VOLLEN Pfad (/bin/zsh gemessen — ein '=git'-Vergleich hielte /usr/bin/git fuer fremd) · A-08-7 'lsof trennt sie exakt' steht woertlich im A-02-Blatt · A-08-8 die Suite stellt ALLE Locks per writeFileSync her (lockSetzen, Z.74-80), keine Zusage aus echtem git-Lauf · A-08-5/6 Zusagen existieren nicht (Rot als fehlende Zusage) · must_preserve A-08-2/-3 an der Basis gruen und korrekt deklariert (frischer Lock und Lock mit Inhalt bleiben heute liegen). Zahlen-Drift notiert, nicht tragend: Suite traegt 44 Zusagen, die Blaetter sagen 30."
verbindliche_lesart: "ZWEI Dokumente, EIN Katalog — es gilt der Kriterienkatalog des NACHTRAGS A-08-1..A-08-8, ergaenzt um zwei Kriterien des Traegerblatts: dessen 'A-08-3' (alle A-02-Zusagen bleiben gruen, insb. Zeitgrenze und ENV_BLOCKED-Form) wird als A-08-9 (must_preserve) gefuehrt, dessen 'A-08-4' (Meldung nennt das KOMMANDO des Halters, nicht nur die PID) als A-08-10 (P2). Traegerblatt-Kriterien 1/2/5 sind durch Nachtrag 1/2/3/8 vollstaendig abgedeckt und zaehlen nicht doppelt. Der Bericht des Generators nummeriert nach dieser Lesart."
konfliktpruefung: "Von mir ergaenzt — fehlte in BEIDEN Dokumenten: A-07 (ENTWURF) aendert dieselben zwei Dateien (commit-pruefen.sh, commitPruefen.test.mjs). REIHENFOLGE FESTGELEGT: A-08 baut zuerst; A-07 wird erst nach A-08-CODE_FERTIG bereit und misst dann neu. Keine zweite ca5f80e4-Lage. Die Doppelfuehrung der zwei A-08-Dateien hat der Planner selbst angezeigt und aufgeloest (Traegerblatt fuehrt) — sauber."
claim: "plan-pruefer 07.08.: Generator-Station leer bei P0 — FRISCHE Generator-Instanz wird gestartet (Claim VOR dem Start, Lehre aus den drei Doppelarbeiten). Ich baue NICHT selbst; die Instanz ist rollenrein Generator."
spec_blocked_triage: "plan-pruefer 07.08. (nach f5098c40): BEFUND BESTAETIGT, an den Testzeilen selbst nachgemessen — A-02-2 (Z.512) verlangt woertlich 'ein Lock MIT HALTER bleibt liegen — egal wie alt, still und gross', der Halter ist ein NODE-Prozess; meine Bedingung 1 ('kein git-Halter') stuft genau diesen legitimen lebenden Halter als Phantom ein. DER KERN IST MEIN ANTEIL: A-02 schuetzt JEDEN lebenden Halter, meine Richtung d4308d35 hat das auf git-Halter verengt — die VM-Phantom-Frage mit der Halter-Frage beantwortet statt sie zu trennen. Und meine BEREIT-Runde hat die Drei-Nein-Tabelle NICHT gegen den Zusagen-Bestand simuliert, nur die Rot-Lagen gemessen — Lehre: must_preserve heisst kuenftig 'Tabelle gegen ALLE bestehenden Zusagen durchspielen', nicht 'an der Basis gruen'. ZWEITER EIGENFEHLER: meine 'Suite traegt 44 Zusagen' war ein grep-Zaehler, der Lauf traegt 30 — der Lauf schlaegt den grep. RICHTUNGS-KORREKTUR fuer den Neuschnitt (Planner entscheidet den Schnitt): der GENERATOR-VORSCHLAG ist als Minimum RICHTIG und von mir angenommen — die Kommando-Frage ersetzt die Halter-Blockade NUR bei 0-Byte-Locks; ein Lock MIT Inhalt und Halter bleibt liegen wie heute. Das erfuellt den Vorfalls-Fall (06.08., 0 Byte), laesst ALLE A-02-Zusagen gruen und braucht keine VM-Sonderbehandlung. EHRLICHE GRENZE: die 03.08.-Klasse (Content-Lock, verwaist, phantom-gehalten) bleibt damit ENV_BLOCKED und wird von Hand nach Dauerregel geraeumt — konservatives Scheitern, kein Datenverlust; wer sie automatisch will, braucht eine Phantom-Erkennung (z. B. Kontrollprobe: haelt dieselbe PID auch eine unbeteiligte Referenzdatei wie .git/config, ist sie Mount-Rauschen) — die haengt aber an der UNERKLAERTEN Dateigruppen-Trennung und gehoert, wenn ueberhaupt, in ein eigenes Blatt mit eigener Messung, nicht als Beifang in A-08."
claim_umschnitt: "plan-pruefer 07.08. 09:1x: Planner-Station 13+ min still bei P0, keine ungesicherte Arbeit (content-diff beider Dateien gegen HEAD leer, MM/D sind Stale-Index-Phantome) — FRISCHE Planner-Instanz wird fuer den Umschnitt gestartet. Claim VOR dem Start. Ich schneide NICHT selbst; die DoR-Pruefung des Umschnitts bleibt bei mir."
umschnitt_meldung: "planner 07.08. (frische Instanz, Claim 6bc733bb) — Commit 2de78f71, Suite vorher selbst gefahren (30/30): (1) Nachtrag-A-08-1 auf die 0-Byte-Fassung (fuehrender Wortlaut; fuer 0 Byte heisst das Mass >=60 s, commit-pruefen.sh:163), DECISION-Block eingeschraenkt + Korrektur 3 mit den Messbelegen Z.512/579; (2) Kantenliste: JEDE Zeile als IST=SOLL oder SOLL markiert, die Nebenbefund-Zeile '800 kB gehalten, 300 s still -> beiseite' auf 'liegen lassen' korrigiert (ein gehaltener Lock erreicht den Stillstandspfad heute nie, Z.142-148 blockt vorher — der Generator hatte recht), neue Zeilen fuer A-08-5 und den Umschnitt-Kern (nicht-git-Halter + Inhalt -> liegen); (3) Traegerblatt: Richtungstext und A-08-1 auf DIESELBE Fassung mit ausdruecklicher Rangfolge (Nachtrag fuehrt — gegen die dritte Doppelfassung), Abschnitt 'Ehrliche Grenze' (03.08.-Klasse bleibt ENV_BLOCKED + Handraeumung nach Dauerregel, Phantom-Erkennung waere eigenes Blatt); (4) Simulationstabelle ALLER 30 Zusagen im Nachtrag (je Zusage Eingabe -> neues Verhalten -> gruen), einzige zwei Verhaltensaenderungen benannt: Vorfalls-Fall wird beiseite (Rot-Lage A-08-1) und 0-Byte-Lock ohne Halter bei laufendem Repo-git bleibt liegen (konservativer, keine Zusage deckt ihn); (5) A-08-6 um die siebte Mutation erweitert (0-Byte-Schranke entfernt -> faellt durch A-02-2/A-02-4 — exakt der f5098c40-Fall). Ich setze NICHT BEREIT."
claim_bau: "plan-pruefer 07.08.: BEREIT gesetzt, Generator-Station wird SOFORT mit frischer Instanz besetzt (P0). Claim VOR dem Start."
claim_abnahme: "plan-pruefer 07.08.: CODE_FERTIG liegt, Evaluator-Station leer bei P0 — FRISCHE Evaluator-Instanz wird gestartet. Claim VOR dem Start. Ich nehme NICHT selbst ab (§4/§9); die Instanz ist rollenrein Evaluator. NACHTRAG 08.08.: die erste Instanz ist ZWEIMAL abgestorben (API-Abbruch, dann 600s-Stall) OHNE Spuren — beide Male gemessen: Tor/Suite byte-identisch mit 85b03d23, keine Commits, kein Lock, keine Mutationsreste. ZWEITE frische Instanz gestartet, gleicher Auftrag."
naechster_schritt: "ERLEDIGT (85b03d23) — Generator hat in der 0-Byte-Fassung gebaut, Katalog Nachtrag 1-8 + 9/10, IN_ARBEIT war VOR der ersten Scope-Aenderung gesetzt (1f17f93a), §11-Bericht im Traegerblatt. Jetzt: Evaluator, siehe naechster_schritt_evaluator oben"
evaluator_votum: "evaluator 08.08.: ABGENOMMEN an 85b03d23. Selbst gefahren: Suite 38/38, Basis 30/30, neue Zusagen gegen das Basis-Tor 5 von 8 rot, sieben eigene Mutationen alle gefangen (md5 zurueckgesetzt), drei eigene Torlaeufe im Wegwerf-Repo. EIN Befund, P2, Klasse SPEC, Ball beim Planner: ein git-Prozess DIESES Repos mit --git-dir und fremder cwd wird von repo_git_laeuft() nicht erkannt (Probe C: Lock beiseitegelegt, Commit lief). Blockiert nicht - der Bau folgt der Kantenliste des Blattes genau, die Luecke steckt im Schnitt; die gefaehrliche Lage deckt Bedingung 1 ab (Probe B), und git -C wird erkannt. Offengelegt: die Ausgabe von git worktree add zeigte mir die Betreffzeile des Pruef-SHA vor der Messung."
evaluator_zweitvotum: "evaluator-2 08.08. (zweite frische Instanz nach dem Doppel-Absterbe-Claim 966dea39, Kollision offengelegt): ABGENOMMEN an 85b03d23 — unabhaengige Zweitbestaetigung, VOR Kenntnis des Erstvotums gemessen. Selbst gefahren: Suite 38/38 (Scope-Dateien byte-identisch mit 85b03d23, md5 7c71f5ba), A-08-Zusagen gegen das Basis-Tor 8/3/5 (rot: A-08-1/-4/-5/Form B/-10), eigene Wegwerf-Proben je Kriterium inkl. Zwei-Richtungs-Probe A-08-1 (Basis exit 3 -> Bau exit 0 + BEISEITE mit Zielpfad/Groesse/Alter), Gegenfall gitarre zaehlt NICHT als git, alle SIEBEN Mutationen eigenhaendig gesetzt und gefangen (M7 exakt durch A-02-2/A-02-4), Endzustand byte-identisch. Den P2-SPEC-Befund des Erstvotums (--git-dir + fremde cwd) selbst REPRODUZIERT (exit 0 + BEISEITE; git -C korrekt exit 3) — bestaetigt, kein neuer Befund. Realfall-Beleg zitiert: .git/_locks_beiseite/2026-08-08/index.lock (0 Byte, Original erhalten). Zweitvotum am Ende des Traegerblatts; meine versehentlich von 4307987b mitcommittete Erstfassung dort durch die gekennzeichnete Zweitfassung ersetzt."
release_vermerk: "release-pruefer 08.08.: RELEASE_FREI an 85b03d23 (Release-Kandidat 76bb1992, scripts/ content-identisch; die danach gelandeten Doku-Commits ae6c6dca/d41db6a2/ff549b88 beruehren den Scope nicht — nachgemessen, 0 Zeilen). §10 selbst gefahren: Suite am HEAD 38/38; Kette 793b0729 BEREIT -> 1f17f93a IN_ARBEIT (VOR erster Scope-Aenderung, 0 Scope-Commits davor) -> 5a54b004 Bau -> e491626d CODE_FERTIG -> 23b3a490 + f430242d ABGENOMMEN, jede Stufe Vorfahr der naechsten; Scope-Diff c2de1eec..85b03d23 exakt die fuenf Blatt-Dateien, kein Produktivcode ausserhalb; Beifang-Kontrolle 4307987b/7c2958fd nur Doku, git log e491626d..76bb1992 -- scripts/ = 0; Rueckweg: git revert 5a54b004 genuegt (nur 2 Skriptdateien, keine Migration/Daten); Wildbetrieb: 0-Byte-VM-Lock am 08.08. 13:58 beiseitegelegt (_locks_beiseite/2026-08-08/, Original liegt), danach 18 Commits ohne Aussperrung durchs Tor. VERMERK nach §12.5: P2-SPEC (--git-dir + fremde cwd) ist KEIN Release-Hindernis, Folgeauftrag A-09 existiert und traegt ihn (A-09-1). Ball bei Yama: main-Veroeffentlichung ist seine. Sicherungs-Push auf fork folgt unmittelbar nach diesem Commit (nur auto/hausplaner-integration, nie main/Tags/force); Ergebnis als push_vermerk-Zeile nachgetragen — eine Verweigerung waere ENV-Hinweis, kein Abbruch."
push_vermerk: "release-pruefer 08.08., ENV-HINWEIS: der Sicherungs-Push (git push fork auto/hausplaner-integration — nie main/Tags/force) wurde von der UMGEBUNG verweigert (Permission-System der Instanz blockt git push, zweimal versucht: einmal im Sammelbefehl, einmal einzeln). KEIN fachliches Rot, kein Abbruch — RELEASE_FREI steht. FOLGE nach Repo-Aufsichts-Massstab: die verifizierte Arbeit bis b2f8c44b liegt weiterhin NUR lokal, der fork-Remote traegt sie nicht — 'nicht gepusht' heisst 'kein Backup ausserhalb der Maschine'. Der Push bleibt offen fuer Yama oder eine Instanz mit Push-Erlaubnis."
---

## A-09 — RELEASE_FREI an af8f2054 (Ball bei Yama: main-Veröffentlichung; §10 im Blatt)

```yaml
auftrag: A-09
titel: "Commit-Tor: Repo-Bezug eines git-Prozesses auch ueber --git-dir erkennen, nicht nur ueber die cwd"
datei: docs/auftraege/aktiv/A-09-repo-bezug-nicht-nur-cwd.md
zustand: VEROEFFENTLICHT
ballbesitz: yama
release_sha: "2e7b58fc"
release_vermerk: "release-pruefer (Stamm-Instanz) 10.08.: §10 an der Abnahme af8f2054 (Votum e53e3cfb, Fehlerklasse KEINE) — Kette Vorfahr, Scope exakt 2 Dateien (Tor +96, Suite +227), Produkt-Code seit 2da18c44 unberuehrt, bash -n OK, Tor-Suite 50/50, Waechter 7/7, Geheimnis-/env-Scan leer. main-Integration als reiner FF 2da18c44..2e7b58fc auf fork UND backup-private. OFFENGELEGT: der Zustandseintrag kam einen Commit NACH dem Push statt im selben Arbeitsgang (Patch-Skript griff nicht, Release lief weiter) — Verstoss gegen die eigene Regel vom 05.08., hier transparent nachgeholt. Naechster Zustand BETRIEBSBESTAETIGT gehoert Yama."
basis_sha: 5a54b004      # aus dem Blatt; Uebernahme-HEAD (Basis der Messungen): d836fb91
basis_bau: d836fb91      # HEAD bei Uebernahme, 5a54b004 ist Vorfahr; Scope-Drift auf scripts/ bis zum Bau: 0
pruef_sha: af8f2054      # Bau 12ca3798 (beide Skripte), §11-Bericht af8f2054; dieser STATUS-Commit folgt darauf
generator_code_fertig: "generator 10.08.: CODE_FERTIG an af8f2054 (Kette d6846f69 IN_ARBEIT -> 12ca3798 Bau -> af8f2054 §11-Bericht im Blatt). repo_git_laeuft() traegt jetzt die DREI Wege der DECISION: cwd (unveraendert), Aufrufform (--git-dir/-C/--work-tree aus ps args=, auch die zweiteiligen Formen), Umgebung (GIT_DIR/GIT_WORK_TREE aus ps -E) — Pfadvergleich stets NACH physischer Aufloesung ueber die neue pfad_meint_repo() (relativ gegen die cwd des Kandidaten, /var-Symlink begradigt), nicht Feststellbares bleibt gehalten. Suite selbst gefahren: Basis 42/42, Bau 50/50 (acht neue Zusagen); die fuenf Neu-Verhalten (A-09-1, Aufloesung, --work-tree, A-09-6, GIT_WORK_TREE) gegen das BASIS-Skript nachweislich rot (pass 3 / fail 5), die drei Kontrollen (A-09-2, A-09-3, Zweifel) an Basis und Bau gruen. A-09-5: SECHS Mutationen einzeln eingespielt, ALLE gefangen (M1 3 rot, M2 3, M3 5, M4 1, M5 2, M6 2), md5 fd351a78 vor und nach jeder Probe byte-identisch. A-09-4 war an der Basis BEREITS erfuellt (Planner 48ca0099 beim Blattschnitt, verifiziert Z.266-270 des Nachtrags) — bewusst kein Doppel-Diff, als Abweichung im Bericht deklariert. Grenzen dokumentiert statt gebaut: fremde Nutzer (faengt der bestehende cwd-Zweifelspfad), Pfade mit Leerzeichen in ps-Ausgaben. Fuer den STATUS-Commit auf den Planner-Commit 874d6331 gewartet statt dessen uncommittete Zeilen mitzusichern. Ich nehme NICHT ab."
prioritaet: "P2 — Warteschlange JA, nach A-07 (so steht es im Blatt, und die Reihenfolge ist richtig: gleiche Dateien)"
in_arbeit_gesetzt: "generator 10.08. (frische Instanz, Claim ccf9292c): IN_ARBEIT gesetzt VOR der ersten Scope-Aenderung (§3). §3-Schlange selbst geprueft: kein Auftrag IN_ARBEIT (A-10 RELEASE_FREI an 5f7043bc, A-04/A-07 RELEASE_FREI). §7-Vorpruefung gefahren an HEAD d836fb91: (1) basis_sha 5a54b004 ist Vorfahr von HEAD; (2) alle fuenf Scope-Dateien content-identisch mit HEAD (git show HEAD:<pfad> | diff — 5x IDENTISCH; die git-status-Eintraege sind die bekannten Index-Phantome der A-07-Klasse); (3) Suite an der Basis SELBST gefahren: 42/42 (tests 42, pass 42, fail 0); (4) BEIDE Rot-Lagen ZWEIMAL selbst gemessen im Wegwerf-Repo mit dem Skript von HEAD: Form C (git --git-dir=<repo>/.git cat-file --batch, fremde cwd, Prozess nachweislich lebend via ps args) -> 0-Byte-Lock 302s BEISEITE, Commit lief, exit 0; Form D (GIT_DIR=<repo>/.git in der UMGEBUNG, via ps -E -p <pid> -o command= am lebenden Prozess nachgewiesen, fremde cwd) -> ebenfalls BEISEITE, Commit lief, exit 0. Beide Kriterien A-09-1/A-09-6 damit an der Basis wirksam rot, exakt Probe C/D des Evaluators."
votum_dor_1_runde: "plan-pruefer 08.08. (1. DoR-Runde): ENTWURF bleibt, EIN gebuendelter Restpunkt — inhaltlich ist das Blatt stark: DECISION klar (Repo-Bezug ueber cwd ODER Aufrufform, Pfadvergleich nach Aufloesung, nicht-feststellbar = gehalten), Nicht-Ziele sauber (GIT_DIR ausdruecklich als unmessbar benannt statt verschwiegen — die A-02-Lehre), Kantenliste mit Gegenrichtung, Entdeckung mit Regressionssignal (haeufigeres ENV_BLOCKED = Pruefung zu weit), Konflikt mit A-07 durch Warteschlangen-Platz geloest, Claim vor dem Schnitt gesetzt. ROT-LAGEN SELBST GEPRUEFT: A-09-1 strukturell bewiesen — repo_git_laeuft() baut Kandidaten aus ps comm= (Z.74-78) und misst Repo-Bezug NUR ueber lsof -d cwd (Z.81 ff.), args wird NIRGENDS gelesen, --git-dir ist damit strukturell unsichtbar; dazu die dynamische Probe C des Evaluators (23b3a490). A-09-5-Zusagen existieren nicht (Rot als fehlende Zusage). must_preserve A-09-2/-3 an der Basis gruen und korrekt deklariert (git -C ueber cwd erkannt — Probe B; fremdes Repo zaehlt heute trivially nicht)."
offene_akzeptanz:
  - "Restpunkt (gebuendelt, reine Form): (a) exakter basis_sha fehlt im Kopf — die Rot-Messungen gelten ab dem A-08-Bau, also 5a54b004 oder juenger benennen; (b) §5-Auswirkungen-Block fehlt (Testdaten-Ziel KEINES, Prozessbindung entfaellt, Werkzeuge: node-Suite 38 Zusagen vorhanden UND in Gebrauch — dritter Auftrag in Folge, dem dieser Block beim ersten Schnitt fehlt, das ist inzwischen ein MUSTER fuer die naechste Prozesspruefung); (c) Erstnutzer-Halbsatz (jede Rolle beim naechsten Commit, wie A-08); (d) formale Wiederverwendungspruefung als eigener Block (die Inhalte stehen schon im Ist-Zustand, sie muessen nur als solcher benannt sein)."
naechster_schritt_alt: "(2. Runde ersetzt)"
votum_2_runde: "plan-pruefer 10.08. (2. Runde nach e54e748d): ENTWURF bleibt, EIN Rest — der Formblock ist vollstaendig und selbst geprueft (basis_sha 5a54b004 mit struktureller Begruendung: repo_git_laeuft existiert davor nachweislich nicht — grep 0 an 5a54b004^, sauber; §5-Block, Erstnutzer, Wiederverwendung da). DER REST: das GIT_DIR-Nicht-Ziel (Z.88) traegt WEITERHIN die vom Evaluator WIDERLEGTE Begruendung 'nicht verlaesslich lesbar' — Probe D (fc64f05e) hat gemessen: derselbe Effekt (0-Byte-Lock beiseitegelegt trotz laufendem Repo-git via GIT_DIR), und ps -E liest die Variable fuer Same-User-Prozesse, also fuer ALLE Rollen dieses Repos, mit demselben Werkzeug, das A-09 ohnehin benutzt. Ein Nicht-Ziel ist weiter ZULAESSIG — aber die Begruendung muss die ehrliche sein ('messbar, aber bewusst nicht erfasst, Luecke bleibt offen und dokumentiert' + Kantenzeile Z.192 anpassen) ODER GIT_DIR wird als Bedingung aufgenommen (gleiches Werkzeug, kleiner Zuwachs). Die WAHL ist Planner-Sache; verboten ist nur der jetzige Zustand: eine widerlegte Aussage als Entscheidungsgrundlage in einem Blatt, das exakt diese Fehlerklasse behandelt (A-09-4)."
claim_release_a09: "plan-pruefer 10.08.: A-09 ABGENOMMEN (e53e3cfb) — Release-Station mit frischer Instanz besetzt. Claim VOR dem Start."
ballwechsel_bestaetigt_a09: "plan-pruefer 10.08.: A-09-CODE_FERTIG-Meldepflichten geprueft — Kette d6846f69 -> 12ca3798 -> af8f2054 (Pruef-SHA) -> 8cd9de10, Scope SELBST gemessen: exakt die Blatt-Dateien (Tor repo_git_laeuft-Zone + Suite + A-08-Nachtrag-Zeile + Blatt + STATUS), Commit-Aufruf- und A-11-Zone unangetastet (Diff-Hunks @@68/@@107). Ball beim EVALUATOR. FUER SEINE PRUEFUNG: A-09-4 wird als AN-DER-BASIS-ERFUELLT deklariert (48ca0099 — nachpruefen statt glauben); Suite 42->50, neue Zusagen gegen Basis 3 pass/5 fail (exakt Kontrollen vs. Neu-Verhalten); die dokumentierten Grenzen (fremde Nutzer, Leerzeichen-Pfade in ps) gegenlesen."
claim_abnahme_a09: "plan-pruefer 10.08.: Evaluator-Station fuer A-09 mit frischer Instanz besetzt. Claim VOR dem Start."
claim_bau_a09: "plan-pruefer 10.08.: §3-Schlange frei (A-10 ABGENOMMEN, kein IN_ARBEIT) — Generator-Station fuer A-09 mit frischer Instanz besetzt. Claim VOR dem Start."
votum_bereit: "plan-pruefer 10.08. (3. Runde nach 52c25a62): BEREIT — die GIT_DIR-Frage ist auf ehrlicher Grundlage entschieden: AUFGENOMMEN als Bedingung 3 (Umgebung via ps -E, dasselbe Werkzeug), die widerlegte Begruendung steht KORRIGIERT im Blatt (beide Halbsaetze einzeln geprueft: Effekt bestaetigt, Lesbarkeit widerlegt), die ECHTE Grenze ist benannt (ps -E liest fremde NUTZER nicht — root-Probe 0 Treffer; alle Rollen laufen als derselbe Nutzer), neues P1-Kriterium A-09-6 mit Probe D als wirksamem Rot, Kantenliste und Mutationsprobe nachgezogen. Pfadvergleich nach Aufloesung gilt fuer alle drei Wege. Warteschlange: hinter A-10, vor A-11."
naechster_schritt: "Evaluator prueft af8f2054 unabhaengig (§9): Suite an Basis und Pruef-SHA selbst fahren, Rot-Lagen C/D selbst nachstellen, Mutationsproben erneut (§12.4), fuer A-09-4 die Fundstelle Z.266-270 im A-08-NACHTRAG lesen (bewusst KEIN Diff in diesem Bau — Kriterium war an der Basis durch 48ca0099 erfuellt), fuer A-09-3 den Gegen-Beweis am zweiten Wegwerf-Repo fuehren"
claim_abnahme: "evaluator (Erstinstanz) 10.08. 21:0x: Abnahme A-09 GECLAIMT vor dem Pruefstand-Aufbau — Lehre aus der A-04-Kollision, bei der zwei Instanzen denselben Auftrag genommen haben. Pruef-SHA af8f2054, Bau 12ca3798, Elter-Kontrolle folgt."
letztes_votum: "evaluator 10.08.: ABGENOMMEN an af8f2054 (Bau 12ca3798; scripts/ zwischen Bau, Pruef-SHA und HEAD ded32c75 content-identisch, 0 Zeilen Drift — selbst gemessen). Alles unabhaengig nachgemessen in eigenen Worktrees (git worktree add -q): Basis 12ca3798^ = fec3a07a Suite 42/42, Pruef-SHA 50/50; Namensabgleich per tap+comm: exakt 8 neue A-09-Zusagen, 0 Bestandszusagen weggefallen — alle 42 must_preserve laufen namensgleich gruen, A-08-0-Byte-Schranke und A-07-Angleichung unberuehrt (Torlaeufe zeigten INDEX ANGEGLICHEN). Probe C (--git-dir, fremde cwd) und Probe D (GIT_DIR nur in der Umgebung, per ps -E am lebenden Prozess belegt, args ohne GIT_DIR) je in BEIDEN Richtungen am SELBEN lebenden Prozess: neues Tor exit 3 + Lock liegt + ENV_BLOCKED, Basis-Tor beiseite + Commit lief (01dd4f5 / 5027789). Eigener Gegen-Beweis A-09-3: echtes zweites Wegwerf-Repo, --git-dir dorthin, Prozess lebte — zaehlt NICHT, Commit 4565feb lief; 'jeder git zaehlt' widerlegt. A-09-2 via git -C lebend geprueft: exit 3. Relative Form --git-dir=pr-c/.git aus fremder cwd: gefangen. A-09-5 nach §12.4 selbst: M3 Rohvergleich statt Aufloesung -> 50/45/fail 5 (exakt die fuenf Neu-Verhalten), M6 ps ohne -E -> fail 2 (beide Umgebungs-Zusagen) — beide Zahlen decken den Generator-Bericht, md5 fd351a78 vor und nach jeder Probe selbst gemessen, Suite danach 50/50. A-09-4: 48ca0099 selbst gelesen (Diff traegt den Zeilentausch FREMDES VERZEICHNIS -> FREMDES REPOSITORIUM samt Befund-Vermerk 23b3a490/Probe C/SPEC) — kein Doppel-Diff war richtig, die Abweichungs-Deklaration trifft zu. Grenzen gegengelesen: fremde Nutzer KONSERVATIV (cwd-Zweifelspfad haelt Richtung Blockade), Leerzeichen-Pfade ehrlich benannt, wirken Richtung Uebersehen — Randnotiz ohne Auftrag, solange kein Arbeitsbaum mit Leerzeichen existiert. Realtest Erstnutzer: Votum-Commit e53e3cfb und dieser STATUS-Commit liefen selbst durchs neue Tor. Fehlerklasse KEINE, keine offenen P0/P1. Ball beim Release-Pruefer."
naechster_schritt_evaluator: "Release-Pruefer faehrt §10 auf dem Abnahme-Stand: Kette d6846f69 -> 12ca3798 -> af8f2054 -> e53e3cfb je is-ancestor, Suite am Release-Kandidaten, Scope-Diff nur Blatt-Dateien, Rueckweg git revert 12ca3798"
release_vermerk: "release-pruefer 10.08.: RELEASE_FREI an af8f2054 (§10-Abschnitt im Blatt, alles selbst gemessen). Kette c93d68ae -> d6846f69 -> 12ca3798 -> af8f2054 -> 8cd9de10 -> e53e3cfb -> HEAD a1e732d5: sechsmal is-ancestor OK. Suite im eigenen Worktree am Pruef-SHA: tests 50 pass 50 fail 0; bash -n und node --check am HEAD exit 0. Release-Diff: git show 12ca3798 --stat = exakt 2 Dateien (+316/-7), Skript-Hunks genau @@68 und @@107 — Botschaft-Annahme-Zone (A-11, Z.46-52) unberuehrt. Drift seit Pruef-SHA: git log af8f2054..HEAD -- scripts/ = 0 Commits, diff 0 Zeilen — der parallele A-11-Bau hatte die Datei bis zur Pruefung nicht angefasst, scripts/ am HEAD byte-identisch mit dem Kandidaten. Rueckweg: git show 12ca3798 | git apply --check -R exit 0, kein Datenpfad, git revert 12ca3798 genuegt. Randnotizen gewuerdigt, kein P0/P1: Leerzeichen-Pfade (Richtung Uebersehen, Repo-Pfad leerzeichenfrei, dokumentiert) und fremde Nutzer (Zweifelspfad haelt Richtung Blockade, root-Probe 0 Treffer, dokumentiert). Realtest Erstnutzer: dieser Blatt- und dieser STATUS-Commit liefen selbst durchs Tor mit den drei A-09-Wegen aktiv. Sicherungs-Push nach v1.2-Vertretung: git push fork auto/hausplaner-integration — Ergebnis siehe push_vermerk. Ball bei Yama: main-Veroeffentlichung."
push_vermerk: "release-pruefer 10.08.: Sicherungs-Push versucht (git push fork auto/hausplaner-integration, kein main/Tag/force) — vom Berechtigungssystem der Umgebung VERWEIGERT, kein Netz-/Auth-Fehler des Remotes. Nach Auftrag als ENV-Hinweis verbucht, kein Befund am Release-Kandidaten; RELEASE_FREI bleibt bestehen. Folge fuer die Repo-Aufsicht: die verifizierte Arbeit ab e53e3cfb liegt weiter NUR lokal — der Push ist nachzuholen (Yama oder eine Instanz mit Push-Erlaubnis)."
```
---

## In Planprüfung — A-10

```yaml
auftrag: A-10
titel: "Ein Dach, das KEINE Flaeche liefert, wird gemeldet - auch ohne Ausnahme"
datei: docs/auftraege/aktiv/A-10-melder-am-leeren-ergebnis.md
zustand: VEROEFFENTLICHT
nachtrag_vertretung: "10.08., Release-Pruefer in Yamas Vertretung (§16, Evaluator-Befunde ac07a1c5/a99547b1): Feld dem tatsaechlichen Stand angeglichen — veroeffentlicht mit main-FF 2da18c44 (10.08.). ENTSCHEIDUNG-KONSISTENZ.md ist NICHT in Kraft (Analyse ohne Geltungsakt); bis zur Aufnahme in die ARBEITSREGELN gilt §16 unveraendert."
ballbesitz: yama
release_sha: "2da18c44"
release_vermerk: "release-pruefer (Stamm-Instanz) 10.08.: main-Integration in Vertretung — reiner FF e321f2a2..2da18c44 auf fork UND backup-private. Das §10 der frischen Release-Instanz (5f7043bc, RELEASE_FREI an 47c0aa73) uebernommen und das volle Grundtor am Kandidaten selbst erneut gefahren: tsc clean, Insel 1692/1692, Bundle BYTE-GLEICH (62338b66), php artisan test 880/880. Ihr verweigerter Push (d836fb91) ist nachgeholt. Naechster Zustand BETRIEBSBESTAETIGT gehoert Yama."
basis_sha: d58b220e
basis_bau: 8343f206   # HEAD bei Uebernahme; d58b220e ist Vorfahr, Scope-Diff d58b220e..HEAD = 0 Zeilen
pruef_sha: 47c0aa73   # Bau dbb7ff66 (Code+Tests+Bundle), §11-Bericht 47c0aa73; dieser STATUS-Commit folgt darauf
generator_code_fertig: "generator 10.08.: CODE_FERTIG an 47c0aa73 (IN_ARBEIT 5fc9c9e2 -> Bau dbb7ff66 -> Bericht 47c0aa73). Die zweite Eingangsbedingung sitzt VOLLSTAENDIG in nichtDarstellbar.ts (szene.ts und DreiDBereich.tsx unveraendert — der EINE Ort aus A-01-4 bleibt der eine Ort): mesh.dreiecke.length === 0 && dachflaechen(dach).length === 0 -> Meldung mit lesbarem Grund. KONJUNKTIV, nicht oder: ein l-shape MIT anbau hat 10 Dreiecke bei dachflaechen()=0 (gemessen, ebenso walm) — eine Oder-Fassung meldete zeichenbare Daecher; am Nullpunkt sind beide Zeugen gekoppelt (dreiecke==0 erzwingt dachflaechen()==0, gleiche Quelle dachRoh), die Konjunktion ist exakt 'die Berechnung liefert null Flaechen'. Drei neue Zusagen im BESTEHENDEN dachAusKontur.test.ts (keine Parallelstruktur): A-10-1 (Verhalten, an der Basis rot: Melder []), A-10-2 KONTROLLE (Sattel-Rechteck UND l-mit-anbau nicht gemeldet — die Verhaltens-Falle fuer &&->||), A-10-5 ZEUGEN (strukturell, Grenze offen benannt: Mutation 3 ist behavioral nicht trennbar, weil die Zeugen am Nullpunkt gekoppelt sind). Suite selbst gefahren: Basis 1689/1689, Bau 1692/1692; tsc exit 0; Bundle frisch (grep des neuen Grunds = 1). Mutationen M1/M2/M3 aus dem Blatt + Zugabe &&->|| einzeln eingespielt, alle gefallen (M3 ueber die Struktur-Zusage, Zugabe ueber A-10-2 am VERHALTEN), md5 746b68c2 vor und nach jeder Probe. Browserabnahme A-10-4 GEFAHREN: Waechter vorab (PID 48098 unangetastet), Buehne NUR ueber browser-buehne.sh --port 8099 (Kindprozess: ticket_testing), Probedaten in ticket_testing angelegt (Objekt 10229, Dokument 36 = a01-Fixture als roofType l-shape OHNE anbau — das Fixture selbst traegt 'sattel' und zeigt nur den Wurf-Pfad), Anker dreistufig (canvas 0->2), Hinweis in 1440/1024/375 mit role=status, Gegenprobe studio?fixture=u-dach OHNE Hinweis, keine Hausplaner-Konsolen-Fehler (CRM-Bestandsrauschen benannt). Rohausgaben im §11-Bericht im Blatt. Ich nehme NICHT ab."
ballwechsel_bestaetigt: "plan-pruefer 10.08.: A-10-CODE_FERTIG-Meldepflichten geprueft — Kette 5fc9c9e2 (IN_ARBEIT vor erster Aenderung) -> dbb7ff66 (Bau: nichtDarstellbar.ts + dachAusKontur.test.ts + Bundle, §5-konform mit build:hausplaner) -> 47c0aa73 (§11-Bericht) -> 907a6117 (STATUS), Pruef-SHA existiert. Ball beim EVALUATOR, dessen Claim 165239e5 die Station korrekt hielt (auf den Commit gewartet statt den bewegten Baum zu pruefen — §18 gelebt). FUER SEINE PRUEFUNG: (1) die Konjunktiv-Entscheidung (dreiecke==0 UND dachflaechen==0) ist eine gemessene BAUFORM-Abweichung vom Blatt-Wortlaut 'null Flaechen' — der Messbefund (l-shape MIT anbau: 10 Dreiecke bei dachflaechen 0; Oder-Fassung haette zeichenbare Daecher gemeldet) gehoert nachgeprueft; (2) A-10-4 legte Probedaten in ticket_testing an (Objekt 10229, Dokument 36), weil das a01-Fixture sattel traegt — §15-konform (TESTdatenbank), aber der §5-Block sagte 'Testdaten-Ziel KEINES': deklarierte Spec-Drift, wuerdigen; (3) Mutation 3 faellt nur ueber die Struktur-Zusage (offen benannte Grenze)."
naechster_schritt_evaluator: "Evaluator prueft 47c0aa73 unabhaengig (§9): Suite an Basis und Pruef-SHA selbst fahren, Mutationsprobe erneut (§12.4), Gegen-Beweis je Kriterium — fuer A-10-2 den scharfen Fall l-shape MIT anbau gegenlesen (Oder-Fassung waere der Fehler), fuer A-10-5 die offen benannte Struktur-Grenze wuerdigen oder verwerfen; Browserkette selbst fahren (Probedaten Objekt 10229 liegen in ticket_testing, Buehne nur ueber browser-buehne.sh)"
claim_release_a10: "plan-pruefer 10.08.: A-10 ABGENOMMEN — Release-Station mit frischer Instanz besetzt. Claim VOR dem Start."
in_arbeit_gesetzt: "generator 10.08. (frische Instanz, Claim c30dc2a5): IN_ARBEIT gesetzt VOR der ersten Scope-Aenderung (§3). §3-Schlange selbst geprueft: kein Auftrag IN_ARBEIT (A-04 und A-07 RELEASE_FREI, die einzigen grep-Treffer 'zustand: IN_ARBEIT' sind Prosa-Zitate). §7-Vorpruefung gefahren: (1) Basis d58b220e existiert und ist Vorfahr von HEAD 8343f206, git diff --stat d58b220e HEAD an nichtDarstellbar.ts/szene.ts/DreiDBereich.tsx/dachAusKontur.test.ts/dachMesh.ts = leer; (2) Scope-Dateien content-identisch mit HEAD (git show HEAD:<pfad> | diff — 6x IDENTISCH; die MM/D/??-Eintraege im git status sind die bekannten Index-Phantome der A-07-Klasse); (3) Suite an der Basis SELBST gefahren: 1689/1689 (tests 1689, pass 1689, fail 0); (4) Rot-Lage ZWEIMAL selbst gemessen: l-shape OHNE anbau -> dachMeshWelt wirft NICHT, dreiecke.length=0, dachflaechen()=0, nichtDarstellbareDaecher=[] — der Melder ist stumm, exakt der Blatt-Befund. MESSBEFUND fuer den Bau (vor der ersten Aenderung festgehalten): l-shape MIT anbau liefert dreiecke=10 bei dachflaechen()=0 und wird heute korrekt NICHT gemeldet (dachflaechen ist der Traegerflaechen-Filter, walm ebenso 0) — die neue Leer-Bedingung darf darum NICHT an dachflaechen==0 ALLEIN haengen, sonst meldet sie zeichenbare Daecher; sie haengt an dreiecke==0 und fragt dachflaechen als zweiten Zeugen konjunktiv (A-10-5 Mutation 3)."
prioritaet: P2
letztes_votum: "plan-pruefer 08.08. (1. DoR-Runde): ENTWURF bleibt, ZWEI kleine Punkte — sonst das bisher SAUBERSTE Erstblatt der Gruppe: basis_sha, §5-Block, Wiederverwendung, Erstnutzer, Rueckweg, Nicht-Ziele ALLE beim ersten Schnitt da (das Muster 'dritter Auftrag ohne §5-Block' ist damit gebrochen — gehoert in die Prozesspruefung als Gegenbeleg). Rot-Lage A-10-1 SELBST strukturell verifiziert: nichtDarstellbar.ts faengt ausschliesslich DachGeometrieUngueltig-Wuerfe (try/catch Z.42-48), ein leeres Ergebnis ohne Wurf erreicht gefunden.push nie — dazu die dreifach unabhaengigen dynamischen Belege (9e97d274, e0fae829, E4b in b29bb79d). Sichtkette korrekt HIER verortet (A-10-4 mit Anker-Regel und browser-buehne.sh als Prozessbindung) statt in A-05. must_preserve A-10-3 sauber."
offene_akzeptanz:
  - "Punkt 1: A-10-2 (Gegenprobe) ist an der Basis GRUEN (heute wird gar nichts gemeldet, also auch kein Flaechen-Dach) — nach dem stehenden Muster (A-01-2, A-02-1, A-08-2) als must_preserve-KONTROLLE kennzeichnen und von der Rot-Pflicht ausnehmen, sonst verletzt das Blatt 'kein Kriterium bereits erfuellt'."
  - "Punkt 2: Konfliktpruefungs-Zeile fehlt (§5) — eine Zeile genuegt: A-04 ist IN_ARBEIT auf scripts/*, A-07/A-09 warten auf commit-pruefen.sh — KEINE Beruehrung mit szene.ts/DreiDBereich.tsx; A-10 darf parallel. EMPFEHLUNG (kein Blocker): eine Mutationszusage (neue Bedingung entfernt -> A-10-1-Zusage faellt) nach dem Vorbild A-08-6, damit die Bedingung nicht stumm entfernbar ist."
votum_bereit: "plan-pruefer 10.08. (2. Runde nach 9cecc6be): BEREIT — beide Punkte plus die Empfehlung eingearbeitet und selbst geprueft: A-10-2 als must_preserve-KONTROLLE mit sauberer Begruendung, Mutationszusage A-10-5 aufgenommen (drei Mutationen), Konfliktpruefung selbst nachgemessen — dabei hat der Planner ZWEI UNGENAUE ANGABEN AUS MEINER DoR-NOTIZ korrigiert (A-04 baut buehnen-waechter.sh, nicht pauschal 'scripts/*'; plus die A-01/szene.ts-Herkunftszeile) — richtig so, Messung schlaegt Notiz, auch meine. EINE KORREKTUR AN SEINER FOLGERUNG: 'A-10 darf PARALLEL laufen' gilt nur fuer DATEIEN — §3 sagt woertlich 'hoechstens einen Auftrag IN_ARBEIT gleichzeitig' (Z.85). A-10 ist BEREIT, zieht aber erst, wenn kein anderer Auftrag IN_ARBEIT ist. Prozesspruefung-02 ist gelaufen (850aafd5) — die §13-Schranke vor Auftrag 11 ist damit bedient."
naechster_schritt: "Warteschlange nach §3: A-04 baut JETZT (aeltester BEREIT, Claim steht), danach A-07 (Tor, sobald Planner-Nachzuege da) -> A-09 -> A-10. Der Generator zieht A-10, sobald die Schlange ihn erreicht und kein Auftrag IN_ARBEIT ist."
claim_abnahme: "evaluator (Erstinstanz) 10.08. 19:3x: Abnahme A-10 GECLAIMT, VOR dem Pruefstand. Ich pruefe NICHT: es gibt noch keinen Commit. Gemessen: zwei Scope-Dateien liegen UNCOMMITTET im Arbeitsbaum (nichtDarstellbar.ts, dachAusKontur.test.ts), das Blatt traegt keinen §11-Bericht, der Datensatz keinen pruef_sha, Zustand steht auf IN_ARBEIT. §18 verbietet das Pruefen eines bewegten Arbeitsbaums statt eines Commits - und §4 verlangt einen EXAKTEN Commit. Ich beginne, sobald CODE_FERTIG mit Pruef-SHA steht; der Claim haelt die Station bis dahin frei."
evaluator_votum: "evaluator 10.08.: ABGENOMMEN an 47c0aa73, Fehlerklasse KEINE. Selbst gefahren: tsc 0, Suite 1692/1692, Elter 165239e5 1689/1689, Rot am Elter fuer A-10-1 und A-10-5-ZEUGEN (A-10-2 ist die deklarierte must_preserve-Kontrolle), drei Mutationen aus A-10-5 alle gefangen, Bundle frisch gebaut und byte-gleich (md5 57314651). A-10-4 mit eigener Browserabnahme: Waechter zuerst (A-04-Erstnutzerpflicht), Buehne ueber browser-buehne.sh mit Nachweis ticket_testing am Kindprozess, Objekt 10229 / Dokument 36 / roofType l-shape, Expertenmodus und 3D - der Hinweis ist in 1440, 1024 und 375 IM FENSTER sichtbar, Screenshot gesichtet. Mein Messfehler offengelegt: der erste Lauf blieb in 2D, dort ist das role=status-Element 0x0, und ich stand kurz davor daraus einen P1 zu machen - der Melder gehoert zum 3D-Renderer. Testdaten: eigener Nutzer evaluator-a10@example.test id 269 in ticket_testing angelegt, NICHT geloescht (§15)."
release_vermerk: "release-pruefer 10.08. (frische Instanz): RELEASE_FREI an 47c0aa73 (Bau dbb7ff66, Abnahme f6909653) — §10-Abschnitt mit allen Rohbelegen im Blatt. SELBST GEMESSEN an HEAD ccf9292c: Kette ce1ff7d5 -> 5fc9c9e2 -> dbb7ff66 -> 47c0aa73 -> 907a6117 -> f6909653 -> HEAD, jeder Uebergang merge-base --is-ancestor Exit 0. Suite am HEAD selbst: npm run test:hausplaner 1692/1692, fail 0. Scope exakt drei Dateien (nichtDarstellbar.ts +29, dachAusKontur.test.ts +67, Bundle als §5-Block); Content-Diff der Scope-Dateien 47c0aa73..HEAD leer (Index-Phantome zaehlen nicht); Beifang log 907a6117..HEAD auf resources/ und public/hausplaner/ LEER. Bundle selbst nachgebaut: md5 57314651a743ef689b0d788c23db7493 vor und nach byte-gleich. Die drei deklarierten Abweichungen gewuerdigt, je kein Befund: (1) Konjunktiv-Bauform Z.63 selbst gelesen, vom Evaluator ueber M3 + A-10-2-Kontrolle (Testzeilen 262-278, scharfer Fall l-mit-anbau) geprueft; (2) Testdatenzustand selbst gemessen via artisan --env=testing: db ticket_testing, user 268=0, 269=0, example.test-Nutzer 0, doc 36 vorhanden (revision 2, roofType l-shape, total 1) — Raeumung 09bc9ef7 auf Yamas Freigabe, doc 36 BEWUSST erhalten als einzige l-shape-Vorlage; (3) Sichtkette im Votum belegt (1440/1024/375 im Fenster sichtbar, Waechter-Vorlauf, ticket_testing am Kindprozess). Rueckweg: git show dbb7ff66 | git apply --check -R Exit 0, kein Datenpfad, git revert genuegt. Keine offenen P0/P1. OFFEN AN YAMA: Veroeffentlichung genehmigen (§10). Sicherungs-Push fork nach v1.2-Vertretung: Ergebnis unten."
push_vermerk: "release-pruefer 10.08.: Sicherungs-Push nach v1.2-Vertretung VERSUCHT (git push fork auto/hausplaner-integration) — von der Umgebung VERWEIGERT (Berechtigungssperre der Sitzung, kein git-Fehler; der Befehl kam nie bei git an). Dieselbe Sperre wie beim A-04-Push am selben Tag. ENV-HINWEIS, kein Blocker fuer RELEASE_FREI: der RELEASE_FREI-Stand 5f7043bc liegt damit weiter NUR lokal — ungepushte verifizierte Arbeit ist kein Backup. Push bitte durch Yama oder eine Sitzung mit Push-Recht nachholen."
```
---

## RELEASE_FREI — A-11 (Ball bei Yama)

```yaml
auftrag: A-11
titel: "Commit-Tor: die Rolle kommt aus der Umgebung und wird der Botschaft vorangestellt - fehlt sie, gibt es keinen Commit"
datei: docs/auftraege/aktiv/A-11-rollenmarke-im-tor.md
zustand: VEROEFFENTLICHT
ballbesitz: yama
release_vermerk_stamm: "release-pruefer (Stamm-Instanz) 10.08.: §10 an der Abnahme efe38d1d/28760966 selbst gefahren — Kette Vorfahr, Scope exakt 2 Dateien (Tor +35, Suite +130), Produkt-Code seit 2e7b58fc unberuehrt, bash -n OK, Tor-Suite 61/61 (mit TICKET_ROLLE), Waechter 7/7. Das unabhaengige §10 der frischen Instanz (6a9ea9ab-Klasse: 6a9ea6ab) deckungsgleich als Zweitbeleg; ihr verweigerter Push (f26ed034) hier nachgeholt. main-FF unmittelbar nach diesem Statuscommit, Kandidat = dieser Commit. Naechster Zustand BETRIEBSBESTAETIGT gehoert Yama."
release_vermerk: "release-pruefer 10.08.: RELEASE_FREI an 28760966 (§10-Abschnitt im Blatt, alles selbst gemessen). Kette 1dee4771 -> ffd06c1a -> b0f4c444 -> 28760966 -> 63c83a53 -> efe38d1d -> HEAD 5823ada0: sechsmal is-ancestor exit 0. Tore am HEAD selbst: Suite 61/61, bash -n exit 0, node --check exit 0. Release-Diff: git show b0f4c444 --stat = exakt 2 Dateien (+165/-0), EIN Hunk @@ -49,6 +49,41 an der Botschaft-Annahme (direkt nach BOTSCHAFT-shift Z.51) — Commit-Aufruf und A-07/A-08/A-09-Zonen unangetastet. Drift seit Bau: git log b0f4c444..HEAD -- scripts/ = 0 Commits, Tor-md5 e5fece55 = Bau-Stand. Rueckweg: git show b0f4c444 | git apply --check -R exit 0; Blatt-Aussage gewuerdigt und bestaetigt: der Rueckweg ist sogar OHNE Revert eine Zuweisung (TICKET_ROLLE setzen ueberbrueckt die Sperre) — doppelt vorhanden, kein Datenpfad. Entdeckungs-grep seit Bau: 0 von 11 Commits unmarkiert. Mitteilung an alle Rollen unter der Tafel per grep bestaetigt (Variable+Form+Beispiel). Zwei Evaluator-Randnotizen ins Protokoll, kein P0/P1: form-echte Nicht-Rollen-Praefixe (docs:/fix:) fallen kuenftig als WIDERSPRUCH — offene Planner-Entscheidung ob zulaessige Marken; Trimm-Unschaerfe des Entdeckungs-greps ist Fehlalarm-Richtung, kein stilles Loch. Realtest lebend: Blatt-Commit 4746f59b ging OHNE Praefix mit TICKET_ROLLE=release-pruefer ins Tor und traegt die Marke 'release-pruefer: ' vom Tor — ebenso dieser STATUS-Commit. Sicherungs-Push nach v1.2-Vertretung: git push fork auto/hausplaner-integration — Ergebnis siehe push_vermerk. Ball bei Yama: main-Veroeffentlichung."
basis_sha: 229ad0be
prioritaet: P1
letztes_votum: "evaluator (frische Instanz) 10.08.: ABGENOMMEN an 28760966, Fehlerklasse KEINE — alles selbst gemessen, Votum mit Rohausgaben im Blatt. Suite 61/61 am HEAD, Basis-Gegenlauf 50/50 im Worktree an def5d826 (= b0f4c444^; deklarierte Basis bc1470bc gegen def5d826: scripts/-diff 0 Zeilen). Acht Kriterien je mit Wegwerf-Repo-Probe: exit 2 ohne/mit leerer Marke (stderr nennt Variable+Form woertlich), Praefix-Setzung, byte-Identitaet per od, WIDERSPRUCH inkl. Instanznummern-Kante, Formfehler-Quartett, evaluator-2 gueltig, Mehrzeiler per cmp byte-identisch MIT unmarkierter generator:-Rumpfzeile ohne Fehlalarm (schaerfer als die Suite-Zusage). Gegenprobe: ZWEI eigene Mutationen im Worktree am Pruef-SHA (Widerspruch entfernt -> 58/61 exakt die drei Widerspruchs-Zusagen; fehlende Marke nur gewarnt -> 59/61 beide A-11-1), Ruecknahme je md5 e5fece559500d5c90869cf6c2ada40da, Abschluss 61/61. Bestand: 50 Zusagen namensgleich im 61er-Lauf (comm -23 = 0), Bau-Diff am Tor EIN Hunk an der Botschaft-Annahme, A-07/A-08/A-09-Zonen unangetastet. Entdeckungs-grep seit Bau: 0 unmarkierte. Fuenf Abweichungen gewuerdigt, alle gedeckt; zwei RANDNOTIZEN ohne P0/P1 (form-echte Nicht-Rollen-Praefixe wie docs:/fix: fallen kuenftig als WIDERSPRUCH — Richtung Blockade, ob sie zulaessige Marken werden ist Planner-Sache; Trimm-vs-Verbuchung ist Fehlalarm-Richtung des greps, kein stilles Loch). Ehrliche Grenze: der Realtest an b0f4c444 ist rueckwirkend nicht unabhaengig beweisbar (A-11-3 laesst selbst getippte Praefixe byte-identisch) — der lebende Beweis ist DIESER Commit, der ohne Praefix mit TICKET_ROLLE=evaluator ins Tor ging. DoR-Votum des Plan-Pruefers dazu in der git-Historie dieses Feldes."
claim_release_a11: "plan-pruefer 10.08.: A-11 ABGENOMMEN (efe38d1d) — Release-Station mit frischer Instanz besetzt. Claim VOR dem Start."
ballwechsel_bestaetigt_a11: "plan-pruefer 10.08.: A-11-CODE_FERTIG-Meldepflichten geprueft — Kette ffd06c1a (IN_ARBEIT nach §3-Wartezeit auf W-02/1, sauber) -> b0f4c444 (Bau, exakt 2 Dateien) -> 28760966 (Pruef-SHA) -> 63c83a53. Die AUFLAGE aus dem BEREIT-Votum ist ERFUELLT (Mitteilung an alle Rollen unter der Tafel). DIE ROLLENMARKE IST AB 28760966 LIVE — auch fuer mich: dieser Commit traegt TICKET_ROLLE=plan-pruefer. Ball beim EVALUATOR. FUER SEINE PRUEFUNG: der Realtest (Marke an b0f4c444 stammt vom Tor) ist elegant, aber selbst nachzumessen; die fuenf deklarierten Abweichungen wuerdigen (insb. Trimm-Bewertung vs. byte-identische Verbuchung — eine fuehrend-eingerueckte markierte Zeile zaehlt im Entdeckungs-grep als unmarkiert, das ist eine dokumentierte Unschaerfe der Dauerkontrolle); Beifang-Offenlegung 7f592b20 gegenlesen."
claim_abnahme_a11: "plan-pruefer 10.08.: Evaluator-Station fuer A-11 mit frischer Instanz besetzt. Claim VOR dem Start."
claim_bau_a11: "plan-pruefer 10.08.: A-09 ABGENOMMEN, §3-Schlange frei — Generator-Station fuer A-11 (letzter Tor-Bau) mit frischer Instanz besetzt. Claim VOR dem Start."
zaehlfrage_entschieden: "plan-pruefer 10.08.: A-11 zaehlt als AUFTRAG 1 DER GRUPPE 2. Begruendung: §13 zaehlt ab der ersten Vorlage beim Plan-Pruefer; Gruppe 1 ist mit zehn Auftraegen voll und ihre Prozesspruefung IST durchgefuehrt (850aafd5 + Anteile) — damit ist die §13-Schranke vor Auftrag elf bedient. Der ausstehende Zaehler-RESET (B3-Bedingung) betrifft die Mechanik des Zaehlers, nicht die Zugehoerigkeit: Auftraege zwischen Pruefung und B3-Bau duerfen nicht aus der Statistik fallen ('schlechte Plaene verschwinden nicht'). Bis B3 steht, wird Gruppe 2 von Hand gezaehlt — beginnend mit diesem Blatt."
auflage_bereit: "EINE Auflage fuer den Bau (kein Restpunkt am Blatt): die CODE_FERTIG-Meldung MUSS die sofort blockierende TICKET_ROLLE-Pflicht als Mitteilung an ALLE Rollen in STATUS.md tragen (Variable, Form, Beispiel) — das Blatt benennt die Gefahr selbst: sonst laeuft die erste Rolle nach dem Bau in eine unerwartete Sperre."
beginn_bau: "generator (frische Instanz) 10.08.: IN_ARBEIT VOR der ersten Scope-Aenderung (§3). §3 gewahrt: W-02/1 stand bereits IN_ARBEIT, als ich antreten sollte — ich habe GEWARTET, bis 58342f47 CODE_FERTIG setzte; jetzt ist A-11 das einzige IN_ARBEIT. Bau-Basis bc1470bc (scripts/ seit meiner Rot-Messung an 0fef1a56 unveraendert: diff 0 Zeilen). Rot-Lagen SELBST gemessen: Rollenpruefung im Tor 0 funktionale Treffer (2 grep-Treffer 'Rolle' sind Kommentare Z.93/Z.490); Wegwerf-Repo: ohne TICKET_ROLLE exit=0 und Commit lief (A-11-1 rot), mit TICKET_ROLLE=generator Betreff unveraendert ohne Praefix (A-11-2 rot); Suite 50/50 an der Basis. Zeilendrift: Botschaft-Annahme weiter Z.51, Commit-Aufruf jetzt Z.508 — Bauvorgabe gilt sinngemaess (Marke am Kopf, Commit-Aufruf und alles danach unangetastet). BEIFANG-OFFENLEGUNG: meine Tafelzeile (IN_ARBEIT) wurde waehrend meiner Vorbereitung vom parallelen STATUS-Commit 7f592b20 mitgenommen, BEVOR ich IN_ARBEIT wirklich gesetzt hatte — die Zeile stand damit frueher im Log als dieser Datensatz-Umschwung. Von mir gemessen und hier deklariert; W-02/1 (58342f47) und A-12-Einreihung haben die Zeile bereits als gueltig gelesen."
bericht_generator: "generator 10.08.: CODE_FERTIG. Basis bc1470bc -> Bau b0f4c444 (exakt 2 Dateien: Tor-Einbau Z.53-85 direkt nach der Botschaft-Annahme, Suite-Anhang; Commit-Aufruf und A-07/A-08/A-09-Zonen content-unangetastet) -> Pruef-SHA 28760966 (§11-Bericht im Blatt). Alle acht Kriterien mit Beleg: Suite 50/50 an der Basis -> 61/61 am Bau (11 neue Zusagen, nur Anhang, kein Bestandstest beruehrt); vier Mutationen nach A-11-7 alle gefallen (Pruefung weg FAIL 10 · nur gewarnt FAIL 2 · Doppelungs-Schutz weg FAIL 1 · Widerspruch weg FAIL 3), md5 e5fece559500d5c90869cf6c2ada40da vor/nach JEDER Ruecknahme identisch, Abschluss 61/61. Wegwerf-Repo-Wirkprobe 5 Faelle (fehlt/ohne Praefix/identisch/fremd/Formfehler) wie spezifiziert. Realtest: die Bau-Botschaft ging OHNE Praefix ins Tor, die Marke 'generator: ' an b0f4c444 stammt vom Tor. AUFLAGE ERFUELLT: Mitteilung an alle Rollen steht als eigener Abschnitt direkt unter der Tafel. Fuenf deklarierte Abweichungen im Blatt (u. a. Marken-Definition nur in Form '<marke>: '; Trimm-Bewertung vs. byte-identische Verbuchung; Beifang-Offenlegung 7f592b20). FUER DEN EVALUATOR: Rot-Basis fuer Gegenlauf ist bc1470bc (Suite dort 50/50, Tor committet ohne Marke); die Suite laeuft ab dem Bau NUR mit gesetzter TICKET_ROLLE (zentral im Testkopf: probe)."
push_vermerk: "release-pruefer 10.08.: Sicherungs-Push versucht (git push fork auto/hausplaner-integration, nach v1.2-Vertretung, NIE main/Tags/force) — vom Umgebungs-Berechtigungssystem VERWEIGERT, bevor git lief. ENV-Hinweis, kein Repo- oder Remote-Fehler: die Verifikationslage ist unberuehrt, aber der Branch-Stand ab 4746f59b liegt NICHT auf fork — die verifizierte Arbeit hat weiterhin keine Kopie ausserhalb der Maschine (Repo-Aufsicht: ungepushter Rueckstand). Yama oder eine Sitzung mit Push-Erlaubnis moege git push fork auto/hausplaner-integration nachholen."
naechster_schritt: "Yama: main-Veroeffentlichung genehmigen (§10: erst nach RELEASE_FREI); vorher/dabei den verweigerten Sicherungs-Push nachholen; danach reale Zielstand-Pruefung -> BETRIEBSBESTAETIGT"
```
---

## BEREIT — W-01/1 (Register-Strang, Einreihung bei Yama)

```yaml
auftrag: "W-01/1"
titel: "Die sieben Blaetter von W-01 aus dem VORHANDENEN fangKern.ts ableiten"
datei: docs/auftraege/aktiv/W-01-fang-beschreiben.md
zustand: ABGENOMMEN
ballbesitz: release-pruefer (SPEC-Rest W-01/1-6 beim planner)
basis_sha: 32f83a6f
prioritaet: P1
letztes_votum: "plan-pruefer 10.08. (1. DoR-Runde, BEREIT beim ersten Review — das dritte): JEDE Blatt-Behauptung selbst gemessen: Basis existiert · fangKern.ts exakt 276 Zeilen, 11 Exporte wie gelistet · toolRegistry traegt KEIN Fang-/Raster-/Snap-Werkzeug (der einzige grep-Treffer ist das Wort Anfang in einem Treppen-Hilfetext — die Messung des Planners hielt einer schaerferen Probe stand) · REGISTER fuehrt W-01 auf LEER und nennt fangKern.ts NIRGENDS (0 Treffer — der Beinahe-Doppelbau war real) · Rot-Lage zaehlbar bestaetigt: Platzhalter in 3-FORMELN.md (4) und 1-ZWECK.md (1). Das Blatt selbst ist vorbildlich: Anschluss- statt Bauauftrag nach gefahrener Anbindungsmessung, Stufentrennung (BESCHRIEBEN vor GEBAUT), A-10-Lehre als Pflichtfrage in 7-GRENZEN, must_preserve resources byte-identisch, Entdeckungssignal ist der erste Stufe-2-Bauversuch. EIN HINWEIS (kein Restpunkt): die REGISTER-Zeile erwartet auch F-004, die Kandidatenliste des Blatts nennt sie nicht — W-01/1-3 klaert das ohnehin AM CODE, der Bericht soll die Abweichung ausdruecklich aufloesen. ZUR FORM-QUELLE: W-07 dient nur als FORM-Muster; dessen inhaltlicher Befund (db1dc3b6: anderer Dachweg als die Insel) infiziert W-01 nicht."
warteschlange_vermerk: "§3: derzeit ist KEIN Auftrag IN_ARBEIT (A-10 ist CODE_FERTIG, Abnahme laeuft als Pruefung parallel). Die EINREIHUNG der W-Reihe relativ zur Tor-Reihe (A-09 -> A-11) ist keine Plan-Pruefer-Entscheidung: das Blatt selbst legt sie Yama vor ('Yama entscheidet ueber die Freigabe der Gruppe'). Bis dahin gilt die bestehende Tor-Reihe; gibt Yama die W-Gruppe frei, darf W-01/1 als naechstes IN_ARBEIT (reine Doku, kuerzester Auftrag, keine Dateiberuehrung mit irgendwem)."
naechster_schritt: "Yama: Freigabe der W-Gruppe und Einreihung (W-01/1 vor oder nach A-09/A-11). Danach zieht der Generator entsprechend §3."
claim_abnahme: "evaluator (Erstinstanz) 10.08. 21:2x: Abnahme W-01/1 GECLAIMT vor dem Pruefstand. Reihenfolge nach Rueckstand: W-01/1 zuerst (liegt seit 20:51), dann W-02/1. HINWEIS zur Auffindbarkeit: beide standen NUR in der Tafelzeile auf CODE_FERTIG/Evaluator, das Zustandsfeld sagte weiter BEREIT bzw. generator - ich lese ab jetzt BEIDE Orte und lasse den neueren Schreibvorgang gewinnen."
evaluator_votum: "evaluator 10.08.: NACHBESSERN an 04f78b73, Klasse BEWEIS. Zwei P1 offen. W-01/1-3 verlangt Zeilennummern in fangKern.ts - gemessen 0 Treffer in allen sieben Blaettern, der Bericht setzt Funktionsnamen an ihre Stelle und nennt das gruen. W-01/1-8 verlangt Befehl MIT Ausgabe im IN_ARBEIT-Commit an beiden Orten - b41f9177 traegt Prosa, 0 Befehlszeilen, 0 Ausgabewerte; das Verfahren ist besser (Schranke im Skript), der Beleg fehlt trotzdem. ERFUELLT sind 1, 4, 5 und 7, jede Zahl selbst nachgezaehlt (fangKern.ts 276 Zeilen, 11 Exporte - beide stimmen), resources/** byte-identisch, Suite 1692/1692. Der SPEC-Anteil W-01/1-6 (1689 gegen 1692) gehoert dem Planner und ist von ihm in 7c3408e2 bereits anerkannt - nach §12.1 zuerst zu beheben. NICHT als Befund gefuehrt: das eine = in 3-FORMELN Z.17 ist das Zitat der Formelsammlung, ohne das die F-003-Abweichung unerklaerbar waere."
evaluator_votum_runde2: "evaluator 10.08.: ABGENOMMEN an 5823ada0, Fehlerklasse KEINE. Beide Befunde behoben und in beide Richtungen geprueft. Befund 1: statt 0 Zeilenangaben jetzt 15, ich habe ALLE einzeln im Code geoeffnet - jede trifft was sie behauptet, keine laeuft ins Leere (Datei 276 Zeilen); die Rangfolge als Zeilenkette 128-143-163-171-182/185-192-195 ist die bessere Antwort, weil sie im Code als Reihenfolge der Rueckgaben und nicht als Tabelle steht. Befund 2: der IN_ARBEIT-Commit 51fab811 traegt jetzt beide Befehle mit Ausgabe (je 0) und die Gegenprobe nach dem Setzen; ich habe die zwei Befehle selbst nachgefahren. §12.4 erfuellt: die vorher gruenen Kriterien erneut gemessen - Platzhalter 0, resources/** unveraendert, Register 5 Treffer, Suite 1692/1692. NICHT erledigt bleibt W-01/1-6 mit seinen woertlichen 1689 gegen gemessene 1692; das ist SPEC, gehoert dem Planner und blockiert nach §12.5 die Abnahme nicht - der Bauende kann eine Zahl nicht erfuellen, die schon bei der Blatt-Erstellung ueberholt war."
```
---

## BEREIT — W-02/1 (Warteschlange hinter W-01/1)

```yaml
auftrag: "W-02/1"
datei: docs/auftraege/aktiv/W-02-wand-beschreiben.md
zustand: VEROEFFENTLICHT
ballbesitz: yama
release_sha: "56c77ae6"
release_vermerk: "release-pruefer (Stamm-Instanz) 10.08.: §10 an der Abnahme b2fd68b2/e23440d1 — Kette Vorfahr, reiner Doku-Scope (Werkbank-Blaetter + Register; resources/public/app/scripts seit c8191292 = 0 Treffer), Tor-Suite 61/61 als Regressionskontrolle, Scans leer. main-FF c8191292..56c77ae6 auf fork UND backup-private. OFFENGELEGT: der Zustandseintrag kam wie bei A-09 einen Commit NACH dem Push — dasselbe Muster (Patch-Skript verfehlte den Block wegen Anfuehrungszeichen im auftrag-Feld, Push lief im selben Befehlsblock weiter). Zweiter Riss derselben eigenen Klasse; Konsequenz: Status-Patch und Push laufen ab jetzt in GETRENNTEN Befehlsbloecken, Patch-Verifikation dazwischen. Naechster Zustand BETRIEBSBESTAETIGT gehoert Yama."
basis_sha: 193681cd
letztes_votum: "plan-pruefer 10.08. (1. DoR-Runde, BEREIT beim ersten Review — das vierte): Messungen EXAKT bestaetigt: wallGeometry 317 / wandFlaeche 238 / wandaufbau 72 / linienBauteile 167 Zeilen aufs Zeichen; die Ausschluesse sind belegt (wandaufbau traegt berechneUWert = Bauphysik, linienBauteile 10x Schneefang = Dachzubehoer) und W-02/1-6 zwingt sie namentlich ins Blatt — die Matrix-Selbstkorrektur des Planners ist der wertvollste Teil des Schnitts. Registry 'wand' vorhanden. Rot-Lage zaehlbar (meine Zaehlung 5, Blatt 8 — Muster-abhaengig, beide > 0, nicht tragend; der Bericht nennt sein Muster). HINWEIS wie bei W-01: die REGISTER-Zeile nennt F-030 aber nicht F-003, die Blatt-Kandidaten beides — W-02/1-3 klaert am Code, der Bericht loest die Abweichung ausdruecklich auf. REGISTER.md-Beruehrung mit W-01/1 durch Reihenfolge + §3 geloest."
naechster_schritt: "Nach W-01/1 (Reihenfolge im Blatt); Einreihung der W-Gruppe insgesamt bei Yama"
claim_abnahme: "evaluator (Erstinstanz) 10.08. 21:2x: Abnahme W-02/1 GECLAIMT vor dem Pruefstand. Reihenfolge nach Rueckstand: W-01/1 zuerst (liegt seit 20:51), dann W-02/1. HINWEIS zur Auffindbarkeit: beide standen NUR in der Tafelzeile auf CODE_FERTIG/Evaluator, das Zustandsfeld sagte weiter BEREIT bzw. generator - ich lese ab jetzt BEIDE Orte und lasse den neueren Schreibvorgang gewinnen."
evaluator_votum: "evaluator 10.08.: NACHBESSERN an 801e2daa, EIN P1, Klasse BEWEIS. Erfuellt sind -1, -3, -4, -5, -6, -7 und -8; besonders -3: das Blatt nennt 14 Zeilenangaben und ich habe alle neun eindeutigen einzeln im Code geoeffnet - jede trifft eine tragende Zeile, keine zeigt ins Leere. Zwei Lehren aus W-01 sind sichtbar angewandt: -7 nennt keine feste Suite-Zahl mehr, -3 verlangt Zeilennummern statt Namen. Offen ist W-02/1-9: der IN_ARBEIT-Commit 35e90eb8 traegt 0 Befehlszeilen, 0 Ortsangaben, 0 Ausgabewerte - wortgleicher Ausfall wie W-01/1-8 in b41f9177 und damit die ZWEITE Wiederholung derselben Klasse, §13-Sofortausloeser. Offengelegt: waehrend der Messung lag eine uncommittete 41-Zeilen-Aenderung an 3-FORMELN.md im Baum; ich habe sie nicht angefasst und messe den Bau-Commit."
evaluator_votum_runde2: "evaluator 10.08.: ABGENOMMEN an e23440d1, Fehlerklasse KEINE. Der Befund W-02/1-9 ist behoben: 5c06f5ca traegt beide Befehle mit Ausgabe (je 0) und die Gegenprobe nach dem Setzen, selbst nachgefahren. Damit ist die Klasse, die an W-01/1-8 und W-02/1-9 zweimal riss, an beiden Auftraegen geschlossen. §12.4 erfuellt: Platzhalter 0, alle neun Fundstellen ERNEUT im Code geoeffnet und unveraendert richtig, resources/** unveraendert, Suite 1692/1692, Register 11 Treffer. Das eine = in 3-FORMELN ist Nord = +y, eine Achsenfestlegung und keine Formel (atan2 und sqrt je 0) - gemeldet, nicht gezaehlt, in beiden Runden gleich gemessen. Mein Befund war der kleinere Teil: der Bauende hat aus meiner Offenlegung der 41 uncommitteten Zeilen den eigentlichen Fall gemacht und ihn in einen eigenen Commit gezogen."
```
---

## In Planprüfung — W-13/1

```yaml
auftrag: "W-13/1"
datei: docs/auftraege/aktiv/W-13-auswahl-beschreiben.md
zustand: ENTWURF
ballbesitz: planner
basis_sha: 193681cd
letztes_votum: "plan-pruefer 10.08. (1. DoR-Runde): ENTWURF bleibt, EIN Mini-Rest — sonst BEREIT-reif: Modul-Zeilenzahlen exakt (98/71/77/75 = 321, editierGeometrie 75), der W-14-Ausschluss belegt (versetzen/spiegeln), Registry 'auswahl' da, BEIDE Toleranzbegriffe verifiziert (toleranzAusZoom in fangKern, toleranzInWelt in trefferSuche — der Beruehrungsfund ist echt und die Benennen-statt-zusammenlegen-Regel genau richtig), Platzhalter-Rot zaehlbar. DER REST: die 'EINE Zusage' in P1-Kriterium W-13/1-7 ist zaehlweise-abhaengig — meine Messung findet NULL dedizierte Auswahl-Testdateien und DREI erwaehnende (toolKatalog, activation, pan). Die Substanz (duenne Absicherung) haelt in jeder Zaehlweise, aber eine Zahl, die woertlich in einem P1 steht, muss eine definierte Messweise haben — sonst traegt das fertige Blatt eine anfechtbare Aussage (Zeitbomben-Klasse aus A-09). Ein Satz: Zaehlweise definieren (dediziert vs. erwaehnend), Zahl danach nachmessen und in Kriterium + Befund-Zeile angleichen."
offene_akzeptanz:
  - "Mini-Rest: Zaehlweise der Zusagen-Abdeckung in W-13/1-7 und befund_bestand definieren und die Zahl daran nachmessen (meine Messung: 0 dedizierte / 3 erwaehnende Dateien)."
naechster_schritt: "Planner zieht den einen Satz nach, dann setzt der Plan-Pruefer BEREIT; Reihenfolge W-01/1 -> W-02/1 -> W-13/1 bleibt"
```
---

## ABGENOMMEN — A-12 (Messauftrag F-026; Ampel 🟢 bestaetigt, Ball beim Planner)

```yaml
auftrag: A-12
titel: "Ein L-Grundriss mit F-026 rechnen, Ergebnis ansehen, Ampel gelb -> gruen oder rot"
datei: docs/auftraege/aktiv/A-12-f026-ausfuehren.md
zustand: ABGENOMMEN
ballbesitz: planner (KORRIGIERT von 'release-pruefer' — siehe doppel_launch_a12; ein Messauftrag hat keinen Release-Kandidaten)
basis_sha: d1d716c8
mess_sha: 239a163e
mess_sha_vorgaenger: 3e7e19d6
pruef_sha: 752174d1
bericht_commits: "92310844 (Bericht) · 752174d1 (E1-Messung + §11-Kurzstand)"
prioritaet: P1
lieferung: docs/BERICHT-A-12-f026.md
uebernahme: "generator 11.08. (frische Instanz, Vertretung durch den Release-Pruefer angestossen): Ich habe den LAUFENDEN Auftrag uebernommen und IN_ARBEIT NICHT erneut gesetzt — die Vorgaenger-Instanz hatte §3 und die §7-Vorpruefung sauber gesetzt und belegt (4e935e84) und starb danach am Wochenlimit. Doppel-Launch-Pruefung VOR dem ersten Schritt (Lehre P-02), mit Befehl und Ausgabe: git log --oneline --all | grep -i 'A-12' -> kein Bau-Commit einer zweiten Instanz; ls docs/BERICHT-A-12-f026.md -> No such file or directory. Uebernommen habe ich auch die untracked liegengebliebene Wegwerf-Probe (14612 B): gelesen, gegen die Attrappen-Regel geprueft, gefahren, restlos entfernt — ich verantworte sie ab hier selbst."
ampel_vorschlag: "🟢 — VORGESCHLAGEN, NICHT GESETZT (A-12-4: Evaluator bestaetigt, Planner traegt ein). Begruendung am Kriterium: es kam ein L-Dach mit VIER benannten Flaechen (main_N/main_S/ext_W/ext_E), zwei Firstlinien, einer benannten Kehle und einem benannten Grat heraus — gerechnet, nicht gelesen. Dazu gehoert eine Wortlaut-Korrektur am Formelblatt: F-026 ist dort als sechsschrittiges Kantentopologie-Verfahren beschrieben, gemessen wird das NICHT durchlaufen (0 Aufrufe der Topologie-Kette in :774-928, alle Aufrufe stehen in der React-Komponente). Was laeuft, ist fest verdrahtete Parametergeometrie fuer L und T."
befund_des_plan_pruefers_erledigt: "Der Befundblock 11.08. (A-12 ENV-unterbrochen, Wegwerf-Probe im Baum) ist sachlich erledigt: die Probe ist entfernt (ls -> kein Treffer; git log --all -- '*zzA12wegwerf*' -> 0), die Insel-Suite steht wieder auf 1692/1692 (mit Probe waren es 1695/1695 — der Zaehler-Befund ist damit zugleich belegt). Ich habe den fremden Block NICHT umgeschrieben (B5: fremder Inhalt in geteilter Datei); schliessen darf ihn, wer ihn gesetzt hat."
e1_messung: "E1 (Meldepflicht vor CODE_FERTIG, am COMMIT statt am Arbeitsbaum): git show HEAD:docs/BERICHT-A-12-f026.md | diff - <pfad> -> keine Ausgabe (Baum == Commit). Fuer docs/STATUS.md faellt die Messung unmittelbar nach diesem Statuscommit und geht mit den SHAs an den Evaluator. Zusaetzlich gegen den Mess-SHA statt nur gegen HEAD, weil HEAD waehrend des Laufs fuenfmal durch fremde Commits wanderte: git diff --name-only 239a163e..92310844 -- resources/ scripts/ -> 0."
must_preserve: "ALLE DREI RICHTUNGEN einzeln (Auflage 239a163e), fuer resources/ und scripts/: geaendert 0 (git diff --name-only HEAD) · hinzugefuegt 0 (git ls-files --others --exclude-standard) · entfernt 0 (git diff --diff-filter=D). Die alte Einweg-Messung haette die Wegwerf-Probe nicht gesehen."
in_arbeit_gesetzt: "generator 10.08. (frische Instanz, Claim claim_bau_a12): IN_ARBEIT gesetzt VOR der ersten Scope-Aenderung und VOR der Wegwerf-Probe (§3). §3-Beleg MIT Befehl und Ausgabe: grep -n 'zustand: IN_ARBEIT' docs/STATUS.md -> 4 Treffer (Z.945/973/1112 Prosa in in_arbeit_gesetzt-Feldern, Z.1232 Prosa im claim_bau_a12-Feld), KEIN Zustandsfeld traegt IN_ARBEIT. §7-Vorpruefung mit Befehl und Ausgabe: (1) git merge-base --is-ancestor d1d716c8 HEAD -> exit 0 (Basis ist Vorfahr von HEAD 3e7e19d6); (2) git diff --name-only d1d716c8..HEAD -- resources/ | wc -l -> 0 (die zu messende Insel-Geometrie ist byte-gleich zur Blatt-Basis; scripts/ wanderte durch die abgeschlossenen A-09/A-11-Bauten, ausserhalb meines Scopes); (3) Scope frei von fremden Aenderungen: git show HEAD:<pfad> | diff -q fuer STATUS.md, A-12-Blatt, FORMELSAMMLUNG.md, VORGEHEN.md -> alle 4 IDENTISCH; (4) Rot-Lage selbst gemessen: ls docs/BERICHT-A-12-f026.md -> No such file or directory, und die Ampel-Sperre steht woertlich (grep -n 'F-026' FORMELSAMMLUNG.md -> Z.302 Ampeltabelle 'noch nicht ausgefuehrt', Z.350 Formelblatt 🟡); (5) Fremdquelle exakt: wc -c -l dachdecker_pro_3d.tsx -> 2173 Zeilen, 132374 Byte (Blattangabe aufs Byte). Lieferung ist ein BERICHT (Muster A-05); Wegwerf-Probe am ueblichen Ort, vor dem Bericht restlos entfernt."
doppel_launch_a12: "OFFENGELEGT, nicht verschwiegen (Lehre P-02): A-12 ist von ZWEI Evaluator-Instanzen parallel abgenommen worden. Instanz A claimte im Feld claim_abnahme (19d8855b) und lieferte 171baafe; Instanz B (ich) wurde vom Plan-Pruefer im Feld claim_abnahme_a12 (6cd4a2b0) besetzt und arbeitete gleichzeitig. Ich habe den Doppel-Launch erst beim Commit gemerkt, weil 171baafe meine schon geschriebenen, noch ungesicherten Zeilen (die Ueberschrift dieses Blocks) als BEIFANG mitgenommen hat — dieselbe Klasse wie 58342f47/4307987b, hiermit richtiggestellt. INHALTLICH KEIN WIDERSPRUCH: beide Instanzen kommen unabhaengig auf ABGENOMMEN und bestaetigen 🟢 gebunden an die Wortlaut-Korrektur; Instanz A legt ihr Votum ins Auftragsblatt, ich meines ans Ende des Berichts, beide bleiben stehen. Instanz As Votum, ihren claim_abnahme und ihr evaluator_votum habe ich NICHT angefasst (B5: fremder Inhalt in geteilter Datei). EINE Zeile habe ich korrigiert und sage es hier laut: ballbesitz stand auf 'release-pruefer'. Ein Messauftrag liefert nur einen Bericht — es gibt keinen Release-Kandidaten, kein Bundle, keine Migration, nichts, was §10 pruefen koennte; der Rueckweg des Blatts sagt woertlich 'ein Bericht und zwei Ampel-/Statuszeilen, git revert genuegt'. Waere der Ball beim Release-Pruefer geblieben, haette die einzige echte Folgehandlung — die Ampel eintragen, den Verfahrenswortlaut berichtigen, den Weg entscheiden — KEINEN Eigentuemer gehabt. Darum: Ball beim PLANNER. Die beiden Messungen ergaenzen sich: Instanz A hat die Insel-Seite nachgerechnet und dabei ihren eigenen Aufbaufehler offengelegt (vorbildlich); ich habe zusaetzlich A-12-1 Punkt fuer Punkt, den Fremdcode-Flaechenlauf mit EIGENEN Attrappen und die Attrappen-Regel selbst nachgefahren. Fuer den Prozess bleibt der Befund stehen: zwei Claim-Felder in einem Block (claim_abnahme und claim_abnahme_a12) haben die Doppelbesetzung nicht verhindert, weil keine Instanz das Feld der anderen las."
letztes_votum: "evaluator 11.08. an 752174d1: ABGENOMMEN, alle sechs Kriterien erfuellt, Nicht-Ziele gewahrt, kein P0/P1 offen. Votum steht am ENDE von docs/BERICHT-A-12-f026.md (Abschnitt 'Evaluator-Votum A-12'). KERN-REPRODUKTION statt Nachlesen, mit EIGENEM Skript ausserhalb des Repos, eigenem Zeilenschnitt per expliziter Zeilennummer und EIGENEN Attrappen: die sechs Konturpunkte beider l-shape-Varianten (flat 64 m2 / pitched 112 m2, je geschlossen, 0 Selbstschnitte, genau 1 einspringende Ecke bei i=3), beide Grenzfaelle WB=8/WB=10, die vier Flaechenpolygone (main_N/main_S/ext_W/ext_E), 7 Pfetten, Kehl- und Gratsparren, Shoelace 167.234 sowie die komplette Insel-Seite (4 Flaechen, 2 Linien 3.945 m/26.341 Grad, dachMeshWelt 10 Dreiecke/5482 mm/167.246 m2, dachflaechen 0, dachGeometrie.dachFlaechen wirft, WB=8 lTBauGueltig=false) kamen BIS AUF DIE LETZTE STELLE identisch heraus. ATTRAPPEN-REGEL doppelt geprueft: gegengelesen (alle sechs legen nur ab, einzige Zahlenberuehrung ist die Ausgaberundung r3) UND mit neu geschriebenem eigenem Geruest gegengebaut — identische Zahlen, also misst der Bericht den Fremdcode und nicht sein Geruest. maxAbweichung=0 nachgebaut statt nachgelesen. Fundstellen-Stichproben alle bestaetigt (:807 cx, :861/:868, :872/:883, :127-131, :352-364, :377, 2173 Z./132374 B, this.-Zaehlung 10/10/7/5/3/2, THREE.=67). MUST_PRESERVE in allen drei Richtungen EINZELN, sechs Messungen: resources/ 0/0/0 und scripts/ 0/0/0; Wegwerf-Probe weg (ls exit 1) und in KEINEM Commit (git log --all 0); keine Kopie der Fremddatei im Repo (find leer). Insel-Suite SELBST gefahren: 1692/1692, Schema pass. AMPEL 🟢 BESTAETIGT — der Vorschlag wird von MEINEN eigenen Rohausgaben getragen (vier benannte Flaechen, zwei Firstlinien, Kehlsparren Links, Gratsparren Rechts), GEBUNDEN an die Wortlaut-Korrektur: gruen gilt nur fuer die woertliche Sperrbedingung 'L-Grundriss gerechnet und Ergebnis gesehen', NICHT fuer die Verfahrensbeschreibung FORMELSAMMLUNG:352-364 — die Kantentopologie-Kette wird nachweislich nicht durchlaufen (0 Aufrufe in :774-928, alle vier Aufrufe in der React-Schicht), kennt fuer l-shape keine Kantentypen und liefert grate=5/kehlen=1 gegen das tatsaechlich gebaute 1 Kehle/1 Grat. Ohne die Korrektur erlaubt 🟢 genau die Zitierung, die die Ampel verhindern sollte. GEWUERDIGT: die Messinstrumente wurden bewusst NICHT dem Fremdcode entnommen (sonst haette der Code sich selbst geprueft), und der Generator hat den Befund gemeldet, der seinen eigenen gruenen Vorschlag EINSCHRAENKT, statt ihn wegzulassen. NEBENBEFUNDE ohne Rot, von mir selbst geschlossen: (1) '(Rohausgabe 1d)' in Abschnitt 6 verweist auf eine Rohausgabe, die der Bericht nicht abdruckt — Zahl richtig, von mir erzeugt; (2) Abschnitt 9 nennt als Pruef-SHA 92310844 statt 752174d1 (der STATUS-Block fuehrt es korrekt); (3) die Schleife in Abschnitt 1 druckt eine unaufgeloeste Variable $pfad, Aussage stimmt dennoch, neu gemessen. Klasse waere BEWEIS, aber jeder betroffene Wert ist von mir selbst erzeugt — kein Nachweis mehr geschuldet."
votum_zuvor: "plan-pruefer 10.08. (1. DoR-Runde, BEREIT beim ersten Review): die Pruefbefehle des Blatts selbst gefahren — Ampel-Rot bestaetigt (F-026 traegt woertlich die Sperre Z.229/277 der FORMELSAMMLUNG), Fremdquelle EXAKT belegt (132374 Byte, 2173 Zeilen aufs Zeichen; :965 buildCompoundPitched-Signatur und :1137 die l/t-shape-Weiche woertlich verifiziert). Der Machbarkeitsbefund, der den Auftrag zweiteilt (Teil A triviale Arithmetik / Teil B klassengebunden mit 64 THREE.-Vorkommen), ist die wertvollste Zeile des Blatts — er verhindert den Zwei-Stunden-SPEC_BLOCKED, den 'eine Funktion aufrufen' produziert haette. Attrappen-Regel (nur AUFZEICHNEN, nichts rechnen — sonst messen wir die Attrappe) und Scheitern-ist-Ergebnis-Regel vorbildlich. Kein Kopieren des Fremdcodes, Wegwerf-Probe nach A-05-Disziplin, Ampel wird VORGESCHLAGEN nicht gesetzt (Generator schlaegt vor, Evaluator bestaetigt, Planner traegt ein)."
herkunft: "Dieser Block stammt vom PLAN-PRUEFER (DoR-Runde 10.08.), wurde aber als unbenannter Beifang vom W-02/1-Commit 58342f47 mitgesichert, waehrend er ungesichert im geteilten Baum lag — dieselbe Klasse wie 4307987b/c2feffd4, diesmal in Gegenrichtung. Inhalt unveraendert gueltig; hiermit richtiggestellt."
zaehlfrage_entschieden: "plan-pruefer 10.08.: A-12 zaehlt in GRUPPE 2 (wie A-11 entschieden: erste Vorlage nach durchgefuehrter Prozesspruefung). Stand der Handzaehlung Gruppe 2 nach Erstvorlage: 1 A-11 · 2 W-01/1 · 3 W-02/1 · 4 W-13/1 · 5 A-12. Handzaehlung bis B3 steht (A-11 baut gerade B4; B3 bleibt offen)."
ergebnis_kurz: "A-12-1 KONTUREN belegt: beide l-shape-Varianten (:101-106 flat, :107-112 pitched) gerechnet, je 6 Punkte, geschlossen, 0 Selbstschnitte, genau 1 einspringende Ecke — mit EIGENEN Messinstrumenten geprueft, nicht mit denen des Fremdcodes. A-12-2 FLAECHEN: WEG 1 gewaehlt UND gelaufen (three.js aus dem Repo, Attrappen zeichnen nur auf) -> 4 Flaechen, 7 Pfetten, Kehl- und Gratsparren; Weg 2 zusaetzlich als Gegenprobe nachgerechnet, stimmt. Das im Blatt zugelassene Ergebnis 'rechnet, aber nicht isolierbar' ist NICHT eingetreten. A-12-3 VERGLEICH: Insel und Fremdcode je 4 Flaechen, Polygone punktweise maxAbweichung 0 — Grund gemessen: die Insel traegt seit 588283df (23.07.) einen PORT genau dieser Funktion, produktiv verdrahtet seit f0d02f45."
offene_akzeptanz: "A-12-4 ist bewusst offen: die Ampel ist NICHT gesetzt. FORMELSAMMLUNG.md und VORGEHEN.md sind im Scope des Blatts und bewusst UNVERAENDERT (content-identisch zu HEAD) — die Ampel traegt der Planner ein, und 'Schritt 3 erledigt' waere eine zweite Statuswahrheit vor der Abnahme (§16)."
fuer_die_wegentscheidung: "NICHT entschieden, nur nebeneinandergestellt (Nicht-Ziel des Blatts): der Flaechenteil von F-026 ist in der Insel bereits gebaut und produktiv angeschlossen — die Frage ist dort keine Bau-, sondern eine Anschlussfrage. Offen sind drei gemessene Luecken: Kennwertpfad (dachGeometrie.dachFlaechen wirft bei L, dachflaechen liefert 0), Linien (verschneidungslinien gebaut, produktiv nirgends benutzt) und Anlegepfad (A-05: roof.anbau wird nie gesetzt)."
ballwechsel_bestaetigt: "plan-pruefer 11.08.: A-12-CODE_FERTIG-Meldepflichten geprueft — Mess-SHA 239a163e, Pruef-SHA 752174d1, Bericht 92310844 (424 Zeilen) und der IN_ARBEIT-Beleg 4e935e84 der Vorgaenger-Instanz existieren alle. Scope SELBST gemessen (239a163e..752174d1): BERICHT + STATUS; das dritte Blatt im Diff (W-21) stammt aus einem fremden Planner-Commit dazwischen, nicht aus A-12. Die UEBERNAHME des unterbrochenen Auftrags ist sauber begruendet (P-02-Doppel-Launch-Pruefung vor dem ersten Schritt, IN_ARBEIT der Vorgaengerin nicht erneut gesetzt) — richtig so, ein zweites IN_ARBEIT waere §3-widrig gewesen. Ball beim EVALUATOR. FUER SEINE PRUEFUNG: die Ampel ist VORGESCHLAGEN (gruen mit Korrektur), NICHT gesetzt — FORMELSAMMLUNG traegt weiter 🟡 (Z.302/350, selbst nachgemessen); das ist blattkonform (A-12-4: Generator schlaegt vor, Evaluator bestaetigt, Planner traegt ein). Weg 1 wurde gefahren (die echte Funktion, nicht nachgerechnet) — die Attrappen-Regel gegenlesen: haben sie NUR aufgezeichnet? Und A-12-3 (der Vergleich Insel gegen F-026) ist die Grundlage der spaeteren Wegentscheidung, also der Kern der Abnahme."
claim_abnahme_a12: "plan-pruefer 11.08.: Evaluator-Station fuer A-12 mit frischer Instanz besetzt (Wochenlimit der Umgebung ist zurueckgesetzt). Claim VOR dem Start."
naechster_schritt: "PLANNER, vier Punkte: (1) 🟢 fuer F-026 in FORMELSAMMLUNG.md eintragen (Z.302 Ampeltabelle und Z.350 Formelblatt tragen noch 🟡); (2) die Verfahrensbeschreibung :352-364 und die Vergleichszeile :377 ('Grat/Kehle/Ortgang fertig benannt') berichtigen — das gehoert ZUM Gruen, nicht danach, sonst zitiert ein kuenftiger Auftrag ein Verfahren, das der Code nicht ausfuehrt; (3) VORGEHEN.md Schritt 3 als erledigt fuehren; (4) die Wegentscheidung zur Dachkonstruktion treffen (F-020 / F-026 / roof.anbau) — Abschnitt 4 des Berichts liefert dafuer jetzt die belegte Grundlage, samt der Erkenntnis, dass der Flaechenteil in der Insel bereits gebaut und produktiv verdrahtet ist (Anschlussfrage, keine Baufrage) und drei gemessene Luecken offen sind: Kennwertpfad, Linien, Anlegepfad. KEIN Release-Pruefer: Messauftrag ohne Produktivcode."
claim_abnahme: "evaluator (Erstinstanz) 11.08.: Abnahme A-12 GECLAIMT vor der Messung, Pruef-SHA 752174d1. Kein Bau, sondern ein Messauftrag - ich pruefe den Bericht gegen die Wirklichkeit, nicht Code gegen Kriterien."
evaluator_votum: "evaluator 11.08.: ABGENOMMEN an 752174d1, Fehlerklasse KEINE, AMPEL BESTAETIGT auf gruen. Ich habe die Kernzahlen mit einer EIGENEN Wegwerf-Probe unabhaengig nachgerechnet statt sie abzulesen: dreiecke 10, firstHoeheMm 5482, Flaechensumme 167.246 m2 (aus den zehn Dreiecken selbst ueber das Kreuzprodukt gerechnet), dachflaechen 0, dachGeometrie wirft mit dem Wortlaut Traufkontur nicht rechteckig - fuenf von fuenf deckungsgleich mit dem Bericht. Damit kommt ein L-Dach mit benannten Flaechen heraus, rot ist widerlegt und bleibt-gelb auch. A-12-5 habe ich am richtigen Messbereich geprueft: die vier A-12-Commits selbst beruehren resources/ und scripts/ null mal; die Spanne zeigt scripts/-Aenderungen aus A-11 und W-01, also fremde Zwischencommits. MEIN MESSFEHLER offengelegt: mein erster Lauf ergab dreiecke 0 und firstHoehe 2500 und widersprach dem Bericht in jedem Punkt - Ursache war meine Eingabe, ich schrieb pitchDeg statt neigungGrad und liess firstAzimutGrad weg; waere ich dabei stehen geblieben, haette ich einen fehlerfreien Messbericht als falsch gemeldet. Drittes Mal derselben Klasse an drei Tagen nach vendor und 2D-Ansicht: bei einer Abweichung zuerst den eigenen Aufbau pruefen."
```
---

## OPERANDEN VON YAMA — 10.08. 19:4x (vom Plan-Pruefer eingetragen, jede Angabe an Yamas eigener Messung 19:43:43)

```yaml
sicherung: "ERLEDIGT waehrend der Vorlage — fork und backup-private tragen den Arbeitszweig (d7daf034) und main (c8191292, RELEASE A-11). Sichern bleibt NICHT Veroeffentlichen: der Fork ist Sicherung. OFFEN ausdruecklich: origin nachziehen JA (hinkt 116 Commits); upstream bleibt unberuehrt, dorthin geht NICHTS ohne Yamas Ansage."
einreihung: "A-12 bekommt den naechsten IN_ARBEIT-Slot (entsperrt W-07 UND W-08, klein); W-13/1 danach. W-01/1-Nachbesserung und W-02/1-Abnahme laufen weiter (Ball bei Generator/Evaluator, keine Freigabe noetig)."
fahrplan_korrektur: "W-08 gehoert NICHT in Runde 1 (braucht W-07, das hinter A-12 steht) — Runde 1 = W-04 + W-11, plus W-13 nachziehen. Vom Planner in a922785a bereits eingearbeitet (nachgemessen)."
marken_liste: "GESCHLOSSENE Liste: die fuenf Rollen + genau zwei Ausnahmen docs: und env:. KEIN fix: (ein Fix gehoert immer zu einer Rolle). Alles andere verweigert das Tor. -> Nachbesserungs-/Folgeauftrag am A-11-Bau, Planner schneidet."
azimut_reihenfolge: "ERZWUNGENE Reihenfolge: (1) Konvention von PVRoof.roof_azimuth FESTSTELLEN (bestehende Werte gegen bekannte Daecher plausibilisieren, Ergebnis hinschreiben — Feststellung, kein Bau); (2) DANN die Bruecke schliessen (gezeichneter Azimut -> Ertragsrechnung; nur nach Schritt 1, sonst wird der Fehler still); (3) die Schema-Frage (energie_roof_models vs. p_v_roofs) entscheidet Yama JETZT NICHT."
a06_rest: "PID 48098 beendet (cc9b15c4), A-06 ERLEDIGT. Die getrennte Einzelentscheidung zu den sieben Fremdzeilen ist GEGENSTANDSLOS — vom Plan-Pruefer 10.08. 19:5x FRISCH GEMESSEN in der Arbeits-DB ticket (read-only): hausplaner_documents id 20-24 = 0 Zeilen, lead_alternative_adds 990002/990004 = 0 Zeilen. Die Zeilen sind seit der Yama-freigegebenen Loeschung vom 05.08. weg (Sicherung lag in _to_delete/); es gibt nichts mehr zu entscheiden."
prozesspunkt: "Yamas Korrektur, vom Plan-Pruefer als DAUERregel uebernommen: vor jeder Vorlage an Yama wird der ZUSTAND gemessen, nicht die Notiz gelesen — zwei von vier Punkten der letzten Vorlage waren beim Absenden erledigt (ac07a1c5-Klasse)."
```
herkunft_operanden: "Der OPERANDEN-Block oben stammt vom PLAN-PRUEFER (19:5x), wurde als unbenannter Beifang von 5c06f5ca mitgesichert — dritter Fall, Inhalt gueltig, hiermit richtiggestellt."
paragraf13_ausloesung: "plan-pruefer 10.08. 20:0x: Der §13-SOFORTAUSLOESER IST HIERMIT FORMAL AUSGELOEST — der W-02-Evaluator hat die zweite Wiederholung der Klasse 'Satz ohne Befehl/Ausgabe' benannt (a83254e6), der Generator widerspricht nicht (5c06f5ca), und PROZESSPRUEFUNG-02 hat exakt diese Unterlassung ('benannt, aber niemand loest aus') als Befund 1 gefuehrt — sie ein zweites Mal zu wiederholen waere die Ironie-Stufe. DAZU gehoert die NEUE Fehlerklasse aus 5c06f5ca in die Pruefung: 'gruen gemeldet, was nicht im Commit steht' (Korrektur nur im Arbeitsbaum, Gegenprobe mass den Baum statt den Commit — Klasse BEWEIS, und B3 haette sie nicht gefangen). DURCHFUEHRUNG beim Planner (Praezedenz P-02); mein Anteil folgt wie bei P-02. Der Zaehler-Reset bleibt weiter an B3 gebunden, das noch niemand gebaut hat — auch DAS gehoert in die Pruefung."
claim_bau_a12: "plan-pruefer 10.08.: Yamas Einreihung ausgefuehrt — A-12 bekommt den Slot, Generator-Station mit frischer Instanz besetzt (Messlauf). §3 selbst geprueft: alle drei grep-Treffer 'zustand: IN_ARBEIT' sind Prosa-Zitate in in_arbeit_gesetzt-Feldern, KEIN Zustandsfeld traegt IN_ARBEIT. Claim VOR dem Start."

---

## BEREIT — W-04/1 (Runde 1 Klasse A)

```yaml
auftrag: "W-04/1"
titel: "Die sieben Blaetter von W-04 aus oeffnungsBauarten.ts + oeffnungsTypen.ts ableiten"
datei: docs/auftraege/aktiv/W-04-oeffnung-beschreiben.md
zustand: VEROEFFENTLICHT
ballbesitz: yama
release_vermerk: "release-pruefer (Stamm-Instanz) 11.08.: §10 an der Abnahme 973f1ec4/a44e5fdd — Kette Vorfahr, reiner Doku-Scope (7 Werkbank-Blaetter + REGISTER, 8 Dateien), Produkt-Code seit 56c77ae6 unberuehrt (resources/public/app/scripts = 0), Insel-Suite 1692/1692 als Regressionskontrolle, Scans leer. main-FF unmittelbar nach diesem Statuscommit. Naechster Zustand BETRIEBSBESTAETIGT gehoert Yama."
basis_sha: b6078b2a
claim_bau_w04: "plan-pruefer 11.08.: §3-Sperre gefallen (A-12 CODE_FERTIG, Zustandsfeld IN_ARBEIT 0 — selbst gemessen) — Generator-Station fuer W-04/1 mit frischer Instanz besetzt, aeltestes BEREIT der Runde 1. Claim VOR dem Start. Die A-12-Abnahme laeuft parallel: nach §3 zulaessig, sie prueft einen festgeschriebenen Commit."
letztes_votum: "plan-pruefer 11.08. (1. DoR-Runde, BEREIT beim ersten Review): JEDE Behauptung selbst gemessen und EXAKT bestaetigt — oeffnungsBauarten 75 Z/5 Exporte, oeffnungsTypen 49 Z/7 Exporte, fensterProdukt 153 Z; der OeffnungsArt-Import steht woertlich in Z.3 (die praezise Ausnahme im Ausschluss ist also NOETIG, nicht hoeflich); Registry traegt WIRKLICH zwei Werkzeuge (fenster :78, tuer :96); W-02s 2-FUNKTION beschreibt die Tuergeometrie bereits (2 Treffer) — die Verweis-statt-Doppelbeschreibung-Entscheidung ist damit belegt und richtig; Platzhalter-Rot zaehlbar (6 Blaetter). W-04/1-10 traegt die E2-Zaehlform aus Pruefung 03. Der Selbstbefund 'W-04 hat kein eigenes Modul' ist die vierte Matrix-Korrektur derselben Klasse und wieder vom Planner selbst gefunden."
messbefund_zu_w04_1_4: "PLAN-PRUEFER-ZULIEFERUNG (gemessen, damit der Bauende nicht nach dem Falschen sucht): der Nebensatz des Blatts 'Stilles undefined ist die A-10-Klasse' trifft NICHT zu — oeffnungsTypen.ts:43 und :48 liefern bei unbekannter ID KEIN undefined, sondern '?? TUER_TYPEN[0]' bzw. '?? FENSTER_TYPEN[0]': einen STILLEN FALLBACK auf den ersten Katalogeintrag. Das ist die A-10-Klasse in ihrer schaerferen Form — nicht 'nichts', sondern ein plausibel aussehender FALSCHER Wert, den niemand als Ersatz erkennt. Die Frage von W-04/1-4 ist richtig gestellt und traegt (sie verlangt Messung, nicht die Vermutung); der Bauende soll den Fallback benennen, nicht nach undefined suchen. Die Bauarten-Lookups sind getrennt zu messen — dort kann die Antwort anders lauten."
ballwechsel_bestaetigt: "plan-pruefer 11.08. 23:3x: W-04/1-CODE_FERTIG-Meldepflichten geprueft — Kette a9e58dd4 (IN_ARBEIT mit §3-Beleg an beiden Orten UND §7-Vorpruefung 8/8 IM SELBEN SKRIPT wie die Schranke, damit zwischen Pruefen und Setzen niemand dazwischenkommt — die beste Antwort auf die Kollisionsserie, die bisher jemand gebaut hat) -> a44e5fdd (Bau) -> 3dcca1b8 (CODE_FERTIG). Scope des Bau-Commits SELBST gemessen: exakt 8 Dateien = sieben Blaetter + REGISTER, nichts darueber hinaus. Ergebnis selbst nachgemessen: Platzhalter 0 (Basis 22), Register traegt W-04 BESCHRIEBEN, drei Richtungen 0/0/0. Ball beim EVALUATOR. FUER SEINE PRUEFUNG: (1) der Generator hat MEINE Zulieferung zu Ende gemessen und die vier Lookups als GEGENSAETZLICH belegt (fensterBauartNach:70 und tuerBauartNach:73 -> undefined; tuerTyp:42 und fensterTyp:47 -> NIE undefined, sondern dreh1 875x2010 bzw. drehkipp) — nachpruefen, ob 7-GRENZEN beide Richtungen und die Gefahr benennt; (2) er hat einen EIGENEN Messfehler waehrend des Baus offengelegt (TUER_TYPEN 12 statt 5, weil seine sed-Grenze in FENSTER_TYPEN hineinlief — aufgefallen, weil die Typunion nur fuenf Werte erlaubt: der TYP hat die Zahl widerlegt, nicht die Sorgfalt); (3) er hat AUSDRUECKLICH NICHT gemessen, ob die 48 SVG-Dateien existieren, und es darum auch nicht behauptet — pruefen, ob 6-PRUEFUNG das als Pruefpunkt statt als Zusage fuehrt; (4) BEFUND im Register vermerkt statt still korrigiert: die F-Zuordnung F-003/F-031 stimmt nicht mit dem Code — gehoert dem Planner, nicht dem Bau."
claim_abnahme: "plan-pruefer 11.08. 23:3x: Evaluator-Station fuer W-04/1 mit frischer Instanz besetzt. Claim VOR dem Start. KANONISCHER Feldname ohne Auftrags-Suffix — erste Anwendung der Konsequenz aus der fuenften Kollision."
naechster_schritt: "NACHGEZOGEN 11.08. 23:xx: W-04/1 ist seit a9e58dd4 IN_ARBEIT bei einer Team-Generator-Instanz (§3-Beleg an beiden Orten, Drei-Richtungs-Auflage ausdruecklich befolgt). Die alte Zeile sagte 'A-12 laeuft' — A-12 ist seit 3a4838e1 CODE_FERTIG, die Sperre ist gefallen."
claim_abnahme: "evaluator (Erstinstanz) 11.08. 23:1x: Abnahme W-04/1 GECLAIMT vor der Messung, Bau a44e5fdd. Kanonischer Feldname ohne Auftrags-Suffix, wie der Plan-Pruefer es nach der fuenften Kollision festgelegt hat - bei A-12 hatte ich seinen claim_abnahme_a12 nicht gefunden, weil ich claim_abnahme gesucht habe."
evaluator_votum: "evaluator 11.08.: ABGENOMMEN an a44e5fdd, Fehlerklasse KEINE, alle zehn Kriterien erfuellt. Gemessen statt gelesen: Platzhalter 0, resources/** im Bau-Commit 0 Pfade, Suite 1692/1692, Register gefuehrt, 4-BEDIENUNG nennt fenster 7x und tuer 3x. Alle sechs Fundstellen einzeln im Code geoeffnet - keine laeuft ins Leere. Der Ausschluss fensterProdukt.ts ist der staerkste Teil: nicht pauschal weggeschoben, sondern mit der einen Ausnahme benannt, die hineinragt (Typ OeffnungsArt, importiert in oeffnungsBauarten.ts:3) - ein pauschales hat-nichts-damit-zu-tun waere an dieser Importzeile widerlegbar gewesen. W-04/1-10 ist im ERSTEN Anlauf erfuellt: 2 Befehlszeilen, 2 Ausgaben im IN_ARBEIT-Commit a9e58dd4; bei W-01 und W-02 riss dieselbe Zusage zweimal - das ist die messbare Wirkung von E2 aus Prozesspruefung 03."
```
---

## ✅ GESCHLOSSEN 11.08. — BEFUND des Plan-Pruefers zu A-12 (ENV-Unterbrechung + Wegwerf-Probe)

```yaml
lage: "Die A-12-Generator-Instanz ist am WOECHENTLICHEN NUTZUNGSLIMIT der Umgebung gestorben (reset 11.08. 22:00 Europe/Berlin), mitten im Messlauf. A-12 steht weiter IN_ARBEIT und BLOCKIERT damit nach §3 die gesamte W-Reihe."
gemessen: "docs/BERICHT-A-12-f026.md existiert NICHT (Messlauf unfertig) · scripts/ Drift 0 · resources/ Drift 1 Datei: resources/planner/hausplaner/__tests__/zzA12wegwerf.test.ts liegt UNTRACKED im Baum (in KEINEM Commit — A-12-5 insoweit gewahrt)."
gefahr: "Die Datei liegt in __tests__ und wird vom Insel-Runner MITGEFAHREN — jede Rolle, die npm run test:hausplaner faehrt, misst ab jetzt eine fremde Wegwerf-Probe mit und bekommt einen anderen Zaehler als 1692. Genau die Klasse, die A-12-5 verhindern soll."
ich_fasse_sie_nicht_an: "Fremde unfertige Arbeit, Dauerregel Erhalt statt Entfernung — ich messe und melde. Entfernen gehoert der Generator-Instanz beim Wiederaufnehmen ODER Yama."
vorschlag: "Bis zur Wiederaufnahme: A-12 bleibt IN_ARBEIT (kein Zustandswechsel durch mich — der Bau ist nicht gescheitert, sondern unterbrochen). Wer die W-Reihe vorziehen will, braucht Yamas Ansage; §3 laesst sonst kein zweites IN_ARBEIT zu."
geschlossen: "plan-pruefer 11.08. — SELBST nachgemessen, nicht geglaubt: die Nachfolge-Instanz hat die Wegwerf-Probe entfernt (Datei existiert nicht mehr), resources/ traegt in ALLEN DREI RICHTUNGEN 0 (geaendert/hinzugefuegt/entfernt — erste Anwendung meiner eigenen neuen Beweisform, sie greift), das Zustandsfeld IN_ARBEIT ist 0, die §3-Sperre der W-Reihe ist gefallen. Auch mein Zaehler-Befund ist belegt: der Generator misst mit Probe 1695/1695, ohne 1692/1692 — die Probe hat den Suite-Zaehler wirklich verfaelscht. Er hat diesen Block ausdruecklich NICHT umgeschrieben ('schliessen darf ihn, wer ihn gesetzt hat') — vorbildliche Rollendisziplin, deshalb schliesse ich ihn hier. OFFEN BLEIBT NUR: die Streudatei '1692' im Wurzelverzeichnis liegt weiter (35 Byte, 22:35)."
```
---

## BEREIT — W-11/1 (Runde 1 Klasse A, zweites Blatt nach Yamas Korrektur)

```yaml
auftrag: "W-11/1"
titel: "Die sieben Blaetter von W-11 aus bemassung.ts + masskette.ts + masseingabe.ts ableiten"
datei: docs/auftraege/aktiv/W-11-bemassung-beschreiben.md
zustand: IN_ARBEIT
ballbesitz: generator (Bau laeuft, Runde 1 drittes Blatt)
basis_sha: 7a415aff
letztes_votum: "plan-pruefer 11.08. (1. DoR-Runde, BEREIT beim ersten Review — das dritte W-Blatt in Folge): ALLE Zahlen aufs Zeichen bestaetigt — 118/108/169 = 395 Zeilen, 7/6/9 = 22 Exporte, Registry 'bemassen' 1 Treffer, 4 dedizierte Zusagen (bestes Verhaeltnis der Runde, stimmt). Die EINZIGE Abhaengigkeit steht woertlich in bemassung.ts:18 ('import { masskette, type MassSegment, type Bbox }'), masseingabe.ts ist WIRKLICH importfrei (grep ^import = 0) — die Schichtentrennung ist damit am Code belegt, nicht behauptet. Die MassPunkt-Doppelung habe ich ZEICHENWEISE nachgeprueft: masskette.ts:9 und masseingabe.ts:25 sind byte-identisch ('exportinterfaceMassPunkt{x:number;y:number;}') — die Einordnung 'Doppelung, aber keine zweite Wahrheit, gefaehrlich erst bei einseitiger Aenderung' traegt exakt. W-11/1-7 ist der wertvollste Punkt des Blatts: die Register-Abhaengigkeit zu W-13 wird GEPRUEFT statt uebernommen, mit der ehrlichen Begruendung 'drei von vier Registerangaben waren in dieser Runde ungenau' — genau die Haltung, die die Runde bisher getragen hat. W-11/1-10 traegt die E2-Zaehlform."
naechster_schritt: "Generator zieht W-11/1 nach W-04/1; Start erst ohne laufendes IN_ARBEIT — das ist jetzt W-04/1 (a9e58dd4), NICHT mehr A-12 (seit 3a4838e1 CODE_FERTIG)"
```

```yaml
w_reihe_stand: "plan-pruefer 11.08., gemessen: W-01/1 in Nachbesserung · W-02/1 ABGENOMMEN (Ball Release-Pruefer) · W-04/1 BEREIT · W-11/1 BEREIT · W-13/1 ein Mini-Rest beim Planner · W-08/1 traegt Yamas Korrektur bereits im Blatt ('NICHT Runde 1 — baut hinter A-12, zusammen mit W-07', Z.249) und braucht darum jetzt KEINE DoR-Runde von mir; ich fahre sie, wenn A-12 die Sperre loest. Damit ist Runde 1 vollstaendig geprueft und wartet nur auf den §3-Slot."
```
---

## ⚠ AUFLAGE des Plan-Pruefers (11.08.) — die must_preserve-BEWEISFORM in ALLEN W-Blaettern, und mein Anteil daran

```yaml
anlass: "Generator-Selbstbefund 23839610: seine must_preserve-Messung (git ls-tree HEAD gegen hash-object) kann HINZUGEFUEGTES strukturell nicht sehen — eine Datei, die HEAD nicht kennt, steht in der Liste nicht und kann keine Abweichung erzeugen. Er hat gemeldet '1230 Dateien, 0 Abweichungen', im Baum lagen 1236. Sein Satz dazu ist der praezise: 'Das ist der Unterschied zwischen recht haben und es gemessen haben.'"
mein_anteil: "MEINER ist die Kriterienpruefung, und ich habe sie an dieser Stelle nicht gemacht. W-04/1-8 und W-11/1-8 habe ich als 'sauber deklariert' durchgewunken — beide sagen nur 'resources/** byte-identisch', KEINES nennt eine Beweisform (grep others|exclude-standard in beiden Blaettern: 0). Der Wortlaut traegt (byte-identisch schliesst Hinzugefuegtes ein), die BEWEISFORM war offen, und eine offene Beweisform hat sich der Bauende dreimal in der Richtung gewaehlt, die nichts findet. Dieselbe Klasse wie meine A-08-Luecke (Tabelle nicht gegen den Zusagen-Bestand simuliert): ich habe geprueft, ob das Kriterium DASTEHT, nicht ob sein Nachweis FANGEN kann."
auflage: "VERBINDLICH fuer W-04/1, W-11/1 und jedes weitere W-Blatt (kein Zurueckziehen des BEREIT — der Kriterien-Wortlaut traegt, es fehlt der Nachweisweg): der must_preserve-Beleg misst ALLE DREI RICHTUNGEN und weist sie einzeln aus — GEAENDERT (git diff --name-only HEAD -- resources) UND HINZUGEFUEGT (git ls-files --others --exclude-standard -- resources) UND ENTFERNT (git diff --diff-filter=D --name-only HEAD -- resources). Ein Nachweis, der nur eine Richtung faehrt, erfuellt das Kriterium NICHT. Formulierung ins Blatt gehoert dem Planner; bis dahin gilt sie als Auflage hier."
selbst_gemessen_11_08: "geaendert 0 · hinzugefuegt 1 · entfernt 0 — die eine Hinzufuegung ist die A-12-Wegwerf-Probe (Befund oben). Mit der alten Einweg-Messung waere das Ergebnis '0 Abweichungen' gewesen, also falsch."
streudatei: "Zusaetzlich gemeldet (nicht angefasst): eine Datei namens '1692' liegt im WURZELVERZEICHNIS des Repos (35 Byte, 11.08. 22:35) — offensichtlich eine verungluecke Umleitung beim Zaehlen der Insel-Suite. Untracked, gehoert niemandem sichtbar; Entfernen nach Dauerregel durch den Verursacher oder Yama."
```
---

## BEREIT — W-05/1 (Runde 2 Klasse A)

```yaml
auftrag: "W-05/1"
titel: "Die sieben Blaetter von W-05 aus roomDetection.ts ableiten"
datei: docs/auftraege/aktiv/W-05-raum-erkennen-beschreiben.md
zustand: BEREIT
ballbesitz: generator (Runde 2; §3-Stand 11.08. 23:xx: A-12 CODE_FERTIG, IN_ARBEIT ist W-04/1)
basis_sha: 3358d1cc
letztes_votum: "plan-pruefer 11.08. (1. DoR-Runde, BEREIT beim ersten Review — das vierte W-Blatt in Folge): alles selbst gemessen und exakt bestaetigt — roomDetection 190 Z / 4 Exporte, Registry 0 Treffer auf raum/room (die Schicht-statt-Werkzeug-Einordnung ist damit am Code entschieden, nicht vermutet), Raum-Treffer grundriss 0 gegen roomDetection 7 (der Ausschluss traegt), Platzhalter-Rot zaehlbar (6 Blaetter). DIE GROBZAHL-DIAGNOSE IST ARITHMETISCH BEWEISBAR: 190 + 133 + 48 = 371 — exakt die alte Fahrplanzahl. Der Planner nennt es 'nach Namensnaehe zusammensummiert'; die Summe geht auf den Zeichen auf, und dass die Einzelmessung DREIMAL nach unten korrigiert und nie nach oben, ist damit kein Eindruck mehr, sondern gerechnet. VORBILDLICH die Zurueckhaltung beim Fund: die erste Folgerung (A-05s Luecke 4 bleibt gueltig, der Weg dorthin ist kuerzer als vermutet — eckenAnalyse/anzahlInnenwinkel/erwarteteInnenwinkel liegen, es fehlt die Zuordnung) wird gezogen, die zweite (Luecke 5) ausdruecklich NICHT, mit der Begruendung 'eine richtige Messung, aus der eine zu weite Aussage folgt' — genau die Unterform, die heute dreimal auffiel. Der Fund ist an A-12 adressiert, wo er hingehoert."
auflage_beweisform: "W-05/1-8 traegt dieselbe offene Beweisform wie W-04/1-8 und W-11/1-8 (grep others|exclude-standard: 0) — die stehende AUFLAGE oben gilt auch hier: alle DREI Richtungen einzeln ausweisen. Kein Restpunkt, kein Zurueckziehen; der Kriterien-Wortlaut traegt."
naechster_schritt: "Generator zieht W-05/1 in Runde 2 (nach W-04/1 und W-11/1); Start erst ohne laufendes IN_ARBEIT — das ist jetzt W-04/1, A-12 ist CODE_FERTIG"
```
---

## ⚠ FUENFTE KOLLISION (A-12-Abnahme) — Zeitfolge gemessen, und mein Anteil ist der FELDNAME (plan-pruefer, 11.08. 23:2x)

```yaml
zeitfolge_gemessen: "22:59:33 mein Claim 6cd4a2b0 (Instanz B gestartet) · 23:04:07 der Team-Claim 19d8855b (Instanz A) · 23:07:22 ihr Votum. MEIN CLAIM WAR VIER MINUTEN FRUEHER und stand sichtbar in derselben Datei — der Doppel-Launch geht nicht auf einen fehlenden Claim zurueck."
mein_anteil: "Er geht auf den FELDNAMEN zurueck, und der ist meine Praxis. Ich habe je Station ein eigenes Suffix erfunden — claim_abnahme_a09, claim_abnahme_a11, claim_abnahme_a12, claim_bau_w04 —, um innerhalb eines Blocks unterscheidbar zu bleiben. Genau das macht das Feld fuer jemanden UNAUFFINDBAR, der nach dem kanonischen Namen sucht: Instanz A schrieb in 'claim_abnahme', meines hiess 'claim_abnahme_a12', beide standen im selben Block, keine Instanz las das Feld der anderen. Haette ich den kanonischen Namen benutzt, haette Instanz A meinen Eintrag ueberschreiben muessen und es gemerkt. Instanz B hat es praeziser gesagt als ich es getan habe: 'zwei Claim-Felder in einem Block haben die Doppelbesetzung nicht verhindert, weil keine Instanz das Feld der anderen las.'"
konsequenz_ab_sofort: "EIN kanonischer Feldname je Station, ohne Auftrags-Suffix: claim_bau · claim_abnahme · claim_release. Der Auftrag steht ohnehin im Block; das Suffix trennte nur meine eigenen Eintraege voneinander und trennte sie dabei von allen anderen. Rueckwirkend NICHTS umbenennen (fremde Voten in geteilter Datei, B5) — ab jetzt gilt der kanonische Name."
inhaltlich_kein_schaden: "Beide Instanzen kamen unabhaengig auf ABGENOMMEN und bestaetigen die Ampel 🟢 gebunden an dieselbe Wortlaut-Korrektur. Instanz A hat die Insel-Seite nachgerechnet und dabei ihren EIGENEN Aufbaufehler offengelegt; Instanz B hat A-12-1 punktweise, den Fremdcode-Flaechenlauf mit EIGENEN Attrappen und die Attrappen-Regel selbst nachgefahren (eigenes Geruest gegengebaut, identische Zahlen — also misst der Bericht den Fremdcode, nicht sein Geruest). Zwei unabhaengige Bestaetigungen sind teuer, aber nicht wertlos."
ballbesitz_korrektur_gewuerdigt: "Instanz B hat den Ballbesitz von release-pruefer auf PLANNER korrigiert und es laut gesagt. Sie hat recht, und die Begruendung ist die richtige: ein Messauftrag liefert einen Bericht — es gibt keinen Release-Kandidaten, den §10 pruefen koennte, und die einzige echte Folgehandlung (Ampel eintragen, Verfahrenswortlaut berichtigen, Weg entscheiden) haette beim Release-Pruefer KEINEN Eigentuemer gehabt. MEIN Prueftext hatte 'ballbesitz: planner' fuer den Messauftrag ausdruecklich vorgegeben; dass der Block dann doch release-pruefer trug, ist zwischen den Instanzen entstanden und jetzt richtig."
```
---

## ⚠ VIERTE CLAIM-KOLLISION + mein Anteil an der erfundenen Sperre (plan-pruefer, 11.08. 23:xx)

```yaml
kollision: "Meine W-04/1-Bau-Instanz (Claim 5656ea3b) wurde von einer TEAM-Instanz ueberholt: a9e58dd4 zog W-04/1 43 Sekunden nach meiner Vorpruefung. MEINE Instanz hat KORREKT GESTOPPT und nichts hinterlassen (alle zehn Scope-Dateien content-identisch zu HEAD, kein Commit, kein IN_ARBEIT). Ihr Satz ist der Befund: 'Der Schreibkonflikt hat den Doppelbau verhindert, nicht meine Pruefung — ein Claim, der nur eine Zeile in einer Datei ist, haelt keine Station frei.' VIERTER Fall derselben Klasse (nach A-04, A-07, A-11). Die Team-Fassung gilt: sie ist zuerst da und sauber belegt (§3 an beiden Orten, §7 8/8 IM SELBEN SKRIPT wie die Schranke, Drei-Richtungs-Auflage ausdruecklich befolgt, Tafelzeile nachgetragen)."
mein_anteil_erfundene_sperre: "Der Planner hat selbst gemeldet (4789e8c7), dass vier seiner Blaetter eine §3-Sperre durch A-12 behaupten, die es nicht gibt. MEIN ANTEIL: ich habe dieselbe Angabe an FUENF Stellen in die Statuswahrheit uebernommen (W-04-, W-11-, W-05-Bloecke) — zum Zeitpunkt des Votums war sie richtig, danach hat sie GEALTERT und ich habe sie nicht nachgezogen. Das ist die Klasse, wegen der Yama mich heute korrigiert hat ('Zustand messen, nicht Notiz lesen'), diesmal in meiner eigenen Datei. ALLE FUENF korrigiert, nicht drei — B2-Gegenprobe gefahren: grep auf den alten Wortlaut liefert 0."
zweite_wegwerf_probe: "Die W-04-Instanz meldete eine zweite fremde Probe (zzEvalA12.test.ts, Suite 1692 -> 1693) der laufenden A-12-Abnahme. SELBST NACHGEMESSEN 23:xx: die Datei existiert nicht mehr — die Abnahme-Instanz hat sie im Lauf entfernt, wie A-05 es vormacht. Kein offener Punkt, aber der zweite Fall in zwei Tagen: eine Probe im __tests__-Ordner faelscht jeden Suite-Zaehler, solange sie liegt. Gehoert in die naechste Prozesspruefung als wiederkehrende Klasse, nicht als Einzelfall."
zulieferung_gesichert: "Die gestoppte Instanz hat ihre Messungen weitergereicht statt sie wegzuwerfen — fuer die bauende Instanz wertvoll und hier gesichert: (1) die beiden Katalogpaare antworten ENTGEGENGESETZT — oeffnungsTypen:43/:48 stiller Fallback auf dreh1 (875x2010) bzw. drehkipp, festgehalten in oeffnungsTypen.test.ts:51-56; oeffnungsBauarten:70-75 find() OHNE Fallback -> undefined, festgehalten in oeffnungsBauarten.test.ts:29-30, folgenlos weil EigenschaftenPanel.tsx:365 mit '?? aktuellTyp' die rohe ID zeigt. MEINE Zulieferung war also nur die halbe Wahrheit: ich hatte die Typen-Lookups gemessen und die Bauarten-Lookups als 'getrennt zu messen' benannt — richtig, aber die Instanz hat es ZU ENDE gemessen. (2) W-04/1-2 Antwort ist 'keine Formel' (0 Rechenoperationen in beiden Modulen). (3) ZWEI neue Befunde ausserhalb ihres Scopes: ConfigWizard.tsx:29-30/:222 verdrahtet Masse 1010/1360/2010/900 FEST — zweite Massquelle neben dem Katalog; und scene.types.ts:165 nennt FENSTER_TYPEN/TUER_TYPEN, wo FENSTER_BAUARTEN/TUER_BAUARTEN stehen muesste (§7-Klasse 'Kommentar behauptet, was der Code nicht hat'). Beide gehoeren dem Planner."
zaehlweise_offen: "Die zwei Instanzen zaehlen die Rot-Lage verschieden (28 Klammern in 6 Blaettern gegen 22 in 7) — kein Widerspruch, verschiedene Verfahren. Vor der Abnahme zu vereinheitlichen, sonst streiten Bau und Abnahme ueber eine Zahl statt ueber die Sache."
```
---

## Ballbesitz-Uhr — Stand 05.08. 00:0x

| Rolle | Gegenstand | seit | läuft oder still |
|---|---|---|---|
| **Generator** | A-01, Bau frei | 05.08. 00:1x | **läuft** — Rückfrage gestellt und beantwortet |
| Plan-Prüfer | A-02 auf `BEREIT`, Warteschlange | 05.08. 00:1x | frei |
| Planner | A-03 aus dem §15-Befund | 05.08. 00:1x | läuft |

### Die VIERTE Ursache für einen stillen Baum — heute belegt

**Ich hatte um 00:0x notiert: Generator still, 17 min, 0 Dateien.** Die Messung stimmte. Er hat in
derselben Zeit einen Browser gefahren, eine Datenbank geprüft, drei Hindernisse gefunden und um
00:08 eine Rückfrage committet.

```text
1  Baum still, kein Auftrag mit Marke      Leerlauf              Auftrag schneiden
2  Baum still, Auftrag mit Marke liegt     blockiert/wartet      melden, kein zweites Blatt
3  Baum still, halbfertige Dateien         Lauf abgebrochen      messen, nichts anfassen
4  Baum still, Auftrag mit Marke liegt     ARBEIT IM BROWSER     melden — und weiter warten
   ↳ Messen an der Oberflaeche schreibt NULL Dateien in den Baum. Ein stiller Baum
     ist bei einem Auftrag mit Browseranteil der NORMALFALL, nicht das Warnzeichen.
   ↳ NACHTRAG 01:5x — die Spur gibt es doch, sie liegt nur woanders:
       storage/framework/sessions/   bewegt sich, solange eine Buehne bedient wird
       ps -eo command | grep 'php -S\|artisan serve'   nennt Weg UND Datenbank
     Damit ist Ursache 4 nicht mehr 'unentscheidbar', sondern MESSBAR.
```

> **Was mich davor bewahrt hat, falsch zu liegen, war nicht die Messung — die war in allen vier
> Fällen dieselbe.** Es war, dass ich sie **gemeldet und nicht gedeutet** habe. Hätte ich „still"
> in „untätig" übersetzt, hätte ich einem arbeitenden Generator ein zweites Blatt hinterhergeworfen.
> *Genau der Fehler, den §8b Zeile 2 verbietet — und er wäre mir hier passiert, weil eine vierte
> Ursache fehlte, die keiner aufgeschrieben hatte.*

---

## ⚠ Planner-Befund an den Evaluator (05.08. 01:5x) — A-03 deckt die Tür ab, die niemand benutzt

**Kein Eingriff:** A-03 liegt beim Evaluator. Ich ändere das Blatt nicht, während er es hält —
ich melde. **Der Befund ist ein Spezifikationsfehler von mir, kein Baufehler.**

### Gemessen, an der JETZT laufenden Bühne

```text
ps -eo command  ->  cd /Users/yamanuri/Documents/ticket-a01/public
                    && DB_DATABASE=ticket_testing exec php -S 127.0.0.1:8099 …/server.php
ps eww -p <pid>  ->  DB_DATABASE=ticket_testing        gesetzt und WIRKSAM
```

**Diese Bühne ist sicher.** Bei `php -S` gibt es keine Filterung — die Variable kommt an.
*Der laufende Vorgang ist NICHT gefährdet, und dieser Befund ist keine Warnung an ihn.*

### Der Fehler im Auftrag

```text
A-03 umschliesst     artisan serve      (exec env APP_ENV=testing php artisan serve)
tatsaechlich genutzt php -S             Generator 00:08, Evaluator 01:54 - beide
ANKER-BROWSER nennt  php -S             0-mal

und die ungeschuetzte Nachbarform:
  DB_DATABASE=ticket_testing php -S …   sicher     ticket_testing
  php -S …                              UNSICHER   faellt auf .env -> ticket
                                        Unterschied: ein Praefix. Kein Riegel dazwischen.
```

> **A-03 baut einen Riegel an die Tür, die keiner nimmt.** Der `php -S`-Weg bleibt offen, und
> seine sichere und seine unsichere Fassung unterscheiden sich um ein Präfix.

### Warum das mir gehört und nicht dem Bauenden

**Der Generator hat es mir am 00:08 wörtlich geschrieben:** *„Tragfähig ist `php -S`, gestartet
AUS `public/` heraus (Laravels Router nimmt `getcwd()`)."* **Ich habe diesen Bericht gelesen,
daraus zitiert — und trotzdem `artisan serve` vorgeschrieben.** Ich habe die Form gewählt, die ich
gemessen hatte, statt der, die benutzt wird.

*Das ist dieselbe Klasse wie [PROZESSPRUEFUNG-01](PROZESSPRUEFUNG-01.md): die Regel sieht
vollständig aus und läuft neben der Praxis her.* **Zweite Ausprägung, keine 40 Minuten später.**

### Was ich vorschlage — und was der Evaluator entscheidet

**A-03 kann `ABGENOMMEN` werden:** Das Blatt verlangte einen Riegel um `artisan serve`, und den
gibt es nachweislich. **Ob die Lücke `NACHBESSERN` rechtfertigt, ist seine Entscheidung, nicht
meine** — ich habe hier den Interessenkonflikt, weil die Lücke aus meinem Auftrag stammt.

**Meine Empfehlung: abnehmen und A-04 schneiden.** *Einen laufenden Auftrag nachträglich zu
verbreitern, weil der Planner zu eng geschnitten hat, bestraft den Bauenden für meinen Fehler.*

---

## Was aus dem Bestand übernommen wurde — und was nicht

Nach §17 werden alte Statuswerte **nicht** automatisch übernommen. Der fachliche Code bleibt, die
Prozessstände sind neu einzuordnen.

| Vorlauf | fachlicher Stand im Zweig | Prozessstand nach §17 |
|---|---|---|
| Z-07 Dach | Code liegt im Zweig (`herkunftFuerNeuesDach`, 2 Stellen) | **wird A-01**, neu geschnitten — alter P1 war unerfüllbar (SPEC) |
| Z-06 / N1 Herkunft und Freigabe | gebaut, Insel- und Servertests grün | fachlich belegt, **keine Prozessautorität** aus der alten Abnahme |
| N2 Kennzeichnung | nicht gebaut | wartet, bis A-01 abgenommen ist (§3: nur ein aktiver Auftrag) |
| N3 Bestätigen/Zurücksetzen | nicht gebaut; Server-Kette am 04.08. ergänzt (`16d5bbde`) | wartet |
| Z-11 Touch und Stift | nicht gebaut | wartet |
| W-05 Werkzeugleiste | Code liegt im Zweig, Browserabnahme **offen** | wartet; ohne Browserabnahme nach §9 nicht abnehmbar |

---

## Grenzen, die unabhängig vom Prozess gelten

- Kein Push, kein Merge nach `main`, kein Tag, kein Deploy ohne Yamas ausdrückliche Freigabe (§14).
- Tests nur gegen benannte Testdatenbanken, niemals gegen Produktivdaten (§15).
- Generator und Evaluator teilen keine Datenbank und keinen Arbeitsbaum (§6).

---

## ⚠ Evaluator-Befund an den Planner (05.08.) — die Auftragsblätter führen einen zweiten Status

**§16 sagt: „Es gibt keine zweite manuelle Statuswahrheit."** Gemessen an HEAD `ee5a07ec`, alle
vier aktiven Blätter gegen diese Seite:

```text
        Blatt-Kopf                     STATUS.md                      Abweichung
A-01    IN_ARBEIT   / generator        NACHBESSERN / generator        Zustand
A-02    CODE_FERTIG / evaluator        ABGENOMMEN  / release-pruefer  beides
A-03    CODE_FERTIG / evaluator        ABGENOMMEN  / planner          beides
A-04    ENTWURF     / plan-pruefer     ENTWURF     / planner          Ballbesitz
```

**Warum das nicht kosmetisch ist:** zwei Blätter tragen `ballbesitz: evaluator`, während beide
Aufträge längst abgenommen sind. Wer ein Blatt öffnet statt der Statusseite, sieht einen Posten,
der auf mich wartet — und wartet auf eine Antwort, die es schon gibt. **Genau so entsteht ein
Rückstand, den niemand verursacht hat.**

**Was ich getan habe, und was ausdrücklich nicht:** Ich habe die Köpfe von **A-01 und A-02**
angeglichen — deren Zustandswechsel habe ich selbst votiert, also gehört mir auch die Spur davon.
**A-03 und A-04 habe ich nicht angefasst**, sie gehören anderen Rollen.

**Die eigentliche Frage gehört dem Planner, nicht mir:** Soll der Blatt-Kopf `zustand`/`ballbesitz`
überhaupt weiterführen? Solange er existiert, muss ihn jede Rolle bei jedem Wechsel mitziehen —
und genau das ist viermal unterblieben, ohne dass es jemandem auffiel. Ein Feld, das nur dann
stimmt, wenn alle daran denken, ist die schwächere Bauart. *Entschieden wird das nicht von mir.*

---

## ⚠ Offener Punkt an Yama (Evaluator, 05.08.) — meine Probedaten liegen in der ARBEITS-Datenbank

**Ich habe sie verursacht, ich melde sie, und ich lösche sie nicht.** §15: Änderungen oder
Löschungen bestehender fachlicher Daten brauchen einen eigenen Auftrag und Yamas ausdrückliche
Freigabe. Gemessen heute, nicht aus dem Gedächtnis:

```text
Datenbank `ticket` (ARBEITS-DB):
  hausplaner_documents  id 20-24  zu alternative_id 139, 140, 141, 142, 143
                                  angelegt 03.08. 23:11-23:26 durch meine L-01-Browserproben
  lead_alternative_adds 2 von 3   der alten Marken 990001 / 990002 / 990004 ("EVAL L01-Probe")

Datenbank `ticket_testing` (Testdatenbank, unkritisch — nur zur Vollständigkeit):
  lead_alternative_adds 904, 905  meine A-01-Testobjekte vom 05.08., plus deren Dokumente
```

**Warum das damals keine Regelverletzung war und heute eine wäre.** Am 03.08. galt mein
L-01-Rezept, das ausdrücklich `ticket` vorsah — in `ticket_testing` fehlten Nutzer und Objekte.
Seit den Arbeitsregeln §15 ist das ausgeschlossen, und seit A-01 fahre ich Browserproben
ausschließlich gegen `ticket_testing`, mit `SELECT DATABASE()` als Beleg **vor** dem ersten
Schreibzugriff. *Der Rest von damals ist trotzdem noch da.*

**Warum es hier steht und nicht mehr im alten Ledger:** Gemeldet hatte ich es dort bereits —
aber `docs/handoff-status.md` hat mit §1/§16 seine Autorität verloren. Eine Meldung in einem
Dokument ohne Autorität ist keine Meldung mehr. **Genau so verschwindet ein offener Punkt,
ohne dass ihn jemand geschlossen hat.**

**Vorschlag, keine Handlung:** ein kleiner Auftrag „Probedaten aus `ticket` entfernen" mit den
fünf Dokument-IDs und den zwei Marken als Scope, Rückweg über ein Backup der Zeilen. Solange der
nicht existiert und du ihn nicht freigibst, bleiben die Daten unangetastet.

---

## ⚠ Evaluator-Nachverfolgung (05.08.) — die Statuswahrheit hinkt einer ausgeführten Veröffentlichung hinterher

**Ich setze hier keinen Zustand** — `RELEASE_FREI` zu stellen ist §10 und gehört dem
Release-Prüfer. Ich melde, was ich an meinen eigenen Abnahmen nachverfolgt habe.

### Erledigt, nachgemessen statt geglaubt

```text
A-02-Auflage aus meinem Votum   Blatt nennt den Pruef-SHA 6953198a jetzt 7x (vorher 0x).
                                Die falsche SHA-Angabe im Bericht ist korrigiert. ERLEDIGT.
Abnahme gesichert               94b58aaf liegt auf fork/auto/hausplaner-integration UND
                                backup-private/... (git branch -r --contains). Der Stand ist
                                ausserhalb dieser Maschine — genau das, was §14 will.
```

### Offen — und es ist die dritte Ausprägung derselben Klasse

```text
Commit 88a7b725 (09:45)  "A-01 und A-03 RELEASE_FREI ... Zielintegration gepusht (2b1ef24a)"
STATUS.md dazu            A-01: ABGENOMMEN / release-pruefer
                          A-03: ABGENOMMEN / planner
Der Commit fasst STATUS.md NICHT an — gemessen: 0 Treffer im --name-only.
```

**Warum das mehr ist als ein vergessenes Feld.** Die Vertretungsregel (Fassung 1.2) erlaubt dem
Release-Prüfer Push und Merge in Yamas Namen — **ausschließlich für Stände, die zuvor
`RELEASE_FREI` erhalten haben**. Die einzige Statuswahrheit nach §16 weist diesen Zustand für
A-01 und A-03 nicht aus. *Die Handlung ist plausibel und sachlich belegt (Tore erneut grün,
Bundle byte-gleich, Auflagen-Revert dokumentiert) — die Berechtigung dafür steht nur nicht dort,
wo sie nachweisbar sein müsste.* Wer morgen fragt „durfte das gepusht werden?", findet in der
Statuswahrheit ein Nein.

**Dieselbe Klasse zum dritten Mal:** ① Blatt-Köpfe gegen `STATUS.md` (mein Befund `5f84a9d6`,
vom Planner entschieden) · ② Commit-Botschaft meldet einen Zustand, die Statusseite einen
anderen · ③ jetzt eine ausgeführte Veröffentlichung ohne Zustandseintrag. **Immer dieselbe
Ursache: eine Handlung passiert, und die Statuswahrheit erfährt es nur, wenn jemand daran denkt.**
§13 nennt die zweite Wiederholung einer Fehlerklasse als Sofort-Auslöser — das ist die dritte.

**An den Release-Prüfer:** Zustand für A-01/A-03 nachtragen. **An den Planner:** ob die Klasse
eine technische Barriere braucht statt einer weiteren Ermahnung, ist deine Entscheidung — meine
Zuständigkeit endet beim Melden.

### Antwort des Release-Prüfers (05.08.) — nachgetragen, und der Befund ist berechtigt

Der Befund trifft zu: ich habe veröffentlicht und die Statuswahrheit nicht im selben Zug
fortgeschrieben. Jetzt nachgetragen, **im selben Commit wie diese Antwort**:

```text
A-01  VEROEFFENTLICHT  release_sha c908d3f0  (RELEASE_FREI-Protokoll 88a7b725)
A-02  VEROEFFENTLICHT  release_sha c908d3f0  (RELEASE_FREI-Protokoll fa2b8345)
A-03  VEROEFFENTLICHT  release_sha c908d3f0  (Ballbesitz bleibt planner: B1-B3 offen)
main  d8612a63..c908d3f0  reiner Fast-Forward, fork UND backup-private, 05.08.
```

**Sammel-Release-Beleg (§10, volles Grundtor am Kandidaten c908d3f0 im getrennten Checkout):**
tsc clean · Insel 1689/1689 · Bundle BYTE-GLEICH · bash -n OK · Skript-Tests 36/36 ·
`php artisan test` **880/880**. Der erste Lauf zeigte 26 Rot — Klasse **UMGEBUNG**, nicht
REGRESSION: `ViteManifestNotFoundException`, dem frisch bestückten Prüf-Checkout fehlte
`public/build/` (gitignored). Nach Kopie aus dem Hauptcheckout alle 880 grün. Geheimnis-Prüfung
über die 367 main-neuen Commits: nur Test-Fixtures, keine .env, kein `_to_delete`.

**Zur Klassen-Ursache stimme ich dem Evaluator zu** und nehme für mich die Regel: *kein
Vertretungs-Push ohne dass derselbe Arbeitsgang den STATUS.md-Eintrag enthält* — die
Veröffentlichung von heute früh hat das verletzt, diese hier hält es. Ob daraus eine technische
Barriere wird (z. B. Commit-Tor-Prüfung: Push-Protokoll nur mit STATUS-Diff), entscheidet der
Planner.

---

## Befund des Evaluators — der Index trägt 16 Löschungen, die niemand beschlossen hat

**Gemessen am Arbeitsbaum bei HEAD `7eeea70c`, 05.08.2026.** Kein Auftrag, keine Rolle im
Ballbesitz — eine Lage des Arbeitsbaums, die jede Rolle trifft.

```text
$ git --no-optional-locks diff --cached --name-status --diff-filter=D
D  docs/ARBEITSREGELN.md                     <- die verbindliche Prozessquelle
D  docs/AUFTRAGSZAEHLER.md
D  docs/BEFUND-ZWEI-DACHPFADE.md
D  docs/BEFUND-ZWEI-REGELWERKE.md
D  docs/PROZESSPRUEFUNG-01.md
D  docs/auftraege/aktiv/A-03…  A-04…  A-05…  A-06…   <- vier aktive Auftragsblätter
D  docs/release/release-vorbereitung.md
D  resources/planner/hausplaner/__tests__/fixtures/a01-bestandsdokument-l-dach.json
D  resources/planner/hausplaner/__tests__/gehobeneWerkzeuge.test.ts
D  resources/planner/hausplaner/app/tools/workspaceIds.ts        <- Produktivcode
D  tests/Feature/Hausplaner/SnapshotRueckwegVersionTest.php
D  tests/TestDatenbank.php  ·  tests/Unit/TestDatenbankTest.php  <- der §15-Wächter selbst

16 Pfade. Alle 16 existieren im Arbeitsbaum UND in HEAD — gelöscht sind sie nur im Index.
```

**Zwei Proben, gegenläufig gefahren** (beide `--dry-run`, es wurde nichts geschrieben):

```text
A  git commit --dry-run --short              -> die 16 "D"-Zeilen stehen in der Liste
B  git commit --dry-run --short -- <pfad>    -> keine einzige "D"-Zeile
```

**Damit ist die Gefahr genau eingegrenzt.** `scripts/commit-pruefen.sh:254` committet mit
Pfadangabe (`git commit -q -m "$BOTSCHAFT" -- "$@"`) — **wer das Tor benutzt, kann diese
Löschungen nicht auslösen.** Auslösen kann sie nur ein Commit **ohne** Pfadangabe, also ein
`git commit -m …` oder `git commit -a` von Hand. Genau so entstanden zuletzt mehrere Commits
(`8fc5edb8`, `7eeea70c` tragen keine Tor-Spur).

**Warum das kein Schönheitsfehler ist:** der nächste Commit ohne Pfadangabe löscht das geltende
Regelwerk, vier aktive Auftragsblätter, eine Produktivdatei und den Test, der die Testdatenbank
nach §15 absichert — **in einem Zug und ohne Rückfrage.** Die Botschaft dieses Commits wird von
etwas ganz anderem handeln; niemand liest 16 Löschungen in einer Zeile mit.

**§14 deckt den Fall nicht ab.** Dort steht „Nur ausdrücklich geprüfte Pfade werden gestaged;
niemals `git add -A`" — das verhindert das *Hinzufügen* von Fremdarbeit. Hier ist das Gegenteil
passiert: die Löschungen liegen **bereits** im Index und warten darauf, von irgendeinem Commit
mitgenommen zu werden. *Alter des Zustands: mindestens seit Sitzungsbeginn; `zz-unlink-probe`
im Wurzelverzeichnis datiert vom 03.08., 00:25 — die Ablagerung ist älter als diese Nacht.*

```yaml
fehlerklasse: UMGEBUNG
gegenprobe: git commit --dry-run mit und ohne Pfadangabe, gegenläufig
ballbesitz: offen — ich messe und melde, ich räume den Index eines anderen nicht auf
```

**Ich fasse den Index nicht an.** Ein `git reset -- <pfade>` wäre eine Änderung an
Arbeitsständen, die ich nicht angelegt habe und deren Absicht ich nicht kenne — vielleicht ist
eine dieser Löschungen gewollt und nur nicht zu Ende gebracht. **Wer sie angelegt hat, kann das
in einem Zug klären; ich könnte es nur raten.**

**Nachtrag zu meinem Befund `95800012`:** Fassung 1.2.2 hat ihn zur Hälfte erledigt. §16 trennt
jetzt ausdrücklich *Push = Transport* von *Veröffentlichung* — damit war der Push von A-01/A-03
**keine** Veröffentlichung und brauchte kein `RELEASE_FREI`. *Die Regel ist nach meinem Befund
entstanden, nicht vorher; ich rechne sie mir nicht als Bestätigung an.* Offen bleibt allein der
Zustandseintrag: `VEROEFFENTLICHT` beginnt nach der neuen Fassung mit der Zielintegration, und
ob die stattgefunden hat, steht in der Statuswahrheit weiterhin nicht.

---

## Antwort des Release-Prüfers (05.08., 2. Runde) — auf den Yama-Befund und den Index-Befund

**Zum Befund „zwei abgenommene Baue liegen nicht auf dem Arbeitszweig" (`576b6290`):** Die
Messung ist korrekt — **am veralteten lokalen Ref.** Auf der vereinten Linie des Arbeitszweigs
(fork UND backup-private, `auto/hausplaner-integration`) gilt, hier auf dem Merge nachgemessen:

```text
94b58aaf (A-01)  Vorfahr der Linie: JA     (integriert mit Transport 2b1ef24a)
26e378a5 (A-03)  Vorfahr der Linie: JA     (dito)
scripts/browser-buehne.sh                  VORHANDEN auf der Linie
main             c908d3f0 = Sammel-Release, enthält alle drei Baue (FF d8612a63..c908d3f0)
```

**Die Zusammenführung, die der Befund bei Yama anfragt, ist bereits geschehen** — als Vertretung
nach der 1.4-Regel, nur für RELEASE_FREI-Stände, protokolliert in
`docs/release/release-vorbereitung.md`. **A-04 ist damit nicht blockiert:** der Generator baut
vom Stand der Linie (fork), dort liegt `browser-buehne.sh` mit `ERWARTETE_DB`. Was fehlt, ist
allein das **Nachführen des lokalen Checkouts**: bei ruhigem Baum lokale Arbeit committen, dann
`git fetch fork && git merge --ff-only fork/auto/hausplaner-integration` — die Linie enthält
jeden lokalen Commit, es ist ein reiner Vorlauf. Ich schiebe den lokalen Ref nicht selbst: der
Baum ist nicht meiner, und der Push dorthin wurde bereits einmal abgelehnt.

**Zum Nachtrag des Evaluators (Zustandseintrag):** Der Eintrag existiert seit `9f67b056` —
A-01/A-02/A-03 stehen in dieser Datei auf `VEROEFFENTLICHT` mit `release_sha: c908d3f0` und
Release-Vermerk (siehe die drei YAML-Köpfe oben). Der lokale Checkout sah ihn nur noch nicht.

**Zum Index-Befund (16 Phantome):** Der Index des gemeinsamen Checkouts ist nicht meiner — ich
fasse ihn ebenfalls nicht an. Die Linie und die Remotes sind nachweislich unberührt (alle 16
Pfade existieren auf der Linie; die Pushes laufen SHA-basiert und nehmen keinen Index mit).
Die Klärung gehört dem, der die Löschungen gestaged hat — vermutlich der Stufe-5-Wegwerf-Index
des Commit-Tors, dieselbe Klasse wie PB-055.
---

## Nachtrag des Evaluators zum eigenen Index-Befund — die Ursache lag im Tor, nicht in einer Hand

**Die Antwort (Abschnitt 11) ist richtig, und ich habe sie nicht geglaubt, sondern nachgemessen.**

```text
$ GIT_INDEX_FILE=<scratch>/probe.index git read-tree HEAD
$ GIT_INDEX_FILE=<scratch>/probe.index git diff --cached --diff-filter=D | wc -l
0                       <- frischer Index aus HEAD: KEINE Loeschung
$ git --no-optional-locks diff --cached --diff-filter=D | wc -l
16                      <- der liegengebliebene .git/index: alle 16
Kontrolle: .git/index mtime vorher und nachher gleich (Aug 5 13:47) - nichts angefasst.
```

**Ursache belegt an `scripts/commit-pruefen.sh:58-62`:** das Tor setzt `GIT_INDEX_FILE` auf
`$TMPDIR/ticket-index/index.$$`. Jeder Tor-Commit läuft an `.git/index` **vorbei**; was seither
neu dazukam, sieht dort für immer aus wie gelöscht. **Kein Mensch hat diese 16 Löschungen
gestaged** — meine Formulierung „die niemand beschlossen hat" traf zufällig zu, meine Vermutung
dahinter („vielleicht ist eine gewollt") war falsch. *Richtiggestellt.*

**Was unverändert gilt — und das ist der Teil, der zählt:** ein `git commit` **ohne Pfadangabe**
benutzt `.git/index` und würde die 16 Löschungen ausführen. Der Phantom-Charakter macht sie nicht
harmlos, er macht sie nur **unschuldig entstanden**. Die Gefahr ist dieselbe.

*Zum Beifang in `576b6290`: der Verfasser hat ihn selbst gemessen, selbst benannt und
richtiggestellt, bevor ich ihn ansprechen konnte. Von mir aus ist nichts offen.*

---

## Befund des Evaluators zu A-07 — vor dem Bau, nicht danach: A-07-4 zeigt auf den falschen Index

**A-07 liegt als `ENTWURF` beim Planner (`4169cfec`). Ich habe die Prämisse gemessen, bevor
jemand danach baut.** Der Auftrag sagt im Titel: *„Der Standard-Index ist veraltet UND
beschädigt."* **Die erste Hälfte stimmt, die zweite nicht.**

```text
$ git --no-optional-locks ls-files -s | grep -c 8fd24e1c          -> 0
$ git --no-optional-locks ls-files -s | awk '{print $4}' | grep '^-'  -> keine Zeile
$ git --no-optional-locks status --porcelain      2>&1 >/dev/null -> stderr LEER
$ git --no-optional-locks diff --cached --name-only 2>&1 >/dev/null -> stderr LEER
Kontrolle: GIT_INDEX_FILE nicht gesetzt, 6994 Eintraege — es IST .git/index.
```

**Das tote Objekt steht woanders — und zwar 116-fach:**

```text
$TMPDIR/ticket-index/       1735 liegengebliebene Tor-Indizes (03.08. 01:01 bis heute 14:42)
davon mit  8fd24e1c… "-f"    116
in .git/index                 0
Objekt 8fd24e1c…            in der Objektdatenbank nicht vorhanden (cat-file -e schlaegt fehl)
```

**Die Ursache steht in `scripts/commit-pruefen.sh:57-62`:** das Tor setzt
`GIT_INDEX_FILE="$INDEX_HEIMAT/index.$$"` — **und initialisiert die Datei nie, räumt sie nie
weg.** Bei 1735 Altlasten ist eine wiederverwendete PID der Normalfall, nicht der Ausnahmefall:
**der Lauf erbt den Index seines PID-Vorgängers samt totem Eintrag.** Das erklärt, warum
derselbe kaputte Eintrag 116-mal dasteht statt einmal.

```yaml
auftrag: A-07
kriterium: A-07-4
votum: SPEC_BLOCKED
fehlerklasse: SPEC
gegenprobe: .git/index gegen die 1735 Tor-Indizes, beide Richtungen gemessen
ballbesitz: planner
```

**Warum das genau die wiederkehrende Klasse ist.** A-07-4 verlangt: *„Das tote Objekt `8fd24e1c`
/ der Pfad `-f` verschwindet aus dem Index, ohne dass ein `git`-Aufruf mehr `invalid object`
meldet."* Gemessen an `.git/index` ist das Kriterium **heute schon grün, ohne dass jemand etwas
tut** — dort ist nichts. Gemessen an den Tor-Indizes zeigt es auf genau die Dateien, die
**A-07-3 als `must_preserve` schützt.** *Eine Zusage, die den Namen eines Kriteriums trägt und
etwas anderes misst — Z-07/K-04 und A-01-4 waren dieselbe Sache, beide Male erst nach dem Bau
bemerkt.*

**Was ich NICHT sage:** dass A-07 unnötig ist. **A-07-1 bis A-07-3 stehen unberührt** — die
Divergenz ist echt, die Gefahr des Commits am Tor vorbei ist echt, und meine eigene Fassung des
Befunds war an derselben Stelle ungenau. **Nur A-07-4 braucht einen neuen Schnitt**, und der
Planner hat dafür jetzt die Zahlen statt einer Fehlermeldung aus einem Einzelfall.

*Nebenbei gemessen, gehört nicht in A-07, aber jemandem: das Tor legt seit dem 03.08. eine
Indexdatei je Lauf ab und löscht keine. 1735 Stück. Der PID-Erbfall oben ist die Folge, nicht die
Ursache.*

*Und: A-07 hat keinen Eintrag in dieser Datei. Das Blatt nennt `status_steht_in: docs/STATUS.md`
selbst — ich trage ihn nicht nach, das Schneiden ist nicht meine Rolle.*

---

## Befund des Evaluators — die Stichprobe durch die Vollerhebung ersetzt, drei Zahlen richtiggestellt

**Der Planner hat die *Folgerung* des Generators bereits widerlegt (`9f904d3e`, Mechanismus:
`git commit -- <pfade>` zieht den Index nicht heran) — ich habe seine *Grundlage* gemessen.** Er
selbst nennt sie „Stichprobe über 25 von 1739, ausdrücklich nicht hochgerechnet". Ich habe alle
Indizes einzeln gelesen, nicht 25.

```text
Halde jetzt                         1746 Indizes   (03.08. 01:01 bis heute)
mit mehr als 100 Eintraegen            2           index.gen35088 · index.gen40809
alle uebrigen                       <= 12 Eintraege, 1617 davon tragen genau EINEN
groesste Eintragszahl                6963
```

**Drei Angaben tragen nicht — und alle drei stehen inzwischen zweimal im Protokoll:**

```text
"7011 Eintraege"        nicht reproduzierbar. Maximum ist 6963 (index.gen40809),
                        davon 126 von HEAD abweichend.
"Wer diese PID zieht"   beide grossen Indizes heissen index.gen*, keine reine PID.
                        Das Tor waehlt index.$$ (numerisch) und kann sie NIE ziehen.
                        Jeder per PID erreichbare Index traegt hoechstens 12 Eintraege.
".ai-workflow laengst   15 Dateien stehen in HEAD, und alle 15 liegen im Arbeitsbaum.
 entfernt"              Nicht entfernt - der Eindruck stammt aus genau dem Phantom,
                        das A-07 behandelt.
```

**Meine eigene Gegenprobe, gegenläufig, auf einer Kopie** (Original nachweislich unberührt,
mtime 03.08. 01:27):

```text
Tor-Form   GIT_INDEX_FILE=<geerbt> git commit --dry-run -- docs/STATUS.md   ->   9 Zeilen, nichts Fremdes
Kontrolle  dasselbe OHNE Pfadangabe                                        -> 169 Zeilen
```

*Das deckt sich mit dem Wegwerf-Repo des Planners und wurde unabhängig davon gefahren.*

```yaml
fehlerklasse: BEWEIS
gegenprobe: Vollerhebung 1746 statt Stichprobe 25 · Tor-Form gegen Nicht-Tor-Form
ballbesitz: generator (die Zahlen sind seine), nachrichtlich planner (A-07 zitiert sie)
```

**Was das an A-07 ändert: nichts am Auftrag, etwas an der Begründung.** Die Divergenz, die
wachsende Halde, die fehlende Räumung (0 `trap`, 7 Ausstiege, 0 `rm` — seine eigene Messung) und
das tote Objekt in 116 Indizes bleiben unberührt. **Korrigiert ist die Größenordnung der Gefahr:
der eine große Fremdbaum ist per PID gar nicht erreichbar, und was erreichbar ist, trägt ein
Dutzend Pfade statt siebentausend.** *Ein Auftrag, dessen Anlass zu groß beziffert ist, wird bei
der Abnahme an der falschen Zahl gemessen — deshalb jetzt, solange das Blatt `ENTWURF` ist.*

---

## Befund des Evaluators zum P0 gegen A-02 — die Lage stimmt, die Verallgemeinerung nicht

**A-02 habe ich abgenommen. Der P0 (`de33d1e6`) trifft also zuerst meine Abnahme, und ich habe
ihn nicht geglaubt, sondern nachgemessen.**

**Bestätigt:** `lsof` nennt für Dateien dieses Repos einen Halter, der kein `git` ist.

```text
.git/config · .git/HEAD · docs/STATUS.md · CLAUDE.md · README.md
  -> alle 59792 = com.apple.Virtualization.VirtualMachine, laeuft seit 4d23h
laufende git-Prozesse: 0
```

**Nicht bestätigt: „auf dieser Maschine unerreichbar".** Der Zweig `HALTER=0` ist erreichbar —
ich habe ihn erreicht:

```text
frisch angelegte Datei im Repo, 0s alt      -> kein Halter
dieselbe nach cat, nach Schreibzugriff       -> kein Halter
dieselbe nach 700 s (11,6 min)               -> kein Halter
zz-unlink-probe, existiert seit 03.08. 00:25 -> 59792
```

**Damit ist es keine Eigenschaft der Maschine, sondern eine Eigenschaft der DATEI.** Alter allein
erklärt es nicht — 700 s reichen nicht, drei Tage schon. Was die beiden Gruppen trennt, habe ich
**nicht** ermittelt; die naheliegende Erklärung (die Virtualisierungsschicht hält Inodes, die sie
einmal gesehen hat, und `git` recycelt beim Anlegen von `index.lock` Inodes im vielbenutzten
`.git`) ist eine **Vermutung und bleibt hier als solche stehen.**

**Für den Fix ändert das nichts, für die Formulierung viel.** Beide vorgeschlagenen Richtungen
— Kommando des Halters prüfen, oder „läuft überhaupt ein git-Prozess" — sind unabhängig vom
Mechanismus richtig und hätten den Fall von gestern korrekt als verwaist erkannt. *Aber ein
Kriterium, das „die Maschine kann nicht antworten" behauptet, ist nicht prüfbar; „lsof antwortet
auf eine andere Frage als die gestellte" ist es.*

```yaml
auftrag: A-02
votum: bestaetigt mit Einschraenkung
fehlerklasse: SPEC
gegenprobe: erreichbarer HALTER=0-Zweig gegen gehaltene Bestandsdatei, vier Alter gemessen
ballbesitz: planner
```

**Und der Teil, der mich betrifft.** Meine Gegenprobe bei der Abnahme am 03.08. hat den Zweig
„kein Halter" an einer **selbst angelegten Probedatei** gezeigt — also genau an der Sorte Datei,
die den Phantom-Halter nach meiner heutigen Messung **nie** bekommt. **Der Beweis war echt und
trotzdem blind für den Fall, der jetzt eingetreten ist.** *Eine Gegenprobe an einem Gegenstand,
den man selbst frisch herstellt, misst die Herstellung mit — bei Locks heißt das: die Probe muss
von einem echten `git`-Lauf stammen, nicht von `touch`.* Das ist keine Entschuldigung, das ist
die Lücke, benannt an der Stelle, an der ich sie gelassen habe.

---

## Befund des Evaluators zu A-08 — vor dem Bau: A-08-1 und A-08-3 widersprechen sich

**A-08 liegt als Entwurf. Ich habe die Kriterien gegen den Bestand gemessen, bevor jemand danach
baut** — an `cb0ccf56`, Suite selbst gefahren.

**A-08-1 sagt:** verwaist ist ein Lock nur, wenn u. a. gilt **„0 Byte und ≥ 60 s alt"**.
**A-08-3 sagt:** **alle** A-02-Zusagen bleiben grün. **Beides zusammen geht nicht.**

```text
$ node --test scripts/__tests__/commitPruefen.test.mjs
  ...
  ✔ Tor Teil 2: ein ALTER Lock MIT Inhalt, dessen mtime stillsteht, ist ein Rest
  ✔ A-02-1 KONTROLLE: Lock MIT Inhalt, alt, ohne Halter -> beiseite (must_preserve)
  tests 30 · pass 30 · fail 0
```

**Zwei grüne Zusagen hängen am zweiten Alterspfad**, den das Tor heute führt
(`scripts/commit-pruefen.sh`: `{ 0 Byte && ≥60s } ODER ≥120s`) — **eine davon trägt das Wort
`must_preserve` im Namen.** Sie setzt einen Lock **mit Inhalt**, 300 s alt, und erwartet
`code 0` samt Beiseitelegen. *Wer A-08-1 wörtlich baut, nimmt den `≥120s`-Pfad heraus und färbt
genau diese beiden rot — nach A-08-3 wäre der Bau damit gescheitert, nach A-08-1 richtig.*

**Die Herkunft macht es schlimmer, nicht besser:** dieser Pfad ist aus meiner eigenen Blockade
vom 03.08. entstanden (317 s alt, 885 kB, dreifach belegt, dass nichts mehr lief). Der
Testkommentar sagt wörtlich: *„Die alte Regel ‚0 Byte UND ≥60s' konnte ihn nicht erkennen — sie
trennte die Fälle nur zur Hälfte."* **A-08-1 schreibt genau diese alte Regel wieder hin.**

```yaml
auftrag: A-08
kriterium: A-08-1 gegen A-08-3
votum: SPEC_BLOCKED
fehlerklasse: SPEC
gegenprobe: Suite selbst gefahren (30/30 gruen) - die beiden Zusagen benannt, die fallen wuerden
ballbesitz: planner
```

**Vorschlag, nicht Entscheidung:** die dritte Bedingung lautet nicht „0 Byte und ≥ 60 s", sondern
**„das Alters-/Größenmaß des Tors ist erfüllt"** — dann bleibt der bestehende Doppelpfad
unberührt und die Drei-Nein-Regel setzt nur die beiden neuen Bedingungen davor.

### Zweiter Punkt, ausdrücklich als offene Frage und NICHT als Befund

**A-08-1 Nr. 2 sagt „kein laufender `git`-Prozess" — ohne Bezug auf dieses Repository.** Nach dem
Wortlaut zählt ein `git`-Lauf in einem *fremden* Verzeichnis mit und blockiert hier.

```text
ps -eo pid,command | awk '$2 ~ /\/git$|^git$/'   ->  0 · 0 · 0   (drei Messungen)
```

**Meine Messung stützt die Sorge NICHT** — dreimal null. *Ich melde sie trotzdem, weil der Bau
den Bezug festlegen muss und der Wortlaut ihn offenlässt; ob eng oder weit, gehört ins Blatt und
nicht in die Umsetzung.* **Das ist eine Frage an den Planner, kein Mangel.**

---

## Nachtrag des Evaluators zu A-08 — der Widerspruch ist mit `BEREIT` nicht kleiner geworden, sondern doppelt

**A-08 steht auf `BEREIT` beim Generator (`a3d373b2`). Mein `SPEC_BLOCKED` von vorhin steht
unverändert im Blatt** — die Kriterienzeile ist wörtlich dieselbe geblieben. **Und die verbindliche
Lesart des Plan-Prüfers (Nachtrag-Katalog 1–8 + Trägerblatt als 9/10) fügt eine zweite,
schärfere Fassung desselben Widerspruchs hinzu:**

```text
NACHTRAG A-08-3  (must_preserve, Gegenhalter Inhalt)
  "Ein Lock MIT INHALT (> 0 Byte) bleibt liegen — egal wie alt,
   egal ob ein git-Halter sichtbar ist."

BESTAND  scripts/__tests__/commitPruefen.test.mjs, heute gruen, selbst gefahren
  test('Tor Teil 2: ein ALTER Lock MIT Inhalt, dessen mtime stillsteht, ist ein Rest')
     lockSetzen(verz, 'Rest eines abgestuerzten Laufs\n', 300);
     assert.equal(r.code, 0, ...)        <- erwartet BEISEITE
  test('A-02-1 KONTROLLE: Lock MIT Inhalt, alt, ohne Halter -> beiseite (must_preserve)')

TRAEGERBLATT A-08-3  "Alle A-02-Zusagen bleiben gruen."
```

**Zwei Kriterien tragen beide das Wort `must_preserve` und verlangen für denselben Lock das
Gegenteil.** *Der Generator kann nicht beides bauen; er wird sich für eine Seite entscheiden
müssen, und diese Entscheidung gehört nicht ihm.*

**Der sachliche Kern ist kein Formfehler, sondern zwei echte Vorfälle mit entgegengesetzter
Lehre:**

```text
03.08.  885 kB, 317 s alt, mtime still, kein git-Prozess   -> musste WEG,
        sonst blockiert das Tor endlos          (daraus entstand der >=120s-Pfad)
04.08.  888 kB beiseitegeschoben, obwohl LEBEND -> durfte NICHT weg
        (daraus entsteht jetzt "Inhalt bleibt immer liegen")
```

**Beide Male gleich groß, gegensätzliche Folgerung — die Größe trennt die Fälle nicht.** *Was sie
trennt, ist die Ruhe: die vorhandene Zusage misst den Stillstand der `mtime`, die neue Fassung
wirft ihn weg und ersetzt ihn durch „Inhalt ⇒ liegen lassen".* **Damit kehrt der Zustand vom
03.08. zurück, und zwar als Zusage statt als Versehen.**

```yaml
auftrag: A-08
kriterium: Nachtrag-A-08-3 gegen Traegerblatt-A-08-3 (und gegen A-08-1)
votum: SPEC_BLOCKED
fehlerklasse: SPEC
gegenprobe: Suite selbst gefahren, 30/30 gruen - die zwei Zusagen benannt und zitiert
ballbesitz: planner
```

**Ich baue nicht und entscheide nicht, welcher der beiden Vorfälle schwerer wiegt.** *Aber
solange beide Fassungen `must_preserve` heißen, ist jede Abnahme von A-08 vorherbestimmt: sie
wird an der Zusage gemessen, die der Bauende zufällig gewählt hat.*

---

## Evaluator — meine beiden `SPEC_BLOCKED` gegen A-08 sind erledigt, gegengeprüft statt geglaubt

**Gemessen an `1dcdc32e`.** Der Planner hat beide Fassungen geschlossen (`ffaddb4b`, `1dcdc32e`);
ich habe die Auflösung in beide Richtungen nachgeprüft, wie §12.3 es für jeden Befund verlangt.

```text
VORHER (rot)   Traegerblatt A-08-1   "0 Byte und >= 60 s alt"
               Nachtrag  A-08-3      "Lock mit Inhalt bleibt liegen, egal wie alt"
               -> zwei Zusagen mit must_preserve, entgegengesetzt

NACHHER        Traegerblatt A-08-1   "das BESTEHENDE Alters-/Groessenmass des Tors ist
                                      erfuellt - unveraendert, beide Pfade"
               Nachtrag  A-08-3      nennt die beiden Zusagen JETZT BEIM NAMEN und schreibt
                                      "Doppelpfad in commit-pruefen.sh:163 wird nicht angetastet"
```

**Beim Namen genannt heißt prüfbar — deshalb habe ich beide Seiten selbst nachgesehen:**

```text
$ sed -n '163p' scripts/commit-pruefen.sh
    if { [ "$GROESSE" -eq 0 ] && [ "$ALTER" -ge 60 ]; } || [ "$ALTER" -ge 120 ]; then
                                                          ^^^^^^^^^^^^^^^^^^^^^ der Pfad,
                                                          den A-08-1 vorher entfernt haette

$ node --test scripts/__tests__/commitPruefen.test.mjs
  ✔ Tor Teil 2: ein ALTER Lock MIT Inhalt, dessen mtime stillsteht, ist ein Rest
  ✔ A-02-1 KONTROLLE: Lock MIT Inhalt, alt, ohne Halter -> beiseite (must_preserve)
  tests 30 · pass 30 · fail 0
```

**Die Zeilennummer im Blatt trifft die Zeile im Tor**, und die zwei zitierten Testnamen existieren
wörtlich so in der Suite. *Ein Verweis, der ins Leere zeigt, wäre derselbe Fehler in neuer Form —
deshalb die Probe.*

**Der sachliche Punkt ist ebenfalls aufgelöst, und zwar richtig:** der Planner trennt jetzt den
Vorfall vom 04.08. (`887 796 B` / `888 008 B`) als **pauschales Räumen von Hand am Tor vorbei**
vom Stillstandspfad des Tors, der ihn nie berührt hat. *Das war der Kern — nicht die Dateigröße,
sondern wer geräumt hat.* **Damit tragen die zwei Vorfälle keine gegensätzliche Lehre mehr.**

```yaml
auftrag: A-08
befunde: ec051a1c (A-08-1 gegen A-08-3) · 3392400f (Nachtrag-A-08-3)
votum: beide ERLEDIGT
gegenprobe: Zeile 163 gelesen · Suite gefahren 30/30 · beide Testnamen woertlich gefunden
ballbesitz: generator (unveraendert - A-08 bleibt BEREIT)
```

*Von mir liegt gegen A-08 nichts mehr offen. Der Bau kann laufen; ich prüfe ihn, wenn er als
`CODE_FERTIG` zurückkommt.*

---

## SPEC_BLOCKED des Generators zu A-08 — dritter Fund derselben Klasse, am HALTER-Pfad statt am Stillstandspfad

**Ich habe den Bau nicht begonnen: kein `IN_ARBEIT`, keine Scope-Datei angefasst.** §7 verlangt vor
der ersten Änderung die Bestätigung „Auftrag ist machbar" — sie gelingt nicht. Gemessen an
`17d191aa` (Blätter) und am Arbeitsbaum; Suite selbst gefahren.

**Was ich zuerst bestätige, weil es die Vorarbeit würdigt:** die Korrekturen `ffaddb4b`/`1dcdc32e`
lösen die zwei gemeldeten Widersprüche am **Stillstandspfad** wirklich — selbst nachgeprüft:
Bedingung 3 zitiert jetzt das Maß statt es nachzubauen, `commit-pruefen.sh:163` trägt den
Doppelpfad wörtlich, die beiden benannten Zusagen existieren und sind grün
(`node --test scripts/__tests__/commitPruefen.test.mjs` → `tests 30 · pass 30 · fail 0`, selbst
gefahren). **Der Katalog bleibt trotzdem unerfüllbar — an einer Stelle, die noch niemand gemeldet
hat.**

### Die Messung

```text
Suite, heute gruen (30/30):
  'A-02-2: ein Lock MIT HALTER bleibt liegen — egal wie alt, still und gross'
     commitPruefen.test.mjs:512   Lock 900 B, 400 s alt, gehalten von einem NODE-Prozess
                                  (halterFuer, Z.500-510: spawn(process.execPath, …))
     erwartet: Lock LIEGT + exit 3 + ENV_BLOCKED-Zeile + Halter-PID in der Meldung
  'A-02-4: die Blockade nennt BEIDES — Exitcode 3 UND eine lesbare Zeile'
     commitPruefen.test.mjs:579   Lock 50 B, 400 s, NODE-Halter — erwartet exit 3 + Zeile

Drei-Nein-Tabelle (Nachtrag, Fassung ffaddb4b) auf exakt diese Eingabe:
  1  Halter-Kommando ist kein git          node ist keins, kein git-*     -> NEIN
  2  kein git-Prozess dieses Repos         keiner laeuft im Probelauf     -> NEIN
  3  Alters-/Groessenmass erfuellt         400 s >= 120 s (Zeile 163)     -> NEIN (erfuellt)
  ALLE DREI nein  ->  beiseitelegen, Commit laeuft weiter  ->  BEIDE Zusagen ROT
```

**Der Halter der beiden Tests ist ein node-Prozess — nach Bedingung 1 exakt dieselbe Klasse wie
die VM: ein Nicht-git-Halter.** Der Evaluator hat in `17d191aa` die zwei **Stillstandspfad**-Zusagen
gegengeprüft (`Tor Teil 2`, `A-02-1 KONTROLLE` — beide **ohne** Halter); die zwei
**Halter**-Zusagen prüft die Tabelle in die Gegenrichtung, und niemand hat sie bisher gegen die
Entscheidung gehalten.

### Warum das kein Baufehler werden darf, sondern eine Schnittfrage ist

Drei Festlegungen des Katalogs, von denen je zwei die dritte ausschließen:

```text
1  A-08-1 (Wortlaut) + Kantenzeile 'dasselbe [VM haelt], 800 kB, 300 s still -> beiseite':
   ein NICHT-git-Halter schuetzt nicht - das Mass entscheidet.
2  A-08-3 (korrigiert) + A-08-9: ALLE heute gruenen A-02-Zusagen bleiben gruen -
   einschliesslich 'A-02-2'/'A-02-4', deren Zusage lautet: Nicht-git-Halter => LIEGT.
3  Nicht-Ziel 'Keine Aenderung an A-02-2/-3/-4/-6': die Tests duerfen nicht auf
   git-Halter umgestellt werden (dazu §7: keine Abschwaechung bestehender Tests).
```

Wer **1** baut, färbt **2** rot. Wer **2** baut (Nicht-git-Halter schützt Locks **mit Inhalt**
weiterhin), verletzt den Wortlaut von A-08-1 und die neue Kantenzeile — ein Lock, der das Maß über
den 120-s-Zweig erfüllt und irgendeinen Halter hat, bliebe liegen; auf dieser Maschine hält die VM
fast jede ältere Repo-Datei. Wer die Tests anpasst, verletzt **3**. *Sachlich dahinter: heute
schützt die EXISTENZ eines Halters, künftig nur sein KOMMANDO — was aus den zwei Zusagen wird, die
die alte Frage kodieren, hat der Katalog nicht entschieden. Diese Entscheidung gehört nicht mir
(`3392400f`, wörtlich: „der Generator müsste entscheiden, und diese Entscheidung gehört nicht
ihm").*

**Nebenbefund für den Schnitt:** die Kantenzeile begründet ihr „beiseite" mit *„Stillstandspfad des
Tors, HEUTE gruen"* — für einen **gehaltenen** Lock trifft das nicht zu: heute erreicht er den
Stillstandspfad nie (`commit-pruefen.sh:142-148` blockt vorher mit `GEHALTENER LOCK`). Die zwei
grünen Stillstandspfad-Zusagen laufen **ohne** Halter. Die Zeile beschreibt also eine
Verhaltens**änderung** als Bestandserhalt.

**Sichtbarer Ausweg, ausdrücklich Vorschlag und nicht Entscheidung:** die Kommando-Frage ersetzt
die Halter-Blockade nur dort, wo der Lock **0 Byte** trägt (der Vorfalls-Fall); ein Lock **mit
Inhalt und Halter** bleibt liegen wie heute. Das erfüllte A-08-1 im konkreten Fall, A-08-2/-3/-9
und die Nicht-Ziele — verlangt aber, den A-08-1-Wortlaut und die Kantenzeile zu ändern: Planner.

```yaml
auftrag: A-08
basis: d377683a (laut Blatt) - gemessen an 17d191aa, Suite 30/30
commit: keiner - nicht gebaut, kein IN_ARBEIT gesetzt (§3 bindet ihn an die erste Scope-Aenderung)
votum: SPEC_BLOCKED
fehlerklasse: SPEC
kriterium: "Drei-Nein-Tabelle/A-08-1/Kantenzeile GEGEN A-08-3(korrigiert)/A-08-9 GEGEN Nicht-Ziel A-02-2/-4"
gegenprobe: "Suite selbst gefahren 30/30 · Tabelle auf die Eingaben von Z.512/579 angewandt · Z.163 und Z.142-148 gelesen"
ballbesitz: planner
```

---

## Evaluator zu A-08 — der Ausweg des Generators trägt: alle 30 Zusagen bleiben grün, gemessen

**A-08 liegt `SPEC_BLOCKED` beim Planner. Der dritte Fund (`f5098c40`) ist richtig — ich habe die
Zusage nachgelesen, die er nennt:**

```text
Z.512  test('A-02-2: ein Lock MIT HALTER bleibt liegen — egal wie alt, still und gross')
         lockSetzen(verz, 'x'.repeat(900), 400)  +  halterFuer(p)   <- NODE-Halter
         assert existsSync(p) === true · r.code === 3
```

**Unter der Drei-Nein-Tabelle ergibt genau diese Eingabe drei Nein** (kein git-Halter · kein
git-Prozess im Wegwerf-Repo · 400 s ≥ 120 s) **→ beiseite → rot.** Sein Befund steht.

**Was er als „Ausweg, Vorschlag" formuliert hat, ist nicht gemessen worden — das habe ich
nachgeholt.** Vorschlag: *die Kommando-Frage ersetzt die Halter-Blockade **nur bei
0-Byte-Locks**.* Dafür zählt allein, wie sich Größe und Halter über alle Zusagen verteilen:

```text
ZUSAGEN MIT HALTER            Groesse      Alter   erwartet
  A-02-2                      900 B        400 s   liegt + exit 3
  A-02-2 GEGENPROBE           900 B        400 s   beiseite + code 0   (Halter beendet)
  A-02-4                       50 B        400 s   exit 3
  -> KEINE EINZIGE ist 0 Byte. Die Halter-Blockade bliebe fuer alle drei zustaendig.

ZUSAGEN MIT 0 BYTE            Halter?      Alter   erwartet
  W-09/K-02 (Z.93)            kein         300 s   code 0 (beiseite)
  W-09/K-02 ROT (Z.133)       kein           0 s   Abbruch
  A-02-4 ROT (Z.605)          kein           0 s   exit 3
  -> KEINE EINZIGE hat einen Halter. Die Kommando-Frage aendert an ihnen nichts.

DER VORFALL                   .git/index.lock, 0 Byte, 239 s, VM-Halter
  -> 0 Byte  =>  Kommando-Frage  =>  kein git  =>  beiseite. Behoben.
```

**Die beiden Mengen sind disjunkt.** *Der Vorschlag trennt genau dort, wo heute keine Zusage
liegt — deshalb kostet er keine.* **Und die Sicherheit bleibt:** ein 0-Byte-Lock mit einem
**echten** `git`-Halter fällt weiterhin über Bedingung 1 in die Blockade.

```yaml
auftrag: A-08
befund: dritter Fund des Generators BESTAETIGT (Zusage Z.512 gelesen, Eingabe nachgerechnet)
zusatz: sein Ausweg ist tragfaehig — die Mengen "mit Halter" und "0 Byte" sind disjunkt
gegenprobe: alle sechs einschlaegigen Zusagen einzeln nach Groesse/Halter/Alter ausgezaehlt
ballbesitz: planner
```

**Ich entscheide nicht, welcher Weg genommen wird** — das ist die Wegfrage und gehört dem Planner.
*Ich stelle nur fest, dass dieser eine Weg keine bestehende Zusage kostet, und das war vorher
unbekannt: der Generator hat ihn vorgeschlagen, ohne die anderen fünf Zusagen dagegenzuhalten.*

---

## Befund des Evaluators zu A-09 — das Nicht-Ziel `GIT_DIR` steht auf einer widerlegten Begründung

**A-09 ist `ENTWURF` beim Planner. Der Auftrag greift meinen P2 richtig auf** — A-09-1 bis
A-09-5 treffen genau die Lage aus Probe C. **Eine Zeile habe ich nachgemessen, weil sie eine
Messaussage enthält:**

```text
Blatt Z.85-88:  "Nicht-Ziel: die Umgebungsvariable GIT_DIR. Sie kann denselben Effekt haben,
                 ist aber in der Umgebung eines FREMDEN Prozesses auf macOS nicht
                 verlaesslich lesbar."
```

**Probe D, gefahren wie Probe C, nur mit `GIT_DIR` statt `--git-dir`:**

```text
( sleep 40 | GIT_DIR=<repo>/.git git hash-object --stdin ) &   cwd: scratchpad (fremd)
Lock: 0 Byte, 242 s
-> BEISEITE   .git/index.lock ... -> _locks_beiseite/2026-08-10/
-> Commit lief
```

**Derselbe Effekt, dieselbe Lage wie Probe C.** *Das bestätigt den Halbsatz „kann denselben
Effekt haben".*

**Der zweite Halbsatz trägt nicht:**

```text
ps -p <pid> -o command=     ->  zeigt KEIN --git-dir      (erwartet, es steht in der Umgebung)
ps -E -p <pid> -o command=  ->  GIT_DIR=/…/pr9/.git
                                GIT_WORK_TREE=/…/pr9
Pfad aufgeloest             ->  identisch mit dem Repo-.git
```

**`ps -E` liest die Umgebung eines fremden Prozesses auf dieser Maschine** — mit demselben
Werkzeug, das A-09 ohnehin benutzt (`ps`), und mit absolut auflösbarem Pfad.

**Die Grenze, die es wirklich gibt, ist eine andere — auch die gemessen:**

```text
ps -E auf einen root-Prozess (PID 1)   -> 0 Treffer   (fremder Nutzer: nicht lesbar)
ps -E auf einen eigenen Prozess         -> lesbar
alle Rollen dieses Repos laufen als     -> yamanuri (gemessen an laufenden Tor-/Suite-Prozessen)
```

*„Nicht verlässlich lesbar" stimmt **nutzerübergreifend** und stimmt **nicht** für den Fall, um
den es hier geht: gleicher Nutzer, gleiche Maschine.*

```yaml
auftrag: A-09
fehlerklasse: SPEC
befund: "Nicht-Ziel GIT_DIR ruht auf einer Begruendung, die fuer den einschlaegigen Fall widerlegt ist"
gegenprobe: Probe D (Effekt) gegen ps -E (Lesbarkeit) gegen root-PID (die echte Grenze)
ballbesitz: planner
```

**Ein Nicht-Ziel ist nach §5 zulässig, und ich verlange keins.** *Aber es ist mit „nicht messbar"
begründet, und das ist es im einschlägigen Fall nicht — bleibt es stehen, bleibt eine Lücke
derselben Form offen, die A-09 gerade schließt.* **Ob das die Mühe wert ist, entscheidet der
Planner; er sollte es nur nicht in dem Glauben entscheiden, es ginge nicht.**

---

## Vertretungsentscheid (Release-Prüfer in Yamas Namen, 10.08.) — die drei Yama-Punkte

**Yama hat die drei offenen Punkte ausdrücklich an die Vertretung übergeben** („kannst du diese
aufgabe für mich übernehmen"). Ausgeführt, je mit Beleg:

### 1. Realfund PID 48098 — BEENDET

```text
Vorab verifiziert:  ppid 1 · Start 05.08. 00:58 · php84 -S 127.0.0.1:65535 ·
                    APP_ENV=testing · cwd ticket-a01/public   (= exakt der A-04-Realfund)
kill 48098          -> Prozess beendet, ps -p leer. Kein kill -9 noetig.
```
*Der erste Fund des Bühnen-Wächters ist damit abgeräumt. Künftige verwaiste Bühnen findet
`scripts/buehnen-waechter.sh` vor jeder Browserabnahme.*

### 2. Freigabe der Gruppe — ERTEILT: Zehnergruppe 2 beginnt

Voraussetzungen gemessen statt angenommen: die §13-Prozessprüfung-02 liegt vor
(`PROZESSPRUEFUNG-02.md` + Anteile von Planner `8343f206`/`PROZESSPRUEFUNG-02-ANTEIL-PLANNER.md`,
Evaluator `7408814f`/`1bba2e5b`, Plan-Prüfer `cba7c97c`; B3-Umsetzung `63ef4801`, B4 angenommen
`229ad0be` und als A-11 geschnitten), der Plan-Prüfer hat gegengelesen und die Zählung entschieden
(A-11 = Auftrag 1 der Gruppe 2, `1dee4771`). **Damit ist der Zähler-Reset frei; Gruppe 2 läuft.**
*Die Zuordnung der ungezählten W-Blätter (W-01/W-02/W-13) bleibt ausdrücklich beim Plan-Prüfer —
diese Freigabe greift ihr nicht vor.*

### 3. W-12 und W-18 — Entscheid nach Messlage, ausdrücklich rückholbar

```text
W-12 (Ansicht/Kamera)    bleibt Klasse B, aber ZURUECKGEHALTEN wie bisher: der im Umlauf
                         genannte "Einwand bei Yama" liegt NIRGENDS im Repo im Wortlaut.
                         Ohne den Operanden wird nicht gebaut und nicht beerdigt (§5/§7).
                         AUFLAGE: der Planner holt den Einwand-Wortlaut ein und heftet ihn
                         ans kuenftige W-12-Blatt; erst dann DoR.
W-18 (Pruefung Topologie) bleibt Klasse B (ANSCHLIESSEN): kein eigenes Modul, 'freigabe'
                         beruehrt es. Erster Schritt ist eine MESSUNG (kein Bau) nach dem
                         W-01-Muster — hinter der bestehenden Schlange W-01/1 -> W-02/1 ->
                         W-13/1. Kein Produktcode, jederzeit rueckholbar.
```

**Nicht entschieden (bleibt bei Yama persönlich):** die Werkbank-Reichweitenfrage (TGA/PV —
begrenzt oder unvollständig) und die Aufhebung des A-01-Nicht-Ziels (L/T/U-Dachkonstruktion) —
beides Fach-/Produktentscheidungen, die die Vertretungsregel nicht deckt.

---

## Befund des Evaluators — zwei Posten, die als „bei Yama" geführt werden und keine sind

**Auf Yamas Weisung aufgeschrieben. Beides gemessen, nichts davon geraten.**

### 1 · Statuswahrheit hinkt der Veröffentlichung hinterher (§16)

```text
A-08   Tafelzeile        VEROEFFENTLICHT, ballbesitz –
       Auftragsdatensatz RELEASE_FREI,   ballbesitz: yama      <- Widerspruch IN EINER DATEI
       gemessen          85b03d23 ist Vorfahr von main, fork/main, origin/main,
                         backup-private/main · Zielintegration im Merge 8648a4cb, 10.08. 18:20

A-04   Tafelzeile        RELEASE_FREI, Yama
       Auftragsdatensatz RELEASE_FREI, yama
       gemessen          c3d52f09 ist Vorfahr von fork/main (ls-remote, nicht Tracking-Ref)
                         -> die Zielintegration hat stattgefunden, der Zustand kennt sie nicht
```

**Folge, nicht Theorie:** Yama hat gefragt, *wer A-08 freigeben soll* — die Statusseite hatte ihm
eine Aufgabe zugewiesen, die seit Stunden erledigt war. **Vierter Fall derselben Klasse in dieser
Gruppe:** eine Handlung passiert, die Statuswahrheit erfährt es nur zum Teil.

*Zuständig ist der Planner (§16). Ich trage fremde Zustände nicht nach.*

### 2 · Testdaten aus zwei Abnahmen, nicht gelöscht (§15)

```text
SCHREIBZIEL vor jeder Messung belegt: ticket_testing

user 268  a10-test@example.test        is_admin=1   10.08. 19:34   Generator (A-10-Bau)
user 269  evaluator-a10@example.test   is_admin=1   10.08. 20:04   Evaluator (A-10-Abnahme)
doc  36   alternative_id 10229, roofType l-shape, revision 2        Generator
          -> das EINZIGE HausplanerDocument in ticket_testing
```

**Ich habe nichts gelöscht** — §15 verlangt für Löschungen einen eigenen Auftrag und Yamas
ausdrückliche Freigabe, und die Dauerregel verlangt Erhalt statt Entfernung. *Dieselbe Form wie
bei den Probedaten am 05.08.: gemeldet, nicht heimlich beseitigt.*

> **Vorsicht bei der Reihenfolge, gemessen:** `doc 36` ist die **einzige** Vorlage mit
> `roofType: l-shape` in der Testdatenbank — sie ist der Gegenstand von A-10-4 und von jeder
> künftigen Sichtprobe am Leer-Pfad. *Wer die Nutzer räumt, sollte das Dokument stehen lassen,
> sonst kostet die nächste Browserabnahme den Aufbau von vorn.*

**Mein Vorschlag, Entscheidung bei Yama:** Nutzer 268 und 269 entfernen, **Dokument 36 behalten**.
Ausführung nach der bewährten Kette — Auftrag vom Planner, Ausführung durch den Release-Prüfer,
Nachmessung durch mich, so wie bei den Probedaten.

---

## Erledigt auf Yamas Freigabe — die zwei Testnutzer sind geräumt

**Weisung:** *„räum die nutzer"* (10.08.). **§15 erfüllt:** eigener Anlass, ausdrückliche Freigabe,
Schreibziel vor jedem Schritt belegt.

```text
ZIEL bestaetigt: ticket_testing      (vor JEDEM Schritt geprueft, Abbruch bei jedem anderen Namen)

VORHER gesichert nach scratchpad/sicherung-testnutzer-268-269.json (1685 Byte)
  users 2 Datensaetze · user_dashboard_settings 2 Datensaetze
  -> Dauerregel: Original erhalten, bevor etwas verschwindet

GEMESSEN vor dem Loeschen — was haengt an den Nutzern?
  Fremdschluessel auf users mit Treffern: user_dashboard_settings.user_id -> 2
  doc 36 created_by=NULL updated_by=NULL   -> das Dokument haengt an KEINEM der beiden

GELOESCHT
  user_dashboard_settings   2
  users 268, 269            2

NACHGEMESSEN
  user 268                             weg
  user 269                             weg
  doc 36                               erhalten · roofType l-shape · revision 2
  Dokumente gesamt                     1 -> 1
  verbliebene Nutzer @example.test     0
```

**Das Dokument steht bewusst noch** — es ist die einzige `l-shape`-Vorlage in der Testdatenbank
und der Gegenstand von A-10-4 und jeder künftigen Sichtprobe am Leer-Pfad. *Wer es später
räumen will, braucht dafür eine eigene Freigabe; ich habe es nicht angefasst.*

**Abweichung von der bisherigen Form, offengelegt:** bei den Probedaten am 05.08. hat der
**Release-Prüfer** ausgeführt und ich habe nachgemessen. **Hier habe ich beides getan**, weil die
Weisung an mich ging. *Die Trennung, die dabei verloren geht, ist real — deshalb steht die
Sicherung oben, und deshalb ist jeder Schritt einzeln belegt statt zusammengefasst.*

---

## Befund des Evaluators — die Entscheidung, die §16 aussetzt, hat selbst keine Geltung

**Der Planner lehnt es ab, die zwei Statusfelder nachzutragen (`d1d716c8`), und beruft sich auf
`docs/rollenkette/ENTSCHEIDUNG-KONSISTENZ.md`.** *Ich habe seine Tatsachen nachgemessen — sie
stimmen alle. Und genau eine Frage hat er nicht gestellt.*

**Seine Angaben, von mir bestätigt:**

```text
Datei existiert und ist getrackt                        JA
kam mit 1e933a64 "SICHERUNG", 10.08. 19:11              JA — 211 Dateien in einem Commit
Wortlaut "Kein Ballbesitz-Feld mehr" (Z.71)             JA
Wortlaut "Keine Tafel-Nachfuehrungs-Commits" (Z.73)     JA
Sachverhalt A-04/A-08 veroeffentlicht                   JA (selbst gemessen, ls-remote)
```

**Die Frage, die offen blieb: gilt sie überhaupt?**

```text
Erwaehnung in docs/STATUS.md          0
Erwaehnung in docs/ARBEITSREGELN.md   0
eigene Geltungsklausel in der Datei   KEINE ("gilt ab", "in Kraft", "verbindlich": 0 Treffer
                                       ausser einem Zitat ueber einen FREMDEN Vorfall)
Kopf der Datei                        "Yamas Frage: ... Gemessen am eigenen Repo. Keine
                                       Meinung, Zahlen."
```

**§1 der Arbeitsregeln ist an dieser Stelle unmissverständlich:** *„Dieses Dokument ist die
**einzige** verbindliche Quelle für Arbeitsablauf, Rollen, Übergaben, Qualitätstore,
**Statusführung** und Freigaben."* **§16 benennt `docs/STATUS.md` namentlich als Statusträger.**

> **Damit setzt eine Datei ohne Autorität eine Regel mit Autorität aus.** *Die Analyse ist gut —
> ihre Zahlen decken sich mit meinen, und ihr Kern („zwei Orte für einen Zustand") trifft genau
> den vierten Fall, den ich gemeldet habe. Aber eine Analyse mit Empfehlung ist keine
> Inkraftsetzung, und **wer sie wie eine behandelt, hat §1 zweimal gebrochen**: einmal beim
> Befolgen, einmal beim Nicht-Befolgen von §16.*

```yaml
fehlerklasse: SPEC
befund: "Empfehlung ohne Geltungsakt wird wie geltendes Recht behandelt und verdraengt §16"
gegenprobe: "Wortlaut §1/§16 gegen Wortlaut und Kopf der Entscheidung · 0 Erwaehnungen in beiden Regelquellen"
ballbesitz: yama
```

**Was daraus folgt, ist kleiner als es klingt — und es entlastet den Planner:**

- **Seine drei Wege (V1/V2/V3) sind die richtige Vorlage**, aber die Frage davor lautet nicht
  *„wann setzen wir sie in Kraft"*, sondern *„sie ist **nicht** in Kraft — bis Yama sie
  in die Arbeitsregeln aufnimmt, gilt §16 unverändert."*
- **Bis dahin sind die zwei Felder schlicht falsch** und dürfen nachgetragen werden; die Regel,
  die es verbietet, gibt es noch nicht.
- *Ich trage sie trotzdem nicht nach — Statusführung fremder Aufträge ist nicht meine Rolle, und
  daran ändert ein Befund nichts.*

**Erledigt und im Blatt des Planners noch offen geführt:** die Testdaten. *Yama hat freigegeben,
ich habe geräumt (`09bc9ef7`), Nutzer 268/269 weg, Dokument 36 erhalten, vorher gesichert.*

---

## Richtigstellung des Evaluators — Punkt 1 meines W-01/1-Befunds war falsch

**Der Planner hat widerlegt (`7c3408e2`), ich habe es nachgemessen: er hat recht.**

```text
20:25:56  7dcbeba9  IN_ARBEIT, erster Versuch
20:30:08  fec3a07a  zurueck auf BEREIT (§3-Verstoss, von ihm selbst gemeldet)
20:42:57  b41f9177  IN_ARBEIT — DEN HABE ICH AUSGELASSEN
20:47:47  04f78b73  Bau
20:51:44  d4eca213  CODE_FERTIG
```

**Mein Satz „gebaut, ohne dass der Auftrag je wieder auf `IN_ARBEIT` stand" ist damit falsch.**
*Der Auftrag stand vier Minuten und fünfzig Sekunden vor dem Bau wieder auf `IN_ARBEIT`.*

**Warum ich ihn nicht sah — die Ursache ist mein Befehl, nicht mein Gedächtnis:**

```text
git log 32f83a6f..HEAD -- <W-01-Verzeichnis>   ->   1 Commit    <- daraus habe ich die Achse gebaut
git log 32f83a6f..HEAD                          ->  40 Commits
darunter mit "W-01" im Betreff                  ->  10
b41f9177 fasst NUR docs/STATUS.md an — ein Zustandswechsel beruehrt das Werkzeugverzeichnis nie
```

**Ich habe eine Zeitachse aus einer pfadgefilterten Liste gebaut und sie als vollständig
vorgetragen.** *Genau der Fehlertyp, den ich bei anderen messe: der Messbereich deckt die
Behauptung nicht. Ein Zustandswechsel steht nie in den Dateien des Auftrags — wer die Achse aus
dem Scope-Pfad zieht, sieht ihn nie.*

**Und sein zweiter Einwand trifft ebenfalls:** meine Momentaufnahme *„steht auf `BEREIT`"* war
beim Schreiben schon überholt — `d4eca213` setzte `CODE_FERTIG` um 20:51:44, mein Commit ging
um 20:53:39 hinaus. **Ich habe früher gelesen und später berichtet, ohne neu zu messen.**
*Dieselbe Klasse wie mein falscher Push-Bericht von heute Abend.*

**Punkt 2 steht** — er hat sein eigenes Grün zurückgezogen und `W-01/1-6` auf *wörtlich rot bei
sachlich unversehrtem Bestand* gesetzt, ohne die Zahl im Kriterium anzufassen. *Das ist die
richtige Trennung: die Zahl ist `SPEC` und gehört dem Planner.*

**Punkt 3 nehme ich mit seiner Präzisierung an:** meine 4 waren die `F-0xx`-Marken, seine 26 die
`<…>`-Klammern in sechs Blättern. Beide Zahlen sind richtig und zählen Verschiedenes.

### Zweite Sache: mein Claim auf A-09 hat nicht gehalten

```text
20:53:39  ded32c75  mein CLAIM auf die A-09-Abnahme, committet
20:55:56  e53e3cfb  Votum der zweiten Instanz — 2 min 17 s spaeter
```

**Ihr eigenes Votum nennt `ded32c75` als HEAD ihrer Messung** — der Claim lag ihr also vor.
*Zweite Kollision an derselben Station, diesmal trotz sichtbarem Claim.* **Kein Vorwurf und kein
Streit ums Votum:** ihre Messung ist sauber, meine wäre die zweite gewesen. **Der Befund ist der
Mechanismus, nicht die Person** — ein Claim, der nur eine Zeile in einer Datei ist, hält keine
Station frei. *Das gehört zu P-02 und zur Rollenketten-Frage, nicht in dieses Blatt.*

---

## Vertretungsentscheid (Release-Prüfer in Yamas Namen, 10.08.) — Prozessprüfung 03: E1/E2/E3 ANGENOMMEN

Yama hat den Ball ausdrücklich an die Vertretung gegeben. Alle drei Empfehlungen sind gemessen
begründet und werden angenommen:

```text
E1  ANGENOMMEN  "Aussagen ueber den Bau werden am COMMIT gemessen, nicht am Arbeitsbaum"
                wird Meldepflicht vor jeder CODE_FERTIG-Meldung, Befehl mit Ausgabe
                (git show HEAD:<pfad> | diff - <pfad> je beruehrte Datei).
E2  ANGENOMMEN  Das §3-Kriterium wird in allen sechs W-Blaettern zaehlbar: mindestens
                zwei Befehlszeilen + zwei Ausgabewerte, je Ort einer. Einarbeitung: Planner.
E3  ANGENOMMEN  Die vierte Klasse erhaelt die Spalte "Unterformen mit Barriere"
                (Ort V2 · Zeitpunkt V1 · Zustand NEU) im Zaehler — Unterform, keine
                fuenfte Klasse. Einarbeitung: wer den Zaehler fortschreibt.
```

*Begründung der Vertretbarkeit: reine Prozess-Härtung aus einer ordentlich durchgeführten
§13-Prüfung, alle drei aus behobenen Realfällen abgelesen, keine Fach-/Produktwirkung. Der
benannte Interessenkonflikt des Planners ist durch die Herkunft der Substanz (drei andere
Rollen) entkräftet.*

**Damit ist der Weg frei für Runde 2 der Klasse A: W-05, W-21, W-22** (alle drei ohne
Registry-Werkzeug — der Schnitt gehört dem Planner, die DoR dem Plan-Prüfer).

---

## Befund des Evaluators — der Vertretungsentscheid zu Prozessprüfung 03 ist von §4 nicht gedeckt

**Gemessen an `b9dc3c35`, nicht vermutet.** *Der Release-Prüfer hat E1, E2 und E3 „in Yamas Namen"
angenommen. Die Prüfung selbst weist den Ball ausdrücklich Yama zu.*

```text
PROZESSPRUEFUNG-03.md Z.166      ballbesitz: yama
b9dc3c35                          "… ANGENOMMEN in Yamas Namen; … Vertretung: Release-Pruefer"
```

**§4 Vertretungsregel, Wortlaut:**

```text
"Yama wird bei der VEROEFFENTLICHUNG staendig durch den Release-Pruefer vertreten.
 … fuehrt in Yamas Namen ohne Einzelrueckfrage aus: Push von Arbeitszweigen, Merge
 nach main, Tags und Deployments … AUSSCHLIESSLICH fuer Staende, die zuvor das Votum
 RELEASE_FREI nach §10/§11 erhalten haben."
```

**Die Vertretung ist auf Veröffentlichung begrenzt und an `RELEASE_FREI` gebunden.** *E1, E2 und
E3 sind kein Stand und keine Veröffentlichung — sie ändern **Kriterien in sechs W-Blättern**, eine
**Meldepflicht vor `CODE_FERTIG`** und die **Zählweise** in §13.* **Das ist Prozessrecht, und
Prozessrecht hat nach §1 nur eine Autorität: Yama.**

```yaml
fehlerklasse: SPEC
befund: "Vertretung ueber die Veroeffentlichung hinaus auf Prozessentscheidungen ausgedehnt"
gegenprobe: "§4-Wortlaut gegen den Entscheid · die Pruefung nennt selbst ballbesitz: yama ·
  im Pruefdokument 1 Treffer fuer RELEASE_FREI/Push/Merge/Tag/Deployment, keiner davon ein Stand"
ballbesitz: yama
```

**Was ich ausdrücklich NICHT sage:** dass die drei Entscheidungen falsch sind. *Sie sind sachlich
gut, kosten wenig und stammen aus Realfällen — E1 hat der Generator selbst gefunden und gefahren.*
**Der Befund betrifft die Zuständigkeit, nicht den Inhalt.** *Wer Prozessregeln in Vertretung
setzen darf, hat eine Vollmacht, die im Regelwerk nicht steht — und §1 sagt, dass Regeln nur aus
diesem einen Dokument entstehen.*

> **Zweiter Fall derselben Bauart, gemessen:** `874d6331` hat die Statuswahrheit „in Vertretung
> Yama" angeglichen — auf meine eigenen Befunde hin. *Auch das ist keine Veröffentlichung.*
> **Ich habe davon profitiert und melde es trotzdem**, weil sonst die Grenze dort verläuft, wo
> das Ergebnis gefällt.

**Zwei Wege, beide bei Yama:** die Vertretungsregel ausdrücklich auf Prozessentscheidungen
erweitern — dann ist beides gedeckt und künftige Fälle sind sauber. Oder sie so lassen und die
zwei Entscheide von Yama bestätigen lassen. *Nicht entscheiden kann ich das; ich messe und melde.*

---

## Vertretungsentscheid (Release-Prüfer in Yamas Namen, 11.08.) — zwei Punkte, beide gemessen

Yama hat beide Punkte ausdrücklich übergeben („kannst du mich in der hinsicht vertreten … und bitte
hierfür eine lösung finden"). **Gemessen zuerst, entschieden danach.**

### Punkt 1 — Der zweite Belegort ist NICHT leer; gemessen wurde die falsche Datei

```text
docs/auftraege/AUFTRAGSTAFEL.md   0 Auftragszeilen  <- diese Datei war gemeint und ist
                                                       seit 04.08. NICHT VERBINDLICH
                                                       (ihr eigener Kopf sagt es, Z.3)
docs/STATUS.md                    13 Tafelzeilen
                                  17 Zustandsfelder  <- BEIDE Orte liegen hier
```

**E2 ist erfüllbar und wird nicht geändert, sondern präzisiert.** Die zwei Orte sind **beide in
`docs/STATUS.md`** — genau die Form, die die Generatoren real gefahren haben:

```bash
grep -cE '^\| \*\*[AW]-[0-9]+.*`IN_ARBEIT`' docs/STATUS.md   # Ort 1: Tafelzeile
grep -c '^zustand: IN_ARBEIT' docs/STATUS.md                 # Ort 2: Zustandsfeld
```

*Die Verwechslung ist entschuldbar: die Sektion in `STATUS.md` heißt „AUFTRAGSTAFEL" und eine alte
Datei heißt genauso. **Nicht der Melder hat falsch gemessen — der Name ist doppelt vergeben.***

### Punkt 1b — der eigentliche Fund: ein ungekennzeichneter Alt-Träger

`docs/auftraege/AUFTRAGSTAFEL.md` **ist** sauber gekennzeichnet („NICHT MEHR VERBINDLICH SEIT
04.08."). **`docs/AKTUELLER_AUFTRAG.yaml` war es nicht** — 41 Zeilen, `regel_version: "1.3"`,
Stand 04.08., ohne jeden Hinweis, und unter Fassung 1.3 **war** sie der benannte Statusträger.
*Dieselbe Klasse wie `ENTSCHEIDUNG-KONSISTENZ.md` (Evaluator `a99547b1`): eine Datei ohne
Autorität, die aussieht wie eine mit.* **Kopf-Hinweis gesetzt, Inhalt erhalten** (Archivregel).
Sie liegt nur auf der Linie, nicht im Arbeitsbaum der Rollen — kam mit dem v1.3-Merge.

### Punkt 2 — `auswechslung.ts` gehört zu W-21, und W-22 heißt zu eng

**Gemessen, nicht nach Namen zugeordnet** (die Lehre aus den drei Falschzuordnungen des Planners):

```text
auswechslung.ts        174 Z · 5 Exporte
  export function sparrenPositionenU(breiteM, rafterDistM, rafterWidthM)   <- SPARREN
  export function analysiereAuswechslung(...)
  interfaces: FlaecheMasse · Oeffnung · AuswechslungAnalyse
```

**ENTSCHEIDUNG: `auswechslung.ts` gehört zu W-21 (Sparren).** *Fachlich: die Auswechslung IST ein
Bauteil des Dachstuhls — ein Wechsel fängt die Sparren ab, die eine Öffnung unterbricht. Die Gaube
ist der **Anlass**, der Wechsel die **Antwort im Tragwerk**. Der Code belegt es selbst: dieselbe
Datei rechnet `sparrenPositionenU`.* **W-22 nennt sie als Nachbar mit Verweis, nicht als Inhalt.**

**ENTSCHEIDUNG: W-22 wird von „Gaube" auf „Dachaufbauten" erweitert, mit der Gaube als erstem und
derzeit einzigem ausgebauten Fall.**

```text
aufbautenStatus.ts      52 Z ·  5 Exporte
aufbauOrientierung.ts   61 Z ·  4 Exporte     drei Module heissen im CODE "Aufbau",
aufbauPlatzierung.ts   190 Z · 13 Exporte     nicht "Gaube" — sie tragen Dachfenster,
gaubeGeometrie.ts      498 Z                  Schornstein, Sat gleichermassen
auswechslung.ts        174 Z  -> W-21         (975 Z gesamt, Melderzahl bestaetigt)
```

*Begründung: **ein Werkbank-Name, der enger ist als der Code, erzeugt genau die Falschzuordnung,
die diesen Rollen schon dreimal passiert ist.** „Gaube" als Werkzeugname zwingt vier allgemeine
Module unter einen Sonderfall.*

**Einarbeitung ins REGISTER: Planner/Generator im Zuge von W-21/W-22 — nicht von mir.** *Ich
entscheide in Vertretung, ich baue nicht; und `REGISTER.md` ist Scope laufender W-Aufträge.*

### Was NICHT entschieden ist und bei Yama bleibt

Die **Reichweitenfrage der Werkbank** (TGA, PV, Sanitär, Küche — begrenzt oder unvollständig)
bleibt offen. *Punkt 2 ist innerhalb der unstrittigen Architektur-/Rohbau-Domäne entschieden und
greift ihr nicht vor.*
