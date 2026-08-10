# W · raster und fang — GRENZEN

> **Dieses Blatt ist Pflicht.**
> Der teuerste Fehler des Projekts bisher: ein Dach, das bei nicht-rechteckiger
> Kontur unsichtbar verschwand statt eine Absage zu geben. Die Domäne verweigerte
> korrekt — der Renderer schluckte die Absage mit `catch { continue; }`.
> **Ein Werkzeug ohne benannte Grenze baut genau diesen Fehler wieder ein.**

## Was dieses Werkzeug NICHT kann

| Fall | Warum nicht | Was der Anwender stattdessen sieht |
|---|---|---|
| **Schnittpunkt zweier Wände fangen** | die Art existiert im Code nicht (`FangArt` kennt sie nicht) | der nächstbeste Kandidat der Rangfolge, benannt |
| **Fang auf die Wand begrenzen** | `lotAufGerade()` rechnet auf die **Gerade**, ohne Begrenzung auf [0,1] | einen Punkt auf der **Verlängerung** — als `achse` oder `verlaengerung` benannt |
| **Fangen ohne Operanden** | jede Art braucht ihr eigenes Feld in `FangOptionen` | die Art feuert gar nicht; `keiner`, wenn nichts übrig bleibt |
| **Aus Pixeln in mm rechnen** | der Kern kennt keinen Zoom | der Aufrufer muss `toleranzAusZoom()` benutzen |
| **Als Werkzeug aufgerufen werden** | in der `toolRegistry` gibt es keinen Eintrag für Raster/Fang | der Fang liegt unter anderen Werkzeugen, er ist keines |
| **Entartete Achse behandeln** | `laenge2 < 1e-9` → `null` | kein Lotfang; die Rangfolge geht weiter |

## Der teuerste Fehler, gegen den dieses Blatt schützt

**Stilles Fangen.** Ein Punkt, der irgendwo einrastet, ohne zu sagen wohin, ist derselbe Fehlertyp
wie ein Dach, das unsichtbar verschwindet: *die Software hat entschieden, und niemand kann es
sehen.* **`art` ist deshalb Teil der Rückgabe und nicht optional.**
