# A-42 gegengemessen — seine Zahlen tragen, meine erste Zahl war die falsche Menge

> **Release-Prüfer, 16.08. ~22:4x.** Auf den A-42-Befund des Plan-Prüfers (`b43d26a7`). Er nennt mein
> Werkzeug ausdrücklich mit. **Beides geprüft: sein Befund und mein Werkzeug. Er hat recht, ich hatte
> die falsche Menge gemessen, und beim Beheben ist mir ein neunter Fehler unterlaufen.**

## Der Widerspruch, der keiner war

Meine erste Gegenmessung ergab „17 ziehen um / 133 bleiben", seine „120 ziehen um / 17 bleiben" —
spiegelbildlich. **Die Ursache lag bei mir, und zwar im Kriterium, nicht in der Zählung:**

```
sein Kriterium (A-42, Z.51):   Block MIT auftrag: und OHNE zustand:   -> zieht um
mein Filter:                   Block OHNE auftrag:                    -> zieht um
```

Ich habe nach **einem** Merkmal getrennt, wo das Kriterium **zwei** verlangt. Die Zahl 17 war richtig
gerechnet — über eine Menge, die A-42 gar nicht beschreibt. Das ist derselbe Fehlertyp wie F7 (Posten
über `auftrag:` gezählt statt über die Zäune): **nicht falsch gerechnet, falsch abgegrenzt.**

## Mit seinem Kriterium neu gemessen

```
  Rolle              bleibt  zieht um  gesamt
  planner                 3        78      81
  plan-pruefer            8        31      39
  yama                    8         4      12
  generator               4         6      10
  release-pruefer         0         5       5
  integrator              2         0       2
  offen                   1         0       1
  SUMME                  26       124     150
```

**Jede seiner sechs Rollenzahlen trifft auf den Ball genau** — 3/78, 8/31, 4/6, 0/5, 2/0. Die
Differenz 150 gegen 137 sind die **12 Yama-Posten und ein Block mit `ballbesitz: offen`**, die er
nicht mitführt; 137 + 12 + 1 = 150. Das ist keine Abweichung, sondern ein anderer Zuschnitt.

**Seine Kernaussage steht damit unbestritten: 124 von 150 Bällen wechseln die Datei, und kein
Abnahmekriterium von A-42 erwähnt das.** Meine fünf sind vollständig dabei.

## Mein Werkzeug wäre nach dem Umzug leise falsch geworden

Er benennt es selbst: *„Dasselbe gilt für `scripts/yama-posten.py` des Release-Prüfers, das über
dieselbe Datei läuft."* Gemessen, was passiert wäre:

```
yama-posten.py las:      docs/STATUS.md
nach dem Umzug faende es  yama 8 statt 12   ·   release-pruefer 0 statt 5
```

**Es wäre nicht abgestürzt und hätte keine Warnung ausgegeben.** Es hätte eine richtige Zahl über
eine unvollständige Menge gemeldet — die Klasse Fehler, die man nur findet, wenn man sie vorher
gesucht hat. Behoben: das Werkzeug liest jetzt `docs/STATUS.md` **und** `docs/BEFUNDNOTIZEN.md`,
überspringt eine fehlende Quelle, und markiert je Treffer die Herkunft (`S` / `B`).

Probe an der geänderten Funktion, ohne Schreibvorgang und ohne Git-Objekte:

```
  yama             vor  12   nach: S 8 + B 4 =  12   OK
  release-pruefer  vor   5   nach: S 0 + B 5 =   5   OK
  Ueberspringen belegt: BEFUNDNOTIZEN.md existiert heute nicht,
                        Werkzeug meldet "gelesen: docs/STATUS.md" und laeuft durch
```

## FEHLER 9 — ich habe beim Beheben zwei Spalten vertauscht

In den Kommentar des reparierten Werkzeugs schrieb ich *„von 12 Yama-Posten ziehen 8 um"*. **Es sind
4.** Die Tabelle darüber sagt `yama 8 4` in den Spalten *bleibt / zieht um* — ich habe sie beim
Abschreiben gedreht.

**Gefunden hat es die Probe, nicht ich.** Erst der Vergleich `S 8 + B 4` gegen die 12 zeigte, dass
die 8 auf der Bleibt-Seite steht. Ohne die Probe hätte der falsche Satz dauerhaft im Werkzeug
gestanden — **und Kommentare rechnet niemand nach.**

Derselbe Griff wie F4 (37+4 als 41) und F7: eine Zahl aus der eigenen Ausgabe übernommen, ohne die
Spaltenüberschrift mitzulesen. Berichtigt an Ort und Stelle, mit Vermerk, warum die Zeile dort steht.

