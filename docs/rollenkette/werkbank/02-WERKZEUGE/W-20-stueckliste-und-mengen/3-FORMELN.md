# W-20 · Stückliste und Mengen — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.** Eine Formel, die an zwei Orten steht, wird an
> einem Ort korrigiert und am anderen vergessen.

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **F-011** | Polygonfläche — *nicht für das Holz, sondern für die noch fehlende Ziegelmenge* | **ja**, aber erst dann; heute nicht benutzt |
| **F-023** | Registereintrag führt sie für W-20 | **zu prüfen beim Bau der Ziegelmenge** |

## Was das Werkzeug HEUTE rechnet — und es ist keine Formel

**Eine Summe über eine Liste.** *`holzMengenAusListe` addiert die echten Stablängen je Bauteilart
(`holzMengen.ts:44-63`); es gibt keine Umformung, keine Trigonometrie, keinen Grenzwert.*

```text
sparrenLaenge = Σ  laenge   ueber alle Staebe mit name beginnend 'Sparren'/'Schiftsparren'
konterLaenge  = Σ  laenge   ueber alle Staebe mit name 'Konterlatte'
lattenLaenge  = Σ  laenge   ueber alle Staebe mit type 'latte'
sparrenAnzahl = #  Staebe   derselben Menge, mit laenge > 0
```

> **Und das ist die eigentliche Aussage dieses Blattes:** *hier steht **absichtlich** keine Formel.*
> **Die Vorgängerlösung hatte eine — sie rechnete `Anzahl × Höhe` aus dem Rechteck-Rahmen, und genau
> das war der Fehler** (`holzMengen.ts:5-8`). *Die Reparatur bestand darin, die Formel durch eine
> **Ablesung der echten Geometrie** zu ersetzen.*
>
> **Wer hier eine Formel nachträgt, stellt die zweite Wahrheit wieder her.**

## Fehlt eine Formel?

**Für die Ziegelmenge: ja — und sie liegt bereit.**

```text
Ziegelmenge (Stk)  =  Dachflaeche  ×  Bedarf_Stk_m2
                      └─ F-011        └─ W-23, Spalte 28/29 (Bedarf_min/max_Stk_m2)
```

*Beide Faktoren existieren: **F-011** ist über W-08 belegt, **W-23** ist seit dem 12.08.
`BESCHRIEBEN` und liefert den Bedarf je Modell mit Herkunft.* **Was fehlt, ist nur die
Multiplikation — und sie gehört in einen eigenen Auftrag**, weil dieses Blatt eine Ablesung ist und
keine Vorgabe. *Siehe `7-GRENZEN.md`.*

## Genauigkeit

- **Eingang** in lfm, **Rechnung** in lfm, **Rückgabe** in lfm — **keine Umrechnung, keine Rundung.**
- **Keine Toleranz.** *Es wird summiert, nicht verglichen.*
- **Die einzige Bereinigung** ist `gueltigeLaenge` (`:40-42`): *ungültige, unendliche oder negative
  Längen werden zu **0** statt zu einem Fehler.* **Das ist bewusst — eine Stückliste, die wegen eines
  kaputten Stabes gar nichts liefert, ist unbrauchbarer als eine, die ihn auslässt.**
- **Bekannte Ungenauigkeit:** *ein Stab mit fehlerhafter Länge verschwindet **still** aus der Summe.
  Es gibt heute keine Meldung darüber* — siehe `7-GRENZEN.md`.
