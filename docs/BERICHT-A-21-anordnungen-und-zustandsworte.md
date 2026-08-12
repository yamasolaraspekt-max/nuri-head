# Baubericht A-21 — E1 und E3 verankert, drei Zustandsworte geordnet

```yaml
auftrag: "A-21"
rolle: "generator"
blatt: docs/auftraege/aktiv/A-21-yamas-anordnungen-und-drei-zustandsworte.md
basis_sha: 877f81ee
in_arbeit_commit: "96b588e0"
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Ein Kriterienpaar lässt sich nicht beides wörtlich erfüllen, und ich entscheide das nicht.**
> *A-21-5 verlangt eine Änderung an P-02s Tafelzeile, A-21-6 verlangt, dass **keine** Tafelzeile
> außer W-21Ls geändert wird. Beide Zahlen stehen unten, die Entscheidung gehört dem Evaluator.*

## Rot-Lage vor dem Bau — selbst gemessen, nicht übernommen

```text
E1 in docs/ARBEITSREGELN.md            0
E3 in docs/ARBEITSREGELN.md            0
ERLEDIGT in docs/ARBEITSREGELN.md      0
VORLAGE  in docs/ARBEITSREGELN.md      0
ZURUECKGESTELLT am Zustandsort         2   (Tafelzeile + zustand:-Feld, beide W-21L)
DECISION_BLOCKED in ARBEITSREGELN.md  10   <- der definierte Zustand gab es schon
```

## A-21-1 · E1 steht in §11, mit dem Befehl und mit Yamas Datum

**Verankert am Ende von §11**, *dort, wo bereits steht „Zahlen ohne zugehörigen Befehl und Commit
gelten nicht als Beweis" — E1 ist genau dessen Vollzug.*

```text
git show HEAD:<pfad> | diff - <pfad>        je beruehrter Datei, vor jeder CODE_FERTIG-Meldung
```

**Herkunft genannt:** *Yamas Anordnung vom **10.08.**, erteilt durch den Release-Prüfer in seinem
Namen mit ausdrücklich übergebenem Ball, `docs/PROZESSPRUEFUNG-03.md`.*

> **Und die Umkehrung steht dabei, weil sie zweimal übersehen wurde:** *auch ein **leerer**
> `git diff` ist kein Beleg — nach einem Commit ist er zwangsläufig leer.* **Wer belegen will, dass
> er nichts Fremdes angefasst hat, misst mit `git show <bau-sha> -- <pfad>`.** *Beide belegten Fälle
> sind genannt: mein eigenes A-20-5 (`99fc86cd`) und A-21s eigener Kriterienblock (`605fde3b`).*

```text
E1 nach dem Bau:  2 Treffer  (Ueberschrift und der Satz in der Regel)
```

## A-21-2 · E3 steht in §13, wo der Zähler beschrieben ist

**Als Tabelle, nicht als Fließtext** — *die vierte Fehlerklasse „Zuordnung annehmen statt messen"
führt eine Spalte `Unterformen mit Barriere`:*

| Unterform | Barriere |
|---|---|
| **Ort** | **V2** — der absolute Pfad steht daneben |
| **Zeitpunkt** | **V1** — `git fetch` im selben zitierten Befehl |
| **Zustand** | **NEU** — `git show HEAD:<p> \| diff`, das ist E1 |

**Die Begründung aus der Quelle mitgenommen, nicht erfunden:** *die Klasse ist **semantisch**, ihre
Unterformen sind es nicht — eine fünfte Klasse würde denselben Fehler zweimal zählen.* **Die drei
Barrieren V1, V2 und NEU habe ich in `docs/PROZESSPRUEFUNG-03.md:88-90` nachgelesen, statt sie aus
dem Kürzel zu raten.**

```text
E3 nach dem Bau:  1 Treffer
```

## A-21-3 · ZURUECKGESTELLT ist am Zustandsort abgeschafft

```text
Zustandsort   vorher 2   nachher 0
  grep -cE '^zustand: *ZURUECKGESTELLT|^\| \*\*[A-Z]+-?[0-9]+[^|]*\| *`?ZURUECKGESTELLT'

W-21L traegt DECISION_BLOCKED:   Tafelzeile 1   ·   zustand:-Feld 1
```

**Volltext bleibt bei 14, und das ist Absicht.** *Die zwölf übrigen Treffer sind Belege — zwei
`vertretungsentscheid:`-Felder, die Yamas Anweisung zitieren, Befunde, Vergleichsmessungen, der
Titel und der `dor_beleg:` dieses Auftrags selbst.* **Sie bleiben alle stehen; A-20-4 verbietet
wörtlich, zu löschen, ohne zu sagen, was gegolten hat.**

> **Die Rechnung geht auf:** *15 am Elter − 2 umgestellte + 1 neuer (mein Vermerk
> `zustandswort_umgestellt:`, der den alten Wortlaut festhält) = **14**.* **Der alte Wortlaut ist
> nicht verschwunden, er steht jetzt in dem Feld, das die Umstellung begründet.**

**`fortsetzung_zustand: ENTWURF` mit eingetragen** — *§3 verlangt beim Eintritt in
`DECISION_BLOCKED` den vorherigen Prüfzustand für die Rückkehr; ohne ihn wäre die Umstellung
formal unvollständig gewesen.*

## A-21-4 · ERLEDIGT und VORLAGE sind in §3 definiert, je mit §3-Platz-Angabe

```text
ERLEDIGT   ausgefuehrt und gegengeprueft, ohne je Code erzeugt zu haben
           belegt eine IN_ARBEIT-Stelle nach §3:  NEIN
           Realfall A-06, ausgefuehrt 880eb726

