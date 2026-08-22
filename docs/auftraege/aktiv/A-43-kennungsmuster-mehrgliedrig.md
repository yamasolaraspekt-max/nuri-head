# A-43 — Das Kennungsmuster erkennt mehrgliedrige Kennungen nicht

```yaml
auftrag: "A-43"
werkzeug: "— (Werkzeug der Rollenkette, kein Hausplaner-Werkzeug)"
art: "MUSTERKORREKTUR — EIN regulaerer Ausdruck in scripts/status-erzeugen.sh wird erweitert.
      KEIN Produktcode, KEINE Aenderung an docs/STATUS.md durch den Bau, KEIN Blattumbau."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer (NACH der A-37-Nachpruefung, so der Dirigent)."
dor_schnitt_sha: "c11f97ac"
status_steht_in: docs/STATUS.md
basis_sha: c11f97ac
prioritaet: P1
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 22.08. 12:12 — Lease SPEZ-planner-kennungsmuster, fencing_token 1."
kennung_geprueft: "A-43 gemessen, nicht geraten: docs/ 0 Treffer, Steuerungsablage 1 Treffer
                   (ein VORSCHLAG in meiner eigenen Frage-Datei vom 21.08., kein vergebener
                   Auftrag), gemeinsamer Checkout 0, git log --all --grep 1 Treffer (derselbe
                   Vorschlag zitiert). KEIN Blatt, KEINE Tafelzeile, KEIN Datensatz. Frei.
                   ACHTUNG — offener Punkt zur Kennungsvergabe: siehe Abschnitt 'Vorbehalt'."
gebaut_in: "ticket-rolle-generator (rolle/generator)"
staut_hinter: "A-37 ABGENOMMEN (erfuellt, Evaluator 12:04) UND Transport in die Integration.
               Vorher nicht bauen: der Bau beruehrt eine Datei, die A-37 gerade transportiert."
regelgrundlage: "Entscheidung des Dirigenten unter Vollmacht, 22.08. 12:03:49 — Weg A
                 (ereignisse/ERRATA-planner-A-37/dirigent-entscheidung-kennungsmuster.yaml)."
anlass: "Planner-Befund 22.08. 11:44 (planner-befund-kennungsmuster-kennt-Z-nicht.yaml):
         das Muster erkennt Z0-I1, Z1-W1-1, Z2-W0-1 und A-37-22b nicht. Zusammen mit dem
         Plan-Pruefer-Befund 11:39 (leerer Commit 96d59689) die zweite Ursache dafuer, dass
         fuenf Z1-W1-Auftraege seit 15 h auf BEREIT stehen."
```

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
`zustand: Z` 6, `zustand: A-` 21)"*. **Diese Zahlen sind ungeankert gemessen.** Selbst nachgefahren:

```
ohne Anker   git log --all --format=%s | grep -c 'zustand: Z'       -> 6
             git log --all --format=%s | grep -c 'zustand: A-'      -> 21
mit Anker    ... | grep -cE '^([a-z-]+(-[0-9]+)?: )?zustand: Z'     -> 3
             ... | grep -cE '^([a-z-]+(-[0-9]+)?: )?zustand: A-'    -> 13
             ... | grep -cE '^([a-z-]+(-[0-9]+)?: )?zustand: '      -> 22   (Summe der Grundmenge)
```

**Die Hälfte der Z-Treffer sind Zitate** — Berichte *über* eine Zustandsmeldung, mitten im Betreff,
keine Zustandsmeldung. *Wer Zitate mitzählt, zählt Belege als Fälle.* Das Skript beschreibt diese
Unterscheidung zwei Zeilen unter seinem eigenen `BEINAHE`-Muster selbst.

**Die geankerte Grundmenge, vollständig aufgelöst (22 Zeilen, einzeln aufgelistet):**

| Kennungsform | Anzahl | heute erkannt |
|---|---|---|
| `A-NN` (A-37, A-38, A-39, A-41, A-42) | 13 | ja |
| `W-17/1` | 6 | ja |
| `Z…` (mehrgliedrig) | 3 | **nein** |
| **Summe** | **22** | |

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

