# W-25 · Pfetten und Kehlbalken — FORMELN

> **Am Code erhoben.** *Die Registerzeile sagt* `ungeprüft — dachformVorlagen, holzBauteile;
> Registry Pfette`. **Gemessen: es gibt hier keine Formel, und das ist richtig so.**

## Es wird gezählt, nicht gerechnet

```text
holzBauteile.ts   Math.*  0 Treffer
                  Trigonometrie  0
                  die einzige Rechnung ist  summe += laenge
```

> ***Keine F-Nummer gehört hierher.*** *Die Sammlung führt Geometrie; W-25 aggregiert fertige
> Längen.* **Wo die Längen herkommen, ist W-21 (Sparren und Lattung) und die Engine** — *dort
> stehen die Formeln, hier steht die Kasse.*

## Die einzige Entscheidung mit fachlichem Gehalt

```text
:66/:69/:72   Trennung nach stk.type: 'pfette' | 'gratsparren' | 'kehlsparren'
```

> **Drei Posten statt einer Summe.** *Das ist keine Rechnung, sondern eine Klassifikation — und sie
> ist die eigentliche Leistung des Moduls.*

## Der KEHLBALKEN aus dem Titel — und eine Messung, die ich zweimal machen musste

**Erste Messung, zu eng:** *`kehlbalken` in `holzBauteile.ts` → **0** Treffer im Rumpf.* **Daraus
hätte ich „gibt es nicht" geschrieben.**

**Zweite Messung, über den ganzen Inselbaum — es gibt ihn:**

```text
dachformVorlagen.ts:91  interface ZimmererFlags {
                   :92    sparren · firstpfette · mittelpfette · fusspfette
                   :93    KEHLBALKEN · stuhlsaeule · strebeKopfband · zange
                   :94    aufschiebling · gratsparren · kehlsparren · schifter
                   :95    wechsel
                   :1339 ff.  je Dachform gesetzt: kehlbalken true/false
Verbraucher ausserhalb dachformVorlagen.ts:   KEINER
```

> ***Der Kehlbalken ist ein DATENFELD, keine Menge.*** *`ZimmererFlags` sagt je Dachform, **welche
> Bauteile fachlich vorkommen** — dreizehn Flaggen, und `kehlbalken` ist eine davon.* **Was fehlt,
> ist der Schritt von der Flagge zur Länge:** *`holzBauteileAusListe` summiert `pfette`,
> `gratsparren` und `kehlsparren`, und keine der dreizehn Flaggen wird irgendwo ausgewertet.*
>
> **Damit ist die Lage schärfer als „fehlt":** *das Wissen, DASS ein Satteldach mit Kehlbalken
> gebaut wird* (`:1339` `kehlbalken: true`), *ist hinterlegt — und niemand fragt es ab.*

## Kehlsparren und Kehlbalken sind zwei Bauteile, nicht eines

*Der Kehlsparren liegt in der Kehle zweier Dachflächen; der Kehlbalken ist ein waagerechter Riegel
zwischen zwei Sparren.* **`holzBauteile.ts` führt den ersten** (`:72`), **die Flaggen kennen
beide** (`:93`, `:94`). *Wer die Wörter verwechselt, sucht die Menge an der falschen Stelle.*
