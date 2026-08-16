# W-25 · Pfetten und Kehlbalken — GRENZEN

## Die Liste der Lücken ist selbst lückenhaft geworden

```text
OFFENE_HOLZBAUTEILE (holzBauteile.ts:45-50)
  1  Mittelpfette      benoetigt Auflagerpunkte/Stuhl        -> gilt weiter
  2  Schwelle          nicht modelliert                      -> gilt weiter
  3  Wechselholz       „nicht eindeutig bestimmt"            -> UEBERHOLT durch
                                                                auswechslung.ts:87
                                                                (11 Zusagen gruen)
  4  Schiftersparren   „nicht eindeutig benannt"             -> UEBERHOLT durch
                                                                schifterListe.ts
                                                                (152 Z., 9 Ausfuhren)
```

**Der Beleg für #4 steht im überholenden Modul selbst** (`schifterListe.ts:6-8`): *„Diese lagen
bisher pauschal als „Sparren" in der Holzliste (siehe `holzBauteile.ts` → `OFFENE_HOLZBAUTEILE` …).
**EA28 schließt genau diese Lücke.**"*

> ***Zwei von vier Einträgen behaupten eine Unfähigkeit, die zwei geprüfte Nachbarmodule nicht mehr
> haben.*** **Und der eigene Wächter verteidigt einen davon** (`6-PRUEFUNG`).

## Der Kehlbalken ist eine Flagge ohne Leser

```text
ZimmererFlags (dachformVorlagen.ts:91-95)   DREIZEHN Flaggen je Dachform
  sparren · firstpfette · mittelpfette · fusspfette · KEHLBALKEN · stuhlsaeule
  strebeKopfband · zange · aufschiebling · gratsparren · kehlsparren · schifter · wechsel
Verbraucher ausserhalb dachformVorlagen.ts:  KEINER
```

> ***Das Wissen, welche Bauteile eine Dachform fachlich hat, ist je Vorlage hinterlegt — und
> niemand fragt es ab.*** **`holzBauteileAusListe` summiert drei Typen aus der Engine-Liste; die
> dreizehn Flaggen spielen dabei keine Rolle.**
>
> *Damit ist der Kehlbalken aus dem Registerzeilen-Titel weder „vorhanden" noch „fehlt":* **er ist
> deklariert und nicht ermittelt.**

## Was das Modul bewusst NICHT tut, und das ist seine Stärke

> **Keine erfundenen Werte.** *`:42-43` sagt es selbst.* **Bei leerer Liste kommt null heraus, nicht
> eine Schätzung** — *und eine Zusage hält es fest.*

**Das ist die Haltung, an der die anderen drei Ablesungen dieser Runde gemessen werden sollten:**
*W-17 sagt „Testfläche — wird nicht gespeichert", W-29 sagt „schematisch, fachlich prüfen", W-25
sagt „diese vier können wir nicht".* ***Drei Module, die ihre Grenze aussprechen statt sie zu
kaschieren.***

## Nachbarschaft — vier Module, verbunden nur durch Kommentare

```text
W-21  Sparren und Lattung   liefert dieselbe Holzliste (Reparatur 7)
W-29  Dachdurchdringungen   auswechslung.ts rechnet Eintrag 3 dieser Liste
      schifterListe.ts      benennt Eintrag 4 — und zitiert die Liste dabei
      dachformVorlagen.ts   ZimmererFlags, dreizehn Flaggen ohne Leser
```

> ***Keiner importiert den anderen.*** *Die Zusammenhänge stehen in Dateiköpfen.* **Eine Kante im
> Text bricht nicht, wenn sich der Code ändert — sie altert nur still.**
