# A-43 — Drei Wortlisten des Tors kennen die eigene Wirklichkeit nicht

*Teil 1: das Kennungsmuster erkennt mehrgliedrige Kennungen nicht.
Teil 2: die Aktionsliste erkennt neun von elf Rollentätigkeiten nicht.
Teil 3: der Dirigent-Bereich ist enger als die Vollmacht, die ihn erteilt.*

```yaml
auftrag: "A-43"
werkzeug: "— (Werkzeug der Rollenkette, kein Hausplaner-Werkzeug)"
art: "WORTLISTEN-KORREKTUR — drei Aufzaehlungen in scripts/ werden erweitert: das Kennungsmuster
      (status-erzeugen.sh), das Aktionsvokabular und der Dirigent-Bereich (beide rollen-tor.sh).
      KEIN Produktcode, KEINE Aenderung an docs/STATUS.md durch den Bau, KEIN Blattumbau."
drei_posten: "Posten 1 Kennungsmuster (Dirigent gen 13, Weg A) · Posten 2 Aktionsvokabular
              (Dirigent gen 14, nach Befunden Evaluator 12:04, Plan-Pruefer 12:06, Integrator
              12:1x, externe Pruefung B-005) · Posten 3 Dirigent-Bereich (Dirigent 13:25:41,
              NACH erteilter DoR und NACH dem Bau — siehe Vermerk in Teil 3).
              Ein Blatt, weil alle drei dieselbe Bauform haben: eine Aufzaehlung im Tor,
              die die Wirklichkeit nicht abbildet, und daneben ein Meldungstext, der sie
              ein zweites Mal fuehrt."
spur: A
heimat_app: ticket
dor_beleg: "Runde 1 NICHT ERTEILT (plan-pruefer 12:30:11, Blatt 352900f3, drei Restpunkte).
            Runde 2 ERTEILT_MIT_AUFLAGE (plan-pruefer 12:53:35, Blatt 47dfbfb2, ergebnis_sha
            794cd018, ersetzt 51d26c29) — alle drei Restpunkte behoben bestaetigt, EINE Auflage
            an A-43-11 (Messbefehl zaehlte Fundstellen, Erwartung sagte eine). Auflage mit
            diesem Stand erfuellt; laut Plan-Pruefer ist dafuer kein erneutes Votum noetig.
            Votum: docs/DOR-A-43-plan-pruefer.md"
dor_schnitt_sha: "352900f3 (Runde 1) · 47dfbfb2 (Runde 2) — Auflage in diesem Commit"
status_steht_in: docs/STATUS.md
basis_sha: c11f97ac
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 22.08. 12:12 — Lease SPEZ-planner-kennungsmuster, fencing_token 1."
kennung_geprueft: "A-43 gemessen, nicht geraten: docs/ 0 Treffer, Steuerungsablage 1 Treffer
                   (ein VORSCHLAG in meiner eigenen Frage-Datei vom 21.08., kein vergebener
                   Auftrag), gemeinsamer Checkout 0, git log --all --grep 1 Treffer (derselbe
                   Vorschlag zitiert). KEIN Blatt, KEINE Tafelzeile, KEIN Datensatz. Frei.
                   Zaehlung der offenen Regelbauten 12:26 berichtigt (A-42 ist ABGENOMMEN):
                   drei offen, A-43 ist die vierte — Yamas Maximum erreicht, nicht ueberschritten."
gebaut_in: "ticket-rolle-generator (rolle/generator)"
staut_hinter: "A-37 ABGENOMMEN (erfuellt, Evaluator 12:04) UND Transport in die Integration.
               Vorher nicht bauen: der Bau beruehrt eine Datei, die A-37 gerade transportiert."
regelgrundlage: "Posten 1: Entscheidung des Dirigenten unter Vollmacht 22.08. 12:03:49, Weg A
                 (ereignisse/ERRATA-planner-A-37/dirigent-entscheidung-kennungsmuster.yaml).
                 Posten 2: Rollenquelle planner gen 14 (12:17:32), posten_2_aktionsvokabular;
                 Uebergangsregel README 6f befristet bis zum Transport dieses Baus."
anlass: "Planner-Befund 22.08. 11:44 (planner-befund-kennungsmuster-kennt-Z-nicht.yaml):
         das Muster erkennt Z0-I1, Z1-W1-1, Z2-W0-1 und A-37-22b nicht. Zusammen mit dem
         Plan-Pruefer-Befund 11:39 (leerer Commit 96d59689) die zweite Ursache dafuer, dass
         fuenf Z1-W1-Auftraege seit 15 h auf BEREIT stehen."
```

# Teil 1 — Das Kennungsmuster

## Worum es geht — in einem Satz

`scripts/status-erzeugen.sh` erzeugt die Statuswahrheit aus dem Commit-Log. **Ein Betreff, dessen
Kennung das Muster nicht erkennt, erzeugt keinen Zustandswechsel — und fällt niemandem auf, weil
er richtig aussieht.** Genau das trifft heute jede mehrgliedrige Kennung.

## Der Befund, gemessen

**Das Muster** (`scripts/status-erzeugen.sh:190` und `:521`, wortgleich):

```
(?P<kennung>[A-Z]+-?[0-9]+[A-Za-z]?(?:/[0-9A-Za-z]+)?)
```

**Acht Formen, je ein sonst formal korrekter Betreff, gegen das Muster gefahren** — nicht
nachgebaut, sondern über denselben Ausleseweg, den das Tor benutzt (`exec` des Blocks
`KERN = (` … `WORTLAUT = re.compile`), gemessen am Endstand `c82df498`:

| Form | heute | Form | heute |
|---|---|---|---|
| `A-42` | **ERKANNT** | `Z0-I1` | **NICHT ERKANNT** |
| `A-37` | **ERKANNT** | `Z1-W1-1` | **NICHT ERKANNT** |
| `W-17/1` | **ERKANNT** | `Z2-W0-1` | **NICHT ERKANNT** |
| `P-05` | **ERKANNT** | `A-37-22b` | **NICHT ERKANNT** |

**Grund:** nach `[A-Z]+-?[0-9]+` bleibt für `-I1` bzw. `-W0-1` nichts übrig — das Muster kennt
genau **einen** Bindestrich und danach Ziffern. Die Gruppe `(?:/[0-9A-Za-z]+)?` fängt den
Schrägstrich von `W-17/1`, aber keinen zweiten Bindestrich.

## Was der Bau NICHT einfach tun darf — zwei belegte Fallen

### Falle 1: das Muster steht ZWEIMAL, gelesen wird nur eine Stelle

**Gemessen:** `grep -c 'P<kennung>' scripts/status-erzeugen.sh` → **2** (`:190` in `KERN`,
`:521` in `BEINAHE`). **Das Tor liest nur die erste** — `commit-pruefen.sh` schneidet den Text
zwischen `KERN = (` und `WORTLAUT = re.compile` heraus und führt ihn aus.

`BEINAHE` ist die Funktion, die **Beinahe-Zustandsmeldungen** meldet, also die Warnung vor genau
dem Fehler, um den es hier geht. **Wer nur `KERN` erweitert, hat danach zwei verschiedene Muster
in einer Datei:** das Tor erkennt `Z0-I1`, die Warnfunktion nicht. *Die Warnung fiele für
Z-Aufträge aus — und zwar für dieselben Aufträge, für die sie gebaut wurde.*

