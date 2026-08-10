# W · import grundriss — GRENZEN

> **Dieses Blatt ist Pflicht.**
> Der teuerste Fehler des Projekts bisher: ein Dach, das bei nicht-rechteckiger
> Kontur unsichtbar verschwand statt eine Absage zu geben. Die Domäne verweigerte
> korrekt — der Renderer schluckte die Absage mit `catch { continue; }`.
> **Ein Werkzeug ohne benannte Grenze baut genau diesen Fehler wieder ein.**

## Was dieses Werkzeug NICHT kann

| Fall | Warum nicht | Was der Anwender stattdessen sieht |
|---|---|---|
| | | |

## Die Absagekette

Für jeden Fall oben muss die Kette vollständig sein:

```
Schicht 1/2 wirft benannten Fehler
        ↓
Schicht 3 fängt und übersetzt
        ↓
Schicht 4 reicht DURCH — kein catch/continue
        ↓
Schicht 5 zeigt dem Anwender einen verständlichen Satz
```

| Fall | Fehlername | Wer fängt | Anwendertext steht in |
|---|---|---|---|
| | | | 4-BEDIENUNG.md |

## Fänger-Prüfung

- [ ] Jeder Fehlerpfad ist durch einen Test belegt, der prüft:
      **die Meldung erreicht die Oberfläche**
- [ ] Kein `catch { }` ohne Weiterreichen im Pfad dieses Werkzeugs
- [ ] Kein stilles `return` bei ungültiger Eingabe

## Bekannte Ungenauigkeiten

| Größe | Abweichung | Ab wann stört es |
|---|---|---|

## Was später kommen könnte

<Absichtlich weggelassene Funktionen, damit sie nicht als Fehler gemeldet werden.>