## Abnahmekriterien

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

  **Verlangt:** Der Nachweis nennt die Grundmenge **geankert** (`^([a-z-]+(-[0-9]+)?: )?zustand: `)
  und benennt die Differenz zur ungeankerten Zählung. Alle Kennungen der geankerten Grundmenge, die
  **genau eine** Kennung tragen, werden nach dem Bau zu **100 %** erkannt.

  **Messbefehl:**
  ```
  git log --all --format=%s | grep -cE '^([a-z-]+(-[0-9]+)?: )?zustand: '        -> Grundmenge
  ... je Betreff gegen WORTLAUT; gezaehlt wird mit wc -l, nicht mit den Augen
  ```

  **Heutiges (rotes) Ergebnis:** Grundmenge **22** (A- 13 · W-17/1 6 · Z 3); ungeankert **29**.
  Von den 22 werden heute **19** erkannt, **3 nicht** — und genau diese 3 tragen Mehrfach- bzw.
  Bereichskennungen und bleiben auch nach dem Bau abgewiesen (siehe A-43-5).

  **Absage-Regel:** Eine Zählung mit `grep -c 'zustand: Z'` **ohne** Anker erfüllt A-43-4 nicht —
  sie zählt Zitate als Fälle. *Selbst belegt: 6 statt 3, also die Hälfte zu viel.*

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

**Arbeitspakete:** AP-1 Muster · AP-2 Tor und Proben · AP-3 Nachweis über die Grundmenge ·
AP-4 Abgrenzungsbeleg.

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

---

## Vorbehalt zur Kennungsvergabe — gemessen, nicht entschieden

**Der Auftrag verlangt die nächste freie A-Kennung samt Messung, dass sie frei ist.** Sie ist frei
(siehe `kennung_geprueft`). **Beim Messen bin ich auf eine ältere Anweisung Yamas gestoßen und
lege sie offen**, statt sie zu übergehen:

> `43771e3b` (16.08., Planner): *„Yama hat vier offene Regelbauten als Maximum gesetzt, A-41 und
> A-42 sind bereits fünf und sechs; ein A-43 wäre der siebte gegen seine ausdrückliche Anweisung."*

**Stand heute selbst gemessen** (Zustände aus `docs/STATUS.md`):

| Auftrag | Zustand | offen? |
|---|---|---|
| A-37 | `CODE_FERTIG` → **ABGENOMMEN** (Evaluator 12:04) | schließt sich mit dem Transport |
| A-38 | `BEREIT` | **offen** |
| A-39 | `BEREIT` | **offen** |
| A-40 | `ENTWURF` | **offen** |
| A-41 | `BETRIEBSBESTAETIGT` | geschlossen |
| A-42 | `BEREIT` | **offen** |

**Nach A-37s Transport sind es genau vier offene — A-43 wäre die fünfte.** Die Lage ist damit
deutlich besser als am 16.08. (damals sechs), aber die Zahl liegt weiterhin **eine über** dem
genannten Maximum.

**Was ich getan habe und was nicht:** Ich habe das Blatt geschrieben, weil der Dirigent es unter
Vollmacht beauftragt hat und die Sacharbeit von der Kennungsfrage unabhängig ist. **Ich habe die
Frage nicht still gelöst.** Ob A-43 bleibt, ob der Auftrag als Unterkriterium an ein bestehendes
Blatt andockt, oder ob Yamas Maximum überholt ist, entscheiden Yama und der Dirigent —
gemeldet als `planner-frage-kennungsmaximum.yaml`.

*Sollte die Kennung wechseln, ist der Umbau billig: Dateiname, `auftrag:`-Feld und die
Kriterienpräfixe. Der gemessene Inhalt bleibt unberührt.*

---

## Messwarnung für jeden, der diese Zahlen nachfährt

**Beim Erstellen dieses Blattes ist mir der vierte Zählfehler derselben Bauart in dieser Sitzung
unterlaufen** — und die Summenprobe hat ihn gefangen, nicht die Aufmerksamkeit:

```
grep 'W-17/1' | sort | uniq -c   ->  5     FALSCH: zaehlt auch Zeilen, in denen
                                            W-17/1 im BELEG-Teil steht
grep -c 'zustand: W-17/1'        ->  6     richtig
Summenprobe: 13 + 6 + 3 = 22 = Grundmenge  ✓
```

**Wer eine Teilmenge zählt, zählt die Summe gegen.** Ohne die Gegenprobe wäre eine falsche Zahl in
ein Kriterium gewandert — und ein Beleg, der um eins danebenliegt, ist als Beleg wertlos.