### Falle 2: die naheliegendste Bauform schaltet das Tor STILL ab

Die saubere Lösung gegen Falle 1 lautet: das Muster **einmal** als Konstante definieren und an
beiden Stellen verwenden. **Diese Bauform ist hier gefährlich**, weil das Tor nicht die Datei
importiert, sondern einen **Textausschnitt** ausführt. Eine Konstante **vor** `KERN = (` liegt
außerhalb des Ausschnitts.

**Selbst geprobt, in einem Wegwerf-Verzeichnis unter `TMPDIR` (A-37-22d), am Endstand `c82df498`:**

```
Fassung mit KENNUNG = r"..." vor KERN, an beiden Stellen verwendet
  -> "HINWEIS  Muster aus scripts/status-erzeugen.sh nicht auswertbar (...) — UNGEPRUEFT."
  -> Rueckgabewert 0
```

**Rückgabewert 0 heißt: der Commit geht durch.** Das Tor prüft dann gar nichts mehr und sagt es
in einer Zeile, die wie ein Hinweis aussieht. **Ein Bau, der die Doppelung sauber auflöst, kann
damit A-37-26 vollständig wirkungslos machen, ohne dass ein Test rot wird.**

> **Das ist die eigentliche Gefahr dieses Auftrags.** Der Musterfehler ist sichtbar und harmlos —
> ein nicht erkannter Zustandswechsel fällt spätestens beim Zählen auf. **Ein still abgeschaltetes
> Tor fällt gar nicht auf.**

## Die Grundmenge — und warum „100 %" so nicht messbar ist

Der Auftrag nennt als Grundmenge *„alle Betreffs mit `zustand:` im Integrationslog (gemessen:
`zustand: Z` 6, `zustand: A-` 21)"*. **Diese Zahlen sind ungeankert gemessen** — und wie eng man
ankert, entscheidet über das Ergebnis. **Drei Zählweisen, je mit dem Befehl, der sie erzeugt,
alle am Stand `86c407e5` (Planner-Baum, `git log --all`) gefahren:**

```
A  ungeankert     git log --all --format=%s | grep -c 'zustand: '                     -> 71
B  halb geankert  ... | grep -cE '(^|: )zustand:'                                     -> 30
C  geankert       ... | grep -cE '^([a-z-]+(-[0-9]+)?: )?zustand: '                   -> 23
```

**C ist die Grundmenge.** A zählt jede Erwähnung irgendwo im Betreff — überwiegend Berichte *über*
Zustandsmeldungen. B fängt zwar den Zeilenanfang, lässt aber jedes `…: zustand:` mitten im Satz
durch. *Wer Zitate mitzählt, zählt Belege als Fälle.* Das Skript beschreibt diese Unterscheidung
zwei Zeilen unter seinem eigenen `BEINAHE`-Muster selbst.

> **Berichtigung, Plan-Prüfer-Restpunkt 2 (12:30):** eine frühere Fassung dieses Blattes nannte
> *„ungeankert 29"*. **Das war die halb geankerte Zahl B, nicht A** — mit dem Wort „ungeankert"
> war sie nicht reproduzierbar; sein Lauf gab folgerichtig 71. *Ein Beleg, dessen Wort nicht zu
> seinem Befehl passt, ist kein Beleg.* Alle drei Zahlen stehen jetzt mit ihrem Befehl da.

**Die geankerte Grundmenge, Stand `86c407e5`** — Kennung **isoliert extrahiert** (erstes Feld nach
`zustand: `), nicht über einen Grep auf die ganze Zeile:

| Kennungsform | Anzahl | heute erkannt |
|---|---|---|
| `A-NN` (A-37, A-38, A-39, A-41, A-42) | 14 | ja |
| `W-17/1` | 6 | ja |
| `Z…` (mehrgliedrig) | 3 | **nein** |
| **Summe** | **23** | **20 ja / 3 nein** |

> **Warum die Kennung isoliert werden muss — zum zweiten Mal dieselbe Falle:** `grep -c 'zustand: A-'`
> auf die ganze Zeile gibt **15**, und dann geht die Summenprobe nicht auf (15+6+3 = 24 ≠ 23). Eine
> Zeile trägt `zustand: A-` zusätzlich im **Belegteil**. Erst die isolierte Extraktion gibt 14 und
> schließt die Summe. **Dieselbe Bauart hatte zuvor W-17/1 mit 5 statt 6 getroffen.**

### Und hier liegt der Widerspruch, den das Kriterium auflösen muss

**Die drei Z-Betreffs tragen KEINE einzelne Kennung.** Wortlaut, gemessen:

```
Z1-W1-1..5 · CODE_FERTIG · evaluator · bau …          <- Bereichsangabe, keine Kennung
Z2-W0-1 · Z2-W0-3 · Z2-W0-7 · Z2-W0-8 · Z… · …        <- Mehrfachkennung
Z2-W0-5 · Z2-W0-10 · Z2-W0-11 · Z2-W0-12 · …          <- Mehrfachkennung
```

**A-37-26 weist Mehrfachkennungen ausdrücklich ab — das ist sein Zweck** (Anlassfall `e9e6ee5b`:
`A-38 · A-42` in einem Betreff, beide Zustände fielen aus). **Ein Kriterium „nach dem Bau erkennt
das Muster 100 % der Grundmenge" wäre damit unerfüllbar** und stünde gegen A-37-26.

**Aufgelöst:** gemessen wird die **Kennungsform**, nicht der Altbetreff. Die drei Altbetreffs
bleiben zu Recht abgewiesen; sie werden vom Integrator als **einzelne, nicht-leere** Zustands-
commits nachgeholt (Folgeposten unten). *Der Bau repariert das Muster, nicht die Vergangenheit.*

---

## Abnahmekriterien Teil 1

- **A-43-1** · **ACHT FORMEN, JEDE EINZELN AUSGELÖST.**

  **Verlangt:** Das erweiterte Muster erkennt `Z0-I1`, `Z1-W1-1`, `Z2-W0-1` und `A-37-22b` **und**
  erkennt `A-42`, `A-37`, `W-17/1`, `P-05` unverändert weiter. Je Form ein eigener Lauf mit
  Rohausgabe, vorher und nachher.

  **Messbefehl:** je Form ein Betreff `integrator: zustand: <FORM> · ABGENOMMEN · evaluator · bau abc12345`,
  geprüft über **denselben Ausleseweg wie das Tor** (Ausschnitt `KERN = (` … `WORTLAUT = re.compile`
  auslesen und ausführen), **nicht** über ein nachgebautes Muster.

  **Heutiges (rotes) Ergebnis, gemessen am Endstand `c82df498`:**
  `A-42 ERKANNT · A-37 ERKANNT · W-17/1 ERKANNT · P-05 ERKANNT · Z0-I1 NICHT ERKANNT ·
  Z1-W1-1 NICHT ERKANNT · Z2-W0-1 NICHT ERKANNT · A-37-22b NICHT ERKANNT` — **4 zu 4.**

  **Absage-Regel:** Ein Nachweis, der das Muster als Zeichenkette in ein eigenes Testskript kopiert,
  erfüllt A-43-1 **nicht**. Er misst dann die Kopie und nicht die Quelle — und übersieht damit
  genau Falle 2.