VORLAGE    Verfahrensvorschlag, wartet auf Yamas Entscheidung
           belegt eine IN_ARBEIT-Stelle nach §3:  NEIN
           zaehlt auch nicht im §13-Zaehler       Realfall P-02, c2de1eec
```

**Beide Realfälle habe ich geöffnet und gelesen, nicht aus dem Auftragsblatt übernommen**
(Pflichtprüfung 7). *`A-06` trägt „ausgeführt `880eb726` · gegengeprüft"; `P-02` trug seine
Definition in der Kommentarspalte.*

> **Warum die §3-Platz-Angabe zur Definition gehört:** *§3 lässt genau einen `IN_ARBEIT` zu — wer
> einen Zustand einführt, ohne zu sagen, ob er auf diese Schranke zählt, hat kein Wort erklärt,
> sondern eine Lücke geschaffen.*

## A-21-5 · P-02s Tafelzeile verweist jetzt auf §3

**Vorher stand die Regel ad hoc in der Kommentarspalte:** *„kein Bauauftrag, zählt nicht im
§13-Zähler".* **Jetzt verweist die Zeile auf §3 — und der alte Wortlaut steht ausdrücklich als
BELEG daneben, wörtlich und nicht gelöscht.**

> *Eine Zustandsregel in der Kommentarspalte einer Tabellenzeile erfährt nur, wer genau diese Zeile
> liest. Derselbe Fehlertyp wie A-20s vier Zustandsorte, eine Ebene kleiner.*

## A-21-6 · Am Commit gemessen — und hier stoßen zwei Kriterien aneinander

**Der Nachweis, wie das berichtigte Kriterium ihn verlangt** — `git show <bau-sha> -- docs/STATUS.md`:

```text
geaenderte zustand:-Zeilen     1     -zustand: ZURUECKGESTELLT / +zustand: DECISION_BLOCKED
                                     ausschliesslich W-21L
geaenderte Tafelzeilen         2     W-21L  (Zustandsspalte: ZURUECKGESTELLT -> DECISION_BLOCKED)
                                     P-02   (NUR Kommentarspalte; Zustand bleibt VORLAGE)
```

> **A-21-6 sagt wörtlich „geänderte `zustand:`-Zeilen UND Tafelzeilen ausschließlich bei W-21L".
> Nach diesem Wortlaut ist es rot: es sind zwei Tafelzeilen.** *Nach dem Sinn — „**KEIN anderer
> Auftragszustand wurde geändert**", so beginnt dasselbe Kriterium — ist es grün: **P-02s Zustand
> ist unverändert `VORLAGE`.***

**Und die zweite Änderung ist nicht meine Zutat, sondern A-21-5s ausdrückliche Forderung.** *Beide
Kriterien lassen sich nicht beides wörtlich erfüllen — A-21-5 verlangt die Änderung, die A-21-6
wörtlich ausschließt.*

**Ich deute nichts still um. Beide Zahlen stehen hier, die Entscheidung gehört dem Evaluator.**

*Für den Fall, dass er dem Wortlaut folgt: der Rückweg ist eine Zeile — P-02s Tafelzeile auf den
alten Stand, dann ist A-21-6 wörtlich grün und A-21-5 rot. Umgekehrt geht es nicht.*

## A-21-7 · Die Wartebedingung, am Elter belegt

```text
git show 877f81ee:docs/STATUS.md      der ELTER des IN_ARBEIT-Commits 96b588e0

  Tafelzeile      | **A-20** Zustand an vier Orten | BETRIEBSBESTAETIGT | – |
  Zustandsfeld    zustand: BETRIEBSBESTAETIGT      ballbesitz: —  # Kette vollstaendig
```

**Die verschärfte Schwelle ist erfüllt, nicht nur die alte `ABGENOMMEN`-Schwelle** — *und gemessen
am Commit, nicht am Arbeitsbaum, wie die berichtigte Fassung es verlangt.*

## Berührte Dateien

```text
docs/ARBEITSREGELN.md    +74 / -0   §3 (ERLEDIGT, VORLAGE) · §11 (E1) · §13 (E3)
docs/STATUS.md            +5 / -3   W-21L beide Zustandsorte · P-02 Kommentarspalte
docs/BERICHT-A-21-anordnungen-und-zustandsworte.md   neu
```

**`must_preserve`:** *0 Dateien außerhalb `docs/`, kein Produktivcode, keine Löschung. Der Bau ist
additiv bis auf die drei ersetzten Zeilen in `docs/STATUS.md`; `git revert` genügt.*
