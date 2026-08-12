# W-23 · Deckung und Material — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.** Eine Formel, die an zwei Orten steht, wird an
> einem Ort korrigiert und am anderen vergessen.

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **F-050** 🟡 | Deckung/Lattung — die Registerzeile führt sie für W-23 | **ja** — die Ampel ist gelb, der Geltungsbereich steht unten |
| **F-053** 🟡 | Lattmaß aus Sparrenlänge und Ziegelbereich (Vertretungsentscheid 12.08.) | **ja** — sie hat einen Fall ohne Ergebnis |

## Die entschiedene Fassung — und die verworfene daneben

**Yamas Fachaussage ist die Grundlage, wörtlich:**

> *„die eindecklattung ist abhängig von dach neigung und dach maße und zulässig überlappung der
> ziegel"*

```text
SCHRANKE   Dachneigung >= Regeldachneigung_grad     sonst KEINE Rechnung
TEILUNG    n_min = aufrunden(L / Lattmass_max)
           n_max = abrunden (L / Lattmass_min)
           n_min <= n_max   ->  TEILBAR: Lattmass = L / n  fuer jedes n im Bereich
           n_min >  n_max   ->  KEINE gleichmaessige Teilung — die Formel gibt
                                KEINEN Wert zurueck, sondern DIESEN FALL
```

> ### Die erste Fassung ist VERWORFEN, und das gehört hierher
>
> *Sie lautete `n = aufrunden(L/max)`, `Lattmass = L/n`.* **Gemessen an den sieben belegten Modellen
> über 801 Sparrenlängen je Modell — 5.607 Fälle — liefert sie in 2,6 % bis 18,2 % einen Wert
> außerhalb des erlaubten Bereichs, und zwar leise.**
>
> *Beispiel `Harzer Pfanne 7` (Variante `Big`) bei `L = 1000`: sie rechnet **333,3 mm**, der Ziegel erlaubt **372–405**.*
> **Der Grund ist Teilbarkeit, nicht Fachwissen:** *zwischen `n=2` (500 mm, zu groß) und `n=3`
> (333 mm, zu klein) liegt eine Lücke — für dieses Dach mit diesem Ziegel gibt es keine
> gleichmäßige Teilung.* **Wer die erste Fassung allein einbaut, baut einen Fehler ein, der in bis zu
> jedem fünften Fall eine falsche Zahl liefert.**

## Ampel und Geltungsbereich

**🟡 — das Werkzeug rechnet die REGELFLÄCHE.**

| Erfasst | Nicht erfasst |
|---|---|
| die gleichmäßige Reihenteilung zwischen Traufe und First | **Traufreihe** · **Firstanschluss** · **Ortgang** · **Restausgleich** |

*Die Ampel wird durch dieses Blatt nicht grüner. **Ein Werkzeug, das die Regelfläche rechnet und die
Anschlüsse nicht kennt, darf nicht so aussehen, als rechne es das Dach.***

## Fehlt eine Formel?

**Nein — aber eine Größe fehlt in den Daten**, und das ist kein Formelmangel:
`Regeldachneigung_grad` ist bei **`Rubin 13V`** (beide Zeilen) **leer**. *Ohne sie ist die Schranke
nicht prüfbar; die Formel greift dort gar nicht erst.* Siehe `7-GRENZEN.md`.

## Genauigkeit

- **Eingang** in mm und Grad, **Rechnung** in mm, **Rückgabe** in mm.
- **Keine Toleranz** an der Schranke: `>=` ist wörtlich gemeint. *Eine Neigung von 24,9° bei
  Regeldachneigung 25° ist eine Absage, kein Grenzfall — die Toleranz gehört zur Fachentscheidung
  und nicht ins Werkzeug.*
- `L / n` ist eine exakte Division; gerundet wird **erst bei der Anzeige**, und der gerundete Wert
  muss weiterhin im Bereich `[min, max]` liegen.