- **A-43-2** · **DIE ZWEITE MUSTERSTELLE WÄCHST MIT.**

  **Verlangt:** Nach dem Bau erkennen **`KERN` und `BEINAHE` dieselben acht Formen**. Zwei
  wortgleiche Kopien sind zulässig, eine gemeinsame Definition ist zulässig — **eine Divergenz
  ist es nicht.**

  **Messbefehl:** dieselben acht Formen zusätzlich gegen `BEINAHE` (`scripts/status-erzeugen.sh:521`)
  fahren; jede Form muss in beiden Mustern dasselbe Ergebnis liefern.

  **Heutiges (rotes) Ergebnis:** `grep -c 'P<kennung>' scripts/status-erzeugen.sh` → **2**, beide
  Stellen wortgleich, beide erkennen `Z0-I1` **nicht**. *Heute sind sie einig, weil beide gleich
  blind sind — nach einer halben Korrektur wären sie es nicht mehr.*

  **Absage-Regel:** „`BEINAHE` ist nur eine Warnung, die darf ungenau sein" erfüllt A-43-2 nicht.
  Die Warnung ist der einzige Melder für den Fall, dass ein Zustandswechsel ausfällt.

- **A-43-3** · **DAS TOR BLEIBT WIRKSAM — NACHGEWIESEN ÜBER DEN RÜCKGABEWERT.**

  **Verlangt:** Nach dem Bau meldet der Ausleseblock in `commit-pruefen.sh` **kein**
  `UNGEPRUEFT`. Nachgewiesen wird das **nicht** am Text, sondern an einer ausgelösten Probe:
  ein Betreff mit **zwei** Kennungen muss weiterhin mit **Rückgabewert 1** abgewiesen werden.

  **Messbefehl:**
  ```
  Betreff: 'generator: zustand: A-38 · A-42 · CODE_FERTIG · evaluator · bau 0f731c22'
    -> erwartet: VERSTOSS, Rueckgabe 1
  Betreff: 'integrator: zustand: Z0-I1 · ABGENOMMEN · evaluator · bau abc12345'
    -> erwartet: durchgelassen, Rueckgabe 0
  Ausgabe je Lauf vollstaendig, dazu 'echo $?' direkt gelesen.
  ```

  **Heutiges (rotes) Ergebnis:** Die Probe-Bauform „Konstante vor `KERN`" liefert
  `HINWEIS … nicht auswertbar — UNGEPRUEFT` bei **Rückgabewert 0** — selbst geprobt im
  Wegwerf-Verzeichnis. **Das Tor lässt dann alles durch.**

  **Absage-Regel:** Ein grüner Lauf ohne `echo $?` erfüllt A-43-3 nicht. `UNGEPRUEFT` und
  „bestanden" sind am Rückgabewert **nicht** unterscheidbar — beide sind 0.

- **A-43-4** · **DIE GRUNDMENGE WIRD GEANKERT GEMESSEN.**

  **Verlangt:** Der Nachweis nennt die Grundmenge **geankert**, **mit dem Stand, an dem er sie
  erhoben hat**, und benennt die Differenz zu den beiden weiteren Zählweisen. Alle Kennungen der
  geankerten Grundmenge, die **genau eine** Kennung tragen, werden nach dem Bau zu **100 %** erkannt.

  **Messbefehl** — alle drei Zählungen, je mit ihrem Befehl, und der Stand wird genannt:
  ```
  STAND=$(git rev-parse --short HEAD)          # gehoert in den Nachweis, die Menge waechst
  git log --all --format=%s | grep -c 'zustand: '                        -> A ungeankert
  git log --all --format=%s | grep -cE '(^|: )zustand:'                  -> B halb geankert
  git log --all --format=%s | grep -cE '^([a-z-]+(-[0-9]+)?: )?zustand: ' -> C Grundmenge
  # Aufteilung: Kennung ISOLIERT extrahieren (erstes Feld nach 'zustand: '), nicht die Zeile greppen
  ... | sed -E 's/^([a-z-]+(-[0-9]+)?: )?zustand: //' | awk '{print $1}' | sort | uniq -c
  # je Betreff gegen WORTLAUT; gezaehlt mit wc -l, nicht mit den Augen; Summenprobe gegen C
  ```

  **Heutiges (rotes) Ergebnis, Stand `86c407e5`:** A **71** · B **30** · **C 23**
  (A- 14 · W-17/1 6 · Z 3, Summenprobe 14+6+3 = 23 ✓). Von den 23 werden heute **20** erkannt,
  **3 nicht** — und genau diese 3 tragen Mehrfach- bzw. Bereichskennungen und bleiben auch nach
  dem Bau abgewiesen (siehe A-43-5).

  **Absage-Regel, zweiteilig:**
  **(a)** Eine Zählung **ohne** Anker erfüllt A-43-4 nicht — sie zählt Zitate als Fälle
  (**71 statt 23**, das Dreifache).
  **(b)** Eine Zahl **ohne Standangabe** erfüllt A-43-4 ebenfalls nicht. *Selbst belegt: die
  Grundmenge war um 12:2x noch **22** und ist um 12:20:02 durch `aec713a6`
  (`integrator: zustand: A-37 · ABGENOMMEN · release-pruefer`) auf **23** gewachsen — während
  dieses Blatt geschrieben wurde.* **Diese Menge wächst mit jedem Zustandscommit;** eine Zahl,
  die nicht sagt, wann sie erhoben wurde, ist beim Nachmessen falsch, ohne dass jemand einen
  Fehler gemacht hat.

- **A-43-5** · **MEHRFACHKENNUNG BLEIBT ABGEWIESEN — AUCH BEI Z.**

  **Verlangt:** Das erweiterte Muster erkennt `Z2-W0-1` als Form, weist aber
  `Z2-W0-1 · Z2-W0-3 · Z2-W0-7` weiterhin ab. **Der Bau erweitert die Kennungs*form*, nicht die
  Kennungs*anzahl*.** Negativprobe ausgelöst, Rückgabewert genannt.

  **Messbefehl:** die drei realen Z-Altbetreffs einzeln durch das Tor fahren; erwartet: je
  **VERSTOSS, Rückgabe 1**. Zusätzlich `Z1-W1-1..5` — eine **Bereichsangabe**, die auch nach dem
  Bau keine Kennung ist.

  **Heutiges (rotes) Ergebnis:** alle drei werden heute abgewiesen — aber aus dem **falschen
  Grund** (Form unbekannt statt Anzahl zu hoch). *Nach dem Bau muss die Abweisung bleiben und der
  Grund muss stimmen.*

  **Absage-Regel:** Ein Muster, das `Z2-W0-1 · Z2-W0-3` als eine Kennung liest, erfüllt A-43-5
  nicht — es hätte A-37-26 aufgehoben statt ergänzt.

