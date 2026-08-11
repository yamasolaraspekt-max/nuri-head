# W-11 · Maß und Bemaßung — BEDIENUNG

## Maße sehen — der Anwender tut nichts

Die Bemaßung wird **gerechnet und gelesen**, nicht bedient. Sie zeigt zwei Ebenen:

```text
INNEN   die Oeffnungskette   Wandstaerken + Fenster/Tuer-Oeffnungen + lichte Masse
AUSSEN  das Gesamt-Aussenmass
```

*Wörtlich aus `app/HausplanerApp.tsx:1266-1267`.*

## Maß eingeben — der Anwender tippt

| Handlung | Funktion | Zeile |
|---|---|---|
| eine Ziffer beginnt die Eingabe | `oeffneMit()` | 138 |
| weitere Ziffern | `tippe()` | 148 |
| zwischen Länge und Winkel wechseln | `wechsleFeld()` | 143 |
| was angezeigt wird | `massEingabeText()` | 160 |

## Der Bedienweg, den dieses Modul bewusst erhält

> *„**Die Richtung kommt aus dem Zeiger, nur die Länge aus dem Feld** — und wer will, tippt den
> Winkel dazu. **Niemand muss.** Das hält den Bedienweg vertraut: man zielt weiter mit der Maus und
> gibt nur das Maß genau an."* (`masseingabe.ts:19-21`)

**Der Winkel ist freiwillig.** *Wer ihn nicht tippt, zielt — wie vorher.*
