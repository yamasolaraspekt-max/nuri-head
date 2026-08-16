# W-29 · Dachdurchdringungen — FORMELN

> **Am Code erhoben.** *Die Registerzeile nennt keine F-Nummer, sondern `ungeprüft — stark gebaut:
> dachOeffnung, dachAusschnitt, auswechslung`.* **Gemessen sind es DREI Nummern, und zwei davon
> stehen dort nicht.**

## Die Bilanz

| Nummer | Registerzeile | gemessen |
|---|---|---|
| **F-011** Fläche eines Polygons | fehlt | **trägt** — `polygonFlaeche.ts` importiert (`:26`), gerufen auf `:314`, `:375`, `:468` |
| **F-010** Orientierung (Schuhbandformel) | fehlt | **trägt** — `:86` `a += p.x*q.y − q.x*p.y`, das Vorzeichen ist die Orientierung |
| **F-004** Schnittpunkt zweier Geraden | fehlt | **trägt NICHT** — kein `geradenSchnitt`, keine Schnittrechnung |

## F-011 trägt, und sie ist die Bilanz des Werkzeugs

```text
dachAusschnitt.ts:26   import { polygonFlaecheM2 } from './polygonFlaeche'
                :314   bruttoFlaecheM2 = polygonFlaecheM2(poly)    Rueckfall W * H
                :375   fpFlaeche       = polygonFlaecheM2(fp)
                :468   bruttoM2        = polygonFlaecheM2(poly)    Rueckfall width * height
                :450   FlaechenBilanz { bruttoM2, oeffnungEchtM2,
                                        oeffnungPrueffeldM2, nettoM2 }
```

> ***Die Bilanz trennt `oeffnungEchtM2` von `oeffnungPrueffeldM2`, und das ist der Kern.*** *Das
> **Prüffeld** ist das Loch **plus Sicherheitsrand** (`dachOeffnung.ts:68-69`), das **echte** Loch
> ist das Bauteil selbst.* **Wer die Dachfläche für die Deckung braucht, zieht das Prüffeld ab; wer
> das Bauteil bestellt, nimmt das echte Loch.** *Zwei Zahlen, weil es zwei Fragen sind.*

## F-010 trägt — als Vorzeichen, nicht als Fläche

```text
dachAusschnitt.ts:86    a += p.x * q.y - q.x * p.y;      Schuhband, SUMME
                 :106   cross = (b.x-a.x)*(c.y-b.y) - (b.y-a.y)*(c.x-b.x)
                 :107   cross * orient <= tol  ->  konkav/kollinear
                 :160   dasselbe Muster, Toleranz 1e-9  ->  L/T/U fallen raus
```

> ***Hier wird die Schuhbandformel nicht für die Fläche benutzt, sondern für ihre RICHTUNG.***
> *`orient` ist ihr Vorzeichen; jede Ecke wird dagegen geprüft.* **Läuft eine Ecke gegen die
> Gesamtorientierung, ist das Polygon an dieser Stelle einspringend** — *und eine einspringende
> Ecke bedeutet: die Fläche ist keine sichere Trapez- oder Konvexfläche mehr.*
>
> **Das ist dieselbe Formel wie in `roomDetection.ts:70` (W-05)**, *wo `signierteFlaeche` beides in
> einer Funktion liefert: Betrag ist die Fläche, Vorzeichen die Orientierung.* *Hier wird nur das
> Vorzeichen gebraucht.*

## Drei Toleranzen, drei verschiedene Größen — und sie sind NICHT dieselbe Zahl

```text
:72   istAchsenRechteck        tol = 1e-4     eine LAENGE (Meter)
:96   istKonvexesViereck       tol = 1e-6     ein KREUZPRODUKT
:115  Math.abs(u.x*v.y - u.y*v.x) / (lu*lv) <= sinTol   ein SINUS, „~8° Toleranz"
:160  cross * orient <= 1e-9                  strenger als :96
:80   KANTEN_RAND_M = 0.2                     ein ABSTAND in Metern
```

> ***Fünf Schwellen, und jede misst etwas anderes.*** **Die Sinus-Toleranz auf `:115` ist die
> lehrreichste:** *sie normiert das Kreuzprodukt durch die beiden Längen und wird damit zum Sinus
> des Zwischenwinkels* — **genau die Bauform, die ich in A-32 für `geradenSchnitt` gebaut habe,
> nachdem der rohe Betrag mit der Länge skalierte.** *Der Kommentar dort nennt die Wirkung in Grad
> („~8°"), nicht die Zahl — das ist die Angabe, die man beim Lesen braucht.*
>
> **`1e-9` auf `:160` gegen `1e-6` auf `:96` ist kein Versehen:** *die strengere Schwelle gehört zur
> Prüfung, die L-, T- und U-Formen ausschließt* (`:161` *sagt es im Kommentar*). *Wer beide Zahlen
> angleicht, lässt eine Form durch, die dort nicht durchgehen soll.*

## Was NICHT gerechnet wird

| erwartet | gemessen |
|---|---|
| **F-004 Geradenschnitt** | kommt **nicht** vor — Löcher werden über Rechteckbänder und Abstände geprüft, nicht über Kantenschnitte |
| **eine Statikformel** | keine. `auswechslung` liefert **Anzahl und Länge** der Wechselhölzer, keine Tragfähigkeit |
| **eine Neigungsrechnung** | keine. Alles läuft in **Flächenkoordinaten** (`u`, `v`), die Neigung steckt in der Fläche selbst |

> ***Die mittlere Zeile ist die wichtigste und gehört in jedes Gespräch über dieses Werkzeug:***
> **W-29 rechnet Geometrie, keine Statik.** *`wechselLaengeM` ist eine Spannweite, keine
> Bemessung — und genau deshalb ist `pruefpflichtig` kein Schwächezeichen, sondern die richtige
> Antwort an der Grenze des Rechenbaren.*