- **A-43-6** · **KEINE KENNUNG BLEIBT KEINE KENNUNG — UND DIE KENNUNG WIRD GANZ GEGRIFFEN.**

  **Verlangt:** Zweierlei. **(a)** `zustand: FOO` wird weiterhin abgewiesen — das erweiterte Muster
  darf nicht so offen werden, dass jedes Großbuchstabenwort als Kennung durchgeht. **(b)** Bei einer
  erkannten mehrgliedrigen Kennung ist der **Inhalt der Gruppe `kennung` die vollständige Kennung**,
  nicht ihr Präfix. Nachgewiesen wird (b) am **Gruppeninhalt**, nicht an „erkannt/nicht erkannt".

  **Messbefehl:**
  ```
  (a) 'integrator: zustand: FOO · …'         -> erwartet NICHT ERKANNT
      'integrator: zustand: ABGENOMMEN · …'  -> erwartet NICHT ERKANNT
  (b) 'integrator: zustand: Z0-I1 · …'       -> match.group('kennung') MUSS 'Z0-I1' sein
      'integrator: zustand: Z2-W0-1 · …'     -> match.group('kennung') MUSS 'Z2-W0-1' sein
  ```

  **Heutiges Ergebnis, selbst gemessen — und der zweite Teil ist der gefährliche:**
  ```
  FOO         NICHT ERKANNT     (a) heute schon richtig
  ABGENOMMEN  NICHT ERKANNT     (a) heute schon richtig
  Z0          ERKANNT           <- das Praefix ALLEIN passt auf [A-Z]+-?[0-9]+
  ```

  **`Z0` ist keine Kennung, sondern das Präfix von `Z0-I1`, `Z0-I2`, `Z0-I3` — und das Muster
  erkennt es.** Heute bleibt das folgenlos, weil `WORTLAUT` mit `^…$` verankert ist: bei
  `zustand: Z0-I1 · …` folgt auf `Z0` kein Trennzeichen, sondern `-I1`, und der Gesamtmatch
  scheitert. **Genau dieser Schutz fällt weg, sobald die Gruppe mehr Bindestriche zulässt.**
  Ein zu lasch erweitertes Muster greift dann `Z0`, der Rest fällt in den Beleg-Teil — und
  `docs/STATUS.md` bekommt einen Zustandswechsel für einen Auftrag, **den es nicht gibt.**

  > **Das ist der schlimmere Ausgang als heute.** Heute fällt ein Zustandswechsel *aus* — das ist
  > sichtbar, sobald jemand zählt. Nach einer laschen Erweiterung stünde ein *falscher* Zustand in
  > der Tafel, und der sieht aus wie ein richtiger.

  **Absage-Regel:** Ein Nachweis, der nur `match`/`kein match` prüft, erfüllt A-43-6 nicht. Der
  Gruppeninhalt muss ausgegeben und mit der erwarteten Kennung **zeichengenau** verglichen werden.

- **A-43-7** · **KEIN PRODUKTCODE, KEINE STATUS-ÄNDERUNG DURCH DEN BAU.**

  **Verlangt:** Der Diff berührt ausschließlich `scripts/status-erzeugen.sh` (und, falls für
  A-43-2 nötig, `scripts/commit-pruefen.sh`). **`docs/STATUS.md` wird durch den Bau selbst nicht
  geändert** — der erste Lauf, der die Tafel neu erzeugt, ist ein eigener Schritt des Integrators.

  **Messbefehl:**
  ```
  git diff --name-only <basis>..<bau>            -> nur scripts/
  git diff --name-only <basis>..<bau> -- resources/ app/ docs/STATUS.md   -> leer
  ```

  **Heutiges (rotes) Ergebnis:** kein Bau vorhanden, Diff leer — das Kriterium ist ein
  **Schutz**beleg und wird am Bau-Diff gemessen, nicht am Bestand.

---

# Teil 2 — Das Aktionsvokabular des Tors

**Dieselbe Bauform, eine Datei weiter.** `scripts/rollen-tor.sh:361-362` führt zwei Wortlisten:

```
Arbeit:  bauen | nachbessern
Pause:   pausieren | angehalten | angehalten_eingefroren | parken | warten
sonst:   "unbekannte aktion" -> Rueckgabe 7
```

**Keine der neun Tätigkeiten, die die Rollen tatsächlich ausüben, steht darin.** Vier Rollen
meldeten das unabhängig: Evaluator (12:04, Befund 1 im Abnahme-Votum), Plan-Prüfer (12:06, um
`spezifizieren` ergänzt), Integrator (12:1x), externe Prüfung (B-005, S1 ab dem Transport).

## Rot gemessen — die echte `case`-Anweisung, nicht nachgebaut

Zeilen 360–378 aus `c82df498` **unverändert extrahiert** und in einem Wegwerf-Verzeichnis unter
`TMPDIR` (A-37-22d) als Funktion gefahren; je Wort Rückgabewert und Meldung:

| Wort | RC | Meldung |
|---|---|---|
| `spezifizieren` (Planner) | **7** | unbekannte aktion |
| `pruefen` · `erteilen` (Plan-Prüfer) | **7** · **7** | unbekannte aktion |
| `abnehmen` · `nachpruefen` (Evaluator) | **7** · **7** | unbekannte aktion |
| `transportieren` · `zustand_nachziehen` (Integrator) | **7** · **7** | unbekannte aktion |
| `steuern` (Dirigent) | **7** | unbekannte aktion |
| `release_pruefen` (Release-Prüfer) | **7** | unbekannte aktion |
| `bauen` · `nachbessern` | 0 · 0 | durchgelassen |
| `warten` · `parken` | 7 · 7 | *keine Arbeitsanweisung* (Pause, korrekt) |
| `warten_dann_nachpruefen` · `rueckweg_…` | **7** · **7** | unbekannte aktion |

**Neun von elf Rollentätigkeiten sind dem Tor unbekannt.** Zwei sind es nicht — und beide gehören
derselben Rolle, dem Generator.

> **Warum das heute nicht knallt:** die Übergangsregel README 6f (Dirigent 12:17). Jede Rollenquelle
> trägt seit 12:17 ein **Tor-Wort** in `aktion:` (`bauen`, auch wenn die Rolle nichts baut) und die
> echte Tätigkeit in `taetigkeit:`. **Gemessen, alle sieben Rollen:** `aktion` ist 5× `bauen`,
> 2× `parken`; `taetigkeit` trägt die Wahrheit (`spezifizieren`, `steuern`, `abnehmen/pruefen`,
> `transportieren + zustand_nachziehen`, `pruefen/erteilen`, 2× `warten`).
> **Das ist eine Krücke mit Ablaufdatum, keine Lösung** — und sie erzeugt genau die zweite Wahrheit,
> die dieses Haus sonst bekämpft: das Feld, das das Tor liest, sagt etwas anderes als das Feld, das
> die Rolle beschreibt.

## Die Falle von Teil 2 — und sie ist die Zwillingsschwester der Falle aus Teil 1

