# W · mass und bemassung — FUNKTION

## Eingabe

| Was | Typ | Einheit | Pflicht | Prüfung |
|---|---|---|---|---|
| | | | | |

## Verarbeitung — der Zustandsautomat

```
Zustand A  ──Ereignis──►  Zustand B  ──Ereignis──►  fertig
     │
     └──Abbruch (Esc)──► Ausgangszustand, nichts geändert
```

<Jeder Zustand mit: was wird angezeigt, was wird erwartet, was passiert bei Abbruch.>

## Ausgabe

| Was | Typ | Wohin |
|---|---|---|
| | | |

## Kommando (für Rückgängig)

- **Name:** `<KommandoName>`
- **Ausführen:** <was genau am Datenmodell geändert wird>
- **Zurücknehmen:** <wie der vorherige Zustand exakt wiederhergestellt wird>
- **Bündelung:** <wird das Werkzeug zu EINEM Kommando gebündelt? Wenn ja, ab wann>

## Schichtzuordnung

- Ändert Schicht 1 (Domäne): <ja/nein — was>
- Rechnet in Schicht 2 (Geometrie): <welche F-Nummern>
- Lebt in Schicht 3 (Anwendung): <Dateiname>
- Zeigt sich in Schicht 4/5: <was der Anwender sieht>