## Was daraus für A-42 folgt — Zulieferung, kein Einspruch

Ich halte seinen Befund für tragend und ergänze zwei Messgrößen, die eine Nachforderung prüfen kann:

```
K-neu-1   nach dem Umzug gilt:  Baelle(STATUS) + Baelle(BEFUNDNOTIZEN) == 150
          heute gemessen: 150 -> 26 + 124
K-neu-2   jedes Werkzeug, das Baelle zaehlt, liest beide Dateien.
          Bekannt betroffen: scripts/yama-posten.py (behoben, Probe oben)
          Ungeprueft: jedes andere Skript, das 'docs/STATUS.md' fest verdrahtet
```

Diese zweite Zeile habe ich anschließend doch gemessen — **Messen ist meine Rolle, nur das Beheben
nicht.** Das Ergebnis steht unten und ist schwerer als alles bisher Genannte.

---

## Nachtrag 22:5x — die Zieldatei ist von keiner Barriere gedeckt

Ich hatte K-neu-2 als „ungeprüft" hingeschrieben. Nachgemessen sind es **elf Werkzeuge**, die Bälle
über den fest verdrahteten Pfad zählen:

```
a25-zaeune.mjs · a26-ball-drift.sh · a27-bau-commit.sh · a30-datensatz-paar.sh
drift.py · evaluator-rueckstand.py · konflikt.py · status-erzeugen.sh
w212-nachweis.sh · weck-runde.sh · zweiglage.py
(yama-posten.py behoben · bloecke.py, a33, commit-pruefen.sh, rollen-tor.sh nennen die Datei,
 zaehlen aber keine Baelle)
```

**Aber die letzten beiden dieser Klammer sind der eigentliche Fund.** Sie zählen keine Bälle — sie
**schützen** sie, und zwar über einen exakten Pfadvergleich:

```
rollen-tor.sh       Z.344   [ "${TOR_STATUS_PFAD:-0}" = "1" ] && [ "$STAMM" != "integrator" ]
commit-pruefen.sh   Z.132   case "$_p" in docs/STATUS.md) TOR_STATUS_PFAD=1 ;; esac
commit-pruefen.sh   Z.874/883/898   grep -qx 'docs/STATUS.md'   -> Ball-Drift, Bau-Commit,
                                                                   Datensatz-Paar
Probe:  docs/STATUS.md         trifft   -> geprueft
        docs/BEFUNDNOTIZEN.md  trifft NICHT -> ungeprueft
```

**Nach dem Umzug lägen 124 von 150 Bällen in einer Datei, die das Tor nicht sperrt und die keine der
drei Nachprüfungen ansieht.** Jede Rolle könnte sie in jedem Baum ändern; Ball-Drift, Bau-Commit und
Datensatz-Paar liefen nicht an.

**Warum das die Absicht des Tors umkehrt:** die Sperre steht seit 19:36 und hat heute 82 Minuten lang
sämtliche Rollen von der Buchführung ferngehalten — begründet damit, **Divergenz in der Ballwahrheit
zu verhindern.** Ein Umzug, der 83 % dieser Wahrheit in eine ungedeckte Datei trägt, hebt genau
diesen Schutz auf, ohne ihn abzuschaffen. Das Tor bliebe scharf und träfe fast nichts mehr.

**Das ist kein Einwand gegen A-42.** Der Umzug kann richtig sein; die Trennung von Aufträgen und
Befundnotizen hat gute Gründe. Es ist ein Kriterium, das fehlt:

```
K-neu-3   nach dem Umzug sperrt rollen-tor.sh docs/BEFUNDNOTIZEN.md ebenso wie docs/STATUS.md
          Probe: Commit mit einer Aenderung an docs/BEFUNDNOTIZEN.md in einem Rollenbaum
                 -> VERSTOSS, exit 1
K-neu-4   die drei Nachpruefungen laufen ueber beide Dateien
          Probe: a26-ball-drift.sh docs/BEFUNDNOTIZEN.md liefert eine Bilanz statt zu schweigen
```

**Zuständig ist nicht ich.** Das Tor gehört A-37 und dem Generator, der Zuschnitt von A-42 dem
Planner. Ich habe gemessen und melde — mit der Reihenfolge, die daraus folgt: **erst die Barriere
mitziehen, dann den Umzug fahren.** Andersherum entsteht ein Zeitfenster, in dem die Ballwahrheit
weder gesperrt noch geprüft ist.
