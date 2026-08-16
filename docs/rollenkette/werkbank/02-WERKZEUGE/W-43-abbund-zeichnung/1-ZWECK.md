# W-43 · Abbund-Zeichnung — ZWECK

> ***EINORDNUNG: W-43 ist eine ABLESUNG, und sie schließt einen Befund ab, der über drei Blätter
> gewachsen ist.***
> **Es gibt keine Zeichnung. Was es gibt, ist Fachwissen in Textform — elf Felder je Dachform,
> dreizehn Dachformen, und außerhalb der Vorlagendatei liest sie niemand.**

```text
ZEICHNUNG     FEHLT    abbund/zimmerer/werkplan in renderers/ 0 · in app/ 0
FACHWISSEN    DA       VorlagenZimmerer, 11 Felder je Vorlage, 13 Vorlagen
LESER         KEINE    alle elf Felder: ausserhalb 0 · im Test 0
QUELLE        OFFEN    Register nennt M-02; in QUELLEN.md nicht auffindbar
```

## Welches Problem des Anwenders löst dieses Werkzeug?

**Der Zimmerer bekommt keine Abbund-Zeichnung** — *weder Ansicht noch Schnitt noch Aufriss.*
**Und er bekommt auch den Text nicht**, *der für ihn geschrieben wurde.*

## Das Fachwissen, das dasteht und niemanden erreicht

`VorlagenZimmerer` (`dachformVorlagen.ts:98-110`) — **elf Felder, je Dachform gefüllt:**

```text
dachstuhltyp · flags (ZimmererFlags) · querschnittSparrenCm
querschnittPfetteCm? · querschnittGratsparrenCm? · materialFestigkeit
holzfeuchteProzent · sparrenabstandCm · abbundhinweis
spannweiteHinweis · lastabtragsweg
```

**Und der Inhalt ist keine Platzhalterprosa** — *drei Beispiele aus den dreizehn:*

> `:1719` „Sparren auf First-/Fußpfette **geklinkt (Kerve)**, Aufschiebling …"
>
> `:1917` „**Gratsparren als 3D-Länge √(dx²+dy²+dz²)**; Schifter an Grat …"
>
> `:1790` „**Trapezblech-Auflager**: Pfettenabstand auf Profil-Tragweite …"

> ***Das ist Zimmererwissen, sauber je Dachform unterschieden*** — *die Kerve gehört zum
> Pfettendach, die 3D-Länge zum Walm, die Profil-Tragweite zum Blechdach.* **Wer das geschrieben
> hat, wusste, wovon er schreibt.**

## Und die Messung, die daneben steht

| Feld | außerhalb gelesen | im Test |
|---|---|---|
| `dachstuhltyp` · `sparrenabstandCm` | **0** | 0 |
| `querschnittSparrenCm` · `querschnittPfetteCm` · `querschnittGratsparrenCm` | **0** | 0 |
| `materialFestigkeit` · `holzfeuchteProzent` | **0** | 0 |
| `abbundhinweis` · `spannweiteHinweis` · `lastabtragsweg` | **0** | 0 |

> **Zehn von zehn, und dazu `flags`, für das W-25 bereits „Verbraucher außerhalb: KEINER"
> gemessen hat.** *Elf Felder, dreizehn Dachformen — und kein einziger Leser.*

## Der Befund, der jetzt über drei Blätter trägt

```text
W-25   ZimmererFlags        13 Flaggen je Dachform      Verbraucher 0
W-26   VorlagenDachdecker   13 Felder je Dachform       ausserhalb gelesen 0
W-43   VorlagenZimmerer     11 Felder je Dachform       ausserhalb gelesen 0
```

> ***`dachformVorlagen.ts` ist nicht in erster Linie eine Geometriedatei, sondern ein
> Fachwissensspeicher*** — *und seine Verbraucherseite fehlt vollständig.* **Drei Blätter haben
> unabhängig voneinander dasselbe gemessen; beim dritten Mal ist es kein Einzelfall mehr, sondern
> die Bauform der Datei.**

## Die Vorlage, die es selbst sagt

`:2054`, drei Felder derselben Dachform:

```text
abbundhinweis:     'Geplant — Abbund erst nach sauberer Geometrie-/Tragwerksumsetzung.'
spannweiteHinweis: 'Geplant — Spannweiten nach Umsetzung statisch zu pruefen.'
lastabtragsweg:    'Geplant — Lastabtrag formabhaengig festzulegen.'
```

> ***Die Daten führen ihren eigenen Reifegrad mit.*** *Wer sie anzeigt, zeigt auch an, was noch
> nicht trägt* — **das ist dieselbe Ehrlichkeit wie `OFFENE_HOLZBAUTEILE` in W-25, nur je Vorlage
> statt je Modul.**
