# W-43 · Abbund-Zeichnung — FORMELN

## Im Werkzeug: keine

**Null Formeln.** *Es wird nichts gerechnet, weil nichts gezeichnet wird.*

## Die einzige Formel, die überhaupt vorkommt, steht in einem SATZ

`:1917`

> „Gratsparren als 3D-Länge **√(dx²+dy²+dz²)**; Schifter an Grat …"

> ***Eine Formel in einer Zeichenkette ist keine Rechnung, sondern eine Notiz.*** **Und sie ist
> anderswo tatsächlich gebaut:** *W-25 misst `holzBauteile.ts` mit `gratsparren` und
> `schifterListe.ts` mit neun Ausfuhren.* **Der Satz beschreibt also korrekt, was ein anderes
> Modul rechnet** — *er ist Dokumentation am falschen Ort, nicht eine zweite Wahrheit.*

## Die Zahlen, die als Zahlen dastehen — und niemanden erreichen

| Feld | Beispielwert | Art |
|---|---|---|
| `querschnittSparrenCm` | `[8, 20]` | Querschnitt in cm, als Zahlenpaar |
| `sparrenabstandCm` | `70` | Zahl |
| `materialFestigkeit` | `'NH C24 (Richtwert)'` | Text, mit eingebautem Vorbehalt |
| `holzfeuchteProzent` | `'≤ 20 %'` | Text, obwohl Zahl gemeint |

> **Zwei der vier sind maschinell verwendbar, zwei sind Text.** *`materialFestigkeit` trägt seinen
> Vorbehalt im Wert — „(Richtwert)" mitten in der Zeichenkette.* **Das ist ehrlich und zugleich der
> Grund, warum das Feld nie gerechnet werden kann:** *ein Wert, der seinen Vorbehalt im Text führt,
> ist nicht auswertbar, ohne ihn zu verlieren.*

> ***`holzfeuchteProzent: '≤ 20 %'` ist derselbe Fall:*** *die Grenze steckt im Zeichen `≤`.* **Wer
> daraus je eine Prüfung bauen will, braucht Feld und Vergleichsoperator getrennt** — *die Form der
> Daten entscheidet mit, ob sie je gerechnet werden können.*

## Was NICHT gerechnet wird, obwohl der Name es nahelegt

- **Keine Verbindungsgeometrie.** *Kerve, Zapfen, Versatz stehen als Wörter in `abbundhinweis`,
  nicht als Maße.*
- **Keine Abbundliste.** *Die Bauteilliste liegt in W-25 (`holzBauteile.ts`) — sie ist eine Menge,
  keine Abbundanweisung.*
- **Keine Statik.** *`spannweiteHinweis` und `lastabtragsweg` sind Text und sagen das selbst:
  „statisch zu prüfen", „formabhängig festzulegen".*

> **Alle drei Auslassungen sind im Bestand ausdrücklich benannt.** *Das ist der Unterschied
> zwischen einer Lücke und einer Unehrlichkeit* — **hier behauptet niemand eine Bemessung.**
