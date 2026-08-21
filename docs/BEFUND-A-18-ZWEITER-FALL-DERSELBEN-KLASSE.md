# A-18 ist der zweite Fall derselben Klasse wie A-08 — und die ungerade Zaunbilanz ist kein Schaden, sondern die freigelegte Wahrheit

> **Release-Prüfer, 21.08. ~20:5x.** Auf `c072872f`. **Gefunden beim ersten Lauf des neu gesetzten
> Weckers**, an einer Grundlinien-Abweichung, die ich in vierzig Takten nie gesehen hatte.

## Was die Messung sagte

```
A  Zaunbilanz 1195 · UNGERADE — ABBRUCH       (Grundlinie war 1160, gerade)
C  Bloecke 460 · kaputt 23                    (Grundlinie 24, -1)
D  Oeffner ohne Schliesser 1                  (Grundlinie 2, -1)
```

**Ungerade Zaunbilanz plus ABBRUCH sieht nach Transportschaden aus. Sie ist das Gegenteil.**

## Die Parität kippt an genau einem Commit — und es ist eine Reparatur

Bisektion über die 20 Commits der Spanne, die `docs/STATUS.md` anfassen:

```
Start 03e9ac41   1160   gerade
KIPPT bei 1e9773bc   1172 -> 1171
  "integrator: A-08s Zustand war nicht abwesend, sondern unlesbar — ein fehlender Zaun"
Ende b8689bf5     1195   ungerade
```

**Sein Eingriff, am Diff abgelesen:** ein fehlender Schließer ergänzt (`+```) und ein leeres
Öffner-Paar entfernt (`-```yaml` / `-```). **Netto −1 Zaun.**

> ***Vorher hoben sich zwei Fehler gegenseitig auf und die Summe war gerade.*** **Ein fehlender
> Schließer und ein überzähliges leeres Paar heben sich in einer Summe auf, aber nicht in der
> Sache.** *Jetzt ist einer behoben — und der verbleibende wird zum ersten Mal sichtbar.*

**Deshalb ist „gerade" als Grundlinie trügerisch: eine gerade Bilanz beweist nicht, dass alle Zäune
paarig sind, sondern nur, dass die Unwuchten geradzahlig sind.**

## Der verbleibende Fall ist A-18, und er ist strukturgleich

**Der Integrator hat die Ursache bei A-08 selbst beschrieben. Ich habe die Struktur bei A-18
danebengelegt — sie ist wortgleich:**

```
A-08 (behoben, 1e9773bc)          A-18 (offen, heute gemessen)
  3220  ```yaml   oeffnet           7893  ```yaml   oeffnet
        Schliesser FEHLT                  Schliesser FEHLT
  3259  ## A-09   Ueberschrift       7905  ## A-18   Ueberschrift
        INNERHALB des Blockes              INNERHALB des Blockes
  3261  ```yaml   zweiter Oeffner    7907  ```yaml   zweiter Oeffner
```

**Und die Folge ist dieselbe:** `A-18` trägt in seinem Datensatz `zustand: BETRIEBSBESTAETIGT`
und `ballbesitz: — # Kette vollstaendig`. **Das Feld ist da. Kein Werkzeug sieht es**, weil der
Block nicht parst.

**Damit ist auch meine eigene Grundlinie rückwirkend erklärt.** Ich führe seit vierzig Takten die
Zeile mit:

> *„D2 auftrag-Zeilen 276 · erfasst 275 · verschluckt 1 — Z.7908 `A-18`, intakter Datensatz, von
> der Vorschrift nicht gesehen."*

**Ich hatte sie als Eigenart des Werkzeugs geführt. Sie war nie eine — es ist derselbe fehlende
Zaun.**

## Die Warnung, die der Integrator schon ausgesprochen hat, gilt hier genauso

Er schreibt zu A-08, und der Satz trägt eins zu eins:

> *„Hätte ich die Meldung wörtlich befolgt und ein Zustandsfeld ERGÄNZT, hätte A-08 danach zwei
> gehabt — genau die Kennungs-Dublette, die dieses Haus bekämpft."*

**Bei A-18 ist die Lage identisch: der Zustand fehlt nicht, er ist unlesbar.** Wer ihn ergänzt,
baut die Dublette. **Der Handgriff ist ein Zaun, kein Feld.**

## Was ich nicht tue

**Ich fasse `docs/STATUS.md` nicht an** — ein Schreiber, und das ist der Integrator. **Ich habe die
Stelle gemessen und lege sie hin; der Eingriff ist derselbe, den er vor 47 Minuten schon einmal
gemacht hat.**

## Neue Grundlinie für meinen Takt

**Ich ziehe meine eigene Messvorschrift nach** — das ist keine fremde Regel, sondern meine
Messlatte:

```
alt   Zaun 1160 gerade · B 10 · C 24 · D 2 · D2 1
neu   Zaun 1195 UNGERADE (erwartet, solange A-18 offen ist) · B 10 · C 23 · D 1 · D2 1
      -> schliesst der Integrator A-18, ist die Erwartung: gerade · C 22 · D 0 · D2 0
```

**Bis dahin ist „ungerade" kein Alarm, sondern der bekannte Stand mit genau einem benannten Grund.**
**Wird die Bilanz ungerade UND D ≠ 1, ist es ein neuer Fall.**

## Ball

**Beim Integrator** — ein fehlender Schließzaun vor `docs/STATUS.md:7905`, nach dem Muster seines
eigenen Commits `1e9773bc`. **Danach parst A-18, sein `BETRIEBSBESTAETIGT` wird sichtbar, die
Zaunbilanz schließt und `D`/`D2` gehen auf 0.**

**Bei niemandem sonst.** Kein Auftrag ist blockiert; A-18 ist inhaltlich abgeschlossen und war es
die ganze Zeit — nur unsichtbar.
