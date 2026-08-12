# W-27 · Dachkantentypen — BEDIENUNG

> **Dieses Blatt ist ENTWURF.** *Es gibt keine Oberfläche — die Angaben sind Vorgabe für den Bau. Wo
> die Quelle schweigt, steht „offen" statt einer erfundenen Taste.*

## Aufruf

| Weg | Wie |
|---|---|
| Werkzeugleiste | **offen** — W-27 ist voraussichtlich kein eigenes Werkzeug mit Modus, sondern eine **Analyse**, die beim Dachbau mitläuft |
| Tastenkürzel | **keines nötig** |
| Automatisch | **ja, der wahrscheinliche Weg** — sobald eine Kontur vorliegt und die Kantentypen festliegen |

## Ablauf am Bildschirm

| Schritt | Anwender tut | Bildschirm zeigt |
|---|---|---|
| 1 | zeichnet eine Kontur, auch nicht-rechteckig | die Kontur mit ihren Ecken |
| 2 | legt je Kante den Typ fest (oder übernimmt die Vorbelegung aus `:182`) | Traufe · Giebel · Pultwand · Walm · Teilwalm |
| 3 | — | **je Ecke: innen/außen und Grat · Kehle · Ortgang · neutral** |
| 4 | liest die Zählung | „2 Grate · 1 Kehle · 4 Ortgänge · 3 innen / 5 außen" |

*Schritt 2 ist der, an dem der Anwender die Fachentscheidung trifft — **das Werkzeug rechnet daraus,
es rät nicht**.*

## Rückmeldungen

| Lage | Anzeige | Ton |
|---|---|---|
| Alles gut | „2 Grate, 1 Kehle, 4 Ortgänge — 3 innere, 5 äußere Ecken." | sachlich |
| **Ecke ohne traufseitige Kante** | **keine Meldung — `neutral` ist ein gültiges Ergebnis** | *stumm, absichtlich* |
| **Zwei gleiche Punkte hintereinander** | **„An dieser Stelle liegen zwei Punkte übereinander; für diese Ecke lässt sich kein Winkel bestimmen. Bitte den doppelten Punkt entfernen."** | erklärend |
| **Weniger als drei Punkte** | **„Für eine Kantenanalyse braucht es mindestens drei Ecken."** | erklärend |
| **Kantenzahl passt nicht zur Punktzahl** | **„Es sind 6 Ecken, aber nur 4 Kantentypen festgelegt. Bitte alle Kanten belegen."** | erklärend |

> **Die zweite Zeile ist eine Entscheidung und keine Auslassung.** *`neutral` ist der **häufigste**
> Ausgang — bei einem einfachen Satteldach sind es die beiden Giebelecken.* **Eine Meldung dafür
> wäre Lärm, und ein Fehler wäre es erst recht.**

> **Die dritte bis fünfte sind Vorgabe, keine Ablesung.** *Der Prototyp fängt die Länge null ab
> (`Math.hypot(...) || 1`, `:201`), sagt es aber niemandem — er rechnet mit einem Ersatzvektor
> weiter.* **Für den Bau ist das zu wenig: ein bedeutungsloser Winkel darf nicht wie ein gemessener
> aussehen.**

## Abbruch

- **Esc** bricht die Kantentyp-Festlegung ab; die Analyse selbst schreibt nichts.
- **Halbfertiges gibt es nicht** — die Funktion ist rein.

## Tastenkürzel während des Werkzeugs

| Taste | Wirkung |
|---|---|
| Esc | abbrechen |
| Eingabe | Kantentyp übernehmen |
| Umschalt · Alt | **offen** — kein Winkelzwang, kein Fang; beides gehört zur Konturzeichnung (W-01/W-02), nicht hierher |