**Pause und Unbekannt geben BEIDE `7` zurück.** Gemessen: `warten` → 7, `spezifizieren` → 7.
Unterscheidbar sind sie **nur an der Meldung** (*„keine Arbeitsanweisung"* gegen *„unbekannte
aktion"*).

> **Teil 1 hatte dasselbe Problem mit der 0:** `UNGEPRUEFT` und „bestanden" sind beide 0.
> **Zweimal in einem Auftrag ist der Rückgabewert allein nicht aussagekräftig.** Wer hier nur
> `echo $?` misst, kann nicht sehen, ob ein Wort als Pause **anerkannt** oder als Müll
> **abgelehnt** wurde — und genau das ist der Unterschied, den Posten 2 herstellen soll.

---

## Abnahmekriterien Teil 2

- **A-43-8** · **JEDE ROLLE HAT EIN ANERKANNTES ARBEITSVERB.**

  **Verlangt:** Die Arbeitsliste kennt für **jede** der sieben Rollen mindestens ein
  rollengerechtes Verb: `spezifizieren` (Planner), `pruefen`/`erteilen` (Plan-Prüfer),
  `abnehmen`/`nachpruefen` (Evaluator), `transportieren`/`zustand_nachziehen` (Integrator),
  `steuern` (Dirigent), `release_pruefen` (Release-Prüfer), `bauen`/`nachbessern` (Generator).

  **Messbefehl:** je Wort ein Lauf gegen die **echte** `case`-Anweisung (Zeilen aus
  `scripts/rollen-tor.sh` extrahiert, nicht nachgebaut), Rückgabewert **und** Meldung notiert;
  erwartet **RC 0, keine Meldung**.

  **Heutiges (rotes) Ergebnis:** **9 von 9** neuen Verben geben **7 „unbekannte aktion"**
  (Rohausgabe in der Tabelle oben). `bauen` und `nachbessern` geben 0.

  **Absage-Regel:** Eine Liste, die nur `spezifizieren` ergänzt, erfüllt A-43-8 nicht. Der Befund
  ist nicht „ein Wort fehlt", sondern „die Liste kennt die Rollen nicht" — belegt an neun Wörtern
  aus sechs Rollen.

- **A-43-9** · **PAUSE WIRD ALS PAUSE ERKANNT — NACHGEWIESEN AN DER MELDUNG.**

  **Verlangt:** `warten` **und die `warten_dann_*`-Formen** werden als **Pause** anerkannt: sie
  sperren weiterhin (Rückgabe 7), aber mit der **Pause-Meldung**, nicht mit *„unbekannte aktion"*.
  Ebenso bleiben `pausieren`, `angehalten`, `angehalten_eingefroren`, `parken` Pause.

  **Messbefehl:** je Wort Rückgabewert **und Meldungstext**; erwartet
  `RC 7` **und** *„das ist keine Arbeitsanweisung"*.

  **Heutiges (rotes) Ergebnis:** `warten` → 7 *„keine Arbeitsanweisung"* (**richtig**);
  `warten_dann_nachpruefen` → 7 *„unbekannte aktion"* (**falsch benannt**).
  *Am Rückgabewert sind beide gleich — der Unterschied steht nur im Text.*

  **Absage-Regel:** Ein Nachweis über `echo $?` allein erfüllt A-43-9 **nicht**. Pause und
  Unbekannt sind beide 7; wer nur die Zahl liest, kann die Erfüllung nicht von der Rot-Lage
  unterscheiden. **Das ist dieselbe Falle wie A-43-3, nur mit 7 statt 0.**

- **A-43-10** · **UNBEKANNT BLEIBT UNBEKANNT.**

  **Verlangt:** Ein Wort, das weder Arbeit noch Pause ist, wird weiterhin mit **7 und
  *„unbekannte aktion"*** abgewiesen. Die Erweiterung darf die Liste nicht durch eine
  Präfix-Regel oder ein `*`-Muster ersetzen, das alles durchlässt.

  **Messbefehl:** `aktion: quatsch` und `aktion: bauenX` → erwartet je **7 „unbekannte aktion"**.
  Zusätzlich der Leerfall `aktion:` (leer) → heute **RC 0 mit HINWEIS**; dieses Verhalten bleibt
  unverändert und wird ausdrücklich mitgemessen, damit es nicht nebenbei kippt.

  **Heutiges (grünes) Ergebnis:** `rueckweg_x` → 7 *„unbekannte aktion"*, selbst gemessen.
  **Regressionsschutz** — ausdrücklich als heute-grün gekennzeichnet.

  **Absage-Regel:** Eine Erweiterung über `warten*` als Glob, die auch `wartenXYZ` durchlässt,
  erfüllt A-43-10 nicht — sie tauscht eine zu enge Liste gegen eine zu weite.

- **A-43-11** · **EINE LISTE, KEINE ZWEITE DEFINITION.**

  **Verlangt:** Nach dem Bau gibt es weiterhin **genau eine** Stelle, an der die Aktionswörter
  aufgezählt sind. `commit-pruefen.sh` und `.githooks/pre-commit` definieren **keine eigene**.

  **Messbefehl** — gezählt werden **Dateien und `case`-Anweisungen**, nicht Fundstellen:
  ```
  grep -rlE 'bauen\|nachbessern|pausieren\|angehalten' scripts/ .githooks/ | wc -l   -> genau 1 Datei
  grep -cF 'case "$AKTION" in' <diese Datei>                                         -> genau 1 Anweisung
  grep -rnE 'bauen\|nachbessern|pausieren\|angehalten' scripts/ .githooks/           -> Zeilen benennen
  ```

  > **Zweite Berichtigung an dieser Stelle (Plan-Prüfer 13:08:35), und sie gehört ins Blatt, weil
  > sie sein Gegenstand ist:** der mittlere Befehl stand hier als `grep -c 'case "$AKTION" in'` —
  > **ohne `-F` liefert er 0**, nicht 1. **`$` ist im regulären Ausdruck der Zeilenend-Anker**, das
  > Muster liest sich als *„`case "` gefolgt vom Zeilenende, danach `AKTION" in`"* und kann nie
  > treffen. Selbst nachgefahren, alle Formen am Stand `a7035fb7`:
  > `ohne -F → 0` · `grep -cF → 1` · `grep -c 'case "\$AKTION" in' → 1` · `grep -cE 'case "[$]AKTION" in' → 1`.
  >
  > **Wie es entstanden ist — ich kann die Auskunft geben, die er ausdrücklich nicht messen konnte:**
  > gemessen habe ich in der Shell mit `'case "\$AKTION" in'`, **mit** Backslash, und dort **1**
  > erhalten. Ins Blatt geschrieben habe ich ihn **ohne**. *Die Messung war richtig, die Niederschrift
  > hat ein Zeichen verloren* — und zwar genau das, an dem alles hängt.
  >
  > **Er hat die Klasse richtig benannt:** drei Zeilen höher steht `bauen\|nachbessern` mit korrekt
  > escaptem Pipe. **Dieselbe Sorgfalt, die das eine Metazeichen erkannt hat, hat das andere
  > übersehen.** Deshalb steht hier jetzt `-F`: es schaltet die Metazeichen ganz ab und ist gegen
  > jede Shell und jedes Escaping robust. *Ein Befehl, den man richtig zitieren muss, damit er
  > funktioniert, ist der falsche Befehl.*
  >
  > **Gegenprobe über das ganze Blatt:** `grep -n '\$'` → **8** Fundstellen; sieben davon sind
  > Shell (`echo $?`, `STAND=$( )`), ein awk-Feld (`{print $1}`) oder die Erklärung des Ankers
  > selbst. **Diese eine war die einzige defekte.**

  **Heutiges (grünes) Ergebnis:** **1 Datei** (`scripts/rollen-tor.sh`), **1 `case`-Anweisung**,
  darin **2 Fundstellen** — `:361` (Arbeit) und `:362` (Pause), die zwei Zweige derselben Anweisung.
  `commit-pruefen.sh` **0**, `.githooks/pre-commit` **0**. **Regressionsschutz.**

  > **Auflage aus DoR-Runde 2 (Plan-Prüfer 12:53:35), hiermit erfüllt:** der frühere Messbefehl
  > zählte **Fundstellen** und erwartete *„genau 1"* — der Rohbefehl liefert aber **2**, und das
  > grüne Ergebnis nannte selbst beide Zeilen. **Ein Kriterium, dessen Messbefehl die eigene
  > Erwartung verfehlt, ist bei der Abnahme nicht entscheidbar.** Gemeint war immer *eine
  > Definition*, nicht *eine Zeile*; die Zählung misst das jetzt. **Kein neues Kriterium, ein Satz
  > am Messweg** — genau der Umfang, den die Auflage vorgibt.

  **Absage-Regel:** Dieselbe Absage wie bei A-43-2. Zwei Wortlisten für dieselbe Frage sind eine
  zweite Wahrheit — und die zweite altert unbemerkt, weil niemand sie liest.
  **Dazu ausdrücklich, Hinweis des Plan-Prüfers und von mir nachgemessen:**
  `scripts/rollen-tor.sh:374-375` wiederholt **beide Wortlisten als Meldungstext**, mit Leerzeichen
  statt Pipe — der Rohbefehl trifft dort **0**. Heute stimmen die Listen überein, deshalb ist es
  **kein Mangel**: Meldungstexte steuern nichts. **Aber der Bau muss sie mitziehen.** Erweitert er
  nur die `case`-Anweisung, sagt die Fehlermeldung weiterhin *„Bekannt als Arbeit: bauen
  nachbessern"*, während das Tor mehr kennt — dann ist die Liste zwar richtig und die **Auskunft
  darüber falsch**. *Das ist keine zweite Definition, aber eine zweite Aussage.*

- **A-43-12** · **DIE GRUNDMENGE LÄUFT DURCH — GEMESSEN, NICHT AUFGEZÄHLT.**

  **Verlangt:** Alle in `rollen/*.yaml` **tatsächlich gesetzten** Werte laufen nach dem Bau
  korrekt: jedes `aktion`-Wort und jedes `taetigkeit`-Wort wird entweder als Arbeit (RC 0) oder
  als Pause (RC 7 mit Pause-Meldung) erkannt — **keines fällt in „unbekannt".**

  **Messbefehl:**
  ```
  für f in rollen/*.yaml: aktion- und taetigkeit-Wert auslesen und je einzeln durchs Tor fahren;
  gezaehlt mit wc -l, Ergebnis je Wort als Zeile
  ```

  **Heutiges (rotes) Ergebnis, gemessen 12:3x:**
  `aktion` = 5× `bauen`, 2× `parken` → laufen (Übergangsregel 6f wirkt).
  `taetigkeit` = `spezifizieren`, `steuern`, `abnehmen/pruefen`, `transportieren + zustand_nachziehen`,
  `pruefen/erteilen`, 2× `warten` → **die fünf Arbeitsverben fallen alle in „unbekannt".**

  **Absage-Regel:** Eine Prüfung gegen eine im Blatt aufgeschriebene Wortliste erfüllt A-43-12
  nicht. Gemessen wird gegen die **Rollenquellen zum Zeitpunkt des Baus** — die Werte wandern,
  und eine Liste im Blatt altert. *Belegt: zwischen 12:03 und 12:17 haben alle sieben Rollen ihre
  `aktion` gewechselt.*

---

# Teil 3 — Der Dirigent-Bereich (Nachtrag nach erteilter DoR)

**Auftrag des Dirigenten unter Vollmacht, 22.08. 13:25:41** (`dirigent-errata-posten3-dirigent-bereich.yaml`).
**Dritte Wortliste desselben Bautyps** — nach Kennungsmuster und Aktionsvokabular jetzt die
Positivliste der Schreibpfade.

**Anlass, ausgelöst und nicht behauptet:** Am 22.08. **13:08** hat das transportierte Tor den
Dirigenten abgewiesen:

```
ROLLEN-TOR  VERSTOSS  Rolle 'dirigent' schreibt ausserhalb ihres Bereichs.
            Erlaubt sind nur: docs/konzept/  docs/regelwerk/  docs/auftraege/
            abgewiesen: docs/backlog/steuerungs-backlog-2026-08-22.md
```

**Kein `--no-verify`** — er hat die Abweisung hingenommen und gemeldet. *Das Tor hat richtig
funktioniert; die Liste darin ist zu eng.*

**Der Widerspruch, beide Quellen im Wortlaut:** `docs/regelwerk/VOLLMACHT-DIRIGENT.md` (Yama,
21.08.) gibt ihm **„Steuerungs- und Konzeptdokumente"**. Die drei Steuerungslisten liegen in
`docs/backlog/`, die Lageberichte in `docs/fortschritt/`. **Das Tor ist enger als die Vollmacht** —
und damit sperrt eine Regel eine Arbeit, die eine höhere Regel ausdrücklich erlaubt.

- **A-43-13** · **DER DIRIGENT-BEREICH DECKT SEINE VOLLMACHT.**

  **Verlangt:** Die Positivliste in `scripts/rollen-tor.sh` (A-37-23) kennt zusätzlich
  **`docs/backlog/`** und **`docs/fortschritt/`**. **Weiterhin abgewiesen bleiben:** `scripts/`,
  `.githooks/`, `docs/STATUS.md`, `docs/BEFUNDNOTIZEN.md` und Produktcode. *Der Bereich wächst um
  zwei Verzeichnisse, er wird nicht zum Schlüssel.*

  **Messbefehl:**
  ```
  Positivprobe : Commit als dirigent mit einem Pfad unter docs/backlog/     -> erwartet 0
                 desgleichen unter docs/fortschritt/                        -> erwartet 0
  Negativprobe : Commit als dirigent mit docs/STATUS.md                     -> erwartet 1
                 desgleichen mit scripts/                                   -> erwartet 1
  je Lauf die Meldung mitnehmen, nicht nur echo $?
  ```

  **Heutiges (rotes) Ergebnis:** `scripts/rollen-tor.sh:754` führt
  `docs/konzept/*|docs/regelwerk/*|docs/auftraege/*` — **`docs/backlog/` und `docs/fortschritt/`
  fehlen beide.** Ausgelöst belegt durch die Abweisung von 13:08 (Dirigent-Stand `572c7fc2`,
  Rückgabe 1).

  > **Am Code gemessen, die Probe nicht selbst gefahren:** ein Lauf mit `TICKET_ROLLE=dirigent`
  > in einem realen Checkout ist nach A-37-22d untersagt. Der Rot-Beleg steht deshalb auf zwei
  > Beinen: der **Positivliste im Code** (von mir gelesen) und der **ausgelösten Abweisung** des
  > Dirigenten (von ihm gefahren). *Ich melde, welche Hälfte von wem stammt.*

  **Absage-Regel — und sie ist dieselbe wie bei A-43-11:** `scripts/rollen-tor.sh:760` **wiederholt
  die Liste als Meldungstext** (*„Erlaubt sind nur: docs/konzept/ docs/regelwerk/ docs/auftraege/"*).
  Wer nur die `case`-Zeile erweitert, lässt die Auskunft falsch stehen: der Dirigent dürfte dann
  nach `docs/backlog/` schreiben, während die Fehlermeldung ihm weiterhin das Gegenteil sagt.
  **Die Meldung muss die Liste aus derselben Quelle lesen** — eine Quelle, eine Ableitung, wie der
  Generator es bei den Aktionswörtern bereits gebaut hat.

## Vermerk zum Zeitpunkt — dieses Kriterium kommt zu spät, und das gehört gesagt

**Gemessene Reihenfolge:**

```
12:53:35  DoR ERTEILT_MIT_AUFLAGE          -> Kriterienstand eingefroren (Yamas Errata-Regel)
13:14:09  Auflage erfuellt (1e5ac476)
13:24:11  Generator CODE_FERTIG (8a08d625) -> A-43-1 bis A-43-12 gebaut und belegt
13:25:41  Auftrag des Dirigenten: A-43-13  -> 90 Sekunden NACH dem Baubericht
```

**Was das bedeutet, ohne Beschönigung:**

- **Der neue Posten ist kein Errata-Posten, sondern ein Kriterium.** Er verlangt Rot/Grün, eine
  Positiv- und eine Negativprobe und eine Codeänderung. Yamas Errata-Regel deckt
  *Erläuterungs-, Beleg- und Formulierungsberichtigungen ohne Kriterienwirkung* — dies hat
  Kriterienwirkung. **Der Kriterienstand nach erteilter DoR ist damit geändert.**
- **Der Bau ist dadurch unvollständig geworden**, ohne dass der Generator etwas falsch gemacht
  hätte: er hat gegen zwölf Kriterien gebaut und zwölf belegt. Das dreizehnte entstand nach
  seinem Bericht.
- **Zwei Folgen, die ich nicht entscheide, aber benenne:** die DoR braucht eine dritte Runde für
  A-43-13, und der Bau eine Nachbesserung. Beides ist klein — ein Verzeichnispaar in einer
  `case`-Zeile plus der Meldungstext.

> **Warum ich es trotzdem eintrage:** der Dirigent hat es unter Vollmacht beauftragt, der Anlass
> ist real und ausgelöst belegt, und die Sache gehört sachlich in dieses Blatt — es ist dieselbe
> Bauform wie Teil 1 und Teil 2. **Ich trage es ein und melde die Folge, statt es still
> einzureihen oder es zu verweigern.**

**Eine Anweisung des Dirigenten ist überholt und ich führe sie nicht aus:** er bittet, A-43-13
*„mit der -F-Korrektur A-43-11 in EINEM Nachtrag (ersetzt_sha a7035fb7)"* zu bündeln. **Die
-F-Korrektur ist seit `1e5ac476` (13:14) gefahren**, vom Plan-Prüfer quittiert (13:14:38) und vom
Generator bereits benutzt. Eine Bündelung würde einen erledigten Stand erneut aufmachen.
*Sein Bündelungswunsch stammt aus einer Minute, in der er den Nachtrag noch nicht kennen konnte.*

---

## Nicht-Ziele

- **Die Kennungsform der 89 Blätter wird NICHT vereinheitlicht** (Weg B). Der Dirigent hat das
  ausdrücklich verworfen: 89 Blätter plus `docs/STATUS.md` für einen Musterfehler ist
  unverhältnismäßig.
- **Z-Aufträge werden NICHT ohne maschinellen Zustandswechsel geführt** (Weg C). Das wäre die
  zweite Wahrheit, die A-41 gerade beseitigt hat.
- **A-37-26 wird NICHT geändert.** Das Kriterium ist richtig; lückenhaft war das Muster darunter.
  A-37 ist eingefroren und abgenommen.
- **Die fünf hängenden Z1-W1-Zustandscommits werden von diesem Auftrag NICHT nachgeholt** —
  siehe Folgeposten.

---

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

*Commit-SHA und Testbeleg werden nach der Umsetzung gefüllt — heute leer, und das ist die
vorgeschriebene Form: eine rückwirkend gefüllte Matrix beweist nichts.*

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A-43-1 acht Formen | AP-1 Muster in `KERN` erweitern | n.U. | n.U. |
| A-43-2 zweite Stelle wächst mit | AP-1 (`BEINAHE` gleichziehen) | n.U. | n.U. |
| A-43-3 Tor bleibt wirksam | AP-2 Ausleseweg absichern | n.U. | n.U. |
| A-43-4 Grundmenge geankert | AP-3 Nachweislauf über 22 Betreffs | n.U. | n.U. |
| A-43-5 Mehrfachkennung abgewiesen | AP-2 (Negativproben) | n.U. | n.U. |
| A-43-6 keine Kennung bleibt keine | AP-2 (Negativproben) | n.U. | n.U. |
| A-43-7 kein Produktcode | AP-4 Diff-Beleg | n.U. | n.U. |
| A-43-8 Arbeitsverb je Rolle | AP-5 Arbeitsliste erweitern | n.U. | n.U. |
| A-43-9 Pause an der Meldung | AP-5 (Pause-Liste) | n.U. | n.U. |
| A-43-10 unbekannt bleibt unbekannt | AP-6 Negativproben Aktion | n.U. | n.U. |
| A-43-11 eine Liste | AP-6 (Abgrenzungsbeleg) | n.U. | n.U. |
| A-43-12 Grundmenge Rollenquellen | AP-7 Nachweis gegen rollen/*.yaml | n.U. | n.U. |
| A-43-13 Dirigent-Bereich | AP-8 Positivliste + Meldungstext | n.U. | n.U. |

**Arbeitspakete:** AP-1 Muster · AP-2 Tor und Proben · AP-3 Nachweis über die Grundmenge ·
AP-4 Abgrenzungsbeleg · AP-5 Aktionslisten · AP-6 Negativproben Aktion · AP-7 Nachweis gegen die
Rollenquellen · AP-8 Dirigent-Bereich (Nachtrag nach erteilter DoR, siehe Teil 3).

**Reihenfolge der Pakete:** AP-5 bis AP-7 (Teil 2) sind **dringlicher** als AP-1 bis AP-4 (Teil 1).
Teil 2 hält heute nur durch eine befristete Übergangsregel; Teil 1 kostet einen ausgefallenen
Zustandswechsel, der sichtbar ist. *Wer beides in einem Bau macht, macht Teil 2 zuerst.*

---

## Rückweg

Der Bau ändert **einen regulären Ausdruck in einer Datei**. Rückweg ist der Revert dieses einen
Commits; es entsteht kein Zustand, der zurückgebaut werden müsste — `docs/STATUS.md` wird durch
den Bau nicht geschrieben (A-43-7).

**Ein Sonderfall braucht Aufmerksamkeit:** Wird nach dem Bau die Tafel neu erzeugt und **danach**
revertiert, stehen Z-Zustände in `docs/STATUS.md`, die das alte Muster nicht mehr erzeugen kann.
*Deshalb: erst Bau abnehmen, dann Tafel erzeugen — nicht in einem Schritt.*

---

## Folgeposten (benannt, NICHT Teil dieses Auftrags)

1. **Die fünf Z1-W1-Zustandscommits** werden nach dem Transport vom **Integrator** als
   **nicht-leere** Commits mit **je einer** Kennung nachgeholt (Plan-Prüfer 11:39: `96d59689` ist
   leer — ein Elter, Baum-Hash identisch, 0 geänderte Dateien).
2. **Die zwei Z2-W0-Sammelbetreffs** tragen je vier bis fünf Kennungen. Sie brauchen dieselbe
   Behandlung; die Aufteilung ist Integrator-Arbeit.
3. **`Z1-W1-1..5` als Bereichsangabe** ist keine Kennung und wird es auch nach A-43 nicht. Ob die
   Schreibweise künftig zulässig sein soll, ist eine Entscheidung über die Kennungssystematik —
   Yama und Dirigent, nicht dieser Auftrag.
4. **Die Übergangsregel README 6f wird zurückgenommen** — Auftrag des Dirigenten (12:26), **Folge-
   posten für ihn, ausdrücklich kein Kriterium.** Sobald das erweiterte Aktionsvokabular
   transportiert ist, tragen die Rollenquellen ihre echte Tätigkeit wieder in `aktion:`, und die
   Tor-Wörter (`aktion: bauen` bei Rollen, die nichts bauen) entfallen.
   **Warum das nicht vergessen werden darf:** solange die Krücke steht, sagt `aktion` etwas anderes
   als `taetigkeit`. *Eine befristete zweite Wahrheit, die niemand zurücknimmt, ist eine
   unbefristete.* Sichtbar wird das Ende an A-43-12: wenn die echten Tätigkeiten durchlaufen,
   braucht kein Tor-Wort mehr gesetzt zu werden.
5. **Der Hinweg des Planners ist blockiert** (`planner-BASE_BLOCKED.yaml`, 12:3x) — kein Kriterium,
   aber für die Bau-Reihenfolge wichtig: der Planner-Baum trägt bis auf Weiteres das **alte** Tor,
   weil `git merge` an der geparkten Z0-I1-Datei abbricht. **Belege dieses Blattes, die am Bau-Stand
   `c82df498` gemessen sind, sind darum über `git show` erhoben, nicht am Arbeitsbaum** — das ist
   im jeweiligen Messbefehl genannt.

---

## Zur Kennungsvergabe — gemessen, offengelegt, berichtigt

**Der Auftrag verlangt die nächste freie A-Kennung samt Messung, dass sie frei ist.** Sie ist frei
(siehe `kennung_geprueft`). **Beim Messen bin ich auf eine ältere Anweisung Yamas gestoßen und
lege sie offen**, statt sie zu übergehen:

> `43771e3b` (16.08., Planner): *„Yama hat vier offene Regelbauten als Maximum gesetzt, A-41 und
> A-42 sind bereits fünf und sechs; ein A-43 wäre der siebte gegen seine ausdrückliche Anweisung."*

### BERICHTIGT — meine erste Zählung stand auf einem veralteten Stand

**Ich hatte A-42 als `BEREIT`/offen gezählt und daraus „A-43 wäre die fünfte" gefolgert. Das war
falsch.** Der Dirigent hat es 12:26 berichtigt; **selbst nachgemessen statt übernommen**:

```
git log --all --format=%s | grep -E '^([a-z-]+: )?zustand: A-42'
  -> integrator: zustand: A-42 · ABGENOMMEN · release-pruefer · abnahme b1c0c2d4 — elf von elf
git show auto/hausplaner-integration:docs/STATUS.md | grep 'A-42'
  -> | **A-42** Befundnotizen ziehen um | `ABGENOMMEN` | Release-Prüfer | … Abnahme b1c0c2d4 |
```

| Auftrag | Zustand (Integrationsstand) | offen? |
|---|---|---|
| A-37 | **ABGENOMMEN** (Evaluator 12:04) | geschlossen |
| A-38 | `BEREIT` | **offen** (geparkt, wird nicht gebaut) |
| A-39 | `BEREIT` | **offen** (geparkt, wird nicht gebaut) |
| A-40 | `ENTWURF` | **offen** |
| A-41 | `BETRIEBSBESTAETIGT` | geschlossen |
| A-42 | **`ABGENOMMEN`** | geschlossen |

**Offen sind drei. A-43 ist die vierte — Yamas Maximum ist damit erreicht, nicht überschritten.
Es gibt keinen Governance-Konflikt.** Der Vorbehalt entfällt; die Frage bleibt Yama zur Kenntnis
vorgelegt (`planner-frage-kennungsmaximum.yaml`, Antwort des Dirigenten 12:26 samt Berichtigung).

> **Warum ich falsch lag, und warum das hierher gehört:** ich habe die Tafelzeile aus **meinem
> Worktree** (`c11f97ac`) gelesen. Der Zustandscommit für A-42 kam danach in die Integration.
> **Das ist exakt der Gegenstand dieses Blattes** — ein Zustand entsteht aus einem Commit, und wer
> an einem alten Stand misst, liest einen alten Zustand. *Ein Beleg ohne Standangabe ist kein
> Beleg;* dieselbe Klasse wie der Errata-Posten 4 zu A-37-22c.

**Was bleibt:** Ich habe das Blatt geschrieben, weil der Dirigent es unter Vollmacht beauftragt hat
und die Sacharbeit von der Kennungsfrage unabhängig ist. **Ich habe die Frage nicht still gelöst,
und ich habe die Berichtigung nicht still eingearbeitet** — beides steht hier.

*Sollte die Kennung dennoch wechseln, ist der Umbau billig: Dateiname, `auftrag:`-Feld und die
Kriterienpräfixe. Der gemessene Inhalt bleibt unberührt.*

---

## Messwarnung für jeden, der diese Zahlen nachfährt

**Beim Erstellen dieses Blattes ist mir derselbe Zählfehler ZWEIMAL unterlaufen** — beide Male hat
die Summenprobe ihn gefangen, nicht die Aufmerksamkeit:

```
1. Runde  grep 'W-17/1' | sort | uniq -c    ->  5    FALSCH — zaehlt auch Zeilen, in denen
          grep -c 'zustand: W-17/1'         ->  6    W-17/1 im BELEG-Teil steht
2. Runde  grep -c 'zustand: A-'             -> 15    FALSCH — dieselbe Ursache, andere Kennung
          Kennung isoliert extrahiert       -> 14    richtig
Summenprobe Stand 86c407e5:  14 + 6 + 3 = 23 = Grundmenge  ✓
```

**Beim zweiten Mal habe ich den Fehler nicht vermieden, obwohl ich ihn eine Stunde zuvor selbst
aufgeschrieben hatte.** *Eine Warnung im Text verhindert nichts; der geänderte Messweg tut es.*
Deshalb steht die isolierte Extraktion jetzt **im Messbefehl von A-43-4** und nicht nur hier.

**Wer eine Teilmenge zählt, zählt die Summe gegen.** Ohne die Gegenprobe wäre beide Male eine
falsche Zahl in ein Kriterium gewandert — und ein Beleg, der um eins danebenliegt, ist als Beleg
wertlos.

### Und die zweite Lehre: eine Zahl ohne Stand altert

Die Grundmenge war **22**, als ich sie erhob, und **23**, als der Plan-Prüfer nachmaß — dazwischen
lag ein einziger Zustandscommit (`aec713a6`, 12:20:02). **Niemand hat einen Fehler gemacht.**
Dasselbe traf meine Zählung der offenen Regelbauten: A-42 war in *meinem* Baum `BEREIT` und in der
Integration längst `ABGENOMMEN`. **Zweimal an einem Vormittag hat ein fehlender Standbezug eine
richtige Messung falsch aussehen lassen.** Jede Zahl in diesem Blatt trägt deshalb ihren Stand.
